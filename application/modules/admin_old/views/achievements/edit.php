<?php if (empty($member_achievement_details)): ?>
	<h3>Member Achievement not found.</h3>
<?php else: ?>
<form id="submit_form" action='/admin/achievements/edit/<?= $member_achievement_details->member_achievement_id ?>' method='post' class='form-inline'>
<fieldset >
	<div>
		<label class="control-label" for="achievement_name"><strong>Achievement Name<em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="" name="achievement_name" disabled='disabled' id="achievement_name" value="<?= set_value('achievement_name', $member_achievement_details->achievement_name) ?>"> 		
		</div>
		<span class='label label-important' id='achievement_name_error' style='display:none;'>Achievement Name Field is required.</span>
	</div>
	<br/>
	<div>
		<label class="control-label" for="max_pairs"><strong>Max Pairs <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span2' placeholder="<?= $member_achievement_details->max_pairs ?>" name="max_pairs" id="max_pairs" value="<?= set_value('max_pairs', $member_achievement_details->max_pairs) ?>" maxlength="3"> 		
		</div>
		<span class='label label-important' id='max_pairs_error' style='display:none;'>Max Pairs Field is required.</span>
	</div>
	<br/>
	<div>
		<label class="control-label" for="earnings_to_upgrade"><strong>Earnings To Upgrade <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span2' placeholder="<?= $member_achievement_details->earnings_to_upgrade ?>" name="earnings_to_upgrade" id="earnings_to_upgrade" value="<?= set_value('earnings_to_upgrade', $member_achievement_details->earnings_to_upgrade) ?>" maxlength="9"> 		
		</div>
		<span class='label label-important' id='earnings_to_upgrade_error' style='display:none;'>Earnings to Upgrade Field is required.</span>
	</div>
	<br/>
	<div>
		<label class="control-label" for="earnings_maintenance"><strong>Earnings Maintenance <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span2' placeholder="<?= $member_achievement_details->earnings_maintenance ?>" name="earnings_maintenance" id="earnings_maintenance" value="<?= set_value('earnings_maintenance', $member_achievement_details->earnings_maintenance) ?>" maxlength="9"> 		
		</div>
		<span class='label label-important' id='earnings_maintenance_error' style='display:none;'>Earnings Maintenance Field is required.</span>
	</div>
	<br/>	
	<div>
		<label class="control-label" for="remarks"><strong>Remarks <em>*</em></strong></label>
		<div class="controls">
			<textarea rows="4" class='span4' placeholder="Place Remarks Here..." name="remarks" id="remarks"></textarea>		
		</div>
		<span class='label label-important' id='remarks_error' style='display:none;'>Remarks Field is required.</span>
	</div>
</fieldset>
</form>
<?php endif; ?>

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

