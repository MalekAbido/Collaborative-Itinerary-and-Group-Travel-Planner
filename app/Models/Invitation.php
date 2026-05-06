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

    public function createToken($itineraryId, $email, $role)
    {
        $secureToken = bin2hex(random_bytes(32));
        $invitationId = uniqid('inv_');

        $sql = "INSERT INTO Invitation (invitationId, secureToken, isActive, itineraryId, email, role) 
                VALUES (:invitationId, :secureToken, 1, :itineraryId, :email, :role)";
        $stmt = $this->db->prepare($sql);
        
        $success = $stmt->execute([
            ':invitationId' => $invitationId,
            ':secureToken'  => $secureToken,
            ':itineraryId'  => $itineraryId,
            ':email'        => $email,
            ':role'         => $role
        ]);

        return $success ? $secureToken : false;
    }

    public function findByToken($secureToken)
    {
        $sql = "SELECT * FROM Invitation WHERE secureToken = :token AND isActive = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $secureToken]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPendingByItinerary($itineraryId)
    {
        $sql = "SELECT * FROM Invitation WHERE itineraryId = :itineraryId AND isActive = 1 AND email IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markUsed($secureToken)
    {
        $sql = "UPDATE Invitation SET isActive = 0 WHERE secureToken = :token";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':token' => $secureToken]);
    }

    public function getOrCreateGeneralToken($itineraryId)
    {
        $sql = "SELECT secureToken FROM Invitation WHERE itineraryId = :itineraryId AND email IS NULL AND isActive = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['secureToken'];
        }

        $secureToken = bin2hex(random_bytes(32));
        $invitationId = uniqid('inv_');

        $sqlInsert = "INSERT INTO Invitation (invitationId, secureToken, isActive, itineraryId, email, role) 
                      VALUES (:invitationId, :secureToken, 1, :itineraryId, NULL, 'Member')";
        
        $stmtInsert = $this->db->prepare($sqlInsert);
        $stmtInsert->execute([
            ':invitationId' => $invitationId,
            ':secureToken'  => $secureToken,
            ':itineraryId'  => $itineraryId
        ]);

        return $secureToken;
    }
}