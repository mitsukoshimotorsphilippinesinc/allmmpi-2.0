<html>
<head>

<?PHP
?>

<div class='alert alert-danger'><h2>Request List(Non-Accountable Forms) <a style = 'float:right;' class = 'btn' href = "add_new_non_accountables">Create New Request</a> <a style = 'float:right; margin-right:5px;' class = 'btn btn-success' >Print Request</a></div>
</head>

<body>

	<form id = "non_accountable_search" method = "get"> 

	<label>Select Status</label>
	<Label style = "position:absolute; left:500px; top:213px;">Enter Search</Label>
	<Select name = "status_option" id = "status_option" style ="margin-left:0px;">
	<option value = "All" >All</option>
	<option value = "PENDING">Pending</option>
	<option value = "RECEIVED">Received</option>
	<option value = "COMPLETED">Completed</option>
	</Select>

	<input TYPE = "TEXT" ID = "txtsearch" onClick="SelectAll('txtsearch')";>

	<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
	<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style='width:100px;'>Date Requested</th>
			<th style='width:80px;'>Reference No.</th>
			<th style='width:118px;'>Status</th>
			<th style='width:130px;'>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
			//var_dump($all_record);
			If (empty($all_record)){
				echo "<tr><td colspan = 4> No Records Found... </td></tr>";
			}else{
				
				foreach ($all_record as $al) {
							$request_summary_id = $al->request_summary_id;
							$date_request = $al->insert_timestamp;
       						$reference_no = $al->request_code;
       						$status = $al->status;

       					 	 echo "<tr>			
								<td>{$date_request}</td>
								<td>{$reference_no}</td>
								<td>{$status}</td>";
								if (($status == "COMPLETED") || ($status == "CANCELLED") || ($status == "RECEIVED") || ($status == "RETURNED")){
								echo 	
								"<td><a href = 'view_non_accountable_details/{$request_summary_id}' class = 'btn view_details btn-primary' data = '{$request_summary_id}'>View Details</a> <a disabled = 'disabled' class = 'btn delete_item btn-danger' data = '{$request_summary_id}'>Cancel</a> <a disabled = 'disabled' class = 'btn update_record btn-success' data = '{$request_summary_id}'>Approved</a></td></tr>";
								}else{
								echo 	
								"<td><a href = 'view_non_accountable_details/{$request_summary_id}' class = 'btn view_details btn-primary' data = '{$request_summary_id}'>View Details</a> <a class = 'btn delete_item btn-danger' data = '{$request_summary_id}'>Cancel</a> <a class = 'btn update_record btn-success' data = '{$request_summary_id}'>Approved</a></td></tr>";	
				  			}
       					};
			}
		?>
		<tr>

		</tr>
	</tbody>
	</table>
		
</body>

</html>
 
<SCRIPT TYPE = "text/javascript">

	$('.update_record').live('click',function(){
		b.request({
			url: "/dpr/form_request/update_summary_request",
			data:{
				"request_summary_id": $(this).attr('data')
			},
			on_success: function(data){
				hideLoading();
				proceedUpdateItemModal = b.modal.new({
				title: 'Approve Request',
				width:450,
				disableClose: true,
				html: 'Successfully Updated...',
				buttons:{
					'Ok' : function(){
						proceedUpdateItemModal.hide();
						showLoading();
						redirect("/dpr/form_request/non_accountables");
					}
				}

			})
			proceedUpdateItemModal.show();
			}
		});
	})

	$('.delete_item').live('click',function(){
		b.request({
			url: "/dpr/form_request/cancel_update_summary_request",
			data:{
				"request_summary_id": $(this).attr('data')
			},
			on_success: function(data){
				hideLoading();
				proceedCancelItemModal = b.modal.new({
				title: 'Cancel Request',
				width:450,
				disableClose: true,
				html: 'Successfully Updated...',
				buttons:{
					'Ok' : function(){
						proceedCancelItemModal.hide();
						showLoading();
						redirect("/dpr/form_request/non_accountables");
					}
				}

			})
			proceedCancelItemModal.show();
			}
		});
	})

</SCRIPT>