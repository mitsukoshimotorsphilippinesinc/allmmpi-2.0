<div class='alert alert-info'><h2>Card Details<span style='float:right;color:#990000;'>&nbsp;[<?=$card_master_details->status?>]</span><span id='card_id_display' style='float:right;'><?=$_card_id?></span></h2></div>
	<div class='row'>			
		<div class='span4'>
			<div class='alert alert-success' align='left' style='height:20px;'>
				<h3>Owner Information</h3>
				<br/>
				<div style='float:left;' class='form-horizontal'>
					<label><strong>Member ID:</strong></label>
					<label title='Member ID' type='text' id='member_id_card' name='member_id_card'><?=$member_details->member_id?></label>
	
					<label><strong>Last Name:</strong></label>
					<label title='Last Name' type='text' id='last_name_card' name='last_name_card'><?=$member_details->last_name?></label>
	
					<label><strong>First Name:</strong></label>
					<label title='First Name' type='text' id='first_name_card' name='first_name_card'><?=$member_details->first_name?></label>
	
					<label><strong>Middle Name:</strong></label>
					<label title='Middle Name' type='text' id='middle_name_card' name='middle_name_card'><?=$member_details->middle_name?></label>
	
					<label><strong>Email:</strong></label>
					<label title='Email' type='text' id='email_card' name='email_card'><?=$member_details->email?></label>
	
					<label><strong>Mobile Number:</strong></label>
					<label title='Mobile Number' type='text' id='mobile_number_card' name='mobile_number_card'><?=$member_details->mobile_number?></label>
				</div>
			</div>	
		</div>
		
		<div class='span7'>
			<div class='alert alert-success' align='left' style='height:20px;'>
				<h3>Card Information</h3>
				<br/>
				<div style='float:left;' class='form-horizontal'>
					<label><strong>Type:</strong></label>
					<label title='Type' type='text' id='type' name='type'><?=$card_master_details->type?></label>
	
					<label><strong>Released To:</strong></label>
					<label title='Released To' type='text' id='released_to_card' name='released_to_card'><?=$card_master_details->released_to?></label>
	
					<label><strong>Date Released:</strong></label>
					<label title='Date Released' type='text' id='date_released_card' name='date_released_card'><?=$card_master_details->released_timestamp?></label>
	
					<label><strong>Issued To:</strong></label>
					<label title='Issued To' type='text' id='issued_to_card' name='issued_to_card'><?=$card_master_details->issued_to?></label>
	
					<label><strong>Date Issued:</strong></label>
					<label title='Date Issued' type='text' id='date_issued_card' name='date_issued_card'><?=$card_master_details->issued_timestamp?></label>
	
					<label><strong>Date Activated:</strong></label>
					<label title='Date Activated' type='text' id='date_activated_card' name='date_activated_card'><?=$card_master_details->activate_timestamp?></label>
	
					<label><strong>Date Used:</strong></label>
					<label title='Date Used' type='text' id='date_used_card' name='date_used_card'>1234567890</label>
				</div>
				
			</div>
		</div>									
	</div>															
</div>										