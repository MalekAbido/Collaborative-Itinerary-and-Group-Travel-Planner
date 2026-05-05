<?php
namespace App\Models;

use App\Models\Subtrip;
use Core\Database;
use PDO;

class Itinerary
{
    private $db;

    private $itineraryId;
    private $title;
    private $description;
    private $startDate;
    private $endDate;
    private $tripMembers = [];
    private $invitations = [];
    private $historyLog;
    private $tripFinance;
    private $activities = null;
    private $subtrips   = null;

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

    public function create($title, $description, $startDate, $endDate)
    {
        $this->itineraryId = uniqid('trip_');

        $sql = "INSERT INTO Itinerary (itineraryId, title, description, startDate, endDate)
                VALUES (:id, :title, :desc, :start, :end)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'    => $this->itineraryId,
            ':title' => $title,
            ':desc'  => $description,
            ':start' => $startDate,
            ':end'   => $endDate,
        ]);

        return $this->itineraryId;
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
            return true;
        }

        return false;
    }

    public function findByIdNumeric($id)
    {
        $sql  = "SELECT * FROM Itinerary WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $description, $startDate, $endDate)
    {
        $sql = "UPDATE Itinerary
                SET title = :title, description = :desc, startDate = :start, endDate = :end
                WHERE itineraryId = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'       => $this->title,
            ':description' => $this->description,
            ':startDate'   => $this->startDate,
            ':endDate'     => $this->endDate,
            ':id'          => $this->id,
        ]);
    }

    public function delete($id)
    {
        $sql  = "DELETE FROM Itinerary WHERE itineraryId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function addActivity($activity)
    {}

    public function removeActivity($activityId)
    {}

    public function addSubtrip($subtrip)
    {}

    public function removeSubtrip($subtripId)
    {}

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

    /**
     * Fetches all subtrips attached to this itinerary.
     */
    public function getSubtrips()
    {

        if ($this->subtrips === null) {
            $this->subtrips = Subtrip::getAllByItineraryId($this->id);

            if ($this->subtrips === false) {
                $this->subtrips = [];
            }
        }

        return $this->subtrips;
    }
}
