<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Form Types <a id = "btn add_form" class = 'btn' style = "float:right;" href = "add_new_form">Add Form</a> </div>

<body>

	<form id = "form_search" method = "get">

<span>Select Class</span>
<Select id = "accountable_option" name = "accountable_option">
	<option value = 'All'>All</option>
	<option value = 'Accountable'>Accountable</option>
	<option value = 'Non-Accountable'>Non-Accountable</option>
</Select>
<span>Form Name</span>
<input style = "margin-left:21px;" id = "form_name" name = "form_name" placeholder="Form Name"  TYPE = "TEXT">
<button id="btn search_record" class='btn' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
<br>
<br>
<table id = "form_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Name</th>
			<th>Code</th>
			<th>Description</th>
			<th>Pcs. Per Booklet</th>
			<th>Remarks</th>
			<th style = "width:250px;">Action</th>
		</tr>
	</thead>
	<tbody id = "all_record">
		<?php
			If (empty($all_record)){
				echo "<tr><td colspan = 4> No Records Found... </td></tr>";
			}else{
				
				foreach ($all_record as $al) {
					$form_name = $al->name;
					$form_type_id = $al->form_type_id;
					$code = $al->code;
       				$description = $al->description;
       				$pieces_per_booklet = $al->pieces_per_booklet;
       				$remarks = $al->remarks;
       				$is_deleted = $al->is_deleted;
       				$is_active = $al->is_active;

       					if ($is_deleted == 1){

       					}else{

       					 	 	echo "<tr>			
								<td>{$form_name}</td>
								<td>{$code}</td>
								<td>{$description}</td>
								<td>{$pieces_per_booklet}</td>
								<td>{$remarks}</td>
								";
								if ($is_active == "1"){
								echo 	
								"<td> <a href='{$this->config->item('base_url')}/dpr/maintenance/view_edit_form/{$form_type_id}' class = 'btn update_form btn-primary' data = '{$form_type_id}'>Update</a> <a class = 'btn delete_form btn-danger' data = '{$form_type_id}'>Delete</a> <a class = 'btn de_activate_form btn-success' data = '{$form_type_id}'>De-Activate</a></td></tr>";
								}else{
								echo 	
								"<td> <a href='{$this->config->item('base_url')}/dpr/maintenance/view_edit_form/{$form_type_id}' class = 'btn update_form btn-primary' data = '{$form_type_id}'>Update</a> <a class = 'btn delete_form btn-danger' data = '{$form_type_id}'>Delete</a> <a class = 'btn activate_form btn-success' data = '{$form_type_id}'>Activate</a></td></tr>";	
								}			
							}
       					};
			}
		?>
	</tbody>
</table>
</form>
	<?= $this->pager->create_links($search_url);  ?>
</body>

<SCRIPT TYPE = "text/javascript">
	
	$('.delete_form').live('click',function(){

		var form_id = $(this).attr('data');
		proceedDeleteModal = b.modal.new({
		title: "Delete Form",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Delete this Form?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/delete_form_type_update",
					data:{
						"form_type_id": form_id
					},
					on_success: function(data){
					hideLoading();
					proceedDeleteModal.hide();
					proceedDeleteFormModal = b.modal.new({
					title: 'Delete Form',
					width:450,
					disableClose: true,
					html: 'Successfully Deleted...',
					buttons:{
						'Ok' : function(){
						proceedDeleteFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/form_types");
								}
							}

						})
					proceedDeleteFormModal.show();
					}

				});
				}
			}
		})
		proceedDeleteModal.show();
	});

	$('.activate_form').live('click',function(){

		var form_id = $(this).attr('data');
		proceedActivateModal = b.modal.new({
		title: "Activate Form",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Activate this Form?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/activate_form_type_update",
					data:{
						"form_type_id": form_id
					},
					on_success: function(data){
					hideLoading();
					proceedActivateModal.hide();
					proceedActivateFormModal = b.modal.new({
					title: 'Activate Form',
					width:450,
					disableClose: true,
					html: 'Successfully Activated...',
					buttons:{
						'Ok' : function(){
						proceedActivateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/form_types");
								}
							}

						})
					proceedActivateFormModal.show();
					}

				});
				}
			}
		})
		proceedActivateModal.show();

	});

	$('.de_activate_form').live('click',function(){

		var form_id = $(this).attr('data');
		proceedDeActivateModal = b.modal.new({
		title: "De-Activate Form",
		width:450,
		disableClose: false,
		html: "Are you sure you want to De-Activate this Form?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/de_activate_form_type_update",
					data:{
						"form_type_id": form_id
					},
					on_success: function(data){
					hideLoading();
					proceedDeActivateModal.hide();
					proceedDeActivateFormModal = b.modal.new({
					title: 'De-Activate Form',
					width:450,
					disableClose: true,
					html: 'Successfully De-Activated...',
					buttons:{
						'Ok' : function(){
						proceedDeActivateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/form_types");
								}
							}

						})
					proceedDeActivateFormModal.show();
					}

				});
				}
			}
		})
		proceedDeActivateModal.show();

	});

</SCRIPT>








