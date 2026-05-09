<?php
require __DIR__ . '/../layouts/header.php';

use App\Helpers\Auth;
use App\Models\HistoryLogEntry;
use App\Enums\TripMemberRole;
use App\Enums\EntityType;
use App\Enums\TransactionType;
?>

        <!-- <main class="max-w-[1280px] mx-auto px-6 lg:px-8 py-8 w-full"> -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="font-display text-display text-on-surface">
                        Trip History &amp; Rollback
                    </h1>
                    <p class="text-body-lg text-on-surface-variant mt-2 max-w-2xl font-body">
                        A chronological record of all modifications to the itinerary.
                    </p>
                </div>
            </div>

            <?php if ($flash = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_SUCCESS)): ?>
            <div class="mb-6 p-4 bg-primary-container text-on-primary rounded-xl border border-primary/20 flex items-center gap-3 hidden">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="font-medium"><?php echo htmlspecialchars($flash); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($flash = \App\Helpers\Session::getFlash(\App\Helpers\Session::FLASH_ERROR)): ?>
            <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-xl border border-error/20 flex items-center gap-3 hidden">
                <span class="material-symbols-outlined">error</span>
                <span class="font-medium"><?php echo htmlspecialchars($flash); ?></span>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <?php if (empty($groupedEntries)): ?>
                    <div class="bg-surface-container-lowest rounded-xl p-12 text-center border border-outline-variant">
                        <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">history</span>
                        <h3 class="text-h3 text-on-surface">No history entries yet</h3>
                        <p class="text-on-surface-variant mt-2 font-body">Any changes made to the trip will appear here.</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($groupedEntries as $dateStr => $dailyEntries): ?>
                    <div class="relative pl-8 border-l-2 border-outline-variant pb-4">
                        <div class="absolute -left-[13px] top-0 bg-surface-container-lowest border-2 border-outline-variant rounded-full p-1 leading-none">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">today</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-6">
                            <?php echo HistoryLogEntry::getFormattedDateHeader($dateStr); ?>
                        </h3>

                        <?php foreach ($dailyEntries as $entry): ?>
                        <?php
                            $member = $entry->getTripMember();
                            $user   = $member ? $member->getUser() : null;
                            $type   = $entry->getTransactionType();

                            $style = match(true) {
                                str_contains($type, 'REMOVED') || str_contains($type, 'DELETED') || str_contains($type, 'LEFT') => [
                                    'bg' => 'bg-error-container text-on-error-container',
                                    'icon' => str_contains($type, 'MEMBER') ? 'person_remove' : 'delete',
                                    'label' => str_contains($type, 'LEFT') ? 'Left' : 'Removed'
                                ],
                                str_contains($type, 'RESTORED') => [
                                    'bg' => 'bg-primary-container text-on-primary-container',
                                    'icon' => 'history',
                                    'label' => 'Restored'
                                ],
                                str_contains($type, 'ADDED') || str_contains($type, 'CREATED') || str_contains($type, 'JOINED') => [
                                    'bg' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                                    'icon' => str_contains($type, 'JOINED') ? 'person_add' : 'add',
                                    'label' => str_contains($type, 'JOINED') ? 'Joined' : 'Added'
                                ],
                                default => [
                                    'bg' => 'bg-secondary-container text-on-secondary-container',
                                    'icon' => 'edit',
                                    'label' => 'Updated'
                                ]
                            };
                        ?>
                        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant mb-6 relative hover:shadow-md transition-shadow group">
                            <div class="absolute w-8 h-[2px] bg-outline-variant -left-8 top-10 group-hover:bg-primary transition-colors"></div>

                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-4">
                                    <?php if ($user && $user->getProfileImage()): ?>
                                    <img src="/<?php echo htmlspecialchars($user->getProfileImage()); ?>" alt="Profile" class="w-10 h-10 rounded-full border border-outline-variant object-cover">
                                    <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold font-display">
                                        <?php echo $user ? strtoupper(substr($user->getFirstName(), 0, 1) . substr($user->getLastName(), 0, 1)) : '??'; ?>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-body text-body-md text-on-surface">
                                            <span class="font-bold text-primary"><?php echo $member ? htmlspecialchars($member->getDisplayName()) : 'System'; ?></span>
                                            <?php
                                            $transType = TransactionType::tryFrom($entry->getTransactionType());
                                            echo strtolower($transType ? $transType->label() : str_replace('_', ' ', $entry->getTransactionType())); 
                                            ?>
                                        </p>
                                        <p class="text-label-xs text-on-surface-variant uppercase tracking-wider font-bold">
                                            <span class="local-time" data-utc="<?= date('c', strtotime($entry->getTimestamp())) ?>" data-format="time"></span>
                                        </p>
                                    </div>
                                </div>
                                <span class="<?php echo $style['bg']; ?> px-3 py-1 rounded-full text-label-xs font-bold uppercase flex items-center gap-1 border border-outline-variant/30">
                                    <span class="material-symbols-outlined text-[14px]"><?php echo $style['icon']; ?></span>
                                    <?php echo $style['label']; ?>
                                </span>
                            </div>

                            <!-- <div class="bg-surface-container-low rounded-lg p-4 border border-outline-variant/50">
                                <h4 class="font-display text-h4 text-on-surface mb-1">
                                    <?php echo htmlspecialchars($entry->getEntitySummary()); ?>
                                </h4>
                                <p class="text-label-xs text-on-surface-variant uppercase font-bold tracking-tight">
                                    Type: <?php echo htmlspecialchars($entry->getChangedEntityType()); ?>
                                </p>
                            </div> -->
                            <div class="bg-surface-container-low rounded-lg p-4 border border-outline-variant/50">
                                <?php 
                                    $rawEntityType = $entry->getChangedEntityType();
                                    $entityTypeEnum = EntityType::tryFrom($rawEntityType);
                                    $entityTypeLabel = $entityTypeEnum ? $entityTypeEnum->label() : $rawEntityType;
                                ?>
                                <?php if ($rawEntityType === EntityType::ACTIVITY->value): ?>
                                    <?php $activity = $entry->getRelatedEntity(); ?>
                                    
                                    <?php if ($activity): ?>
                                        <h4 class="font-display text-h4 text-on-surface mb-1">
                                            <?= htmlspecialchars($activity->getName() ?? 'Unknown Activity'); ?>
                                        </h4>
                                        <p class="text-label-xs text-on-surface-variant font-bold tracking-tight mb-3">
                                            Type: <?= $entityTypeLabel ?>
                                        </p>
                                        
                                        <div class="flex flex-col gap-2 text-sm text-on-surface-variant">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                <span>
                                                    <?php 
                                                        $start = strtotime($activity->getStartTime());
                                                        $end = strtotime($activity->getEndTime());
                                                        $isSameDay = date('Y-m-d', $start) === date('Y-m-d', $end);
                                                    ?>
                                                    <?php if ($isSameDay): ?>
                                                        <?= date('M d, ', $start) ?> <?= date('h:i A', $start) ?> - <?= date('h:i A', $end) ?>
                                                    <?php else: ?>
                                                        <?= date('M d, h:i A', $start) ?> - <?= date('M d, h:i A', $end) ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                                <span><?= htmlspecialchars($activity->getLocation()->getName() ?? 'N/A') ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <h4 class="font-display text-h4 text-on-surface mb-1">Activity Unavailable</h4>
                                        <p class="text-label-xs text-on-surface-variant font-bold tracking-tight">
                                            Type: <?= $entityTypeLabel ?>
                                        </p>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <h4 class="font-display text-h4 text-on-surface mb-1">
                                        <?= htmlspecialchars($entry->getEntitySummary()); ?>
                                    </h4>
                                    <p class="text-label-xs text-on-surface-variant font-bold tracking-tight">
                                        Type: <?= $entityTypeLabel ?>
                                    </p>
                                <?php endif; ?>
                            </div>                            

                            <?php if ($entry->isUndoable && Auth::hasRole(TripMemberRole::EDITOR->value, $memberRole)): ?>
                            <div class="mt-4 flex justify-end">
                                <form action="/itinerary/<?php echo $itineraryId; ?>/history/revert/<?php echo $entry->getId(); ?>" method="POST">
                                    <input type="hidden" name="timezone" id="clientTimezoneReopen" value="">
                                    <button class="cursor-pointer inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-bold text-sm px-6 py-2.5 rounded-lg transition-transform duration-200 ease-out hover:scale-103 active:scale-98 transform-gpu antialiased shadow-sm"    type="submit" 
                                        >
                                        <span class="material-symbols-outlined text-[18px]">history</span>
                                        Undo this change
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Start Marker -->
                    <div class="relative pl-8">
                        <div class="absolute -left-[7px] top-0 w-[14px] h-[14px] bg-outline rounded-full ring-4 ring-surface"></div>
                        <p class="font-body text-body-sm text-on-surface-variant italic">
                            Trip created on <?= $tripCreatedAt ? date('M j, Y', strtotime($tripCreatedAt)) : 'Oct 1, 2024' ?>
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant">
                        <h3 class="font-display text-h3 text-on-surface mb-6">Filter Log</h3>
                        <form id="filterForm" method="GET" class="space-y-6">
                            <div>
                                <label class="text-label-xs font-bold uppercase tracking-wider text-on-surface-variant block mb-3">Action Type</label>
                                <div class="flex flex-wrap gap-2">
                                    <?php 
                                        $actions = [
                                            null => 'All',
                                            'additions' => 'Additions',
                                            'edits' => 'Edits',
                                            'removals' => 'Removals',
                                            'rollbacks' => 'Rollbacks'
                                        ];
                                        foreach ($actions as $val => $label):
                                            $isActive = ($filters['action'] ?? null) == $val;
                                    ?>
                                        <button type="button" onclick="setFilter('action', '<?= $val ?>')" 
                                            class="cursor-pointer px-3 py-1 rounded-full border text-body-sm font-semibold transition-colors <?= $isActive ? 'bg-primary border-primary text-on-primary' : 'border-outline-variant hover:bg-surface-container text-on-surface-variant' ?>">
                                            <?= $label ?>
                                        </button>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="action" id="filter-action" value="<?= htmlspecialchars($filters['action'] ?? '') ?>">
                                </div>
                            </div>

                            <div>
                                <label class="text-label-xs font-bold uppercase tracking-wider text-on-surface-variant block mb-3">Entity Type</label>
                                <div class="flex flex-wrap gap-2">
                                    <?php 
                                        $entities = [null => 'All'];
                                        foreach (EntityType::cases() as $case) {
                                            $entities[$case->value] = $case->label();
                                        }
                                        foreach ($entities as $val => $label):
                                            $isActive = ($filters['entityType'] ?? null) == $val;
                                    ?>
                                        <button type="button" onclick="setFilter('entityType', '<?= $val ?>')" 
                                            class="cursor-pointer px-3 py-1 rounded-full border text-body-sm font-semibold transition-colors <?= $isActive ? 'bg-primary border-primary text-on-primary' : 'border-outline-variant hover:bg-surface-container text-on-surface-variant' ?>">
                                            <?= $label ?>
                                        </button>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="entityType" id="filter-entityType" value="<?= htmlspecialchars($filters['entityType'] ?? '') ?>">
                                </div>
                            </div>

                            <div>
                                <label class="text-label-xs font-bold uppercase tracking-wider text-on-surface-variant block mb-3">Modified By</label>
                                <div class="relative">
                                    <select name="memberId" onchange="this.form.submit()" 
                                        class="cursor-pointer w-full bg-surface-container-lowest border border-outline-variant text-on-surface text-body-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5 appearance-none outline-none">
                                        <option value="">All Users</option>
                                        <?php foreach ($allMembers as $m): ?>
                                            <option value="<?= $m['memberId'] ?>" <?= ($filters['memberId'] ?? '') == $m['memberId'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($m['firstName'] . ' ' . $m['lastName']) ?> 
                                                <?= $m['deletedAt'] ? '(Former)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="material-symbols-outlined text-on-surface-variant">keyboard_arrow_down</span>
                                    </div>
                                </div>
                            </div>

                            <?php if (array_filter($filters)): ?>
                                <div class="pt-2">
                                    <a href="/itinerary/<?= $itineraryId ?>/history" class="cursor-pointer text-body-sm font-bold text-primary hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">close</span> Clear All Filters
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <script>
                        function setFilter(name, value) {
                            document.getElementById('filter-' + name).value = value;
                            document.getElementById('filterForm').submit();
                        }
                    </script>

                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant">
                        <h3 class="font-display text-h3 text-on-surface mb-4">Log Summary</h3>
                        <div class="flex flex-col gap-2 font-body">
                            <div class="flex justify-between items-center py-3 border-b border-outline-variant/30">
                                <span class="text-body-sm text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-tertiary">add_circle</span>
                                    Additions
                                </span>
                                <span class="text-h3 text-on-surface"><?php echo $counts['additions']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-outline-variant/30">
                                <span class="text-body-sm text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-error">delete</span>
                                    Removals
                                </span>
                                <span class="text-h3 text-on-surface"><?php echo $counts['removals']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-outline-variant/30">
                                <span class="text-body-sm text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-secondary">edit</span>
                                    Updates
                                </span>
                                <span class="text-h3 text-on-surface"><?php echo $counts['updated'] ?? 0; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-body-sm text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-primary">history</span>
                                    Rollbacks
                                </span>
                                <span class="text-h3 text-on-surface"><?php echo $counts['rollbacks']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="/assets/js/timezone.js"></script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
