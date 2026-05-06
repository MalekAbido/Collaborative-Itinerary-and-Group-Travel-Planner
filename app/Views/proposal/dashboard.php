<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Activity Status Management - VoyageSync</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <style>
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
    </style>
</head>

<body class="bg-background text-on-background antialiased flex h-screen overflow-hidden font-body-md text-body-md">
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-full w-full relative">
        <!-- TopNavBar -->
        <nav
            class="fixed inset-x-0 top-0 z-50 h-16 bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm">
            <div class="mx-auto flex h-full max-w-[1280px] items-center justify-between px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="/dashboard"
                        class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="/dashboard"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Dashboard</a>
                        <a href="/itinerary/dashboard/<?php echo htmlspecialchars($itineraryId ?? '') ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Itinerary</a>
                        <a href="/itinerary/polls/<?php echo htmlspecialchars($itineraryId ?? '') ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Polls</a>
                        <a href="/finance/dashboard/<?php echo htmlspecialchars($itineraryId ?? '') ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Finances</a>
                        <?php if (\App\Helpers\Auth::hasRole("Editor", $memberRole)): ?>
                        <a href="/itinerary/<?php echo htmlspecialchars($itineraryId ?? '') ?>/proposals"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-primary border-b-2 border-primary">Proposals</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="inline-flex items-center gap-1 rounded-lg border-2 border-error px-3 py-1.5 text-body-xs font-bold tracking-wide text-error hover:bg-error-container transition">
                        <span class="material-symbols-outlined text-base">warning</span>SOS
                    </button>
                    <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                        <?php $currentUser = \App\Helpers\Auth::user(); ?>
                        <?php if ($currentUser && $currentUser->getProfileImage()): ?>
                        <img src="/<?php echo htmlspecialchars($currentUser->getProfileImage() ?? '') ?>" alt="Profile"
                            class="h-8 w-8 rounded-full border-2 border-outline-variant object-cover">
                        <?php elseif ($currentUser): ?>
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                            <?php echo strtoupper(substr($currentUser->getFirstName() ?? '', 0, 1) . substr($currentUser->getLastName() ?? '', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </nav>
        <!-- Main Scrollable Canvas -->
        <main class="flex-1 overflow-y-auto bg-surface pb-16 pt-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
                <!-- Page Header -->
                <div class="mb-10">
                    <h1 class="text-4xl font-bold text-on-surface mb-2">Proposed Activities Review</h1>
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
                    ?>
                    <article
                        class="bg-surface-container-lowest rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant p-6 border-l-4 border-l-primary flex flex-col gap-4 relative hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                        <!-- Header Row -->
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-on-surface pr-4"><?php echo htmlspecialchars($activity->getName() ?? ''); ?></h3>
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
                                <span><?php echo date('M d, h:i A', strtotime($activity->getStartTime())); ?> - <?php echo date('h:i A', strtotime($activity->getEndTime())); ?></span>
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
                                    <span class="text-sm text-on-surface font-semibold"><?php echo $proposerUser ? htmlspecialchars(($proposerUser->getFirstName() ?? '') . ' ' . ($proposerUser->getLastName() ?? '')) : 'Unknown'; ?></span>
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
                                <label class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Poll Deadline</label>
                                <input
                                    class="w-full bg-surface border border-outline-variant rounded-md px-4 py-2 text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer"
                                    type="datetime-local" 
                                    name="poll_deadline" 
                                    required />
                                <!-- <span class="text-[10px] text-on-surface-variant italic">Select the date and time for the poll to close.</span> -->
                            </div>
                            <!-- Description -->
                            <p class="text-sm text-on-surface-variant line-clamp-3 mb-4 flex-1">
                                <?php echo htmlspecialchars($activity->getDescription() ?? ''); ?>
                            </p>
                        </form>

                        <!-- Actions Buttons -->
                        <div class="flex items-center gap-4 mt-auto pt-4 border-t border-surface-variant">
                            <button type="submit" form="approve_form_<?php echo $activity->getId(); ?>"
                                class="flex-1 bg-primary text-on-primary font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 hover:bg-primary/90 transition-colors shadow-sm text-sm">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                Confirm to Voting
                            </button>
                            <form action="/itinerary/<?php echo $itineraryId; ?>/proposals/<?php echo $activity->getId(); ?>/reject" method="POST" class="flex-1">
                                <button type="submit"
                                    class="w-full bg-transparent border border-outline text-on-surface font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 hover:bg-surface-variant transition-colors text-sm">
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
                                    class="text-[10px] font-bold uppercase text-error px-2 py-1 bg-error-container/30 rounded">Rejected</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 text-on-surface-variant/80 text-xs">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span><?php echo $location ? htmlspecialchars($location->getName() ?? '') : 'No Location'; ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-on-surface-variant/80 text-xs">
                                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                    <span><?php echo date('M d, h:i A', strtotime($activity->getStartTime())); ?> - <?php echo date('h:i A', strtotime($activity->getEndTime())); ?></span>
                                </div>
                            </div>
                            <div class="mt-1 flex items-center gap-1.5">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Proposed
                                    by:</span>
                                <span class="text-xs text-on-surface-variant font-medium"><?php echo $proposerUser ? htmlspecialchars(($proposerUser->getFirstName() ?? '') . ' ' . ($proposerUser->getLastName() ?? '')) : 'Unknown'; ?></span>
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
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 rounded-xl transition-colors"
            href="/itinerary/dashboard/<?php echo htmlspecialchars($itineraryId ?? '') ?>">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="map">map</span>
            <span>Trip</span>
        </a>
        <a class="flex flex-col items-center justify-center text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30 rounded-xl px-4 py-1 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 transition-transform duration-100 scale-90"
            href="/itinerary/<?php echo htmlspecialchars($itineraryId ?? '') ?>/proposals">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="rule"
                style="font-variation-settings: 'FILL' 1;">rule</span>
            <span>Manage</span>
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 rounded-xl transition-colors"
            href="#">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="inventory_2">inventory_2</span>
            <span>Inventory</span>
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 w-16 h-14 rounded-xl transition-colors"
            href="/profile">
            <span class="material-symbols-outlined mb-1 text-[24px]" data-icon="person">person</span>
            <span>Profile</span>
        </a>
    </nav>
</body>

</html>
