<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members_login";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/members_login/' . $featured_members_login_ad->image_filename);
?>

<div class="alert alert-info">	
	<h2 id="header_text">Member's Login Ad #<?= $featured_members_login_ad->members_login_ad_id; ?> :: Details
		<div style="float:right;"><button id="back" class="btn btn-default" style="margin-top: -5px; margin-right: -30px;"><span>Back</span></button></div>
	</h2>
</div>
<div class="row-fluid">
	<input type="hidden" value="<?= $featured_members_login_ad->members_login_ad_id; ?>" id="members_login_ad_id" name="members_login_ad_id" />
	<div id="member_details">
		<table class="table table-striped table-condensed">
			<tbody>
				<tr><td><strong>Ad Image</strong></td><td id="ad_image"><img src="<?= $member_picture?>"></img></td></tr>
				<tr><td><strong>Slide Name</strong></td><td id="slide_name"><?= $featured_members_login_ad->slide_name; ?></td></tr>
				<tr><td><strong>Description</strong></td><td id="description"><?= $featured_members_login_ad->description; ?></td></tr>
				<tr><td><strong>Priority ID</strong></td><td id="priority_id"><?= $featured_members_login_ad->priority_id; ?></td></tr>
				<tr><td><strong>Image Filename</strong></td><td id="image_filename"><?= $featured_members_login_ad->image_filename; ?></td></tr>
			</tbody>
		</table>
	</div>	
	<div id="image_upload"></div>
</div>

<script>
	$(document).on('ready', function() {
			
		var members_login_ad_id = $("#members_login_ad_id").val();
	
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=members_login_ad_'+members_login_ad_id+'&location=<?=$_upload_url?>&width=500&height=800&ts=<?=time()?>',
			onComplete: function() {
				$("#member_picture").html('<img src="<?=$upload_url?>/members_login_ad_'+members_login_ad_id+'.jpg?v=' + Math.floor(Math.random() * 999999)+'">');
				
				b.request({
					url: '/cms/members_login/update_image',
					data: {
						"filename": 'members_login_ad_'+members_login_ad_id+'.jpg',
						"members_login_ad_id": members_login_ad_id
					},
					on_success: function(data) {		
					}
				});		
			}
		});
	});
	
	$("#back").click(function(){
		redirect('/cms/members_login');
	});
</script>