<?php

namespace App\Models;

use Core\Database;
use PDO;

class EmergencyContact
{
    private $db;
    private $id;
    private $contactId;
    private $name;
    private $phone;
    private $email;
    private $relationship;
    private $userId;

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

    public function getContactId()
    {
        return $this->contactId;
    }
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getRelationship()
    {
        return $this->relationship;
    }
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
    }

    public function getUserId()
    {
        return $this->userId;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function create()
    {
        $this->contactId = uniqid('emg_');
        $sql = "INSERT INTO EmergencyContact (contactId, name, phone, email, relationship, userId) VALUES (:contactId, :name, :phone, :email, :relationship, :userId)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':contactId' => $this->contactId,
            ':name' => $this->name,
            ':phone' => $this->phone,
            ':email' => $this->email,
            ':relationship' => $this->relationship,
            ':userId' => $this->userId
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }
        return $success;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM EmergencyContact WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->contactId = $data['contactId'];
            $this->name = $data['name'];
            $this->phone = $data['phone'];
            $this->email = $data['email'];
            $this->relationship = $data['relationship'];
            $this->userId = $data['userId'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = "UPDATE EmergencyContact SET name = :name, phone = :phone, email = :email, relationship = :relationship WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $this->name,
            ':phone' => $this->phone,
            ':email' => $this->email,
            ':relationship' => $this->relationship,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "DELETE FROM EmergencyContact WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }
}
