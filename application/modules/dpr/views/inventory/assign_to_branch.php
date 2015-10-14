<?php

	$branch_options = array();

	foreach ($branch_rack_location_view_details as $brlvd) {
	 	$branch_options[$brlvd->branch_id] = $brlvd->branch_name;
	}

?>

<h2>Booklet Releasing</h2>
<hr/>
<div class="span6">
	<label>Branch Name:</label>
	<?= form_dropdown('branch_name',$branch_options, set_value('branch_name',NULL),'id="branch_name"') ?>	
	<label>Form Type:</label>
	<input>
	<label>Form Type:</label>
	<input>
</div>
<div class="span6">
	<label>Branch Name:</label>
	<input>
	<label>Form Type:</label>
	<input>
	<label>Form Type:</label>
	<input>
</div>
