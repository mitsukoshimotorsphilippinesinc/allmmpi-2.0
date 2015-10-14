<style type="text/css">
	.crediting-container {
		margin: 20px;
		padding: 10px;
	}
</style>

<h2>Auto Crediting</h2>
<hr/>
<div class="row-fluid">
	<div class="span6">
		<h3>Download Sample Template</h3>
			<div class="crediting-container" style="margin-left: -50px;">
				<div class="row-fluid form-horizontal">
					<div class="control-group">
						<label class="control-label">Payout Type</label>
						<div class="controls">
								<select class="input input-medium template-payout-type">
									<option value="IGPSM">IGPSM</option>
									<option value="UNILEVEL">UNILEVEL</option>
								</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Start Date</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input input-medium template-start-date" />
								<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">End Date</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input input-medium template-end-date" />
								<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">&nbsp;</label>
						<div class="controls">
							<button class="btn btn-success btn-template-download"><i class="icon-download icon-white"></i> Download Template</button>
						</div>
					</div>			
				</div>
			</div>
	</div>
	<div class="span6">
		<h3>Upload Crediting File</h3>
			<div class="crediting-container" style="margin-left: -50px;">
				<form action="<?= site_url('/admin/crediting/upload') ?>" method="POST" enctype="multipart/form-data" class="form form-horizontal">
					<div class="control-group">
						<label class="control-label">Upload File</label>
						<div class="controls">
							<div class="file_upload"></div>
						</div>
					</div>
				</form>
			</div>
	</div>
</div>

<div>
	<h3>Crediting Log</h3>
	<div class="crediting-logs">
		
	</div>
</div>

<script type="text/template" id="crediting_logs_template">
	<table class="table table-striped table-bordered" style="margin-top: 20px;">
		<thead>
			<tr>
				<th>Payout Type</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Status</th>
				<th>Uploaded By</th>
				<th>Datetime</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% for(i in auto_crediting) { %>
			<tr>
				<td><%= auto_crediting[i].payout_type %></td>
				<td><%= auto_crediting[i].start_date %></td>
				<td><%= auto_crediting[i].end_date %></td>
				<td><%= auto_crediting[i].status %></td>
				<td><%= auto_crediting[i].user_name %></td>
				<td><%= auto_crediting[i].insert_timestamp %></td>
				<td>
					<% if(auto_crediting[i].status == "PENDING") { %>
					<button class="btn btn-success btn-run-crediting" title="Run Crediting" data-id="<%= auto_crediting[i].crediting_id %>"><i class="icon-play icon-white"></i></button>
					<% } %>
					<button class="btn btn-primary btn-crediting-history" title="View Crediting History" data-enddate="<%= auto_crediting[i].end_date %>" data-startdate="<%= auto_crediting[i].start_date %>" data-type="<%= auto_crediting[i].payout_type %>" data-id="<%= auto_crediting[i].crediting_id %>"><i class="icon-list-alt icon-white"></i></button>
					<button class="btn btn-primary btn-download-crediting" title="Download Crediting" data-id="<%= auto_crediting[i].crediting_id %>"><i class="icon-download icon-white"></i></button>
					<button class="btn btn-danger btn-delete-crediting" title="Delete Crediting" data-id="<%= auto_crediting[i].crediting_id %>"><i class="icon-remove icon-white"></i></button>
					
					<% if(auto_crediting[i].status == "COMPLETED") { %>
					<button class="btn btn-warning btn-generate-report" title="Generate Report" data-id="<%= auto_crediting[i].crediting_id %>"><i class="icon-list icon-white"></i></button>
					<% } %>	
				</td>
			</tr>
			<% } %>
		</tbody>
	</table>
</script>

<script type="text/template" id="crediting_log_history_template">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Created By</th>
				<th>Created On</th>
			</tr>
		</thead>
		<tbody>
			<% for(i in crediting_logs) { %>
			<tr>
				<td><%= crediting_logs[i].user_name %></td>
				<td><%= crediting_logs[i].insert_timestamp %></td>
			</tr>
			<% } %>
		</tbody>
	</table>
