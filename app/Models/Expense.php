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