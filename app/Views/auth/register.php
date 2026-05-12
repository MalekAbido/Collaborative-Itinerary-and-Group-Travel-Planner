<?php 
use App\Services\Session;
$websiteName = 'VoyageSync';?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= $websiteName ?> | Register</title>

    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/notyf.min.css">
    <script src="/assets/js/notyf.min.js"></script>

</head>

<body class="bg-[#f65a411c] text-on-background font-body min-h-screen flex flex-col p-4 py-12 md:p-8 animate-fade-in">

    <div
        class="m-auto w-full max-w-250 bg-surface-container-lowest rounded-3xl shadow-xl flex flex-col lg:flex-row overflow-hidden min-h-150">

        <div class="hidden lg:block lg:w-1/2 relative bg-surface-variant">
            <img src="/assets/images/log5.jpeg" alt="Travel Planning"
                class="absolute inset-0 w-full h-full object-cover" />
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

            <div class="absolute bottom-12 left-12 right-12 text-white">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-4xl text-primary-fixed">format_quote</span>
                </div>
                <h3 class="font-display text-[26px] font-bold leading-tight mb-4 text-white">
                    "<?= $websiteName ?> made planning our summer trip an absolute breeze. Highly recommended!"
                </h3>
                <p class="font-body text-[15px] text-white/80 font-medium">Hana AbdelHamid</p>
                <p class="font-body text-[13px] text-white/60">Group Organizer</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 p-8 md:p-14 flex flex-col justify-center">

            <div class="flex items-center gap-2 mb-8">
                <span class="material-symbols-outlined text-primary text-3xl">flight_takeoff</span>
                <span class="text-xl font-display font-extrabold text-on-surface tracking-tight"><?= $websiteName ?></span>
            </div>

            <h2 class="font-display text-3xl font-bold text-on-surface mb-2">Create an account</h2>
            <p class="font-body text-on-surface-variant mb-8">Join us to start planning your next adventure.</p>

            <form id="register-form" action="/register/process" method="POST" class="flex flex-col gap-5" novalidate>

                <div class="flex flex-col md:flex-row gap-5">
                    <div class="flex-1">
                        <label
                            class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">First
                            Name</label>
                        <input type="text" name="first_name" placeholder="John" required
                            class="w-full px-4 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                    <div class="flex-1">
                        <label
                            class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">Last
                            Name</label>
                        <input type="text" name="last_name" placeholder="Doe" required
                            class="w-full px-4 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label
                        class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">Email
                        Address</label>
                    <input type="email" name="email" placeholder="hello@example.com" required
                        class="w-full px-4 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                </div>

                <div class="flex flex-col md:flex-row gap-5">
                    <div class="flex-1">
                        <label
                            class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="reg-password" name="password" placeholder="••••••••" required
                                class="w-full pl-4 pr-12 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors flex items-center justify-center cursor-pointer"    type="button" onclick="toggleVisibility('reg-password', 'reg-eye')"
                                >
                                <span id="reg-eye" class="material-symbols-outlined">visibility_off</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label
                            class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">Confirm</label>
                        <div class="relative">
                            <input type="password" id="reg-confirm" name="confirm_password" placeholder="••••••••"
                                required
                                class="w-full pl-4 pr-12 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors flex items-center justify-center cursor-pointer"    type="button" onclick="toggleVisibility('reg-confirm', 'confirm-eye')"
                                >
                                <span id="confirm-eye" class="material-symbols-outlined">visibility_off</span>
                            </button>
                        </div>
                    </div>
                </div>

                <button class="w-full bg-primary text-white font-semibold py-4 rounded-xl shadow-lg hover:bg-on-primary-fixed-variant transition-colors mt-2 flex justify-center items-center gap-2 cursor-pointer"    type="submit"
                    >
                    Register
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </button>
            </form>

            <p class="text-center text-sm text-on-surface-variant mt-8">
                Already have an account?
                <a class="font-bold text-primary hover:underline ml-1 cursor-pointer"    href="/login" >Login here</a>
            </p>
        </div>

    </div>


    <script src="/assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notyf = new Notyf({
                duration: 4000,
                position: { x: 'right', y: 'bottom' },
                dismissible: true
            });

            <?php if (Session::hasFlash('success')): ?>
                notyf.success("<?= addslashes(Session::getFlash('success')) ?>");
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
                notyf.error("<?= addslashes(Session::getFlash('error')) ?>");
            <?php endif; ?>

            <?php if (Session::hasFlash('info')): ?>
                notyf.open({
                    type: 'info',
                    message: "<?= addslashes(Session::getFlash('info')) ?>",
                    background: '#3b82f6',
                    icon: { className: 'material-symbols-outlined', tagName: 'i', text: 'info' }
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>