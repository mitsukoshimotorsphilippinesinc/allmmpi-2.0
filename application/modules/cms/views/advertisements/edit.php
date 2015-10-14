<?php
	$upload_url = $this->config->item("media_url") . "/advertisements";
	$_upload_url = urlencode($upload_url);
?>
<h2>Edit Advertisement  <a href='/cms/advertisements' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($advertisement)): ?>
<h3>Advertisement not found.</h3>
<?php else: ?>
<form action='/cms/advertisements/edit/<?= $advertisement->advertisement_id ?>' method='post' class='form-horizontal'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('advertisement_title') ?>">
			<label class="control-label" for="advertisement_title">Advertisement Title <em>*</em></label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="advertisement_title" id="advertisement_title" value="<?= set_value('advertisement_title',$advertisement->advertisement_title) ?>">
				<p class="help-block"><?= $this->form_validation->error('advertisement_title'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('advertisement_description') ?>">
			<label class="control-label" for="advertisement_description">Advertisement Description </label>
			<div class="controls">
				<textarea name="advertisement_description" id="advertisement_description" class="input-xlarge span6" rows="6"><?= set_value('advertisement_description',$advertisement->advertisement_description) ?></textarea>
				<p class="help-block"><?= $this->form_validation->error('advertisement_description'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('url') ?>">
			<label class="control-label" for="url">URL</label>
			<div class="controls">
				<input type="text" class='span6' placeholder="" name="url" id="url" value="<?= set_value('url',$advertisement->url) ?>">
				<p class="help-block"><?= $this->form_validation->error('url'); ?></p>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('image_filename') ?>">
			<label class="control-label" for="image_filename">Image </label>
			<div class="controls">
				<div id="image_upload"></div>
				<br>
				<div>
					<?php if(!empty($advertisement->image_filename)): ?><a class="btn btn-primary" id="advert_image" href="/assets/media/advertisements/<?= $advertisement->image_filename; ?>" />View Image</a><?php endif; ?>
				</div>
			</div>
		</div>
		<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
			<label class="control-label" for="is_active">Active? <em>*</em></label>
			<div class="controls">
				<?php
				
				$options = array('' => 'Please Choose', '1' => 'Yes', '0' => 'No');
				
				echo form_dropdown("is_active", $options, set_value('is_active',$advertisement->is_active),"id='is_active' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('is_active'); ?></p>
			</div>
		</div>
		<div id="with_active_period_group" class="control-group <?= $this->form_validation->error_class('with_active_period') ?>" style="display:none">
			<label class="control-label" for="with_active_period">With Active Period?</label>
			<div class="controls">
				<?php
				
				$options = array('0' => 'No', '1' => 'Yes');
				
				echo form_dropdown("with_active_period", $options, set_value('with_active_period',$advertisement->with_active_period),"id='with_active_period' style='width:auto;'");
				
				?>
				<p class="help-block"><?= $this->form_validation->error('with_active_period'); ?></p>
			</div>
		</div>
		<?php
			if(set_value('active_from',$advertisement->active_from) == '')
			{
				$active_from_year = 0;
				$active_from_month = 0;
				$active_from_day = 0;
			}
			else
			{
				$active_from_year = date('Y',strtotime(set_value('active_from',$advertisement->active_from)));
				$active_from_month = date('m',strtotime(set_value('active_from',$advertisement->active_from)));
				$active_from_day = date('d',strtotime(set_value('active_from',$advertisement->active_from)));
			}
		?>
		<div id="active_from_group" class="control-group <?= $this->form_validation->error_class('active_from') ?>" style="display:none">
			<label class="control-label" for="active_from">Active From <em>*</em></label>
			<div id="active_from_container" class="controls form-inline wc-date">
				<?= form_dropdown('active_from_month', $months, $active_from_month, 'id="active_from_month" class="wc-date-month"') ?>
				<?= form_dropdown('active_from_day', $days, $active_from_day, 'id="active_from_day" class="wc-date-day"') ?>
				<?= form_dropdown('active_from_year', $years, $active_from_year, 'id="active_from_year" class="wc-date-year"') ?>
				<input type="hidden" id="active_from" name="active_from" value="<?= set_value('active_from',$advertisement->active_from); ?>" />
				<p class="help-block"><?= $this->form_validation->error('active_from'); ?></p>
			</div>
		</div>
		<?php
			if(set_value('active_until',$advertisement->active_until) == '')
			{
				$active_until_year = 0;
				$active_until_month = 0;
				$active_until_day = 0;
			}
			else
			{
				$active_until_year = date('Y',strtotime(set_value('active_until',$advertisement->active_until)));
				$active_until_month = date('m',strtotime(set_value('active_until',$advertisement->active_until)));
				$active_until_day = date('d',strtotime(set_value('active_until',$advertisement->active_until)));
			}
		?>
		<div id="active_until_group" class="control-group <?= $this->form_validation->error_class('active_until') ?>" style="display:none">
			<label class="control-label" for="active_until">Active Until <em>*</em></label>
			<div id="active_until_container" class="controls form-inline wc-date">
				<?= form_dropdown('active_until_month', $months, $active_until_month, 'id="active_until_month" class="wc-date-month"') ?>
				<?= form_dropdown('active_until_day', $days, $active_until_day, 'id="active_until_day" class="wc-date-day"') ?>
				<?= form_dropdown('active_until_year', $years, $active_until_year, 'id="active_until_year" class="wc-date-year"') ?>
				<input type="hidden" id="active_until" name="active_until" value="<?= set_value('active_until',$advertisement->active_until); ?>" />
				<p class="help-block"><?= $this->form_validation->error('active_until'); ?></p>
			</div>
		</div>
		<hr>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Edit Advertisement</button>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">

	var base_url = "<?=$this->config->item('base_url');?>";
	
	$(document).ready(function(){
		
		// uploader
		$('#image_upload').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions: ['.gif','.jpg', '.png'],
			target : base_url + '/admin/upload/process?location=<?=$_upload_url?>&filename=advertisement_<?= $advertisement->advertisement_id; ?>&ts=<?=time()?>&width=500&height=500',
			onComplete: function(files) {
				var image_file = files[0];
				var file_type = image_file.fileName.split('.').pop();
				
				b.request({
			       url: '/cms/advertisements/update_image',
			       data: {
						"filename": '<?= "advertisement_" . $advertisement->advertisement_id?>.'+file_type,
						"advertisement_id":<?= $advertisement->advertisement_id?>
					},
			       on_success: function(data) {		
			       }
			   });
			}
		});
		
		$('#active_from_month').change(function() {
			beyond.webcontrol.updateDateControl('active_from');
		});
		$('#active_from_day').change(function() {
			beyond.webcontrol.updateDateControl('active_from');
		});
		$('#active_from_year').change(function() {
			beyond.webcontrol.updateDateControl('active_from');
		});

		$('#active_from_month').trigger('change');
		$('#active_from_day').trigger('change');
		$('#active_from_year').trigger('change');
		
		$('#active_until_month').change(function() {
			beyond.webcontrol.updateDateControl('active_until');
		});
		$('#active_until_day').change(function() {
			beyond.webcontrol.updateDateControl('active_until');
		});
		$('#active_until_year').change(function() {
			beyond.webcontrol.updateDateControl('active_until');
		});

		$('#active_until_month').trigger('change');
		$('#active_until_day').trigger('change');
		$('#active_until_year').trigger('change');
		
		$("#is_active").trigger('change');
		$("#with_active_period").trigger('change');
	});
	
	$("#is_active").change(function(){
		if($(this).val() == true)
		{
			$("#with_active_period_group").css("display","");
		}
		else
		{
			$("#with_active_period_group").css("display","none");
			$("#active_from_group").css("display","none");
			$("#active_until_group").css("display","none");
			$("#with_active_period").val(false);
			$("#active_from_month").val(0);
			$("#active_from_day").val(0);
			$("#active_from_year").val(0);
			$("#active_until_month").val(0);
			$("#active_until_day").val(0);
			$("#active_until_year").val(0);
			$("#active_from").val("");
			$("#active_until").val("");
		}
	});
	
	$("#with_active_period").change(function(){
		if($(this).val() == true)
		{
			$("#active_from_group").css("display","");
			$("#active_until_group").css("display","");
		}
		else
		{
			$("#active_from_group").css("display","none");
			$("#active_until_group").css("display","none");
			$("#with_active_period").val(false);
			$("#active_from_month").val(0);
			$("#active_from_day").val(0);
			$("#active_from_year").val(0);
			$("#active_until_month").val(0);
			$("#active_until_day").val(0);
			$("#active_until_year").val(0);
			$("#active_from").val("");
			$("#active_until").val("");
		}
	});
</script>
<?php endif; ?>