<?php
// Load Menu
$this->template->menu('users');
?>

<div id="container">
    <?php if(isset($users)) { ?>
        <table id="users_table" class="board">
            <tr>
                <th class="blue-gradient">ID</th>
                <th class="blue-gradient">Email</th>
                <th class="blue-gradient">Level</th>
                <th class="blue-gradient">Date Created</th>
                <th class="blue-gradient">Actions</th>
            </tr>
        <?php foreach ($users as $user) { ?>
            <tr id="user_<?php echo $user['id']; ?>" class="darker-on-hover">
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $level_list[$user['level']]; ?></td>
                <td><?php echo date("j/M/Y, g:i a", strtotime($user['date_created'])); ?></td>
                <td>
                    <?php echo anchor('user/edit/'.$user['id'], '<img src="images/edit.png" title="Edit User"/>'); ?>
                    <?php echo anchor('user/remove/'.$user['id'], '<img src="images/remove.png" title="Remove User"/>', 'class="remove-user-event"'); ?>
                </td>
            </tr>
        <?php } ?>
        </div>
    <?php } ?>
</div>
