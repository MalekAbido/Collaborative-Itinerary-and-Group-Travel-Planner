<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;
use App\Models\GroupFund; // Make sure to import this!

class FinanceController extends Controller
{
    public function dashboard($itineraryId)
    {
        $finance = new TripFinance();
        $isFound = $finance->readByItinerary($itineraryId);

        if (!$isFound) {
            die("<h2>Error: Finance profile not found for Itinerary ID: {$itineraryId}</h2>");
        }

        $actualSpending = $finance->getActualSpending();
        $budgetAlert = $finance->checkBudgetAlert();

        // --- FETCH REAL GROUP FUND DATA ---
        $groupFund = new GroupFund();
        $isFundFound = $groupFund->readByTripFinanceId($finance->getId());
        
        $fundId = $isFundFound ? $groupFund->getFundId() : null;
        $kittyBalance = $isFundFound ? $groupFund->getCurrentBalance() : 0;

        $this->view('finance/dashboard', [
            'itineraryId' => $itineraryId,
            'baseCurrency' => $finance->getBaseCurrency(),
            'totalBudget' => $finance->getTotalBudgetLimit(),
            'actualSpending' => $actualSpending,
            'alert' => $budgetAlert,
            'fundId' => $fundId, 
            'kittyBalance' => $kittyBalance 
        ]);
    }
}