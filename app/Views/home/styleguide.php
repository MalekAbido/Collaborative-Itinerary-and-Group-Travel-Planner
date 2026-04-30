<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itinerary Planner - Test</title>

    <link href="/assets/css/tailwind.css" rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="flex items-center justify-center bg-surface h-screen">

    <div
        class="max-w-md w-full bg-surface-container p-8 rounded-2xl shadow-lg border border-outline-variant text-center">
        <h1 class="text-h1 font-display text-primary mb-4">Hello Docker!</h1>

        <p class="text-body-md text-on-surface-variant mb-6">
            If this text is styled correctly, your Tailwind watcher inside the Docker container is working perfectly in
            real-time.
        </p>

        <button
            class="bg-tester hover:bg-primary-container text-on-primary font-bold py-3 px-6 rounded-lg transition-colors shadow-sm">
            Start Planning
        </button>

        <div class="mt-6 flex justify-center space-x-2">
            <span
                class="inline-flex items-center rounded-full bg-success-container px-3 py-1 text-label-xs text-on-success-container">
                Success Active
            </span>
            <span
                class="inline-flex items-center rounded-full bg-error-container px-3 py-1 text-label-xs text-on-error-container">
                Error Test
            </span>
        </div>
    </div>

</body>

</html>
