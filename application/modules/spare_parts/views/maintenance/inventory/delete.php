<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Delete Inventory  <a href='/spare_parts/maintenance/inventory' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/spare_parts/maintenance/edit_inventory/<?= $item_details->item_id ?>' method='post' class='form-horizontal'>
	<fieldset >

		<div class="control-group <?= $this->form_validation->error_class('warehouse_id') ?>">
			<label class="control-label" for="warehouse_id">Warehouse Name <em>*</em></label>
			<div class="controls">
				<?php
				
				$warehouse_options = array();
				$warehouse_options = array('' => 'None');
				foreach ($warehouse_details as $wd) {
				 	$warehouse_options[$wd->warehouse_id] = $wd->warehouse_name;
				}				
				?>

				<?= form_dropdown('warehouse_id',$warehouse_options, set_value(NULL, 'warehouse_id'),'id="warehouse_id"') ?>
									
				<p class="help-block"><?= $this->form_validation->error('warehouse_id'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('item_details') ?>">
			<label class="control-label" for="item_details">Item Details <em>*</em></label>
			<div class="controls">
				<input type="text" readonly="readonly" class='span2' name="sku" id="sku" value="<?= set_value('sku') ?>">
				<input type="text" readonly="readonly" class='span6' placeholder="Click here to Select Item..." name="item_details" id="item_details" value="<?= set_value('item_details') ?>">	
				<p class="help-block"><?= $this->form_validation->error('item_details'); ?></p>						
			</div>
		</div>						

		<div class="control-group <?= $this->form_validation->error_class('good_quantity') ?>">
			<label class="control-label" for="good_quantity">Good Quantity <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2 number' placeholder="Good Quantity" name="good_quantity" id="good_quantity" value="<?= set_value('good_quantity') ?>">
				<p class="help-block"><?= $this->form_validation->error('good_quantity'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('bad_quantity') ?>">
			<label class="control-label" for="bad_quantity">Bad Quantity <em>*</em></label>
			<div class="controls">
				<input type="text" class='span2 number' placeholder="Bad Quantity" name="bad_quantity" id="bad_quantity" value="<?= set_value('bad_quantity') ?>">
				<p class="help-block"><?= $this->form_validation->error('bad_quantity'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('rack_location') ?>">
			<label class="control-label" for="rack_location">Rack Location <em>*</em></label>
			<div class="controls">
				<?php
				
				$rack_location_options = array();
				$rack_location_options = array('' => 'None');
				foreach ($rack_location_details as $rld) {
				 	$rack_location_options[$rld->rack_location] = $rld->rack_location;
				}				
				?>

				<?= form_dropdown('rack_location',$rack_location_options, NULL,'id="rack_location"') ?>
									
				<p class="help-block"><?= $this->form_validation->error('rack_location'); ?></p>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Delete Inventory</button>
			</div>
		</div>
	</fieldset>
</form>
<?php
	$this->load->view('template_search_item_inventory');
?>