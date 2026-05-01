<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Allergy;
use App\Models\User;

class AllergyController extends Controller
{
    public function addAllergy($userId, $allergyData)
    {
        if (!$this->validateAllergyInput($allergyData)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid allergy data.']);
        }

        $allergy = new Allergy();
        $allergy->setAllergen($allergyData['allergen']);
        $allergy->setSeverity($allergyData['severity']);
        $allergy->setReaction($allergyData['reaction'] ?? '');
        $allergy->setUserId($userId);

        if ($allergy->create()) {
            $user = User::getByUserId($userId);
            $user->addAllergy($allergy);
            return json_encode(['status' => 'success', 'message' => 'Allergy successfully recorded.']);
        }

        return json_encode(['status' => 'error', 'message' => 'Database error while saving allergy.']);
    }

    public function removeAllergy($userId, $allergyId)
    {
        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $userId) {
            if ($allergy->delete()) {
                return json_encode(['status' => 'success', 'message' => 'Allergy successfully removed.']);
            }
        }
        
        return json_encode(['status' => 'error', 'message' => 'Failed to remove allergy or unauthorized.']);
    }

    public function updateAllergy($userId, $allergyId, $allergyData)
    {
        if (!$this->validateAllergyInput($allergyData)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid allergy data.']);
        }

        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $userId) {
            $allergy->setAllergen($allergyData['allergen']);
            $allergy->setSeverity($allergyData['severity']);
            $allergy->setReaction($allergyData['reaction'] ?? '');
            
            if ($allergy->update()) {
                return json_encode(['status' => 'success', 'message' => 'Allergy successfully updated.']);
            }
        }
        
        return json_encode(['status' => 'error', 'message' => 'Failed to update allergy or unauthorized.']);
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