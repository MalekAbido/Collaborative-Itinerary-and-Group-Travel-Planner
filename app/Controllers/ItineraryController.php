<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Itinerary;
use App\Models\TripFinance;

class ItineraryController extends Controller{

    public function create(){
        $this->view("itinerary/create");
    }

    public function store(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $itineraryModel = new Itinerary();

            $newTripId = $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $_POST['startDate'], 
                $_POST['endDate']
            );
            $tripFinance = new TripFinance();
            header("Location: /itinerary/dashboard/" . $newTripId);
            exit;
        }
    }

    public function settings($id){
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);
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
        $itineraryModel = new Itinerary();
        $tripData = $itineraryModel->findById($id);

        if(!$tripData){
            header("Location: /dashboard");
            exit;
        }
        $this->view("itinerary/dashboard", ['trip' => $tripData]);
    }
}