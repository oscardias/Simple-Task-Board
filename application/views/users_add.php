<?php echo validation_errors('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>', '</div>'); ?>
    
<form class="form-horizontal" method="post" action="<?php echo base_url('user/save'); ?>">
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="email">Email *</label>
                <div class="controls">
                    <input type="text" class="input-xxlarge" name="email" id="email" placeholder="Email" value="<?php echo set_value('email', $email); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="password">Password *</label>
                <div class="controls">
                    <?php if (isset($id)) { ?>
                        <input type="password" class="input-xxlarge" name="password" id="password" placeholder="Password" value="<?php echo set_value('password', $password); ?>" disabled />
                        <?php echo form_checkbox('reset_password', 1, false, 'id="reset_password" title="Edit Password"'); ?>
                    <?php } else { ?>
                        <input type="password" class="input-xxlarge" name="password" id="password" placeholder="Password" value="<?php echo set_value('password', $password); ?>" />
                        <?php echo form_hidden('reset_password', 1); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="level">Level *</label>
                <div class="controls">
                    <?php echo form_dropdown('level', $level_list, set_value('level', $level), 'class="input-xxlarge"'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <hr/>
    
    <?php if (isset($id)) echo form_hidden('id', $id); ?>
    <div class="btn-group btn-center">
        <button type="submit" name="save" class="btn btn-success">
            <i class="icon-white icon-ok"></i>
            Save
        </button>
        <button type="submit" name="cancel" class="btn btn-warning">
            <i class="icon-white icon-remove"></i>
            Cancel
        </button>
    </div>

</form>
