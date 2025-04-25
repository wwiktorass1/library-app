document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('book-form');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            const errorContainer = document.getElementById('form-errors');
            if (errorContainer) {
                errorContainer.innerHTML = '';
            }

            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                alert('✅ Book submitted via AJAX!');
                form.reset();
            } else if (response.status === 400) {
                const result = await response.json();
                if (errorContainer && result.errors) {
                    for (const [field, messages] of Object.entries(result.errors)) {
                        messages.forEach(msg => {
                            const p = document.createElement('p');
                            p.textContent = `${field}: ${msg}`;
                            p.classList.add('text-danger');
                            errorContainer.appendChild(p);
                        });
                    }
                }
            } else {
                alert('❌ Failed to submit form');
            }
        });
    }

    const searchInput = document.getElementById('search-input');
    const bookList = document.getElementById('book-list');

    if (searchInput && bookList) {
        searchInput.addEventListener('keyup', function () {
            fetch(`/book/search?q=${encodeURIComponent(this.value)}`)
                .then(response => response.text())
                .then(html => {
                    bookList.innerHTML = html;
                });
        });
    }
});
