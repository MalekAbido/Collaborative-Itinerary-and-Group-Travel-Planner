<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Breakdown</title>
</head>
<body>
    <h1>Expense Receipt</h1>

    <!-- Parent Expense Details -->
    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h2><?= htmlspecialchars($expense->getDescription()) ?></h2>
        <p><strong>Category:</strong> <?= htmlspecialchars($expense->getCategory()) ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($expense->getAmount(), 2) ?></p>
        
        <!-- Added the new fields you put in the Controller! -->
        <p><strong>Currency:</strong> <?= htmlspecialchars($expense->getCurrencyType()) ?></p>
        <p><strong>Non-Cash:</strong> <?= $expense->getIsNonCash() ? 'Yes' : 'No' ?></p>
    </div>

    <!-- Payer Section -->
    <div style="background-color: #e6f7ff; padding: 15px; margin-bottom: 20px;">
        <h3>Who Paid?</h3>
        <?php if ($payer): ?>
            <p>Member ID <?= $payer->getTripMemberId() ?> fronted the cash for this expense.</p>
            <p>Their personal share of the bill was: $<?= number_format($payer->getAmount(), 2) ?></p>
        <?php else: ?>
            <p>Error: No payer found for this expense.</p>
        <?php endif; ?>
    </div>

    <!-- Debtors Section (The Breakdown) -->
    <div style="background-color: #fff1f0; padding: 15px; margin-bottom: 20px;">
        <h3>Who Owes What?</h3>
        <?php if (empty($debtors)): ?>
            <p>No one else was involved in this expense.</p>
        <?php else: ?>
            <ul>
                <!-- Loop through the debtors array using PHP -->
                <?php foreach ($debtors as $debtor): ?>
                    <li>
                        Member ID <?= $debtor->getTripMemberId() ?> owes 
                        <strong>$<?= number_format($debtor->getAmount(), 2) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Delete Button Form -->
    <form action="/finance/expense/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
        <!-- Make sure to use ->getId() here! -->
        <input type="hidden" name="expenseId" value="<?= $expense->getId() ?>">
        <button type="submit" style="color: white; background-color: red; padding: 10px;">
            Delete This Expense
        </button>
    </form>
    
    <br>
    <a href="/finance/dashboard">Back to Dashboard</a>
</body>
</html>