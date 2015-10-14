<?php
	// make sure that model is loaded
	$this->load->model('navigations_model');
	$navs = $this->navigations_model->get_navigations_by_system($this->current_system);

	$nav_items = array();
	for ($idx = 0; $idx < count($navs); $idx++)
	{
		$nav_items[$navs[$idx]->navigation_id] = $navs[$idx];
		$nav_items[$navs[$idx]->navigation_id]->children = array();
	}
	
	// create a child section
	$parents = array();
	for ($idx = 0; $idx < count($navs); $idx++)
	{
		if ($navs[$idx]->parent_id != 0 && isset($nav_items[$navs[$idx]->parent_id]))
		{
			$parents[] = $navs[$idx]->parent_id;
			$nav_items[$navs[$idx]->parent_id]->children[] = $navs[$idx];
		}
		
		if ($navs[$idx]->parent_id == 0) 
		{
			$parents[] = $navs[$idx]->navigation_id;
		}
	}
		
	$parents = array_unique($parents);
	

	$nav_tags = '';
	foreach ($parents as $parent_id)
	{
		if (count($nav_items[$parent_id]->children) > 0)
		{
			$parent_active_class = '';
			$sub_nav_tags = '';
			foreach ($nav_items[$parent_id]->children as $subitem)
			{
				$active_class = $subitem->code == $this->selected_menu ? 'active' : '';
				if ($active_class == 'active' && $parent_active_class == '') $parent_active_class = 'active';
				$sub_nav_tags .= '<li class="'.$active_class.'"><a href="'.$subitem->url.'">'.$subitem->title.'</a><li>';
			}
			
			$nav_tags .= '<li class="dropdown dropdown-auto '.$parent_active_class.' ">
		        <a >'.$nav_items[$parent_id]->title.' <b class="caret"></b></a>
		        <ul class="dropdown-menu">
		          '.$sub_nav_tags.'
		        </ul>
		      </li>';
			
		}
		else 
		{
			$active_class = $nav_items[$parent_id]->code == $this->selected_menu ? 'active' : '';
			$nav_tags .= '<li class=" '.$active_class.' "><a href="'.$nav_items[$parent_id]->url.'">'.$nav_items[$parent_id]->title.'</a><li>';
		}
	}

?>

<div class="subnav subnav-fixed">
	<ul class="nav nav-pills">
		<?= $nav_tags ?>
	</ul>
</div>