<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Edit User</h2>

        <form action="<?= BASE_URL ?>User/update/<?= $data['user']['id'] ?>" method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= $data['user']['name'] ?>" class="form-control">
            </div>

            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?= $data['user']['age'] ?>" class="form-control">
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= $data['user']['email'] ?>" class="form-control">
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="<?= $data['user']['password'] ?>" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary mt-2">Update</button>
            <a href="<?= BASE_URL ?>User/index" class="btn btn-danger mt-2">Back</a>
        </form>
    </div>
</body>