<div class="home-title blue-gradient">Simple Task Board</div>
<div id="menu">
    <?php if($view == 'dashboard') { ?>
        <!-- Dashboard menu -->
        <?php
        if($this->usercontrol->has_permission('user'))
            echo anchor('user', 'Edit Users', 'class="btn btn_users"');
        ?>
        <?php
        if($this->usercontrol->has_permission('project'))
            echo anchor('project/add', 'Add new project', 'class="btn btn_add"');
        ?>
    <?php } elseif($view == 'task_board') { ?>
        <!-- Task board menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('project'))
            echo anchor('project/edit/'.$project_id, 'Edit project', 'class="btn btn_edit"');
        ?>
        <?php
        if($this->usercontrol->has_permission('task'))
            echo anchor('task/add/'.$project_id, 'Add new task', 'class="btn btn_add"');
        ?>
    <?php } elseif($view == 'task_view') { ?>
        <!-- Task View menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('project', 'tasks'))
            echo anchor('project/tasks/'.$project_id, 'Task Board', 'class="btn btn_taskboard"');
        ?>
        <?php
        if($this->usercontrol->has_permission('task'))
            echo anchor('task/edit/'.$project_id.'/'.$task_id, 'Edit Task', 'class="btn btn_edit"');
        ?>
    <?php } elseif($view == 'task_history') { ?>
        <!-- Task History menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('project', 'task'))
            echo (isset($task_id))?anchor('task/view/'.$project_id.'/'.$task_id, 'View Task', 'class="btn btn_taskboard"'):'';
        ?>
    <?php } elseif($view == 'task_edit') { ?>
        <!-- Task Edit menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('project', 'task'))
            echo anchor('project/tasks/'.$project_id, 'Task Board', 'class="btn btn_taskboard"');
        ?>
        <?php
        if($this->usercontrol->has_permission('project', 'task'))
            echo (isset($task_id))?anchor('task/view/'.$project_id.'/'.$task_id, 'View Task', 'class="btn btn_taskboard"'):'';
        ?>
    <?php } elseif($view == 'projects') { ?>
        <!-- Projects menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
    <?php } elseif($view == 'project_edit') { ?>
        <!-- Task Edit menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('project', 'task'))
            echo anchor('project/tasks/'.$project_id, 'Task Board', 'class="btn btn_taskboard"');
        ?>
    <?php } elseif($view == 'users') { ?>
        <!-- Users menu -->
        <?php echo anchor('dashboard', 'Dashboard', 'class="btn btn_dashboard"'); ?>
        <?php
        if($this->usercontrol->has_permission('user'))
            echo anchor('user/add', 'Add new user', 'class="btn btn_add"');
        ?>
    <?php } ?>
    <?php echo anchor('login/logout', 'Logout', 'class="btn btn_logout"'); ?>
    <div class="clear"></div>
</div>
<div id="page-title">
    <?php echo $page_title; ?>
    <?php if($view == 'task_board') { ?>
    <span id="switch-project-view" class="global-tasks btn" title="Show all"></span>
    <?php } ?>
</div>