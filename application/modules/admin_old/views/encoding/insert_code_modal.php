<form>
	<fieldset>
		<label class="control-label" for="card_code"><strong>Card Code <em>*</em></strong></label>
		<div class="control-label">
			<input type="text" class='span4'  placeholder="Card Code" name="card_code" id="card_code" value="">
		</div>
		<span class='label label-important' id='card_code_error' style='display:none;'>Card Code Field is required.</span>
	
		<div>
			<label class="control-label" for="position"><strong>Position <em>*</em></strong></label>
			<select id="position">
				<option value="left">Left</option>
				<option value="right">Right</option>
			</select>
			<span class='label label-important' id='position_error' style='display:none;'>Position Field is required.</span>
		</div>
		
	</fieldset>
</form>

