<div class='alert alert-info'><h2>Account Details<span id='account_id_display' style='float:right;color:#990000;'><?=$account_details->account_id?></span></h2></div>
	<div class='row'>			
		<div class='span4'>
			<div class='alert alert-success' align='left' style='height:20px;'>
				<h3>Member Details</h3>
				<br/>
				<div class='form-horizontal'>
					<label><strong>Member ID:</strong></label>
					<label title='Member ID' style='margin-left:20px;' type='text' id='member_id_account' name='member_id_account'><?=$member_details->member_id?></label>
					
					<label><strong>Last Name:</strong></label>
					<label title='Last Name' style='margin-left:20px;' type='text' id='last_name_account' name='last_name_account'><?=$member_details->last_name?></label>
					
					<label><strong>First Name:</strong></label>
					<label title='First Name' style='margin-left:20px;' type='text' id='first_name_account' name='first_name_account'><?=$member_details->first_name?></label>
					
					<label><strong>Middle Name:</strong></label>
					<label title='Middle Name' style='margin-left:20px;' type='text' id='middle_name_account' name='middle_name_account'><?=$member_details->middle_name?></label>
					
					<label><strong>Email:</strong></label>
					<label title='Email' style='margin-left:20px;' type='text' id='email_account' name='email_account'><?=$member_details->email?></label>
				
					<label><strong>Mobile Number:</strong></label>
					<label title='Mobile Number' style='margin-left:20px;' type='text' id='mobile_number_account' name='mobile_number_account'><?=$member_details->mobile_number?></label>
				</div>
			</div>	
		</div>									
								
		<div class='span7'>
			<div class='alert alert-success' align='left' style='height:20px;'>
				<h3>Account Details</h3>
				<br/>
				<div class='form-horizontal'>
					
					<label><strong>Status:</strong></label>
					<label title='Released To' style='margin-left:20px;' type='text' id='account_status' name='account_status'><?=$account_status_details->account_status?></label>
					
					<label><strong>Sponsor ID:</strong></label>
					<label title='Released To' style='margin-left:20px;' type='text' id='sponsor_id' name='sponsor_id'><?=$account_details->sponsor_id?></label>
					
					<label><strong>Upline ID:</strong></label>
					<label title='Date Released' style='margin-left:20px;' type='text' id='upline_id' name='upline_id'><?=$account_details->upline_id?></label>
					
					<label><strong>Account Type:</strong></label>
					<label title='Issued To' style='margin-left:20px;' type='text' id='account_type' name='account_type'><?=$account_type_details->account_type?> -  <?=$account_type_details->description?></label>
					
					<label><strong>Points:</strong></label>
					<table class='table table-striped table-bordered'>
						<thead>
							<tr>
								<td><strong>Left SP</strong></td>
								<td><strong>Right SP</strong></td>
								<td><strong>Pair SP</strong></td>
							
								<td><strong>Left RS</strong></td>
								<td><strong>Right RS</strong></td>
								<td><strong>Pair RS</strong></td>
								
								<td><strong>Left VP</strong></td>
								<td><strong>Right VP</strong></td>
								<td><strong>Pair VP</strong></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong><?=$account_details->left_sp?></strong></td>
								<td><strong><?=$account_details->right_sp?></strong></td>
								<td><strong><?=$account_details->pairs_sp?></strong></td>
								
								<td><strong><?=$account_details->left_rs?></strong></td>
								<td><strong><?=$account_details->right_rs?></strong></td>
								<td><strong><?=$account_details->pairs_rs?></strong></td>
								
								<td><strong><?=$account_details->left_vp?></strong></td>
								<td><strong><?=$account_details->right_vp?></strong></td>
								<td><strong><?=$account_details->pairs_vp?></strong></td>
							</tr>
						</tbody>
					</table>	
					
				</div>									
			</div>
		</div>																					
	</div>									