<?php

namespace App\Models;

use App\Helpers\HistoryLogger;
use App\Models\TransactionType;
use Core\Database;
use PDO;

class Expense
{
    private $pdo;
    private $id;
    private $expenseId;
    private $amount;
    private $refundedAmount;
    private $currencyType;
    private $description;
    private $category;
    private $isNonCash;
    private $paidByKitty;
    private $tripFinanceId;
    private $tripMemberId;
    private $deletedAt;

    public array $expenseShares = [];


    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getExpenseId()
    {
        return $this->expenseId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getRefundedAmount()
    {
        return $this->refundedAmount;
    }

    public function getCurrencyType()
    {
        return $this->currencyType;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getIsNonCash()
    {
        return $this->isNonCash;
    }

    public function getPaidByKitty()
    {
        return $this->paidByKitty;
    }

    public function getTripFinanceId()
    {
        return $this->tripFinanceId;
    }

    public function getTripMemberId()
    {
        return $this->tripMemberId;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function getExpenseShares()
    {
        return $this->expenseShares;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setExpenseId($expenseId)
    {
        $this->expenseId = $expenseId;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setRefundedAmount($refundedAmount)
    {
        $this->refundedAmount = $refundedAmount;
    }

    public function setCurrencyType($currencyType)
    {
        $this->currencyType = $currencyType;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setIsNonCash($isNonCash)
    {
        $this->isNonCash = $isNonCash;
    }

    public function setPaidByKitty($paidByKitty)
    {
        $this->paidByKitty = $paidByKitty;
    }

    public function setTripFinanceId($tripFinanceId)
    {
        $this->tripFinanceId = $tripFinanceId;
    }

    public function setTripMemberId($tripMemberId)
    {
        $this->tripMemberId = $tripMemberId;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function setExpenseShares(array $expenseShares)
    {
        $this->expenseShares = $expenseShares;
    }

    public function getItinerary()
    {
        $sql = "SELECT itineraryId FROM TripFinance WHERE id = :financeId LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['financeId' => $this->tripFinanceId]);
                
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $uniqueExpenseId = 'EXP-' . uniqid();

        $sql = "INSERT INTO Expense (expenseId, amount, currencyType, description, category, isNonCash, paidByKitty, tripFinanceId, tripMemberId) 
                VALUES (:expenseId, :amount, :currencyType, :description, :category, :isNonCash, :paidByKitty, :tripFinanceId, :tripMemberId)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'expenseId' => $uniqueExpenseId,
            'amount' => $data['amount'],
            'currencyType' => $data['currencyType'] ?? 'USD',
            'description' => $data['description'],
            'category' => $data['category'],
            'isNonCash' => $data['isNonCash'] ? 1 : 0,
            'paidByKitty' => $data['paidByKitty'] ? 1 : 0,
            'tripFinanceId' => $data['financeId'],
            'tripMemberId' => $data['payerId']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM Expense WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $expense = new self();
            $expense->setId($data['id']);
            $expense->setExpenseId($data['expenseId']);
            $expense->setAmount($data['amount']);
            $expense->setRefundedAmount($data['refundedAmount'] ?? 0);
            $expense->setCurrencyType($data['currencyType']);
            $expense->setDescription($data['description']);
            $expense->setCategory($data['category']);
            $expense->setIsNonCash($data['isNonCash']);
            $expense->setPaidByKitty($data['paidByKitty']);
            $expense->setTripFinanceId($data['tripFinanceId']);
            $expense->setTripMemberId($data['tripMemberId']);
            $expense->setDeletedAt($data['deletedAt'] ?? null);
            return $expense;
        }
        return null;
    }

    public function delete($deletedByTripMemberId)
    {
        $sql = "UPDATE Expense SET deletedAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute(['id' => $this->id]);
        return $success;
    }

    public function update()
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE Expense 
                SET expenseId = :expenseId,
                    amount = :amount,
                    refundedAmount = :refundedAmount,
                    currencyType = :currencyType,
                    description = :description,
                    category = :category,
                    isNonCash = :isNonCash,
                    paidByKitty = :paidByKitty,
                    tripFinanceId = :tripFinanceId,
                    tripMemberId = :tripMemberId,
                    deletedAt = :deletedAt
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'expenseId' => $this->expenseId,
            'amount' => $this->amount,
            'refundedAmount' => $this->refundedAmount,
            'currencyType' => $this->currencyType,
            'description' => $this->description,
            'category' => $this->category,
            'isNonCash' => $this->isNonCash,
            'paidByKitty' => $this->paidByKitty,
            'tripFinanceId' => $this->tripFinanceId,
            'tripMemberId' => $this->tripMemberId,
            'deletedAt' => $this->deletedAt,
            'id' => $this->id
        ]);
    }

    public function updateRefundedAmount($newRefundInput)
    {
        if ($newRefundInput <= 0 || $newRefundInput > $this->amount) {
            return false; 
        }

        $reductionRatio = $newRefundInput / $this->amount;

        $this->amount -= $newRefundInput;
        $this->refundedAmount += $newRefundInput;
        $this->update();

        $shares = $this->loadShares($this->id);
        
        foreach ($shares as $share) {
            $shareReduction = $share->getAmount() * $reductionRatio;
            $newShareAmount = $share->getAmount() - $shareReduction;
            
            $share->setAmount($newShareAmount);
            $share->update();
        }

        return true;
    }

    public function loadShares($expenseId)
    {
        $this->pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM ExpenseShare WHERE expenseId = :expenseId";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['expenseId' => $expenseId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->expenseShares = [];
        foreach ($data as $row) {
            $share = new ExpenseShare();
            $share->setId($row['id']);
            $share->setShareId($row['shareId']);
            $share->setAmount($row['amount']);
            $share->setIsPayer($row['isPayer']);
            $share->setExpenseId($row['expenseId']);
            $share->setTripMemberId($row['tripMemberId']);
            $this->expenseShares[] = $share;
        }
        return $this->expenseShares;
    }
}