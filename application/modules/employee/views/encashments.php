<div class='section-header'>Member ID: <?= $this->member->member_id; ?></div>
<div>
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="get_html active" data="profile"><a href="#profile" data-toggle="tab">Profile</a></li>
			<li class="get_html" data="accounts"><a href="#accounts" data-toggle="tab">Accounts</a></li>
			<li class="get_html" data="vouchers"><a href="#vouchers" data-toggle="tab">Vouchers</a></li>
			<li class="get_html" data="encoding"><a href="#encoding" data-toggle="tab">Encoding</a></li>
        </ul>
		<div class="tab-content">
			<div id="tab_html"></div>
		</div>
    </div>
</div>
<script type="text/javascript">
	
	//js for main dashboard
	$(document).ready(function(){
		$(".get_html.active").trigger("click");
	});
	
	$(".get_html").click(function(){
		var page = $(this).attr("data");
		b.request({
			url: "/members/get_html",
			data: {
				"member_id": <?= $this->member->member_id; ?>,
				"page": page
			},
			on_success: function(data){
				$("#tab_html").html(data.data.html);
				if(data.status == "ok")
				{
					if($(".get_account_earnings").length > 0) $(".get_account_earnings.active").trigger("click");
				}
				
			},
			on_error: function(){
				
			}
		});
	});
	
	//end of js for main dashboard
	
	//js for accounts tab
	$(document).on("click",".get_account_earnings",function(){
		var account_id = $(this).attr("data");
		
		b.request({
			url: "/members/get_account_html",
			data: {
				"member_id": <?= $this->member->member_id; ?>,
				"account_id": account_id
			},
			on_success: function(data){
				$("#account_tab_html").html(data.data.html);
			},
			on_error: function(){
				
			}
		});
	});
	
	$("#submit_dates").click(function(){
		$("#submit_type").submit();
	});
	
	$(document).on("click","#get_earnings",function(){
		var account_id = $(this).attr("data");
		
		beyond.request({
			url: '/members/earnings/get_account_earnings',
			data: {
				'start_date': $("#start_date_transactions").val(),
				'end_date':  $("#end_date_transactions").val(),
				'account_id': account_id,
				'member_id': <?= $this->member->member_id; ?>
			},
			on_success: function(data) {
				if (data.status == 'ok'){
					$("#earnings").html(data.data.html);
				}
				else
				{	
				}
			}
		});
	});
	
	$(document).ready(function(){

		$('#start_date_month').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_day').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});
		$('#start_date_year').change(function() {
			beyond.webcontrol.updateDateControl('start_date');
		});

		$('#start_date_month').trigger('change');
		$('#start_date_day').trigger('change');
		$('#start_date_year').trigger('change');

	});
	
	$(document).ready(function(){

		$('#end_date_month').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_day').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});
		$('#end_date_year').change(function() {
			beyond.webcontrol.updateDateControl('end_date');
		});

		$('#end_date_month').trigger('change');
		$('#end_date_day').trigger('change');
		$('#end_date_year').trigger('change');

	});
	//end of js for accounts tab
</script>