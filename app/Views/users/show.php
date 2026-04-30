<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <!-- Requires the local build process to resolve custom variables -->
    <link href="<?php echo BASE_URL ?>assets/css/tailwind.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
</head>

<body class="bg-surface text-on-surface font-body antialiased p-8">
    <div class="max-w-3xl mx-auto mt-10">

        <h2 class="t-h2 mb-6">User Details</h2>

        <!-- Uses the @apply .card component from your input.css -->
        <div class="card p-0 mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-outline-variant">
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-4 px-6 font-semibold text-on-surface-variant w-1/3">Name</td>
                            <td class="py-4 px-6"><?php echo htmlspecialchars($data['user']['firstName'] ?? '') ?></td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-4 px-6 font-semibold text-on-surface-variant">Nationality</td>
                            <td class="py-4 px-6"><?php echo htmlspecialchars($data['user']['nationality'] ?? '') ?></td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-4 px-6 font-semibold text-on-surface-variant">Email</td>
                            <td class="py-4 px-6"><?php echo htmlspecialchars($data['user']['email'] ?? '') ?></td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-4 px-6 font-semibold text-on-surface-variant">Role</td>
                            <td class="py-4 px-6">
                                <!-- Using arbitrary values and theme colors requiring compiler -->
                                <span
                                    class="bg-primary-fixed text-on-primary-fixed px-3 py-1 rounded-full text-sm font-bold tracking-wide">
                                    <?php echo htmlspecialchars($data['user']['nationality'] ?? '') ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="<?php echo BASE_URL ?>users/" class="btn btn-outline-action">
            &larr; Back to Users
        </a>

    </div>
</body>

</html>