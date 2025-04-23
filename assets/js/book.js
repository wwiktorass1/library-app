document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('book-form');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
            });

            if (response.ok) {
                alert('✅ Book submitted via AJAX!');
                form.reset();
            } else {
                alert('❌ Failed to submit form');
            }
        });
    }
});
