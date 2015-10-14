<?php	
    $current_date = date("Y-m-d");
		
	$today = date('Y-m-d',strtotime($current_date));
	$yesterday = date('Y-m-d',mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));
	
	$upload_url = $this->config->item("media_url") . "/members";
	$_upload_url = urlencode($upload_url);
?>

<div>
	<div class="alert alert-info">	
		<h2 id="header_text">Wall Of Fame - Member's Achievement
			<div style="float:right;"><button class="btn btn-default" id="add_featured_member" style="margin-top: -5px; margin-right: -30px;"><span>Add New</span></button></div>
		</h2>
	</div>
	<div id="main_page_container" >
		<div id="search_container">
			<form id='search_details' method='get' action ='/cms/members' class="form-inline">
				<strong>Search By:&nbsp;</strong>
				<select name="search_option" id="search_option" style="width:auto;" value="<?= $search_by ?>">
					<option value="name">Name</option>
					<option value="member_id">Member ID</option>
				</select>
				<input title="Search" class="input-large search-query" style="margin-left:5px;" type="text" id="search_string" name="search_string" value="" maxlength='25' autofocus="">

				<button id="button_search" class='btn btn-primary' style="margin-left:5px;"><span>Search</span></button>
				<a id='button_refresh' class='btn' style="margin-top:0;"><span>Refresh</span></a>

				<br/>
				<span id="search_error" class="label label-important" style="display:none">Search String must be at least three (3) characters.</span>

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
						<th>Member Name</th>
						<th>Achievement</th>
						<th>Group Name</th>
						<th>Actions</th>
					<tr>
				</thead>
				<tbody>
					<?php foreach($featured_members as $f): ?>
					<tr>						
						<?php
							$proper_name = "";
							$member_details = $this->members_model->get_member_by_id($f->member_id);
							
							if ((trim($f->member_name) == '') || ($f->member_name == NULL)) {								
								$proper_name = strtoupper($member_details->first_name) . ' ' . strtoupper($member_details->last_name);										
							} else {
								$proper_name = $f->member_name;
							}
							
							$image = "male.jpg";
							if ((trim($f->image_filename) == '') || ($f->image_filename == NULL)) {
								if ($member_details->sex == "F") 																	
									$image = "female.jpg";								
							} else {		
								$image = $f->image_filename;
							}
							
							echo "<td><img style='height:80px;width:75px;' src='/assets/media/members/{$image}'></td>";	
						?>
						
						<td><?= $proper_name; ?></td>
						
						<?php
						  // get achievement title from member_achievements table
						  $achievement_title = "";
						  $member_achievement_details = $this->contents_model->get_member_achievement_by_id($f->achievement_id);
						  
						  if (!empty($member_achievement_details)) {
						    $achievement_title = $member_achievement_details->achievement_name;			
						  }
						  
						?>
						
						<td><?= $achievement_title; ?></td>						
						<td><?= $f->group_name; ?></td>
						<td>
							<a href='/cms/members/view_member/<?= $f->featured_member_id ?>' class='btn btn-small btn-info' title='View'><i class="icon-search icon-white"></i></a>
							<a href='/cms/members/edit/<?= $f->featured_member_id ?>' class='btn btn-small btn-primary' title='View'><i class="icon-pencil icon-white"></i></a>
							<a href='/cms/members/delete/<?= $f->featured_member_id ?>' class='btn btn-small btn-danger' title="Delete"><i class="icon-remove icon-white"></i></a>
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
	
	
	$(document).on('click', "#add_featured_member", function() {
		b.request({
			url: '/cms/members/add_featured_modal',
			data: {},
			on_success: function(data) {
				if(data.status == 1) {
					var add_modal = b.modal.new({
						title: 'Add New Featured Member',
						html: data.html,
						width: '600px',
						disableClose: true,
						buttons: {
							'Cancel' : function() {
								add_modal.hide();
							},
							'Add' : function() {
								var has_error = false;
								$("#member_name_error").css("display","none");
								$("#group_name_error").css("display","none");
								
								if(_.isEmpty($("#member_name").val()))
								{
									has_error = true;
									$("#member_name_error").css("display","");
								}
								if(_.isEmpty($("#group_name").val()))
								{
									has_error = true;
									$("#group_name_error").css("display","");
								}
								
								if(!has_error)
								{
									b.request({
										url: '/cms/members/add_process',
										data: {
											'member_id' : $("#member_id").val(),
											'member_name' : $("#member_name").val(),
											'group_name' : $("#group_name").val(),
											'achievement_type_id' : $("#achievement_type").val(),
										},
										on_success: function(data) {
											if(data.status == "ok") {
												//add_modal.hide();
												window.location.href = '/cms/members/view_member/'+data.data.id;	
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