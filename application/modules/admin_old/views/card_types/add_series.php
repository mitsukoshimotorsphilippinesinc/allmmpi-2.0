<form>
	<fieldset >

	
	
	
		$card_type_details ???
	
	
	
	
	
	
		<input id="last_card_number" class="span2" type="hidden" value="<?= $last_card_count; ?>" name="last_card_number" />
		<label class="control-label" for="last_name"><strong>Last Card Number: </strong></label>
		<div class="controls">
			<label type="text" class='span4' placeholder="Last Card Number" name="last_card_number" id="last_card_count" value=""><i><?= $last_card_count; ?></i></label>
		</div>
		<br/>	
		<hr/>
				

		<div>
			<label class="control-label" for="card_series_id"><strong>Card Series <em>*</em></strong></label>
			<div class="controls">
				<?php

				$voucher_type_options = array('' => 'Please Select a Card Series');

				foreach($card_series_details as $card_series)
				{
					$card_series_options[$card_series->card_series_id] = $card_series->series_number;
				}
				echo form_dropdown('card_series_id', $card_series_options, set_value('card_series_id'),'id="card_series_id" class="span2"');
				?>			
			</div>
			<span class='label label-important' id='card_series_id_error' style='display:none;'>Card Series Field is required.</span>
		</div>
		
		
		<input id="last_card_number" class="span2" type="hidden" value="" name="last_card_number" maxlength="4">		
		<label class="control-label" for="last_card_number"><strong>Last Card Number Of this series: </strong></label>
		<div class="control-label">
			<input type="text" class='span4' disabled="disabled" placeholder="Last Card Count" name="last_card_count" id="last_card_count" value="<?= $last_card_count; ?>">
		</div>
			

		<div  class="control-label">
			<label><strong>Quantity <em>*</em></strong></label>
			<input id="quantity" class="span2" type="text" value="" name="quantity" placeholder="1 - 9999" maxlength="4">
		</div>	
		<span class='label label-important' id='quantity_error' style='display:none;'>Quantity must be between 1 to 9999.</span>
		

	</fieldset>
</form>



		<div  class="control-label">
			<label><strong>Released To <em>*</em></strong></label>
			<input id="released_to" class="span5" type="text" value="" name="released_to" placeholder="Name here...">
		</div>	
		<span class='label label-important' id='released_to_error' style='display:none;'>Released To Field is required.</span>
		
	</fieldset>
</form>


<script type="text/javascript">
		$("#quantity").keypress(function (e) {
          if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
                return false;
          }
        });


		$('#card_series_id').change(function() {			
			// get the last card id for the series					   
			var card_series_id = $('#card_series_id').val();
		
			beyond.request({
				url : '/admin/card_series/get_last',
				data : {'_card_series_id' : card_series_id},
				on_success : function(data) {				
					$('#last_card_count').val(data.last_number);			
				}
			})
			return false;		
		})


</script>