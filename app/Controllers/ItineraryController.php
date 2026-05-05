<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Itinerary;
use App\Models\TripFinance;
use App\Models\TripMember;
use App\Helpers\Auth;
use App\Helpers\Session;

class ItineraryController extends Controller {

    public function create(){
        Auth::requireLogin();
        $this->view("itinerary/create");
    }

    public function store(){
        Auth::requireLogin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $itineraryModel = new Itinerary();

            // This variable gets the string ID (e.g., "trip_abc123")
            $stringTripId = $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $_POST['startDate'], 
                $_POST['endDate']
            );
            
            // ADD THIS: Get the numeric ID (e.g., 5) for the database relationship
            $numericTripId = $itineraryModel->getId();

            $tripMember = new TripMember();
            $tripMember->setItineraryId($numericTripId); // Pass the integer here!
            $tripMember->setUserId(Auth::id());
            $tripMember->setRole('Leader');
            $tripMember->setJoinedAt(date('Y-m-d H:i:s'));
            $tripMember->create();

            $tripFinance = new TripFinance(); 
            
            // Redirect using the string ID
            header("Location: /itinerary/dashboard/" . $stringTripId);
            exit;
        }
    }

    public function settings($id){
        Auth::requireLogin();
        $this->requireMembership($id);

        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);
        
        $this->view("itinerary/settings", ['trip' => $tripData]);
    }

    public function update($id){
        Auth::requireLogin();
        $member = $this->requireMembership($id);
        
        // Optional: Check if they have the right role to edit
        // Auth::requireRole('Editor', $member->getRole());

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $itineraryModel = new Itinerary();
            $itineraryModel->update(
                $id,
                $_POST['title'],
                $_POST['description'],
                $_POST['startDate'],
                $_POST['endDate']
            );

            header("Location: /itinerary/settings/" . $id . "?status=updated");
            exit;
        }
    }

    public function destroy($id){
        Auth::requireLogin();
        $member = $this->requireMembership($id);
        
        // Optional: Ensure only a Leader can delete the trip
        // Auth::requireRole('Leader', $member->getRole());

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $itineraryModel = new Itinerary();
            $itineraryModel->delete($id);

            header("Location: /dashboard");
            exit;
        }
    }

    public function getDashboard($id){
        Auth::requireLogin();
        
        $itineraryModel = new Itinerary();
        // 1. Find the trip using the string ID from the URL
        $tripData = $itineraryModel->findByIdNumeric($id);

        if(!$tripData){
            header("Location: /dashboard");
            exit;
        }

        // 2. NOW check membership using the internal integer ID
        $this->requireMembership($tripData['id']);

        // 3. Fetch the members using the internal integer ID
        $memberModel = new \App\Models\TripMember();
        $members = $memberModel->getAllByItineraryId($tripData['id']); 
        
        $this->view("itinerary/dashboard", [
            'trip' => $tripData, 
            'members' => $members
        ]);
    }

    /**
     * Helper method to verify if the currently logged-in user 
     * is an active member of the requested itinerary.
     */
    private function requireMembership($itineraryId) {
        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'Access denied. You are not a member of this itinerary.');
            header("Location: /dashboard");
            exit;
        }

        return $member; // Returning the member object in case you need to check their role (e.g., Leader/Editor)
    }
}