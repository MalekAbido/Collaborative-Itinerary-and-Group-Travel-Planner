<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <!-- Requires the local build process to resolve custom variables -->
    <link href="<?php echo BASE_URL ?>assets/css/tailwind.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
</head>

<body class="bg-surface text-on-surface font-body antialiased p-8 flex justify-center items-center min-h-screen">

    <!-- Using arbitrary grid width requiring local compiler -->
    <div class="w-full max-w-[500px]">

        <h2 class="t-h2 mb-6 text-center">Create New User</h2>

        <div class="card p-8">
            <form action="<?php echo BASE_URL ?>users/1" method="POST" class="flex flex-col gap-5">

                <div>
                    <label
                        class="block text-xs font-bold tracking-widest uppercase text-on-surface-variant mb-2">Name</label>
                    <!-- Uses the custom @apply .form-input class -->
                    <input type="text" name="name" value="<?php echo htmlspecialchars($data['old']['name'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl text-on-surface placeholder-outline focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                    <?php
                    if (! empty($data['errors']['name'])): ?>
                    <p class="text-error text-sm mt-1.5 font-medium"><?php echo htmlspecialchars($data['errors']['name']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold tracking-widest uppercase text-on-surface-variant mb-2">Age</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($data['old']['age'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl text-on-surface placeholder-outline focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                    <?php
                    if (! empty($data['errors']['age'])): ?>
                    <p class="text-error text-sm mt-1.5 font-medium"><?php echo htmlspecialchars($data['errors']['age']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold tracking-widest uppercase text-on-surface-variant mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($data['old']['email'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl text-on-surface placeholder-outline focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                    <?php
                    if (! empty($data['errors']['email'])): ?>
                    <p class="text-error text-sm mt-1.5 font-medium"><?php echo htmlspecialchars($data['errors']['email']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold tracking-widest uppercase text-on-surface-variant mb-2">Password</label>
                    <input type="password" name="password"
                        value="<?php echo htmlspecialchars($data['old']['password'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl text-on-surface font-mono text-lg tracking-widest placeholder-outline focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                    <?php
                    if (! empty($data['errors']['password'])): ?>
                    <p class="text-error text-sm mt-1.5 font-medium">
                        <?php echo htmlspecialchars($data['errors']['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3 mt-4 pt-4 border-t border-outline-variant">
                    <button type="submit" class="btn btn-primary flex-1 py-3">
                        Save User
                    </button>
                    <a href="<?php echo BASE_URL ?>users/" class="btn btn-outline-action py-3 px-6">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>