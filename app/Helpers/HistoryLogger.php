<?php
namespace App\Helpers;

use App\Models\HistoryLog;
use App\Models\HistoryLogEntry;
use App\Models\TransactionType;
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
        $entityType = $reflect->getShortName();
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
