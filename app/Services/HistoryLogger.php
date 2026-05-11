<?php
namespace App\Services;

use App\Models\HistoryLog;
use App\Models\HistoryLogEntry;
use App\Enums\TransactionType;
use App\Enums\EntityType;
use ReflectionClass;

class HistoryLogger
{
    public static function log($itineraryId, TransactionType $transactionType, $entity, $tripMemberId)
    {
        $historyLog = HistoryLog::findByItineraryId($itineraryId);

        if (! $historyLog) {
            $historyLog = new HistoryLog();
            $historyLog->setItineraryId($itineraryId);
            $historyLog->create();
        }

        $reflect    = new ReflectionClass($entity);
        $entityTypeName = $reflect->getShortName();
        $entityType = EntityType::tryFrom($entityTypeName);
        $entityId   = $entity->getId();

        $previousEntry      = HistoryLogEntry::findLatestForEntity($entityId, $entityType);
        $previousSnapshotId = $previousEntry ? $previousEntry->getId() : null;

        $entry = new HistoryLogEntry();
        $entry->setTransactionType($transactionType->value);
        $entry->setChangedEntityId($entityId);
        $entry->setChangedEntityType($entityType);
        $entry->setHistoryLogId($historyLog->getId());
        $entry->setTripMemberId($tripMemberId);
        $entry->setPreviousSnapshotId($previousSnapshotId);

        return $entry->create();
    }
}
