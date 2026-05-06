<?php
    $itineraryId = $data['itineraryId'];
    $pendingActivity = $data['pendingActivity'] ?? [];
    $conflictingActivities = $data['conflictingActivities'] ?? [];
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VoyageSync - Add Activity</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
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
                        "background": "#fcf8f8", "on-background": "#191c1d", "surface": "#fcf8f8",
                        "surface-container-lowest": "#ffffff", "surface-container-low": "#f3f4f5", "surface-container": "#edeeef", "surface-container-highest": "#e1e3e4",
                        "on-surface": "#191c1d", "on-surface-variant": "#414754", "outline": "#727785", "outline-variant": "#c1c6d6",
                        "error": "#ba1a1a", "error-container": "#ffdad6", "on-error": "#ffffff", "on-error-container": "#93000a",
                        "success": "#16a34a", "success-container": "#bbf7d0", "on-success": "#ffffff", "on-success-container": "#064e2c",
                    },
                    fontFamily: { display: ["'Plus Jakarta Sans'", "sans-serif"], body: ["'Inter'", "sans-serif"] },
                }
            }
        };
    </script>
</head>
<body class="bg-background text-on-background font-body min-h-screen relative">
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
                            (<?= htmlspecialchars(date('M d, H:i', strtotime($conflict['startTime']))) ?> - <?= htmlspecialchars(date('M d, H:i', strtotime($conflict['endTime']))) ?>)
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

    <div class="max-w-2xl mx-auto py-10 px-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display text-3xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">add_box</span> Add Activity
            </h1>
            <a href="/itinerary/dashboard/<?= htmlspecialchars($itineraryId) ?>" class="text-on-surface-variant hover:text-primary transition font-medium">Back to Itinerary</a>
        </div>

        <?php $flashError = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_ERROR); ?>
        <?php if ($flashError): ?>
            <div class="mb-6 rounded-xl border border-error/30 bg-error-container px-4 py-3 text-on-error-container flex items-start gap-3">
                <span class="material-symbols-outlined text-error">error</span>
                <p class="font-medium"><?= htmlspecialchars($flashError) ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
            <form id="activity-form" action="/itinerary/<?= htmlspecialchars($itineraryId) ?>/activity/store" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Activity Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($pendingActivity['name'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition" placeholder="e.g. Visit Louvre Museum">
                </div>

                <div>
                    <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition" placeholder="Activity details..."><?= htmlspecialchars($pendingActivity['description'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Start Time</label>
                        <input type="datetime-local" name="start_time" value="<?= htmlspecialchars($pendingActivity['start_time'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">End Time</label>
                        <input type="datetime-local" name="end_time" value="<?= htmlspecialchars($pendingActivity['end_time'] ?? '') ?>" required class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Category</label>
                        <?php $selectedCategory = $pendingActivity['category'] ?? 'General'; ?>
                        <select name="category" class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition">
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
                            <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Location Name</label>
                            <input type="text" name="location_name" value="<?= htmlspecialchars($pendingActivity['location_name'] ?? '') ?>" class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition" placeholder="e.g. Louvre Museum">
                        </div>
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-wider text-on-surface-variant mb-2">Location Address</label>
                            <input type="text" name="location_address" value="<?= htmlspecialchars($pendingActivity['location_address'] ?? '') ?>" class="w-full rounded-md border border-outline-variant bg-surface px-4 py-2 focus:border-primary focus:ring-primary transition" placeholder="e.g. Paris, France">
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
</body>
</html>