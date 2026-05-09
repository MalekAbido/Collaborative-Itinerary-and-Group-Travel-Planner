document.addEventListener('DOMContentLoaded', function() {
                            const sosBtn = document.getElementById('sos-btn');
                            const notyf = new Notyf({
                                duration: 5000,
                                position: { x: 'right', y: 'bottom' },
                                dismissible: true
                            });

                            if (sosBtn) {
                                sosBtn.addEventListener('click', function() {
                                    if (!confirm('⚠️ WARNING: This will immediately send an emergency email to all your saved emergency contacts. Are you sure you want to proceed?')) {
                                        return;
                                    }

                                    notyf.success('SOS Activated! Emergency contacts are being notified.');

                                    fetch('/sos/trigger', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log('SOS Request Sent:', data);
                                    })
                                    .catch(error => {
                                        console.error('SOS Error:', error);
                                    });
                                });
                            }
                        });