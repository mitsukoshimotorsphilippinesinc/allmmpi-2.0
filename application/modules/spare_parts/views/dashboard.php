<h1 style="">Dashboard <small>Spare Parts</small></h1>
<hr/>

<div>	
	<div id="dashboard-figures">	
	</div>	
</div>



<div class='span6'>
	<ol class="breadcrumb"><h3>Charts</h3></ol>
	<div id="pie-chart">			
		<img src="http://chart.apis.google.com/chart?cht=p3&chs=450x200&chd=t:20,20,10,50&chl=Makoto|Lifan|Sym|Other&chtt=Spare%20Parts&chco=D9534F,F0AD4E,5CB85C"></img>
	</div>	
</div>	
<div class="clearfix;"></div>

<div class='span6'>
	<ol class="breadcrumb"><h3>Warehouse Critical Inventory</h3></ol>
	<div id="warehouse-critical-inventory">	
	</div>	
</div>

<div class='span6'>
	<ol class="breadcrumb"><h3>Reservation Requests Count</h3></ol>
	<div id="pending-warehouse-reservation">	
	</div>	
</div>

<div class='span6'>
	<ol class="breadcrumb"><h3>Pending Requests for Approval</h3></ol>
	<div id="pending-approvals">	
	</div>	
</div>

<script type="text/javascript">
	
	$(document).ready(function(){

		//google.load("visualization", "1", {packages:["corechart"]});
		//google.setOnLoadCallback(drawChart);

		/*b.request({
			url : '/spare_parts/pie_chart',
			data : {								
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					$("#pie-chart").html(data.data.html);										
				} else {
				
				}
			},
			on_error : function(data) {
					
			}
		})*/

		// ajax request
		b.request({
			url : '/spare_parts/warehouse_critical_inventory',
			data : {								
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					$("#warehouse-critical-inventory").html(data.data.html);					
					$("#pending-for-approval").html(data.data.html);
				} else {
				
				}
			},
			on_error : function(data) {
					
			}
		})

		b.request({
			url : '/spare_parts/dashboard_figures',
			data : {								
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					$("#dashboard-figures").html(data.data.html);					
				} else {
				
				}
			},
			on_error : function(data) {
					
			}
		})

		b.request({
			url : '/spare_parts/pending_reservations',
			data : {								
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					$("#pending-warehouse-reservation").html(data.data.html);					
				} else {
				
				}
			},
			on_error : function(data) {
					
			}
		})

		/*b.request({
			url : '/spare_parts/pending_approvals',
			data : {								
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					$("#pending-approvals").html(data.data.html);					
				} else {
				
				}
			},
			on_error : function(data) {
					
			}
		})*/
	});

   	
	


</script>