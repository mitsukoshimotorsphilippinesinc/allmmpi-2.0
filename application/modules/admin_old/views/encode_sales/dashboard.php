
<div class="alert alert-info">
	<h2>
		Encode Repeat Sales
	</h2>
</div>
	
<div class='clearfix'>
	
	<div class="span6" style='width: 440px;'>
		
		<div class="control-group">					
			<label class="control-label">Account ID: <em>*</em></label>
			<input class='input-large' type='text' id='account_id' placeholder="88********" name='account_id' maxlength='10'>	           				
		</div>     		
		
		<div class="control-group">					
			<label class="control-label">Control Code: <em>*</em></label>
			<input class='input-large' type='text' id='card_id' placeholder="75********" name='card_id'maxlength='10'>	           				
		</div>     		
		
		<div class="control-group">	
			<label class="control-label" for="card_code">RSRN: <em>*</em></label>
			<input type="text" class='input-large'  placeholder="ABC123DE" name="card_code" id="card_code" value="">
		</div>

		<div class="control-group">	
			<label class="control-label" for="position">Position: <em>*</em></label>
			<select id="position">
				<option value="left">Left</option>
				<option value="right">Right</option>
			</select>					
		</div>

		<div class="control-group">	
			<label class="control-label" for="position">Maintenance Period: <em>*</em></label>
			<select id="maintenance_period">
				<option value="monthly">Monthly</option>
				<option value="annual">Annual</option>
			</select>					
		</div>

		<hr/>
		<div align="left">    
			<button type='button' id='btn_submit' class='btn btn-medium btn-primary'><span>Submit</span></button>
		</div>			 
	</div>

	<div class=" span6">
		<div id="account_details_container">
			<label><strong>Name:</strong></label>
			<label><strong>Email:</strong></label>
			<hr/>
			<label><strong>Points:</strong></label>
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<td><strong>Details</strong></td>
						<td><strong>Points</strong></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Left RS</td>
						<td style='text-align:right;'>0.00</td>
					</tr>

					<tr>
						<td>Right RS</td>
						<td style='text-align:right;'>0.00</td>
					</tr>

					<tr>
						<td>Pairs RS</td>
						<td style='text-align:right;'>0</td>
					</tr>
				</tbody>
			</table>

		</div>	
		
	</div>

</div>

<script type="text/javascript">
	
	$(document).ready(function() {	
	    vitalc.encodeSales.initialize();
    });

	$(function() {
		
		
		$('#btn_rs_promo_register').click(function(e) {
			e.preventDefault();
			
			var _reg_num = $.trim($('#rs_promo_reg_number').val());
			
			if (_reg_num.length > 0)
			{
				
				var _modal = b.modal.create({
					title: "RS Promo Registration",
					html: "<p>You are about to register #<strong>"+_reg_num.toUpperCase()+"</strong> to your<br/>Account ID: <strong>"+vitalc.member.selected_account_id+"</strong>.</p>"
						  + "<p>What would you like to do?</p>",
					width: 420,
					buttons : {
						'Proceed' : function() {
							_modal.hide();
							
							b.request({
					            url: '/members/encoding/rs_promo_register',
					            data: {'account_id' : vitalc.member.selected_account_id, 'reg_num' : _reg_num},
					            on_success: function(data, status) {
									if (data.status == 'ok') {
										$('#rs_promo_reg_number').val('');
										b.modal.create({
											title: "RS Promo Registration Success!",
											html: "<p>"+data.msg+"</p>",
											width: 450,
										}).show();
									} else {
										b.modal.create({
											title: "RS Promo Registration Error!",
											html: "<p>"+data.msg+"</p>",
											width: 450,
										}).show();
									}
					            }
					        });
						}
					}
				});
				
				_modal.show();

			}
			
			
		});
		
	});
	
</script>
