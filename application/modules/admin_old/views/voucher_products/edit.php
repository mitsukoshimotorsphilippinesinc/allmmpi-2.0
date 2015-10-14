<?php if (empty($voucher_product)): ?>
	<h3>Voucher Product not found.</h3>
<?php else: ?>
<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/products";
	$_upload_url = urlencode($upload_url);
?>
<form action='/admin/voucher_products/edit/<?= $voucher_product->voucher_product_id ?>' method='post' class='form-inline'>
	<fieldset >
		
		<div class="control-group <?= $this->form_validation->error_class('voucher_product_name') ?>">
			<label class="control-label" for="standard_retail_price"><strong>Product Name <em>*</em></strong></label>
			<div class="controls">
				<textarea type="text" class='span5' rows='3' placeholder="Product Name" name="voucher_product_name" id="voucher_product_name" value=""><?= set_value('voucher_product_name',$voucher_product->voucher_product_name) ?></textarea>
			</div>
			<span class='label label-important' id='voucher_product_name_error' style='display:none;'>Product Name Field is required.</span>
		</div>
		
		<div class="control-group <?= $this->form_validation->error_class('item_id') ?>">
			<label class="control-label" for="item_id"><strong>Item <em>*</em></strong></label>
			<div class="controls">
				<?php

				$voucher_type_options = array('' => 'Please Select an Item');

				foreach($voucher_types as $voucher_type)
				{
					$voucher_type_options[$voucher_type->voucher_type_id] = $voucher_type->code . ' - ' . $voucher_type->name;
				}
				echo form_dropdown('voucher_type_id',$voucher_type_options,set_value('voucher_type_id',$voucher_product->voucher_type_id),'id="voucher_type_id" class="span4"');
				?>				
			</div>
			<span class='label label-important' id='voucher_type_id_error' style='display:none;'>Voucher Type Field is required.</span>
		</div>		
	</fieldset>
</form>
<?php endif; ?>
<script type="text/javascript">

	var get_items = function(item_name,ajax_call)
	{
		beyond.request({
			url: '/admin/products/get_items',
			data: {
				'item_name': item_name
			},
			on_success: function(data)
			{
				if(data.status == "ok")
				{
					var items = _.map(data.data,function(item){
						return {
							label: item.item_name,
							value: item.item_name,
							item_id: item.item_id
						};
					});
					if(_.isFunction(ajax_call)) ajax_call.call(this,items);
				}
			}
		});
	};

	$("#item_name").keyup(function(){
		if($(this).val() != "")
		{
			get_items($(this).val(),function(data){
				$("#item_name").autocomplete({
					source: data,
					focus: function()
					{
						return false;
					},
					select: function(event, ui)
					{
						$("#item_name").val(ui.item.label);
					}
				});
			});
		}
	});
</script>