<?php
namespace App\Models;

use Core\Database;
use PDO;

class Invitation
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createToken($itineraryId, $email, $role = 'Member')
    {
        $token = bin2hex(random_bytes(16));

        $sql = "INSERT INTO Invitation (itineraryId, email, token, role, createdAt, expiresAt, used) 
                VALUES (:itineraryId, :email, :token, :role, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 0)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':itineraryId' => $itineraryId,
            ':email'       => $email,
            ':token'       => $token,
            ':role'        => $role
        ]);

        return $success ? $token : false;
    }

    public function findByToken($token)
    {
        $sql = "SELECT * FROM Invitation 
                WHERE token = :token 
                AND used = 0 
                AND expiresAt > NOW() 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPendingByItinerary($itineraryId)
    {
        $sql = "SELECT * FROM Invitation 
                WHERE itineraryId = :itineraryId 
                AND used = 0 
                AND expiresAt > NOW() 
                ORDER BY createdAt DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markUsed($token)
    {
        $sql = "UPDATE Invitation SET used = 1 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    public function getOrCreateGeneralToken($itineraryId)
    {
        $sql = "SELECT token FROM Invitation 
                WHERE itineraryId = :itineraryId 
                AND email = 'general_link@voyagesync.com' 
                AND used = 0 
                AND expiresAt > NOW() 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['token'];
        }

        $token = bin2hex(random_bytes(16));

        $sqlInsert = "INSERT INTO Invitation (itineraryId, email, token, role, createdAt, expiresAt, used) 
                      VALUES (:itineraryId, :email, :token, 'Member', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0)";
        
        $stmtInsert = $this->db->prepare($sqlInsert);
        $stmtInsert->execute([
            ':itineraryId' => $itineraryId,
            ':email'       => 'general_link@voyagesync.com',
            ':token'       => $token
        ]);

        return $token;
    }
}