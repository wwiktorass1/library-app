import $ from 'jquery';

$(function () {
    $('#book-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const action = $form.attr('action');
        const method = $form.attr('method');

        $.ajax({
            url: action,
            method: method,
            data: $form.serialize(),
            success: function () {
                const successMessage = `
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        ✅ Book successfully created!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#book-form').before(successMessage);

                setTimeout(() => {
                    window.location.href = '/book';
                }, 2000);
            },
            error: function (xhr) {
                const response = xhr.responseText;
                const newDom = $('<div>').html(response);
                const newForm = newDom.find('#book-form');

                if (newForm.length) {
                    $('#book-form').replaceWith(newForm);
                } else {
                    alert('Something went wrong. Please try again.');
                }

                console.error('Form error response:', response);
            }
        });
    });
});
