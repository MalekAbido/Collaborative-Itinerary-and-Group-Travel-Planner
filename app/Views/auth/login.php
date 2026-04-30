<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Itinerary | Login</title>

    <link rel="stylesheet" href="/assets/css/tailwind.css">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet" />
</head>

<body class="bg-[#f65a411c] text-on-background font-body min-h-screen flex items-center justify-center p-4 md:p-8">

    <div
        class="w-full max-w-250 bg-surface-container-lowest rounded-3xl shadow-xl flex flex-col lg:flex-row overflow-hidden min-h-150">

        <div class="hidden lg:block lg:w-1/2 relative bg-surface-variant">
            <img src="/assets/images/log1.jpeg" alt="Travel Planning"
                class="absolute inset-0 w-full h-full object-cover" />
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent"></div>

            <div class="absolute bottom-12 left-12 right-12 text-white">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-4xl text-primary-fixed">format_quote</span>
                </div>
                <h3 class="font-display text-[26px] font-bold leading-tight mb-4 text-white">
                    "Itinerary made planning our summer trip an absolute breeze. Highly recommended!"
                </h3>
                <p class="font-body text-[15px] text-white/80 font-medium">Hana AbdelHamid</p>
                <p class="font-body text-[13px] text-white/60">Group Organizer</p>
            </div>
        </div>


        <div class="w-full lg:w-1/2 p-8 md:p-14 flex flex-col justify-center">

            <div class="flex items-center gap-2 mb-10">
                <span class="material-symbols-outlined text-primary text-3xl">flight_takeoff</span>
                <span class="text-xl font-display font-extrabold text-on-surface tracking-tight">Itinerary</span>
            </div>

            <h2 class="font-display text-3xl font-bold text-on-surface mb-2">Welcome back</h2>
            <p class="font-body text-on-surface-variant mb-8">Please enter your details to login.</p>

            <form action="/login/process" method="POST" class="flex flex-col gap-5">
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
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3.5 bg-surface border border-outline-variant rounded-xl text-on-surface placeholder-outline/60 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none font-mono text-lg">
                </div>

                <a type="submit"
                    class="w-full bg-primary text-white font-semibold py-4 rounded-xl shadow-lg hover:bg-on-primary-fixed-variant transition-colors mt-2 flex justify-center items-center gap-2 cursor-pointer">
                    Login
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
            </form>

            <p class="text-center text-sm text-on-surface-variant mt-8">
                Don't have an account?
                <a href="/register" class="font-bold text-primary hover:underline ml-1">Register now</a>
            </p>
        </div>

    </div>
</body>

</html>