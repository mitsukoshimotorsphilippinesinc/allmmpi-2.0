<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Vital-C Kiosk</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="shortcut icon" href="<?=$this->config->item('base_url');?><?=$this->config->item('img_url');?>/favicon.ico" />
	<?php echo css('bootstrap.css');?>
	<?php echo css('main.css');?>
	<?php echo css('/assets/kiosk/css/basic.css');?>
	<?php // echo css('slider.css');?>

	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
	<?php echo js('libs/dropdown.js'); ?>
	<?php echo js('libs/modernizr-2.5.3-respond-1.1.0.min.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/bootstrap.min.js'); ?>
	<?php echo js('libs/tweet/jquery.tweet.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('/assets/kiosk/js/kiosk.js'); ?>    
	
</head>

<body>

	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Please wait while processing your request...</p>
		</div>
	</div>
	
	<div class="container">
		<header class="row-fluid">
			<div class="clearfix">
				<h1><span>&nbsp;</span><span>VitalC</span></h1>
			</div>
		</header>
		<div id='content' class="row">
			<?= $content ?>
		</div>
	</div>
</body>

</html>

