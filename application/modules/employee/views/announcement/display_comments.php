<div>	
<?php
	// get announcement messages
	$where = "announcement_id = " . $announcement_id;
	$announcement_message_details = $this->asset_model->get_announcement_message($where, NULL, "announcement_message_id");

	if (count($announcement_message_details) > 0) {
		foreach ($announcement_message_details as $amd) {
			if ($amd->from_id_number == 'n/a') 
				echo "<div class='alert alert-success' style='border:1px solid;'><strong>ADMIN: </strong>{$amd->message}</div>";
			else	
				echo "<div class='alert alert' style='border:1px solid;'><strong>ME: </strong>{$amd->message}</div>";
		}
	}
?>	
</div>			