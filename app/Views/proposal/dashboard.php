<?php 
use App\Enums\ActivityStatus;
require __DIR__ . '/../layouts/header.php'; ?>
        <!-- Main Scrollable Canvas -->
        <!-- <main class="flex-1 overflow-y-auto bg-surface pb-16 pt-20"> -->
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-0">
                <!-- Page Header -->
                <div class="mb-10">
                    <h1 class="font-display text-display text-on-surface mb-2">Proposed Activities Review</h1>
                    <p class="text-lg text-on-surface-variant max-w-2xl">Review draft submissions from group members. Approving a draft creates an active poll, while rejecting it removes it from consideration.</p>
                </div>
                
                <!-- Flash Messages -->
                <?php if ($flash = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_SUCCESS)): ?>
                    <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 hidden">
                        <?php echo htmlspecialchars($flash ?? ''); ?>
                    </div>
                <?php endif; ?>
                <?php if ($flash = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_ERROR)): ?>
                    <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 hidden">
                        <?php echo htmlspecialchars($flash ?? ''); ?>
                    </div>
                <?php endif; ?>

                <!-- Content Grid (Cards) -->
                <?php if (!empty($draftActivities)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($draftActivities as $activity): ?>
                    <?php 
                        $proposer = $activity->getTripMember();
                        $proposerUser = $proposer ? $proposer->getUser() : null;
                        $location = $activity->getLocation();
                        $startTime = strtotime($activity->getStartTime());
                        $maxDeadline = date('Y-m-d\TH:i', $startTime - 86400);
                    ?>
                    <article
                        class="bg-surface-container-lowest rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant p-6 border-l-4 border-l-primary flex flex-col gap-4 relative hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                        <!-- Header Row -->
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-xl font-bold text-on-surface pr-4"><?php echo htmlspecialchars($activity->getName() ?? ''); ?></h3>
                                <?php if (!empty($activity->conflicts)): ?>
                                    <button class="cursor-pointer mt-1 flex items-center gap-1.5 text-error text-[12px] font-bold hover:underline"    type="button" onclick="showConflictDetails(<?= htmlspecialchars(json_encode(['name' => $activity->getName(), 'conflicts' => array_map(fn($c) => ['name' => $c->getName()], $activity->conflicts)]), ENT_QUOTES, 'UTF-8') ?>)"  >
                                        <span class="material-symbols-outlined text-[16px]">warning</span>
                                        Conflicts with confirmed activities!
                                    </button>
                                <?php endif; ?>
                            </div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full bg-surface-container-highest text-on-surface text-xs font-bold uppercase shrink-0">
                                <?php echo $activity->getCategory(); ?>
                            </span>
                        </div>
                        <!-- Meta Info Row -->
                        <div class="flex flex-col gap-2 mb-2">
                            <div class="flex items-center gap-2 text-on-surface-variant text-sm">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                <span><?php echo $location ? htmlspecialchars($location->getName() ?? '') : 'No Location'; ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-on-surface-variant text-sm">
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                <span>
                                    <span class="local-time" data-utc="<?= date('c', strtotime($activity->getStartTime())) ?>" data-format="datetime"></span> - 
                                    <span class="local-time" data-utc="<?= date('c', strtotime($activity->getEndTime())) ?>" data-format="datetime"></span>
                                </span>
                            </div>
                        </div>
                        <!-- Proposer & Flags -->
                        <div
                            class="flex items-center justify-between bg-surface p-4 rounded-lg border border-surface-variant mb-4">
                            <div class="flex items-center gap-3">
                                <?php if ($proposerUser && $proposerUser->getProfileImage()): ?>
                                <img alt="Avatar" class="w-10 h-10 rounded-full ring-2 ring-white object-cover"
                                    src="/<?php echo htmlspecialchars($proposerUser->getProfileImage() ?? ''); ?>" />
                                <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold text-sm ring-2 ring-white">
                                    <?php echo $proposerUser ? strtoupper(substr($proposerUser->getFirstName() ?? '', 0, 1) . substr($proposerUser->getLastName() ?? '', 0, 1)) : '??'; ?>
                                </div>
                                <?php endif; ?>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Proposed
                                        By</span>
                                    <span class="text-sm text-on-surface font-semibold"><?php echo $proposer ? htmlspecialchars($proposer->getDisplayName()) : 'Unknown'; ?></span>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1 px-3 py-1 rounded-full <?php echo $activity->getIsAnonymous() ? 'bg-surface-container-highest text-on-surface-variant' : 'bg-tertiary-fixed text-on-tertiary-fixed'; ?>">
                                <span class="material-symbols-outlined text-[14px]"><?php echo $activity->getIsAnonymous() ? 'visibility_off' : 'how_to_vote'; ?></span>
                                <span class="text-[10px] uppercase font-bold tracking-wider">
                                    <?php echo $activity->getIsAnonymous() ? 'Anonymous' : 'Public'; ?> Voting
                                </span>
                            </div>
                        </div>
                        
                        <!-- Actions Form -->
                        <form id="approve_form_<?php echo $activity->getId(); ?>" action="/itinerary/<?php echo $itineraryId; ?>/proposals/<?php echo $activity->getId(); ?>/approve" method="POST" class="flex flex-col gap-4 flex-1">
                            <!-- Poll Deadline -->
                            <div class="mb-2 flex flex-col gap-2">
                                <input type="hidden" name="timezone" class="clientTimezone" value="">
                                <label class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Poll Deadline</label>
                                <input
                                    class="w-full bg-surface border border-outline-variant rounded-md px-4 py-2 text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer"
                                    type="datetime-local" 
                                    name="poll_deadline" 
                                    min="<?= date('Y-m-d\TH:i') ?>"
                                    max="<?= $maxDeadline ?>"
                                    required />
                                <span class="text-[10px] text-on-surface-variant italic">Must be at least 24 hours before activity start.</span>
                            </div>
                            <!-- Description -->
                            <p class="text-sm text-on-surface-variant line-clamp-3 mb-4 flex-1">
                                <?php echo htmlspecialchars($activity->getDescription() ?? ''); ?>
                            </p>
                        </form>

                        <!-- Actions Buttons -->
                        <div class="flex items-center gap-4 mt-auto pt-4 border-t border-surface-variant">
                                <button class="cursor-pointer flex-1 bg-primary text-on-primary font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-transform duration-200 ease-out hover:scale-103 active:scale-98 transform-gpu antialiased shadow-sm text-sm"    type="submit" form="approve_form_<?php echo $activity->getId(); ?>"
                                     >
                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                    Confirm to Voting
                                </button>
                            <form action="/itinerary/<?php echo $itineraryId; ?>/proposals/<?php echo $activity->getId(); ?>/reject" method="POST" class="flex-1">
                                <button class="cursor-pointer w-full bg-transparent border border-outline text-on-surface font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all duration-200 ease-out hover:bg-surface-variant hover:scale-103 active:scale-98 transform-gpu antialiased text-sm"    type="submit"
                                    >
                                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                                    Reject
                                </button>
                            </form>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-12 text-center flex flex-col items-center gap-4">
                    <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">drafts</span>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-xl font-bold text-on-surface">No draft proposals to review</h3>
                        <p class="text-on-surface-variant">When group members propose new activities, they will appear here for your review.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Rejected Activities Section -->
                <div class="mt-16 border-t border-surface-variant pt-10">
                    <h2 class="text-2xl font-bold text-on-surface mb-6">Rejected Activities</h2>
                    <?php if (!empty($rejectedActivities)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($rejectedActivities as $activity): ?>
                        <?php 
                            $proposer = $activity->getTripMember();
                            $proposerUser = $proposer ? $proposer->getUser() : null;
                            $location = $activity->getLocation();
                        ?>
                        <article
                            class="bg-surface-container-low rounded-lg border border-outline-variant p-4 flex flex-col gap-2 opacity-75">
                            <div class="flex justify-between items-start">
                                <h4 class="text-base font-semibold text-on-surface-variant"><?php echo htmlspecialchars($activity->getName() ?? ''); ?></h4>
                                <span
                                    class="text-[10px] font-bold uppercase text-error px-2 py-1 bg-error-container/30 rounded"><?php echo ActivityStatus::REJECTED->value; ?></span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 text-on-surface-variant/80 text-xs">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span><?php echo $location ? htmlspecialchars($location->getName() ?? '') : 'No Location'; ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-on-surface-variant/80 text-xs">
                                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                    <span>
                                        <span class="local-time" data-utc="<?= date('c', strtotime($activity->getStartTime())) ?>" data-format="datetime"></span> - 
                                        <span class="local-time" data-utc="<?= date('c', strtotime($activity->getEndTime())) ?>" data-format="datetime"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="mt-1 flex items-center gap-1.5">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Proposed
                                    by:</span>
                                <span class="text-xs text-on-surface-variant font-medium"><?php echo $proposer ? htmlspecialchars($proposer->getDisplayName()) : 'Unknown'; ?></span>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="bg-surface-container-low border border-outline-variant rounded-xl p-8 text-center opacity-60">
                        <p class="text-on-surface-variant text-sm italic">No rejected activities yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <!-- BottomNavBar (Mobile) -->
    <nav
        class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md text-orange-600 dark:text-orange-400 font-['Plus_Jakarta_Sans'] text-[10px] font-semibold fixed bottom-0 w-full rounded-t-2xl border-t border-zinc-100 dark:border-zinc-800 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] lg:hidden flex justify-around items-center px-4 pb-6 pt-2 z-50">
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 rounded-xl transition-colors cursor-pointer"    
            href="/itinerary/dashboard/<?php echo htmlspecialchars($itineraryId ?? '') ?>">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="map">map</span>
            <span>Trip</span>
        </a>
        <a class="flex flex-col items-center justify-center text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30 rounded-xl px-4 py-1 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 transition-transform duration-100 scale-90 cursor-pointer"    
            href="/itinerary/<?php echo htmlspecialchars($itineraryId ?? '') ?>/proposals">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="rule"
                style="font-variation-settings: 'FILL' 1;">rule</span>
            <span>Manage</span>
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 rounded-xl transition-colors cursor-pointer"    
            href="/profile">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="person">person</span>
            <span>Profile</span>
        </a>
    </nav>
    <!-- Conflict Details Modal -->
    <div id="conflictDetailsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-on-background/50 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-2xl bg-surface-container-lowest shadow-lg border border-outline-variant overflow-hidden">
            <div class="flex items-center justify-between border-b border-outline-variant px-6 py-4 bg-surface">
                <h3 class="font-display text-[20px] font-semibold text-on-surface m-0 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">warning</span> Conflict Details
                </h3>
                <button class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition cursor-pointer"    type="button" onclick="closeConflictDetailsModal()" >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-on-surface-variant mb-4">
                    The activity "<span id="modalActivityName" class="font-bold"></span>" overlaps with the following confirmed activities:
                </p>
                <ul id="modalConflictsList" class="space-y-3 mb-6">
                    <!-- Conflicts will be injected here -->
                </ul>
                <div class="flex justify-end">
                    <button class="px-6 py-2 rounded-lg bg-surface-container-highest text-on-surface font-semibold text-sm hover:bg-outline-variant transition cursor-pointer"    type="button" onclick="closeConflictDetailsModal()" >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/timezone.js"></script>
    <script>
        function showConflictDetails(data) {
            document.getElementById('modalActivityName').innerText = data.name;
            const list = document.getElementById('modalConflictsList');
            list.innerHTML = data.conflicts.map(c => `
                <li class="flex items-center gap-3 p-3 bg-error/5 rounded-lg border border-error/10 text-error">
                    <span class="material-symbols-outlined text-[20px]">event_busy</span>
                    <span class="font-medium">${c.name}</span>
                </li>
            `).join('');
            document.getElementById('conflictDetailsModal').classList.remove('hidden');
        }

        function closeConflictDetailsModal() {
            document.getElementById('conflictDetailsModal').classList.add('hidden');
        }

        // Close on escape or outside click
        window.onclick = function(event) {
            const modal = document.getElementById('conflictDetailsModal');
            if (event.target == modal) closeConflictDetailsModal();
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeConflictDetailsModal();
            }
        });

        // Set all hidden timezone inputs
        document.addEventListener('DOMContentLoaded', () => {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.querySelectorAll('.clientTimezone').forEach(el => {
                el.value = tz;
            });

            // Update min attribute to local time every minute to stay accurate
            const updateMinTime = () => {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                const minStr = now.toISOString().slice(0, 16);
                document.querySelectorAll('input[type="datetime-local"][name="poll_deadline"]').forEach(input => {
                    input.min = minStr;
                });
            };
            updateMinTime();
            setInterval(updateMinTime, 60000);
        });
    </script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
