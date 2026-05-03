<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Allergy;
use App\Models\User;

class AllergyController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = 1; // Session.getUserId();
    }

    public function addAllergy()
    {
        $allergyData = $_POST;

        if (!$this->validateAllergyInput($allergyData)) {
            // In a real app, you might redirect back with an error flash message in the session
            die("Invalid allergy data.");
        }

        $allergyData['userId'] = $this->userId;

        $allergy = new Allergy();
        if ($allergy->create($allergyData)) {
            header("Location: /profile");
            exit;
        }

        die("Database error while saving allergy.");
    }

    public function removeAllergy()
    {
        // The ID comes from the hidden input field in our HTML form
        $allergyId = $_POST['allergyId'] ?? null;
        
        if (!$allergyId) {
            die("No allergy ID provided.");
        }

        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $this->userId) {
            if ($allergy->delete()) {
                header("Location: /profile");
                exit;
            }
        }
        
        die("Failed to remove allergy or unauthorized.");
    }

    public function updateAllergy()
    {
        $allergyData = $_POST;
        $allergyId = $allergyData['allergyId'] ?? null;

        if (!$allergyId || !$this->validateAllergyInput($allergyData)) {
            die("Invalid allergy data.");
        }

        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $this->userId) {
            $allergy->setAllergen($allergyData['allergen']);
            $allergy->setSeverity($allergyData['severity']);
            $allergy->setReaction($allergyData['reaction'] ?? '');
            
            if ($allergy->update()) {
                header("Location: /profile");
                exit;
            }
        }
        
        die("Failed to update allergy or unauthorized.");
    }

    public function getTripAllergies($tripId)
    {
    }

    public function validateAllergyInput($allergyData)
    {
        if (empty($allergyData['allergen']) || empty($allergyData['severity'])) {
            return false;
        }
        
        return true;
    }
}