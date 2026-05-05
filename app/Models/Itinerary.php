<?php
namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

class Itinerary {
    private $db;

    private $itineraryId;
    private $title;
    private $description;
    private $startDate;
    private $endDate;

    public function getItineraryId() {
        return $this->itineraryId;
    }
    public function __construct(){
        $this->db=Database::getInstance()->getConnection();
    }

    public function create($title, $description, $startDate, $endDate){
        $this->itineraryId = uniqid('trip_');                                                                   
        
        $sql = "INSERT INTO Itinerary (itineraryId, title, description, startDate, endDate) 
                VALUES (:id, :title, :desc, :start, :end)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $this->itineraryId,
            ':title' => $title,
            ':desc' => $description,
            ':start' => $startDate,
            ':end' => $endDate
        ]);
        
        return $this->itineraryId; 
    }

    public function findById($id){
        $sql = "SELECT * FROM Itinerary WHERE itineraryId = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function findByIdNumeric($id){
        $sql = "SELECT * FROM Itinerary WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function update($id, $title, $description, $startDate, $endDate){
        $sql = "UPDATE Itinerary 
                SET title = :title, description = :desc, startDate = :start, endDate = :end 
                WHERE itineraryId = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':desc' => $description,
            ':start' => $startDate,
            ':end' => $endDate
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM Itinerary WHERE itineraryId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]); 
    }
}