<head>

<h3><?= $record_summary->printing_press_id ?> - <?= $record_summary->complete_name ?></h3>
<hr>

</head>

<body>

<span>Printing Press Name</span>
<span style = "margin-left:230px;">Printing Press Address</span>
<br>
<input style = "width:350px;" id = 'printing_press_name' placeholder="Printing Press Name" TYPE = "TEXT" value = "<?=$record_summary->complete_name?>">
<input style = "width:600px;" id = 'printing_press_address' placeholder="Printing Press Address" TYPE = "TEXT" value = "<?=$record_summary->complete_address?>">

<span>Contact No.</span>
<span style = "margin-left:142px;">Contact Person</span>
<span style = "margin-left:122px;">Remarks</span>
<br>
<input id = 'printing_press_contact_number' placeholder="Printing Press Contact Number" TYPE = "TEXT" value = "<?=$record_summary->contact_number?>">
<input id = 'printing_press_contact_person' placeholder="Printing Press Contact Person" TYPE = "TEXT" value = "<?=$record_summary->contact_person?>">
<input style = "width:515px;" id = 'printing_press_remarks' placeholder="Remarks" TYPE = "TEXT" style = "width:500px;" value = "<?=$record_summary->remarks?>">

<button id="update_printing_press" class='btn btn-success'><span>Update Printing Press</span></button>

</body>

<SCRIPT TYPE = "text/javascript">

	$('#update_printing_press').click(function(){

		var press_id = "<?=$record_summary->printing_press_id?>";
		proceedUpdateModal = b.modal.new({
		title: "Update Printing Press",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Update this Printing Press?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/printing_press_update",
					data:{
						"printing_press_id": press_id,
						"printing_press_name" : $('#printing_press_name').val(),
						"printing_press_address" : $('#printing_press_address').val(),
						"printing_press_contact_person" : $('#printing_press_contact_person').val(),
						"printing_press_contact_number" : $('#printing_press_contact_number').val(),
						"printing_press_remarks" : $('#printing_press_remarks').val()
					},
					on_success: function(data){
					hideLoading();
					proceedUpdateModal.hide();
					proceedUpdateFormModal = b.modal.new({
					title: 'Update Printing Press',
					width:450,
					disableClose: true,
					html: 'Successfully Updated...',
					buttons:{
						'Ok' : function(){
						proceedUpdateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/printing_press");
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