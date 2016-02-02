<div>
	<div class='alert alert-danger'><h2>LDAP TESTING</h2></div>	
</div>	

<form action='/workbench/ldap_test/process' method='post' class='form-horizontal'>
	<fieldset>
		<div class="control-group <?= $this->form_validation->error_class('server_ip') ?>">
			<label class="control-label" for="server_ip">Server IP / Domain <em>*</em></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="e.g. 127.0.0.1" name="server_ip" id="server_ip" value="<?= set_value('server_ip') ?>">
				<p class="help-block"><?= $this->form_validation->error('server_ip'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('common_name') ?>">
			<label class="control-label" for="common_name">Common Name (CN) <em>*</em></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="e.g. ryan.rosaldo" name="common_name" id="common_name" value="<?= set_value('common_name') ?>">
				<p class="help-block"><?= $this->form_validation->error('common_name'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('organizational_unit') ?>">
			<label class="control-label" for="organizational_unit">Organizational Unit (OU) <em>*</em></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="e.g. Information_Technology" name="organizational_unit" id="organizational_unit" value="<?= set_value('organizational_unit') ?>">
				<p class="help-block"><?= $this->form_validation->error('organizational_unit'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('domain_component') ?>">
			<label class="control-label" for="domain_component">Domain Component (DC) <em>*</em></label>
			<div class="controls">
				<input type="text" class='span4' placeholder="e.g. mitsukoshimotors" name="domain_component" id="domain_component" value="<?= set_value('domain_component') ?>">
				<p class="help-block"><?= $this->form_validation->error('domain_component'); ?></p>
			</div>
		</div>

		<div class="control-group <?= $this->form_validation->error_class('password') ?>">
			<label class="control-label" for="password">Password <em>*</em></label>
			<div class="controls">
				<input type="password" class='span4' placeholder="e.g. 123456" name="password" id="password" value="<?= set_value('password') ?>">
				<p class="help-block"><?= $this->form_validation->error('password'); ?></p>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary">Go</button>
			</div>
		</div>

</fieldset>
</form>

<script type="text/javascript">

	
	$("#populate-btn").click(function(){
		
		/*b.request({
			url: "/workbench/ldap_createfile/process",
			data: {
				"department_id" : $("#department").val()
			},
			on_success: function(data){
				var xls_modal = b.modal.new({});
				if(data.status == "1")
				{
					alert("OK!");
				} else {
					alert("ERROR!");
				}
			},
			on_error: function(){				
			}
		});*/
		
	});
	
</script>