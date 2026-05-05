<?php

use App\Helpers\Auth;

/**
 * @var array $itinerary
 * @var array $activePolls
 * @var array $closedPolls
 * @var array $ratingChoices
 * @var string $userRole
 */

// Determine if the current user has permission to manage polls
$canManagePolls = Auth::hasRole('Editor', $userRole ?? 'Member');
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polls - <?= htmlspecialchars($itinerary['title']) ?> - VoyageSync</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f65a41",
                        "primary-container": "#ff8b71",
                        "primary-fixed": "#ffdad3",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#7b2a1a",
                        "secondary": "#825500",
                        "secondary-container": "#feaa00",
                        "secondary-fixed": "#ffddb3",
                        "on-secondary": "#ffffff",
                        "tertiary": "#006a5f",
                        "tertiary-fixed": "#8df5e4",
                        "on-tertiary": "#ffffff",
                        "success": "#16a34a",
                        "success-container": "#bbf7d0",
                        "on-success": "#ffffff",
                        "on-success-container": "#064e2c",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a",
                        "background": "#fcf8f8",
                        "on-background": "#191c1d",
                        "surface": "#fcf8f8",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f3f4f5",
                        "surface-container": "#edeeef",
                        "surface-container-highest": "#e1e3e4",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#414754",
                        "outline": "#727785",
                        "outline-variant": "#c1c6d6",
                    },
                    fontFamily: {
                        display: ["'Plus Jakarta Sans'", "sans-serif"],
                        body: ["'Inter'", "sans-serif"]
                    },
                }
            }
        };
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body { height: 100%; }
            body { @apply font-body text-[16px] bg-background text-on-background overflow-hidden m-0; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
            .scroll-thin::-webkit-scrollbar { width: 6px; }
            .scroll-thin::-webkit-scrollbar-track { background: transparent; }
            .scroll-thin::-webkit-scrollbar-thumb { @apply bg-outline-variant rounded-full; }
        }
    </style>
</head>

