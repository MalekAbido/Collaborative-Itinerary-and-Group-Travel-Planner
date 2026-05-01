<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyController extends Controller
{
    public function addEmergencyContact($userId, $contactData)
    {
        if (!$this->validateEmergencyInput($contactData)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid contact data.']);
        }

        $contact = new EmergencyContact();
        $contact->setName($contactData['name']);
        $contact->setEmail($contactData['email'] ?? '');
        $contact->setPhone($contactData['phone'] ?? '');
        $contact->setRelationship($contactData['relationship']);
        $contact->setUserId($userId);

        if ($contact->create()) {
            $user = User::getByUserId($userId);
            $user->addEmergencyContact($contact);
            return json_encode(['status' => 'success', 'message' => 'Emergency contact successfully recorded.']);
        }

        return json_encode(['status' => 'error', 'message' => 'Database error while saving emergency contact.']);
    }

    public function removeEmergencyContact($userId, $contactId)
    {
        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $userId) {
            if ($contact->delete()) {
                return json_encode(['status' => 'success', 'message' => 'Emergency contact successfully removed.']);
            }
        }

        return json_encode(['status' => 'error', 'message' => 'Failed to remove contact or unauthorized.']);
    }

    public function updateEmergencyContact($userId, $contactId, $contactData)
    {
        if (!$this->validateEmergencyInput($contactData)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid contact data.']);
        }

        $contact = new EmergencyContact();

        if ($contact->read($contactId) && $contact->getUserId() == $userId) {
            $contact->setName($contactData['name']);
            $contact->setEmail($contactData['email'] ?? '');
            $contact->setPhone($contactData['phone'] ?? '');
            $contact->setRelationship($contactData['relationship']);

            if ($contact->update()) {
                return json_encode(['status' => 'success', 'message' => 'Emergency contact successfully updated.']);
            }
        }

        return json_encode(['status' => 'error', 'message' => 'Failed to update contact or unauthorized.']);
    }

    public function triggerEmergencyAlert($userId, $location) {}

    public function validateEmergencyRequest($userId) {}

    public function validateEmergencyInput($contactData)
    {
        if (empty($contactData['name']) || empty($contactData['relationship'])) {
            return false;
        }

        return true;
    }
}
