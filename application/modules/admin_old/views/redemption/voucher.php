<div class='alert alert-info'><h2>Voucher Redemption Dashboard </h2></div>

<div
	<div>
   
		<fieldset>
	        <p>
	        	<label><strong>Enter Voucher Code:</strong></label>
	        	<input class='medium' type='text' id='voucher_code' name='voucher_code' style='width:200px;'>
	           	<button type='button' id='check-voucher' class='btn btn-primary' style='margin-bottom:10px;'><span>Check</span></button>
	    	</p>     
		</fieldset>

	    <div style='min-height:500px;'>  
         	
	    	<div id='result_container'>                
				<div style='text-align:center; padding-top: 200px; font-size:16px; color:#666;'>
					<span>Please type in a voucher code and click the Check Button</span>
				</div>
	        </div>          
	    </div>             
	</div>
</div>
<script type="text/javascript">

	var updateStatus = function(voucher_code,status,remarks)
	{
		b.request({
			url : '/admin/redemption/update_status',
			data : {
				'voucher_code': voucher_code,
				'status':status,
				'remarks':remarks
			},
			on_success : function(data, status) {
			}
		});
	
	}

	var checkVoucher = function (voucher_code) {

		if (voucher_code=='') {
			// modal
			var voucherModal = b.modal.new({
				title: 'Voucher :: Error',
				disableClose: true,
				html: '<p>Please correct the following:</p><p>Please enter a voucher code.</p>',
				buttons: {					
					'Ok' : function() {						
						voucherModal.hide();
					}
				}
			});
			voucherModal.show();
		} else {
			beyond.request({
				url : '/admin/redemption/check',
				data : {
					'voucher_code' : voucher_code
				},
				on_success : function(data) {
					$("#result_container").html(data.html);
				} // end on_success
			})
		
		
		}
	}

	$("#check-voucher").live("click",function(){
		voucher_code = $("#voucher_code").val().trim();       
		
		checkVoucher(voucher_code);
	});
	
	$("#update-status").live("click",function(){
		
		var _current_status = $("#voucher-status").html();
		
		var _voucher_code = $("#voucher-code").html();		
		
		var _statuses = "<select id='voucher_status'>";

		if (_current_status=='ACTIVE') 
		{
			_statuses = _statuses + "<option value='BOOKED'>BOOK</option>";
			_statuses = _statuses + "<option value='REDEEMED'>REDEEM</option>";
		}
		else if (_current_status=='BOOKED')
		{
			_statuses = _statuses + "<option value='REDEEMED'>REDEEM</option>";
		}
		_statuses = _statuses + "</select>";
				
		var _html = "<label>Select Status</label>" + _statuses + "<br/><br/><label>Remarks</label><textarea id='remarks' rows='3' style='width:250px;'></textarea>";
		
		var updateVoucherModal = b.modal.new({
			title: 'Update Voucher Status',
			disableClose: true,
			width:'300px',
			html: _html,
			buttons: {					
				'Cancel' : function() {						
					updateVoucherModal.hide();
				},
				'Update' : function() {					
					
					
					var _status = $("#voucher_status").val();
					var _remarks = $("#remarks").val();

					var confirmVoucherUpdateModal = b.modal.new({
						title: 'Confirm Update',
						disableClose: true,
						width:'300px',
						html: "Are you sure you want to update the status of <strong>" + _voucher_code + "</strong> to <strong>" + _status + "</strong>?",
						buttons: {					
							'Cancel' : function() {						
								confirmVoucherUpdateModal.hide();
							},
							'Confirm' : function() {					
								
								updateStatus(_voucher_code,_status,_remarks);

								$("#check-voucher").click();

								confirmVoucherUpdateModal.hide();
							}
						}
					});
						
					confirmVoucherUpdateModal.show();
					
					updateVoucherModal.hide();
				}
			}
		});
		updateVoucherModal.show();		
	});

	$("#update-owner-details").live("click",function(){
		
		var _voucher_code = $("#voucher-code").html();
		
		var _html = "<label>Full Name</label><input type='text' class='span2' id='last_name' placeholder='Last name'>&nbsp;<input type='text' class='span2' id='first_name' placeholder='First name'>&nbsp;<input type='text' class='span2' id='middle_name' placeholder='Middle name'><br/><br/><label>Email</label><input type='text' id='email' placeholder='your_name@yahoo.com'><br/><br/><label>Mobile Number</label><input type='text' id='mobile_number' placeholder='0919xxxxxxx'>";
		
		var updateOwnerDetails = b.modal.new({
			title: 'Update Owner Details',
			disableClose: true,
			html: _html,
			buttons: {					
				'Cancel' : function() {						
					updateOwnerDetails.hide();
				},
				'Update' : function() {						
					
					var last_name = $("#last_name").val();
					var first_name = $("#first_name").val();
					var middle_name = $("#middle_name").val();
					var email = $("#email").val();
					var mobile_number = $("#mobile_number").val();							
					
					var confirmOwnerDetails = b.modal.new({
						title: 'Confirm Update',
						disableClose: true,
						width:'300px',
						html: "Are you sure you want to update the owner details of " + _voucher_code + "?",
						buttons: {					
							'Cancel' : function() {						
								confirmOwnerDetails.hide();
							},
							'Confirm' : function() {					
																
								b.request({
									url : '/admin/redemption/update_owner_details',
									data : {
										'voucher_code': _voucher_code,
										'last_name':last_name,
										'first_name':first_name,
										'middle_name':middle_name,
										'email':email,
										'mobile_number':mobile_number
									},
									on_success : function(data, status) {
									}
								});
								
								confirmOwnerDetails.hide();

								$("#check-voucher").click();
							}
						}
					});
						
					confirmOwnerDetails.show();
					
					updateOwnerDetails.hide();				}
			}
		});
		updateOwnerDetails.show();		
	});	
</script>
