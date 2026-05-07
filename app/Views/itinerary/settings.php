<?php require __DIR__ . '/../layouts/header.php'; ?>

    <!-- <main class="max-w-[800px] mx-auto mt-[100px] px-6 pb-12"> -->
        
        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-[28px]">settings</span>
            <h2 class="font-display text-h2 text-on-surface m-0">Trip Settings</h2>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
            <div class="flex items-center gap-3 rounded-xl border border-success/30 bg-success-container px-4 py-3 mb-8">
                <span class="material-symbols-outlined text-success">check_circle</span>
                <div class="flex-1">
                    <span class="block text-label-xs uppercase text-success font-bold">Success</span>
                    <h4 class="text-body-md font-semibold text-on-success-container m-0">Trip details updated successfully!</h4>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 lg:p-8 mb-8">
            <h3 class="font-display text-h4 text-on-surface mb-6 border-b border-outline-variant pb-3">Edit Details</h3>
            
            <form action="/itinerary/update/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Trip Title</label>
                    <input type="text" name="title" required
                           value="<?= htmlspecialchars($trip['title']) ?>" 
                           class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition resize-y"><?= htmlspecialchars($trip['description']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Start Date</label>
                        <input type="date" name="startDate" 
                               value="<?= htmlspecialchars($trip['startDate']) ?>"
                               class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-label-caps uppercase text-on-surface-variant mb-2">End Date</label>
                        <input type="date" name="endDate" 
                               value="<?= htmlspecialchars($trip['endDate']) ?>"
                               class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                    </div>
                </div>

                <div class="pt-6 border-t border-outline-variant mt-6">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-3 shadow-sm hover:bg-on-primary-fixed-variant transition">
                        <span class="material-symbols-outlined text-[20px]">save</span> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-error-container/30 border border-error/30 rounded-xl shadow-sm p-6 lg:p-8">
            <h3 class="font-display text-h4 text-error mb-2">Danger Zone</h3>
            <p class="text-body-sm text-on-error-container mb-6">Once you delete a trip, there is no going back. All activities, polls, and budget data will be permanently removed.</p>
            
            <form action="/itinerary/destroy/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this trip?');">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-error text-on-error font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-error-container transition">
                    <span class="material-symbols-outlined text-base">delete_forever</span> Delete Trip
                </button>
            </form>
        </div>

    </main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
