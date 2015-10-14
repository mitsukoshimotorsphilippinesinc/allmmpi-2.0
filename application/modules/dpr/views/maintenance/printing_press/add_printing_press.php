<head>

<h3>Add New Printing Press</h3>
<hr>

</head>

<body>

<span>Printing Press Name</span>
<span style = "margin-left:230px;">Printing Press Address</span>
<br>
<input style = "width:350px;" id = 'printing_press_name' placeholder="Printing Press Name" TYPE = "TEXT">
<input style = "width:600px;" id = 'printing_press_address' placeholder="Printing Press Address" TYPE = "TEXT">

<span>Contact No.</span>
<span style = "margin-left:142px;">Contact Person</span>
<span style = "margin-left:122px;">Remarks</span>
<br>
<input id = 'printing_press_contact_number' placeholder="Printing Press Contact Number" TYPE = "TEXT">
<input id = 'printing_press_contact_person' placeholder="Printing Press Contact Person" TYPE = "TEXT">
<input style = "width:515px;" id = 'printing_press_remarks' placeholder="Remarks" TYPE = "TEXT" style = "width:500px;">

<button id="save_printing_press" class='btn btn-success'><span>Save Printing Press</span></button>
<span id = "error_message_name" style = "color:red; display:none;">No Printing Press Name...</span>
<span id = "error_message_address" style = "color:red; display:none;">No Printing Press Address...</span>
<span id = "error_message_number" style = "color:red; display:none;">No Printing Press Contact Number...</span>
<span id = "error_message_person" style = "color:red; display:none;">No Printing Press Contact Person...</span>
<span id = "error_message_remarks" style = "color:red; display:none;">No Printing Press Remarks...</span>
</body>

<SCRIPT TYPE = "text/javascript">
	
	$('#printing_press_name').keypress(function(){
		$('#error_message_name').hide();
	});

	$('#printing_press_address').keypress(function(){
		$('#error_message_address').hide();
	});

	$('#printing_press_contact_number').keypress(function(){
		$('#error_message_number').hide();
	});

	$('#printing_press_contact_person').keypress(function(){
		$('#error_message_person').hide();
	});

	$('#printing_press_remarks').keypress(function(){
		$('#error_message_remarks').hide();
	});

	$('#save_printing_press').click(function(){

		if ($('#printing_press_name').val() == ""){
			$('#error_message_name').show();
			return;
		}

		if ($('#printing_press_address').val() == ""){
			$('#error_message_address').show();
			return;
		}

		if ($('#printing_press_contact_number').val() == ""){
			$('#error_message_number').show();
			return;
		}

		if ($('#printing_press_contact_person').val() == ""){
			$('#error_message_person').show();
			return;
		}

		if ($('#printing_press_remarks').val() == ""){
			$('#error_message_remarks').show();
			return;
		}

		proceedAddPressModal = b.modal.new({
		title: "Add New Printing Press",
		width:450,
		disableClose: false,
		html: "Are you sure you want to save this Printing Press?",
		buttons: {
			'Ok' : function() {
				proceedAddPressModal.hide();
				showLoading();
				b.request({
					url: "/dpr/maintenance/insert_new_printing_press",
					data: {
					"printing_press_name": $('#printing_press_name').val(),
					"printing_press_address": $('#printing_press_address').val(),
					"printing_press_contact_number": $('#printing_press_contact_number').val(),
					"printing_press_contact_person": $('#printing_press_contact_person').val(),
					"printing_press_remarks": $('#printing_press_remarks').val(),
					},
					on_success: function(data){
					hideLoading();
					proceedAddPressModal.hide();
					proceedUpdatePressModal = b.modal.new({
					title: 'Add New Printing Press',
					width:450,
					disableClose: true,
					html: 'Successfully Insert...',
					buttons:{
						'Ok' : function(){
						proceedUpdatePressModal.hide();
						showLoading();
						redirect("/dpr/maintenance/printing_press");
								}
							}

						})
					proceedUpdatePressModal.show();
					}	
				});
			}
			}
		})
		proceedAddPressModal.show();
	});	

</SCRIPT>