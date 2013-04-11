<?php if(isset($stories) || isset($tasks) || isset($tests) || isset($done)) { ?>
<table class="table">
    <tr>
        <th width="25%">To Do</th>
        <th width="25%">In Progress</th>
        <th width="25%">Testing</th>
        <th width="25%">Done</th>
    </tr>
    <tr>
        <td>
            <?php
            if(isset($stories)) {
                $i = 0;
                foreach ($stories as $value) {
                    $i++;
                    $this->load->view('templates/single_task', array('i' => $i, 'project' => $project_id, 'task' => $value));
                }
            }
            ?>
        </td>
        <td>
            <?php
            if(isset($tasks)) {
                $i = 0;
                foreach ($tasks as $value) {
                    $i++;
                    $this->load->view('templates/single_task', array('i' => $i, 'project' => $project_id, 'task' => $value));
                }
            }
            ?>
        </td>
        <td>
            <?php
            if(isset($tests)) {
                $i = 0;
                foreach ($tests as $value) {
                    $i++;
                    $this->load->view('templates/single_task', array('i' => $i, 'project' => $project_id, 'task' => $value));
                }
            }
            ?>
        </td>
        <td>
            <?php
            if(isset($done)) {
                $i = 0;
                foreach ($done as $value) {
                    $i++;
                    $this->load->view('templates/single_task', array('i' => $i, 'project' => $project_id, 'task' => $value));
                }
            }
            ?>
        </td>
    </tr>

</table>
<?php } else { ?>
<div class="alert">This project doesn't have any task.</div>
<?php } ?>