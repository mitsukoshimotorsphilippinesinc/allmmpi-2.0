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

	<link rel="shortcut icon" href="<?= image_url('favicon.ico'); ?>" />
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
	<?php echo js('libs/yoxview/yoxview-init.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('apps/site.js'); ?>
	<?php echo js('libs/pdf.js'); ?>
	<?php if($this->member->member_id>0) : ?>
		<script type="text/javascript" >
			vitalc.member.member_id = <?=$this->member->member_id;?>;
			vitalc.member.first_name = "<?=$this->member->first_name;?>";
			vitalc.member.last_name = "<?=$this->member->last_name;?>";
			vitalc.member.funds = <?=$this->member->funds;?>;
			vitalc.member.gift_cheques = <?=$this->member->gift_cheques;?>;
			vitalc.member.gcep = <?=$this->member->gcep;?>;
			vitalc.member.on_hold_funds = <?=$this->member->on_hold_funds;?>;
			vitalc.member.is_paycard_corpo = <?=$this->member->is_paycard_corpo;?>;
		</script>
	<?php endif; ?>
	
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
				<!--img style="width:8em;" src="$this->/assets/img/mitsukoshi-logo.svg"></img-->
				<!--temporarily using svg image from public_html-->
				<!--img style="width:10em;" alt="Mitsukoshi Motors Logo" src="http://mitsukoshimotors.local/wp-content/uploads/2015/02/mitsukoshi-logo.svg"-->
			</h1>
			<?= $this->load->view('snippets/menu', NULL, TRUE,'main');  ?>
			<?= $this->load->view('snippets/navigation', NULL, TRUE,'main');  ?>
		</div>
		
	</header>
	<div id='content'>
		<div class='content-area'>
			<!--?= $this->load->view('snippets/alerts', NULL, TRUE,'main');  ?-->
		</div>
		<?= $content ?>
	</div>
	<footer>
		<div class="grid12">
			<div class="row">
				<p class="span5">@2015 Mitsukoshi Motors PHilippines Inc. All Rights Reserved.</p>
				<ul class="span7" class="text-align:right;">
					<li><a href="#">Home</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Terms and Conditions</a></li>					
				</ul>
			</div>
			<div class="row">	
				<p class='span12'>Powered by<a href="#"> MMPI IT Department</a></p>
			</div>
		</div>
	</footer>

</body>

</html>

