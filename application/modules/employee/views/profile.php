<?php

	$upload_url = $this->config->item("media_url") . "/employees";
	$_upload_url = urlencode($upload_url);

	// date
	$months = array(
		'01' => 'January',
		'02' => 'February',
		'03' => 'March',
		'04' => 'April',
		'05' => 'May',
		'06' => 'June',
		'07' => 'July',
		'08' => 'August',
		'09' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);


	$days = array();
	for ($i = 1; $i <= 31; $i++) {
		if ($i < 10) {
			$days["0".$i] = "0" . $i;
		} else {
			$days[$i] = $i;
		}
	}

	$years = array();
	$today = getdate();
	for ($i = $today['year']; $i >= 1900 ; $i--)
		$years[$i] = $i;
		
	
	$image_filename = "ni_male.png";
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

<div class="page-header">
  <center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #1828AA 90%) repeat scroll 0 0">My Profile</h2></center>
</div>
<div>
	<div class="tabbable">
		<ul class="nav nav-tabs">
            <li class="active" data="personal"><a href="#personal" data-toggle="tab">Personal Information</a></li>
			<li data="employment"><a href="#employment" data-toggle="tab">Employment Information</a></li>
			<li data="contact"><a href="#contact" data-toggle="tab">Contact Information</a></li>			
			<li data="change_password"><a href="#change_password" data-toggle="tab">Change Password</a></li>
        </ul>
		<div class="tab-content">
			<div class="tab-pane active" id="personal">
				<div class="span2">
					<img id="member_image" style="width:150px; height:150px;border:1px dashed gray;" alt="" src="<?= $this->config->item('admin_base_url') . '/assets/media/employees/'. $image_filename ?>">
					<center><button class="btn btn-primary btn-upload-photo hide" style="margin: 5px auto;">Upload</button></center>
				</div>
	  			<div class="span10" style='position: relative;'>
					<div style='position: absolute; top: 5px; right:10px;'>
						<small id='profile_processing' style='margin-right:10px; display:none;'><img src='/assets/img/loading2.gif' alt='' /></small>
						<button id='btn_edit_profile' class='btn btn-primary btn_profile_edit'>Edit</button>
						<button id='btn_save_profile' class='btn btn-warning btn_profile_save' style='display:none;'>Save</button>
						<button id='btn_cancel_profile' class='btn btn-danger btn_profile_save' style='display:none;'>Cancel</button>
					</div>
					
					<div class='row' >	
						<div class='span5'>
							
							<div class="control-group">
								<label class="control-label" for="fname">First Name</label>
								<div class="controls">
									<input class='input-xlarge' title='First Name' type='text' id='fname' name='fname' value='<?=$personal_information->first_name?>' data-orig-value='<?=$personal_information->first_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="mname">Middle Name</label>
								<div class="controls">
									<input class='input-xlarge' title='Middle Name' type='text' id='mname' name='mname' value='<?=$personal_information->middle_name?>' data-orig-value='<?=$personal_information->middle_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="lname">Last Name</label>
								<div class="controls">
									<input class='input-xlarge' title='Middle Name' type='text' id='lname' name='lname' value='<?=$personal_information->last_name?>' data-orig-value='<?=$personal_information->last_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="lname">Suffix Name</label>
								<div class="controls">
									<input class='input-xlarge' title='Suffix Name' type='text' id='sname' name='sname' value='<?=$personal_information->suffix_name?>' data-orig-value='<?=$personal_information->suffix_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label>Birthday</label>
								<div id="birth_date_container" class="controls form-inline wc-date">
									<?php
									$birth_year = date('Y',strtotime($personal_information->birthdate));
									$birth_month = date('m',strtotime($personal_information->birthdate));
									$birth_day = date('d',strtotime($personal_information->birthdate));
									?>
									<?= form_dropdown('birth_month', $months, $birth_month, 'id="birth_month" class="wc-date-month profile_info" disabled') ?>
									<?= form_dropdown('birth_day', $days, $birth_day, 'id="birth_day" class="wc-date-day profile_info" disabled') ?>
									<?= form_dropdown('birth_year', $years, $birth_year, 'id="birth_year" class="wc-date-year profile_info" disabled') ?>
									<input type="hidden" id="birthdate" name="birthdate" value="<?= set_value('birthdate'); ?>" readonl/>
								</div>
							</div>
						</div>
						
						
						<div class='span5'>
							
							<div class="control-group">
								<label class="control-label" for="gender">Gender</label>
								<div class="controls">
									<?= form_dropdown('gender', array("MALE" => "Male", "FEMALE" => "Female"), $personal_information->gender,'id="gender" class="input-small profile_info" data-orig-value="'.$personal_information->gender.'" disabled');?>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="marital_status">Marital Status</label>
								<div class="controls">
									<?php
									$marital_status = array("S" => "Single", "M" => "Married");
									echo form_dropdown('marital_status', $marital_status, $personal_information->marital_status,'id="marital_status" class="input-small profile_info" data-orig-value="'.$personal_information->marital_status.'" disabled');
									?>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="nationality">Nationality</label>
								<div class="controls">
									<input class='input-medium profile_info' title='Nationality' type='text' id='nationality' name='nationality' value='<?=trim($personal_information->nationality)?>'  data-orig-value='<?=trim($personal_information->nationality)?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="tin">T.I.N</label>
								<div class="controls">
									<input class='input-large profile_info' title='T.I.N.' type='text' id='tin' name='tin' value='<?=trim($personal_information->tin)?>' data-orig-value='<?=trim($personal_information->tin)?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
						</div>
					</div>

				</div>
			</div>

			<div class="tab-pane" id="employment">
				
				<div class="span10" style='position: relative;'>

					<div class='row' >	
						<div class='span5'>
							
							<div class="control-group">
								<label class="control-label" for="fname">ID Number</label>
								<div class="controls">
									<input class='input-xlarge' title='Id Number' type='text' id='id_number' name='id_number' value='<?=$employment_information->id_number?>' data-orig-value='<?=$employment_information->id_number?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="mname">Company Email Address</label>
								<div class="controls">
									<input class='input-xlarge' title='Company Email Address' type='text' id='company_email_address' name='company_email_address' value='<?=$employment_information->company_email_address?>' data-orig-value='<?=$employment_information->company_email_address?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="lname">Company Name</label>
								<?php
									$company_name = "";
									$company_details = $this->human_relations_model->get_company_by_id($employment_information->company_id);
									if (empty($company_details))
										$company_name = "N/A";
									else
										$company_name = $company_details->company_name;									
								?>
								<div class="controls">
									<input class='input-xlarge' title='Company Name' type='text' id='company_name' name='company_name' value='<?=$company_name?>' data-orig-value='<?=$company_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="lname">Department Name</label>
								<?php
									$department_name = "";
									$department_details = $this->human_relations_model->get_department_by_id($employment_information->department_id);
									if (empty($department_details))
										$department_name = "N/A";
									else
										$department_name = $department_details->department_name;									
								?>
								<div class="controls">
									<input class='input-xlarge' title='Department Name' type='text' id='department_name' name='department_name' value='<?=$department_name?>' data-orig-value='<?=$department_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="lname">Branch Name</label>
								<?php
									$branch_name = "";
									$branch_details = $this->human_relations_model->get_branch_by_id($employment_information->branch_id);
									if (empty($branch_details))
										$branch_name = "N/A";
									else
										$branch_name = $branch_details->branch_name;									
								?>
								<div class="controls">
									<input class='input-xlarge' title='Branch Name' type='text' id='branch_name' name='branch_name' value='<?=$branch_name?>' data-orig-value='<?=$branch_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>
							


							<div class="control-group">
								<label>Date Started</label>
								<div id="birth_date_container" class="controls form-inline wc-date">
									<?php
									$birth_year = date('Y',strtotime($personal_information->birthdate));
									$birth_month = date('m',strtotime($personal_information->birthdate));
									$birth_day = date('d',strtotime($personal_information->birthdate));
									?>
									<?= form_dropdown('birth_month', $months, $birth_month, 'id="birth_month" class="wc-date-month profile_info" disabled') ?>
									<?= form_dropdown('birth_day', $days, $birth_day, 'id="birth_day" class="wc-date-day profile_info" disabled') ?>
									<?= form_dropdown('birth_year', $years, $birth_year, 'id="birth_year" class="wc-date-year profile_info" disabled') ?>
									<input type="hidden" id="birthdate" name="birthdate" value="<?= set_value('birthdate'); ?>" readonl/>
								</div>
							</div>
						</div>
						
						
						<div class='span5'>
							
							<div class="control-group">
								<label class="control-label" for="fname">Job Grade Level</label>
								<?php
									$job_grade_level_name = "";
									$job_grade_level_details = $this->human_relations_model->get_job_grade_level_by_id($employment_information->job_grade_level_id);
									if (empty($job_grade_level_details))
										$job_grade_level_name = "N/A";
									else
										$job_grade_level_name = $job_grade_level_details->grade_level_name;									
								?>
								<div class="controls">
									<input class='input-xlarge' title='Job Grade Level' type='text' id='job_grade_level' name='job_grade_level' value='<?=$job_grade_level_name?>' data-orig-value='<?=$job_grade_level_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="fname">Position</label>
								<?php
									$position_name = "";
									$position_details = $this->human_relations_model->get_position_by_id($employment_information->position_id);
									if (empty($position_details))
										$position_name = "N/A";
									else
										$position_name = $position_details->position_name;									
								?>
								<div class="controls">
									<input class='input-xlarge' title='Position' type='text' id='position' name='position' value='<?=$position_name?>' data-orig-value='<?=$position_name?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="fname">Employment Status</label>
								<div class="controls">
									<input class='input-xlarge' title='Employment Status' type='text' id='employment_status' name='employment_status' value='<?=$employment_information->employment_status_id?>' data-orig-value='<?=$employment_information->employment_status_id?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="fname">Paycode</label>
								<div class="controls">
									<input class='input-xlarge' title='Paycode' type='text' id='paycode' name='paycode' value='<?=$employment_information->paycode?>' data-orig-value='<?=$employment_information->paycode?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="fname">ATM Number</label>
								<div class="controls">
									<input class='input-xlarge' title='ATM' type='text' id='atm_number' name='atm_number' value='<?=$employment_information->atm?>' data-orig-value='<?=$employment_information->atm?>' readonly/>
									<span class="help-block"></span>
								</div>
							</div>

						</div>
					</div>

				</div>




			</div>
			
			<div class="tab-pane" id="contact">
				<div class='span12'>
					<div class='row'>
						<div class='span6'>
							<div class='control-group'>
								<label class="control-label" for="email">Personal Email Address</label>
								<div class="controls">
									
									<div class="input-append">
										<input type='email' id='email' name='email' class='span4' value='<?= $personal_information->personal_email_address ?>' readonly/>
										<?php if ($personal_information->is_personal_email_verified == 0) : ?>
										<button id="edit_email" class="btn btn-primary" data-type='email'  type="button">Edit</button>
										<?php if (!empty($personal_information->personal_email_address)) : ?>
										<button class="btn btn-warning verify_email_mobile_number" data-type='email' type="button">Verify</button>
										<?php
												endif;
											else :
										?>
										<span class="add-on"><i class='icon-ok'></i> Verified</span>
										<?php
											endif;
										?>
									</div>
	
								</div>
							</div>
						</div>
						<div class='span6'>
							<div class='control-group'>	
								<label class="control-label" for="mobile_number">Mobile Number</label>
								<div class='controls'>
									
									<div class="input-append">
										<input type='text' id='mobile_number' name='mobile_number' class='span4' value='<?=$personal_information->mobile_number?>' readonly/>
										<?php if ($personal_information->is_mobile_number_verified == 0) : ?>
										<button id="edit_mobile" class="btn btn-primary" data-type='mobile_number' type="button">Edit</button>
										<?php if (!empty($personal_information->mobile_number)) : ?>
										<button class="btn btn-warning verify_email_mobile_number" data-type='mobile_number' type="button">Verify</button>
										<?php
												endif;
											else :
										?>
										<span class="add-on"><i class='icon-ok'></i> Verified</span>
										<?php
											endif;
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='well well-small clearfix' style='margin-bottom:10px; margin-top:10px;'>
						<h4 class='pull-left' style='margin:0;'>Address <small id='address_processing' style='display:none;'><img src='/assets/img/loading2.gif' alt='' /></small></h4>
						<div class='pull-right'>
							<button id='btn_edit_address' class='btn btn-small btn-primary btn_address_edit'>Edit</button>
							<button id='btn_save_address' class='btn btn-small btn-warning btn_address_save' style='display:none;'>Save</button>
							<button id='btn_cancel_address' class='btn btn-small btn-danger btn_address_save' style='display:none;'>Cancel</button>
						</div>
					</div>
					<div class='row'>

							<div class="control-group span8">
								<label class="control-label" for="home_address_street">Street</label>
								<div class="controls">
									<input class='input-block-level profile_address'  type='text' id='home_address_street' name='home_address_street' value='<?= $personal_information->address_street?>' data-orig-value='<?= $member->home_address_street?>' readonly/>
								</div>
							</div>

							<div class="control-group span4">
								<label class="control-label" for="home_address_city">City/Municipality</label>
								<div class="controls">
									<input class="input-block-level profile_address" type='text' id='home_address_city' name='home_address_city' value='<?= $personal_information->address_city?>' data-orig-value='<?= $personal_information->address_city?>' readonly/>
								</div>
							</div>

							<div class="control-group span4">
								<label class="control-label" for="home_address_province">State/Province</label>
								<div class="controls">
									<input class="input-block-level profile_address" type='text' id='home_address_province' name='home_address_province' value='<?= $personal_information->address_province?>' data-orig-value='<?= $personal_information->address_province?>' readonly/>
								</div>
							</div>

							<div class="control-group span4">
								<label class="control-label" for="home_address_country">Country</label>
								<div class="controls">
									<input class="input-block-level profile_address" type='text' id='home_address_country' name='home_address_country'  value='<?= $personal_information->address_country?>' data-orig-value='<?= $personal_information->address_country?>' readonly/>
								</div>
							</div>

							<div class="control-group span4">
								<label class="control-label" for="home_zip_postalcode">Zip/Postal Code</label>
								<div class="controls">
									<input class="input-block-level profile_address" type='text' id='home_zip_postalcode' name='home_zip_postalcode' value='<?=$personal_information->address_zip_code?>' data-orig-value='<?=$personal_information->address_zip_code?>'  readonly/>
								</div>
							</div>

					</div>
				</div>

			</div>
			
			<div id="change_password" class="tab-pane">
				<div class='span12'>
					<div class='control-group'>
						<label>Current Password <span class='required'>*&nbsp;</span></label>
						<div class='controls'>
                        	<input class="profile-label-container" title='Password' class='old_password' id='old_password' type='password' name='old_password' maxlength='16'/>
							<span id="old_password_error" class="old_password_error help-block"></span>
						</div>
                    </div>
					<div class='control-group'>
						<label>New Password <span class='required'>*&nbsp;</span></label>
						<div class='controls'>
                        	<input class="profile-label-container" title='New Password' class='new_password' id='new_password' type='password' name='new_password' maxlength='16'/>
							<span id="new_password_error" class="new_password_error help-block"></span>
						</div>
                    </div>
					<div class='control-group'>
						<label>Re-type New Password <span class='required'>*&nbsp;</span></label>
						<div class='controls'>
                        	<input class="profile-label-container" title='Retype Password' class='new_password_retype' id='new_password_retype' type='password' name='new_password_retype' maxlength='16'/>
							<span id="new_password_retype_error" class="new_password_retype_error help-block"></span>
						</div>
                    </div>
					
					<div class='control-group'>
						<button id="submit_password" class='btn btn-warning'>Submit</button>
					</div>

				</div>
				
			</div>
		</div>
	</div>

