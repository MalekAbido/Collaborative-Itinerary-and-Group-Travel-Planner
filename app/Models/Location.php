<?php
namespace App\Models;

use Core\Database;
use PDO;

class Location
{
    private $db;
    private $id;
    private $placeId;
    private $name;
    private $address;
    private $latitude;
    private $longitude;
    private $timeZoneId;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function getPlaceId() { return $this->placeId; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }
    public function getLatitude() { return $this->latitude; }
    public function setLatitude($lat) { $this->latitude = $lat; }
    public function getLongitude() { return $this->longitude; }
    public function setLongitude($lng) { $this->longitude = $lng; }
    public function getTimeZoneId() { return $this->timeZoneId; }
    public function setTimeZoneId($tz) { $this->timeZoneId = $tz; }

    public function create()
    {
        $this->placeId = uniqid('loc_');
        $sql = "INSERT INTO Location (placeId, name, address, latitude, longitude, timeZoneId)
                VALUES (:placeId, :name, :address, :latitude, :longitude, :timeZoneId)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':placeId'    => $this->placeId,
            ':name'       => $this->name,
            ':address'    => $this->address,
            ':latitude'   => $this->latitude,
            ':longitude'  => $this->longitude,
            ':timeZoneId' => $this->timeZoneId
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }

        return $success;
    }

    public static function read($id)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM Location WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $loc = new self();
            $loc->id = $row['id'];
            $loc->placeId = $row['placeId'];
            $loc->name = $row['name'];
            $loc->address = $row['address'];
            $loc->latitude = $row['latitude'];
            $loc->longitude = $row['longitude'];
            $loc->timeZoneId = $row['timeZoneId'];
            return $loc;
        }

        return null;
    }
}