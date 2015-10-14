	$(document).on('click', '.action', function() {
		var arg = $(this);
		var remarks = $('#action_remarks').val();
		var req_action = 'approve';//($(this).attr("req_action"));
		var type = 'statement';//($(this).attr("type"));
		var confirm_modal = b.modal.new({
		title: 'Confirm Action: ',
		width: 300,
		html: 'Are you sure you want to '+req_action+ ' this ' +type+' ?',
		buttons: {
			'Confirm' : function() {
				validateSuccessful(arg, remarks);
				confirm_modal.hide();
			}
		}
	});

	confirm_modal.show();
	});
	
	function validateSuccessful(arg, remarks){
		var data_array = (arg.attr("data")).split("|");
		if (arg.attr("id") == 'reject')
		{
			var workflow_status_id = 3;
		}
		else
		{
			var workflow_status_id = 2;
		}
		
		var workflow_id = data_array[0];//$(this).attr("data");
		var form_id = data_array[1];//<?= $order->purchase_order_id; ?>;
		var action = arg.attr("id");
		var source = data_array[2]; //checks source of action (dashboard or view)
		var form = data_array[3];
		
		beyond.request({
			url : '/inventory/pending/action',
			data : {
				'workflow_id': workflow_id,
				'workflow_status_id': workflow_status_id,
				'remarks': remarks,
				'form_id': form_id,
				'form': form,
				'action': action
			},
			on_success : function(data) {
				if (data.status == 'ok') {
					if(source == "view")
					{
						var form_source = $('#form_source').val();
						var function_dest = $('#function_dest').val();
						window.location.href = '/inventory/'+form_source+'/'+function_dest+'/'+form_id+'';
					}
					else if(source == "dashboard")
					{
						window.location.href = '/inventory';
					}
					
				}
                  
			},                
		}); 


		return;
	}


