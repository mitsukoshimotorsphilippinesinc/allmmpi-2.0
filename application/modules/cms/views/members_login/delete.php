<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members_login";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/members_login/' . $featured_member_login_ad->image_filename);
?>

<div class="alert alert-info">	
	<h2 id="header_text">Member's Login Ad #<?= $featured_member_login_ad->members_login_ad_id; ?> :: Delete
		<div style="float:right;"><button id="back" class="btn btn-default" style="margin-top: -5px; margin-right: -30px;"><span>Back</span></button></div>
	</h2>
</div>
<div class="row-fluid">
	<form action='/cms/members_login/delete/<?= $featured_member_login_ad->members_login_ad_id ?>' method='post' class='form-horizontal'>
		<fieldset>
			<input type="hidden" value="<?= $featured_member_login_ad->members_login_ad_id; ?>" id="members_login_ad_id" name="members_login_ad_id" />
			<div id="member_details">
				<table class="table table-striped table-condensed">
					<tbody>
						<tr><td>Member Picture</td><td id="member_picture"><img src="<?= $member_picture?>"></img></td></tr>
						<tr><td>Slide Name</td><td id="member_name"><?= $featured_member_login_ad->slide_name; ?></td></tr>
						<tr><td>Description</td><td id="member_since"><?= $featured_member_login_ad->description; ?></td></tr>
						<tr><td>Is Active?</td><td id="member_since"><?= $featured_member_login_ad->is_active; ?></td></tr>
						<tr><td>Priority Number</td><td id="member_since"><?= $featured_member_login_ad->priority_id; ?></td></tr>
						<tr><td>Image Filename</td><td id="image_filename"><?= $featured_member_login_ad->image_filename; ?></td></tr>
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
		
	$("#back").click(function(){
		redirect('/cms/members_login');
	});
</script>