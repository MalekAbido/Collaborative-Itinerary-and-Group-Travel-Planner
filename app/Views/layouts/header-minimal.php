<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <?php
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
    
    <style type="text/tailwindcss">
        @layer base {
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
        }
    </style>
</head>

<body class="bg-background text-on-background font-body text-body-md min-h-screen flex flex-col m-0 overflow-x-hidden overflow-y-auto">
    <header
        class="fixed top-0 left-0 w-full h-navbar z-50 flex items-center justify-between px-6 lg:px-8 bg-surface-container-lowest/90 backdrop-blur-md border-b border-outline-variant shadow-sm">
        <a href="/" class="font-display text-[22px] font-extrabold tracking-tight text-primary"><?php echo $websiteName; ?></a>
        <div class="flex items-center gap-4">
            <a href="/login"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                Log In
            </a>
            <a href="/register"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                Register
            </a>
        </div>
    </header>
