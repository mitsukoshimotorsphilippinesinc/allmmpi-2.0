<div>
	<div class='alert alert-danger'><h2>LDAP TESTING</h2></div>
	
	<a class='btn btn-small btn-default'id="populate-btn" style="float:right;" title='Go'>GO</a></h2>
</div>	
<script type="text/javascript">

	
	$("#populate-btn").click(function(){
		
		b.request({
			url: "/workbench/ldap_createfile/process",
			data: {
				"department_id" : $("#department").val()
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