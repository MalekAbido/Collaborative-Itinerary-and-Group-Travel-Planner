<?php

    use App\Helpers\Auth;
    $itineraryId         = $data['itineraryId'];
    $activity            = $data['activity'];
    $userRole            = $data['userRole'];
    $currentMemberId     = $data['currentMemberId'];
    $currentMemberStatus = $data['currentMemberStatus'];
    $attendanceList      = $data['attendanceList'];
    $goingMembers        = $data['goingMembers'];
    $pendingMembers      = $data['pendingMembers'];
    $notGoingMembers     = $data['notGoingMembers'];
    $totalGoing          = $data['totalGoing'];
    $totalMembers        = count($attendanceList->getMembers());
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>VoyageSync - Activity Detail</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;900&amp;family=Inter:wght@400;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/tailwind.css">
</head>

<body class="bg-background text-on-background font-body text-body-md m-0 overflow-hidden">
    <!-- TopNavBar -->

    <div class="flex h-screen overflow-hidden">
        <nav
            class="fixed inset-x-0 top-0 z-50 h-navbar bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm">
            <div class="mx-auto flex h-full max-w-[1280px] items-center justify-between px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="/dashboard"
                        class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="/dashboard"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Dashboard</a>
                        <a href="#"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-primary border-b-2 border-primary">Itinerary</a>
                        <a href="/itinerary/polls/<?= htmlspecialchars($itineraryId) ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Polls</a>
                        <a href="/finance/dashboard/<?= htmlspecialchars($itineraryId) ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Finances</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="inline-flex items-center gap-1 rounded-lg border-2 border-error px-3 py-1.5 text-body-xs font-bold tracking-wide text-error hover:bg-error-container transition">
                        <span class="material-symbols-outlined text-base">warning</span>SOS
                    </button>
                    <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                        <?php $currentUser = \App\Helpers\Auth::user(); ?>
                        <?php if ($currentUser->getProfileImage()): ?>
                        <img src="/<?= htmlspecialchars($currentUser->getProfileImage()) ?>" alt="Profile"
                            class="h-8 w-8 rounded-full border-2 border-outline-variant object-cover">
                        <?php else: ?>
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                            <?= strtoupper(substr($currentUser->getFirstName(), 0, 1) . substr($currentUser->getLastName(), 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main
            class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
            <div class="max-w-[1280px] mx-auto grid grid-cols-12 gap-6">
                <!-- Activity Header & Detail Column -->
                <div class="col-span-12 lg:col-span-8 flex flex-col gap-6">
                    <!-- Hero Card -->
                    <div
                        class="bg-surface rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border-l-4 border-primary overflow-hidden relative">
                        <div class="h-48 w-full bg-cover bg-center relative"
                            data-alt="A breathtaking view of Mount Fuji towering over a serene lake, reflecting perfectly in the still water. The sky is a clear, vibrant blue, characteristic of a crisp autumn morning in Japan. The scene embodies exploration and awe, fitting the 'Structured Exploration' theme of the travel app with high contrast and clean lines."
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAP0GA5BMqfjA9S_J6C8IYH2XP2cYgizm7IlSqBm8LkYC3Li2e4GEPTlgdZP7doWnvRCYr4Qua4WJpmyBzL73w4tVN7itPOZaqG1HsoTedgiOX2M4EzdFloidufze5t_X0YPhXSSv6ix9oadjTtsTZIajspW6FXbEsZLIEQmlfzGz2MvFf1bYwdyyR-8oLlwfFYa6Gh_HMGmhjdju1zQBSZTVunUiBFxSxebwSRXXBnEpEJzaWa88NJoCSYmpCKrleezvRJUkQLJVQ');">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div
                                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface"><?php echo htmlspecialchars($activity->getCategory()) ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h1 class="font-h1 text-h1 text-on-surface mb-2">
                                        <?php echo htmlspecialchars($activity->getName()) ?></h1>
                                    <div
                                        class="flex items-center gap-4 text-on-surface-variant font-body-sm text-body-sm">
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base">calendar_month</span>
                                            <?php echo date('M j, g:i A', strtotime($activity->getStartTime())) ?> -
                                            <?php echo date('g:i A', strtotime($activity->getEndTime())) ?>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base">location_on</span>
                                            <?php echo $activity->getLocationId() ?>

                                        </div>
                                    </div>
                                </div>
                                <!-- RSVP Dropdown -->
                                <div class="relative">
                                    <form
                                        action="/itinerary/<?php echo $itineraryId ?>/activity/<?php echo $activity->getId() ?>/updateAttendance"
                                        method="POST">
                                        <select name="status" onchange="this.form.submit()"
                                            class="bg-none appearance-none bg-surface-container border border-outline-variant text-on-surface font-button text-button py-2 pl-4 pr-10 rounded-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">

                                            <option value="PENDING"
                                                <?php
                                                echo $currentMemberStatus->getStatus() === 'PENDING' ? 'selected' : '' ?>>
                                                Pending
                                            </option>
                                            <option value="GOING"
                                                <?php
                                                echo $currentMemberStatus->getStatus() === 'GOING' ? 'selected' : '' ?>>Going
                                            </option>
                                            <option value="NOT_GOING"
                                                <?php
                                                echo $currentMemberStatus->getStatus() === 'NOT_GOING' ? 'selected' : '' ?>>
                                                Not
                                                Going
                                            </option>

                                        </select>
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                    </form>
                                </div>
                            </div>
                            <div
                                class="prose max-w-none text-on-surface-variant font-body-md text-body-md border-t border-surface-variant pt-4 mt-2">
                                <p><?php echo nl2br(htmlspecialchars($activity->getDescription())) ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Attendees Bento -->
                    <div
                        class="bg-surface rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)]  border border-surface-variant p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-h3 text-h3 text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-tertiary">group</span> Attendees
                            </h2>
                            <span
                                class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-full">
                                <?php echo $totalGoing; ?>
                                / <?php echo $totalMembers; ?></span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-surface-variant">
                                        <th
                                            class="py-2 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                                            Member</th>
                                        <th
                                            class="py-2 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="font-body-sm text-body-sm">
                                    <?php

                                    if ($attendanceList): ?>
                                    <?php

                                    foreach ($attendanceList->getMembers() as $memberStatus): ?>
                                    <?php
                                        $person = $memberStatus->getTripMember()->getUser();
                                    ?>
                                    <tr
                                        class="border-b border-surface-variant/50 hover:bg-surface-bright transition-colors">

                                        <!-- 1. The User's Profile Info -->
                                        <td class="py-3 px-4 flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-surface-variant flex items-center justify-center font-bold text-primary">
                                                <!-- Use their initial as a fallback avatar -->
                                                <?php echo substr($person->getFirstName(), 0, 1) ?>
                                            </div>
                                            <span class="font-semibold text-on-surface">
                                                <?php echo htmlspecialchars($person->getFirstName() . ' ' . $person->getLastName()) ?>

                                                <?php

                                                if ($memberStatus->getTripMemberId() == $currentMemberId): ?>
                                                <span class="text-xs text-gray-400 ml-2">(You)</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-4">
                                            <?php

                                            if ($memberStatus->getStatus() === 'GOING'): ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-tertiary/10 text-tertiary px-3 py-1 rounded-full text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                Going
                                            </span>

                                            <?php elseif ($memberStatus->getStatus() === 'PENDING'): ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-surface-container text-on-surface-variant px-3 py-1 rounded-full text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[14px]">help</span>
                                                Pending
                                            </span>

                                            <?php else: ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-surface-container text-on-surface-variant px-3 py-1 rounded-full text-xs font-semibold opacity-60">
                                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                                Not
                                                Going
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="p-4 text-center">There are no attendants yet.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right Sidebar Column -->
                <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                    <!-- Danger Zone -->
                    <?php

                    if (Auth::hasRole("Editor", $userRole)): ?>
                    <div class="mt-auto pt-6 flex justify-end border-surface-variant">
                        <form
                            action="/itinerary/<?php echo $itineraryId ?>/activity/<?php echo $activity->getId() ?>/delete"
                            method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');">
                            <button type="submit"
                                class="text-error font-button text-button flex items-center gap-1 hover:bg-error-container/50 px-3 py-2 rounded-md transition-colors opacity-70 hover:opacity-100">
                                <span class="material-symbols-outlined text-[16px]">delete</span> Delete Activity
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <!-- BottomNavBar (Mobile Only) -->
    <nav
        class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md font-['Plus_Jakarta_Sans'] text-[10px] font-semibold fixed bottom-0 w-full rounded-t-2xl border-t border-zinc-100 dark:border-zinc-800 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-2">
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="map">map</span>
            Trip
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="rule">rule</span>
            Manage
        </a>
        <a class="flex flex-col items-center justify-center text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30 rounded-xl px-4 py-1 Active: scale-90 transition-transform duration-100"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="inventory_2" data-weight="fill"
                style="font-variation-settings: 'FILL' 1;">inventory_2</span>
            Inventory
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="person">person</span>
            Profile
        </a>
    </nav>
</body>

</html>