<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Poll;
use App\Models\Vote;
use App\Models\TripMember;
use App\Models\Itinerary;
use App\Helpers\Auth; // Added Auth helper

class PollController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Africa/Cairo'); 
    }

    public function store()
    {
        $this->requireAuth();

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
        $this->requireAuth();

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

        $db = \Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM TripMember WHERE userId = :userId AND itineraryId = :itineraryId LIMIT 1");
        $stmt->execute([':userId' => $userId, ':itineraryId' => $itineraryId]);
        $member = $stmt->fetch();

        if (!$member) {
            die("You are not a member of this trip.");
        }

        $tripMemberId = $member['id'];
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
            // openPoll() updates the object in the DB, so it saves the new deadline too!
            $poll->openPoll();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    private function enforceEditorRole($itineraryId)
    {
        $userId = Auth::id();
        
        $db = \Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT role FROM TripMember WHERE userId = :userId AND itineraryId = :itineraryId LIMIT 1");
        $stmt->execute([':userId' => $userId, ':itineraryId' => $itineraryId]);
        $member = $stmt->fetch();
        
        $role = $member ? $member['role'] : 'Member';

        if (!Auth::requireRole('Editor', $role)) {
            die("Unauthorized: You must be an Editor or Leader to perform this action.");
        }
    }

    public function index($itineraryId)
    {
        $this->requireAuth();

        $itineraryModel = new Itinerary();
        $itinerary = $itineraryModel->findByIdNumeric($itineraryId);
        
        if (!$itinerary) {
            die("Itinerary not found.");
        }

        $db = \Core\Database::getInstance()->getConnection();
        
        // Fetch user role for the UI conditional rendering
        $userId = Auth::id() ?? 1;
        $stmtRole = $db->prepare("SELECT role FROM TripMember WHERE userId = :userId AND itineraryId = :itineraryId LIMIT 1");
        $stmtRole->execute([':userId' => $userId, ':itineraryId' => $itineraryId]);
        $memberRow = $stmtRole->fetch();
        $userRole = $memberRow ? $memberRow['role'] : 'Member';

        $sql = "SELECT p.*, a.name as activityName 
                FROM Poll p 
                JOIN Activity a ON p.activityId = a.id 
                WHERE a.itineraryId = :itineraryId
                ORDER BY p.deadline ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $allPolls = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
            'userRole' => $userRole // Pass the role to the view
        ]);
    }
}