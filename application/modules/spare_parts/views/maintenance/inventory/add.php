<?php	
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Add New Inventory  <a href='/spare_parts/maintenance/inventory' class='btn btn-small' style="float:right;">Back</a></h2>
<hr/>
<form action='/spare_parts/maintenance/add_inventory' method='post' class='form-horizontal'>
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
				<button type="submit" class="btn btn-primary">Add New Inventory</button>
			</div>
		</div>
	</fieldset>
</form>
<?php
	$this->load->view('template_search_item_inventory');
?>

<script type="text/javascript">

	$(document).on("click",'#item_details',function(e) {	
		e.preventDefault();		
		search_item();

	});

	$('.number').keypress(function(event) {
		if ((event.which != 0) && (event.which != 8) && (event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        	event.preventDefault();
    	}    
	});

	var search_item = function() {
		
		document.search_item_modal = b.modal.create({
			title: "Search Item",
			width: 900,
			html: _.template($("#search-item-inventory-template").html(),{}),
		});
		
		document.search_item_modal.show();
		
		$("#item_type_search").change(function(e) {
			var search_key = $.trim($("#txt_item_search_key").val());
			if(search_key != "") $("#btn_item_search").trigger("click");		
		})
		
		$("#btn_item_search").click(function(e) {
			e.preventDefault();
			
			var search_key = $.trim($("#txt_item_search_key").val());
			var item_type_id = $.trim($("#item_type_search").val());

			var warehouse_option = $("#item_warehouse_option").val();

			if (search_key.length == 0) 
			{
				return;
			}
			
			b.request({
				url: "/spare_parts/search_item_inventory",
				data: {
					"search_key": search_key,
				},
				on_success: function(data) {

					var items = data.data.items;

					$("#item-inventory-listing").html(_.template($("#item-inventory-list-template").html(),{"items": items}));
					
					$("#item-inventory-listing .btn-select-item").click(function(e) {
						e.preventDefault();
						var item_id = $(this).data("id");
						
						$("#item_details").val($(this).data("description"));
						$("#sku").val($(this).data("sku"));
						
						document.search_item_modal.hide();
						document.search_item_modal = null;
					});
					
				} 
			});
		});
	}



</script>