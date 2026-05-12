<?php
namespace App\Controllers;

use App\Services\Auth;
use App\Services\Session;
use App\Constants\Messages;
use App\Services\HistoryLogger;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Enums\TransactionType;
use App\Models\TripFinance;
use App\Enums\SplitMethod;
use App\Enums\TripMemberRole;
use Core\Controller;

class ExpenseController extends Controller
{
    public function showAddForm($id)
    {
        $itineraryId     = $id;
        $tripMemberModel = new \App\Models\TripMember();
        $members         = $tripMemberModel->getAllByItineraryId($itineraryId);

        $financeModel = new \App\Models\TripFinance();
        $financeModel->readByItinerary($itineraryId);
        $financeId = $financeModel->getId();

        $groupFundBalance = 0;
        $groupFundModel   = new \App\Models\GroupFund();

        if ($groupFundModel->readByTripFinanceId($financeId)) {
            $groupFundBalance = $groupFundModel->getCurrentBalance();
        }

        $this->view('expenses/add', [
            'members'          => $members,
            'financeId'        => $financeId,
            'itineraryId'      => $id,
            'groupFundBalance' => $groupFundBalance,
            'baseCurrency'     => $financeModel->getBaseCurrency(),
            'activeTab'        => 'addExpense',
        ]);
    }

    public function createExpense()
    {
        Auth::requireLogin();

        $financeId    = $_POST['financeId'] ?? '';
        $payerId      = (int) ($_POST['payerId'] ?? 0);
        $splitMethod  = $_POST['splitMethod'] ?? SplitMethod::EVEN->value;
        $totalAmount  = (float) ($_POST['amount'] ?? 0);
        $currencyType = trim($_POST['currencyType'] ?? 'EGP');
        $isNonCash    = isset($_POST['isNonCash']) ? 1 : 0;
        $paidByKitty  = isset($_POST['paidByKitty']) ? 1 : 0;

        $details = [
            'description' => $_POST['description'] ?? '',
            'category'    => $_POST['category'] ?? '',
            'shares'      => $_POST['shares'] ?? [],
        ];

        $financeModel = new \App\Models\TripFinance();

        if (! $financeModel->read($financeId)) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /dashboard");
            exit;
        }

        if ($paidByKitty === 1) {
            $itineraryId     = $financeModel->getItineraryId();
            $tripMemberModel = new \App\Models\TripMember();
            $currentMember   = Auth::requireMembership($itineraryId);

            $groupFundModel = new \App\Models\GroupFund();
            $fundExists     = $groupFundModel->readByTripFinanceId($financeId);

            if (! $fundExists) {
                Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
                header("Location: /finance/dashboard/" . $itineraryId);
                exit;
            }

            if ($groupFundModel->getCurrentBalance() < $totalAmount) {
                $financeModel = new \App\Models\TripFinance();
                $financeModel->readByItinerary($itineraryId);
                $financeId = $financeModel->getId();

                $groupFundBalance = $groupFundModel->getCurrentBalance();

                $this->view('expenses/add', [
                    'members'          => $tripMemberModel->getAllByItineraryId($itineraryId),
                    'financeId'        => $financeId,
                    'itineraryId'      => $itineraryId,
                    'groupFundBalance' => $groupFundBalance,
                    'error'            => 'Insufficient funds in the group kitty to cover this expense. Available: ' . number_format($groupFundBalance, 2),
                ]);
                return;
            } else {
                $groupFundModel->deductExpense($totalAmount);
            }
        }

        $isValid = $this->validateExpenseData($splitMethod, $totalAmount, $details['shares'], $paidByKitty);

        if (! $isValid) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $expenseModel = new Expense();
        $expenseId    = $expenseModel->create([
            'financeId'    => $financeId,
            'payerId'      => $payerId,
            'amount'       => $totalAmount,
            'currencyType' => $currencyType,
            'isNonCash'    => $isNonCash,
            'paidByKitty'  => $paidByKitty,
            'description'  => $details['description'],
            'category'     => $details['category'],
        ]);

        if ($expenseId) {
            $newExpense    = $expenseModel->findById($expenseId);
            $itineraryData = $newExpense->getItinerary();
            $itineraryId   = $itineraryData['itineraryId'] ?? null;
            HistoryLogger::log($itineraryId, TransactionType::ADDED_EXPENSE, $newExpense, $payerId);
        }

