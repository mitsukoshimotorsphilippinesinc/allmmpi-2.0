<div class='alert alert-info'><h2>Card Encoding Dashboard </h2></div>

	<div>
   		<fieldset class=''>
	        <p>
	        	<label><strong>Enter Account ID:</strong></label>
	        	<input class='medium' type='text' id='account_id' name='account_id' style='width:200px;' maxlength='10'>
	           	<button type='button' id='btn_check_account' class='btn btn-primary' style='margin-bottom:10px;'><span>Check Account</span></button>
				<button type='button' id='btn_refresh_account_id' class='btn btn-success' style='margin-bottom:10px;'><span>Refresh</span></button>
	    	</p>     
		</fieldset>
	
	    <div class='' style='min-height:500px;'>         	
	    	<div id='account_result_container' class='' style='background:#E7EDEF; min-height:450px; width:100%; padding-top:10px;'>                
				<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'>
					<span>Please type in an Account ID and click the Check Account Button</span>
				</div>
	        </div>          
	    </div>
        <hr/>

		<div id="card_details_container" style="display:none;">
			<fieldset class=''>
		        <p>
		        	<label><strong>Enter Card ID:</strong></label>
		        	<input class='medium' type='text' id='card_id' name='card_id' style='width:200px;' maxlength='10'>
		           	<button type='button' id='btn_check_card' class='btn btn-primary' style='margin-bottom:10px;'><span>Check</span></button>
					<button type='button' id='btn_refresh_card_id' class='btn btn-success' style='margin-bottom:10px;'><span>Refresh</span></button>
		    	</p>     
			</fieldset>

	  	  	<div class='' style='min-height:500px;'>  
         	
		    	<div id='card_result_container' class='' style='background:#E7EDEF; min-height:450px; width:100%; padding-top:10px;'>                
					<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'>
						<span>Please type in a Card ID and click the Check Button</span>
					</div>
		        </div>          
		   </div>
		
	    
			<hr/>
	    	<div align="center">    
	 			<button type='button' id='btn_submit' class='btn btn-large btn-primary' style='height:60px;width:200px;'><span>Submit</span></button>
	    	</div>
		</div> 
