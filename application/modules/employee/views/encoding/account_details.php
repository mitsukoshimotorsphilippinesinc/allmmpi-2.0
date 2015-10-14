<label><strong>Points:</strong></label>
<table class='table table-striped table-bordered'>
	<thead>	
		<tr>
			<td style='text-align:center;'><strong>Details</strong></td>
			<td style='text-align:center;'><strong>Points</strong></td>			
		</td>		
	</thead>
	<tbody>
		<tr>		
			<td>Left RS</td>
			<td style='text-align:right;'><?=number_format($account_details->left_rs)?></td>
		</tr>
		
		<tr>		
			<td>Right RS</td>
			<td style='text-align:right;'><?=number_format($account_details->right_rs)?></td>			
		</tr>
	
		<tr>		
			<td>Pairs RS</td>
			<td style='text-align:right;'><?=number_format($account_details->pairs_rs)?></td>			
		</tr>
	</tbody>
</table>

<label><strong>Maintenance:</strong></label>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<td style='text-align:center;'><strong>Type</strong></td>
			<td id='series_73_header' style='text-align:center;'><strong>73 Series</strong></td>
			<td style='text-align:center;'><strong>75 Series</strong></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Monthly</td>
			<td id='series_73_info_monthly' style='text-align:right;'><?= $account_details->ms_monthly_maintenance_ctr ?></td>
			<td style='text-align:right;'><?= $account_details->monthly_maintenance_ctr ?></td>
		</tr>

		<tr>
			<td>Annual</td>
			<td id='series_73_info_annual' style='text-align:right;'><?= $account_details->ms_annual_maintenance_ctr ?></td>
			<td style='text-align:right;'><?= $account_details->annual_maintenance_ctr ?></td>
		</tr>
	</tbody>
</table>
