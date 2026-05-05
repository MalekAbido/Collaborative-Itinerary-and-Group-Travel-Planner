<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;
use App\Models\GroupFund;
use App\Models\Itinerary;
use App\Helpers\Auth;
use App\Models\TripMember;
use App\Helpers\Session;

class FinanceController extends Controller
{
    public function dashboard($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'You must be a member of this itinerary to view its finances.');
            header("Location: /dashboard");
            exit;
        }

        $finance = new TripFinance();
        $isFound = $finance->readByItinerary($itineraryId);

        if (!$isFound) {
            $finance->create($itineraryId, 'USD', 0);
            $finance->readByItinerary($itineraryId);
        }

        $itineraryModel = new Itinerary();
        $trip = $itineraryModel->findByIdNumeric($itineraryId);
        $tripStringId = $trip ? $trip['itineraryId'] : '';

        $actualSpending = $finance->getActualSpending();
        $budgetAlert = $finance->checkBudgetAlert();

        // --- FETCH REAL GROUP FUND DATA ---
        $groupFund = new GroupFund();
        $isFundFound = $groupFund->readByTripFinanceId($finance->getId());
        
        $fundId = $isFundFound ? $groupFund->getFundId() : null;
        $kittyBalance = $isFundFound ? $groupFund->getCurrentBalance() : 0;
        $contributions = $isFundFound ? $groupFund->getContributions() : [];

        $this->view('finance/dashboard', [
            'itineraryId' => $itineraryId,
            'tripStringId' => $tripStringId,
            'baseCurrency' => $finance->getBaseCurrency(),
            'totalBudget' => $finance->getTotalBudgetLimit(),
            'actualSpending' => $actualSpending,
            'alert' => $budgetAlert,
            'fundId' => $fundId, 
            'kittyBalance' => $kittyBalance,
            'contributions' => $contributions,
            'expenses' => $finance->getExpenses(),
            'userRole' => $member->getRole()
        ]);
    }

    public function updateSettings($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'You must be a member of this itinerary to update its settings.');
            header("Location: /dashboard");
            exit;
        }

        Auth::requireRole('Editor', $member->getRole());

        $budget = floatval($_POST['budgetLimit'] ?? 0);
        $currency = $_POST['baseCurrency'] ?? 'USD';

        $finance = new TripFinance();
        $finance->updateSettingsByItineraryId($itineraryId, $budget, $currency);

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }

    public function createGroupFund($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'You must be a member of this itinerary to create a group fund.');
            header("Location: /dashboard");
            exit;
        }

        Auth::requireRole('Editor', $member->getRole());

        $finance = new TripFinance();
        if ($finance->readByItinerary($itineraryId)) {
            $groupFund = new GroupFund();
            if (!$groupFund->readByTripFinanceId($finance->getId())) {
                $groupFund->create($finance->getId());
            }
        }
        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}