<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <?php
    use App\Services\Auth;
    $websiteName = 'NoVoyageSync';
    $pageTitle = $pageTitle ?? $websiteName;
    ?>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
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
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
        }
    </style>
</head>

<body class="bg-background text-on-background font-body text-body-md min-h-screen flex flex-col m-0 overflow-x-hidden overflow-y-auto">
    <header
        class="fixed top-0 left-0 w-full h-navbar z-50 flex items-center justify-between px-6 lg:px-8 bg-surface-container-lowest/90 backdrop-blur-md border-b border-outline-variant shadow-sm">
        <?php if(Auth::check()): ?>
            <div class="flex items-center gap-4">
                <a class="font-display text-[22px] font-extrabold tracking-tight text-primary cursor-pointer"    href="/" ><?php echo $websiteName; ?></a>
                <a class="flex items-center h-full px-4 text-body-sm transition-all font-medium text-on-surface-variant border-b-2 border-transparent hover:text-primary cursor-pointer"    href="/dashboard"> Dashboard </a>
            </div>
            <a class="flex items-center gap-2 cursor-pointer"    href="/profile" >
                        <?php
                        $currentUser = Auth::user();
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
        <?php else: ?>
            <a class="font-display text-[22px] font-extrabold tracking-tight text-primary cursor-pointer"    href="/" ><?php echo $websiteName; ?></a>
            <div class="flex items-center gap-4">
                <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    href="/login"
                    >
                    Log In
                </a>
                <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    href="/register"
                    >
                    Register
                </a>
            </div>
        <?php endif; ?>
    </header>
