<div>
	Transfer Fund ID <input type="text" class="fund_transfer_id" />
	<button class="btn btn-send-email">Send</button>
</div>
<script type="text/javascript">
	$(document).ready(function(){

		$('.btn-send-email').click(function(){
			var fund_transfer_id = $('.fund_transfer_id').val();
			beyond.request({
				url: '/workbench/email/resend_transfer_fund_email',
				data: {
					'fund_transfer_id': fund_transfer_id
				},
				on_success: function(data){
					
				}
			});
		});

	});
</script>