<?php

namespace App\Models;

use Core\Database;
use PDO;

class TripFinance
{
    private $db;
    private $id;
    private $financeId;
    private $itineraryId;
    private $baseCurrency;
    private $totalBudgetLimit;

    private $groupFund;     
    private $expenses = []; 

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getFinanceId() { return $this->financeId; }
    public function setFinanceId($financeId) { $this->financeId = $financeId; }

    public function getItineraryId() { return $this->itineraryId; }
    public function setItineraryId($itineraryId) { $this->itineraryId = $itineraryId; }

    public function getBaseCurrency() { return $this->baseCurrency; }
    public function setBaseCurrency($baseCurrency) { $this->baseCurrency = $baseCurrency; }

    public function getTotalBudgetLimit() { return $this->totalBudgetLimit; }
    public function setTotalBudgetLimit($limit) { $this->totalBudgetLimit = $limit; }

    public function getGroupFund() { return $this->groupFund; }
    public function setGroupFund(GroupFund $groupFund) { $this->groupFund = $groupFund; }

    public function getExpenses() { return $this->expenses; }
    public function setExpenses(array $expenses) { $this->expenses = $expenses; }
    
    public function getActualSpending()
    {
        $total = 0;
        foreach ($this->expenses as $expense) {
            $total += $expense->getAmount();
        }
        return $total;
    }

    public function checkBudgetAlert()
    {
        $currentSpending = $this->getActualSpending();

        if ($this->totalBudgetLimit > 0 && $currentSpending >= $this->totalBudgetLimit) {
            return [
                'status' => 'warning',
                'message' => "Budget limit exceeded. Actual: {$currentSpending} {$this->baseCurrency}, Limit: {$this->totalBudgetLimit} {$this->baseCurrency}"
            ];
        }
        return ['status' => 'ok', 'message' => 'Within budget'];
    }

    public function readByItinerary($itineraryId)
    {
        $sql = "SELECT * FROM TripFinance WHERE itineraryId = :itineraryId LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->financeId = $data['financeId'];
            $this->itineraryId = $data['itineraryId'];
            $this->baseCurrency = $data['baseCurrency'];
            $this->totalBudgetLimit = $data['budgetLimit']; 
            
            $this->loadExpensesFromDatabase();
            
            return true;
        }
        return false;
    }

    private function loadExpensesFromDatabase()
    {
        $this->expenses = []; 
        
        $sql = "SELECT * FROM Expense WHERE tripFinanceId = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $this->id]);
        
        $expenseRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($expenseRows as $row) {
            $this->expenses[] = (object) $row; 
        }
    }
}