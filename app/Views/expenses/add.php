<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense - VoyageSync</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#f65a41", "on-primary-fixed-variant": "#7b2a1a",
                        "surface": "#fcf8f8", "surface-container-lowest": "#ffffff", 
                        "surface-container": "#edeeef", "on-surface": "#191c1d", 
                        "on-surface-variant": "#414754", "outline": "#727785", 
                        "outline-variant": "#c1c6d6",
                    },
                    fontFamily: { display: ["'Plus Jakarta Sans'", "sans-serif"], body: ["'Inter'", "sans-serif"] },
                    fontSize: {
                        "h2": ["28px", { lineHeight: "1.3", fontWeight: "600" }],
                        "h4": ["17px", { lineHeight: "1.4", fontWeight: "600" }],
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
    <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
            <p class="text-body-md font-medium">⚠️ <?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>
    <main class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-display text-h2 text-on-surface m-0">Log a New Expense</h1>
            <a href="/finance/dashboard/<?= htmlspecialchars($financeId ?? '') ?>" class="text-body-sm font-semibold text-outline hover:text-primary transition">Cancel</a>        </div>

        <form action="/finance/expense/create" method="POST" class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 md:p-8">
            
            <input type="hidden" name="financeId" value="<?= htmlspecialchars($financeId ?? '') ?>"> 

            <!-- Top Grid: Desc, Category, Amount, Currency -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Description</label>
                    <input type="text" name="description" placeholder="e.g. Dinner at Luigi's" required 
                           class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Category</label>
                    <input type="text" name="category" placeholder="Food, Transport, etc." 
                           class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Total Amount</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.01" id="amountInput" name="amount" required placeholder="0.00"
                               class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                        
                        <!-- Currency Dropdown right next to Amount -->
                        <select name="currencyType" required class="rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                            <option value="EGP">EGP</option>    
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="NGN">NGN</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Checkboxes Section --> 
            <div class="mb-6 pt-6 border-t border-outline-variant flex flex-col sm:flex-row gap-6">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input id="paidByKittyCheckbox" type="checkbox" name="paidByKitty" value="1" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                    <span class="text-body-md font-medium text-on-surface group-hover:text-primary transition">Paid by Group Kitty</span>
                </label>
                <span id="fundBalanceInfo" class="text-body-sm text-on-surface-variant italic hidden">Available: <?= number_format($groupFundBalance, 2) ?></span>
            </div>

            <!-- Kitty Balance Warning -->
            <div id="kittyWarning" class="hidden mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
                <p class="text-body-md font-medium">Insufficient Group Kitty Balance</p>
                <p class="text-body-sm mt-1">Your expense amount exceeds the available group fund. Available: <span id="availableAmount"></span></p>
            </div>

            <!-- Who Paid Radio Buttons -->
            <div id="whoPaidSection" class="mb-6 pt-6 border-t border-outline-variant">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-3">Who Paid?</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php foreach ($members as $member): ?>
                        <label class="flex items-center gap-2 p-3 border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container transition">
                            <input type="radio" name="payerId" value="<?= htmlspecialchars($member['memberId']) ?>" required class="text-primary focus:ring-primary">
                            <span class="text-body-sm font-medium whitespace-nowrap overflow-hidden text-ellipsis">
                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Split Method -->
            <div id="splitMethodSection" class="mb-6 pt-6 border-t border-outline-variant">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Split Method</label>
                <select name="splitMethod" id="splitMethod" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                    <option value="EVEN">Even Split (Everyone pays equally)</option>
                    <option value="UNEVEN">Uneven Split (Specify exact amounts)</option>
                </select>
            </div>

            <!-- Dynamic Shares Section -->
            <div id="sharesSection" class="mb-8 bg-surface p-4 rounded-lg border border-outline-variant/50">
                <h3 class="font-display text-h4 text-on-surface mb-4">Participant Shares</h3>
                <div class="flex flex-col gap-3">
                    <?php foreach ($members as $member): ?>
                        <div class="flex items-center justify-between">
                            <label class="text-body-sm font-medium text-on-surface truncate pr-4">
                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                            </label>
                            <input type="number" step="0.01" name="shares[<?= htmlspecialchars($member['memberId']) ?>]" class="share-input w-32 rounded-md border border-outline-variant bg-surface-container-lowest px-2 py-1 text-body-sm text-right focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition" value="0.00">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex justify-end">
                <button id="submitBtn" type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-white font-semibold text-body-sm px-8 py-3 shadow-sm hover:bg-on-primary-fixed-variant transition w-full sm:w-auto">
                    Save Expense
                </button>
            </div>
        </form>
    </main>

    <script>
        const groupFundBalance = <?= $groupFundBalance ?>;
        const paidByKittyCheckbox = document.getElementById('paidByKittyCheckbox');
        const whoPaidSection = document.getElementById('whoPaidSection');
        const splitMethodSection = document.getElementById('splitMethodSection');
        const sharesSection = document.getElementById('sharesSection');
        const splitMethodDropdown = document.getElementById('splitMethod');
        const payerInputs = document.querySelectorAll('input[name="payerId"]');
        const shareInputs = document.querySelectorAll('.share-input');
        const amountInput = document.getElementById('amountInput');
        const kittyWarning = document.getElementById('kittyWarning');
        const fundBalanceInfo = document.getElementById('fundBalanceInfo');
        const submitBtn = document.getElementById('submitBtn');
        const availableAmount = document.getElementById('availableAmount');

        function updateExpenseSections() {
            const isKitty = paidByKittyCheckbox.checked;

            whoPaidSection.classList.toggle('hidden', isKitty);
            splitMethodSection.classList.toggle('hidden', isKitty);
            sharesSection.classList.toggle('hidden', isKitty);
            fundBalanceInfo.classList.toggle('hidden', !isKitty);

            payerInputs.forEach(input => {
                input.required = !isKitty;
                input.disabled = isKitty;
            });

            splitMethodDropdown.disabled = isKitty;

            shareInputs.forEach(input => {
                input.disabled = isKitty;
                if (isKitty || splitMethodDropdown.value === 'EVEN') {
                    input.readOnly = true;
                    input.classList.add('opacity-50', 'bg-surface-container');
                } else {
                    input.readOnly = false;
                    input.classList.remove('opacity-50', 'bg-surface-container');
                }
            });

            validateKittyAmount();
        }

        function validateKittyAmount() {
            const amount = parseFloat(amountInput.value) || 0;
            const isKitty = paidByKittyCheckbox.checked;
            const exceedsBalance = amount > groupFundBalance;

            if (isKitty && exceedsBalance) {
                kittyWarning.classList.remove('hidden');
                availableAmount.textContent = groupFundBalance.toFixed(2);
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                kittyWarning.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        amountInput.addEventListener('input', validateKittyAmount);
        paidByKittyCheckbox.addEventListener('change', updateExpenseSections);

        splitMethodDropdown.addEventListener('change', function() {
            if (!paidByKittyCheckbox.checked) {
                shareInputs.forEach(input => {
                    if (this.value === 'EVEN') {
                        input.readOnly = true;
                        input.classList.add('opacity-50', 'bg-surface-container');
                    } else {
                        input.readOnly = false;
                        input.classList.remove('opacity-50', 'bg-surface-container');
                    }
                });
            }
        });

        updateExpenseSections();
        splitMethodDropdown.dispatchEvent(new Event('change'));
    </script>
</body>
</html>