<?php
// Load Menu
$this->template->menu('task_board');
?>

<div id="container">
    <?php if(isset($transports)) { ?>
        <div class="transport">
        <?php foreach ($transports as $transport) { ?>
            <p id="transport_<?php echo $transport['id']; ?>" class="transport_info"><?php
            echo anchor('transport/edit/'.$transport['id'], 'Transport #'.$transport['id']).' - ';
            $task_list = explode(",", $transport['tasks']);
            $files = explode(",", $transport['files']);
            $database = explode(",", $transport['database']);
            echo "Contents: ".
                (($transport['tasks'])?count($task_list):0)." tasks, ".
                (($transport['files'])?count($files):0)." files changed, ".
                (($transport['database'])?count($database):0)." tables or fields updated. ".
                anchor('transport/close/'.$transport['id'], 'Close'); ?></p>
        <?php } ?>
        </div>
    <?php } ?>

    <?php if(isset($stories) || isset($tasks) || isset($tests) || isset($done)) { ?>
    <table class="board">
        <tr>
            <th class="blue-gradient">To Do</th>
            <th class="blue-gradient">In Progress</th>
            <th class="blue-gradient">Testing</th>
            <th class="blue-gradient">Done</th>
        </tr>
        <tr>
            <td>
                <?php if(isset($stories)) {
                      $i = 0; ?>
                <?php foreach ($stories as $task) {
                      $i++; ?>
                <div id="task_<?php echo $task['id']; ?>" <?php if($i >= 3) { ?>title="<?php echo $task['description']; ?>"<?php } ?>
                     class="task <?php echo ($task['user'] == $current_user)?'my-task':'project-task'; ?>">
                    <p class="task_id">#<?php echo $task['id']; ?></p>
                    <p class="task_title"><?php echo anchor('task/edit/'.$project.'/'.$task['id'], $task['title']); ?></p>
                    <?php if($i < 3) { ?>
                    <p class="task_text"><?php echo word_limiter($task['description'], 30); ?></p>
                    <?php } ?>
                    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/1', 'Start &raquo;'); ?></p>
                </div>
                <?php } ?>
                <?php } ?>
            </td>
            <td>
                <?php if(isset($tasks)) { ?>
                <?php foreach ($tasks as $task) { ?>
                <div id="task_<?php echo $task['id']; ?>" class="task <?php echo ($task['user'] == $current_user)?'my-task':'project-task'; ?>">
                    <p class="task_id">#<?php echo $task['id']; ?></p>
                    <p class="task_title"><?php echo anchor('task/edit/'.$project.'/'.$task['id'], $task['title']); ?></p>
                    <p class="task_text"><?php echo word_limiter($task['description'], 30); ?></p>
                    <p class="task_info"><?php
                        $files = explode(",",$task['files']);
                        $database = explode(",",$task['database']);
                        echo "Changes: ".(($task['files'])?count($files):0)." files, ".(($task['database'])?count($database):0)." tables/fields"; ?></p>
                    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/0', '&laquo; Back'); ?> |
                        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/2', 'Test &raquo;'); ?></p>
                </div>
                <?php } ?>
                <?php } ?>
            </td>
            <td>
                <?php if(isset($tests)) { ?>
                <?php foreach ($tests as $task) { ?>
                <div id="task_<?php echo $task['id']; ?>" class="task <?php echo ($task['user'] == $current_user)?'my-task':'project-task'; ?>" title="<?php echo $task['description']; ?>">
                    <p class="task_id">#<?php echo $task['id']; ?></p>
                    <p class="task_title"><?php echo anchor('task/edit/'.$project.'/'.$task['id'], $task['title']); ?></p>
                    <!--<p class="task_text"><?php echo word_limiter($task['description'], 30); ?></p>-->
                    <p class="task_info"><?php
                        $files = explode(",",$task['files']);
                        $database = explode(",",$task['database']);
                        echo "Changes: ".(($task['files'])?count($files):0)." files, ".(($task['database'])?count($database):0)." tables/fields"; ?></p>
                    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/1', '&laquo; Back'); ?> |
                        <?php echo anchor('task/move/'.$project.'/'.$task['id'].'/3', 'Finish &raquo;'); ?></p>
                </div>
                <?php } ?>
                <?php } ?>
            </td>
            <td>
                <?php if(isset($done)) { ?>
                <?php foreach ($done as $task) { ?>
                <div id="task_<?php echo $task['id']; ?>" class="task <?php echo ($task['user'] == $current_user)?'my-task':'project-task'; ?>" title="<?php echo $task['description']; ?>">
                    <p class="task_id">#<?php echo $task['id']; ?></p>
                    <p class="task_title"><?php echo anchor('task/edit/'.$project.'/'.$task['id'], $task['title']); ?></p>
                    <!--<p class="task_text"><?php echo word_limiter($task['description'], 30); ?></p>-->
                    <p class="task_links"><?php echo anchor('task/move/'.$project.'/'.$task['id'].'/2', '&laquo; Back'); ?></p>
                </div>
                <?php } ?>
                <?php } ?>
            </td>
        </tr>

    </table>
    <?php } else { ?>
    <div class="notice">This project doesn't have any task.</div>
    <?php } ?>
</div>