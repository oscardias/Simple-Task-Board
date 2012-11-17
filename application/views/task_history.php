<?php
// Load Menu
$this->template->menu('task_history');
?>

<div id="container">

    <div class="task-info">
        <p class="task-info-title">
            <?php echo $title; ?>
        </p>
        <?php $this->load->view('task_history_details', array('task_history' => $task_history)) ?>
        
    </div>

</div>