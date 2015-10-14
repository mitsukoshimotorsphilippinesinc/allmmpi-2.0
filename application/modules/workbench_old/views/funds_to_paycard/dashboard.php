<div class="span4">
	
	<div>
		<label>Test:</label>
	</div>
	<div>
		<select id="adjustmenttype">
			<option value="FUNDSTOPAYCARD" selected="selected">FUNDS TO PAYCARD</option>
			<option value="ADJUSTMENTS">ADJUSTMENTS</option>
			<option value="DEDUCTIONS">DEDUCTIONS</option>
			<option value="DEDUCTIONS-GCEP">DEDUCTIONS-GCEP</option>
		</select>
	</div>
	
	<div>
		<label>Type:</label>
	</div>
	<div>
		<select id="type">
			<option value="ALL">ALL</option>
			<option value="IGPSM" selected="selected">IGPSM</option>
			<option value="UNILEVEL">UNILEVEL</option>
		</select>
	</div>

	<button id="view_report" class='btn'><span>Execute</span></button>
</div>
<script type="text/javascript">
	
	
	
	$("#view_report").on('click', function(e) {
		var type = $.trim($("#type").val());
		var adjustmenttype = $.trim($("#adjustmenttype").val());
		
		
		
		beyond.request({
			url: '/workbench/funds_to_paycard/get_groups',
			data: {
				'type': type,
				'adjustment_type':adjustmenttype
			},
			on_success: function(data){
				if(data.status == "ok"){
					var group_report_modal = b.modal.create({
						title: "Message",
						html: "Successful",
						disableClose: true,
						width: 400,
						buttons: {
							"Ok": function() {
								group_report_modal.hide();
							}
						}
					});
					
					group_report_modal.show();
				} else {
					var errorMessageModal = b.modal.create({
						title: "Message",
						html: data.data.html,
						disableClose: true,
						width: 400,
						buttons: {
							"Ok": function() {
								errorMessageModal.hide();
							}
						}
					});
					
					errorMessageModal.show();
				}
			}
		});
		
	
		
	});
	
	
</script>