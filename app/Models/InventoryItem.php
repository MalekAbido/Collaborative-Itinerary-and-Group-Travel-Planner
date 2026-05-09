<?php
namespace App\Models;

use Core\Database;
use PDO;

class InventoryItem
{
    private $db;
    private $id;
    private $itemId;
    private $name;
    private $quantity;
    private $description;
    private $isPacked;
    private $activityId;
    private $tripMemberId;
    private $creatorMemberId;
    private $deletedAt;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getItemId() { return $this->itemId; }
    public function setItemId($itemId) { $this->itemId = $itemId; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getIsPacked() { return (bool)$this->isPacked; }
    public function setIsPacked($isPacked) { $this->isPacked = $isPacked; }

    public function getActivityId() { return $this->activityId; }
    public function setActivityId($activityId) { $this->activityId = $activityId; }

    public function getTripMemberId() { return $this->tripMemberId; }
    public function setTripMemberId($tripMemberId) { $this->tripMemberId = $tripMemberId; }

    public function getCreatorMemberId() { return $this->creatorMemberId; }
    public function setCreatorMemberId($creatorMemberId) { $this->creatorMemberId = $creatorMemberId; }

    public function getDeletedAt() { return $this->deletedAt; }
    public function setDeletedAt($deletedAt) { $this->deletedAt = $deletedAt; }

    public function fill(array $row)
    {
        $this->id = $row['id'];
        $this->itemId = $row['itemId'];
        $this->name = $row['name'];
        $this->quantity = $row['quantity'];
        $this->description = $row['description'];
        $this->isPacked = (bool)$row['isPacked'];
        $this->activityId = $row['activityId'];
        $this->tripMemberId = $row['tripMemberId'];
        $this->creatorMemberId = $row['creatorMemberId'] ?? null;
        $this->deletedAt = $row['deletedAt'] ?? null;
    }

    public function create()
    {
        $this->itemId = uniqid('inv_');
        $sql = "INSERT INTO InventoryItem (itemId, name, quantity, description, isPacked, activityId, tripMemberId, creatorMemberId)
                VALUES (:itemId, :name, :quantity, :description, :isPacked, :activityId, :tripMemberId, :creatorMemberId)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':itemId' => $this->itemId,
            ':name' => $this->name,
            ':quantity' => $this->quantity,
            ':description' => $this->description,
            ':isPacked' => $this->isPacked ? 1 : 0,
            ':activityId' => $this->activityId,
            ':tripMemberId' => $this->tripMemberId,
            ':creatorMemberId' => $this->creatorMemberId
        ]);
        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM InventoryItem WHERE id = :id AND deletedAt IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->fill($row);
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = "UPDATE InventoryItem SET name = :name, quantity = :quantity, description = :description, 
                isPacked = :isPacked, activityId = :activityId, tripMemberId = :tripMemberId,
                creatorMemberId = :creatorMemberId
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $this->name,
            ':quantity' => $this->quantity,
            ':description' => $this->description,
            ':isPacked' => $this->isPacked ? 1 : 0,
            ':activityId' => $this->activityId,
            ':tripMemberId' => $this->tripMemberId,
            ':creatorMemberId' => $this->creatorMemberId,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "UPDATE InventoryItem SET deletedAt = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public static function getByItineraryId($itineraryId)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT ii.*, a.name as activityName, u.firstName, u.lastName, u.profileImage 
                FROM InventoryItem ii
                JOIN Activity a ON ii.activityId = a.id
                LEFT JOIN TripMember tm ON ii.tripMemberId = tm.id
                LEFT JOIN User u ON tm.userId = u.id
                WHERE a.itineraryId = :itineraryId
                AND ii.deletedAt IS NULL
                ORDER BY a.startTime ASC, ii.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByActivityId($activityId)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM InventoryItem WHERE activityId = :activityId AND deletedAt IS NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute([':activityId' => $activityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
