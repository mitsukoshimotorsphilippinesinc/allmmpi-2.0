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

	<link rel="shortcut icon" href="<?=$this->config->item('base_url');?><?=$this->config->item('img_url');?>/favicon.ico" />
	
	<?php echo css('jquery-ui/jquery-ui-1.9.1.css');?>
	
	<?php echo css('base.css');?>
	<?php echo css('main.css');?>
	<?php echo css('google-code-prettify/prettify.css');?>
	<?php echo css('switcherMenu.css');?>

	<?php echo css('tablesorter.css'); ?>

	

	<!--[if lt IE 9]>
		<?php echo js('libs/html5shiv.js'); ?>
	<![endif]-->
	
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="50">
<style>
body > .container > #content {
	min-height: 500px;
}
</style>
	
	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
	<?php echo js('libs/jquery.tools.1.2.6.min.js'); ?>
	<?php echo js('libs/jquery-ui-timepicker-addon.js'); ?>
	<?php echo js('libs/jquery.clock.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/google-code-prettify/prettify.js'); ?>
	<?php echo js('libs/base_plugins.min.js'); ?>
	<?php echo js('libs/uploadrr.min.js'); ?>
	<?php echo js('libs/tinymce/jquery.tinymce.js'); ?>
	<?php echo js('libs/yoxview/yoxview-init.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('apps/inventory.js'); ?>
	<?php echo js('apps/site.js'); ?>

	<script type='text/javascript'>
		var base_url = "<?=$this->config->item('base_url');?>";
	</script>

	<div id="loading_overlay">
	    <div class="loading_message round_bottom">Loading...</div>
	</div>
	
	<div id="header" class='navbar navbar-fixed-top cleafix'>
		<div class="navbar-inner">
			<div class="container" style='width:95%;'>
				<a class="brand" href="/admin">MMPI<br/>Administration</a>
				<div class='pull-left'>
					<?= $this->load->view('switchers/systems', null, TRUE, 'admin'); ?>
				</div>
				
				<div class='pull-right admin-login-profile'>
					<span class='admin-login-profile-name'>Hi <?= ucfirst($this->user->first_name).' '.ucfirst($this->user->last_name) ?></span>
					<a href='/admin/signin/signout' class='btn'><span>Logout</span></a>
				</div>
				
				<div class='pull-right' style='margin: 5px 20px 0 0;'>
					<?= $this->load->view('switchers/facilities', null, TRUE, 'admin'); ?>
				</div>
			</div>
			<?= $this->load->view('navigation_top', null, TRUE, 'admin');  ?>
		</div>
	</div>
	
	<div class="container-fluid">
		<div class="row-fluid">
		    <div class="span12">
		      <?php
					if (!$this->users_model->is_user_allowed($this->uri->uri_string())) 
						echo "<div class='alert alert-error'>You do not have access to this module. Please contact your administrator if you require accesss to this module</div>";
					else
			      		echo $content;						
			  ?>
		    </div>
		  </div>
		<footer>
			<p class="pull-right"></p>
			<p></p>
		</footer>
	</div>
</body>
</html>
