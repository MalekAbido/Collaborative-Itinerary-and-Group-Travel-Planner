<?php
// Provided by UserController::showUserProfile()
// $user (User Object), $allergies (Array of Allergy Objects), $emergencyContacts (Array of Contact Objects)
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Profile Settings | Itinerary</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" />

    <!-- Link to your compiled Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/tailwind.css">

    <style>
        /* Fallback for custom scrollbar */
        .scroll-thin::-webkit-scrollbar {
            width: 6px;
        }

        .scroll-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scroll-thin::-webkit-scrollbar-thumb {
            background-color: #c1c6d6;
            border-radius: 9999px;
        }
    </style>
</head>

<body class="bg-background text-on-background font-body text-body-md m-0 overflow-hidden">

    <div class="flex h-screen overflow-hidden">

        <!-- NAVBAR WITH EMBEDDED NAVIGATION -->
        <nav class="fixed inset-x-0 top-0 z-50 h-navbar bg-surface-container-lowest/90 backdrop-blur-md border-b border-outline-variant shadow-sm">
            <div class="mx-auto flex h-full max-w-content items-center justify-between px-6 lg:px-8">
                <div class="flex items-center gap-8">
                    <a href="/dashboard" class="font-display text-[22px] font-extrabold tracking-tight text-primary whitespace-nowrap">
                        Itinerary
                    </a>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex items-center gap-2">
                        <a href="/dashboard" class="px-3 py-2 rounded-md text-body-sm font-medium text-on-surface-variant hover:text-primary hover:bg-primary-fixed/40 transition">
                            Dashboard
                        </a>
                        <a href="/profile" class="px-3 py-2 rounded-md text-body-sm font-medium text-primary border-b-2 border-primary">
                            Profile Settings
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container transition">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                    </button>
                    <a href="/profile" class="flex items-center gap-2 cursor-pointer">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-fixed text-primary text-xs font-bold border-2 border-outline-variant">
                            <?= strtoupper(substr($user->getFirstName(), 0, 1) . substr($user->getLastName(), 0, 1)) ?>
                        </div>
                    </a>
                </div>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="flex-1 mt-navbar h-[calc(100vh-theme(spacing.navbar))] overflow-y-auto bg-surface p-6 lg:p-8 scroll-thin">
            <div class="max-w-content mx-auto max-w-4xl">

                <header class="mb-8">
                    <h1 class="font-display text-h1 text-on-surface mb-2">Profile Settings</h1>
                    <p class="text-body-md text-on-surface-variant">Manage your personal information, medical needs, and emergency contacts.</p>
                </header>

                <!-- SECTION 1: General Information -->
                <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                        <span class="material-symbols-outlined text-primary">person</span>
                        <h2 class="font-display text-h3 text-on-surface m-0">General Information</h2>
                    </div>

                    <form action="/profile/update" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">First Name</label>
                            <input type="text" name="firstName" value="<?= htmlspecialchars($user->getFirstName()) ?>" required
                                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                        </div>
                        <div>
                            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Last Name</label>
                            <input type="text" name="lastName" value="<?= htmlspecialchars($user->getLastName()) ?>" required
                                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                        </div>
                        <!-- CHANGED: Made editable, added name="email" -->
                        <div class="md:col-span-2">
                            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required
                                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                        </div>
                        <div>
                            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Nationality</label>
                            <input type="text" name="nationality" value="<?= htmlspecialchars($user->getNationality() ?? '') ?>" placeholder="e.g., Egyptian"
                                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                        </div>
                        <div>
                            <label class="block text-label-caps uppercase text-on-surface-variant mb-2">Insurance Policy Number</label>
                            <input type="text" name="policyNumber" value="<?= htmlspecialchars($user->getPolicyNumber() ?? '') ?>" placeholder="Optional"
                                class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                        </div>

                        <div class="md:col-span-2 flex justify-end mt-4">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition">
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </section>

                <!-- SECTION 2: Medical & Allergies -->
                <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                        <span class="material-symbols-outlined text-secondary">medical_information</span>
                        <h2 class="font-display text-h3 text-on-surface m-0">Medical & Allergies</h2>
                    </div>

                    <!-- List Existing Allergies -->
                    <div class="mb-6 divide-y divide-outline-variant">
                        <?php if (!empty($allergies)): ?>
                            <?php foreach ($allergies as $allergy): ?>
                                <div class="flex items-center justify-between py-4">
                                    <div>
                                        <h4 class="font-display text-h4 text-on-surface mb-0.5"><?= htmlspecialchars($allergy->getAllergen()) ?></h4>
                                        <div class="flex items-center gap-2 text-body-xs text-on-surface-variant">
                                            <span class="inline-flex items-center rounded-full bg-surface-container-highest px-2 py-0.5 text-label-xs uppercase text-outline">Severity: <?= htmlspecialchars($allergy->getSeverity()) ?></span>
                                            <span>Reaction: <?= htmlspecialchars($allergy->getReaction()) ?></span>
                                        </div>
                                    </div>
                                    <form action="/allergy/remove" method="POST" class="delete-form">
                                        <input type="hidden" name="allergyId" value="<?= $allergy->getId() ?>">
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-error text-error hover:bg-error-container transition" title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-body-sm text-outline py-4">No medical conditions or allergies recorded.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Add New Allergy Form -->
                    <div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant">
                        <h4 class="text-label-caps uppercase text-on-surface-variant mb-4">Add Medical Record</h4>
                        <form action="/allergy/add" method="POST" class="flex flex-col md:flex-row items-end gap-4">
                            <div class="w-full md:w-1/3">
                                <label class="block text-label-xs uppercase text-outline mb-1.5">Allergen / Condition</label>
                                <input type="text" name="allergen" placeholder="e.g., Peanuts" required
                                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                            </div>
                            <div class="w-full md:w-1/4">
                                <label class="block text-label-xs uppercase text-outline mb-1.5">Severity</label>
                                <select name="severity" required class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                                    <option value="" disabled selected>Select...</option>
                                    <option value="Mild">Mild</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                            </div>
                            <div class="w-full md:flex-1">
                                <label class="block text-label-xs uppercase text-outline mb-1.5">Reaction</label>
                                <input type="text" name="reaction" placeholder="e.g., Swelling" required
                                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                            </div>
                            <div class="w-full md:w-auto">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border-2 border-primary text-primary font-semibold text-body-sm px-6 py-2 hover:bg-primary-fixed transition">
                                    <span class="material-symbols-outlined text-[18px]">add</span> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- SECTION 3: Emergency Contacts -->
                <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
                        <span class="material-symbols-outlined text-error">contact_emergency</span>
                        <h2 class="font-display text-h3 text-on-surface m-0">Emergency Contacts</h2>
                    </div>

                    <!-- List Existing Contacts -->
                    <div class="mb-6 divide-y divide-outline-variant">
                        <?php if (!empty($emergencyContacts)): ?>
                            <?php foreach ($emergencyContacts as $contact): ?>
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-container-highest text-outline shrink-0">
                                            <span class="material-symbols-outlined text-[24px]">person</span>
                                        </div>
                                        <div>
                                            <h4 class="font-display text-h4 text-on-surface mb-0.5"><?= htmlspecialchars($contact->getName()) ?></h4>
                                            <div class="flex flex-wrap gap-2 text-body-xs text-on-surface-variant">
                                                <span class="inline-flex items-center rounded-full bg-surface-container-highest px-2 py-0.5 text-label-xs uppercase text-outline"><?= htmlspecialchars($contact->getRelationship()) ?></span>
                                                <span><?= htmlspecialchars($contact->getPhone()) ?></span>
                                                <?php if ($contact->getEmail()): ?>
                                                    <span>· <?= htmlspecialchars($contact->getEmail()) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="/emergency-contact/remove" method="POST" class="delete-form">
                                        <input type="hidden" name="contactId" value="<?= $contact->getId() ?>">
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-error text-error hover:bg-error-container transition" title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center gap-3 rounded-xl border border-secondary/30 bg-secondary-fixed px-4 py-3 mb-4">
                                <span class="material-symbols-outlined text-secondary">warning</span>
                                <div class="flex-1">
                                    <span class="block text-label-xs uppercase text-secondary font-bold">Action Required</span>
                                    <h4 class="text-body-sm font-semibold text-on-secondary-container m-0">Please add at least one emergency contact for safety during trips.</h4>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Add New Contact Form -->
                    <div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant">
                        <h4 class="text-label-caps uppercase text-on-surface-variant mb-4">Add New Contact</h4>
                        <form action="/emergency-contact/add" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-label-xs uppercase text-outline mb-1.5">Full Name</label>
                                    <input type="text" name="name" required
                                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                                </div>
                                <div>
                                    <label class="block text-label-xs uppercase text-outline mb-1.5">Relationship</label>
                                    <input type="text" name="relationship" placeholder="e.g., Parent, Spouse" required
                                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                                </div>
                                <div>
                                    <label class="block text-label-xs uppercase text-outline mb-1.5">Phone Number</label>
                                    <input type="tel" name="phone" required
                                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                                </div>
                                <div>
                                    <label class="block text-label-xs uppercase text-outline mb-1.5">Email (Optional)</label>
                                    <input type="email" name="email"
                                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-secondary-container text-on-secondary-container font-semibold text-body-sm px-6 py-2 shadow-sm hover:bg-secondary-fixed-dim transition">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span> Save Contact
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <footer class="mt-12 pt-8 pb-4 text-center text-body-xs text-outline border-t border-outline-variant">
                    © <?= date('Y') ?> Itinerary. All rights reserved.
                </footer>

            </div>
        </main>

    </div>

    <script src="/assets/js/user.js"></script>
</body>

</html>