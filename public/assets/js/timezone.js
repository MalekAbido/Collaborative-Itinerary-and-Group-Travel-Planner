// Modal functions
function openReopenModal(pollId) {
    document.getElementById('reopenPollId').value = pollId;
    document.getElementById('reopenPollModal').classList.remove('hidden');
}

function closeReopenModal() {
    document.getElementById('reopenPollModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const reopenTzInput = document.getElementById('clientTimezoneReopen');
    if (reopenTzInput) {
        reopenTzInput.value = userTimezone;
    }

    document.querySelectorAll('.local-time').forEach(el => {
        const utcString = el.getAttribute('data-utc');
        if (!utcString) return;

        const date = new Date(utcString);
        const formatType = el.getAttribute('data-format');

        let options = {};

        if (formatType === 'date') {
            options = { month: 'short', day: 'numeric' };
        } else {
            options = { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' };
        }

        el.textContent = date.toLocaleString(undefined, options);
    });
});