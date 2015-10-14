<form id="submit_form" action='/inventory/product_lines/add' method='post' class='form-inline'>
    <fieldset>
        <div class="control-group <?= $this->form_validation->error_class('product_line') ?>">
            <label class="control-label" for="product_line"><strong>Name <em>*</em></strong></label>
            <div class="controls">
                <input type="text" class='span3' placeholder="Product Line" name="product_line" id="product_line" value="<?= set_value('product_line') ?>">
            </div>
			<span class='label label-important' id='product_line_error' style='display:none;'>Product Line Field is required.</span>
        </div>
        <div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
            <label class="control-label" for="is_visible"><strong>Visible? <em>*</em></strong></label>
            <div class="controls">
				<?= form_dropdown("is_visible",array("" => "Please Choose", 1 => "Yes", 0 => "No"),null,"id='is_visible'"); ?>
            </div>
			<span class='label label-important' id='is_visible_error' style='display:none;'>Visible Field is required.</span>
        </div>
        <div class="control-group">
            <div class="controls">
            </div>
        </div>
    </fieldset>
</form>

<script type="text/javascript">

	$("#add_product_line").click(function(){
		var action = "add";
		var type = "Product Line";
		validateAction(action, type);
	});
</script>
