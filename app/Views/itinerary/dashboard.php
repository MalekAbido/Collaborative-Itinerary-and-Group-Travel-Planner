<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php 
    $trip      = $data['trip'] ?? null;
    $tripId    = $trip['id'] ?? null;
    $userRole  = $data['userRole'] ?? $userRole ?? null;
 ?>

        <div class="max-w-[1280px] mx-auto">

                <header class="mb-10 flex justify-between items-end">
                    <div>
                        <span
                            class="inline-flex items-center rounded-full bg-success-container px-3 py-1 text-label-xs font-bold uppercase text-on-success-container mb-3">✓
                            Active Trip</span>
                        <h1 class="font-display text-display text-on-surface mb-2">
                            <?= htmlspecialchars($data['trip']['title']) ?></h1>
                        <p class="text-body-lg text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                            <?= htmlspecialchars($data['trip']['startDate']) ?> to
                            <?= htmlspecialchars($data['trip']['endDate']) ?>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <?php if($userRole !== 'Member'):?>
                        <a href="/itinerary/settings/<?= htmlspecialchars($data['trip']['id']) ?>" class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-body-sm px-6 py-2.5 hover:bg-surface-container transition">
                            <span class="material-symbols-outlined text-[18px]">settings</span> Settings
                        </a>
                        <?php endif;?>
                        <a href="/itinerary/<?= htmlspecialchars($data['trip']['id']) ?>/activity/create"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                            <span class="material-symbols-outlined text-[18px]">add</span> Add Activity
                        </a>
                    </div>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    
                    <div class="sm:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-tertiary-fixed"><span class="material-symbols-outlined text-[18px] text-tertiary">description</span></div>
                            <span class="text-label-caps uppercase text-outline m-0">Trip Description</span>
                        </div>
                        <div class="text-body-md text-on-surface font-medium">
                            <?= htmlspecialchars($data['trip']['description'] ?: 'No description provided.') ?></div>
                    </div>
                    
                    <div class="sm:col-span-1 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-fixed"><span class="material-symbols-outlined text-[18px] text-primary">group</span></div>
                            <span class="text-label-caps uppercase text-outline m-0">Members</span>
                        </div>
                        <div class="font-display text-[28px] font-extrabold text-on-surface"><?= count($data['members']) ?></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                            <span class="material-symbols-outlined text-primary">map</span>
                            <h2 class="font-display text-h2 text-on-surface m-0">Itinerary Timeline</h2>
                        </div>

                        <?php if (!empty($data['activities'])): ?>
                            <div class="relative border-l-2 border-outline-variant ml-4 mt-4">
                                <?php foreach ($data['activities'] as $activity): ?>
                                    <?php 
                                        $start = strtotime($activity->getStartTime());
                                        $end = strtotime($activity->getEndTime());
                                        $isSameDay = date('Y-m-d', $start) === date('Y-m-d', $end);
                                    ?>
                                    <div class="mb-8 ml-8 relative group">
                                        <span class="absolute flex items-center justify-center w-4 h-4 bg-primary rounded-full -left-[41px] top-1 ring-4 ring-surface"></span>
                                        
                                        <div class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 hover:shadow-md transition relative">
                                            
                                            <a href="/itinerary/<?= htmlspecialchars($data['trip']['id']) ?>/activity/<?= $activity->getId() ?>" class="absolute inset-0 z-0 rounded-xl focus:outline-none">
                                                <span class="sr-only">View Activity Details</span>
                                            </a>

                                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="font-display text-h4 text-on-surface mb-1 group-hover:text-primary transition-colors">
                                                        <?= htmlspecialchars($activity->getName()) ?>
                                                    </h3>
                                                    
                                                    <div class="flex flex-wrap items-center text-body-sm text-on-surface-variant font-medium gap-x-4 gap-y-2 mt-2">
                                                        
                                                        <div class="flex items-center gap-1 whitespace-nowrap">
                                                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                            <?php if ($isSameDay): ?>
                                                                <?= date('M d, Y', $start) ?> • <?= date('h:i A', $start) ?> - <?= date('h:i A', $end) ?>
                                                            <?php else: ?>
                                                                <?= date('M d, h:i A', $start) ?> - <?= date('M d, h:i A', $end) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if ($activity->getLocation() && $activity->getLocation()->getName()): ?>
                                                        <div class="flex items-center gap-1 min-w-0">
                                                            <span class="material-symbols-outlined text-[16px] shrink-0">location_on</span>
                                                            <span class="truncate"><?= htmlspecialchars($activity->getLocation()->getName()) ?></span>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-3 relative z-10 shrink-0">
                                                    <span class="inline-flex items-center rounded-full bg-primary-fixed px-3 py-1 text-label-xs font-bold uppercase text-primary">
                                                        <?= htmlspecialchars($activity->getCategory()) ?>
                                                    </span>
                                                    
                                                    <?php if($userRole !== 'Member'): ?>
                                                        <form action="/itinerary/<?= htmlspecialchars($data['trip']['id']) ?>/activity/<?= $activity->getId() ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');" class="m-0">
                                                            <button type="submit" class="text-error hover:bg-error-container/50 p-1.5 rounded-md transition-colors flex items-center justify-center" title="Delete Activity">
                                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <?php if (!empty($activity->getDescription())): ?>
                                                <p class="text-body-md text-on-surface mt-4 pt-4 border-t border-outline-variant/50 relative z-10">
                                                    <?= nl2br(htmlspecialchars($activity->getDescription())) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <article
                                class="flex bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden hover:shadow-md transition mb-4 opacity-50">
                                <div
                                    class="flex flex-col items-center justify-center bg-surface-container-highest text-outline px-4 py-5 w-24 shrink-0">
                                    <span class="material-symbols-outlined text-[28px]">event_busy</span>
                                </div>
                                <div class="flex-1 p-5 flex flex-col justify-center">
                                    <h4 class="font-display text-h4 text-on-surface mb-1">No activities planned yet.</h4>
                                    <p class="text-body-sm text-on-surface-variant">Click "Add Activity" above to start
                                        building your timeline.</p>
                                </div>
                            </article>
                        <?php endif; ?>
                    </div>

                <div class="space-y-8">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-outline-variant">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">group</span>
                                <h2 class="font-display text-h3 text-on-surface m-0">Members</h2>
                            </div>
                            <a href="/itinerary/members/<?= htmlspecialchars($data['trip']['id']) ?>" class="text-body-sm font-semibold text-primary hover:underline">Manage</a>                        
                        </div> 
                        
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-4">
                            <div class="divide-y divide-outline-variant">
                                <?php if (!empty($data['members'])): ?>
                                    <?php foreach ($data['members'] as $member): ?>
                                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                                            <div class="flex items-center gap-3">
                                                <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary text-sm font-semibold">
                                                    <?= strtoupper(substr($member['firstName'], 0, 1) . substr($member['lastName'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="font-display text-h4 text-on-surface"><?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?></div>
                                                    <div class="text-body-xs text-outline"><?= htmlspecialchars($member['email']) ?></div>
                                                </div>
                                            </div>
                                            <div>
                                                <?php if ($member['role'] === 'Organizer'): ?>
                                                    <span class="inline-flex items-center rounded-full bg-primary-fixed px-2 py-0.5 text-[10px] font-bold uppercase text-primary">👑 Organizer</span>
                                                <?php elseif ($member['role'] === 'Editor'): ?>
                                                    <span class="inline-flex items-center rounded-full bg-secondary-fixed px-2 py-0.5 text-[10px] font-bold uppercase text-secondary">✏️ Editor</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center rounded-full bg-surface-container-highest px-2 py-0.5 text-[10px] font-bold uppercase text-outline">👤 Member</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="py-2 text-center text-on-surface-variant text-body-sm">No members found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        </div>
                </div>

                </div>
            </div>
        <?php require __DIR__ . '/../layouts/footer.php'; ?>