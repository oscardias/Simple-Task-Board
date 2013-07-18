$(document).ready(function(){
    /* Login focus */
    $('input[name=email]').focus();
    
    // Date Picker
    $('.datepicker').datepicker();
    $('.datepicker-action').click(function(e){
        e.preventDefault();
        $(this).prev('input').datepicker('show');
    });
    
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
    
    var profile_links = $('#profile_links');
    if(profile_links[0]){
        $('.add_link', profile_links).live('click', function(e){
            e.preventDefault();
            var $icon = $(this).find('i');
            
            if ($icon.hasClass('icon-plus')) {
                var add = $('<div>' + $('div', profile_links).last().html() + '</div>').hide();
                profile_links.append(add);
                add.find('input').val('');
                add.fadeIn();
                $icon.removeClass('icon-plus').addClass('icon-minus');
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
        return false;
    });
    
    /* Task actios */
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
                       duration.html(data.duration + ' - running');
                   }
               }
           },
           'json'
       );
           
       return false;
   });
   
   $('#task-history-details').click(function(){
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
   
    $('.get-comment-action').click(function(){
        var href = $(this).attr('href').split('/');
        href = CI.base_url + 'task/ajax_comment/' + href[href.length - 3] + '/' + href[href.length - 2] + '/' + href[href.length - 1];
        
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
    
    $('#task-comment-submit').live('click', function(){
        var $form = $('#task-comment-modal-form');
        
        $.post($form.attr('action'),
            $form.serialize(),
            function(d){
                window.location = d;
            }
        );
    });
 });