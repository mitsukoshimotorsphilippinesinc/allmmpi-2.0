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
		
		$this->template->view('ldap_createfile/ldapcreatefile');
	}

	public function process() 
	{


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
							ELSE b.department_name 
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
					order by 
						b.department_name";

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
}