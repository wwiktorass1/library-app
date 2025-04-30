$(document).on('submit', '#book-form', function(e) {
    e.preventDefault();
    
    var $form = $(this);
    var $spinner = $('#spinner');
    var $alert = $('#alert-success');
    
    $spinner.show();
    $.ajax({
        url: $form.attr('action'),
        method: $form.attr('method'),
        data: $form.serialize(),
        success: function(response) {
            $('#book-list').html(response); 
            $alert.text('Operacija sėkminga!').fadeIn();
            setTimeout(function() {
                $alert.fadeOut();
            }, 4000);
        },
        error: function(xhr) {
            alert('Įvyko klaida: ' + xhr.responseText);
        },
        complete: function() {
            $spinner.hide();
        }
    });
});
