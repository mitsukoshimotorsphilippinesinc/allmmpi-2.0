<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/employee_login_ads";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/employee_login_ads/' . $featured_employee_login_ad->image_filename);
	
	$breadcrumb_container = assemble_breadcrumb();
	
?>

<?= $breadcrumb_container; ?>

<div class="">	
	<h2 id="header_text">Delete Login Ad Details #<?= $featured_employee_login_ad->employee_login_ad_id; ?>
		<div style="float:right;"><a href="/operations/login_ad"><button class="btn btn-default" style="margin-top: -5px; margin-right: -10px;"><span>Back</span></button></a></div>
	</h2>
</div>

<div class="row-fluid">
	<form action='/cms/members_login/delete/<?= $featured_employee_login_ad->members_login_ad_id ?>' method='post' class='form-horizontal'>
		<fieldset>
			<input type="hidden" value="<?= $featured_employee_login_ad->employee_login_ad_id; ?>" id="employee_login_ad_id" name="employee_login_ad_id" />
			<div id="member_details">
				<table class="table table-striped table-condensed">
					<tbody>
						<tr><td>Member Picture</td><td id="member_picture"><img src="<?= $member_picture?>"></img></td></tr>
						<tr><td>Ad Name</td><td id="member_name"><?= $featured_employee_login_ad->ad_name; ?></td></tr>
						<tr><td>Description</td><td id="member_since"><?= $featured_employee_login_ad->description; ?></td></tr>
						<tr><td>Is Active?</td><td id="member_since"><?= $featured_employee_login_ad->is_active; ?></td></tr>
						<tr><td>Priority Number</td><td id="member_since"><?= $featured_employee_login_ad->priority_id; ?></td></tr>
						<tr><td>Image Filename</td><td id="image_filename"><?= $featured_employee_login_ad->image_filename; ?></td></tr>
					</tbody>
				</table>
			</div>
			<div >
				<button type="submit" class="btn btn-primary">Confirm Deletion</button>
			</div>
		</fieldset>
	</form>
</div>

<script>
	
</script>