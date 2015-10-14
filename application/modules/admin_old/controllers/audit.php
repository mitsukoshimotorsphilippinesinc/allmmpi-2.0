<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit extends Systems_Controller 
{
	function __construct() 
	{
  		parent::__construct();
		$this->set_navigation('audit_trail');
		// load pager library
		$this->load->library('pager');
		$this->load->model('tracking_model');
		$this->load->model('users_model');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{
		$from_date = trim($this->input->get_post('from_date'));
		$to_date = trim($this->input->get_post('to_date'));
		$action = strtoupper(trim($this->input->get_post('action')));
		$section = strtolower(trim($this->input->get_post('section')));
		$user_id = $this->input->get_post('user_id');
		//$facility_id = $this->input->get_post('facility_id');
		
		$export = $this->input->get_post('export');
		
		$users = $this->users_model->get_users();
		
		if (empty($from_date)) $from_date = date('Y-m-d');
		if (empty($to_date)) $to_date = date('Y-m-d');
		
		$where = "";
		
		if(empty($section))
			$section = 'admin';
		
		//date set
		$from_dt = $from_date;
		$to_dt = $to_date;
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= "(DATE(insert_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= "insert_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= "insert_timestamp <= '{$to_dt}'";
			
		//action set
		if($action != 'ALL' && !empty($action)) 
			$where .= " AND action = '{$action}'";
			
		if(!empty($user_id) && $user_id != '0')
			$where .= "AND user_id = '{$user_id}'";
		
		$total_records = $this->tracking_model->get_audit_logs_count($where, $section);
		

		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/audit/view',
		    'total_items' => $total_records,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		$audit_logs = $this->tracking_model->get_audit_logs($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC', "", $section);
		$logs = array();
		foreach($audit_logs as $row)
		{
			if(!empty($row->user_id)) //admin or inventory logs
			{
				$user = $this->users_model->get_user_by_id($row->user_id);
				$row->name = $user->first_name . ' ' . $user->last_name;
			}elseif(!empty($row->member_id)) //member logs
			{
				if($row->member_id != 0)
				{
					$member = $this->members_model->get_member_by_id($row->member_id);
					$row->name = $member->first_name . ' ' . $member->last_name;
				}
				else
				{
					$row->name = "None";
				}
			}
			
			//before and after list
			$details_before = json_decode($row->details_before);
			if(!empty($details_before))
			{
				$row->decoded_details_before = "<table class='table table-condensed'><tbody>";
				foreach($details_before->details as $k => $b)
				{	
					$row->decoded_details_before .= "<tr><td>{$k}</td><td>{$b}</td></tr>";
				}
				$row->decoded_details_before .= "</tbody></table>";
			}else
			{
				$row->decoded_details_before = "None";
			}
			
			$details_after = json_decode($row->details_after);
			if(!empty($details_after))
			{
				$row->decoded_details_after = "<table class='table table-condensed'><tbody>";
				foreach($details_after->details as $k => $b)
				{	
					$row->decoded_details_after .= "<tr><td>{$k}</td><td>{$b}</td></tr>";
				}
				$row->decoded_details_after .= "</tbody></table>";
			}else
			{
				$row->decoded_details_after = "None";
			}
				
		}

		// check if to export
		if ($export == 'excel')
		{
			$data = new ArrayClass(array(
				'from_date' => $from_date,
				'to_date' => $to_date,
				'audit_logs' => $audit_logs,
				'section' => $section,
				'action' => $action,
				'where' => $where
			));
			
			$this->_export($data);
		}
		else
		{
			$this->template->from_date = $from_date;
			$this->template->to_date = $to_date;
			$this->template->search_url = strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '';
			$this->template->audit_logs = $audit_logs;
			//$this->template->facilities = $facilities;
			$this->template->users = $users;
			$this->template->user_id = $user_id;
			$this->template->view('audit_trail/list');
		}
	}
	
	
	private function _export($data)
	{
		
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		
		$from_date = slugify($data->from_date);
		$to_date = slugify($data->to_date);
		//$section = slugify($data->section);
		//$action = slugify($data->action);
		
		$title = 'Audit Trail';
		
		$objPHPExcel->getProperties()->setTitle($title)->setDescription("Exported Audit Trail");
		$start_column_num = 5;

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($title);

		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':J' . $start_column_num)->getFont()->setBold(true);
			
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$header = "VITAL C HEALTH PRODUCTS, INC.";
		$header2 = "Audit Trail Logs";
		$header3 = " Between  " . $data->from_date . " to " . $data->to_date;

		$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
		
		$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
		
		$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
		
		$objPHPExcel->getActiveSheet()->getStyle('E' . 4)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E' . 4)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('E4:F4');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . 4, "DETAILS BEFORE");
		
		$objPHPExcel->getActiveSheet()->getStyle('G' . 4)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H' . 4)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . 4, "DETAILS AFTER");
		
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'NAME');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'MODULE NAME');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'TABLE NAME');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'ACTION'); 
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'FIELD');
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'VALUE');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'FIELD');
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $start_column_num, 'VALUE');		
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $start_column_num, 'REMARKS');
		$objPHPExcel->getActiveSheet()->setCellValue('J' . $start_column_num, 'TIMESTAMP');

		$row = $start_column_num + 1;
		
		$objPHPExcel->getActiveSheet()->freezePane('A' . $row);
		
		/* --------------------- */
		
		$offset = 0;
		$rows_per_page = 1000;
		$total_records = $this->tracking_model->get_audit_logs_count($data->where, $data->section);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		
		for ($page = 0; $page < $cnt; $page++)
		{
			$row = $start_column_num + 1;
			$limit = array('rows' => 1000, 'offset' => $page * $rows_per_page);

			$audit_logs = $this->tracking_model->get_audit_logs($data->where, $limit, 'insert_timestamp DESC', "", $data->section);
			
			/* --------------------- */

			//===============================
			//get payment transaction details
			//===============================

			foreach($audit_logs as $a)
			{
				if(!empty($a->user_id)) //admin or inventory logs
				{
					$user = $this->users_model->get_user_by_id($a->user_id);
					$name = $user->first_name . ' ' . $user->last_name;
				}elseif(!empty($a->member_id)) //member logs
				{
					if($a->member_id != 0)
					{
						$member = $this->members_model->get_member_by_id($a->member_id);
						$name = $member->first_name . ' ' . $member->last_name;
					}
					else
					{
						$name = "None";
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $name);
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $a->module_name);
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $a->table_name);
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $a->action);
				//$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $a->action); details before
				//$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $a->action); details after
				$objPHPExcel->getActiveSheet()->setCellValue('I'. $row, $a->remarks);			
				$objPHPExcel->getActiveSheet()->setCellValue('J'. $row, $a->insert_timestamp);
				
				//before and after list
				$details_before = json_decode($a->details_before);
				$before_row = $row;
				if(!empty($details_before) && !is_null($details_before))
				{
					if(!is_null($details_before->details))
					{
						foreach($details_before->details as $k => $b)
						{
							$objPHPExcel->getActiveSheet()->setCellValue('E'. $before_row, $k);
							$objPHPExcel->getActiveSheet()->setCellValue('F'. $before_row, html_entity_decode($b));
							$before_row++;
						}
					}
				}else
				{
					$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, "NONE");
					$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, "NONE");
				}
				
				$details_after = json_decode($a->details_after);
				$after_row = $row;
				if(!empty($details_after))
				{
					if(!is_null($details_after->details))
					{
						foreach($details_after->details as $k => $b)
						{
							$objPHPExcel->getActiveSheet()->setCellValue('G'. $after_row, $k);
							$objPHPExcel->getActiveSheet()->setCellValue('H'. $after_row, html_entity_decode($b));
							$after_row++;
						}
					}
				}else
				{
					$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, "NONE");
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $row, "NONE");
				}
				
				if ($before_row > $after_row)
					$row = $before_row;
				elseif($before_row < $after_row)
					$row = $after_row;
				
				$row++;
			}
		}
		
		
				
		if ($from_date == $to_date) $filename_date = $from_date;
		else $filename_date = $from_date .'_to_' . $to_date;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="transactions_between_'.$filename_date.'.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
	}
}
