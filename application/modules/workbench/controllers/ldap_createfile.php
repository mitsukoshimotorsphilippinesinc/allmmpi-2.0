<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap_createfile extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db = $this->load->database('default', TRUE);
		$this->db_human_relations = $this->load->database('human_relations', TRUE);
	}

	public function index()
	{
		
		$this->template->view('ldap_createfile/ldapcreatefile2');
	}

	public function process() 
	{

		$department_id = abs($this->input->post("department_id"));

		$where_add_department = "";

		if ($department_id > 0) {
			$where_add_department = " AND a.department_id = {$department_id} ";
		}

		/*$get_sql ="SELECT 
						a.id_number, 
						a.complete_name, 
						a.last_name, 
						a.first_name, 
						concat(substr(a.middle_name, 1 , 1), '.') as middle_initial,
		 				a.company_email_address,  
		 				a.company_id, 
		 				a.department_id, 
		 				b.department_name
					from
						pm_employment_information_view a
					left join
						tmp_ldap_department b on (a.department_id = b.portal_department_id)
					where 
						a.is_employed = 1 
					and 
						a.company_email_address is not null 
					and 
						a.company_id = 1
					and 
						department_id not in (17, 45, 51)
					order by 
						department_name";*/

		$get_sql ="SELECT 
						a.id_number, 
						a.complete_name, 
						a.last_name, 
						a.first_name, 
						concat(substr(a.middle_name, 1 , 1), '.') as middle_initial,
		 				a.company_email_address,  
		 				a.company_id, 
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
					{$where_add_department}	
					order by 
						b.department_name";

		//var_dump($get_sql);				

		$active_users = $this->db_human_relations->query($get_sql);
		$active_users = $active_users->result();		

		foreach ($active_users as $au) {
			$filename = substr($au->company_email_address, 0, strpos($au->company_email_address, '@'));

			$kumpletos_recados = $au->first_name . " " . $au->middle_initial . " " . $au->last_name;
		
			$myfile = fopen("/tmp/ldap/" . $filename . ".ldif", "a+") or die("Unable to open file!");
			$txt = "dn: cn=" . $filename . ",ou=" . $au->department_name . ",dc=mitsukoshimotors,dc=com\ncn: " . $au->first_name . " \nsn: " . $au->last_name . "\nobjectClass: inetOrgPerson\nuserPassword: mmpi2015\nuid: " . $kumpletos_recados;	
			fwrite($myfile, $txt);
			fclose($myfile);

		}
		
		echo "json_encode('status' => '1')";

		return;
	}

	public function process2() 
	{

		$depcomp_details = trim($this->input->post("depcomp_details"));

			if ($depcomp_details <> 0) {
			$data = explode('|' ,$depcomp_details);

			$type = $data[0];
			$depcomp_name = $data[1];
			$ou_value = $data[2];

			$where_add_department = "";

			if ($type == "department") {
				$where_add_department = " WHERE a.department = '{$depcomp_name}'";
			} else {
				$where_add_department = " WHERE a.company = '{$depcomp_name}'";
			}
		} else {
			$where_add_department = "";
		}
		
		$get_sql ="SELECT 
						a.id_number,
						a.company,
						a.department,
						a.branch,
						a.job_grade_level,
						a.position,						
						a.is_employed,
						b.complete_name,
						b.last_name,
						b.first_name,
						b.middle_name,
						b.mobile_number,
						b.personal_email_address,
						b.mobile_number,
						b.birthdate,
						b.birthplace,
						b.gender,
						b.religion,
						b.marital_status,
						case when a.department is null then CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(a.company, ' ', 1), ' ', -1), '_', SUBSTRING_INDEX(SUBSTRING_INDEX(a.company, ' ', 2), ' ', -1)) 
					when a.department like 'Bayswater%' then CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(a.department, ' ', 1), ' ', -1), '_', SUBSTRING_INDEX(SUBSTRING_INDEX(a.department, ' ', 2), ' ', -1)) 
					else REPLACE(REPLACE(REPLACE(department,' ', '_'), ')',''), '(', '') end as ou_value
					FROM 
						ldap_employment_information a
					LEFT JOIN 
						ldap_personal_information b on a.id_number = b.id_number
					{$where_add_department}	
					order by 
						a.id_number";
		

		$active_users = $this->db_human_relations->query($get_sql);
		$active_users = $active_users->result();		

		foreach ($active_users as $au) {

			$uid = str_replace("ñ", "n", str_replace(" ", "", strtolower($au->first_name))) . "." . str_replace("ñ", "n", str_replace(" ", "", strtolower($au->last_name)));

			$cn = trim($au->first_name) . " " . trim($au->last_name);

			$mail = $au->personal_email_address . "@mitsukoshimotors.com";

			$myfile = fopen("/tmp/ldap/" . $uid . ".ldif", "a+") or die("Unable to open file!");
			$txt = "dn: uid=" . $uid . ",cn=mail,ou=Users,dc=mmpimotors,dc=com\ncn: " . $cn . "\nsn: " . $au->last_name . "\nobjectClass: inetOrgPerson\nuserPassword: mmpi2016\nuid: " . $uid . "\nmail: "  . $mail . "\ncompany: "  . $au->company . "\nposition: "  . $au->position . "\nbirthday: "  . $au->birthdate . "\ngender: "  . $au->gender . "\nreligion: "  . $au->religion . "\nmarital_status: "  . $au->marital_status;
			fwrite($myfile, $txt);
			fclose($myfile);

			echo $myfile;

		}
		
		echo "json_encode('status' => '1')";

		return;
	}
}