$(document).ready(function(){
    $("#reset_password").click(function(){
        if ($(this).is(':checked')) {
            $('#password').removeAttr('disabled');
        } else {
            $('#password').attr('disabled', true);
        }   
    });
    
    $(".remove-user-event").click(function(){
        var url = $(this).attr('href');
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "Remove": function() {
                    window.location = url;
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        
        return false;
    });
 });