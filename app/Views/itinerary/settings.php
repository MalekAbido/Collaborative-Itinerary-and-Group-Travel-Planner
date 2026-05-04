<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Settings - VoyageSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <nav class="bg-white shadow-sm px-6 py-4 mb-8 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">VoyageSync</h1>
        <a href="/itinerary/dashboard/<?= htmlspecialchars($trip['itineraryId']) ?>" class="text-sm font-medium text-gray-500 hover:text-blue-600">
            &larr; Back to Dashboard
        </a>
    </nav>

    <div class="max-w-2xl mx-auto px-4">
        
        <h2 class="text-2xl font-bold mb-6">Trip Settings</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md shadow-sm">
                <p class="text-green-700 font-medium">Trip successfully updated!</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Edit Details</h3>
            
            <form action="/itinerary/update/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trip Title</label>
                    <input type="text" name="title" required
                           value="<?= htmlspecialchars($trip['title']) ?>" 
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= htmlspecialchars($trip['description']) ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="startDate" 
                               value="<?= htmlspecialchars($trip['startDate']) ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="endDate" 
                               value="<?= htmlspecialchars($trip['endDate']) ?>"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-md hover:bg-blue-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-red-50 rounded-lg shadow-sm border border-red-100 p-6">
            <h3 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-red-600 mb-4">Once you delete a trip, there is no going back. Please be certain.</p>
            
            <form action="/itinerary/destroy/<?= htmlspecialchars($trip['itineraryId']) ?>" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this trip?');">
                <button type="submit" class="bg-white border border-red-300 text-red-600 font-semibold py-2 px-4 rounded-md hover:bg-red-50 transition">
                    Delete Trip
                </button>
            </form>
        </div>

    </div>

</body>
</html>