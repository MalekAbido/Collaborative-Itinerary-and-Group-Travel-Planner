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
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;900&amp;family=Inter:wght@400;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "secondary-fixed-dim": "#ffb4a7",
                    "on-error": "#ffffff",
                    "inverse-surface": "#2f3131",
                    "on-secondary-container": "#71160b",
                    "tertiary-container": "#367f85",
                    "on-primary-fixed": "#3f0200",
                    "on-error-container": "#93000a",
                    "surface-tint": "#b32a17",
                    "on-primary-container": "#fffbff",
                    "on-secondary": "#ffffff",
                    "surface-container-high": "#e8e8e8",
                    "secondary-container": "#fd7d69",
                    "secondary-fixed": "#ffdad4",
                    "inverse-primary": "#ffb4a6",
                    "surface-container-lowest": "#ffffff",
                    "on-secondary-fixed": "#400100",
                    "on-tertiary-fixed-variant": "#004f54",
                    "primary-container": "#d2402a",
                    "on-tertiary": "#ffffff",
                    "primary-fixed": "#ffdad4",
                    "on-tertiary-container": "#f5feff",
                    "surface-bright": "#f9f9f9",
                    "secondary": "#a43b2c",
                    "on-tertiary-fixed": "#002022",
                    "on-primary-fixed-variant": "#900e00",
                    "background": "#f9f9f9",
                    "surface": "#f9f9f9",
                    "on-surface-variant": "#5a413c",
                    "on-secondary-fixed-variant": "#842417",
                    "on-background": "#1a1c1c",
                    "surface-container-low": "#f3f3f4",
                    "primary-fixed-dim": "#ffb4a6",
                    "error": "#ba1a1a",
                    "surface-dim": "#dadada",
                    "outline-variant": "#e2beb8",
                    "tertiary": "#14666c",
                    "tertiary-fixed-dim": "#8cd2d9",
                    "surface-container": "#eeeeee",
                    "error-container": "#ffdad6",
                    "surface-container-highest": "#e2e2e2",
                    "inverse-on-surface": "#f0f1f1",
                    "tertiary-fixed": "#a7eff5",
                    "on-surface": "#1a1c1c",
                    "surface-variant": "#e2e2e2",
                    "primary": "#af2714",
                    "outline": "#8e706b",
                    "on-primary": "#ffffff"
                },
                "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
                "spacing": {
                    "xs": "4px",
                    "gutter": "24px",
                    "xl": "80px",
                    "sm": "12px",
                    "lg": "48px",
                    "base": "8px",
                    "container-max": "1280px",
                    "md": "24px"
                },
                "fontFamily": {
                    "body-sm": ["Inter"],
                    "label-caps": ["Inter"],
                    "h3": ["Plus Jakarta Sans"],
                    "h1": ["Plus Jakarta Sans"],
                    "body-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "h2": ["Plus Jakarta Sans"],
                    "button": ["Inter"]
                },
                "fontSize": {
                    "body-sm": ["14px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "label-caps": ["12px", {
                        "lineHeight": "1",
                        "letterSpacing": "0.05em",
                        "fontWeight": "700"
                    }],
                    "h3": ["20px", {
                        "lineHeight": "1.4",
                        "fontWeight": "600"
                    }],
                    "h1": ["36px", {
                        "lineHeight": "1.2",
                        "letterSpacing": "-0.02em",
                        "fontWeight": "700"
                    }],
                    "body-md": ["16px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "body-lg": ["18px", {
                        "lineHeight": "1.6",
                        "fontWeight": "400"
                    }],
                    "h2": ["28px", {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }],
                    "button": ["14px", {
                        "lineHeight": "1",
                        "fontWeight": "600"
                    }]
                }
            }
        }
    }
    </script>
</head>

<body class="bg-background text-on-background font-body-md h-screen overflow-hidden flex flex-col">
    <!-- TopNavBar -->
    <header
        class="bg-white dark:bg-zinc-950 font-['Plus_Jakarta_Sans'] antialiased docked full-width top-0 z-50 border-b border-zinc-100 dark:border-zinc-800 shadow-sm dark:shadow-none hidden md:block">
        <div class="flex justify-between items-center w-full px-6 py-3 max-w-[1280px] mx-auto">
            <div class="flex items-center gap-6">
                <div class="text-2xl font-black tracking-tight text-orange-600 dark:text-orange-500">VoyageSync</div>
                <nav class="flex items-center gap-4">
                    <a class="text-orange-600 dark:text-orange-400 font-bold border-b-2 border-orange-600 pb-1"
                        href="#">Itinerary</a>
                    <a class="text-zinc-500 dark:text-zinc-400 font-medium hover:text-orange-600 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors duration-200 Active: opacity-80 transition-opacity px-3 py-1 rounded"
                        href="#">Leaderboard</a>
                    <a class="text-zinc-500 dark:text-zinc-400 font-medium hover:text-orange-600 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors duration-200 Active: opacity-80 transition-opacity px-3 py-1 rounded"
                        href="#">Members</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-64">
                    <span
                        class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant"
                        data-icon="search">search</span>
                    <input
                        class="w-full bg-surface-container-low text-on-surface border border-surface-variant rounded-full py-2 pl-4 pr-10 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-body-sm font-body-sm"
                        placeholder="Search..." type="text" />
                </div>
                <button
                    class="text-zinc-500 hover:text-orange-600 transition-colors duration-200 p-2 rounded-full hover:bg-zinc-50">
                    <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                </button>
                <button
                    class="text-zinc-500 hover:text-orange-600 transition-colors duration-200 p-2 rounded-full hover:bg-zinc-50">
                    <span class="material-symbols-outlined" data-icon="settings">settings</span>
                </button>
                <img alt="User profile photo"
                    class="w-8 h-8 rounded-full border-2 border-surface-container-highest cursor-pointer"
                    data-alt="A small circular profile picture of a young professional man with short dark hair, wearing a casual grey shirt, smiling warmly against a blurred, light background. The style is modern, bright, and professional, fitting a corporate travel app UI."
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCqO1N54yLVUPs-ZhPND_95OONb1qUcd69U9ynSKYF5R4X7V1Jtg8DDRzTuH7uyxrTEulPzv7qSh3pFh_rSlt9O8m3OtmJUjft9Eb0XCyAZoK8DNpYHBJqSJhPLjsyyILrSzNd4ZeQ3csLg6qhDMuLPw8z2xqNR9BrVX5w76TQNOb6jZRVbq6ev9LapJhE-1kyx8X_qwUNq76jrf9-DwnfY08FKJTmVgfre1wT1R4xpx04TnJklx8TzE5S6f8C-QOuhQRhdpJ_pVLs" />
            </div>
        </div>
    </header>
    <div class="flex flex-1 overflow-hidden">

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-surface-container-low p-gutter">
            <div class="max-w-[1280px] mx-auto grid grid-cols-12 gap-gutter">
                <!-- Activity Header & Detail Column -->
                <div class="col-span-12 lg:col-span-8 flex flex-col gap-gutter">
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
                        class="bg-surface rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant p-6">
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
                <div class="col-span-12 lg:col-span-4 flex flex-col gap-gutter">
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