
<div class='alert alert-info'><h2>Member Deductions
	<a href="members_deductions/add" class="btn btn-small pull-right"><i class="icon-plus"></i> <span>Add New</span></a>
</h2></div>

<button class="btn btn-large btn-primary pull-right btn-download-deductions">Download</button>
<div id="deduction_inset_form"></div>

<div class="input-append">
	<input type="text" class="span5 search-member-name" placeholder="Member Name" />
	<button class="btn btn-primary btn-search-member-name" ><i class="icon-search icon-white"></i> Search</button>
</div>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Name</th>
			<th style="width: 150px;">Total Amount</th>
			<th style="width: 150px;">Amount Due</th>
			<th style="width: 150px;">Deduction per Payout</th>
			<th style="width: 50px;">Terms</th>
			<th>Remarks</th>
			<th style="width: 80px;">&nbsp;</th>
		</tr>
	</thead>
	<tbody class="deduction-table-body">
		<tr>
			<td colspan="6" style="text-align: center; font-weight: bold;">No Deductions Found</center</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	var root = this;
	root['deduction_type'] = "individual";

	$(document).ready(function(){
		root.deduction_type = "individual";

		$('.btn-search-member-name').click(function(){
			getMemberDeductions();
		});

		$('.btn-download-deductions').click(function(){
			$('#deduction_inset_form').html('<form action="/admin/members_deductions/download_deductions" method="post" style="display:none;" id="deduction_download_form"> \
				</form>');
	    	$('#deduction_download_form').submit();
		});

		getMemberDeductions();
	});

	var getMemberDeductions = function(){
		beyond.request({
			url: '/admin/members_deductions/get_member_deductions',
			data: {
				'search_member_name' : $('.search-member-name').val()
			},
			on_success: function(data){
				if(data.status==1){
					if(data.data.count > 0) {
						$('.deduction-table-body').html('');
						for(deductions in data.data.results) {
							var deduct_obj = data.data.results[deductions];
							$('<tr><td>'+deduct_obj.first_name+' '+deduct_obj.middle_name+' '+deduct_obj.last_name+'</td><td style="text-align: right;">'+deduct_obj.total_amount+'</td><td style="text-align: right;">'+deduct_obj.amount_due+'</td><td style="text-align: right;">'+deduct_obj.deduction_per_payout+'</td><td>'+deduct_obj.terms+'</td><td>'+deduct_obj.remarks+'</td><td><button class="btn btn-primary btn-deduction-edit" title="Edit" data-deduction_id="'+deduct_obj.deduction_id+'"><i class="icon-pencil icon-white"></i></button></td></tr>').appendTo('.deduction-table-body');
						}

						$('.btn-deduction-edit').click(function(){
							var deduction_id = $(this).attr('data-deduction_id');
							beyond.request({
								url: '/admin/members_deductions/edit/'+deduction_id,
								on_success: function(data){
									var member_deduction_edit_modal = beyond.modal.new({
										title: 'Edit Member Deduction',
										width: 600,
										html: data.data,
										buttons: {
											'Edit' : function(){
												var validate = validateDeductionForm();
												if(validate[0]) {
													var edit_deduction_modal = beyond.modal.new({
														title: 'Edit Member Deduction',
														width: 600,
														disableClose: true,
														html: 'Are you sure you want to edit this?',
														buttons : {
															'No' : function(){
																edit_deduction_modal.hide();
															},
															'Yes' : function(){
																edit_deduction_modal.hide();

																var is_to_all = (deduction_type == "individual")?0:1;
																var member_id = $('.deduction-form-member-id').val();
																var total_amount = $('.deduction-form-total-amount').val();
																var amount_due = $('.deduction-form-amount-due').val();
																var deduction_per_payout = $('.deduction-form-deduction-per-payout').val();
																var terms = $('.deduction-form-terms').val();
																var remarks = $('.deduction-form-remarks').val();
																beyond.request({
																	url : '/admin/members_deductions/edit',
																	data : {
																		'deduction_id': deduction_id,
																		'member_id': member_id,
																		'total_amount': total_amount,
																		'amount_due': amount_due,
																		'deduction_per_payout': deduction_per_payout,
																		'terms': terms,
																		'remarks': remarks,
																		'is_to_all': is_to_all
																	},
																	on_success : function(data){
																		if(data.status == 1){
																			redirect('/admin/members_deductions');
																		}
																	}
																});
																member_deduction_edit_modal.hide();
															}
														}
													});
													edit_deduction_modal.show();
												} else {
													var edit_error_deduction_modal = beyond.modal.new({
														title: 'Edit Member Deduction',
														width: 400,
														html: validate[1]
													});
													edit_error_deduction_modal.show();
												}
											}
										}
									});
									member_deduction_edit_modal.show();

									deduction_type = "individual";

									$("input:radio[name=deduction_type]").click(function(){
										root.deduction_type = $(this).val();
										if(root.deduction_type == "all") {
											$('.control-group-member-selection').hide();
										} else {
											$('.control-group-member-selection').show();
										}
									});									
									
									$('.deduction-form-total-amount').keyup(function(){
										$('.deduction-form-amount-due').val($(this).val());
										if($('.deduction-form-terms').val() > 0){
											$('.deduction-form-terms-help').removeClass('hide');
											var deduction_per_payout = Math.ceil($('.deduction-form-total-amount').val()/$('.deduction-form-terms').val());
											$('.deduction-form-deduction-per-payout').val(deduction_per_payout);
											$('.deduction-form-terms-help').html('<span class="label label-success">Deduction per Payout: ' + deduction_per_payout + '</span>');
										}
									});

									$('.deduction-form-terms').keyup(function(){
										$('.deduction-form-terms-help').removeClass('hide');
										var deduction_per_payout = Math.ceil($('.deduction-form-total-amount').val()/$(this).val());
										$('.deduction-form-deduction-per-payout').val(deduction_per_payout);
										$('.deduction-form-terms-help').html('<span class="label label-success">Deduction per Payout: ' + deduction_per_payout + '</span>');
									});

									$('.btn-get-member-id').click(function(){
										var search_text = $('.get-member-search-text').val();
										beyond.request({
											url : '/admin/members_deductions/select_member',
											data : {
												'search_text': search_text
											},
											on_success : function(data) {
												if(data.status == 1){
													var return_html = data.data.html;
													var member_selection_modal = beyond.modal.new({
														title: 'Member Selection',
														width: 600,
														html: return_html
													});
													member_selection_modal.show();
													if(data.data.count > 0) {
														$('.btn-member-selection').click(function(){
															var member_id = $(this).attr('data-member-id');
															var member_name = $(this).attr('data-member-name');
															$('.deduction-form-member-id').val(member_id);
															$('.deduction-form-member-view').html("["+member_id+"] "+member_name).removeClass('hide');
															$('.get-member-search-text').val('');
															member_selection_modal.hide();
														});
													} else {
														$('.deduction-form-member-id').val('');
														$('.deduction-form-member-view').html('').addClass('hide');
														$('.get-member-search-text').val('');
													}
												}
											}
										});
									});
								}
							});
						});
					} else {
						$('.deduction-table-body').html('<tr><td colspan="6" style="text-align: center; font-weight: bold;">No Deductions Found</center</td></tr>');
					}
				}
			}
		});
	};

	var validateDeductionForm = function(){
		$('.control-group').each(function(){
			$(this).removeClass('error');
		});

		$('.help-inline').each(function(){
			$(this).addClass('hide');
		});

		var ret = true;
		var msg = "";

		if($('.deduction-form-member-id').val() == "" && root.deduction_type == "individual") {
			$('.deduction-form-member-id').parent().parent().addClass('error');
			$('.deduction-form-member-id-help').html('Member ID is Required').removeClass('hide');
			msg = 'Member ID is Required';
			ret = false;
		}

		if(!is_numeric($('.deduction-form-terms').val())) {
			$('.deduction-form-terms').parent().parent().addClass('error');
			$('.deduction-form-terms-help').html('Invalid Terms').removeClass('hide');
			msg = 'Invalid Terms';
			ret = false;
		}

		if(!is_numeric($('.deduction-form-total-amount').val())) {
			$('.deduction-form-total-amount').parent().parent().addClass('error');
			$('.deduction-form-total-amount-help').html('Invalid Total Amount').removeClass('hide');
			msg = 'Invalid Total Amount';
			ret = false;
		}

		if(!is_numeric($('.deduction-form-amount-due').val())) {
			$('.deduction-form-amount-due').parent().parent().addClass('error');
			$('.deduction-form-amount-due-help').html('Invalid Amount Due').removeClass('hide');
			msg = 'Invalid Amount Due';
			ret = false;
		}

		if(!is_numeric($('.deduction-form-deduction-per-payout').val())) {
			$('.deduction-form-deduction-per-payout').parent().parent().addClass('error');
			$('.deduction-form-deduction-per-payout-help').html('Invalid Deduction per Payout').removeClass('hide');
			msg = 'Invalid Deduction per Payout';
			ret = false;
		}

		return [ret,msg];
	};

	var is_numeric = function(mixed_var){
		return (typeof(mixed_var) === 'number' || typeof(mixed_var) === 'string') && mixed_var !== '' && !isNaN(mixed_var);
	}
</script>