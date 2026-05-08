<?php

use PHPUnit\Framework\TestCase;
use App\Models\Expense;

class ExpenseTest extends TestCase
{
    public function testUpdateRefundedAmountBoundaries()
    {
        $expense = new Expense();
        $expense->setAmount(100.00);

        // Path 1: Invalid (0)
        $this->assertFalse($expense->updateRefundedAmount(0));

        // Path 2: Invalid (> amount)
        $this->assertFalse($expense->updateRefundedAmount(101));

        // Path 3: Valid boundary
        $this->assertTrue($expense->updateRefundedAmount(50));
    }
}
