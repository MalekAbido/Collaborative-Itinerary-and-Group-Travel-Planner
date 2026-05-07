function formatLocalTimes(elements) {
    const targets = elements || document.querySelectorAll('.local-time');
    targets.forEach(el => {
        const utcString = el.getAttribute('data-utc');
        if (!utcString) return;

        // Ensure the string is treated as UTC if it doesn't have a timezone indicator
        // PHP date('c') usually includes it, but raw DB strings might not.
        const date = new Date(utcString.includes('Z') || utcString.includes('+') ? utcString : utcString + 'Z');
        
        // Check if date is valid
        if (isNaN(date.getTime())) return;

        const formatType = el.getAttribute('data-format');

        let options = {};
        if (formatType === 'date') {
            options = { month: 'short', day: 'numeric' };
        } else if (formatType === 'datetime') {
            options = { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' };
        } else {
            options = { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' };
        }

        el.textContent = date.toLocaleString(undefined, options);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const reopenTzInput = document.getElementById('clientTimezoneReopen');
    if (reopenTzInput) {
        reopenTzInput.value = userTimezone;
    }

    formatLocalTimes();
});
