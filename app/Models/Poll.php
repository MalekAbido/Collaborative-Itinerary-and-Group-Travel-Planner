<?php

namespace App\Models;

use Core\Database;
use PDO;

class Poll
{
    private $db;
    private $id;
    private $pollId;
    private $deadline;
    private $status;
    private $isAnonymous;
    private $weightedTotal;
    private $activityId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters and Seters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPollId()
    {
        return $this->pollId;
    }
    public function setPollId($pollId)
    {
        $this->pollId = $pollId;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getIsAnonymous()
    {
        return $this->isAnonymous;
    }
    public function setIsAnonymous($isAnonymous)
    {
        $this->isAnonymous = $isAnonymous;
    }

    public function getWeightedTotal()
    {
        return $this->weightedTotal;
    }
    public function setWeightedTotal($weightedTotal)
    {
        $this->weightedTotal = $weightedTotal;
    }

    public function getActivityId()
    {
        return $this->activityId;
    }
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    // CRUD Operations
    public function create()
    {
        $this->pollId = uniqid('poll_');
        $sql = "INSERT INTO Poll (pollId, deadline, status, isAnonymous, weightedTotal, activityId) 
                VALUES (:pollId, :deadline, :status, :isAnonymous, :weightedTotal, :activityId)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':pollId' => $this->pollId,
            ':deadline' => $this->deadline,
            ':status' => $this->status ?? 'OPEN',
            ':isAnonymous' => $this->isAnonymous ? 1 : 0,
            ':weightedTotal' => $this->weightedTotal ?? 0.0,
            ':activityId' => $this->activityId
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM Poll WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->pollId = $data['pollId'];
            $this->deadline = $data['deadline'];
            $this->status = $data['status'];
            $this->isAnonymous = (bool)$data['isAnonymous'];
            $this->weightedTotal = $data['weightedTotal'];
            $this->activityId = $data['activityId'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = "UPDATE Poll SET deadline = :deadline, status = :status, isAnonymous = :isAnonymous, 
                weightedTotal = :weightedTotal, activityId = :activityId WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':deadline' => $this->deadline,
            ':status' => $this->status,
            ':isAnonymous' => $this->isAnonymous ? 1 : 0,
            ':weightedTotal' => $this->weightedTotal,
            ':activityId' => $this->activityId,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "DELETE FROM Poll WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    // Object specific methods
    public function openPoll()
    {
        $this->status = 'OPEN';
        return $this->update();
    }

    public function closePoll()
    {
        $this->status = 'CLOSED';
        return $this->update();
    }

    public function calculateTotalPoints()
    {
        $sql = "SELECT ratingChoice FROM Vote WHERE pollId = :pollId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pollId' => $this->id]);
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($votes as $vote) {
            $total += Vote::getWeight($vote['ratingChoice']);
        }

        $this->weightedTotal = $total;
        return $this->update();
    }

    public function assignLeadingOption()
    {
        // This logic might depend on how we define "leading option"
        // For now, let's just say it calculates the points.
        return $this->calculateTotalPoints();
    }

    // Collections
    public function getVotes()
    {
        $sql = "SELECT * FROM Vote WHERE pollId = :pollId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pollId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVoteStats()
    {
        $sql = "SELECT ratingChoice, COUNT(*) as count 
                FROM Vote 
                WHERE pollId = :pollId 
                GROUP BY ratingChoice";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pollId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVoterDetails()
    {
        $sql = "SELECT v.*, u.firstName, u.lastName, u.profileImage, tm.role 
                FROM Vote v
                JOIN TripMember tm ON v.tripMemberId = tm.id
                JOIN User u ON tm.userId = u.id
                WHERE v.pollId = :pollId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pollId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByActivityId($activityId)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Poll WHERE activityId = :activityId";
        $stmt = $db->prepare($sql);
        $stmt->execute([':activityId' => $activityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPollsByItinerary($itineraryId)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT p.*, a.name as activityName, a.isAnonymous as activityIsAnonymous 
                FROM Poll p 
                JOIN Activity a ON p.activityId = a.id 
                WHERE a.itineraryId = :itineraryId
                ORDER BY p.deadline ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
