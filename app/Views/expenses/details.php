<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Breakdown - VoyageSync</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#f65a41", "error": "#ba1a1a", "on-error": "#ffffff",
                        "surface": "#fcf8f8", "surface-container-lowest": "#ffffff", 
                        "surface-container": "#edeeef", "on-surface": "#191c1d", 
                        "on-surface-variant": "#414754", "outline": "#727785", 
                        "outline-variant": "#c1c6d6", "success-container": "#bbf7d0", "on-success-container": "#064e2c"
                    },
                    fontFamily: { display: ["'Plus Jakarta Sans'", "sans-serif"], body: ["'Inter'", "sans-serif"] },
                    fontSize: {
                        "h2": ["28px", { lineHeight: "1.3", fontWeight: "600" }],
                        "h3": ["20px", { lineHeight: "1.4", fontWeight: "600" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "label-caps": ["12px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen p-6 lg:p-8">

    <main class="max-w-2xl mx-auto">
        
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-display text-h2 text-on-surface m-0">Expense Receipt</h1>
            <!-- Added your dynamic Dashboard link here! -->
            <a href="/finance/dashboard/<?= $expense->getTripFinanceId() ?>" class="text-body-sm font-semibold text-outline hover:text-primary flex items-center gap-1 transition">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Dashboard
            </a>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden mb-6">
            
            <!-- Header Section -->
            <div class="p-6 border-b border-outline-variant bg-surface/50">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="font-display text-[28px] font-bold text-on-surface mb-1"><?= htmlspecialchars($expense->getDescription()) ?></h2>
                        <span class="inline-block bg-surface-container px-2 py-1 rounded-md text-label-caps uppercase text-on-surface-variant tracking-wider">
                            <?= htmlspecialchars($expense->getCategory()) ?>
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="font-display text-[32px] font-extrabold text-primary">
                            <?= number_format($expense->getAmount(), 2) ?> <span class="text-body-sm font-medium text-outline"><?= htmlspecialchars($expense->getCurrencyType()) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Attributes Row -->
                <div class="flex gap-3 mt-4">
                    <?php if ($expense->getIsNonCash()): ?>
                        <span class="inline-flex items-center gap-1 bg-outline-variant/30 px-2 py-1 rounded text-label-caps uppercase text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px]">money_off</span> Non-Cash
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($expense->getPaidByKitty()): ?>
                        <span class="inline-flex items-center gap-1 bg-outline-variant/30 px-2 py-1 rounded text-label-caps uppercase text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px]">volunteer_activism</span> Kitty Paid
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Who Paid Section -->
                <div class="mb-8">
                    <h3 class="font-display text-h3 text-on-surface flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-success-container text-on-success-container rounded-full bg-success-container p-1 text-[18px]">account_balance_wallet</span> 
                        Who Paid?
                    </h3>
                    <?php if ($payer): ?>
                        <div class="flex justify-between items-center bg-surface p-4 rounded-lg border border-outline-variant/50">
                            <span class="text-body-lg font-semibold">Member ID <?= $payer->getTripMemberId() ?></span>
                            <div class="text-right">
                                <span class="block text-label-caps uppercase text-outline mb-0.5">Their Share</span>
                                <span class="text-body-md font-bold"><?= number_format($payer->getAmount(), 2) ?> <?= htmlspecialchars($expense->getCurrencyType()) ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-body-sm text-error">Error: No payer found for this expense.</p>
                    <?php endif; ?>
                </div>

                <!-- Who Owes What Section -->
                <div>
                    <h3 class="font-display text-h3 text-on-surface flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-outline rounded-full bg-surface-container p-1 text-[18px]">group</span> 
                        Who Owes What?
                    </h3>
                    
                    <?php if (empty($debtors)): ?>
                        <p class="text-body-md text-on-surface-variant italic p-4 text-center border border-dashed border-outline-variant rounded-lg">No one else was involved in this expense.</p>
                    <?php else: ?>
                        <ul class="divide-y divide-outline-variant/50 border border-outline-variant/50 rounded-lg overflow-hidden">
                            <?php foreach ($debtors as $debtor): ?>
                                <li class="px-5 py-4 flex items-center justify-between bg-surface-container-lowest">
                                    <span class="text-body-md font-medium text-on-surface">Member ID <?= $debtor->getTripMemberId() ?></span>
                                    <span class="text-body-md font-bold text-on-surface">
                                        <?= number_format($debtor->getAmount(), 2) ?> <span class="text-body-sm font-normal text-outline"><?= htmlspecialchars($expense->getCurrencyType()) ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions Footer -->
            <div class="p-4 border-t border-outline-variant bg-surface/50 flex justify-end">
                <form action="/finance/expense/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                    <input type="hidden" name="expenseId" value="<?= $expense->getId() ?>">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-error text-white font-semibold text-body-sm px-5 py-2 shadow-sm hover:bg-error/90 transition">
                        <span class="material-symbols-outlined text-[18px]">delete</span> Delete Expense
                    </button>
                </form>
            </div>
        </div>
        
    </main>

</body>
</html>