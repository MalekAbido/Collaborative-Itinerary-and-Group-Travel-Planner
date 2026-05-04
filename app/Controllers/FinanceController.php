<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;
use App\Models\GroupFund;
use App\Models\Itinerary;

class FinanceController extends Controller
{
    public function dashboard($itineraryId)
    {
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
            'contributions' => $contributions
        ]);
    }

    public function updateSettings($itineraryId)
    {
        $budget = floatval($_POST['budgetLimit'] ?? 0);
        $currency = $_POST['baseCurrency'] ?? 'USD';

        $finance = new TripFinance();
        $finance->updateSettingsByItineraryId($itineraryId, $budget, $currency);

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }

    public function createGroupFund($itineraryId)
    {
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