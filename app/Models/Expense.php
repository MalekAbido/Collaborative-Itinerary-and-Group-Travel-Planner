<?php

namespace App\Models;
use Core\Database;
use PDO;

class Expense 
{
    public function create($data) 
    {
        $pdo = Database::getInstance()->getConnection();

        $uniqueExpenseId = 'EXP-' . uniqid();

        $sql = "INSERT INTO Expense (expenseId, amount, description, category, tripFinanceId, tripMemberId) 
                VALUES (:expenseId, :amount, :description, :category, :tripFinanceId, :tripMemberId)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'expenseId' => $uniqueExpenseId,
            'amount' => $data['amount'],
            'description' => $data['description'],
            'category' => $data['category'],
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
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) 
    {
        $pdo = Database::getInstance()->getConnection();
        $sql = "DELETE FROM Expense WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}