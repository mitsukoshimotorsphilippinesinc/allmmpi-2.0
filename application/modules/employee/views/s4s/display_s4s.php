<div class="page-header clearfix">
	<center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">S4S <small style="color:#FFFFFF">(System for System)</small></h2></center>
</div>

<div class="page-header">
  <h2><?= $course_details->pp_name; ?> <br/><small><?= $course_details->pp_description; ?></small></h2>
</div>

<?php
foreach ($asset_details as $ad) {

echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#../../../media/s4s/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";
//echo "<iframe id='viewer' src='/assets/js/libs/ViewerJS/index.html#http://portal.mmpi.local/assets/media/s4s/{$ad->asset_filename}' style='width:100%;height:800px;' allowfullscreen webkitallowfullscreen=''></iframe> <br/>";

}

echo "<br/>
	  <div id='s4s-comments'></div>
	  <textarea class='' style='width:67.5em;' id='new-s4s-comment'></textarea>
	  <button class='button-post btn btn-primary pull-right' style='margin-right:margin-bottom:10px;' data='{$course_details->s4s_id}' title='Post'>Post</button>	  
	  ";

?>

<script type="text/javascript">

	var _s4sId = <?= $course_details->s4s_id ?>;

	$(".button-post").live("click", function(e){

		var _s4s_id = $(this).attr("data");	
		var _comment = $("#new-s4s-comment").val();

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

			displayComment(_s4s_id, _comment);			
		}

	})

	var displayComment = function(_s4s_id, _comment) {
		b.request({
			url : '/employee/s4s/display_comments',
			data : {				
				"_s4s_id" : _s4s_id,
				"_comment" : _comment,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					
					$("#new-s4s-comment").val("");
					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: "Add Comment :: Successful",
						width:450,
						disableClose: true,
						html: "You have successfully added a new comment. Thank you!",
						buttons: {
							'Ok' : function() {
								proceedApproveRequestModal.hide();
								loadComments(_s4s_id);								
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
		loadComments(_s4sId);
	});

	var loadComments = function(_s4s_id){

		beyond.request({
			url: '/employee/s4s/get_s4s_comments',
			data: {				
				"_s4s_id" : _s4s_id

			},
			on_success: function(data){
				if(data.status) {										
					$("#s4s-comments").html(data.data.html);				
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