</script>

<?php echo js('libs/uploadrr.js'); ?>
<script type="text/javascript">
	var load_crediting_log = function(){
		$('.crediting-logs').html('');
		beyond.request({
			url: '/admin/crediting/get_crediting_logs',
			on_success: function(data){
				$('.crediting-logs').html(_.template($('#crediting_logs_template').html(), data.data));
				crediting_log_bindings();
			}
		});
	};

	var crediting_log_bindings = function(){
		$('.btn-download-crediting').click(function(e){
			e.preventDefault();
			var crediting_id = $(this).data('id');

			beyond.request({
				url: '/admin/crediting/download_crediting_log',
				data: {
					crediting_id: crediting_id
				},
				on_success: function(data){
					if(data.status) {
						window.location = data.data.file;
					} else {
						var err_modal = beyond.modal.create({
							title: 'Download Crediting',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});

		$('.btn-run-crediting').click(function(e){
			e.preventDefault();
			var crediting_id = $(this).data('id');

			var confirm_modal = beyond.modal.create({
				title: 'Run Crediting',
				html: 'Are you sure you want to run this crediting?',
				disableClose: true,
				buttons: {
					'No': function(){
						confirm_modal.hide();
					},
					'Yes Credit': function(){
						confirm_modal.hide();
						run_crediting(crediting_id);
						//alert(crediting_id);
					}
				}
			});
			confirm_modal.show();
		});

		$('.btn-delete-crediting').click(function(e){
			e.preventDefault();
			var crediting_id = $(this).data('id');

			var confirm_modal = beyond.modal.create({
				title: 'Delete Crediting Log',
				html: 'Are you sure you want to delete this Crediting Log?',
				disableClose: true,
				buttons: {
					'No': function(){
						confirm_modal.hide();
					},
					'Yes Delete': function(){
						confirm_modal.hide();

						beyond.request({
							url: '/admin/crediting/delete_crediting_log',
							data: {
								crediting_id: crediting_id
							},
							on_success: function(data){
								if(data.status) {
									var success_modal = beyond.modal.create({
										title: 'Delete Crediting Log',
										html: 'Crediting Log was deleted',
										disableClose: true,
										buttons: {
											'Ok': function(){
												success_modal.hide();
												load_crediting_log();
											}
										}
									});
									success_modal.show();
								} else {
									var err_modal = beyond.modal.create({
										title: 'Delete Crediting Log',
										html: data.msg
									});
									err_modal.show();
								}
							}
						});
					}
				}
			});
			confirm_modal.show();
		});

		$('.btn-crediting-history').click(function(e){
			e.preventDefault();
			var crediting_id = $(this).data('id');
			var crediting_type = $(this).data('type');
			var start_date = $(this).data('startdate').split(" ")[0];
			var end_date = $(this).data('enddate').split(" ")[0];

			beyond.request({
				url: '/admin/crediting/get_crediting_history',
				data: {
					crediting_id: crediting_id
				},
				on_success: function(data){
					if(data.status) {
						var credit_history_modal = beyond.modal.create({
							title: 'Crediting History ' + crediting_type + ' ' + start_date + ' to ' + end_date,
							html: _.template($('#crediting_log_history_template').html(), data.data),
							// buttons: {
							// 	'Rerun': function(){
							// 		var confirm_modal = beyond.modal.create({
							// 			title: 'Rerun Crediting',
							// 			html: '<b>WARNING:</b> Are you sure you want to rerun this crediting?',
							// 			disableClose: true,
							// 			buttons: {
							// 				'No': function(){
							// 					confirm_modal.hide();
							// 				},
							// 				'Yes Rerun Crediting': function(){
							// 					confirm_modal.hide();
							// 					run_crediting(crediting_id);
							// 				}
							// 			}
							// 		});
							// 		confirm_modal.show();
							// 	}
							// }
						});
						credit_history_modal.show();
					} else {
						var err_modal = beyond.modal.create({
							title: 'Crediting History',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});

		$('.btn-generate-report').click(function(e){
			e.preventDefault();
			var crediting_id = $(this).data('id');

			beyond.request({
				url: '/admin/crediting/check_report_generation',
				data: {
					crediting_id: crediting_id
				},
				on_success: function(data){					
					if(data.status == "1") {

						var success_modal = beyond.modal.create({
							title: 'Generate Report :: Confirm',
							html: data.data.html,
							disableClose: true,
							buttons: {
								'Proceed': function(){
									success_modal.hide();
									redirect('/admin/crediting/export_xls/'+ crediting_id);
								}, 
								'Cancel': function(){
									success_modal.hide();			
								}
							}
						});
						success_modal.show();

						
					} else {
						var err_modal = beyond.modal.create({
							title: 'Generate Report',
							html: data.msg
						});
						err_modal.show();
					}
				}
			});
		});

	};

	var run_crediting = function(crediting_id){
		beyond.request({
			url: '/admin/crediting/run_crediting',
			data: {
				crediting_id: crediting_id
			},
			on_success: function(data) {
				if(data.status) {
					var success_modal = beyond.modal.create({
						title: 'Run Crediting',
						html: 'Crediting was successful',
						disableClose: true,
						buttons: {
							'Ok': function(){
								success_modal.hide();
								//load_crediting_log();
								window.location = "<?= site_url('/admin/crediting') ?>";
							}
						}
					});
					success_modal.show();
				} else {
					var err_modal = beyond.modal.create({
						title: 'Run Crediting',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});	
	};


	$(document).ready(function(){

		load_crediting_log();

		$('.btn-template-download').click(function(e){
			e.preventDefault();
			var payout_type = $('.template-payout-type').val();
			var start_date = $('.template-start-date').val();
			var end_date = $('.template-end-date').val();
            
            // check first if the selected date is a valid payout period
            beyond.request({
    			url: '/admin/crediting/check_payout_period',
    			data: {
    				'payout_type': payout_type,
                    'start_date': start_date,
                    'end_date': end_date
    			},
    			on_success: function(data) {
    				if(data.status) {   				
 					   window.location = "<?= site_url('/admin/crediting/get_template') ?>/" + payout_type + '/' + start_date + '/' + end_date;
    				} else {
    					var err_modal = beyond.modal.create({
    						title: 'Crediting :: Error',
    						html: data.msg
    					});
    					err_modal.show();
    				}
    			}
    		});

			
		});

		var currDate = new Date();
		var currYear = new Date().getFullYear();
		var yrRange = "2005:" + currYear;

		$(".template-start-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		$(".template-start-date").datepicker('setDate', currDate);
		
		$("#start-date-icon").click(function(e) {
			$(".template-start-date").datepicker("show");
		});

		$(".template-end-date").datepicker({
			'dateFormat' : "yy-mm-dd",
			'changeYear' : true,
			'yearRange' : yrRange,
			'changeMonth' : true
		});

		var _end_date = new Date();
		_end_date.setDate(_end_date.getDate()+6);
		$(".template-end-date").datepicker('setDate', _end_date);
		
		$("#end-date-icon").click(function(e) {
			$(".template-end-date").datepicker("show");
		});

	});

	$('.file_upload').html("");

	$('.file_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.xlsx'],
		target : '<?= site_url('/admin/crediting/upload') ?>',
		onComplete: function(){
			console.log('complete');
			load_crediting_log();
		},
		onError: function(e){
			console.log(e);
			console.log('error');
			
			autoCreditingModal = b.modal.new({
				title: "Auto Crediting :: Error",
				width:450,
				disableClose: true,
				//html: e,
                html: "[ERR3] Invalid Payout Period. Please check the Start Date and End Date on your attachment filename.",
				buttons: {
					'Reset Upload' : function() {
						autoCreditingModal.hide();
                        redirect('admin/crediting');
                        
                        
					}
				}
			});
			autoCreditingModal.show();		
			
		}
	});

	$('.fake').css({
		'z-index': '0'
	});
</script>	
