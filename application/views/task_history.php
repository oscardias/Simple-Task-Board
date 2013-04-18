<h2><?php echo $title; ?></h2>
<table class="table table-bordered table-striped">
        <tr>
            <th>Phase</th>
            <th>User</th>
            <th>Started</th>
            <th>Finished</th>
            <th>Duration</th>
        </tr>
<?php foreach ($task_history as $value) { ?>
    <tr>
        <td><?php echo $status_arr[$value['status']]; ?></td>
        <td><?php echo ($value['email'])?$value['email']:'-'; ?></td>
        <td><?php echo $value['date_created']; ?></td>
        <td><?php echo ($value['date_finished'])?$value['date_finished']:'-'; ?></td>
        <td><?php echo ($value['duration']?timespan_diff($value['duration']):''); ?></td>
    </tr>
<?php } ?>
</table>
