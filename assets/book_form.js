document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#book-form');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        const previousAlert = form.querySelector('.alert');
        if (previousAlert) previousAlert.remove();

        const formData = new FormData(form);
        const url = form.getAttribute('action');
        const method = form.getAttribute('method') || 'POST';

        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...`;

        try {
            const response = await fetch(url, {
                method,
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (response.ok) {
                const successMsg = document.createElement('div');
                successMsg.className = 'alert alert-success mt-3';
                successMsg.innerText = 'Saved successfully!';
                form.prepend(successMsg);
                form.reset();
            } else if (response.status === 400 && data.errors) {
                let firstInvalidField = null;
                for (const [fieldName, messages] of Object.entries(data.errors)) {
                    const field = form.querySelector('[name$="[' + fieldName + ']"]');
                    if (field) {
                        field.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.innerText = messages.join(', ');
                        field.after(errorDiv);

                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    }
                }

                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        } catch (err) {
            console.error('Submission failed', err);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    });
});
