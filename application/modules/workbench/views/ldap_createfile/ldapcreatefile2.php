
<div class='alert alert-danger'><h2>Create LDAP Files

	<?php 

		$this->db_human_relations = $this->load->database('human_relations', TRUE);

		$get_sql ="SELECT 
					case when department is null then 'company'
					else 'department' end as type,
					case when department is null then company
					else department end as depcomp,
					case when department is null then CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(company, ' ', 1), ' ', -1), '_', SUBSTRING_INDEX(SUBSTRING_INDEX(company, ' ', 2), ' ', -1)) 
					when department like 'Bayswater%' then CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(department, ' ', 1), ' ', -1), '_', SUBSTRING_INDEX(SUBSTRING_INDEX(department, ' ', 2), ' ', -1)) 
					else REPLACE(REPLACE(REPLACE(department,' ', '_'), ')',''), '(', '') end as ou_value from ldap_employment_information 
					where company <> ''
					group by ou_value
					";

		$department_details = $this->db_human_relations->query($get_sql);
		$department_details = $department_details->result();		

		$department_options = array();
		$department_options = array('0' => 'All');
		foreach ($department_details as $wd) {
		 	$department_options[$wd->type . '|' . $wd->depcomp . '|' . $wd->ou_value] = $wd->ou_value;
		}
	?>				
	
	<?= form_dropdown('department',$department_options, NULL,'id="department"') ?>
	
	<a class='btn btn-small btn-default'id="populate-btn" style="float:right;" title='Go'>GO</a></h2>
</div>
<script type="text/javascript">

	
	$("#populate-btn").click(function(){
		
		b.request({
			url: "/workbench/ldap_createfile/process2",
			data: {
				"depcomp_details" : $("#department").val()
			},
			on_success: function(data){
				var xls_modal = b.modal.new({});
				if(data.status == "1")
				{
					alert("OK!");
				} else {
					alert("ERROR!");
				}
			},
			on_error: function(){				
			}
		});
		
	});
	
</script>