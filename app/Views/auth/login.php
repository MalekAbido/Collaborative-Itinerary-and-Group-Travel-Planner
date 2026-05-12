<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <?php use App\Enums\TripMemberRole; ?>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Itinerary | Login</title>

    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/vendor/notyf/notyf.min.css">
    <script src="/assets/vendor/notyf/notyf.min.js"></script>

</head>

<body
    class="bg-[#f65a411c] text-on-background font-body min-h-screen flex items-center justify-center p-4 md:p-8 animate-fade-in">

    <div
        class="w-full max-w-250 bg-surface-container-lowest rounded-3xl shadow-xl flex flex-col lg:flex-row overflow-hidden min-h-150">

        <div class="hidden lg:block lg:w-1/2 relative bg-surface-variant">
            <img src="/assets/images/log6.jpeg" alt="Travel Planning"
                class="absolute inset-0 w-full h-full object-cover" />
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

            <div class="absolute bottom-12 left-12 right-12 text-white">
                <span class="material-symbols-outlined text-4xl text-tertiary-fixed mb-4">explore</span>
                <h3 class="font-display text-[26px] font-bold leading-tight mb-4">
                    The ultimate tool to sync up your group trips without the headache.
                </h3>
            </div>
        </div>


        <div class="w-full lg:w-1/2 p-8 md:p-14 flex flex-col justify-center">

            <div class="flex items-center gap-2 mb-10">
                <span class="material-symbols-outlined text-primary text-3xl">flight_takeoff</span>
                <span class="text-xl font-display font-extrabold text-on-surface tracking-tight">Itinerary</span>
            </div>

            <h2 class="font-display text-3xl font-bold text-on-surface mb-2">Welcome back</h2>
            <p class="font-body text-on-surface-variant mb-8">Please enter your details to login.</p>

            <form id="login-form" action="/login/process" method="POST" class="flex flex-col gap-5" novalidate>
                <div>
                    <label
                        class="block text-[12px] font-bold tracking-widest text-on-surface-variant uppercase mb-2">Email
                        Address</label>
                    <input type="email" name="email" placeholder="hello@example.com" required
                        class="w-full px-4 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                </div>

                <div>
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

                <div class="flex items-center -mt-1 mb-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember_me"
                            class="w-4 h-4 text-primary bg-surface border-outline-variant rounded focus:ring-primary focus:ring-2 cursor-pointer">
                        <span class="text-sm text-on-surface-variant font-medium">Remember me</span>
                    </label>
                </div>

                <button class="w-full bg-primary text-white font-semibold py-4 rounded-xl shadow-lg hover:bg-on-primary-fixed-variant transition-colors flex justify-center items-center gap-2 cursor-pointer"    type="submit"
                    >
                    Login
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </form>

            <div class="mt-8 flex flex-col gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-px flex-1 bg-outline-variant"></div>
                    <span class="text-[11px] font-bold tracking-[0.2em] text-on-surface-variant uppercase">Test Accounts</span>
                    <div class="h-px flex-1 bg-outline-variant"></div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <button class="cursor-pointer flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-primary/5 hover:bg-primary/10 border border-primary/10 transition-colors group"    type="button" onclick="testLogin('<?= TripMemberRole::ORGANIZER->value ?>')"
                         >
                        <span class="material-symbols-outlined text-primary text-xl group-hover:scale-110 transition-transform">shield_person</span>
                        <span class="text-[11px] font-bold text-primary">Organizer</span>
                    </button>
                    <button class="cursor-pointer flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-tertiary/5 hover:bg-tertiary/10 border border-tertiary/10 transition-colors group"    type="button" onclick="testLogin('<?= TripMemberRole::EDITOR->value ?>')"
                         >
                        <span class="material-symbols-outlined text-tertiary text-xl group-hover:scale-110 transition-transform">edit_square</span>
                        <span class="text-[11px] font-bold text-tertiary">Editor</span>
                    </button>
                    <button class="cursor-pointer flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-on-surface-variant/5 hover:bg-on-surface-variant/10 border border-on-surface-variant/10 transition-colors group"    type="button" onclick="testLogin('<?= TripMemberRole::MEMBER->value ?>')"
                         >
                        <span class="material-symbols-outlined text-on-surface-variant text-xl group-hover:scale-110 transition-transform">person</span>
                        <span class="text-[11px] font-bold text-on-surface-variant">Member</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-sm text-on-surface-variant mt-8">
                Don't have an account?
                <a class="font-bold text-primary hover:underline ml-1 cursor-pointer"    href="/register" >Register now</a>
            </p>
        </div>

    </div>
</body>


<script src="/assets/js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'bottom' },
            dismissible: true
        });

        <?php use App\Services\Session; ?>

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

</html>