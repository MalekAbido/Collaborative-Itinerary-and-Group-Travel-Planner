<?php

namespace App\Enums;

enum EntityType: string
{
    case ACTIVITY = 'Activity';
    case EXPENSE = 'Expense';
    case FUND_CONTRIBUTION = 'FundContribution';
    case INVENTORY_ITEM = 'InventoryItem';

    public function label(): string
    {
        return match($this) {
            self::ACTIVITY => 'Activity',
            self::EXPENSE => 'Expense',
            self::FUND_CONTRIBUTION => 'Fund Contribution',
            self::INVENTORY_ITEM => 'Inventory Item',
        };
    }
}
