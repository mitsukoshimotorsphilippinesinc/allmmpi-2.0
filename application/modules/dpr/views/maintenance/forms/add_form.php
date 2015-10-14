<head>

<h3>Add New Form</h3>
<hr>

</head>

<body>
<span>Form Name</span>
<span style = "margin-left:158px;">Code</span>
<span style = "margin-left:192px;">Description</span>
<br>
<input id = 'form_name' placeholder="Form Name" TYPE = "TEXT">
<input id = 'form_code' placeholder="Code" TYPE = "TEXT">
<input id = 'form_description' placeholder="Description" TYPE = "TEXT" style = "width:500px;">
<span>Class</span>
<span style = "margin-left:187px;">Pieces Per Booklet</span>
<span style = "margin-left:92px;">Remarks</span>
<br>
<Select name = "class_option" id = "class_option">
	<option value = "1">Accountable</option>
	<option value = "0">Non-Accountable</option>
</Select>
<input id = 'form_pieces' placeholder="Pieces Per Booklet" TYPE = "TEXT">
<input id = 'form_remarks' placeholder="Remarks" TYPE = "TEXT" style = "width:500px;">

<button id="save_form" class='btn btn-success'><span>Save Form</span></button>
<span id = "error_message_name" style = "color:red; display:none;">No Form Name...</span>
<span id = "error_message_code" style = "color:red; display:none;">No Form Code...</span>
<span id = "error_message_description" style = "color:red; display:none;">No Form Description...</span>
<span id = "error_message_pieces" style = "color:red; display:none;">No Form Pieces Per Booklet...</span>
<span id = "error_message_remarks" style = "color:red; display:none;">No Form Pieces Per Booklet...</span>
</body>

<SCRIPT TYPE = "text/javascript">
	
	$("#form_pieces").keypress(function (e) {
	if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
	return false;
	}
	});

	$('#form_name').keypress(function(){
		$('#error_message_name').hide();
	});
	
	$('#form_code').keypress(function(){
		$('#error_message_code').hide();
	});

	$('#form_description').keypress(function(){
		$('#error_message_description').hide();
	});

	$('#form_pieces').keypress(function(){
		if (_.isNumber($('#form_pieces').val() * 1)){
			$('#error_message_pieces').hide();
			return;
		}
	});

	$('#form_remarks').keypress(function(){
		$('#error_message_remarks').hide();
	});	

	$('#save_form').click(function(){
		if ($('#form_name').val() == ""){
			$('#error_message_name').show();
			return;
		}
		if ($('#form_code').val() == ""){
			$('#error_message_code').show();
			return;
		}
		if ($('#form_description').val() == ""){
			$('#error_message_description').show();
			return;
		}

		if ($('#form_pieces').val() <= 0){
			$('#error_message_pieces').show();
			return;
		}

		if ($('#form_remarks').val() <= 0){
			$('#error_message_remarks').show();
			return;
		}

		proceedAddFormModal = b.modal.new({
		title: "Add New Form",
		width:450,
		disableClose: false,
		html: "Are you sure you want to save this Form?",
		buttons: {
			'Ok' : function() {
				proceedAddFormModal.hide();
				showLoading();
				b.request({
					url: "/dpr/maintenance/insert_new_form_type",
					data: {
					"form_name": $('#form_name').val(),
					"form_code": $('#form_code').val(),
					"form_description": $('#form_description').val(),
					"class_option": $('#class_option').val(),
					"form_pieces": $('#form_pieces').val(),
					"form_remarks": $('#form_remarks').val()
					},
					on_success: function(data){
					hideLoading();
					proceedAddFormModal.hide();
					proceedUpdateFormModal = b.modal.new({
					title: 'Insert Form',
					width:450,
					disableClose: true,
					html: 'Successfully Insert...',
					buttons:{
						'Ok' : function(){
						proceedUpdateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/form_type");
								}
							}

						})
					proceedUpdateFormModal.show();
					}	
				});
			}
			}
		})
		proceedAddFormModal.show();
	});

</SCRIPT>