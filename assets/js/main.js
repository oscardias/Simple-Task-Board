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
    
    $('.remove-item-action').click(function(){
        var $modal = $('#removeModal');
        var html = $modal.html();
        html = html.replace('{url}', $(this).attr('href'));
        $modal.html(html).modal('show');
        
        return false;
    });
    
    var profile_links = $('#profile-links');
    if(profile_links[0]){
        $('.add_link', profile_links).live('click', function(){
            if ($(this).html() == '(+)') {
                var add = $('<div>' + $('div', profile_links).last().html() + '</div>').hide();
                profile_links.append(add);
                add.find('input').val('');
                add.fadeIn();
                $(this).html('(-)');
            } else {
                $(this).parent().fadeOut().remove();
            }   
        });
    }
    
    var profile_photo = $('#profile_photo');
    if(profile_photo[0]){
        $('img', profile_photo).click(function(){
            $(this).parent().next().click();
        });
    }
    
    $('.view-profile-details').live('click', function(){
        var href = $(this).attr('href');
        
        $.get(href, function(d){
            var $modal = $(d);
            
            $('body').append($modal);
            $modal.modal('show');

            $modal.on('hidden', function(){
                $modal.remove();
            });
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
       var duration = element.next();
       
       $.get(element.attr('href'),
           function(data){
               if(data.result == 1){
                   if(element.hasClass('stop')) {
                       element.attr('title', 'Continue');
                       element.attr('href', data.new_action);
                       element.removeClass('stop').addClass('play');
                       duration.html(data.duration);
                   } else {
                       element.attr('title', 'Stop');
                       element.attr('href', data.new_action);
                       element.removeClass('play').addClass('stop');
                       duration.html(data.duration + ' - ongoing');
                   }
               }
           },
           'json'
       );
           
       return false;
   });
   
   $('#task-history-details').click(function(){
       var href = $(this).attr('href');
       $.get(href, 
           function(data){
               $(data).dialog({
                   resizable: false,
                   height:0.8 * $(window).height(),
                   width:0.8 * $(window).width(),
                   modal: true,
                   buttons: {
                       Close: function() {
                           $( this ).dialog( "close" );
                       }
                   }
               });
           }
       );
       
       return false;
   });
    
 });