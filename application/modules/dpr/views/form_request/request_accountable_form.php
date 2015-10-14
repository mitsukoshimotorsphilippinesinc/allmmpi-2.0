<html>
<head>

<?php
	$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<div class='alert alert-danger'><h2>Request List(Accountable Forms) <a style = 'float:right;' class = 'btn' href = "add_new_accountables" >Create New Request</a> <a style = 'float:right; margin-right:5px;' class = 'btn btn-success' >Print Request</a></div>
</head>

<body>

	<form id = "accountable_search" method = "get"> 

	<span>Select Status</span>
	<span style = "margin-left:130px;">Enter Search</span>
	<br>
	<Select name = "status_option" id = "status_option" style ="margin-left:0px;">
	<option value = "All" >All</option>
	<option value = "PENDING">Pending</option>
	<option value = "RECEIVED">Received</option>
	<option value = "COMPLETED">Completed</option>
	</Select>

	<input TYPE = "TEXT" name = "txtsearch" ID = "txtsearch" onClick="SelectAll('txtsearch')";>
	
	<button id="button_search" class='btn' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
	
	<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style='width:100px;'>Date Requested</th>
			<th style='width:80px;'>Reference No.</th>
			<th style='width:80px;'>Completion Progress</th>
			<th style='width:118px;'>Status</th>
			<th style='width:118px;'>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php			
			If (empty($all_record)){
				echo "<tr><td colspan = 4> No Records Found... </td></tr>";
			}else{
				
				foreach ($all_record as $al) {
							$request_summary_id = $al->request_summary_id;
							$date_request = $al->insert_timestamp;
       						$reference_no = $al->request_code;
       						$status = $al->status;

       						// completion
       						$detail_total_count = $this->dpr_model->get_request_detail_count("request_summary_id = '{$request_summary_id}' AND status NOT IN ('CANCELLED')");
       						$detail_completed_count = $this->dpr_model->get_request_detail_count("request_summary_id = '{$request_summary_id}' AND status = 'COMPLETED'");

       						if ($detail_total_count == 0) {
       							$detail_percent_complete = "0%";
       							$detail_display = "0 / 0";
       						} else {
       							$detail_percent_complete = ($detail_completed_count / $detail_total_count) * 100;
       							$detail_display = "{$detail_completed_count} / {$detail_total_count}";
       						}	       					

       					 	 echo "<tr>			
								<td>{$date_request}</td>
								<td>{$reference_no}</td>
								<td><div class='progress'>
  										<div class='progress-bar label-success' role='progressbar' aria-valuenow='66' aria-valuemin='0' aria-valuemax='100' style='width: {$detail_percent_complete}%;'>
    										<center style='color:#ffffff'>{$detail_display}</center>
  										</div>  										
									</div>
								</td>
								<td>{$status}</td>
								<td>
								";

								echo "<a href='{$this->config->item('base_url')}/dpr/form_request/view_accountable_details/{$request_summary_id}' class = 'btn view_details btn-primary' data = '{$request_summary_id}'>View Details</a>";

								if (!($status == "CANCELLED")) {
									echo "<a  style='margin-left:5px;' class = 'btn delete_item btn-danger' data = '{$request_summary_id}'>Cancel</a>";		
								}

								echo "</td></tr>";
	
       					};
			}
		?>
		<tr>

		</tr>
	</tbody>
	</table>

</form>
<?= $this->pager->create_links($search_url);  ?>
</body>

</html>
 
<SCRIPT TYPE = "text/javascript">

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
						redirect("/dpr/form_request/accountables");
					}
				}

			})
			proceedCancelItemModal.show();
			}
		});
	})

</SCRIPT>
