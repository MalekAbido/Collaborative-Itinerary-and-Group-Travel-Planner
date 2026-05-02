<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Expense;
use App\Models\ExpenseShare;

class ExpenseController extends Controller 
{
    public function createExpense() 
    {
        $financeId = $_POST['financeId'] ?? '';
        $payerId = $_POST['payerId'] ?? '';
        $splitMethod = $_POST['splitMethod'] ?? 'EVEN';
        $totalAmount = (float)($_POST['amount'] ?? 0); 

        $details = [
            'description' => $_POST['description'],
            'category' => $_POST['category'],
            'shares' => $_POST['shares']
        ];

        if ($splitMethod == 'UNEVEN') {
            $sumOfShares = array_sum($details['shares']);
            if (abs($sumOfShares - $totalAmount > 0.01)) {
                die("Error shares must equal total");
            }
        }

        $expenseModel = new Expense();
        $expenseId = $expenseModel->create([
            'financeId' => $financeId,
            'amount' => $totalAmount,
            'description' => $details['description'],
            'category' => $details['category'],
            'splitMethod' => $splitMethod
        ]);

        $shareModel = new ExpenseShare();

        if($splitMethod === 'EVEN') {
            $memberCount = count($details['shares']);
            $evenSplitAmount = $totalAmount / $memberCount; 
            
            foreach ($details['shares'] as $memberId => $dummyValue) {
                $isPayer = ($memberId === $payerId);
                $shareModel->create($expenseId, $memberId, $evenSplitAmount, $isPayer);
            }
        } elseif ($splitMethod === 'UNEVEN') {
            foreach ($details['shares'] as $memberId => $amountOwed) {
                $isPayer = ($memberId === $payerId);
                $shareModel->create($expenseId, $memberId, $amountOwed, $isPayer);
            }
        }
        
        
        header("Location: /finance/expense/details?id=" . $expenseId);
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
        $shares = $shareModel->findByExpenseId($expenseId);

        if (!$expense) {
            die("Expense not found.");
        }

        $payer = null;
        $debtors = [];

        foreach ($shares as $share) {
            if ($share['isPayer']) {
                $payer = $share;
            } else {
                $debtors[] = $share;
            }
        }

        $this->view('expenses/details', [
            'expense' => $expense,
            'payer' => $payer,
            'debtors' => $debtors
        ]);
    }
}