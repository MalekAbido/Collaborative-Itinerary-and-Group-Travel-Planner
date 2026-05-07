<?php

namespace App\Controllers;

use Core\Controller;
use App\Helpers\Auth;
use App\Models\Activity;
use App\Models\InventoryItem;
use App\Models\Itinerary;
use App\Models\TripMember;

class InventoryController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    public function showInventory(int $itineraryId)
    {
        $itineraryModel = new Itinerary();
        $itinerary = $itineraryModel->findByIdNumeric($itineraryId);

        if (!$itinerary) {
            die("Itinerary not found.");
        }

        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            die("You are not a member of this trip.");
        }

        $allItems = InventoryItem::getByItineraryId($itineraryId);
        $activities = Activity::getAllByItineraryId($itineraryId);

        $myVolunteers = [];
        $otherItems = [];

        foreach ($allItems as $item) {
            if ($item['tripMemberId'] == $member->getId()) {
                $myVolunteers[] = $item;
            } else {
                $otherItems[] = $item;
            }
        }

        $this->view('inventory/inventory', [
            'itinerary' => $itinerary,
            'itineraryId' => $itineraryId,
            'activeTab' => 'inventory',
            'myVolunteers' => $myVolunteers,
            'otherItems' => $otherItems,
            'activities' => $activities,
            'currentMemberId' => $member->getId()
        ]);
    }

    public function addInventoryItem()
    {
        $itineraryId = $_POST['itineraryId'] ?? null;
        $name = $_POST['name'] ?? '';
        $quantity = $_POST['quantity'] ?? 1;
        $description = $_POST['description'] ?? '';
        $activityId = $_POST['activityId'] ?? null;

        if (!$itineraryId || !$name || !$activityId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $item = new InventoryItem();
        $item->setName($name);
        $item->setQuantity($quantity);
        $item->setDescription($description);
        $item->setActivityId($activityId);
        $item->setIsPacked(false);

        if ($item->create()) {
            header("Location: /itinerary/inventory/" . $itineraryId);
        } else {
            die("Failed to add inventory item.");
        }
    }

    public function volunteerToBringItem()
    {
        $itemId = $_POST['itemId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;
        $userId = Auth::id();

        if (!$itemId || !$itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            die("Member not found.");
        }

        $item = new InventoryItem();
        if ($item->read($itemId)) {
            if ($item->getTripMemberId() === null) {
                $item->setTripMemberId($member->getId());
                $item->update();
            }
        }

        header("Location: /itinerary/inventory/" . $itineraryId);
        exit();
    }

    public function unvolunteer()
    {
        $itemId = $_POST['itemId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;
        $userId = Auth::id();

        if (!$itemId || !$itineraryId) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            die("Member not found.");
        }

        $item = new InventoryItem();
        if ($item->read($itemId)) {
            if ($item->getTripMemberId() == $member->getId()) {
                $item->setTripMemberId(null);
                $item->update();
            }
        }

        header("Location: /itinerary/inventory/" . $itineraryId);
        exit();
    }
}
