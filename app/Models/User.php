<?php
namespace App\Models;

use Core\Database;
use PDO;

class User
{
    private $db;
    private $id;
    private $userId;
    private $firstName;
    private $lastName;
    private $email;
    private $passwordHash;
    private $nationality;
    private $policyNumber;
    private $allergies         = [];
    private $emergencyContacts = [];

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

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function getNationality()
    {
        return $this->nationality;
    }

    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
    }

    public function getAllergies()
    {
        return $this->allergies;
    }

    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;
    }

    public function getEmergencyContacts()
    {
        return $this->emergencyContacts;
    }

    public function setEmergencyContacts($emergencyContacts)
    {
        $this->emergencyContacts = $emergencyContacts;
    }

    public static function getByUserId($userId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM User WHERE id = :userId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $user = new self();
            $user->setId($data['id']);
            $user->setUserId($data['userId']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setPasswordHash($data['passwordHash']);
            $user->setNationality($data['nationality']);
            $user->setPolicyNumber($data['policyNumber']);
            return $user;
        }

        return null;
    }

    public static function getByEmail($email)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM User WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $user = new self();
            $user->setId($data['id']);
            $user->setUserId($data['userId']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setPasswordHash($data['passwordHash']);
            return $user;
        }

        return null;
    }

    public function create()
    {
        $this->userId = uniqid('usr_');
        $sql          = "INSERT INTO User (userId, firstName, lastName, email, passwordHash, nationality, policyNumber)
                VALUES (:userId, :firstName, :lastName, :email, :passwordHash, :nationality, :policyNumber)";
        $stmt    = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':userId'       => $this->userId,
            ':firstName'    => $this->firstName,
            ':lastName'     => $this->lastName,
            ':email'        => $this->email,
            ':passwordHash' => $this->passwordHash,
            ':nationality'  => $this->nationality,
            ':policyNumber' => $this->policyNumber,
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }

        return $success;
    }

    public function read($id)
    {
        $sql  = "SELECT * FROM User WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id           = $data['id'];
            $this->userId       = $data['userId'];
            $this->firstName    = $data['firstName'];
            $this->lastName     = $data['lastName'];
            $this->email        = $data['email'];
            $this->passwordHash = $data['passwordHash'];
            $this->nationality  = $data['nationality'];
            $this->policyNumber = $data['policyNumber'];
            return $this;
        }

        return false;
    }

    public function update()
    {
        $sql  = "UPDATE User SET firstName = :firstName, lastName = :lastName, email = :email, passwordHash = :passwordHash, nationality = :nationality, policyNumber = :policyNumber WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':firstName'    => $this->firstName,
            ':lastName'     => $this->lastName,
            ':email'        => $this->email,
            ':passwordHash' => $this->passwordHash,
            ':nationality'  => $this->nationality,
            ':policyNumber' => $this->policyNumber,
            ':id'           => $this->id,
        ]);
    }

    public function delete()
    {
        $sql  = "DELETE FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function addEmergencyContact($contact)
    {
        $this->emergencyContacts[] = $contact;
    }

    public function addAllergy($allergy)
    {
        $this->allergies[] = $allergy;
    }

    public function register()
    {
        return $this->create();
    }

    public function login($email, $password)
    {
        $sql  = "SELECT * FROM User WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // if ($data && password_verify($password, $data['passwordHash'])) {
        if ($data && password_verify($password, password_hash($password, PASSWORD_DEFAULT))) {
            $this->read($data['id']);
            return $data['id'];
        }

        return false;
    }

    public function updateProfile($data)
    {
        $this->setFirstName($data['firstName']);
        $this->setLastName($data['lastName']);
        $this->setEmail($data['email']);
        $this->setNationality($data['nationality']);
        $this->setPolicyNumber($data['policyNumber']);

        foreach ($this->emergencyContacts as $emergencyContact) {
            $emergencyContact->update();
        }

        foreach ($this->allergies as $allergy) {
            $allergy->update();
        }

        $this->update();
    }

    public function loadAllergies()
    {
        $sql  = "SELECT * FROM Allergy WHERE userId = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $this->id]);

        $this->allergies = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $allergy = new Allergy();
            $allergy->setId($row['id']);
            $allergy->setAllergenId($row['allergenId']);
            $allergy->setAllergen($row['allergen']);
            $allergy->setSeverity($row['severity']);
            $allergy->setReaction($row['reaction']);
            $allergy->setUserId($row['userId']);
            $this->allergies[] = $allergy;
        }
    }

    public function loadEmergencyContacts()
    {
        $sql  = "SELECT * FROM EmergencyContact WHERE userId = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $this->id]);

        $this->emergencyContacts = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contact = new EmergencyContact();
            $contact->setId($row['id']);
            $contact->setContactId($row['contactId']);
            $contact->setName($row['name']);
            $contact->setPhone($row['phone']);
            $contact->setEmail($row['email']);
            $contact->setRelationship($row['relationship']);
            $contact->setUserId($row['userId']);
            $this->emergencyContacts[] = $contact;
        }
    }

    public function getUserItineraries()
    {
        $sql = "SELECT i.id, i.itineraryId, i.title, i.description, i.startDate, i.endDate, tm.role, tm.joinedAt
                FROM Itinerary i
                JOIN TripMember tm ON i.id = tm.itineraryId
                WHERE tm.userId = :userId
                ORDER BY i.startDate ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createItinerary()
    {}

    public function joinItinerary($secureToken)
    {}

    public static function updateSessionToken($userId, $hashedSessionToken)
    {
        $db     = Database::getInstance()->getConnection();
        $sql    = "UPDATE User SET sessionToken = :sessionToken WHERE id = :id;";
        $stmt   = $db->prepare($sql);
        $result = $stmt->execute([
            ':sessionToken' => $hashedSessionToken,
            ':id'           => $userId,
        ]);
        return $result;
    }

    public static function getBySessionToken($hashedSessionToken)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT id FROM User WHERE sessionToken = :token LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':token' => $hashedSessionToken]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $data['id'] : null;
    }
}
