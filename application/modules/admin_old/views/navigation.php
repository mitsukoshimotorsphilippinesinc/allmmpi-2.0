<?php
	// make sure that model is loaded
	$this->load->model('navigations_model');
	$nav_items = $this->navigations_model->get_navigations_by_system($this->current_system);

	$nav_tags = '';
	foreach ($nav_items as $nav)
	{
		if (strtolower($nav->type) == 'link')
		{ // nav link
			$active_class = $nav->code == $this->selected_menu ? 'active' : '';
			$nav_tags .= '<li class="'.$active_class.'"><a href="'.$nav->url.'">'.$nav->title.'</a><li>';
		}
		else
		{ // assumed that other than link are headers
			$nav_tags .= '<li class="nav-header">'.$nav->title.'<li>';
		}
	}
?>
<div class='well' style='padding: 8px 0;'>

	<ul class="nav nav nav-list">
		<?= $nav_tags ?>
	</ul>
</div>