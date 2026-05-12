<?php

namespace App\Services;

use App\Models\SettlementPayment;
use App\Models\TripFinance;

class FinanceService
{
    private float $tolerance = 0.01;

    public function getTripSettlementLedger($itineraryId): array
    {
        $finance = new TripFinance();
        if (!$finance->readByItinerary($itineraryId)) {
            return [
                'success' => false,
                'message' => 'Unable to load finance records for this itinerary.',
                'transactions' => [],
                'balances' => []
            ];
        }

        $balances = $this->calculateBalances($finance->getExpenses());
        $paidSettlements = SettlementPayment::getPaidByItinerary($itineraryId);

        foreach ($paidSettlements as $payment) {
            $fromId = $payment->getFromTripMemberId();
            $toId = $payment->getToTripMemberId();
            $amount = $payment->getAmount();

            $balances[$fromId] = round(($balances[$fromId] ?? 0) + $amount, 2);
            $balances[$toId] = round(($balances[$toId] ?? 0) - $amount, 2);
        }
        return $this->computeMinimalTransactions($balances);
    }

    private function calculateBalances(array $expenses): array
    {
        $balances = [];
        foreach ($expenses as $expense) {
            if ($expense->getPaidByKitty()) {
                continue;
            }

            $expense->loadShares($expense->getId());
            $payerShareTotal = 0;
            $payerId = null;

            foreach ($expense->getExpenseShares() as $share) {
                if ($share->getIsPayer()) {
                    $payerShareTotal = round($payerShareTotal + $share->getAmount(), 2);
                    $payerId = $share->getTripMemberId();
                }
            }

            if ($payerId === null) {
                continue;
            }

            $payerBalance = round($expense->getAmount() - $payerShareTotal, 2);
            $balances[$payerId] = round(($balances[$payerId] ?? 0) + $payerBalance, 2);
            foreach ($expense->getExpenseShares() as $share) {
                if ($share->getIsPayer()) {
                    continue;
                }

                $memberId = $share->getTripMemberId();
                $balances[$memberId] = round(($balances[$memberId] ?? 0) - $share->getAmount(), 2);
            }
        }
        return $balances;
    }

    public function computeMinimalTransactions(array $balances): array
    {
        if (empty($balances)) {
            return [
                'success' => true,
                'transactions' => [],
                'balances' => []
            ];
        }

        $balances = array_map(fn($value) => round($value, 2), $balances);
        $totalBalance = round(array_sum($balances), 2);

        if (abs($totalBalance) > $this->tolerance) {
            return [
                'success' => false,
                'message' => 'Balances do not net to zero. Please review expense shares.',
                'transactions' => [],
                'balances' => $balances
            ];
        }

        $transactions = [];
        $debtors = [];
        $creditors = [];

        foreach ($balances as $userId => $balance) {
            if ($balance < 0) {
                $debtors[] = ['userId' => $userId, 'amount' => -$balance];
            } elseif ($balance > 0) {
                $creditors[] = ['userId' => $userId, 'amount' => $balance];
            }
        }

        usort($debtors, function ($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });
        usort($creditors, function ($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        $i = 0;
        $j = 0;

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor = $debtors[$i];
            $creditor = $creditors[$j];
            $amount = min($debtor['amount'], $creditor['amount']);

            $transactions[] = [
                'from' => $debtor['userId'],
                'to' => $creditor['userId'],
                'amount' => $amount
            ];

            $debtors[$i]['amount'] -= $amount;
            $creditors[$j]['amount'] -= $amount;

            if (abs($debtors[$i]['amount']) < $this->tolerance) {
                $i++;
            }
            if (abs($creditors[$j]['amount']) < $this->tolerance) {
                $j++;
            }
        }

        return [
            'success' => true,
            'transactions' => $transactions,
            'balances' => $balances
        ];
    }
}
