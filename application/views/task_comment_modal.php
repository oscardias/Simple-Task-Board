<div id="task-comment-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Move Task</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" id="task-comment-modal-form" method="post" action="<?php echo base_url("task/ajax_comment/$project_id/$task_id/$status"); ?>">
            <div class="row-fluid">
                <div class="span12">
                    Assign to: 
                    <?php
                    $options = array();
                    foreach ($users as $value) {
                        $options[$value['id']] = $value['email'];
                    }
                    echo form_dropdown('user_id', $options, set_value('user_id', $user_id));
                    ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <textarea name="comment" id="comment" rows="5" class="input-xxlarge" placeholder="your comment (optional)"></textarea>
                </div>
            </div>
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
            <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
        </form>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="task-comment-submit">Continue</button>
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </div>
</div>
