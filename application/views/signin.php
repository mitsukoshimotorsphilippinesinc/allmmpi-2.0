

<div class="content-area">
	
	<style>
    #slider-2{
        visibility: hidden;
    }
</style>
<?php
	$this->load->model('contents_model');
	$this->load->model('members_model');
?>

<ul class="member-login-ads" style="list-style: none;">
	<li>		
		<div id='slider-2' class="slider-horizontal" style="height:800px">
			
			<?php								
				$login_ads = $this->contents_model->get_members_login_ads("is_active = 1", "", "priority_id");
				
				if(!empty($login_ads))
				{
					foreach($login_ads as $g)
					{
						//$member = $this->members_model->get_member_by_id($g->member_id);
						if(!is_null($g->image_filename))
						{
							//$image = $this->config->item('media_url') . '/members_login/' . $g->image_filename;
							//$image = check_image_path($image. 0, 1, 1);
							$image = 'http://vital-c.net/assets/media/members_login/' . $g->image_filename;
							
						} else {
							$image = $this->config->item('media_url') . '/members_login/' . $g->image_filename;
							$image = check_image_path($image. 1, 1, 1);
							
						}
			?>
				<div class='member-image'>
					<div class='member-image' style="width:500px;height:800px">
						<a  href="<?= $image ?>" target="_blank"><img src="<?= $image ?>" class="member-login-ads-image" style="width:498px;max-height:800px"></img></a>
					</div>					
				</div>
					
			<?php
					}
					
				}
			?>
		</div>
	</li>	
</ul>
<?php
	//random number generator
	$slider2_random = rand(2, 5);
	$slider_speed = abs($this->settings->member_login_ads_speed);
	
?>
<input type="hidden" id="random-slider-2" value="<?= $slider_speed * 1000; ?>" />
</div>

<section id='admin_signin'>	

	<?php
		if ($this->settings->disable_members_login == 0) {
		?>

		<div class=''>
			<div class=''>
				<div class='well' style="margin-top:75px;">
				<h2>Members Login</h2>
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


				
				<form action="/members/signin/authenticate" method="post" class="form-inline">
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
							<span style='float:right;padding-top:6px;padding-right:40px;' ><a href='/members/signin/forgot_password' >Forgot Password</a></span>
						</div>		
					</fieldset>
				</form>
			</div>	
		</div>	
	<?php 
	} else {
	?>
	
	<div class="alert alert-sucess"><p><strong>Advisory:</strong></p><p>Member's Signin is currently disabled by Admin. Please contact the IT Department for queries.<br/>Thank you.</p></div>
	<?php 
	}
	?>

</section>

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