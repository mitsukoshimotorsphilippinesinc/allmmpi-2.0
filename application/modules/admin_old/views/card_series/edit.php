<?php if (empty($card_series)): ?>
	<h3>Card Series not found.</h3>
<?php else: ?>
<form action='/admin/card_series/edit/<?= $card_series->card_series_id ?>' method='post' class='form-inline'>
	<fieldset >
		
		<div>
			<label class="control-label" for="series_number"><strong>Series Number <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span3' disabled='disabled' placeholder="" name="series_number" id="series_number" value="<?= set_value('card_series_name',$card_series->series_number) ?>"/>
			</div>
			<span class='label label-important' id='card_series_name_error' style='display:none;'>Series Number Field is required and must be between 1 to 99.</span>
		</div>
		
		<div>
			<label class="control-label" for="card_type_code"><strong>Code - Name <em>*</em></strong></label>
			<div class="controls">
				<?php

				$voucher_type_options = array('' => 'Please Select a Card Type');

				foreach($card_types as $card_type)
				{
					$card_type_options[$card_type->card_type_id] = $card_type->code . ' - ' . $card_type->name;
				}
				echo form_dropdown('card_type_id', $card_type_options, set_value('card_type_id',$card_series->card_type_id),'id="card_type_id" class="span5"');
				?>			
			</div>
			<span class='label label-important' id='card_type_id_error' style='display:none;'>Card Type Field is required.</span>
		</div>
		
		<div>
			<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
			<div class="controls">
				<textarea type="text" class='span5' rows="4" placeholder="Description" name="description" id="description" value=""><?= set_value('description',$card_series->description) ?></textarea>
			</div>
			<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
		</div>
		
	</fieldset>
</form>
<?php endif; ?>