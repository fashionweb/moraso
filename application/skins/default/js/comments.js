$(document).on('click', '.deleteComment', function(event) {
    event.preventDefault();

    var parentElement = $(this).parent('li').parent('ul').parent('div').parent('li');

    $.post('#', {
        action: 'deleteComment',
        node_id: $(parentElement).data('node_id')
    }, function(data) {
        if (data.success) {
            $(parentElement).slideUp('slow', function() {
                $(this).remove();
            });
        }
    }, 'json');
});