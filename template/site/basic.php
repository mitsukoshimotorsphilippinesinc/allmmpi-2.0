<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Mitsukoshi Motors Philippines Inc</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="google-site-verification" content="n3OStZb5h80YSMRjBwO8RTzDFXGEWuDdF-pYHUmNDus" />

	<link rel="shortcut icon" href="<?= image_url('/favicon.ico'); ?>" />
	<?php echo css('bootstrap.css');?>
	<?php echo css('main.css');?>
	<?php echo css('mmpi.css');?>
	<?php echo css('slider.css');?>

	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
    <?php echo js('libs/jquery.easing.1.3.js'); ?>
    <?php echo js('libs/flowslider.jquery.js'); ?>
	<?php echo js('libs/dropdown.js'); ?>
	<?php echo js('libs/modernizr-2.5.3-respond-1.1.0.min.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/bootstrap.min.js'); ?>
	<?php echo js('libs/jquery.countdown.min.js'); ?>
	<?php echo js('libs/tweet/jquery.tweet.js'); ?>
	<?php echo js('libs/slides.min.jquery.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('apps/site.js'); ?>   
	
</head>

<body>

	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Please wait while processing...</p>
		</div>
	</div>	
	
	
	<header>
		
		
		<div class="clearfix">
			<h1 class="pull-left" style="margin-top:-10px;">
				<a href="/">MMPI</a>
				<!--a href="/">VitalC</a-->
			</h1>
			<ul class="nav pull-right">
				<li><a href="/employee/signin">Login</a></li>
				<li><a href="/">Join Us</a></li>
				<li><a href="#">Contact Us</a></li>
				<li><a href="#">FAQs</a></li>
				<li>
					<form class="search-query">
						<input placeholder="Search"/>
						<a href="#"><i>icon-search</i></a>
					</form>
				</li>
			</ul>
			<?= $this->load->view('snippets/navigation', NULL, TRUE,'main');  ?>
		</div>
		
	</header>
	<div id='content'>
		<?= $content ?>
	</div>
	<footer>
		<div class="grid12">
			<div class="row-fluid">
				<p class="span5">@2015 Mitsukoshi Motors Philippines Inc. All Rights Reserved.</p>
				<ul class="span7">
					<li><a href="#">Home</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Terms and Conditions</a></li>
					<li><a href="#">Careers</a></li>
				</ul>
			</div>
			<div class="row-fluid">
				<p>Powered by<a href="http://www.mitsukoshimotors.com"> Mitsukoshi Motors Philippines, Inc.</a></p>
			</div>
		</div>
	</footer>

</body>

</html>

