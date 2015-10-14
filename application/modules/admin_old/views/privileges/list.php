<!--div class='alert alert-info'><h2>Privileges <a class='btn btn-small' href='/admin/privileges/add' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div-->

<div class='alert alert-info'><h2>Privileges <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<div class="tabbable">
	<ul id="privileges_tab" class="nav nav-tabs">
		<li class="active inv_tab"><a href="#inv_tab" data-toggle="tab">Inventory</a></li>
		<li class="net_tab"><a href="#net_tab" data-toggle="tab">MLM Network</a></li>
		<li class="poi_tab"><a href="#poi_tab" data-toggle="tab">Web-POI</a></li>
		<li class="sys_tab"><a href="#sys_tab" data-toggle="tab">System</a></li>
		<li class="site_tab"><a href="#site_tab" data-toggle="tab">Site</a></li>
		<li class="cms_tab"><a href="#cms_tab" data-toggle="tab">CMS</a></li>
		<li class="mis_tab"><a href="#mis_tab" data-toggle="tab">MIS Reports</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="inv_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($inventory)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($inventory as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>						
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="net_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($network)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($network as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="poi_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($webpoi)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($webpoi as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="sys_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($system)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($system as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="site_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($site)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($site as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<!--a href='/admin/privileges/edit/<?= $nav->privilege_id ?>' class='btn btn-small btn-primary'><i class="icon-pencil icon-white"></i></a>
							<a href='/admin/privileges/delete/<?= $nav->privilege_id ?>' class='btn btn-small btn-danger'><i class="icon-trash icon-white"></i></a-->
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="cms_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($cms)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($cms as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="mis_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Code</th>
						<th>Description</th>
						<th>URI</th>
						<th style='width:90px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($mis)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($mis as $nav): ?>
					<tr data='<?= $nav->privilege_id ?>'>
						<td><?= $nav->privilege_code; ?></td>
						<td><?= $nav->privilege_description; ?></td>
						<td><?= implode('<br/>',json_decode($nav->privilege_uri))?></td>
						<td>
							<a class='btn btn-small btn-primary btn_edit_privilege'><i class="icon-pencil icon-white"></i></a>
							<a class='btn btn-small btn-danger btn_delete_privilege'><i class="icon-trash icon-white"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div>
<?= $this->pager->create_links();  ?>
</div>

<script type="text/javascript">
	$('#btn_add_new').live("click",function() {		
		beyond.request({
			url : '/admin/privileges/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addPrivilegeModal = b.modal.new({
						title: 'Add New Privilege',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addPrivilegeModal.hide();
							},
							'Add' : function() {
								var _system_code = $('#system_code').val();
								var _privilege_code = $('#privilege_code').val();
								var _privilege_description = $('#privilege_description').val();
								var _privilege_uri = $('#privilege_uri').val();
															
								checkRequiredFields(_system_code, _privilege_code, _privilege_description, _privilege_uri);
								
								if (hasError == 0) {																	
									confirmAddPrivilege(_system_code, _privilege_code, _privilege_description, _privilege_uri);
									addPrivilegeModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addPrivilegeModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddPrivilege = function(system_code, privilege_code, privilege_description, privilege_uri) {
		beyond.request({
			url : '/admin/privileges/confirm_add',
			data : {
				'_system_code' : system_code,
				'_privilege_code' : privilege_code,
				'_privilege_description' : privilege_description,
				'_privilege_uri' : privilege_uri
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddPrivilegeModal = b.modal.new({
						title: 'Add New Privilege',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddPrivilegeModal.hide();
							},
							'Yes' : function() {
								addPrivilege(system_code, privilege_code, privilege_description, privilege_uri);
								confirmAddPrivilegeModal.hide();
							}
						}
					});
					confirmAddPrivilegeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var addPrivilege = function(system_code, privilege_code, privilege_description, privilege_uri) {	
		beyond.request({
			url : '/admin/privileges/add_privilege',
			data : {
				'_system_code' : system_code,
				'_privilege_code' : privilege_code,
				'_privilege_description' : privilege_description,
				'_privilege_uri' : privilege_uri
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addPrivilegeModal = b.modal.new({
						title: 'Add New Privilege : Successful',
						disableClose: true,
						html: 'You have successfully added a new Privilege',
						buttons: {
							'Ok' : function() {
								addPrivilegeModal.hide();
								redirect('/admin/privileges');
							}
						}
					});
					addPrivilegeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_privilege').live("click",function() {		
		var _privilege_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/privileges/edit',
			data : {
					'_privilege_id' : _privilege_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editPrivilegeModal = b.modal.new({
						title: 'Edit Privilege',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editPrivilegeModal.hide();
							},
							'Update' : function() {
								var _system_code = $('#system_code').val();
								var _privilege_code = $('#privilege_code').val();
								var _privilege_description = $('#privilege_description').val();
								var _privilege_uri = $('#privilege_uri').val();
															
								checkRequiredFields(_system_code, _privilege_code, _privilege_description, _privilege_uri);
								
								if (hasError == 0) {																	
									confirmEditPrivilege(_system_code, _privilege_code, _privilege_description, _privilege_uri, _privilege_id);
									editPrivilegeModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editPrivilegeModal.show();					
				}
			}
		})
		return false;		
	});
	
	var confirmEditPrivilege = function(system_code, privilege_code, privilege_description, privilege_uri, privilege_id) {
		
		beyond.request({
			url : '/admin/privileges/confirm_edit',
			data : {
				'_system_code' : system_code,
				'_privilege_code' : privilege_code,
				'_privilege_description' : privilege_description,
				'_privilege_uri' : privilege_uri,
				'_privilege_id' : privilege_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditPrivilegeModal = b.modal.new({
						title: 'Edit Privilege',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditPrivilegeModal.hide();
							},
							'Yes' : function() {
								updatePrivilege(system_code, privilege_code, privilege_description, privilege_uri, privilege_id);
								confirmEditPrivilegeModal.hide();
							}
						}
					});
					confirmEditPrivilegeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var updatePrivilege = function(system_code, privilege_code, privilege_description, privilege_uri, privilege_id) {	
		beyond.request({
			url : '/admin/privileges/update_privilege',
			data : {
				'_system_code' : system_code,
				'_privilege_code' : privilege_code,
				'_privilege_description' : privilege_description,
				'_privilege_uri' : privilege_uri,
				'_privilege_id' : privilege_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var updateItemModal = b.modal.new({
						title: 'Edit Privilege :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Privilege',
						buttons: {
							'Ok' : function() {
								updateItemModal.hide();
								redirect('/admin/privileges');
							}
						}
					});
					updateItemModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_delete_privilege').live("click",function() {		
		var _privilege_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/privileges/delete',
			data : {
					'_privilege_id' : _privilege_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deletePrivilegeModal = b.modal.new({
						title: 'Delete Privilege',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deletePrivilegeModal.hide();
							},
							'Delete' : function() {																															
								confirmDeletePrivilege(_privilege_id);
								deletePrivilegeModal.hide();																
							}
						}
					});
					deletePrivilegeModal.show();					
				}
			}
		})
		return false;
	});
	
	
	
	var confirmDeletePrivilege = function(privilege_id) {
		beyond.request({
			url : '/admin/privileges/confirm_delete',
			data : {
				'_privilege_id' : privilege_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeletePrivilegeModal = b.modal.new({
						title: 'Delete Privilege',
						disableClose: true,
						html: data.html,
						width: 300,
						buttons: {
							'No' : function() {
								confirmDeletePrivilegeModal.hide();
							},
							'Yes' : function() {
								deletePrivilege(privilege_id);
								confirmDeletePrivilegeModal.hide();
							}
						}
					});
					confirmDeletePrivilegeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deletePrivilege = function(privilege_id) {	
		beyond.request({
			url : '/admin/privileges/delete_privilege',
			data : {
				'_privilege_id' : privilege_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deletePrivilegeModal = b.modal.new({
						title: 'Delete Privilege :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Privilege',
						buttons: {
							'Ok' : function() {
								deletePrivilegeModal.hide();
								redirect('/admin/privileges');
							}
						}
					});
					deletePrivilegeModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	
	
	var checkRequiredFields = function(system_code, privilege_code, privilege_description, privilege_uri) {
		hasError = 0;
		$('#system_code_error').hide();
		$('#privilege_code_error').hide();
		$('#privilege_description_error').hide();
		$('#privilege_uri_error').hide();
		
		if (system_code == '') {
			$('#system_code_error').show();
			hasError = 1;
		}
		
		if (privilege_code == '') {
			$('#privilege_code_error').show();
			hasError = 1;
		}
		
		if (privilege_description == '') {
			$('#privilege_description_error').show();
			hasError = 1;
		}
		
		if (privilege_uri == '') {
			$('#privilege_uri_error').show();
			hasError = 1;
		}
	
	
		return hasError;
	};
	
</script>