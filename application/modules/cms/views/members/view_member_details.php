<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/members/' . $featured_member->image_filename);
?>

<div class="alert alert-info">	
	<h2 id="header_text">Member's Profile
		<div style="float:right;"><button id="back" class="btn btn-default" style="margin-top: -5px; margin-right: -30px;"><span>Back</span></button></div>
	</h2>
</div>
<div class="row-fluid">
	<input type="hidden" value="<?= $featured_member->featured_member_id; ?>" id="featured_member_id" name="featured_member_id" />
	<div id="member_details">
		<table class="table table-striped table-condensed">
			<tbody>
				<tr><td>Member Picture</td><td id="member_picture"><img src="<?= $member_picture?>"></img></td></tr>
				<tr><td>Member Name</td><td id="member_name"><?= $featured_member->member_name; ?></td></tr>
				<tr><td>Group Name</td><td id="member_since"><?= $featured_member->group_name; ?></td></tr>
				<tr><td>Image Filename</td><td id="image_filename"><?= $featured_member->image_filename; ?></td></tr>
			</tbody>
		</table>
	</div>	
	<div id="image_upload"></div>
</div>

<script>
	$(document).on('ready', function() {
			
		var featured_member_id = $("#featured_member_id").val();
	
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=featured_member_'+featured_member_id+'&location=<?=$_upload_url?>&width=400&height=400&ts=<?=time()?>',
			onComplete: function() {
				$("#member_picture").html('<img src="<?=$upload_url?>/featured_member_'+featured_member_id+'.jpg?v=' + Math.floor(Math.random() * 999999)+'">');
				
				b.request({
					url: '/cms/members/update_image',
					data: {
						"filename": 'featured_member_'+featured_member_id+'.jpg',
						"featured_member_id": featured_member_id
					},
					on_success: function(data) {		
					}
				});		
			}
		});
	});
	
	$("#back").click(function(){
		redirect('/cms/members');
	});
</script>