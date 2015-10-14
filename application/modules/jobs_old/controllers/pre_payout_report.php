<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Pre_payout_report extends Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $this->load->model('payout_model');
        $this->load->model('members_model');
        $this->load->model('facilities_model');
        $this->load->model('tracking_model');
	}
	
	public function index() 
	{
		echo "Pre Process Payout...";
	}

	public function process ($params=array()) 
	{
		error_log(json_encode($params));
		$group_id = trim($params['group_id']);
		$start_date = trim($params['start_date']);
		$end_date = trim($params['end_date']);
		
		$member_group = $this->members_model->get_member_group_by_group_id($group_id);
		
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$member_group->group_name}_pre_payout_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";
		
		if(!empty($member_group))
		{
			try {
				set_time_limit(0); // eliminating the timeout
				ini_set('memory_limit', '2048M');
				error_log("hi");

				$title = "{$member_group->group_name} Pre-Payout Group Report {$start_date} to {$end_date}";

				$objPHPExcel = new PHPExcel();
		        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

		        $title = $member_group->group_name;

		        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->setTitle($title);

				$title = "Pre-Payout Group Report {$start_date} to {$end_date}";

				$start_column_num = 3;

				//set width of first column
				$worksheet->getColumnDimension('A')->setWidth(12.00);
				$worksheet->mergeCells('A1:W1');

				// set column header to bold
				$worksheet->getStyle('A1')->getFont()->setBold(true);
				$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('K' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('L' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('M' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('N' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('O' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('P' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('Q' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('R' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('S' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('T' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('U' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('V' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('W' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('X' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('Y' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('Z' . $start_column_num)->getFont()->setBold(true);

				//center column names
				$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('O' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('P' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('Q' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('R' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('S' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('T' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('U' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('V' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('W' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('X' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('Y' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('Z' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				
				//set column names
				$worksheet->setCellValue('A1', $title);
				$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
				$worksheet->setCellValue('B' . $start_column_num, 'Member Name');
				$worksheet->setCellValue('C' . $start_column_num, 'Group Name');
				$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
				$worksheet->setCellValue('E' . $start_column_num, 'Sponsor ID');
				$worksheet->setCellValue('F' . $start_column_num, 'Upline ID');
				$worksheet->setCellValue('G' . $start_column_num, 'Status');
				$worksheet->setCellValue('H' . $start_column_num, 'Auto-Payout');
				$worksheet->setCellValue('I' . $start_column_num, 'Funds');
				$worksheet->setCellValue('J' . $start_column_num, 'GCs');
				$worksheet->setCellValue('K' . $start_column_num, 'Monthly Maintenance Counter');
				$worksheet->setCellValue('L' . $start_column_num, 'Annual Maintenance Counter');
				$worksheet->setCellValue('M' . $start_column_num, 'MS Monthly Maintenance Counter');
				$worksheet->setCellValue('N' . $start_column_num, 'MS Annual Maintenance Counter');
				$worksheet->setCellValue('O' . $start_column_num, 'Left SP');
				$worksheet->setCellValue('P' . $start_column_num, 'Right SP');
				$worksheet->setCellValue('Q' . $start_column_num, 'GC SP');
				$worksheet->setCellValue('R' . $start_column_num, 'Left VP');
				$worksheet->setCellValue('S' . $start_column_num, 'Right VP');
				$worksheet->setCellValue('T' . $start_column_num, 'GC VP');
				$worksheet->setCellValue('U' . $start_column_num, 'Left RS');
				$worksheet->setCellValue('V' . $start_column_num, 'Right RS');
				$worksheet->setCellValue('W' . $start_column_num, 'GC RS');
				$worksheet->setCellValue('X' . $start_column_num, 'IGPSM');
				$worksheet->setCellValue('Y' . $start_column_num, 'Unilevel');
				$worksheet->setCellValue('Z' . $start_column_num, 'GC');

				//first row
				$row = 4;
				$first_row = $row;

				$sql = "SELECT 
							* 
						FROM 
							`dgo_tmp_pre_payout_group_report`
						WHERE 
							`group_name` = '{$member_group->group_name}' 
						ORDER BY 
							`member_id` ASC";
						
				$select_query = $this->db->query($sql);
				$members = $select_query->result();

				$select_query->free_result();

				$member_count = 1;
				if(count($members) > 0) $member_count = count($members);


				$data_array = array_fill (0,$member_count,array());

				$member_credited_amount = array();
				$account_id_search = array();
				foreach ($members as $key => $m)
				{
					if(empty($m->account_id) || empty($m->member_id)) continue;
					array_push($account_id_search,$m->account_id);
					$member_credited_amount[$m->account_id]['igpsm'] = 0;
					$member_credited_amount[$m->account_id]['unilevel'] = 0;
					$member_credited_amount[$m->account_id]['gc'] = 0;
				}

				//get credit logs for 
				if(!empty($account_id_search))
				{
					$account_ids = implode(",",$account_id_search);

					$igpsm_sql = "SELECT 
									`member_id`,`account_id`,`amount`
								FROM 
									`dgo_tmp_pre_payout_group_report` 
								WHERE
									`account_id` IN ({$account_ids})
								AND 
									`transaction_code` >= 100 
								AND 
									`transaction_code` <= 104 
								AND 
									`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'";

					$select_query = $this->db->query($igpsm_sql);
					$igpsm = $select_query->result();
					$select_query->free_result();
					$igpsm_amount = 0.00;

					foreach($igpsm as $i)
					{
						$member_credited_amount[$i->account_id]['igpsm'] += $i->amount;
					}

					$unilevel_sql = "SELECT
										`member_id`,`account_id`,`amount`
									FROM 
										`dgo_tmp_pre_payout_group_report`
									WHERE 
										`account_id` IN ({$account_ids})
									AND 
										`transaction_code` = 105 
									AND 
										`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'";

					$select_query = $this->db->query($unilevel_sql);
					$unilevel = $select_query->result();
					$select_query->free_result();
					$unilevel_amount = 0;
					foreach($unilevel as $u)
					{
						$member_credited_amount[$u->account_id]['unilevel'] += $u->amount;
					}

					$gc_sql = "SELECT 
									`member_id`,`account_id`,`amount`
								FROM 
									`dgo_tmp_pre_payout_group_report`
								WHERE 
									`account_id` IN ({$account_ids})
								AND 
									`transaction_code` >= 106
								AND 
									`transaction_code` <= 109
								AND 
									`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'";

					$select_query = $this->db->query($gc_sql);
					$gc = $select_query->result();
					$select_query->free_result();
					$gc_amount = 0;
					foreach($gc as $g)
					{
						$member_credited_amount[$g->account_id]['gc'] += $g->amount;
					}

					foreach ($members as $key => $m)
					{
						if(empty($m->account_id) || empty($m->member_id)) continue;

						$data_array_row = array();
						//place everything inside the row
						array_push($data_array_row, $m->member_id);
						array_push($data_array_row, $m->member_name);
						array_push($data_array_row, $m->group_name);
						array_push($data_array_row, $m->account_id);
						array_push($data_array_row, $m->sponsor_id);
						array_push($data_array_row, $m->upline_id);
						array_push($data_array_row, $m->status);
						array_push($data_array_row, $m->auto_payout);
						array_push($data_array_row, $m->funds);
						array_push($data_array_row, $m->gift_cheques);
						array_push($data_array_row, $m->monthly_maintenance_ctr);
						array_push($data_array_row, $m->annual_maintenance_ctr);
						array_push($data_array_row, $m->ms_monthly_maintenance_ctr);
						array_push($data_array_row, $m->ms_annual_maintenance_ctr);
						array_push($data_array_row, $m->left_sp);
						array_push($data_array_row, $m->right_sp);
						array_push($data_array_row, $m->gc_sp);
						array_push($data_array_row, $m->left_vp);
						array_push($data_array_row, $m->right_vp);
						array_push($data_array_row, $m->gc_vp);
						array_push($data_array_row, $m->left_rs);
						array_push($data_array_row, $m->right_rs);
						array_push($data_array_row, $m->gc_rs);
						array_push($data_array_row, number_format($member_credited_amount[$m->account_id]['igpsm'],2));
						array_push($data_array_row, number_format($member_credited_amount[$m->account_id]['unilevel'],2));
						array_push($data_array_row, number_format($member_credited_amount[$m->account_id]['gc'],2));

						//push the row to the data_array
						$data_array[$key] = $data_array_row;
						error_log("ahihi: {$row}");
						$row++;
					}
				}


				error_log("done");
				//write the 2d array
				error_log("write: start");
				$worksheet->fromArray($data_array,null,"A".$first_row);
				error_log("write: done");
				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);
				$worksheet->getColumnDimension('G')->setAutoSize(true);
				$worksheet->getColumnDimension('H')->setAutoSize(true);
				$worksheet->getColumnDimension('I')->setAutoSize(true);
				$worksheet->getColumnDimension('J')->setAutoSize(true);
				$worksheet->getColumnDimension('K')->setAutoSize(true);
				$worksheet->getColumnDimension('L')->setAutoSize(true);
				$worksheet->getColumnDimension('M')->setAutoSize(true);
				$worksheet->getColumnDimension('N')->setAutoSize(true);
				$worksheet->getColumnDimension('O')->setAutoSize(true);
				$worksheet->getColumnDimension('P')->setAutoSize(true);
				$worksheet->getColumnDimension('Q')->setAutoSize(true);
				$worksheet->getColumnDimension('R')->setAutoSize(true);
				$worksheet->getColumnDimension('S')->setAutoSize(true);
				$worksheet->getColumnDimension('T')->setAutoSize(true);
				$worksheet->getColumnDimension('U')->setAutoSize(true);
				$worksheet->getColumnDimension('V')->setAutoSize(true);
				$worksheet->getColumnDimension('W')->setAutoSize(true);
				$worksheet->getColumnDimension('X')->setAutoSize(true);
				$worksheet->getColumnDimension('Y')->setAutoSize(true);
				$worksheet->getColumnDimension('Z')->setAutoSize(true);

				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename='.$filename.'');
				header('Cache-Control: max-age=0');

				//save empty file
				$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
				if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
					unlink(FCPATH . "assets/media/tmp/" . $filename);
				}
				error_log("saving");
				$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
				error_log("saved");

				$objPHPExcel->disconnectWorksheets();
				unset($objPHPExcel);

				//exit(0);
			} catch (Exception $e) {
				//exit($e->getMessage());
			}
			
			echo "SUCCESS";
		}
		else
		{
			echo "FAILED";
		}
		
        return;	
	}
	
	public function merge($params = array())
	{

		$start_date = trim($params['start_date']);
		$end_date = trim($params['end_date']);
		
		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);
		
		$filenames = array();
		$member_group = $this->members_model->get_member_groups();
		
		if(empty($member_group))
		{
			$this->return_json("error","fail");
			return;
		}
		
		
		foreach($member_group as $mg)
		{
			$filenames[] = FCPATH . "assets/media/tmp/{$mg->group_name}_pre_payout_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";
		}
		
		set_time_limit(0); // eliminating the timeout
		ini_set('memory_limit', '2048M');
		
		$bigExcel = new PHPExcel();
		$bigExcel->removeSheetByIndex(0);

		$reader = new PHPExcel_Reader_Excel2007();
		
		foreach ($filenames as $filename)
		{
			if(file_exists($filename))
			{
				$excel = $reader->load($filename);
			    foreach ($excel->getAllSheets() as $sheet) {
			        $bigExcel->addExternalSheet($sheet);
			        break;
			    }
			    //unlink($filename);
			}
		}

		if(!is_dir(FCPATH . "assets/media/pre_payout"))
		{
			mkdir(FCPATH . "assets/media/pre_payout/", 0777);
		}

		$writer = new PHPExcel_Writer_Excel2007($bigExcel);
		$merged_filename = 'group_pre_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx';
		$writer->save(FCPATH . "assets/media/pre_payout/" . $merged_filename);
		
		echo "SUCCESS";
		
		return;
	}
}