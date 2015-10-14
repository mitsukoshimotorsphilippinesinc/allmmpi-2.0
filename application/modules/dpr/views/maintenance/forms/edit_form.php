
<head>

<h3><?= $record_summary->form_type_id ?> - <?= $record_summary->name ?></h3>
<hr>

</head>

<body>
<span>Form Name</span>
<span style = "margin-left:158px;">Code</span>
<span style = "margin-left:192px;">Description</span>
<br>
<input id = 'form_name' value = "<?=$record_summary->name?>" placeholder="Form Name" TYPE = "TEXT">
<input id = 'form_code' value = "<?=$record_summary->code?>" placeholder="Code" TYPE = "TEXT">
<input id = 'form_description' value = "<?=$record_summary->description?>" placeholder="Description" TYPE = "TEXT" style = "width:500px;">

<span>Class</span>
<span style = "margin-left:187px;">Pieces Per Booklet</span>
<span style = "margin-left:92px;">Remarks</span>
<br>
<Select name = "class_option" id = "class_option">
	<?php
		if ($record_summary->is_accountable == 1){
			echo "<option value = '1'>Accountable</option>
			<option value = '0'>Non-Accountable</option>";
		}else{
			echo "<option value = '0'>Non-Accountable</option>
			<option value = '1' >Accountable</option>";
		}
	?>
</Select>
<input id = 'form_pieces' value = "<?=$record_summary->pieces_per_booklet?>" placeholder="Pieces Per Booklet" TYPE = "TEXT">
<input id = 'form_remarks' value = "<?=$record_summary->remarks?>" placeholder="Remarks" TYPE = "TEXT" style = "width:500px;">

<button id="update_form" class='btn btn-success'><span>Update Form</span></button>

</body>

<SCRIPT TYPE = "text/javascript">

	$('#update_form').click(function(){

		var form_id = "<?=$record_summary->form_type_id?>";
		var form_name = $('#form_name').val();
		var form_code = $('#form_code').val();
		var form_description = $('#form_description').val();
		var form_pieces = $('#form_pieces').val();
		var form_remarks = $('#form_remarks').val();
		var class_option_number = $('#class_option').val();
		proceedUpdateModal = b.modal.new({
		title: "Update Form",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Update this Form?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/form_type_update",
					data:{
						"form_type_id": form_id,
						"form_name_val" : form_name,
						"form_code_val" : form_code,
						"form_description_val" : form_description,
						"form_pieces_val" : form_pieces,
						"form_remarks_val" : form_remarks,
						"class_option": class_option_number
					},
					on_success: function(data){
					hideLoading();
					proceedUpdateModal.hide();
					proceedUpdateFormModal = b.modal.new({
					title: 'Update Form',
					width:450,
					disableClose: true,
					html: 'Successfully Updated...',
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
		proceedUpdateModal.show();
	});

</SCRIPT>



