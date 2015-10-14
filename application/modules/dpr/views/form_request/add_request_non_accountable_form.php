

<div class='alert alert-danger'><h2>Add New Request(Non-Accountable Forms) <a id = "btn_save_request" class = 'btn' style = "float:right;" href = "add_new_non_accountables">New Request</a></div>

<div>

	<span>Reference No.</span>
	<input id = "txtrequestcode" TYPE = "TEXT" disabled = "disabled"></input>
	<br>
	<span>Form Type</span>
	<span style = "position:absolute; left:500px;">Last Series No.</span>
	<span style = "position:absolute; left:730px;">Pcs. Per Booklet</span>
	<span style = "position:absolute; left:955px;">Quantity</span>
	<br>
	<Select id = "form_option">
		<option value = '0'>Select Form</option>
	<?php
		$where = "is_accountable = 0 and is_active = 1 and is_deleted = 0";
		$form_list=$this->dpr_model->get_form_type($where,null,'form_type_id ASC');
		foreach($form_list as $fl){
			$form_name = $fl->name;
			$form_id = $fl->form_type_id;
			echo"<option name = 'form_data' value = {$fl->form_type_id} data-pcs='{$fl->pieces_per_booklet}'>{$form_id} - {$form_name}</option>";
		}
	?>
	</Select>
	<input placeholder="Last Series Number" TYPE = "TEXT" disabled = "disabled">
	<input style = "text-align:right;" id = "txtpcs" placeholder="Pcs. Per Booklet" TYPE = "TEXT" disabled = "disabled">
	<input style = "text-align:right;" id = "txtqty" placeholder="0" TYPE = "TEXT" style = "text-align:right;">
	<br>

	<Label>Select Printing Press</Label>
	<Select id = "press_option" style = "width:450px;">
		<option value = '0'>Select Printing Press</option>
	<?php
		$where = "is_active = 1 and is_deleted = 0";
		$press_list=$this->dpr_model->get_press_name($where,null,'printing_press_id ASC');
		foreach($press_list as $pl){
			$press_name = $pl->complete_name;
			$press_id = $pl->printing_press_id;
			echo "<option value = '{$pl->printing_press_id}'>{$press_id} - {$press_name}</option>";
		}
	?>
	</Select>
	<br>
	<a id = "btn_add_new" class = 'btn' >Add</a>
	<span id = "Error_Message_Form" style = "color:red; display:none;">Select form first...</span>
	<span id = "Error_Message_Press" style = "color:red; display:none;">Select Printing Press first...</span>
	<span id = "Error_Message_QTY" style = "color:red; display:none;">Invalid Quantity Value...</span>

	<br>
	<br>
	<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Type Of Form</th>
			<th>Last Series No.</th>
			<th>Pcs. Per Booklet</th>
			<th>QTY</th>		
			<th>Printing Press</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody id = "record_data">
	</tbody>
	</table>
	<br>
		
</div>
 
<SCRIPT TYPE = "text/javascript">
	
	$("#txtqty").keypress(function (e) {
	if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
	return false;
	}
	});

	$('#form_option').change(function(){
		$('#txtpcs').val( $(this).find(':selected').data('pcs'));
		$('#Error_Message_Form').hide();
	});

	$('#press_option').change(function(){
		$('#Error_Message_Press').hide();
	});

	$('#txtqty').keypress(function(){
		if (_.isNumber($('#txtqty').val() * 1)){
			$('#Error_Message_QTY').hide();
			return;
		}
		$('#Error_Message_QTY').show();
	});

	$('#btn_add_new').click(function(){
		if ($('#form_option').val() == 0){
			$('#Error_Message_Form').show();
			return;
		}
		if ($('#press_option').val() == 0){
			$('#Error_Message_Press').show();
			return;
		}
		if ($('#txtqty').val() <= 0){
			$('#Error_Message_QTY').show();
			return;
		}

		proceedAddNewModal = b.modal.new({
		title: "Add New Request",
		width:450,
		disableClose: false,
		html: "Are you sure you want to save this Item?",
		buttons: {
			'Ok' : function() {
				proceedAddNewModal.hide();

				b.request({
					url: "/dpr/form_request/add_new_item_na",
					data: {
					"last_serial_number": 0,
					"form_type_id": $('#form_option').val(),
					"quantity": $('#txtqty').val(),
					"printing_press_id": $('#press_option').val(),
					"request_code": $('#txtrequestcode').val()
					},

					on_success: function(data){

						if (data.status == "1") {

							// show add form modal 
							proceedAddNewItemModal = b.modal.new({
								title: 'Add New Item',
								width:450,
								disableClose: true,
								html: 'Successfully add new item...',
								buttons: {
									'Ok' : function() {
										proceedAddNewItemModal.hide();
										//alert(data.data.request_code);
										$('#txtrequestcode').val(data.data.request_code);

										b.request({
											url:"/dpr/form_request/refresh_list_details_na",
											data:{
											"request_code": $('#txtrequestcode').val()	
											},

										on_success: function(data){
											//alert(data.data.html);
											$('#record_data').html(data.data.html);
										}

										})
									}
								}
							});
						proceedAddNewItemModal.show();
						}
					}
				});
			}
		}	
		})
		proceedAddNewModal.show();
	});
	
	$('.delete_item').live('click',function(){
		//alert($(this).attr('data'));
		b.request({
			url: "/dpr/form_request/delete_item",
			data:{
				"request_detail_id": $(this).attr('data')
			},
			on_success: function(data){
				proceedDeleteItemModal = b.modal.new({
					title: 'Delete Item',
					width:450,
					disableClose: true,
					html: 'Successfully delete item...',
					buttons: {
						'Ok' : function() {
							proceedDeleteItemModal.hide();
							
							b.request({
								url:"/dpr/form_request/refresh_list_details_na",
								data:{
								"request_code": $('#txtrequestcode').val()	
								},

								on_success: function(data){
								//alert(data.data.html);
								$('#record_data').html(data.data.html);
								}	
							});						
						}
					}
				})
				proceedDeleteItemModal.show();
			}
		});
	});

</SCRIPT>