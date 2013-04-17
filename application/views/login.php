    <?php if(isset($answer)) { ?>
    <div class="alert alert-success"><?php echo $answer; ?></div>
    <?php } ?>
    
    <?php if(isset($already_installed) && $already_installed) { ?>
        <?php if(isset($update_database) && $update_database) { ?>
            <form class="form-signin" action="<?php echo base_url('install/database'); ?>" method="post">
                <button class="btn btn-primary" type="upgrade">Upgrade</button>
            </form>
        <?php } else { ?>
            <div class="alert">Simple Task Board has already been installed.</div>
        <?php } ?>
    <?php } else { ?>
        <form class="form-signin" method="post"
              action="<?php echo (isset($already_installed) && !$already_installed)?base_url('install/run'):base_url('login/validate'); ?>">
            <input name="email" type="text" class="input-block-level" placeholder="Email" value="<?php echo set_value('email'); ?>">
            <input name="password" type="password" class="input-block-level" placeholder="Password" value="<?php echo set_value('password'); ?>">
            <?php if(isset($error) && $error) : ?>
                <div class="alert alert-error">That's not right! Please check your information and try again.</div>
            <?php endif; ?>
            <?php if(isset($already_installed) && !$already_installed) : ?>
                <button class="btn btn-primary" type="install">Install</button>
            <?php else : ?>
                <button class="btn btn-primary" type="login">Login</button>
            <?php endif; ?>
        </form>
    <?php } ?>