<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;
use App\Models\GroupFund;
use App\Models\Itinerary;
use App\Models\SettlementPayment;
use App\Enums\TransactionType;
use App\Enums\TripMemberRole;
use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Models\TripMember;
use App\Helpers\Session;
use App\Services\FinanceService;

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

        $groupFund = new GroupFund();
        $isFundFound = $groupFund->readByTripFinanceId($finance->getId());

        $fundId = $isFundFound ? $groupFund->getFundId() : null;
        $kittyBalance = $isFundFound ? $groupFund->getCurrentBalance() : 0;
        $contributions = $isFundFound ? $groupFund->getContributions() : [];

        $financeService = new FinanceService();
        $settlementResult = $financeService->getTripSettlementLedger($itineraryId);

        // Fetch all members (active and deleted) for the mapping
        $memberModel = new TripMember();
        $allMembers = $memberModel->getAllByItineraryId($itineraryId, true);

        $memberMap = [];
        foreach ($allMembers as $memberRow) {
            $name = trim($memberRow['firstName'] . ' ' . $memberRow['lastName']);
            if ($memberRow['deletedAt'] !== null) {
                $name .= ' (Former Member)';
            }
            $memberMap[$memberRow['memberId']] = $name;
        }

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
            'settlementResult' => $settlementResult,
            'memberMap' => $memberMap,
            'userRole' => $member->getRole(),
            'currentMemberId' => $member->getId(),
            'activeTab' => 'finance'
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

        Auth::requireRole(TripMemberRole::EDITOR->value, $member->getRole());

        $budget = floatval($_POST['budgetLimit'] ?? 0);

        $finance = new TripFinance();
        $finance->updateBudgetByItineraryId($itineraryId, $budget);

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

        Auth::requireRole(TripMemberRole::EDITOR->value, $member->getRole());

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

    public function markSettlementPaid($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (! $member) {
            Session::setFlash(Session::FLASH_ERROR, 'You must be a member of this itinerary to settle payments.');
            header("Location: /dashboard");
            exit;
        }

        $fromMemberId = (int) ($_POST['fromMemberId'] ?? 0);
        $toMemberId = (int) ($_POST['toMemberId'] ?? 0);
        $amount = round((float) ($_POST['amount'] ?? 0), 2);

        if ($fromMemberId !== $member->getId()) {
            Session::setFlash(Session::FLASH_ERROR, 'Only the payer can confirm this settlement.');
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        if ($amount <= 0 || $toMemberId <= 0) {
            Session::setFlash(Session::FLASH_ERROR, 'Invalid settlement payment information.');
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        $settlement = new SettlementPayment();
        $settlementId = $settlement->create($fromMemberId, $toMemberId, $itineraryId, $amount);

        if ($settlementId) {
            HistoryLogger::log($itineraryId, TransactionType::SETTLEMENT_PAID, $settlement, $member->getId());
            Session::setFlash(Session::FLASH_SUCCESS, 'Settlement marked as paid.');
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Unable to record the settlement payment.');
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}