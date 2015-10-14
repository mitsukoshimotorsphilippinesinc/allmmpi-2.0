<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?= $title; ?></title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<?php echo css('jquery-ui/jquery-ui-1.9.1.css');?>
	<?php echo css('base.css');?>
	<?php echo css('main.css');?>
	<?php echo css('google-code-prettify/prettify.css');?>
	<?php echo css('simple-sidebar/css/simple-sidebar.css');?>	
	<?php echo css('font-awesome-4.4.0/css/font-awesome.min.css');?>		
	<?php echo css('/assets/css/tablesorter.css');?>	
	
	<!--[if lt IE 9]>
		<?php echo js('libs/html5shiv.js'); ?>
	<![endif]-->
	
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">

	<!--div id="loading_overlay">
	    <div class="loading_message round_bottom">Loading...</div>
	</div-->

	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading_red.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Processing your request...</p>
		</div>
	</div>	
	
	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
	<?php echo js('libs/jquery.tools.1.2.6.min.js'); ?>
	<?php echo js('libs/jquery-ui-timepicker-addon.js'); ?>
	<?php echo js('libs/jquery.clock.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/google-code-prettify/prettify.js'); ?>
	<?php echo js('libs/base_plugins.min.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<!--?php echo js('apps/site.js'); ?--> 

	<div class="container-full">
		<div id='content'>
			<?= $content ?>
		</div>
	</div>
</body>
</html>
