<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hris_missing_attendances extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db_hrisdb = $this->load->database('hrisdb', TRUE);

		$this->load->model('hris_model');
	}

	public function index()
	{
		$where = "termination_id is NULL";
		$employees = $this->hris_model->get_employee($where);
		
		foreach ($employees as $employee) {

			// iterate involved datest
			$sql = "SELECT 
						DATE_FORMAT(`attendance_date`, '%Y-%m-%d') AS `attendance_date` 
					FROM 
						`tmp_attendance_record_date` 
					ORDER BY 
						`id`";

			$query = $this->db_hrisdb->query($sql);
			
			if(count($query->result_array()) > 0) {
				$tmp_dates = $query->result_object();			
			}					

			if (count($tmp_dates) > 0) {
				foreach ($tmp_dates as $td) {
					$sql = "SELECT 
								`punch_in_user_time` 
							FROM 
								`ohrm_attendance_record` 
							WHERE
								DATE_FORMAT(`punch_in_user_time`, '%Y-%m-%d') = '{$td->attendance_date}'
							AND
								emp_number = '{$employee->emp_number}'";

					$query = $this->db_hrisdb->query($sql);
			
					if(count($query->result_array()) > 0) {
						// do nothing			
					} else {
						
						$testtest = $this->get_workshift($employee->emp_number, $td->attendance_date);

						if (count($testtest) > 0)
							$starttime = $testtest[0]->start_time;
						 else
							$starttime = "no workshift set";

						$html = "INSERT INTO ohrm_attendance_record(`emp_number`, `punch_in_utc_time`, `punch_in_user_time`, `state`) VALUES ('{$employee->emp_number}', '{$td->attendance_date} {$starttime}', '{$td->attendance_date} {$starttime}', 'PUNCHED IN');</br>";
						//echo $html;
					}					
				}

				foreach ($tmp_dates as $td) {
					$sql = "SELECT 
								`punch_out_user_time` 
							FROM 
								`ohrm_attendance_record` 
							WHERE
								DATE_FORMAT(`punch_out_user_time`, '%Y-%m-%d') = '{$td->attendance_date}'
							AND
								emp_number = '{$employee->emp_number}'";

					$query = $this->db_hrisdb->query($sql);
			
					if(count($query->result_array()) > 0) {
						// do nothing			
					} else {

						$testtest = $this->get_workshift($employee->emp_number, $td->attendance_date);

						if (count($testtest) > 0)
							$endtime = $testtest[0]->end_time;
						 else
							$endtime = "no workshift set";


						// get fid
						$sql_first_in = "SELECT 
											id, min(punch_in_user_time) AS punch_in_user_time 
										FROM 
											`ohrm_attendance_record` 
										WHERE 
											punch_in_user_time LIke '{$td->attendance_date}%'
										AND emp_number = '{$employee->emp_number}'";

						$query = $this->db_hrisdb->query($sql_first_in);
						
						if(count($query->result_array()) > 0) {
							$tmp_fid = $query->result_object();		
							$fid = 	$tmp_fid[0]->id;
						} else {
							$fid = 0;
						}

						$html = "INSERT INTO ohrm_attendance_record(`emp_number`, `punch_out_utc_time`, `punch_out_user_time`, `state`, `firstin_id`) VALUES ('{$employee->emp_number}', '{$td->attendance_date} {$endtime}', '{$td->attendance_date} {$endtime}', 'PUNCHED OUT', '{$fid}');</br>";
						echo $html;
					}					
				}
			}


			
		}


		$this->template->view('hris/missing_attendances');
	}

	public function get_workshift($empNumber, $attendanceDate)
	{
		// get work shift
		$sql_work_shift = "SELECT 
								a.`work_shift_id`, a.`emp_number`, a.`day_name`, b.`start_time`, b.`end_time` 
							FROM 
								`ohrm_employee_work_shift` a
							LEFT JOIN 
								`ohrm_work_shift` b on a.`work_shift_id` = b.`id`
							WHERE 
								a.`emp_number` = '{$empNumber}' 
							AND 
								a.`day_name` LIKE CONCAT(DATE_FORMAT('{$attendanceDate}','%a'), '%')";

		$query = $this->db_hrisdb->query($sql_work_shift);

		$tmp_workshift = $query->result_object();	

		return $tmp_workshift;											
	}

	public function process() 
	{

		echo "json_encode('status' => '1')";

		return;
	}
}