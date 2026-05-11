<?php
namespace App\Controllers;

use App\Services\Auth;
use App\Services\Session;
use App\Constants\Messages;
use App\Models\Allergy;
use Core\Controller;

class AllergyController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = Auth::id();
    }

    public function addAllergy()
    {
        $allergyData = $_POST;

        if (!$this->validateAllergyInput($allergyData)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location:" . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $allergyData['userId'] = $this->userId;

        $allergy = new Allergy();
        if ($allergy->create($allergyData)) {
            Session::setFlash(Session::FLASH_SUCCESS, Messages::ALLERGY_ADDED);
            header("Location:" . $_SERVER['HTTP_REFERER']);
            exit;
        }

        Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        header("Location:" . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function removeAllergy()
    {
        // The ID comes from the hidden input field in our HTML form
        $allergyId = $_POST['allergyId'] ?? null;
        
        if (!$allergyId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /profile");
            exit;
        }

        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $this->userId) {
            if ($allergy->delete()) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::ALLERGY_REMOVED);
                header("Location: /profile");
                exit;
            }
        }
        
        Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
        header("Location: /profile");
        exit;
    }

    public function updateAllergy()
    {
        $allergyData = $_POST;
        $allergyId = $allergyData['allergyId'] ?? null;

        if (!$allergyId || !$this->validateAllergyInput($allergyData)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /profile");
            exit;
        }

        $allergy = new Allergy();
        
        if ($allergy->read($allergyId) && $allergy->getUserId() == $this->userId) {
            $allergy->setAllergen($allergyData['allergen']);
            $allergy->setSeverity($allergyData['severity']);
            $allergy->setReaction($allergyData['reaction'] ?? '');
            
            if ($allergy->update()) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::ALLERGY_UPDATED);
                header("Location: /profile");
                exit;
            }
        }
        
        Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
        header("Location: /profile");
        exit;
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