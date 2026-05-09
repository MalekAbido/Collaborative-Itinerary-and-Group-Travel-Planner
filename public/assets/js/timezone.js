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

        if (formatType === 'date') {
            el.textContent = date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
        } else if (formatType === 'time') {
            el.textContent = date.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
        } else if (formatType === 'datetime') {
            el.textContent = date.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        } else {
            el.textContent = date.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Support both ID (for legacy) and name="timezone" (for multiple forms)
    const timezoneInputs = document.querySelectorAll('#clientTimezoneReopen, input[name="timezone"]');
    timezoneInputs.forEach(input => {
        input.value = userTimezone;
    });

    formatLocalTimes();
});
