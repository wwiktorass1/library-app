document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-book-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this book?')) {
                return;
            }

            const url = form.getAttribute('data-url');
            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    form.closest('.book-item').remove(); 
                } else {
                    alert('❌ Failed to delete the book.');
                }
            } catch (err) {
                console.error('Delete failed:', err);
            }
        });
    });
});
