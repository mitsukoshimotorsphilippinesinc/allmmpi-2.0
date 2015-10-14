<?php
$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Printing Press <a id = "btn add_printing_press" style="float:right;margin-right:-30px;margin-top:5px;" class = "btn" href = "add_new_printing_press">Add Printing Press</a> </div>

<body>

	<form id = "printing_press_search" method = "get">

<span>Name</span>
<input style = "margin-left:21px;" id = "press_name" name = "press_name" placeholder="Printing Press Name"  TYPE = "TEXT">
<button id="btn search_record" class='btn' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
<br>
<br>
<table id = "press_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Name</th>
			<th>Address</th>
			<th>Contact No</th>
			<th>Contact Person</th>
			<th>Is Active?</th>
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
					$printing_press_id = $al->printing_press_id;
					$press_name = $al->complete_name;
					$press_address = $al->complete_address;
					$contact_no = $al->contact_number;
       				$contact_person = $al->contact_person;
       				$remarks = $al->remarks;
       				$is_deleted = $al->is_deleted;
       				$is_active = $al->is_active;

       				if ($is_active == 1)
       					$html = "<span class='alert-success'><strong>YES</strong></span>";
       				else 
       					$html = "<span class='alert-danger'><strong>NO</strong></span>";

       					if ($is_deleted == 1){

       					}else{

       					 	 	echo "<tr class='danger'>			
								<td>{$press_name}</td>
								<td>{$press_address}</td>
								<td>{$contact_no}</td>
								<td>{$contact_person}</td>
								<td style='text-align:center'>{$html}</td>
								<td>{$remarks}</td>
								";
								if ($is_active == "1"){
								echo 	
								"<td> <a href='{$this->config->item('base_url')}/dpr/maintenance/view_edit_printing_press/{$printing_press_id}' class = 'btn update_press btn-primary' data = '{$printing_press_id}'>Update</a> <a class = 'btn delete_press btn-danger' data = '{$printing_press_id}'>Delete</a> <a class = 'btn de_activate_press btn-success' data = '{$printing_press_id}'>De-Activate</a></td></tr>";
								}else{
								echo 	
								"<td> <a href='{$this->config->item('base_url')}/dpr/maintenance/view_edit_printing_press/{$printing_press_id}' class = 'btn update_press btn-primary' data = '{$printing_press_id}'>Update</a> <a class = 'btn delete_press btn-danger' data = '{$printing_press_id}'>Delete</a> <a class = 'btn activate_press btn-success' data = '{$printing_press_id}'>Activate</a></td></tr>";	
								}			
							}
       					};
			}
		?>
	</tbody>
<table>
</form>
	<?= $this->pager->create_links($search_url);  ?>
</body>

<SCRIPT TYPE = "text/javascript">

	$('.delete_press').live('click',function(){
		var press_id = $(this).attr('data');
		proceedDeleteModal = b.modal.new({
		title: "Delete Printing Press",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Delete this Printing Press?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/delete_printing_press_update",
					data:{
						"printing_press_id": press_id
					},
					on_success: function(data){
					hideLoading();
					proceedDeleteModal.hide();
					proceedDeleteFormModal = b.modal.new({
					title: 'Delete Printing Press',
					width:450,
					disableClose: true,
					html: 'Successfully Deleted...',
					buttons:{
						'Ok' : function(){
						proceedDeleteFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/printing_press");
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
	
	$('.activate_press').live('click',function(){

		var press_id = $(this).attr('data');
		proceedActivateModal = b.modal.new({
		title: "Activate Printing Press",
		width:450,
		disableClose: false,
		html: "Are you sure you want to Activate this Printing Press?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/activate_printing_press_update",
					data:{
						"printing_press_id": press_id
					},
					on_success: function(data){
					hideLoading();
					proceedActivateModal.hide();
					proceedActivateFormModal = b.modal.new({
					title: 'Activate Printing Press',
					width:450,
					disableClose: true,
					html: 'Successfully Activated...',
					buttons:{
						'Ok' : function(){
						proceedActivateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/printing_press");
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

	$('.de_activate_press').live('click',function(){

		var press_id = $(this).attr('data');
		proceedActivateModal = b.modal.new({
		title: "De-Activate Printing Press",
		width:450,
		disableClose: false,
		html: "Are you sure you want to De-Activate this Printing Press?",
		buttons: {
			'Ok' : function() {
				b.request({
					url: "/dpr/maintenance/de_activate_printing_press_update",
					data:{
						"printing_press_id": press_id
					},
					on_success: function(data){
					hideLoading();
					proceedActivateModal.hide();
					proceedActivateFormModal = b.modal.new({
					title: 'De-Activate Printing Press',
					width:450,
					disableClose: true,
					html: 'Successfully De-Activated...',
					buttons:{
						'Ok' : function(){
						proceedActivateFormModal.hide();
						showLoading();
						redirect("/dpr/maintenance/printing_press");
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

</SCRIPT>