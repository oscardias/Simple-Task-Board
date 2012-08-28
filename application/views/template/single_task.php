<div id="task_<?php echo $task['id']; ?>" <?php if($task['status'] == 3) { ?>title="<?php echo $task['description']; ?>"<?php } ?>
        class="task <?php echo ($task['user'] == $current_user)?'my-task':'project-task'; ?>">
    <p class="task_id">#<?php echo $task['id']; ?></p>
    <p class="task_title"><?php echo anchor('task/view/'.$project.'/'.$task['id'], $task['title']); ?></p>
    <p class="task_user">Assigned to: <?php echo $users[$task['user']]['email']; ?></p>
    <?php if($task['status'] < 3) { ?>
    <p class="task_text"><?php echo word_limiter($task['description'], 30); ?></p>
    <?php } ?>
    <?php if($task['status'] == 0) { ?>
    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/1', 'Start &raquo;'); ?></p>
    <?php } else if($task['status'] == 1) { ?>
    <p class="task_links">
        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/0', '&laquo; Back'); ?>
        |
        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/2', 'Test &raquo;'); ?>
    </p>
    <?php } else if($task['status'] == 2) { ?>
    <p class="task_links">
        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/1', '&laquo; Back'); ?>
        |
        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/3', 'Finish &raquo;'); ?>
    </p>
    <?php } else if($task['status'] == 3) { ?>
    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/2', '&laquo; Back'); ?></p>
    <?php } ?>
</div>
