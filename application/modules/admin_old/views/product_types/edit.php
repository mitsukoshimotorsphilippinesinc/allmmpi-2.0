<?php if (empty($product_type)): ?>
<h3>Product Type not found.</h3>
<?php else: ?>
<form id="submit_form" action='/admin/product_types/edit/<?= $product_type->product_type_id ?>' method='post' class='form-inline'>
    <fieldset>
    	<input type='hidden' id='orig_product_type' name='orig_product_type' value='<?= $product_type->product_type_id ?>' />
        <div class="control-group <?= $this->form_validation->error_class('name') ?>">
            <label class="control-label" for="name"><strong>Name <em>*</em></strong></label>
            <div class="controls">
                <input type="text" class='span3' placeholder="Name" name="name" id="name" value="<?= set_value('name',$product_type->name) ?>">
            </div>
			<span class='label label-important' id='name_error' style='display:none;'>Name Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_visible') ?>">
			<label class="control-label" for="is_visible"><strong>Visible? <em>*</em></strong></label>
			<div class="controls">
				<?= form_dropdown("is_visible",array("" => "Please Choose", 1 => "Yes", 0 => "No"),$product_type->is_visible,"id='is_visible'"); ?>
            </div>
			<span class='label label-important' id='is_visible_error' style='display:none;'>Visible Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_regular_buyable') ?>">
			<label class="control-label" for="is_regular_buyable"><strong>Regular Buyable? <em>*</em></strong></label>
			<div class="controls">
				<?= form_dropdown("is_regular_buyable",array("" => "Please Choose", 1 => "Yes", 0 => "No"),$product_type->is_regular,"id='is_regular_buyable'"); ?>
            </div>
			<span class='label label-important' id='is_regular_buyable_error' style='display:none;'>Regular Buyable Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_gc_buyable') ?>">
            <label class="control-label" for="is_gc_buyable"><strong>GC Buyable? <em>*</em></strong></label>
            <div class="controls">
				<?= form_dropdown("is_gc_buyable",array("" => "Please Choose", 1 => "Yes", 0 => "No"),$product_type->is_gc_buyable,"id='is_gc_buyable'"); ?>
            </div>
			<span class='label label-important' id='is_gc_buyable_error' style='display:none;'>GC Buyable Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_package') ?>">
            <label class="control-label" for="is_package"><strong>Is Package? <em>*</em></strong></label>
            <div class="controls">
				<?= form_dropdown("is_package",array("" => "Please Choose", 1 => "Yes", 0 => "No"),$product_type->is_package,"id='is_package'"); ?>
            </div>
			<span class='label label-important' id='is_package_error' style='display:none;'>Is Package Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_cpoints') ?>">
            <label class="control-label" for="is_cpoints"><strong>Is C Points? <em>*</em></strong></label>
            <div class="controls">
				<?= form_dropdown("is_cpoints",array("" => "Please Choose", 1 => "Yes", 0 => "No"),$product_type->is_cpoints,"id='is_cpoints'"); ?>
            </div>
			<span class='label label-important' id='is_cpoints_error' style='display:none;'>Is C Points Field is required.</span>
        </div>
		<div class="control-group <?= $this->form_validation->error_class('is_igpsm') ?>">
            <label class="control-label" for="is_igpsm"><strong>Is IGPSM? <em>*</em></strong></label>
            <div class="controls">
				<?= form_dropdown("is_igpsm",array("" => "Please Choose", 1 => "Yes", 0 => "No", 2 => "Both"),$product_type->is_igpsm,"id='is_igpsm'"); ?>
            </div>
			<span class='label label-important' id='is_igpsm_error' style='display:none;'>Is IGPSM Field is required.</span>
        </div>
        <div class="control-group">
            <div class="controls">
            </div>
        </div>
    </fieldset>
</form>
<?php endif; ?>


<script type="text/javascript">

	$("#edit_product_type").click(function(){
		var action = "edit";
		var type = "Item Type";
		validateAction(action, type);
	});
</script>
