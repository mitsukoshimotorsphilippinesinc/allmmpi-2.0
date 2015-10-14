<?php

	$login_url = $this->config->item('base_url') . "/members/signin";
	$logout_url = $this->config->item('base_url') . "/members/signout";
	$contact_url = $this->config->item('base_url') . "/main/contactus";		
	$registration_url = $this->config->item('base_url') . "/main/registration";		
	$faq_url = $this->config->item('base_url') . "/main/faqs";		
	//$members_url = $this->config->item('base_url') . "/members";


	if ($this->member->member_id!=0) {
			
		echo "<ul class='secondary-nav'>
				<li><a href='{$logout_url}'>Logout</a></li>
				<li><a href='{$registration_url}'>Register</a></li>
				<li><a href='{$contact_url}'>Contact Us</a></li>
				<li><a href='{$faq_url}'>FAQs</a></li>
				</ul>";				


	} else {
		echo "<ul class='secondary-nav'>
				<li><a href='{$login_url}'>Login</a></li>
				<li><a href='{$registration_url}'>Register</a></li>
				<li><a href='{$contact_url}'>Contact Us</a></li>
				<li><a href='{$faq_url}'>FAQ</a></li>
				</ul>";				
	}
?>