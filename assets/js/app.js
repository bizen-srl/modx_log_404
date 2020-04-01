$(document).ready(function(){

    $('#Datatable').DataTable();

    $('.btn-danger').click(function(e){
        e.preventDefault();
        let el = $(this);
        let action = el.data('action');
        let url = el.data('url');
        if (action) {
            $.ajax({
                method: "DELETE",
                url: action,
                data: {'url': url}
            }).done(function(res){
                console.log(res);
                el.closest('tr').fadeOut('slow');
            });
        }
    });
});