<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">

</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center min-vh-100"">
            <div class="card shadow-lg p-5 text-center w-50">
                <h1 class="mb-4">Welcome to My PHP MVC Project</h1>

                <p class="lead mb-4">
                    This is the home page.
                </p>

                <a href="<?= BASE_URL ?>User/index" class="btn btn-primary btn-lg">
                    Go to Users Page
                </a>
            </div>
    </div>

</body>

</html>