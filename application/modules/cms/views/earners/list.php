<?php
	$top_thirty = array();
	$top_seventy = array();

	foreach($top_earners as $t)
	{
		if($t->earner_type_id == 1)
		{
			array_push($top_thirty,$t);
		}
		elseif($t->earner_type_id == 2)
		{
			array_push($top_seventy,$t);
		}
	}
	
?>
<style>
th.rank {
	width: 50px;
}
th.actions {
	width: 35px;
}
</style>
<div>
	<div class="alert alert-info">	
		<h2 id="header_text">Top Earners
		</h2>
	</div>
	<div id="main_page_container" >
		<div id="search_container">
			<form id='search_details' method='get' action ='/cms/earners' class="form-inline">
				<strong>Search By:&nbsp;</strong>
				<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
					<option value="name">Name</option>
				</select>
				<input title="Search" class="input-large search-query" style="margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">

				<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Search</span></button>
				<a id='button_refresh' class='btn' style="margin-top:0;"><span>Refresh</span></a>

				<br/>
				<span id="search_error" class="label label-important" style="display:none">Search String must not be empty.</span>

				<?php if ($search_text == ""): ?>
					<div id="search_summary" style="display:none;">
				<?php else: ?>
					<div id="search_summary">
				<?php endif; ?>

					<span class="label label-info">Search Results for:</span>
					<span class="label label-success"><?= $search_by ?></span>
					<span class="label label-success"><?= $search_text ?></span>
				</div>
			</form>
		</div>
		<div class="featured_members_list">
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#thirty" data-toggle="tab">Monthly Top Thirty</a></li>
					<li><a href="#seventy" data-toggle="tab">Overall Top Seventy</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="thirty">
						<table class="table table-bordered table-condensed table-striped">
							<thead>
								<tr>
									<th class="rank">Rank</th>
									<th>Member Name</th>
									<th>Group Name</th>
									<th class="actions">&nbsp;</th>
								<tr>
							</thead>
							<tbody>
								<?php if(empty($top_thirty)):?>
									<tr><td colspan="10" style='text-align:center;'><strong>No Records Found</strong></td></tr>
								<?php else: ?>
									<?php foreach($top_thirty as $t): ?>
									<tr>
										<td><?= $t->position; ?></td>
										<td><?= $t->member_name; ?></td>
										<td><?= $t->group_name; ?></td>
										<td>
											<a data='<?= $t->earner_id ?>' data-rank="<?= $t->position ?>" data-type="monthly" class='btn btn-small btn-primary edit_rank' title="Delete"><i class="icon-pencil icon-white"></i></a>
										</td>
									</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
					<div class="tab-pane" id="seventy">
						<table class="table table-bordered table-condensed table-striped">
							<thead>
								<tr>
									<th class="rank">Rank</th>
									<th>Member Name</th>
									<th>Group Name</th>
									<th class="actions">&nbsp;</th>
								<tr>
							</thead>
							<tbody>
								<?php if(empty($top_seventy)): ?>
									<tr><td colspan="10" style='text-align:center;'><strong>No Records Found</strong></td></tr>
								<?php else: ?>
									<?php foreach($top_seventy as $t): ?>
									<tr>
										<td><?= $t->position; ?></td>
										<td><?= $t->member_name; ?></td>
										<td><?= $t->group_name; ?></td>
										<td>
											<a data='<?= $t->earner_id ?>' data-rank="<?= $t->position ?>" data-type="overall" class='btn btn-small btn-primary edit_rank' title="Delete"><i class="icon-pencil icon-white"></i></a>
										</td>
									</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class='pagination_html'>
		<?= $this->pager->create_links(); ?>
	</div>
</div>				
	
<script type="text/javascript">
  //<![CDATA[
	
	$("#button_search").live("click",function() {
		var _search_string = $.trim($("#search_string").val());
        var _search_option = $("#search_option").val();
		
		if (_search_string == '') {
			$("#search_error").show();
			$("#search_summary").hide();
		} else {
			$("#search_details").submit();
			$("#search_error").hide(); 
			$("#search_summary").show();           
		}
		
		return false;
	});
	
	$("#button_refresh").live("click",function() {
		redirect('/cms/earners');
	});
	
	
	$(document).on('click', ".edit_rank", function() {
		var type = $(this).data("type");
		b.request({
			url: "/cms/earners/edit",
			data: {
				"type": type,
				"earner_id": $(this).attr("data"),
				"rank": $(this).data("rank")
			},
			on_success: function(data){
				var title = "";
				if(type == "overall") title = "Overall Top Seventy";
				else if(type == "monthly") title = "Monthly Top Thirty";
				
				var earner_modal = b.modal.new({
					title: title,
					html: data.data.html,
					width: 330,
					disableClose: true,
					buttons: {
						"Cancel": function(){
							earner_modal.hide();
						},
						"Save": function(){
							var earner_id = $("#earner_id").val();
							var member_name = $("#member_name").val();
							var group_name = $("#group_name").val();
							
							var confirm_modal = b.modal.new({
								title: "Confirmation",
								html: "Are you sure you want to save this ranking?",
								disableClose: true,
								width: 300,
								buttons: {
									"No": function(){
										confirm_modal.hide();
									},
									"Yes": function(){
										b.request({
											url: "/cms/earners/update",
											data: {
												"earner_id": earner_id,
												"member_name": member_name,
												"group_name": group_name
											},
											on_success: function(data){
												var result_modal = b.modal.new({});
												if(data.status == "ok")
												{
													result_modal.init({
														title: "Update Success",
														html: data.msg,
														width: 300,
														disableClose: true,
														buttons: {
															"Close": function(){
																result_modal.hide();
																document.location.reload(true);
															}
														}
													});
												}
												
												result_modal.show();
											},
											on_error: function(){
												result_modal.init({
													title: "Error Notification",
													html: "There was an error in your request.",
													width: 300,
													disableClose: true,
													buttons: {
														"Close": function(){
															result_modal.hide();
															document.location.reload(true);
														}
													}
												});
												result_modal.show();
											}
										});
										confirm_modal.hide();
										earner_modal.hide();
									}
								}
							});
							
							confirm_modal.show();
						}
					}
				});
				earner_modal.show();
			}
		});
	});
//]]>
</script>