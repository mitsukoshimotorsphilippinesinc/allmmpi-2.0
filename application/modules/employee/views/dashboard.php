<style>
	th.earner-rank {
		width: 50px;
	}
	th.earner-name {
		width: 400px;
	}
	th.earner-group {
		width: 273px;
	}
</style>

<div class="page-header">
  <center><h2 style="color:#ffffff;border:1px solid;background:rgba(0, 0, 0, 0) linear-gradient(90deg, #0088CC 10%, #0B4B77 90%) repeat scroll 0 0">Dashboard</h2></center>
</div>
	<div>		
		<div class="tab-pane active" id="announcements">
			<?php					
				// get all announcements
				$where = "`is_published` = 1";
				$order_by = "insert_timestamp DESC";					
				$limit_details = $this->setting_model->get_setting_by_slug('announcements_on_dashboard');
				$limit = $limit_details->value;
				
				$sql = "SELECT * FROM `am_announcement` WHERE " . $where . " ORDER BY " . $order_by . " LIMIT " . $limit;
				
				$query = $this->db->query($sql);
				$announcements = $query->result();
				
				$total = count((array)$announcements);
				
				if(empty($announcements)) {
					echo "<center><h3>No Announcement Found.</h3></center>";
				}
			
				$ctr = 0;
				foreach($announcements as $a) {
				
					$proper_date = date("jS F Y h:i:s a", strtotime($a->insert_timestamp));
					
					echo "<h2 style='float:left;'>{$a->title}</h2><div style='clear:both;'></div><span style='float:left;margin-top:-15px;'><i>{$proper_date}</i></span><div style='clear:both;'></div><br/>";
					echo $a->body;
					if ($ctr < $total - 1) { 
						echo "<div style='width: 100%; height: 3px; background: #F87431; overflow: hidden;'></div>";
					}
					$ctr++;
				}
			
			?>
			
		</div>
	</div>
<script type="text/javascript">
	
	$(document).ready(function(){

        $('img').live('contextmenu', function(e) {
	        return false;
	    }); 
	    $('img').live('dragstart', function(e) {
	        return false;
	    }); 
	});

	
</script>
