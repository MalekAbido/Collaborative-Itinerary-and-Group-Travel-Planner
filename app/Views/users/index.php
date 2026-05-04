<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <!-- Requires the local build process to resolve custom variables -->
    <link href="<?php echo BASE_URL ?>assets/css/tailwind.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
</head>

<body class="bg-surface text-on-surface font-body antialiased p-8">
    <div class="max-w-5xl mx-auto mt-10">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <h2 class="t-h2 text-primary">Users Directory</h2>
            <div class="flex gap-3">
                <a href="<?php echo BASE_URL ?>users/create" class="btn btn-primary">
                    + Create New User
                </a>
                <a href="<?php echo BASE_URL ?>" class="btn btn-outline-action">
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Custom Card Component -->
        <div class="card p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant">
                            <th class="py-4 px-6 text-sm font-bold tracking-widest uppercase text-on-surface-variant">
                                Name</th>
                            <th class="py-4 px-6 text-sm font-bold tracking-widest uppercase text-on-surface-variant">
                                Email</th>
                            <th
                                class="py-4 px-6 text-sm font-bold tracking-widest uppercase text-on-surface-variant text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <?php
                        foreach ($data['users'] as $user): ?>
                        <tr class="hover:bg-surface-container-low transition-colors group">
                            <td class="py-4 px-6 font-medium"><?php echo htmlspecialchars($user['firstName']) ?></td>
                            <td class="py-4 px-6 text-on-surface-variant"><?php echo htmlspecialchars($user['email']) ?></td>
                            <td class="py-4 px-6 text-right">
                                <a href="<?php echo BASE_URL ?>users/<?php echo $user['id'] ?>"
                                    class="btn bg-surface-container-high text-on-surface hover:bg-surface-dim px-4 py-2 text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>