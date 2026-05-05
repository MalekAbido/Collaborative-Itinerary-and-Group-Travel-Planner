<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Poll;
use App\Models\Vote;
use App\Models\TripMember;
use App\Models\Itinerary;
use App\Helpers\Auth;

class PollController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
        date_default_timezone_set('Africa/Cairo');
    }

    public function store()
    {
        $activityId = $_POST['activityId'] ?? null;
        $deadlineRaw = $_POST['deadline'] ?? null;
        $isAnonymous = isset($_POST['isAnonymous']);

        if (!$activityId || !$deadlineRaw) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $formattedDeadline = date('Y-m-d H:i:s', strtotime($deadlineRaw));

        $poll = new Poll();
        $poll->setActivityId($activityId);
        $poll->setDeadline($formattedDeadline);
        $poll->setIsAnonymous($isAnonymous);
        $poll->setStatus('OPEN');
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
        $pollId = $_POST['pollId'] ?? null;
        $ratingChoiceId = $_POST['ratingChoiceId'] ?? null;
        $userId = $_SESSION['user_id'] ?? Auth::id();
        $itineraryId = $_POST['itineraryId'] ?? null;

        if (!$pollId || !$ratingChoiceId || !$itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $poll = new Poll();
        if (!$poll->read($pollId)) {
            die("Poll not found.");
        }

        // Check if manually closed OR deadline passed
        if ($poll->getStatus() === 'CLOSED' || strtotime($poll->getDeadline()) <= time()) {
            if ($poll->getStatus() === 'OPEN') {
                $poll->closePoll();
            }
            header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=poll_closed");
            exit();
        }

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            die("You are not a member of this trip.");
        }

        $tripMemberId = $member->getId();
        
        $vote = Vote::getByMemberAndPoll($tripMemberId, $pollId);

        if ($vote) {
            $vote->setRatingChoiceId($ratingChoiceId);
            $vote->update();
        } else {
            $vote = new Vote();
            $vote->setPollId($pollId);
            $vote->setTripMemberId($tripMemberId);
            $vote->setRatingChoiceId($ratingChoiceId);
            $vote->setVoteWeight(1.0);
            $vote->create();
        }

        $poll->calculateTotalPoints();

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function closeEarly()
    {
        $pollId = $_POST['pollId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;

        if (!$pollId || !$itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->enforceEditorRole($itineraryId);

        $poll = new Poll();
        if ($poll->read($pollId)) {
            $poll->closePoll();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function reopen()
    {
        $pollId = $_POST['pollId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;
        $newDeadlineRaw = $_POST['newDeadline'] ?? null;

        if (!$pollId || !$itineraryId || !$newDeadlineRaw) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->enforceEditorRole($itineraryId);

        $poll = new Poll();
        if ($poll->read($pollId)) {
            $formattedDeadline = date('Y-m-d H:i:s', strtotime($newDeadlineRaw));
            $poll->setDeadline($formattedDeadline);
            $poll->openPoll();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    private function enforceEditorRole($itineraryId)
    {
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        
        // Handle array or object
        if ($member) {
            $role = is_array($member) ? $member['role'] : $member->getRole();
        } else {
            $role = 'Member';
        }

        Auth::requireRole('Editor', $role);
    }

    public function index($itineraryId)
    {
        $itineraryModel = new Itinerary();
        $itinerary = $itineraryModel->findByIdNumeric($itineraryId);

        if (!$itinerary) {
            die("Itinerary not found.");
        }

        $userId = Auth::id() ?? 1;

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        
        if ($member) {
            $userRole = is_array($member) ? $member['role'] : $member->getRole();
        } else {
            $userRole = 'Member';
        }

        $allPolls = Poll::getPollsByItinerary($itineraryId);

        $activePolls = [];
        $closedPolls = [];
        $currentTime = time();

        foreach ($allPolls as $pollData) {
            $deadlineTime = strtotime($pollData['deadline']);

            if ($pollData['status'] === 'OPEN' && $deadlineTime <= $currentTime) {
                $pollObj = new Poll();
                if ($pollObj->read($pollData['id'])) {
                    $pollObj->closePoll();
                }
                $pollData['status'] = 'CLOSED';
            }

            if ($pollData['status'] === 'OPEN') {
                $activePolls[] = $pollData;
            } else {
                $closedPolls[] = $pollData;
            }
        }

        $ratingChoices = Vote::getRatingOptions();

        $this->view('polls/polls', [
            'itinerary' => $itinerary,
            'activePolls' => $activePolls,
            'closedPolls' => $closedPolls,
            'ratingChoices' => $ratingChoices,
            'userRole' => $userRole
        ]);
    }
}