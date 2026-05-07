<?php
require __DIR__ . '/../layouts/header.php';
use App\Models\HistoryLogEntry;
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
                                str_contains($type, 'REMOVED') || str_contains($type, 'DELETED') => [
                                    'bg' => 'bg-error-container text-on-error-container',
                                    'icon' => 'delete',
                                    'label' => 'Removed'
                                ],
                                str_contains($type, 'RESTORED') => [
                                    'bg' => 'bg-primary-container text-on-primary-container',
                                    'icon' => 'history',
                                    'label' => 'Restored'
                                ],
                                str_contains($type, 'ADDED') || str_contains($type, 'CREATED') => [
                                    'bg' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                                    'icon' => 'add',
                                    'label' => 'Added'
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
                                            <span class="font-bold text-primary"><?php echo $user ? htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()) : 'System'; ?></span>
                                            <?php
                                            $actionMessage = explode('_', $entry->getTransactionType())??['', ''];
                                            echo strtolower($actionMessage[1] . ' ' . $actionMessage[0]);
                                            // echo strtolower(str_replace('_', ' ', $entry->getTransactionType())); ?>
                                        </p>
                                        <p class="text-label-xs text-on-surface-variant uppercase tracking-wider font-bold">
                                            <?php echo date('h:i A', strtotime($entry->getTimestamp())); ?>
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
                                <?php if ($entry->getChangedEntityType() === 'Activity'): ?>
                                    <?php $activity = $entry->getRelatedEntity(); ?>
                                    
                                    <?php if ($activity): ?>
                                        <h4 class="font-display text-h4 text-on-surface mb-1">
                                            <?= htmlspecialchars($activity->getName() ?? 'Unknown Activity'); ?>
                                        </h4>
                                        <p class="text-label-xs text-on-surface-variant uppercase font-bold tracking-tight mb-3">
                                            Type: Activity
                                        </p>
                                        
                                        <div class="flex flex-col gap-2 text-sm text-on-surface-variant">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                <span>
                                                    <?= date('M j, Y • g:i A', strtotime($activity->getStartTime())) ?> - 
                                                    <?= date('g:i A', strtotime($activity->getEndTime())) ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                                <span><?= htmlspecialchars($activity->getLocation()->getName() ?? 'N/A') ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <h4 class="font-display text-h4 text-on-surface mb-1">Activity Unavailable</h4>
                                        <p class="text-label-xs text-on-surface-variant uppercase font-bold tracking-tight">
                                            Type: Activity
                                        </p>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <h4 class="font-display text-h4 text-on-surface mb-1">
                                        <?= htmlspecialchars($entry->getEntitySummary()); ?>
                                    </h4>
                                    <p class="text-label-xs text-on-surface-variant uppercase font-bold tracking-tight">
                                        Type: <?= htmlspecialchars($entry->getChangedEntityType()); ?>
                                    </p>
                                <?php endif; ?>
                            </div>                            

                            <?php if ($entry->isUndoable): ?>
                            <div class="mt-4 flex justify-end">
                                <form action="/itinerary/<?php echo $itineraryId; ?>/history/revert/<?php echo $entry->getId(); ?>" method="POST">
                                    <button type="submit" 
                                        class="cursor-pointer inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-bold text-sm px-6 py-2.5 rounded-lg 
                                            transition-transform duration-200 ease-out 
                                            hover:scale-103 active:scale-98
                                            transform-gpu antialiased shadow-sm">
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
                </div>

                <div class="lg:col-span-4 flex flex-col gap-6">
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
<?php require __DIR__ . '/../layouts/footer.php'; ?>
