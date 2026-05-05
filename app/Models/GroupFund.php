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

    public function create($tripFinanceId, $targetBalance = 0)
    {
        $this->fundId = uniqid('fund_');
        $sql = "INSERT INTO GroupFund (fundId, targetBalance, currentBalance, tripFinanceId) 
                VALUES (:fundId, :targetBalance, 0, :tripFinanceId)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':fundId' => $this->fundId,
            ':targetBalance' => $targetBalance,
            ':tripFinanceId' => $tripFinanceId
        ]);
    }

    public function addFunds($contributorId, $amount)
    {
        $sql = "UPDATE GroupFund SET currentBalance = currentBalance + :amount WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':amount' => $amount,
            ':id' => $this->fundId
        ]);

        $contributionId = uniqid('cont_');

        $logSql = "INSERT INTO FundContribution (contributionId, amount, timestamp, groupFundId, tripMemberId) 
                   VALUES (:contributionId, :amount, NOW(), :groupFundId, :tripMemberId)";
        
        $logStmt = $this->db->prepare($logSql);
        return $logStmt->execute([
            ':contributionId' => $contributionId,
            ':amount'         => $amount,
            ':groupFundId'    => $this->fundId,
            ':tripMemberId'   => $contributorId
        ]);
    }

    public function deductExpense($amount)
    {
        if ($amount > $this->currentBalance) {
            return false;
        }

        $this->currentBalance -= $amount;

        // I made a change, instead of passing the string id I pass the integer id.
        // instead of fundId I will pass id.
        $sql = "UPDATE GroupFund SET currentBalance = :balance WHERE id = :id";
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

    public function readByTripFinanceId($tripFinanceId)
    {
        $sql = "SELECT * FROM GroupFund WHERE tripFinanceId = :tripFinanceId LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tripFinanceId' => $tripFinanceId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            $this->fundId = $data['id']; // We use the internal integer ID
            $this->currentBalance = $data['currentBalance'];
            return true;
        }
        return false;
    }

    public function getContributions()
    {
        if (!$this->fundId) return [];

        $sql = "SELECT fc.amount, fc.timestamp, u.firstName, u.lastName 
                FROM FundContribution fc
                JOIN TripMember tm ON fc.tripMemberId = tm.id
                JOIN User u ON tm.userId = u.id
                WHERE fc.groupFundId = :groupFundId
                ORDER BY fc.timestamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':groupFundId' => $this->fundId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}