<fieldset >		
	<div>
		<label class='control-label' for=''><strong>Request Code</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $dealer_request->request_code; ?>">
		</div>	
	</div>
	
	<?php
		// get dealer name
		$dealer = $this->spare_parts_model->get_dealer_by_id($dealer_request->dealer_id);

		if (empty($dealer)) {			
			$dealer_name = 'N/A';
		} else 	{
			$dealer_name = $dealer->complete_name;
		}

	?>	

	<?php
		// get agent name
		$agent = $this->spare_parts_model->get_agent_by_id($dealer_request->agent_id);

		if (empty($agent)) {
			$agent_name = 'N/A';
		} else {
			$agent_name = $agent->complete_name;
		}

	?>	


	<div class='control-group'>
		<label for='dealer-name' class='control-label'><strong>Dealer Name</strong></label>
		<div class='controls'>
			<input style="width:300px;" disabled="disabled" value="<?= $dealer_name; ?>">
		</div>
	</div>								
	
	<div class='control-group'>
		<label for='agent-name' class='control-label'><strong>Agent Name</strong></label>
		<div class='controls'>
			<input style="width:300px;" disabled="disabled" value="<?= $agent_name; ?>">
		</div>
	</div>										
		
	<div>
		<label class='control-label' for='payout type'><strong>Status</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $dealer_request->status; ?>">
		</div>	
	</div>
	
	<div>
		<label class='control-label' for='is official'><strong>Purchase Order Number</strong></label>
		<div class="controls">
			<input disabled="disabled" value="<?= $dealer_request->purchase_order_number; ?>">
		</div>	
	</div>
													
</fieldset>
