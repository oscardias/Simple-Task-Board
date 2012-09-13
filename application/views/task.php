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
            </p>
            <?php endif; ?>
            
            <p class="task-info-description">
                <p><strong>Description</strong></p>
                <?php echo $description; ?>
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
                <em><?php echo $user; ?></em>
            </p>
            <p class="task-info-status">
                <strong>History:</strong>
            <ul class="task-history">
                <?php if($task_history) { ?>
                <?php foreach ($task_history as $value) { ?>
                <li>
                    <?php echo $status_arr[$value['status']]; ?><br/>
                    <ul>
                        <li>
                            <em>
                        <?php if($task_history_last['status'] == $value['status']) { ?>
                        <?php echo timespan_diff($value['duration'] + (time() - strtotime($task_history_last['date_created']))); ?> - ongoing
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