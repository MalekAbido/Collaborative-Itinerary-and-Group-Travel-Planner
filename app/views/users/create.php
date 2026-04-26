<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Create User</h2>

        <form action="<?= BASE_URL ?>User/store" method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= $data['old']['name'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?= $data['errors']['name'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?= $data['old']['age'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?= $data['errors']['age'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= $data['old']['email'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?= $data['errors']['email'] ?? '' ?></span>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="<?= $data['old']['password'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?= $data['errors']['password'] ?? '' ?></span>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Save</button>
            <a href="<?= BASE_URL ?>User/index" class="btn btn-danger mt-2">Back</a>
        </form>
    </div>
</body>