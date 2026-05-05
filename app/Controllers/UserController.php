<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Helpers\Auth;
use App\Models\User;
use Core\Controller;

class UserController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = Auth::id();
    }

    public function showUserProfile()
    {
        $user = new User();

        if ($user->read($this->userId)) {
            $user->loadAllergies();
            $user->loadEmergencyContacts();

            return $this->view('user/profile', [
                'user' => $user,
                'allergies' => $user->getAllergies(),
                'emergencyContacts' => $user->getEmergencyContacts()
            ]);
        }

        return json_encode(['status' => 'error', 'message' => 'User profile not found.']);
    }

    public function showUserTripsDashboard()
    {
        $user = new User();

        if ($user->read($this->userId)) {
            $myTrips = $user->getUserItineraries();

            return $this->view('user/dashboard', [
                'myTrips' => $myTrips,
            ]);
        }

        return json_encode(['status' => 'error', 'message' => 'User profile not found.']);
    }

    public function updateUserProfile()
    {
        // 1. Grab POST data directly
        $data = $_POST;

        if (!$this->validateProfileData($data)) {
            die("Invalid profile data provided. Please check your inputs.");
        }

        $user = new User();

        if ($user->read($this->userId)) {
            $user->updateProfile($data);

            header("Location: /profile");
            exit;
        }

        die('User not found.');
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
