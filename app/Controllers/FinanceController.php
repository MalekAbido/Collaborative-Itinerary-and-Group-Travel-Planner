<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\TripFinance;
use App\Models\GroupFund;
use App\Models\Itinerary;
use App\Models\SettlementPayment;
use App\Enums\TransactionType;
use App\Enums\TripMemberRole;
use App\Services\Auth;
use App\Services\HistoryLogger;
use App\Models\TripMember;
use App\Services\Session;
use App\Constants\Messages;
use App\Services\FinanceService;

class FinanceController extends Controller
{
    public function dashboard($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::FINANCE_ACCESS_DENIED);
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
            Session::setFlash(Session::FLASH_ERROR, Messages::ITIN_MEMBER_ACCESS);
            header("Location: /dashboard");
            exit;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $member->getRole());

        $budget = floatval($_POST['budgetLimit'] ?? 0);

        $finance = new TripFinance();
        if ($finance->updateBudgetByItineraryId($itineraryId, $budget)) {
            Session::setFlash(Session::FLASH_SUCCESS, Messages::BUDGET_UPDATED);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }

    public function createGroupFund($itineraryId)
    {
        Auth::requireLogin();
        $userId = Auth::id();

        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);
        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ITIN_MEMBER_ACCESS);
            header("Location: /dashboard");
            exit;
        }

        Auth::requireRole(TripMemberRole::EDITOR->value, $member->getRole());

        $finance = new TripFinance();
        if ($finance->readByItinerary($itineraryId)) {
            $groupFund = new GroupFund();
            if (!$groupFund->readByTripFinanceId($finance->getId())) {
                if ($groupFund->create($finance->getId())) {
                    Session::setFlash(Session::FLASH_SUCCESS, Messages::GROUP_FUND_CREATED);
                }
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
            Session::setFlash(Session::FLASH_ERROR, Messages::ITIN_MEMBER_ACCESS);
            header("Location: /dashboard");
            exit;
        }

        $fromMemberId = (int) ($_POST['fromMemberId'] ?? 0);
        $toMemberId = (int) ($_POST['toMemberId'] ?? 0);
        $amount = round((float) ($_POST['amount'] ?? 0), 2);

        if ($fromMemberId !== $member->getId()) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ACCESS_DENIED);
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        if ($amount <= 0 || $toMemberId <= 0) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /finance/dashboard/" . $itineraryId);
            exit;
        }

        $settlement = new SettlementPayment();
        $settlementId = $settlement->create($fromMemberId, $toMemberId, $itineraryId, $amount);

        if ($settlementId) {
            HistoryLogger::log($itineraryId, TransactionType::SETTLEMENT_PAID, $settlement, $member->getId());
            Session::setFlash(Session::FLASH_SUCCESS, Messages::SETTLEMENT_PAID);
        } else {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
        }

        header("Location: /finance/dashboard/" . $itineraryId);
        exit;
    }
}
