<div class='control-group'>
	<div class="control-group <?= $this->form_validation->error_class('qty'); ?>">
		<label class='control-label' for='product_id'><strong>Product Name <em>*</em></strong></label>
		<div class='controls'>
			<?php
				$products_array = array();
				$products_ids = array();
				foreach($products as $key => $p)
				{
					$products_array[$p->product_name] = $p->product_name;
					$products_ids[$p->product_name] = $p->product_id;
				}
				$products_array = array_combine($products_ids,$products_array);
				echo form_dropdown("product_id",$products_array,null,"id='product_id' style='width:auto;'");
			?>
		</div>
		<span class='label label-important' id='product_id_error' style='display:none;'>Product Name is required</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('qty'); ?>">
		<label class='control-label' for='qty'><strong>Quantity <em>*</em></strong></label>
		<div class='controls'>
			<input type='text' class='span2' placeholder='Quantity' name='qty' id='qty' value='0'>
		</div>
		<span class='label label-important' id='qty_error' style='display:none;'>Quantity is required</span>
	</div>
</div>

<script type="text/javascript">

</script>