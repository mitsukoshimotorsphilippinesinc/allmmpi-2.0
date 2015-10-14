
<div class='alert alert-danger'><h2>Generate VPN Keys<a class='btn btn-small btn-default'id="populate-btn" style="float:right;" title='Download'>GO</a></h2></div>
<input type="checkbox" name="chkbx" id="chkbx" value="1">Test value
<div id="result">
</div>

<script type="text/javascript">


	$("#populate-btn").click(function(){

		var checkedValue = $('#chkbx:checked').val();

		
		alert(checkedValue);
		
		return;

		
		b.request({
			url: "/workbench/generate_vpn_keys/process",
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