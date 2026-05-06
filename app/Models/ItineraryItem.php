<?php
namespace App\Models;

use App\Models\TripMember;
use Core\Database;

abstract class ItineraryItem
{
    protected $db;
    protected $id;
    protected $itemId;
    protected $name;
    protected $description;
    protected $startTime;
    protected $endTime;
    protected $itineraryId;
    protected $tripMemberId;
    protected $itineraryObject = null;
    protected $creatorObject   = null;

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
}
