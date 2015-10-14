<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Populate_emprequirements extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->load->model('human_relations_model');
		$this->load->model('hrdatabase_model');
	}

	public function index()
	{
		
		$this->template->view('emprequirements');
	}

	public function process() 
	{

		$where_processed = "is_processed = 0";
		$oldrecords = $this->hrdatabase_model->get_employment_requirements($where_processed);

		$save_val = "N/A";

		foreach ($oldrecords as $or) {
			// iterate rf_employment_requirement
			$where = "";
			$requirements = $this->human_relations_model->get_employment_requirement($where);

			// get personal_information details based on Employee
			$where_pi = "replace(complete_name, '\'', '') = '" . str_replace("'", "", $or->Employee) . "'";
			$personal_information_details = $this->human_relations_model->get_personal_information($where_pi);
			if (empty($personal_information_details)) {
				$pid = 0;
			} else {
				$pid = $personal_information_details[0]->personal_information_id;
			}

			foreach($requirements as $r) {
				// insert to pm_employee_requirement
				
				if ($r->requirement_name = '2x2 ID Picture/s') 
					$save_val = $or->Pic_2x2;
				if ($r->requirement_name = '1x1 ID Picture/s') 
					$save_val = $or->Pic_1x1;
				if ($r->requirement_name = 'NBI Clearance') 
					$save_val = $or->NBI_Clearance;
				if ($r->requirement_name = 'Transcript of Records') 
					$save_val = $or->TOR;
				if ($r->requirement_name = 'College Diploma') 
					$save_val = $or->College_Diploma;
				if ($r->requirement_name = 'SSS') 
					$save_val = $or->SSS;
				if ($r->requirement_name = 'TIN') 
					$save_val = $or->TIN;
				if ($r->requirement_name = 'BIR 2x') 
					$save_val = $or->BIR_2x;
				if ($r->requirement_name = 'Clearance') 
					$save_val = $or->Clearance;
				if ($r->requirement_name = 'Birth Certificate') 
					$save_val = $or->BirthCertificate;
				if ($r->requirement_name = 'Dependent\'s Birth Certificate') 
					$save_val = $or->BirthCertificateDep;
				if ($r->requirement_name = 'Marriage Contract') 
					$save_val = $or->MarriageContract;
				if ($r->requirement_name = 'Drivers License') 
					$save_val = $or->Drivers_License;
				if ($r->requirement_name = 'X-ray') 
					$save_val = $or->XRay;
				if ($r->requirement_name = 'CBC') 
					$save_val = $or->CBC;
				if ($r->requirement_name = 'Urinalysis') 
					$save_val = $or->Urinalysis;
				if ($r->requirement_name = 'Fecalysis') 
					$save_val = $or->Fecalysis;

				$data_insert = array(
					'id_number' => $or->IDNo,
					'requirement_id' => $r->employment_requirement_id,
					'personal_information_id' => $pid,
					'status' => $save_val,
					);
				$this->human_relations_model->insert_employee_requirement($data_insert);

			}

			$where_update = "IDNo = " . $or->IDNo;
			$data_update = array(
				'is_processed' => 1
				);

			$this->hrdatabase_model->update_employment_requirements($data_update, $where_update);
		}



		$data = array(
				'oldrecords' => $oldrecords,
			);

		//$this->rturn_json("1","Ok.",array("data" => $data));

		echo "json_encode('status' => '1')";

		return;
	}
}