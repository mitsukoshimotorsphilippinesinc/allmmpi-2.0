<?php
	$msg_content = "This feature is currently disabled by Admin. Please contact IT Department. Thank you.";
	$msg_content = $this->contents_model->get_content_by_slug('disable_rs_encoding_message');
?>
<div class="page-header clearfix">
	<h2>Encode Sales</h2>
</div>
<div align="center" class="alert alert-info alert-msg-item">
	<strong>Vital C Alert:</strong>
	<div><?= $msg_content->body ?></div>
</div>
