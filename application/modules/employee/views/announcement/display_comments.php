<div>	
<?php
	// get announcement messages
	$where = "announcement_id = " . $announcement_id . " AND (from_id_number = '" . $this->employee->id_number . "' OR to_id_number = '" . $this->employee->id_number . "')";
	$announcement_message_details = $this->asset_model->get_announcement_message($where, NULL, "announcement_message_id");

	if (count($announcement_message_details) > 0) {
		foreach ($announcement_message_details as $amd) {
			if ($amd->from_id_number == 'n/a') {
				echo "<div class='alert alert-success' style='border:1px solid;;margin-bottom:5px;'><strong>ADMIN: </strong>{$amd->message}</div>";
			} else {	
				if ($amd->is_removed == 0) {
					echo "<div class='alert alert' style='border:1px solid;;margin-bottom:5px'><strong>ME: </strong>{$amd->message}</div>";
				} else {
					echo "<div class='alert alert' style='border:1px solid;;margin-bottom:5px'><strong>ME: </strong><i style='color:#ff1100;'>Your message was removed by Admin.</i></div>";
				}	
			}	
		}
	}
?>	
</div>			