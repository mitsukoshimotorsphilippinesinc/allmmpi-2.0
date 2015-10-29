<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/employee_login_ad";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/employee_login_ads/' . $featured_employee_login_ad->image_filename);

	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>

<div class="">	
	<h2 id="header_text">View Login Ad Details #<?= $featured_employee_login_ad->employee_login_ad_id; ?>
		<div style="float:right;"><a href="/operations/login_ad"><button class="btn btn-default" style="margin-top: -5px; margin-right: -10px;"><span>Back</span></button></a></div>
	</h2>
</div>
<div class="row-fluid">
	<input type="hidden" value="<?= $featured_employee_login_ad->employee_login_ad_id; ?>" id="employee_login_ad_id" name="employee_login_ad_id" />
	<div id="member_details">
		<table class="table table-striped table-condensed">
			<tbody>
				<tr><td><strong>Ad Image</strong></td><td id="ad_image"><img src="<?= $member_picture?>"></img></td></tr>
				<tr><td><strong>Ad Name</strong></td><td id="slide_name"><?= $featured_employee_login_ad->ad_name; ?></td></tr>
				<tr><td><strong>Description</strong></td><td id="description"><?= $featured_employee_login_ad->description; ?></td></tr>
				<tr><td><strong>Priority ID</strong></td><td id="priority_id"><?= $featured_employee_login_ad->priority_id; ?></td></tr>
				<tr><td><strong>Image Filename</strong></td><td id="image_filename"><?= $featured_employee_login_ad->image_filename; ?></td></tr>
			</tbody>
		</table>
	</div>	
	<div id="image_upload"></div>
</div>

<script>
	$(document).on('ready', function() {
			
		var employee_login_ad_id = $("#employee_login_ad_id").val();
	
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=members_login_ad_'+employee_login_ad_id+'&location=<?=$_upload_url?>&width=500&height=800&ts=<?=time()?>',
			onComplete: function() {
				$("#member_picture").html('<img src="<?=$upload_url?>/members_login_ad_'+employee_login_ad_id+'.jpg?v=' + Math.floor(Math.random() * 999999)+'">');
				
				b.request({
					url: '/operations/login_ad/update_image',
					data: {
						"filename": 'members_login_ad_'+employee_login_ad_id+'.jpg',
						"employee_login_ad_id": employee_login_ad_id
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