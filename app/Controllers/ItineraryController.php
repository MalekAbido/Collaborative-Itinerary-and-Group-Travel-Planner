<?php
namespace App\Controllers;

use App\Constants\Messages;
use App\Enums\ActivityStatus;
use App\Enums\TripMemberRole;
use App\Models\Activity;
use App\Models\Itinerary;
use App\Models\TripMember;
use App\Services\Auth;
use App\Services\Session;
use Core\Controller;

class ItineraryController extends Controller
{

    public function create()
    {
        Auth::requireLogin();
        $this->view("itinerary/create", [
            'activeTab' => 'createItinerary',
        ]);
    }

    public function store()
    {
        // 1. Require user to be logged in
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $_POST['startDate'];
            $endDate   = $_POST['endDate'];

// 2. Inline Validation Check (Dates)
            if (strtotime($endDate) < strtotime($startDate)) {
                Session::setFlash(Session::FLASH_ERROR, Messages::ITIN_DATE_ERROR);
                Session::setFlash('old_title', $_POST['title']);
                Session::setFlash('old_description', $_POST['description']);

                header("Location: /itinerary/create");
                exit;
            }

            // --- NEW: HANDLE IMAGE UPLOAD ---
            $coverImagePath = null;

            if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
                // Define where the image should be saved (e.g., public/uploads/itineraries)
                $uploadDir = __DIR__ . '/../../public/uploads/itineraries/';

// Create the directory if it doesn't exist yet
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate a unique file name so images don't overwrite each other
                $fileName   = uniqid('trip_img_') . '_' . basename($_FILES['coverImage']['name']);
                $targetPath = $uploadDir . $fileName;

// Move the file from the temp folder to your public uploads folder
                if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $targetPath)) {
                    $coverImagePath = 'uploads/itineraries/' . $fileName; // Path saved in DB
                }
            }

            // 3. Create the Itinerary (Now passing the image path!)
            $itineraryModel = new Itinerary();
            $itineraryModel->create(
                $_POST['title'],
                $_POST['description'],
                $startDate,
                $endDate,
                $coverImagePath
            );
            $stringTripId = $itineraryModel->getItineraryId();

            // Grab the numeric ID for database relationships
            $numericTripId = $itineraryModel->getId();

            // 4. Add the Creator as the Organizer
            $tripMember = new TripMember();
            $tripMember->setItineraryId($numericTripId);
            $tripMember->setUserId(Auth::id());
            $tripMember->setRole(TripMemberRole::ORGANIZER->value);
            $tripMember->setJoinedAt(date('Y-m-d H:i:s'));
            $tripMember->create();

            // 5. Initialize Trip Finances
            $baseCurrency = $_POST['baseCurrency'] ?? 'USD';
            $tripFinance  = new \App\Models\TripFinance();
            $tripFinance->create($numericTripId, $baseCurrency, 0);

            // 6. Handle Email Invitations
            $inviteEmailsRaw = $_POST['inviteEmails'] ?? '';

            if (! empty(trim($inviteEmailsRaw))) {
                $emails          = explode(',', $inviteEmailsRaw);
                $invitationModel = new \App\Models\Invitation();
                $baseUrl         = $_ENV['APP_URL'] ?? 'http://localhost:8080';

                foreach ($emails as $rawEmail) {
                    $email = trim($rawEmail);

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $token = $invitationModel->createToken($numericTripId, $email, TripMemberRole::MEMBER->value);

                        if ($token) {
                            $joinLink = $baseUrl . "/join/" . $token;
                            $subject  = "You've been invited to a trip on VoyageSync!";

                            $body = "<h2>You have a new trip invitation!</h2>
                                     <p>Click the link below to join the itinerary:</p>
                                     <a href='{$joinLink}' style='display:inline-block; padding:10px 20px; background:#f65a41; color:#fff; text-decoration:none; border-radius:5px;'>Join Trip</a>";

                            \App\Services\Mailer::send($email, $subject, $body);
                        }
                    }
                }
            }

            // 7. Redirect to the new dashboard
            Session::setFlash(Session::FLASH_SUCCESS, Messages::ITIN_CREATED);
            header("Location: /itinerary/dashboard/" . $stringTripId);
            exit;
        }
    }

    public function settings($id)
    {
        Auth::requireLogin();
        $member = Auth::requireMembership($id);
        $role   = $member->getRole();
        Auth::requireRole(TripMemberRole::ORGANIZER->value, $role);

        $itineraryModel = new Itinerary();
        $tripData       = $itineraryModel->findByIdNumeric($id);

        $this->view("itinerary/settings", [
            'trip'        => $tripData,
            'itineraryId' => $tripData['id'],
            'activeTab'   => 'settings',
        ]);
    }

    public function update($id)
    {
        Auth::requireLogin();
        $itineraryModel = new Itinerary();

        // Find the trip using the string ID from the URL
        $tripData = $itineraryModel->findById($id);

        if (! $tripData) {
            header("Location: /dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $_POST['startDate'];
            $endDate   = $_POST['endDate'];

            if (strtotime($endDate) < strtotime($startDate)) {
                Session::setFlash(Session::FLASH_ERROR, Messages::ITIN_DATE_ERROR);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

                                                                // Require Organizer permissions
            $member = Auth::requireMembership($tripData['id']); // uses numeric ID
            $role   = $member->getRole();
            Auth::requireRole(TripMemberRole::ORGANIZER->value, $role);

            // --- NEW: HANDLE IMAGE UPLOAD ON UPDATE ---
            $coverImagePath = null;
// Stays null unless they actually uploaded a new file

            if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/itineraries/';

                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName   = uniqid('trip_img_') . '_' . basename($_FILES['coverImage']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $targetPath)) {
                    $coverImagePath = 'uploads/itineraries/' . $fileName;
                }
            }

// Update the database
            if ($itineraryModel->update(
                $id,
                $_POST['title'],
                $_POST['description'],
                $startDate,
                $endDate,
                $coverImagePath // This will be null if no new image, or the new path if uploaded!
            )) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::ITIN_UPDATED);
            }

            // Redirect back to settings with the success flag
            header("Location: /itinerary/settings/" . $id . "?status=updated");
            exit;
        }
    }

    public function destroy($id)
    {
        Auth::requireLogin();
        $itineraryModel = new Itinerary();
        $tripData       = $itineraryModel->findById($id);

        if (! $tripData) {
            header("Location: /dashboard");
            exit;
        }

        $member = Auth::requireMembership($tripData['id']);
        $role   = $member->getRole();
        Auth::requireRole(TripMemberRole::ORGANIZER->value, $role);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($itineraryModel->delete($id)) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::ITIN_DELETED);
            }

            header("Location: /dashboard");
            exit;
        }
    }

    public function getDashboard($id)
    {
        Auth::requireLogin();

        $itineraryModel = new Itinerary();
        $tripData       = $itineraryModel->findByIdNumeric($id);

        if (! $tripData) {
            header("Location: /dashboard");
            exit;
        }

        $member      = Auth::requireMembership($tripData['id']);
        $memberModel = new TripMember();
        $members     = $memberModel->getAllByItineraryId($tripData['id']);

// --- NEW CODE: Fetch and sort the timeline activities ---
        // This uses your existing method which already has ORDER BY startTime ASC
        $timelineActivities = Activity::getAllByStatusAndItinerary(ActivityStatus::CONFIRMED, $tripData['id']);

        $this->view("itinerary/dashboard", [
            'trip'        => $tripData,
            'members'     => $members,
            'activities'  => $timelineActivities, // Pass the sorted array to the view
            'userRole'    => $member->getRole(),
            'itineraryId' => $tripData['id'],
            'activeTab'   => 'itinerary',
        ]);
    }
}
