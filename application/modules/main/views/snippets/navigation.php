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

<div id='site-top-menu' class="navbar">
	<ul class="nav pull-right">
		<!--li class="dropdown-auto _menu_items">
			<a class="dropdown-toggle" data-toggle="dropdown" href="/profile">My Profile</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="/main/products" class="dropdown-important">All Products</a></li>
				<li><a href="/main/products/view/1">Vital C Sodium Ascorbate</a></li>
				<li><a href="/main/products/view/2">Daily Supplement Line</a></li>
				<li><a href="/main/products/view/3">Beverage Line</a></li>
				<li><a href="/main/products/view/4">Personal Hygiene Line</a></li>
				<li><a href="/main/products/view/5">Personal Care Line</a></li>
				<li><a href="/main/products/view/0">Packages</a></li>
			</ul>
		</li>
		<li class="dropdown-auto _menu_items">
			<a class="dropdown-toggle" data-toggle="dropdown" href="/messages">Messages</a>
			<!--ul class="dropdown-menu" role="menu">
				<li><a href="/pages/how-to-join" class="dropdown-important">How to Join</a></li>
				<li><a href="/pages/starter-packages">Packages</a></li>
				<li><a href="/pages/unilevel">Unilevel</a></li>
				<li><a href="/pages/direct-selling">Direct Selling</a></li>
				<li><a href="/pages/igpsm">IGPSM</a></li>
			</ul>
		</li>
		<li class="dropdown-auto _menu_items">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">My Department</a>
			<!--ul class="dropdown-menu" role="menu">
				<li><a href="/pages/chairman">Chairman</a></li>
				<li><a href="/pages/management">Management</a></li>
				<li><a href="/pages/members">Members</a></li>
				<li><a href="/pages/awards-and-certificates">Awards and Certificates</a></li>
				<li><a href="/pages/doctors-profile">Doctor's Profile</a></li>
				<li><a href="/main/contactus/page">Contact Us </a></li>
				<li><a href="/main/faqs">FAQs</a></li>
				<li><a href="/pages/careers-at-vitalc">Careers</a></li>
			</ul>
		</li>
		<li class="dropdown-auto _menu_items">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Tools</a>
			<!--ul class="dropdown-menu" role="menu">
				<li><a href="/main/news">News</a></li>
				<li><a href="/main/gallery">Gallery</a></li>
			</ul>
		</li>
		<li class="dropdown-auto _menu_items">
			<a href="#">Workflow Management</a>
		</li>
		<li class="dropdown-auto _menu_items">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Settings</a>
			<!--ul class="dropdown-menu" role="menu">
				<li><a href="/main/wall/grandmasters">Grand Masters</a></li>
				<li><a href="/main/wall/presidents">Presidents</a></li>
				<li><a href="/main/wall/millionaires">Elite</a></li>
				<li><a href="/main/wall/results">Results</a></li>
			</ul>
		</li>
		<li class="dropdown-auto _menu_items">					
			<a href="#">Menu7</a>			
		</li-->
	</ul>
</div>
<script type="text/javascript">
$(function() {
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
	$('body').on('touchstart.dropdown', '.dropdown-submenu', function (e) { e.stopPropagation(); });
});
</script>