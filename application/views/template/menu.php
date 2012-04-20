<div class="home-title blue-gradient">Simple Task Board</div>
<div id="menu">
    <?php if($view == 'dashboard') { ?>
        <!-- Dashboard menu -->
        <?php echo anchor('user', 'Edit Users', 'class="btn btn_users"'); ?>
    <?php } elseif($view == 'task_board') { ?>
        <!-- Task board menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php echo anchor('task/add', 'Add new task', 'class="btn btn_add"'); ?>
        <?php echo anchor('transport/add', 'Create new transport', 'class="btn btn_transport"'); ?>
    <?php } ?>
    <?php echo anchor('login/logout', 'Logout', 'class="btn btn_logout"'); ?>
    <div class="clear"></div>
</div>
