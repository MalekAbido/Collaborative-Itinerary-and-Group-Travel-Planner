<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?php echo BASE_URL ?>assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Create User</h2>

        <form action="<?php echo BASE_URL ?>users/1" method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo $data['old']['name'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?php echo $data['errors']['name'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?php echo $data['old']['age'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?php echo $data['errors']['age'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $data['old']['email'] ?? '' ?>" class="form-control">
                <span class="text-danger"><?php echo $data['errors']['email'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="<?php echo $data['old']['password'] ?? '' ?>"
                    class="form-control">
                <span class="text-danger"><?php echo $data['errors']['password'] ?? '' ?></span>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Save</button>
            <a href="<?php echo BASE_URL ?>users/" class="btn btn-danger mt-2">Back</a>
        </form>
    </div>
</body>