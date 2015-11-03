<?php

	/*$ci = ci();	
	$ci->db_gen = $ci->load->database($this->uri->segment(1), TRUE);

	$module_title = str_replace("_", " ", $this->uri->segment(1));
	$module_title = strtoupper($module_title);

	$where = "is_active = 1 AND segment_name <> '{$segment_name}'";

	$department_module_details_sql = "SELECT * FROM rf_department_module WHERE {$where}";
	$department_module_details = $ci->db_gen->query($department_module_details_sql);
	$department_module_details = $department_module_details->result();		
	
	if (($segment_name == NULL) || (trim($segment_name) == "")) {
		$active_segment_sql = "SELECT * FROM rf_department_module WHERE segment_name = '{$segment_name}'";
	} else {
		$active_segment_sql = "SELECT * FROM rf_department_module WHERE segment_name = '{$segment_name}'";
		$active_segment = $ci->db_gen->query($active_segment_sql);
	}
	$active_segment = $active_segment->result();		
	$active_segment = $active_segment[0];				


	// get all submodules
	$where = "department_module_id = " . $active_segment->department_module_id;
	$module_submodule_details_sql = "SELECT * FROM rf_department_module_submodule WHERE {$where}";
	$module_submodule_details = $ci->db_gen->query($module_submodule_details_sql);
	$module_submodule_details = $module_submodule_details->result();		
	*/

	// NEW
	$this->load->model("navigation_model");
	$this->load->model("human_relations_model");	

	$department_details = $this->human_relations_model->get_department_by_url($this->uri->segment(1));

	$second_segment = $this->uri->segment(2);

	if (($this->uri->segment(2) == NULL) || (empty($second_segment))) {
		$where = "department_id = {$department_details->department_id} AND name = 'main_dashboard' AND is_active = 1";
	} else {
		$where = "department_id = {$department_details->department_id} AND name = '{$this->uri->segment(2)}' AND is_active = 1 AND parent_id = 0 AND is_active = 1 AND type = 'MODULE'";
	}	
	$current_navigation_details = $this->navigation_model->get_navigation($where);
	$current_navigation_details = $current_navigation_details[0];

	$where = "parent_id = {$current_navigation_details->navigation_id} AND is_active = 1";
	$current_submodule_details = $this->navigation_model->get_navigation($where, NULL, "priority_order ASC");	

	// get all navigation	
	$where = "department_id = '{$department_details->department_id}' AND navigation_id <> {$current_navigation_details->navigation_id} AND parent_id = 0 AND is_active = 1 AND type = 'MODULE'";
	$navigation_details = $this->navigation_model->get_navigation($where);
	
?>

<div id="wrapper">
	<div id="sidebar-wrapper" style="background-color:#0C0C0C;font-family:"Droid Sans"">	
		<ul class="sidebar-nav">
			<li class="sidebar-brand">
				<!--a href="#"><strong><?= $module_title ?></strong></a-->
				<a href="#"><strong><?= $current_navigation_details->title; ?></strong></a>
			</li>	
			<li>
				<?php
				foreach ($current_submodule_details as $csd) {
					echo "<a class='collapsed' data-target='#dealer-menu' data-toggle='collapse' href='javascript:;' aria-expanded='false'>	
							<a href='{$csd->url}' style='font-size:13px;color:white;'>{$csd->title}</a>
						</a>";
				}
				?>	
								
			</li>	
			<li>
				<a href="#"><hr/></a>	
			</li>
			
			<?php foreach ($navigation_details as $nd) { 
				echo "<li>
						<a href='{$this->config->item('base_url')}{$nd->url}'>{$nd->title}</a>	
					  </li>";
			 } ?>
		</ul>	
	</div>
</div>

<!--script type="text/javascript">
	$("#sidebar-wrapper").click(function(){
		alert('test');
		$('#sidebar-wrapper').removeClass('hidden-xs');     
	});
</script-->  


<script type="text/javascript">

$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
 $("#menu-toggle-2").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled-2");
    $('#menu ul').hide();
});

 function initMenu() {
  $('#menu ul').hide();
  $('#menu ul').children('.current').parent().show();
  //$('#menu ul:first').show();
  $('#menu li a').click(
    function() {
      var checkElement = $(this).next();
      if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
        return false;
        }
      if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
        $('#menu ul:visible').slideUp('normal');
        checkElement.slideDown('normal');
        return false;
        }
      }
    );
  }
$(document).ready(function() {initMenu();});

</script>
