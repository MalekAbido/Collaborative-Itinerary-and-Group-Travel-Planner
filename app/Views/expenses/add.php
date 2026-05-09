<?php require __DIR__ . '/../layouts/header.php'; ?>
    <!-- <main class="max-w-2xl mx-auto"> -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-display text-h2 text-on-surface m-0">Log a New Expense</h1>
            <a href="/finance/dashboard/<?= htmlspecialchars($itineraryId ?? '') ?>" class="text-body-sm font-semibold text-outline hover:text-primary transition">Cancel</a>        </div>

        <form action="/finance/expense/create" method="POST" id="expenseForm" class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 md:p-8">
            
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
                        <input type="number" step="100" min="0" max="999999999" id="amountInput" name="amount" required placeholder="0"
                               class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition">
                        
                        <!-- Currency Dropdown right next to Amount -->
                        <div class="relative min-w-[100px]">
                            <select name="currencyType_display" disabled class="w-full rounded-md border border-outline-variant bg-surface-container px-3 py-2 text-body-md focus:outline-none transition opacity-70 cursor-not-allowed appearance-none">
                                <option value="<?= htmlspecialchars($baseCurrency) ?>" selected><?= htmlspecialchars($baseCurrency) ?></option>
                            </select>
                            <input type="hidden" name="currencyType" value="<?= htmlspecialchars($baseCurrency) ?>">
                        </div>
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
                            <input type="radio" name="payerId" value="<?= $member['memberId'] ?>" required class="text-primary focus:ring-primary">
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
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-h4 text-on-surface m-0">Participant Shares</h3>
                    <div id="remainingLabel" class="hidden text-body-xs font-semibold px-2 py-1 rounded bg-secondary-container text-on-secondary-container transition-colors duration-200">
                        Remaining: <span id="remainingAmount">0.00</span>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <?php foreach ($members as $member): ?>
                        <div class="flex items-center justify-between">
                            <label class="text-body-sm font-medium text-on-surface truncate pr-4">
                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                            </label>
                            <input type="number" step="10" name="shares[<?= $member['memberId'] ?>]" class="share-input w-32 rounded-md border border-outline-variant bg-surface-container-lowest px-2 py-1 text-body-sm text-right focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition" value="0.00">
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="shareValidationError" class="hidden mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center gap-2 animate-in fade-in slide-in-from-top-1">
                    <span class="material-symbols-outlined text-error">⚠️</span>
                    <p class="text-body-sm font-medium">The total of participant shares must equal the total expense amount.</p>
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
        const baseCurrency = "<?= htmlspecialchars($baseCurrency) ?>";
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
        const remainingLabel = document.getElementById('remainingLabel');
        const remainingAmountSpan = document.getElementById('remainingAmount');
        const shareValidationError = document.getElementById('shareValidationError');
        const expenseForm = document.getElementById('expenseForm');

        // Allow any value on submission by temporarily changing step to 'any'
        submitBtn.addEventListener('click', () => {
            amountInput.step = 'any';
            shareInputs.forEach(input => input.step = 'any');
        });

        // Restore steps on focus to keep the 100/10 step behavior for spinners
        amountInput.addEventListener('focus', () => {
            amountInput.step = '100';
        });

        shareInputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.step = '10';
            });
        });

        // Force arrows to go by 100 but allow custom typed values
        amountInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
                let val = parseFloat(this.value) || 0;
                let step = 100;
                if (e.key === 'ArrowUp') {
                    this.value = (val + step).toFixed(2);
                } else {
                    this.value = Math.max(0.00, val - step).toFixed(2);
                }
                this.dispatchEvent(new Event('input'));
            }
        });

        function calculateEvenSplit() {
            if (splitMethodDropdown.value === 'EVEN' && !paidByKittyCheckbox.checked) {
                const totalAmount = parseFloat(amountInput.value) || 0;
                const count = shareInputs.length;
                if (count > 0) {
                    const share = (totalAmount / count).toFixed(2);
                    shareInputs.forEach(input => {
                        input.value = share;
                    });
                }
            }
            updateRemainingAmount();
        }

        function updateRemainingAmount() {
            const totalAmount = parseFloat(amountInput.value) || 0;
            let sum = 0;
            shareInputs.forEach(input => {
                sum += parseFloat(input.value) || 0;
            });

            const diff = totalAmount - sum;
            const absDiff = Math.abs(diff).toFixed(2);
            
            if (diff < 0) {
                remainingLabel.childNodes[0].textContent = `Exceeds total by: `;
                remainingAmountSpan.textContent = absDiff;
            } else {
                remainingLabel.childNodes[0].textContent = `Remaining: `;
                remainingAmountSpan.textContent = diff.toFixed(2);
            }
            
            if (splitMethodDropdown.value === 'UNEVEN' && !paidByKittyCheckbox.checked) {
                remainingLabel.classList.remove('hidden');
                if (Math.abs(diff) > 0.01) {
                    remainingLabel.classList.add('bg-error-container', 'text-on-error-container');
                    remainingLabel.classList.remove('bg-secondary-container', 'text-on-secondary-container');
                } else {
                    remainingLabel.classList.remove('bg-error-container', 'text-on-error-container');
                    remainingLabel.classList.add('bg-secondary-container', 'text-on-secondary-container');
                }
            } else {
                remainingLabel.classList.add('hidden');
            }

            // Real-time validation visual feedback
            if (!paidByKittyCheckbox.checked && Math.abs(diff) > 1) {
                shareValidationError.classList.remove('hidden');
            } else {
                shareValidationError.classList.add('hidden');
            }
        }

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

            if (isKitty) {
                remainingLabel.classList.add('hidden');
                shareValidationError.classList.add('hidden');
            } else {
                calculateEvenSplit();
            }

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

        amountInput.addEventListener('input', () => {
            validateKittyAmount();
            calculateEvenSplit();
        });

        paidByKittyCheckbox.addEventListener('change', updateExpenseSections);

        splitMethodDropdown.addEventListener('change', function() {
            updateExpenseSections();
        });

        shareInputs.forEach(input => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    let val = parseFloat(this.value) || 0;
                    let step = 10;
                    if (e.key === 'ArrowUp') {
                        this.value = (val + step).toFixed(2);
                    } else {
                        this.value = Math.max(0.00, val - step).toFixed(2);
                    }
                    this.dispatchEvent(new Event('input'));
                }
            });
            input.addEventListener('input', updateRemainingAmount);
        });

        expenseForm.addEventListener('submit', function(e) {
            if (!paidByKittyCheckbox.checked) {
                const totalAmount = parseFloat(amountInput.value) || 0;
                let sum = 0;
                shareInputs.forEach(input => {
                    sum += parseFloat(input.value) || 0;
                });

                if (Math.abs(sum - totalAmount) > 1) {
                    e.preventDefault();
                    shareValidationError.classList.remove('hidden');
                    shareValidationError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        updateExpenseSections();
        splitMethodDropdown.dispatchEvent(new Event('change'));
    </script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>