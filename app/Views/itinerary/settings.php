<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Settings - VoyageSync</title>

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
                        "h4": ["17px", { lineHeight: "1.4", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "label-caps": ["12px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
                        "label-xs": ["11px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "700" }],
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

    <nav class="fixed inset-x-0 top-0 z-50 h-[64px] bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm flex items-center justify-between px-6">
        <a href="/home" class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
        <a href="/itinerary/dashboard/<?= htmlspecialchars($trip['itineraryId']) ?>" class="inline-flex items-center gap-1 text-body-sm font-semibold text-outline hover:text-primary transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back to Dashboard
        </a>
    </nav>

    <main class="max-w-[800px] mx-auto mt-[100px] px-6 pb-12">
        
        <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-[28px]">settings</span>
            <h2 class="font-display text-h2 text-on-surface m-0">Trip Settings</h2>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
            <div class="flex items-center gap-3 rounded-xl border border-success/30 bg-success-container px-4 py-3 mb-8">
                <span class="material-symbols-outlined text-success">check_circle</span>
                <div class="flex-1">
                    <span class="block text-label-xs uppercase text-success font-bold">Success</span>
                    <h4 class="text-body-md font-semibold text-on-success-container m-0">Trip details updated successfully!</h4>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 lg:p-8 mb-8">
            <h3 class="font-display text-h4 text-on-surface mb-6 border-b border-outline-variant pb-3">Edit Details</h3>
            
            <form action="/itinerary/update/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Trip Title</label>
                    <input type="text" name="title" required
                           value="<?= htmlspecialchars($trip['title']) ?>" 
                           class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition resize-y"><?= htmlspecialchars($trip['description']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Start Date</label>
                        <input type="date" name="startDate" 
                               value="<?= htmlspecialchars($trip['startDate']) ?>"
                               class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-label-caps uppercase text-on-surface-variant mb-2">End Date</label>
                        <input type="date" name="endDate" 
                               value="<?= htmlspecialchars($trip['endDate']) ?>"
                               class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                    </div>
                </div>

                <div class="pt-6 border-t border-outline-variant mt-6">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-3 shadow-sm hover:bg-on-primary-fixed-variant transition">
                        <span class="material-symbols-outlined text-[20px]">save</span> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-error-container/30 border border-error/30 rounded-xl shadow-sm p-6 lg:p-8">
            <h3 class="font-display text-h4 text-error mb-2">Danger Zone</h3>
            <p class="text-body-sm text-on-error-container mb-6">Once you delete a trip, there is no going back. All activities, polls, and budget data will be permanently removed.</p>
            
            <form action="/itinerary/destroy/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this trip?');">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-error text-on-error font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-error-container transition">
                    <span class="material-symbols-outlined text-base">delete_forever</span> Delete Trip
                </button>
            </form>
        </div>

    </main>

</body>
</html>