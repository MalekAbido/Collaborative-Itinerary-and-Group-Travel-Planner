<?php

namespace App\Enums;

enum EntityType: string
{
    case ACTIVITY = 'Activity';
    case EXPENSE = 'Expense';
    case FUND_CONTRIBUTION = 'FundContribution';
    case INVENTORY_ITEM = 'InventoryItem';
    case TRIP_MEMBER = 'TripMember';
    case SETTLEMENT_PAYMENT = 'SettlementPayment';

    public function label(): string
    {
        return match($this) {
            self::ACTIVITY => 'Activity',
            self::EXPENSE => 'Expense',
            self::FUND_CONTRIBUTION => 'Fund Contribution',
            self::INVENTORY_ITEM => 'Inventory Item',
            self::TRIP_MEMBER => 'Trip Member',
            self::SETTLEMENT_PAYMENT => 'Settlement Payment',
        };
    }
}
