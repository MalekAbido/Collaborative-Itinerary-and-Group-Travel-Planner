<?php

use PHPUnit\Framework\TestCase;
use App\Models\TripFinance;
use App\Models\Expense;

class TripFinanceTest extends TestCase
{
    public function testCheckBudgetAlertPaths()
    {
        $finance = new TripFinance();
        $finance->setTotalBudgetLimit(1000);

        // Path 1: Over budget
        $e1 = new Expense(); $e1->setAmount(1200);
        $finance->setExpenses([$e1]);
        $res = $finance->checkBudgetAlert();
        $this->assertEquals('warning', $res['status']);

        // Path 2: Under budget
        $e1->setAmount(800);
        $res = $finance->checkBudgetAlert();
        $this->assertEquals('ok', $res['status']);
    }
}
