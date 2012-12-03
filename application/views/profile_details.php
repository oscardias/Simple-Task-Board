<div id="profile-info" title="User Profile">
    <div class="profile-photo">
        <?php if($user['photo']) : ?>
        <img src="<?php echo base_url().$user['photo']; ?>" title="Your photo - click to change" />
        <?php else : ?>
        <img src="<?php echo base_url().'images/profile/default128.png'; ?>" title="Your photo - click to change" />
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
