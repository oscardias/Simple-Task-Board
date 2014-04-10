<?php echo validation_errors('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>', '</div>'); ?>

<form class="form-horizontal" method="post" action="<?php echo base_url('profile/save'); ?>" accept-charset="utf-8" enctype="multipart/form-data">
    
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                    <input type="text" class="input-xxlarge" name="name" id="name"
                           placeholder="Name" value="<?php echo set_value('name', $name); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="email">Email *</label>
                <div class="controls">
                    <input type="text" class="input-xxlarge" name="email" id="email"
                           placeholder="Email" value="<?php echo set_value('email', $email); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                    <?php if (isset($id)) : ?>
                    
                        <input type="password" class="input-xxlarge" name="password" id="password" disabled
                               placeholder="Password" value="<?php echo set_value('password', $password); ?>" />
                        <?php echo form_checkbox('reset_password', 1, false, 'id="reset_password" title="Edit Password"'); ?>
                        
                    <?php else : ?>
                        
                        <input type="password" class="input-xxlarge" name="password" id="password"
                               placeholder="Password" value="<?php echo set_value('password', $password); ?>" />
                        <?php echo form_hidden('reset_password', 1); ?>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="links">
                    Links
                    <small>Website, Twitter, Facebook, Linkedin...</small>
                </label>
                <div class="controls">
                    <div id="profile_links">
                    <?php
                    if($links) :
                        $total = count($links);
                        $i = 0;
                    foreach ($links as $link) : ?>
                    <div>
                        <?php echo form_input('links[]', set_value('links[]', $link), 'class="input-xxlarge"'); ?>
                        <a href="#" class="add_link btn btn-small">
                            <?php if(++$i === $total) : ?>
                            <i class="icon-plus"></i>
                            <?php else : ?>
                            <i class="icon-minus"></i>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <div>
                        <?php echo form_input('links[]'); ?>
                        <a href="#" class="add_link btn btn-small">
                            <i class="icon-plus"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="control-group">
                <label class="control-label" for="photo">Photo</label>
                <div class="controls">
                    <div id="profile_photo">
                        <?php if($photo) : ?>
                        <img src="<?php echo base_url().$photo; ?>" title="Your photo - click to change" />
                        <?php else : ?>
                        <img src="<?php echo base_url().'assets/img/profile/default128.png'; ?>" title="Your photo - click to change" />
                        <?php endif; ?>
                    </div>
                    <?php echo form_upload('photo'); ?>
                </div>
            </div>
        </div>
    </div>

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