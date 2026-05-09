<?php
namespace App\Models;

use App\Enums\AttendanceStatus;
use Core\Database;
use PDO;

class AttendanceMember
{
    private $db;
    private $id;
    /** @var AttendanceStatus|null */
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

    /** @return string|null */
    public function getStatus()
    {
        return $this->status instanceof AttendanceStatus ? $this->status->value : $this->status;
    }

    public function setStatus($status)
    {
        if (is_string($status)) {
            $this->status = AttendanceStatus::tryFrom($status);
        } else {
            $this->status = $status;
        }
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
        $this->status           = AttendanceStatus::tryFrom($row['status']);
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
                        SELECT :status, :listId, tm.id
                        FROM TripMember tm
                        WHERE tm.itineraryId = :itineraryId AND tm.deletedAt IS NULL
                        AND tm.id NOT IN (
                            SELECT tripMemberId FROM AttendanceMember WHERE attendanceListId = :listId
                        )";
            $syncStmt = $db->prepare($syncSql);
            $syncStmt->execute([
                ':status'      => AttendanceStatus::PENDING->value,
                ':listId'      => $listId, 
                ':itineraryId' => $itineraryId
            ]);
        }

        $sql = "SELECT * FROM AttendanceMember WHERE attendanceListId = :listId ORDER BY FIELD(status, :going, :notGoing, :pending)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':listId'   => $listId,
            ':going'    => AttendanceStatus::GOING->value,
            ':notGoing' => AttendanceStatus::NOT_GOING->value,
            ':pending'  => AttendanceStatus::PENDING->value
        ]);
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
            ':status' => $this->status instanceof AttendanceStatus ? $this->status->value : $this->status,
            ':note'   => $this->note,
            ':id'     => $this->id,
        ]);
        return $result;
    }
}
