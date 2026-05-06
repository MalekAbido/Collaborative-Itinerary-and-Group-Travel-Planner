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
        $token = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO Invitation (itineraryId, email, token, role, createdAt, expiresAt) 
                VALUES (:itineraryId, :email, :token, :role, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':itineraryId' => $itineraryId,
            ':email' => $email,
            ':token' => $token,
            ':role' => $role
        ]);
        
        return $token;
    }

    public function findByToken($token)
    {
        $sql = "SELECT * FROM Invitation WHERE token = :token AND expiresAt > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markUsed($token)
    {
        $sql = "UPDATE Invitation SET used = 1 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
    }
}