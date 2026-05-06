<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripMember;
use App\Models\Itinerary;
// use App\Models\User; // You will likely need this to look up users by email!

class TripMemberController extends Controller
{
    /**
     * 1. View the Members Dashboard
     * URL: GET /itinerary/members/{id}
     */
    public function index($id)
    {
        // 1. Get the trip details so the view has the title and ID
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);

        if (!$tripData) {
            header("Location: /dashboard");
            exit;
        }

        // 2. Get all members for this specific trip
        $memberModel = new TripMember();
        $members = $memberModel->getAllByItineraryId($id); 

        // 3. Load the view we just built
        $this->view("itinerary/members", [
            'trip' => $tripData,
            'members' => $members
        ]);
    }

    /**
     * 2. Invite a New Member by Email
     * URL: POST /itinerary/members/invite/{id}
     */
    public function store($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            
            $invitationModel = new \App\Models\Invitation();
            $token = $invitationModel->createToken($id, $email, $role);

            if ($token) {
                $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';
                $joinLink = $baseUrl . "/join/" . $token;
                
                $subject = "You've been invited to join a trip!";
                $body = "Click here to join: $joinLink";
                
                \App\Helpers\Mailer::send($email, $subject, $body);
            }

            header("Location: /itinerary/members/" . $id . "?status=invited");
            exit;
        }
    }

    /**
     * 3. Update a Member's Role
     * URL: POST /itinerary/members/updateRole/{id}
     */
    public function updateRole($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memberId = $_POST['memberId'];
            $newRole = $_POST['newRole'];

            $member = new TripMember();
            
            // Find the member, change the role, and save it
            if ($member->read($memberId)) {
                $member->setRole($newRole);
                $member->update();
            }

            header("Location: /itinerary/members/" . $id . "?status=role_updated");
            exit;
        }
    }

    /**
     * 4. Remove a Member
     * URL: POST /itinerary/members/remove/{id}
     */
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
        \App\Helpers\Auth::requireLogin();
        $userId = \App\Helpers\Auth::id();
        
        $invitationModel = new \App\Models\Invitation();
        $invitation = $invitationModel->findByToken($token);
        
        if (!$invitation) {
            die("Invalid or expired invitation.");
        }
        
        $existing = \App\Models\TripMember::getByUserAndItinerary($userId, $invitation['itineraryId']);
        if ($existing) {
            header("Location: /itinerary/dashboard/" . $invitation['itineraryId']);
            exit;
        }
        
        $newMember = new \App\Models\TripMember();
        $newMember->setItineraryId($invitation['itineraryId']);
        $newMember->setUserId($userId);
        $newMember->setRole($invitation['role']);
        $newMember->setJoinedAt(date('Y-m-d H:i:s'));
        $newMember->create();
        
        $invitationModel->markUsed($token);
        
        header("Location: /itinerary/dashboard/" . $invitation['itineraryId']);
        exit;
    }
}