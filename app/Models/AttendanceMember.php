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
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM AttendanceMember WHERE attendanceListId = :listId";
        $stmt = $db->prepare($sql);
        $stmt->execute([':listId' => $listId]);
        $data    = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $members = [];

        foreach ($data as $row) {
            $member = new self();
            $member->fill($row);
            $members[] = $member;
        }

        return $members;
    }

    public function update()
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "UPDATE AttendanceMember SET status = :status, note = :note WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':status' => $this->status,
            ':note'   => $this->note,
            ':id'     => $this->id,
        ]);
    }
}
