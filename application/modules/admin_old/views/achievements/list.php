<div class='alert alert-info'><h2>Member Achievements <a id='btn_add_new_achievements' class='btn btn-small' style='float:right;margin-right:-30px;margin-top:5px;margin-left:5px;'><i class="icon-plus"></i><span> Add New Achievement</span></a>

<a href="/admin/achievements/excel_view" style='float:right;margin-top:5px;margin-left:5px;' class="btn btn-primary btn-small pdf"><i class="icon-print icon-white"></i> Export As Excel</a>
</h2></div>

<div >
	<form id='search_details' method='get' action ='/admin/achievements'>
		<strong>Search By:&nbsp;</strong>
		<select name="search_option" id="search_option_wo" style="width:150px;" value="<?= $search_by ?>">
			<option value="code">Name</option>			
		</select>                 
	
		<input title="Search" class="input-large search-query" style="margin-top:-10px;margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">	

		<button id="button_search" class='btn btn-primary' style="margin-top:-10px;margin-left:5px;"><span>Search</span></button>
		<button id='button_refresh' class='btn' style="margin-top:-10px;"><span>Refresh</span></button>
	
		<br/>
		<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>	
	
		<?php
		if ($search_text == "") {
		?>	
			<div id="search_summary" style="display:none;">
		<?php
		} else {
		?>	
			<div id="search_summary">
		<?php
		};
		?>		
		
			<span class="label label-info">Search Results for:</span>
			<span class="label label-success"><?= $search_by ?></span>
			<span class="label label-success"><?= $search_text ?></span>
		</div>		
	</form>
</div>

