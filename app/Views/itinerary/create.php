<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trip - VoyageSync</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f65a41", "primary-container": "#ff8b71", "primary-fixed": "#ffdad3", "on-primary": "#ffffff", "on-primary-fixed-variant": "#7b2a1a",
                        "secondary": "#825500", "secondary-container": "#feaa00", "secondary-fixed": "#ffddb3", "on-secondary": "#ffffff",
                        "success": "#16a34a", "success-container": "#bbf7d0", "on-success": "#ffffff", "on-success-container": "#064e2c",
                        "error": "#ba1a1a", "error-container": "#ffdad6", "on-error": "#ffffff", "on-error-container": "#93000a",
                        "background": "#fcf8f8", "on-background": "#191c1d", "surface": "#fcf8f8",
                        "surface-container-lowest": "#ffffff", "surface-container": "#edeeef", "on-surface": "#191c1d", "on-surface-variant": "#414754",
                        "outline": "#727785", "outline-variant": "#c1c6d6",
                    },
                    fontFamily: { display: ["'Plus Jakarta Sans'", "sans-serif"], body: ["'Inter'", "sans-serif"] },
                    fontSize: {
                        "h2": ["28px", { lineHeight: "1.3", fontWeight: "600" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "label-caps": ["12px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
                    }
                }
            }
        };
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body { height: 100%; }
            body { @apply font-body bg-background text-on-background m-0; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; line-height: 1; }
        }
    </style>
</head>
<body class="bg-surface overflow-y-auto">

    <nav class="fixed inset-x-0 top-0 z-50 h-[64px] bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm flex items-center px-6">
        <a href="/home" class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
    </nav>

    <main class="max-w-[800px] mx-auto mt-[100px] px-6">
        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-[28px]">flight_takeoff</span>
            <h2 class="font-display text-h2 text-on-surface m-0">Plan a New Trip</h2>
        </div>

        <form action="/itinerary/store" method="POST" class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 lg:p-8">
            
            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="title">Trip Title</label>
                <input id="title" name="title" type="text" placeholder="e.g., Summer in Kyoto" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
            </div>

            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="description">Trip Notes / Description</label>
                <textarea id="description" name="description" rows="3" placeholder="What is the vibe of this trip?"
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition resize-y"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="startDate">Departure Date</label>
                    <input id="startDate" name="startDate" type="date" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                </div>
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="endDate">Return Date</label>
                    <input id="endDate" name="endDate" type="date" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="inviteEmails">
                    Invite Members <span class="normal-case text-outline font-normal ml-1">(Optional)</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute inset-y-0 left-3 flex items-center text-[20px] text-outline pointer-events-none">mail</span>
                    <input id="inviteEmails" name="inviteEmails" type="text" placeholder="e.g., ahmed@gmail.com, sara@gmail.com"
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest pl-11 pr-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                </div>
                <p class="mt-1.5 text-body-xs text-outline">Separate multiple email addresses with a comma.</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-outline-variant">
                <a href="/dashboard" class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-body-sm px-6 py-2.5 hover:bg-surface-container transition">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                    Create Itinerary <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </div>
        </form>
    </main>

</body>
</html>