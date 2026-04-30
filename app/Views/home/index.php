<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Itinerary Planner</title>

    <link rel="stylesheet" href="<?php echo BASE_URL ?>assets/css/tailwind.css">
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-10 max-w-lg w-full text-center border border-slate-100">

            <h1 class="text-3xl  text-slate-900 mb-3 tracking-tight">
                Welcome to the MVC Project
            </h1>

            <p class="text-lg text-slate-500 mb-8">
                The router is working, and Tailwind CSS is successfully compiling. This is the home page.
            </p>

            <div class="flex flex-col space-y-4">
                <a href="<?php echo BASE_URL ?>styleguide"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 ease-in-out shadow-sm hover:shadow-md">
                    View UI Components Playground here
                </a>
                <br>
                <a href="<?php echo BASE_URL ?>users"
                    class="inline-block bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-3 px-6 rounded-lg transition duration-200 ease-in-out">
                    Go to Users Page
                </a>
            </div>

        </div>
    </div>

</body>

</html>