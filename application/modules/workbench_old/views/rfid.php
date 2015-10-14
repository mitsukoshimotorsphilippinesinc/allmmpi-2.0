<?php  
	echo js('apps/rfid.js'); 
?>

<div class='alert alert-info'><h2>RFID Test</h2></div>

<form class="well form-search" onsubmit='return false;'>
	<input id='txt_search' type="text" class="input-xxlarge search-query">
	<button id='btn_search' class="btn">Search</button>
	<button id='btn_clear' class="btn">Clear</button>
	<button id='btn_scan' class="btn">Scan</button>
</form>

<div id='result' class='well'>
</div>

<script type="text/javascript">

	$(function() {
		
		$('#btn_search').click(function(e) {
			e.preventDefault();
			
			var _val = $.trim($('#txt_search').val());
			$('#result').html(_val);
			
		});
		
		$('#btn_clear').click(function(e) {
			e.preventDefault();
			$('#txt_search').val('');
			$('#result').html('');
			
		});
		
		$('#btn_scan').click(function(e) {
			e.preventDefault();
			
			rfid.scan({
				target_id : 'txt_search',
				on_ok : function(value) {
					var _val = $('#txt_search').val();
					if (_val != value) $('#txt_search').val(value);
					$('#btn_search').click();
				},
				on_cancel : function() {
					
				}
			});
			
		});
		
	});
	
</script>