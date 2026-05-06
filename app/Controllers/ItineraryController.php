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
            
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            // 1. Inline Validation Check
            if (strtotime($endDate) < strtotime($startDate)) {
                // Flash an error specifically for the date field
                \App\Helpers\Session::setFlash('date_error', 'The end date cannot be before the start date.');
                
                // Optional UX boost: Save their typed data so it doesn't clear the form
                \App\Helpers\Session::setFlash('old_title', $_POST['title']);
                \App\Helpers\Session::setFlash('old_description', $_POST['description']);
                
                // Instantly bounce them back to the form
                header("Location: /itinerary/create");
                exit;
            }

            // 2. Normal execution if dates are fine
            $itineraryModel = new Itinerary();
            $stringTripId = $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $startDate, 
                $endDate
            );
            
            $numericTripId = $itineraryModel->getId();

            $tripMember = new TripMember();
            $tripMember->setItineraryId($numericTripId);
            $tripMember->setUserId(Auth::id());
            $tripMember->setRole('Organizer');
            $tripMember->setJoinedAt(date('Y-m-d H:i:s'));
            $tripMember->create();
            
            $tripFinance = new TripFinance(); 
    
            header("Location: /itinerary/dashboard/" . $stringTripId);
            exit;
        }
    }

    public function settings($id){
        Auth::requireLogin();
        $member = Auth::requireMembership($id);
        $role = $member->getRole();
        Auth::requireRole('Organizer', $role);

        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);
        
        $this->view("itinerary/settings", ['trip' => $tripData]);
    }

    public function update($id){
        Auth::requireLogin();    
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);

        if (!$tripData) {
            header("Location: /itinerary/dashboard/" . $id);
            exit;
        }
        $member = Auth::requireMembership($tripData['id']);
        $role = $member->getRole();
        Auth::requireRole('Organizer', $role);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
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
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);
        if (!$tripData) {
            header("Location: /dashboard");
            exit;
        }

        $member = Auth::requireMembership($tripData['id']); 
        $role = $member->getRole();
        Auth::requireRole('Organizer', $role);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $itineraryModel->delete($id);
            header("Location: /dashboard");
            exit;
        }
    }

    public function getDashboard($id){
        Auth::requireLogin();
        
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);

        if(!$tripData){
            header("Location: /dashboard");
            exit;
        }
        $member = Auth::requireMembership($tripData['id']);
        $memberModel = new TripMember();
        $members = $memberModel->getAllByItineraryId($tripData['id']); 
        
        $this->view("itinerary/dashboard", [
            'trip' => $tripData, 
            'members' => $members
        ]);
    }
}