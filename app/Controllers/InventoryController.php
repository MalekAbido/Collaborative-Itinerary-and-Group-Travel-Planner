<?php

namespace App\Controllers;

use Core\Controller;
use App\Helpers\Auth;
use App\Constants\Messages;
use App\Helpers\HistoryLogger;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\InventoryItem;
use App\Models\Itinerary;
use App\Models\TripMember;
use App\Enums\TripMemberRole;
use App\Enums\ActivityStatus;
use App\Enums\TransactionType;

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
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /dashboard");
            exit;
        }

        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header("Location: /dashboard");
            exit;
        }

        $allItems = InventoryItem::getByItineraryId($itineraryId);
        
        // Filter activities: Only CONFIRMED and start time after Now
        $allActivities = Activity::getAllByItineraryId($itineraryId);
        $currentTime = time();
        $activities = array_filter($allActivities, function($activity) use ($currentTime) {
            return $activity->getActivityStatus() === ActivityStatus::CONFIRMED->value && 
                   strtotime($activity->getStartTime()) > $currentTime;
        });

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
            'currentMemberId' => $member->getId(),
            'currentMemberRole' => $member->getRole()
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

        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header("Location: /dashboard");
            exit;
        }

        // Validate Activity: Must be CONFIRMED and start time after Now
        $activity = Activity::getByActivityId($activityId);
        if (!$activity || $activity->getItineraryId() != $itineraryId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/inventory/" . $itineraryId);
            exit;
        }

        if ($activity->getActivityStatus() !== ActivityStatus::CONFIRMED->value || strtotime($activity->getStartTime()) <= time()) {
            Session::setFlash(Session::FLASH_ERROR, Messages::INV_FUTURE_CONFIRMED_ONLY);
            header("Location: /itinerary/inventory/" . $itineraryId);
            exit();
        }

        $item = new InventoryItem();
        $item->setName($name);
        $item->setQuantity($quantity);
        $item->setDescription($description);
        $item->setActivityId($activityId);
        $item->setIsPacked(false);
        $item->setCreatorMemberId($member->getId());

        if ($item->create()) {
            header("Location: /itinerary/inventory/" . $itineraryId);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /itinerary/inventory/" . $itineraryId);
            exit;
        }
    }

    public function deleteInventoryItem()
    {
        $itemId = $_POST['itemId'] ?? null;
        $itineraryId = $_POST['itineraryId'] ?? null;

        if (!$itemId || !$itineraryId) {
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/dashboard'));
            exit();
        }

        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
            header("Location: /dashboard");
            exit;
        }

        $item = new InventoryItem();
        if ($item->read($itemId)) {
            // Check authorization: creator OR organizer/editor
            $isCreator = ($item->getCreatorMemberId() == $member->getId());
            $isManager = Auth::hasRole(TripMemberRole::EDITOR->value, $member->getRole());

            if ($isCreator || $isManager) {
                if ($item->delete()) {
                    HistoryLogger::log($itineraryId, TransactionType::REMOVED_INVENTORY_ITEM, $item, $member->getId());
                    Session::setFlash(Session::FLASH_SUCCESS, Messages::INVENTORY_REMOVED);
                } else {
                    Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
                }
            } else {
                Session::setFlash(Session::FLASH_ERROR, Messages::INVENTORY_ACCESS_DENIED);
            }
        }

        header("Location: /itinerary/inventory/" . $itineraryId);
        exit();
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
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header("Location: /dashboard");
            exit;
        }

        $item = new InventoryItem();
        if ($item->read($itemId)) {
            if ($item->getTripMemberId() === null) {
                $item->setTripMemberId($member->getId());
                if ($item->update()) {
                    HistoryLogger::log($itineraryId, TransactionType::VOLUNTEERED_FOR_ITEM, $item, $member->getId());
                    Session::setFlash(Session::FLASH_SUCCESS, Messages::INVENTORY_VOLUNTEERED);
                }
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
            Session::setFlash(Session::FLASH_ERROR, Messages::NOT_A_MEMBER);
            header("Location: /dashboard");
            exit;
        }

        $item = new InventoryItem();
        if ($item->read($itemId)) {
            if ($item->getTripMemberId() == $member->getId()) {
                $item->setTripMemberId(null);
                if ($item->update()) {
                    HistoryLogger::log($itineraryId, TransactionType::UNVOLUNTEERED_FOR_ITEM, $item, $member->getId());
                    Session::setFlash(Session::FLASH_INFO, Messages::INVENTORY_UNVOLUNTEERED);
                }
            }
        }

        header("Location: /itinerary/inventory/" . $itineraryId);
        exit();
    }
}
