<?php

namespace App\Models;

use Core\Database;
use PDO;

class Expense
{
    private $id;
    private $expenseId;
    private $amount;
    private $currencyType;
    private $description;
    private $category;
    private $isNonCash;
    private $paidByKitty;
    private $tripFinanceId;
    private $tripMemberId;

    public array $expenseShares = [];

    public function getId() {
        return $this->id;
    }

    public function getExpenseId() {
        return $this->expenseId;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrencyType() {
        return $this->currencyType;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getIsNonCash() {
        return $this->isNonCash;
    }

    public function getPaidByKitty() {
        return $this->paidByKitty;
    }

    public function getTripFinanceId() {
        return $this->tripFinanceId;
    }

    public function getTripMemberId() {
        return $this->tripMemberId;
    }

    public function getExpenseShares() {
        return $this->expenseShares;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setExpenseId($expenseId) {
        $this->expenseId = $expenseId;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setCurrencyType($currencyType) {
        $this->currencyType = $currencyType;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setIsNonCash($isNonCash) {
        $this->isNonCash = $isNonCash;
    }

    public function setPaidByKitty($paidByKitty) {
        $this->paidByKitty = $paidByKitty;
    }

    public function setTripFinanceId($tripFinanceId) {
        $this->tripFinanceId = $tripFinanceId;
    }

    public function setTripMemberId($tripMemberId) {
        $this->tripMemberId = $tripMemberId;
    }

    public function setExpenseShares(array $expenseShares) {
        $this->expenseShares = $expenseShares;
    }

    public function create($data)
    {
        $pdo = Database::getInstance()->getConnection();

        $uniqueExpenseId = 'EXP-' . uniqid();

        $sql = "INSERT INTO Expense (expenseId, amount, currencyType, description, category, isNonCash, paidByKitty, tripFinanceId, tripMemberId) 
                VALUES (:expenseId, :amount, :currencyType, :description, :category, :isNonCash, :paidByKitty, :tripFinanceId, :tripMemberId)";
        
        $stmt = $pdo->prepare($sql);
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
        
        return $pdo->lastInsertId(); 
    }
    
    public function findById($id) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Expense WHERE id = :id";
        
        $stmt = $pdo->prepare($sql); 
        
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $expense = new self();
            $expense->setId($data['id']);
            $expense->setExpenseId($data['expenseId']);
            $expense->setAmount($data['amount']);
            $expense->setCurrencyType($data['currencyType']);
            $expense->setDescription($data['description']);
            $expense->setCategory($data['category']);
            $expense->setIsNonCash($data['isNonCash']);
            $expense->setPaidByKitty($data['paidByKitty']);
            $expense->setTripFinanceId($data['tripFinanceId']);
            $expense->setTripMemberId($data['tripMemberId']);
            return $expense;
        }
        return null;
    }

    public function delete($id) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM Expense WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
    public function loadShares($expenseId) {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM ExpenseShare WHERE expenseId = :expenseId";

        $stmt = $pdo->prepare($sql);

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