<div class="container row-fluid">    
	<!-- main column -->
	<div class="main-col span12">
		<div class="span8">
		<h2><?=$title?></h2>
		<?=$contents?>
		</div>
		<div class="span4">
			<div>
				<?php echo Modules::run('main/calendar/index'); ?>
			</div>
			<div>
			<?php 
				// twitter feeds
				echo $this->load->view('snippets/tweets', NULL, TRUE,'main');  
			?>
			</div>
		</div>
	</div><!-- end: div.main-col -->

	<!-- left sidebar -->
	<?php echo Modules::run('main/left_sidebar',NULL); ?>		

	<!-- right sidebar -->
	<?php echo Modules::run('main/right_sidebar',NULL); ?>		

</div><!-- end: div.container-->