</div>
<script type="text/javascript">
	
		var _is_package = "";

	$('#btn_check_account').live("click",function() {
        account_id = $("#account_id").val().trim();
		
		if ($('#member_id').html() == null) {
		
			if (account_id=='') {
				// modal
				var accountErrorModal = b.modal.new({
					title: 'Account Verification :: Error',
					disableClose: true,
					html: '<p>Please correct the following:</p><p>Please enter an Account ID.</p>',
					buttons: {					
						'Close' : function() {						
							accountErrorModal.hide();
						}
					}
				});
				accountErrorModal.show();
			} else {
				beyond.request({
					url : '/admin/encoding/check_account',
					data : {
						'_account_id' : account_id
					},
					on_success : function(data) {
						if (data.status == "1")	{
				
							$("#account_result_container").html(data.html);							
							$("#card_details_container").show();							
						} else {					
							var accountErrorModal = b.modal.new({
								title: 'Card Verification :: Error',
								disableClose: true,
								html: data.html,
								buttons: {					
									'Close' : function() {						
										accountErrorModal.hide();
									}
								}
							});
							accountErrorModal.show();
						}
					} // end on_success
				})					
			}
			return false;
		} else {		
			// clear card container			
			clearCardContainer("card");
		}
	});	

	$('#btn_refresh_card_id').live("click",function() {		
		clearContainer("card");	
	});
	
	
	$('#btn_refresh_account_id').live("click",function() {		
		clearContainer("account");	
	});
	
	

	var clearContainer = function(container) {		
		if (container == 'card') {
			$('#card_id').val("");
			_html = "<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'><span>Please type in a Card ID and click the Check Button</span></div>";		
			$('#card_result_container').html(_html);
		} else {
			$('#card_id').val("");	
			_html = "<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'><span>Please type in a Card ID and click the Check Button</span></div>";	
			$('#card_result_container').html(_html);
			$('#card_result_container').hide();
			
			$('#card_id').val("");
			_html = "<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'><span>Please type in an Account ID and click the Check Account Button</span></div>";		
			$('#account_result_container').html(_html);
			
		}
	}; 


    $('#btn_check_card').live("click",function() {
        card_id = $("#card_id").val().trim();

		if (card_id=='') {
			// modal
			var cardErrorModal = b.modal.new({
				title: 'Voucher :: Error',
				disableClose: true,
				html: '<p>Please correct the following:</p><p>Please enter a Card ID.</p>',
				buttons: {					
					'Close' : function() {						
						cardErrorModal.hide();
					}
				}
			});
			cardErrorModal.show();
		} else {
			beyond.request({
				url : '/admin/encoding/check_card',
				data : {
					'card_id' : card_id
				},
				on_success : function(data) {
					if (data.status == "1")	{
				
						$("#card_result_container").html(data.html);	
						
						_is_package = data.is_package;
						
					} else {
						var cardErrorModal = b.modal.new({
							title: 'Card Verification :: Error',
							disableClose: true,
							html: data.html,
							buttons: {					
								'Close' : function() {						
									cardErrorModal.hide();
								}
							}
						});
						cardErrorModal.show();
					}
				} // end on_success
			})
			return false;			
		}
	});
	
	$('#btn_submit').live("click",function() {	
		
		if ($("#account_id_display").html() == null) {
			_account_id = "";
		} else {
			_account_id = $("#account_id_display").html().trim();
		}
		
		if ($("#card_id_display").html() == null) {
			_card_id = "";
		} else {
			_card_id = $("#card_id_display").html().trim();
		}
		
		if ($("#member_id_account").html() == null) {
			_member_id_account = "";
		} else {
			_member_id_account = $("#member_id_account").html().trim();
		}
		
		if ($("#member_id_card").html() == null) {
			_member_id_card = "";
		} else {
			_member_id_card = $("#member_id_card").html().trim();
		}
		
		beyond.request({
			url : '/admin/encoding/submit_check',
			data : {
				'_account_id' : _account_id,
				'_card_id' : _card_id,
				'_member_id_account' : _member_id_account,
				'_member_id_card' : _member_id_card,
				'_is_package' : _is_package
			},
			on_success : function(data) {
				if (data.status == "1")	{
					var cardModal = b.modal.new({
						title: 'Encode Card :: Enter Card Code and Position',
						disableClose: true,
						html: data.html,
						buttons: {							
							'Proceed' : function() {
								var _position = $('#position').val();
								var _card_code = $('#card_code').val();
								
								// check if user enters the required fields
								checkRequiredFields(_position, _card_code);
								var _var_data = _position + '|' + _card_code;
								checkRequiredFields('credit_points', _var_data);
								
								if (hasError == 0) { 
									// check card_code format is valid
									checkCardCodes(_card_code);								
																	
															
															
															
																	
									//creditCardPoints(_account_id, _card_id, _member_id_account, _member_id_card, _is_package, _position, _card_code);								
									cardModal.hide();
								}
							},
							'Cancel' : function() {
								cardModal.hide();
							}
						}
					});
					cardModal.show();
				
				} else {
					var cardErrorModal = b.modal.new({
						title: 'Encode Card :: Error',
						disableClose: true,
						html: data.html,
						buttons: {					
							'Close' : function() {						
								cardErrorModal.hide();
							}
						}
					});
					cardErrorModal.show();
				}
			} // end on_success
		})
		return false;
	
	});
	
	var checkRequiredFields = function(action, var_data) {	
		hasError = 0;
		
		if (action == 'credit_points') {
			var data = var_data.split('|');
			var position = data[0];		
			var card_code = data[1];			
			
			$('#card_code_error').hide();
			$('#position_error').hide();
			
			if ($.trim(card_code) == '') {
				$('#card_code_error').show();
				hasError = 1;
			}
			
			if ($.trim(position) == '') {
				$('#position_error').show();
				hasError = 1;
			}
												
		} 
	
		return hasError;
	};
	
	var checkCardCodes = function(card_codes) {
	
		b.request({
			url: "admin/encoding/check_card_codes",
			data: {
				"_card_codes": card_codes				
			},
			on_sucess: function(data){

			}
		});
	}
	
</script>
