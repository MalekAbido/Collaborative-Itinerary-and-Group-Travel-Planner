<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\Session;
use App\Constants\Messages;
use App\Models\EmergencyContact;
use Core\Controller;
use App\Models\User;
use App\Services\Mailer;

class EmergencyController extends Controller
{
    private $userId;

    public function __construct()
    {
        Auth::requireLogin();
        $this->userId = Auth::id();
    }

    public function addEmergencyContact()
    {
        $contactData = $_POST;

        if (!$this->validateEmergencyInput($contactData)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /profile");
            exit;
        }

        $contactData['userId'] = $this->userId;
        
        $contact = new EmergencyContact();
        if ($contact->create($contactData)) {
            Session::setFlash(Session::FLASH_SUCCESS, Messages::EMERGENCY_CONTACT_ADDED);
            header("Location: /profile");
            exit;
        }

        Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        header("Location: /profile");
        exit;
    }

    public function removeEmergencyContact()
    {
        $contactId = $_POST['contactId'] ?? null;

        if (!$contactId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /profile");
            exit;
        }

        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $this->userId) {
            if ($contact->delete()) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::EMERGENCY_CONTACT_REMOVED);
                header("Location: /profile");
                exit;
            }
        }

        Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
        header("Location: /profile");
        exit;
    }

    public function updateEmergencyContact()
    {
        $contactData = $_POST;
        $contactId = $contactData['contactId'] ?? null;

        if (!$contactId || !$this->validateEmergencyInput($contactData)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /profile");
            exit;
        }

        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $this->userId) {
            $contact->setName($contactData['name']);
            $contact->setEmail($contactData['email'] ?? '');
            $contact->setPhone($contactData['phone'] ?? '');
            $contact->setRelationship($contactData['relationship']);

            if ($contact->update()) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::EMERGENCY_CONTACT_UPDATED);
                header("Location: /profile");
                exit;
            }
        }

        Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
        header("Location: /profile");
        exit;
    }

    public function triggerSOS()
    {
        $userId = $this->userId;
        session_write_close();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);

        ignore_user_abort(true);
        ob_start();
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        ob_end_flush();
        flush();

        ignore_user_abort(true);
        set_time_limit(0);

        $user = new User();
        if (!$user->read($userId)) {
            exit;
        }

        $user->loadEmergencyContacts();
        $contacts = $user->getEmergencyContacts();

        if (empty($contacts)) {
            exit;
        }

        $userName = trim($user->getFirstName() . ' ' . $user->getLastName());
        $subject = "🚨 URGENT: Emergency SOS Alert from " . $userName;
        
        $baseBody = "
            <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 2px solid #ef4444; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #ef4444; text-align: center;'>EMERGENCY SOS ALERT</h2>
                <p>This is an automated emergency message from the VoyageSync application on behalf of <strong>{$userName}</strong>.</p>
                <p>They have just triggered the SOS button in their travel itinerary app, indicating they may be in trouble or need immediate assistance.</p>
                <p><strong>Please try to contact them immediately.</strong></p>
            </div>
        ";

        foreach ($contacts as $contact) {
            $email = $contact->getEmail();
            $contactName = $contact->getName();
            
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $personalizedBody = "<h3 style='color: #333;'>Dear {$contactName},</h3>" . $baseBody; 
                Mailer::send($email, $subject, $personalizedBody);
                
                sleep(7);
            }
        }
        exit;
    }

    public function triggerEmergencyAlert($location) {}

    public function validateEmergencyRequest() {}

    public function validateEmergencyInput($contactData)
    {
        if (empty($contactData['name']) || empty($contactData['relationship']) || empty($contactData['email'])) {
            return false;
        }

        if (!filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
