<?php
/*
<ul class="nav pull-right">	
	<?php	
		if (!isset($selected_menu)) $selected_menu = 'home';
		
		// get all site links
		$navigation_links = $this->navigations_model->get_navigations("system_code = 'site' AND parent_id = 0", NULL,"ordering ASC");
		foreach ($navigation_links as $l) {
			if ($l->is_active) {
				$link = $this->config->item('base_url') . $l->url;				
				echo "<li>";
				echo "<a href='{$link}'>{$l->title}</a>";
				echo "</li>";
			}
		}
	?>
</ul>
*/
?>
<style type="text/css">
	._menu_items a {
		padding-left: 10px !important;
		padding-right: 10px !important;
	}
</style>

<div id='site-top-menu' class="navbar" style="margin-top:-1.5em">
	<ul class="nav pull-right">		
	</ul>
</div>
<script type="text/javascript">
$(function() {
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
	$('body').on('touchstart.dropdown', '.dropdown-submenu', function (e) { e.stopPropagation(); });
});
</script>