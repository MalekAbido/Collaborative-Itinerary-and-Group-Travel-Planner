<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="mb-3">
            <h2>Users</h2>
            <a href="<?= BASE_URL ?>users/create" class="btn btn-success mr-2">Create New User</a>
            <a href="<?= BASE_URL ?>" class="btn btn-danger">Back</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>

                        <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td><?= $user['firstName'] ?></td>
                            <td><?= $user['email'] ?></td>

                            <td>
                                <a href="<?= BASE_URL ?>users/<?= $user['id'] ?>"
                                    class="btn btn-outline-primary btn-sm mr-2">View</a>
                                <!-- <a href="<?= BASE_URL ?>users/edit/ ?>" class="btn btn-outline-secondary btn-sm mr-2">Edit</a> -->
                                <!-- <a href="<?= BASE_URL ?>User/delete/ ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete?')">Delete</a> -->
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>