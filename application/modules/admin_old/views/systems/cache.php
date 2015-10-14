<div class="alert alert-info">
	<h2>Cache Management</h2>
</div>
<div>
	<button id='btn_clear' class='btn btn-large btn-danger'>Clear All Cached Data</button>
</div>

<script type="text/javascript">

	$(function() {
		
		$('#btn_clear').click(function() {
			
			var modal = b.modal.create({
				title: "Cache Confirmation!",
				html: "<p>You are about to clear the system's cache data.</p><p>Are you sure?</p>",
				width: 350,
				disableClose : true,
				buttons : {
					'No' : function() {
						modal.hide();
					},
					'Yes' : function() {
						modal.hide();
						
						b.request({
							'with_overlay': true,
							url: '/admin/cache/clear',
							on_success: function(data, status) {
								b.modal.create({
									title: "Cache Confirmation!",
									html: "<p>Cache cleared successfully!</p>",
									width: 300,
								}).show();
								
							}
						});
						
					},
				}
			});
			
			modal.show();
			
		});
		
		
	});
</script>