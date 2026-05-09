<?php 
use App\Enums\TripMemberRole;
require __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-[1280px] mx-auto">
    <header class="mb-10 flex justify-between items-end">
        <div>
            <h1 class="font-display text-display text-on-surface mb-2"><?= htmlspecialchars($itinerary['title']) ?></h1>
            <p class="text-[18px] text-on-surface-variant">Coordinate items and equipment needed for the trip.</p>
        </div>
        <div class="flex gap-3">
            <button class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-bold text-[14px] px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    onclick="openAddItemModal()" >
                <span class="material-symbols-outlined text-[18px]">add_box</span> Add Item
            </button>
            <a class="cursor-pointer inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-[14px] px-6 py-2.5 hover:bg-surface-container transition"    href="/itinerary/dashboard/<?= htmlspecialchars($itineraryId) ?>"  >
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back
            </a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- MY VOLUNTEERS SECTION -->
        <div class="lg:col-span-2 space-y-8">
            <section>
                <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                    <span class="material-symbols-outlined text-primary">volunteer_activism</span>
                    <h2 class="font-display text-[24px] font-semibold text-on-surface m-0">My Volunteers</h2>
                </div>

                <?php if (empty($myVolunteers)): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant border-dashed rounded-xl p-8 text-center">
                        <p class="text-on-surface-variant italic">You haven't volunteered for any items yet.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($myVolunteers as $item): ?>
                            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 shadow-sm relative group">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-display text-[18px] font-bold text-on-surface"><?= htmlspecialchars($item['name']) ?></h3>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-primary-fixed text-primary px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide">Qty: <?= htmlspecialchars($item['quantity']) ?></span>
                                        <?php if ($item['creatorMemberId'] == $currentMemberId || App\Helpers\Auth::hasRole(TripMemberRole::EDITOR->value, $currentMemberRole)): ?>
                                            <form action="/inventory/delete" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from the inventory?');">
                                                <input type="hidden" name="itemId" value="<?= $item['id'] ?>">
                                                <input type="hidden" name="itineraryId" value="<?= $itineraryId ?>">
                                                <button class="text-error hover:text-error/80 transition p-1 leading-none cursor-pointer"    type="submit"  title="Delete Item">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="text-[14px] text-on-surface-variant mb-4"><?= htmlspecialchars($item['description']) ?></p>
                                <div class="flex items-center gap-2 text-[12px] text-outline mb-4">
                                    <span class="material-symbols-outlined text-[16px]">event</span>
                                    <span>For: <?= htmlspecialchars($item['activityName']) ?></span>
                                </div>
                                <form action="/inventory/unvolunteer" method="POST">
                                    <input type="hidden" name="itemId" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="itineraryId" value="<?= $itineraryId ?>">
                                    <button class="w-full py-2 rounded-lg border border-error text-error text-[13px] font-bold hover:bg-error/5 transition flex items-center justify-center gap-2 cursor-pointer"    type="submit" >
                                        <span class="material-symbols-outlined text-[16px]">undo</span> Unvolunteer
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ALL OTHER ITEMS SECTION -->
            <section>
                <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                    <span class="material-symbols-outlined text-secondary">inventory_2</span>
                    <h2 class="font-display text-[24px] font-semibold text-on-surface m-0">All Other Items</h2>
                </div>

                <?php if (empty($otherItems)): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-8 text-center">
                        <p class="text-on-surface-variant italic">No other items in the inventory.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($otherItems as $item): ?>
                            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm relative group">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-display text-[18px] font-bold text-on-surface"><?= htmlspecialchars($item['name']) ?></h3>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-surface-container-highest text-on-surface-variant px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide">Qty: <?= htmlspecialchars($item['quantity']) ?></span>
                                        <?php if ($item['creatorMemberId'] == $currentMemberId || App\Helpers\Auth::hasRole(TripMemberRole::EDITOR->value, $currentMemberRole)): ?>
                                            <form action="/inventory/delete" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from the inventory?');">
                                                <input type="hidden" name="itemId" value="<?= $item['id'] ?>">
                                                <input type="hidden" name="itineraryId" value="<?= $itineraryId ?>">
                                                <button class="text-error hover:text-error/80 transition p-1 leading-none cursor-pointer"    type="submit"  title="Delete Item">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="text-[14px] text-on-surface-variant mb-4"><?= htmlspecialchars($item['description']) ?></p>
                                <div class="flex items-center gap-2 text-[12px] text-outline mb-4">
                                    <span class="material-symbols-outlined text-[16px]">event</span>
                                    <span>For: <?= htmlspecialchars($item['activityName']) ?></span>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-outline-variant flex items-center justify-between">
                                    <?php if ($item['tripMemberId']): ?>
                                        <div class="flex items-center gap-2">
                                            <?php if (!empty($item['profileImage'])): ?>
                                                <img src="/<?= htmlspecialchars($item['profileImage']) ?>" alt="Volunteer" class="h-6 w-6 rounded-full object-cover border border-outline-variant">
                                            <?php else: ?>
                                                <div class="w-6 h-6 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center text-[10px] font-bold">
                                                    <?= strtoupper(substr($item['firstName'], 0, 1) . substr($item['lastName'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <span class="text-[12px] font-medium text-on-surface-variant"><?= htmlspecialchars($item['firstName']) ?> is bringing this</span>
                                        </div>
                                    <?php else: ?>
                                        <form action="/inventory/volunteer" method="POST" class="w-full">
                                            <input type="hidden" name="itemId" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="itineraryId" value="<?= $itineraryId ?>">
                                            <button class="w-full py-2 rounded-lg bg-secondary text-on-secondary text-[13px] font-bold hover:bg-secondary/90 transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"    type="submit" >
                                                <span class="material-symbols-outlined text-[16px]">volunteer_activism</span> Volunteer to Bring
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- SIDEBAR -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
                <h3 class="font-display text-[18px] font-bold text-on-surface mb-4">Inventory Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-on-surface-variant">Total Items</span>
                        <span class="font-bold text-on-surface"><?= count($myVolunteers) + count($otherItems) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-on-surface-variant">Volunteered</span>
                        <?php 
                            $volunteeredCount = count($myVolunteers) + count(array_filter($otherItems, fn($i) => $i['tripMemberId'] !== null));
                        ?>
                        <span class="font-bold text-secondary"><?= $volunteeredCount ?></span>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-on-surface-variant">Needed</span>
                        <span class="font-bold text-error"><?= (count($myVolunteers) + count($otherItems)) - $volunteeredCount ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-primary-fixed-dim rounded-xl p-6 text-on-primary-fixed-variant shadow-sm">
                <h3 class="font-display text-[16px] font-bold mb-2">Packing Tip</h3>
                <p class="text-[13px] leading-relaxed opacity-90">Don't forget to mark items as packed once they are in your bag! Coordination helps avoid duplicates and extra weight.</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-on-background/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-lg border border-outline-variant overflow-hidden">
        <div class="flex items-center justify-between border-b border-outline-variant px-6 py-4 bg-surface">
            <h3 class="font-display text-[20px] font-semibold text-on-surface m-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">add_box</span> Add Inventory Item
            </h3>
            <button class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition cursor-pointer"    type="button" onclick="closeAddItemModal()" >
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6">
            <form action="/inventory/add" method="POST" class="space-y-4">
                <input type="hidden" name="itineraryId" value="<?= $itineraryId ?>">
                
                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Item Name</label>
                    <input type="text" name="name" required placeholder="e.g., First Aid Kit, Camera, Tent"
                        class="block w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/3">
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="block w-full rounded-lg border border-outline-variant bg-surface px-4 py-2 text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition h-[45px]">
                    </div>
                    <div class="w-full sm:w-2/3">
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Related Activity</label>
                        <select name="activityId" required class="block w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition h-[45px]">
                            <?php foreach ($activities as $activity): ?>
                                <option value="<?= $activity->getId() ?>"><?= htmlspecialchars($activity->getName()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Description (Optional)</label>
                    <textarea name="description" rows="3" placeholder="Additional details or specific requirements..."
                        class="block w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-[14px] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition resize-none"></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface font-semibold text-[14px] hover:bg-surface-container transition cursor-pointer"    type="button" onclick="closeAddItemModal()" >
                        Cancel
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-[14px] px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    type="submit" >
                        Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddItemModal() {
        document.getElementById('addItemModal').classList.remove('hidden');
    }

    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAddItemModal();
    });

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('addItemModal');
        if (event.target == modal) closeAddItemModal();
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
