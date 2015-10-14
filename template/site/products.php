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

	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="<?= image_url('favicon.ico'); ?>" />
	<?php echo css('base.css');?>
	<?php echo css('main.css');?>
	<?php echo css('site.css');?>
	<?php echo css('slider.css');?>

	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
	<?php echo js('libs/modernizr-2.5.3-respond-1.1.0.min.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/bootstrap.min.js'); ?>
	<?php echo js('libs/slides.min.jquery.js'); ?>
	<?php echo js('libs/tweet/jquery.tweet.js'); ?>
	<?php echo js('libs/yoxview/yoxview-init.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('apps/site.js'); ?>    
	<?php echo js('apps/ordering.js'); ?>
	
</head>

<body>
	
	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Please wait while processing...</p>
		</div>
	</div>
	
	<div class="clearfix">
		<div id="header">
			<div class="wrapper">
				<h1 id="logo"><a href="/ordering">Vital-C</a></h1>
        		
				<form method="post" id="search-bar" class="pull-left" >
					<input type="search" placeholder="search..." id="search-field" />
					<button id="search-button" class="btn"><i class="icon-search"></i></button>
				</form>

			
			    <?php 
					// load profile box or secondary menu
					echo $this->load->view('snippets/user_profile', NULL, TRUE,'main');  
				?>
            
				<div class='social-media-links'></div>
                    
                <?php
                if ($this->authenticate->is_logged_in())
                {
                    echo $this->load->view('snippets/user_navigation', NULL, TRUE,'main');
                }
                ?>
	
				<ul class='secondary-nav' style="height:70px;">
				</ul>

	            <div class="navbar">
			    	<?php
			 			echo $this->load->view('snippets/navigation', NULL, TRUE,'main');  
					?>
				</div>
	            <div class="navbar-2">
					<?php
			 			echo $this->load->view('snippets/ordering_navigation', NULL, TRUE,'main');  
					?>
				</div>			
			</div><!-- end #header-wrapper -->
		</div><!-- end #header -->

		<div class="wrapper">			
			<div id='content'>
				<?= $content ?>
			</div>
		</div>			

		<div id="footer">
			<div class="wrapper">			
		 	<?= $this->load->view('snippets/footer', NULL, TRUE,'main');  ?>
			</div><!-- end #footer-wrapper -->		
		</div><!-- end #footer -->
	</div>
</body>


</html>

