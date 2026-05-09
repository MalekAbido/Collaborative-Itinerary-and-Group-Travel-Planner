<?php
namespace App\Controllers;

use App\Enums\ActivityStatus;
use App\Enums\EntityType;
use App\Enums\TransactionType;
use App\Enums\TripMemberRole;
use App\Helpers\Auth;
use App\Helpers\HistoryLogger;
use App\Helpers\Session;
use App\Models\Activity;
use App\Models\Expense;
use App\Models\FundContribution;
use App\Models\HistoryLogEntry;
use App\Models\InventoryItem;
use Core\Controller;

class HistoryController extends Controller
{

    public function index($itineraryId)
    {
        $member = Auth::requireMembership($itineraryId);

        $entries = HistoryLogEntry::getAllByItineraryId($itineraryId);

        $groupedEntries = [];
        $counts         = ['additions' => 0, 'removals' => 0, 'rollbacks' => 0];

        $undoableTypes = [
            TransactionType::REMOVED_ACTIVITY->value,
            TransactionType::DELETED_EXPENSE->value,
            TransactionType::DELETED_FUND_CONTRIBUTION->value,
            TransactionType::REMOVED_INVENTORY_ITEM->value,
        ];

        $processedEntities = [];

        foreach ($entries as $entry) {
            $key = $entry->getChangedEntityType() . '_' . $entry->getChangedEntityId();

            if (! isset($processedEntities[$key])) {
                $processedEntities[$key] = true;

                if (in_array($entry->getTransactionType(), $undoableTypes)) {
                    $entry->isUndoable = true;
                }
            }

            $date                    = date('Y-m-d', strtotime($entry->getTimestamp()));
            $groupedEntries[$date][] = $entry;

            $type = $entry->getTransactionType();

            if (str_contains($type, 'ADDED') || str_contains($type, 'CREATED')) {
                $counts['additions']++;
            } elseif (str_contains($type, 'REMOVED') || str_contains($type, 'DELETED')) {
                $counts['removals']++;
            } elseif (str_contains($type, 'RESTORED')) {
                $counts['rollbacks']++;
            } else {
                if (!isset($counts['updated'])) $counts['updated'] = 0;
                $counts['updated']++;
            }
        }

        $this->view('history/dashboard', [
            'groupedEntries' => $groupedEntries,
            'counts'         => $counts,
            'itineraryId'    => $itineraryId,
            'memberRole'     => $member->getRole(),
            'activeTab'      => 'history',
        ]);
    }

    public function revert($itineraryId, $entryId)
    {
        $member = Auth::requireMembership($itineraryId);
        Auth::requireRole(TripMemberRole::EDITOR->value, $member->getRole());

        $entry = HistoryLogEntry::findByEntryId($entryId);

        if (! $entry) {
            Session::setFlash(Session::FLASH_ERROR, 'Log entry not found.');
            header("Location: /itinerary/{$itineraryId}/history");
            exit;
        }

        $latest = HistoryLogEntry::findLatestForEntity($entry->getChangedEntityId(), $entry->getChangedEntityType());

        if ($latest->getId() !== $entry->getId()) {
            Session::setFlash(Session::FLASH_ERROR, 'Only the most recent action for this entity can be undone.');
            header("Location: /itinerary/{$itineraryId}/history");
            exit;
        }

        $success = false;
        $message = "Action reverted successfully.";

        switch (TransactionType::tryFrom($entry->getTransactionType())) {
            case TransactionType::REMOVED_ACTIVITY:
                $activity = Activity::getByActivityId($entry->getChangedEntityId());

                if ($activity) {
// if ($activity->hasOverlap($itineraryId, $activity->getStartTime(), $activity->getEndTime())) {
                    if (false) {
                        Session::setFlash(Session::FLASH_ERROR, 'Cannot restore activity: It overlaps with another existing activity.');
                        header("Location: /itinerary/{$itineraryId}/history");
                        exit;
                    }

                    $success = $activity->updateStatus(ActivityStatus::CONFIRMED);
                    if ($success) {
                        HistoryLogger::log($itineraryId, TransactionType::RESTORED_ACTIVITY, $activity, $member->getId());
                    }
                }

                break;

            case TransactionType::DELETED_EXPENSE:
                $expense = (new Expense())->findById($entry->getChangedEntityId());
                if ($expense) {
                    $expense->setDeletedAt(null);
                    $success = $expense->update();
                    if ($success) {
                        HistoryLogger::log($itineraryId, TransactionType::RESTORED_EXPENSE, $expense, $member->getId());
                    }
                }

                break;

            case TransactionType::REMOVED_INVENTORY_ITEM:
                $item = new InventoryItem();
                if ($item->read($entry->getChangedEntityId(), true)) {
                    $item->setDeletedAt(null);
                    $success = $item->update();
                    if ($success) {
                        HistoryLogger::log($itineraryId, TransactionType::RESTORED_INVENTORY_ITEM, $item, $member->getId());
                    }
                }
                break;

            case TransactionType::DELETED_FUND_CONTRIBUTION:
                $contribution = new FundContribution();
                if ($contribution->read($entry->getChangedEntityId())) {
                    $contribution->setDeletedAt(null);
                    $success = $contribution->update();
                    if ($success) {
                        // Update balance
                        $db = \Core\Database::getInstance()->getConnection();
                        $sql = "UPDATE GroupFund SET currentBalance = currentBalance + :amount WHERE id = :id";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([':amount' => $contribution->getAmount(), ':id' => $contribution->getGroupFundId()]);

                        HistoryLogger::log($itineraryId, TransactionType::RESTORED_FUND_CONTRIBUTION, $contribution, $member->getId());
                    }
                }

                break;

            default:
                Session::setFlash(Session::FLASH_ERROR, 'This action cannot be undone.');
                header("Location: /itinerary/{$itineraryId}/history");
                exit;
        }

        if ($success) {
            Session::setFlash(Session::FLASH_SUCCESS, $message);
        } else {
            Session::setFlash(Session::FLASH_ERROR, 'Failed to revert the action.');
        }

        header("Location: /itinerary/{$itineraryId}/history");
        exit;
    }
}
