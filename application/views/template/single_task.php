<div id="task_<?php echo $task['task_id']; ?>" <?php if($task['status'] == 3) { ?> title="<?php echo strip_tags($task['description']); ?>"<?php } ?>
        class="task <?php echo ($task['user_id'] == $current_user)?'my-task':'project-task'; ?>">
    <p class="task_id">#<?php echo $task['code']; ?></p>
    <p class="task_title"><?php echo task_parents_html($project, $task['parent_id']).anchor('task/view/'.$project.'/'.$task['task_id'], $task['title']); ?></p>
    <p class="task_user">Assigned to:
        <?php echo anchor('profile/view/'.$task['user_id'], 
                ($users[$task['user_id']]['name'])?$users[$task['user_id']]['name']:$users[$task['user_id']]['email'], 
                'class="view-profile-details"'); ?></p>

    <?php if($task['status'] < 3) { ?>
    
        <?php if($task['status'] > 0) { ?>
    
            <?php if($task['total_duration'] || $task['task_history_date_created']) { ?>
            <p class="task_time">Duration:
                <?php if($task['task_history_date_created']) { ?>
                    <?php echo anchor('task/timer/'.$project.'/'.$task['task_id'].'/stop', 'Stop', 'class="task_time_control stop" title="Stop"'); ?>

                    <span class="task_time_value">
                    <?php echo timespan_diff($task['total_duration'] + (time() - strtotime($task['task_history_date_created']))); ?>
                    <?php if($task['status'] != 3) { ?>
                    - ongoing
                    <?php } ?>
                    </span>
                <?php } else { ?>
                    <?php echo anchor('task/timer/'.$project.'/'.$task['task_id'].'/play', 'Continue', 'class="task_time_control play" title="Continue"'); ?>

                    <span class="task_time_value">
                    <?php echo timespan_diff($task['total_duration']); ?>
                    </span>
                <?php } ?>
            </p>
            <?php } ?>
            
        <?php } ?>

            <p class="task_text"><?php echo nl2br(word_limiter($task['description'], 30)); ?></p>
    
    <?php } ?>
    
    <?php if($task['status'] == 0) { ?>
    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/1', 'Start &raquo;'); ?></p>
    <?php } else if($task['status'] == 1) { ?>
    <p class="task_links">
        <?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/0', '&laquo; Back'); ?>
        |
        <?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/2', 'Test &raquo;'); ?>
    </p>
    <?php } else if($task['status'] == 2) { ?>
    <p class="task_links">
        <?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/1', '&laquo; Back'); ?>
        |
        <?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/3', 'Finish &raquo;'); ?>
    </p>
    <?php } else if($task['status'] == 3) { ?>
    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['task_id'].'/2', '&laquo; Back'); ?></p>
    <?php } ?>
</div>
