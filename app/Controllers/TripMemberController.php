<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripMember;
use App\Models\Itinerary;
use App\Models\Invitation;
use App\Helpers\Auth;
use App\Helpers\Mailer;
// use App\Models\User; // You will likely need this to look up users by email!

class TripMemberController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }
    
    public function index($id)
    {
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);

        if (!$tripData) {
            header("Location: /dashboard");
            exit;
        }

        $memberModel = new TripMember();
        $members = $memberModel->getAllByItineraryId($tripData['id']); 

        $invitationModel = new Invitation();
        $pendingInvites = $invitationModel->getPendingByItinerary($id);
        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';

        $generalToken = $invitationModel->getOrCreateGeneralToken($id);
        $generalLink = $appUrl . "/join/" . $generalToken;

        $currentUserId = Auth::id();
        $currentMember = TripMember::getByUserAndItinerary($currentUserId, $id);
        
        $currentUserRole = 'Member'; // Default
        if ($currentMember) {
            $currentUserRole = is_array($currentMember) ? $currentMember['role'] : $currentMember->getRole();
        }

        $this->view("itinerary/members", [
            'trip' => $tripData,
            'members' => $members,
            'pendingInvites' => $pendingInvites,
            'appUrl' => $appUrl,
            'generalLink' => $generalLink,
            'currentUserRole' => $currentUserRole,
            'itineraryId' => $tripData['id'],
            'activeTab' => 'members'
        ]);
    }

    public function store($id)
    {
        $member = Auth::requireMembership($id);
        $memberRole = $member->getRole();
        Auth::requireRole('Organizer', $memberRole);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            
            $invitationModel = new Invitation();
            $token = $invitationModel->createToken($id, $email, $role);

            
            $userId = rand(100, 999);

            if (!$token) { die("Failed to generate invitation token."); }

            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';
            $joinLink = $baseUrl . "/join/" . $token;

            $subject = "You've been invited to a trip on Itinerary!";
            
            $body = "<h2>You have a new trip invitation!</h2>
                     <p>Click the link below to join the itinerary:</p>
                     <a href='{$joinLink}' style='display:inline-block; padding:10px 20px; background:#f65a41; color:#fff; text-decoration:none; border-radius:5px;'>Join Trip</a>";

            Mailer::send($email, $subject, $body);

            header("Location: /itinerary/members/" . $id . "?status=invited");
            exit;
        }
    }

    public function updateRole($id)
    {
        $member = Auth::requireMembership($id);
        $memberRole = $member->getRole();
        Auth::requireRole('Organizer', $memberRole);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memberId = $_POST['memberId'];
            $newRole = $_POST['newRole'];

            $member = new TripMember();
            
            if ($member->read($memberId)) {
                $member->setRole($newRole);
                $member->update();
            }

            header("Location: /itinerary/members/" . $id . "?status=role_updated");
            exit;
        }
    }

    public function destroy($id)
    {
        $currentMember = Auth::requireMembership($id);
        $currentMemberRole = $currentMember->getRole();
        Auth::requireRole('Organizer', $currentMemberRole);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memberId = $_POST['memberId'];
            $memberModel = new \App\Models\TripMember();
            
            if ($memberModel->read($memberId)) {
                // Ensure this is the correct itinerary
                if ($memberModel->getItineraryId() != $id) {
                    header("Location: /itinerary/members/" . $id . "?status=error");
                    exit;
                }

                // Check if this is the last organizer
                $allMembers = $memberModel->getAllByItineraryId($id);
                $organizers = array_filter($allMembers, function($m) {
                    return $m['role'] === 'Organizer';
                });

                if ($memberModel->getRole() === 'Organizer' && count($organizers) <= 1) {
                    header("Location: /itinerary/members/" . $id . "?status=error_last_organizer");
                    exit;
                }

                // Invalidate pending invitations for this user's email if possible
                $user = $memberModel->getUser();
                if ($user) {
                    $invitationModel = new Invitation();
                    $invitationModel->invalidateByEmail($id, $user->getEmail());
                }

                $memberModel->delete(); // This is now a soft delete
            }

            header("Location: /itinerary/members/" . $id . "?status=removed");
            exit;
        }
    }

    public function leave($id)
    {
        $currentMember = Auth::requireMembership($id);
        $currentMemberRole = $currentMember->getRole();

        if (Auth::hasRole('Organizer', $currentMemberRole)) {
            header("Location: /itinerary/members/" . $id . "?status=error_organizer_cannot_leave");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentMember->delete();
            header("Location: /dashboard?status=left_trip");
            exit;
        }
    }

    public function joinTrip($token)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $invitationModel = new Invitation();
        $invitation = $invitationModel->findByToken($token);

        if (!$invitation) {
            die("This invitation link is invalid or has expired.");
        }

        $itineraryId = $invitation['itineraryId'];
        $role = $invitation['role'];

        $existingMember = TripMember::getByUserAndItinerary($userId, $itineraryId, true);
        
        if ($existingMember) {
            if ($existingMember->getDeletedAt() === null) {
                header("Location: /itinerary/dashboard/" . $itineraryId . "?status=already_joined");
                exit;
            } else {
                // Reactivate
                if ($existingMember->reactivate($role)) {
                    if ($invitation['email'] !== null) {
                        $invitationModel->markUsed($invitation['secureToken']);
                    }
                    header("Location: /itinerary/dashboard/" . $itineraryId . "?status=joined");
                    exit;
                }
            }
        }

        $newMember = new TripMember();
        $newMember->setItineraryId($itineraryId);
        $newMember->setUserId($userId);
        $newMember->setRole($role);
        $newMember->setJoinedAt(date('Y-m-d H:i:s'));
        $newMember->setMembershipId(uniqid('mem_')); 

        if ($newMember->create()) {
            
            if ($invitation['email'] !== null) {
                $invitationModel->markUsed($invitation['secureToken']);
            }
            
            header("Location: /itinerary/dashboard/" . $itineraryId . "?status=joined");
            exit;
        } else {
            die("An error occurred while adding you to the trip.");
        }
    }
}