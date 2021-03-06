<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Mitsukoshi Motors Philippines Inc</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="shortcut icon" href="<?= image_url('/favicon.ico'); ?>" />
	<?php echo css('bootstrap.css');?>
	<?php echo css('main.css');?>
	<?php echo css('mmpi.css');?>
	<?php echo css('slider.css');?>
	<?php echo css('webpoi.css');?>
	<?php echo css('jquery-ui/jquery-ui-1.9.1.css');?>

	<?php echo js('libs/jquery-1.8.3.min.js'); ?>
	<?php echo js('libs/jquery-ui-1.9.1.min.js'); ?>
	<?php echo js('libs/dropdown.js'); ?>
	<?php echo js('libs/modernizr-2.5.3-respond-1.1.0.min.js'); ?>
	<?php echo js('libs/jquery-ui-timepicker-addon.js'); ?>
	<?php echo js('libs/underscore-min.js'); ?>
	<?php echo js('libs/bootstrap.min.js'); ?>
	<?php echo js('libs/tweet/jquery.tweet.js'); ?>
	<?php echo js('libs/uploadrr.js'); ?>
	<?php echo js('apps/core.js'); ?>
	<?php echo js('apps/site.js'); ?>
	<!--?php echo js('libs/pdf.js'); ?-->
	<?php if($this->member->member_id>0) : ?>
		<script type="text/javascript" >
			vitalc.member.member_id = <?=$this->member->member_id;?>;
			vitalc.member.first_name = "<?=$this->member->first_name;?>";
			vitalc.member.last_name = "<?=$this->member->last_name;?>";
			vitalc.member.funds = <?=$this->member->funds;?>;
			vitalc.member.gift_cheques = <?=$this->member->gift_cheques;?>;
			vitalc.member.gcep = <?=$this->member->gcep;?>;
			vitalc.member.on_hold_funds = <?=$this->member->on_hold_funds;?>;
			vitalc.member.is_paycard_corpo = <?=$this->member->is_paycard_corpo;?>;
			<?php
				if (!isset($this->session->userdata('selected_account')->account_id))
				{
					$accounts = $this->members_model->get_member_accounts("member_id = {$this->member->member_id}",array("rows"=>1,"offset"=>0),"insert_timestamp ASC");
					$selected_account_id = $accounts[0]->account_id;
				}
				else
				{
					$selected_account_id = $this->session->userdata('selected_account')->account_id;		
				}
			?>
			vitalc.member.selected_account_id = '<?=$selected_account_id?>';
		</script>
	<?php endif; ?>
	
</head>

