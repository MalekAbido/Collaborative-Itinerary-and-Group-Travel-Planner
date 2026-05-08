<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
    <span class="material-symbols-outlined text-primary text-[28px]">settings</span>
    <h2 class="font-display text-h2 text-on-surface m-0">Trip Settings</h2>
</div>

<?php
if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
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

    <form action="/itinerary/update/<?php echo htmlspecialchars($trip['itineraryId']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">

        <div>
            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Trip Title</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($trip['title']) ?>"
                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
        </div>

        <div>
            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Description</label>
            <textarea name="description" rows="4"
                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition resize-y"><?php echo htmlspecialchars($trip['description']) ?></textarea>
        </div>

        <div>
            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Cover Image</label>
            <?php if (!empty($trip['coverImage'])): ?>
                <div class="mb-3">
                    <img src="/<?php echo htmlspecialchars($trip['coverImage']) ?>" alt="Current Cover" class="h-40 w-full object-cover rounded-lg border border-outline-variant">
                    <p class="text-body-xs text-outline mt-1">Current cover image. Upload a new file below to replace it.</p>
                </div>
            <?php endif; ?>
            <input type="file" name="coverImage" accept="image/*"
                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-body-sm file:font-semibold file:bg-primary-fixed file:text-primary hover:file:bg-primary-fixed-variant transition cursor-pointer">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Start Date</label>
                <input type="date" name="startDate" value="<?php echo htmlspecialchars($trip['startDate']) ?>"
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
            </div>
            <div>
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2">End Date</label>
                <input type="date" name="endDate" value="<?php echo htmlspecialchars($trip['endDate']) ?>"
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
            </div>
        </div>

        <div class="pt-6 border-t border-outline-variant mt-6">
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-3 shadow-sm hover:bg-on-primary-fixed-variant transition">
                <span class="material-symbols-outlined text-[20px]">save</span> Save Changes
            </button>
        </div>
    </form>
</div>

<div class="bg-error-container/30 border border-error/30 rounded-xl shadow-sm p-6 lg:p-8">
    <h3 class="font-display text-h4 text-error mb-2">Danger Zone</h3>
    <p class="text-body-sm text-on-error-container mb-6">Once you delete a trip, there is no going back. All activities,
        polls, and budget data will be permanently removed.</p>

    <form action="/itinerary/destroy/<?php echo htmlspecialchars($trip['itineraryId']) ?>" method="POST"
        onsubmit="return confirm('Are you absolutely sure you want to delete this trip?');">
        <button type="submit"
            class="inline-flex items-center gap-2 rounded-lg bg-error text-on-error font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-error-container transition">
            <span class="material-symbols-outlined text-base">delete_forever</span> Delete Trip
        </button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>