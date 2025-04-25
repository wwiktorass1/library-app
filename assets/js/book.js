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

    const searchInput = document.getElementById('search-input');
    const bookList = document.getElementById('book-list');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            fetch(`/book/search?q=${encodeURIComponent(this.value)}`)
                .then(response => response.text())
                .then(html => {
                    bookList.innerHTML = html;
                });
        });
    }
});
