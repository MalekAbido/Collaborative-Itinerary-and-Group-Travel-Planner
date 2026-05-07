<?php
    require __DIR__ . '/../layouts/header.php';
    $itineraryId = $data['itineraryId'];
    $pendingActivity = $data['pendingActivity'] ?? [];
    $conflictingActivities = $data['conflictingActivities'] ?? [];
?>

    <!-- <div class="max-w-2xl mx-auto py-10 px-6"> -->
        <?php if (!empty($conflictingActivities)): ?>
            <div id="conflict-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-surface rounded-xl shadow-lg p-6 max-w-lg w-full">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-error text-3xl">warning</span>
                        <h2 class="text-xl font-bold text-on-surface">Time Conflict Detected</h2>
                    </div>
                    <p class="text-on-surface-variant mb-4">
                        This activity overlaps with the following confirmed activities:
                    </p>
                    <ul class="list-disc list-inside mb-6 pl-5 text-on-surface-variant">
                        <?php foreach ($conflictingActivities as $conflict): ?>
                            <li>
                                <strong><?= htmlspecialchars($conflict['name']) ?></strong> 
                                (<span class="local-time" data-utc="<?= date('c', strtotime($conflict['startTime'])) ?>" data-format="datetime"></span> - <span class="local-time" data-utc="<?= date('c', strtotime($conflict['endTime'])) ?>" data-format="datetime"></span>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-on-surface-variant mb-6">
                        Do you still want to save this activity as a draft?
                    </p>
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="document.getElementById('conflict-modal').style.display='none';" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface hover:bg-surface-container transition">
                            Cancel
                        </button>
                        <button type="button" onclick="const form = document.getElementById('activity-form'); const input = document.createElement('input'); input.type = 'hidden'; input.name = 'confirm_override'; input.value = '1'; form.appendChild(input); form.submit();" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary-container transition font-semibold">
                            Confirm & Save Draft
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display text-3xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">add_box</span> Add Activity
            </h1>
        </div>

        <?php $flashError = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_ERROR); ?>
        <?php if ($flashError): ?>
            <div class="mb-6 rounded-xl border border-error/30 bg-error-container px-4 py-3 text-on-error-container flex items-start gap-3">
                <span class="material-symbols-outlined text-error">error</span>
                <p class="font-medium"><?= htmlspecialchars($flashError) ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
            <form id="activity-form" action="/itinerary/<?= htmlspecialchars($itineraryId) ?>/activity/store" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="timezone" id="clientTimezoneReopen" value="">

                <!-- Banner Image Upload -->
                <div class="space-y-4">
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2 text-center">Activity Banner (Optional)</label>
                    <div class="flex flex-col items-center gap-4">
                        <div id="banner-preview-container" class="w-full h-48 rounded-xl overflow-hidden border-2 border-dashed border-outline-variant bg-surface-container flex items-center justify-center relative">
                            <div id="banner-placeholder" class="text-on-surface-variant flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl">image</span>
                                <span class="text-body-sm">Click below to upload a banner</span>
                            </div>
                        </div>
                        <div class="w-full max-w-xs">
                            <input type="file" name="bannerImage" id="banner-input" accept="image/*"
                                class="w-full text-body-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-body-sm file:font-semibold file:bg-primary-fixed file:text-primary hover:file:bg-primary-container transition" />
                        </div>
                    </div>
                </div>

                <script>
                document.getElementById('banner-input').onchange = evt => {
                    const [file] = evt.target.files
                    if (file) {
                        const container = document.getElementById('banner-preview-container');
                        let preview = document.getElementById('banner-preview');

                        if (!preview) {
                            preview = document.createElement('img');
                            preview.id = 'banner-preview';
                            preview.className = 'w-full h-full object-cover';
                            container.innerHTML = '';
                            container.appendChild(preview);
                        }
                        preview.src = URL.createObjectURL(file);
                    }
                }
                </script>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Activity Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($pendingActivity['name'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition" placeholder="e.g. Visit Louvre Museum">
                </div>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition" placeholder="Activity details..."><?= htmlspecialchars($pendingActivity['description'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Start Time</label>
                        <input type="datetime-local" name="start_time" value="<?= htmlspecialchars($pendingActivity['start_time'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">End Time</label>
                        <input type="datetime-local" name="end_time" value="<?= htmlspecialchars($pendingActivity['end_time'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Category</label>
                        <?php $selectedCategory = $pendingActivity['category'] ?? 'General'; ?>
                        <select name="category" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition">
                            <option value="General" <?= $selectedCategory === 'General' ? 'selected' : '' ?>>General</option>
                            <option value="Flight" <?= $selectedCategory === 'Flight' ? 'selected' : '' ?>>Flight</option>
                            <option value="Accommodation" <?= $selectedCategory === 'Accommodation' ? 'selected' : '' ?>>Accommodation</option>
                            <option value="Dining" <?= $selectedCategory === 'Dining' ? 'selected' : '' ?>>Dining</option>
                            <option value="Transport" <?= $selectedCategory === 'Transport' ? 'selected' : '' ?>>Transport</option>
                            <option value="Sightseeing" <?= $selectedCategory === 'Sightseeing' ? 'selected' : '' ?>>Sightseeing</option>
                        </select>
                    </div>
                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" <?= !empty($pendingActivity['is_anonymous']) ? 'checked' : '' ?> class="h-5 w-5 rounded border-outline-variant text-primary focus:ring-primary transition">
                        <label for="is_anonymous" class="ml-3 text-body-md font-semibold text-on-surface">Anonymous Voting</label>
                    </div>
                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Location Name</label>
                            <input type="text" name="location_name" value="<?= htmlspecialchars($pendingActivity['location_name'] ?? '') ?>" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition" placeholder="e.g. Louvre Museum">
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Location Address</label>
                            <input type="text" name="location_address" value="<?= htmlspecialchars($pendingActivity['location_address'] ?? '') ?>" class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition" placeholder="e.g. Paris, France">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-outline-variant flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-semibold px-6 py-3 hover:bg-primary-container transition">
                        <span class="material-symbols-outlined text-[20px]">save</span> Create Draft Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="/assets/js/timezone.js"></script>
</body>
</html>