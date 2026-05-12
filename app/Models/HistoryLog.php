<?php

namespace App\Models;

use Core\Database;
use PDO;

class HistoryLog
{
    private $id;
    private $logId;
    private $itineraryId;
    private $createdAt;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLogId()
    {
        return $this->logId;
    }

    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    public function getItineraryId()
    {
        return $this->itineraryId;
    }

    public function setItineraryId($itineraryId)
    {
        $this->itineraryId = $itineraryId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public static function findByItineraryId($itineraryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM HistoryLog WHERE itineraryId = :itineraryId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $log = new self();
            $log->fill($data);
            return $log;
        }
        return null;
    }

    public function read($itineraryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM HistoryLog WHERE itineraryId = :itineraryId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->fill($data);
            return $this;
        }
        return null;
    }

    public function create()
    {
        $this->logId = uniqid('log_');
        $sql         = "INSERT INTO HistoryLog (logId, itineraryId, createdAt) VALUES (:logId, :itineraryId, NOW())";
        $stmt        = $this->db->prepare($sql);
        $success     = $stmt->execute([
            ':logId'       => $this->logId,
            ':itineraryId' => $this->itineraryId,
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function fill(array $data)
    {
        $this->id          = $data['id'];
        $this->logId       = $data['logId'];
        $this->itineraryId = $data['itineraryId'];
        $this->createdAt   = $data['createdAt'];
    }
}
