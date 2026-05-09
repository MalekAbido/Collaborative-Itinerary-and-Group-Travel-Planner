<?php 
use App\Enums\TripMemberRole;
require __DIR__ . '/../layouts/header.php';
$canManageMembers = App\Helpers\Auth::hasRole(TripMemberRole::ORGANIZER->value, $currentUserRole);
?>

<!-- <main class="max-w-[900px] mx-auto mt-[100px] px-6 pb-12"> -->

<!-- Reverted this header back to normal -->
<div class="flex items-center gap-2 pb-3 mb-8 border-b border-outline-variant">
    <span class="material-symbols-outlined text-primary text-[28px]">group</span>
    <h2 class="font-display text-h2 text-on-surface m-0"><?php echo($canManageMembers)?'Manage':'Itinerary'; ?> Members</h2>
</div>

<?php if ($canManageMembers): ?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">

    <!-- NEW: Flex container to hold the title and the Master Link button side-by-side -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-h4 text-on-surface m-0">Invite to Trip</h3>

        <button onclick="copyInviteLink('<?php echo htmlspecialchars($generalLink) ?>')"
            class="inline-flex items-center gap-1.5 rounded-lg border-2 border-outline-variant bg-surface-container-lowest text-on-surface-variant font-semibold text-body-sm px-3 py-1.5 shadow-sm hover:text-primary hover:border-primary transition">
            <span class="material-symbols-outlined text-[18px]">link</span> Copy General Link
        </button>
    </div>

    <form action="/itinerary/members/invite/<?php echo htmlspecialchars($trip['id'] ?? '') ?>" method="POST"
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
                <option value="<?= TripMemberRole::MEMBER->value ?>">👤 Member</option>
                <option value="<?= TripMemberRole::EDITOR->value ?>">✏️ Editor</option>
            </select>
        </div>
        <button type="submit"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition h-[42px]">
            <span class="material-symbols-outlined text-base">person_add</span> Send Invite
        </button>
    </form>
</div>
<?php endif; ?>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">
    <h3 class="font-display text-h4 text-on-surface mb-4">Current Members</h3>
    <div class="divide-y divide-outline-variant">

        <div class="hidden sm:grid <?php echo ($canManageMembers ? 'sm:grid-cols-[1fr_120px_120px_100px]' : 'sm:grid-cols-[1fr_120px_120px]'); ?> gap-4 py-3 text-label-caps uppercase text-outline">
            <span>Member</span><span class="text-center">Role</span><span>Joined</span>
            <?php if ($canManageMembers): ?>
            <span class="text-right">Actions</span>
            <?php endif; ?>
        </div>

        <?php
        if (! empty($data['members'])): ?>
        <?php
        foreach ($data['members'] as $member): ?>
        <div class="grid grid-cols-1 <?php echo ($canManageMembers ? 'sm:grid-cols-[1fr_120px_120px_100px]' : 'sm:grid-cols-[1fr_120px_120px]'); ?> gap-4 py-4 sm:py-3 items-center">

            <!-- 1. Avatar and Info -->
            <div class="flex items-center gap-3">
                <div
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary text-sm font-semibold">
                    <?php echo strtoupper(substr($member['firstName'], 0, 1) . substr($member['lastName'], 0, 1)) ?>
                </div>
                <div>
                    <div class="font-display text-h4 text-on-surface">
                        <?php echo htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?></div>
                    <div class="text-body-xs text-outline"><?php echo htmlspecialchars($member['email']) ?></div>
                </div>
            </div>

            <!-- 2. Role Badge -->
            <div class="text-left sm:text-center">
                <?php
                if ($member['role'] === TripMemberRole::ORGANIZER->value): ?>
                <span
                    class="inline-flex items-center rounded-full bg-primary-fixed px-3 py-1 text-label-xs font-bold uppercase text-primary">👑
                    <?php echo htmlspecialchars($member['role']) ?></span>
                <?php elseif ($member['role'] === TripMemberRole::EDITOR->value): ?>
                <span
                    class="inline-flex items-center rounded-full bg-secondary-fixed px-3 py-1 text-label-xs font-bold uppercase text-secondary">✏️
                    Editor</span>
                <?php else: ?>
                <span
                    class="inline-flex items-center rounded-full bg-surface-container-highest px-3 py-1 text-label-xs font-bold uppercase text-outline">👤
                    <?php echo htmlspecialchars($member['role']) ?></span>
                <?php endif; ?>
            </div>

            <!-- 3. Join Date -->
            <span class="text-body-xs text-on-surface-variant hidden sm:block">
                <?php echo ! empty($member['joinedAt']) ? date('M j, Y', strtotime($member['joinedAt'])) : 'Just now' ?>
            </span>

            <!-- 4. Action Buttons -->
            <?php if ($canManageMembers): ?>
            <div class="flex justify-start sm:justify-end gap-2">
                <?php
                if ($member['role'] !== TripMemberRole::ORGANIZER->value): ?>
                <form action="/itinerary/members/updateRole/<?php echo htmlspecialchars($data['trip']['id']) ?>" method="POST"
                    class="inline">
                    <input type="hidden" name="memberId" value="<?php echo htmlspecialchars($member['memberId'] ?? '') ?>">
                    <input type="hidden" name="newRole"
                        value="<?php echo $member['role'] === TripMemberRole::MEMBER->value ? TripMemberRole::EDITOR->value : TripMemberRole::MEMBER->value ?>">
                    <button type="submit" title="Toggle Role"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-primary text-primary hover:bg-primary-fixed transition">
                        <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                    </button>
                </form>

                <form action="/itinerary/members/remove/<?php echo htmlspecialchars($data['trip']['id']) ?>" method="POST"
                    class="inline" onsubmit="return confirm('Remove this user from the trip?');">
                    <input type="hidden" name="memberId" value="<?php echo htmlspecialchars($member['memberId'] ?? '') ?>">
                    <button type="submit" title="Remove Member"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-error text-error hover:bg-error-container transition">
                        <span class="material-symbols-outlined text-[16px]">person_remove</span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="py-8 text-center text-on-surface-variant text-body-sm">
            No members have been added yet. Invite someone above!
        </div>
        <?php endif; ?>

    </div>
