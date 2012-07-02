<?php
// Load Menu
$this->template->menu('users');
?>

<div id="container">

    <?php echo form_open('user/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Email', 'email'); ?>
            </td>
            <td>
                <?php echo form_input('email', $email); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Password', 'password'); ?>
            </td>
            <td>
                <?php if (isset($id)) { ?>
                    <?php echo form_password('password', $password, 'id="password" disabled'); ?>
                    <?php echo form_checkbox('reset_password', 1, false, 'id="reset_password" title="Edit Password"'); ?>
                <?php } else { ?>
                    <?php echo form_password('password', $password, 'id="password"'); ?>
                    <?php echo form_hidden('reset_password', 1); ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Level', 'level'); ?>
            </td>
            <td>
                <?php echo form_dropdown('level', $level_list, $level); ?>
            </td>
        </tr>
        <tr>
                <td colspan="2">
                    <?php if (isset($id)) echo form_hidden('id', $id); ?>
                    <div class="form-save-buttons">
                        <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                        <?php echo form_button('cancel', 'Cancel', 'class="btn-blue" onClick="history.go(-1)"');; ?>
                    </div>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>