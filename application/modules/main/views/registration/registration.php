<?php

	$form_register_start = form_open($this->config->item('base_url').'/main/registration/register', array('id' => 'user-register', 'name' => 'user-register', 'method' => 'post'));	
	
	// set
	$fname = set_value('fname');
	$mname = set_value('mname');
	$lname = set_value('lname');
	$member_type = $this->input->post('member_type');
	$marital_status = set_value('marital_status');
	$nationality = set_value('nationality');
	$birthday = $this->input->post('birthday');
	$service_depot = $this->input->post('service_depot');
	$tin_number = $this->input->post('tin_number');

	// contact details
	$country_code = set_value('country_code');	
	if(empty($country_code)) $country_code = "63";
	$area_code = set_value('area_code');
	$mobile_number = set_value('mobile_number');

	$email = set_value('email');
	$address = set_value('address');	
	$home_city = set_value('home_city');
	$home_state_province = set_value('home_state_province');
	$home_zip_postalcode = set_value('home_zip_postalcode');

	// account details
	$account_id = set_value('account_id');
	$account_code = set_value('account_code');
	$username = set_value('username');
	$password1 = set_value('password1');
	$password2 = set_value('password2');
	$sponsor_id = set_value('sponsor_id');
	$beneficiary1 = set_value('beneficiary1');
	$beneficiary2 = set_value('beneficiary2');
	
	if ($position == "") {
		$position = set_value('position');
	} else {
		$position = $position;
	}
	
	if ($upline_id == "") {
		$upline_id = set_value('upline_id');
	} else {
		$upline_id = $upline_id;
	}
		
    $rf_id = set_value('rf_id');
    $group_name = set_value('group_name');

	// registration type
	$html_registration_type = "
						  <div class='control-group'>
						  <label class='control-label'>Registration Type</label>
	                      <select name='registration_type' id='registration_type' style='width:auto;'>
	                      	<option value=''>Choose Registration Type</option>
	                        <option value='INDIVIDUAL'>INDIVIDUAL</option>
	                        <option value='GROUP' >GROUP</option>
	                      </select>
						  </div>";
	
	// get country codes from rf_settings
	$country_options = "";
	$country_codes = $this->settings->country_codes;
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
	$provinces_municipalities = $this->settings->ph_provinces_and_municipalities;
	$province_list = $provinces_municipalities;

	$html_home_province = "<div class='control-group'><label class='control-label'>State/Province <span class='is required'>*&nbsp;</span></label>
	                      <select name='select_home_province' id='select_home_province'>                      
	                      </select></div>";                      

	$html_home_municipality = "<div class='control-group'><label class='control-label'>City/Municipality <span class='is required'>*</span></label>
	                      <select name='select_home_municipality' id='select_home_municipality'>                      
	                      </select></div>";                                                 
	
	$depot_options = "";	
	// get all depots using is_facilities
	//$service_depot = $this->facilities_model->get_facilities("facility_type_id = 2");
	$service_depot = $this->facilities_model->get_service_depots(); 
	
	$depot_options .= "<option value=\"\">Choose Service Depot</option>";
	
	foreach($service_depot as $sd)
	{
	    //$depot_options .= "<option value=\"".$sd->facility_id."\">".$sd->facility_name."</option>";
	    $depot_options .= "<option value=\"".$sd->service_depot_id."\">".$sd->depot_name."</option>"; 
	}
	$html_service_depot = "<div class='control-group'><label class='control-label'>Service Depot <span class='is required'>*</span></label>
	                      <select name='service_depot' id='service_depot'>
	                      {$depot_options}
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
	<h2>New Distributor's Registration</h2>

	<?=$form_register_start?>
	<div class="row-fluid">				
		<div class="section-header center">
			Account Information
		</div>		
		<div class="span6">	
			<fieldset class='section-container'>
				<div class='control-group'>
					<label class='control-label' for='account_id'>Control Code<span class='is required'>*&nbsp;</span></label>
		            <input title='Please enter the correct number codes from your scratch card.' type='text' type='text' id='account_id' name='account_id' maxlength='10' value='<?=$account_id?>' placeholder='65xxxxxxxx'/> 
				</div>

				<div class='control-group'>	
					<label class='control-label'>RN <span class='is required'>*&nbsp;</span></label>
		            <input title='Please enter the correct code for your scratch card.' type='text' type='text' id='account_code' name='account_code' maxlength='16' value='<?=$account_code?>' style='float: left; margin-bottom: 5px;' /><div style='float:left;margin:5px;cursor:pointer;' title='Check Availability'><button id='check-account-id' class='btn btn-success' style='margin-top:-8px;' type='button'><span>Check</span></button></div>		            
		            <div style='clear:both;'></div>
				</div>

				<div class='control-group'>	
	                <label class='control-label'>Sponsor ID <span class='is required'>*&nbsp;</span></label>
		            <input title='Please ask your Sponsor for his/her Sponsor ID.' type='text' type='text' id='sponsor_id' name='sponsor_id' maxlength='10' value='<?=$sponsor_id?>' style='float: left; margin-bottom: 5px;' placeholder='65xxxxxxxx'/><div style='float:left;margin:5px;cursor:pointer;' title='Check Sponsor'><button class='btn btn-success' id='check-sponsor-availability' style='margin-top:-8px;' type='button'><span>Check</span></button></div>
		            <div style='clear:both;'></div>	
				</div>

				<div class='control-group'>	
					<label class='control-label'>Upline ID <span class='is required'>*&nbsp;</span></label>
		            <input title='Please ask your Sponsor for the Upline ID and position.' type='text' type='text' id='upline_id' name='upline_id' maxlength='10' style='float: left; margin-bottom: 5px;' placeholder='65xxxxxxxx' value='<?=$upline_id?>'/><div style='float:left;margin:5px;cursor:pointer;' title='Check Upline'><button id='check-upline-availability' class='btn btn-success' style='margin-top:-8px;' type='button'><span>Check</span></button></div>
		            <div style='clear:both;'></div>
				</div>

                <div class='control-group'>
                    <label class='control-label'>Group Name </label>
                    <input type='text' type='text' id='group_name' name='group_name' style='float: left; margin-bottom: 5px;' disabled=disabled />
                    <div style='clear:both;'></div>
                </div>
			
				<div class='control-group'>	
					<label class='control-label'>Position <span class='is required'>*</span></label>
                    <select id='position' name='position'>
					<?php
						if ($position == 1) {
					?>	
                        	<option value='left'>Left</option>
                        	<option value='right' selected="selected">Right</option>
					<?php
					} else  if ($position == 2) {
					?>
							<option value='left' selected="selected">Left</option>
                    		<option value='right'>Right</option>
					<?php
					} else {
					?>
							<option value='left'>Left</option>
                    		<option value='right'>Right</option>
					<?php
					}
					?>
					
                    </select>
				</div>
			</fieldset>
		</div>
	
		<div class="span5">	
			<fieldset>						
				<div class='control-group'>	
					<label class='control-label'>Username <span class='is required'>*&nbsp;</span></label>
		            <input type='text' type='text' id='username' name='username' maxlength='16' value='<?=$username?>' style='float: left; margin-bottom: 5px;' /><div style='float:left;margin:5px;cursor:pointer;' title='Check Availability'><button id='check-username-availability' class='btn btn-success' style='margin-top:-8px;' type='button'><span>Check</span></button></div>		            
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
					<?=$html_service_depot?>

                <div class='control-group'>
                    <label class='control-label' for='rf_id'>RF ID</label>
                    <input type='text' type='text' id='rf_id' name='rf_id' maxlength='10' value='<?=$rf_id?>' placeholder='0******'/>
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
				<?=$html_registration_type?>
	            
				<div class='control-group'>	
					<label id='lbl_fname'>First Name <span class='is required'>*&nbsp;</span></label>	            
		            <input title='First Name' type='text' id='fname' name='fname' value='<?=$fname?>' autofocus=''/>
				</div>
                
				<div id='middle_last_name_container'>		            
					<div class='control-group'>	
						<label class='control-label'>Middle Name <span class='is required'>*&nbsp;</span></label>
						<input title='Middle Name' type='text' id='mname' name='mname' value='<?=$mname?>'/>		                               
					</div>
					<div class='control-group'>	
						<label class='control-label'>Last Name <span class='is required'>*&nbsp;</span></label>
						<input title='Last Name' type='text' id='lname' name='lname' value='<?=$lname?>'/>		                
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
		            <input type='text' id='nationality' name='nationality' value='<?=$nationality?>'/>
				</div>

				<div class='control-group'>					
					<label class='control-label'>Taxpayer Identifiction Number (TIN) </label>
			        <input title='TIN' type='text' id='tin_number' name='tin_number' value='<?=$tin_number?>' placeholder='000-000-000-000'/>
				</div>
				
				<div class='control-group'>	
					<label class='control-label'>Beneficiary 1</label>
		            <input title='Beneficiary 1' id='beneficiary1' class='input-xlarge' type='text' name='beneficiary1' value='<?=$beneficiary1?>'/>
				</div>	
				
				<div class='control-group'>	
					<label class='control-label'>Beneficiary 2</label>
		            <input title='Beneficiary 2' id='beneficiary2' class='input-xlarge' type='text' name='beneficiary2' value='<?=$beneficiary2?>'/>
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
					<label class='control-label'>Email <span class='is required'>*&nbsp;</span></label>
		            <input title='Email'  type='text' type='text' id='email' class='input-xlarge' name='email' value='<?=$email?>' placeholder='your_name@yahoo.com'/>
				</div>

				<div class='control-group'>	
					<label class='control-label'>Mobile Number <span class='is required'>*&nbsp;</span></label>
		            <select id="country_code" name="country_code" class="mobile_country_code">						
					<?php
						// get country codes
						$country_list = $this->settings->mobile_country_codes; 
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
			
		            <input title='Country Code' type='text' id='country_code_display' style='width:30px; float: left; margin-right: 5px;' value='<?=$country_code?>' maxlength='4' disabled />
					<input title='Area Code' type='text' id='area_code' name='area_code' style='width:30px; float: left; margin-right: 5px;' value='<?=$area_code?>' maxlength='3' placeholder='920'/>
					<input title='Mobile Number' type='text' id='mobile_number' name='mobile_number' class='input-small' value='<?=$mobile_number?>' maxlength='7' placeholder='1234567'/>
				</div>
			</fieldset>
		</div>
		<div class="span5">
			<fieldset class='section-container'>
				<div class='control-group'>	
					<label class='control-label'>Address <span class='is required'>*&nbsp;</span></label>
                   	<input type='text' id='home_address' name='home_address' class='input-xlarge' value='<?=$address?>' placeholder='Number, Street, Baranggay, Town'/>                                                       		
				</div>
				<div class='control-group'>					
                   <?=$html_home_country?>	
                </div>
                   	<div id='home_city_province_combobox_block'>
						<div class='control-group'>	
                       		<?=$html_home_province?>                                            	                        
						</div>
	                 	<div class='control-group'>								
                       	<?=$html_home_municipality?>
	                    </div>
                   </div>
				

                   <div id='home_city_province_textbox_block' style='display:none;'>
	                   <div class='control-group'>	
                       		<label class='control-label'>City <span class='is required'>*</span></label>
                       		<input type='text' id='text_home_municipality' name='text_home_municipality' value='<?=$home_city?>'/>
	                   </div>	

		               <div class='control-group'>	
                       		<label class='control-label'>State/Province <span class='is required'>*</span></label>
                       		<input type='text' id='text_home_province' name='text_home_province' value='<?=$home_state_province?>'/>                                    
                   		</div>	
                   </div>
                 
                   <label class='control-label'>Zip/Postal Code</label>
                   <input class='input-small' type='text' id='home_zip_postalcode' name='home_zip_postalcode' value='<?=$home_zip_postalcode?>'/>                                    
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
//<![CDATA[
    var user_data;
    var _is_vitalc_member = 0;	
	var _position = "n/a";

   $(document).ready(function() {

		$('#birth_month').change(function() {
			beyond.webcontrol.updateDateControl('birthdate');
		});
		$('#birth_day').change(function() {
			beyond.webcontrol.updateDateControl('birthdate');
		});
		$('#birth_year').change(function() {
			beyond.webcontrol.updateDateControl('birthdate');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');
		
		$("#registration_type").change(function(){

            if ($("#registration_type").val().toLowerCase() == '' || $("#registration_type").val().toLowerCase() == 'individual') {
                $("#middle_last_name_container").show();
                $("#gender_mstatus_container").show();
                $("#lbl_fname").html("First Name <span class='is required'>*&nbsp;</span>");
            } else if ($("#registration_type").val().toLowerCase() == 'group'){
                $("#middle_last_name_container").hide();
                $("#gender_mstatus_container").hide();
                $("#lbl_fname").html("Group Name <span class='is required'>*&nbsp;</span>");
            }
        });

		$("#area_code, #mobile_number, #country_code, #home_zip_postalcode").addClass("numeric-entry");

		$('.mobile_country_code').change(function(){
            var _target = $(this).attr("id") + "_display";
            var _val = $(this).val();

            $("#"+_target).val(_val);

            return false;

        });

        $("#select_home_province").append("<option id='0' value='0'>Select state or province</option>");
        $("#select_home_municipality").append("<option id='0' value='0'>Select city or municipality</option>");

        // check json province
        var province_json = <?= $province_list;?>;

         // list all provinces
         $.each(province_json,function(key,data) {
            var option = "<option id='"+data.province_id+"' value='"+data.province_id+"'>"+data.province_name+"</option>";
            $("#select_home_province").append(option);
         });

        $("#select_home_country").change(function() {
            if ($("#select_home_country").val() == 'PH') {
                //alert("ph");
                $("#home_city_province_combobox_block").show();
                $("#home_city_province_textbox_block").hide();
            } else {
                $("#home_city_province_combobox_block").hide();
                $("#home_city_province_textbox_block").show();
            }
        });

        $("#select_home_province").change(function() {
            $("#select_home_province option[id='0']").remove();
            $("#select_home_municipality").empty();
            var province_val = $(this).val();

            $.each(province_json,function(key,data) {
                if (data.province_id==province_val)
                {
                    var municipalities = data.municipality_list.split("|");
                    //alert(municipalities);
                     $.each(municipalities,function(key,data){
                        var option1 = "<option id='"+data+"' value='"+data+"'>"+data+"</option>";
                        $("#select_home_municipality").append(option1);
                    });
                }
            });
        });

        $("#check-username-availability").unbind("click");
		$("#check-username-availability").bind("click",function(e){
            e.preventDefault();
			checkUsernameAvailability();
			return false;
		});

        $("#check-sponsor-availability").unbind('click');
		$("#check-sponsor-availability").bind("click",function(e){
            e.preventDefault();
			checkSponsorIdAvailability(false);
			return false;
		});

        $("#check-upline-availability").unbind("click");
		$("#check-upline-availability").bind("click",function(e){
            e.preventDefault();
			checkUplineIdAvailability(false);
			return false;
		});

        $("#check-account-id").unbind("click");
		$("#check-account-id").bind("click",function(e){
            e.preventDefault();
			checkAccountIDandAccountCode();
			return false;
		});

		$("#account_code").unbind("blur");
		$("#account_code").bind("blur", function(e){
			e.preventDefault();
			checkAccountIDandAccountCode();
			return false;
		});

        $("#validate-account").unbind("click");
		$("#validate-account").bind("click",function(e){
            e.preventDefault();
			validateAccount();
			return false;
		});

    });

	var checkUsernameAvailability = function () {           
		var _elem = $("#username");
        var username = $.trim(_elem.val());
		var _parent = _elem.parent();
        
        if (username != '') {
            var ret = validateUsername(username);
            if (ret.code == 0) {		
				b.request({
					url: '/main/registration/check_username',
		        	data: {
						'username': username
					},
					on_success: function(data, status) {
						if (data.status==1)
						{
							_parent.addClass("control-group success");
							_parent.find(".check").remove();
							_parent.find(".required").append("<span class='check'>(Available)</span>");							
						}
						else
						{
							_parent.addClass("control-group error");
							_parent.find(".check").remove();
							_parent.find(".required").append("<span class='check'>(" + data.message + ")</span>");														
						}
					}																
		        });
			} else {
				// error
				var check_username_modal = b.modal.new({
					title: 'Registration Notice',
					width: '400px',
					html: "<p>"+ret.text+"</p>"
				});
				
				setFormControlErrorMsg(_elem.ret.text);
				
				check_username_modal.show();
			}	
		}
	};		
				
	var validateUsername = function(username) {

	    var m_username = jQuery.trim(username);
	    var m_result = {code: 0, username: m_username, text: ''};

	    if (m_username != '') {

	        // check the lengh first
	        if (m_username.length >= 5 && m_username.length <= 16) {

	            var result = /^[a-z0-9]+$/i.test(m_username);

	            if (!result) {
	                // invalid username
	                m_result.code = 2;
	                m_result.text = "Username '<b>" + m_username + "</b>' has invalid characters.";
	            } 

	        } else {
	            // username to short or long
	            var m_len = 'short';
	            if (m_username.length > 16) m_len = 'long';
	            m_result.code = 3;
	            m_result.text = "Username '<b>" + m_username + "</b>' too " + m_len + ".";
	        }

	    } else {
	        m_result.code = 1;
	        m_result.text = "Username is required";
	    }

	    return m_result;
	};

	
	var checkSponsorIdAvailability = function(from_submit, callback) {           
		var _sponsor_id = $("#sponsor_id");
		var _upline_id = $("#upline_id");
		var _parent = _sponsor_id.parent();
		var _upline_parent = _upline_id.parent();
        var sponsor_id = $.trim(_sponsor_id.val());
		var upline_id = $.trim(_upline_id.val());
    	var is_valid_sponsor = 0;

		b.request({
			url: '/main/registration/check_sponsor_id',
        	data: {
				'sponsor_id': sponsor_id,
				'upline_id': upline_id,
				'from_submit': from_submit
			},

			on_success: function(data, status) {
							
				if (data.status==1) {
					if (from_submit == false) {
						var check_sponsorid_modal = b.modal.new({
							title: 'Sponsor Verification',
							width: '580px',
							html: data.html
						});
						check_sponsorid_modal.show();
					}					
					
					if (data.valid_sponsor == 1) {
						_parent.removeClass("control-group error");					
						_parent.addClass("control-group success");
						_parent.find(".check").remove();
						_parent.find(".required").append("<span class='check'>(Valid)</span>");		
						_parent.find(".help-inline").remove();
																													
					} else {
						_parent.removeClass("control-group success");	
						_parent.addClass("control-group error");
						_parent.find(".check").remove();
						_parent.find(".required").append("<span class='check'>(Invalid)</span>");
						_parent.find(".help-inline").remove();
					}

					//alert("from data:" + data.valid_sponsor);
					is_valid_sponsor = data.valid_sponsor;

				} 
				else 
				{
					if (data.upline_flag == 1) {
						_upline_parent.addClass("control-group error");
						_upline_parent.find(".check").remove();
						_upline_parent.find(".required").append("<span class='check'>(Required)</span>");
					}	
					
					_parent.addClass("control-group error");
					_parent.find(".check").remove();
					_parent.find(".required").append("<span class='check'>(" + data.html + ")</span>");	
																												
				}
				if(_.isFunction(callback)) callback.call(this, is_valid_sponsor);				
			}
																														
        });
		
		//return is_valid_sponsor;
	};
	
	
	var checkUplineIdAvailability = function(from_submit, callback) {          
		var _upline_id = $("#upline_id");
		var _parent = _upline_id.parent();
        var upline_id = $.trim(_upline_id.val());
    	var is_valid_upline = 0;

		var m_result = {available_side: 0, is_valid_upline: false, message: '', group_name: ''};


		b.request({
			url: '/main/registration/check_upline_id',
        	data: {
				'upline_id': upline_id
			},

			on_success: function(data, status) {				
				if (data.status==1)				
				{
					//is_valid_upline = data.is_valid_upline;
					//_position = data.side_value;
					m_result.available_side = data.side_value;
					m_result.is_valid_upline = data.is_valid_upline;
                    m_result.group_name = data.group_name;

					//alert(data.html);

					if (from_submit == false)  					
					{												
						var check_sponsorid_modal = b.modal.new({
							title: 'Upline Verification',
							width: '580px',
							html: data.html,
							disableClose : true,
							buttons: {
								'Ok' : function() {
									//if (data.side_value == "left")
                                    if ($('#available_position').val() == "left")
                                    {
										$("#position option[value='left']").attr("selected", true);
										$("#position").attr("disabled", true);
									
										_parent.addClass("control-group success");
										_parent.find(".check").remove();
										_parent.find(".required").append("<span class='check'>(Valid)</span>");
										_parent.find(".help-inline").remove();
									}
                                    else if ($('#available_position').val() == "right")
									// else if (data.side_value == "right")
									{
										$("#position option[value='right']").attr("selected", true);
										$("#position").attr("disabled", true);
										resetFormControl($("#position"));
									
										_parent.addClass("control-group success");
										_parent.find(".check").remove();
										_parent.find(".required").append("<span class='check'>(Valid)</span>");
										_parent.find(".help-inline").remove();
									} 
									/*else if (data.side_value == "both")
									{
										$("#position option[value='right']").attr("selected", true);										
										resetFormControl($("#position"));
										
										_parent.addClass("control-group success");
										_parent.find(".check").remove();
										_parent.find(".required").append("<span class='check'>(Valid)</span>");
									}*/
									else 
									{								
										_parent.addClass("control-group error");
										_parent.find(".check").remove();
										_parent.find(".required").append("<span class='check'>(No Available Slot)</span>");
									}

                                    var upline_id_group_name = $("#upline_id_group_name").html();

                                    $('#group_name').val(upline_id_group_name);
									check_sponsorid_modal.hide();
																
								},
								'Cancel' : function() {

                                    $('#group_name').val("");
                                    _parent.removeClass("control-group success");
                                    _parent.find(".check").remove();
									check_sponsorid_modal.hide();
								}							
							}																	        
						});
						check_sponsorid_modal.show();
					} else {

				    	$('#group_name').val(data.group_name);
									
						_parent.removeClass("control-group error");					
						_parent.addClass("control-group success");
						_parent.find(".check").remove();					
						_parent.find(".required").append("<span class='check'>(Valid)</span>");				
					}
				}
					else
				{
					_parent.removeClass("control-group success");
					
					_parent.addClass("control-group error");
					_parent.find(".check").remove();
					_parent.find(".required").append("<span class='check'>(" + data.html + ")</span>");
					
					
				}
				if(_.isFunction(callback)) callback.call(this, m_result);			
			}																																		
        });
	};
	
	var checkAccountIDandAccountCode = function()
	{
		var _account_id = $("#account_id");
		var _parent = _account_id.parent();
        var account_id = $.trim(_account_id.val());
        var account_code = $.trim($("#account_code").val());

		var _account_code = $("#account_code");
		var _parent_account_code = _account_code.parent();

		b.request({
			url: '/main/registration/check_account_id',
        	data: {
				'account_id': account_id,
				'account_code':account_code
			},

			on_success: function(data, status) {
				if (data.status==1)
				{
					_parent.addClass("control-group success");
					_parent.find(".check").remove();
					_parent.find(".required").append("<span class='check'>(Valid)</span>");																			

					//_parent_account_code.removeClass("error");
					_parent_account_code.addClass("control-group success");
					_parent_account_code.find(".check").remove();
					_parent_account_code.find(".required").append("<span class='check'>(" + data.html + ")</span>");
					_parent_account_code.find(".help-inline").remove();

				}
				else if (data.status==0)
				{
					_parent.removeClass("success");
					_parent.addClass("control-group error");
					_parent.find(".check").remove();
					_parent.find(".required").append("<span class='check'>(" + data.html + ")</span>");
					
					_parent_account_code.removeClass("success");
					_parent_account_code.addClass("control-group error");
					_parent_account_code.find(".check").remove();
					_parent_account_code.find(".required").append("<span class='check'>(" + data.html + ")</span>");
																								
				} else {
					_parent.removeClass("error");
					_parent.find(".check").remove();
					
					_parent_account_code.removeClass("success");
					_parent_account_code.addClass("control-group error");
					_parent_account_code.find(".check").remove();
					_parent_account_code.find(".required").append("<span class='check'>(" + data.html + ")</span>");
				}
			}																											
        });		
	}
	
	var validateAccount = function () {
    	var hasError = false;
	
		var birth_d = '';
		// add 0 pad to day
		if ($("#birth_day").val() < 10) {
			birth_d = '0' +  $("#birth_day").val();
		} else {
			birth_d = $("#birth_day").val();
		}	
		
		// set bday             
		var bday = $("#birth_year").val() + '-' + $("#birth_month").val() + '-' + birth_d;

		var _account_id = $("#account_id");
		resetFormControl(_account_id);
        if ($.trim(_account_id.val()) == '') {
        	hasError = true;
			setFormControlErrorMsg(_account_id,"Account ID is required");
        }

		var _account_code = $("#account_code");
		resetFormControl(_account_code);		        
        if ($.trim(_account_code.val()) == '') {
        	hasError = true;
			setFormControlErrorMsg(_account_code,"Account Code is required");
        }
		
		var _service_depot = $("#service_depot");
		resetFormControl(_service_depot);		        
        if ($.trim(_service_depot.val()) == '') {
        	hasError = true;
			setFormControlErrorMsg(_service_depot,"Service Depot is required");
        }
		
		/*// position
		var _position_set = $("#position");
				        
        if ($.trim(_position) == 'n/a') {
        	hasError = true;
			setFormControlErrorMsg(_position_set,"No available position for Upline ID.");
        }*/
		
		resetFormControl($("#registration_type"));
		if(_.isEmpty($("#registration_type").val())) {
			setFormControlErrorMsg($("#registration_type"),"Registration Type is required");
		}
		
		var _fname = $("#fname");
		var _lname = $("#lname");
		var _mname = $("#mname");
		resetFormControl(_fname);
        if ($.trim(_fname.val()) == '') {
        	hasError = true;
			if ($("#registration_type").val().toLowerCase() == 'individual' || $("#registration_type").val().toLowerCase() == '')
			{
				setFormControlErrorMsg(_fname,"First name is required");
			}
			else if ($("#registration_type").val().toLowerCase() == 'group')
			{
				setFormControlErrorMsg(_fname,"Group name is required");
			}
        }

		resetFormControl(_lname);
		resetFormControl(_mname);
        // validate middle and last name if registration_type = individual 
        if ($("#registration_type").val().toLowerCase() == 'individual' || $("#registration_type").val().toLowerCase() == '') {
			
        	// middle name
            if ($.trim(_mname.val()) == '') {
            	hasError = true;
				setFormControlErrorMsg(_mname,"Middle name is required");
            }                 
            // last name
            if ($.trim(_lname.val()) == '') {
            	hasError = true;
				setFormControlErrorMsg(_lname,"Last name is required");
            }
        }

		var _username = $("#username");
		resetFormControl(_username);
     	// username
        if ($.trim(_username.val()) == '') {
        	hasError = true;
			setFormControlErrorMsg(_username,"Username is required.");
        } else {
        	pw = $.trim($("#username").val());
        	if (pw.length < 5) {
            	hasError = true;
				setFormControlErrorMsg(_username,"Must be more than 4 characters");
        	}
  		}                 

		var _email = $("#email");
		resetFormControl(_email);
        if ($.trim(_email.val()) == '') {
        	hasError = true;
			setFormControlErrorMsg(_email,"Email address is required");
        }

		var _password1 = $("#password1");
        resetFormControl(_password1);
      	// password
        if ($.trim(_password1.val()) == '') {
			hasError = true;
			setFormControlErrorMsg(_password1,"Password is required");
        } else {
			pw = $.trim(_password1.val());
			if (pw.length < 5) {
				hasError = true;
				setFormControlErrorMsg(_password1,"Must be more than 4 characters");
			}
        }

		var _password2 = $("#password2");
        resetFormControl(_password2);
        if ($.trim(_password2.val()) != $.trim($("#password1").val())) {
			hasError = true;
			setFormControlErrorMsg(_password2,"Password does not match");
        }            
        
		resetFormControl($("#birth_month"));
		// birthday
		if (($.trim($("#birth_month").val()) == '') || ($.trim($("#birth_day").val()) == '') || ($.trim($("#birth_year").val()) == '')) { 
			var bday_error = "";
			hasError = true;
			if($.trim($("#birth_month").val()) == '') bday_error = bday_error + "Month is required. ";
			
			if($.trim($("#birth_day").val()) == '') bday_error = bday_error + "Day is required. ";
			
			if($.trim($("#birth_year").val()) == '') bday_error = bday_error + "Year is required. ";
				
			setFormControlErrorMsg($("#birth_month"),bday_error);
		} else {
			
			var today = new Date();
			var birthDate = new Date(bday);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

			// only check age if registration type individual
            if ($("#registration_type").val().toLowerCase() == 'individual') {
            	if (age <18) {
                	hasError = true;
 				  	setFormControlErrorMsg($("#birth_month"),"Must be at least 18 years old");
                } 
            }			
		}		
		
		
		
		var _home_address = $("#home_address");
		resetFormControl(_home_address);
		// address block
		if ($.trim(_home_address.val()) == '') {
        	hasError = true;
		  	setFormControlErrorMsg(_home_address,"Address is required");
       	}

		resetFormControl($("#select_home_country"));
		if (($("#select_home_country").val() == 0)) {
			hasError = true;
		  	setFormControlErrorMsg($("#select_home_country"),"Country is required");
   		}

		resetFormControl($("#select_home_province"));
		resetFormControl($("#text_home_province"));
		
		if (($("#select_home_province").val() == 0) && ($("#text_home_province").val() == '')) {
			hasError = true;
		  	setFormControlErrorMsg($("#select_home_province"),"Province is required");
		  	setFormControlErrorMsg($("#text_home_province"),"Province is required");
   		}    
		resetFormControl($("#select_home_municipality"));
		resetFormControl($("#text_home_municipality"));
		if (($("#select_home_municipality").val() == 0) && ($("#text_home_municipality").val() == '')) {
			hasError = true;
			setFormControlErrorMsg($("#select_home_municipality"),"Municipality is required");
			setFormControlErrorMsg($("#text_home_municipality"),"Municipality is required");
		}    
		
		var _country_code = $("#country_code");
		var _area_code = $("#area_code");
		var _mobile_number = $("#mobile_number");
 
		var country_code = $.trim(_country_code.val());
		var area_code = $.trim(_area_code.val());
		var mobile_number = $.trim(_mobile_number.val());
		
		resetFormControl(_country_code);
		
		if ((country_code=='')||(area_code=='')||(mobile_number=='')) {
			hasError = true;
			setFormControlErrorMsg(_country_code,"Invalid Number");
		}
        
		var _gender = $("#gender");
		resetFormControl(_gender);
		if(_.isEmpty($(_gender).val()) && $("#registration_type").val().toLowerCase() == 'individual')
		{
			hasError = true;
			setFormControlErrorMsg(_gender,"Gender is required");
		}
		
		var _marital_status = $("#marital_status");
		resetFormControl(_marital_status);
		if(_.isEmpty($(_marital_status).val())  && $("#registration_type").val().toLowerCase() == 'individual')
		{
			hasError = true;
			setFormControlErrorMsg(_marital_status,"Marital Status is required");
		}
		
		var _nationality = $("#nationality");
		resetFormControl(_nationality);
		if(_.isEmpty($(_nationality).val()))
		{
			hasError = true;
			setFormControlErrorMsg(_nationality,"Nationality is required");
		}
		
		var _sponsor_id = $("#sponsor_id");
		resetFormControl(_sponsor_id);
		// sponsor id
		if ($.trim(_sponsor_id.val()) == '') {
	    	hasError = true;
		  	setFormControlErrorMsg(_sponsor_id,"Sponsor is required");	
	    } 
		//else {
	    //	pw = $.trim(_sponsor_id.val());
	    //    if (pw.length < 10) {
	    //    	hasError = true;
		//	  	setFormControlErrorMsg(_sponsor_id,"Invalid Sponsor ID");	
	    //    }
	  	//}

		var _upline_id = $("#upline_id");
		var _position = $("#position");
        var _rf_id = $("#rf_id");

		resetFormControl(_upline_id);
		resetFormControl(_position);

		// upline id
		if ($.trim(_upline_id.val()) == '') {
	    	hasError = true;
		  	setFormControlErrorMsg(_upline_id,"Upline is required");
		
	    } 
		//else {
	    //	pw = $.trim(_upline_id.val());
	    //    if (pw.length < 10) {
	    //    	hasError = true;
		//	  	setFormControlErrorMsg(_upline_id,"Invalid Upline ID");			
	    //    }
	  	//}
    	
		// check upline
		checkUplineIdAvailability(true, function(var_upline){
			//alert(var_upline.available_side + "|" + var_upline.is_valid_upline);

            if (var_upline.is_valid_upline == 0) {
				hasError = true;
			    setFormControlErrorMsg(_upline_id,"Invalid Upline ID");	
			}

            //alert(var_upline.available_side + "|" + $("#position").val() );
            //return;

            if ((var_upline.available_side == "both") || (var_upline.available_side == "n/a")) {

            } else {
                if(var_upline.available_side != $("#position").val()) {
                    hasError = true;
                    setFormControlErrorMsg(_position,"Position not available");
                }
            }

            var _group_name = $("#group_name");

            // check sponsor
            checkSponsorIdAvailability(true, function(isValidSponsor){
                if (isValidSponsor == 0) {
                    hasError = true;
                }
            });

            if (hasError) {
                var registration_error_modal = b.modal.new({
                    title: 'Registration Notice',
                    width: '400px',
                    html: "<div><p>Please fill out necessary fields and correct the errors found.</p></div>"
                });

                registration_error_modal.show();
            } else {
                // validate values

                if ($("#select_home_country").val() == 'PH') {
                    var is_province = $.trim($('#select_home_province>option:selected').text());
                    var is_municipality = $.trim($('#select_home_municipality>option:selected').text());
                } else {
                    var is_province = $.trim($('#text_home_province').val());
                    var is_municipality = $.trim($('#text_home_municipality').val());
                }

                var user_data = {
                    'account_id': $.trim(_account_id.val()),
                    'account_code': $.trim(_account_code.val()),
                    'username': $.trim(_username.val()),
                    'fname': $.trim(_fname.val()),
                    'mname': $.trim(_mname.val()),
                    'lname': $.trim(_lname.val()),
                    'email': $.trim(_email.val()),
                    'rf_id': $.trim(_rf_id.val()),
                    'group_name': $.trim(_group_name.val()),
                    'password1': $.trim(_password1.val()),
                    'password2': $.trim(_password2.val()),
                    'country_code': $.trim(_country_code.val()),
                    'area_code': $.trim(_area_code.val()),
                    'mobile_number': $.trim(_mobile_number.val()),
                    'birthday': bday,
                    'gender': $.trim($('#gender').val()),
                    'marital_status': $.trim($('#marital_status').val()),
                    'nationality': $.trim($('#nationality').val()),
                    'tin_number': $.trim($('#tin_number').val()),
                    'service_depot': $.trim($('#service_depot').val()),
                    'sponsor_id': $.trim(_sponsor_id.val()),
                    'upline_id': $.trim(_upline_id.val()),
                    'position': $.trim($('#position').val()),
                    'beneficiary1': $.trim($('#beneficiary1').val()),
                    'beneficiary2': $.trim($('#beneficiary2').val()),
                    'address': $.trim(_home_address.val()),
                    'city': is_municipality,
                    'state_province': is_province,
                    'zip_postalcode': $.trim($('#home_zip_postalcode').val()),
                    'country': $.trim($('#select_home_country').val()),
                    'registration_type': $.trim($('#registration_type').val().toLowerCase()),
                    'is_ajax': 1
                }

                b.request({
                    url: '/main/registration/check_reg_details',
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

        })


	};


	var setFormControlErrorMsg = function(elem,msg)
	{
		elem.parent().addClass("error");
		elem.parent().find(".help-inline").remove();			
		elem.parent().append("<span class='help-inline'>" + msg + "</span>");		
	}
	
	var resetFormControl = function(elem) 
	{
		elem.parent().removeClass("error");
		elem.parent().find(".help-inline").remove();			
		elem.parent().append("<span class='help-inline'></span>");		
	}

    $('#username').keyup(function (){
        $(this).siblings().children('.help-inline').remove();
        $(this).siblings('.help-inline').remove();
        var username = $('#username').val();
        if(username.length<5){
            $(this).siblings(':last').append('<span class="help-inline" style="color:#B94A48;">Must be more than 4 characters</span>');
        }
    });
    $('#password1, #password2').keyup(function (){
        $(this).parent().children('.help-inline').remove();
        var password = $(this).val();
        if(password.length<5){
            $(this).after('<span class="help-inline" style="color:#B94A48;">Must be more than 4 characters</span>');
        }
    });


</script>