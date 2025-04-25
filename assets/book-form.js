import $ from 'jquery';

$(function () {
    $(document).on('submit', '#book-form', function (e) {
        e.preventDefault();
        const $form = $(this);

        $('#form-errors').empty();
        $('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function () {
                alert('✅ Book submitted via AJAX!');
                $form.trigger('reset');
            },
            error: function (xhr) {
                if (xhr.status === 400 || xhr.status === 422) {
                    const json = xhr.responseJSON;

                    if (json && json.errors) {
                        const $errorContainer = $('#form-errors');
                        $errorContainer.empty();

                        $.each(json.errors, function (field, messages) {
                            messages.forEach(function (msg) {
                                $errorContainer.append(`<p class="text-danger">${field}: ${msg}</p>`);
                            });
                        });
                    }
                } else {
                    console.error('Unexpected error:', xhr);
                }
            }
        });
    });
});
