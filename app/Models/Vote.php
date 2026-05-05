<?php
namespace App\Models;

use Core\Database;
use PDO;

class Vote
{
    private $db;
    private $id;
    private $voteId;
    private $voteWeight;
    private $timestamp;
    private $pollId;
    private $tripMemberId;
    private $ratingChoiceId;

    public const MUST_HAVE = 1;
    public const NICE_TO_HAVE = 2;
    public const NOT_NEEDED = 3;

    public static function getWeight($choiceId)
    {
        $weights = [
            self::MUST_HAVE => 3,
            self::NICE_TO_HAVE => 1,
            self::NOT_NEEDED => -1
        ];
        return $weights[$choiceId] ?? 0;
    }

    public static function getRatingOptions()
    {
        return [
            ['id' => self::MUST_HAVE, 'label' => 'Must Have', 'value' => 'MUST_HAVE'],
            ['id' => self::NICE_TO_HAVE, 'label' => 'Nice to Have', 'value' => 'NICE_TO_HAVE'],
            ['id' => self::NOT_NEEDED, 'label' => 'Not Needed', 'value' => 'NOT_NEEDED'],
        ];
    }

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getVoteId() { return $this->voteId; }
    public function setVoteId($voteId) { $this->voteId = $voteId; }

    public function getVoteWeight() { return $this->voteWeight; }
    public function setVoteWeight($voteWeight) { $this->voteWeight = $voteWeight; }

    public function getTimestamp() { return $this->timestamp; }
    public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }

    public function getPollId() { return $this->pollId; }
    public function setPollId($pollId) { $this->pollId = $pollId; }

    public function getTripMemberId() { return $this->tripMemberId; }
    public function setTripMemberId($tripMemberId) { $this->tripMemberId = $tripMemberId; }

    public function getRatingChoiceId() { return $this->ratingChoiceId; }
    public function setRatingChoiceId($ratingChoiceId) { $this->ratingChoiceId = $ratingChoiceId; }

    // CRUD Operations
    public function create()
    {
        $this->voteId = uniqid('vote_');
        $this->timestamp = date('Y-m-d H:i:s');
        $sql = "INSERT INTO Vote (voteId, voteWeight, timestamp, pollId, tripMemberId, ratingChoiceId) 
                VALUES (:voteId, :voteWeight, :timestamp, :pollId, :tripMemberId, :ratingChoiceId)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':voteId' => $this->voteId,
            ':voteWeight' => $this->voteWeight ?? 1.0,
            ':timestamp' => $this->timestamp,
            ':pollId' => $this->pollId,
            ':tripMemberId' => $this->tripMemberId,
            ':ratingChoiceId' => $this->ratingChoiceId
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM Vote WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->voteId = $data['voteId'];
            $this->voteWeight = $data['voteWeight'];
            $this->timestamp = $data['timestamp'];
            $this->pollId = $data['pollId'];
            $this->tripMemberId = $data['tripMemberId'];
            $this->ratingChoiceId = $data['ratingChoiceId'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $this->timestamp = date('Y-m-d H:i:s');
        $sql = "UPDATE Vote SET voteWeight = :voteWeight, timestamp = :timestamp, 
                ratingChoiceId = :ratingChoiceId WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':voteWeight' => $this->voteWeight,
            ':timestamp' => $this->timestamp,
            ':ratingChoiceId' => $this->ratingChoiceId,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "DELETE FROM Vote WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    // Object methods
    public static function getByMemberAndPoll($tripMemberId, $pollId)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Vote WHERE tripMemberId = :tripMemberId AND pollId = :pollId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':tripMemberId' => $tripMemberId, ':pollId' => $pollId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $vote = new self();
            $vote->id = $data['id'];
            $vote->voteId = $data['voteId'];
            $vote->voteWeight = $data['voteWeight'];
            $vote->timestamp = $data['timestamp'];
            $vote->pollId = $data['pollId'];
            $vote->tripMemberId = $data['tripMemberId'];
            $vote->ratingChoiceId = $data['ratingChoiceId'];
            return $vote;
        }
        return null;
    }
}
