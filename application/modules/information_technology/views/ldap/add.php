<?php

// get country codes from rf_settings
	$country_options = "";
	$country_codes = $this->setting->country_codes;
	$countries = json_decode($country_codes);
	
	$country_options .= "<option value=''>Select country</option>";

	foreach($countries as $c){
	    $selected = ($c->code=="PH") ? "selected='selected'":"";
	    $country_options .= "<option value=\"".$c->code."\" ".$selected.">".$c->name."</option>";
	}

	// country list
	$html_home_country = "
						  <div class='control-group'>
						  <label class='control-label'>Country <span class='is required'>*</span></label>
	                      <select name='select_home_country' id='select_home_country'>
	                      {$country_options}
	                      </select>
						  </div>";

	// PH provinces and municipalities
	$provinces_municipalities = $this->setting->ph_provinces_and_municipalities;
	$province_list = $provinces_municipalities;

	$html_home_province = "<div class='control-group'><label class='control-label'>State/Province <span class='is required'>*&nbsp;</span></label>
	                      <select name='select_home_province' id='select_home_province'>                      
	                      </select></div>";                                        

	$html_home_municipality = "<div class='control-group'><label class='control-label'>City/Municipality <span class='is required'>*</span></label>
	                      <select name='select_home_municipality' id='select_home_municipality'>                      
	                      </select></div>";                                                 
	

