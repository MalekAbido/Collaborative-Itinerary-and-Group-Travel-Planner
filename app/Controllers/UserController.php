<?php
namespace App\Controllers;

use App\Services\Auth;
use App\Services\Session;
use App\Constants\Messages;
use App\Services\Validator;
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
        Auth::requireLogin();
        $user = new User();
        if ($user->read($this->userId)) {
            $user->loadAllergies();
            $user->loadEmergencyContacts();

            return $this->view('user/profile', [
                'user'              => $user,
                'allergies'         => $user->getAllergies(),
                'emergencyContacts' => $user->getEmergencyContacts(),
                'activeTab' => 'userSettings'
            ]);
        }
        
        Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
        header("Location: /dashboard");
        exit;
    }

    public function showUserTripsDashboard()
    {
        Auth::requireLogin();
        $user = new User();
        if ($user->read($this->userId)) {
            $myTrips = method_exists($user, 'getUserItineraries') ? $user->getUserItineraries() : [];

            return $this->view('user/dashboard', [
                'myTrips' => $myTrips,
                'activeTab' => 'dashboard'
            ]);
        }
        
        Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
        header("Location: /login");
        exit;
    }

    public function updateUserProfile()
    {
        $data = $_POST;

        if (!$this->validateProfileData($data)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /profile");
            exit;
        }

        $user = new User();
        if ($user->read($this->userId)) {
            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profileImage']['tmp_name'];
                $fileName = $_FILES['profileImage']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = dirname(__DIR__, 2) . '/public/uploads/profiles/';

                    $mask = $uploadFileDir . $user->getUserId() . '.*';
                    $existingFiles = glob($mask);
                    if ($existingFiles) {
                        foreach ($existingFiles as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                    }

                    $newFileName = $user->getUserId() . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $data['profileImage'] = 'uploads/profiles/' . $newFileName;
                    }
                }
            }

            if ($user->updateProfile($data)) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::SUCCESS_GENERIC);
            }
            
            header("Location: /profile");
            exit;
        }

        Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
        header("Location: /dashboard");
        exit;
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