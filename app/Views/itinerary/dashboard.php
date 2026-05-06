<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <?php $trip = $data['trip'] ?? []; ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($trip['title']) ?> - VoyageSync</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet" />

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
                fontSize: {
                    "display": ["48px", {
                        lineHeight: "1.2",
                        letterSpacing: "-0.02em",
                        fontWeight: "800"
                    }],
                    "h1": ["36px", {
                        lineHeight: "1.2",
                        letterSpacing: "-0.02em",
                        fontWeight: "700"
                    }],
                    "h2": ["28px", {
                        lineHeight: "1.3",
                        fontWeight: "600"
                    }],
                    "h3": ["20px", {
                        lineHeight: "1.4",
                        fontWeight: "600"
                    }],
                    "h4": ["17px", {
                        lineHeight: "1.4",
                        fontWeight: "600"
                    }],
                    "body-lg": ["18px", {
                        lineHeight: "1.6",
                        fontWeight: "400"
                    }],
                    "body-md": ["16px", {
                        lineHeight: "1.5",
                        fontWeight: "400"
                    }],
                    "body-sm": ["14px", {
                        lineHeight: "1.5",
                        fontWeight: "400"
                    }],
                    "body-xs": ["13px", {
                        lineHeight: "1.4",
                        fontWeight: "400"
                    }],
                    "label-caps": ["12px", {
                        lineHeight: "1",
                        letterSpacing: "0.05em",
                        fontWeight: "700"
                    }],
                    "label-xs": ["11px", {
                        lineHeight: "1",
                        letterSpacing: "0.05em",
                        fontWeight: "700"
                    }],
                    "micro": ["10px", {
                        lineHeight: "1",
                        letterSpacing: "0.08em",
                        fontWeight: "500"
                    }],
                },
                spacing: {
                    "navbar": "64px"
                }
            }
        }
    };
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body { height: 100%; }
            body { @apply font-body text-body-md bg-background text-on-background overflow-hidden m-0; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
            .scroll-thin::-webkit-scrollbar { width: 6px; }
            .scroll-thin::-webkit-scrollbar-track { background: transparent; }
            .scroll-thin::-webkit-scrollbar-thumb { @apply bg-outline-variant rounded-full; }
        }
    </style>
</head>

<body>

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
                        <a href="/itinerary/polls/<?= htmlspecialchars($data['trip']['id']) ?>"
                            class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary transition">Polls</a>
                        <a href="/finance/dashboard/<?= htmlspecialchars($data['trip']['id']) ?>"
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

        <main
            class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
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
                        <a href="/itinerary/settings/<?= htmlspecialchars($data['trip']['id']) ?>"
                            class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-body-sm px-6 py-2.5 hover:bg-surface-container transition">
                            <span class="material-symbols-outlined text-[18px]">settings</span> Settings
                        </a>
                        <a href="/itinerary/<?= htmlspecialchars($data['trip']['id']) ?>/activity/create"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                            <span class="material-symbols-outlined text-[18px]">add</span> Add Activity
                        </a>
                    </div>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-label-caps uppercase text-outline">Trip Description</span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-tertiary-fixed"><span
                                    class="material-symbols-outlined text-[18px] text-tertiary">description</span></div>
                        </div>
                        <div class="text-body-md text-on-surface font-medium">
                            <?= htmlspecialchars($data['trip']['description'] ?: 'No description provided.') ?></div>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-label-caps uppercase text-outline">Group Balance</span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary-fixed"><span
                                    class="material-symbols-outlined text-[18px] text-secondary">account_balance_wallet</span>
                            </div>
                        </div>
                        <div class="font-display text-[28px] font-extrabold text-on-surface">EGP 0</div>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-label-caps uppercase text-outline">Members</span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-fixed"><span
                                    class="material-symbols-outlined text-[18px] text-primary">group</span></div>
                        </div>
                        <div class="font-display text-[28px] font-extrabold text-on-surface">1</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                            <span class="material-symbols-outlined text-primary">map</span>
                            <h2 class="font-display text-h2 text-on-surface m-0">Itinerary Timeline</h2>
                        </div>

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
                    </div>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-outline-variant">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">group</span>
                                    <h2 class="font-display text-h3 text-on-surface m-0">Members</h2>
                                </div>
                                <a href="/itinerary/members/<?= htmlspecialchars($trip['id'] ?? $data['trip']['id'] ?? '') ?>"
                                    class="text-body-sm font-semibold text-primary hover:underline">Manage</a>
                            </div>
                            <div
                                class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary text-sm font-semibold">
                                        ME</div>
                                    <div>
                                        <div class="font-display text-h4 text-on-surface">You</div>
                                        <span
                                            class="inline-flex items-center rounded-full bg-primary-fixed px-2 py-0.5 mt-1 text-[10px] font-bold uppercase text-primary">👑
                                            Leader</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>

</html>