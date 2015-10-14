<div class="page-header clearfix">
	<h2>My Concerns <small></small></h2>
</div>
<form id='frm_filter' class='form-horizontal' method='get' action ='/members/support/view'>
	<fieldset>
		<div class='clearfix'>
			<div class='span6'>
				<div class='control-group'>
					<label for='ticket_id'>Search Ticket ID:</label>
						<input type='text' id='ticket_id' name='ticket_id' value=''/>
						<button class='btn btn-primary' id='filter_results' style='margin-right: 10px;'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
				</div>
			</div>
			<div class='span6'>
				<a class="btn btn-primary" id='add_concern' style='float:right;margin-top:25px' id="btn_add_concern">Submit a Concern</a>
			</div>
		</div>
	</fieldset>
</form>
<div class="ui-element">
	<div>
		<table class='table table-condensed table-striped table-bordered' style="font-size:12px;">
			<thead>
				<tr>
					<th>Ticket ID</th>
					<th>Inquiry Type</th>
					<th>Details</th>	
					<th>Status</th>
					<th>Last Updated</th>								
				</tr>
			</thead>
			<tbody id="order_tbody_html">
				<?php if(empty($member_concerns)): ?>
					<tr><td colspan="10" style="text-align:center;"><strong>No Results Found.</strong></td></tr>
				<?php else: ?>
					<?php foreach($member_concerns as $m): ?>
						<tr> 
							<td><?= str_pad($m->log_id, 6, "0", STR_PAD_LEFT) ?></td>
							<td><?= strtoupper($m->inquiry_type) ?></td>
							<td><?= $m->details ?></td>
							<td><?= strtoupper($m->status)?></td>
							<td><?= $update_timestamp = ($m->update_timestamp == '0000-00-00 00:00:00' ? 'None' : $m->update_timestamp) ?></td> 						
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<div>
	<?= $this->pager->create_links(); ?>
</div>

<script type="text/javascript">
  //<![CDATA[
	var member_id = "<?=$member_id?>";
	
	$('body').on('change', '#inquiry_type', function() {
		var inquiry_type = $("#inquiry_type").val();
		if(inquiry_type == 'Others')
			$("#others_inquiry").show();
		else 
			$("#others_inquiry").hide();
	});
	
	$('body').on('click', '#add_concern', function(){
		var support_types_option = "<label>Inquiry Type</label><?=$inquiry_options?>";
		var inquiry_error = "<span class='label label-important' id='inquiry_error' style='display:none;margin-left:240px'>Please enter an inquiry type.</span>";
		var remarks_html = "<label>Description</label><textarea name='concern_remarks' id='concern_remarks' title='Remarks' class='small' value='' validation='required' style='width:95%;height:100px;' maxlength='255' ></textarea>";
		var remarks_error = "<span class='label label-important' id='desc_error' style='display:none;'>Please enter a short description of your concern.</span>";

		var	dialog_html = support_types_option + inquiry_error + remarks_html + remarks_error;
		var details_html = "";
		var addConcernModal = b.modal.create({
			title: 'Add Concern',
			html: dialog_html,
			width: 500,
			disableClose: true,
			buttons: {
				'Confirm': function() {
					var details = $('#concern_remarks').val();
					var inquiry_type = $("#inquiry_type").val();
					var others_inquiry = $("#variable_inquiry").val();
					var error_count = 0;
					if(details == "") {
						error_count++;
						$("#desc_error").show();
					}
					else {
						$("#desc_error").hide();
					}
					if(inquiry_type == "Others" && others_inquiry == "") {
						error_count++;
						$("#inquiry_error").show();
					}
					else {
						$("#inquiry_error").hide();
					}
					if(error_count == 0)
					{
						var confirm_modal = b.modal.create({
							title: 'Confirm Concern',
							html: 'Are you sure you want to send this concern?',
							width: 300,
							disableClose: true,
							buttons: {
								'Send': function() {
									addConcernModal.hide();
									b.request({
										with_overlay: true,
										url: '/members/support/add_concern',
										data: {
											'member_id' : member_id,
											'details': details,
											'inquiry_type': inquiry_type,
											'others_inquiry': others_inquiry
										},
										on_success: function(data, status) {
											if (data.status == 'ok') {
												confirm_modal.hide();
												var success_modal = b.modal.create({
													title: 'Concern Successfully Sent',
													html: data.msg,
													width: 450
												});
												success_modal.show();
											}

										}
									});
								},
								'Cancel': function() {
									confirm_modal.hide();
								}
							}
						});
						confirm_modal.show();
					}
				},
				'Cancel' : function() {
					addConcernModal.hide();
				}
			}
		});
		addConcernModal.show();
	});
//]]>
</script>