</div>
<script type="text/javascript">

$(document).on('ready', function(){
	
})

$(function() {

	//var _upload_photo_filename = "";
	
	//var _is_email_verified = <?= $member->is_email_verified == 1 ? 1 : 0 ?>;
	//var _is_mobile_number_verified = <?= $member->is_email_verified == 1 ? 1 : 0 ?>;
	//var _member_id = <?= $member->member_id?>;
	
	// ------- edit / save / handlers for header buttons
	
	// Profile handlers
	$('.btn-upload-photo').click(function(e){
		e.preventDefault();
		var member_id = _member_id;
		var photo_upload_form_modal = beyond.modal.create({
			title: 'Profile: Upload Photo',
			html: '<div class="photo-upload-form" style="margin-bottom: -30px;"></div>',
			width: 450,
			disableClose: true,
			buttons: {
				'Upload': function(){
					_upload_photo_filename = $('.uploadBox_fu .fileItem i').html();
					$('.uploadBox_fu input[value="Upload"]').click();
				},
				'Cancel': function(){
					photo_upload_form_modal.hide();
				}
			}
		});

		photo_upload_form_modal.show();
		$('.photo-upload-form').Uploadrr({
			singleUpload : true,
			progressGIF : '<?= image_path('pr.gif') ?>',
			allowedExtensions : ['.gif','.jpg', '.png'],
			target : '<?php echo $this->config->item('base_url'); ?>/members/profile/upload_profile_picture?filename=member_'+member_id+'&location=<?=$_upload_url?>&width=200&height=200&ts=<?=time()?>',
			onUploadFinished: function(data) {
				/*
				data.fileName
				data.fileSize
				data.fileType
				*/
			},
			onComplete: function(data) {
				var _upload_file_ext = _upload_photo_filename.split('.')[1];
				$('#member_image').attr('src','<?=$upload_url?>/member_'+member_id+'.'+_upload_file_ext+'?v=' + Math.floor(Math.random() * 999999) + '');
				beyond.request({
					url: 'profile/update_image',
					data: {
						"filename": 'member_'+member_id+'.'+_upload_file_ext,
						"member_id": member_id
					},
					on_success: function(data){
					}
				});
				photo_upload_form_modal.hide();	
			}
		});

		$('.uploadBox_fu .file').css({'opacity': 0, 'width':'300px'});
		$('.uploadBox_fu .fake').css({'margin-bottom': '-40px'});
		$('.uploadBox_fu input[value="Upload"]').css({'opacity':0, 'position':'absolute', 'top':'-99999px'});
		$('.uploadBox_fu .fake input').each(function(){ $(this).removeAttr('style'); });
		$('.uploadBox_fu .fake').addClass('input-append');
		$('.uploadBox_fu input[value="Browse"]').addClass('btn btn-primary');
	});

	$('#btn_edit_profile').click(function(e) {
		e.preventDefault();
		$(this).hide();
		$('.btn_profile_save').show();
		$('.btn-upload-photo').show();
		$('.profile_info').prop('disabled', false).prop('readonly', false);
	});
	
	$('#btn_save_profile').click(function(e) {
		e.preventDefault();
		
		b.disableButtons('.btn_profile_save');
		$('#profile_processing').show();
		
		var gender = $("#gender").val();
		var marital_status = $("#marital_status").val();
		var nationality = $("#nationality").val();
		var tin = $("#tin").val();
		var member_id = _member_id;
		var birth_month = $("#birth_month").val();
		var birth_year = $("#birth_year").val();
		var birth_day = $("#birth_day").val();
		
		var confirm_modal = b.modal.new({
			title: "Confirm Changes",
			html: "Are you sure you want to save these changes?",
			disableClose: true,
			width: 450,
			buttons: {
				'Confirm': function() {
					confirm_modal.hide();
					b.request({
						url: 'profile/edit_profile_information',
						data: {
							"gender": gender,
							'marital_status': marital_status,
							'nationality': nationality,
							'tin': tin,
							'member_id': member_id,
							'birth_month': birth_month,
							'birth_year': birth_year,
							'birth_day': birth_day,
							'type': 'personal'
						},
						on_success: function(data){
							if(data.status == '1') {
								var success_modal = b.modal.new({
									title: 'Edit Profile Success',
									html: 'You have successfully changed your personal information.',
									disableClose: true,
									width: 400,
									buttons: {
										'Close': function(){
											success_modal.hide();
											location.reload();
										}
									}
								});
								success_modal.show();
							}
						}
					})
				},
				'Cancel': function() {
					confirm_modal.hide();
					b.enableButtons('.btn_profile_save');
					$('#profile_processing').hide();
				}
			}
		});
		confirm_modal.show();
		
	});
	
	$('#btn_cancel_profile').click(function(e) {
		e.preventDefault();

		$('.btn-upload-photo').hide();
		$('.btn_profile_save').hide();
		$('.btn_profile_edit').show();
		
		// revert back to original value
		$('.profile_info').each(function() {
			$(this).val($(this).data('orig-value'));
		});
		
		$('.profile_info').prop('disabled', true).prop('readonly', true);
		
	});
	
	// Address handlers
	$('#btn_edit_address').click(function(e) {
		e.preventDefault();
		$(this).hide();
		$('.btn_address_save').show();
		$('.profile_address').prop('disabled', false).prop('readonly', false);
	});
	
	$('#btn_save_address').click(function(e) {
		e.preventDefault();
		
		b.disableButtons('.btn_address_save');
		$('#address_processing').show();
		
		var home_address_street = $("#home_address_street").val();
		var home_address_city = $("#home_address_city").val();
		var home_address_province = $("#home_address_province").val();
		var home_address_country = $("#home_address_country").val();
		var home_zip_postalcode = $("#home_zip_postalcode").val();
		var member_id = _member_id;
		
		var confirm_modal = b.modal.new({
			title: "Confirm Changes",
			html: "Are you sure you want to save these changes?",
			disableClose: true,
			width: 450,
			buttons: {
				'Confirm': function() {
					confirm_modal.hide();
					b.request({
						url: 'profile/edit_profile_information',
						data: {
							"home_address_street": home_address_street,
							'home_address_city': home_address_city,
							'home_address_province': home_address_province,
							'home_address_country': home_address_country,
							'home_zip_postalcode': home_zip_postalcode,
							'member_id': member_id,
							'type': 'address'
						},
						on_success: function(data){
							if(data.status == '1') {
								var success_modal = b.modal.new({
									title: 'Edit Profile Success',
									html: 'You have successfully changed your contact information.',
									disableClose: true,
									width: 400,
									buttons: {
										'Close': function(){
											success_modal.hide();
											location.reload();
										}
									}
								});
								success_modal.show();
							}
						}
					})
				},
				'Cancel': function() {
					confirm_modal.hide();
					b.enableButtons('.btn_profile_save');
					$('#profile_processing').hide();
				}
			}
		});
		confirm_modal.show();
		
	});
	
	$('#btn_cancel_address').click(function(e) {
		e.preventDefault();

		$('.btn_address_save').hide();
		$('.btn_address_edit').show();
		
		// revert back to original value
		$('.profile_address').each(function() {
			$(this).val($(this).data('orig-value'));
		});
		
		$('.profile_address').prop('disabled', true).prop('readonly', true);
		
	});
	
	// Others handler
	$('#btn_edit_others').click(function(e) {
		e.preventDefault();
		$(this).hide();
		$('.btn_others_save').show();
		$('.profile_others').prop('disabled', false).prop('readonly', false);
	});
	
	$('#btn_save_others').click(function(e) {
		e.preventDefault();
		
		b.disableButtons('.btn_others_save');
		$('#others_processing').show();
		
		var beneficiary1 = $("#beneficiary1").val();
		var beneficiary2 = $("#beneficiary2").val();
		var group_name = $("#group_name").val();
		var member_id = _member_id;
		
		var confirm_modal = b.modal.new({
			title: "Confirm Changes",
			html: "Are you sure you want to save these changes?",
			disableClose: true,
			width: 450,
			buttons: {
				'Confirm': function() {
					confirm_modal.hide();
					b.request({
						url: 'profile/edit_profile_information',
						data: {
							"beneficiary1": beneficiary1,
							'beneficiary2': beneficiary2,
							'group_name': group_name,
							'member_id': member_id,
							'type': 'account'
						},
						on_success: function(data){
							if(data.status == '1') {
								var success_modal = b.modal.new({
									title: 'Edit Profile Success',
									html: 'You have successfully changed your account information.',
									disableClose: true,
									width: 400,
									buttons: {
										'Close': function(){
											success_modal.hide();
											location.reload();
										}
									}
								});
								success_modal.show();
							}
						}
					})
				},
				'Cancel': function() {
					confirm_modal.hide();
					//b.enableButtons('.btn_profile_save');
					b.enableButtons('.btn_others_save');
					
					$('#profile_processing').hide();
					$('#others_processing').hide();
				}
			}
		});
		confirm_modal.show();
		
	});
	
	$('#btn_cancel_others').click(function(e) {
		e.preventDefault();
		
		$('.btn_others_save').hide();
		$('.btn_others_edit').show();
		
		// revert back to original value
		$('.profile_others').each(function() {
			$(this).val($(this).data('orig-value'));
		});
		
		$('.profile_others').prop('disabled', true).prop('readonly', true);
		
		var member_group_name = '<?= $member->group_name ?>';
		$("#group_name option[value='"+member_group_name+"']").attr("selected",true) ;
		
	});
	

	$('.verify_email_mobile_number').live("click",function() {		
		var _code_type = $(this).data("type");

		b.request({
			url: "/members/verify_form",
			data: {
				"_code_type": _code_type
			},
			on_success: function(data){
				if(data.status == "1") {
					
					var verifyModal = b.modal.new({
						title: "Verify " + data.data.code_type_title +" :: Verification Code",
						html: data.data.html,
						disableClose: true,
						width: 450,
						buttons: {
							/*"Resend Verification": function(){
								verifyModal.hide();
                                resendVerification(_member_id, _code_type);
							},
							"Confirm": function(){
								//alert($("#code").val());
								
								if ($.trim($("#code").val()) == "") {								
									$("#code").parent().addClass("error");
									$("#code").parent().find(".help-inline").remove();			
									$("#code").parent().append("<span class='help-inline'>Please enter code here</span>");
								} else { 
								
									proceedCodeVerification(_member_id, _code_type , $("#code").val());
									verifyModal.hide();
								}						
							},*/
							"Close": function(){
								verifyModal.hide();								
							}
						}
					});
					
					verifyModal.show();
				}
			}
		});
	
		return false;
		
	});
	
	$("#edit_rf_id").click(function(e){
		e.preventDefault();
		
		var edit_modal = b.modal.new({
			title: "Edit RFID",
			html: "\
					<div style='margin-bottom: 10px;'>\n\
						<strong>Disclaimer: </strong> You can only edit your RFID once. Any requests to change your RFID must be submitted to the <strong>Vital C IT Department</strong>.\n\
					</div>\n\
					<div>\n\
						<strong>RFID</strong>:<br/><input title='RF ID' id='member_rf_id' type='text' name='rf_id' value='' style='width:50%;' />\n\
						<div id='rfid_error' class='control-group error' style='display:none;'><span class='help-inline'>Please enter your RFID</span></div>\n\
					</div>",
			width: 400,
			disableClose: true,
			buttons: {
				"Continue": function(){
					var rf_id = $("#member_rf_id").val();
					
					if(_.isEmpty(rf_id))
					{
						$("#rfid_error").css("display","");
						return;
					}
					
					// check first if rf_id is already taken					
					b.request({
						url: "/members/profile/check_rfid",
						data: {
							"rf_id": rf_id,
							"member_id": _member_id
						},
						on_success: function(data){
							
							edit_modal.hide();
							
							if (data.status == "same")
							{
								$("#rfid").val(rf_id);
								
								var errorVerificationModal = b.modal.new({
									title: "Edit RFID :: Notification",
									html: "<p>You have entered your current RFID in our system.</p>",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							}
							else if (data.status == "error")
							{
								var errorVerificationModal = b.modal.new({
									html: "<p>"+data.msg+"</p>",
									title: "Edit RFID :: Notification",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							} else {
							
								var confirm_modal = b.modal.new({
									title: "Edit Confirmation",
									html: "<p>You're about to update your RFID to <strong>"+rf_id+"</strong></p><p>Any requests to change your RFID must be submitted to the Vital C IT Department.</p><p>Would you like to proceed?</p>",
									width: 400,
									disableClose: true,
									buttons: {
										"Yes": function(){
											edit_modal.hide();
											confirm_modal.hide();
											b.request({
												url: "/members/profile/edit_rfid",
												data: {
													"rfid": rf_id,
													"member_id": _member_id
												},
												on_success: function(data){
													if(data.status == "ok")
													{
														$("#rfid").val(rf_id);
														
														var success_modal = b.modal.new({
															title: "Edit Successful",
															html: "<p>You have successfully updated your RFID. Your next step is to verify your RFID.</p>",
															disableClose: true,
															width: 300,
															buttons: {
																"Close": function(){
																	success_modal.hide();
																	redirect("/members/profile");
																}
															}
														});
														
														success_modal.show();
													}
													else
													{
														var error_modal = b.modal.new({
															title: "Error Notification",
															html: "<p>"+data.msg+"</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	error_modal.hide();
																}
															}
														});

														error_modal.show();
													}
												}
											});
										},
										"No": function(){
											confirm_modal.hide();
										}
									}
								});
												
								confirm_modal.show();
							
							}
						}
					});
					
					
					
					$("#rfid_error").css("display","none");
					
					//if(_.isEmpty(rf_id))
					//{
					//	$("#rfid_error").css("display","");
					//}
					//else
					//{
					//	confirm_modal.show();
					//}					
				},
				"Cancel": function(){
					edit_modal.hide();
				}
			}
		});
		
		edit_modal.show();
	});
	
	$(document).on("click","#is_paycard_corpo",function(){
			
		if($(this).is(":checked"))
		{
			$("#member_paycard").val("");
			$("#member_paycard").attr("readonly","readonly");
		}
		else
		{
			$("#member_paycard").removeAttr("readonly");
		}
		
	});
	
	$("#edit_paycard").click(function(e){
		e.preventDefault();
		var is_paycard_corpo = '<?= $this->member->is_paycard_corpo; ?>';
		var checked = "";
		var readonly = "";
		
		if(is_paycard_corpo == 1)
		{
			checked = "checked";
			readonly = "readonly";
		}
		
		var edit_modal = b.modal.new({
			title: "Edit Metrobank Paycard Number",
			html: "\
					<div style='margin-bottom: 10px;'>\n\
						<strong>Disclaimer: </strong> You can only edit your Metrobank Paycard Number once. Any requests to change your Metrobank Paycard Number must be submitted to the <strong>Vital C IT Department</strong>.\n\
					</div>\n\
					<div style='margin-bottom: 10px;'>\n\
						<label class='checkbox'><input id='is_paycard_corpo' type='checkbox' "+checked+">Set as <strong>CORPO</strong> account</label>\n\
					</div>\n\
					<div>\n\
						<strong>Metrobank Paycard Number</strong>:<br/><input title='Metrobank Paycard Number' id='member_paycard' class='numeric-entry' type='text' name='paycard' value='' style='width:50%;' maxlength='19' "+readonly+" />\n\
						<div><small>* Your paycard number must be 19 characters long and must not contain any spaces or hyphens.</small></div>\n\
						<div id='paycard_error' class='control-group error' style='display:none;'><span class='help-inline'></span></div>\n\
					</div>",
			width: 450,
			disableClose: true,
			buttons: {
				"Continue": function(){
					var paycard = $("#member_paycard").val();
					var pattern=/^$|^[0-9]{19}$/;
					var checked_corpo = $("#is_paycard_corpo").is(":checked") ? 1 : 0;

					$("#paycard_error").css("display","none");
					$("#paycard_error>span").text("");
					if(_.isEmpty($.trim(paycard)) && checked_corpo == 0)
					{
						$("#paycard_error").css("display","");
						$("#paycard_error>span").text("Please enter your Metrobank Paycard Number");
						return;
					}

					if(!pattern.test(paycard))
					{
						$("#paycard_error").css("display","");
						if(paycard.length < 19) $("#paycard_error>span").append("Your paycard contains less than 19 characters.<br>");
						if(/ /g.test(paycard)) $("#paycard_error>span").append("Your paycard contains spaces.<br>");
						if(/-/g.test(paycard)) $("#paycard_error>span").append("Your paycard contains hyphens.");
						return;
					}
					
					// check first if email is already taken					
					b.request({
						url: "/members/profile/check_paycard",
						data: {
							"paycard": paycard,
							"member_id": _member_id,
							"is_paycard_corpo": checked_corpo
						},
						on_success: function(data){
							
							edit_modal.hide();
							
							if (data.status == "same")
							{
								$("#paycard").val(paycard);
								
								var errorVerificationModal = b.modal.new({
									title: "Edit Paycard Number :: Notification",
									html: "<p>You have entered your current Paycard Number in our system.</p>",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							}
							else if (data.status == "error")
							{
								var errorVerificationModal = b.modal.new({
									html: "<p>"+data.msg+"</p>",
									title: "Edit Paycard Number :: Notification",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							} else {
							
								var confirm_modal = b.modal.new({
									title: "Edit Confirmation",
									html: "<p>You're about to update your Metrobank Paycard Number to <strong>"+paycard+"</strong></p><p>Any requests to change your Metrobank Paycard Number must be submitted to the Vital C IT Department.</p><p>Would you like to proceed?</p>",
									width: 450,
									disableClose: true,
									buttons: {
										"Yes": function(){
											edit_modal.hide();
											confirm_modal.hide();
											b.request({
												url: "/members/profile/edit_paycard",
												data: {
													"paycard": paycard,
													"member_id": _member_id,
													"is_paycard_corpo": checked_corpo
												},
												on_success: function(data){
													if(data.status == "ok")
													{
														$("#paycard").val(paycard);
														
														var success_modal = b.modal.new({
															title: "Edit Successful",
															html: "<p>You have successfully updated your Metrobank Paycard Number. Your next step is to verify your Metrobank Paycard Number.</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	success_modal.hide();
																	redirect("/members/profile");
																}
															}
														});
														
														success_modal.show();
													}
													else
													{
														var error_modal = b.modal.new({
															title: "Error Notification",
															html: "<p>"+data.msg+"</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	error_modal.hide();
																}
															}
														});
														
														error_modal.show();
													}
												}
											});
										},
										"No": function(){
											confirm_modal.hide();
										}
									}
								});
												
								confirm_modal.show();
							
							}
						}
					});
					
					
					$("#paycard_error").css("display","none");
					
					//if(_.isEmpty(paycard))
					//{
					//	$("#paycard_error").css("display","");
					//}
					//else
					//{
					//	confirm_modal.show();
					//}
				},
				"Cancel": function(){
					edit_modal.hide();
				}
			}
		});
		
		edit_modal.show();
	});
	
	$("#edit_email").click(function(e){
		e.preventDefault();
		
		var edit_modal = b.modal.new({
			title: "Edit Email",
			html: "\
					<div style='margin-bottom: 10px;'>\n\
						<strong>Disclaimer: </strong> You can only edit your Email once. Any requests to change your Email must be submitted to the <strong>Vital C IT Department</strong>.\n\
					</div>\n\
					<div>\n\
						<strong>Email</strong>:<br/><input title='Email' id='member_email' type='text' name='email' value='' style='width:50%;' />\n\
						<div id='email_error' class='control-group error' style='display:none;'><span class='help-inline'>Please enter your Email</span></div>\n\
					</div>",
			width: 450,
			disableClose: true,
			buttons: {
				"Continue": function(){
					var email = $("#member_email").val();
					
					if(_.isEmpty(email))
					{
						$("#email_error").css("display","");
						return;
					}
					
					// check first if email is already taken					
					b.request({
						url: "/members/profile/check_email",
						data: {
							"email": email,
							"member_id": _member_id
						},
						on_success: function(data){
							
							edit_modal.hide();
							
							if (data.status == "same")
							{
								$("#email").val(email);
								
								var errorVerificationModal = b.modal.new({
									title: "Edit Email :: Notification",
									html: "<p>You have entered your current Email in our system.</p>",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							}
							else if (data.status == "not ok")
							{
								var errorVerificationModal = b.modal.new({
									html: data.msg,
									title: "Edit Email :: Notification",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							} else {
							
								var confirm_modal = b.modal.new({
									title: "Edit Confirmation",
									html: "<p>You're about to update your email to <strong>"+email+"</strong></p><p>Any requests to change your Email must be submitted to the Vital C IT Department.</p><p>Would you like to proceed?</p>",
									width: 450,
									disableClose: true,
									buttons: {
										"Yes": function(){
											edit_modal.hide();
											confirm_modal.hide();
											b.request({
												url: "/members/profile/edit_email",
												data: {
													"email": email,
													"member_id": _member_id
												},
												on_success: function(data){
													if(data.status == "ok")
													{
														$("#email").val(email);
														
														var success_modal = b.modal.new({
															title: "Edit Successful",
															html: "<p>You have successfully updated your Email. Your next step is to verify your Email.</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	success_modal.hide();
																	redirect("/members/profile");
																}
															}
														});
														
														success_modal.show();
													}
													else
													{
														var error_modal = b.modal.new({
															title: "Error Notification",
															html: "<p>"+data.msg+"</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	error_modal.hide();
																}
															}
														});
														
														error_modal.show();
													}
												}
											});
										},
										"No": function(){
											confirm_modal.hide();
										}
									}
								});
								
								confirm_modal.show();
							
							}
						}
					});
					
					
					$("#email_error").css("display","none");
					
					//if(_.isEmpty(email))
					//{
					//	$("#email_error").css("display","");
					//}
					//else
					//{
					//	confirm_modal.show();
					//}
				},
				"Cancel": function(){
					edit_modal.hide();
				}
			}
		});
		
		edit_modal.show();
	});
	
	$("#edit_mobile").click(function(e){
		e.preventDefault();
		
		var edit_modal = b.modal.new({
			title: "Edit Mobile Number",
			html: "\
					<div style='margin-bottom: 10px;'>\n\
						<strong>Disclaimer: </strong> You can only edit your Mobile number once. Any requests to change your Mobile number must be submitted to the <strong>Vital C IT Department</strong>.\n\
					</div>\n\
					<div>\n\
						<strong>Mobile Number</strong>:<br/><input title='Mobile number' id='member_mobile' type='text' name='mobile' value='' style='width:50%;' maxlength='13'/>\n\
						<div id='mobile_error' class='control-group error' style='display:none;'><span class='help-inline'>Please enter your Mobile number</span></div>\n\
					</div>",
			width: 450,
			disableClose: true,
			buttons: {
				"Continue": function(){
					var mobile = $("#member_mobile").val();
					
					if(_.isEmpty(mobile))
					{
						$("#mobile_error").css("display","");
						return;
					}
					
					// check first if mobile is already taken					
					b.request({
						url: "/members/profile/check_mobile",
						data: {
							"mobile": mobile,
							"member_id": _member_id
						},
						on_success: function(data){
							
							edit_modal.hide();
							
							if (data.status == "same")
							{
								$("#mobile_number").val(mobile);
								
								var errorVerificationModal = b.modal.new({
									title: "Edit Mobile Number :: Notification",
									html: "<p>You have entered your current Mobile Number in our system.</p>",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							}
							else if (data.status == "not ok")
							{
								var errorVerificationModal = b.modal.new({
									title: "Edit Mobile Number :: Notification",
									html: "<p>Mobile Number is already taken. Please enter another. Thank you.</p>",
									disableClose: true,
									width: 350,
									buttons: {
										"Close": function(){
											errorVerificationModal.hide();													
										}
									}
								});
								
								errorVerificationModal.show();
							} else {
							
								var confirm_modal = b.modal.new({
									title: "Edit Confirmation",
									html: "<p>You're about to update your Mobile number to <strong>"+mobile+"</strong></p><p>Any requests to change your Mobile number must be submitted to the Vital C IT Department.</p><p>Would you like to proceed?</p>",
									width: 450,
									disableClose: true,
									buttons: {
										"Yes": function(){
											edit_modal.hide();
											confirm_modal.hide();
											b.request({
												url: "/members/profile/edit_mobile",
												data: {
													"mobile": mobile,
													"member_id": _member_id
												},
												on_success: function(data){
													if(data.status == "ok")
													{
														$("#mobile_number").val(mobile);
														
														var success_modal = b.modal.new({
															title: "Edit Successful",
															html: "<p>You have successfully updated your Mobile number. Your next step is to verify your Mobile number.</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	success_modal.hide();
																	redirect("/members/profile");
																}
															}
														});
														
														success_modal.show();
													}
													else
													{
														var error_modal = b.modal.new({
															title: "Error Notification",
															html: "<p>"+data.msg+"</p>",
															disableClose: true,
															width: 350,
															buttons: {
																"Close": function(){
																	error_modal.hide();
																}
															}
														});
														
														error_modal.show();
													}
												}
											});
										},
										"No": function(){
											confirm_modal.hide();
										}
									}
								});
								confirm_modal.show();
								
							}
						}
					});
					
					
					$("#mobile_error").css("display","none");
					
					//if(_.isEmpty(mobile))
					//{
					//	$("#mobile_error").css("display","");
					//}
					//else
					//{
					//	confirm_modal.show();
					//}
				},
				"Cancel": function(){
					edit_modal.hide();
				}
			}
		});
		
		edit_modal.show();
		$("#member_mobile").addClass("numeric-entry");
	});
	
    var resendVerification = function(member_id, code_type) {

        b.request({
            url: "/members/resend_email_verification",
            data: {
                "_member_id": member_id,
                "_code_type": code_type
            },
            on_success: function(data){
                if(data.status == "1") {

                    var resendModal = b.modal.new({
                        title: "Resend " + data.data.code_type_title +" Verification Code :: Successful",
                        html: data.data.html,
                        disableClose: true,
                        width: 500,
                        buttons: {
                            "Ok": function(){
                                resendModal.hide();
                            }
                        }
                    });
                    resendModal.show();

                } else {
                    var resendErrorModal = b.modal.new({
                        title: "Resend " + data.data.code_type_title +" Verification Code :: Error",
                        html: data.data.html,
                        disableClose: true,
                        width: 500,
                        buttons: {
                            "Ok": function(){
                                resendErrorModal.hide();                            }
                        }
                    });
                    resendErrorModal.show();
                }
            }
        });

        return false;


    }
	
	var proceedCodeVerification = function(member_id, code_type, code) {

		b.request({
			url: "/members/verify_proceed",
			data: {
				"_member_id": member_id,
				"_code_type": code_type,
				"_code": code,
				"_number": $("#"+code_type).val()
			},
			on_success: function(data){
				if(data.status == "1") {
					
					var proceedModal = b.modal.new({
						title: "Verify " + data.data.code_type_title +" :: Successful",
						html: data.data.html,
						disableClose: true,
						width: 500,
						buttons: {
							"Ok": function(){
								proceedModal.hide();
                                redirect('/members/profile');
							}
						}
					});
                    proceedModal.show();
					
				} else {
					var proceedErrorModal = b.modal.new({
						title: "Verify " + data.data.code_type_title +" :: Error",
						html: data.data.html,
						disableClose: true,
						width: 500,
						buttons: {							
							"Ok": function(){
                                proceedErrorModal.hide();
							}
						}
					});
                    proceedErrorModal.show();
				}
			}
		});
	
		return false;
	
	
	}
	
	$("body").on("change", "#auto_payout", function() {
		var me = this;
		var is_auto_payout = $(this).val();
		var orig_val = $(this).data("orig");
		if (is_auto_payout == 1)
		{
			var html = " enable transfer commissions to your paycard?";
			var action = "enabled";
		}
		else
		{
			var html = " disable transfer commissions to your paycard?";
			var action = "disabled";
		}
		var payout_modal = b.modal.new({
			title: "Confirm Change",
			html: "Are you sure you want to " + html,
			disableClose: true,
			width: 400, 
			buttons: {
				"Confirm": function(){
					payout_modal.hide();
					updatePayout(is_auto_payout, action);						
				},
				"Cancel": function(){
					$(me).val(orig_val);
					payout_modal.hide();								
				}
			}
		});
		payout_modal.show();
	});	
		
	var updatePayout = function(is_auto_payout,action) {	
		b.request({
			url: "/members/update_payout",
			data: {
				"_member_id": <?= $this->member->member_id; ?>,
				"_is_auto_payout": is_auto_payout,
				"_action": action
			},
			on_success: function(data){
				if(data.status == "1")
				{
					$("#auto_payout").data("orig",is_auto_payout)
					var successModal = b.modal.new({
						title: "Auto Payout :: Successful",
						html: data.html,
						disableClose: true,
						width: 400,
						buttons: {
							"Close": function(){
								successModal.hide();								
							}
						}
					});
					
					successModal.show();
				}
			}
		});
	
		return false;
	}
	
	
	
	$(document).on("click","#submit_password",function(){
		
		var error_found = false;
		var password = $("#old_password").val();
		var new_password = $("#new_password").val();
		
		$(".new_password_error").css("display","none");
		$(".old_password_error").css("display","none");
		$(".new_password_retype_error").css("display","none");
		
		b.request({
			url: "/members/check_password",
			data: {
				"member_id": <?= $this->member->member_id; ?>,
				"password": password
			},
			on_success: function(data){
				
				if(data.status == "error")
				{
					error_found = true;
					$(".old_password_error").css("display","");
					$("#old_password_error").text(data.msg);
				}
				
				if($("#new_password").val().length == 0)
				{
					error_found = true;
					$(".new_password_error").css("display","");
					$("#new_password_error").text("New password is required.");
				}
				else if($("#new_password").val().length < 5)
				{
					error_found = true;
					$(".new_password_error").css("display","");
					$("#new_password_error").text("Password minimum length is 5.");
				}
				
				if($("#new_password").val() != $("#new_password_retype").val())
				{
					error_found = true;
					$(".new_password_retype_error").css("display","");
					$("#new_password_retype_error").text("The password you typed did not match.");
				}
				
				if(!error_found)
				{
					b.request({
						url: "/members/change_password",
						data: {
							"member_id": <?= $this->member->member_id; ?>,
							"new_password": new_password
						},
						on_success: function(data){
							if(data.status == "ok")
							{
								var change_success = b.modal.new({
									title: "Change Password Success",
									html: "Your password was changed successfully!",
									disableClose: true,
									width: 320,
									buttons: {
										"Close": function(){
											change_success.hide();
											document.location.reload(true);
										}
									}
								});
								
								change_success.show();
							}
						}
					});
					
				}
			}
		});
	});
	
	$('body').on('click', 'option.add_new_group', function() {
		var add_group_modal = b.modal.new({
			title: 'Add New Group',
			html: "<label>Enter Group Name: </label>"
			+"<label class='label label-important'>WARNING: The new group will only be created upon clicking 'Save'.</label>"
			+"<input type='text' id='add_new_group_name' style='width:400px'></input>",
			width: 450,
			disableClose: true,
			buttons: {
				'Confirm': function(){
					var new_group_name = $("#add_new_group_name").val();
					$('#group_name').append('<option value="'+new_group_name+'" selected="selected">'+new_group_name+'</option>');
					add_group_modal.hide();
				},
				'Cancel': function() {
					add_group_modal.hide();
				}
			}
		});
		add_group_modal.show();
	});
	
});

</script>