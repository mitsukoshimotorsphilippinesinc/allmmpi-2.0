<form id="submit_form" action='/admin/card_types/add' method='post' class='form-inline'>
<fieldset >
	<div>
		<label class="control-label" for="code"><strong>Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Card Type Code here..." name="card_type_code" id="card_type_code" value="<?= set_value('code') ?>">
		</div>
		<span class='label label-important' id='card_type_code_error' style='display:none;'>Card Type Code Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="facility_description"><strong>Name <em>*</em></strong></label>
		<div class="controls">
			<textarea class='span4' rows="2" placeholder="Card Type Name here..." name="card_type_name" id="card_type_name" ></textarea>		
		</div>
		<span class='label label-important' id='card_type_name_error' style='display:none;'>Card Type Name Field is required.</span>
	</div>	
	<br/>
	
	<div>
		<label class="control-label" for="series_number"><strong>Card Series <em>*</em></strong></label>
		<div class="controls">
			<select id="series_number" class='span1'>
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
			</select>
		</div>
		<span class='label label-important' id='series_number_error' style='display:none;'>Series Number Field is required and must be between 1 to 99.</span>
	</div>
	</br/>
		
	<div>
		<label class="control-label" for="sales_package_option"><strong>Type <em>*</em></strong></label>
		<div class="controls">
			<select id="sales_package_option" class='span2'>
			  <option value ="0">Sales Card</option>
			  <option value ="1">Package Card</option>			 
			</select>
		</div>
		<span class='label label-important' id='sales_package_option_error' style='display:none;'>Type Field is required.</span>
	</div>
	<br/>	
	<div>
		<label class="control-label" for="description"><strong>Description <em>*</em></strong></label>
		<div class="controls">
			<textarea class='span4' rows="3" placeholder="Description here..." name="description" id="description"></textarea> 			
		</div>
		<span class='label label-important' id='description_error' style='display:none;'>Description Field is required.</span>
	</div>	
</fieldset>
</form>