<hr/>
<table class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Max Pairs</th>
			<th>Earnings To Upgrade</th>
			<th>Earnings Maintenance</th>
			<th>Date Created</th>
			<th style='width:150px;'>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($achievements)): ?>
		<tr><td colspan='10' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($achievements as $achievements): ?>
		<tr data='<?= $achievements->member_achievement_id ?>'>
			<td><?= $achievements->member_achievement_id; ?></td>
			<td><?= $achievements->achievement_name; ?></td>
			<td><?= $achievements->max_pairs; ?></td>			
			<td><?= $achievements->earnings_to_upgrade; ?></td>
			<td><?= $achievements->earnings_maintenance; ?></td>
			<td><?= $achievements->insert_timestamp; ?></td>			
			<td>							
				<a title='Edit Details' class='btn btn-small btn-primary btn_edit_achievements'><i class="icon-pencil icon-white"></i></a>				
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

	$('#btn_add_new_achievements').live("click",function() {
				
		beyond.request({
			url : '/admin/achievements/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addAchievementModal = b.modal.new({
						title: 'Add New Achievement',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addAchievementModal.hide();
							},
							'Add' : function() {
								var _achievement_name = $('#achievement_name').val();
								var _max_pairs = $('#max_pairs').val();
								var _earnings_to_upgrade = $('#earnings_to_upgrade').val();
								var _earnings_maintenance = $('#earnings_maintenance').val();
								var _remarks = $('#remarks').val();		
								
								var _var_data = _achievement_name + '|' + _max_pairs + '|' + _earnings_to_upgrade + '|' + _earnings_maintenance + '|' + _remarks;
								checkRequiredFields('add', _var_data);
							
								if (hasError == 0) {																	
									confirmAddAchivement(_achievement_name, _max_pairs, _earnings_to_upgrade, _earnings_maintenance, _remarks);
									addAchievementModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addAchievementModal.show();					
				}
			}
		})				
	});

	var confirmAddAchivement = function(achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks) {
		
		beyond.request({
			url : '/admin/achievements/confirm_add',
			data : {
				'_achievement_name' : achievement_name,
				'_max_pairs' : max_pairs,
				'_earnings_to_upgrade' : earnings_to_upgrade,
				'_earnings_maintenance' : earnings_maintenance,
				'_remarks' : remarks
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmaddAchievementModal = b.modal.new({
						title: 'Add New Achievement',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmaddAchievementModal.hide();
							},
							'Yes' : function() {
								addAchievement(achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks);
								confirmaddAchievementModal.hide();
							}
						}
					});
					confirmaddAchievementModal.show();	
				} else {					
					var errorCardTypeModal = b.modal.new({
						title: 'Add New Achievement :: Error',
						disableClose: true,
						html: data.html,
						buttons: {
							'Ok' : function() {
								errorCardTypeModal.hide();
							}
						}
					});
					errorCardTypeModal.show();
				}
			} // end on_success
		})
	};

	var addAchievement = function(achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks) {	
		
		beyond.request({
			url : '/admin/achievements/add_member_achievement',
			data : {
				'_achievement_name' : achievement_name,
				'_max_pairs' : max_pairs,
				'_earnings_to_upgrade' : earnings_to_upgrade,
				'_earnings_maintenance' : earnings_maintenance,
				'_remarks' : remarks
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var addAchievementModal = b.modal.new({
						title: 'Add New Achievement : Successful',
						disableClose: true,
						html: 'You have successfully added a new Achievement',
						buttons: {
							'Ok' : function() {
								addAchievementModal.hide();
								redirect('/admin/achievements');
							}
						}
					});
					addAchievementModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_achievements').live("click",function() {		
		var achievement_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/achievements/edit',
			data : {
					'achievement_id' : achievement_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
				
					// show add form modal					
					var editAchievementModal = b.modal.new({
						title: 'Edit Achievement',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editAchievementModal.hide();
							},
							'Update' : function() {																
								var achievement_name = $('#achievement_name').val();
								var max_pairs = $('#max_pairs').val();
								var earnings_to_upgrade = $('#earnings_to_upgrade').val();
								var earnings_maintenance = $('#earnings_maintenance').val();
								var remarks = $('#remarks').val();		

								var _var_data = achievement_name + '|' + max_pairs + '|' + earnings_to_upgrade + '|' + earnings_maintenance + '|' + remarks;
								checkRequiredFields('edit', _var_data);
								
								if (hasError == 0) {																	
									confirmEditAchivement(achievement_id, achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks);
									editAchievementModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editAchievementModal.show();					
				}
			}
		})	
	});
	
	
	var confirmEditAchivement = function(achievement_id, achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks) {
	
		beyond.request({
			url : '/admin/achievements/confirm_edit',
			data : {
				'_achievement_name' : achievement_name,
				'_max_pairs' : max_pairs,
				'_earnings_to_upgrade' : earnings_to_upgrade,
				'_earnings_maintenance' : earnings_maintenance,
				'_remarks' : remarks
				
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var confirmeditAchievementModal = b.modal.new({
						title: 'Edit Achievement',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmeditAchievementModal.hide();
							},
							'Yes' : function() {
								updateMemberAchievement(achievement_id, achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks);
								confirmeditAchievementModal.hide();
							}
						}
					});
					confirmeditAchievementModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};

	var updateMemberAchievement = function(achievement_id, achievement_name, max_pairs, earnings_to_upgrade, earnings_maintenance, remarks) {	

		beyond.request({
			url : '/admin/achievements/update_achievement',
			data : {
			    '_member_achievement_id' : achievement_id,
				'_achievement_name' : achievement_name,
				'_max_pairs' : max_pairs,
				'_earnings_to_upgrade' : earnings_to_upgrade,
				'_earnings_maintenance' : earnings_maintenance,
				'_remarks' : remarks
			},
			on_success : function(data) {
				if (data.status == "1")	{
				
					var updateMemberAchievementModal = b.modal.new({
						title: 'Edit Achievement :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Member Achievement',
						buttons: {
							'Ok' : function() {
								updateMemberAchievementModal.hide();
								redirect('/admin/achievements');
							}
						}
					});
					updateMemberAchievementModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	var checkRequiredFields = function(action, var_data) {	
		hasError = 0;
		
		if (action == 'edit') {
			var data = var_data.split('|');
			var achievement_name = data[0];	
			var max_pairs = data[1];		
			var earnings_to_upgrade = data[2];	
			var earnings_maintenance = data[3];
			var remarks = data[4];
			
			$('#achievement_name_error').hide();
			$('#max_pairs_error').hide();
			$('#earnings_to_upgrade_error').hide();
			$('#earnings_maintenance_error').hide();
			$('#remarks_error').hide();
			
			if ((max_pairs == '') || (max_pairs == 0)) {
				$('#max_pairs_error').show();
				hasError = 1;
			}
			
			if ((earnings_to_upgrade == '') || (earnings_maintenance == 0)) {
				$('#earnings_to_upgrade_error').show();
				hasError = 1;
			}
		
			if ((earnings_maintenance == '') || (earnings_maintenance == 0)) {
				$('#earnings_maintenance').show();
				hasError = 1;
			}
			
			if ($.trim(remarks) == '') {
				$('#remarks_error').show();
				hasError = 1;
			}		
		}
		return hasError;		
	};
	
	
</script>