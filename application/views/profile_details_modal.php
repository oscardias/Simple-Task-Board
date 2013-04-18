<div id="profile-info" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo ($user['name'])?$user['name']:$user['email']; ?></h3>
    </div>
    <div class="modal-body">
        <div class="profile-photo">
            <?php if($user['photo']) : ?>
            <img src="<?php echo base_url().$user['photo']; ?>" />
            <?php else : ?>
            <img src="<?php echo base_url().'images/profile/default128.png'; ?>" />
            <?php endif; ?>
        </div>
        <div class="profile-details">
            <?php if($user['name']) : ?>
            <div><strong>Name:</strong> <?php echo $user['name']; ?></div>
            <?php endif; ?>
            <div><strong>Email:</strong> <?php echo mailto($user['email']); ?></div>
            <?php if($user['links']) : ?>
            <div>
                <strong>Links:</strong>
                    <div id="profile-links">
                        <ul>
                        <?php
                            $total = count($user['links']);
                            $i = 0;
                        foreach ($user['links'] as $link) : ?>
                        <li>
                            <?php echo anchor($link); ?>
                            <?php echo (++$i === $total)?'':'<br/>'; ?>
                        </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>
