<?php
namespace App\Models;

use App\Services\HistoryLogger;
use Core\Database;
use PDO;

class SettlementPayment
{
    private $db;
    private $id;
    private $settlementId;
    private $fromTripMemberId;
    private $toTripMemberId;
    private $itineraryId;
    private $amount;
    private $status;
    private $createdAt;
    private $paidAt;
    private $deletedAt;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSettlementId()
    {
        return $this->settlementId;
    }

    public function getFromTripMemberId()
    {
        return $this->fromTripMemberId;
    }

    public function getToTripMemberId()
    {
        return $this->toTripMemberId;
    }

    public function getItineraryId()
    {
        return $this->itineraryId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getPaidAt()
    {
        return $this->paidAt;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function fill(array $data)
    {
        $this->id = $data['id'];
        $this->settlementId = $data['settlementId'];
        $this->fromTripMemberId = $data['fromTripMemberId'];
        $this->toTripMemberId = $data['toTripMemberId'];
        $this->itineraryId = $data['itineraryId'];
        $this->amount = $data['amount'];
        $this->status = $data['status'];
        $this->createdAt = $data['createdAt'];
        $this->paidAt = $data['paidAt'] ?? null;
        $this->deletedAt = $data['deletedAt'] ?? null;
    }

    public function create($fromTripMemberId, $toTripMemberId, $itineraryId, $amount)
    {
        $this->settlementId = 'STL-' . uniqid();
        $this->fromTripMemberId = $fromTripMemberId;
        $this->toTripMemberId = $toTripMemberId;
        $this->itineraryId = $itineraryId;
        $this->amount = round($amount, 2);
        $this->status = 'PAID';

        $sql = "INSERT INTO SettlementPayment (settlementId, fromTripMemberId, toTripMemberId, itineraryId, amount, status, paidAt)
                VALUES (:settlementId, :fromTripMemberId, :toTripMemberId, :itineraryId, :amount, :status, NOW())";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':settlementId' => $this->settlementId,
            ':fromTripMemberId' => $this->fromTripMemberId,
            ':toTripMemberId' => $this->toTripMemberId,
            ':itineraryId' => $this->itineraryId,
            ':amount' => $this->amount,
            ':status' => $this->status,
        ]);

        if ($success) {
            $this->id = $this->db->lastInsertId();
            return $this->id;
        }

        return false;
    }

    public static function getPaidByItinerary($itineraryId): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM SettlementPayment WHERE itineraryId = :itineraryId AND status = 'PAID' AND deletedAt IS NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute([':itineraryId' => $itineraryId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $payments = [];
        foreach ($rows as $row) {
            $payment = new self();
            $payment->fill($row);
            $payments[] = $payment;
        }

        return $payments;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM SettlementPayment WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    public function markPaid()
    {
        $sql = "UPDATE SettlementPayment SET status = 'PAID', paidAt = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }
}
