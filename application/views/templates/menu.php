<div class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo base_url(); ?>">Simple Task Board</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    
                    <?php if(strpos($menu, 'dashboard') !== FALSE) : ?>
                        <!-- Dashboard btn -->
                        <li <?php if($controller == 'dashboard') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('dashboard'); ?>">
                                <i class="icon-bar"></i>
                                Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'users') !== FALSE && $this->usercontrol->has_permission('user')) : ?>
                        <!-- Users btn -->
                        <li <?php if($controller == 'user') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('user'); ?>">
                                <i class="icon-bar"></i>
                                Edit Users
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'add_user') !== FALSE && $this->usercontrol->has_permission('user')) : ?>
                        <!-- Users btn -->
                        <li <?php if($controller == 'user') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('user'); ?>">
                                <i class="icon-bar"></i>
                                Add New User
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'new_project') !== FALSE && $this->usercontrol->has_permission('project')) : ?>
                        <!-- Add project btn -->
                        <li <?php if($controller == 'project') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('project'); ?>">
                                <i class="icon-bar"></i>
                                Add New Project
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'edit_project') !== FALSE && $this->usercontrol->has_permission('project')) : ?>
                        <!-- Edit project btn -->
                        <li <?php if($controller == 'project') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('project/edit/'.$project_id); ?>">
                                <i class="icon-bar"></i>
                                Edit Project
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'new_task') !== FALSE && $this->usercontrol->has_permission('task')) : ?>
                        <!-- Add task btn -->
                        <li <?php if($controller == 'task') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('task/add/'.$project_id); ?>">
                                <i class="icon-bar"></i>
                                Add New Task
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'edit_task') !== FALSE && $this->usercontrol->has_permission('task')) : ?>
                        <!-- Edit task btn -->
                        <li <?php if($controller == 'task') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('task/edit/'.$project_id.'/'.$task_id); ?>">
                                <i class="icon-bar"></i>
                                Add New Task
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'tasks') !== FALSE && $this->usercontrol->has_permission('project', 'tasks')) : ?>
                        <!-- View task board btn -->
                        <li <?php if($controller == 'project') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('project/tasks/'.$project_id); ?>">
                                <i class="icon-bar"></i>
                                Add New Task
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(strpos($menu, 'view_task') !== FALSE && $this->usercontrol->has_permission('project', 'task')) : ?>
                        <!-- View task board btn -->
                        <li <?php if($controller == 'project') echo 'class="active"'; ?>>
                            <a href="<?php echo base_url('task/view/'.$project_id.'/'.$task_id); ?>">
                                <i class="icon-bar"></i>
                                Add New Task
                            </a>
                        </li>
                    <?php endif; ?>
                        
                </ul>
                
                <?php if(strpos($menu, 'none') === FALSE) : ?>
                <ul class="nav pull-right">
                    <li <?php if($controller == 'profile') echo 'class="active"'; ?>>
                        <a href="<?php echo base_url('profile'); ?>">
                            <i class="icon-bar"></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo base_url('login/logout'); ?>">
                            <i class="icon-bar"></i>
                            Logout
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!--<div id="page-title">
    <?php //echo $page_title; ?>
    <?php //if($view == 'task_board') { ?>
    <span id="switch-project-view" class="global-tasks btn" title="Show all"></span>
    <?php //} ?>
</div>-->