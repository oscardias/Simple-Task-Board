<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Task Board | <?php if (isset($id)) { echo "Edit Task #".$id; } else { echo "New Task"; } ?></title>

	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/style.css" />
    <!--[if IE]>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>css/styleIE.css" />
    <![endif]-->
        
</head>
<body>