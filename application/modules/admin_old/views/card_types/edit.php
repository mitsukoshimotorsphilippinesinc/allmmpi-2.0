<?php if (empty($card_type)): ?>
	<h3>Card Type not found.</h3>
<?php else: ?>
<form id="submit_form" action='/admin/card_types/edit/<?= $card_type->card_type_id ?>' method='post' class='form-inline'>
<fieldset >
	<div>
		<label class="control-label" for="card_type_code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="card_type_code" disabled='disabled' id="card_type_code" value="<?= set_value('card_type_code', $card_type->code) ?>"> 		
		</div>
		<span class='label label-important' id='facility_name_error' style='display:none;'>Card Type Code Field is required.</span>
	</div>
	<div>
		<label class="control-label" for="card_type_code"><strong>Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="card_type_name" id="card_type_name" value="<?= set_value('card_type_name', $card_type->name) ?>"> 		
		</div>
		<span class='label label-important' id='facility_name_error' style='display:none;'>Card Type Name Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="series_number"><strong>Card Series <em>*</em></strong></label>
		<div class="controls">
			<?php
			$card_series = "";
			
			// get current series of card type
			$where = "card_type_id = '{$card_type->card_type_id}'";
			$card_series_details = $this->cards_model->get_card_series($where,null,"series_number");
			
			if (count($card_series_details) == 0) {
				$card_series = "NONE";
			} else {	
				$cnt = 1;									
				foreach ($card_series_details as $csd) {
					if ($cnt == 1) {
						$card_series = $csd->series_number; 
					} else {
						$card_series = $card_series . ", " . $csd->series_number;
					}
					$cnt++;
				}
			}
			?>
			
			<input type="text" class='span4' placeholder="" name="series_number_list" disabled='disabled' id="series_number_list" value="<?= $card_series ?>"> 
			<br/>
			<!--select id="series_number" class='span1'>
				<?php
				for ($cnt=1;$cnt<100;$cnt++) {
					if ($cnt < 10) {
						$display_num = '0' . $cnt;
					} else {
						$display_num = $cnt;
					}
					
					$where="cast(series_number as unsigned) = '{$cnt}'";
					$card_series = $this->cards_model->get_card_series($where);
					
					if (count($card_series) == 0) {
				?>
						<option value ='<?=$cnt?>'><?=$display_num?></option>	
				<?php
					}			
				} 				
				?>			 
			</select-->		
		</div>
	
		<span class='label label-important' id='series_number_error' style='display:none;'>Series Number Field is required and must be between 1 to 99.</span>
	</div>
    <br/>
	
	<div>
		<label class="control-label" for="sale_package_option"><strong>Type <em>*</em></strong></label>
		<div class="controls">
			<select id="sales_package_option" class='span2'>
			<?php
			  	if ($card_type->is_package == 0) {
			?>
					<option value ="0" selected="selected">Sales Card</option>
			  		<option value ="1" >Package Card</option>
			<?php
				} else {
			?>		
					<option value ="0" >Sales Card</option>
		  			<option value ="1" selected="selected">Package Card</option> 
			<?php
				}
			?>
			</select>
		</div>
		<span class='label label-important' id='sale_package_option_error' style='display:none;'>Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea rows="4" class='span4' placeholder="" name="description" id="description"><?= set_value('description', $card_type->description) ?></textarea>		
		</div>
		<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
	</div>
</fieldset>
</form>
<?php endif; ?>

