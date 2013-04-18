<div id="task-history-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Task History</h3>
    </div>
    <div class="modal-body">
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
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>
