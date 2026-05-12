<?php
use App\Services\Auth;
$isLoggedIn = Auth::check();
$activeTab = '404';

if ($isLoggedIn) {
    require_once __DIR__ . '/../layouts/header.php';
} else {
    require_once __DIR__ . '/../layouts/header-minimal.php';
?>
    <main class="grow pt-navbar flex items-center justify-center">
<?php } ?>

    <!-- Main Content Canvas -->
    <div class="max-w-[1280px] w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center px-6 py-12">
        <!-- Illustration Section (Bento Card Style) -->
        <div class="lg:col-span-6 flex justify-center lg:justify-end order-2 lg:order-1">
            <div class="relative bg-surface rounded-xl p-6 shadow-sm border border-outline-variant max-w-md w-full aspect-square flex items-center justify-center overflow-hidden">
                <!-- Stylized Background Decor -->
                <div class="absolute inset-0 opacity-5 pointer-events-none">
                    <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 2px 2px, #af2714 1px, transparent 0); background-size: 24px 24px;"></div>
                </div>
                <!-- Main Illustration -->
                <div class="relative z-10 w-full h-full flex items-center justify-center">
                    <img class="rounded-lg object-cover h-full w-full shadow-lg" alt="Confused traveler illustration" src="/assets/images/404.png" />
                    <!-- Floating Navigation Elements Decor -->
                    <div class="absolute -top-4 -right-4 bg-primary text-on-primary p-3 rounded-lg shadow-lg flex items-center gap-2">
                        <span class="material-symbols-outlined">explore</span>
                        <span class="text-xs font-bold uppercase tracking-wider">SIGNAL LOST</span>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-tertiary text-on-tertiary p-3 rounded-lg shadow-lg flex items-center gap-2">
                        <span class="material-symbols-outlined">wrong_location</span>
                        <span class="text-xs font-bold uppercase tracking-wider">OFF GRID</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Section -->
        <div class="lg:col-span-6 flex flex-col items-center lg:items-start text-center lg:text-left space-y-6 order-1 lg:order-2">
            <h1 class="text-[120px] font-extrabold text-primary leading-none tracking-tighter">404</h1>
            <div class="space-y-2">
                <h2 class="text-4xl font-bold text-on-surface">Destination Not Found</h2>
                <p class="text-lg text-on-surface-variant max-w-md">
                    Looks like this destination isn't on the map. Let's get you back on track before the next itinerary item starts!
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 pt-4 w-full sm:w-auto">
                <!-- Primary Action -->
                <a class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-semibold px-8 py-4 rounded-lg shadow-sm hover:opacity-90 active:scale-95 duration-150 ease-in-out transition-all cursor-pointer"     href="/dashboard">
                    <span class="material-symbols-outlined">dashboard</span>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
