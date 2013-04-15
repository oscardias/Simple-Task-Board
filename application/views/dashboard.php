<?php if(isset($tasks) && $tasks) : ?>
<h2>You Tasks and Projects</h2>
<div class="row-fluid">
    <?php
    $current_project = 0;
    $column_control = 0;
    ?>
    <?php foreach ($tasks as $task) : ?>
        <?php if($current_project != $task['project_id']) : ?>
            <?php if($current_project != 0) : ?>
                </div>
            <?php endif; ?>

            <?php if($column_control % 4 == 0) { ?>
            </div>
            <div class="row-fluid">
            <?php } ?>
                
            <?php
            $current_project = $task['project_id'];
            $column_control++;
            ?>
            <div class="span3">
                <h3><a href="<?php echo base_url('project/tasks/'.$task['project_id']); ?>"><?php echo $task['project_name']; ?></a></h3>
        <?php endif; ?>
            <?php if($task['task_id']) : ?>
                <?php echo anchor('task/view/'.$task['project_id'].'/'.$task['task_id'], '#'.$task['code'].' - '.$task['title']); ?>
                (<?php echo $status_arr[$task['status']]; ?>)
                <br/>
            <?php else : ?>
                <div class="alert">No tasks here...</div>
            <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php else : ?>
<div class="alert">You don't have any tasks or projects assigned to you.</div>
<?php endif; ?>