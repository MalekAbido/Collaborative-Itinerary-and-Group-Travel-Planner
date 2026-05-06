<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\Poll;
use App\Models\TripMember;
use Core\Controller;

class ProposalController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    public function index($itineraryId)
    {
        $userId = Auth::id();
        $tripMember = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if ($tripMember === null) {
            Session::setFlash(Session::FLASH_ERROR, 'You do not have access to this itinerary.');
            header('Location: /dashboard');
            exit;
        }

        Auth::requireRole('Editor', $tripMember->getRole());

        $draftActivities = Activity::getAllByStatusAndItinerary('Draft', $itineraryId);
        $rejectedActivities = Activity::getAllByStatusAndItinerary('Rejected', $itineraryId);

        $this->view('proposal/dashboard', [
            'itineraryId' => $itineraryId,
            'draftActivities' => $draftActivities,
            'rejectedActivities' => $rejectedActivities,
            'memberRole' => $tripMember->getRole(),
            'activeTab' => 'proposals'
        ]);
    }

    public function approve($itineraryId, $activityId)
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

        if ($activity === null || $activity->getActivityStatus() !== 'Draft') {
            Session::setFlash(Session::FLASH_ERROR, 'Invalid activity or activity is not a draft.');
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        // $date = $_POST['deadline_date'] ?? '';
        // $time = $_POST['deadline_time'] ?? '';

        // if (empty($date) || empty($time)) {
        //     Session::setFlash(Session::FLASH_ERROR, 'Both date and time are required for the poll deadline.');
        //     header("Location: /itinerary/{$itineraryId}/proposals");
        //     exit;
        // }

        // $pollDeadline = $date . ' ' . $time;
        $pollDeadline = $_POST['poll_deadline'] ?? '';
        if (empty($pollDeadline)) {
            Session::setFlash(Session::FLASH_ERROR, 'Both date and time are required for the poll deadline.');
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }


        if ($activity->updateStatus('Proposed')) {
            $poll = new Poll();
            $poll->setActivityId($activityId);
            $poll->setDeadline($pollDeadline);
            $poll->setStatus('OPEN');
            $poll->setIsAnonymous($activity->getIsAnonymous());
            
            if ($poll->create()) {
                Session::setFlash(Session::FLASH_SUCCESS, 'Proposal approved and poll created successfully.');
            } else {
                Session::setFlash(Session::FLASH_ERROR, 'Activity status updated, but failed to create poll.');
            }
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to update activity status.');
        }

        header("Location: /itinerary/{$itineraryId}/proposals");
        exit;
    }

    public function reject($itineraryId, $activityId)
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

        if ($activity === null || $activity->getActivityStatus() !== 'Draft') {
            Session::setFlash(Session::FLASH_ERROR, 'Invalid activity or activity is not a draft.');
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        if ($activity->updateStatus('Rejected')) {
            Session::setFlash(Session::FLASH_SUCCESS, 'Proposal has been rejected.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to reject the proposal.');
        }

        header("Location: /itinerary/{$itineraryId}/proposals");
        exit;
    }
}