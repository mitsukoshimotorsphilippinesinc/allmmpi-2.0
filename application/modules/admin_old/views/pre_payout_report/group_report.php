<div class="control-group ">
	<label for="start-date" class="control-label"><strong>Start Date</strong></label>
	<div class="controls">
		<div class="input-append" >
			<input title="Start Date" class="input-medium" type="text" id="start-date" name="start-date" value="" readonly="readonly">
			<span id='start-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
		</div>
	</div>
</div>
<div class="control-group ">
	<label for="end-date" class="control-label"><strong>End Date</strong></label>
	<div class="controls">
		<div class="input-append" >
			<input title="End Date" class="input-medium" type="text" id="end-date" name="end-date" value="" readonly="readonly">
			<span id='end-date-icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>					
		</div>
	</div>
</div>

<button id="view_report" class='btn'><span>View Report</span></button>
<script type="text/javascript">
	
	var currDate = new Date();
	var currYear = new Date().getFullYear();
	var yrRange = "2005:" + currYear;

	$("#start-date").datepicker({
		'dateFormat' : "yy-mm-dd",
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});

	$("#start-date").datepicker('setDate', currDate);
	
	$("#start-date-icon").click(function(e) {
		$("#start-date").datepicker("show");
	});

	$("#end-date").datepicker({
		'dateFormat' : "yy-mm-dd",
		'changeYear' : true,
		'yearRange' : yrRange,
		'changeMonth' : true
	});

	var _end_date = new Date();
	_end_date.setDate(_end_date.getDate()+6);
	$("#end-date").datepicker('setDate', _end_date);
	
	$("#end-date-icon").click(function(e) {
		$("#end-date").datepicker("show");
	});
	
	$("#view_report").on('click', function(e) {
		var start_date = $.trim($("#start-date").val());
		var end_date = $.trim($("#end-date").val());
		
		var group_report_modal = b.modal.create({
			title: "Download Group Report",
			html: "Would you like to download the group report?",
			disableClose: true,
			width: 300,
			buttons: {
				"No": function() {
					group_report_modal.hide();
				},
				"Yes": function() {
					download.current_start_date = start_date;
					download.current_end_date = end_date;
					download.init();
					group_report_modal.hide();
				}
			}
		});
		
		group_report_modal.show();
		
	});
	
	var root = this;
	var download = {};
	root['download'] = download;

	download._dlList = [];
	download._pollSpeed = 2000;
	download.currentPollingID = 0;
	download.currentJobID = 0;
	download.current_start_date = "";
	download.current_end_date = "";
	download.form = function() {};
	download.init = function(){
		beyond.request({
			url: '/admin/pre_payout_report/get_groups',
			data: {
				start_date: download.current_start_date,
				end_date: download.current_end_date
			},
			on_success: function(data){
				if(data.status == "ok"){
					download.form = beyond.modal.create({
						title: 'Groups Report Download',
						width: 530,
						html: data.data.html,
						disableClose: true
					});
					download.form.show();
					
					download._dlList = [];
					$('.dl-sheets').each(function(){
						download._dlList.push($(this).attr('data-dl'));
					});
					if(download._dlList.length > 0){
						download.processList();
					} else {
						download.mergeDownload();
					}
				}
			}
		});
	};

	download.processList = function(){
		var group_id = download._dlList.splice(0,1);
		console.log('start_download_job: ' + group_id);
		beyond.request({
			url: '/admin/pre_payout_report/start_group_report_download_job',
			data: {
				'group_id' : group_id[0],
				'start_date': download.current_start_date,
				'end_date': download.current_end_date
			},
			with_overlay: false,
			on_success: function(data){
				if(data.status == "ok"){
					$('.dl-sheets[data-dl="'+group_id+'"] div').html('Processing...').addClass('label-info');
					$('.dl-sheets[data-dl="'+group_id+'"]').addClass('current-job-sheet');
					download.currentJobID = data.data.job_id;
					download.currentPollingID = setInterval(download.pollingCheck, download._pollSpeed);
				}
			}
		});
	};

	download.pollingCheck = function(){
		console.log('polling check: ' + download.currentJobID);
		beyond.request({
			url: '/admin/pre_payout_report/check_group_report_job_status',
			data: {
				'job_id': download.currentJobID
			},
			with_overlay: false,
			on_success: function(data){
				console.log('polling return: ' + data.status);
				
				if(data.status == "ok"){
					$('.current-job-sheet div').html('Completed: ' + data.data.date_generated).attr('class','label label-success');
					$('.current-job-sheet').removeClass('current-job-sheet');
					clearInterval(download.currentPollingID);
					
					if(download._dlList.length > 0){
						console.log("HEY");
						download.processList();
					} else {
						download.mergeDownload();
					}
				}
			}
		});
	};

	download.mergeDownload = function(){
		beyond.request({
			url: '/admin/pre_payout_report/merge_download_segmented_excel',
			data: {
				'start_date': download.current_start_date,
				'end_date': download.current_end_date
			},
			on_success: function(data){
				if(data.status == "ok"){
					download.current_start_date = "";
					download.current_end_date = "";
					download._dlList = [];
					download._pollSpeed = 2000;
					download.currentPollingID = 0;
					download.currentJobID = 0;
					window.location = base_url + '/assets/media/pre_payout/' + data.data.filename;
					download.form.hide();
					/*download.currentJobID = data.data.job_id;
					download.currentPollingID = setInterval(download.mergePollingCheck, download._pollSpeed);*/
				}
			}
		});
	};
	
	download.mergePollingCheck = function(){
		console.log('polling check: ' + download.currentJobID);
		beyond.request({
			url: '/admin/pre_payout_report/check_group_report_job_status',
			data: {
				'job_id': download.currentJobID
			},
			with_overlay: false,
			on_success: function(data){
				console.log('polling return: ' + data.status);
				
				if(data.status == "ok"){
					$('.current-job-sheet div').html('Completed: ' + data.data.date_generated).attr('class','label label-success');
					$('.current-job-sheet').removeClass('current-job-sheet');
					clearInterval(download.currentPollingID);
					
				}
			}
		});
	};
</script>