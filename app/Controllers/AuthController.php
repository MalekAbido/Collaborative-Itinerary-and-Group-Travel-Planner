<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\User;
use App\Enums\TripMemberRole;
use Core\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            Session::setFlash(Session::FLASH_ERROR, 'You are already logged in!');
            header("Location: /dashboard");
        } else {
            $this->view("auth/login");
        }
    }

    public function processLogin()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'errors' => ["All fields are required."]]);
                exit();
            }

            $user = new User();

            if ($user->login($email, $password)) {
                Auth::login($user->getId());

                $redirectUrl = '/dashboard';
                $intended    = Session::get('intended_url');

                if ($intended) {
                    if (strpos($intended, 'login') === false) {
                        $redirectUrl = $intended;
                    }

                    Session::set('intended_url', null);
                }

                echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
                exit();
            } else {
                echo json_encode(['success' => false, 'errors' => ["Invalid email or password."]]);
                exit();
            }
        }
    }

    public function testLogin()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $role = $_POST['role'] ?? TripMemberRole::MEMBER->value;
            $db   = \Core\Database::getInstance()->getConnection();
            $sql  = "SELECT u.id, u.email
                    FROM User u
                    JOIN TripMember tm ON u.id = tm.userId
                    WHERE tm.role = :role
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute([':role' => $role]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($userData) {
                Auth::login($userData['id']);
                echo json_encode(['success' => true, 'redirect' => '/dashboard']);
                exit();
            } else {
                echo json_encode(['success' => false, 'errors' => ["No user found with role: $role"]]);
                exit();
            }
        }
    }

    public function register()
    {
        if (Auth::check()) {
            Session::setFlash(Session::FLASH_ERROR, 'You are already logged in!');
            header("Location: /dashboard");
        } else {
            $this->view("auth/register");
        }
    }

    public function processRegister()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName       = htmlspecialchars(trim($_POST['first_name']));
            $lastName        = htmlspecialchars(trim($_POST['last_name']));
            $email           = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password        = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $errors = [];

            if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
                $errors[] = "All fields are required.";
            } else

            if (! empty($email) && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid Email format.";
            } else

            if (User::getByEmail($email)) {
                $errors[] = "Email already in use.";
            } else

            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            } else

            if (! preg_match('/[A-Z]/', $password)) {
                $errors[] = "Password must contain at least one uppercase letter.";
            } else

            if (! preg_match('/[0-9]/', $password)) {
                $errors[] = "Password must contain at least one number.";
            } else

            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match.";
            }

            if (! empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit();
            }

            $user = new User();
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user->setPasswordHash($hashedPassword);

            $user->setNationality(null);
            $user->setPolicyNumber(null);

            if ($user->register()) {
                echo json_encode(['success' => true, 'redirect' => '/login']);
                exit();
            } else {
                echo json_encode(['success' => false, 'errors' => ["Failed to create account. Please try again."]]);
                exit();
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        header("Location: /login");
        exit();
    }
}
