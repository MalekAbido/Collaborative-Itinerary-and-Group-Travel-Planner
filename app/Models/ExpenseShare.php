<?php

namespace App\Models;
use Core\Database;
use PDO;


class ExpenseShare
{
    private $id;
    private $shareId;
    private $amount;
    private $isPayer;
    private $expenseId;
    private $tripMemberId;
    
    public function getId() {
        return $this->id;
    }

    public function getShareId() {
        return $this->shareId;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getIsPayer() {
        return $this->isPayer;
    }

    public function getExpenseId() {
        return $this->expenseId;
    }

    public function getTripMemberId() {
        return $this->tripMemberId;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setShareId($shareId) {
        $this->shareId = $shareId;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setIsPayer($isPayer) {
        $this->isPayer = $isPayer;
    }

    public function setExpenseId($expenseId) {
        $this->expenseId = $expenseId;
    }

    public function setTripMemberId($tripMemberId) {
        $this->tripMemberId = $tripMemberId;
    }
    
    public function create($expenseId, $tripMemberId, $amount, $isPayer) 
    {
        $pdo = Database::getInstance()->getConnection();
        
        $uniqueShareId = 'SHR-' . uniqid(); 
        
        $sql = "INSERT INTO ExpenseShare (shareId, amount, isPayer, expenseId, tripMemberId) 
                VALUES (:shareId, :amount, :isPayer, :expenseId, :tripMemberId)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'shareId' => $uniqueShareId,
            'amount' => $amount,
            'isPayer' => $isPayer ? 1 : 0, 
            'expenseId' => $expenseId,
            'tripMemberId' => $tripMemberId
        ]);
    }

    public function update()
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE ExpenseShare 
                SET shareId = :shareId,
                    amount = :amount,
                    isPayer = :isPayer,
                    expenseId = :expenseId,
                    tripMemberId = :tripMemberId
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'shareId' => $this->shareId,
            'amount' => $this->amount,
            'isPayer' => $this->isPayer,
            'expenseId' => $this->expenseId,
            'tripMemberId' => $this->tripMemberId,
            'id' => $this->id
        ]);
    }

    public function findByExpenseId($expenseId) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM ExpenseShare WHERE expenseId = :expenseId";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute(['expenseId' => $expenseId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $shares = [];
        foreach ($data as $row) {
            $share = new self();
            $share->setId($row['id']);
            $share->setShareId($row['shareId']);
            $share->setAmount($row['amount']);
            $share->setIsPayer($row['isPayer']);
            $share->setExpenseId($row['expenseId']);
            $share->setTripMemberId($row['tripMemberId']);
            $shares[] = $share;
        }
        return $shares;
    }

    public function deleteByExpenseId($expenseId) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM ExpenseShare WHERE expenseId = :expenseId";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['expenseId' => $expenseId]);
    }
}