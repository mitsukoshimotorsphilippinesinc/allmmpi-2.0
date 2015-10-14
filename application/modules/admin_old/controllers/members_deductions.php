<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Members_deductions extends Systems_Controller
{	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model("members_model");
  		$this->load->model("tracking_model");
  	}
	
	public function index()
	{
		$this->view();
	}

	public function view()
	{
		$this->template->view('deductions/list');
	}

	public function add()
	{
		if($_POST)
		{
			$member_id = $this->input->get_post("member_id");
			$total_amount = $this->input->get_post("total_amount");
			$amount_due = $this->input->get_post("amount_due");
			$deduction_per_payout = $this->input->get_post("deduction_per_payout");
			$terms = $this->input->get_post("terms");
			$remarks = $this->input->get_post("remarks");
			$data = array(
				'member_id' => $member_id,
				'total_amount' => $total_amount,
				'amount_due' => $amount_due,
				'deduction_per_payout' => $deduction_per_payout,
				'terms' => $terms,
				'remarks' => $remarks,
				'user_id' => $this->user->user_id
			);
			$this->members_model->insert_member_deductions($data);

			// add log on tr_admin_logs
			$log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'DEDUCTION',
				'table_name' => 'cm_member_deductions',
				'action' => 'ADD',
				'details_before' => '',
				'details_after' => json_encode($data),
				'remarks' => 'deduction added'
			);
			$this->tracking_model->insert_logs('admin',$log_data);

			$this->return_json(1,"success");
		}
		else
		{
			$this->template->view('deductions/add');
		}
	}

	public function add_to_all()
	{
		$total_amount = $this->input->get_post("total_amount");
		$amount_due = $this->input->get_post("amount_due");
		$deduction_per_payout = $this->input->get_post("deduction_per_payout");
		$terms = $this->input->get_post("terms");
		$remarks = $this->input->get_post("remarks");

		$data = array(
			'total_amount' => $total_amount,
			'amount_due' => $amount_due,
			'deduction_per_payout' => $deduction_per_payout,
			'terms' => $terms,
			'remarks' => $remarks,
			'is_to_all' => 1,
			'user_id' => $this->user->user_id
		);
		$this->members_model->insert_member_deductions($data);

		// add log on tr_admin_logs
		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'DEDUCTION',
			'table_name' => 'cm_member_deductions',
			'action' => 'ADD',
			'details_before' => '',
			'details_after' => json_encode($data),
			'remarks' => 'deduction to all'
		);
		$this->tracking_model->insert_logs('admin',$log_data);

		$this->return_json(1,"success");
	}

	public function download_deductions()
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $filename = "member_deductions_list.xlsx";

        try {
        	$title = "Member Deductions List";

        	$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Member Deductions";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_member_deduction_worksheet($worksheet);

			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit(0);
        } catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	private function _get_member_deduction_worksheet($worksheet)
	{
		$title = "Member Deductions List";

		$start_column_num = 3;

		// set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

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

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Total Amount');
		$worksheet->setCellValue('E' . $start_column_num, 'Amount Due');
		$worksheet->setCellValue('F' . $start_column_num, 'Deduction per Payout');
		$worksheet->setCellValue('G' . $start_column_num, 'Terms');
		$worksheet->setCellValue('H' . $start_column_num, 'Remarks');
		$worksheet->setCellValue('I' . $start_column_num, 'Lapsed Balance');
		$worksheet->setCellValue('J' . $start_column_num, 'Deduct to All');

		$row = 4;
		$deductions = $this->members_model->get_member_deductions(null, null, 'member_id ASC');
		foreach($deductions as $deduct)
		{
			if($deduct->is_to_all == 0)
			{
				$member = $this->members_model->get_member_by_id($deduct->member_id);
				$last_name = $member->last_name;
				$first_name = $member->first_name;
				$middle_name = $member->middle_name;
				$amount_due = $deduct->amount_due;
				$lapsed_balance = $deduct->lapsed_balance;
				$deduct_to_all = 'No';
			}
			else
			{
				$last_name = '';
				$first_name = '';
				$middle_name = '';
				$amount_due = 'n/a';
				$lapsed_balance = 'n/a';
				$deduct_to_all = 'Yes';
			}

			$worksheet->setCellValue('A'. $row, $last_name);
			$worksheet->setCellValue('B'. $row, $first_name);
			$worksheet->setCellValue('C'. $row, $middle_name);
			$worksheet->setCellValue('D'. $row, $deduct->total_amount);
			$worksheet->setCellValue('E'. $row, $amount_due);
			$worksheet->setCellValue('F'. $row, $deduct->deduction_per_payout);
			$worksheet->setCellValue('G'. $row, $deduct->terms);
			$worksheet->setCellValue('H'. $row, $deduct->remarks);
			$worksheet->setCellValue('I'. $row, $lapsed_balance);
			$worksheet->setCellValue('J'. $row, $deduct_to_all);

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

			// format total amount if negative
			$worksheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
			$worksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
			$worksheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
			$worksheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
			
			$row++;
		}
	}

	public function edit($deduction_id = null)
	{
		if($_POST)
		{
			$deduction_id = $this->input->get_post("deduction_id");
			if(isset($deduction_id) && !empty($deduction_id))
			{
				$deduction = $this->members_model->get_member_deductions(array('deduction_id'=>$deduction_id));
				if(count($deduction) > 0)
				{
					$is_to_all = $this->input->get_post("is_to_all");
					$member_id = $this->input->get_post("member_id");
					$total_amount = $this->input->get_post("total_amount");
					$amount_due = $this->input->get_post("amount_due");
					$deduction_per_payout = $this->input->get_post("deduction_per_payout");
					$terms = $this->input->get_post("terms");
					$remarks = $this->input->get_post("remarks");

					$data = array(
						'member_id' => $member_id,
						'total_amount' => $total_amount,
						'amount_due' => $amount_due,
						'deduction_per_payout' => $deduction_per_payout,
						'terms' => $terms,
						'remarks' => $remarks,
						'user_id' => $this->user->user_id,
						'is_to_all' => $is_to_all
					);
					$this->members_model->update_member_deductions($data, array('deduction_id'=>$deduction_id));

					$deduction = $deduction[0];
					$old_data = array(
						'member_id' => $deduction->member_id,
						'total_amount' => $deduction->total_amount,
						'amount_due' => $deduction->amount_due,
						'deduction_per_payout' => $deduction->deduction_per_payout,
						'terms' => $deduction->terms,
						'remarks' => $deduction->remarks,
						'user_id' => $deduction->user_id,
						'is_to_all' => $is_to_all
					);

					// edit log on tr_admin_logs
					$log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'DEDUCTION',
						'table_name' => 'cm_member_deductions',
						'action' => 'UPDATE',
						'details_before' => json_encode($data),
						'details_after' => json_encode($old_data),
						'remarks' => 'deduction edited'
					);
					$this->tracking_model->insert_logs('admin',$log_data);

					$this->return_json(1,"success");
				}
				else
				{
					$this->return_json(0,"failed","deduction_id not found");
				}
			}
			else
			{
				$this->return_json(0,"failed","null deduction_id");
			}
		}
		else
		{
			if(isset($deduction_id) && !empty($deduction_id))
			{
				$deduction = $this->members_model->get_member_deductions(array('deduction_id'=>$deduction_id));
				$deduction = $deduction[0];

				if($deduction->is_to_all == 0)
				{
					$member = $this->members_model->get_member_by_id($deduction->member_id);
					$member_id = $member->member_id;
					$member_name = $member->first_name . " " . $member->middle_name . " " . $member->last_name;
					$type_opt1 = 'checked="checked"';
					$type_opt2 = '';
					$member_search = '';
				}
				else
				{
					$member_id = '';
					$member_name = 'ALL MEMBERS';
					$type_opt2 = 'checked="checked"';
					$type_opt1 = '';
					$member_search = 'hide';
				}
				
				$term_opts = "";
				for($i = 1; $i <= 30; $i++)
				{
					$selected = ($deduction->terms == $i)?'selected="selected"':'';
					$term_opts .= "<option value='" . $i . "' " . $selected . ">" . $i . "</option>";
				}
				$html = '
				<div class="deduction-form">

					<div class="control-group">
						<label class="control-label"><strong>Deduction Type <em>*</em></strong></label>
						<div class="controls">
							<span style="margin-right: 20px;">
								<input type="radio" name="deduction_type" value="individual" ' . $type_opt1 . ' />
								Individual
							</span>
							<span>
								<input type="radio" name="deduction_type" value="all" ' . $type_opt2 . ' />
								All
							</span>
						</div>
						<span class="help-inline deduction-form-total-amount-help hide"></span>
					</div>

					<div class="control-group control-group-member-selection ' . $member_search . '">
						<label class="control-label"><strong>Select Member <em>*</em></strong></label>
						<div class="controls">
							<div style="margin-bottom: 10px;">
								<span class="label label-success deduction-form-member-view ' . $member_search . '">[' . $member_id . '] ' . $member_name . '</span>
							</div>
							<input type="hidden" class="deduction-form-member-id" value="' . $member_id . '">
							<div class="input-append">
								<input type="text" class="get-member-search-text" placeholder="Member Name" />
								<button class="btn btn-get-member-id"><i class="icon-search"></i> Search</button>
							</div>
						</div>
						<span class="help-inline deduction-form-member-id-help hide"></span>
					</div>

					<div class="control-group">
						<label class="control-label"><strong>Total Amount <em>*</em></strong></label>
						<div class="controls">
							<input type="text" class="deduction-form-total-amount" value="' . $deduction->total_amount . '">
						</div>
						<span class="help-inline deduction-form-total-amount-help hide"></span>
					</div>

					<div class="control-group hide">
						<label class="control-label"><strong>Amount Due <em>*</em></strong></label>
						<div class="controls">
							<input type="text" class="deduction-form-amount-due" value="' . $deduction->amount_due . '">
						</div>
						<span class="help-inline deduction-form-amount-due-help hide"></span>
					</div>

					<div class="control-group hide">
						<label class="control-label"><strong>Deduction per Payout <em>*</em></strong></label>
						<div class="controls">
							<input type="text" class="deduction-form-deduction-per-payout" value="' . $deduction->deduction_per_payout . '">
						</div>
						<span class="help-inline deduction-form-deduction-per-payout-help hide"></span>
					</div>

					<div class="control-group">
						<label class="control-label"><strong>Terms <em>*</em> (number of deductions per payout)</strong></label>
						<div class="controls">
							<input type="text" class="deduction-form-terms span1" value="1">
						</div>
						<span class="help-inline deduction-form-terms-help"><span class="label label-success">Deduction per Payout: ' . $deduction->deduction_per_payout . '</span></span>
					</div>

					<div class="control-group">
						<label class="control-label"><strong>Remarks</strong></label>
						<div class="controls">
							<textarea type="text" class="deduction-form-remarks span5" rows="4">' . $deduction->remarks . '</textarea>
						</div>
						<span class="help-inline deduction-form-remarks-help hide"></span>
					</div>
				</div>
				';
				$this->return_json(1,"success",$html);
			}
			else
			{
				$this->return_json(0,"failed");
			}
		}
	}

	public function select_member()
	{
		$search_text = $this->input->get_post("search_text");
		$search_count = 0;
		if(empty($search_text))
		{
			$html = "<div style='text-align: center; font-weight: bold;'>No Member Found</div>";
		}
		else
		{
			$keys = explode(" ",strtoupper($search_text));
			for ($i = 0; $i < count($keys); $i++)
		    {
		      $escaped_keys[] = mysql_real_escape_string($keys[$i]);
		    }

		    $key_count = count($escaped_keys);

		    // get possible combinations
		    $combinations = array();

		    $this->load->library('Math_Combinatorics');
		    $combinatorics = new Math_Combinatorics;
		    foreach( range(1, count($escaped_keys)) as $subset_size ) {
		        foreach($combinatorics->permutations($escaped_keys, $subset_size) as $p) {
		          $combinations[sizeof($p)-1][] = $p;
		        }
		    }

		    $combinations = array_reverse($combinations);

		    // exact match search
		    $has_exact = false;
		    $tmp_members = array();

		    foreach($combinations as $comb_group)
		    {
				foreach($comb_group as $comb)
				{
					$name = strtoupper(join('', $comb));
					$sql = "
						SELECT * FROM cm_members WHERE
						REPLACE(CONCAT(last_name, first_name, middle_name),' ','') LIKE '%{$name}%';
					";
		        	$query = $this->db->query($sql);
		        	if(count($query->result_array()) > 0)
		        	{
		          		$tmp_members = $query->result_object();
		          		$has_exact = true;
		          		break;
		        	}
		      	}
		      	if($has_exact)
		      	{
		        	break;
		      	}
		    }

		    if(count($tmp_members) > 0)
		    {
		    	$html = "
		    		<table class='table table-striped table-condensed'>
	    				<thead>
	    					<th style='width: 70px;'>Member ID</th>
	    					<th>Name</th>
	    					<th style='width: 50px;'>&nbsp;</th>
	    				</thead>
	    			</table>
		    		<div style='max-height: 300px; overflow: auto;'>
		    			<table class='table table-striped table-condensed'>";
			    foreach($tmp_members as $mem)
			    {
			    	$html .= "<tr>
			    				<td style='width: 70px;'>{$mem->member_id}</td>
			    				<td>{$mem->first_name} {$mem->middle_name} {$mem->last_name}</td>
			    				<td style='width: 50px;'><button class='btn btn-primary btn-small btn-member-selection' data-member-id='{$mem->member_id}' data-member-name='{$mem->first_name} {$mem->middle_name} {$mem->last_name}'>Select</button></td>
			    			</tr>";
			    	$search_count++;
			    }
			    $html .= "</table>";
			    $html .= "</div>";
		    }
		    else
		    {
		    	$html = "<div style='text-align: center; font-weight: bold;'>No Member Found</div>";
		    }
		}
		$data = array(
	    	'html' => $html,
	    	'count' => $search_count
	    );
		$this->return_json(1,"success", $data);
	}

	public function get_member_deductions()
	{
		$search_member_name = $this->input->get_post("search_member_name");
		if(empty($search_member_name))
		{
			// display all deductions group by member_id
			$deductions = $this->members_model->get_member_deductions(null,null,'member_id ASC');
			$results = array();
			foreach($deductions as $member_deduction)
			{
				if($member = $this->members_model->get_member_by_id($member_deduction->member_id))
				{
					$entry = array(
						'deduction_id' => $member_deduction->deduction_id,
						'member_id' => $member_deduction->member_id,
						'last_name' => $member->last_name,
						'first_name' => $member->first_name,
						'middle_name' => $member->middle_name,
						'total_amount' => number_format($member_deduction->total_amount,2),
						'amount_due' => number_format($member_deduction->amount_due,2),
						'deduction_per_payout' => number_format($member_deduction->deduction_per_payout,2),
						'terms' => $member_deduction->terms,
						'remarks' => $member_deduction->remarks
					);
					$results[] = $entry;
				}

				// include is_to_all entries
				if($member_deduction->is_to_all == 1)
				{
					$entry = array(
						'deduction_id' => $member_deduction->deduction_id,
						'member_id' => '',
						'last_name' => '',
						'first_name' => 'ALL MEMBERS',
						'middle_name' => '',
						'total_amount' => number_format($member_deduction->total_amount,2),
						'amount_due' => number_format($member_deduction->amount_due,2),
						'deduction_per_payout' => number_format($member_deduction->deduction_per_payout,2),
						'terms' => $member_deduction->terms,
						'remarks' => $member_deduction->remarks
					);
					$results[] = $entry;
				}
			}
			$data = array(
				'count' => count($deductions),
				'results' => $results
			);
		}
		else
		{
			// search member name
			$keys = explode(" ",strtoupper($search_member_name));
			for ($i = 0; $i < count($keys); $i++)
		    {
		      $escaped_keys[] = mysql_real_escape_string($keys[$i]);
		    }

		    $key_count = count($escaped_keys);

		    // get possible combinations
		    $combinations = array();

		    $this->load->library('Math_Combinatorics');
		    $combinatorics = new Math_Combinatorics;
		    foreach( range(1, count($escaped_keys)) as $subset_size ) {
		        foreach($combinatorics->permutations($escaped_keys, $subset_size) as $p) {
		          $combinations[sizeof($p)-1][] = $p;
		        }
		    }

		    $combinations = array_reverse($combinations);

		    // exact match search
		    $has_exact = false;
		    $tmp_members = array();

		    foreach($combinations as $comb_group)
		    {
				foreach($comb_group as $comb)
				{
					$name = strtoupper(join('', $comb));
					$sql = "
						SELECT * FROM cm_members WHERE
						REPLACE(CONCAT(last_name, first_name, middle_name),' ','') LIKE '%{$name}%';
					";
		        	$query = $this->db->query($sql);
		        	if(count($query->result_array()) > 0)
		        	{
		          		$tmp_members = $query->result_object();
		          		$has_exact = true;
		          		break;
		        	}
		      	}
		      	if($has_exact)
		      	{
		        	break;
		      	}
		    }

		    $results = array();
		    $deduction_count = 0;

			// get deduction per member
			foreach($tmp_members as $mem)
			{
				$deductions_per_member = $this->members_model->get_member_deductions(array('member_id'=>$mem->member_id));
				foreach($deductions_per_member as $deduction)
				{
					$entry = array(
						'deduction_id' => $deduction->deduction_id,
						'member_id' => $deduction->member_id,
						'last_name' => $mem->last_name,
						'first_name' => $mem->first_name,
						'middle_name' => $mem->middle_name,
						'total_amount' => number_format($deduction->total_amount,2),
						'amount_due' => number_format($deduction->amount_due,2),
						'deduction_per_payout' => number_format($deduction->deduction_per_payout,2),
						'terms' => $deduction->terms,
						'remarks' => $deduction->remarks
					);
					$results[] = $entry;
					$deduction_count++;
				}
			}
			$data = array(
				'count' => $deduction_count,
				'results' => $results
			);
		}
		$this->return_json(1,"success", $data);
	}
}