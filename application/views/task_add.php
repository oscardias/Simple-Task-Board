<?php
// Load Menu
$this->template->menu('return_to_tasks');
?>

<div id="container">

    <?php echo form_open('task/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Title', 'title'); ?>
            </td>
            <td>
                <?php echo form_input('title', $title, 'maxlength="50"'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Priority', 'priority'); ?>
            </td>
            <td>
                <?php $options = array('0' => 'Very High', '1' => 'High', '2' => 'Normal', '3' => 'Low', '4' => 'Very Low');
                      echo form_dropdown('priority', $options, $priority); ?>
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
                              'value'       => $description,
                              'rows'        => '6',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Assigned to', 'user'); ?>
            </td>
            <td>
                <?php
                $options = array();
                foreach ($users as $value) {
                    $options[$value['id']] = $value['email'];
                }
                echo form_dropdown('user', $options, $user);
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
                              'value'       => $files,
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
                              'value'       => $database,
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
                <td colspan="2">
                    <?php if (isset($id)) echo form_hidden('id', $id); ?>
                    <?php if (isset($status)) echo form_hidden('status', $status); ?>
                    <?php if (isset($project)) echo form_hidden('project', $project); ?>

                    <div class="form-save-buttons">
                        <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                        <?php echo form_button('cancel', 'Cancel', 'class="btn-blue" onClick="history.go(-1)"');; ?>
                        <?php
                        $remove_url = base_url().'task/remove/'.$project.'/'.$id;
                        echo form_button('remove', 'Remove', 'class="btn-blue" id="remove-task" target-url="'.$remove_url.'"'); ?>
                    </div>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>