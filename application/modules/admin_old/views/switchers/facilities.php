<?php 

	if (ci()->current_system == 'inventory' || ci()->current_system == 'webpoi')
	{		
		$user_id = ci()->user->user_id;

		$selected_facility = ci()->selected_facility;
		
		if ($selected_facility == FALSE) 
		{
			$selected_facility = $this->users_model->get_user_facilities("user_id = {$user_id} AND is_default = 1",NULL,NULL,"facility_id,facility_name");			
			$selected_facility = $selected_facility[0];
		}
		
		// get user facilities
		
		$user_facilities = $this->users_model->get_user_facilities("user_id = {$user_id}",NULL,NULL,"facility_id,facility_name,is_default");
		
		$uf_tags = "";
		
		foreach ($user_facilities as $uf)
		{
			if ($uf->facility_id == $selected_facility->facility_id)
			{
				$facility_name = $uf->facility_name;
				continue; // skip this item
			}
					
			$uf_tags .= "<li><a href='#' class='facility_selector' data-id='{$uf->facility_id}'>{$uf->facility_name}</a></li>";
		}
		
		$_html_tags = "<div class='btn-group'>
		  <a class='btn btn-info dropdown-toggle' data-toggle='dropdown' href='#'>
		    <span id='selected_facility'>{$facility_name}</span>
		    <span class='caret'></span>
		  </a>
		  <ul class='dropdown-menu'>
			{$uf_tags}
		  </ul>
		</div>";
		
		echo $_html_tags;
	} 
	else
	{
		echo "";
	}


