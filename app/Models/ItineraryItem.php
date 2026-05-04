<?php
namespace App\Models;

use Core\Database;

abstract class ItineraryItem
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
    private $itineraryObject = null;
    private $creatorObject   = null;

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

    public function getItinerary()
    {

        // if ($this->itineraryObject === null) {
        //     $this->itineraryObject = (new Itinerary())->read($this->itineraryId);
        // }

        return $this->itineraryObject;
    }

    public function getCreator()
    {

        // if ($this->creatorObject === null) {
        //     $this->creatorObject = (new TripMember())->read($this->creatorId);
        // }

        return $this->creatorObject;
    }
}
