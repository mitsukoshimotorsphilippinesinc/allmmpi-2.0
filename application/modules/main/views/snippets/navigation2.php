<ul class="nav">	
	<?php
		if (!isset($selected_menu)) $selected_menu = 'home';
		
		$selected_parent_menu = $this->navigations_model->get_navigations("system_code = 'site' AND code = '{$selected_menu}'");
		if (!empty($selected_parent_menu))
			$parent_id = $selected_parent_menu[0]->navigation_id;
		else 
			$parent_id = -1;
				
						
		// get all site links
		$navigation_links = $this->navigations_model->get_navigations("system_code = 'site' AND parent_id = {$parent_id}");
		if (!empty($navigation_links))
		{			
			foreach ($navigation_links as $l) {
				if ($l->is_active) {
					$link = $this->config->item('base_url') . $l->url;

					if ($selected_menu == $l->code)
						echo "<li class='active'><a href='{$link}'>{$l->title}</a></li>";
					else
						echo "<li><a href='{$link}'>{$l->title}</a></li>";				
				}
			}
		}
	?>
</ul>