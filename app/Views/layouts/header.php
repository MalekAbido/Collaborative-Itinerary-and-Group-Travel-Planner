<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <?php
    use App\Services\Auth;
    use App\Services\Session;
    use App\Models\TripMember;
    use App\Enums\TripMemberRole;
    $currentUser = Auth::user();
    $websiteName = 'VoyageSync';
    $pageTitle = ($activeTab . ' - ' . $websiteName) ?? $websiteName;
    ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

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

<body class="bg-surface overflow-y-auto">
    <div class="flex h-screen overflow-hidden">
        <nav
            class="fixed inset-x-0 top-0 z-50 h-navbar bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm">
            <div class="mx-auto flex h-full max-w-[1280px] items-center justify-between px-6 lg:px-8">
                <div class="flex items-center h-full gap-8">
                    <a class="font-display text-[22px] font-extrabold tracking-tight text-primary cursor-pointer"    href="/"
                        ><?php echo $websiteName; ?></a>

                    <div class="hidden md:flex items-center h-full gap-1">
                        <a class="flex items-center h-full px-4 text-body-sm transition-all <?= (in_array($activeTab ?? '', ['dashboard'])) ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/dashboard"
                             >
                            Dashboard
                        </a>

                        <?php if ($itineraryId ?? false): ?>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= (in_array($activeTab ?? '', ['itinerary', 'activity', 'settings', 'members', 'createActivity'])) ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/itinerary/dashboard/<?= htmlspecialchars($itineraryId) ?>"
                                 >
                                Itinerary
                            </a>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= (in_array($activeTab ?? '', ['polls', 'poll'])) ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/itinerary/polls/<?= htmlspecialchars($itineraryId) ?>"
                                 >
                                Polls
                            </a>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= ($activeTab ?? '') === 'inventory' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/itinerary/inventory/<?= htmlspecialchars($itineraryId) ?>"
                                 >
                                Inventory
                            </a>
                            <?php $currentMember = TripMember::getByUserAndItinerary($currentUser->getId(), $itineraryId); ?>
                            <?php if ($currentMember && Auth::hasRole(TripMemberRole::EDITOR->value, $currentMember->getRole())): ?>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= ($activeTab ?? '') === 'proposals' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/itinerary/<?= htmlspecialchars($itineraryId) ?>/proposals"
                                 >
                                Proposals
                            </a>
                            <?php endif; ?>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= ($activeTab ?? '') === 'finance' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/finance/dashboard/<?= htmlspecialchars($itineraryId) ?>"
                                 >
                                Finances
                            </a>
                            <a class="flex items-center h-full px-4 text-body-sm transition-all <?= ($activeTab ?? '') === 'history' ? 'font-bold text-primary border-b-2 border-primary' : 'font-medium text-on-surface-variant border-transparent hover:text-primary' ?> cursor-pointer"  href="/itinerary/<?= htmlspecialchars($itineraryId) ?>/history"
                                 >
                                History
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <?php if ($itineraryId ?? false): ?>
                        <button class="inline-flex items-center gap-1 rounded-lg border-2 border-error px-3 py-1.5 text-body-xs font-bold tracking-wide text-error hover:bg-error-container transition focus:ring-2 focus:ring-error focus:outline-none cursor-pointer"    id="sos-btn" type="button"
                            >
                            <span class="material-symbols-outlined text-base">warning</span>SOS
                        </button>
                        <script src="/assets/js/triggerEmergency.js"></script>
                    <?php endif; ?>
                    <a class="flex items-center gap-2 cursor-pointer"    href="/profile" >
                        <?php
                        if ($currentUser->getProfileImage()): ?>
                            <img src="/<?php echo htmlspecialchars($currentUser->getProfileImage()) ?>" alt="Profile"
                                class="h-8 w-8 rounded-full border-2 border-outline-variant object-cover">
                        <?php else: ?>
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                                <?php echo strtoupper(substr($currentUser->getFirstName(), 0, 1) . substr($currentUser->getLastName(), 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </nav>


        <main class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface scroll-thin">
            
            <?php if (in_array($activeTab ?? '', ['createActivity', 'createItinerary', 'settings', 'members', 'addExpense', 'expense'])): ?>
                <div class="max-w-[800px] mx-auto w-full p-6 lg:p-10">
            <?php elseif (in_array($activeTab ?? '', ['members'])): ?>
                <div class="max-w-[900px] mx-auto mt-[100px] px-6 pb-12">
            <?php elseif (in_array($activeTab ?? '', ['history'])): ?>
                <div class="max-w-[1280px] mx-auto px-6 lg:px-8 py-8 w-full">
            <?php elseif (in_array($activeTab ?? '', ['activity'])): ?>
                <div class="max-w-[900px] mx-auto px-6 lg:px-8 py-8 w-full">
            <?php else: ?>
                <div class="w-full p-6 lg:p-8">
            <?php endif; ?>

            <div class="mb-6">
                <?php if (in_array($activeTab ?? '', ['activity', 'settings', 'members', 'createActivity'])): ?>
                    <a class="inline-flex items-center gap-1 text-body-sm font-semibold text-on-surface-variant hover:text-primary transition-colors cursor-pointer"  href="/itinerary/dashboard/<?= htmlspecialchars($itineraryId ?? '') ?>" 
                     >
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> 
                        Back to Itinerary Dashboard
                    </a>

                <?php elseif (($activeTab ?? '') === 'poll'): ?>
                    <a class="inline-flex items-center gap-1 text-body-sm font-semibold text-on-surface-variant hover:text-primary transition-colors cursor-pointer"  href="/itinerary/polls/<?= htmlspecialchars($itineraryId ?? '') ?>" 
                     >
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> 
                        Back to Itinerary Polls
                    </a>

                <?php elseif (in_array($activeTab ?? '', ['addExpense', 'expense'])): ?>
                    <a class="inline-flex items-center gap-1 text-body-sm font-semibold text-on-surface-variant hover:text-primary transition-colors cursor-pointer"  href="/finance/dashboard/<?= htmlspecialchars($itineraryId ?? '') ?>" 
                     >
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> 
                        Back to Itinerary Finance
                    </a>
                
                <?php elseif (($activeTab ?? '') === 'createItinerary'): ?>
                    <a class="inline-flex items-center gap-1 text-body-sm font-semibold text-on-surface-variant hover:text-primary transition-colors cursor-pointer"    href="/dashboard" 
                    >
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span> 
                        Back to Dashboard
                    </a>
                    
                <?php endif; ?>
            </div>