<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\AttendanceList;
use App\Models\AttendanceMember;
use App\Models\Location;
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

        $this->view('activity/create', ['itineraryId' => $itineraryId]);
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
        $startTime       = $_POST['start_time'] ?? '';
        $endTime         = $_POST['end_time'] ?? '';
        $category        = $_POST['category'] ?? 'General';
        $locationName    = trim($_POST['location_name'] ?? '');
        $locationAddress = trim($_POST['location_address'] ?? '');

        if (empty($name) || empty($startTime) || empty($endTime)) {
            Session::setFlash(Session::FLASH_ERROR, 'Name, start time, and end time are required.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            Session::setFlash(Session::FLASH_ERROR, 'End time must be after start time.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        if (Activity::hasOverlap($itineraryId, $startTime, $endTime)) {
            Session::setFlash(Session::FLASH_ERROR, 'This activity overlaps with an existing activity.');
            header("Location: /itinerary/{$itineraryId}/activity/create");
            exit;
        }

        $locationId = 1;

// Fallback
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
        $activity->setLocationId($locationId);
        $activity->setActivityStatus('Draft');
        $activity->setItineraryId($itineraryId);
        $activity->setTripMemberId($tripMember->getId());

        if ($activity->create()) {
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
            'userRole'            => $tripMember->getRole(), // 'Leader', 'Editor', or 'Member'
            'currentMemberId'     => $tripMember->getId(),   // Useful for highlighting the current user in the list
            'currentMemberStatus' => $currentMemberStatus,   // Useful for highlighting the current user in the list
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
            Session::setFlash(Session::FLASH_SUCCESS, 'Activity removed successfully.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to remove the activity.');
        }

        header("Location: /itinerary/dashboard/{$itineraryId}");
        exit;
    }
}
