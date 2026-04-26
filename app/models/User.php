<?php

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
        $sql = "INSERT INTO user (name, age, email, password, userType) 
                VALUES (:name, :age, :email, :password, :userType)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":name"  => $name,
            ":age"   => $age,
            ":email" => $email,
            ":password"   => $password,
            ":userType"   => $userType
        ]);
    }

    /* GET ONE USER */
    public function getUserById($id)
    {
        $sql = "SELECT * FROM user WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* GET ALL user */
    public function getAllUsers()
    {
        $stmt = $this->db->prepare("SELECT * FROM user ORDER BY id DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* UPDATE USER */
    public function updateUser($id, $name, $age, $email, $password, $userType)
    {
        $sql = "UPDATE user 
                SET name = :name, age = :age, email = :email, password = :password, userType = :userType
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":id"    => $id,
            ":name"  => $name,
            ":age"   => $age,
            ":email" => $email,
            ":password"   => $password,
            ":userType"   => $userType
        ]);
    }

    /* DELETE USER */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM user WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([":id" => $id]);
    }

}
