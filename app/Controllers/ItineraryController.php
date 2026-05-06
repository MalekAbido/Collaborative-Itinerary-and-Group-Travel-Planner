<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Itinerary;
use App\Models\TripFinance;

class ItineraryController extends Controller{

    public function create(){
        $this->view("itinerary/create");
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itineraryModel = new Itinerary();
            $newTripId = $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $_POST['startDate'],
                $_POST['endDate']
            );
            
            $inviteEmailsRaw = $_POST['inviteEmails'] ?? '';
            
            if (!empty(trim($inviteEmailsRaw))) {
                $emails = explode(',', $inviteEmailsRaw);
                $invitationModel = new \App\Models\Invitation();
                $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8080';

                foreach ($emails as $rawEmail) {
                    $email = trim($rawEmail);
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $token = $invitationModel->createToken($newTripId, $email, 'Member');
                        
                        if ($token) {
                            $joinLink = $baseUrl . "/join/" . $token;
                            $subject = "You've been invited to a trip on Itinerary!";
                            
                            $body = "<h2>You have a new trip invitation!</h2>
                                     <p>Click the link below to join the itinerary:</p>
                                     <a href='{$joinLink}' style='display:inline-block; padding:10px 20px; background:#f65a41; color:#fff; text-decoration:none; border-radius:5px;'>Join Trip</a>";
                            
                            \App\Helpers\Mailer::send($email, $subject, $body);
                        }
                    }
                }
            }

            $tripFinance = new \App\Models\TripFinance();
            header("Location: /itinerary/dashboard/" . $newTripId);
            exit;
        }
    }

    public function settings($id){
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);
        $this->view("itinerary/settings", ['trip' => $tripData]);
    }

    public function update($id){
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
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $itineraryModel = new Itinerary();
            $itineraryModel->delete($id);

            header("Location: /dashboard");
            exit;
        }
    }

    public function getDashboard($id){
        \App\Helpers\Auth::requireLogin();
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findByIdNumeric($id);

        if(!$tripData){
            header("Location: /dashboard");
            exit;
        }
        $this->view("itinerary/dashboard", ['trip' => $tripData]);
    }
}