<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Helpers\Session;
use App\Constants\Messages;
use App\Helpers\TimeHelper;
use App\Models\Activity;
use App\Models\AttendanceList;
use App\Models\AttendanceMember;
use App\Models\Location;
use App\Enums\TransactionType;
use App\Enums\ActivityStatus;
use App\Enums\AttendanceStatus;
use App\Enums\TripMemberRole;
use App\Models\TripMember;
use Core\Controller;

class ActivityController extends Controller
{
    private $user;
    private $tripMember;

    public function __construct()
    {
        Auth::requireLogin();
    }

    public function create($itineraryId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        $pendingActivity       = Session::get('pending_activity');
        $conflictingActivities = Session::get('conflicting_activities');

        Session::set('pending_activity', null);
        Session::set('conflicting_activities', null);

        $itineraryModel = new \App\Models\Itinerary();
        $itinerary = $itineraryModel->findByIdNumeric($itineraryId);

        $this->view('activity/create', [
            'itineraryId'           => $itineraryId,
            'itineraryStartDate'    => $itinerary['startDate'] ?? null,
            'itineraryEndDate'      => $itinerary['endDate'] ?? null,
            'pendingActivity'       => $pendingActivity,
            'conflictingActivities' => $conflictingActivities,
            'activeTab'             => 'createActivity',
        ]);
    }

