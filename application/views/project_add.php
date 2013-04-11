<?php echo validation_errors('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>', '</div>'); ?>
    
<form class="form-horizontal" method="post" action="<?php echo base_url('project/save'); ?>">
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="name">Name *</label>
                <div class="controls">
                    <input type="text" class="input-xxlarge" name="name" id="name" placeholder="Name" value="<?php echo set_value('name', $name); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="description">Description *</label>
                <div class="controls">
                    <textarea name="description" id="description" class="input-xxlarge" rows="4">
                    <?php echo set_value('description', $description); ?>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label">Associated Users</label>
                <div class="controls">
                    <?php foreach ($users as $user) { ?>
                    <div class="half-width">
                        <label>
                        <?php echo form_checkbox('users[]', $user['id'], set_checkbox('users[]', $user['id'], ($user['project'])?1:0)); ?>
                        <?php echo $user['email']; ?></label>
                    </div>
                    <?php } ?>
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
