var SEARCH ={};
SEARCH.search = function(){
    $('.form_search').submit(function(event) {
        event.preventDefault();
        $.ajaxSetup({
            data: {csrf_enuy_name:$('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType:'json',
            data: $(this).serialize()
        })
        .done(function(data) {
            if (data.code == 200) {
                toastr.success(data.message);
                window.location.reload();
            }
            else {
                toastr.error(data.message);
            }
        })
    });
}
$(function() {
    SEARCH.search();
});