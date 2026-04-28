<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css"> -->
</head>

<body>
    <div class="container">
        <h2>User Details</h2>

        <div class="card shadow-sm">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <td>Name</td>
                            <!-- <td><?php var_dump($data) ?></td> -->
                            <td><?= $data['user']['firstName'] ?></td>
                        </tr>
                        <tr>
                            <td>Age</td>
                            <td><?= $data['user']['nationality'] ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?= $data['user']['email'] ?></td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td><?= $data['user']['nationality'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <a href="<?= BASE_URL ?>users/" class="btn btn-danger mt-2">Back</a>

    </div>
</body>

</html>