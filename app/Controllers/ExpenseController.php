<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\TransactionType;
use App\Models\TripFinance;
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

        // Get group fund balance if it exists
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
            'activeTab'        => 'addExpense',
        ]);
    }

    public function createExpense()
    {
        Auth::requireLogin();

        $financeId    = $_POST['financeId'] ?? '';
        $payerId      = (int) ($_POST['payerId'] ?? 0);
        $splitMethod  = $_POST['splitMethod'] ?? 'EVEN';
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
            die("Error: Invalid finance record.");
        }

        if ($paidByKitty === 1) {
            $itineraryId     = $financeModel->getItineraryId();
            $tripMemberModel = new \App\Models\TripMember();
            $currentMember   = Auth::requireMembership($itineraryId);

            $groupFundModel = new \App\Models\GroupFund();
            $fundExists     = $groupFundModel->readByTripFinanceId($financeId);

            if (! $fundExists) {
                die("Error: There is no Group Fund set up for this trip yet.");
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
            die("Error: Invalid expense data or shares do not equal the total amount.");
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

            if ($splitMethod === 'EVEN') {
                $memberCount     = count($details['shares']);
                $evenSplitAmount = $totalAmount / $memberCount;

                foreach ($details['shares'] as $memberId => $dummyValue) {
                    $isPayer = ((int) $memberId === $payerId) ? 1 : 0;
                    $shareModel->create($expenseId, $memberId, $evenSplitAmount, $isPayer);
                }
            } elseif ($splitMethod === 'UNEVEN') {

                foreach ($details['shares'] as $memberId => $amountOwed) {
                    $isPayer = ((int) $memberId === $payerId) ? 1 : 0;
                    $shareModel->create($expenseId, $memberId, $amountOwed, $isPayer);
                }
            }
        }

        $financeModel = new \App\Models\TripFinance();
        $financeModel->read($financeId);
        $itineraryId = $financeModel->getItineraryId();

        header("Location: /finance/dashboard/" . $itineraryId . "?success=ADDED_EXPENSE");
        exit();
    }

    public function deleteExpense()
    {
        $itineraryData = $expense->getItinerary();
        $itineraryId   = $itineraryData['itineraryId'] ?? null;

        Auth::requireLogin();
        $tripMember = Auth::requireMembership($itineraryId);
        Auth::requireRole('Editor', $tripMember->getRole());

        $expenseId = $_POST['expenseId'] ?? null;

        if (! $expenseId) {
            die("Error: No Expense ID provided.");
        }

        $expenseModel = new Expense();
        // Verify expense exists
        $expense = $expenseModel->findById($expenseId);

        if (! $expense) {
            die("Error: Expense not found.");
        }

        if ($expense->delete($tripMember->getId())) {
            HistoryLogger::log($itineraryId, TransactionType::DELETED_EXPENSE, $expense, $tripMember);
        }

        header("Location: /finance/dashboard/" . $itineraryId . "?success=DELETED_EXPENSE");
        exit();
    }

    public function getExpenseDetails()
    {
        $expenseId = $_GET['id'] ?? null;

        if (! $expenseId) {
            die("Expense ID is required to view details.");
        }

        $expenseModel = new Expense();
        $shareModel   = new ExpenseShare();

        $expense = $expenseModel->findById($expenseId);

        if (! $expense) {
            die("Expense not found.");
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
            die("Error: No Expense ID provided.");
        }

        $expenseModel = new Expense();
        $expense      = $expenseModel->findById($expenseId);

        if (! $expense) {
            die("Error: Expense not found.");
        }

        if ($newRefundInput <= 0 || $newRefundInput > $expense->getAmount()) {
            die("Error: Invalid refund amount. It must be greater than 0 and cannot exceed the current expense amount.");
        }

        $expense->updateRefundedAmount($newRefundInput);

        $itineraryData = $expense->getItinerary();
        $itineraryId   = $itineraryData['itineraryId'] ?? null;

        header("Location: /finance/dashboard/" . $itineraryId . "?success=refund_applied");
        exit();
    }

    /**
     * @param string $splitMethod (EVEN or UNEVEN)
     * @param float $totalAmount The total bill amount
     * @param array $shares The array of member debts
     * @return bool Returns true if valid, false if invalid
     */

    private function validateExpenseData($splitMethod, $totalAmount, $shares, $paidByKitty = 0)
    {

        if ($paidByKitty === 1) {
            return true;
        }

        if (empty($shares)) {
            return false;
        }

        if ($splitMethod === 'UNEVEN') {
            $sumOfShares = array_sum($shares);

// the epsilon method to handle decimal inaccuracy
            if (abs($sumOfShares - $totalAmount) > 0.01) {
                return false;
            }
        }

        return true;
    }
}
