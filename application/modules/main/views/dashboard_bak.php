<div class="container">
	<!-- featured promos -->
	<?php echo Modules::run('main/featured_promos',NULL); ?>	
   
    <!-- featured products -->
	<?php echo Modules::run('main/featured_products',NULL); ?>	
</div><!-- end: div.container-->


<div class="container">

	<!-- left sidebar -->
	<?php echo Modules::run('main/left_sidebar',NULL); ?>		
    
	<!-- main column -->
	<div class="main-col">

		<!-- Promos -->				
		<?php echo Modules::run('main/promos',NULL); ?>								
        
		<!-- News -->
		<?php echo Modules::run('main/news/featured'); ?>	

        <div class="adspace">
        	<img src="assets/img/ad_main-column_ad1.jpg">
            <img src="assets/img/ad_main-column_ad2.jpg">
            <img src="assets/img/ad_main-column_ad3.jpg">
            <img src="assets/img/ad_main-column_ad4.jpg">
        </div>
        
        <div class="trustmarks"><img src="assets/img/trustmarks.jpg"></div>        
	</div><!-- end: div.main-col -->

	<!-- right sidebar -->
	<?php echo Modules::run('main/right_sidebar',NULL); ?>		

</div><!-- end: div.container-->



<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){		
		$(".featured-content").slides({
			paginationClass: "ticker",
			play: 3000,
			pause: 5000,
			hoverPause: true
		});
	});
</script>