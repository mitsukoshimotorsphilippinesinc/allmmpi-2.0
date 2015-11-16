<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/employee_login_ads";
	$_upload_url = urlencode($upload_url);
	
	$member_picture = check_image_path($this->config->item('media_url') . '/employee_login_ads/' . $featured_employee_login_ad->image_filename);
	
	
	$counter_100 = 1;
	
	
	while($counter_100 <= 3)
	{
		$where = "is_active = 1 AND employee_login_ad_id <> {$featured_employee_login_ad->employee_login_ad_id} AND priority_id = {$counter_100}";
		$login_ad_details = $this->asset_model->get_employee_login_ad($where);
		
		if (empty($login_ad_details)) {	
			$options[$counter_100] = $counter_100;
		} else {
			$options[0] =  $featured_employee_login_ad->priority_id;
		}		
				
		$counter_100++;
	}

	$breadcrumb_container = assemble_breadcrumb();
	
?>

<?= $breadcrumb_container; ?>

<div class="">	
	<h2 id="header_text">Edit Login Ad Details #<?= $featured_employee_login_ad->employee_login_ad_id; ?>
		<div style="float:right;"><a href="/operations/login_ad"><button class="btn btn-default" style="margin-top: -5px; margin-right: -10px;"><span>Back</span></button></a></div>
	</h2>
</div>
<form action='/operations/login_ad/edit/<?= $featured_employee_login_ad->employee_login_ad_id ?>' method='post' class='form-horizontal'>
	<fieldset>
		<div class="row-fluid">
			<input type="hidden" value="<?= $featured_employee_login_ad->employee_login_ad_id; ?>" id="member_login_ad_id" name="member_login_ad_id" />
			<div id="member_details">
				<table class="table table-striped table-condensed">
					<tbody>
						<tr><td><strong>Ad Picture</strong></td><td id="member_picture"><img src="<?= $member_picture?>"></img></td></tr>
						<tr><td><strong>Ad Name</strong></td><td><input id="ad_name" name="ad_name" type="input" value="<?= $featured_employee_login_ad->ad_name; ?>"></td></tr>
						<tr><td><strong>Description</strong></td><td><input id="description" name="description" type="input" value="<?= $featured_employee_login_ad->description; ?>"></td></tr>
						<tr><td><strong>Priority Number</strong></td>
							<td>
								<?php echo form_dropdown('priority_id', $options, set_value('priority_id', $featured_employee_login_ad->priority_id), "name='priority_id' id='priority_id'")?>
							</td>
						</tr>
						<tr><td><strong>Is Active?</strong></td>
							<td>
								<?php echo form_dropdown('is_active', array("0" => "No", "1" => "Yes"), set_value('is_active', $featured_employee_login_ad->is_active), "name='is_active' id='is_active'")?>
							</td>
						</tr>
						<tr><td><strong>Image Filename</strong></td><td id="image_filename"><?= $featured_employee_login_ad->image_filename; ?></td></tr>
					</tbody>
				</table>
			</div>	
			<div id="image_upload"></div>
			<hr>
			<div>
				<div>
					<button type="6" class="btn btn-primary">Update Ad</button>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<script>
	$(document).on('ready', function() {
			
		var member_login_ad_id = $("#member_login_ad_id").val();
	
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?filename=members_login_ad_'+member_login_ad_id+'&location=<?=$_upload_url?>&width=500&height=500&ts=<?=time()?>',
			onComplete: function() {
				$("#member_picture").html('<img src="<?=$upload_url?>/members_login_ad_'+member_login_ad_id+'.jpg?v=' + Math.floor(Math.random() * 999999)+'">');
				
				b.request({
					url: '/operations/login_ad/update_image',
					data: {
						"filename": 'members_login_ad_'+member_login_ad_id+'.jpg',
						"employee_login_ad_id": member_login_ad_id
					},
					on_success: function(data) {
					}
				});		
			}
		});
	});
	
	$("#back").click(function(){
		redirect('/operations/login_ad');
	});
</script>