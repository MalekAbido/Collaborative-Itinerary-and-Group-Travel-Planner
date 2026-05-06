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

    public function getId()
    {return $this->id;}

    public function getPlaceId()
    {return $this->placeId;}

    public function getName()
    {return $this->name;}

    public function setName($name)
    {$this->name = $name;}

    public function getAddress()
    {return $this->address;}

    public function setAddress($address)
    {$this->address = $address;}

    public function getLatitude()
    {return $this->latitude;}

    public function setLatitude($lat)
    {$this->latitude = $lat;}

    public function getLongitude()
    {return $this->longitude;}

    public function setLongitude($lng)
    {$this->longitude = $lng;}

    public function getTimeZoneId()
    {return $this->timeZoneId;}

    public function setTimeZoneId($tz)
    {$this->timeZoneId = $tz;}

    public function create()
    {
        $this->placeId = uniqid('loc_');
        $sql           = "INSERT INTO Location (placeId, name, address, latitude, longitude, timeZoneId)
                VALUES (:placeId, :name, :address, :latitude, :longitude, :timeZoneId)";

        $stmt    = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':placeId'    => $this->placeId,
            ':name'       => $this->name,
            ':address'    => $this->address,
            ':latitude'   => $this->latitude,
            ':longitude'  => $this->longitude,
            ':timeZoneId' => $this->timeZoneId,
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
        }

        return $success;
    }

    public function fill(array $row)
    {
        $this->id         = $row['id'];
        $this->placeId    = $row['placeId'];
        $this->name       = $row['name'];
        $this->address    = $row['address'];
        $this->latitude   = $row['latitude'];
        $this->longitude  = $row['longitude'];
        $this->timeZoneId = $row['timeZoneId'];
    }

    public static function getByLocationId($locationId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Location WHERE id = :locationId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':locationId' => $locationId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $location = new self();
            $location->fill($data);
            return $location;
        }

        return null;
    }

    public function read($id)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM Location WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->fill($row);
            return $this;
        }

        return null;
    }
}
