

<div class="content-area">
	
	<style>
    #slider-2{
        visibility: hidden;
    }
</style>
<?php
	$this->load->model('asset_model');
	$this->load->model('human_relations_model');
?>

<?php
	
	// get all alert messages
	$where = "is_visible = 1 AND employee_only = 0 AND ((now() BETWEEN start_timestamp AND end_timestamp) OR (start_timestamp = '0000-00-00 00:00:00' AND end_timestamp = '0000-00-00 00:00:00'))";
	$active_alert_message_details = $this->asset_model->get_alert_message($where, NULL, 'insert_timestamp DESC');

	foreach($active_alert_message_details as $aamd) {
		echo "<div class='alert alert-danger'><strong>{$aamd->title}</strong><br/>{$aamd->content}</div>";
	}
?>

<ul class="member-login-ads" style="list-style: none;">
	<li>		
		<div id='slider-2' class="slider-horizontal" style="height:500px">
			
			<?php								
				$login_ads = $this->asset_model->get_employee_login_ad("is_active = 1", "", "priority_id");
				
				if(!empty($login_ads))
				{
					foreach($login_ads as $g)
					{

						//$member = $this->members_model->get_member_by_id($g->member_id);
						if(!is_null($g->image_filename))
						{
							$image = $this->config->item('media_url') . '/employee_login_ads/' . $g->image_filename;
							//$image = check_image_path($image. 0, 1, 1);
							//$image = 'http://mmpi.local/assets/media/employee_login_ads/' . $g->image_filename;
							
						} else {
							$image = $this->config->item('media_url') . '/employee_login_ads/' . $g->image_filename;
							$image = check_image_path($image. 1, 1, 1);
							
						}
			?>
				<div class='member-image'>
					<div class='member-image' style="width:500px;height:500px">
						<a  href="<?= $image ?>" target="_blank"><img src="<?= $this->config->item('admin_base_url') . $image ?>" class="member-login-ads-image" style="width:100%;height:500px"></img><?= $this->config->item('admin_base_url') ?></a>
						<!--a href="<?= $image ?>" target="_blank"><?= $image ?></a-->
					</div>					
				</div>
					
			<?php
					}
					
				}
			?>
		</div>
		<div style="clear:both;"></div>
	</li>	
</ul>
<?php
	//random number generator
	$slider2_random = rand(2, 5);
	$slider_speed = abs($this->setting->employee_login_ad_speed);
	
?>
<input type="hidden" id="random-slider-2" value="<?= $slider_speed * 1000; ?>" />
</div>

<section id='admin_signin'>	

	<?php
	if ($this->setting->disable_employee_login == 0) {
	?>

		<div class=''>
			<div class='span5'>
				<div class='well' style="margin-top:75px;">
				<h4>Welcome to MMPI Employee's Portal</h4>
				<?php if (isset($invalid_login)) if ($invalid_login) :?>
					<div class="alert alert-error">
						<h4 class="alert-heading">Error!</h4>
					<?php if(isset($active_member) && !$active_member) : ?>
						Your Account is Inactive.
					<?php else : ?>
						Invalid Username or Password.
					<?php endif; ?>
					</div>
				<?php endif?>
				</div>


				
				<form action="/employee/signin/authenticate" method="post" class="form-inline">
					<fieldset>
						
						<div id="username_control" class="control-group <?= $this->form_validation->error_class('username') ?>">
							<label class="control-label" for="username">Username</label>
							<div class="controls">
								<input type="text" placeholder="" name="username" id="username" value="<?= set_value('username') ?>"> 
								<p id="username_help" class="help-block"><?= $this->form_validation->error('username'); ?></p>
							</div>
						</div>
						
						<div id="password_control" class="control-group <?= $this->form_validation->error_class('password') ?>">
							<label class="control-label" for="password">Password</label>
							<div class="controls">
								<input type="password" placeholder="" name="password" id="password" value="<?= set_value('password') ?>"> 
								<p id="username_help" class="help-block"><?= $this->form_validation->error('password') ?></p>
							</div>
						</div>
						<div >
							<button type="submit" class="btn btn-success">Log In</button>
							<span style='float:right;padding-top:6px;padding-right:160px;' ><a href='/employee/signin/forgot_password' >Forgot Password</a></span>
						</div>		
					</fieldset>
				</form>
			</div>	
		</div>	
	<?php 
	} else {
	?>
	
	<div class="alert alert-sucess"><p><strong>Advisory:</strong></p><p>Signin is currently disabled by Admin. Please contact the IT Department for queries.<br/>Thank you.</p></div>
	<?php 
	}
	?>

</section>
<div class="clearfix"></div>
<div class="login-ads pull-right">
</div>

<script>
	$(document).ready(function (){

	});
	
	$("#slider-2").FlowSlider({
		detectTouchDevice: false,
		infinite: true,
		marginStart: 0,
		marginEnd: 0,
		startPosition: 0,
		position: 0,
		controllers: ["Timer"],
		controllerOptions: [
			{
				el: $(document),
				eventStart: "ready",
				step: 500,
				time: $("#random-slider-2").val(),
				rewind: true,
			}
		]
	});

    var imgs = $("#slider-2 .member-login-ads-image");
    var count = imgs.length;
    if (count) {

        imgs.each(function(index, elem){

            $(elem).load(function(){
                count--;
                console.log("count:" +count);
                if (count==0) {
                    setTimeout(function(){$("#slider-2").css("visibility","visible")},700);
                }
            });
        });

    }setTimeout(function(){$("#slider-2").css("visibility","visible");},700);

    
</script>
<br/>