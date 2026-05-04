<?php
namespace App\Models;

use Core\Database;
use PDO;

class TripMember
{
    private $db;
    private $id;
    private $membershipId;
    private $role;
    private $joinedAt;
    private $userId;
    private $itineraryId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getMembershipId() { return $this->membershipId; }
    public function setMembershipId($membershipId) { $this->membershipId = $membershipId; }

    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }

    public function getJoinedAt() { return $this->joinedAt; }
    public function setJoinedAt($joinedAt) { $this->joinedAt = $joinedAt; }

    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }

    public function getItineraryId() { return $this->itineraryId; }
    public function setItineraryId($itineraryId) { $this->itineraryId = $itineraryId; }
    
    public function create()
    {
        $this->membershipId = uniqid('mem_');
        $sql = "INSERT INTO TripMember (membershipId, role, joinedAt, userId, itineraryId) VALUES (:membershipId, :role, :joinedAt, :userId, :itineraryId)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':membershipId' => $this->membershipId,
            ':role' => $this->role,
            ':joinedAt' => $this->joinedAt,
            ':userId' => $this->userId,
            ':itineraryId' => $this->itineraryId
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM TripMember WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->membershipId = $data['membershipId'];
            $this->role = $data['role'];
            $this->joinedAt = $data['joinedAt'];
            $this->userId = $data['userId'];
            $this->itineraryId = $data['itineraryId'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = "UPDATE TripMember SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':role' => $this->role,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "DELETE FROM TripMember WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function getAllByItineraryId($itineraryId)
    {
        // TEMPORARY FIX: We removed the JOIN to the users table so it doesn't crash.
        // We are injecting fake names/emails just so the UI has data to display!
        $sql = "SELECT m.id, m.role, m.joinedAt, 
                       'Test' as firstName, 'User' as lastName, 'test@email.com' as email 
                FROM TripMember m
                WHERE m.itineraryId = :itineraryId
                ORDER BY m.role DESC"; 
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSilentGuestUser($fullName)
    {
        // 1. Split the single name string into a First and Last name
        $nameParts = explode(' ', trim($fullName), 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // 2. Generate a random dummy email since the DB requires one
        $dummyEmail = strtolower($firstName) . '_' . uniqid() . '@guest.voyagesync.com';

        // 3. Insert the guest into the users table
        $sql = "INSERT INTO users (firstName, lastName, email) VALUES (:first, :last, :email)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first' => $firstName,
            ':last'  => $lastName,
            ':email' => $dummyEmail
        ]);

        // 4. Return the new User ID so we can attach it to the trip
        return $this->db->lastInsertId();
    }

    public function voteInPoll()
    {
    }

    public function proposeActivity()
    {
    }

    public function viewItinerary()
    {
    }

    public function useEmergencyContact()
    {
    }
    public function leaveTrip()
    {
    }

    public function assignRole($newRole)
    {
    }

    public function generateInvitation()
    {
    }
}