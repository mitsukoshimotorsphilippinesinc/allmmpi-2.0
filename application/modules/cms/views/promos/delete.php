<h2>Delete Featured Promo  <a href='/cms/promos' class='btn btn-small' >Back</a></h2>
<hr/>
<?php if (empty($promo)): ?>
	<h3>Featured Promo not found.</h3>
<?php else: ?>
<form action='/cms/promos/delete/<?= $promo->promo_id ?>' method='post' class='form-horizontal'>
<fieldset >
	<input type='hidden' id='promo_id' name='promo_id' value='<?= $promo->promo_id ?>' />
	<div class="control-group ">
		<label class="control-label" for="promo_title">Promo Title</label>
		<div class="controls">
			<label class='data'><?= $promo->promo_title ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="promo_description">Promo Description</label>
		<div class="controls">
			<label class='data'><?= $promo->promo_description ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="promo_text">Promo Text</label>
		<div class="controls">
			<label class='data'><?= $promo->promo_text ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="short_body">Image</label>
		<div class="controls">
			<label class='data'>
			<?php if($promo->image_filename):?>
				<img id="member_image" style="width:100px; height:100px;" alt="" src="/assets/media/featured/<?= $promo->image_filename ?>">
			<?php endif; ?>
			</label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="url">Slug</label>
		<div class="controls">
			<label class='data'><?= $promo->url ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="is_active">Active?</label>
		<div class="controls">
			<label class='data'><?= ($promo->is_active) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="with_active_period">With Active Period?</label>
		<div class="controls">
			<label class='data'><?= ($promo->with_active_period) ? 'Yes' : 'No'  ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="active_start">Start Date</label>
		<div class="controls">
			<label class='data'><?= $promo->active_start ?></label>
		</div>
	</div>
	<div class="control-group ">
		<label class="control-label" for="active_end">End Date</label>
		<div class="controls">
			<label class='data'><?= $promo->active_end ?></label>
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