</div>

<?php
if (! empty($pendingInvites)): ?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mt-8">
    <h3 class="font-display text-h4 text-on-surface mb-4">Pending Invitations</h3>

    <div class="divide-y divide-outline-variant">
        <div class="hidden sm:grid grid-cols-[1fr_120px_140px] gap-4 py-3 text-label-caps uppercase text-outline">
            <span>Invited Email</span><span class="text-center">Role</span><span class="text-right">Share
                Link</span>
        </div>

        <?php
        foreach ($pendingInvites as $invite): ?>
        <?php $joinLink = $appUrl . "/join/" . $invite['secureToken']; ?>
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_120px_140px] gap-4 py-4 sm:py-3 items-center">

            <div class="flex items-center gap-3">
                <div
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-container-highest text-outline text-sm font-semibold">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                </div>
                <div>
                    <div class="font-display text-h4 text-on-surface">
                        <?php echo htmlspecialchars($invite['email'] ?? 'Unknown') ?></div>
                    <div class="text-body-xs text-outline">Awaiting response...</div>
                </div>
            </div>

            <div class="text-left sm:text-center">
                <?php
                if ($invite['role'] === TripMemberRole::EDITOR->value): ?>
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
                <button onclick="copyInviteLink('<?php echo $joinLink ?>')" title="Copy Join Link"
                    class="inline-flex items-center gap-1 h-9 px-3 rounded-lg border-2 border-primary text-primary hover:bg-primary-fixed transition text-body-sm font-semibold">
                    <span class="material-symbols-outlined text-[16px]">content_copy</span> Copy Link
                </button>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<?php if (!$canManageMembers): ?>
<div class="flex justify-end mt-12 pb-12">
    <form action="/itinerary/members/leave/<?php echo htmlspecialchars($trip['id'] ?? '') ?>" method="POST"
        onsubmit="return confirm('Are you sure you want to leave this itinerary? This action cannot be undone.');">
        <button type="submit"
            class="inline-flex items-center gap-2 rounded-lg border-2 border-error text-error font-semibold text-body-sm px-6 py-2.5 hover:bg-error-container transition cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">logout</span> Leave Itinerary
        </button>
    </form>
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



<?php require __DIR__ . '/../layouts/footer.php'; ?>