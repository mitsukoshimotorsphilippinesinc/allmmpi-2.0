<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('facilities_model');
		$this->load->model('members_model');
		$this->load->model('payment_model');
		$this->load->model('items_model');
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{
		//process transaction from ipay return url
		if($this->input->post('MerchantCode'))
		{	
			$this->payment_model->process_ipay('response',$this->input->post());
		}

		$member_id = $this->member->member_id;
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		$status = $this->input->get_post('status');
		$payment_method = $this->input->get_post('payment_method');
		
		$export = $this->input->get_post('export');
		
		$where = "member_id = " . $member_id;
		
		if(!empty($status) && $status != 'all')	$where .= " AND status = '" . strtoupper($status) . "'";
		if(!empty($payment_method) && $payment_method != 'all') $where .= " AND LOWER(transaction_type) = '" . $payment_method . "'";
		
		$from_dt = $from_date;
		$to_dt = $to_date;
		
		//$from_t = strtotime($from_date);
		//$to_t = strtotime($to_date);
		//if ($from_date !== false) $from_dt = date('Y-m-d H:i:s', $from_t); 
		//if ($to_date !== false) $to_dt = date('Y-m-d H:i:s', $to_t); 
		
		//if ($from_t !== false && $to_t !== false)
		if ((!($from_date == "")) && (!($to_date == "")))
			$where .= " AND (date(insert_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ";
		//else if ($from_t !== false && $to_t === false)
		else if ((!($from_date == "")) && ($to_date == ""))
			$where .= " AND insert_timestamp >= '{$from_date}'";
		//else if ($from_t === false && $to_t !== false)
		else if ((!($to_date == "")) && ($from_date == ""))
			$where .= " AND insert_timestamp <= '{$to_date}'";
			
		// get member info
		$member = $this->members_model->get_member_by_id($member_id);
		$facilities = $this->facilities_model->get_facilities();
		
		$facilities_array = array();
		
		foreach($facilities as $f) $facilities_array[$f->facility_id] = $f->facility_name;

		// search filters
		$get_data = "?status=" . $status . "&payment_method=" . $payment_method . "&from_date=" . $from_date . "&to_date=" . $to_date;
		
		// initialize pagination class
		// set pagination data
		/*
		$config = array(
			'pagination_url' => '/members/orders/page/',
		    'total_items' => $this->payment_model->get_payment_transaction_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		*/
		
		if($export == 'excel')
		{
			$data = new ArrayClass(array(
				'from_date' => $from_date,
				'to_date' => $to_date,
				'member_id' => $member_id,
				'status' => $status,
				'payment_method' => $payment_method,
				'where' => $where
			));
			
			$this->_export($data);
		}
		else
		{
			$tr_where = "d.member_id = '{$member_id}' ";

			if(!empty($status) && $status != 'all')	$tr_where .= " AND status = '" . strtoupper($status) . "'";
			//if(!empty($payment_method) && $payment_method != 'all') $tr_where .= " AND LOWER(transaction_type) = '" . $payment_method . "'";
			if(!empty($payment_method) && $payment_method != 'all') $tr_where .= " AND LOWER(payment_method) = '" . $payment_method . "'";

			if ((!($from_date == "")) && (!($to_date == "")))
				$tr_where .= " AND (date(a.insert_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ";
			else if ((!($from_date == "")) && ($to_date == ""))
				$tr_where .= " AND a.insert_timestamp >= '{$from_date}'";
			else if ((!($to_date == "")) && ($from_date == ""))
				$tr_where .= " AND a.insert_timestamp <= '{$to_date}'";

			// get total transactions			
			$sql = "SELECT
						1
						from is_payment_transaction_details a
						left join is_payment_transactions d on (a.transaction_id = d.transaction_id)
					WHERE
						{$tr_where}";
			
			
			
			$query = $this->db->query($sql);
			$total_transactions = $query->result();

			// set pagination
			$config = array(
				'pagination_url' => '/members/orders/page/',
			    'total_items' => count($total_transactions),
			    'per_page' => 10,
			    'uri_segment' => 4,
			);

			$this->pager->set_config($config);

			// display paged transaction
			$limit = "";
			if($this->pager->per_page != null && $this->pager->offset != null)
			{
				$limit = "limit {$this->pager->per_page} offset {$this->pager->offset} ";
			}
			
			$sql = "SELECT
						a.transaction_id,
						d.transaction_code,
						a.payment_method as transaction_type,
						a.amount as total_amount,
						d.status,
						d.facility_id,
						d.releasing_facility_id,
						a.insert_timestamp,
						a.remarks,
						d.ar_number
						from is_payment_transaction_details a
						left join is_payment_transactions d on (a.transaction_id = d.transaction_id)
					WHERE
						{$tr_where}
					ORDER BY
						d.insert_timestamp DESC
					{$limit}	";	
			
			$query = $this->db->query($sql);
			$transactions = $query->result();
		
			//var_dump($transactions);
		
			$between_timestamps = $from_date . " - " . $to_date;
			if (trim($between_timestamps) == "-") {
				$between_timestamps = "n/a";
			}

			//notice for paypal and bdo transactions
			$transaction_notice = array();
			if($this->input->get('transaction_code'))
			{
				$transaction = $this->payment_model->get_payment_transactions(array('transaction_code'=>$this->input->get('transaction_code')));
				if(sizeof($transaction)>0)
				{
					$transaction = $transaction[0];
					if($transaction->transaction_type == 'PAYPAL' || $transaction->transaction_type == 'BDO')
					{
						if($transaction->status == 'WAITING')
						{
							$transaction_notice['payment_method'] = $transaction->transaction_type;
							$transaction_notice['message'] = 'The transaction (<strong>'.$transaction->transaction_code.'</strong>) is still processing.'; 
						}
						elseif($transaction->status == 'FAILED' || $transaction->status == 'CANCELLED')
						{
							$transaction_notice['payment_method'] = $transaction->transaction_type;
							$transaction_notice['message'] = 'The transaction (<strong>'.$transaction->transaction_code.'</strong>) was not completed. Please try again'; 
						}
						else
						{
							$transaction_notice['payment_method'] = $transaction->transaction_type;
							$transaction_notice['message'] = 'The transaction (<strong>'.$transaction->transaction_code.'</strong>) has been completed. An email has been sent to you'; 
						}
					}
				}
			}

			$this->template->transaction_notice = $transaction_notice;
			$this->template->between_timestamps = strtoupper($between_timestamps);
		
			$this->template->facilities = $this->facilities_model->get_facilities(array('facility_type_id' => '2'));
			$this->template->facilities_array = $facilities_array;
			//$this->template->transactions = $this->payment_model->get_payment_transactions($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
			$this->template->transactions = $transactions;
			$this->template->member_id = $member_id;
			$this->template->member = $member;
			$this->template->from_date = $from_date;
			$this->template->to_date = $to_date;
			$this->template->payment_method = $payment_method;
			$this->template->status = $status;
			$this->template->get_data = $get_data;
			$this->template->current_page = 'orders';
			$this->template->view('orders');
		}
	}
	
	private function _export($data)
	{
		$where = $data->where;
		$from_date = slugify($data->from_date);
		$to_date = slugify($data->to_date);
		$member_id = $data->member_id;
		$status = $data->status;
		$payment_method = $data->payment_method;
	
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		
		$member = $this->members_model->get_member_by_id($member_id);
		$member_name = $member->first_name . ' ' . $member->last_name;
		$title = 'Member Orders';
		
		$objPHPExcel->getProperties()->setTitle($title)->setDescription("Exported Member Orders");
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
		
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':G' . $start_column_num)->getFont()->setBold(true);
			
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$header = "VITAL C HEALTH PRODUCTS, INC.";
		$header2 = "Member Orders for " . $member_name;
		$header3 = " Between  " . $data->from_date . " to " . $data->to_date;

		$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
		
		$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
		
		$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
		$objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
		
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'TRANSACTION CODE');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'FACILITY');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'PRODUCTS BOUGHT');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'PAYMENT METHOD'); 
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'TOTAL AMOUNT');
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'STATUS');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'DATE ORDERED');

		$row = $start_column_num + 1;
		
		$objPHPExcel->getActiveSheet()->freezePane('A' . $row);
		
		/* --------------------- */
		
		$offset = 0;
		$rows_per_page = 1000;
		$total_records = $this->payment_model->get_payment_transaction_count($where, "", 'insert_timestamp DESC');
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		
		for ($page = 0; $page < $cnt; $page++)
		{
			$row = $start_column_num + 1;
			$limit = array('rows' => 1000, 'offset' => $page * $rows_per_page);

			$transactions = $this->payment_model->get_payment_transactions($where, $limit, 'insert_timestamp DESC');
			
			/* --------------------- */

			//===============================
			//get payment transaction details
			//===============================

			foreach($transactions as $t)
			{
				//set transaction code
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $t->transaction_code);
				
				//set facility
				$facility = $this->facilities_model->get_facility_by_id($t->facility_id);
				if(!empty($facility))
					$facility_name = $facility->facility_name;
				else
					$facility_name = "None";
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $facility_name);
				
				//set products bought
				$products_row = $row;
				$transaction_products = $this->payment_model->get_payment_transaction_products(array('transaction_id' => $t->transaction_id, 'package_product_id' => 0));
				foreach($transaction_products as $p)
				{
					$product = $this->items_model->get_product_by_id($p->product_id);
						$product_bought = $p->quantity . ' ' . html_entity_decode($product->product_name);
					$objPHPExcel->getActiveSheet()->setCellValue('C'. $products_row, $product_bought);
					
					$package = $this->items_model->get_product_product_by_product_id($p->product_id);
					if(!empty($package)) //is a package
					{
						$products_row++;
						$product_products = $this->payment_model->get_payment_transaction_products(array('transaction_id' => $t->transaction_id, 'package_product_id' => $p->product_id));
						foreach($product_products as $prod)
						{
							$child = $this->items_model->get_product_by_id($prod->product_id);
							$child_product = '   - '.$prod->quantity . ' x ' . html_entity_decode($child->product_name);
							$objPHPExcel->getActiveSheet()->setCellValue('C'. $products_row, $child_product);
							$products_row++;
						}
					}
					else
					{
						$products_row++;
					}
				}
				
				//payment method
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $t->transaction_type);
				
				//total amount
				$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $t->total_amount);
				
				//status
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $t->status);
				
				//timestamp
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $t->insert_timestamp);
				
				if($products_row > $row)
					$row = $products_row;
				
				$row++;
			}
		}
		
		
				
		if ($from_date == $to_date) $filename_date = $from_date;
		else $filename_date = $from_date .'_to_' . $to_date;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="transactions_for_member_'.$member_name.'_between_'.$filename_date.'.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	
	}
	
	public function change_transaction_facility()
	{
		$transaction_id = $this->input->post('transaction_id');
		$facility_id = $this->input->post('facility_id');
		
		//check if items are available
		$transaction = $this->payment_model->get_payment_transaction_by_id($transaction_id);
		//get products
		$transaction_products_select = "SELECT *, sum(quantity) as total_quantity FROM is_payment_transaction_products WHERE transaction_id = {$transaction->transaction_id} GROUP BY product_id";
		$query = $this->db->query($transaction_products_select);
		$transaction_products = $query->result();
		
		$old_releasing_facility_id = $transaction->releasing_facility_id;
		$insufficient_qty_flag = 0;
		$out_of_stock_html = "<ul>";
		foreach($transaction_products as $p)
		{
			$product = $this->items_model->get_product_by_id($p->product_id);
			if($product->item_id != 0)
			{
				//check facility item
				$facility_item = $this->facilities_model->get_facility_items_by_facility_id_and_item_id($facility_id, $product->item_id);
				$total_current_qty = $facility_item->qty - $facility_item->qty_pending;
				if($p->total_quantity > $total_current_qty)
				{
					$insufficient_qty_flag++;
					if($total_current_qty <= 0) $total_current_qty = 0;
					$out_of_stock_html .= "<li>{$p->total_quantity} x {$product->product_name} (Available: {$total_current_qty})</li>";
				}
			}
		}
		$out_of_stock_html .= "</ul>";
		
		if($insufficient_qty_flag > 0) //at least one item is insufficient
		{
			$this->return_json('error', '<p>The following items are not available:</p>', array('html' => $out_of_stock_html));
			return;
		}
		
		if($transaction->transaction_type == 'FUNDS' || $transaction->transaction_type == 'GIFTCHEQUE')
		{
			foreach($transaction_products as $p)
			{
				//do changing of pending here
				$product = $this->items_model->get_product_by_id($p->product_id);
				if($product->item_id != 0)
				{
					//deduct from old facility
					$facility_item = $this->facilities_model->get_facility_items_by_facility_id_and_item_id($old_releasing_facility_id, $product->item_id);
					$qty_pending = $facility_item->qty_pending - $p->total_quantity;
					if($qty_pending <= 0) $qty_pending = 0;
					$data = array(
						'qty_pending' => $qty_pending
					);
					$facility_item_where = array(
						'facility_id' => $old_releasing_facility_id,
						'item_id' => $product->item_id
					);
					$this->facilities_model->update_facility_item($data, $facility_item_where);
				
					//add to pending in new facility
					$this->facilities_model->add_to_pending($facility_id, $product->item_id, $p->total_quantity);
				}
			}
		}
		
		$data = array(
			'releasing_facility_id' => $facility_id
		);
		
		$this->payment_model->update_payment_transaction($data, array('transaction_id' => $transaction_id));
		$transaction = $this->payment_model->get_payment_transaction_by_id($transaction_id);
		
		$data = array(
			'transaction_code' => $transaction->transaction_code
		);
		
		$this->return_json('ok', 'Success', $data);
		return;
	}
	
	public function get_transaction_details()
	{
		$transaction_id = $this->input->post('transaction_id');
		$transaction_code = $this->input->post('transaction_code');
		
		// get all records with same transaction_id
		$transaction_details = $this->payment_model->get_payment_transaction_details_by_transaction_id($transaction_id);
		
		if (empty($transaction_details)) {
			$data = array('html' => "There is something wrong with this transaction. Missing details for Transaction ID {$transaction_id}");
			$this->return_json('error', 'Error', $data);
			return;
		}
		
		$html = "<p>
					<label>Transaction ID <strong>{$transaction_id}</strong>:</label>
					<table class='table table-striped table-bordered'>
						<thead>
							<td><strong>Payment Method</strong></td>
							<td><strong>Amount</strong></td>
							<td><strong>Reference Number</strong></td>
							<td><strong>Reference Detail</strong></td>
							<td><strong>Remarks</strong></td>
						</thead>
						<tbody>
						";
		
		foreach ($transaction_details as $td) {
					
			$html .= "<tr>
					<td><label style=''>{$td->payment_method}</label></td>									
					<td><label style=''>{$td->amount}</label></td>										
					<td><label style=''>{$td->reference_number}</label></td>										
					<td><label style=''>{$td->reference_detail}</label></td>										
					<td><label style=''>{$td->remarks}</label></td>
					</tr>";		
		}			
		
		$html .=	"				
				</tbody>
				</table>						
				</p>";
		
		
		$data = array('html' => $html);
		$this->return_json('ok', 'Success', $data);
		return;
	}	
}
