<?php

    use App\Helpers\Auth;
    require __DIR__ . '/../layouts/header.php';
    
    // FIX 1: Safely count members only if the attendance list exists
    $totalMembers = $attendanceList ? count($attendanceList->getMembers()) : 0;
?>

        <div class="col-span-12 lg:col-span-8 flex flex-col gap-6">
                    <div
                        class="bg-surface rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border-l-4 border-primary overflow-hidden relative">
                        <div class="h-48 w-full bg-cover bg-center relative"
                            data-alt="A breathtaking view of Mount Fuji towering over a serene lake, reflecting perfectly in the still water. The sky is a clear, vibrant blue, characteristic of a crisp autumn morning in Japan. The scene embodies exploration and awe, fitting the 'Structured Exploration' theme of the travel app with high contrast and clean lines."
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAP0GA5BMqfjA9S_J6C8IYH2XP2cYgizm7IlSqBm8LkYC3Li2e4GEPTlgdZP7doWnvRCYr4Qua4WJpmyBzL73w4tVN7itPOZaqG1HsoTedgiOX2M4EzdFloidufze5t_X0YPhXSSv6ix9oadjTtsTZIajspW6FXbEsZLIEQmlfzGz2MvFf1bYwdyyR-8oLlwfFYa6Gh_HMGmhjdju1zQBSZTVunUiBFxSxebwSRXXBnEpEJzaWa88NJoCSYmpCKrleezvRJUkQLJVQ');">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div
                                class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span
                                    class="font-label-caps text-label-caps text-on-surface"><?php echo htmlspecialchars($activity->getCategory()) ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h1 class="font-h1 text-h1 text-on-surface mb-2">
                                        <?php echo htmlspecialchars($activity->getName()) ?></h1>
                                    <div
                                        class="flex items-center gap-4 text-on-surface-variant font-body-sm text-body-sm">
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base">calendar_month</span>
                                            <?php echo date('M j, g:i A', strtotime($activity->getStartTime())) ?> -
                                            <?php echo date('g:i A', strtotime($activity->getEndTime())) ?>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base">location_on</span>
                                            <?php echo $activity->getLocation()->getName(); ?>

                                        </div>
                                    </div>
                                </div>
                                <?php if ($activity->getActivityStatus() == 'CONFIRMED'): ?>
                                <div class="relative">
                                    <form
                                        action="/itinerary/<?php echo $itineraryId ?>/activity/<?php echo $activity->getId() ?>/updateAttendance"
                                        method="POST">
                                        <select name="status" onchange="this.form.submit()"
                                            class="bg-none appearance-none bg-surface-container border border-outline-variant text-on-surface font-button text-button py-2 pl-4 pr-10 rounded-full focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">

                                            <option value="PENDING"
                                                <?php
                                                echo ($currentMemberStatus && $currentMemberStatus->getStatus() === 'PENDING') ? 'selected' : '' ?>>
                                                Pending
                                            </option>
                                            <option value="GOING"
                                                <?php
                                                echo ($currentMemberStatus && $currentMemberStatus->getStatus() === 'GOING') ? 'selected' : '' ?>>Going
                                            </option>
                                            <option value="NOT_GOING"
                                                <?php
                                                echo ($currentMemberStatus && $currentMemberStatus->getStatus() === 'NOT_GOING') ? 'selected' : '' ?>>
                                                Not
                                                Going
                                            </option>

                                        </select>
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div
                                class="prose max-w-none text-on-surface-variant font-body-md text-body-md border-t border-surface-variant pt-4 mt-2">
                                <p><?php echo nl2br(htmlspecialchars($activity->getDescription())) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($activity->getActivityStatus() == 'CONFIRMED'): ?>
                    <div class="bg-surface rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)]  border border-surface-variant p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-h3 text-h3 text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-tertiary">group</span> Attendees
                            </h2>
                            <span
                                class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-full">
                                <?php echo $totalGoing; ?>
                                / <?php echo $totalMembers; ?></span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-surface-variant">
                                        <th
                                            class="py-2 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                                            Member</th>
                                        <th
                                            class="py-2 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="font-body-sm text-body-sm">
                                    <?php

                                    if ($attendanceList): ?>
                                    <?php

                                    foreach ($attendanceList->getMembers() as $memberStatus): ?>
                                    <?php
                                        $person = $memberStatus->getTripMember()->getUser();
                                    ?>
                                    <tr
                                        class="border-b border-surface-variant/50 hover:bg-surface-bright transition-colors">

                                        <td class="py-3 px-4 flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-surface-variant flex items-center justify-center font-bold text-primary">
                                                <?php echo substr($person->getFirstName(), 0, 1) ?>
                                            </div>
                                            <span class="font-semibold text-on-surface">
                                                <?php echo htmlspecialchars($person->getFirstName() . ' ' . $person->getLastName()) ?>

                                                <?php

                                                if ($memberStatus->getTripMemberId() == $currentMemberId): ?>
                                                <span class="text-xs text-gray-400 ml-2">(You)</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-4">
                                            <?php

                                            if ($memberStatus->getStatus() === 'GOING'): ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-tertiary/10 text-tertiary px-3 py-1 rounded-full text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                Going
                                            </span>

                                            <?php elseif ($memberStatus->getStatus() === 'PENDING'): ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-surface-container text-on-surface-variant px-3 py-1 rounded-full text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[14px]">help</span>
                                                Pending
                                            </span>

                                            <?php else: ?>
                                            <span
                                                class="inline-flex items-center gap-1 bg-surface-container text-on-surface-variant px-3 py-1 rounded-full text-xs font-semibold opacity-60">
                                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                                Not
                                                Going
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="p-4 text-center">No attendance tracking set up yet.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (Auth::hasRole("Editor", $userRole) && $activity->getActivityStatus() == 'CONFIRMED'): ?>
                        <div class="mt-auto pt-6 flex justify-end border-surface-variant">
                            <form
                                action="/itinerary/<?php echo $itineraryId ?>/activity/<?php echo $activity->getId() ?>/delete"
                                method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');">
                                <button type="submit"
                                    class="text-error font-button text-button flex items-center gap-1 hover:bg-error-container/50 px-3 py-2 rounded-md transition-colors opacity-70 hover:opacity-100">
                                    <span class="material-symbols-outlined text-[16px]">delete</span> Delete Activity
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
        </div>
    <nav
        class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md font-['Plus_Jakarta_Sans'] text-[10px] font-semibold fixed bottom-0 w-full rounded-t-2xl border-t border-zinc-100 dark:border-zinc-800 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-2">
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="map">map</span>
            Trip
        </a>
        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="rule">rule</span>
            Manage
        </a>

        <a class="flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 active:bg-zinc-100 dark:active:bg-zinc-800 rounded-xl px-4 py-1"
            href="#">
            <span class="material-symbols-outlined mb-1" data-icon="person">person</span>
            Profile
        </a>
    </nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>