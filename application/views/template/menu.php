<div class="home-title blue-gradient">Simple Task Board</div>
<div id="menu">
    <?php if($view == 'dashboard') { ?>
        <!-- Dashboard menu -->
        <?php echo anchor('user', 'Edit Users', 'class="btn btn_users"'); ?>
        <?php echo anchor('project/add', 'Add new project', 'class="btn btn_add"'); ?>
    <?php } elseif($view == 'task_board') { ?>
        <!-- Task board menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php echo anchor('project/edit/'.$project, 'Edit project', 'class="btn btn_edit"'); ?>
        <?php echo anchor('task/add', 'Add new task', 'class="btn btn_add"'); ?>
        <?php //echo anchor('transport/add', 'Create new transport', 'class="btn btn_transport"'); ?>
    <?php } elseif($view == 'projects') { ?>
        <!-- Users menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
    <?php } elseif($view == 'users') { ?>
        <!-- Users menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php echo anchor('user/add', 'Add new user', 'class="btn btn_add"'); ?>
    <?php } ?>
    <?php echo anchor('login/logout', 'Logout', 'class="btn btn_logout"'); ?>
    <div class="clear"></div>
</div>
<div id="page-title"><?php echo $page_title; ?></div>