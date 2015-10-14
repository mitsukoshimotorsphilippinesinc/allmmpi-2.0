	<div class="page-header clearfix">
		<h2 class='pull-left'>Encode Repeat Sales <small id="header_account_id"></small></h2>
		<div class="control-group section-control-group pull-right">
			<label class="control-label">Select Account:</label>
			<?= $this->load->view('account/switcher', null, TRUE, 'members'); ?>
		</div>
	</div>

	<div class='clearfix'>
		
		<div class="span6" style='width: 440px;'>
			
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
					<option class="standard-maintenance-period-options" value="monthly">Monthly</option>
					<option class="standard-maintenance-period-options" value="annual">Annual</option>
				</select>					
			</div>

			<hr/>
	    	<div align="left">    
	 			<button type='button' id='btn_submit' class='btn btn-medium btn-primary'><span>Submit</span></button>
	    	</div>			 
		</div>
	
		<div class=" span6">
			<div id="account_details_container">

                <label>Points:</label>
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
							<td>0.00</td>
						</tr>

						<tr>
							<td>Right RS</td>
							<td>0.00</td>
						</tr>

						<tr>
							<td>Pairs RS</td>
							<td>0</td>
						</tr>
					</tbody>
				</table>
				
				

			</div>	
			<div class='well well-small'>
				<h4>Repeat Sales Promo Entry Registration:</h4>
				<div class="input-append">
				  <input class="input-xlarge" id="rs_promo_reg_number" type="text" class='input-large' placeholder='Enter Registration Number'>
				  <div class="btn-group">
				    <button id='btn_rs_promo_register' class="btn btn-success" >Register Now!</button>
				  </div>
				</div>
			</div>
		</div>

	</div>

<script type="text/javascript">
	
	$(document).ready(function() {	
	    vitalc.encodeSales.initialize();
		
	    var check74 = function(){
	    	var c_series = $('#card_id').val().substring(0,2);
	    	$('.extra-maintenance-period-options').remove();
	    	if(c_series == "74" || c_series == "72"){

	    		$('.standard-maintenance-period-options').remove();
	    		$('#maintenance_period').html('');				
				if (c_series == 72) {
					$('#maintenance_period').append('<option class="extra-maintenance-period-options" value="unilevel">UNILEVEL</option>');
				} else {
					$('#maintenance_period').append('<option class="extra-maintenance-period-options" value="igpsm">IGPSM</option>');
				}
				$('#maintenance_period').append('<option class="extra-maintenance-period-options" value="raffle">Raffle Promo</option>');
				//$('#maintenance_period').val('raffle');
				$('#maintenance_period').val('igpsm');
				//$('#position').parent().hide();

	    	} else {
			
				$('.extra-maintenance-period-options').remove();
				$('#maintenance_period').html('');
				$('#maintenance_period').append('<option class="standard-maintenance-period-options" value="monthly">Monthly</option>');
				$('#maintenance_period').append('<option class="standard-maintenance-period-options" value="annual">Annual</option>');	

				$('#maintenance_period').val('monthly');
				$('#position').parent().show();
	    	}
	    }; 

	    $('#card_id').keyup(function(){
	    	check74();
	    });

	    $('#card_id').blur(function(){
	    	check74();
	    });

	    $('#maintenance_period').change(function(){
	    	if($(this).val() == "raffle"){
	    		$('#position').parent().hide();
	    	} else {
	    		$('#position').parent().show();
	    	}
	    });
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
