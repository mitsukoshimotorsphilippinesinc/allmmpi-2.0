<?php
	$upload_url = $this->config->item("media_url") . "/advertisements";
	$_upload_url = urlencode($upload_url);
?>
<h2>View Advertisement  <a href='/cms/advertisements' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($advertisement)): ?>
	<h3>Advertisement not found.</h3>
<?php else: ?>
<form action='' method='' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='advertisement_id' name='advertisement_id' value='<?= $advertisement->advertisement_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="advertisement_title">Advertisement Title</label>
		<div class="controls">
			<label class='data'><?= $advertisement->advertisement_title ?></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="advertisement_description">Advertisement Description</label>
		<div class="controls">
			<label class='data span5' style="margin-left: 0px;"><?= $advertisement->advertisement_description ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="url">URL</label>
		<div class="controls">
			<label class='data'><?= (!empty($advertisement->url)) ? "None" : anchor($advertisement->url) ?></label>
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
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($advertisement->is_active) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="with_active_period">With Active Period?</label>
		<div class="controls">
			<label class='data'><?= ($advertisement->with_active_period) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<?php if($advertisement->with_active_period): ?>
	<div class="control-group ">
		<label class="control-label" for="active_from">Active From</label>
		<div class="controls">
			<label class='data'><?= date("F j, Y",strtotime($advertisement->active_from))  ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="active_until">Active Until</label>
		<div class="controls">
			<label class='data'><?= date("F j, Y",strtotime($advertisement->active_until))  ?></label>
		</div>
	</div>
	<?php endif; ?>
</fieldset>
</form>
<script type="text/javascript">

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
	})
	
</script>
<?php endif; ?>
