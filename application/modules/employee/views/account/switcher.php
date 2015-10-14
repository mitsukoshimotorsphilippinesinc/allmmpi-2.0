<?php 

		$member_id = ci()->member->member_id;
		
		$this->selected_account = $this->session->userdata('selected_account');
		
		if ($this->selected_account==FALSE)
		{
			$selected_account = $this->members_model->get_member_accounts("member_id = {$this->member->member_id}",NULL,"insert_timestamp ASC");			
			$selected_account = $selected_account[0];			
			$selected_account_id = $selected_account->account_id;
		}
		else
		{
			$selected_account = $this->selected_account;
		}

		$member_accounts = $this->members_model->get_member_accounts("member_id = {$member_id}",NULL,"length(node_address), insert_timestamp","member_id,account_id,monthly_maintenance_ctr, ms_monthly_maintenance_ctr,annual_maintenance_ctr, ms_annual_maintenance_ctr, sms_notification");
		
		$tags = "";
		$tag_is = "";
		foreach ($member_accounts as $ma)
		{
			
			$maintenance_tag = "&nbsp;";
			$sms_tag = "&nbsp;";
			// get monthly and annual maintenance values		
			if (($ma->monthly_maintenance_ctr + $ma->ms_monthly_maintenance_ctr) < $this->settings->monthly_maintenance) {
				$maintenance_tag .= "<span class='label label-important'>M</span>";
			}
			
			if (( $ma->annual_maintenance_ctr + $ma->ms_annual_maintenance_ctr) < $this->settings->monthly_maintenance) {
				$maintenance_tag .= "&nbsp;<span class='label label-important'>A</span>";
			}
			
			if ($ma->sms_notification == 0) {
				$sms_tag .= "&nbsp;<span class='label label-important'>sms</span>";
			} else {
				$sms_tag .= "&nbsp;<span class='label label-success'>sms</span>";
			}
			
		
			if ($ma->account_id == $selected_account->account_id)
			{
				$_account_id = $ma->account_id;
				$tag_is = $maintenance_tag;
				$sms_tag_is = $sms_tag;
				//continue; // skip this item
			}
					
			$tags .= "<li><a href='#' class='account_selector' data-id='{$ma->account_id}'>{$sms_tag}&nbsp;{$ma->account_id}{$maintenance_tag}</a></li>";
		}
		
		$_html_tags = "<div class='btn-group'>
		  <a class='btn btn-success dropdown-toggle' data-toggle='dropdown' href='#'>
		    <span id='selected_account'>{$sms_tag_is}&nbsp;{$_account_id}{$tag_is}</span>
		    <span class='caret'></span>
		  </a>
		  <ul class='dropdown-menu'>
			{$tags}
		  </ul>
		</div>";
		
		echo $_html_tags;