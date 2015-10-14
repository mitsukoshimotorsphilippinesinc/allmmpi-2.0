<div class="subnav subnav-fixed">
	<ul class="nav nav-pills">
		<li class=""><a href="/human_relations">Human Relations</a><li>
		<li class=""><a href="/accounting">Accounting</a><li>
		<li class=""><a href="/customer_service">Customer Service/CSD</a><li>
		<li  class="dropdown dropdown-auto">
			<a href="/dpr" class="dropdown-toggle" data-toggle="dropdown">DPR<b class="caret"></b></a>
			 <ul class="dropdown-menu">
	        <?php							
				// get all department modules excluding current segment (alphabetically)
				$where = "is_active = 1";
				$department_module_details = $this->dpr_model->get_department_module($where);

				foreach ($department_module_details as $dmd) {
					echo "<li class='dropdown dropdown-auto'><a href='/dpr/{$dmd->segment_name}' style='margin-top:5px'>{$dmd->module_name}</a></li>";
				}
			?>			 
	        </ul>
		</li>	
	    <li class="dropdown dropdown-auto">
	        <a href="/spare_parts" class="dropdown-toggle" data-toggle="dropdown">Spare Parts<b class="caret"></b></a>
	        <ul class="dropdown-menu">
	        <?php			
				$this->load->model("spare_parts_model");
				
				// get all department modules excluding current segment (alphabetically)
				$where = "is_active = 1";
				$department_module_details = $this->spare_parts_model->get_department_module($where);

				foreach ($department_module_details as $dmd) {
					echo "<li class='dropdown dropdown-auto'><a href='/spare_parts/{$dmd->segment_name}' style='margin-top:5px'>{$dmd->module_name}</a></li>";
				}
			?>			 
	        </ul>
	    </li>
	    <li class=""><a href="/warehouse">Warehouse</a><li>
		<li class=""><a href="/information_technology">Information Technology</a><li>	          
	</ul>
</div>