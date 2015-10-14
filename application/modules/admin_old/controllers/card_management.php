<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_management extends Systems_Controller 
{
	function __construct() 
	{
  		parent::__construct();

		$this->set_navigation('card_management');
		$this->load->library('pager');
		$this->load->model('cards_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->load->model('jobs_model');
		$this->load->model('items_model');
		$this->load->model('users_model');

		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{
		$this->template->view('card_management/dashboard');
	}

	public function card_reencode()
	{
		// get reencode history
		$reencode_history = $this->tracking_model->get_audit_logs(array('module_name' => 'REENCODE CARD'), null, null, $fields = null, 'admin');

		foreach($reencode_history as $hist)
		{
			$hist_data = json_decode($hist->details_after);
			$hist->cards = implode(", ", $hist_data->cards);
			$hist->mod_ids = $hist_data->mods;
			$hist->mod_names = array();
			foreach($hist->mod_ids as $mod_id)
			{
				$modifier_data = $this->cards_model->get_modifiers(array('modifier_id' => $mod_id));
				if(sizeof($modifier_data) > 0)
				{
					$modifier_data = $modifier_data[0];
					$hist->mod_names[] = $modifier_data->modifier_name;
				}
			}
			$hist->mod_names = implode(", ", $hist->mod_names);

			$user = $this->users_model->get_user_by_id($hist->user_id);
			$hist->username = $user->username;

		}

		$this->template->reencode_history = $reencode_history;
		$this->template->view('card_management/card_reencode');
	}

	public function run_card_modifiers()
	{
		$cards = $this->input->post('cards');
		$mods = $this->input->post('mods');

		if(!$mods)
		{
			$this->return_json(0, 'Please select a modifier.');
			return;
		}


		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'REENCODE CARD',
			'table_name' => 'tr_member_acct_credit_logs',
			'action' => 'ADD',
			'details_before' => '',
			'details_after' => json_encode(array('cards' => $cards, 'mods' => $mods)),
			'remarks' => ''
		);

		$this->tracking_model->insert_logs('admin',$log_data);

		// get mods
		$mod_where = "modifier_id IN (" . implode(",", $mods) . ")";
		$modifiers = $this->cards_model->get_modifiers($mod_where);
		$arr_methods = array();
		$mods_run = array();
		$bad_cards = array();
		$good_cards = array();
		$return_str = '';

		foreach($cards as $card_id)
		{
			// get params
			$job_where = "job_type_id = '1' AND parameters LIKE '%card_id\":\"{$card_id}%' AND status = 'completed' ";
			$job_data = $this->jobs_model->get_jobs($job_where);

			if(sizeof($job_data) == 0)
			{
				$bad_cards[] = $card_id;
				// job entry not found
				continue;
			}
			$good_cards[] = $card_id;

			$job_data = $job_data[0];
			$params = json_decode($job_data->parameters);

			// convert obj to array
			$params = (array) $params;
			
			$arr_methods = array();
			$mods_run = array();
			foreach($modifiers as $modifier)
			{
				$method = "process_modifier_" . str_replace(" ", "_", strtolower($modifier->modifier_name));
				$s = "jobs/encoding/{$method}";
				$arr_methods[] = $s;
				$output = Modules::run($s,$params);
				if(!in_array($modifier->modifier_name, $mods_run)) $mods_run[] = $modifier->modifier_name;
			}
		}

		if($good_cards)
		{
			$return_str .= "Card(s) ".implode(', ',$good_cards)." was reencoded with ".implode(', ',$mods_run)."<br /><br />";
		}

		if($bad_cards)
		{
			$return_str .= "Card(s) ".implode(', ',$bad_cards)." was ignored since it is still active";
		}

		//$this->return_json(1, 'Success', array('methods' => $arr_methods, 'modifiers' => $mods_run));
		$this->return_json(1, $return_str);
		return;
	}

	public function get_rs_card_modifiers()
	{
		$card_id = $this->input->post('card_id');

		$card_data = $this->cards_model->get_rs_card(array(
			'card_id' => $card_id
		));

		if(sizeof($card_data) == 0)
		{
			$this->return_json(0, 'Card not Found');
			return;
		}

		$card_data = $card_data[0];

		$card_type_data = $this->cards_model->get_card_types(array(
			'code' => $card_data->type
		));

		if(sizeof($card_type_data) == 0) 
		{
			$this->return_json(0, 'Card type not Found');
			return;
		}

		$card_type_data = $card_type_data[0];

		$card_type_modifiers = $this->cards_model->get_card_modifiers(array('card_type_id' => $card_type_data->card_type_id));

		foreach($card_type_modifiers as $card_type_modifier)
		{
			$modifier_data = $this->cards_model->get_modifiers(array('modifier_id' => $card_type_modifier->modifier_id));
			if(sizeof($modifier_data) == 0) continue;

			$card_type_modifier->modifier_name = $modifier_data[0]->modifier_name;
		}

		$this->return_json(1, 'Success', array('modifiers' => $card_type_modifiers));
		return;
	}

	public function check_card_p2p_downlines()
	{
		$head_account_id = $this->input->post('head_account_id');

		if(empty($head_account_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$head_account_data = $this->members_model->get_member_accounts(array(
			'account_id' => $head_account_id
		));

		if(sizeof($head_account_data) == 0)
		{
			$this->return_json(0, 'Head Account not found');
			return;
		}

		$head_account_data = $head_account_data[0];
		$where = "node_address LIKE '{$head_account_data->node_address}%' AND account_id <> '{$head_account_id}' ";
		$uncredited_transactions = $this->members_model->get_member_account_product_transaction($where);
		// get account_id, usage, product name, product price
		$position_sorted = array();
		foreach($uncredited_transactions as $uncredited_transaction)
		{
			// initially set all to unused
			$uncredited_transaction->usage = 'UNUSED';

			// get correct usage from cm_member_account_credited_transaction
			$usage_where = "account_id = '{$head_account_id}' AND (left_account_id = '{$uncredited_transaction->account_id}' OR right_account_id = '{$uncredited_transaction->account_id}') ";
			$credited_data = $this->members_model->get_member_account_credited_transaction($usage_where);
			if(sizeof($credited_data) > 0) $uncredited_transaction->usage = 'USED';

			// get product_data
			$product_data = $this->items_model->get_products(array('product_id' => $uncredited_transaction->product_id),null,null,array('product_name','standard_retail_price'));
			$uncredited_transaction->product_data = $product_data[0];

			// get position based from head acct
			$pos = substr($uncredited_transaction->node_address, strlen($head_account_data->node_address), 1);
			$pos = ($pos == 1)?'RIGHT':'LEFT';

			$position_sorted[$pos][] = $uncredited_transaction;
		}

		// get all credited to head account
		$credited_stuff = $this->members_model->get_member_account_credited_transaction(array('account_id' => $head_account_id));
		foreach($credited_stuff as $stuff)
		{
			// get product_data
			$product_data = $this->items_model->get_products(array('product_id' => $stuff->product_id),null,null,array('product_name','standard_retail_price'));
			$stuff->product_data = $product_data[0];
		}
		$position_sorted['CREDITED'] = $credited_stuff;

		$this->return_json(1, 'Success', $position_sorted);
		return;
	}

	public function p2p_downline_check()
	{
		$this->template->view('card_management/p2p_downline_check');
	}

	public function fix_mismatch()
	{
		$missing_account_id = $this->input->post('missing_account_id');
		$missing_product_id = $this->input->post('missing_product_id');
		$log_id = $this->input->post('log_id');

		if(empty($missing_account_id) || empty($missing_product_id) || empty($log_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		// check account id
		$account_id = $this->members_model->get_member_accounts(array('account_id' => $missing_account_id));
		if(sizeof($account_id) == 0)
		{
			$this->return_json(0, 'Account ID not found');
			return;
		}
		
		// check product id
		$product_id = $this->items_model->get_products(array('product_id' => $missing_product_id));
		if(sizeof($product_id) == 0)
		{
			$this->return_json(0, 'Product ID not found');
			return;
		}

		// check log id
		$log_data = $this->tracking_model->get_audit_logs(array(
			'log_id' => $log_id
		), null, null, null, 'members');
		if(sizeof($log_data) == 0)
		{
			$this->return_json(0, 'Log ID not found');
			return;
		}
		$mismatch_data = json_decode($log_data[0]->details_after);

		// get lower product id
		$lower_product_id = $this->items_model->get_lower_product_from_ids($mismatch_data->waiting_product_id,$missing_product_id);

		// insert into cm_member_account_credited_transaction
		$data_credit_transaction = array(
			'account_id' => $mismatch_data->trigger_account_id,
			'left_account_id' => ($mismatch_data->missing_position == "LEFT")?$mismatch_data->waiting_account_id:$missing_account_id,
			'right_account_id' => ($mismatch_data->missing_position == "RIGHT")?$mismatch_data->waiting_account_id:$missing_account_id,
			'product_id' => $lower_product_id,
		);
		$this->members_model->insert_member_account_credited_transaction($data_credit_transaction);

		// update mismatch issue status
		$this->members_model->update_member_p2p_mismatch(array(
			'status' => 'FIXED'
		), array(
			'log_id' => $log_id
		));

		$this->return_json(1, 'success');
		return;
	}

	public function p2p_mismatch()
	{
		$mismatches = $this->members_model->get_member_p2p_mismatch();

		foreach($mismatches as $mismatch)
		{
			$log_data = $this->tracking_model->get_audit_logs(array(
				'log_id' => $mismatch->log_id
			), null, null, null, 'members');
			$mismatch->log_data = json_decode($log_data[0]->details_after);
		}

		$this->template->mismatches = $mismatches;
		$this->template->view('card_management/p2p_mismatch');
	}

	public function verify_card()
	{
		$card_id = $this->input->post('card_id');

		if(empty($card_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		// check sp
		$sp_card = $this->cards_model->get_sp_card(array('card_id' => $card_id));
		$rs_card = $this->cards_model->get_rs_card(array('card_id' => $card_id));
		$card_data = null;
		if(sizeof($sp_card) > 0)
		{
			$card_data = $sp_card[0];
		}
		elseif(sizeof($rs_card) > 0)
		{
			$card_data = $rs_card[0];
		}
		else
		{
			$this->return_json(0, 'Card not found');
			return;
		}

		$this->return_json(1, 'Success', array('card_data' => $card_data));
		return;
	}

	public function activate_cards()
	{
		$starting_index = $this->input->post('starting_index');
		$ending_index = $this->input->post('ending_index');
		$card_type = $this->input->post('card_type');

		if(empty($starting_index) || empty($ending_index) || empty($card_type))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		if(substr($starting_index, 0, 2) != substr($ending_index, 0, 2)) {
			$this->return_json(0, 'Cannot export multiple series type at the same time.');
			return;
		}

		$method = "update_{$card_type}_card";
		if(!method_exists($this->cards_model, $method))
		{
			$this->return_json(0, 'Card Type does not exist');
			return;
		}

		$where = "status <> 'USED' AND card_id BETWEEN '{$starting_index}' AND '{$ending_index}' ";
		$this->cards_model->update_sp_card(array(
			'status' => 'ACTIVE'
		), $where);

		$this->return_json(1, 'Success');
		return;
	}

	public function export_generation()
	{
		$starting_index = $this->input->post('starting_index');
		$ending_index = $this->input->post('ending_index');
		$card_type = $this->input->post('card_type');

		if(empty($starting_index) || empty($ending_index) || empty($card_type))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		if(substr($starting_index, 0, 2) != substr($ending_index, 0, 2))
		{
			$this->return_json(0, 'Cannot export multiple series type at the same time.');
			return;
		}

		$method = "get_{$card_type}_card";
		if(!method_exists($this->cards_model, $method))
		{
			$this->return_json(0, 'Card Type does not exist');
			return;
		}

		// check for starting series and ending series
		$starting_card = $this->cards_model->$method(array(
			'card_id' => $starting_index
		));
		if(sizeof($starting_card) == 0)
		{
			$this->return_json(0, 'Some of the card in the range specified does not exist');
			return;
		}

		$ending_card = $this->cards_model->$method(array(
			'card_id' => $ending_index
		));
		if(sizeof($ending_card) == 0)
		{
			$this->return_json(0, 'Some of the card in the range specified does not exist');
			return;
		}

		$filename = $this->generation_series_excel($starting_index, $ending_index, $card_type);
		
		$this->return_json(1, 'Success', array('filename' => $filename));
		return;
	}

	private function generation_series_excel($starting_index, $ending_index, $card_type)
	{
		$_timestamp = date("Ymd_his");
		$_card_type = strtolower($card_type);
		$card_type = strtoupper($card_type);
		$filename = "{$_card_type}_cards_{$starting_index}_to_{$ending_index}_generated_{$_timestamp}.xlsx";

		try {
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '1024M');
			
			$title = "{$card_type} Cards {$starting_index} to {$ending_index}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "{$card_type} Cards";

	        $worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			$title = "{$card_type} Cards";

			$start_column_num = 3;

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			//set column names
			$worksheet->setCellValue('A1', $title);

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Card ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Card Code');
			$worksheet->setCellValue('C' . $start_column_num, 'Status');
			$worksheet->setCellValue('D' . $start_column_num, 'Type');

		    $where = "card_id BETWEEN '{$starting_index}' AND '{$ending_index}' ";
		    $method = "get_{$card_type}_card";
		
			$count_cards = $this->cards_model->$method($where, null, null, "count(*) as cnt");
			if(!empty($count_cards))
				$total_items = $count_cards[0]->cnt;
			else
				$total_items = 0;
		
			$rows = 20000;
			$pages = $total_items / $rows;
			$page_count = floor($pages);
			if($pages - $page_count > 0) $page_count += 1;
			$page = 0;
			$row = 4;
			for ($page = 0; $page < $page_count; $page++)
			{
				$offset = $page * $rows;
				if($offset > 0) $offset += 1;
				$limit = array('rows' => $rows, 'offset' => $offset);
				$cards = $this->cards_model->$method($where, $limit);
				
				if($page != 0) $objPHPExcel->createSheet();
				$worksheet = $objPHPExcel->setActiveSheetIndex($page);
				
				$start_column_num = 3;

				//set width of first column
				$worksheet->getColumnDimension('A')->setWidth(12.00);
				$worksheet->mergeCells('A1:E1');

				//set column names
				$worksheet->setCellValue('A1', $title);

				// set column header to bold
				$worksheet->getStyle('A1')->getFont()->setBold(true);
				$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
				$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);

				//center column names
				$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				//set column names
				$worksheet->setCellValue('A1', $title);
				$worksheet->setCellValue('A' . $start_column_num, 'Card ID');
				$worksheet->setCellValue('B' . $start_column_num, 'Card Code');
				$worksheet->setCellValue('C' . $start_column_num, 'Status');
				$worksheet->setCellValue('D' . $start_column_num, 'Type');
				
				$row = 4;

			    foreach($cards as $card)
			    {
					$worksheet->setCellValue('A'. $row, $card->card_id);
					$worksheet->setCellValue('B'. $row, $card->card_code);
					$worksheet->setCellValue('C'. $row, $card->status);
					$worksheet->setCellValue('D'. $row, $card->type);

					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(true);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);

					$row++;
			    }
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);

			return $filename;
		} catch (Exception $e) {
			//exit($e->getMessage());
			echo $e->getMessage();
			exit($e->getMessage());
		}
	}

	public function card_types()
	{
		// get types
		$card_types = $this->cards_model->get_card_types(array('status' => 'ACTIVE'));
		foreach($card_types as $item)
		{
			// set card mapping
			$maps = $this->cards_model->get_upgrade_card_mapping(array('base_card_type_id' => $item->card_type_id), null, null, array('upgrade_card_type_code'));
			$mapping = array();
			foreach($maps as $i) $mapping[] = $i->upgrade_card_type_code;
			$item->mapping = $mapping;

			// set card point type name
			$point_type = $this->cards_model->get_card_types(array('card_type_id' => $item->merge_to_point_type), null, null, array('code'));
			$item->point_type_name = $point_type[0]->code;
		}
		$this->template->card_types = $card_types;
		$modifiers = $this->cards_model->get_modifiers();
		$this->template->modifiers = $modifiers;
		$this->template->colors = $this->color_picker();

		$this->template->view('card_management/card_types');
	}

	public function remove_upgrade_mapping()
	{
		$map_id = $this->input->post('map_id');

		if(empty($map_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_upgrade_card_mapping(array('map_id' => $map_id));
		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Mapping not Found');
			return;
		}

		$this->cards_model->delete_upgrade_card_mapping(array('map_id' => $map_id));
		$this->return_json(1, 'Ok');
		return;
	}

	public function add_upgrade_mapping()
	{
		$base_card_type_id = $this->input->post('base_card_type_id');
		$upgrade_card_type_id = $this->input->post('upgrade_card_type_id');

		if(empty($base_card_type_id) || empty($upgrade_card_type_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$base_card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $base_card_type_id
		));
		if(sizeof($base_card_type) == 0)
		{
			$this->return_json(0, 'Invalid Base Card Type');
			return;	
		}

		$upgrade_card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $upgrade_card_type_id
		));
		if(sizeof($upgrade_card_type) == 0)
		{
			$this->return_json(0, 'Invalid Upgrade Card Type');
			return;	
		}

		$base_card_type = $base_card_type[0];
		$upgrade_card_type = $upgrade_card_type[0];

		$existing = $this->cards_model->get_upgrade_card_mapping(array(
			'base_card_type_id' => $base_card_type->card_type_id,
			'upgrade_card_type_id' => $upgrade_card_type->card_type_id
		));

		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Mapping already exists');
			return;	
		}

		$this->cards_model->insert_upgrade_card_mapping(array(
			'base_card_type_id' => $base_card_type->card_type_id,
			'base_card_type_code' => $base_card_type->code,
			'upgrade_card_type_id' => $upgrade_card_type->card_type_id,
			'upgrade_card_type_code' => $upgrade_card_type->code,
			'user_id' => $this->user->user_id,
		));

		$this->return_json(1, 'Ok');
		return;
	}

	public function get_upgrade_mappings()
	{
		$upgrade_mappings = $this->cards_model->get_upgrade_card_mapping();
		$card_types = $this->cards_model->get_card_types(array('is_package' => 1), null, null, array('card_type_id', 'code'));

		$base_cards = array();
		$upgrade_cards = array();

		foreach($card_types as $item)
		{
			$mods = $this->cards_model->get_modifiers_by_card_type($item->code);

			if(in_array("UPGRADE", $mods))
				$upgrade_cards[$item->card_type_id] = $item->code;
			else
				$base_cards[$item->card_type_id] = $item->code;
		}

		$this->return_json(1, 'Ok', array(
			'upgrade_mappings' => $upgrade_mappings,
			'base_cards' => $base_cards,
			'upgrade_cards' => $upgrade_cards
		));
		return;
	}

	public function get_type_modifiers()
	{
		$id = $this->input->post('id');

		if(empty($id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$all_modifiers = $this->cards_model->get_modifiers();
		$tmp = array();
		foreach($all_modifiers as $row) $tmp[$row->modifier_id] = $row->modifier_name;
		$all_modifiers = $tmp;
		$card_modifiers = $this->cards_model->get_card_modifiers(array(
			'card_type_id' => $id
		));

		foreach($card_modifiers as $row) $row->name = $all_modifiers[$row->modifier_id];

		$this->return_json(1, 'Ok', array('card_modifiers' => $card_modifiers));
		return;
	}

	public function add_card_type_modifier()
	{
		$type_id = $this->input->post('type_id');
		$modifier_id = $this->input->post('modifier_id');
		$modifier_condition = $this->input->post('modifier_condition');

		if(empty($type_id) || empty($modifier_id) || empty($modifier_condition))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_modifiers(array(
			'card_type_id' => $type_id,
			'modifier_id' => $modifier_id
		));

		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Card Type Modifier already exists');
			return;
		}

		$data = array(
			'card_type_id' => $type_id,
			'modifier_id' => $modifier_id,
			'user_id' => $this->user->user_id,
			'condition' => $modifier_condition
		);

		$this->cards_model->insert_card_modifiers($data);

		$this->card_type_modifier_log('ADD',$data);


		$this->return_json(1, 'Ok');
		return;
	}

	public function delete_card_type_modifier()
	{
		$type_id = $this->input->post('type_id');
		$modifier_id = $this->input->post('modifier_id');

		if(empty($type_id) || empty($modifier_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}
		

		$existing = $this->cards_model->get_card_modifiers(array(
			'card_type_id' => $type_id,
			'modifier_id' => $modifier_id
		));

		$data = array(
			'card_type_id' => $type_id,
			'modifier_id' => $modifier_id,
			'user_id' => $existing[0]->user_id
		);

		$this->cards_model->delete_card_modifier($data);	

		$this->card_type_modifier_log('DELETE',$data);

		$this->return_json(1, 'Ok');
		return;
	}

	public function add_new_card_type()
	{
		$code = $this->input->post('code');
		$name = $this->input->post('name');
		$type = $this->input->post('type');
		$points_to_pair = $this->input->post('points_to_pair');
		$points = $this->input->post('points');
		$color = $this->input->post('color');
		$desc = $this->input->post('desc');

		if(!$color)
			$color = "Gray";

		if(empty($code) || empty($name) || empty($points_to_pair) || empty($points))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_types(array(
			'code' => $code
		));

		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Card Code Already Exists');
			return;
		}

		$data = array(
			'code' => $code,
			'name' => $name,
			'is_package' => $type,
			'status' => 'ACTIVE',
			'is_locked' => '0',
			'points_to_pair' => $points_to_pair,
			'points' => $points,
			'color' => $color,
			'description' => $desc
		);

		$this->cards_model->insert_card_types($data);

		$data['card_type_id'] = $this->db->insert_id();
		
		$this->card_type_log($data);

		$this->cards_model->update_card_types(array(
			'merge_to_point_type' => $data['card_type_id']
		), array(
			'card_type_id' => $data['card_type_id']
		));

		$this->return_json(1, 'Ok');
		return;
	}

	public function get_type_commission_bonuses()
	{
		$id = $this->input->post('id');

		if(empty($id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$type_bonuses = $this->cards_model->get_card_type_bonuses(array(
			'card_type_id' => $id
		));

		$this->return_json(1, 'Ok', array(
			'bonuses' => $type_bonuses
		));
		return;
	}

	public function delete_card_type_commission()
	{
		$id = $this->input->post('id');

		if(empty($id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		
		$this->card_type_comissions_log("",$id,"DELETE");

		$this->cards_model->delete_card_type_bonuses(array(
			'type_bonus_id' => $id
		));


		$this->return_json(1, 'Ok');
		return;
	}

	public function add_card_type_commission()
	{
		$id = $this->input->post('id');
		$commission_type = $this->input->post('commission_type');
		$bonus_type = $this->input->post('bonus_type');
		$qty_amount = $this->input->post('qty_amount');

		if(empty($id) || empty($commission_type) || empty($bonus_type) || empty($qty_amount))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $id
		));

		if(sizeof($card_type) == 0)
		{
			$this->return_json(0, 'Card Type not Found');
			return;
		}

		$data = array(
			'card_type_id' => $id,
			'commission_type' => $commission_type,
			'bonus_type' => $bonus_type,
			'qty_amount' => $qty_amount,
			'user_id' => $this->user->user_id
		);

		$this->cards_model->insert_card_type_bonuses($data);

		$data['type_bonus_id'] = $this->db->insert_id();

		$this->card_type_comissions_log($data, $data['type_bonus_id']);

		$this->return_json(1, 'Ok');
		return;
	}

	public function edit_card_type()
	{
		$id = $this->input->post('id');
		$code = $this->input->post('code');
		$name = $this->input->post('name');
		$type = $this->input->post('type');
		$points_to_pair = $this->input->post('points_to_pair');
		$point_type = $this->input->post('point_type');
		$points = $this->input->post('points');
		$color = $this->input->post('color');
		$desc = $this->input->post('desc');

		if(!$color)
			$color = "Gray";

		if(empty($id) || empty($code) || empty($name) || empty($points_to_pair) || empty($points))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_types(array(
			'card_type_id' => $id
		));

		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Card Type not Found');
			return;
		}

		$data = array(
			'code' => $code,
			'name' => $name,
			'is_package' => $type,
			'points_to_pair' => $points_to_pair,
			'merge_to_point_type' => $point_type,
			'points' => $points,
			'color' => $color,
			'description' => $desc
		);

		$this->cards_model->update_card_types($data, array(
			'card_type_id' => $id
		));

		$this->card_type_log($data,$id,"EDIT", $existing[0]);

		$this->return_json(1, 'Ok');
		return;
	}

	public function edit_card_type_commission()
	{
		$id = $this->input->post('id');
		$commission_type = $this->input->post('commission_type');
		$bonus_type = $this->input->post('bonus_type');
		$qty_amount = $this->input->post('qty_amount');

		if(empty($id) || empty($commission_type) || empty($bonus_type) || empty($qty_amount))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$type_commission = $this->cards_model->get_card_type_bonuses(array(
			'type_bonus_id' => $id
		));

		if(sizeof($type_commission) == 0)
		{
			$this->return_json(0, 'Commission not Found');
			return;
		}

		$data = array(
			'commission_type' => $commission_type,
			'bonus_type' => $bonus_type,
			'qty_amount' => $qty_amount
		);

		$this->cards_model->update_card_type_bonuses($data, array(
			'type_bonus_id' => $id
		));

		$this->card_type_comissions_log($data,$id,"EDIT");

		$this->return_json(1, 'Ok');
		return;
	}

	public function delete_card_type()
	{
		$id = $this->input->post('id');

		if(empty($id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		if(!$this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN'))
		{
			$this->return_json(0, 'You have no permission to delete card types');
			return;
		}	

		$existing = $this->cards_model->get_card_types(array(
			'card_type_id' => $id
		));

		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Card Type not Found');
			return;
		}

		$data['status'] = 'DELETED';

		$this->cards_model->update_card_types($data, array(
			'card_type_id' => $id
		));

		$this->card_type_log($data,$id,"DELETE");


		$this->return_json(1, 'Ok');
		return;
	}

	public function get_deleted_card_types()
	{
		$deleted = $this->cards_model->get_card_types(array("status"=>"DELETED"));
			
		$this->return_json(1,'Ok',array("deleted"=>$deleted));
		return;

	}

	public function get_deleted_card_series()
	{
		$deleted = $this->cards_model->get_card_series(array("status"=>"DELETED"));
			
		$this->return_json(1,'Ok',array("deleted"=>$deleted));
		return;

	}

	public function card_series()
	{
		// get series
		$card_series = $this->cards_model->get_card_series(array('status'=>'ACTIVE'), null, 'series_number ASC');

		// get series counts
		$card_series_counts = $this->tracking_model->get_card_series_generation_count_by_series();
		$tmp = array();
		foreach($card_series_counts as $item) $tmp[$item->card_series_code] = $item->generated_count;
		$this->template->card_series_counts = $tmp;

		// get series types
		$card_series_types = $this->cards_model->get_card_type_series(null, null, 'starting_index ASC');
		$tmp = array();

		foreach($card_series_types as $item)
		{
			$type = $this->cards_model->get_card_types(array(
				'card_type_id' => $item->card_type_id
			));
			if(sizeof($type) > 0) {
				$type = $type[0];
				$item->type_code = $type->code;
			}
			$tmp[$item->card_series_id][] = $item;
		}
		$this->template->card_series_types = $tmp;

		// get types
		$card_types = $this->cards_model->get_card_types(array('status'=>'active'), null, null, array('card_type_id', 'code', 'name', 'is_package'));
		
		$this->template->card_series = $card_series;
		$this->template->card_types = $card_types;
		$this->template->view('card_management/card_series');
	}

	public function generate_card_series()
	{
		$series_id = $this->input->post('series_id');
		$qty = $this->input->post('qty');
		$starting_index = $this->input->post('starting_index');
		$card_type = $this->input->post('card_type');

		if(empty($series_id) || empty($qty) || empty($starting_index) || empty($card_type))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$series_obj = $this->cards_model->get_card_series(array(
			'card_series_id' => $series_id
		));

		if(sizeof($series_obj) == 0)
		{
			$this->return_json(0, 'Series does not exist');
			return;
		}
		$series_obj = $series_obj[0];

		$series_number = $series_obj->series_number;
		$starting_index = $series_number.$starting_index;
		if(strlen($starting_index) != 10)
		{
			$this->return_json(0,'Invalid Starting Index');
			return;
		}


		$card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $card_type
		));

		if(sizeof($card_type) == 0)
		{
			$this->return_json(0, 'Card Type does not exist');
			return;
		}
		$card_type = $card_type[0];


		// static limit of card generation per batch
		if($qty > 100000)
		{
			$this->return_json(0, 'Maximum quantity per card generation is limited to 100,000');
			return;
		}


		// generation log
		$ending_index = $this->card_id_format($starting_index+$qty-1);
		
		if($str = $this->check_if_exist($starting_index,$ending_index))
		{
			$this->return_json(0, $str.'There is a conflict with existing card id');
			return;
		}

		$this->tracking_model->insert_card_series_generation_logs(array(
			'card_series_code' => $series_number,
			'qty' => $qty,
			'starting_index' => $starting_index,
			'ending_index' => $ending_index,
			'user_id' => $this->user->user_id,
		));

		// generate default card type
		$this->cards_model->insert_card_type_series(array(
			'card_type_id' => $card_type->card_type_id,
			'card_series_id' => $series_id,
			'starting_index' => $starting_index,
			'ending_index' => $ending_index,
			'qty' => $qty,
		));

		// start generation backend process
		$params = array(
			'is_package' => $series_obj->is_package,
			'starting_index' => $starting_index,
			'qty' => $qty,
			'card_type_code' => $card_type->code,
			'series_number' => $series_obj->series_number,
			'card_type_id' => $card_type->card_type_id,
			'series_id' => $series_id
		);
		$job_data = array(
			'job_type_id' => 2, // card generation
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);

		$job_id = $this->jobs_model->insert_id();
		
		job_exec($job_id, true);

		$this->return_json(1, 'Ok');
		return;
	}

	public function get_series_type_active_count()
	{
		$series_id = $this->input->post('series_id');
		$type_id = $this->input->post('type_id');
		$series_type_id = $this->input->post('series_type_id');

		if(empty($series_id) || empty($type_id) || empty($series_type_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_type_series(array(
			'card_series_id' => $series_id,
			'card_type_id' => $type_id,
			'card_series_type_id' => $series_type_id
		));

		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Series Type does not exists');
			return;
		}

		$card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $type_id
		));

		if(sizeof($card_type) == 0)
		{
			$this->return_json(0, 'Card Type does not exists');
			return;
		}
		$card_type = $card_type[0];
		$method = ((boolean)$card_type->is_package)?'get_sp_card':'get_rs_card';

		$existing = $existing[0];
		$where = "card_id BETWEEN {$existing->starting_index} AND {$existing->ending_index} AND status = 'ACTIVE' ";
		$count = $this->cards_model->$method($where, null, null, 'COUNT(1) AS cnt');
		$count = $count[0]->cnt;

		$this->return_json(1, 'Ok', array('active' => $count));
		return;
	}

	public function get_card_series_types()
	{
		$series_id = $this->input->post('id');

		if(empty($series_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$current_card_types = $this->cards_model->get_card_type_series(array(
			'card_series_id' => $series_id
		), null, null, array('card_type_id'));

		$tmp = array();
		foreach($current_card_types as $item)
		{
			$card = $this->cards_model->get_card_types(array(
				'card_type_id' => $item->card_type_id
			), null, null, array('code'));
			$tmp[] = $card[0]->code;
		}
		$current_card_types = $tmp;

		$all_card_types = $this->cards_model->get_card_types(null, null, null, array('card_type_id','code'));

		$tmp = array();
		foreach($all_card_types as $item) $tmp[$item->card_type_id] = $item->code;
		$all_card_types = $tmp;

		$possible_entries = array_diff($all_card_types, $current_card_types);

		$this->return_json(1, 'ok', array('items' => $possible_entries));
		return;
	}

	public function add_card_series()
	{
		$series_number = $this->input->post('series_number');
		$series_type = $this->input->post('series_type');

		if(empty($series_number))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		if(strlen($series_number) > 3)
		{
			$this->return_json(0, 'Invalid Series Number');
		}

		if(strlen($series_number) == 3)
			$where = "series_number = '".$series_number."' OR  series_number = '".substr($series_number,0,2)."'";
		else
			$where = "series_number = '".$series_number."' OR series_number LIKE '".$series_number."%'";

		$existing = $this->cards_model->get_card_series($where);

		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Series Number already exists');
			return;
		}

		$data = array(
			'series_number' => $series_number,
			'is_package' => $series_type
		);

		$this->cards_model->insert_card_series($data);

		$data['card_series_id'] = $this->db->insert_id();
		$data['status'] = 'ACTIVE';
		$data['is_locked'] = '0';

		$this->card_series_log($data,'',"ADD");

		$this->return_json(1, 'Ok');
		return;
	}

	public function add_card_series_type()
	{
		$series_id = $this->input->post('series_id');
		$type_id = $this->input->post('type_id');

		if(empty($series_id) || empty($type_id))
		{	
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_type_series(array(
			'card_type_id' => $type_id,
			'card_series_id' => $series_id
		));

		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Series Type already exists');
			return;
		}

		$this->cards_model->insert_card_type_series(array(
			'card_type_id' => $type_id,
			'card_series_id' => $series_id
		));

		$this->return_json(1, 'Ok');
		return;
	}

	public function delete_card_series()
	{
		$series_id = $this->input->post('id');

		if(empty($series_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		if(!$this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN'))
		{
			$this->return_json(0, 'You have no permission to delete card series');
			return;
		}	

		$existing = $this->cards_model->get_card_series(array(
			'card_series_id' => $series_id
		));

		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Series does not exist');
			return;
		}

		$data = array('status'=>'DELETED');

		$this->cards_model->update_card_series($data, array(
			'card_series_id' => $series_id
		));

		$this->card_series_log($data,$series_id,'DELETE');


		$this->return_json(1, 'Ok');
		return;
	}

	public function get_series_generation_history()
	{
		$series_id = $this->input->post('id');

		if(empty($series_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_series(array(
			'card_series_id' => $series_id
		));

		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Series does not exist');
			return;
		}

		$existing = $existing[0];

		$series_history = $this->tracking_model->get_card_series_generation_logs(array(
			'card_series_code' => $existing->series_number
		), null, 'starting_index ASC');

		foreach($series_history as $one_history)
		{
			//$one_history->card_series_code
			$card_series_data = $this->cards_model->get_card_series(array('series_number' => $one_history->card_series_code));
			$card_series_data = $card_series_data[0];
			$one_history->card_type = ($card_series_data->is_package == 0)?'rs':'sp';
		}

		$this->return_json(1, 'Ok', array(
			'history' => $series_history,
			'code' => $existing->series_number,
			'type' => $existing->is_package
		));
		return;
	}

	public function add_card_series_type_allotment()
	{
		$series_id = $this->input->post('series_id');
		$type_id = $this->input->post('type_id');
		$starting_index = $this->input->post('starting_index');
		$ending_index = $this->input->post('ending_index');

		if(empty($series_id) || empty($type_id) || empty($starting_index) || empty($ending_index))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$card_series = $this->cards_model->get_card_series(array(
			'card_series_id' => $series_id
		));

		if(sizeof($card_series) == 0)
		{
			$this->return_json(0, 'Card Series does not exist');
			return;
		}

		$card_series = $card_series[0];

		if(!$card_series->is_package)
		{
			$get_card_count = 'get_rs_card_count';
			$get_card = 'get_rs_card';
		}
		else
		{
			$get_card_count = 'get_sp_card_count';
			$get_card = 'get_sp_card';
		}

		$card_type = $this->cards_model->get_card_types(array(
			'card_type_id' => $type_id
		));

		if(sizeof($card_type) == 0)
		{
			$this->return_json(0, 'Card Type does not exist');
			return;
		}

		$card_type = $card_type[0];

		$series_number = $card_series->series_number;
		$starting_index = $series_number.$starting_index;
		$ending_index = $series_number.$ending_index;

		if($starting_index > $ending_index)
		{
			$this->return_json(0, 'Invalid Index');
			return;
		}

		//check if all cards in the index are generated
		/*$where = "card_id >= '{$starting_index}' AND card_id <= '{$ending_index}' ";
		$series_count = $this->cards_model->$get_card_count($where);
		if($series_count !=  (($ending_index+1)-$starting_index))
		{	
			$this->return_json(0, 'The index that you have entered is not in the range of generated cards or the range you have indicated does not yet exist. Please check the <b>Series Generation History</b>');
			return;
		}*/

		// check for same type sub series
		$where = "type <> '{$card_type->code}' AND  card_id >= '{$starting_index}' AND card_id <= '{$ending_index}' ";
		$series_type = $this->cards_model->$get_card($where);
		if(sizeof($series_type) == 0)
		{
			$this->return_json(0, 'The index that you have entered is not in the range of generated cards or the range you have indicated does not yet exist. Please check the <b>Series Generation History</b>');
			return;
		}

		//check the allotments that will be affected
		$where = "card_series_id = '{$series_id}'";
		$series = $this->cards_model->get_card_type_series($where);

		//check of cards are between the stated
		if($this->verify_inside_index($series,$starting_index,$ending_index))
		{
			$this->return_json(0, 'The index that you have entered is not in the range of generated cards');
			return;
		}

		if(sizeof($series) > 0)
		{
			foreach($series as $tmp)
			{
				if($tmp->starting_index >= $starting_index && $tmp->ending_index <= $ending_index)
					$this->cards_model->delete_card_type_series(array('card_series_type_id'=>$tmp->card_series_type_id));
				if($tmp->starting_index <= $starting_index && $tmp->ending_index <= $ending_index)
					$this->cards_model->update_card_type_series(array('ending_index'=>$this->card_id_format($starting_index-1), 'qty' => $starting_index-$tmp->starting_index),array('card_series_type_id'=>$tmp->card_series_type_id));
				if($tmp->starting_index >= $starting_index && $tmp->ending_index >= $ending_index)
					$this->cards_model->update_card_type_series(array('starting_index'=>$this->card_id_format($ending_index+1), 'qty' => $tmp->ending_index-$ending_index),array('card_series_type_id'=>$tmp->card_series_type_id));
			}
		}
		// adjust into rf_card_type_series
		$where = "card_series_id = '{$series_id}' AND  starting_index <= '{$starting_index}' AND ending_index >= '{$ending_index}' ";
		$series_type = $this->cards_model->get_card_type_series($where);

		if(sizeof($series_type) > 0)
		{
			$series_type = $series_type[0];

			if($series_type->starting_index-$starting_index != 0) {
				$start = $series_type->starting_index;
				$end = $starting_index-1;
				$this->cards_model->insert_card_type_series(array(
					'card_type_id' => $series_type->card_type_id,
					'card_series_id' => $series_type->card_series_id,
					'starting_index' => $this->card_id_format($start),
					'ending_index' => $this->card_id_format($end),
					'qty' => ($end-$start+1)
				));
			}

			if($series_type->ending_index-$ending_index != 0) {
				$start = $ending_index+1;
				$end = $series_type->ending_index;
				$this->cards_model->insert_card_type_series(array(
					'card_type_id' => $series_type->card_type_id,
					'card_series_id' => $series_type->card_series_id,
					'starting_index' => $this->card_id_format($start),
					'ending_index' => $this->card_id_format($end),
					'qty' => ($end-$start+1)
				));
			}

			$this->cards_model->delete_card_type_series($where);

		}

		// check for same adjacent type
		$left_adj = $this->card_id_format($starting_index-1);
		$right_adj = $this->card_id_format($ending_index+1);
		$left_card_data = $this->cards_model->get_sp_card_by_card_id($left_adj);
		$right_card_data = $this->cards_model->get_sp_card_by_card_id($right_adj);

		if(!empty($left_card_data) && !empty($right_card_data) && $card_type->code == $left_card_data->type && $left_card_data->type == $right_card_data->type) {
			// merge 3 parts

			$where = "starting_index <= '{$left_adj}' AND ending_index >= '{$left_adj}'";
			$left_series_type = $this->cards_model->get_card_type_series($where);
			$left_series_type = $left_series_type[0];
			$this->cards_model->delete_card_type_series($where);

			$where = "starting_index <= '{$right_adj}' AND ending_index >= '{$right_adj}'";
			$right_series_type = $this->cards_model->get_card_type_series($where);
			$right_series_type = $right_series_type[0];
			$this->cards_model->delete_card_type_series($where);

			$this->cards_model->insert_card_type_series(array(
				'card_type_id' => $type_id,
				'card_series_id' => $series_type->card_series_id,
				'starting_index' => $left_series_type->starting_index,
				'ending_index' => $right_series_type->ending_index,
				'qty' => ($right_series_type->ending_index-$left_series_type->starting_index+1)
			));

		} elseif(!empty($left_card_data) && $card_type->code == $left_card_data->type) {
			// merge left part


			$where = "starting_index <= '{$left_adj}' AND ending_index >= '{$left_adj}'";
			$left_series_type = $this->cards_model->get_card_type_series($where);
			$left_series_type = $left_series_type[0];
			$this->cards_model->delete_card_type_series($where);

			$this->cards_model->insert_card_type_series(array(
				'card_type_id' => $type_id,
				'card_series_id' => $series_type->card_series_id,
				'starting_index' => $left_series_type->starting_index,
				'ending_index' => $ending_index,
				'qty' => ($ending_index-$left_series_type->starting_index+1)
			));

		} elseif(!empty($right_card_data) && $card_type->code == $right_card_data->type) {
			// merge right part


			$where = "starting_index <= '{$right_adj}' AND ending_index >= '{$right_adj}'";
			$right_series_type = $this->cards_model->get_card_type_series($where);
			$right_series_type = $right_series_type[0];
			$this->cards_model->delete_card_type_series($where);

			$this->cards_model->insert_card_type_series(array(
				'card_type_id' => $type_id,
				'card_series_id' => $series_type->card_series_id,
				'starting_index' => $starting_index,
				'ending_index' => $right_series_type->ending_index,
				'qty' => ($right_series_type->ending_index-$starting_index+1)
			));

		} else {
			// proceed as normal


			$where = "card_id <= '".$this->card_id_format($starting_index)."' AND card_id >= '".$this->card_id_format($ending_index)."' AND `status` = 'USED' ";
			$used = $this->cards_model->$get_card_count($where);

			$this->cards_model->insert_card_type_series(array(
				'card_type_id' => $type_id,
				'card_series_id' => $series_id,
				'starting_index' => $starting_index,
				'ending_index' => $ending_index,
				'qty' => ($ending_index-$starting_index+1),
				'used' => $used
			));
		}

		// tr_card_type_allocation_log
		// $this->tracking_model->insert_card_type_allocation_logs(array(
		// 	'card_series_id' => '',
		// 	'card_type_id' => '',
		// 	'starting_index' => '',
		// 	'ending_index' => '',
		// 	'qty' => '',
		// 	'user_id' => ''
		// ));

		$method = ((boolean)$card_series->is_package)?"update_sp_card":"update_rs_card";
		// update cards on is_sp/rs_cards
		// $where = "status = 'ACTIVE' AND card_id BETWEEN '{$starting_index}' AND '{$ending_index}'";
		$where = "card_id BETWEEN '{$starting_index}' AND '{$ending_index}'";
		$this->cards_model->$method(array(
			'type' => $card_type->code
		), $where);

		$where = "card_series_id = '{$series_id}'";
		$series = $this->cards_model->get_card_type_series($where);

		if(sizeof($series) > 0)
		{
			foreach($series as $tmp)
			{
				$used = $this->cards_model->$get_card_count("card_id >= '".$tmp->starting_index."' AND card_id <= '".$tmp->ending_index."' AND status = 'used'");
				$this->cards_model->update_card_type_series(array('used'=>$used),array('card_series_type_id'=>$tmp->card_series_type_id));
			}	
		}

		$this->return_json(1, 'ok');
		return;
	}

	public function add_new_card_modifier()
	{
		$name = $this->input->post("name");
		$desc = $this->input->post("desc");

		if(empty($name))
		{
			$this->return_json(0,'Invalid Request');
			return;
		}

		if($this->cards_model->get_card_modifiers(array("modifier_name"=>$name)))
		{
			$this->return_json(0,'Modifier is already existing');
			return;
		}

		$this->cards_model->insert_card_modifiers(array(
			"modifier_name" => $name,
			"description" => $desc
		));


		$this->return_json(1, 'Ok');
		return;
	}

	public function update_card_modifier()
	{
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$desc = $this->input->post("desc");

		if(empty($name))
		{
			$this->return_json(0,'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_card_modifiers(array("modifier_name"=>$name));
		if( sizeof($existing) > 0 )
		{
			if($existing[0]->modifier_id != $id)	
			{
				$this->return_json(0,'Modifier is already existing');
				return;
			}
		}

		$this->cards_model->update_card_modifier(array("modifier_name"=>$name,"description"=>$desc), array("modifier_id"=>$id));
		$this->return_json(1,'Ok');
		return;
	}

	public function delete_card_modifier()
	{
		$id = $this->input->post("id");
		
		if(empty($id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$this->cards_model->delete_card_modifier(array('modifier_id'=>$id));	

		$this->return_json(1,"Ok");
		return;
	}

	public function modifiers()
	{
		$card_modifiers = $this->cards_model->get_card_modifiers();	
		$this->template->card_modifiers = $card_modifiers;
		$this->template->view('card_management/card_modifiers');
	}

	function restore_card_type()
	{
		$id = $this->input->post('id');

		$data['status'] = 'ACTIVE';

		$this->cards_model->update_card_types($data, array(
			'card_type_id' => $id
		));
		$this->card_type_log($data,$id,"RESTORE");

		$this->return_json(1,'Ok');
		return;
	}

	function restore_card_series()
	{
		$id = $this->input->post('id');

		$data['status'] = 'ACTIVE';

		$this->cards_model->update_card_series($data, array(
			'card_series_id' => $id
		));

		$this->card_series_log($data,$id,'RESTORE');

		$this->return_json(1,'Ok');
		return;
	}

	private function card_type_log($data = "", $id = 0, $action = "ADD", $olddata = "" )
	{
		
		if($id)
		{
			$existing = $this->cards_model->get_card_types(array(
				'card_type_id' => $id
			));

			if(isset($existing))	
			{	
				$existing = $existing[0];

				// $olddata[$key] = $value;
				// if(!isset($data[$key]) && $action != "DELETE") $data[$key] = $value;

				$olddata = json_encode($olddata);
			}
		}

		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARD TYPES',
			'table_name' => 'rf_card_types',
			'action' => $action,
			'details_before' => $olddata,
			'details_after' => json_encode($data),
			'remarks' => ''
		);

		$this->tracking_model->insert_logs('admin',$log_data);
	}

	private function card_type_comissions_log($data = "", $id = NULL, $action = "ADD", $olddata = "")
	{
		if(!is_null($id))
		{
			$existing = $this->cards_model->get_card_type_bonuses(array(
				'type_bonus_id' => $id
			));

			if(isset($existing))
			{
				$existing = $existing[0];

				foreach($existing as $key=> $value)
				{	
					$olddata[$key] = $value;
					if(!isset($data[$key]) && $action != "DELETE")
						$data[$key] = $value;
				}
				$olddata = json_encode($olddata);
			}
		}

		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARD TYPE COMMISIONS',
			'table_name' => 'rf_card_type_bonuses',
			'action' => $action,
			'details_before' => $olddata,
			'details_after' => json_encode($data),
			'remarks' => ''
		);

		$this->tracking_model->insert_logs('admin',$log_data);
	}

	private function card_type_modifier_log($action = "",$temp_data = "")
	{
		$data = '';
		$olddata = '';
		if($action == "ADD")
			$data = $temp_data;
		if($action == "DELETE")
			$olddata = $temp_data;

		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARD TYPE MODIFIER',
			'table_name' => 'rf_card_modifiers',
			'action' => $action,
			'details_before' => json_encode($olddata),
			'details_after' => json_encode($data),
			'remarks' => ''
		);

		$this->tracking_model->insert_logs('admin',$log_data);
	}

	private function card_series_log($data = "", $id = 0, $action = "ADD", $olddata = "")
	{	
		if($id)
		{
			$existing = $this->cards_model->get_card_series(array("card_series_id"=>$id));
			if(isset($existing))
			{
				$existing = $existing[0];

				foreach($existing as $key => $value)
				{
					$olddata[$key] = $value;
					if(!isset($data[$key]))
						$data[$key] = $value;
				}
				$olddata = json_encode($olddata);
			}
		}

		$log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'CARD SERIES',
				'table_name' => 'rf_card_series',
				'action' => $action,
				'details_before' => $olddata,
				'details_after' => json_encode($data),
				'remarks' => ''
		);

		$this->tracking_model->insert_logs('admin',$log_data);
	}

	private function check_if_exist($starting_index = '',$ending_index = '')
	{
		$card_series = $this->tracking_model->get_card_series_generation_logs();

		if(sizeof($card_series))
		{
			foreach($card_series as $series)
			{
				//$str .= "<br />".$series->starting_index." <= ".$starting_index." && ".$series->ending_index." >= ".$starting_index;
				if(($series->starting_index <= $starting_index &&  $series->ending_index >= $starting_index) || ($series->starting_index <= $ending_index &&  $series->ending_index >= $ending_index))
					return true;
			}
			return false;
		}
	}

	private function card_id_format($card_id)
	{	
		for($i = strlen($card_id) ; $i < 10 ; $i++)
			$card_id = '0'.$card_id;
		return $card_id;
	}

	private function color_picker()
	{
		$colors = '[{"color":"Orange"},{"color":"Yellow"},{"color":"Red"},{"color":"Brown"},{"color":"Violet"},{"color":"Blue"},{"color":"Green"},{"color":"Teal"},{"color":"White"},{"color":"Aquamarine"},{"color":"Pink"},{"color":"OrangeRed"},{"color":"YellowGreen"},{"color":"Maroon"},{"color":"Blue"},{"color":"Gray"}]';
		$colors = json_decode($colors);

		$ctr = 0; 

		$str  = "<div class=\"card-color-picker-holder\">\n";
		foreach($colors as $color) 
		{
			$str .="<div style=\"background:{$color->color};\" data-color=\"{$color->color}\" title=\"{$color->color}\" class=\"card-color-picker\"></div>\n";
			$ctr++; 
			if($ctr == 4) { 
				$str .='<div style="clear:both";></div>'; 
				$ctr = 0; 
			} 
		} 
		$str .= "</div>";

		$str .= "\n\n";
		$str .= "<style>";
		$str .= ".card-color-picker-holder {";
		$str .= "\t\tdisplay:none; position:absolute; background:#FFFFFF; padding:5px; border:1px solid #CCC;" ;
		$str .= "}";
		$str .= ".card-color-picker {";
		$str .= "\t\twidth:23px; height:18px; margin:1px; float:left; cursor:pointer;" ;
		$str .= "}";
		$str .= ".color-picker {";
		$str .= "\t\twidth:36px; height:26px; border-radius:4px; display:inline-block; margin-left:5px; border:1px solid #CCC; float:left; cursor:pointer;" ;
		$str .= "}";
		$str .= "</style>";
		
		return $str;	
	}


	private function verify_inside_index($series = array(),$starting_index = '',$ending_index = '')
	{
		if(sizeof($series))
		{	$starting_index_max = $series[0]->starting_index;
			$ending_index_max = $series[0]->ending_index;

			foreach($series as $tmp)
			{
				if($starting_index_max > $tmp->starting_index)
					$starting_index_max = $tmp->starting_index;
				if($ending_index_max < $tmp->ending_index)
					$ending_index_max = $tmp->ending_index;
			}

			if($starting_index_max <= $starting_index  &&  $ending_index <= $ending_index_max)
				return false;
		}
		return true;
	
	}

	public function excel_view()
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
	
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->getStyle('A1:H999')->getAlignment()->setWrapText(true); 
		
		//show timestamp
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Generated Card Series as of '.date("Y-m-d H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
	
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:A100')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		

		$objPHPExcel->getActiveSheet()->mergeCells('A3:A4');
		$objPHPExcel->getActiveSheet()->mergeCells('B3:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('C3:H3');
		$objPHPExcel->getActiveSheet()->mergeCells('I3:I4');
		$objPHPExcel->getActiveSheet()->mergeCells('J3:J4');

		$objPHPExcel->getActiveSheet()->getStyle('A3:A4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B3:B4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C3:H3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I3:I4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J3:J4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->setCellValue('A3','Series Number');
		$objPHPExcel->getActiveSheet()->setCellValue('B3','Count');
		$objPHPExcel->getActiveSheet()->setCellValue('C3','Card Type');
		$objPHPExcel->getActiveSheet()->setCellValue('C4','Code');
		$objPHPExcel->getActiveSheet()->setCellValue('D4','Starting Index');
		$objPHPExcel->getActiveSheet()->setCellValue('E4','Ending Index');
		$objPHPExcel->getActiveSheet()->setCellValue('F4','Unused');
		$objPHPExcel->getActiveSheet()->setCellValue('G4','Used');
		$objPHPExcel->getActiveSheet()->setCellValue('H4','Total');
		$objPHPExcel->getActiveSheet()->setCellValue('I3','Type');
		$objPHPExcel->getActiveSheet()->setCellValue('J3','Date Created');

		$start_column_num = 5;       

		$card_series_counts = $this->tracking_model->get_card_series_generation_count_by_series();
		$tmp = array();
		foreach($card_series_counts as $item) $tmp[$item->card_series_code] = $item->generated_count;

		$where = array("status"=>"ACTIVE");
		$order_by = 'series_number DESC';
		$card_series = $this->cards_model->get_card_series($where);


		if(sizeof($card_series) > 0)
		{
			foreach($card_series as $cards)
			{
				$objPHPExcel->getActiveSheet()->getCell('A'.$start_column_num)->setValueExplicit($cards->series_number, PHPExcel_Cell_DataType::TYPE_STRING);
				if(isset(  $tmp[$cards->series_number] ))
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$start_column_num, $tmp[$cards->series_number]);
				
				if($cards->is_package == 0)
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_column_num, 'Package');
				else
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$start_column_num, 'Sales');
				
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$start_column_num, $cards->insert_timestamp);

				$card_series_types = $this->cards_model->get_card_type_series(array('card_series_id'=>$cards->card_series_id), null, 'starting_index ASC');
				if(sizeof($card_series_types)>0)
				{
					foreach($card_series_types as $item)
					{
						$type = $this->cards_model->get_card_types(array(
							'card_type_id' => $item->card_type_id
						));
						if(sizeof($type) > 0) {
							$objPHPExcel->getActiveSheet()->getStyle('C'.$start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('D'.$start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('E'.$start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$start_column_num, $type[0]->code);
							$objPHPExcel->getActiveSheet()->getCell('D'.$start_column_num)->setValueExplicit($item->starting_index, PHPExcel_Cell_DataType::TYPE_STRING);							
							$objPHPExcel->getActiveSheet()->getCell('E'.$start_column_num)->setValueExplicit($item->ending_index, PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$start_column_num, $item->qty - $item->used);
							$objPHPExcel->getActiveSheet()->getStyle('G'.$start_column_num)->getNumberFormat()->setFormatCode('0');
							$objPHPExcel->getActiveSheet()->setCellValue('G'.$start_column_num,  $item->used);
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$start_column_num, $item->qty+0);
							$start_column_num++;	
						}
					} 
				}
				else
				{
					$start_column_num++;
				}
			}
		}

		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="generated_card_series_for_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}
