<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;

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

        $this->view('finance/dashboard', [
            'itineraryId' => $itineraryId,
            'baseCurrency' => $finance->getBaseCurrency(),
            'totalBudget' => $finance->getTotalBudgetLimit(),
            'actualSpending' => $actualSpending,
            'alert' => $budgetAlert
        ]);
    }
}