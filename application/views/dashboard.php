<?php if(isset($tasks)) { ?>
<div id="dash_tasks" class="dash_wrap">
    <div id="dash_task_title" class="dash_wrap_title blue-gradient">
        Tasks
    </div>
    <div id="dash_task_items" class="dash_wrap_items">
        <ul>
        <?php if($tasks) { ?>
            <?php $listControl = 0; ?>
            <?php foreach ($tasks as $task) { ?>
                <li>
                <?php echo anchor('task/view/'.$task['project_id'].'/'.$task['task_id'], '#'.$task['code'].' - '.$task['title']); ?>
                (<?php echo $status_arr[$task['status']] ?>)
                </li>
                <?php $listControl++; ?>
                <?php if($listControl % 10 == 0) { ?>
            </ul><ul>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            There are no tasks assigned to you.
        <?php } ?>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<?php } ?>

<?php if(isset($projects)) { ?>
<div id="dash_projects" class="dash_wrap">
    <div id="dash_projects_title" class="dash_wrap_title blue-gradient">
        Projects
    </div>
    <div id="dash_projects_items" class="dash_wrap_items">
<?php foreach ($projects as $project) { ?>
    <div id="project_<?php echo $project['id']; ?>" class="dash_project_group">
        <p class="dash_project_title blue-gradient"><?php echo anchor('project/tasks/'.$project['id'], $project['name']); ?></p>

        <?php if($project['tasks']) { ?>
        <ul>
        <?php foreach ($project['tasks'] as $key => $task) { ?>
            <?php if($key < 4) { ?>
            <li><?php echo anchor('task/view/'.$project['id'].'/'.$task['task_id'], '#'.$task['code'].' - '.$task['title']); ?>
                <em>(<?php echo $status_arr[$task['status']] ?>)</em></li>
            <?php } else { ?>
            <li><em>more ...</em></li>
            <?php } ?>
        <?php } ?>
        </ul>
        <p class="form-save-buttons"><?php echo anchor('project/tasks/'.$project['id'], 'View all tasks', 'class="btn-blue dash_view_all_tasks"'); ?></p>
        <?php } else { ?>
        No tasks here!
        <?php } ?>
    </div>
<?php } ?>
    </div>
</div>
<?php } ?>