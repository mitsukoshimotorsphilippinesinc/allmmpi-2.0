<?php
	$upload_url = $this->config->item("media_url") . "/results";
	$_upload_url = urlencode($upload_url);
?>

<h2>Edit Result  <a href='/cms/results' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($result)): ?>
<h3>results not found.</h3>
<?php else: ?>
<form action='/cms/results/edit/<?= $result->result_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('result') ?>">
			<label class="control-label" for="result">Result <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Result" name="result" id="result" value="<?= $this->form_validation->set_value('result',$result->result) ?>">
				<p class="help-block"><?= $this->form_validation->error('result'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('member_name') ?>">
			<label class="control-label" for="member_name">Member Name <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="Member Name" name="member_name" id="member_name" value="<?= $this->form_validation->set_value('member_name',$result->member_name) ?>">
				<p class="help-block"><?= $this->form_validation->error('member_name'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_filename">
					<?php if(!empty($result->image_filename)):?>
						<img id="result_image" style="width:260px; height:172px;" alt="" src="<?= $upload_url; ?>/<?= $result->image_filename ?>">
					<?php endif; ?>
					<input type='hidden' value='<?= $result->image_filename ?>'>
				</div>
				<div id="image_upload"></div>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_featured') ?>">
			<label class="control-label" for="is_featured">Featured? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_featured", $options, set_value('is_featured',$result->is_featured),"id='is_featured' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_featured'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="is_published">Published? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_published", $options, set_value('is_published',$result->is_published),"id='is_published' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_published'); ?></p>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Update Result</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var base_url = "<?=$this->config->item('base_url');?>";
	
	// uploader
	$('#image_upload').Uploadrr({
		singleUpload : true,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.gif','.jpg', '.png'],
		target : base_url + '/admin/upload/process?filename=result_<?= $result->result_id?>&location=<?=$_upload_url?>&width=960&ts=<?=time()?>',
		onComplete: function() {
			$("#result_image").attr('src', '<?=$upload_url?>/result_'+<?= $result->result_id?>+'.jpg?v=' + Math.floor(Math.random() * 999999));

			b.request({
		        url: '/cms/results/update_image',
		        data: {
					"filename": '<?= "result_" . $result->result_id .".jpg"?>',
					"result_id":<?= $result->result_id?>
				},
		        on_success: function(data) {		
		        }
		    });		
		}
	});
	
</script>
<?php endif; ?>