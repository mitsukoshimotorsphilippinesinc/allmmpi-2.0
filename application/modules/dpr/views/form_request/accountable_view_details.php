<html>

<head>
<?php
$breadcrumb_container = assemble_breadcrumb();
?>

<?= $breadcrumb_container; ?>
<h2>Request Code: <?= $request_code ?> <a class="btn btn-small" style="float:right" href="/dpr/form_request/accountables">Back</a></h2>
</head>

<body>

<br>
<table id = "request_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Branch</th>
			<th>TIN</th>
			<th>Type Of Form</th>
			<th>Last Series No.</th>
			<th>Pcs. Per Booklet</th>
			<th>QTY</th>		
			<th>Printing Press</th>
			<th>Status</th>
			<th>Current State</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$this->db_dpr = $this->load->database('dpr', TRUE);

			foreach ($record_detail as $rd) {
				$branch_id = $rd->branch_id;
   				$form_type_id = $rd->form_type_id;
   				$printing_press_id = $rd->printing_press_id;
   				$request_detail_id = $rd->request_detail_id;
   				$status = $rd->status;

   				$last_series = $rd->last_serial_number;
   				$quantity = $rd->quantity;
   				$status = $rd->status;

   				$branch_info = $this->human_relations_model->get_branch_by_id($branch_id);
				$form_info = $this->dpr_model->get_form_by_id($form_type_id);
				$printing_press_info = $this->dpr_model->get_printing_press_by_id($printing_press_id);

				$sql = "SELECT COLUMN_NAME as col_name, ORDINAL_POSITION, COLUMN_DEFAULT
				FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA = 'dpr' AND TABLE_NAME = 'tr_request_detail'
				AND DATA_TYPE = 'timestamp' 
				AND COLUMN_DEFAULT = '0000-00-00 00:00:00'
				AND COLUMN_NAME NOT IN ('date_delivered', 'update_timestamp')
				ORDER BY ORDINAL_POSITION;";

				$detail_update_column = $this->db_dpr->query($sql);
				$detail_update_column = $detail_update_column->result(); 

				$has_pending = 0;
				$current_col_state = "";

				foreach ($detail_update_column as $duc) {
		
					$sql_test = "SELECT ". $duc->col_name ." AS timestamp_col FROM tr_request_detail WHERE request_detail_id = {$request_detail_id}";
					$detail_test = $this->db_dpr->query($sql_test);
					$detail_test = $detail_test->result(); 
					$detail_test = $detail_test[0]; 

					if ($detail_test->timestamp_col == '0000-00-00 00:00:00') {
						$has_pending = 1;
						$current_col_state = str_replace("_", " ", strtoupper($duc->col_name));
						break;
					}
				}	

				$status_class = str_replace(" ", "-", $status);

				echo "
				<tr>
					<td>{$branch_info->branch_name}</td>
					<td>{$branch_info->tin}</td>
					<td>{$form_info->name}</td>
					<td style='text-align:right'>{$last_series}</td>
					<td style='text-align:right'>{$form_info->pieces_per_booklet}</td>
					<td style='text-align:right'>{$quantity}</td>		
					<td>{$printing_press_info->complete_name}</td>										
					<td><span class='label label-{$status_class}' >{$status}</span></td>				
					<td>{$current_col_state}</td>";
					
						if ($has_pending == 1){
							If ($status == "CANCELLED"){
								echo"
								<td><a id = 'update_detail_item' class = 'btn update_item btn-primary' data = '{$request_detail_id}'>View Details</a><a style = 'display:none;' id = 'update_detail_item' class = 'btn update_item btn-success' data = '{$request_detail_id}'>Update</a></td>";	
							}else{
								echo"
								<td><a id = 'update_detail_item' class = 'btn update_item btn-success' data = '{$request_detail_id}'>Update</a></td>";
							}	
						}else{
							echo"
							<td><a id = 'update_detail_item' class = 'btn update_item btn-primary' data = '{$request_detail_id}'>View Details</a></td>";	
						}
				echo"					
				</tr>";

			}
		?>
	</tbody>
</table>

</body>

<SCRIPT TYPE = "text/javascript">

	$('.update_item').click(function(){

		var request_detail_id = $(this).attr('data');
		var request_summary_id = '<?=$record_summary[0]->request_summary_id?>';
		b.request({
			url: "/dpr/form_request/view_update_accountable",
			data:{
				"request_detail_id": request_detail_id
			},
		
			on_success: function(data){

				if (data.data.request_detail_status == "PENDING") {
					proceedAddNewModal = b.modal.new({
						title: "Update Request Detail",
						width:1000,
						disableClose: false,
						html: data.data.html,
						buttons: {
							'Update' : function() {
								proceedupdateNewItemModal = b.modal.new({
									title: 'Update Item',
									width:450,
									disableClose: true,
									html: 'Are you sure you want to update' + " " + data.data.current_col_name + " " + "of this Item",
									buttons: {
										'Cancel' : function() {
											proceedupdateNewItemModal.hide();
										}
										,
										'Ok' : function() {
											b.request({
												url: "/dpr/form_request/proceed_update_request_detail",
												data:{
													'request_detail_id' : request_detail_id,
													'current_col_name_unformat' : data.data.current_col_name_unformat
												},				
												on_success: function(data){
													proceedupdateNewItemModal.hide();
													proceedAddNewModal.hide();
													
													proceedSaveNewItemModal = b.modal.new({
													title: 'Update Item',
													width:450,
													disableClose: true,
													html: 'Successfully updating this Item',
													buttons:{
														'Ok':function(){
															redirect('dpr/form_request/view_accountable_details/' + request_summary_id +'');
														}
													}
													});
													proceedSaveNewItemModal.show();
												}
											})
										}
									}
								});
								proceedupdateNewItemModal.show();
							}
						}					
					})			
					proceedAddNewModal.show();

				} else {
					proceedAddNewModal = b.modal.new({
						title: "Update Request Detail",
						width:1000,
						disableClose: false,
						html: data.data.html						
					})			
					
					proceedAddNewModal.show();
				}
			}
		});
	});
</SCRIPT>