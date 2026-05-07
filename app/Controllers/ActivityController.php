<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\AttendanceList;
use App\Models\AttendanceMember;
use App\Models\Location;
use App\Models\TransactionType;
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
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        $pendingActivity       = Session::get('pending_activity');
        $conflictingActivities = Session::get('conflicting_activities');

        Session::set('pending_activity', null);
        Session::set('conflicting_activities', null);

        $this->view('activity/create', [
            'itineraryId'           => $itineraryId,
            'pendingActivity'       => $pendingActivity,
            'conflictingActivities' => $conflictingActivities,
        ]);
    }

    public function store($itineraryId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        $name            = trim($_POST['name'] ?? '');
        $description     = trim($_POST['description'] ?? '');
        $startTime       = date('Y-m-d H:i:s', strtotime($_POST['start_time'] ?? ''));
        $endTime         = date('Y-m-d H:i:s', strtotime($_POST['end_time'] ?? ''));
        $category        = $_POST['category'] ?? 'General';
        $isAnonymous     = isset($_POST['is_anonymous']) ? true : false;
        $locationName    = trim($_POST['location_name'] ?? '');
        $locationAddress = trim($_POST['location_address'] ?? '');

        if (empty($name) || empty($_POST['start_time']) || empty($_POST['end_time'])) {
            Session::setFlash(Session::FLASH_ERROR, 'Name, start time, and end time are required.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            Session::setFlash(Session::FLASH_ERROR, 'End time must be after start time.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

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
        $activity->setName($name);
        $activity->setDescription($description);
        $activity->setStartTime($startTime);
        $activity->setEndTime($endTime);
        $activity->setCategory($category);
        $activity->setIsAnonymous($isAnonymous);
        $activity->setLocationId($locationId);
        $activity->setActivityStatus('Draft');
        $activity->setItineraryId($itineraryId);
        $activity->setTripMemberId($tripMember->getId());

        if ($activity->create()) {
            HistoryLogger::log($itineraryId, TransactionType::ACTIVITY_ADDED, $activity, $tripMember->getId());
            $attendanceList = new AttendanceList();

            if ($attendanceList->create($activity->getId())) {
                $allMembers = $tripMember->getAllByItineraryId($itineraryId);

                foreach ($allMembers as $memberData) {
                    $attendanceList->updateStatus($memberData['id'], 'PENDING');
                }
            }

            Session::setFlash(Session::FLASH_SUCCESS, 'Activity created successfully as a Draft.');
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to create activity.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }
    }

    public function show($itineraryId, $activityId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null || $activity->getActivityStatus() === "REMOVED") {
            Session::setFlash(Session::FLASH_ERROR, 'Activity not found or does not belong to this trip.');
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        $attendanceList = $activity->getAttendanceList();

        $goingMembers    = [];
        $pendingMembers  = [];
        $notGoingMembers = [];
        $totalGoing      = 0;

        if ($attendanceList) {
            $goingMembers    = $attendanceList->getMembersByStatus('GOING');
            $pendingMembers  = $attendanceList->getMembersByStatus('PENDING');
            $notGoingMembers = $attendanceList->getMembersByStatus('NOT_GOING');
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
        ]);
    }

    public function updateAttendance($itineraryId, $activityId)
    {
        $userId     = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null) {
            Session::setFlash(Session::FLASH_ERROR, 'Activity not found.');
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        $newStatus      = $_POST['status'] ?? 'PENDING';
        $attendanceList = $activity->getAttendanceList();

        if ($attendanceList) {
            $attendanceList->updateStatus($tripMember->getId(), $newStatus, null);
            Session::setFlash(Session::FLASH_SUCCESS, 'Your attendance status for the activity has been updated.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Attendance tracking is not set up for this activity.');
        }

        header("Location: /itinerary/{$itineraryId}/activity/{$activityId}");
        exit;
    }

    public function delete($itineraryId, $activityId)
    {
        $userId = Auth::id();

        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        Auth::requireRole('Editor', $tripMember->getRole());

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null) {
            Session::setFlash(Session::FLASH_ERROR, 'Activity not found or does not belong to this trip.');
            header("Location: /itinerary/dashboard/{$itineraryId}");
            exit;
        }

        if ($activity->delete()) {
            HistoryLogger::log($itineraryId, TransactionType::ACTIVITY_REMOVED, $activity, $tripMember->getId());
            Session::setFlash(Session::FLASH_SUCCESS, 'Activity removed successfully.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to remove the activity.');
        }

        header("Location: /itinerary/dashboard/{$itineraryId}");
        exit;
    }
}
