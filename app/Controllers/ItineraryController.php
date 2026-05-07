<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Itinerary;
use App\Models\TripFinance;
use App\Models\TripMember;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\Activity;

class ItineraryController extends Controller {

    public function create(){
        Auth::requireLogin();
        $this->view("itinerary/create",[
            'activeTab' => 'createItinerary'
        ]);
    }

public function store()
    {
        // 1. Require user to be logged in
        Auth::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            // 2. Inline Validation Check (Dates)
            if (strtotime($endDate) < strtotime($startDate)) {
                \App\Helpers\Session::setFlash('date_error', 'The end date cannot be before the start date.');
                \App\Helpers\Session::setFlash('old_title', $_POST['title']);
                \App\Helpers\Session::setFlash('old_description', $_POST['description']);
                
                header("Location: /itinerary/create");
                exit;
            }

            // 3. Create the Itinerary
            $itineraryModel = new Itinerary();
            $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $startDate, 
                $endDate
            );
            $stringTripId = $itineraryModel->getItineraryId();
            
            // Grab the numeric ID for database relationships
            $numericTripId = $itineraryModel->getId();

            // 4. Add the Creator as the Organizer
            $tripMember = new TripMember();
            $tripMember->setItineraryId($numericTripId);
            $tripMember->setUserId(Auth::id());
            $tripMember->setRole('Organizer');
            $tripMember->setJoinedAt(date('Y-m-d H:i:s'));
            $tripMember->create();
            
            // 5. Initialize Trip Finances
            $tripFinance = new \App\Models\TripFinance(); 

            // 6. Handle Email Invitations
            $inviteEmailsRaw = $_POST['inviteEmails'] ?? '';
            
            if (!empty(trim($inviteEmailsRaw))) {
                $emails = explode(',', $inviteEmailsRaw);
                $invitationModel = new \App\Models\Invitation();
                $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';

                foreach ($emails as $rawEmail) {
                    $email = trim($rawEmail);
                    
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        // NOTE: Using $numericTripId here because your DB schema requires an INT
                        $token = $invitationModel->createToken($numericTripId, $email, 'Member');
                        
                        if ($token) {
                            $joinLink = $baseUrl . "/join/" . $token;
                            $subject = "You've been invited to a trip on VoyageSync!";
                            
                            $body = "<h2>You have a new trip invitation!</h2>
                                     <p>Click the link below to join the itinerary:</p>
                                     <a href='{$joinLink}' style='display:inline-block; padding:10px 20px; background:#f65a41; color:#fff; text-decoration:none; border-radius:5px;'>Join Trip</a>";
                            
                            \App\Helpers\Mailer::send($email, $subject, $body);
                        }
                    }
                }
            }

            // 7. Redirect to the new dashboard using the String ID
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
        
        $this->view("itinerary/settings", [
            'trip' => $tripData,
            'itineraryId' => $tripData['id'],
            'activeTab' => 'settings'
        ]);
    }

    public function update($id){
        Auth::requireLogin();    
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);

        if (!$tripData) {
            header("Location: /itinerary/dashboard/" . $id);
            exit;
        }
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
            if (strtotime($endDate) < strtotime($startDate)) {
                \App\Helpers\Session::setFlash('date_error', 'The end date cannot be before the start date.');
                \App\Helpers\Session::setFlash('old_title', $_POST['title']);
                \App\Helpers\Session::setFlash('old_description', $_POST['description']);
                header("Location: " . $_SERVER['HTTP_REFERER']);
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

            header("Location: " . $_SERVER['HTTP_REFERER']);
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
    
    // --- NEW CODE: Fetch and sort the timeline activities ---
    // This uses your existing method which already has ORDER BY startTime ASC
    $timelineActivities = Activity::getAllByStatusAndItinerary('Confirmed', $tripData['id']);

    $this->view("itinerary/dashboard", [
        'trip' => $tripData, 
        'members' => $members,
        'activities' => $timelineActivities, // Pass the sorted array to the view
        'userRole' => $member->getRole(),
        'itineraryId' => $tripData['id'],
        'activeTab' => 'itinerary'
    ]);
    }
}