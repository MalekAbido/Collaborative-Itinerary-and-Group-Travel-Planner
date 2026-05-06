<?php
namespace App\Helpers;

use App\Models\HistoryLog;
use App\Models\HistoryLogEntry;
use App\Models\TransactionType;
use ReflectionClass;

class HistoryLogger
{
    /**
     * Log a transaction in the history.
     *
     * @param int $itineraryId
     * @param TransactionType $transactionType
     * @param object $entity
     * @param int $tripMemberId
     * @return bool
     */
    public static function log($itineraryId, TransactionType $transactionType, $entity, $tripMemberId)
    {
        // 1. Find or create the HistoryLog for the itinerary
        $historyLog = HistoryLog::findByItineraryId($itineraryId);

        if (! $historyLog) {
            $historyLog = new HistoryLog();
            $historyLog->setItineraryId($itineraryId);
            $historyLog->create();
        }

        // 2. Extract short class name from $entity
        $reflect    = new ReflectionClass($entity);
        $entityType = $reflect->getShortName();
        $entityId   = $entity->getId();

        // 3. Find the previousSnapshotId
        $previousEntry      = HistoryLogEntry::findLatestForEntity($entityId, $entityType);
        $previousSnapshotId = $previousEntry ? $previousEntry->getId() : null;

        // 4. Insert new record into HistoryLogEntry
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
