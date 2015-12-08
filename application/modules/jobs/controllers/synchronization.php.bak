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
		$sql = "SHOW TABLES LIKE 'Personal_Information_diff'";		
		$query = $this->db_human_relations->query($sql);
		$table_exists = $query->first_row();

		/*
		if (!(empty($table_exists))) {
			// backup personal_information_diff to personal_information_diff_<date>
			$sql = "RENAME TABLE `Personal_Information_diff` TO Personal_Information_diff_{$date_time}";
			$this->db_human_relations->query($sql);
		} else {
			// TODO: Log error 
			echo "Personal_Information_diff does not exists." . "<br/>";
		}
		*/	

		// check if human_relations.Personal_Information table exists
		$sql = "SHOW TABLES LIKE 'Personal_Information'";		
		$query = $this->db_human_relations->query($sql);
		$table_exists = $query->first_row();

		if (!(empty($table_exists))) {

			/*

			// drop human_relations.Personal_Information_last table	
			$sql = "DROP TABLE IF EXISTS `Personal_Information_last`";
			echo $sql . "</br>";
			$this->db_human_relations->query($sql);

			// rename old human_relations.Personal_Information to Personal_Information_last
			$sql = "RENAME TABLE `Personal_Information` TO `Personal_Information_last`";
			echo $sql . "</br>";
			$this->db_human_relations->query($sql);	

			// create Personal_Information table
			$sql = "CREATE TABLE Personal_Information LIKE `Personal_Information_last`";
			$this->db_human_relations->query($sql);			
			echo $sql . "</br>";
			

			// get all employed records
			$sql = "SELECT 
						*
					FROM
						Personal_Information
					WHERE 
						Employed = 'YES'";
			
			echo $sql . "</br>";
			$query = $this->db->query($sql);
			$active_employees = $query->result();			
			
			if (count($active_employees) == 0) {
				// TODO: Log error 
				echo "Personal_Information from HRIS has no active record." . "<br/>";
				return;
			} else {
				foreach ($active_employees as $ae) {
					// insert $ae to Portal's Personal_Information table
					$sql = "
						INSERT INTO Personal_Information
						(
						  `HO_Branch`,
						  `Company`,
						  `DeptBranch`,
						  `Agency`,
						  `IDNo`,
						  `SurName`,
						  `FirstName`,
						  `MiddleName`,
						  `CompleteName`,
						  `CellNo`,
						  `JobGradeLevel`,
						  `EmpPosition`,
						  `EmploymentStatus`,
						  `DateEff`,
						  `SalDateEff`,
						  `DateStarted`,
						  `DateRegular`,
						  `DateBeg`,
						  `DateEnd`,
						  `DateBegPromotion`,
						  `DateEndPromotion`,
						  `ScheduledDO`,
						  `SchedBegL`,
						  `SchedEndL`,
						  `SchedBegC`,
						  `SchedEndC`,
						  `Picture`,
						  `Employed`,
						  `TypeOfFinishied`,
						  `DateEnded`
						) 
						VALUES 
						(
						  '{$ae->HO_Branch}',
						  '{$ae->Company}',
						  '{$ae->DeptBranch}',
						  '{$ae->Agency}',
						  '{$ae->IDNo}',
						  '{$ae->SurName}',
						  '{$ae->FirstName}',
						  '{$ae->MiddleName}',
						  '{$ae->CompleteName}',
						  '{$ae->CellNo}',
						  '{$ae->JobGradeLevel}',
						  '{$ae->EmpPosition}',
						  '{$ae->EmploymentStatus}',
						  '{$ae->DateEff}',
						  '{$ae->SalDateEff}',
						  '{$ae->DateStarted}',
						  '{$ae->DateRegular}',
						  '{$ae->DateBeg}',
						  '{$ae->DateEnd}',
						  '{$ae->DateBegPromotion}',
						  '{$ae->DateEndPromotion}',
						  '{$ae->ScheduledDO}',
						  '{$ae->SchedBegL}',
						  '{$ae->SchedEndL}',
						  '{$ae->SchedBegC}',
						  '{$ae->SchedEndC}',
						  '{$ae->Picture}',
						  '{$ae->Employed}',
						  '{$ae->TypeOfFinishied}',
						  '{$ae->DateEnded}'
						)";

					echo $sql . "</br>";
					$query = $this->db_human_relations->query($sql);

				}
			}
	
			// create personal_information_diff table to handle the result of comparing Personal_Information_last to Personal_Information
			$sql = "CREATE TABLE `Personal_Information_diff` AS 
					(
					SELECT 
						State, IDNo, HO_Branch, Company, DeptBranch, Agency, FirstName, MiddleName, Surname, CellNo, JobGradeLevel, EmpPosition, EmploymentStatus, Employed
					FROM 
						(
							SELECT 
								'OLD' as State, IDNo, HO_Branch, Company, DeptBranch, Agency, FirstName, MiddleName, Surname, CellNo, JobGradeLevel, EmpPosition, EmploymentStatus, Employed 
							FROM 
								Personal_Information_last
							UNION ALL
							SELECT 
								'NEW' as State, IDNo, HO_Branch, Company, DeptBranch, Agency, FirstName, MiddleName, Surname, CellNo, JobGradeLevel, EmpPosition, EmploymentStatus, Employed 
							FROM 
								Personal_Information
						) tbl
					GROUP BY 
						IDNo, HO_Branch, Company, DeptBranch, Agency, FirstName, MiddleName, Surname, CellNo, JobGradeLevel, EmpPosition, EmploymentStatus, Employed
					HAVING 
						count(*) = 1
					ORDER BY 
						IDNo
					)";
			
			echo $sql . "</br>";
			$this->db_human_relations->query($sql);
			*/


			// process records for DEACTIVATION
			// check all State = OLD with no NEW partner - means that it is an old record
			$sql = "SELECT 
					IDNo 
				FROM 
					`Personal_Information_diff` 
				WHERE 
					State = 'OLD'
				AND 
					IDNo NOT IN (SELECT IDNo FROM `Personal_Information_diff` WHERE State = 'NEW')";
						
			$query = $this->db_human_relations->query($sql);
			$wold_wonew = $query->result();


			if (!(empty($wold_wonew))) {

				foreach ($wold_wonew as $ww) {									
					// cross check to human_relations.pm_employment_information and set is_employed = 0
					$sql = "UPDATE pm_employment_information SET is_employed = 0 WHERE id_number = '{$ww->IdNo}'";
					$this->db_human_relations->query($sql);

					$sql = "UPDATE sa_user SET is_active = 0 WHERE id_number = '{$ww->IdNo}'";					
					$this->db->query($sql);
				}	
			}


			// process records for UPDATE
			// 	chack id numbers with OLD and NEW entries - means that system needs to update
			$sql = "SELECT
						* 
					FROM 
						`Personal_Information_diff` 
					WHERE 
						State = 'NEW'
					AND 
						IDNo IN (SELECT IDNo FROM `Personal_Information_diff` where State = 'OLD')";
		
			$query = $this->db_human_relations->query($sql);
			$wold_wnew = $query->result();			


			// update all fields
			if (!(empty($wold_wnew))) {

				foreach ($wold_wnew as $ww) {									

					// Company 
					$company_id = $this->get_company_id($ww->Company);

					// DeptBranch
					if ($ww->HO_Branch == "Branch") {
						$branch_id = $this->get_branch_id($ww->DeptBranch);
						$department_id = 0;
					} else {
						$branch_id = 0;
						$department_id = $this->get_department_id($ww->DeptBranch);
					}

					//// Agency
					$agency_id = $this->get_agency_id($ww->Agency);

					// JobGradeLevel
					$job_grade_level_id = $this->get_job_grade_level_id($ww->JobGradeLevel);

					// EmpPosition
					$position_id = $this->get_position_id($ww->EmpPosition);

					// EmploymentStatus
					$employment_status_id = $this->get_employment_status_id($ww->EmploymentStatus);

					$sql = "UPDATE 
								pm_employment_information
							SET	
								company_id = {$company_id},
								branch_id = {$branch_id}, 
								department_id = {$department_id}, 
								agency_id = {$agency_id}, 
								job_grade_level_id = {$job_grade_level_id}, 
								position_id = {$position_id}, 
								employment_status_id = {$employment_status_id}
							WHERE
								id_number = '{$ww->IDNo}'
							AND
								is_employed = 1	
					";

					echo $sql . "</br>";
					$query = $this->db_human_relations->query($sql);

				}	
			}
			
			// process records for UPDATE			
			// check all State = NEW
			$sql = "SELECT 
						* 
					FROM 
						personal_information_diff 
					WHERE 
						State = 'NEW'
					AND 
						IDNo NOT IN (SELECT IDNo FROM personal_information_diff WHERE State = 'OLD')";	

			$query = $this->db_human_relations->query($sql);
			$woold_wnew = $query->result();			
			

			if (!(empty($woold_wnew))) {

				foreach ($woold_wnew as $ww) {			

					// Company 
					$company_id = $this->get_company_id($ww->Company);

					// DeptBranch
					if ($ww->HO_Branch == "Branch") {
						$branch_id = $this->get_branch_id($ww->DeptBranch);
						$department_id = 0;
					} else {
						$branch_id = 0;
						$department_id = $this->get_department_id($ww->DeptBranch);
					}

					//// Agency
					$agency_id = $this->get_agency_id($ww->Agency);

					// JobGradeLevel
					$job_grade_level_id = $this->get_job_grade_level_id($ww->JobGradeLevel);

					// EmpPosition
					$position_id = $this->get_position_id($ww->EmpPosition);

					// EmploymentStatus
					$employment_status_id = $this->get_employment_status_id($ww->EmploymentStatus);

					// check if already existing in pm_employment_information
					$sql = "SELECT
								*
							FROM
								pm_employment_information
							WHERE
								id_number = '{$ww->IDNo}'";
					
					$query = $this->db_human_relations->query($sql);
					$employment_info_details = $query->result();

					if (empty($employment_info_details)) {

						// insert to pm_personal_information
						$sql = "INSERT INTO 
									pm_personal_information
									(
										complete_name, 
										last_name, 
										first_name, 
										middle_name, 
										mobile_number,
										job_grade_level_id,
										position_id,
										employment_status_id,
										is_employed
									)
								VALUES 
									(
									)";
							

						
						// insert to pm_employment_information
						$sql = "INSERT INTO 
									pm_employment_information 
									(
										id_number, 
										company_id, 
										branch_id, 
										department_id, 
										agency_id,
										job_grade_level_id,
										position_id,
										employment_status_id,
										is_employed
									)
								VALUES 
									(
									)";
						
						

					} else {
						// update
						$sql = "UPDATE 
								pm_employment_information
							SET	
								company_id = {$company_id},
								branch_id = {$branch_id}, 
								department_id = {$department_id}, 
								agency_id = {$agency_id}, 
								job_grade_level_id = {$job_grade_level_id}, 
								position_id = {$position_id}, 
								employment_status_id = {$employment_status_id},								
								is_employed = 1
							WHERE
								id_number = '{$ww->IDNo}'							
						";												
					}	

					echo $sql . "</br>";
					$query = $this->db_human_relations->query($sql);

				}	
			}



			
		} else {

			// TODO: Log missing table
			echo "Personal_Information does not exists.";
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