        if ($paidByKitty !== 1) {
            $shareModel = new ExpenseShare();

            if ($splitMethod === SplitMethod::EVEN->value) {
                $memberIds = array_keys($details['shares']);
                $memberCount = count($memberIds);
                $totalCents = (int) round($totalAmount * 100);
                $baseCents = intdiv($totalCents, $memberCount);
                $remainderCents = $totalCents % $memberCount;

                foreach ($memberIds as $index => $memberId) {
                    $shareCents = $baseCents + ($index < $remainderCents ? 1 : 0);
                    $shareAmount = round($shareCents / 100, 2);
                    $isPayer = ((int) $memberId === $payerId) ? 1 : 0;
                    $shareModel->create($expenseId, $memberId, $shareAmount, $isPayer);
                }
            } elseif ($splitMethod === SplitMethod::UNEVEN->value) {
                foreach ($details['shares'] as $memberId => $amountOwed) {
                    $isPayer = ((int) $memberId === $payerId) ? 1 : 0;
                    $shareModel->create($expenseId, $memberId, round((float) $amountOwed, 2), $isPayer);
                }
            }
        }

        $financeModel = new \App\Models\TripFinance();
        $financeModel->read($financeId);
        $itineraryId = $financeModel->getItineraryId();

        Session::setFlash(Session::FLASH_SUCCESS, Messages::EXPENSE_ADDED);
        header("Location: /finance/dashboard/" . $itineraryId);
        exit();
    }

    public function deleteExpense()
    {
        Auth::requireLogin();

        $expenseId = $_POST['expenseId'] ?? null;
        if (! $expenseId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /dashboard");
            exit;
        }

        $expenseModel = new Expense();
        $expense = $expenseModel->findById($expenseId);
        if (! $expense) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /dashboard");
            exit;
        }

        $itineraryData = $expense->getItinerary();
        $itineraryId   = $itineraryData['itineraryId'] ?? null;

        $tripMember = Auth::requireMembership($itineraryId);
        Auth::requireRole(TripMemberRole::EDITOR->value, $tripMember->getRole());

        if ($expense->delete($tripMember->getId())) {
            HistoryLogger::log($itineraryId, TransactionType::DELETED_EXPENSE, $expense, $tripMember->getId());
        }

        Session::setFlash(Session::FLASH_SUCCESS, Messages::EXPENSE_DELETED);
        header("Location: /finance/dashboard/" . $itineraryId);
        exit();
    }

    public function getExpenseDetails()
    {
        $expenseId = $_GET['id'] ?? null;

        if (! $expenseId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /dashboard");
            exit;
        }

        $expenseModel = new Expense();
        $shareModel   = new ExpenseShare();

        $expense = $expenseModel->findById($expenseId);

        if (! $expense) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /dashboard");
            exit;
        }

        $expense->loadShares($expenseId);
        $payer   = null;
        $debtors = [];

        foreach ($expense->expenseShares as $share) {
            if ($share->getIsPayer() == 1) {
                $payer = $share;
            } else {
                $debtors[] = $share;
            }
        }

        $financeId = $expense->getTripFinanceId();
        $finance   = new TripFinance();
        $finance->read($financeId);
        $this->view('expenses/details', [
            'expense'     => $expense,
            'payer'       => $payer,
            'debtors'     => $debtors,
            'itineraryId' => $finance->getItineraryId(),
            'activeTab'   => 'expense',
        ]);
    }

    public function refundExpense()
    {
        $expenseId      = $_POST['expenseId'] ?? null;
        $newRefundInput = (float) ($_POST['refundAmount'] ?? 0);

        if (! $expenseId) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: /dashboard");
            exit;
        }

        $expenseModel = new Expense();
        $expense      = $expenseModel->findById($expenseId);

        if (! $expense) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_NOT_FOUND);
            header("Location: /dashboard");
            exit;
        }

        if ($newRefundInput <= 0 || $newRefundInput > $expense->getAmount()) {
            Session::setFlash(Session::FLASH_ERROR, Messages::ERROR_GENERIC);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $expense->updateRefundedAmount($newRefundInput);

        $itineraryData = $expense->getItinerary();
        $itineraryId   = $itineraryData['itineraryId'] ?? null;

        Session::setFlash(Session::FLASH_SUCCESS, Messages::SUCCESS_GENERIC);
        header("Location: /finance/dashboard/" . $itineraryId);
        exit();
    }

    private function validateExpenseData($splitMethod, $totalAmount, $shares, $paidByKitty = 0)
    {

        if ($paidByKitty === 1) {
            return true;
        }

        if (empty($shares)) {
            return false;
        }

        if ($splitMethod === SplitMethod::UNEVEN->value) {
            $sumOfShares = array_sum($shares);

            if (abs($sumOfShares - $totalAmount) > 0.01) {
                return false;
            }
        }

        return true;
    }
}
