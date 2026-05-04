<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Expense;
use App\Models\ExpenseShare;

class ExpenseController extends Controller 
{
    public function showAddForm() 
    {
        // $itineraryId = $_GET['itineraryId'] ?? 1; 

        // $tripMemberModel = new \App\Models\TripMember(); needs to be created

        // $members = $tripMemberModel->findByItineraryId($itineraryId);

        // $this->view('expenses/add', [
        //     'members' => $members
        // ]);

        $this->view('expenses/add');
    }

    public function createExpense() 
    {
        $financeId = $_POST['financeId'] ?? '';
        $payerId = (int)($_POST['payerId'] ?? 0);
        $splitMethod = $_POST['splitMethod'] ?? 'EVEN';
        $totalAmount = (float)($_POST['amount'] ?? 0);
        $currencyType = trim($_POST['currencyType'] ?? 'USD');
        $isNonCash = isset($_POST['isNonCash']) ? 1 : 0;
        $paidByKitty = isset($_POST['paidByKitty']) ? 1 : 0;
        
        $details = [
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? '',
            'shares' => $_POST['shares'] ?? []
        ];

        $isValid = $this->validateExpenseData($splitMethod, $totalAmount, $details['shares']);
        if (!$isValid) {
            die("Error: Invalid expense data or shares do not equal the total amount.");
        }

        $expenseModel = new Expense();
        $expenseId = $expenseModel->create([
            'financeId'   => $financeId,
            'payerId'     => $payerId,
            'amount'      => $totalAmount,
            'currencyType'=> $currencyType,
            'isNonCash'   => $isNonCash,
            'paidByKitty' => $paidByKitty,
            'description' => $details['description'],
            'category'    => $details['category']
        ]);

        $shareModel = new ExpenseShare();

        if ($splitMethod === 'EVEN') {
            $memberCount = count($details['shares']);
            $evenSplitAmount = $totalAmount / $memberCount; 
            
            foreach ($details['shares'] as $memberId => $dummyValue) {
                $isPayer = ((int)$memberId === $payerId) ? 1 : 0; 
                $shareModel->create($expenseId, $memberId, $evenSplitAmount, $isPayer);
            }

        } elseif ($splitMethod === 'UNEVEN') {
            foreach ($details['shares'] as $memberId => $amountOwed) {
                $isPayer = ((int)$memberId === $payerId) ? 1 : 0;
                $shareModel->create($expenseId, $memberId, $amountOwed, $isPayer);
            }
        }
        
        header("Location: /finance/expense/details?id=" . $expenseId);
        exit();
    }

    public function deleteExpense() 
    {
        $expenseId = $_POST['expenseId'] ?? null;

        if (!$expenseId) {
            die("Error: No Expense ID provided.");
        }

        $expenseModel = new Expense();
        $shareModel = new ExpenseShare();

        // Verify expense exists
        $expense = $expenseModel->findById($expenseId);
        if (!$expense) {
            die("Error: Expense not found.");
        }

        $shareModel->deleteByExpenseId($expenseId);
        $expenseModel->delete($expenseId);

        header("Location: /finance/dashboard");
        exit();
    }

    public function getExpenseDetails() 
    {
        $expenseId = $_GET['id'] ?? null;

        if (!$expenseId) {
            die("Expense ID is required to view details.");
        }

        $expenseModel = new Expense();
        $shareModel = new ExpenseShare();

        $expense = $expenseModel->findById($expenseId);

        if (!$expense) {
            die("Expense not found.");
        }

        $expense->loadShares($expenseId);
        $payer = null;
        $debtors = [];

        foreach ($expense->expenseShares as $share) {
            if ($share->isPayer == 1) {
                $payer = $share;
            } else {
                $debtors[] = $share;
            }
        }
        
        $this->view('expenses/details', [
            'expense' => $expense,
            'payer'   => $payer,
            'debtors' => $debtors
        ]);
    }

    /**
     * @param string $splitMethod (EVEN or UNEVEN)
     * @param float $totalAmount The total bill amount
     * @param array $shares The array of member debts
     * @return bool Returns true if valid, false if invalid
     */

    private function validateExpenseData($splitMethod, $totalAmount, $shares) 
    {
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