<div class='alert alert-danger'><h2>Add New TR<a id = "btn add_tr" class = 'btn' style = "float:right;" href = "add_new_tr">New TR</a>   <a id = "btn add_tr" class = 'btn' style = "float:right;" href = "print_tr">Print TR</a></div>

<body>

<span>Select Branch</span>
<Select id = "branch_option" name = "branch_option">
	<option value = '0'>Select Branch</option>
	<?php
		$where = "is_active = 1";
		$branch_list=$this->human_relations_model->get_branch($where,null,'branch_name ASC');
		foreach ($branch_list as $bl) {
			$branch_name = $bl->branch_name;
			$branch_id = $bl->branch_id;
			echo "<option name = 'branch_data' value = {$bl->branch_id} data-tin='{$bl->tin}' data-address='{$bl->address_street}'>{$branch_id} - {$branch_name}</option>";
		}
	?>
</Select>
<span>TR No</span>
<input style = "margin-left:21px;" id = "tr_number" name = "tr_number" placeholder="TR Number"  TYPE = "TEXT">
<span><input type="checkbox" id= "check_borrowed" name="check_borrowed" value = "1"/> Borrowed</span>

<Select id = "branch_borrowed" name = "branch_borrowed">
	<option value = ''></option>
	<?php
		$where = "is_active = 1";
		$branch_list=$this->human_relations_model->get_branch($where,null,'branch_name ASC');
		foreach ($branch_list as $bl) {
			$branch_name = $bl->branch_name;
			$branch_id = $bl->branch_id;
			echo "<option name = 'branch_data' value = {$bl->branch_id} data-tin='{$bl->tin}' data-address='{$bl->address_street}'>{$branch_id} - {$branch_name}</option>";
		}
	?>
</Select>

<br>
<span>Select Form</span>
<span style = "margin-left:145px;">Booklet No from</span>
<span style = "margin-left:113px;">Booklet No To</span>
<br>
<Select id = "form_option" name = "form_option">
		<option value = '0'>Select Form</option>
	<?php
		$where = "is_accountable = 1 and is_active = 1 and is_deleted = 0";
		$form_list=$this->dpr_model->get_form_type($where,null,'form_type_id ASC');
		foreach($form_list as $fl){
			$form_name = $fl->name;
			$form_id = $fl->form_type_id;
			echo"<option name = 'form_data' value = {$fl->form_type_id} data-pcs='{$fl->pieces_per_booklet}'>${form_id} - {$form_name}</option>";
		}
	?>
</Select>


<input id = "booklet_number_from" name = "booklet_number_from" placeholder="Booklet Number From"  TYPE = "TEXT">
<input id = "booklet_number_to" name = "booklet_number_to" placeholder="Booklet Number To"  TYPE = "TEXT">
<a id = "btn_add_new" class = 'btn' >Add</a>
<span id = "Error_Message_Branch" style = "color:red; display:none;">Select branch first...</span>
<span id = "Error_Message_Form" style = "color:red; display:none;">Select form first...</span>
<span id = "Error_Message_From" style = "color:red; display:none;">Select Booklet No. from first...</span>
<span id = "Error_Message_To" style = "color:red; display:none;">Select Booklet No. to first...</span>
<span id = "Error_Message_TR" style = "color:red; display:none;">Input TR. Number first...</span>
<table id = "form_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Form</th>
			<th>Booklet No.</th>
			<th>Series From</th>
			<th>Series To</th>
			<th>Remarks</th>
			<th>Borrowed By</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody id = "record_data">
	</tbody>
</table>
	
	<div id = "record_result">
	</div>

</body>

<SCRIPT TYPE = "text/javascript">
	
	$("#booklet_number_from").keypress(function (e) {
	if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
	return false;
	}
	});

	$("#booklet_number_to").keypress(function (e) {
	if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
	return false;
	}
	});

	$('#branch_option').change(function(){
		$('#Error_Message_Branch').hide();
	});

	$('#form_option').change(function(){
		$('#Error_Message_Form').hide();
	});

	$('#booklet_number_from').keypress(function(){		
		$('#Error_Message_From').hide();
	});

	$('#booklet_number_to').keypress(function(){		
		$('#Error_Message_To').hide();
	});

	$('#tr_number').keypress(function(){		
		$('#Error_Message_TR').hide();
	});

	$('#btn_add_new').click(function(){

		
		var checkedValue = $('#check_borrowed:checked').val();

		if (checkedValue == "1"){
			var borrowed_by = 1;	
		}else{
			var borrowed_by = 0;	
		}
		

		//alert(borrowed_by);

		if ($('#branch_option').val() == 0){
			$('#Error_Message_Branch').show();
			return;
		}

		if ($('#form_option').val() == 0){
			//alert ( $(this).find(':selected').data('pcs'));
			$('#Error_Message_Form').show();
			return;
		}

		if ($('#booklet_number_from').val() == ""){
			$('#Error_Message_From').show();
			return;
		}

		if ($('#booklet_number_to').val() == ""){
			$('#Error_Message_To').show();
			return;
		}

		if ($('#tr_number').val() == ""){
			$('#Error_Message_TR').show();
			return;
		}

	

		proceedAddNewModal = b.modal.new({
		title: "Add New Booklet",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Add this Booklet?",
		buttons: {
			'Ok' : function() {
				proceedAddNewModal.hide();
				showLoading();
				b.request({
					url: "/dpr/branch/add_new_booklet",
					data: {
					"branch_id": $('#branch_option').val(),
					"form_type_id": $('#form_option').val(),
					"tr_number": $('#tr_number').val(),
					"booklet_number_from": $('#booklet_number_from').val(),
					"booklet_number_to": $('#booklet_number_to').val(),
					"borrowed_branch_id":$('#branch_borrowed').val(),
					"check_borrowed": borrowed_by
					},

					on_success: function(data){
						hideLoading();
						if (data.status == "1") {

							// show add form modal
							//alert(data.status);
							proceedAddNewItemModal = b.modal.new({
								title: 'Add New Booklet',
								width:450,
								disableClose: true,
								html: 'Successfully add new Booklet...',
								buttons: {
									'Ok' : function() {
										proceedAddNewItemModal.hide();
										showLoading();
										b.request({
											url:"/dpr/branch/refresh_list_details",
											data:{
											"tr_number": $('#tr_number').val()	
											},

										on_success: function(data){
											hideLoading();
											//alert(data.data.html);
											$('#record_data').html(data.data.html);
										}

										})
									}
								}
							});
						proceedAddNewItemModal.show();
						}else{
							proceedNoInvModal = b.modal.new({
								title: 'Add New Booklet',
								width:450,
								disableClose: false,
								html: 'Not enough inventory...'
							});
							proceedNoInvModal.show();
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
		showLoading();
		b.request({
			url: "/dpr/branch/delete_booklet",
			data:{
				"release_detail_id": $(this).attr('data')
			},
			on_success: function(data){
				hideLoading();
				proceedDeleteItemModal = b.modal.new({
					title: 'Delete booklet',
					width:450,
					disableClose: true,
					html: 'Successfully delete booklet...',
					buttons: {
						'Ok' : function() {
							proceedDeleteItemModal.hide();
							showLoading();
							b.request({
								url:"/dpr/branch/refresh_list_details",
								data:{
								"tr_number": $('#tr_number').val()	
								},

								on_success: function(data){
								hideLoading();
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