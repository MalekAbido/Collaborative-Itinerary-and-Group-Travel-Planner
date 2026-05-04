<?php
/**
 * @var array $myTrips
 */
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Trips | Itinerary</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <!-- Link to your compiled Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/tailwind.css">

    <style>
        /* Fallback for custom scrollbar */
        .scroll-thin::-webkit-scrollbar { width: 6px; }
        .scroll-thin::-webkit-scrollbar-track { background: transparent; }
        .scroll-thin::-webkit-scrollbar-thumb { background-color: #c1c6d6; border-radius: 9999px; }
    </style>
</head>

<body class="bg-background text-on-background font-body text-body-md m-0 overflow-hidden">

<div class="flex h-screen overflow-hidden">

    <!-- NAVBAR WITH EMBEDDED NAVIGATION -->
    <nav class="fixed inset-x-0 top-0 z-50 h-navbar bg-surface-container-lowest/90 backdrop-blur-md border-b border-outline-variant shadow-sm">
        <div class="mx-auto flex h-full max-w-content items-center justify-between px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <a href="/dashboard" class="font-display text-[22px] font-extrabold tracking-tight text-primary whitespace-nowrap">
                    Itinerary
                </a>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center gap-2">
                    <a href="/dashboard" class="px-3 py-2 rounded-md text-body-sm font-medium text-primary border-b-2 border-primary">
                        Dashboard
                    </a>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container transition">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                </button>
                <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                        <?= isset($user) ? strtoupper(substr($user->getFirstName(), 0, 1) . substr($user->getLastName(), 0, 1)) : 'ME' ?>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT (Sidebar margin removed) -->
    <main class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
        <div class="max-w-content mx-auto">

            <!-- Page Hero -->
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-display text-display text-on-surface mb-3">Dashboard</h1>
                    <p class="text-body-lg text-on-surface-variant max-w-[640px]">
                        Manage your upcoming itineraries and past adventures.
                    </p>
                </div>
                    <a href="/itinerary/create" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-4 shadow-sm hover:bg-on-primary-fixed-variant transition shrink-0">
                        <span class="material-symbols-outlined text-[20px]">add</span> Create Trip
                    </a>
            </header>

            <!-- Trips Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($myTrips)): ?>
                    <?php foreach ($myTrips as $trip): ?>
                        <article class="flex flex-col bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                            <div class="relative h-40 bg-gradient-primary">
                                <span class="material-symbols-outlined absolute bottom-3 left-3 text-5xl text-on-primary/30">travel_explore</span>
                                
                                <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-surface-container-lowest/20 backdrop-blur px-3 py-1 text-label-xs font-bold uppercase text-on-primary">
                                    <?= htmlspecialchars($trip['role']) ?>
                                </span>
                            </div>
                            
                            <div class="p-5 border-l-4 border-primary flex-1 flex flex-col">
                                <h4 class="font-display text-h4 text-on-surface mb-1"><?= htmlspecialchars($trip['title']) ?></h4>
                                
                                <p class="text-body-xs text-on-surface-variant mb-3">
                                    <?= date('M j', strtotime($trip['startDate'])) ?> – <?= date('M j, Y', strtotime($trip['endDate'])) ?>
                                </p>
                                
                                <p class="text-body-sm text-on-surface-variant mb-4 line-clamp-2">
                                    <?= htmlspecialchars($trip['description']) ?>
                                </p>
                                
                                <a href="/itinerary/dashboard/<?= htmlspecialchars($trip['itineraryId']) ?>" class="mt-auto inline-flex items-center gap-1 text-body-sm font-semibold text-primary hover:underline group">
                                    View Dashboard <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="col-span-full flex flex-col items-center justify-center py-20 border-2 border-dashed border-outline-variant rounded-xl bg-surface-container-lowest">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-container mb-4">
                            <span class="material-symbols-outlined text-[32px] text-outline">flight_takeoff</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-1">No trips yet</h3>
                        <p class="text-body-md text-on-surface-variant mb-4">You aren't a member of any itineraries.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <footer class="mt-12 pt-8 pb-4 text-center text-body-xs text-outline border-t border-outline-variant">
                © <?= date('Y') ?> Itinerary. All rights reserved.
            </footer>

        </div>
    </main>

</div>

<script src="/assets/js/user.js"></script>
</body>
</html>