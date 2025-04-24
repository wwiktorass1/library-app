import $ from 'jquery';

$(function () {
    $(document).on('submit', '#book-form', function (e) {
        e.preventDefault();
        const $form = $(this);

        $('.form-error-message').text('');

        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: $form.serialize(),
            success: function () {
                window.location.href = '/book';
            },
            error: function (xhr) {
                if (xhr.status === 422 || xhr.status === 400) {
                    $('#form-container').html(xhr.responseText);
                } else {
                    console.error('Unexpected error:', xhr);
                }
            }
        });
    });
});
