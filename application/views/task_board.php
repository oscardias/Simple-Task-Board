<?php
// Load Menu
$this->template->menu('task_board');
?>

<div id="container">
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
                <?php
                if(isset($stories)) {
                    // Load Tasks
                    $this->template->tasks($project, $stories);
                } ?>
            </td>
            <td>
                <?php if(isset($tasks)) {
                    // Load Tasks
                    $this->template->tasks($project, $tasks);
                } ?>
            </td>
            <td>
                <?php if(isset($tests)) {
                    // Load Tasks
                    $this->template->tasks($project, $tests);
                } ?>
            </td>
            <td>
                <?php if(isset($done)) {
                    // Load Tasks
                    $this->template->tasks($project, $done);
                } ?>
            </td>
        </tr>

    </table>
    <?php } else { ?>
    <div class="notice">This project doesn't have any task.</div>
    <?php } ?>
</div>