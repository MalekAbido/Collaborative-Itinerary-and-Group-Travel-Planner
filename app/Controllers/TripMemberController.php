<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripMember;
use App\Models\Itinerary;

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

        $memberModel = new TripMember();
        $members = $memberModel->getAllByItineraryId($tripData['id']); 

        $this->view("itinerary/members", [
            'trip' => $tripData,
            'members' => $members
        ]);
    }

    public function store($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $role = $_POST['role'];

            
            $userId = rand(100, 999);

            $newMember = new TripMember();
            $newMember->setItineraryId($id);
            $newMember->setUserId($userId);
            $newMember->setRole($role);
            $newMember->setJoinedAt(date('Y-m-d H:i:s'));
            
            $newMember->create();

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
            $member = new \App\Models\TripMember();
            $member->setId($memberId);
            $member->delete();

            header("Location: /itinerary/members/" . $id . "?status=removed");
            exit;
        }
    }
}