// date
	$months = array(
		'' => 'Month',
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
	
	$days = array('' => 'Day');
	for ($i = 1; $i <= 31; $i++) {
		if ($i < 10) {
			$days[$i] = "0" . $i;
		} else {
			$days[$i] = $i;
		}
	}

	$years = array('' => 'Year');
	$today = getdate();
	for ($i = $today['year']; $i >= 1900 ; $i--)
		$years[$i] = $i;
		
		
	$birth_year = date('Y');
	$birth_month = date('m');
	$birth_day = date('d');	
?>

<div class='content-area'>
	<h2>New LDAP Account</h2>
	<hr/>
	<div class="row-fluid">				
		<div class="span5">	
			<fieldset>						
				<div class='control-group'>	
					<label class='control-label'>Username <span class='is required'>*&nbsp;</span></label>
		            <input type='text' type='text' id='username' name='username' maxlength='16' value='' style='float: left; margin-bottom: 5px;' /><div style='float:left;margin:5px;cursor:pointer;' title='Check Availability'><button id='check-username-availability' class='btn btn-success' style='margin-top:-8px;' type='button'><span>Check</span></button></div>		            
		            <div style='clear:both;'></div>
				</div>

				<div class='control-group'>	
					<label class='control-label'>Password <span class='is required'>*&nbsp;</span></label>
		            <input title='Password' id='password1' type='password' name='password1' maxlength='16'/>
				</div>

				<div class='control-group'>	
		            <label class='control-label'>Re-type Password <span class='is required'>*&nbsp;</span></label>
		            <input title='Retype Password' id='password2' type='password' name='password2' maxlength='16'/>
				</div>
			</fieldset>
		</div>
	</div>							

	<div class="row-fluid">
		<div class="section-header center">
			Personal Information
		</div>											
		<div class="span6">
			<fieldset class='section-container'>					
				
				<div class='control-group'>	
					<label id='lbl_fname'>First Name <span class='is required'>*&nbsp;</span></label>	            
		            <input title='First Name' type='text' id='fname' name='fname' value='' autofocus=''/>
				</div>
                
				<div id='middle_last_name_container'>		            
					<div class='control-group'>	
						<label class='control-label'>Middle Name <span class='is required'>*&nbsp;</span></label>
						<input title='Middle Name' type='text' id='mname' name='mname' value=''/>		                               
					</div>
					<div class='control-group'>	
						<label class='control-label'>Last Name <span class='is required'>*&nbsp;</span></label>
						<input title='Last Name' type='text' id='lname' name='lname' value=''/>		                
					</div>
				</div>

					<label class='control-label'>Birthday <span class='is required'>*&nbsp;</span></label>
					<div id="birth_date_container" class="controls form-inline wc-date">
					<div class='control-group'>					
						<?= form_dropdown('birth_month', $months, $birth_month, 'id="birth_month" class="wc-date-month"') ?>
						<?= form_dropdown('birth_day', $days, $birth_day, 'id="birth_day" class="wc-date-day"') ?>
						<?= form_dropdown('birth_year', $years, $birth_year, 'id="birth_year" class="wc-date-year"') ?>
						<input type="hidden" id="birthdate" name="birthdate" value="<?= set_value('birthdate'); ?>" />							
					</div>
					</div>

			</fieldset>
		</div>
		<div class='span5'>
			<fieldset class='section-container'>
				<div id='gender_mstatus_container'>
					<div class='control-group'>						
	                	<label class='control-label'>Gender <span class='is required'>*</span></label>
	                    <select id='gender' name='gender'>
							<option value=''>Choose gender</option>
	                        <option value='M'>Male</option>
	                        <option value='F'>Female</option>
	                    </select>
					</div>

					<div class='control-group'>	               						                                   
	                	<label class='control-label'>Marital Status <span class='is required'>*</span></label>
	                    <select id='marital_status' name='marital_status'>
							<option value=''>Choose marital status</option>
	                        <option value='Single'>Single</option>
	                        <option value='Married'>Married</option>	                                        
	                    </select>
					</div>
				</div>

				<div class='control-group'>				
					<label class='control-label'>Nationality <span class='is required'>*</span></label>
		            <input type='text' id='nationality' name='nationality' value=''/>
				</div>

				<div class='control-group'>					
					<label class='control-label'>Taxpayer Identifiction Number (TIN) </label>
			        <input title='TIN' type='text' id='tin_number' name='tin_number' value='' placeholder='000-000-000-000'/>
				</div>			
			</fieldset>
		</div>
	</div>
	<div class="row-fluid">			
		<div class="section-header center">
			Contact Information
		</div>																				
		<div class="span6">	
			<fieldset class='section-container'>
				<div class='control-group'>					
					<label class='control-label'>Company Email <span class='is required'>*&nbsp;</span></label>
		            <input title='Email'  type='text' type='text' id='email' class='input-xlarge' name='email' value='' placeholder='firstname.lastname@mitsukoshimotors.com'/>
				</div>

				<div class='control-group'>	
					<label class='control-label'>Mobile Number <span class='is required'>*&nbsp;</span></label>
		            <select id="country_code" name="country_code" class="mobile_country_code">						
					<?php
						// get country codes
						$country_list = $this->setting->mobile_country_codes; 
	                          $countries = json_decode($country_list);
	                          foreach($countries as $c){
	                              $selected = ($c->code=="63") ? "selected='selected'":"";
	                      ?>
	                          <option value="<?=$c->code?>" <?=$selected?>> <?=$c->name?></option>
	                      <?php
	                          }
	                      ?>
					</select>
					<div class="clearfix"></div><br/>
			
		            <input title='Country Code' type='text' id='country_code_display' style='width:30px; float: left; margin-right: 5px;' value='' maxlength='4' disabled />
					<input title='Area Code' type='text' id='area_code' name='area_code' style='width:30px; float: left; margin-right: 5px;' value='' maxlength='3' placeholder='920'/>
					<input title='Mobile Number' type='text' id='mobile_number' name='mobile_number' class='input-small' value='' maxlength='7' placeholder='1234567'/>
				</div>
			</fieldset>
		</div>
		<div class="span5">
			<fieldset class='section-container'>
				<div class='control-group'>	
					<label class='control-label'>Address <span class='is required'>*&nbsp;</span></label>
                   	<input type='text' id='home_address' name='home_address' class='input-xlarge' value='' placeholder='Number, Street, Baranggay, Town'/>                                                       		
				</div>
				                  
			</fieldset>													
		</div>		
	</div>
	
	<div class="section-header center">		
	</div>
	
		<fieldset align='right'>
			<center><button type='button' class='btn btn-primary' style="width:200px;height:40px;font-size:20px;line-height:25px" id='validate-account' ><span>Submit</span></button></center>
			<input type='hidden' name='action' value='register'/>
        </fieldset>
	</form>
</div> <!-- end content-area-->


<script type="text/javascript">

	$("#validate-account").unbind("click");
		
	$("#validate-account").bind("click",function(e){
        e.preventDefault();
		validateAccount();
		return false;
	});


	var validateAccount = function() {

		var user_data = {
	        'username': $.trim($("#username").val()),
	        'fname': $.trim($("#fname").val()),
	        'mname': $.trim($("#mname").val()),
	        'lname': $.trim($("#lname").val()),
	        'email': $.trim($("#email").val()),
	        'password1': $.trim($("#password1").val()),
	        'password2': $.trim($("#password2").val()),
	        'country_code': $.trim($("#country_code").val()),
	        'area_code': $.trim($("#area_code").val()),
	        'mobile_number': $.trim($("#mobile_number").val()),
	        //'birthday': bday,
	        'gender': $.trim($('#gender').val()),
	        'marital_status': $.trim($('#marital_status').val()),
	        'nationality': $.trim($('#nationality').val()),
	        'tin_number': $.trim($('#tin_number').val()),
	        'address': $.trim($("#address").val()),
	        'is_ajax': 1
	    }

	    b.request({
	        url: '/information_technology/ldap/create_ldap_file',
	        data: user_data,
	        on_success: function(data, status) {
	            if (data.status == 0) {
	                var check_details_modal = b.modal.new({
	                    title: 'Registration Notice',
	                    width: '400px',
	                    html: "<div><p>"+data.message+"</p></div>"
	                });
	                check_details_modal.show();
	            } else {

	                // display registration summary
	                var reg_confirm_modal = b.modal.new({
	                    title: 'Confirm Registration',
	                    width: '650px',
	                    html: data.html,
						disableClose: true,
	                    buttons: {
							'Cancel' : function() {
								reg_confirm_modal.hide();
							},
	                        'Confirm' : function() {
	                            // ajax request
	                            b.request({
	                                url: '/main/registration/register',
	                                data: user_data,

	                                on_success: function(data, status) {
	                                    if (data.status == 1) {
	                                        // SUCCESS
	                                        redirect('main/registration/finish');


	                                    } else {
	                                        var register_error_modal = b.modal.new({
	                                            title: 'Registration Notice',
	                                            html: "<div><p>"+data.message+"</p></div>"
	                                        });
	                                        register_error_modal.show();
	                                    }
	                                }
	                            });
	                            reg_confirm_modal.hide();
	                        }
	                    }
	                });
	                reg_confirm_modal.show();
	                //css for registration_confirm_modal
	                $('.modal').each(function (){
	                    this.style.setProperty('left','10%', 'important');
	                    this.style.setProperty('width','80%', 'important');
	                });
	                $('.modal-body').css('padding','0 15px 0 15px');
	                $('.confirm-modal hr').css('margin','0');

	                $('#confirm_rf_id').html(user_data.rf_id);
	                $('#confirm_group_name').html(user_data.group_name);
	                $('#confirm_username').html(user_data.username);
	                $('#confirm_fname').html(user_data.fname);
	                $('#confirm_mname').html(user_data.mname);
	                $('#confirm_lname').html(user_data.lname);
	                $('#confirm_email').html(user_data.email);
	                $('#confirm_country_code').html(user_data.country_code);
	                $('#confirm_area_code').html(user_data.area_code);
	                $('#confirm_mobile_number').html(user_data.mobile_number);
	                $('#confirm_birthday').html(user_data.birthday);
	                $('#confirm_gender').html(user_data.gender);
	                $('#confirm_marital_status').html(user_data.marital_status);
	                $('#confirm_nationality').html(user_data.nationality);
	                $('#confirm_service_depot').html(user_data.service_depot);
	                $('#confirm_tin_number').html(user_data.tin_number);
	                $('#confirm_address').html(user_data.address);
	                $('#confirm_city').html(user_data.city);
	                $('#confirm_state_province').html(user_data.state_province);
	                $('#confirm_zip_postalcode').html(user_data.zip_postalcode);
	                $('#confirm_country').html(user_data.country);
	                $('#confirm_sponsor_id').html(user_data.sponsor_id);
	                $('#confirm_upline_id').html(user_data.upline_id);
	                $('#confirm_position').html(user_data.position);
	                $('#confirm_beneficiary1').html(user_data.beneficiary1);
	                $('#confirm_beneficiary2').html(user_data.beneficiary2);

	                // show group details format
	                if ($("#registration_type").val().toLowerCase() == 'individual') {
	                    $("#confirm_message").html("<p style='margin-left:15px;'>You are registering under Upline ID " + $('#upline_id').val() + " with Sponsor ID "+ $('#sponsor_id').val() +". The following data will be used for your enrollment as a new distributor. Do you want to proceed?</p>");
	                } else {
	                    $("#confirm_message").html("<p style='margin-left:15px;'>You are registering under Upline ID " + $('#upline_id').val() + " with Sponsor ID "+ $('#sponsor_id').val() +". The following data will be used for enrollment as a new group. Do you want to proceed?</p>");
	                }
	            }
	        }
	    });
	}


</script>