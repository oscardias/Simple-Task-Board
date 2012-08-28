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
            <p class="task-info-description">
                <p><strong>Description</strong></p>
                <?php echo $description; ?>
            </p>
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
        </div>
        
    </div>
    
    <div class="task-comments">
        <p class="task-comments-title"><strong>Comments</strong></p>
        <?php if($comments) { ?>
        <?php foreach ($comments as $comment) { ?>
        <div class="task-comment-single" id="task-comment-id-<?php echo $comment['id']; ?>">
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
            
            <?php echo form_hidden('project', $project); ?>
            <?php echo form_hidden('task', $task); ?>
            
            <p><?php echo form_submit('submit', 'Submit', 'class="blue-gradient"'); ?></p>
            
            <?php echo form_close(); ?>
        </div>
    </div>

</div>