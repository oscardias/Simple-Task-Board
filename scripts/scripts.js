$(document).ready(function(){
    /* Login focus */
    $('input[name=email]').focus();
    
    /* User actions */
    $('#reset_password').click(function(){
        if ($(this).is(':checked')) {
            $('#password').removeAttr('disabled');
        } else {
            $('#password').attr('disabled', true);
        }   
    });
    
    $('a.remove-user-event').click(function(){
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
    
    /* General actions */
    $('#show-hide-users').click(function(){
        var targetId = $(this).attr('target-id');
        if($(this).hasClass('expand')){
            $(this).removeClass('expand').addClass('collapse');
            $('#' + targetId).show('slow');
        } else {
            $(this).removeClass('collapse').addClass('expand');
            $('#' + targetId).hide('slow');
        }
    });
    
    /* Dashboard actions*/
    $('#switch-project-view').click(function(){
        if($(this).hasClass('global-tasks')) {
            $('div.project-task').show('slow');
            $(this).attr('title', 'Show mine');
            $(this).removeClass('global-tasks').addClass('user-tasks');
        } else {
            $('div.project-task').hide('slow');
            $(this).attr('title', 'Show all');
            $(this).removeClass('user-tasks').addClass('global-tasks');
        }
    });
    
    /* Task actios */
    $('#remove-task').click(function(){
        var targetUrl = $(this).attr('target-url');
        
        $('#dialog-confirm').dialog({
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "Remove": function() {
                    window.location = targetUrl;
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        
        return false;
    });
    
    $('a.task_time_control').live('click', function(){
        var element = $(this);
        var parent = element.parent();
        
        $.get(element.attr('href'),
            function(data){
                if(data.result == 1){
                    if(element.hasClass('stop')) {
                        element.attr('title', 'Continue');
                        element.attr('href', data.new_action);
                        element.removeClass('stop').addClass('play');
                        
                        var html = parent.html();
                        parent.html(html.replace('- ongoing', ''));
                    } else {
                        element.attr('title', 'Stop');
                        element.attr('href', data.new_action);
                        element.removeClass('play').addClass('stop');
                        
                        var html = parent.html();
                        parent.html(html + '- ongoing');
                    }
                }
            },
            'json'
        );
            
        return false;
    });
    
 });