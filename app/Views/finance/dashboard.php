<?php
// Calculate the percentage for the progress bar
$percentage = 0;
if ($totalBudget > 0) {
    $percentage = min(100, round(($actualSpending / $totalBudget) * 100));
}

// Determine progress bar color based on percentage
$barColor = 'bg-primary'; // Default
if ($percentage >= 100) {
    $barColor = 'bg-error';
} elseif ($percentage >= 80) {
    $barColor = 'bg-secondary-container'; // Warning approaching limit
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Finance Dashboard - ItineraryPlanner</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="/assets/css/tailwind.css">
</head>

<body class="bg-background text-on-background font-body text-body-md min-h-screen flex flex-col m-0">
    
    <header class="w-full h-16 z-50 flex items-center justify-between px-6 lg:px-8 bg-surface-container-lowest border-b border-outline-variant shadow-sm">
        <div class="text-[22px] font-display font-extrabold text-primary tracking-tight">ItineraryPlanner</div>
        <div class="flex items-center gap-4">
            <a href="/dashboard" class="text-body-sm font-semibold text-outline hover:text-primary transition">Back to Trips</a>
        </div>
    </header>

    <main class="grow py-8 bg-surface">
        <div class="max-w-content mx-auto p-6 lg:p-8">

            <header class="mb-8">
                <div class="flex items-center gap-2 text-body-sm text-outline mb-2">
                    <a href="/trips" class="hover:text-primary">Trips</a>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <a href="/itinerary/<?= htmlspecialchars($itineraryId) ?>" class="hover:text-primary">Itinerary #<?= htmlspecialchars($itineraryId) ?></a>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span class="text-on-surface font-medium">Finance Overview</span>
                </div>
                <h1 class="font-display text-h1 text-on-surface mb-2">Finance Dashboard</h1>
                <p class="text-body-lg text-on-surface-variant">Track group expenses and monitor budget limits.</p>
            </header>

            <?php if ($alert['status'] === 'warning'): ?>
            <div class="mb-8 flex items-center gap-3 rounded-xl border border-secondary/30 bg-secondary-fixed px-4 py-3 shadow-sm">
                <span class="material-symbols-outlined text-secondary">warning</span>
                <div class="flex-1">
                    <span class="block text-label-xs uppercase text-secondary font-bold">Budget Warning</span>
                    <h4 class="text-body-md font-semibold text-on-secondary-container m-0">
                        <?= htmlspecialchars($alert['message']) ?>
                    </h4>
                </div>
            </div>
            <?php elseif ($percentage >= 100): ?>
            <div class="mb-8 flex items-center gap-3 rounded-xl border border-error/30 bg-error-container px-4 py-3 shadow-sm">
                <span class="material-symbols-outlined text-error">error</span>
                <div class="flex-1">
                    <span class="block text-label-xs uppercase text-error font-bold">Over Budget</span>
                    <h4 class="text-body-md font-semibold text-on-error-container m-0">
                        You have exceeded the total budget limit of <?= htmlspecialchars($totalBudget . ' ' . $baseCurrency) ?>.
                    </h4>
                </div>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-label-caps uppercase text-outline">Total Spent</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-fixed">
                            <span class="material-symbols-outlined text-[18px] text-primary">payments</span>
                        </div>
                    </div>
                    <div class="font-display text-[28px] font-extrabold text-on-surface">
                        <?= number_format($actualSpending) ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-label-caps uppercase text-outline">Budget Limit</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-highest">
                            <span class="material-symbols-outlined text-[18px] text-outline">account_balance</span>
                        </div>
                    </div>
                    <div class="font-display text-[28px] font-extrabold text-on-surface">
                        <?= $totalBudget > 0 ? number_format($totalBudget) : 'No Limit' ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-label-caps uppercase text-outline">Remaining</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-success-container">
                            <span class="material-symbols-outlined text-[18px] text-on-success-container">savings</span>
                        </div>
                    </div>
                    <div class="font-display text-[28px] font-extrabold <?= ($totalBudget - $actualSpending) < 0 ? 'text-error' : 'text-on-surface' ?>">
                        <?= $totalBudget > 0 ? number_format($totalBudget - $actualSpending) : '∞' ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
                    </div>
                </div>
            </div>

            <?php if ($totalBudget > 0): ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">
                <h3 class="font-display text-h4 text-on-surface mb-5">Budget Usage</h3>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-label-caps uppercase text-outline">0 <?= htmlspecialchars($baseCurrency) ?></span>
                        <span class="text-body-sm font-semibold <?= $percentage >= 100 ? 'text-error' : 'text-primary' ?>">
                            <?= $percentage ?>% Used
                        </span>
                        <span class="text-label-caps uppercase text-outline"><?= number_format($totalBudget) ?> <?= htmlspecialchars($baseCurrency) ?></span>
                    </div>
                    <div class="h-3 w-full rounded-full bg-outline-variant overflow-hidden">
                        <div class="h-full rounded-full <?= $barColor ?> transition-all" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>

<div class="max-w-content mx-auto p-6 lg:p-8">

    <header class="mb-8">
        <div class="flex items-center gap-2 text-body-sm text-outline mb-2">
            <a href="/trips" class="hover:text-primary">Trips</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="/itinerary/<?= htmlspecialchars($itineraryId) ?>" class="hover:text-primary">Itinerary #<?= htmlspecialchars($itineraryId) ?></a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-medium">Finance Overview</span>
        </div>
        <h1 class="font-display text-h1 text-on-surface mb-2">Finance Dashboard</h1>
        <p class="text-body-lg text-on-surface-variant">Track group expenses and monitor budget limits.</p>
    </header>

    <?php if ($alert['status'] === 'warning'): ?>
    <div class="mb-8 flex items-center gap-3 rounded-xl border border-secondary/30 bg-secondary-fixed px-4 py-3 shadow-sm">
        <span class="material-symbols-outlined text-secondary">warning</span>
        <div class="flex-1">
            <span class="block text-label-xs uppercase text-secondary font-bold">Budget Warning</span>
            <h4 class="text-body-md font-semibold text-on-secondary-container m-0">
                <?= htmlspecialchars($alert['message']) ?>
            </h4>
        </div>
    </div>
    <?php elseif ($percentage >= 100): ?>
    <div class="mb-8 flex items-center gap-3 rounded-xl border border-error/30 bg-error-container px-4 py-3 shadow-sm">
        <span class="material-symbols-outlined text-error">error</span>
        <div class="flex-1">
            <span class="block text-label-xs uppercase text-error font-bold">Over Budget</span>
            <h4 class="text-body-md font-semibold text-on-error-container m-0">
                You have exceeded the total budget limit of <?= htmlspecialchars($totalBudget . ' ' . $baseCurrency) ?>.
            </h4>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-label-caps uppercase text-outline">Total Spent</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-fixed">
                    <span class="material-symbols-outlined text-[18px] text-primary">payments</span>
                </div>
            </div>
            <div class="font-display text-[28px] font-extrabold text-on-surface">
                <?= number_format($actualSpending) ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-label-caps uppercase text-outline">Budget Limit</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-highest">
                    <span class="material-symbols-outlined text-[18px] text-outline">account_balance</span>
                </div>
            </div>
            <div class="font-display text-[28px] font-extrabold text-on-surface">
                <?= $totalBudget > 0 ? number_format($totalBudget) : 'No Limit' ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-label-caps uppercase text-outline">Remaining</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-success-container">
                    <span class="material-symbols-outlined text-[18px] text-on-success-container">savings</span>
                </div>
            </div>
            <div class="font-display text-[28px] font-extrabold <?= ($totalBudget - $actualSpending) < 0 ? 'text-error' : 'text-on-surface' ?>">
                <?= $totalBudget > 0 ? number_format($totalBudget - $actualSpending) : '∞' ?> <span class="text-h4 text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
            </div>
        </div>
    </div>

    <?php if ($totalBudget > 0): ?>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">
        <h3 class="font-display text-h4 text-on-surface mb-5">Budget Usage</h3>
        <div>
            <div class="flex justify-between mb-2">
                <span class="text-label-caps uppercase text-outline">0 <?= htmlspecialchars($baseCurrency) ?></span>
                <span class="text-body-sm font-semibold <?= $percentage >= 100 ? 'text-error' : 'text-primary' ?>">
                    <?= $percentage ?>% Used
                </span>
                <span class="text-label-caps uppercase text-outline"><?= number_format($totalBudget) ?> <?= htmlspecialchars($baseCurrency) ?></span>
            </div>
            <div class="h-3 w-full rounded-full bg-outline-variant overflow-hidden">
                <div class="h-full rounded-full <?= $barColor ?> transition-all" style="width: <?= $percentage ?>%"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
