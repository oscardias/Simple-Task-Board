<?php
// Load Menu
$this->template->menu('profile');
?>

<div id="container">

    <?php $this->load->view('profile_details', array('user' => $user)); ?>        

</div>