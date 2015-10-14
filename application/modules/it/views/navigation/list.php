<!--div class='alert alert-info'><h2>Navigations <a class='btn btn-small' href='/admin/navigations/add' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div-->

<div class='alert alert-danger'><h2>Navigation <a class='btn btn-small' id='btn_add_new' style='float:right;margin-right:-30px;margin-top:5px;'><i class="icon-plus"></i><span> Add New</span></a></h2></div>

<?php
	// get all systems
	$this->load->model("human_relations_model");

	$where = "url IS NOT NULL AND is_active = 1";
	$department_details = $this->human_relations_model->get_department($where, NULL, "department_name");
?>

<div >
	<form id='search_details' method='get' action =''>

		<strong>Department:&nbsp;</strong>
		<select>
			<option value="0">ALL</option>
			<?php 
			foreach ($department_details as $dd) {
			?>
			<option value="<?= $dd->department_id; ?>"><?= $dd->department_name; ?></option>
			<?php	
			}
			?>	
		</select>	
		<br/>
		<strong>Search By: &nbsp;</strong>
		<select name="search_option" id="search_option" style="width:220px;" value="<?= $search_by ?>">
			<option value="name">Name</option>
			<option value="title">Title</option>
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

			<span class="label label-important">Search Results for:</span>
			<span class="label label-default"><?= $search_status ?></span>
			<span class="label label-default"><?= $search_by ?></span>
			<span class="label label-default"><?= $search_text ?></span>
		</div>		

	</form>
</div>

<table class='table table-striped table-bordered'>
	<thead>
		<tr>			
			<th style='width:80px;'>Department/System</th>
			<th>Name</th>
			<th style=''>Title</th>
			<th style=''>url</th>
			<th style=''>Priority Order</th>
			<th style=''>Type</th>
			<th style=''>Parent Module</th>
			<th style=';'>Is Active?</th>			
			<th style='width:70px;'>Date Created</th>
			<th style='width:80px;'>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php if(empty($navigations)):?>
		<tr><td colspan='9' style='text-align:center;'><strong>No Records Found</strong></td></tr>
	<?php else: ?>
	<?php foreach ($navigations as $nav): ?>
		<tr>
			<?php
				$department_details = $this->human_relations_model->get_department_by_id($nav->department_id);

				$parent_module_details = $this->navigation_model->get_navigation_by_id($nav->parent_id);
			if (empty($parent_module_details))
				$title = "N/A";
			else
				$title = $parent_module_details->title;

			?>
			<td><?= $department_details->department_name; ?></td>
			<td><?= $nav->name; ?></td>
			<td><?= $nav->title; ?></td>
			<td><?= $nav->url; ?></td>
			<td><?= $nav->priority_order; ?></td>
			<td><?= $nav->type; ?></td>
			<td><?= $title; ?></td>
			<td><?= ($nav->is_active) ? 'Yes' : 'No'  ?></td>										
			<td><?= $nav->insert_timestamp; ?></td>

			<td data1="<?= $nav->navigation_id ?>">				
				<a class='btn btn-small btn-primary edit-btn' title='Edit'><i class='icon-white icon-pencil'></i></a>
				<a class='btn btn-small btn-danger delete-btn' title='Delete'><i class='icon-white icon-remove'></i></a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>

	

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