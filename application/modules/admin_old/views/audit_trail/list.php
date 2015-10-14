<?php
	$user_tags = '';
	foreach ($users as $user)
	{
		$selected = '';
		if ($user_id == $user->user_id) $selected = "selected='selected'";
		$name = strtoupper($user->first_name . ' ' . $user->last_name);
		$user_tags .= "<option value='{$user->user_id}' {$selected} >{$name}</option>";
	}
?>
<div class='alert alert-info'><h2>Audit Trail</h2></div>
<div>
	<form id='frm_filter' class='form-horizontal' method='get' action ='/admin/audit/view'>
		<fieldset>
			<div class='row-fluid'>
				<div class='span span4'>
					<div class="control-group">
						<label class="control-label" for="use_date_range">From Date:</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input-medium" id="from_date" name='from_date' readonly='readonly' style='cursor:pointer;' />
								<span id='from_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="use_date_range">To Date:</label>
						<div class="controls">
							<div class="input-append">
								<input type="text" class="input-medium" id="to_date" name='to_date' readonly='readonly' style='cursor:pointer;' />
								<span id='to_date_icon' class="add-on" style='cursor:pointer;'><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
				</div>
				<div class='span span4'>
					<div class="control-group">
						<label class="control-label" for="use_date_range">Section:</label>
						<div class="controls">
							<select id="section" name='section'>
								<option value='admin'>Admin</option>
								<option value='inventory'>Inventory</option>
								<option value='members'>Members</option>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="use_date_range">User:</label>
						<div class="controls">
							<select id="user_id" name='user_id'>
								<option value='0'>All</option>
								<?= $user_tags ?>
							</select>
						</div>
					</div>
				</div>
				<div class="span span3">
					<div class="control-group">
						<label class="control-label" for="use_date_range">Action:</label>
						<div class="controls">
							<select id="action" name='action'>
								<option value='all'>All</option>
								<option value='add'>ADD</option>
								<option value='update'>UPDATE</option>
								<option value='delete'>DELETE</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button class='btn btn-primary'>&nbsp;&nbsp;Go&nbsp;&nbsp;</button>
					<button id='btn_today' class='btn btn-info'>Today</button>
					<a id='btn_download' href='#' target='_blank' class='btn btn-success' style='margin-left:20px;'>Download</a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th>Name</th>
			<th>Module Name</th>
			<th>Table Name</th>
			<th>Action</th>
			<th>Details Before</th>
			<th>Details After</th>
			<th>Timestamp</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($audit_logs)): ?>
		<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($audit_logs as $l): ?>
		<tr>			
			<td><?= $l->name; ?></td>
			<td><?= $l->module_name; ?></td>
			<td><?= $l->table_name; ?></td>
			<td><?= $l->action?></td>
			<td><?= $l->decoded_details_before?></td>
			<td><?= $l->decoded_details_after?></td>
			<td><?= $l->insert_timestamp; ?></td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>

<script type="text/javascript">
	$(function() {
		
		$("#from_date").datepicker({
            'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
            timeFormat: 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd') + ' 12:00 am');
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd h:M:s tt'));
			$('#frm_filter').submit();
		});
		
		var dl_url = b.uri.path + (b.uri.query.length > 0 ? '?' + b.uri.query + '&export=excel' : '?export=excel');
		$('#btn_download').attr('href', dl_url);

	});
</script>