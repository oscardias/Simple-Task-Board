<?php
// Load Menu
$this->template->menu('dashboard');
?>

<div id="container">
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
                    <?php echo anchor('task/edit/'.$task['project'].'/'.$task['id'], '#'.$task['id'].' - '.$task['title']); ?>
                    (<?php echo $status[$task['status']] ?>)
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
            <ul>
                <?php if($project['tasks']) { ?>
                <?php foreach ($project['tasks'] as $task) { ?>
                <li>
                    <?php echo anchor('task/edit/'.$project['id'].'/'.$task['id'], '#'.$task['id'].' - '.$task['title']); ?>
                    (<?php echo $status[$task['status']] ?>)
                </li>
                <?php } ?>
                <?php } else { ?>
                No tasks here!
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>