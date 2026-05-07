<?php require __DIR__ . '/../layouts/header.php'; ?>

    <!-- <main class="max-w-2xl mx-auto"> -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-display text-h2 text-on-surface m-0">Log a New Expense</h1>
            <a href="/finance/dashboard/<?= htmlspecialchars($itineraryId ?? '') ?>" class="text-body-sm font-semibold text-outline hover:text-primary transition">Cancel</a>        </div>

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
                        <input type="number" step="0.01" name="amount" required placeholder="0.00"
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
                    <input type="checkbox" name="isNonCash" value="1" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                    <span class="text-body-md font-medium text-on-surface group-hover:text-primary transition">Non-Cash Transaction</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="paidByKitty" value="1" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                    <span class="text-body-md font-medium text-on-surface group-hover:text-primary transition">Paid by Group Kitty</span>
                </label>
            </div>

            <!-- Who Paid Radio Buttons -->
            <div class="mb-6 pt-6 border-t border-outline-variant">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-3">Who Paid?</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php foreach ($members as $member): ?>
                        <label class="flex items-center gap-2 p-3 border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container transition">
                            <input type="radio" name="payerId" value="<?= $member['id'] ?>" required class="text-primary focus:ring-primary">
                            <span class="text-body-sm font-medium whitespace-nowrap overflow-hidden text-ellipsis">
                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Split Method -->
            <div class="mb-6 pt-6 border-t border-outline-variant">
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
                            <input type="number" step="0.01" name="shares[<?= $member['id'] ?>]" class="share-input w-32 rounded-md border border-outline-variant bg-surface-container-lowest px-2 py-1 text-body-sm text-right focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition" value="0.00">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-white font-semibold text-body-sm px-8 py-3 shadow-sm hover:bg-on-primary-fixed-variant transition w-full sm:w-auto">
                    Save Expense
                </button>
            </div>
        </form>
    </main>

    <script>
        const splitMethodDropdown = document.getElementById('splitMethod');
        const shareInputs = document.querySelectorAll('.share-input');

        splitMethodDropdown.addEventListener('change', function() {
            if (this.value === 'EVEN') {
                shareInputs.forEach(input => {
                    input.readOnly = true;
                    input.classList.add('opacity-50', 'bg-surface-container');
                });
            } else {
                shareInputs.forEach(input => {
                    input.readOnly = false;
                    input.classList.remove('opacity-50', 'bg-surface-container');
                });
            }
        });
        
        splitMethodDropdown.dispatchEvent(new Event('change'));
    </script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>