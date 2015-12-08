<div class="alert alert-danger">
	<h2>Privileges (By Position)<a href='/operations/s4s/s4s_view' class='btn btn-small' style="float:right; margin-top: 5px; margin-right: -30px;"><span> Privilege (By S4S)</span></a></h2>
</div>
<hr/>

<div id="search_container">
	<form id='search_details' method='get' action ='/operations/s4s/position_view/page' class="form-inline">
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
			<option value="position_name">Name</option>			
		</select>
		<input title="Search" class="input-large search-query" style="margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">

		<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Search</span></button>
		<a id='button_refresh' class='btn' style="margin-top:0;"><span>Refresh</span></a>

		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>

		<?php if ($search_text == ""): ?>
			<div id="search_summary" style="display:none;">
		<?php else: ?>
			<br/>
			<div id="search_summary">
		<?php endif; ?>

			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>
	</form>
</div>


<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>			
			<th style='width:30px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($position_list)): ?>
		<tr><td colspan='4' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($position_list as $a): ?>
		<tr>
			<td><?= $a->position_name; ?></td>
			<?php
				// get all s4s under position_id order by priority_id
				$where = "position_id = {$a->position_id}";
				$s4s_position = $this->human_relations_model->get_s4s_position($where);
			?>
			<td>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>S4S Name</th>			
						<th>Status</th>
					</tr>
				</thead>
				<tbody>	
				<?php if(empty($s4s_position)) { ?>
					<tr><td colspan='2' style='text-align:center;'><i>No Assigned Policy/Procedure.</i></td></tr>
				<?php 
					} else {
						foreach ($s4s_position as $sp) {

							// get s4s details						
							$s4s_details = $this->human_relations_model->get_s4s_by_id($sp->s4s_id);

							foreach ($s4s_details as $sdet) {
								echo "<td>{$sdet->pp_name}</td>";
								echo "<td>YES</td>";
							}
							
						}

						
					}	
				?>
				</tbody>	
			</table>		
			</td>
			
			<td>				
				<a href='/operations/s4s/position_view_edit/<?= $a->s4s_id ?>' class='btn btn-small btn-primary' title="Edit"><i class="icon-pencil icon-white"></i></a>				
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<div>
<?= $this->pager->create_links($search_url);  ?>
</div>
	
<script type="text/javascript">
  //<![CDATA[
  	$("#button_refresh").live("click",function() {
		redirect('/operations/s4s');
	});

	$("#button_privileges").live("click",function() {
		var _s4sId = $(this).attr('data');	

		b.request({
			url : '/operations/s4s/select_view',
			data : {
				's4s_id' : _s4sId,				
			},
			on_success : function(data) {
				if (data.status == "1")	{													
					// show add form modal					
					proceedAssignModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
						buttons: {
							'Ok' : function() {
								if ($("#view_status").val() == "position") {
									redirect('operations/s4s/position_view/' + _s4sId);
								} else {
									redirect('operations/s4s/s4s_view/' + _s4sId);
								}	
							}
						}
					});
					proceedAssignModal.show();
				} else {
					proceedAssignModal.hide();					
					errorAssignModal = b.modal.new({
						title: data.data.title,
						width:450,	
						html: data.data.html,
					});
					errorAssignModal.show();
				}												
			} 
		})


	});


</script>  	