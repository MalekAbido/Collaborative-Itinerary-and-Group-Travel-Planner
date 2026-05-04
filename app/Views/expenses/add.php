<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
</head>
<body>
    <h1>Log a New Expense</h1>

    <form action="/finance/expense/create" method="POST">
        
        <input type="hidden" name="financeId" value="1"> 

        <div>
            <label>Description (e.g. Dinner at Luigi's):</label>
            <input type="text" name="description" required>
        </div>

        <div>
            <label>Category:</label>
            <input type="text" name="category" placeholder="Food, Transport, etc.">
        </div>

        <div>
            <label>Total Amount ($):</label>
            <input type="number" step="0.01" name="amount" required>
        </div>

        <div>
            <label>Who Paid?</label>
            <select name="payerId" required>
                <option value="1">Alice</option>
                <option value="2">Bob</option>
                <option value="3">Charlie</option>
            </select>
        </div>

        <div>
            <label>Split Method:</label>
            <select name="splitMethod" id="splitMethod">
                <option value="EVEN">Even Split</option>
                <option value="UNEVEN">Uneven Split</option>
            </select>
        </div>

        <div id="sharesSection">
            <h3>Who is involved? (If Uneven, enter exact amounts)</h3>
            
            <div>
                <label>Alice's Share:</label>
                <input type="number" step="0.01" name="shares[1]" class="share-input" value="0">
            </div>
            <div>
                <label>Bob's Share:</label>
                <input type="number" step="0.01" name="shares[2]" class="share-input" value="0">
            </div>
            <div>
                <label>Charlie's Share:</label>
                <input type="number" step="0.01" name="shares[3]" class="share-input" value="0">
            </div>
        </div>

        <button type="submit">Save Expense</button>
    </form>

    <script>
        const splitMethodDropdown = document.getElementById('splitMethod');
        const shareInputs = document.querySelectorAll('.share-input');

        splitMethodDropdown.addEventListener('change', function() {
            if (this.value === 'EVEN') {
                shareInputs.forEach(input => input.readOnly = true);
            } else {
                shareInputs.forEach(input => input.readOnly = false);
            }
        });
        
        splitMethodDropdown.dispatchEvent(new Event('change'));
    </script>
</body>
</html>