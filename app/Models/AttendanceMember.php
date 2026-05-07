<?php
namespace App\Models;

use Core\Database;
use PDO;

class AttendanceMember
{
    private $db;
    private $id;
    private $status;
    private $note;
    private $attendanceListId;
    private $tripMemberId;

    private $tripMemberObject = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note)
    {
        $this->note = $note;
    }

    public function getAttendanceListId()
    {
        return $this->attendanceListId;
    }

    public function getTripMemberId()
    {
        return $this->tripMemberId;
    }

    public function fill($row)
    {
        $this->id               = $row['id'];
        $this->status           = $row['status'];
        $this->note             = $row['note'];
        $this->attendanceListId = $row['attendanceListId'];
        $this->tripMemberId     = $row['tripMemberId'];
    }

    public function getTripMember()
    {

        if ($this->tripMemberObject === null) {
            $this->tripMemberObject = (new TripMember())->read($this->tripMemberId);
        }

        return $this->tripMemberObject;
    }

    public static function getAllByListId($listId)
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT a.itineraryId FROM AttendanceList al 
                JOIN Activity a ON al.activityId = a.id 
                WHERE al.id = :listId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':listId' => $listId]);
        $itineraryId = $stmt->fetchColumn();

        if ($itineraryId) {
            $syncSql = "INSERT INTO AttendanceMember (status, attendanceListId, tripMemberId)
                        SELECT 'Pending', :listId, tm.id
                        FROM TripMember tm
                        WHERE tm.itineraryId = :itineraryId
                        AND tm.id NOT IN (
                            SELECT tripMemberId FROM AttendanceMember WHERE attendanceListId = :listId
                        )";
            $syncStmt = $db->prepare($syncSql);
            $syncStmt->execute([':listId' => $listId, ':itineraryId' => $itineraryId]);
        }

        $sql = "SELECT * FROM AttendanceMember WHERE attendanceListId = :listId ORDER BY FIELD(status, 'Going', 'Not Going', 'Pending')";
        $stmt = $db->prepare($sql);
        $stmt->execute([':listId' => $listId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $members = [];
        foreach ($data as $row) {
            $member = new self();
            $member->fill($row);
            $members[] = $member;
        }

        return $members;
    }

    public static function getByTripMemberAndAttendanceList($attendanceListId, $tripMemberId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM AttendanceMember WHERE attendanceListId = :listId AND tripMemberId = :tripMemberId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':listId'       => $attendanceListId,
            ':tripMemberId' => $tripMemberId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $member = new self();
            $member->fill($row);
            return $member;
        }

        return null;
    }

    public function update()
    {
        $db     = Database::getInstance()->getConnection();
        $sql    = "UPDATE AttendanceMember SET status = :status, note = :note WHERE id = :id";
        $stmt   = $db->prepare($sql);
        $result = $stmt->execute([
            ':status' => $this->status,
            ':note'   => $this->note,
            ':id'     => $this->id,
        ]);
        return $result;
    }
}
