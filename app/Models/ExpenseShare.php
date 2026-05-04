<?php

namespace App\Models;
use Core\Database;
use PDO;


class ExpenseShare
{
    public $id;
    public $shareId;
    public $amount;
    public $isPayer;
    public $expenseId;
    public $tripMemberId;
    
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

    public function findByExpenseId($expenseId) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM ExpenseShare WHERE expenseId = :expenseId";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $stmt->execute(['expenseId' => $expenseId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByExpenseId($expenseId) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM ExpenseShare WHERE expenseId = :expenseId";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['expenseId' => $expenseId]);
    }
}