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
        $tripData = $itineraryModel->findByIdNumeric($id);

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
            $role = $_POST['role']; // 'Member' or 'Editor'

            // --- IMPORTANT LOGIC MISSING HERE ---
            // In a real app, you need to search your `users` table for this email
            // to get their real `$userId`. For now, we will mock a fake user ID.
            // Example: $user = $userModel->findByEmail($email);
            // $userId = $user['id'];
            
            $userId = rand(100, 999); // Fake ID for testing until User search is built

            $newMember = new TripMember();
            $newMember->setItineraryId($id);
            $newMember->setUserId($userId);
            $newMember->setRole($role);
            $newMember->setJoinedAt(date('Y-m-d H:i:s'));
            
            $newMember->create();

            // 3. Refresh the page to see them in the list!
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
}