<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Itinerary</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet" />

    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#f65a41",
                    "primary-container": "#ff8b71",
                    "primary-fixed": "#ffdad3",
                    "on-primary": "#ffffff",
                    "on-primary-fixed-variant": "#7b2a1a",
                    "secondary": "#825500",
                    "secondary-container": "#feaa00",
                    "secondary-fixed": "#ffddb3",
                    "on-secondary": "#ffffff",
                    "success": "#16a34a",
                    "success-container": "#bbf7d0",
                    "on-success": "#ffffff",
                    "on-success-container": "#064e2c",
                    "error": "#ba1a1a",
                    "error-container": "#ffdad6",
                    "on-error": "#ffffff",
                    "on-error-container": "#93000a",
                    "background": "#fcf8f8",
                    "on-background": "#191c1d",
                    "surface": "#fcf8f8",
                    "surface-container-lowest": "#ffffff",
                    "surface-container": "#edeeef",
                    "on-surface": "#191c1d",
                    "on-surface-variant": "#414754",
                    "outline": "#727785",
                    "outline-variant": "#c1c6d6",
                },
                fontFamily: {
                    display: ["'Plus Jakarta Sans'", "sans-serif"],
                    body: ["'Inter'", "sans-serif"]
                },
                fontSize: {
                    "h2": ["28px", {
                        lineHeight: "1.3",
                        fontWeight: "600"
                    }],
                    "h4": ["17px", {
                        lineHeight: "1.4",
                        fontWeight: "600"
                    }],
                    "body-md": ["16px", {
                        lineHeight: "1.5",
                        fontWeight: "400"
                    }],
                    "body-sm": ["14px", {
                        lineHeight: "1.5",
                        fontWeight: "400"
                    }],
                    "body-xs": ["13px", {
                        lineHeight: "1.4",
                        fontWeight: "400"
                    }],
                    "label-caps": ["12px", {
                        lineHeight: "1",
                        letterSpacing: "0.05em",
                        fontWeight: "700"
                    }],
                    "label-xs": ["11px", {
                        lineHeight: "1",
                        letterSpacing: "0.05em",
                        fontWeight: "700"
                    }],
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

    <nav
        class="fixed inset-x-0 top-0 z-50 h-[64px] bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant shadow-sm flex items-center justify-between px-6">
        <a href="/home" class="font-display text-[22px] font-extrabold tracking-tight text-primary">VoyageSync</a>
        <a href="/itinerary/dashboard/<?= htmlspecialchars($trip['id'] ?? '') ?>"
            class="inline-flex items-center gap-1 text-body-sm font-semibold text-outline hover:text-primary transition">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back to Dashboard
        </a>
    </nav>

    <main class="max-w-[900px] mx-auto mt-[100px] px-6 pb-12">

        <!-- Reverted this header back to normal -->
        <div class="flex items-center gap-2 pb-3 mb-8 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-[28px]">group</span>
            <h2 class="font-display text-h2 text-on-surface m-0">Manage Members</h2>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">

            <!-- NEW: Flex container to hold the title and the Master Link button side-by-side -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-h4 text-on-surface m-0">Invite to Trip</h3>

                <?php if (isset($currentUserRole) && $currentUserRole === 'Leader'): ?>
                <button onclick="copyInviteLink('<?= htmlspecialchars($generalLink) ?>')"
                    class="inline-flex items-center gap-1.5 rounded-lg border-2 border-outline-variant bg-surface-container-lowest text-on-surface-variant font-semibold text-body-sm px-3 py-1.5 shadow-sm hover:text-primary hover:border-primary transition">
                    <span class="material-symbols-outlined text-[18px]">link</span> Copy General Link
                </button>
                <?php endif; ?>
            </div>

            <form action="/itinerary/members/invite/<?= htmlspecialchars($trip['id'] ?? '') ?>" method="POST"
                class="flex flex-col sm:flex-row items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="email">User Email
                        Address</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute inset-y-0 left-3 flex items-center text-[20px] text-outline pointer-events-none">mail</span>
                        <input id="email" name="email" type="email" placeholder="e.g., ahmed@voyagesync.com" required
                            class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest pl-11 pr-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                    </div>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="role">Assign
                        Role</label>
                    <select id="role" name="role"
                        class="w-full sm:w-48 rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                        <option value="Member">👤 Member</option>
                        <option value="Editor">✏️ Editor</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition h-[42px]">
                    <span class="material-symbols-outlined text-base">person_add</span> Send Invite
                </button>
            </form>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
            <h3 class="font-display text-h4 text-on-surface mb-4">Current Members</h3>
            <div class="divide-y divide-outline-variant">

                <div
                    class="hidden sm:grid grid-cols-[1fr_120px_120px_100px] gap-4 py-3 text-label-caps uppercase text-outline">
                    <span>Member</span><span class="text-center">Role</span><span>Joined</span><span
                        class="text-right">Actions</span>
                </div>

                <?php if (!empty($members)): ?>
                <?php foreach ($members as $member): ?>
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_120px_120px_100px] gap-4 py-4 sm:py-3 items-center">

                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary text-sm font-semibold">
                            <?= strtoupper(substr($member['firstName'], 0, 1) . substr($member['lastName'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="font-display text-h4 text-on-surface">
                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?></div>
                            <div class="text-body-xs text-outline"><?= htmlspecialchars($member['email']) ?></div>
                        </div>
                    </div>

                    <div class="text-left sm:text-center">
                        <?php if ($member['role'] === 'Leader'): ?>
                        <span
                            class="inline-flex items-center rounded-full bg-primary-fixed px-3 py-1 text-label-xs font-bold uppercase text-primary">👑
                            Leader</span>
                        <?php elseif ($member['role'] === 'Editor'): ?>
                        <span
                            class="inline-flex items-center rounded-full bg-secondary-fixed px-3 py-1 text-label-xs font-bold uppercase text-secondary">✏️
                            Editor</span>
                        <?php else: ?>
                        <span
                            class="inline-flex items-center rounded-full bg-surface-container-highest px-3 py-1 text-label-xs font-bold uppercase text-outline">👤
                            Member</span>
                        <?php endif; ?>
                    </div>

                    <span class="text-body-xs text-on-surface-variant hidden sm:block">
                        <?= date('M j, Y', strtotime($member['joinedAt'])) ?>
                    </span>

                    <div class="flex justify-start sm:justify-end gap-2">
                        <?php if ($member['role'] !== 'Leader'): ?>
                        <form action="/itinerary/members/updateRole/<?= htmlspecialchars($trip['id']) ?>" method="POST"
                            class="inline">
                            <input type="hidden" name="memberId" value="<?= $member['id'] ?>">
                            <input type="hidden" name="newRole"
                                value="<?= $member['role'] === 'Member' ? 'Editor' : 'Member' ?>">
                            <button type="submit" title="Toggle Role"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-primary text-primary hover:bg-primary-fixed transition">
                                <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                            </button>
                        </form>

                        <form action="/itinerary/members/remove/<?= htmlspecialchars($trip['id']) ?>" method="POST"
                            class="inline" onsubmit="return confirm('Remove this user from the trip?');">
                            <input type="hidden" name="memberId" value="<?= $member['id'] ?>">
                            <button type="submit" title="Remove Member"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-error text-error hover:bg-error-container transition">
                                <span class="material-symbols-outlined text-[16px]">person_remove</span>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="py-8 text-center text-on-surface-variant text-body-sm">
                    No members have been added yet. Invite someone above!
                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if (!empty($pendingInvites)): ?>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mt-8">
            <h3 class="font-display text-h4 text-on-surface mb-4">Pending Invitations</h3>

            <div class="divide-y divide-outline-variant">
                <div
                    class="hidden sm:grid grid-cols-[1fr_120px_140px] gap-4 py-3 text-label-caps uppercase text-outline">
                    <span>Invited Email</span><span class="text-center">Role</span><span class="text-right">Share
                        Link</span>
                </div>

                <?php foreach ($pendingInvites as $invite): ?>
                <?php $joinLink = $appUrl . "/join/" . $invite['secureToken']; ?>
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_120px_140px] gap-4 py-4 sm:py-3 items-center">

                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-container-highest text-outline text-sm font-semibold">
                            <span class="material-symbols-outlined text-[18px]">schedule</span>
                        </div>
                        <div>
                            <div class="font-display text-h4 text-on-surface">
                                <?= htmlspecialchars($invite['email'] ?? 'Unknown') ?></div>
                            <div class="text-body-xs text-outline">Awaiting response...</div>
                        </div>
                    </div>

                    <div class="text-left sm:text-center">
                        <?php if ($invite['role'] === 'Editor'): ?>
                        <span
                            class="inline-flex items-center rounded-full bg-secondary-fixed px-3 py-1 text-label-xs font-bold uppercase text-secondary">
                            Editor</span>
                        <?php else: ?>
                        <span
                            class="inline-flex items-center rounded-full bg-surface-container-highest px-3 py-1 text-label-xs font-bold uppercase text-outline">
                            Member</span>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-start sm:justify-end gap-2">
                        <button onclick="copyInviteLink('<?= $joinLink ?>')" title="Copy Join Link"
                            class="inline-flex items-center gap-1 h-9 px-3 rounded-lg border-2 border-primary text-primary hover:bg-primary-fixed transition text-body-sm font-semibold">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span> Copy Link
                        </button>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <script>
        function copyInviteLink(link) {
            navigator.clipboard.writeText(link).then(function() {
                alert("Join link copied to your clipboard!");
            }).catch(function(err) {
                alert("Failed to copy link. Please select and copy manually: " + link);
            });
        }
        </script>

    </main>

</body>

</html>