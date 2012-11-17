<div title="Task History">
    <table class="history">
        <thead>
            <tr>
                <td class="blue-gradient">Phase</td>
                <td class="blue-gradient">User</td>
                <td class="blue-gradient">Started</td>
                <td class="blue-gradient">Finished</td>
                <td class="blue-gradient">Duration (sec)</td>
            </tr>
        <thead>
        <tbody>
    <?php foreach ($task_history as $value) { ?>
        <tr>
            <td><?php echo $status_arr[$value['status']]; ?></td>
            <td><?php echo ($value['email'])?$value['email']:'-'; ?></td>
            <td><?php echo $value['date_created']; ?></td>
            <td><?php echo ($value['date_finished'])?$value['date_finished']:'-'; ?></td>
            <td><?php echo $value['duration']; ?></td>
        </tr>
    <?php } ?>
        </tbody>
    </table>
</div>