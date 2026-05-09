<?php

namespace App\Enums;

enum TransactionType: string
{
    case ADDED_ACTIVITY = 'ADDED_ACTIVITY';
    case REMOVED_ACTIVITY = 'REMOVED_ACTIVITY';
    case RESTORED_ACTIVITY = 'RESTORED_ACTIVITY';
    case ADDED_EXPENSE = 'ADDED_EXPENSE';
    case DELETED_EXPENSE = 'DELETED_EXPENSE';
    case RESTORED_EXPENSE = 'RESTORED_EXPENSE';
    case ADDED_FUND_CONTRIBUTION = 'ADDED_FUND_CONTRIBUTION';
    case DELETED_FUND_CONTRIBUTION = 'DELETED_FUND_CONTRIBUTION';
    case RESTORED_FUND_CONTRIBUTION = 'RESTORED_FUND_CONTRIBUTION';
    case SETTLEMENT_PAID = 'SETTLEMENT_PAID';
    case VOLUNTEERED_FOR_ITEM = 'VOLUNTEERED_FOR_ITEM';
    case UNVOLUNTEERED_FOR_ITEM = 'UNVOLUNTEERED_FOR_ITEM';
    case REMOVED_INVENTORY_ITEM = 'REMOVED_INVENTORY_ITEM';
    case RESTORED_INVENTORY_ITEM = 'RESTORED_INVENTORY_ITEM';
    case MEMBER_JOINED = 'MEMBER_JOINED';
    case MEMBER_LEFT = 'MEMBER_LEFT';
    case MEMBER_REMOVED = 'MEMBER_REMOVED';

    public function label(): string
    {
        return match($this) {
            self::ADDED_ACTIVITY => 'Added Activity',
            self::REMOVED_ACTIVITY => 'Removed Activity',
            self::RESTORED_ACTIVITY => 'Restored Activity',
            self::ADDED_EXPENSE => 'Added Expense',
            self::DELETED_EXPENSE => 'Deleted Expense',
            self::RESTORED_EXPENSE => 'Restored Expense',
            self::ADDED_FUND_CONTRIBUTION => 'Added Fund Contribution',
            self::DELETED_FUND_CONTRIBUTION => 'Deleted Fund Contribution',
            self::RESTORED_FUND_CONTRIBUTION => 'Restored Fund Contribution',
            self::SETTLEMENT_PAID => 'Paid Settlement',
            self::VOLUNTEERED_FOR_ITEM => 'Volunteered for Item',
            self::UNVOLUNTEERED_FOR_ITEM => 'Unvolunteered for Item',
            self::REMOVED_INVENTORY_ITEM => 'Removed Inventory Item',
            self::RESTORED_INVENTORY_ITEM => 'Restored Inventory Item',
            self::MEMBER_JOINED => 'Joined Itinerary',
            self::MEMBER_LEFT => 'Left Itinerary',
            self::MEMBER_REMOVED => 'Removed from Itinerary',
        };
    }
}