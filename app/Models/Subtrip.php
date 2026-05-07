<?php
namespace App\Models;

use App\Helpers\HistoryLogger;
use App\Models\ItineraryItem;
use Core\Database;
use PDO;

class Subtrip extends ItineraryItem
{
    private $deletedAt;

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function fill(array $row)
    {
        $this->setId($row['id']);
        $this->setItemId($row['itemId']);
        $this->setName($row['name']);
        $this->setDescription($row['description']);
        $this->setStartTime($row['startTime']);
        $this->setEndTime($row['endTime']);
        $this->setItineraryId($row['itineraryId']);
        $this->setTripMemberId($row['tripMemberId']);
        $this->setDeletedAt($row['deletedAt'] ?? null);
    }

    public function read($id)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Subtrip WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    public function delete($deletedByTripMemberId)
    {
        $db      = Database::getInstance()->getConnection();
        $sql     = "UPDATE Subtrip SET deletedAt = NOW() WHERE id = :id";
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute([':id' => $this->id]);

        if ($success) {
            HistoryLogger::log($this->itineraryId, \App\Models\TransactionType::DELETED_SUBTRIP, $this, $deletedByTripMemberId);
        }

        return $success;
    }

    public function update()
    {
        $db  = Database::getInstance()->getConnection();
        $sql = "UPDATE Subtrip SET
                name = :name,
                description = :description,
                startTime = :startTime,
                endTime = :endTime,
                deletedAt = :deletedAt
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':name'        => $this->name,
            ':description' => $this->description,
            ':startTime'   => $this->startTime,
            ':endTime'     => $this->endTime,
            ':deletedAt'   => $this->deletedAt,
            ':id'          => $this->id,
        ]);
    }

    public static function getAllByItineraryId($itineraryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Subtrip WHERE itineraryId = :itineraryId AND deletedAt IS NULL ORDER BY startTime ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $subtrips = [];

        foreach ($data as $row) {
            $subtrip = new self();
            $subtrip->fill($row);
            $subtrips[] = $subtrip;
        }

        return $subtrips;
    }
}