<body>

	<div id="loading_overlay">
	    <div class="loading_message well clearfix">
		<img src='/assets/img/loading.gif' class='pull-left' style='height: 55px;' alt='' />
		<p>Please wait while processing...</p>
		</div>
	</div>	
	
	
	<header>
		<div style="clearfix">
			<h1 class="pull-left" style="margin-top:-10px;">
				<a href="/">MMPI</a>
				<!--a href="/">MMPI</a-->
			</h1>
			<?= $this->load->view('snippets/menu', NULL, TRUE,'main');  ?>
			<?= $this->load->view('snippets/navigation', NULL, TRUE,'main');  ?>
		</div>
	</header>
	<div id='content'>
		<div class='content-area'>			
			<script type='text/javascript'>
				$(function() {
					$('.promo-links a').tooltip({'placement' : 'bottom'});
				});
			</script>
		</div>

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
		

			// check if  password has been changed (first login)
			$user_access_details = $this->user_model->get_user_by_id_number($this->employee->id_number);

			if ($user_access_details->is_password_changed > 0) {

		?>
		
		<div class='content-area member-area clearfix'>
			
			<?php
				$this->load->model('asset_model');
				// get all alert messages
				$where = "is_visible = 1 AND ((now() BETWEEN start_timestamp AND end_timestamp) OR (start_timestamp = '0000-00-00 00:00:00' AND end_timestamp = '0000-00-00 00:00:00'))";
				$active_alert_message_details = $this->asset_model->get_alert_message($where, NULL, 'insert_timestamp DESC');

				foreach($active_alert_message_details as $aamd) {
					echo "<div class='alert alert-danger'><strong>{$aamd->title}</strong><br/>{$aamd->content}</div>";
				}
			?>
			
			
			<ul id='user-nav-box' class="nav nav-pills">
				
				<li class='<?= isset($this->uri->uri_string) ? ($this->uri->uri_string == 'employee' ? 'active' : '') : '';  ?>'><a href="/employee" >Dashboard</a></li>				
				<li class='<?= isset($this->uri->uri_string) ? ($this->uri->segment(2) == 'profile' ? 'active' : '') : '';  ?> dropdown'>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Profile <i class="icon-play-down" style="padding-right: 3px;"></i></a>
					<ul class="dropdown-menu" role="menu">
						<li role="menuitem"><a href="/employee/profile">My Profile</a></li>	
						<li role="menuitem"><a href="/employee/profile/job_description">My JD</a></li>	
					</ul>
				</li>	
				<li class='<?= isset($this->uri->uri_string) ? ($this->uri->uri_string == 'employee/announcement' ? 'active' : '') : '';  ?>'><a href="/employee/announcement" >Announcements</a></li>	
				

				<li class='<?= isset($this->uri->uri_string) ? ($this->uri->segment(2) == 's4s' ? 'active' : '') : '';  ?> dropdown'>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">S4S <i class="icon-play-down" style="padding-right: 3px;"></i></a>
					<ul class="dropdown-menu" role="menu">
						<li role="menuitem"><a href="/employee/s4s/page/operations">Operations</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/human_relations">Human Resource</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/treasury">Treasury</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/accounting">Accounting</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/corporate_services">Corporate Services</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/spare_parts">Spare Parts</a></li>
						<li role="menuitem"><a href="/employee/s4s/page/information_technology">Information Technology</a></li>						
					</ul>
				</li>	

				<?php
				$cod_access_setting = $this->setting_model->get_setting_by_slug("code_of_discipline_access_position_ids");

				if ($cod_access_setting->value == "ALL") {
				?>
					<li class='<?= isset($this->uri->uri_string) ? ($this->uri->segment(2) == 'cod' ? 'active' : '') : '';  ?>'><a href="/employee/cod" >Code of Discipline</a></li>						
				<?php	
				} else { 

					// check if employee's position_id is in the list
					$position_id = explode("|", $cod_access_setting->value);

					foreach ($position_id as $pi) {
						if ($pi == $this->employee->position_id) {
				?>
							<li class='<?= isset($this->uri->uri_string) ? ($this->uri->segment(2) == 'cod' ? 'active' : '') : '';  ?>'><a href="/employee/cod" >Code of Discipline</a></li>								
				<?php
							break;							
						}
					}				
				}
				?>
				
				
			</ul>
			<hr class='user-nav-divider'/>
			
			<?php
			}
			?>

			<div class="clearfix">
				<?= $content ?>
			</div>				
		</div>			
	</div>
	<footer>
		<?= $this->load->view('snippets/footer', NULL, TRUE,'main');  ?>
		<!--div class="grid12">
			<div class="row">	
				<p class="span5">@2015 Mitsukoshi Motors Philippines Inc. All Rights Reserved.</p>
				<ul class="span7">
					<li><a href="<?=$this->config->item('base_url')?>/employee">Home</a></li>
					<li><a href="<?=$this->config->item('base_url')?>/pages/privacy_policy">Privacy Policy</a></li>
					<li><a href="<?=$this->config->item('base_url')?>/pages/terms_and_conditions">Terms and Conditions</a></li>
					<li><a  target="_blank" href="http://mitsukoshimotors.com/careers/">Careers</a></li>
                </ul>
			</div>
			<div class="row-fluid">					
				<p>Powered by<a href="http://mitsukoshimotors.com"> MMPI IT Department</a></p>
			</div>
		</div-->
	</footer>
	
</body>

</html>

