<div class="home-title blue-gradient">Simple Task Board</div>
<div id="login">
    <?php if(isset($answer)) { ?>
    <div class="install-answer"><?php echo $answer; ?></div>
    <?php } ?>
    
    <?php if(isset($already_installed) && $already_installed) { ?>
        <?php if(isset($update_database) && $update_database) { ?>
            <p>Your database needs to be upgraded.</p>
            <?php echo form_open('install/database'); ?>

            <?php echo form_submit('upgrade', 'Upgrade', 'class="btn-blue"'); ?>

            <?php echo form_close(); ?>
        <?php } else { ?>
            Simple Task Board has already been installed.
        <?php } ?>
    <?php } else { ?>
        <?php if(isset($already_installed) && !$already_installed) { echo form_open('install/run'); }
        else { echo form_open('login/validate'); } ?>

        <?php echo form_label('Email', 'email'); ?>
        <?php echo form_input('email', $email); ?>
        <br/>
        <?php echo form_label('Password', 'password'); ?>
        <?php echo form_password('password', $password); ?>
        
        <?php if(isset($error) && $error) { ?>
        <p class="error">That's not right! Please check your information and try again.</p>
        <?php } ?>

        <?php if(isset($already_installed) && !$already_installed) { echo form_submit('install', 'Install', 'class="btn-blue"'); }
        else { echo form_submit('login', 'Login', 'class="btn-blue"'); } ?>

        <?php echo form_close(); ?>
    <?php } ?>
</div>