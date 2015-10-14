<form id="submit_form" action='/admin/achievements/add' method='post' class='form-inline'>
<fieldset >
	<div>
		<label class="control-label" for="achievement_name"><strong>Achievement Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Achievement Name here..." name="achievement_name" id="achievement_name" value="<?= set_value('code') ?>">
		</div>
		<span class='label label-important' id='achievement_name_error' style='display:none;'>Achievement Name Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="max_pairs"><strong>Max Pairs <em>*</em></strong></label>
		<div class="controls">
			<input class='span2' maxlength="3" placeholder="Max Pairs here..." name="max_pairs" id="max_pairs" maxlength="3"></input>		
		</div>
		<span class='label label-important' id='max_pairs_error' style='display:none;'>Max Pairs Field is required.</span>
	</div>	
	<br/>
		
	<div>
		<label class="control-label" for="earnings_to_upgrade"><strong>Earnings To Upgrade <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Earnings To Upgrade here..." maxlength="9" name="earnings_to_upgrade" id="earnings_to_upgrade" value="<?= set_value('code') ?>">
		</div>
		<span class='label label-important' id='earnings_to_upgrade_error' style='display:none;'>Earnings To Upgrade Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="earnings_maintenance"><strong>Earnings Maintenance <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Earnings Maintenance here..." maxlength="9" name="earnings_maintenance" id="earnings_maintenance" value="<?= set_value('code') ?>">
		</div>
		<span class='label label-important' id='earnings_maintenance_error' style='display:none;'>Earnings Maintenance Field is required.</span>
	</div>
	<br/>
	
	<div>
		<label class="control-label" for="remarks"><strong>Remarks <em>*</em></strong></label>
		<div class="controls">
			<textarea class='span4' rows="3" placeholder="Remarks here..." name="remarks" id="remarks"></textarea> 			
		</div>
		<span class='label label-important' id='remarks_error' style='display:none;'>Remarks Field is required.</span>
	</div>	
</fieldset>
</form>

<script type="text/javascript">

  	$(document).ready(function() {
		$("#max_pairs").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
		
		$("#earnings_to_upgrade").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
		
		$("#earnings_maintenance").keypress(function (e) {
			if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
				return false;
			}
		});
	});	

</script>


