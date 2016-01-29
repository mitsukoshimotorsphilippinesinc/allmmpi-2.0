
<div class='alert alert-danger'><h2>Create LDAP Files

	<?php 

		$this->db_human_relations = $this->load->database('human_relations', TRUE);

		$get_sql ="SELECT  
		 				a.department_id, 
		 				CASE 
							WHEN b.department_name = 'Treasury (Payables)' THEN 'Treasury-Payables'  
							WHEN b.department_name = 'Treasury (Receivables)' THEN 'Treasury-Receivables' 
							ELSE REPLACE(b.department_name, ' ', '_') 
					END as department_name
					from
						pm_employment_information_view a
					left join
						rf_department b on (a.department_id = b.department_id)
					where 
						a.is_employed = 1 
					and 
						a.company_email_address is not null 
					and 
						a.company_id = 1
					and 
						a.department_id not in (45, 51)
					group by 
						b.department_id	
					order by 
						b.department_name";

		$department_details = $this->db_human_relations->query($get_sql);
		$department_details = $department_details->result();		


		$department_options = array();
		$department_options = array('0' => 'All');
		foreach ($department_details as $wd) {
		 	$department_options[$wd->department_id] = $wd->department_name;
		}
	?>				
	
	<?= form_dropdown('department',$department_options, NULL,'id="department"') ?>
	
	<a class='btn btn-small btn-default'id="populate-btn" style="float:right;" title='Go'>GO</a></h2>
</div>
<script type="text/javascript">

	
	$("#populate-btn").click(function(){
		
		b.request({
			url: "/workbench/ldap_createfile/process",
			data: {
				"department_id" : $("#department").val()
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