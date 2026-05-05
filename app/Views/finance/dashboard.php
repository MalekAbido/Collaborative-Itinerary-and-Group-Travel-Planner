<?php
/**
 * @var float $totalBudget
 * @var float $actualSpending
 * @var string $baseCurrency
 * @var float $kittyBalance
 * @var array $alert
 * @var float $percentage
 * @var string $barColor
 * @var string $tripStringId
 * @var int $itineraryId
 * @var \App\Models\User $user
 * @var int|null $fundId
 * @var array $contributions
 * @var \App\Models\Expense[] $expenses  <-- Added this for Intelephense
 */

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
<html lang="en" class="light">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Finance Dashboard - VoyageSync</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f65a41", "primary-container": "#ff8b71", "primary-fixed": "#ffdad3", "on-primary": "#ffffff", "on-primary-fixed-variant": "#7b2a1a",
                        "secondary": "#825500", "secondary-container": "#feaa00", "secondary-fixed": "#ffddb3", "on-secondary": "#ffffff", "on-secondary-fixed": "#291800",
                        "tertiary": "#006a5f", "tertiary-fixed": "#8df5e4", "on-tertiary": "#ffffff", "on-tertiary-fixed-variant": "#128200",
                        "success": "#16a34a", "success-container": "#bbf7d0", "on-success": "#ffffff", "on-success-container": "#064e2c",
                        "error": "#ba1a1a", "error-container": "#ffdad6", "on-error": "#ffffff", "on-error-container": "#93000a",
                        "background": "#fcf8f8", "on-background": "#191c1d", "surface": "#fcf8f8",
                        "surface-container-lowest": "#ffffff", "surface-container-low": "#f3f4f5", "surface-container": "#edeeef", "surface-container-highest": "#e1e3e4",
                        "on-surface": "#191c1d", "on-surface-variant": "#414754", "outline": "#727785", "outline-variant": "#c1c6d6",
                    },
                    fontFamily: { display: ["'Plus Jakarta Sans'", "sans-serif"], body: ["'Inter'", "sans-serif"] },
                    fontSize: {
                        "display": ["48px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "800" }],
                        "h1": ["36px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "h2": ["28px", { lineHeight: "1.3", fontWeight: "600" }],
                        "h3": ["20px", { lineHeight: "1.4", fontWeight: "600" }],
                        "h4": ["17px", { lineHeight: "1.4", fontWeight: "600" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-xs": ["13px", { lineHeight: "1.4", fontWeight: "400" }],
                        "label-caps": ["12px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
                        "label-xs": ["11px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
                        "micro": ["10px", { lineHeight: "1", letterSpacing: "0.08em", fontWeight: "500" }],
                    },
                    spacing: { "navbar": "64px" }
                }
            }
        };
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body { height: 100%; }
            body { @apply font-body text-body-md bg-background text-on-background overflow-hidden m-0; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
            .scroll-thin::-webkit-scrollbar { width: 6px; }
            .scroll-thin::-webkit-scrollbar-track { background: transparent; }
            .scroll-thin::-webkit-scrollbar-thumb { @apply bg-outline-variant rounded-full; }
        }
    </style>
</head>
<body>

<div class="flex h-screen overflow-hidden">

    <nav class="fixed inset-x-0 top-0 z-50 h-navbar bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm">
        <div class="mx-auto flex h-full max-w-[1280px] items-center justify-between px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <a href="/home" class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
                <div class="hidden md:flex items-center gap-1">
                    <a href="/dashboard/" class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Dashboard</a>
                    <a href="/itinerary/dashboard/<?= htmlspecialchars($tripStringId) ?>" class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Itinerary</a>
                    <a href="/polls" class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Polls</a>
                    <a href="/finance/dashboard/<?= htmlspecialchars($itineraryId) ?>" class="px-3 py-2 rounded-md text-body-sm font-medium text-primary border-b-2 border-primary">Finances</a>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center gap-1 rounded-lg border-2 border-error px-3 py-1.5 text-body-xs font-bold tracking-wide text-error hover:bg-error-container transition">
                    <span class="material-symbols-outlined text-base">warning</span>SOS
                </button>
                <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                        <?= isset($user) ? strtoupper(substr($user->getFirstName(), 0, 1) . substr($user->getLastName(), 0, 1)) : 'ME' ?>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
        <div class="max-w-[1280px] mx-auto">
            
            <header class="mb-10 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h1 class="font-display text-display text-on-surface mb-2">Finance Dashboard</h1>
                    <p class="text-body-lg text-on-surface-variant">Track group expenses and monitor budget limits.</p>
                </div>
                <button type="button" onclick="document.getElementById('financeSettingsModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-body-sm px-6 py-2.5 hover:bg-surface-container transition">
                    <span class="material-symbols-outlined text-[18px]">settings</span> Settings
                </button>
            </header>

            <?php if (isset($alert) && $alert['status'] === 'warning'): ?>
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

            <!-- Group Fund Section -->
            <div class="mt-8 pt-8 border-t border-outline-variant">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-tertiary">volunteer_activism</span>
                    <h2 class="font-display text-h2 text-on-surface m-0">Group Fund (Kitty)</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="bg-tertiary-fixed border border-outline-variant rounded-xl shadow-sm p-6 flex flex-col justify-center items-center text-center">
                        <span class="text-label-caps uppercase text-on-tertiary-fixed-variant mb-2">Current Pool Balance</span>
                        <div class="font-display text-[40px] font-extrabold text-on-surface">
                            <?= number_format($kittyBalance ?? 0) ?> <span class="text-h3 text-on-surface-variant"><?= htmlspecialchars($baseCurrency) ?></span>
                        </div>
                        <p class="text-body-sm text-on-surface-variant mt-2">Available for central group expenses</p>
                    </div>

                    <div class="lg:col-span-2">
                        <?php if (isset($fundId) && $fundId): ?>
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                            <h3 class="font-display text-h4 text-on-surface mb-1">Make a Contribution</h3>
                            <p class="text-body-xs text-on-surface-variant mb-4">Add your money to the central pool.</p>
                            
                            <form action="/fund/contribute/<?= htmlspecialchars($fundId) ?>" method="POST" class="flex flex-col gap-3">
                                <input type="hidden" name="itineraryId" value="<?= htmlspecialchars($itineraryId) ?>">
                                <input type="hidden" name="userId" value="1"> <div class="flex items-center gap-2">
                                    <span class="text-body-md font-semibold text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
                                    <input type="number" name="amount" min="1" step="0.01" required placeholder="0.00" 
                                           class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-primary focus:outline-none transition">
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-tertiary text-on-tertiary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-tertiary/90 transition">
                                    Add Funds
                                </button>
                            </form>
                        </div>
                        
                        <div class="mt-6 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between">
                                <h4 class="font-display text-body-lg font-semibold text-on-surface">Recent Contributions</h4>
                                <span class="text-label-xs uppercase text-outline font-bold"><?= isset($contributions) ? count($contributions) : 0 ?> Records</span>
                            </div>
                            <?php if (empty($contributions)): ?>
                                <div class="p-6 text-center text-body-sm text-on-surface-variant">
                                    No contributions have been made yet.
                                </div>
                            <?php else: ?>
                                <ul class="divide-y divide-outline-variant/50 max-h-[300px] overflow-y-auto scroll-thin">
                                    <?php foreach ($contributions as $contribution): ?>
                                        <li class="px-5 py-3 flex items-center justify-between hover:bg-surface-container/50 transition">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-tertiary-fixed text-tertiary text-xs font-bold border border-outline-variant/30">
                                                    <?= strtoupper(substr($contribution['firstName'], 0, 1) . substr($contribution['lastName'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <p class="text-body-sm font-semibold text-on-surface m-0">
                                                        <?= htmlspecialchars($contribution['firstName'] . ' ' . $contribution['lastName']) ?>
                                                    </p>
                                                    <p class="text-label-xs text-outline m-0 mt-0.5">
                                                        <?= date('M j, Y - g:i A', strtotime($contribution['timestamp'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-body-md font-bold text-on-surface">
                                                +<?= number_format($contribution['amount']) ?> <span class="text-body-xs font-normal text-outline"><?= htmlspecialchars($baseCurrency) ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 flex flex-col justify-center items-center text-center text-on-surface-variant min-h-[160px]">
                            <span class="material-symbols-outlined text-[40px] mb-3 text-outline">account_balance_wallet</span>
                            <p class="text-body-md mb-4">No Group Fund exists for this trip yet.</p>
                            <form action="/finance/create-fund/<?= htmlspecialchars($itineraryId) ?>" method="POST">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-tertiary text-on-tertiary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-tertiary/90 transition">
                                    <span class="material-symbols-outlined text-base">add</span> Set up Group Fund
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Expenses Section -->
            <div class="mt-8 pt-8 border-t border-outline-variant">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        <h2 class="font-display text-h2 text-on-surface m-0">Expenses</h2>
                    </div>
                    <a href="/finance/expense/add/<?= htmlspecialchars($itineraryId) ?>" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                        <span class="material-symbols-outlined text-[18px]">add</span> Add Expense
                    </a>
                </div>

                <?php if (empty($expenses)): ?>
                    <div class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-outline-variant rounded-xl bg-surface-container-lowest text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-container mb-4">
                            <span class="material-symbols-outlined text-[32px] text-outline">receipt_long</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-1">No Expenses Yet</h3>
                        <p class="text-body-md text-on-surface-variant mb-4">Start tracking what the group spends.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($expenses as $expense): ?>
                            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 flex flex-col hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 pr-4">
                                        <h4 class="font-display text-h4 text-on-surface leading-tight mb-1"><?= htmlspecialchars($expense->getDescription()) ?></h4>
                                        <span class="inline-block bg-surface-container-highest px-2 py-0.5 rounded-md text-label-xs uppercase text-on-surface-variant tracking-wider">
                                            <?= htmlspecialchars($expense->getCategory()) ?>
                                        </span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-display text-h3 font-extrabold text-on-surface">
                                            <?= number_format($expense->getAmount(), 2) ?> <span class="text-body-xs font-normal text-outline"><?= htmlspecialchars($expense->getCurrencyType()) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Partial Refund Warning Box -->
                                <?php if ($expense->getRefundedAmount() > 0): ?>
                                    <div class="mt-3 bg-secondary-fixed text-on-secondary-fixed px-3 py-2.5 rounded-lg text-body-sm border border-secondary/20">
                                        <div class="flex items-center gap-1.5 font-bold mb-0.5">
                                            <span class="material-symbols-outlined text-[18px]">currency_exchange</span>
                                            Refund Applied: <?= number_format($expense->getRefundedAmount(), 2) ?> <?= htmlspecialchars($expense->getCurrencyType()) ?>
                                        </div>
                                        <div class="text-label-xs opacity-80 uppercase tracking-wide">
                                            Original Total: <?= number_format($expense->getAmount() + $expense->getRefundedAmount(), 2) ?> <?= htmlspecialchars($expense->getCurrencyType()) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Card Actions -->
                                <div class="mt-auto pt-5 flex items-center justify-between gap-3 border-t border-outline-variant/50">
                                    <a href="/finance/expense/details?id=<?= htmlspecialchars($expense->getId()) ?>" class="text-body-sm font-semibold text-primary hover:underline group flex items-center gap-1">
                                        View Details <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                                    </a>
                                    
                                    <?php if ($expense->getAmount() > 0): ?>
                                        <button onclick="openRefundModal(<?= htmlspecialchars($expense->getId()) ?>, <?= htmlspecialchars($expense->getAmount()) ?>)" class="inline-flex items-center gap-1.5 text-body-sm font-semibold text-outline hover:text-secondary transition">
                                            <span class="material-symbols-outlined text-[18px]">undo</span> Partial Refund
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-success text-body-sm font-bold bg-success-container px-2 py-1 rounded-md">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span> Fully Refunded
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- Finance Settings Modal -->
<div id="financeSettingsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-on-background/50 backdrop-blur-sm transition-opacity">
    <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-lg border border-outline-variant overflow-hidden">
        <div class="flex items-center justify-between border-b border-outline-variant px-6 py-4 bg-surface">
            <h3 class="font-display text-h3 text-on-surface m-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">settings</span> Finance Settings
            </h3>
            <button type="button" onclick="document.getElementById('financeSettingsModal').classList.add('hidden')" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6">
            <form action="/finance/update-settings/<?= htmlspecialchars($itineraryId) ?>" method="POST" class="flex flex-col gap-5">
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Base Currency</label>
                    <select name="baseCurrency" required class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-primary focus:outline-none transition">
                        <?php
                        $currencies = ['USD' => 'US Dollar (USD)', 'EUR' => 'Euro (EUR)', 'GBP' => 'British Pound (GBP)', 'JPY' => 'Japanese Yen (JPY)', 'EGP' => 'Egyptian Pound (EGP)', 'AED' => 'Egyptian Pound (AED)', 'SAR' => 'Saudi Riyal (SAR)', 'CAD' => 'Canadian Dollar (CAD)', 'AUD' => 'Canadian Dollar (AUD)', 'CHF' => 'Swiss Franc (CHF)', 'CNY' => 'Chinese Yuan (CNY)', 'INR' => 'Chinese Yuan (INR)'];
                        foreach ($currencies as $code => $name):
                            $selected = ($baseCurrency === $code) ? 'selected' : '';
                            echo "<option value=\"$code\" $selected>$name</option>";
                        endforeach;
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Total Budget Limit</label>
                    <input type="number" name="budgetLimit" min="0" step="0.01" value="<?= htmlspecialchars($totalBudget) ?>" placeholder="0.00" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-primary focus:outline-none transition">
                    <p class="text-body-xs text-outline mt-2">Set to 0 for no limit.</p>
                </div>
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('financeSettingsModal').classList.add('hidden')" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface font-semibold text-body-sm hover:bg-surface-container transition">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-on-background/50 backdrop-blur-sm transition-opacity">
    <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-lg border border-outline-variant overflow-hidden">
        <div class="flex items-center justify-between border-b border-outline-variant px-6 py-4 bg-surface">
            <h3 class="font-display text-h3 text-on-surface m-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">currency_exchange</span> Apply Refund
            </h3>
            <button type="button" onclick="closeRefundModal()" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6">
            <form action="/finance/expense/refund" method="POST" class="flex flex-col gap-5">
                <input type="hidden" name="expenseId" id="refundExpenseId" value="">
                
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Amount to Refund</label>
                    <div class="flex items-center gap-2">
                        <span class="text-body-md font-semibold text-outline"><?= htmlspecialchars($baseCurrency ?? '$') ?></span>
                        <input type="number" name="refundAmount" id="refundAmountInput" min="0.01" step="0.01" required placeholder="0.00" 
                               class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-secondary focus:ring-secondary focus:outline-none transition">
                    </div>
                    <p class="text-body-xs text-outline mt-2 font-medium">Maximum refund available: <span id="maxRefundDisplay" class="text-on-surface"></span></p>
                </div>
                
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" onclick="closeRefundModal()" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface font-semibold text-body-sm hover:bg-surface-container transition">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-secondary text-on-secondary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-secondary-container transition">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handles populating and opening the refund modal
    function openRefundModal(expenseId, maxAmount) {
        document.getElementById('refundExpenseId').value = expenseId;
        
        const input = document.getElementById('refundAmountInput');
        input.max = maxAmount;
        input.value = ''; // Reset on open
        
        document.getElementById('maxRefundDisplay').innerText = parseFloat(maxAmount).toFixed(2);
        document.getElementById('refundModal').classList.remove('hidden');
    }

    // Closes the refund modal
    function closeRefundModal() {
        document.getElementById('refundModal').classList.add('hidden');
    }
</script>

</body>
</html>