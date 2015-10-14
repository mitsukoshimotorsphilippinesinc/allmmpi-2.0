<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members_login";
	$_upload_url = urlencode($upload_url);

?>

<div>
	<div class="alert alert-info">	
		<h2 id="header_text">Members Login Ads
			<div style="float:right;"><button class="btn btn-default" id="add_members_login_ad" style="margin-top: -5px; margin-right: -30px;"><span>Add New</span></button></div>
		</h2>
	</div>
	<div id="main_page_container" >
		<div id="search_container">
			<form id='search_details' method='get' action ='/cms/members_login' class="form-inline">
				<strong>Search By:&nbsp;</strong>
				<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
					<option value="slide_name">Slide Name</option>
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
						<th>Slide Name</th>
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
							$member_picture = check_image_path($this->config->item('media_url') . '/members_login/' . $f->image_filename);
						?>
						<td><img style="height:130px;width:100px;" src="<?= $member_picture?>"></img></td>
						<td><?= $f->slide_name; ?></td>
						<td><?= $f->description; ?></td>
						<td><?= $f->priority_id; ?></td>
						<td><?= $f->is_active; ?></td>
						<td><?= $f->insert_timestamp; ?></td>
						<td>
							<a href='/cms/members_login/view_member/<?= $f->members_login_ad_id ?>' class='btn btn-small btn-info' title='View'><i class="icon-search icon-white"></i></a>
							<a href='/cms/members_login/edit/<?= $f->members_login_ad_id ?>' class='btn btn-small btn-primary' id="test_test" title='Edit'><i class="icon-pencil icon-white"></i></a>
							<a href='/cms/members_login/delete/<?= $f->members_login_ad_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
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
  //<![CDATA[
	
	$(document).ready(function() {

		$('.btn_edit_profile').live("click", function(e) {
			
			var _member_id = $(this).parent().attr("data");
			
			$('#member_details').hide();
			$('#member_achievements').hide();
			
			$('#available_achievements').attr("data","");
			$('#available_achievements').html("");
			$('#member_picture').html("");
			$('#member_id').text("");
			$('#member_name').text("");
			$('#member_since').text("");	
			$('#achievement_details').html("");
			
            showProfile(_member_id);			
			return false;
		});
	});
	
	$("#button_refresh").live("click",function() {
		redirect('/cms/members');
	});
	
	$(document).on('click', "#add_members_login_ad", function() {
		b.request({
			url: '/cms/members_login/add_featured_modal',
			data: {},
			on_success: function(data) {
				if(data.status == 1) {
					var add_modal = b.modal.new({
						title: 'Add New Members Login Ad',
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
										url: '/cms/members_login/add_process',
										data: {
											'slide_name' : $("#slide_name").val(),
											'description' : $("#description").val(),
											'priority_id' : $("#priority_id").val(),
										},
										on_success: function(data) {
											if(data.status == "ok") {
												window.location.href = '/cms/members_login/view_member/'+data.data.id;	
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
	
	
	
	$(document).on("click",'.btn-remove-achievement',function(e){
		if(!$(e.target).hasClass("no_clicking"))
		{
			$(e.target).addClass("no_clicking")
			var data = $(this).attr("data");
			data = data.split("|");
			achievement_actions(e,data[0],data[1],"remove");
		}
		
	});
	
	$("#add_achievement").click(function(e){
		if(!$(e.target).hasClass("no_clicking"))
		{
			$(e.target).addClass("no_clicking");
			achievement_actions(e,$("#available_achievements").attr("data"),$("#available_achievements").val(),"add")
		}
	});
	
	var achievement_actions = function(e,member_id, achievement_id, action){
		b.request({
			url: "/cms/members/member_achievements",
			data: {
				member_id: member_id,
				achievement_id: achievement_id,
				action: action
			},
			on_success: function(data){
				if(data.status == "ok")
				{
					if(_.isEmpty(data.data.available_achievements))
					{
						$('#add_achievements').hide();
					}
					else
					{
						$('#add_achievements').show();
						$('#available_achievements').html(data.data.available_achievements);
						$('#available_achievements').attr("data",member_id);
					}

					$('#achievement_details').html(data.data.achievement_details);
					
					if(data.data.has_achievements == 1)
					{
						$('#image_upload').show();
					}
					else
					{
						$('#image_upload').hide();
					}
				}
				$(e.target).removeClass("no_clicking");
			},
			on_error: function(data){
				$(e.target).removeClass("no_clicking");
			}
		});
		
	}	
	
	var showProfile = function(member_id){
		
		b.request({
	        url: '/cms/members/view_profile',
	        data: {
				"member_id" : member_id				
			},
			on_success: function(data, status) {
				
				$('#member_picture').html(data.data.member_picture);
				$('#member_id').text(data.data.member_id);
				$('#member_name').text(data.data.member_name);
				$('#member_since').text(data.data.member_since);				
				
				if(_.isEmpty(data.data.available_achievements))
				{
					$('#add_achievements').hide();
				}
				else
				{
					$('#add_achievements').show();
					$('#available_achievements').html(data.data.available_achievements);
					$('#available_achievements').attr("data",member_id);
				}
				
				
				$('#achievement_details').html(data.data.achievement_details);
				$('#member_details').show();
				$('#member_achievements').show();

				if(data.data.has_achievements == 1)
				{
					$('#image_upload').show();
				}
				else
				{
					$('#image_upload').hide();
				}
				
				$('#image_upload').html("");
				
				// uploader
				$('#image_upload').Uploadrr({
					singleUpload : true,
					progressGIF : '<?= image_path('pr.gif') ?>',
					allowedExtensions: ['.gif','.jpg', '.png'],
					target : base_url + '/admin/upload/process?filename=featured_member_'+member_id+'&location=<?=$_upload_url?>&width=200&height=200&ts=<?=time()?>',
					onComplete: function() {
						$("#member_picture").html('<img src="<?=$upload_url?>/featured_member_'+member_id+'.jpg?v=' + Math.floor(Math.random() * 999999)+'">');
						
						b.request({
					       url: '/cms/members/update_image',
					       data: {
								"filename": 'featured_member_'+member_id+'.jpg',
								"member_id": member_id
							},
					       on_success: function(data) {		
					       }
					   });		
					}
				});
		    }
		});
		
		return;
		
	}
//]]>
</script>