<?php

namespace App\Models;

use App\Models\Itinerary;
use App\Models\User;
use App\Enums\TripMemberRole;
use Core\Database;
use PDO;

class TripMember
{
    private $db;
    private $id;
    private $membershipId;
    /** @var TripMemberRole|null */
    private $role;
    private $joinedAt;
    private $userId;
    private $itineraryId;
    private $deletedAt;

    private $userObject      = null;
    private $itineraryObject = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getMembershipId()
    {
        return $this->membershipId;
    }

    public function setMembershipId($membershipId)
    {
        $this->membershipId = $membershipId;
    }

    /** @return string|null */
    public function getRole()
    {
        return $this->role instanceof TripMemberRole ? $this->role->value : $this->role;
    }

    public function setRole($role)
    {
        if (is_string($role)) {
            $this->role = TripMemberRole::tryFrom($role);
        } else {
            $this->role = $role;
        }
    }

    public function getJoinedAt()
    {
        return $this->joinedAt;
    }

    public function setJoinedAt($joinedAt)
    {
        $this->joinedAt = $joinedAt;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getItineraryId()
    {
        return $this->itineraryId;
    }

    public function setItineraryId($itineraryId)
    {
        $this->itineraryId = $itineraryId;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function create()
    {
        $this->membershipId = uniqid('mem_');
        $sql                = "INSERT INTO TripMember (membershipId, role, joinedAt, userId, itineraryId) VALUES (:membershipId, :role, :joinedAt, :userId, :itineraryId)";
        $stmt               = $this->db->prepare($sql);
        $success            = $stmt->execute([
            ':membershipId' => $this->membershipId,
            ':role'         => $this->role instanceof TripMemberRole ? $this->role->value : ($this->role ?? TripMemberRole::MEMBER->value),
            ':joinedAt'     => $this->joinedAt,
            ':userId'       => $this->userId,
            ':itineraryId'  => $this->itineraryId,
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }

        return $success;
    }

    public function read($id)
    {
        $sql  = "SELECT * FROM TripMember WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id           = $data['id'];
            $this->membershipId = $data['membershipId'];
            $this->role         = TripMemberRole::tryFrom($data['role']);
            $this->joinedAt     = $data['joinedAt'];
            $this->userId       = $data['userId'];
            $this->itineraryId  = $data['itineraryId'];
            $this->deletedAt    = $data['deletedAt'];
            return $this;
        }

        return false;
    }

    public function update()
    {
        $sql  = "UPDATE TripMember SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':role' => $this->role instanceof TripMemberRole ? $this->role->value : $this->role,
            ':id'   => $this->id,
        ]);
    }

    public function delete()
    {
        if (empty($this->id)) {
            return false;
        }

        // 1. Physically delete attendance records (User's request)
        $stmt = $this->db->prepare("DELETE FROM AttendanceMember WHERE tripMemberId = :id");
        $stmt->execute([':id' => $this->id]);

        // 2. Unassign inventory items (User's request)
        $stmt = $this->db->prepare("UPDATE InventoryItem SET tripMemberId = NULL WHERE tripMemberId = :id");
        $stmt->execute([':id' => $this->id]);

        // 3. Perform Soft Delete
        $this->deletedAt = date('Y-m-d H:i:s');
        $sql  = "UPDATE TripMember SET deletedAt = :deletedAt WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':deletedAt' => $this->deletedAt,
            ':id'        => $this->id
        ]);
    }

    public function reactivate($role = null)
    {
        $this->deletedAt = null;
        if ($role) {
            $this->setRole($role);
        }
        
        $sql  = "UPDATE TripMember SET deletedAt = NULL, role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':role' => $this->role instanceof TripMemberRole ? $this->role->value : $this->role,
            ':id'   => $this->id
        ]);
    }

    public function getAllByItineraryId($itineraryId, $includeDeleted = false)
    {
        // REAL FIX: Joining the TripMember table with the User table
        $sql = "SELECT m.id as memberId, m.role, m.joinedAt, m.itineraryId, m.deletedAt,
                       u.id as userId, u.firstName, u.lastName, u.email
                FROM TripMember m
                JOIN User u ON m.userId = u.id
                WHERE m.itineraryId = :itineraryId";
        
        if (!$includeDeleted) {
            $sql .= " AND m.deletedAt IS NULL";
        }

        $sql .= " ORDER BY 
                    CASE m.role 
                        WHEN '" . TripMemberRole::ORGANIZER->value . "' THEN 1 
                        WHEN '" . TripMemberRole::EDITOR->value . "' THEN 2 
                        WHEN '" . TripMemberRole::MEMBER->value . "' THEN 3 
                        ELSE 4 
                    END, 
                    m.joinedAt ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getByUserAndItinerary($userId, $itineraryId, $includeDeleted = false)
    {
        $db = \Core\Database::getInstance()->getConnection();

        $sql  = "SELECT * FROM TripMember WHERE userId = :userId AND itineraryId = :itineraryId";
        if (!$includeDeleted) {
            $sql .= " AND deletedAt IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':userId'      => $userId,
            ':itineraryId' => $itineraryId,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            $member = new self();
            $member->setId($row['id']);
            $member->setMembershipId($row['membershipId']);
            $member->setRole(TripMemberRole::tryFrom($row['role']));
            $member->setJoinedAt($row['joinedAt']);
            $member->setUserId($row['userId']);
            $member->setItineraryId($row['itineraryId']);
            $member->setDeletedAt($row['deletedAt']);

            return $member;
        }

        return null;
    }

    public function getUser()
    {

        if ($this->userObject === null) {
            $user = new User();

            if ($user->read($this->userId)) {
                $this->userObject = $user;
            }
        }

        return $this->userObject;
    }

    public function getItinerary()
    {

        if ($this->itineraryObject === null) {
            $itinerary = new Itinerary();

            if ($itinerary->read($this->itineraryId)) {
                $this->itineraryObject = $itinerary;
            }
        }

        return $this->itineraryObject;
    }

    public function getDisplayName()
    {
        $user = $this->getUser();
        if (!$user) return 'Unknown User';
        
        $name = $user->getFirstName() . ' ' . $user->getLastName();
        if ($this->deletedAt !== null) {
            $name .= ' (Former Member)';
        }
        return $name;
    }
}
