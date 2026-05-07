<?php
namespace App\Models;

use Core\Database;
use PDO;
use \App\Models\AttendanceList;
use \App\Models\Location;

class Activity
{
    private $db;
    private $id;
    private $itemId;
    private $name;
    private $description;
    private $startTime;
    private $endTime;
    private $itineraryId;
    private $tripMemberId;
    private $category;
    private $activityStatus;
    private $locationId;
    private $isAnonymous = false;
    private $bannerImage;
    private $attendanceListObject = null;
    private $locationObject       = null;
    private $itineraryObject      = null;
    private $creatorObject        = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId($activityId)
    {
        $this->itemId = $activityId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    public function getItineraryId()
    {
        return $this->itineraryId;
    }

    public function setItineraryId($itineraryId)
    {
        $this->itineraryId = $itineraryId;
    }

    public function getTripMemberId()
    {
        return $this->tripMemberId;
    }

    public function setTripMemberId($tripMemberId)
    {
        $this->tripMemberId = $tripMemberId;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    public function setBannerImage($bannerImage)
    {
        $this->bannerImage = $bannerImage;
    }

    public function getActivityStatus()
    {
        return $this->activityStatus;
    }

    public function setActivityStatus($activityStatus)
    {
        $this->activityStatus = $activityStatus;
    }

    public function getLocationId()
    {
        return $this->locationId;
    }

    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;
    }

    public function getIsAnonymous()
    {
        return (bool) $this->isAnonymous;
    }

    public function setIsAnonymous($isAnonymous)
    {
        $this->isAnonymous = $isAnonymous;
    }

    public function fill(array $row)
    {
        $this->setId($row['id']);
        $this->setItemId($row['itemId']);
        $this->setName($row['name']);
        $this->setDescription($row['description']);
        $this->setStartTime($row['startTime']);
        $this->setEndTime($row['endTime']);
        $this->setCategory($row['category']);
        $this->setActivityStatus($row['status']);
        $this->setItineraryId($row['itineraryId']);
        $this->setTripMemberId($row['tripMemberId']);
        $this->setLocationId($row['locationId']);
        $this->setIsAnonymous($row['isAnonymous'] ?? false);
        $this->setBannerImage($row['bannerImage'] ?? null);
    }

    public static function getByActivityId($activityId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Activity WHERE id = :activityId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':activityId' => $activityId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $activity = new self();
            $activity->fill($data);
            return $activity;
        }

        return null;
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();

        if (empty($this->itemId)) {
            $this->itemId = uniqid('act_');
        }

        $sql = "INSERT INTO Activity (itemId, name, description, startTime, endTime, category, status, itineraryId, tripMemberId, locationId, isAnonymous, bannerImage)
                VALUES (:itemId, :name, :description, :startTime, :endTime, :category, :status, :itineraryId, :tripMemberId, :locationId, :isAnonymous, :bannerImage)";

        $stmt    = $db->prepare($sql);
        $success = $stmt->execute([
            ':itemId'       => $this->itemId,
            ':name'         => $this->name,
            ':description'  => $this->description,
            ':startTime'    => $this->startTime,
            ':endTime'      => $this->endTime,
            ':category'     => $this->category,
            ':status'       => $this->activityStatus,
            ':itineraryId'  => $this->itineraryId,
            ':tripMemberId' => $this->tripMemberId,
            ':locationId'   => $this->locationId,
            ':isAnonymous'  => $this->isAnonymous ? 1 : 0,
            ':bannerImage'  => $this->bannerImage,
        ]);

        if ($success) {
            $this->id = $db->lastInsertId();
        }

        return $success;
    }

    public function getConflictingConfirmedActivities()
    {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Activity
                WHERE itineraryId = :itineraryId
                AND status = 'Confirmed'
                AND (startTime < :endTime AND endTime > :startTime)";

        $params = [
            ':itineraryId' => $this->itineraryId,
            ':startTime'   => $this->startTime,
            ':endTime'     => $this->endTime,
        ];

        if ($this->id) {
            $sql           .= " AND id != :id";
            $params[':id']  = $this->id;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $data       = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $activities = [];

        foreach ($data as $row) {
            $activity = new self();
            $activity->fill($row);
            $activities[] = $activity;
        }

        return $activities;
    }

    public function read($id)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Activity WHERE id = :activityId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':activityId' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    public static function getByIdAndItinerary($activityId, $itineraryId)
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM Activity WHERE id = :id AND itineraryId = :itineraryId LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id'          => $activityId,
            ':itineraryId' => $itineraryId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $activity = new self();
            $activity->fill($row);

            return $activity;
        }

        return null;
    }

    public function delete()
    {
        $db      = Database::getInstance()->getConnection();
        $sql     = "UPDATE Activity SET status = 'Removed' WHERE id = :id";
        $stmt    = $db->prepare($sql);
        $success = $stmt->execute([':id' => ($this->id)]);
        return $success;
    }

    public static function getAllByItineraryId($itineraryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Activity WHERE itineraryId = :itineraryId AND status != 'Removed' ORDER BY startTime ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $data            = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $activityObjects = [];

        foreach ($data as $row) {
            $activity = new self();
            $activity->fill($row);
            $activityObjects[] = $activity;
        }

        return $activityObjects;
    }

    public static function getAllByStatusAndItinerary($status, $itineraryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Activity WHERE status = :status AND itineraryId = :itineraryId AND status != 'Removed' ORDER BY startTime ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':status'      => $status,
            ':itineraryId' => $itineraryId,
        ]);
        $data            = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $activityObjects = [];

        foreach ($data as $row) {
            $activity = new self();
            $activity->fill($row);
            $activityObjects[] = $activity;
        }

        return $activityObjects;
    }

    public function updateStatus($status)
    {
        $db                   = Database::getInstance()->getConnection();
        $sql                  = "UPDATE Activity SET status = :status WHERE id = :id";
        $stmt                 = $db->prepare($sql);
        $this->activityStatus = $status;
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $this->id,
        ]);
    }

    public function getItinerary()
    {

        if ($this->itineraryObject === null) {
            $this->itineraryObject = (new Itinerary())->read($this->itineraryId);
        }

        return $this->itineraryObject;
    }

    public function getTripMember()
    {

        if ($this->creatorObject === null && $this->getTripMemberId()) {
            $member = new TripMember();

            if ($member->read($this->getTripMemberId())) {
                $this->creatorObject = $member;
            }
        }

        return $this->creatorObject;
    }

    public function getAttendanceList()
    {

        if ($this->attendanceListObject === null) {
            $this->attendanceListObject = AttendanceList::getByActivityId($this->id);
        }

        return $this->attendanceListObject;
    }

    public function getLocation()
    {

        if ($this->locationObject === null && $this->getLocationId()) {
            $location = new Location();

            if ($location->read($this->getLocationId())) {
                $this->locationObject = $location;
            }
        }

        return $this->locationObject;
    }
}
