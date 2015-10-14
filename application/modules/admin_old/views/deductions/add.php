<div class="alert alert-info">
	<h3>
		Create New Member Deduction
		<a class="btn return-btn pull-right">Back to Inventory Dashboard</a>
	</h3>
</div>
<div class="deduction-form">
	<div class="control-group">
		<label class="control-label"><strong>Deduction Type <em>*</em></strong></label>
		<div class="controls">
			<span style="margin-right: 20px;">
				<input type="radio" name="deduction_type" value="individual" checked="checked" />
				Individual
			</span>
			<span>
				<input type="radio" name="deduction_type" value="all" />
				All
			</span>
		</div>
		<span class="help-inline deduction-form-total-amount-help hide"></span>
	</div>

	<div class="control-group control-group-member-selection">
		<label class="control-label"><strong>Select Member <em>*</em></strong></label>
		<div class="controls">
			<div style="margin-bottom: 10px;">
				<span class="label label-success deduction-form-member-view hide"></span>
			</div>
			<input type="hidden" class="deduction-form-member-id" value="">
			<div class="input-append">
				<input type="text" class="get-member-search-text" placeholder="Member Name" />
				<button class="btn btn-get-member-id"><i class="icon-search"></i> Search</button>
			</div>
		</div>
		<span class="help-inline deduction-form-member-id-help hide"></span>
	</div>

	<div class="control-group">
		<label class="control-label"><strong>Total Amount <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class="deduction-form-total-amount" value="">
		</div>
		<span class="help-inline deduction-form-total-amount-help hide"></span>
	</div>

	<div class="control-group hide">
		<label class="control-label"><strong>Amount Due <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class="deduction-form-amount-due" value="">
		</div>
		<span class="help-inline deduction-form-amount-due-help hide"></span>
	</div>

	<div class="control-group hide">
		<label class="control-label"><strong>Deduction per Payout <em>*</em></strong></label>
		<div class="controls">
			<input type="text" class="deduction-form-deduction-per-payout" value="">
		</div>
		<span class="help-inline deduction-form-deduction-per-payout-help hide"></span>
	</div>

	<div class="control-group">
		<label class="control-label"><strong>Terms <em>*</em> (number of deductions per payout)</strong></label>
		<div class="controls">
			<input type="text" class="deduction-form-terms span1" value="1">
		</div>
		<span class="help-inline deduction-form-terms-help hide"></span>
	</div>

	<div class="control-group">
		<label class="control-label"><strong>Remarks</strong></label>
		<div class="controls">
			<textarea type="text" class="deduction-form-remarks span5" rows="4"></textarea>
		</div>
		<span class="help-inline deduction-form-remarks-help hide"></span>
	</div>

	<div class="controls" align="right">
		<a class="btn btn-primary btn-save-member-deduction">Save Member Deduction</a>
		<a class="btn return-btn">Cancel</a>
	</div>
</div>

<script type="text/javascript">
	var root = this;
	root['deduction_type'] = "individual";
	$(document).ready(function(){
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

		$('.return-btn').click(function(){
			redirect('/admin/members_deductions');
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

		$('.btn-save-member-deduction').click(function(){
			var validate = validateDeductionForm();
			if(validate[0]) {
				var save_deduction_modal = beyond.modal.new({
					title: 'Save Member Deduction',
					width: 600,
					disableClose: true,
					html: 'Are you sure you want to save this?',
					buttons : {
						'No' : function(){
							save_deduction_modal.hide();
						},
						'Yes' : function(){
							save_deduction_modal.hide();

							var url = (deduction_type == "individual")?"add":"add_to_all";
							var member_id = $('.deduction-form-member-id').val();
							var total_amount = $('.deduction-form-total-amount').val();
							var amount_due = $('.deduction-form-amount-due').val();
							var deduction_per_payout = $('.deduction-form-deduction-per-payout').val();
							var terms = $('.deduction-form-terms').val();
							var remarks = $('.deduction-form-remarks').val();
							beyond.request({
								url : '/admin/members_deductions/' + url,
								data : {
									'member_id': member_id,
									'total_amount': total_amount,
									'amount_due': amount_due,
									'deduction_per_payout': deduction_per_payout,
									'terms': terms,
									'remarks': remarks
								},
								on_success : function(data){
									if(data.status == 1){
										redirect('/admin/members_deductions');
									}
								}
							});
						}
					}
				});
				save_deduction_modal.show();
			} else {
				var save_error_deduction_modal = beyond.modal.new({
					title: 'Save Member Deduction',
					width: 400,
					html: validate[1]
				});
				save_error_deduction_modal.show();
			}
		});
	});

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