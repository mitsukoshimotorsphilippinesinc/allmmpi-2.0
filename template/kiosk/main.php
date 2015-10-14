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
	<?php echo css('/assets/kiosk/css/kiosk.css');?>
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
	<?php
		
		if (!isset($screen)) $screen = 'store';
		if (!isset($screen_title)) $screen_title = 'Vital-C iStore';
	
	?>
	<script type="text/javascript">
		k.screen = "<?= $screen ?>";
		k.screen_url = "<?= $screen_url ?>";
		k.screen_title = "<?= $screen_title ?>";
	</script>
</head>

<body>

	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Please wait while processing your request...</p>
		</div>
	</div>	
<?php
	// include templates
	echo $this->load->view('templates/breadcrumbs', null, false, 'kiosk');

?>
	<div id='wrapper'>
		<header class="row-fluid">
			<div class="clearfix">
				<h1 id='screen_title' class="page-title"><?= isset($screen_title) ? $screen_title : ''; ?></h1>
				<div class="pull-left">
					<button id='btn_generic_back' class="btn btn-back" style='display:none;'><i class="icon-arrow-left"></i> Back</button>
					<?php if ($screen == 'members_area') : ?>
						<a id='btn_store' href='/kiosk/store' class="btn btn-info"><i class="icon-shopping-cart icon-white"></i> Store</a>
					<?php elseif ($screen == 'store') :?>
						<a id='btn_store' href='/kiosk/profile' class="btn btn-info"><i class="icon-user icon-white"></i> Member's Area</a>
					<?php endif; ?>
				</div>
				<ul class="nav nav-pills pull-right">
					<?php if (isset($with_search)) if ($with_search) : ?>
					<li><a href="#"><i class="icon-search"></i> Search</a></li>
					<?php endif; ?>
					<?php 

						if ($this->authenticate->is_logged_in()) : 
							$fullname = $this->member->first_name . " " . $this->member->last_name;
					?>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-user"></i> <?= $fullname ?> <b class="caret"></b></a>
						<ul id='profile-dropdown-menu' class="dropdown-menu">
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='profile' >Profile</a></li>
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='accounts' >Accounts</a></li>
							<li class="divider"></li>
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='earnings' >Earnings</a></li>
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='orders' >Orders</a></li>
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='encoding' >Encode Sales</a></li>
							<li><a href="javascript://void(0);" class='active-link' data-type='profile' data-id='vouchers' >Vouchers</a></li>
						</ul>
					</li>
					<li><a href="/kiosk/signout"><i class="icon-off"></i> Logout</a></li>
					<?php else :?>
					<li><a href="/kiosk"><i class="icon-off"></i> Login</a></li>
					<?php endif; ?>

				</ul>

			</div>
		</header>
		<ul id='kiosk_breadcrumbs' class="breadcrumb">
		</ul>

		<div id='content'>
			<?= $content ?>
		</div>
	</div>
</body>

</html>

