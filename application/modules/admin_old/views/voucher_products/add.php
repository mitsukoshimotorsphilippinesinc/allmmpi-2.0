<form action='/admin/voucher_products/add' method='post' class='form-inline'>
	<fieldset >
		<div class="control-group <?= $this->form_validation->error_class('voucher_product_name') ?>">
			<label class="control-label" for="voucher_product_name"><strong>Product Name <em>*</em></strong></label>
			<div class="controls">
				<textarea type="text" class='span5' rows='3' placeholder="Voucher Product Name" name="voucher_product_name" id="voucher_product_name" value="<?= set_value('member_price') ?>"></textarea>
			</div>
			<span class='label label-important' id='voucher_product_name_error' style='display:none;'>Product Name Field is required.</span>
		</div>
		
		<div class="control-group <?= $this->form_validation->error_class('item_id') ?>">
			<label class="control-label" for="item_id"><strong>Voucher Type <em>*</em></strong></label>
			<div class="controls">
				<?php

				$voucher_type_options = array('' => 'Please Select a Voucher Type');

				foreach($voucher_types as $voucher_type)
				{
					$voucher_type_options[$voucher_type->voucher_type_id] = $voucher_type->code . ' - ' . $voucher_type->name;
				}
				echo form_dropdown('voucher_type_id',$voucher_type_options,set_value('voucher_type_id'),'id="voucher_type_id" class="span4"');
				?>			
			</div>
			<span class='label label-important' id='voucher_type_id_error' style='display:none;'>Voucher Type Field is required.</span>
		</div>		
	</fieldset>
</form>
