<h2>Delete Testimonial  <a href='/cms/testimonials' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($testimonial)): ?>
	<h3>Testimonial not found.</h3>
<?php else: ?>
<form action='/cms/testimonials/delete/<?= $testimonial->testimonial_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='testimonial_id' name='testimonial_id' value='<?= $testimonial->testimonial_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="title">Member Name</label>
		<div class="controls">
			<label class='data'><?= $testimonial->member_name ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="sub_title">Member Details</label>
		<div class="controls">
			<label class='data'><?= $testimonial->member_details ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="body">Body</label>
		<div class="controls">
			<label class='data'><?= $testimonial->body ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($testimonial->image_filename):?>
				<img id="member_image" style="width:100px; height:100px;" alt="" src="/assets/media/testimonials/<?= $testimonial->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_approved">Approved?</label>
		<div class="controls">
			<label class='data'><?= ($testimonial->is_approved) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary">Confirm Deletion</button>
		</div>
	</div>
</fieldset>
</form>
<?php endif; ?>
