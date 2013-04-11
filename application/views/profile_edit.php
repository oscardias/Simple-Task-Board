<?php echo form_open_multipart('profile/save'); ?>

<table>
    <tr>
        <td>
            <?php echo form_label('Name', 'name'); ?>
        </td>
        <td>
            <?php echo form_input('name', set_value('name', $name)); ?>
        </td>
    </tr>
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
            <?php echo form_label('Links', 'links'); ?>
            <small>Website, Twitter, Facebook, Linkedin...</small>
        </td>
        <td>
            <div id="profile_links">
            <?php
            if($links) :
                $total = count($links);
                $i = 0;
            foreach ($links as $link) : ?>
            <div>
                <?php echo form_input('links[]', set_value('links[]', $link)); ?>
                <span class="add_link"><?php echo (++$i === $total)?'(+)':'(-)'; ?></span>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <div>
                <?php echo form_input('links[]'); ?>
                <span class="add_link">(+)</span>
            </div>
            <?php endif; ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo form_label('Photo', 'photo'); ?>
        </td>
        <td>
            <div id="profile_photo">
                <?php if($photo) : ?>
                <img src="<?php echo base_url().$photo; ?>" title="Your photo - click to change" />
                <?php else : ?>
                <img src="<?php echo base_url().'images/profile/default128.png'; ?>" title="Your photo - click to change" />
                <?php endif; ?>
            </div>
            <?php echo form_upload('photo'); ?>
        </td>
    </tr>
    <?php if(isset($error)) : ?>
    <tr>
        <td colspan="2" class="error">
            <?php echo ($error === true)?validation_errors():$error; ?>
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
