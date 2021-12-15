var PAGECONTACT ={};
PAGECONTACT.sendContact = function(){
    $('.d_sendcontact').submit(function(event) {
        event.preventDefault();
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
    PAGECONTACT.sendContact();
});