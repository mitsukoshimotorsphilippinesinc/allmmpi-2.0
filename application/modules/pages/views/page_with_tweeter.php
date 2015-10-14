<div class="content-area">
	<div class="row-fluid">
		<div class="span8">
			<h2><?= $title ?></h2>
			<?= $body ?>
		</div>
		<div cass="span4">
			<?php 
				// doctors profile
				echo $this->load->view('snippets/tweets', NULL, TRUE,'main');  
			?>
		</div>
	</div>
</div>
