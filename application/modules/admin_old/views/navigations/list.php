<!--div class='alert alert-info'><h2>Navigations <a class='btn btn-small' href='/admin/navigations/add' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div-->

<div class='alert alert-info'><h2>Navigations <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<hr/>
<div class="tabbable">
	<ul id="navigations_tab" class="nav nav-tabs">
		<li class="active inv_tab"><a href="#inv_tab" data-toggle="tab">Inventory</a></li>
		<li class="net_tab"><a href="#net_tab" data-toggle="tab">MLM Network</a></li>
		<li class="pos_tab"><a href="#pos_tab" data-toggle="tab">Web-POI</a></li>
		<li class="sys_tab"><a href="#sys_tab" data-toggle="tab">System</a></li>
		<li class="site_tab"><a href="#site_tab" data-toggle="tab">Site</a></li>
		<li class="cms_tab"><a href="#cms_tab" data-toggle="tab">CMS</a></li>
		<li class='mis_tab'><a href='#mis_tab' data-toggle='tab'>MIS Reports</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="inv_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<!--th style="width:150px;">Ordering</th-->
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($inventory)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($inventory as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $inv_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						
							<!--a href='/admin/navigations/edit/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a href='/admin/navigations/delete/<?= 	$nav->navigation_id ?>' class='btn btn-small btn-danger'><i class="icon-remove icon-white" title="Delete" ></i></a-->
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($network)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($network as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $net_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="pos_tab">
			<table class='table table-striped table-bordered'>
				<thead>
					<tr>
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($webpos)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($webpos as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $pos_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($system)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($system as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $sys_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($site)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($site as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $site_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($cms)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($cms as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $cms_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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
						<th>Title</th>
						<th>System</th>
						<th>Code</th>
						<th>Type</th>
						<th>Link</th>
						<th style="width:71px;">Ordering</th>
						<th style='width:150px;'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php if(empty($mis)): ?>
					<tr>
						<tr><td colspan='7' style='text-align:center;'><strong>No Records Found</strong></td></tr>
					</tr>
				<?php else: ?>
				<?php foreach ($mis as $nav): ?>
					<tr data='<?= 	$nav->navigation_id ?>'>
						<td><?= $nav->title; ?></td>
						<td><?= $nav->system_code; ?></td>
						<td><?= empty($nav->code) ? '&nbsp;' : $nav->code; ?></td>
						<td><?= $nav->type; ?></td>
						<td><?= ($nav->url == '#')? '&nbsp;' : anchor($nav->url,$nav->url); ?></td>
						<td>
							<?php if($nav->ordering > 1): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/up/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Up" ><i class="icon-arrow-up icon-white"></i></a>
							<?php endif; ?>
							<?php if($nav->ordering < $cms_max_order): ?>
							<a href='/admin/navigations/order/<?= $nav->system_code; ?>/down/<?= $nav->navigation_id ?>' class='btn btn-small btn-primary' title="Down" ><i class="icon-arrow-down icon-white"></i></a>
							<?php endif; ?>
						<td>
							<a class='btn btn-small btn-primary btn_edit_navigation'><i class="icon-pencil icon-white" title="Edit" ></i></a>
							<a class='btn btn-small btn-danger btn_delete_navigation'><i class="icon-remove icon-white" title="Delete" ></i></a>
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

	var hash = b.uri.anchor;
	hash = hash.toLowerCase();

	// reset active tab
	$('#navigations_tab li').removeClass('active');
	$('.tab-content .tab-pane').removeClass('active');

	// get selected tab
	if (hash == 'inventory') {
		$('#navigations_tab li.inv_tab').addClass('active');
		$('#inv_tab').addClass('active');
	} else if (hash == 'network') {
		$('#navigations_tab li.net_tab').addClass('active');
		$('#net_tab').addClass('active');
	} else if (hash == 'webpos') {
		$('#navigations_tab li.pos_tab').addClass('active');
		$('#pos_tab').addClass('active');
	} else if (hash == 'system') {
		$('#navigations_tab li.sys_tab').addClass('active');
		$('#sys_tab').addClass('active');
	} else if (hash == 'site') {
		$('#navigations_tab li.site_tab').addClass('active');
		$('#site_tab').addClass('active');
	} else if (hash == 'cms') {
		$('#navigations_tab li.cms_tab').addClass('active');
		$('#cms_tab').addClass('active');
	} else {
		$('#navigations_tab li.inv_tab').addClass('active');
		$('#inv_tab').addClass('active');
	}

	$('#btn_add_new').live("click",function() {	
		
		beyond.request({
			url : '/admin/navigations/add',
			data : {},
			on_success : function(data) {
				if (data.status == "1")	{
					// show add form modal					
					var addNagivationModal = b.modal.new({
						title: 'Add New Navigations',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								addNagivationModal.hide();
							},
							'Add' : function() {
								var _type = $('#type').val();
								var _system_code = $('#system_code').val();
								var _parent_id = $('#parent_id').val();
								var _code = $('#code').val();
								var _title = $('#title').val();
								var _url = $('#url').val();
								var _is_active = $('#is_active').val();
															
								checkRequiredFields(_type, _system_code, _parent_id, _code, _title, _url, _is_active);
								
								if (hasError == 0) {																	
									confirmAddNavigation(_type, _system_code, _parent_id, _code, _title, _url, _is_active);
									addNagivationModal.hide();	
								} // end if (hasError == 0) {								
							}
						}
					});
					addNagivationModal.show();					
				}
			}
		})
		return false;				
	});
	
	var confirmAddNavigation = function(type, system_code, parent_id, code, title, url, is_active) {
		beyond.request({
			url : '/admin/navigations/confirm_add',
			data : {
				'_type' : type,
				'_system_code' : system_code,
				'_parent_id' : parent_id,
				'_code' : code,
				'_title' : title,
				'_url' : url,
				'_is_active' : is_active
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmAddNavigationModal = b.modal.new({
						title: 'Add New Navigation',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmAddNavigationModal.hide();
							},
							'Yes' : function() {
								addNavigation(type, system_code, parent_id, code, title, url, is_active);
								confirmAddNavigationModal.hide();
							}
						}
					});
					confirmAddNavigationModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var addNavigation = function(type, system_code, parent_id, code, title, url, is_active) {	
		beyond.request({
			url : '/admin/navigations/add_navigation',
			data : {
				'_type' : type,
				'_system_code' : system_code,
				'_parent_id' : parent_id,
				'_code' : code,
				'_title' : title,
				'_url' : url,
				'_is_active' : is_active
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var addNavigationModal = b.modal.new({
						title: 'Add New Navigation : Successful',
						disableClose: true,
						html: 'You have successfully added a new Navigation',
						buttons: {
							'Ok' : function() {
								addNavigationModal.hide();
								redirect('/admin/navigations');
							}
						}
					});
					addNavigationModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	$('.btn_edit_navigation').live("click",function() {		
		var _navigation_id = $(this).parent().parent().attr("data");
	
		beyond.request({
			url : '/admin/navigations/edit',
			data : {
					'_navigation_id' : _navigation_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var editNavigationModal = b.modal.new({
						title: 'Edit Navigation',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								editNavigationModal.hide();
							},
							'Update' : function() {
								var _type = $('#type').val();
								var _system_code = $('#system_code').val();
								var _parent_id = $('#parent_id').val();
								var _code = $('#code').val();
								var _title = $('#title').val();
								var _url = $('#url').val();
								var _is_active = $('#is_active').val();
								var _orig_system_code = $('#orig_system_code').val();
															
								checkRequiredFields(_type, _system_code, _parent_id, _code, _title, _url, _is_active);
								
								if (hasError == 0) {																	
									confirmEditNavigation(_type, _system_code, _parent_id, _code, _title, _url, _is_active, _navigation_id, _orig_system_code);
									editNavigationModal.hide();	
								} // end if (hasError == 0) {							
							}
						}
					});
					editNavigationModal.show();					
				}
			}
		})
		return false;		
	});
	
	var confirmEditNavigation = function(type, system_code, parent_id, code, title, url, is_active, navigation_id, orig_system_code) {
		
		beyond.request({
			url : '/admin/navigations/confirm_edit',
			data : {
					'_type' : type,
					'_system_code' : system_code,
					'_parent_id' : parent_id,
					'_code' : code,
					'_title' : title,
					'_url' : url,
					'_is_active' : is_active,
					'_navigation_id' : navigation_id,
					'_orig_system_code' : orig_system_code
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmEditNavigationModal = b.modal.new({
						title: 'Edit Navigation',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmEditNavigationModal.hide();
							},
							'Yes' : function() {
								updateNavigation(type, system_code, parent_id, code, title, url, is_active, navigation_id, orig_system_code);
								confirmEditNavigationModal.hide();
							}
						}
					});
					confirmEditNavigationModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	var updateNavigation = function(type, system_code, parent_id, code, title, url, is_active, navigation_id, orig_system_code) {	
		beyond.request({
			url : '/admin/navigations/update_navigation',
			data : {
				'_type' : type,
				'_system_code' : system_code,
				'_parent_id' : parent_id,
				'_code' : code,
				'_title' : title,
				'_url' : url,
				'_is_active' : is_active,
				'_navigation_id' : navigation_id,
				'_orig_system_code' : orig_system_code
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var updateItemModal = b.modal.new({
						title: 'Edit Navigation :: Successful',
						disableClose: true,
						html: 'You have successfully updated a Navigation',
						buttons: {
							'Ok' : function() {
								updateItemModal.hide();
								redirect('/admin/navigations');
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
	
	$('.btn_delete_navigation').live("click",function() {		
		var _navigation_id = $(this).parent().parent().attr("data");
		
		beyond.request({
			url : '/admin/navigations/delete',
			data : {
					'_navigation_id' : _navigation_id
					},
			on_success : function(data) {
				if (data.status == "1")	{
					
					// show add form modal					
					var deleteNavigationModal = b.modal.new({
						title: 'Delete Navigation',
						width: 400,
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								deleteNavigationModal.hide();
							},
							'Delete' : function() {																															
								confirmDeleteNavigation(_navigation_id);
								deleteNavigationModal.hide();																
							}
						}
					});
					deleteNavigationModal.show();					
				}
			}
		})
		return false;
	});
	
	
	
	var confirmDeleteNavigation = function(navigation_id) {
		beyond.request({
			url : '/admin/navigations/confirm_delete',
			data : {
				'_navigation_id' : navigation_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var confirmDeleteNavigationModal = b.modal.new({
						title: 'Delete Navigation',
						disableClose: true,
						html: data.html,
						buttons: {
							'Cancel' : function() {
								confirmDeleteNavigationModal.hide();
							},
							'Yes' : function() {
								deleteNavigation(navigation_id);
								confirmDeleteNavigationModal.hide();
							}
						}
					});
					confirmDeleteNavigationModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})
	};
	
	
	var deleteNavigation = function(navigation_id) {	
		beyond.request({
			url : '/admin/navigations/delete_navigation',
			data : {
				'_navigation_id' : navigation_id
			},
			on_success : function(data) {
				if (data.status == "1")	{
					
					var deleteNavigationModal = b.modal.new({
						title: 'Delete Navigation :: Successful',
						disableClose: true,
						html: 'You have successfully deleted a Navigation',
						buttons: {
							'Ok' : function() {
								deleteNavigationModal.hide();
								redirect('/admin/navigations');
							}
						}
					});
					deleteNavigationModal.show();	
				} else {
					// TODO: error here...
				}
			} // end on_success
		})		
	};
	
	
	var checkRequiredFields = function(type, system_code, parent_id, code, title, url, is_active) {
		hasError = 0;
		$('#type_error').hide();
		$('#system_code_error').hide();
		$('#parent_id_error').hide();
		$('#code_error').hide();
		$('#title_error').hide();
		$('#url_error').hide();
		$('#is_active_error').hide();
		
		if (type == '') {
			$('#type_error').show();
			hasError = 1;
		}
		
		if (system_code == '') {
			$('#system_code_error').show();
			hasError = 1;
		}
		
		if ((type == "LINK" || type == "") && parent_id == '') {
			$('#parent_id_error').show();
			hasError = 1;
		}
		
		if (code == '') {
			$('#code_error').show();
			hasError = 1;
		}
		
		if (url == '') {
			$('#url_error').show();
			hasError = 1;
		}
		
		if (title == '') {
			$('#title_error').show();
			hasError = 1;
		}
		
		if (is_active == '') {
			$('#is_active_error').show();
			hasError = 1;
		}
	
	
		return hasError;
	};

</script>