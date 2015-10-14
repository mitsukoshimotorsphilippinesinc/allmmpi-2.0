<div class="wrapper">
    <?php
    // packages
    //echo $this->load->view('snippets/packages', NULL, TRUE,'main');
    echo $this->load->view('snippets/featured_package_news', NULL, TRUE,'main');
    ?>
</div>

<div class="content-area">
	<?php 
		//product slider
		echo $this->load->view('snippets/products', NULL, TRUE,'main');  
	?>
</div>



<div class="content-area">
	<?php 
		// wall
		echo $this->load->view('snippets/wall', NULL, TRUE,'main');  
		
		// doctors profile
		echo $this->load->view('snippets/doctors', NULL, TRUE,'main');
		
		// testimonials
		echo $this->load->view('snippets/testimonials', NULL, TRUE,'main');
	?>
</div>