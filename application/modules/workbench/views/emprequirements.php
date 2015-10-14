
<div class='alert alert-danger'><h2>Populate pm_employee_requirement Table<a class='btn btn-small btn-default'id="populate-btn" style="float:right;" title='Download'>GO</a></h2></div>

<div id="result">
</div>

<script type="text/javascript">


	$("#populate-btn").click(function(){
		
		b.request({
			url: "/workbench/populate_emprequirements/process",
			data: {
			},
			on_success: function(data){
				var xls_modal = b.modal.new({});
				if(data.status == "1")
				{
					alert("OK!");
				} else {
					alert("ERROR!");
				}
			},
			on_error: function(){				
			}
		});
		
	});
	
</script>