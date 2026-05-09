<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Helpers\TimeHelper;
use App\Models\Activity;
use App\Models\Itinerary;
use App\Models\Poll;
use App\Enums\TransactionType;
use App\Enums\ActivityStatus;
use App\Enums\PollStatus;
use App\Enums\TripMemberRole;
use App\Models\TripMember;
use App\Models\Vote;
use Core\Controller;

class PollController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
        date_default_timezone_set('UTC');
    }

    public function store()
    {
        $activityId        = $_POST['activityId'] ?? null;
        $deadlineRaw       = $_POST['deadline'] ?? null;
        $clientTimezoneStr = $_POST['timezone'] ?? 'UTC';
        $isAnonymous       = isset($_POST['isAnonymous']);

        if (! $activityId || ! $deadlineRaw) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $formattedDeadline = TimeHelper::convertToUTC($deadlineRaw, $clientTimezoneStr);

        if (! $formattedDeadline) {
            die("Invalid datetime or timezone provided.");
        }

        $activity = Activity::getByActivityId($activityId);
        if ($activity) {
            $startTime = strtotime($activity->getStartTime());
            $deadlineTime = strtotime($formattedDeadline);
            if ($deadlineTime > ($startTime - 86400)) {
                die("Poll deadline must be at least 24 hours before the activity starts.");
            }
        }

        $poll = new Poll();
        $poll->setActivityId($activityId);
        $poll->setDeadline($formattedDeadline);
        $poll->setIsAnonymous($isAnonymous);
        $poll->setStatus(PollStatus::OPEN);
        $poll->setWeightedTotal(0.0);

        if ($poll->create()) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            die("Failed to create poll.");
        }
    }

    public function vote()
    {
        $pollId       = $_POST['pollId'] ?? null;
        $ratingChoice = $_POST['ratingChoice'] ?? null;
        $userId       = $_SESSION['user_id'] ?? Auth::id();
        $itineraryId  = $_POST['itineraryId'] ?? null;

        if (! $pollId || ! $ratingChoice || ! $itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $poll = new Poll();

        if (! $poll->read($pollId)) {
            die("Poll not found.");
        }

        if ($poll->getStatus() === PollStatus::CLOSED || strtotime($poll->getDeadline()) <= time()) {
            if ($poll->getStatus() === PollStatus::OPEN) {
                $poll->closePoll();
            }

            header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=poll_closed");
            exit();
        }

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (! $member) {
            die("You are not a member of this trip.");
        }

        $tripMemberId = $member->getId();

        $vote = Vote::getByMemberAndPoll($tripMemberId, $pollId);

        if ($vote) {
            $vote->setRatingChoice($ratingChoice);
            $vote->update();
        } else {
            $vote = new Vote();
            $vote->setPollId($pollId);
            $vote->setTripMemberId($tripMemberId);
            $vote->setRatingChoice($ratingChoice);
            $vote->create();
        }

        $poll->calculateTotalPoints();

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function closeEarly()
    {
        $pollId      = $_POST['pollId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;

        if (! $pollId || ! $itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->enforceEditorRole($itineraryId);

        $poll = new Poll();

        if ($poll->read($pollId)) {
            $this->closePoll($itineraryId, $poll);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function reopen()
    {
        $pollId            = $_POST['pollId'] ?? null;
        $itineraryId       = $_POST['itineraryId'] ?? null;
        $newDeadlineRaw    = $_POST['newDeadline'] ?? null;
        $clientTimezoneStr = $_POST['timezone'] ?? 'UTC';

        if (! $pollId || ! $itineraryId || ! $newDeadlineRaw) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->enforceEditorRole($itineraryId);

        $poll = new Poll();

        if ($poll->read($pollId)) {
            $formattedDeadline = TimeHelper::convertToUTC($newDeadlineRaw, $clientTimezoneStr);

            if ($formattedDeadline) {
                // Check against activity start time
                $activity = Activity::getByActivityId($poll->getActivityId());
                if ($activity) {
                    $startTime = strtotime($activity->getStartTime());
                    $deadlineTime = strtotime($formattedDeadline);
                    if ($deadlineTime > ($startTime - 86400)) {
                        die("Poll deadline must be at least 24 hours before the activity starts.");
                    }
                }
                $poll->setDeadline($formattedDeadline);
                $this->openPoll($itineraryId, $poll);
            } else {
                die("Invalid datetime or timezone provided.");
            }
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    private function enforceEditorRole(int $itineraryId)
    {
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

// Handle array or object
        if ($member) {
            $role = is_array($member) ? $member['role'] : $member->getRole();
        } else {
            $role = TripMemberRole::MEMBER->value;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $role);
    }

    public function index(int $itineraryId)
    {
        $itineraryModel = new Itinerary();
        $itinerary      = $itineraryModel->findByIdNumeric($itineraryId);

        if (! $itinerary) {
            die("Itinerary not found.");
        }

        $userId = Auth::id() ?? 1;

        $member = Auth::requireMembership($itineraryId);
        $userRole = $member->getRole();

        $canManagePolls = Auth::hasRole(TripMemberRole::EDITOR->value, $userRole);

        $allPolls = Poll::getPollsByItinerary($itineraryId);

        $activePolls = [];
        $closedPolls = [];

        $currentTime = time();

        foreach ($allPolls as &$pollData) {
            $pollObj = new Poll();
            $pollObj->read($pollData['id']);

            $deadlineTime = strtotime($pollData['deadline']);

            if ($pollData['status'] === PollStatus::OPEN->value && $deadlineTime <= $currentTime) {
                if ($pollObj->read($pollData['id'])) {
                    $this->closePoll($itineraryId, $pollObj);
                }

                $pollData['status'] = PollStatus::CLOSED->value;
            }

            $pollData['stats']      = $pollObj->getVoteStats();
            $pollData['voters']     = $pollObj->getVoterDetails();
            $pollData['totalVotes'] = array_sum(array_column($pollData['stats'], 'count'));

            $activity = Activity::getByActivityId($pollData['activityId']);

            // Fetch activity details for the modal
            $pollData['activityDescription'] = $activity ? $activity->getDescription() : '';
            $pollData['startTime'] = $activity ? $activity->getStartTime() : '';
            $pollData['endTime'] = $activity ? $activity->getEndTime() : '';
            $location = $activity ? $activity->getLocation() : null;
            $pollData['locationName'] = $location ? $location->getName() : '';

            // Fetch conflicting activities and their poll points
            $pollData['conflicts'] = [];
            if ($activity) {
                $conflicts = $activity->getConflictingConfirmedActivities();
                foreach ($conflicts as $conflict) {
                    $conflictPolls = Poll::getByActivityId($conflict->getId());
                    // Use the first poll (assuming 1:1 ratio)
                    $cpPoints = !empty($conflictPolls) ? (float)$conflictPolls[0]['weightedTotal'] : 0;
                    
                    $pollData['conflicts'][] = [
                        'name' => $conflict->getName(),
                        'points' => $cpPoints
                    ];
                }
            }

            if ($pollData['status'] === PollStatus::OPEN->value) {
                $activePolls[] = $pollData;
            } else {
                $closedPolls[] = $pollData;
            }
        }

        $ratingChoices = Vote::getRatingOptions();

        $this->view('polls/polls', [
            'itinerary'      => $itinerary,
            'itineraryId'    => $itineraryId,
            'activeTab'      => 'polls',
            'activePolls'    => $activePolls,
            'closedPolls'    => $closedPolls,
            'ratingChoices'  => $ratingChoices,
            'userRole'       => $userRole,
            'canManagePolls' => $canManagePolls,
        ]);
    }

    public function closePoll(int $itineraryId, Poll $poll)
    {
        $poll->closePoll();
        $activity = Activity::getByIdAndItinerary($poll->getActivityId(), $itineraryId);
        $currentPoints = $poll->calculateTotalPoints();

        $conflicts = $activity->getConflictingConfirmedActivities();
        
        $canConfirm = ($currentPoints > 0);
        foreach ($conflicts as $conflict) {
            $conflictPolls = Poll::getByActivityId($conflict->getId());
            $cpPoints = !empty($conflictPolls) ? (float)$conflictPolls[0]['weightedTotal'] : 0;
            
            // If current poll doesn't beat each independent conflict (or match it), it cannot be confirmed
            if ($currentPoints < $cpPoints) {
                $canConfirm = false;
                break;
            }
        }

        if ($canConfirm) {
            HistoryLogger::log($itineraryId, TransactionType::ADDED_ACTIVITY, $activity, $activity->getTripMemberId());
            $activity->updateStatus(ActivityStatus::CONFIRMED);
            foreach ($conflicts as $conflict) {
                $conflict->updateStatus(ActivityStatus::DECLINED);
            }
        } else {
            $activity->updateStatus(ActivityStatus::DECLINED);
        }
    }

    public function openPoll(int $itineraryId, Poll $poll)
    {
        $poll->openPoll();
        $activity = Activity::getByIdAndItinerary($poll->getActivityId(), $itineraryId);
        $activity->updateStatus(ActivityStatus::PROPOSED);
    }
}
