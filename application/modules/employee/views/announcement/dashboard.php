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
				<div class="announcement-comments">
					<div>	
					<?php
						// get announcement messages
						$where = "announcement_id = " . $a->announcement_id;
						$announcement_message_details = $this->asset_model->get_announcement_message($where, NULL, "announcement_message_id");

						if (count($announcement_message_details) > 0) {
							foreach ($announcement_message_details as $amd) {
								if ($amd->from_id_number == 'n/a') 
									echo "<div class='alert alert-success' style='border:1px solid;'><strong>ADMIN: </strong>{$amd->message}</div>";
								else	
									echo "<div class='alert alert' style='border:1px solid;'><strong>ME: </strong>{$amd->message}</div>";
							}
						}
					?>	
					</div>				
				
					<div class="new-comment">
						<label style="color:gray;"><i>Post a comment</i></label>
						<div class="clearfix">
							<textarea class="span12">
							</textarea>	
						</div>
						<a class="btn btn-small btn-primary" title="Post" href="">Post</a>
					</div>	
				</div>
				<hr/>					
			
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
	

	$(function() {
		
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
			
	});
//]]>
</script>