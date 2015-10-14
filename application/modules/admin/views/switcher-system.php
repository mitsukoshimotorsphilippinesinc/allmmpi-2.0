<?php

	$nav_tags = "";
	if ($this->uri->segment(1) == "admin") {

		$active_class = "Accounting";		
		
		$ctr = 0;
		foreach ($this->systems as $item)
		{				
			$url = $this->config->item('base_url') . '/' . $item->url;			
			if ($ctr == 0) {
				$nav_tags .= '<li ><a href="'. $url .'" class="'.$active_class.' active">'.$item->department_name .'</a><li>';	
			} else {
				$nav_tags .= '<li ><a href="'. $url .'" class="'.$active_class.'">'.$item->department_name .'</a><li>';	
			}
			$ctr++;	
		}

	} else {

		foreach ($this->systems as $item)
		{	
			$active_class = $item->department_name == ci()->current_system ? 'active' : '';		
			$url = $this->config->item('base_url') . '/' . $item->url;			
			$nav_tags .= '<li ><a href="'. $url .'" class="'.$active_class.'">'.$item->department_name .'</a><li>';	
		}
	}

?>

<div class="switcherMenu">
	<ul style="margin-left:10px;" class="dropdown">
		<?= $nav_tags; ?>
	</ul>
</div>