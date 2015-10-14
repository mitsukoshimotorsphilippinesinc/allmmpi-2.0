<?php
//	var_dump($all_record);
?>
<div class='alert alert-danger'><h2>Releasing of Form<a id = "btn add_tr" class = 'btn' style = "float:right;" href = "add_new_tr">New TR</a> </div>

<body>

	<form id = "release_search" method = "get">

<span>Select Branch</span>

<Select id = "branch_option" name = "branch_option">
	<option value = '0'>All</option>
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
<button id="btn search_record" class='btn' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
<table id = "form_list" class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style = "width:70px;">TR No.</th>
			<th style = "width:150px;">Branch</th>
			<th style = "width:70px;">Status</th>
			<th>Remarks</th>
			<th style = "width:140px;">Date</th>
			<th style = "width:200px;">Action</th>
		</tr>
	</thead>
	<tbody id = "all_record">
		<?php
			If (empty($all_record)){
				echo "<tr><td colspan = 4> No Records Found... </td></tr>";
			}else{
				foreach ($all_record as $al) {
					//var_dump($al);
					$release_summary_id = $al->release_summary_id;
					$branch_id = $al->branch_id;
					$tr_number = $al->tr_number;
					$status = $al->status;
       				$remarks = $al->remarks;
       				$date_released = $al->insert_timestamp;
       				
       				$branch_info = $this->human_relations_model->get_branch_by_id($branch_id);

       				echo "<tr>			
					<td style = 'text-align:center;'>{$tr_number}</td>
					<td>{$branch_info->branch_name}</td>
					<td>{$status}</td>
					<td>{$remarks}</td>
					<td>{$date_released}</td>";
					if (($status == "CANCELLED") || ($status == "RECIEVED")){
						echo 	
						"<td> <a href='{$this->config->item('base_url')}/dpr/branch/view_release_detail/{$release_summary_id}' class = 'btn view_details btn-primary' data = '{$release_summary_id}'>View Details</a></td></tr>";
					}else{
						echo 	
						"<td> <a href='{$this->config->item('base_url')}/dpr/branch/view_release_detail/{$release_summary_id}' class = 'btn view_details btn-primary' data = '{$release_summary_id}'>View Details</a> <a class = 'btn cancel_release btn-danger' data = '{$release_summary_id}'>Cancel</a></td></tr>";
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

	$('.cancel_release').live('click',function(){
		b.request({
			url: "/dpr/branch/cancel_update_summary_release",
			data:{
				"release_summary_id": $(this).attr('data')
			},
			on_success: function(data){
				hideLoading();
				proceedCancelItemModal = b.modal.new({
				title: 'Cancel Release',
				width:450,
				disableClose: true,
				html: 'Successfully Updated...',
				buttons:{
					'Ok' : function(){
						proceedCancelItemModal.hide();
						showLoading();
						redirect("/dpr/branch/releasing_of_form");
					}
				}

			})
			proceedCancelItemModal.show();
			}
		});
	});

</SCRIPT>