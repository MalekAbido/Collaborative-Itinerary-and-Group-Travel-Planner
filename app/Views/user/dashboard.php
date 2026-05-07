<?php 
use App\Helpers\Session;
require __DIR__ . '/../layouts/header.php'; ?>

        <div class="max-w-content mx-auto">

                <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="font-display text-display text-on-surface mb-3">Dashboard</h1>
                        <p class="text-body-lg text-on-surface-variant max-w-[640px]">
                            Manage your upcoming itineraries and past adventures.
                        </p>
                        <br>
                        <p>
                            <?php echo Session::getFlash(Session::FLASH_ERROR); ?>
                        </p>
                    </div>
                    <a href="/itinerary/create"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-4 shadow-sm hover:bg-on-primary-fixed-variant transition shrink-0">
                        <span class="material-symbols-outlined text-[20px]">add</span> Create Trip
                    </a>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($myTrips)): ?>
                    <?php foreach ($myTrips as $trip): ?>
                    <article
                        class="flex flex-col bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        
                        <div class="relative h-40 overflow-hidden bg-surface-container-highest">
                            <?php if (!empty($trip['coverImage'])): ?>
                                <img src="/<?= htmlspecialchars($trip['coverImage']) ?>" alt="<?= htmlspecialchars($trip['title']) ?> Cover" class="absolute inset-0 h-full w-full object-cover">
                                <div class="absolute inset-0 bg-black/10"></div>
                            <?php else: ?>
                                <div class="absolute inset-0 bg-gradient-primary"></div>
                            <?php endif; ?>

                            <span class="material-symbols-outlined absolute bottom-3 left-3 text-5xl text-white/50">travel_explore</span>

                            <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-black/30 backdrop-blur px-3 py-1 text-label-xs font-bold uppercase text-white">
                                <?= htmlspecialchars($trip['role']) ?>
                            </span>
                        </div>

                        <div class="p-5 border-l-4 border-primary flex-1 flex flex-col">
                            <h4 class="font-display text-h4 text-on-surface mb-1">
                                <?= htmlspecialchars($trip['title']) ?></h4>

                            <p class="text-body-xs text-on-surface-variant mb-3">
                                <?= date('M j', strtotime($trip['startDate'])) ?> –
                                <?= date('M j, Y', strtotime($trip['endDate'])) ?>
                            </p>

                            <p class="text-body-sm text-on-surface-variant mb-4 line-clamp-2">
                                <?= htmlspecialchars($trip['description']) ?>
                            </p>

                            <a href="/itinerary/dashboard/<?= htmlspecialchars($trip['id']) ?>"
                                class="mt-auto inline-flex items-center gap-1 text-body-sm font-semibold text-primary hover:underline group">
                                View Dashboard <span
                                    class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-20 border-2 border-dashed border-outline-variant rounded-xl bg-surface-container-lowest">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-container mb-4">
                            <span class="material-symbols-outlined text-[32px] text-outline">flight_takeoff</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-1">No trips yet</h3>
                        <p class="text-body-md text-on-surface-variant mb-4">You aren't a member of any itineraries.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <footer class="mt-12 pt-8 pb-4 text-center text-body-xs text-outline border-t border-outline-variant">
                    © <?= date('Y') ?> VoyageSync. All rights reserved.
                </footer>

            </div>
        </div>

    <script src="/assets/js/user.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>