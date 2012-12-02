<?php
// Load Menu
$this->template->menu('users');
?>

<div id="container">

    <?php echo form_open('user/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Email *', 'email'); ?>
            </td>
            <td>
                <?php echo form_input('email', set_value('email', $email)); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Password', 'password'); ?>
            </td>
            <td>
                <?php if (isset($id)) { ?>
                    <?php echo form_password('password', set_value('password', $password), 'id="password" disabled'); ?>
                    <?php echo form_checkbox('reset_password', 1, false, 'id="reset_password" title="Edit Password"'); ?>
                <?php } else { ?>
                    <?php echo form_password('password', set_value('password', $password), 'id="password"'); ?>
                    <?php echo form_hidden('reset_password', 1); ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Level *', 'level'); ?>
            </td>
            <td>
                <?php echo form_dropdown('level', $level_list, set_value('level', $level)); ?>
            </td>
        </tr>
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
                        <?php echo form_submit('cancel', 'Cancel', 'class="btn-blue"');; ?>
                    </div>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>