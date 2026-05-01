<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\User;
use Core\Controller;

class UserController extends Controller
{
    public function showUserProfile($userId)
    {
        $user = new User();

        if ($user->read($userId)) {
            $user->loadAllergies();
            $user->loadEmergencyContacts();

            return $this->view('profile', [
                'user' => $user,
                'allergies' => $user->getAllergies(),
                'emergencyContacts' => $user->getEmergencyContacts(),
            ]);
        }

        return json_encode(['status' => 'error', 'message' => 'User profile not found.']);
    }

    public function showUserTripsDashboard($userId)
    {
        $user = new User();

        if ($user->read($userId)) {
            $myTrips = $user->getUserItineraries();

            return $this->view('dashboard', [
                'myTrips' => $myTrips,
            ]);
        }

        return json_encode(['status' => 'error', 'message' => 'User profile not found.']);
    }

    public function updateProfile($userId, $data)
    {
        if (!$this->validateProfileData($data)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid standard data provided.']);
        }

        $user = new User();

        if ($user->read($userId)) {
            $user->updateProfile($data);
            return json_encode(['status' => 'success', 'message' => 'Profile successfully updated.']);
        }

        return json_encode(['status' => 'error', 'message' => 'User not found.']);
    }

    public function validateProfileData($data)
    {
        $validator = new Validator();
        $validator->required('First Name', $data['firstName']);
        $validator->required('Last Name', $data['lastName']);
        $validator->required('Email', $data['email']);
        $validator->email('Email', $data['email']);

        return $validator->passes();
    }
}
