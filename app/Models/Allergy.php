<?php

namespace App\Models;

use Core\Database;
use PDO;

class Allergy
{
    private $db;
    private $id;
    private $allergenId;
    private $allergen;
    private $severity;
    private $reaction;
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

    public function getAllergenId()
    {
        return $this->allergenId;
    }
    public function setAllergenId($allergenId)
    {
        $this->allergenId = $allergenId;
    }

    public function getAllergen()
    {
        return $this->allergen;
    }
    public function setAllergen($allergen)
    {
        $this->allergen = $allergen;
    }

    public function getSeverity()
    {
        return $this->severity;
    }
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    public function getReaction()
    {
        return $this->reaction;
    }
    public function setReaction($reaction)
    {
        $this->reaction = $reaction;
    }

    public function getUserId()
    {
        return $this->userId;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function create($data)
    {
        $this->setUserId($data['userId']);
        $this->setAllergen($data['allergen']);
        $this->setSeverity($data['severity']);
        $this->setReaction($data['reaction'] ?? '');
        $this->setAllergenId(uniqid('alg_'));

        $sql = "INSERT INTO Allergy (allergenId, allergen, severity, reaction, userId)
                VALUES (:allergenId, :allergen, :severity, :reaction, :userId)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':allergenId' => $this->allergenId,
            ':allergen' => $this->allergen,
            ':severity' => $this->severity,
            ':reaction' => $this->reaction,
            ':userId' => $this->userId
        ]);
    }

    public function read($id)
    {
        $sql = "SELECT * FROM Allergy WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->allergenId = $data['allergenId'];
            $this->allergen = $data['allergen'];
            $this->severity = $data['severity'];
            $this->reaction = $data['reaction'];
            $this->userId = $data['userId'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = "UPDATE Allergy SET allergen = :allergen, severity = :severity, reaction = :reaction WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':allergen' => $this->allergen,
            ':severity' => $this->severity,
            ':reaction' => $this->reaction,
            ':id' => $this->id
        ]);
    }

    public function delete()
    {
        $sql = "DELETE FROM Allergy WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }
}
