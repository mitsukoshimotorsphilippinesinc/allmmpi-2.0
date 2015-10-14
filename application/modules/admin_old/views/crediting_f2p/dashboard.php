<style type="text/css">
	.crediting-container {
		margin: 20px;
		padding: 10px;
	}
</style>

<div class="crediting-container well">
	<div>[download funds to paycard crediting template here]</div>
	<div><a href="<?= site_url('/assets/media/uploads/funds_to_paycard_(date)_PROCESSED.xlsx') ?>" class="btn btn-success"><i class="icon-download icon-white"></i> Download</a></div>
</div>

<div class="crediting-container well">
	<form action="<?= site_url('/admin/crediting/upload') ?>" method="POST" enctype="multipart/form-data" class="form form-horizontal">
		<div class="control-group">
			<label class="control-label">Upload Funds to Paycard File</label>
			<div class="controls">
				<div class="file_upload"></div>
			</div>
		</div>
	</form>
</div>

<div class="crediting-container well">[history here]</div>

<?php echo js('libs/uploadrr.js'); ?>
<script type="text/javascript">
	$('.file_upload').html("");

	$('.file_upload').Uploadrr({
		singleUpload : false,
		progressGIF : '<?= image_path('pr.gif') ?>',
		allowedExtensions: ['.xlsx'],
		target : '<?= site_url('/admin/crediting_f2p/upload') ?>',
		onComplete: function(){
			console.log('complete');
		},
		onError: function(){
			console.log('error');
		}
	});

	$('.fake').css({
		'z-index': '0'
	});
</script>