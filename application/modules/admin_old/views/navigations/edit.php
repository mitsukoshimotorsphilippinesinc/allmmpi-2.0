<?php if (empty($navigation)): ?>
	<h3>Navigation not found.</h3>
<?php else: ?>

<form action='/admin/navigations/edit/<?= $navigation->navigation_id ?>' method='post' class='form-inline'>
<fieldset >
	<div class="control-group <?= $this->form_validation->error_class('type') ?>">
		<label class="control-label" for="type"><strong>Type <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Please Select Type', 'LINK' => 'Link', 'HEADER' => 'Header');
				$extra = "id='type' class='span3'";
				echo form_dropdown('type', $options, set_value('type',$navigation->type), $extra);

			?>
		</div>
		<span class='label label-important' id='type_error' style='display:none;'>Type Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('system_code') ?>">
		<input type="hidden" id="orig_system_code" name="orig_system_code" value="<?= $navigation->system_code; ?>">
		<label class="control-label" for="system_code"><strong>System <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Select a System');
				
				foreach($systems as $s)
					$options[$s->code] = $s->pretty_name;
				$extra = "id='system_code' class='span3'";
				echo form_dropdown('system_code', $options, set_value('system_code',$navigation->system_code), $extra);

			?>
		</div>
		<span class='label label-important' id='system_code_error' style='display:none;'>System Code Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('parent_id'); ?>">
		<label class="control-label" for="parent_id"><strong>Section <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Select a Section','0' => 'None' );
				$extra = "id='parent_id' class='span3'";
				echo form_dropdown('parent_id', $options, set_value('parent_id',$navigation->parent_id), $extra);

			?>		
		</div>
		<div id="hidden_parent_id">
		</div>
		<span class='label label-important' id='parent_id_error' style='display:none;'>Section Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('code') ?>">
		<label class="control-label" for="code"><strong>Navigation Code <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Navigation Code" name="code" id="code" value="<?= set_value('code',$navigation->code) ?>">			
		</div>
		<span class='label label-important' id='code_error' style='display:none;'>Navigation Code Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('title') ?>">
		<label class="control-label" for="title"><strong>Name <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Name" name="title" id="title" value="<?= set_value('title',$navigation->title) ?>">		
		</div>
		<span class='label label-important' id='title_error' style='display:none;'>Name Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('url') ?>">
		<label class="control-label" for="url"><strong>Link <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class='span4' placeholder="Link" name="url" id="url" value="<?= set_value('url',$navigation->url) ?>">		
		</div>
		<span class='label label-important' id='url_error' style='display:none;'>Link Field is required.</span>
	</div>
	<div class="control-group <?= $this->form_validation->error_class('is_active') ?>">
		<label class="control-label" for="is_active"><strong>Is Active? <em>*</em></strong></label>
		<div class="controls">
			<?php

				$options = array('' => 'Choose', TRUE => 'Yes', FALSE => 'No');
				$extra = "id='is_active' class='span3'";
				echo form_dropdown('is_active', $options, set_value('is_active',$navigation->is_active), $extra);

			?>			
		</div>
		<span class='label label-important' id='is_active_error' style='display:none;'>Is Active Field is required.</span>
	</div>
</fieldset>
</form>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function(){
		var s_code = $('#system_code').val();
		
		if($("#type").val() == "HEADER")
		{			
			$("#type").trigger('change');
		}
		
		$("#system_code").trigger('change');
		
	});


	$("#type").change(function(){
		if($(this).val() == "HEADER")
		{
			$("#url").val("#");
			$("#url").attr("readonly","readonly");
			$("#parent_id").val('0');
			$("#parent_id").attr("disabled","disabled");
			$("#hidden_parent_id").html("<input type='hidden' name='parent_id' value='0'>");
		}
		else
		{
			$("#url").val("");
			$("#url").removeAttr("readonly");
			$("#parent_id").val('');
			$("#parent_id").removeAttr("disabled");
			$("#hidden_parent_id").html("");
		}
		
		alert('2');
	});

	$("#system_code").change(function(){

		beyond.request({
			url: '/admin/navigations/get_headers',
			data: {
				'system_code': $(this).val()
			},
			on_success: function(data){
				if(data.status == "ok")
				{
					$("select[id='parent_id']>option").map(function() {
						if(!_.include(['','0'],$(this).val()))
						{
							$("select[id='parent_id'] option[value='"+$(this).val()+"']").remove();
						}
					});

					var headers = data.data;
					var options = "";
					_.each(headers,function(header, key)
					{
						options = options.concat('<option value="'+header.navigation_id+'">'+header.title+'</option>');
					});

					$(options).insertAfter("select[id='parent_id'] option[value='0']");

					$("#parent_id").val('');
					
					var val = '<?= set_value('parent_id',$navigation->parent_id); ?>';
					if(!_.isEmpty(val))
					{
						$("#parent_id").val(val);
					}
				}
			}
		});


	});
</script>