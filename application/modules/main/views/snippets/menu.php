<div id='members-top-menu' class="navbar" style="margin-top:4em;">
	<ul class="nav pull-right">
		<!--?php if ($this->authenticate->is_logged_in()) : ?-->
		<?php 
			if ($this->authenticate->e_is_logged_in()) { ?>
			
			<li class='dropdown-auto'>
				<a id='nav_members_area' class="dropdown-toggle" data-toggle="dropdown" href='/members'>My Menu<i class='icon-play-down'></i></a>
				<ul class='dropdown-menu' role='menu'>
					<li role='menuitem'><a href="#" >My Profile</a></li>
					<li role='menuitem'><a href='#'>My Department</a></li>
					<li role='menuitem'><a href="#" >Options</a></li>					
					<li role='menuitem'><a href="#" >Tools</a></li>
					<li role='menuitem' class='divider'><div class='divider'></div></li>
					<li role='menuitem'><a href='/employee/signout'><i class="icon-off"></i> Sign Out</a></li>
				</ul>
			</li>

		<?php

			$fullname = $this->employee->first_name . " " . $this->employee->last_name;
			$image_filename = "male.jpg";
			if (empty($this->employee->image_filename) || ($this->employee->image_filename == NULL) || (trim($this->employee->image_filename) == "")) {
				// check gender of member
				if (trim($this->employee->gender) == "FEMALE") {
					$image_filename = "ni_female.png";
				} else {
					$image_filename = "ni_male.png";
				}
			} else {
				$image_filename = $this->employee->image_filename;
			}
		?>



			<img placeholder="your image here" src="<?= $this->config->item('admin_base_url') . '/assets/media/employees/'. $image_filename ?>" style="width:50px;height:50px;margin-top:10px;float:right;border:1px dashed gray;"></img>
			<label style="margin-top:10px;margin-right:10px;float:right">Hello, <?= $fullname ?></label>

		<!--?php else : 	?-->
		<?php 
			} else {
		
				if ($this->setting->disable_employee_login == 0) {
					echo "<li class='dropdown-auto'><a href='/employee/signin'>Login</a></li>";
				}

				if ($this->setting->disable_registration == 0) {
					echo "<li class='dropdown-auto'><a href='/main/registration/'>Register</a></li>";
				}
			}
		?>
		<!--?php 
			}
		endif; ?-->

		<!--li class='dropdown-auto'><a href="/main/contactus">Contact Us</a></li>
		<li class='dropdown-auto'><a href="/main/faqs">FAQs</a></li-->
		<!--li>
			<form class="search-query">
				<input placeholder="Search"/>
				<a href="#"><i>icon-search</i></a>
			</form>
		</li-->
	</ul>
</div>
<script type="text/javascript">
$(function() {
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
	$('body').on('touchstart.dropdown', '.dropdown-submenu', function (e) { e.stopPropagation(); });
});
</script>