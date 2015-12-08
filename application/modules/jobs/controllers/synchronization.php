<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Synchronization extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
		
		$this->db_human_relations = $this->load->database("human_relations", TRUE);
		$this->load->model("human_relations_model");
	}
	
	public function index() 
	{
		echo "Sync DBs.";
	}

	public function update_portal_db()
	{
		// get date time
		$date_time = date('Ymd_His');

		// check if human_relations.personal_information_diff table exists
		$sql = "SHOW TABLES LIKE 'hris_employment_information_diff'";		
		$query = $this->db_human_relations->query($sql);
		$table_exists = $query->first_row();

		
		if (!(empty($table_exists))) {
			// backup personal_information_diff to personal_information_diff_<date>
			$sql = "RENAME TABLE `hris_employment_information_diff` TO hris_employment_information_diff_{$date_time}";
			$this->db_human_relations->query($sql);
		} else {
			// TODO: Log error 
			echo "hris_employment_information_diff does not exists." . "<br/>";
		}
			
		$sql = "SHOW TABLES LIKE 'hris_employment_information'";		
		$query = $this->db_human_relations->query($sql);
		$table_exists = $query->first_row();

		if (!(empty($table_exists))) {
			// check if table to compare to exists
			$sql = "SHOW TABLES LIKE 'hris_employment_information_last'";		
			$query = $this->db_human_relations->query($sql);
			$has_last_table = $query->first_row();

			if (empty($has_last_table)) {
				$sql = "CREATE TABLE hris_employment_information_last LIKE `hris_employment_information`";
				$this->db_human_relations->query($sql);			
				echo $sql . "</br>";
			} 

			// COMPARE			
			$sql = "CREATE TABLE `hris_employment_information_diff` AS 
					(
					SELECT 
						state,
						id_number,
						company,
						department,
						branch,
						job_grade_level,
						position,
						employment_status
					FROM 
						(
						SELECT 
							'OLD' as state, 
							LPAD(id_number, 7, '0') AS id_number,
							company,
							department,
							branch,
							job_grade_level,
							position,
							employment_status
						FROM 
							hris_employment_information_last
						UNION ALL
						SELECT 
							'NEW' as state,
							LPAD(id_number, 7, '0') AS id_number,
							company,
							department,
							branch,
							job_grade_level,
							position,
							employment_status 
						FROM 
							hris_employment_information
						) tbl
					GROUP BY 
						id_number,
						company,
						department,
						branch,
						job_grade_level,
						position,
						employment_status
					HAVING 
						count(*) = 1
					ORDER BY 
						id_number
					)";
			
			echo $sql . "</br>";
			$this->db_human_relations->query($sql);

			if (!(empty($has_last_table))) {	
				$sql = "RENAME TABLE `hris_employment_information` TO `hris_employment_information_last`";
				echo $sql . "</br>";
				$this->db_human_relations->query($sql);	
			}	

			// process OLD only
			$sql = "SELECT 
					id_number 
				FROM 
					`hris_employment_information_diff` 
				WHERE 
					state = 'OLD'
				AND 
					id_number NOT IN (SELECT id_number FROM `hris_employment_information_diff` WHERE State = 'NEW')";
						
			$query = $this->db_human_relations->query($sql);
			$wold_wonew = $query->result();


			if (!(empty($wold_wonew))) {

				foreach ($wold_wonew as $ww) {									
					// cross check to human_relations.pm_employment_information and set is_employed = 0
					$sql = "UPDATE pm_employment_information SET is_employed = 0 WHERE id_number = LPAD('{$ww->id_number}', 7, '0')";
					//$this->db_human_relations->query($sql);

					$sql = "UPDATE sa_user SET is_active = 0 WHERE id_number = LPAD('{$ww->id_number}', 7, '0')";
					//$this->db->query($sql);
				}	
			}

			// process BOTH
			$sql = "SELECT
						* 
					FROM 
						`hris_employment_information_diff` 
					WHERE 
						state = 'NEW'
					AND 
						id_number IN (SELECT id_number FROM `hris_employment_information_diff` where state = 'OLD')";
		
			$query = $this->db_human_relations->query($sql);
			$wold_wnew = $query->result();			


			// update all fields
			if (!(empty($wold_wnew))) {

				foreach ($wold_wnew as $ww) {									

					$company_id = $this->get_company_id($ww->company);
					
					// department					
					$department_id = $this->get_department_id($ww->department);
					
					// branch
					$branch_id = $this->get_branch_id($ww->branch);
					
					// JobGradeLevel
					$job_grade_level_id = $this->get_job_grade_level_id($ww->job_grade_level);

					// EmpPosition
					$position_id = $this->get_position_id($ww->position);

					// EmploymentStatus
					$employment_status_id = $this->get_employment_status_id($ww->employment_status);

					$sql = "UPDATE 
								pm_employment_information
							SET	
								company_id = {$company_id},
								branch_id = {$branch_id}, 
								department_id = {$department_id}, 								
								job_grade_level_id = {$job_grade_level_id}, 
								position_id = {$position_id}, 
								employment_status_id = {$employment_status_id}
							WHERE
								id_number = LPAD('{$ww->id_number}', 7, '0')
							AND
								is_employed = 1	
					";

					echo $sql . "</br>";
					//$query = $this->db_human_relations->query($sql);

				}	
			}

			// process NEW
			$sql = "SELECT 
						* 
					FROM 
						hris_employment_information_diff 
					WHERE 
						state = 'NEW'
					AND 
						id_number NOT IN (SELECT id_number FROM hris_employment_information_diff WHERE state = 'OLD')";	

			echo $sql . "</br>";

			$query = $this->db_human_relations->query($sql);
			$woold_wnew = $query->result();			
			

			if (!(empty($woold_wnew))) {

				foreach ($woold_wnew as $ww) {			

					$company_id = $this->get_company_id($ww->company);
					
					// department					
					$department_id = $this->get_department_id($ww->department);
					
					// branch
					$branch_id = $this->get_branch_id($ww->branch);
					
					// JobGradeLevel
					$job_grade_level_id = $this->get_job_grade_level_id($ww->job_grade_level);

					// EmpPosition
					$position_id = $this->get_position_id($ww->position);

					// EmploymentStatus
					$employment_status_id = $this->get_employment_status_id($ww->employment_status);

					// check if already existing in pm_employment_information
					$sql = "SELECT
								*
							FROM
								pm_employment_information
							WHERE
								id_number = LPAD('{$ww->id_number}', 7, '0')";
					
					$query = $this->db_human_relations->query($sql);
					$employment_info_details = $query->result();

					if (empty($employment_info_details)) {
						
						// insert to pm_employment_information
						$sql = "INSERT INTO 
									pm_employment_information 
									(
										id_number, 
										company_id, 
										branch_id, 
										department_id, 										
										job_grade_level_id,
										position_id,
										employment_status_id,
										is_employed
									)
								VALUES 
									(
										LPAD('{$ww->id_number}', 7, '0'),
										'{$company_id}',
										'{$branch_id}',
										'{$department_id}',
										'{$job_grade_level_id}',
										'{$position_id}',
										'{$employment_status_id}',
										'1',
									)";
						
					} else {
						// update
						$sql = "UPDATE 
								pm_employment_information
							SET	
								company_id = {$company_id},
								branch_id = {$branch_id}, 
								department_id = {$department_id}, 								
								job_grade_level_id = {$job_grade_level_id}, 
								position_id = {$position_id}, 
								employment_status_id = {$employment_status_id},								
								is_employed = 1
							WHERE
								id_number = LPAD('{$ww->id_number}', 7, '0')
						";												
					}	

					echo $sql . "</br>";
					//$query = $this->db_human_relations->query($sql);

				}	
			}

		} else {

			// TODO: Log missing table
			echo "hris_employment_information does not exists.";
			return;
		}
	}

	public function get_company_id($company_name)
	{

		$where = "LOWER(company_name) = LOWER('{$company_name}')";
		$company_details = $this->human_relations_model->get_company($where);		

		$company_id = 0;

		if (!(empty($company_details))) {
			$company_details = $company_details[0];
			$company_id = $company_details->company_id;
		}

		return $company_id;
	}


	public function get_branch_id($branch_name)
	{

		$where = "LOWER(branch_name) = LOWER('{$branch_name}')";
		$branch_details = $this->human_relations_model->get_branch($where);		

		$branch_id = 0;

		if (!(empty($branch_details))) {
			$branch_details = $branch_details[0];
			$branch_id = $branch_details->branch_id;
		}

		return $branch_id;
	}

	public function get_department_id($department_name)
	{

		$where = "LOWER(department_name) = LOWER('{$department_name}')";
		$department_details = $this->human_relations_model->get_department($where);		

		$department_id = 0;

		if (!(empty($department_details))) {
			$department_details = $department_details[0];
			$department_id = $department_details->department_id;
		}

		return $department_id;
	}

	public function get_agency_id($agency_name)
	{

		$where = "LOWER(agency_name) = LOWER('{$agency_name}')";


		$agency_details = $this->human_relations_model->get_agency($where);

		$agency_id = 0;

		if (!(empty($agency_details))) {
			$agency_details = $agency_details[0];	
			$agency_id 	= $agency_details->agency_id;
		}	

		return $agency_id;
	}

	public function get_job_grade_level_id($job_grade_level)
	{

		$where = "LOWER(grade_level_name) = LOWER('{$job_grade_level}')";
		$job_grade_level_details = $this->human_relations_model->get_job_grade_level($where);		

		$job_grade_level_id = 0;

		if (!(empty($job_grade_level_details))) {
			$job_grade_level_details = $job_grade_level_details[0];
			$job_grade_level_id = $job_grade_level_details->job_grade_level_id;
		}	

		return $job_grade_level_id;
	}

	public function get_position_id($position_name)
	{

		$where = "LOWER(position_name) = LOWER('{$position_name}')";
		$position_details = $this->human_relations_model->get_position($where);		

		$position_id = 0;

		if (!(empty($position_details))) {
			$position_details = $position_details[0];
			$position_id = $position_details->position_id;
		}

		return $position_id;
	}

	public function get_employment_status_id($status_name)
	{

		echo $status_name;

		$where = "LOWER(status_name) = LOWER('{$status_name}')";
		$employment_status_details = $this->human_relations_model->get_employment_status($where);		

		$employment_status_id = 0;

		if (!(empty($employment_status_details))) {
			$employment_status_details = $employment_status_details[0];
			$employment_status_id = $employment_status_details->employment_status_id;
		}

		return $employment_status_id;
	}
   
}