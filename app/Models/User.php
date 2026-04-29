<?php
namespace App\Models;

use Core\Database;
use PDO;

class User
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = Database::getInstance()->getConnection();
    }

    /* CREATE USER */
    public function createUser($name, $age, $email, $password, $userType)
    {
        // Mapped:
        // :name -> firstName | :age -> nationality | :password -> passwordHash | :userType -> lastName
        // userId is auto-generated via MySQL's UUID() to satisfy the NOT NULL constraint.
        $sql = "INSERT INTO User (userId, firstName, nationality, email, passwordHash, lastName)
                VALUES (UUID(), :name, :age, :email, :password, :userType)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":name"     => $name,
            ":age"      => $age,
            ":email"    => $email,
            ":password" => $password,
            ":userType" => $userType,
        ]);
    }

    /* GET ONE USER */
    public function getUserById($id)
    {
        // Table name updated to match case of the new schema
        $sql = "SELECT * FROM User WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* GET ALL user */
    public function getAllUsers()
    {
        // Table name updated to match case of the new schema
        $stmt = $this->db->prepare("SELECT * FROM User ORDER BY id DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* UPDATE USER */
    public function updateUser($id, $name, $age, $email, $password, $userType)
    {
        // Mapped to match the INSERT query logic
        $sql = "UPDATE User
                SET firstName = :name, nationality = :age, email = :email, passwordHash = :password, lastName = :userType
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":id"       => $id,
            ":name"     => $name,
            ":age"      => $age,
            ":email"    => $email,
            ":password" => $password,
            ":userType" => $userType,
        ]);
    }

    /* DELETE USER */
    public function deleteUser($id)
    {
        // Table name updated to match case of the new schema
        $sql = "DELETE FROM User WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([":id" => $id]);
    }
}
