<?php
// Load Menu
$this->template->menu('task_edit');
?>

<div id="container">

    <?php echo form_open('task/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Title *', 'title'); ?>
            </td>
            <td>
                <?php echo form_input('title', set_value('title', $title), 'maxlength="50"'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Parent', 'parent_id'); ?>
            </td>
            <td>
                <?php
                $options = array();
                $options[0] = '- None -';
                foreach ($tasks as $value) {
                    $options[$value['id']] = $value['title'];
                }
                echo form_dropdown('parent_id', $options, set_value('parent_id', $parent_id));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Priority', 'priority'); ?>
            </td>
            <td>
                <?php $options = array('0' => 'Very High', '1' => 'High', '2' => 'Normal', '3' => 'Low', '4' => 'Very Low');
                      echo form_dropdown('priority', $options, set_value('priority', $priority)); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Description', 'text'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'description',
                              'id'          => 'description',
                              'value'       => set_value('description', $description),
                              'rows'        => '6',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Assigned to', 'user_id'); ?>
            </td>
            <td>
                <?php
                $options = array();
                foreach ($users as $value) {
                    $options[$value['id']] = $value['email'];
                }
                echo form_dropdown('user_id', $options, set_value('user_id', $user_id));
                ?>
            </td>
        </tr>
        <?php if (isset($id)) { ?>
        <tr>
            <td>
                <?php echo form_label('Files changed', 'files'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'files',
                              'id'          => 'files',
                              'value'       => set_value('files', $files),
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Database changes', 'database'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'database',
                              'id'          => 'database',
                              'value'       => set_value('database', $database),
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <?php } ?>
        <?php if(isset($error)) : ?>
        <tr>
            <td colspan="2" class="error">
                <?php echo validation_errors(); ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
                <td colspan="2">
                    <?php if (isset($task_id)) echo form_hidden('task_id', $task_id); ?>
                    <?php if (isset($status)) echo form_hidden('status', $status); ?>
                    <?php if (isset($project_id)) echo form_hidden('project_id', $project_id); ?>

                    <div class="form-save-buttons">
                        <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                        <?php echo form_submit('cancel', 'Cancel', 'class="btn-blue"');; ?>
                        <?php
                        if(isset($task_id)) {
                            $remove_url = base_url().'task/remove/'.$project_id.'/'.$task_id;
                            echo form_button('remove', 'Remove', 'class="btn-blue" id="remove-task" target-url="'.$remove_url.'"');
                        } ?>
                    </div>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>