<div class='alert alert-info'><h2>Card Verification</h2></div>
<div>
	<div style="padding-left:20px;margin-left:50px;">
		Control Code: <input type="text" autofocus="" maxlength="10" value="" name="card_id" id="card_id" style="margin-top:-10px;margin-left:5px;" class="input-large search-query" title="Search">
		<button style="margin-top:-10px;margin-left:5px;" class="btn btn-primary" id="btn-search-card"><span>Search</span></button>
		<button style="margin-top:-10px;margin-left:5px;" class="btn" id="btn-clear-search-card"><span>Clear</span></button>
	</div>
	<div style="padding:20px; margin:20px 50px; border:1px solid #999999; min-height:400px;" >
		<div id="result" style="display:none;"  />
	</div>
</div>
<script id="card-info-template" type="text/html">
<div id="card-result" >
	<div class="alert alert-success">
		<h4>Control Code: <%=card_id%></h4>
	</div>
	<table style="width:100%;" >
		<tr>
			<td >
				<div style="padding:10px 30px;">
					<div class="control-group ">
						<label class="control-label"><strong>Registry Number</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=card_code%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Status</strong></label>
						<div class="controls">							
							<input id="form-status" type="text" readonly="" value="<%=status%>" class="span4">
							<button id="btn-change-status" class="btn btn-primary" style="margin-top:-10px;margin-left:5px;">Change</button>
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Type</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=type%>" class="span4">
						</div>
					</div>
					<div class="control-group ">	
						<label class="control-label"><strong>Owner</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=other_info.member_owner%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Account ID</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=other_info.member_account%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Released To</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=other_info.member_released%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Issued To</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=other_info.member_issued%>" class="span4">
						</div>
					</div>
				</div>
			</td>
			<td>
				<div style="padding:10px 30px;">
					<div class="control-group ">
						<label class="control-label"><strong>Date Created</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=insert_timestamp%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Date Released</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=released_timestamp%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Date Activated</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=activate_timestamp%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Date Issued</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=issued_timestamp%>" class="span4">
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label"><strong>Date Used</strong></label>
						<div class="controls">
							<input type="text" readonly="" value="<%=used_timestamp%>" class="span4">
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	
	
</div>
</script>
<script>
	$(document).ready(function(){
		$("#btn-clear-search-card").on('click', function(e){
			e.preventDefault();
			$("#card_id").val("");
			$("#card-result").remove();
			$("#result").hide();
			return false;
		});
		
		$("#btn-search-card").on('click', function(e){
			var _card_id = $.trim($("#card_id").val());
			beyond.request({
				url: "/admin/card_verification/search",
				data: { 'card_id': _card_id },
				on_success: function(data) {
					if(data.status==1){
						$("#card-result").remove();
						$("#result").html(_.template($('#card-info-template').html(), data.data));
						$("#result").show();
					}else{
						b.modal.create({
							title: "Card Verification Error!",
							html: "<p>"+data.msg+"</p>",
							width: 320,
						}).show();
					}
				}
			});
		});
		
		$("body").on('click', '#btn-change-status', function() {
			//alert(1);
			var _card_id = $.trim($("#card_id").val());
			beyond.request({
				url: "/admin/card_verification/change_status",
				data: { 'card_id': _card_id },
				on_success: function(data) {
					if(data.status==1){
						var changeStatusModal = b.modal.new({
							title: data.data.title,
							disableClose: true,
							html: data.data.html,
							width: 300,
							buttons: {
								'Cancel' : function() {
									changeStatusModal.hide();
								},
								'Update' : function() {
									
									var status_option = $("#status_option").val();
									var status_remarks = $("#status_remarks").val();
									
									proceedChangeStatus(data.data.card_type, _card_id, data.data.status_from, status_option, status_remarks);
									changeStatusModal.hide();
								}
							}
						});
						changeStatusModal.show();	
					
					} else {
						var changeStatusErrorModal = b.modal.new({
							title: data.data.title,
							disableClose: true,
							html: data.data.html,
							width: 400,
							buttons: {
								'Ok' : function() {
									changeStatusErrorModal.hide();
								}
							}
						});
						changeStatusErrorModal.show();						
					}
				}
			});
		});
	
		var proceedChangeStatus = function(_card_type, _card_id, _status_from, _status_to, _status_remarks) {
			beyond.request({
				url : '/admin/card_verification/proceed_change_status',
				data : {
						'card_type' : _card_type,
						'card_id':_card_id,
						'status_from':_status_from,
						'status_to':_status_to,
						'status_remarks':_status_remarks
						},
				on_success : function(data) {
					if (data.status == 1)	{					
						var successUpdateStatusModal = b.modal.new({
							title: data.data.title,
							width: 400,
							disableClose: true,
							html: data.data.html,
							buttons: {
								'Ok' : function() {
									$("#form-status").val(_status_to);
									successUpdateStatusModal.hide();									
								}
							}
						});
						successUpdateStatusModal.show();					
					}
				}
			})
			return false;
		}
		
	});
	
</script>