<h2><?php echo $title; ?></h2>
<div class="row-fluid">
    <div class="span8">
        <?php if($parent_tasks) : ?>
        <ul class="breadcrumb">
            <?php foreach($parent_tasks as $key => $value): ?>
            <li>
                <?php echo anchor(base_url()."task/view/{$project_id}/{$value['id']}", $value['title']); ?>
                <span class="divider">&gt;</span>
            </li>
            <?php endforeach; ?>
            <li class="active"><?php echo $title; ?></li>
        </ul>
        <?php endif; ?>

        <p class="task-info-description">
            <p><strong>Description</strong></p>
            <?php echo $description; ?>
        </p>

        <?php if($children_tasks) : ?>
        <p>
            <p><strong>Children</strong></p>
            <?php task_hierarchy_html($project_id, $children_tasks); ?>
        </p>
        <?php endif; ?>
    </div>
    
    <div class="span4 well">
        <p>
            <strong>Assigned To:</strong>
            <em><?php echo anchor('profile/view/'.$user['id'], ($user['name'])?$user['name']:$user['email'], 'class="view-profile-details"'); ?></em>
        </p>
        <p>
            <strong>Priority:</strong>
            <?php echo task_priority_text($priority); ?>
        </p>
        <p>
            <strong>Current Phase:</strong>
            <?php echo $status_arr[$status]; ?>
        </p>
        <?php if($due_date) { ?>
        <p>
            <strong>Due date:</strong>
            <?php echo date('m/d/Y', strtotime($due_date)); ?>
            
            <?php if(($status < 3) && (strtotime(date('Y-m-d')) - strtotime($due_date)) > 0) : ?>
            <span class="label label-important">late</span>
            <?php endif; ?>
        </p>
        <?php } ?>
        <p >
            <?php if($total_duration || $task_history_date_created) { ?>
            <p><strong>Duration:</strong><br/>
                <?php if($task_history_date_created) { ?>
                    <?php echo anchor('task/timer/'.$project_id.'/'.$task_id.'/stop', 'Stop', 'class="task_time_control stop" title="Stop"'); ?>

                    <span class="task_time_value">
                    <?php echo timespan_diff($total_duration + (time() - strtotime($task_history_date_created))); ?>
                    <?php if($status != (count($status_arr) - 1)) { ?>
                    - ongoing
                    <?php } ?>
                    </span>
                <?php } else { ?>
                    <?php echo anchor('task/timer/'.$project_id.'/'.$task_id.'/play', 'Continue', 'class="task_time_control play" title="Continue"'); ?>

                    <span class="task_time_value">
                    <?php echo timespan_diff($total_duration); ?>
                    </span>
                <?php } ?>
            </p>
            <?php } ?>
            <strong>History (<?php echo anchor('task/history/'.$project_id.'/'.$task_id, 'details', 'id="task-history-details"'); ?>):</strong>
        <ul class="task-history">
            <?php if($task_history) { ?>
            <?php foreach ($task_history as $value) { ?>
            <li>
                <?php echo $status_arr[$value['status']]; ?><br/>
                <ul>
                    <li>
                        <em>
                    <?php if($task_history_date_created) { ?>
                    <?php echo timespan_diff($value['duration'] + (time() - strtotime($task_history_date_created))); ?>
                            <?php if($value['status'] != (count($status_arr) - 1)) { ?>
                            - running
                            <?php } ?>
                    <?php } else { ?>
                    <?php echo timespan_diff($value['duration']); ?>
                    <?php } ?>
                        </em>
                    </li>
                </ul>
            </li>
            <?php } ?>
            <?php } else { ?>
            <li><?php echo $status_arr[$status]; ?><br/></li>
            <?php } ?>
        </ul>
        </p>
    </div>
</div>

<div class="well">
<h4>Comments</h4>
<div class="row-fluid">
    <div class="span12">
        <?php if($comments) { ?>
        <?php foreach ($comments as $comment) { ?>
        <div id="task-comment-id-<?php echo $comment['task_comments_id']; ?>" class="well">
            <p><strong><?php echo ($comment['name']?$comment['name']:$comment['email']); ?></strong> <em>(<?php echo $comment['date_created']; ?>)</em></p>
            <?php echo $comment['comment']; ?>
        </div>
        <?php } ?>
        <?php } else { ?>
        No comments here!
        <?php } ?>
    </div>
</div>

<hr/>

<form class="form-horizontal" method="post" action="<?php echo base_url('task/comment'); ?>">

    <div class="row-fluid">
        <div class="span12">
            <textarea name="comment" id="comment" rows="5" class="input-xxlarge" placeholder="New comment"><?php echo set_value('comment'); ?></textarea>
            <button type="submit" name="submit" class="btn btn-success">
                <i class="icon-white icon-ok"></i>
                Submit
            </button>
        </div>
    </div>

    <?php echo form_hidden('project_id', $project_id); ?>
    <?php echo form_hidden('task_id', $task_id); ?>

</form>
</div>