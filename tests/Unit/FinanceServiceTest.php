<?php

use PHPUnit\Framework\TestCase;
use App\Services\FinanceService;
use App\Models\Expense;
use Core\Database;

class FinanceServiceTest extends TestCase
{
    public function testComputeMinimalTransactions()
    {
        $service = new FinanceService();

        // Path 1: Valid net-zero
        $balances = ['A' => 10, 'B' => -10];
        $res = $service->computeMinimalTransactions($balances);
        $this->assertTrue($res['success']);

        // Path 2: Non-zero sum
        $balances = ['A' => 10, 'B' => -5];
        $res = $service->computeMinimalTransactions($balances);
        $this->assertFalse($res['success']);
    }

    public function testCalculateBalances()
    {
        $service = new FinanceService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('calculateBalances');
        
        Database::$nextResult = [
            ['id' => 1, 'shareId' => 'SHR-1', 'amount' => 100, 'isPayer' => 1, 'expenseId' => 1, 'tripMemberId' => 1]
        ];

        $e = new Expense();
        $e->setPaidByKitty(false);
        $e->setAmount(100);

        $balances = $method->invoke($service, [$e]);
        $this->assertArrayHasKey(1, $balances);
        $this->assertEquals(0, $balances[1]);
    }
}
