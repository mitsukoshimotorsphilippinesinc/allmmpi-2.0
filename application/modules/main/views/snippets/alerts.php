<?php
	$this->load->model('asset_model');
	$where = "is_visible = 1 AND (NOW() BETWEEN start_timestamp AND end_timestamp OR start_timestamp = '0000-00-00 00:00:00')";
	$alert_messages = $this->asset_model->get_alert_message($where, null, "insert_timestamp DESC");
	
	$profile_tab_link = "<a href='/members/profile'>Profile Tab</a>";	
	$test_html = "Kindly complete the following details for your Profile. Go to " . $profile_tab_link . " to fill-up and verify the following info. Thank you.";
	
	if ($this->authenticate->is_logged_in()) {
		// TODO : must be dependent to members session id
		$is_notify = 0;

		$error_tag = "<ul>";
		
		if ((($this->member->email) == NULL) || ((trim($this->member->email)) == "") || ($this->member->is_email_verified) == 0) {
			$verify_msg = "";
			if($this->member->email != NULL && trim($this->member->email != "") && !empty($this->member->email)) //no email
				$verify_msg = "(Requires Verification)";
			$error_tag .= "<li>Email {$verify_msg}</li>";
			$is_notify = 1;
		}
		if ((($this->member->rf_id) == NULL) || ((trim($this->member->rf_id)) == "") || ($this->member->is_rf_id_verified == 0)) {
			$verify_msg = "";
			if($this->member->rf_id != NULL && trim($this->member->rf_id != "") && !empty($this->member->rf_id)) //no rf_id
				$verify_msg = "(Requires Verification)";
			$error_tag .= "<li>RF ID {$verify_msg}</li>";
			$is_notify = 1;
		}
		if ((($this->member->metrobank_paycard_number) == NULL) || ((trim($this->member->metrobank_paycard_number)) == "") || ($this->member->is_paycard_verified == 0)) {
			$verify_msg = "";
			if($this->member->metrobank_paycard_number != NULL && trim($this->member->metrobank_paycard_number != "") && !empty($this->member->metrobank_paycard_number)) //no paycard
				$verify_msg = " (Requires Verification)";
			$error_tag .= "<li>Metrobank Paycard Number {$verify_msg}</li>";
			$is_notify = 1;
		}
		if ((($this->member->mobile_number) == NULL) || ((trim($this->member->mobile_number)) == "") || ($this->member->is_mobile_verified == 0)) {
			$verify_msg = "";
			if($this->member->mobile_number != NULL && trim($this->member->mobile_number != "") && !empty($this->member->mobile_number)) //no mobile
				$verify_msg = " (Requires Verification)";
			$error_tag .= "<li>Mobile Number {$verify_msg}</li>";
			$is_notify = 1;
		}

		$error_tag .= "</ul>";
	}
	
?>

<style>
.alert-msg-item {
	
}
.alert-msg-item p {
	margin:0;
}
</style>

<?php 
if ($this->authenticate->is_logged_in()) {
?>
<div class="alert alert-danger alert-msg-item">
	<blink><label class="label label-warning">NOTIFICATION</label></blink>
	<strong>Complete your Member's Profile</strong>
	<div><p><?=$test_html;?><?=$error_tag;?></p></div>
</div>
<?php
}
?>

<?php
	foreach ($alert_messages as $item) :
		
		$content = sanitize_html($item->content);
		// remove br
		$content = str_replace('<br>', '', $content);
		$content = str_replace('<br/>', '', $content);
		$content = str_replace('<br />', '', $content);
?>
	<div class="alert alert-info alert-msg-item">
  		<strong><?=$item->title;?>:</strong>
		<div><?=$content;?></div>
	</div>
<?php 
	endforeach; 
?>

