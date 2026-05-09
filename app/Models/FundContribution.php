<?php
namespace App\Models;

use App\Helpers\HistoryLogger;
use Core\Database;
use PDO;

class FundContribution
{
    private $db;
    private $id;
    private $contributionId;
    private $amount;
    private $timestamp;
    private $groupFundId;
    private $tripMemberId;
    private $deletedAt;

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

    public function getContributionId()
    {
        return $this->contributionId;
    }

    public function setContributionId($contributionId)
    {
        $this->contributionId = $contributionId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getGroupFundId()
    {
        return $this->groupFundId;
    }

    public function setGroupFundId($groupFundId)
    {
        $this->groupFundId = $groupFundId;
    }

    public function getTripMemberId()
    {
        return $this->tripMemberId;
    }

    public function setTripMemberId($tripMemberId)
    {
        $this->tripMemberId = $tripMemberId;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function fill(array $data)
    {
        $this->id             = $data['id'];
        $this->contributionId = $data['contributionId'];
        $this->amount         = $data['amount'];
        $this->timestamp      = $data['timestamp'];
        $this->groupFundId    = $data['groupFundId'];
        $this->tripMemberId   = $data['tripMemberId'];
        $this->deletedAt      = $data['deletedAt'] ?? null;
    }

    public function read($id)
    {
        $sql  = "SELECT * FROM FundContribution WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    public function delete($deletedByTripMemberId)
    {
        $sql     = "UPDATE FundContribution SET deletedAt = NOW() WHERE id = :id";
        $stmt    = $this->db->prepare($sql);
        $success = $stmt->execute([':id' => $this->id]);

        if ($success) {
            $itineraryId = $this->getItineraryId();
            // move this to fund contribution controller
            HistoryLogger::log($itineraryId, \App\Enums\TransactionType::DELETED_FUND_CONTRIBUTION, $this, $deletedByTripMemberId);
        }

        return $success;
    }

    public function getItineraryId()
    {
        $sql = "SELECT tf.itineraryId FROM GroupFund gf
                JOIN TripFinance tf ON gf.tripFinanceId = tf.id
                WHERE gf.id = :groupFundId LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':groupFundId' => $this->groupFundId]);
        return $stmt->fetchColumn();
    }

    public function update()
    {
        $sql  = "UPDATE FundContribution SET deletedAt = :deletedAt WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':deletedAt' => $this->deletedAt,
            ':id'        => $this->id,
        ]);
    }
}
