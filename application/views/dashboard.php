<?php
// Load Menu
$this->template->menu('dashboard');
?>

<div id="container">
    <?php if(isset($tasks)) { ?>
    <div id="tasks">
    <?php foreach ($tasks as $task) { ?>
        #<?php echo $task->id; ?> <?php echo $task->title; ?>
    <?php } ?>
    </div>
    <?php } ?>
    
    <?php if(isset($projects)) { ?>
    <div id="projects">
    <?php foreach ($projects as $project) { ?>
        <?php echo anchor('project/tasks/'.$project->id, $project->name); ?>
    <?php } ?>
    </div>
    <?php } ?>
</div>