<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members_login";
	$_upload_url = urlencode($upload_url);

	$breadcrumb_container = assemble_breadcrumb();

?>

<div>	
	<?= $breadcrumb_container; ?>

	<div class='alert alert-danger'>
		<h2>Login Ads/Images <div style="float:right;"><button class="btn btn-default" id="add_employee_login_ad" style="margin-top: -5px; margin-right: -30px;"><span>Add New</span></button></div></h2>
	</div>
	<hr/>
	<div id="main_page_container" >
		<div id="search_container">
			<form id='search_details' method='get' action ='/operations/login_ad' class="form-inline">
				<strong>Search By:&nbsp;</strong>
				<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
					<option value="ad_name">Ad/Image Name</option>
					<option value="priority_id">Priority ID</option>
				</select>
				<input title="Search" class="input-large search-query" style="margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">

				<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Search</span></button>
				<a id='button_refresh' class='btn' style="margin-top:0;"><span>Refresh</span></a>

				<br/>
				<span id="search_error" class="label label-important" style="display:none">Search String must be at least one (1) character.</span>

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
			<table class="table table-bordered table-condensed table-striped">
				<thead>
					<tr>
						<th>Image</th>
						<th>Ad Name</th>
						<th>Description</th>
						<th>Priority ID</th>
						<th>Is Active</th>
						<th>Date Created</th>
						<th>Actions</th>
					<tr>
				</thead>
				<tbody>
					<?php foreach($member_login_ads as $f): ?>
					<tr>		
						<?php
							$member_picture = check_image_path($this->config->item('media_url') . '/employee_login_ads/' . $f->image_filename);
						?>
						<td><img style="height:130px;width:100px;" src="<?= $member_picture?>"></img></td>
						<td><?= $f->ad_name; ?></td>
						<td><?= $f->description; ?></td>
						<td><?= $f->priority_id; ?></td>
						<td><?= $f->is_active; ?></td>
						<td><?= $f->insert_timestamp; ?></td>
						<td>
							<a href='/operations/login_ad/view_ad/<?= $f->employee_login_ad_id ?>' class='btn btn-small btn-info' title='View'><i class="icon-search icon-white"></i></a>
							<a href='/operations/login_ad/edit/<?= $f->employee_login_ad_id ?>' class='btn btn-small btn-primary' id="test_test" title='Edit'><i class="icon-pencil icon-white"></i></a>
							<a href='/operations/login_ad/delete/<?= $f->employee_login_ad_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="clearfix"></div>
		</div>
		<div class='pagination_html'>
			<?= $this->pager->create_links(); ?>
		</div>
	</div>
</div>				
	
<script type="text/javascript">

	$(document).on('click', "#add_employee_login_ad", function() {

		b.request({
			url: '/operations/login_ad/add_featured_modal',
			data: {},
			on_success: function(data) {
				if(data.status == 1) {
					var add_modal = b.modal.new({
						title: 'Add New Employee Login Ad',
						html: data.html,
						width: '500px',
						disableClose: true,
						buttons: {
							'Cancel' : function() {
								add_modal.hide();
							},
							'Add' : function() {
								var has_error = false;
								$("#slide_name_error").css("display","none");
								$("#description_error").css("display","none");
								
								if(_.isEmpty($("#slide_name").val()))
								{
									has_error = true;
									$("#slide_name_error").css("display","");
								}
								if(_.isEmpty($("#description").val()))
								{
									has_error = true;
									$("#description_error").css("display","");
								}
								
								if(_.isEmpty($("#priority_id").val()))
								{
									has_error = true;
									$("#priority_id_error").css("display","");
								}
								
								if(!has_error)
								{

									b.request({
										url: '/operations/login_ad/add_ad',
										data: {
											'ad_name' : $("#slide_name").val(),
											'description' : $("#description").val(),
											'priority_id' : $("#priority_id").val(),
										},
										on_success: function(data) {
											if(data.status == "ok") {
												window.location.href = '/operations/login_ad/view_ad/'+data.data.id;	
											}
										}
									});
								}
								else
								{
									
								}
							}
						}
						
					});
					add_modal.show();
				}
			}
		});
	
	});

</script>