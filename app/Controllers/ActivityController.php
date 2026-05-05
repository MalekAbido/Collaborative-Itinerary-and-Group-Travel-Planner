<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\AttendanceMember;
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

        if ($activity === null) {
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

        $currentMemberStatus = AttendanceMember::getByTripMemberAndAttendanceList($attendanceList->getId(), $tripMember->getId());

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
