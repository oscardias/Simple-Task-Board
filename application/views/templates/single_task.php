<div id="task_<?php echo $task['task_id']; ?>" <?php if($task['status'] == 3) { ?> title="<?php echo strip_tags($task['description']); ?>"<?php } ?>
        class="task <?php echo ($task['user_id'] == $current_user)?'my-task':'project-task'; ?>">
    <p class="task_id">#<?php echo $task['code']; ?></p>
    <p class="task_title"><?php echo task_parents_html($project, $task['parent_id']).anchor('task/view/'.$project.'/'.$task['task_id'], $task['title']); ?></p>

    <?php if($task['status'] > 0 && $task['status'] < 3) { ?>
    
            <?php if($task['total_duration'] || $task['task_history_date_created']) { ?>
            <p class="task_time">Duration:
                <?php if($task['task_history_date_created']) { ?>
                    <?php echo anchor('task/timer/'.$project.'/'.$task['task_id'].'/stop', 'Stop', 'class="task_time_control stop" title="Stop"'); ?>

                    <span class="task_time_value">
                    <?php echo timespan_diff($task['total_duration'] + (time() - strtotime($task['task_history_date_created']))); ?>
                    <?php if($task['status'] != 3) { ?>
                    - running
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
    
    <div>
        
        <span class="label <?php echo task_priority_class($task['priority']); ?>"><?php echo strtolower(task_priority_text($task['priority'])); ?></span>
        
        <?php if($task['due_date'] && (strtotime(date('Y-m-d')) - strtotime($task['due_date'])) > 0) : ?>
        <span class="label label-important">late</span>
        <?php endif; ?>
    </div>

    <div class="task_user"><?php echo anchor('profile/view/'.$task['user_id'], 
                ($users[$task['user_id']]['name'])?$users[$task['user_id']]['name']:$users[$task['user_id']]['email'], 
                'class="view-profile-details"'); ?></div>
    
    <div class="task_links">
        <?php if($task['status'] == 0) { ?>
        
        <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/1'); ?>" class="btn btn-mini">
            <i class="icon-step-forward"></i>
            Start
        </a>
        
        <?php } else if($task['status'] == 1) { ?>
        
        <div class="btn-group">
            <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/0'); ?>" class="btn btn-mini get-comment-action">
                <i class="icon-step-backward"></i>
                Back
            </a>
            <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/2'); ?>" class="btn btn-mini get-comment-action">
                <i class="icon-step-forward"></i>
                Test
            </a>
        </div>
        
        <?php } else if($task['status'] == 2) { ?>

        <div class="btn-group">
            <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/1'); ?>" class="btn btn-mini get-comment-action">
                <i class="icon-step-backward"></i>
                Back
            </a>
            <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/3'); ?>" class="btn btn-mini get-comment-action">
                <i class="icon-step-forward"></i>
                Finish
            </a>
        </div>
        
        <?php } else if($task['status'] == 3) { ?>
            <a href="<?php echo base_url('task/move/'.$project.'/'.$task['task_id'].'/2'); ?>" class="btn btn-mini get-comment-action">
                <i class="icon-step-backward"></i>
                Back
            </a>
        <?php } ?>
    </div>
</div>
