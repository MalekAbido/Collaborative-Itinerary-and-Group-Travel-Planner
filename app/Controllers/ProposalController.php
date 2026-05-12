<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Services\Session;
use App\Constants\Messages;
use App\Services\TimeHelper;
use App\Models\Activity;
use App\Models\Poll;
use App\Models\TripMember;
use App\Enums\ActivityStatus;
use App\Enums\PollStatus;
use App\Enums\TripMemberRole;
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
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $tripMember->getRole());

        $allDrafts = Activity::getAllByStatusAndItinerary(ActivityStatus::DRAFT, $itineraryId);
        $draftActivities = [];
        $currentTime = time();

        foreach ($allDrafts as $activity) {
            $startTime = strtotime($activity->getStartTime());
            if ($startTime <= $currentTime) {
                $activity->updateStatus(ActivityStatus::REJECTED);
                continue;
            }

            // Fetch conflicts
            $conflicts = $activity->getConflictingConfirmedActivities();
            // $activity->conflicts = $conflicts; // Attach conflicts to the activity object
            $draftActivities[] = $activity;
        }

        $rejectedActivities = Activity::getAllByStatusAndItinerary(ActivityStatus::REJECTED, $itineraryId);

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
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header('Location: /dashboard');
            exit;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $tripMember->getRole());

        $activity = Activity::getByIdAndItinerary($activityId, $itineraryId);

        if ($activity === null || $activity->getActivityStatus() !== ActivityStatus::DRAFT->value) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        $currentTime = time();
        $startTime = strtotime($activity->getStartTime());

        if ($startTime <= $currentTime) {
            $activity->updateStatus(ActivityStatus::REJECTED);
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        $pollDeadlineRaw   = $_POST['poll_deadline'] ?? '';
        $clientTimezoneStr = $_POST['timezone'] ?? 'UTC';

        if (empty($pollDeadlineRaw)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        $pollDeadline = TimeHelper::convertToUTC($pollDeadlineRaw, $clientTimezoneStr);

        if (!$pollDeadline) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        $pollDeadlineTime = strtotime($pollDeadline);
        if ($pollDeadlineTime > ($startTime - 43200)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        if ($activity->updateStatus(ActivityStatus::PROPOSED)) {
            $poll = new Poll();
            $poll->setActivityId($activityId);
            $poll->setDeadline($pollDeadline);
            $poll->setStatus(PollStatus::OPEN);
            $poll->setIsAnonymous($activity->getIsAnonymous());
            
            if ($poll->create()) {
                Session::setFlash(Session::FLASH_SUCCESS, Messages::PROPOSAL_APPROVED);
            } else {
                Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            }
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /itinerary/{$itineraryId}/proposals");
        exit;
    }

    public function reject($itineraryId, $activityId)
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

        if ($activity === null || $activity->getActivityStatus() !== ActivityStatus::DRAFT->value) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/{$itineraryId}/proposals");
            exit;
        }

        if ($activity->updateStatus(ActivityStatus::REJECTED)) {
            Session::setFlash(Session::FLASH_SUCCESS, Messages::PROPOSAL_REJECTED);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /itinerary/{$itineraryId}/proposals");
        exit;
    }
}