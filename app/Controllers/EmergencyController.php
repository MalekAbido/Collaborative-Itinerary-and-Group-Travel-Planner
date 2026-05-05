<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\EmergencyContact;
use Core\Controller;

class EmergencyController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = Auth::id();
    }

    public function addEmergencyContact()
    {
        $contactData = $_POST;

        if (!$this->validateEmergencyInput($contactData)) {
            die("Invalid contact data.");
        }

        $contactData['userId'] = $this->userId;
        
        $contact = new EmergencyContact();
        if ($contact->create($contactData)) {
            header("Location: /profile");
            exit;
        }

        die("Database error while saving emergency contact.");
    }

    public function removeEmergencyContact()
    {
        $contactId = $_POST['contactId'] ?? null;

        if (!$contactId) {
            die("No contact ID provided.");
        }

        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $this->userId) {
            if ($contact->delete()) {
                header("Location: /profile");
                exit;
            }
        }

        die("Failed to remove contact or unauthorized.");
    }

    public function updateEmergencyContact()
    {
        $contactData = $_POST;
        $contactId = $contactData['contactId'] ?? null;

        if (!$contactId || !$this->validateEmergencyInput($contactData)) {
            die("Invalid contact data.");
        }

        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $this->userId) {
            $contact->setName($contactData['name']);
            $contact->setEmail($contactData['email'] ?? '');
            $contact->setPhone($contactData['phone'] ?? '');
            $contact->setRelationship($contactData['relationship']);

            if ($contact->update()) {
                header("Location: /profile");
                exit;
            }
        }

        die("Failed to update contact or unauthorized.");
    }

    public function triggerEmergencyAlert($location) {}

    public function validateEmergencyRequest() {}

    public function validateEmergencyInput($contactData)
    {
        if (empty($contactData['name']) || empty($contactData['relationship'])) {
            return false;
        }

        return true;
    }
}