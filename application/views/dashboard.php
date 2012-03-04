<?php
// Load Menu
$this->template->menu('dashboard');
?>

<div id="container">
    <?php if(isset($projects)) { ?>
    <?php foreach ($projects as $project) { ?>
        <?php echo $project->name; ?>
    <?php } ?>
    <?php } ?>
</div>