<?php
namespace App\Models;

use Core\Database;
use PDO;

class Vote
{
    private $db;
    private $id;
    private $voteId;
    private $timestamp;
    private $pollId;
    private $tripMemberId;
    private $ratingChoice;

    public const MUST_HAVE = 'MUST_HAVE';
    public const NICE_TO_HAVE = 'NICE_TO_HAVE';
    public const NOT_NEEDED = 'NOT_NEEDED';

    public static function getWeight($choice)
    {
        $weights = [
            self::MUST_HAVE => 3,
            self::NICE_TO_HAVE => 1,
            self::NOT_NEEDED => -1
        ];
        return $weights[$choice] ?? 0;
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

    public function getTimestamp() { return $this->timestamp; }
    public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }

    public function getPollId() { return $this->pollId; }
    public function setPollId($pollId) { $this->pollId = $pollId; }

    public function getTripMemberId() { return $this->tripMemberId; }
    public function setTripMemberId($tripMemberId) { $this->tripMemberId = $tripMemberId; }

    public function getRatingChoice() { return $this->ratingChoice; }
    public function setRatingChoice($ratingChoice) { $this->ratingChoice = $ratingChoice; }

    // CRUD Operations
    public function create()
    {
        $this->voteId = uniqid('vote_');
        $this->timestamp = date('Y-m-d H:i:s');
        $sql = "INSERT INTO Vote (voteId, timestamp, pollId, tripMemberId, ratingChoice) 
                VALUES (:voteId, :timestamp, :pollId, :tripMemberId, :ratingChoice)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':voteId' => $this->voteId,
            ':timestamp' => $this->timestamp,
            ':pollId' => $this->pollId,
            ':tripMemberId' => $this->tripMemberId,
            ':ratingChoice' => $this->ratingChoice
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
            $this->timestamp = $data['timestamp'];
            $this->pollId = $data['pollId'];
            $this->tripMemberId = $data['tripMemberId'];
            $this->ratingChoice = $data['ratingChoice'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $this->timestamp = date('Y-m-d H:i:s');
        $sql = "UPDATE Vote SET timestamp = :timestamp, 
                ratingChoice = :ratingChoice WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':timestamp' => $this->timestamp,
            ':ratingChoice' => $this->ratingChoice,
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
            $vote->timestamp = $data['timestamp'];
            $vote->pollId = $data['pollId'];
            $vote->tripMemberId = $data['tripMemberId'];
            $vote->ratingChoice = $data['ratingChoice'];
            return $vote;
        }
        return null;
    }
}
