<?php
// check if this shoud be accessible by current user
$cod_access_setting = $this->setting->code_of_discipline_access_position_ids;

$position_id = explode("|", $cod_access_setting);

$is_allowed = 0;

foreach ($position_id as $pi) {
	if ($pi == $this->employee->position_id) {
		$is_allowed = 1;
		break;
	}
}

if (($is_allowed == 1) || ($cod_access_setting == "ALL")) {

	if (empty($cod_details)) {
		echo "<center><h3>No Document Found.</h3><center>";	
	} else {
	?>

		<div class="page-header clearfix">
			<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">Code Of Discipline</small></h2></center>
		</div>

		<div class="page-header">  
		  <h2><?= $cod_details->cod_name; ?> <br/><small><?= $cod_details->cod_description; ?></small></h2>
		</div>

		<?php

		echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../media/cod/{$cod_details->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";

		//echo "<br/>
		//	  <div id='cod-comments'></div>
		//	  <textarea placeholder='Put your comments here...' class='' style='width:67.5em;' id='new-cod-comment'></textarea>
		//	  <button class='button-post btn btn-primary pull-right' style='margin-right:margin-bottom:10px;' data='{$cod_details->cod_id}' title='Post'>Post</button>	  
		//	  ";

	}
} else {
	echo "<center><h3><span style='color:red;'>Warning!</span> You are not allowed to directly access this page.<br/><small>Contact the IT Department if you need assistance. Thank you.</small></h3><center>";	
}

?>

<script type="text/javascript">
	
	var _cod_id = <?= $cod_details->cod_id ?>;

	$(".button-post").live("click", function(e){
		
		//var _cod_id = $(this).attr("data");	
		var _comment = $("#new-cod-comment").val();

		if (_comment.trim().length < 2) {
			
			errorModal = b.modal.new({
				title: "Add Comment :: Error",
				width:450,
				disableClose: true,
				html: "Comment message must be at least two (2) characters long.",
				buttons: {
					'Ok' : function() {
						errorModal.hide();						
					}
				}
			});
			errorModal.show();
			return false;
		} else { 		

			displayComment(_cod_id, _comment);			
		}

	})

	var displayComment = function(_cod_id, _comment) {
		b.request({
			url : '/employee/cod/display_comments',
			data : {				
				"_cod_id" : _cod_id,
				"_comment" : _comment,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					
					$("#new-cod-comment").val("");
					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: "Add Comment :: Successful",
						width:450,
						disableClose: true,
						html: "You have successfully added a new comment. Thank you!",
						buttons: {
							'Ok' : function() {
								proceedApproveRequestModal.hide();
								loadComments(_cod_id);								
							}
						}
					});
					proceedApproveRequestModal.show();
					
				} else {
					// show add form modal
					approveRequestModal.hide();					
					errorApproveRequestModal = b.modal.new({
						title: "Add Comment :: Error",
						width:450,	
						html: "Oopps! There is something wrong. Please try again or contact IT Department.",
					});
					errorApproveRequestModal.show();	

				}
			}

		})
		return false;
	}

	$(document).ready(function(){
		loadComments(_cod_id);
	});

	var loadComments = function(_cod_id){

		beyond.request({
			url: '/employee/cod/get_cod_comments',
			data: {				
				"_cod_id" : _cod_id

			},
			on_success: function(data){
				if(data.status) {										
					$("#cod-comments").html(data.data.html);				
				} else {
					var err_modal = beyond.modal.create({
						title: 'Error :: Error',
						html: data.msg
					});
					err_modal.show();
				}
			}
		});
	};


</script>