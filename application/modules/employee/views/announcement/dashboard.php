<div class="page-header clearfix">
	<center><h2 style="color:gray;">Announcements <small></small></h2></center>
</div>

<div class="ui-element">
	<div>		
		<form id='search_details' method='get' action =''>
		<?php							
			foreach($announcements as $a) {
			
				$proper_date = date("jS F Y", strtotime($a->insert_timestamp));
				
				echo "<h2 style='float:left;'>{$a->title}</h2><div style='clear:both;'></div><span style='float:left;margin-top:-15px;'><i>{$proper_date}</i></span><div style='clear:both;'></div><br/>";
				echo $a->body;
		?>						
				<br/>
				<div class='announcement-comments-<?= $a->announcement_id ?>' >
					<div>	
					<?php
						// get announcement messages
						$where = "announcement_id = " . $a->announcement_id;
						$announcement_message_details = $this->asset_model->get_announcement_message($where, NULL, "announcement_message_id");

						if (count($announcement_message_details) > 0) {
							foreach ($announcement_message_details as $amd) {
								if ($amd->from_id_number == 'n/a') {
									echo "<div class='alert alert-success' style='border:1px solid;'><strong>ADMIN: </strong>{$amd->message}</div>";
								} else {

									if ($amd->is_removed == 0) {
										echo "<div class='alert alert' style='border:1px solid;'><strong>ME: </strong>{$amd->message}</div>";
									} else {
										echo "<div class='alert alert' style='border:1px solid;'><strong>ME: </strong><i style='color:#ff1100;'>Your message was removed by Admin.</i></div>";
									}	
								}
							}
						}
					?>	
					</div>					
				</div>
				<div class="">
					<label style="color:gray;"><i>Post a comment</i></label>
					<div class="clearfix">
						<textarea class="span12 new-comment-<?= $a->announcement_id ?>"></textarea>	
					</div>
					<a class="btn btn-small btn-primary button-post" title="Post" href="" data="<?= $a->announcement_id ?>">Post</a>
				</div>
				<br/>
				<div style="width: 100%; height: 3px; background: #F87431; overflow: hidden;"></div>
			
			<?php	
			}			
			?>
			
		</form>
	</div>
</div>


<div>
	<?= $this->pager->create_links($get_data); ?>
</div>

<script type="text/javascript">
  //<![CDATA[

  	$(document).ready(function() {

		$('img').live('contextmenu', function(e) {
	        return false;
	    }); 
	    $('img').live('dragstart', function(e) {
	        return false;
	    }); 

		//showTransactions(1);               
	});			
	

	/*$(function() {
		
		$("#from_date").datepicker({
            //'timeFormat': 'hh:mm tt',
			'dateFormat' : "yy-mm-dd",
		});

		$("#from_date").datepicker('setDate', '<?= $from_date ?>');
		
		$("#from_date_icon").click(function(e) {
			$("#from_date").datepicker("show");
		});
		
		$("#to_date").datepicker({
           // 'timeFormat': 'H:mm:ss',
			'dateFormat' : "yy-mm-dd",			
		});
		
		$("#to_date_icon").click(function(e) {
			$("#to_date").datepicker("show");
		});
		
		$("#to_date").datepicker('setDate', '<?= $to_date ?>');
		
		$('#btn_today').click(function(e) {
			e.preventDefault();
			
			$("#from_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$("#to_date").datepicker('setDate', b.dateFormat('yyyy-mm-dd'));
			$('#frm_filter').submit();
		});
			
	});*/	

	$(".button-post").live('click', function(e){
		var _announcement_id = $(this).attr("data");	
		var _comment = $(".new-comment-" + _announcement_id).val();

		//alert(_comment);
		b.request({
			url : '/employee/announcement/display_comments',
			data : {				
				"_announcement_id" : _announcement_id,
				"_comment" : _comment,
			},
			on_success : function(data) {
				
				if (data.status == "1")	{
					
					// show add form modal					
					proceedApproveRequestModal = b.modal.new({
						title: "Add Comment :: Successful",
						width:450,
						disableClose: true,
						html: "You have successfully added a new comment. Thank you!",
						buttons: {
							'Ok' : function() {
								proceedApproveRequestModal.hide();
								$(".announcement-comments-" + _announcement_id).html(data.data.html);
								$(".new-comment-" + _announcement_id).val("");
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


	})

//]]>
</script>