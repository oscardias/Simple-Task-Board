<?php
// Load Menu
if (isset($id))
    $this->template->menu('project_edit');
else
    $this->template->menu('projects');
?>

<div id="container">

    <?php echo form_open('project/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Name *', 'name'); ?>
            </td>
            <td>
                <?php echo form_input('name', set_value('name', $name)); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Description', 'description'); ?>
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
            <td colspan="2">
                <div class="project-users blue-gradient">
                    Associated Users
                    <span id="show-hide-users" class="expand" target-id="associated-users"></span>
                </div>
            </td>
        </tr>
        <tbody id="associated-users">
        <tr>
            <td colspan="2">
            <?php $counter = 0; ?>
            <?php foreach ($users as $user) { ?>
            <div class="half-width">
                <label>
                <?php echo form_checkbox('users[]', $user['id'], set_checkbox('users[]', $user['id'], ($user['project'])?1:0)); ?>
                <?php echo $user['email']; ?></label>
                <?php //echo form_label($user['email'], 'users[]'); ?>
            </div>
                <?php $counter++; ?>
                <?php if($counter % 2 == 0) { ?>
                <!--</tr><tr>-->
                <?php } ?>
            <?php } ?>
            </td>
        </tr>
        </tbody>
        <?php if(isset($error)) : ?>
        <tr>
            <td colspan="2" class="error">
                <?php echo validation_errors(); ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
                <td colspan="2">
                    <?php if (isset($id)) echo form_hidden('id', $id); ?>

                    <div class="form-save-buttons">
                        <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                        <?php echo form_submit('cancel', 'Cancel', 'class="btn-blue"'); ?>
                    </div>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>