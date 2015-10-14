<form>
	<fieldset >
		<label class="control-label" for="last_name"><strong>Last Name </strong></label>
		<div class="controls">
			<input type="text" class='span5' rows='3' placeholder="Last Name" name="last_name" id="last_name" value="<?= set_value('last_name') ?>">
		</div>
		<span class='label label-important' id='last_name_error' style='display:none;'>Last Name Field is required.</span>
		
		<label class="control-label" for="first_name"><strong>First Name </strong></label>
		<div class="controls">
			<input type="text" class='span5' placeholder="First Name" name="first_name" id="first_name" value="<?= set_value('first_name') ?>">
		</div>
		<span class='label label-important' id='first_name_error' style='display:none;'>First Name Field is required.</span>
		
		<label class="control-label" for="last_name"><strong>Middle Name </strong></label>
		<div class="controls">
			<input type="text" class='span5' placeholder="Middle Name" name="middle_name" id="middle_name" value="<?= set_value('middle_name') ?>">
		</div>
		<span class='label label-important' id='middle_name_error' style='display:none;'>Middle Name Field is required.</span>
		
		<label class="control-label" for="last_name"><strong>Mobile Number </strong></label>
		<div class="controls">
			<input type="text" class='span3' placeholder="Mobile Number" name="mobile_number" id="mobile_number" value="<?= set_value('mobile_number') ?>">
		</div>
		<span class='label label-important' id='mobile_number_error' style='display:none;'>Mobile Number Field is required.</span>
		
		<label class="control-label" for="last_name"><strong>Email </strong></label>
		<div class="control-label">
			<input type="text" class='span4' placeholder="Email" name="email" id="email" value="<?= set_value('email') ?>">
		</div>
		<span class='label label-important' id='email_error' style='display:none;'>Email Field is required.</span>
		
		<div  class="control-label">
			<label><strong>Quantity <em>*</em></strong></label>
			<input id="quantity" class="span2" type="text" value="" name="quantity" placeholder="1 - 9999" maxlength="4">
		</div>	
		<span class='label label-important' id='quantity_error' style='display:none;'>Quantity must be between 1 to 9999.</span>
		
	</fieldset>
</form>

<script type="text/javascript">
		$("#mobile_number, #quantity").keypress(function (e) {
          if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
                return false;
          }
        });
</script>