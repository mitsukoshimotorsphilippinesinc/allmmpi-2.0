<form action='/admin/card_series/add' method='post' class='form-inline'>
	<fieldset >
		<div>
			<label class="control-label" for="series_number"><strong>Series Number <em>*</em></strong></label>
			<div class="controls">
				<input type="text" class='span3' maxlength='2' placeholder="01-99" name="series_number" id="series_number" value="<?= set_value('series_number') ?>">
			</div>
			<span class='label label-important' id='series_number_error' style='display:none;'>Series Number Field is required and must be between 1 to 99.</span>
		</div>
		
		
		<div>
			<label class="control-label" for="card_type_code"><strong>Code - Name <em>*</em></strong></label>
			<div class="controls">
				<?php

				$card_type_options = array('' => 'Please Select a Card Type');

				foreach($card_types as $card_type)
				{
					$card_type_options[$card_type->card_type_id] = $card_type->code . ' - ' . $card_type->name;
				}
				echo form_dropdown('card_type_id', $card_type_options, set_value('card_type_id'),'id="card_type_id" class="span5"');
				?>			
			</div>
			<span class='label label-important' id='card_type_id_error' style='display:none;'>Card Type Field is required.</span>
		</div>		
		
		<div>
			<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
			<div class="controls">
				<textarea type="text" class='span5' rows="4" placeholder="Description" name="description" id="description" value="<?= set_value('description') ?>"></textarea>
			</div>
			<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
		</div>
		
	</fieldset>
</form>

<script type="text/javascript">
		$("#series_number").keypress(function (e) {
          if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
                return false;
          }
        });
</script>