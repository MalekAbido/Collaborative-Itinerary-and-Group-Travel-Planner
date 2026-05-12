<?php
namespace App\Models;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\FundContribution;
use App\Models\InventoryItem;
use App\Enums\EntityType;
use Core\Database;
use DateTime;
use PDO;

class HistoryLogEntry
{
    private $id;
    private $entryId;
    private $transactionType;
    private $timestamp;
    private $changedEntityId;
    private $changedEntityType;
    private $historyLogId;
    private $tripMemberId;
    private $previousSnapshotId;
    public bool $isUndoable = false;
    private $db;
    private $tripMember = null;

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

    public function getEntryId()
    {
        return $this->entryId;
    }

    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }

    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getChangedEntityId()
    {
        return $this->changedEntityId;
    }

    public function setChangedEntityId($changedEntityId)
    {
        $this->changedEntityId = $changedEntityId;
    }

    public function getChangedEntityType()
    {
        return $this->changedEntityType instanceof EntityType ? $this->changedEntityType->value : $this->changedEntityType;
    }

    public function setChangedEntityType($changedEntityType)
    {
        if (is_string($changedEntityType)) {
            $this->changedEntityType = EntityType::tryFrom($changedEntityType);
        } else {
            $this->changedEntityType = $changedEntityType;
        }
    }

    public function getHistoryLogId()
    {
        return $this->historyLogId;
    }

    public function setHistoryLogId($historyLogId)
    {
        $this->historyLogId = $historyLogId;
    }

    public function getTripMemberId()
    {
        return $this->tripMemberId;
    }

    public function setTripMemberId($tripMemberId)
    {
        $this->tripMemberId = $tripMemberId;
    }

    public function getPreviousSnapshotId()
    {
        return $this->previousSnapshotId;
    }

    public function setPreviousSnapshotId($previousSnapshotId)
    {
        $this->previousSnapshotId = $previousSnapshotId;
    }

    public function getTripMember()
    {
        if ($this->tripMember === null && $this->tripMemberId) {
            $member = new TripMember();
            if ($member->read($this->tripMemberId)) {
                $this->tripMember = $member;
            }
        }
        return $this->tripMember;
    }

    public function getRelatedEntity()
    {
        $type = $this->changedEntityType instanceof EntityType ? $this->changedEntityType : EntityType::tryFrom((string)$this->getChangedEntityType());
        switch ($type) {
            case EntityType::ACTIVITY:
                $activity = new Activity();

                if ($activity->read($this->changedEntityId)) {
                    return $activity;
                }

                return null;

            default:
                return null;
        }
    }

    public function getEntitySummary(): string
    {
        $id = $this->changedEntityId;
        $type = $this->changedEntityType instanceof EntityType ? $this->changedEntityType : EntityType::tryFrom((string)$this->getChangedEntityType());
        switch ($type) {
            case EntityType::ACTIVITY:
                $item = Activity::getByActivityId($id);
                return $item ? $item->getName() : 'Unknown Activity';
            case EntityType::EXPENSE:
                $item = (new Expense())->findById($id);
                return $item ? $item->getDescription() . ' (' . $item->getAmount() . ' ' . $item->getCurrencyType() . ')' : 'Unknown Expense';
            case EntityType::INVENTORY_ITEM:
                $item = new InventoryItem();
                return ($item->read($id, true)) ? $item->getName() : 'Unknown Item';
            case EntityType::FUND_CONTRIBUTION:
                $item = new FundContribution();
                return ($item->read($id)) ? 'Contribution of ' . $item->getAmount() : 'Unknown Contribution';
            case EntityType::TRIP_MEMBER:
                $item = new TripMember();
                return ($item->read($id)) ? $item->getDisplayName() : 'Unknown Member';
            case EntityType::SETTLEMENT_PAYMENT:
                $item = new SettlementPayment();
                return ($item->read($id)) ? 'Settlement of ' . $item->getAmount() : 'Unknown Settlement';
        }
        return 'Unknown Entity';
    }

    public static function getFormattedDateHeader(string $dateStr): string
    {
        $date      = new DateTime($dateStr);
        $today     = new DateTime('today');
        $yesterday = new DateTime('yesterday');

        if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
            return 'Today';
        }

        if ($date->format('Y-m-d') === $yesterday->format('Y-m-d')) {
            return 'Yesterday';
        }

        return $date->format('F j, Y');
    }

    public function fill(array $data)
    {
        $this->id                 = $data['id'];
        $this->entryId            = $data['entryId'];
        $this->transactionType    = $data['transactionType'];
        $this->timestamp          = $data['timestamp'];
        $this->changedEntityId    = $data['changedEntityId'];
        $this->changedEntityType  = EntityType::tryFrom($data['changedEntityType']);
        $this->historyLogId       = $data['historyLogId'];
        $this->tripMemberId       = $data['tripMemberId'];
        $this->previousSnapshotId = $data['previousSnapshotId'];
    }

    public function create()
    {
        $this->entryId = uniqid('entry_');
        $sql           = "INSERT INTO HistoryLogEntry (entryId, transactionType, timestamp, changedEntityId, changedEntityType, historyLogId, tripMemberId, previousSnapshotId)
                VALUES (:entryId, :transactionType, NOW(), :changedEntityId, :changedEntityType, :historyLogId, :tripMemberId, :previousSnapshotId)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':entryId'            => $this->entryId,
            ':transactionType'    => $this->transactionType,
            ':changedEntityId'    => $this->changedEntityId,
            ':changedEntityType'  => $this->getChangedEntityType(),
            ':historyLogId'       => $this->historyLogId,
            ':tripMemberId'       => $this->tripMemberId,
            ':previousSnapshotId' => $this->previousSnapshotId,
        ]);
    }

    public static function findLatestForEntity($entityId, $entityType)
    {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM HistoryLogEntry
                WHERE changedEntityId = :entityId AND changedEntityType = :entityType
                ORDER BY timestamp DESC LIMIT 1";
        
        $typeValue = $entityType instanceof EntityType ? $entityType->value : $entityType;
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':entityId'   => $entityId,
            ':entityType' => $typeValue,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $entry = new self();
            $entry->fill($data);
            return $entry;
        }
        return null;
    }

    public static function getAllByItineraryId($itineraryId, array $filters = [])
    {
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT hle.* FROM HistoryLogEntry hle
                JOIN HistoryLog hl ON hle.historyLogId = hl.id
                WHERE hl.itineraryId = :itineraryId";
        
        $params = [':itineraryId' => $itineraryId];

        if (!empty($filters['entityType'])) {
            $sql .= " AND hle.changedEntityType = :entityType";
            $params[':entityType'] = $filters['entityType'];
        }

        if (!empty($filters['memberId'])) {
            $sql .= " AND hle.tripMemberId = :memberId";
            $params[':memberId'] = $filters['memberId'];
        }

        if (!empty($filters['action'])) {
            switch ($filters['action']) {
                case 'additions':
                    $sql .= " AND (hle.transactionType LIKE '%ADDED%' OR hle.transactionType LIKE '%CREATED%' OR hle.transactionType LIKE '%JOINED%')";
                    break;
                case 'removals':
                    $sql .= " AND (hle.transactionType LIKE '%REMOVED%' OR hle.transactionType LIKE '%DELETED%' OR hle.transactionType LIKE '%LEFT%') AND hle.transactionType != 'MEMBER_REMOVED'";
                    break;
                case 'edits':
                    $sql .= " AND (hle.transactionType LIKE '%VOLUNTEERED%' OR hle.transactionType LIKE '%SETTLEMENT%' OR hle.transactionType = 'MEMBER_REMOVED')";
                    break;
                case 'rollbacks':
                    $sql .= " AND hle.transactionType LIKE '%RESTORED%'";
                    break;
            }
        }
        $sql .= " ORDER BY hle.timestamp DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $entries = [];
        foreach ($results as $data) {
            $entry = new self();
            $entry->fill($data);
            $entries[] = $entry;
        }
        return $entries;
    }

    public static function findByEntryId($entryId)
    {
        $db   = Database::getInstance()->getConnection();
        $sql  = "SELECT * FROM HistoryLogEntry WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $entryId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $entry = new self();
            $entry->fill($data);
            return $entry;
        }
        return null;
    }
}
