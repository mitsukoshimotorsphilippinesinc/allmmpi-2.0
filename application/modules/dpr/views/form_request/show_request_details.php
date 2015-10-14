
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
                              <th>Action</th>
                        </tr>
                  </thead>
           <tbody>
            <?php
       	$html = "";

       	    foreach ($request_summary_last as $rsl) {
       						$branch_id = $rsl->branch_id;
       						$form_type_id = $rsl->form_type_id;
       						$printing_press_id = $rsl->printing_press_id;

       						$last_series = $rsl->last_serial_number;
       						$quantity = $rsl->quantity;
       						$status = $rsl->status;

							$branch_info = $this->human_relations_model->get_branch_by_id($branch_id);
							$form_info = $this->dpr_model->get_form_by_id($form_type_id);
							$printing_press_info = $this->dpr_model->get_printing_press_by_id($printing_press_id);
       					   

       					 	$html .= "<tr>			
								<td>{$branch_info->branch_name}</td>
								<td>{$branch_info->tin}</td>
								<td>{$form_info->name}</td>
								<td>{$last_series}</td>
								<td>{$form_info->pieces_per_booklet}</td>
								<td>{$quantity}</td>		
								<td>{$printing_press_info->complete_name}</td>
								<td>{$status}</td>
				  			</tr>";			

       					};

    	
  
		$html .= "</tbody></table>";					
		