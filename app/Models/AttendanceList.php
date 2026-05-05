<?php
namespace App\Models;

use Core\Database;
use PDO;

class AttendanceList
{
    private $db;
    private $id;
    private $totalAttendeeCount;
    private $activityId;

    private $members = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTotalAttendeeCount()
    {
        $this->calculateTotalCount();
        return $this->totalAttendeeCount;
    }

    public function getActivityId()
    {
        return $this->activityId;
    }

    public function fill(array $row)
    {
        $this->id                 = $row['id'];
        $this->totalAttendeeCount = $row['totalAttendeeCount'];
        $this->activityId         = $row['activityId'];
    }

    public function getMembers()
    {

        if ($this->members === null) {
            $this->members = AttendanceMember::getAllByListId($this->id);
        }

        return $this->members;
    }

    public function updateStatus($tripMemberId, $newStatus, $note = null)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM AttendanceMember WHERE attendanceListId = :listId AND tripMemberId = :memberId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':listId' => $this->id, ':memberId' => $tripMemberId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $member = new AttendanceMember();
            $member->fill($row);
            $member->setStatus($newStatus);

            if ($note !== null) {
                $member->setNote($note);
            }

            $member->update();
        } else {
            $insertSql  = "INSERT INTO AttendanceMember (status, note, attendanceListId, tripMemberId) VALUES (:status, :note, :listId, :memberId)";
            $insertStmt = $db->prepare($insertSql);
            $insertStmt->execute([
                ':status'   => $newStatus,
                ':note'     => $note,
                ':listId'   => $this->id,
                ':memberId' => $tripMemberId,
            ]);
        }

        $this->members = null;

        $this->calculateTotalCount();
    }

    public function getMembersByStatus($status)
    {
        $members = [];

        foreach ($this->getMembers() as $member) {
            if ($member->getStatus() === $status) {
                $members[] = $member->getTripMember();
            }
        }

        return $members;
    }

    public function calculateTotalCount()
    {
        $db = Database::getInstance()->getConnection();

        $status = 'GOING';
        $sql    = "SELECT COUNT(*) FROM AttendanceMember WHERE attendanceListId = :listId AND status = :status";
        $stmt   = $db->prepare($sql);
        $stmt->execute([':listId' => $this->id, ':status' => $status]);
        $count = $stmt->fetchColumn();

        $this->totalAttendeeCount = $count;

        $updateSql  = "UPDATE AttendanceList SET totalAttendeeCount = :count WHERE id = :id";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute([':count' => $count, ':id' => $this->id]);
    }

    public static function getByActivityId($activityId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM AttendanceList WHERE activityId = :activityId LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':activityId' => $activityId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $list = new self();
            $list->fill($row);
            return $list;
        }

        return null;
    }
}
