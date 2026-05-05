<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Validator;
use App\Models\User;
use Core\Controller;

class UserController extends Controller
{
    private $userId;

    public function __construct()
    {
        Auth::requireLogin();
        $this->userId = Auth::id();
    }

    public function showUserProfile()
    {
        $user = new User();

        if ($user->read($this->userId)) {
            $user->loadAllergies();
            $user->loadEmergencyContacts();

            return $this->view('user/profile', [
                'user'              => $user,
                'allergies'         => $user->getAllergies(),
                'emergencyContacts' => $user->getEmergencyContacts(),
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
        $data = $_POST;

        if (!$this->validateProfileData($data)) {
            die("Invalid profile data provided. Please check your inputs.");
        }

        $user = new User();

        if ($user->read($this->userId)) {
            // Handle image upload
            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profileImage']['tmp_name'];
                $fileName = $_FILES['profileImage']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = dirname(__DIR__, 2) . '/public/uploads/profiles/';
                    
                    // Delete any existing files for this user (handles extension changes)
                    $mask = $uploadFileDir . $user->getUserId() . '.*';
                    $existingFiles = glob($mask);
                    if ($existingFiles) {
                        foreach ($existingFiles as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                    }

                    // New Naming: User ID
                    $newFileName = $user->getUserId() . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $data['profileImage'] = 'uploads/profiles/' . $newFileName;
                    }
                }
            }

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
