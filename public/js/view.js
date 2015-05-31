var global = {};

$(function() {
    
    global.baseUrl = $('#baseUrl').val();
    
    $("a.btn-import-view").on("click", function () {
        var importId = $(this).attr('data-id');
        
        $(this).button('loading');
        $.ajax({
            async: false,
            url: global.baseUrl + '/import/view/id/' + importId,
            type: "get",
            success: function(response){
                $('#viewImportModal .modal-body').html(response);
                $('#viewImportModal').modal();
            }
        }).always(function () {
            $('.btn-import-view').button('reset');
        });
    });
    
    // GENERIC DELETE
    $("a.btn-delete").on("click", function () {
        var url = $(this).attr('data-url');
        $('#deleteModal .modal-footer a').attr('href', url);
        $('#deleteModal').modal();
    });
    $('#deleteModal').on('hidden.bs.modal', function () {
        $('#deleteModal .modal-footer a').attr('href', '');
    });
    
});