    public function store($itineraryId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        $name              = trim($_POST['name'] ?? '');
        $description       = trim($_POST['description'] ?? '');
        $startTimeRaw      = $_POST['start_time'] ?? '';
        $endTimeRaw        = $_POST['end_time'] ?? '';
        $clientTimezoneStr = $_POST['timezone'] ?? 'UTC';

        if (empty($name) || empty($startTimeRaw) || empty($endTimeRaw)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_REQUIRED_FIELDS);
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        $startTime = TimeHelper::convertToUTC($startTimeRaw, $clientTimezoneStr);
        $endTime   = TimeHelper::convertToUTC($endTimeRaw, $clientTimezoneStr);

        if (! $startTime || ! $endTime) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_TIME_ERROR);
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        // Validation: Duration shouldn't exceed 24 hours
        $durationInSeconds = strtotime($endTime) - strtotime($startTime);
        if ($durationInSeconds > 86400) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_DURATION_ERROR);
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        // Fetch Itinerary for date validation
        $itineraryModel = new \App\Models\Itinerary();
        $itineraryData = $itineraryModel->findByIdNumeric($itineraryId);
        if ($itineraryData) {
            $itinStart = $itineraryData['startDate']; // YYYY-MM-DD
            $itinEnd   = $itineraryData['endDate'];   // YYYY-MM-DD

            // Compare as naive dates/times (ignoring timezone offset for range check)
            // Itinerary start date is considered 00:00:00 of that day
            // Itinerary end date is considered 23:59:59 of that day
            
            $activityStartNaive = strtotime(substr($startTimeRaw, 0, 10)); // Just the date part
            $activityEndNaive   = strtotime(substr($endTimeRaw, 0, 10));   // Just the date part
            
            $itinStartTs = strtotime($itinStart);
            $itinEndTs   = strtotime($itinEnd);

            if ($activityStartNaive < $itinStartTs) {
                Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_BOUND_ERROR);
                header("Location: /itinerary/{$itineraryId}/activity/create");
                exit;
            }

            if ($activityEndNaive > $itinEndTs) {
                Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_BOUND_ERROR);
                header("Location: /itinerary/{$itineraryId}/activity/create");
                exit;
            }
        }

        $category        = $_POST['category'] ?? 'General';
        $isAnonymous     = isset($_POST['is_anonymous']) ? true : false;
        $locationName    = trim($_POST['location_name'] ?? '');
        $locationAddress = trim($_POST['location_address'] ?? '');

        if (! isset($_POST['confirm_override'])) {
            $checkActivity = new Activity();
            $checkActivity->setItineraryId($itineraryId);
            $checkActivity->setStartTime($startTime);
            $checkActivity->setEndTime($endTime);

            $conflictingConfirmed = $checkActivity->getConflictingConfirmedActivities();

            if (! empty($conflictingConfirmed)) {
                $conflictData = array_map(function ($activity) {
                    return [
                        'id'        => $activity->getId(),
                        'name'      => $activity->getName(),
                        'startTime' => $activity->getStartTime(),
                        'endTime'   => $activity->getEndTime(),
                    ];
                }, $conflictingConfirmed);

                Session::set('pending_activity', $_POST);
                Session::set('conflicting_activities', $conflictData);
                header("Location: /itinerary/{$itineraryId}/activity/create");
                exit;
            }
        }

        $locationId = 1;

        if (! empty($locationName) || ! empty($locationAddress)) {
            $location = new Location();
            $location->setName($locationName);
            $location->setAddress($locationAddress);

            if ($location->create()) {
                $locationId = $location->getId();
            }
        }

        $activity = new Activity();
        $activity->setItemId(uniqid('act_'));

        $bannerImage = null;
        if (isset($_FILES['bannerImage']) && $_FILES['bannerImage']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['bannerImage']['tmp_name'];
            $fileName = $_FILES['bannerImage']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = dirname(__DIR__, 2) . '/public/uploads/activities/';
                $newFileName = $activity->getItemId() . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $bannerImage = 'uploads/activities/' . $newFileName;
                }
            }
        }

        $activity->setName($name);
        $activity->setDescription($description);
        $activity->setStartTime($startTime);
        $activity->setEndTime($endTime);
        $activity->setCategory($category);
        $activity->setIsAnonymous($isAnonymous);
        $activity->setLocationId($locationId);
        $activity->setActivityStatus(ActivityStatus::DRAFT);
        $activity->setItineraryId($itineraryId);
        $activity->setTripMemberId($tripMember->getId());
        $activity->setBannerImage($bannerImage);

        if ($activity->create()) {
            $attendanceList = new AttendanceList();

            if ($attendanceList->create($activity->getId())) {
                $allMembers = $tripMember->getAllByItineraryId($itineraryId);

                foreach ($allMembers as $memberData) {
                    $attendanceList->updateStatus($memberData['memberId'], AttendanceStatus::PENDING);
                }
            }

            Session::setFlash(Session::FLASH_SUCCESS, Messages::ACTIVITY_CREATED);
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }
    }

    public function show($itineraryId, $activityId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null || $activity->getActivityStatus() === ActivityStatus::REMOVED->value) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_NOT_FOUND);
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        $attendanceList = $activity->getAttendanceList();

        $goingMembers    = [];
        $pendingMembers  = [];
        $notGoingMembers = [];
        $totalGoing      = 0;

        if ($attendanceList) {
            $goingMembers    = $attendanceList->getMembersByStatus(AttendanceStatus::GOING);
            $pendingMembers  = $attendanceList->getMembersByStatus(AttendanceStatus::PENDING);
            $notGoingMembers = $attendanceList->getMembersByStatus(AttendanceStatus::NOT_GOING);
            $totalGoing      = $attendanceList->getTotalAttendeeCount();
        }

        $currentMemberStatus = AttendanceMember::getByTripMemberAndAttendanceList($attendanceList ? $attendanceList->getId() : 0, $tripMember->getId());

        $this->view('activity/show', [
            'itineraryId'         => $itineraryId,
            'activity'            => $activity,
            'userRole'            => $tripMember->getRole(),
            'currentMemberId'     => $tripMember->getId(),
            'currentMemberStatus' => $currentMemberStatus,
            'attendanceList'      => $attendanceList,
            'totalGoing'          => $totalGoing,
            'goingMembers'        => $goingMembers,
            'pendingMembers'      => $pendingMembers,
            'notGoingMembers'     => $notGoingMembers,
            'activeTab'           => 'activity',
        ]);
    }

    public function updateAttendance($itineraryId, $activityId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        $newStatus      = $_POST['status'] ?? AttendanceStatus::PENDING->value;
        $attendanceList = $activity->getAttendanceList();

        if ($attendanceList) {
            $attendanceList->updateStatus($tripMember->getId(), $newStatus, null);
            Session::setFlash(Session::FLASH_SUCCESS, Messages::ATTENDANCE_UPDATED);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ATTENDANCE_ERROR);
        }

        header("Location: /itinerary/{$itineraryId}/activity/{$activityId}");
        exit;
    }

    public function delete($itineraryId, $activityId)
    {
        $userId = Auth::id();

        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $tripMember->getRole());

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACTIVITY_NOT_FOUND);
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        if ($activity->delete()) {
            HistoryLogger::log($itineraryId, TransactionType::REMOVED_ACTIVITY, $activity, $tripMember->getId());
            Session::setFlash(Session::FLASH_SUCCESS, Messages::ACTIVITY_REMOVED);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /itinerary/dashboard/{$itineraryId}");
        exit;
    }
}
