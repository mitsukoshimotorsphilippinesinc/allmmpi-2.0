<h3> Branch Monitoring </h3>
<br>

<span>Select Branch</span>
<Select id = "branch_option" name = "branch_option">
	<option value = '0'>Select Branch</option>
	<?php
		$where = "is_active = 1";
		$branch_list=$this->human_relations_model->get_branch($where,null,'branch_name ASC');
		foreach ($branch_list as $bl) {
			$branch_name = $bl->branch_name;
			$branch_id = $bl->branch_id;
			echo "<option name = 'branch_data' value = {$bl->branch_id} data-tin='{$bl->tin}' data-address='{$bl->address_street}'>{$branch_id} - {$branch_name}</option>";
		}
	?>
</Select>
<br>
<table id = "form_list" class='table table-striped table-bordered'>
	<thead>
		<tr>
			<?php	

				$where = "is_active = 1";
				$form_info = $this->dpr_model->get_form_type($where,null,'form_type_id ASC');
				foreach ($form_info as $fi){
					echo "
					<th>{$fi->code}</th>";
				}
			?>
		</tr>
	</thead>
</table>
<div class = "span6">
<table id = "last_series" class='table table-striped table-bordered'>
	<thead>
		<tr>
			<th style = "width:100px;">Type of Form</th>
			<th style = "width:30px;">Last</th>
			<th style = "width:30px;">Next</th>
		</tr>
	</thead>
</table>
</div>