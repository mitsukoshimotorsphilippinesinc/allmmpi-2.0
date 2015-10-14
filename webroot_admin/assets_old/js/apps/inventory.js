$(document).on('click', '.more_items', function(e) {
	
	e.preventDefault();
	
	var item = $(this);
	//change 'More' to 'Less'
	item.parent().children('.more_items').html('Less...');
	item.parent().children('.more_items').attr('class', 'less_items');
	//display the rest of the items
	item.parent().children('.items_list').attr('style', 'height:auto; overflow:auto');
});

$(document).on('click', '.less_items', function(e) {
	
	e.preventDefault();
	
	var item = $(this);
	//change 'Less' to 'More'
	item.parent().children('.less_items').html('More...');
	item.parent().children('.less_items').attr('class', 'more_items');
	//display only first two items
	item.parent().children('.items_list').attr('style', 'height:35px; overflow:hidden');
});


$(".view-form").live('click',function(){		
	var form_id = $(this).html();		

	viewForm(form_id);	
	return false;	
});



var viewForm = function(form_id) {
	var form_type = form_id.substring(0,2);
	
	var title;
	var form;
	
	if (form_type=='PO') 
	{
		title = "Purchase Order View";
		form = "orders";
	}
	else if (form_type=='ST') 
	{
		title = "Stocks Transfer View";
		form = "transfers";
	}
	else if (form_type=='RF') 
	{
		title = "Requisition View";
		form = "requisition";
	}
	else if (form_type=='SR') 
	{
		title = "Stocks Receiving View";
		form = "receiving";
	}
	
	b.request({
		url: '/inventory/'+form+'/modal_view',
		data: {
			'form_id': form_id,
		},
		on_success: function(data, status) {
			var view_form = b.modal.new({});
			
			if(data.data.editable && form_type != "SR")
			{
				var modal_buttons = {
					'Edit' : function() {
						view_form.hide();
						window.location.href = '/inventory/'+form+'/edit/'+data.data.id+'/0';
					}	
				};
			}
			else
			{
				var modal_buttons = {};
			}

			
			view_form.init({
				title: title+" (TN: "+data.data.tracking_number+")",
				width: '900px',
				html: data.html,
				buttons: modal_buttons
			});
			
			view_form.show();
		}
	});		

	return;	
}

function validateAction(action, type) {
	var confirm_modal = b.modal.new({
		title: 'Confirm Action: ',
		width: '300px',
		disableClose: true,
		html: 'Are you sure you want to '+action+ ' this ' +type+' ?',
		buttons: {
			'No' : function() {
				confirm_modal.hide();
			},
			'Yes' : function() {
				$("#submit_form").submit();
				confirm_modal.hide();
			
			}
		}
	});

	confirm_modal.show();
}

//used for status updates that do not use forms
function confirmUpdate(action, type, id, page){
	var status_modal = b.modal.new({
		title: 'Confirm Action: ',
		width: '300px',
		disableClose: true,
		html: 'Are you sure you want to '+action+ ' this ' +type+' ?',
		buttons: {
			'No' : function() {
				status_modal.hide();
			},
			'Yes' : function() {
				updateStatus(id, page);
				status_modal.hide();
			}
		}
	});

	status_modal.show();
}

function checkDuplicates(action, type, process, id){

	var user_actions = new Array();
	var unique_actions = new Array();
	var required_approval = $("#require_approvals").attr("checked");
	var check_duplicates = b.modal.new({});

	user_actions = $(".user_action").map(function(){
		return $(this).val();
	});

	console.log(required_approval);

	if(required_approval == "checked" && user_actions.length == 0)
	{
		check_duplicates.init({
			title: 'Error: No Available Actions',
			width: '300px',
			html: '<p>Can not require approvals. There are no available actions.</p>'
		});
		check_duplicates.show();
		return false;
	}

	unique_actions = _.uniq(user_actions);

	//check the lengths of user_action and unique actions are not equal to ensure that all actions have a unique user to perform them
	if(unique_actions.length != user_actions.length)
	{
		check_duplicates.init({
			title: 'Error: Duplicate Users',
			width: '300px',
			html: '<p>One or more users have been assigned multiple workflow actions.</p> Please limit one user to one action.'
		});
		check_duplicates.show();
		return false;
	}

	validateAction(action, type);
}

$(document).on('click', 'a.action', function() {
	var arg = $(this);
	var remarks = $('#action_remarks').val();
	var req_action = 'approve';//($(this).attr("req_action"));
	var type = 'form';//($(this).attr("type"));
	var confirm_modal = b.modal.new({
		title: 'Confirm Action: ',
		width: 300,
		disableClose: true,
		html: 'Are you sure you want to '+req_action+ ' this ' +type+' ?',
		buttons: {
			'No' : function() {
				confirm_modal.hide();
			},
			'Yes' : function() {
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
	var dest_id = data_array[4];
	
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
			if (data.status == 1) {
				if(source == "view")
				{
					var form_source = $('#form_source').val();
					var function_dest = $('#function_dest').val();
					var confirm_modal = b.modal.new({
						title: 'Confirm Action: ',
						width: 300,
						disableClose: true,
						html: 'Action Successful!',
						buttons: {
							'Close' : function() {
								confirm_modal.hide();
								window.location.href = '/inventory/'+form_source+'/'+function_dest+'/'+dest_id+'';
							}
						}
					});
					confirm_modal.show();
				}
				else if(source == "dashboard")
				{
					window.location.href = '/inventory';
				}
			}
			else{
				alert(data.status);
			}
                 
		},                
	}); 

	return;
}


