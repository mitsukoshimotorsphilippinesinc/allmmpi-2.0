<?php if(1) : ?>
<div>
	<div class="container">
		<div class="alert alert-info alert-executing hide">
			<h4>Notice</h4>
			<div>Executed Job ID <span class="exec-job-id"></span></div>
		</div>
	</div>
	<table class='table table-striped table-bordered'>
		<thead>
			<tr>
				<th>Job ID</th>
				<th>Job Type</th>
				<th>Status</th>
				<th>Exceptions</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($jobs as $job) : ?>
			<tr>
				<td><?php echo $job->job_id; ?></td>
				<td><?php echo $job->job_type; ?></td>
				<td>
					<?php if($job->status == "pending") : ?>
					<span class="label label-success"><?php echo $job->status; ?></span>
					<?php elseif($job->status == "processing") : ?>
					<span class="label label-warning"><?php echo $job->status; ?></span>
					<?php else : ?>
					<span class="label label-important"><?php echo $job->status; ?></span>
					<?php endif; ?>
				</td>
				<td><?php echo $job->exceptions; ?></td>
				<td>
					<?php if($job->status != "completed") : ?>
					<button class="btn btn-run-job" data-job-id="<?php echo $job->job_id; ?>">Run</button>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$('.btn-run-job').click(function(){
		$('.alert-executing').show();
		$('.exec-job-id').html($(this).attr('data-job-id'));
		beyond.request({
			url: "jobs/run_job",
			data: {
				"job_id": $(this).attr('data-job-id')
			},
			on_success: function(data){
				if(data.status == 1)
				{

				}
			}
		});
	});
</script>
<?php endif; ?>
