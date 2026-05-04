<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Breakdown</title>
</head>
<body>
    <h1>Expense Receipt</h1>

    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h2><?= htmlspecialchars($expense['description']) ?></h2>
        <p><strong>Category:</strong> <?= htmlspecialchars($expense['category']) ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($expense['amount'], 2) ?></p>
    </div>

    <div style="background-color: #e6f7ff; padding: 15px; margin-bottom: 20px;">
        <h3>Who Paid?</h3>
        <?php if ($payer): ?>
            <p>Member ID <?= $payer['tripMemberId'] ?> fronted the cash for this expense.</p>
            <p>Their personal share of the bill was: $<?= number_format($payer['amount'], 2) ?></p>
        <?php else: ?>
            <p>Error: No payer found for this expense.</p>
        <?php endif; ?>
    </div>

    <div style="background-color: #fff1f0; padding: 15px; margin-bottom: 20px;">
        <h3>Who Owes What?</h3>
        <?php if (empty($debtors)): ?>
            <p>No one else was involved in this expense.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($debtors as $debtor): ?>
                    <li>
                        Member ID <?= $debtor['tripMemberId'] ?> owes 
                        <strong>$<?= number_format($debtor['amount'], 2) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <form action="/finance/expense/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
        <input type="hidden" name="expenseId" value="<?= $expense['id'] ?>">
        <button type="submit" style="color: white; background-color: red; padding: 10px;">
            Delete This Expense
        </button>
    </form>
    
    <br>
    <a href="/finance/dashboard">Back to Dashboard</a>
</body>
</html>