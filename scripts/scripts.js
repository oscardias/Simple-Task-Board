$(document).ready(function(){
    $('#reset_password').click(function(){
        if ($(this).is(':checked')) {
            $('#password').removeAttr('disabled');
        } else {
            $('#password').attr('disabled', true);
        }   
    });
    
    $('.remove-user-event').click(function(){
        var url = $(this).attr('href');
        $('#dialog-confirm').dialog({
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
    
    $('.show-hide-event').click(function(){
        var targetId = $(this).attr('target-id');
        if($(this).hasClass('expand')){
            $(this).removeClass('expand').addClass('collapse');
            $('#' + targetId).show('slow');
        } else {
            $(this).removeClass('collapse').addClass('expand');
            $('#' + targetId).hide('slow');
        }
    });
    
    $('.switch-project-view-event').click(function(){
        if($(this).hasClass('global-tasks')) {
            $('.project-task').show('slow');
            $(this).attr('title', 'Show mine');
            $(this).removeClass('global-tasks').addClass('user-tasks');
        } else {
            $('.project-task').hide('slow');
            $(this).attr('title', 'Show all');
            $(this).removeClass('user-tasks').addClass('global-tasks');
        }
    });
 });