<body>

    <div class="flex h-screen overflow-hidden">

        <nav class="fixed inset-x-0 top-0 z-50 h-[64px] bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm">
            <div class="mx-auto flex h-full max-w-[1280px] items-center justify-between px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="/dashboard" class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="/dashboard" class="px-3 py-2 rounded-md text-[14px] font-medium text-on-surface-variant hover:text-primary transition">Dashboard</a>
                        <a href="/itinerary/dashboard/<?= htmlspecialchars($itinerary['id']) ?>" class="px-3 py-2 rounded-md text-[14px] font-medium text-on-surface-variant hover:text-primary transition">Itinerary</a>
                        <a href="#" class="px-3 py-2 rounded-md text-[14px] font-medium text-primary border-b-2 border-primary">Polls</a>
                        <a href="/finance/dashboard/<?= htmlspecialchars($itinerary['id']) ?>" class="px-3 py-2 rounded-md text-[14px] font-medium text-on-surface-variant hover:text-primary transition">Finances</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="inline-flex items-center gap-1 rounded-lg border-2 border-error px-3 py-1.5 text-body-xs font-bold tracking-wide text-error hover:bg-error-container transition">
                        <span class="material-symbols-outlined text-base">warning</span>SOS
                    </button>
                    <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                            <?php
                            $currentUser = \App\Helpers\Auth::user();
                            echo $currentUser ? strtoupper(substr($currentUser->getFirstName(), 0, 1) . substr($currentUser->getLastName(), 0, 1)) : 'ME';
                            ?>
                        </div>
                    </a>
                </div>
            </div>
        </nav>

        <main class="flex-1 mt-[64px] h-[calc(100vh-64px)] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
            <div class="max-w-[1280px] mx-auto">

                <header class="mb-10 flex justify-between items-end">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-primary-fixed px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-primary mb-3">Polls & Voting</span>
                        <h1 class="font-display text-[36px] font-bold text-on-surface mb-2"><?= htmlspecialchars($itinerary['title']) ?></h1>
                        <p class="text-[18px] text-on-surface-variant">Help the group decide on activities and itinerary details.</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="/itinerary/dashboard/<?= htmlspecialchars($itinerary['id']) ?>" class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-[14px] px-6 py-2.5 hover:bg-surface-container transition">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back to Itinerary
                        </a>
                    </div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- ACTIVE POLLS SECTION -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                            <span class="material-symbols-outlined text-primary">ballot</span>
                            <h2 class="font-display text-[28px] font-semibold text-on-surface m-0">Active Polls</h2>
                        </div>

                        <?php if (empty($activePolls)): ?>
                            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-12 text-center">
                                <span class="material-symbols-outlined text-[48px] text-outline mb-4">how_to_vote</span>
                                <h3 class="font-display text-[20px] font-semibold text-on-surface mb-2">No active polls</h3>
                                <p class="text-[16px] text-on-surface-variant">Active polls for this trip will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 gap-6">
                                <?php foreach ($activePolls as $poll): ?>
                                    <article class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <span class="text-[12px] font-bold uppercase tracking-wider text-outline mb-1 block">Activity Poll</span>
                                                <h3 class="font-display text-[20px] font-semibold text-on-surface"><?= htmlspecialchars($poll['activityName']) ?></h3>
                                            </div>
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-secondary-fixed px-3 py-1 text-[11px] font-bold text-secondary">
                                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                                    Ends: <?= date('M j, g:i A', strtotime($poll['deadline'])) ?>
                                                </span>

                                                <?php if ($canManagePolls): ?>
                                                    <form action="/poll/close-early" method="POST" class="mt-1">
                                                        <input type="hidden" name="pollId" value="<?= $poll['id'] ?>">
                                                        <input type="hidden" name="itineraryId" value="<?= $itinerary['id'] ?>">
                                                        <button type="submit" class="text-[12px] text-error hover:underline flex items-center gap-1 font-semibold" onclick="return confirm('Are you sure you want to close this poll early? No one else will be able to vote.')">
                                                            <span class="material-symbols-outlined text-[14px]">cancel</span> Close Early
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <div class="mt-2 text-[14px] font-bold text-primary">Current Points: <?= number_format($poll['weightedTotal'], 1) ?></div>
                                            </div>
                                        </div>

                                        <form action="/poll/vote" method="POST" class="mt-6 border-t border-outline-variant pt-6">
                                            <input type="hidden" name="pollId" value="<?= $poll['id'] ?>">
                                            <input type="hidden" name="itineraryId" value="<?= $itinerary['id'] ?>">

                                            <div class="grid grid-cols-3 gap-3">
                                                <?php foreach ($ratingChoices as $choice): ?>
                                                    <label class="relative flex flex-col items-center justify-center p-4 border-2 rounded-xl cursor-pointer hover:bg-surface-container transition group">
                                                        <input type="radio" name="ratingChoiceId" value="<?= $choice['id'] ?>" class="peer sr-only">
                                                        <div class="w-full h-full absolute inset-0 border-primary rounded-xl opacity-0 peer-checked:opacity-100 peer-checked:border-4 transition-all"></div>

                                                        <span class="material-symbols-outlined mb-2 text-outline group-hover:text-primary transition">
                                                            <?php
                                                            if ($choice['value'] === 'MUST_HAVE') echo 'star';
                                                            elseif ($choice['value'] === 'NICE_TO_HAVE') echo 'thumb_up';
                                                            else echo 'block';
                                                            ?>
                                                        </span>
                                                        <span class="font-display text-[14px] font-bold text-on-surface-variant group-hover:text-primary transition text-center"><?= htmlspecialchars($choice['label']) ?></span>
                                                        <span class="text-[10px] font-bold text-outline mt-1"><?= ($choice['id'] == 1 ? '+3' : ($choice['id'] == 2 ? '+1' : '-1')) ?> pts</span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="mt-6 flex justify-end">
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-bold text-[14px] px-8 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                                                    Submit Vote
                                                </button>
                                            </div>
                                        </form>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- SIDEBAR SECTION -->
                    <div class="space-y-8">

                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
                            <h3 class="font-display text-[20px] font-semibold text-on-surface mb-4">How it works</h3>
                            <ul class="space-y-4 text-[14px] text-on-surface-variant">
                                <li class="flex gap-3">
                                    <span class="material-symbols-outlined text-primary text-[20px]">star</span>
                                    <span><strong>Must Have (3 pts):</strong> Critical activities that define the trip.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="material-symbols-outlined text-tertiary text-[20px]">thumb_up</span>
                                    <span><strong>Nice to Have (1 pt):</strong> Great additions if time and budget permit.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="material-symbols-outlined text-error text-[20px]">block</span>
                                    <span><strong>Not Needed (-1 pt):</strong> Activities you'd prefer to skip.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- CLOSED POLLS SECTION -->
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
                            <h3 class="font-display text-[20px] font-semibold text-on-surface mb-4 flex items-center gap-2 border-b border-outline-variant pb-3">
                                <span class="material-symbols-outlined text-outline">history</span>
                                Recently Closed
                            </h3>
                            <div class="space-y-4">
                                <?php if (empty($closedPolls)): ?>
                                    <p class="text-[14px] text-on-surface-variant italic">No recently closed polls.</p>
                                <?php else: ?>
                                    <?php foreach ($closedPolls as $closedPoll): ?>
                                        <div class="p-3 bg-surface-container rounded-lg border border-outline-variant/50">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="font-semibold text-on-surface text-[15px]"><?= htmlspecialchars($closedPoll['activityName']) ?></h4>
                                                <span class="inline-flex items-center gap-1 rounded bg-surface-container-highest px-1.5 py-0.5 text-[10px] font-bold uppercase text-outline">Closed</span>
                                            </div>
                                            <div class="flex justify-between items-end mt-2">
                                                <div class="flex flex-col">
                                                    <span class="text-[12px] text-on-surface-variant">Ended: <?= date('M j', strtotime($closedPoll['deadline'])) ?></span>

                                                    <!-- REOPEN BUTTON (Editors/Leaders only) -->
                                                    <?php if ($canManagePolls): ?>
                                                        <button type="button" onclick="openReopenModal(<?= $closedPoll['id'] ?>)" class="text-[12px] text-primary hover:underline flex items-center gap-1 mt-1 font-semibold">
                                                            <span class="material-symbols-outlined text-[14px]">update</span> Reopen Poll
                                                        </button>
                                                    <?php endif; ?>

                                                </div>
                                                <span class="font-bold text-primary text-[14px]">Total: <?= number_format($closedPoll['weightedTotal'], 1) ?> pts</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Reopen Poll Modal -->
    <div id="reopenPollModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-on-background/50 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-lg border border-outline-variant overflow-hidden">
            <div class="flex items-center justify-between border-b border-outline-variant px-6 py-4 bg-surface">
                <h3 class="font-display text-[20px] font-semibold text-on-surface m-0 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">update</span> Reopen Poll
                </h3>
                <button type="button" onclick="closeReopenModal()" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-6">
                <form action="/poll/reopen" method="POST" class="flex flex-col gap-5">
                    <input type="hidden" name="pollId" id="reopenPollId" value="">
                    <input type="hidden" name="itineraryId" value="<?= htmlspecialchars($itinerary['id']) ?>">

                    <div>
                        <label class="block text-[12px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">New Deadline</label>
                        <input type="datetime-local" name="newDeadline" required
                            class="w-full rounded-md border border-outline-variant bg-surface px-3 py-2 text-[14px] focus:border-primary focus:ring-primary focus:outline-none transition">
                    </div>

                    <div class="mt-2 flex justify-end gap-3">
                        <button type="button" onclick="closeReopenModal()" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface font-semibold text-[14px] hover:bg-surface-container transition">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-[14px] px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                            Reopen Poll
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openReopenModal(pollId) {
            document.getElementById('reopenPollId').value = pollId;
            document.getElementById('reopenPollModal').classList.remove('hidden');
        }

        function closeReopenModal() {
            document.getElementById('reopenPollModal').classList.add('hidden');
        }
    </script>

</body>

</html>