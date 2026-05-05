document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            const isConfirmed = confirm("Are you sure you want to remove this item? This action cannot be undone.");
            if (isConfirmed) {
                this.submit(); 
            }
        });
    });

    const profileForm = document.querySelector('form[action="/profile/update"]');
    if (profileForm) {
        profileForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = `<span class="inline-block w-4 h-4 rounded-full border-2 border-on-primary border-t-transparent animate-spin-slow"></span> Saving…`;
            submitBtn.classList.add('cursor-not-allowed', 'opacity-90');
        });
    }
});