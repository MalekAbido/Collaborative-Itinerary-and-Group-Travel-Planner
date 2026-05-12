<?php
namespace App\Models;

use App\Models\Subtrip;
use Core\Database;
use PDO;

class Itinerary
{
    private $db;

    private $id;
    private $itineraryItems;

    private $itineraryId;
    private $title;
    private $description;
    private $startDate;
    private $endDate;
    private $coverImage;
    private $tripMembers = [];
    private $invitations = [];
    private $historyLog;
    private $tripFinance;
    private $activities = null;

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

    public function getItineraryId()
    {
        return $this->itineraryId;
    }

    public function setItineraryId($itineraryId)
    {
        $this->itineraryId = $itineraryId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getTripMembers()
    {
        return $this->tripMembers;
    }

    public function setTripMembers($tripMembers)
    {
        $this->tripMembers = $tripMembers;
    }

    public function getInvitations()
    {
        return $this->invitations;
    }

    public function setInvitations($invitations)
    {
        $this->invitations = $invitations;
    }

    public function getHistoryLog()
    {
        return $this->historyLog;
    }

    public function setHistoryLog($historyLog)
    {
        $this->historyLog = $historyLog;
    }

    public function getTripFinance()
    {
        return $this->tripFinance;
    }

    public function setTripFinance($tripFinance)
    {
        $this->tripFinance = $tripFinance;
    }

    public function getItineraryItems()
    {
        return $this->itineraryItems;
    }

    public function setItineraryItems($itineraryItems)
    {
        $this->itineraryItems = $itineraryItems;
    }

    public function create($title, $description, $startDate, $endDate, $coverImage = null)
    {
        $this->itineraryId = uniqid('trip_');

        $sql = "INSERT INTO Itinerary (itineraryId, title, description, startDate, endDate, coverImage)
                VALUES (:id, :title, :desc, :start, :end, :coverImage)";

        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            ':id'         => $this->itineraryId,
            ':title'      => $title,
            ':desc'       => $description,
            ':start'      => $startDate,
            ':end'        => $endDate,
            ':coverImage' => $coverImage
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
            return $this->id;
        }

        return false;
    }

    public function read($id)
    {
        $sql  = "SELECT * FROM Itinerary WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id          = $data['id'];
            $this->itineraryId = $data['itineraryId'];
            $this->title       = $data['title'];
            $this->description = $data['description'];
            $this->startDate   = $data['startDate'];
            $this->endDate     = $data['endDate'];
            $this->coverImage  = $data['coverImage'];
            return true;
        }

        return false;
    }

    public function findById($id)
    {
        $sql  = "SELECT * FROM Itinerary WHERE itineraryId = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByIdNumeric($id)
    {
        $sql  = "SELECT * FROM Itinerary WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $description, $startDate, $endDate, $coverImage = null)
    {
        if ($coverImage === null) {
            $sql = "UPDATE Itinerary
                    SET title = :title, description = :desc, startDate = :start, endDate = :end
                    WHERE itineraryId = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':title' => $title,
                ':desc'  => $description,
                ':start' => $startDate,
                ':end'   => $endDate,
                ':id'    => $id,
            ]);
        } else {
            $sql = "UPDATE Itinerary
                    SET title = :title, description = :desc, startDate = :start, endDate = :end, coverImage = :coverImage
                    WHERE itineraryId = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':title'      => $title,
                ':desc'       => $description,
                ':start'      => $startDate,
                ':end'        => $endDate,
                ':coverImage' => $coverImage,
                ':id'         => $id,
            ]);
        }
    }

    public function delete($id)
    {
        $trip = $this->findById($id);
        if (!$trip) {
            return false;
        }
        $numericId = $trip['id'];

        try {
            $this->db->beginTransaction();

            $this->db->prepare("DELETE FROM Vote WHERE pollId IN (SELECT id FROM Poll WHERE activityId IN (SELECT id FROM Activity WHERE itineraryId = ?))")->execute([$numericId]);
            $this->db->prepare("DELETE FROM AttendanceMember WHERE attendanceListId IN (SELECT id FROM AttendanceList WHERE activityId IN (SELECT id FROM Activity WHERE itineraryId = ?))")->execute([$numericId]);
            
            $this->db->prepare("DELETE FROM Poll WHERE activityId IN (SELECT id FROM Activity WHERE itineraryId = ?)")->execute([$numericId]);
            $this->db->prepare("DELETE FROM AttendanceList WHERE activityId IN (SELECT id FROM Activity WHERE itineraryId = ?)")->execute([$numericId]);
            $this->db->prepare("DELETE FROM InventoryItem WHERE activityId IN (SELECT id FROM Activity WHERE itineraryId = ?)")->execute([$numericId]);

            $this->db->prepare("DELETE FROM Activity WHERE itineraryId = ?")->execute([$numericId]);

            $this->db->prepare("DELETE FROM ExpenseShare WHERE expenseId IN (SELECT id FROM Expense WHERE tripFinanceId IN (SELECT id FROM TripFinance WHERE itineraryId = ?))")->execute([$numericId]);
            $this->db->prepare("DELETE FROM Expense WHERE tripFinanceId IN (SELECT id FROM TripFinance WHERE itineraryId = ?)")->execute([$numericId]);
            $this->db->prepare("DELETE FROM FundContribution WHERE groupFundId IN (SELECT id FROM GroupFund WHERE tripFinanceId IN (SELECT id FROM TripFinance WHERE itineraryId = ?))")->execute([$numericId]);
            $this->db->prepare("DELETE FROM GroupFund WHERE tripFinanceId IN (SELECT id FROM TripFinance WHERE itineraryId = ?)")->execute([$numericId]);
            $this->db->prepare("DELETE FROM TripFinance WHERE itineraryId = ?")->execute([$numericId]);

            $this->db->prepare("DELETE FROM HistoryLogEntry WHERE historyLogId IN (SELECT id FROM HistoryLog WHERE itineraryId = ?)")->execute([$numericId]);
            $this->db->prepare("DELETE FROM HistoryLog WHERE itineraryId = ?")->execute([$numericId]);

            $this->db->prepare("DELETE FROM SettlementPayment WHERE itineraryId = ?")->execute([$numericId]);
            $this->db->prepare("DELETE FROM Invitation WHERE itineraryId = ?")->execute([$numericId]);

            $this->db->prepare("DELETE FROM TripMember WHERE itineraryId = ?")->execute([$numericId]);
            
            $sql  = "DELETE FROM Itinerary WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $numericId]);

            $this->db->commit();
            return $result;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Failed to delete itinerary: " . $e->getMessage());
            return false;
        }
    }

    public function getActivities()
    {

        if ($this->activities === null) {
            $this->activities = Activity::getAllByItineraryId($this->id);

            if ($this->activities === false) {
                $this->activities = [];
            }
        }

        return $this->activities;
    }
}