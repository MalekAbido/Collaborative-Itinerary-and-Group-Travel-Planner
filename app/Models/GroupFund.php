<?php

namespace App\Models;

use Core\Database;
use PDO;

class GroupFund
{
    private $db;
    private $fundId;
    private $financeId;
    private $currentBalance;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getFundId() { return $this->fundId; }
    public function setFundId($fundId) { $this->fundId = $fundId; }

    public function getFinanceId() { return $this->financeId; }
    public function setFinanceId($financeId) { $this->financeId = $financeId; }

    public function getCurrentBalance() { return $this->currentBalance; }
    public function setCurrentBalance($currentBalance) { $this->currentBalance = $currentBalance; }

    public function addFunds($contributorId, $amount)
    {
        $this->currentBalance += $amount;

        $sql = "UPDATE GroupFund SET currentBalance = :balance WHERE fundId = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':balance' => $this->currentBalance,
            ':id' => $this->fundId
        ]);

        $logSql = "INSERT INTO Contribution (fundId, contributorId, amount, timestamp) 
                   VALUES (:fundId, :contributorId, :amount, NOW())";
        $logStmt = $this->db->prepare($logSql);
        return $logStmt->execute([
            ':fundId' => $this->fundId,
            ':contributorId' => $contributorId,
            ':amount' => $amount
        ]);
    }

    public function deductExpense($amount)
    {
        if ($amount > $this->currentBalance) {
            return false;
        }

        $this->currentBalance -= $amount;

        $sql = "UPDATE GroupFund SET currentBalance = :balance WHERE fundId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':balance' => $this->currentBalance,
            ':id' => $this->fundId
        ]);
    }

    public function processRefund($refundAmount)
    {
        $this->currentBalance += $refundAmount;
        
        $sql = "UPDATE GroupFund SET currentBalance = :balance WHERE fundId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':balance' => $this->currentBalance,
            ':id' => $this->fundId
        ]);
    }
}