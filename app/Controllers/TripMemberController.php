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
public function index($id)
    {
        $itineraryModel = new Itinerary();
        
        $tripData = $itineraryModel->findById($id);

        if (!$tripData) {
            header("Location: /dashboard");
            exit;
        }

        $numericId = $tripData['id'];

        $memberModel = new TripMember();
        $members = $memberModel->getAllByItineraryId($numericId); 

        $invitationModel = new Invitation();
        $pendingInvites = $invitationModel->getPendingByItinerary($numericId);
        
        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';
        $generalToken = $invitationModel->getOrCreateGeneralToken($numericId);
        $generalLink = $appUrl . "/join/" . $generalToken;

        $currentUserId = Auth::id();
        $currentMember = TripMember::getByUserAndItinerary($currentUserId, $numericId);
        
        $currentUserRole = 'Member';
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            
            $itineraryModel = new Itinerary();
            $tripData = $itineraryModel->findById($id);

            if (!$tripData) {
                header("Location: /dashboard");
                exit;
            }

            $numericId = $tripData['id'];

            $invitationModel = new Invitation();
            $token = $invitationModel->createToken($numericId, $email, $role);

            if (!$token) { die("Failed to generate invitation token."); }

            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';
            $joinLink = $baseUrl . "/join/" . $token;

            $subject = "You've been invited to a trip on VoyageSync!";
            
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memberId = $_POST['memberId'];
            $member = new TripMember();
            $member->setId($memberId);
            $member->delete();

            header("Location: /itinerary/members/" . $id . "?status=removed");
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

        $existingMember = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if ($existingMember) {
            header("Location: /itinerary/dashboard/" . $itineraryId . "?status=already_joined");
            exit;
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