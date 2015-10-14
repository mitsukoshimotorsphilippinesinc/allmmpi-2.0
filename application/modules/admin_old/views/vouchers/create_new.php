<form action='/admin/vouchers/create_new' method='post' class='form-inline'>
	<fieldset >
	    <div>	
			<label class="control-label" for="item_id"><strong>Voucher Product <em>*</em></strong></label>
			<div class="controls">
				<?php

				$voucher_product_options = array('' => 'Please Select a Voucher Product');

				foreach($voucher_products as $voucher_product)
				{
					$voucher_product_options[$voucher_product->voucher_product_id] = $voucher_product->voucher_product_name;
				}
				echo form_dropdown('voucher_product_id',$voucher_product_options,set_value('voucher_product_id'),'id="voucher_product_id" class="span5"');
				
				?>			
			</div>
			<span class='label label-important' id='voucher_product_id_error' style='display:none;'>Voucher Product Field is required.</span>
		</div>
		<br/>
		<div>
			<label><strong>Selected Voucher Product</strong></label>
			<br/>
			<textarea style="width:500px;" id="selected_voucher_area" type="text" value="" name="selected_voucher_area" placeholder="" readonly></textarea>
		</div>
		<br/>			
		<div>
			<label><strong>Quantity <em>*</em></strong></label>
			<br/>
			<input id="quantity" class="span2" type="text" value="" name="quantity" placeholder="" maxlength="4">
		</div>	
		<span class='label label-important' id='quantity_error' style='display:none;'>Quantity must be between 1 to 9999.</span>		
	</fieldset>
</form>

<script type="text/javascript">
		$("#quantity").keypress(function (e) {
          if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
                return false;
          }
        });

		$("#voucher_product_id").change(function() {
			
			
			b.request({
				url : '/admin/vouchers/get_voucher_product',
				data : {
						'_voucher_product_id' : $("#voucher_product_id").val()
						},
				on_success : function(data) {
					$("#selected_voucher_area").html(data.html);
				}
			})
			
			
			
		});
		
</script>