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
	<?php echo css('simple-sidebar/css/simple-sidebar.css');?>	
	<?php echo css('font-awesome-4.4.0/css/font-awesome.min.css');?>		
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
	<!--?php echo js('apps/inventory.js'); ?-->
	<!--?php echo js('apps/site.js'); ?-->

	<script type='text/javascript'>
		var base_url = "<?=$this->config->item('base_url');?>";
	</script>
	
	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading_red.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Processing your request...</p>
		</div>
	</div>	
	
	<div id="header" class='navbar navbar-fixed-top cleafix'>
		<div class="navbar-inner">
			<div class="container" style='width:95%;;height:50px;'>
				<a class="brand" style="font-size:1.3em;margin-top:.5em;" href="/admin">Mitsukoshi Motors Philippines Inc.<br/>Administration</a>
				<div class='pull-left'>
					<?= $this->load->view('switcher-system', null, TRUE, 'admin'); ?>
				</div>
				
				<div class='pull-right admin-login-profile'>
					<?php
						$this->load->model("human_relations_model");
						$upload_url = $this->config->item("media_url") . "/employees";

						$employment_view_details = $this->human_relations_model->get_employment_information_view_by_id($this->user->id_number);

						if ((empty($employment_view_details->image_filename)) || ($employment_view_details->image_filename == NULL) || (trim($employment_view_details->image_filename) == "")) {
							$image_display = "ni_". strtolower($employment_view_details->gender) .".png";
						} else {
							$image_display = $employment_view_details->image_filename;
						}

					?>
					<img id="" src="<?= $upload_url; ?>/<?= $image_display ?>" alt="" style="margin-right:5px;margin-bottom:5px;width:30px; height:30px;"></img>
					<span class='admin-login-profile-name'>Hi <?= ucfirst($employment_view_details->first_name) ?></span>
					<a href='/admin/signin/signout' class='btn'><span>Logout</span></a>					
				
					<button style="margin-top:-2px;width:30px;height:30px;" class="navbar-toggle collapse in" data-toggle="collapse" id="menu-toggle-2">
						<span class="icon-th-large" aria-hidden="true"/>
					</button>
				</div>	
			</div>			
			<!--?= $this->load->view('navigation_top', null, TRUE, 'admin');  ?-->
		</div>
	</div>
	
	<?php
	if ($this->uri->segment(1) == "admin") {		
		echo "<div class='container-full'>";		
	} else {

		$data = array();
		$data = array(
			'system_name' => $this->uri->segment(1),
			'segment_name' => $this->uri->segment(2)
		);

	?>	
	<?= $this->load->view('navigation_side', $data, TRUE, 'admin'); ?>
	<?php		
		echo "<div class='container'>";	
	}
	?>	
		<div style="margin-top:40px;width:1000px;" class='content-wrapper'>
			<div id='content'>
		      <?php
					//if (!$this->user_model->is_user_allowed($this->uri->uri_string())) 
					//	echo "<div class='alert alert-error'>You do not have access to this module. Please contact your administrator if you require accesss to this module</div>";
					//else 
			      		echo $content;						
			  ?>
			</div>
		</div>		
		<div style="clear:both;"></div>
		<footer>
			<p class="pull-right"></p>
			<p></p>
		</footer>
	</div>

</body>
</html>
