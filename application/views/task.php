<?php
// Load Menu
$this->template->menu('task_view');
?>

<div id="container">

    <div class="task-info">
        <p class="task-info-title">
            <?php echo $title; ?>
        </p>
        <div class="task-info-left">
            
            <?php if($parent_tasks) : ?>
            <p class="task-info-breadcrumb">
                <?php foreach($parent_tasks as $key => $value){ ?>
                <?php echo ($key == 0)?'':'&gt;'; ?>
                <?php echo anchor(base_url()."task/view/{$project_id}/{$value['id']}", $value['title']); ?>
                <?php } ?>
                &gt;
                <?php echo $title; ?>
            </p>
            <?php endif; ?>
            
            <p class="task-info-description">
                <p><strong>Description</strong></p>
                <?php echo nl2br(htmlspecialchars($description)); ?>
            </p>
            
            <?php if($children_tasks) : ?>
            <p class="task-info-breadcrumb">
                <p><strong>Children</strong></p>
                <?php task_hierarchy_html($project_id, $children_tasks); ?>
            </p>
            <?php endif; ?>
            
            <?php if($files) { ?>
            <p class="task-info-files">
                <strong>Files:</strong>
                <?php echo $files; ?>
            </p>
            <?php } ?>
            <?php if($database) { ?>
            <p class="task-info-database">
                <strong>Database:</strong>
                <?php echo $database; ?>
            </p>
            <?php } ?>
        </div>
        
        <div class="task-info-right">
            <p class="task-info-priority">
                <strong>Priority:</strong>
                <?php $options = array('0' => 'Very High', '1' => 'High', '2' => 'Normal', '3' => 'Low', '4' => 'Very Low'); ?>
                <?php echo $options[$priority]; ?>
            </p>
            <p class="task-info-user">
                <strong>Assigned To:</strong>
                <em><?php echo anchor('profile/view/'.$user['id'], ($user['name'])?$user['name']:$user['email'], 'class="view-profile-details"'); ?></em>
            </p>
            <p>
                <strong>Current Phase:</strong>
                <?php echo $status_arr[$status]; ?>
            </p>
            <p class="task-info-status">
                <?php if($total_duration || $task_history_date_created) { ?>
                <p><strong>Duration:</strong><br/>
                    <?php if($task_history_date_created) { ?>
                        <?php echo anchor('task/timer/'.$project_id.'/'.$task_id.'/stop', 'Stop', 'class="task_time_control stop" title="Stop"'); ?>

                        <span class="task_time_value">
                        <?php echo timespan_diff($total_duration + (time() - strtotime($task_history_date_created))); ?>
                        <?php if($status != (count($status_arr) - 1)) { ?>
                        - ongoing
                        <?php } ?>
                        </span>
                    <?php } else { ?>
                        <?php echo anchor('task/timer/'.$project_id.'/'.$task_id.'/play', 'Continue', 'class="task_time_control play" title="Continue"'); ?>

                        <span class="task_time_value">
                        <?php echo timespan_diff($total_duration); ?>
                        </span>
                    <?php } ?>
                </p>
                <?php } ?>
                <strong>History (<?php echo anchor('task/history/'.$project_id.'/'.$task_id, 'details', 'id="task-history-details"'); ?>):</strong>
            <ul class="task-history">
                <?php if($task_history) { ?>
                <?php foreach ($task_history as $value) { ?>
                <li>
                    <?php echo $status_arr[$value['status']]; ?><br/>
                    <ul>
                        <li>
                            <em>
                        <?php if($task_history_date_created) { ?>
                        <?php echo timespan_diff($value['duration'] + (time() - strtotime($task_history_date_created))); ?>
                                <?php if($value['status'] != (count($status_arr) - 1)) { ?>
                                - ongoing
                                <?php } ?>
                        <?php } else { ?>
                        <?php echo timespan_diff($value['duration']); ?>
                        <?php } ?>
                            </em>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <?php } else { ?>
                <li><?php echo $status_arr[$status]; ?><br/></li>
                <?php } ?>
            </ul>
            </p>
        </div>
        
    </div>
    
    <div class="task-comments">
        <p class="task-comments-title"><strong>Comments</strong></p>
        <?php if($comments) { ?>
        <?php foreach ($comments as $comment) { ?>
        <div class="task-comment-single" id="task-comment-id-<?php echo $comment['task_comments_id']; ?>">
            <p><strong><?php echo $comment['email']; ?></strong> <em>(<?php echo $comment['date_created']; ?>)</em></p>
            <?php echo $comment['comment']; ?>
        </div>
        <?php } ?>
        <?php } else { ?>
        No comments here!
        <?php } ?>
        <div class="task-comment-form">
            <?php echo form_open('task/comment'); ?>

            <p class="task-comments-title"><strong><?php echo form_label('New Comment', 'comment'); ?></strong></p>
            <?php
            $data = array('name'        => 'comment',
                          'id'          => 'comment',
                          'value'       => set_value('comment'),
                          'rows'        => '6',
                          'cols'        => '80');

            echo form_textarea($data); ?>
            
            <?php echo form_hidden('project_id', $project_id); ?>
            <?php echo form_hidden('task_id', $task_id); ?>
            
            <p><?php echo form_submit('submit', 'Submit', 'class="blue-gradient"'); ?></p>
            
            <?php echo form_close(); ?>
        </div>
    </div>

</div>