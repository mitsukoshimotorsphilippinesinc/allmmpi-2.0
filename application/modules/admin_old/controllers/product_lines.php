<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_lines extends Systems_Controller {

	private $_validation_rule = array(
			array(
							'field' => 'product_line',
							'label' => 'Product Line',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'is_visible',
							'label' => 'Visible',
							'rules' => 'trim|required'
			)

	);

	function __construct()
	{
		// call the base model constructor
		parent::__construct();
		$this->load->model('items_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('product_lines');

	}

	public function index()
	{
		$this->view();
	}

	public function view()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		
		
		$search_url = "";
	
		if (($search_text == "") || empty($search_text)) {
			$where = "";			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/admin/product_lines/view/',
		    'total_items' => $this->items_model->get_product_line_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);


		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;	 
		
		$this->template->product_lines = $this->items_model->get_product_lines($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('product_lines/list');
	}

	public function add()
	{
		$data = "";

		$html = $this->load->view('/product_lines/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}

	public function confirm_add() {
		$product_line = $this->input->post('product_line');
		$is_visible = $this->input->post('is_visible');
		
		$_product_line = strtoupper($product_line);
		$_is_visible = ($is_visible == 1) ? "YES" : "NO";
				
		$html = "<p><label>You are about to add a new Product Line with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_product_line}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Visible?</strong></label></td>
							<td><label class=''>{$_is_visible}</label></td>		
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function add_product_line() {
		$product_line_name = $this->input->post('product_line');
		$is_visible = $this->input->post('is_visible');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		
		
		$product_line = $this->items_model->get_product_lines(array("product_line" => strtoupper($product_line_name)));
		if(!empty($product_line))
		{
			$this->return_json("error","This Product Line already exists.");
			return;
		}
		
		// insert the new item
		$data = array(
			'product_line' => strtoupper($product_line_name),
			'is_visible' => strtoupper($is_visible)
		);
		
		$this->items_model->insert_product_line($data);
	
		$table_id = $this->items_model->insert_id();
		
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_product_type_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCT LINES',
			'table_name' => 'rf_product_lines',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_product_type_log_data);
	
 		$this->return_json("ok","Product Line added successfully");
		return;
	}
	
	public function edit($product_line_id = 0)
	{
		$_product_line_id = $this->input->post('_product_line_id');
		
		$_product_line = $this->items_model->get_product_line_by_id($_product_line_id);

		$data = array(
			'product_line' => $_product_line
		);

		$html = $this->load->view('/product_lines/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}

	public function confirm_edit() {
		$product_line = $this->input->post('product_line');
		$is_visible = $this->input->post('is_visible');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		$product_line_id = $this->input->post('product_line_id');

		$_product_line = strtoupper($product_line);
		$_is_visible = ($is_visible == 1) ? "YES" : "NO";
		$_is_gc_buyable = ($is_gc_buyable == 1) ? "YES" : "NO";
			
		$html = "<p><label>You are about to add a new Product Line with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_product_line}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Visible?</strong></label></td>
							<td><label class=''>{$_is_visible}</label></td>		
						</tr>
							</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_product_line() {
		
		$product_line_name = $this->input->post('product_line');
		$is_visible = $this->input->post('is_visible');
		$product_line_id = $this->input->post('product_line_id');
		
		if(empty($product_line_id))
		{
			$this->return_json("error","Invalid Product Line ID.");
			return;
		}
		
		$product_line = $this->items_model->get_product_line_by_id($product_line_id);
	
		if(empty($product_line) || is_null($product_line))
		{
			$this->return_json("error","This Product Line does not exist.");
			return;
		}
		
		$product_line_name = strtoupper($product_line_name);
	
		// edit the product_line
		$data = array(
				'product_line' => $product_line_name,
				'is_visible' => $is_visible
		);
		
		$previous_product_line_data = $this->items_model->get_product_line_by_id($product_line_id);
		$details_before = array('id' => $product_line_id, 'details' => $previous_product_line_data);
		$details_before = json_encode($details_before);

		$this->items_model->update_product_line($data, array('product_line_id' => $product_line_id));

		$details_after = array('id' => $product_line_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_product_line_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCT LINES',
			'table_name' => 'rf_product_lines',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_product_line_log_data);
		
		$this->return_json("ok","Product Line edited.");
		return;	
	}

	public function delete($product_line_id = 0)
	{
		$product_line_id = $this->input->post('product_line_id');	
		
		$product_line = $this->items_model->get_product_line_by_id($product_line_id);

		$data = array(
			'product_line' => $product_line
		);

		$html = $this->load->view('/product_lines/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$product_line_id = $this->input->post('product_line_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_product_line() {	
		$product_line_id = $this->input->post('product_line_id');
		
		if(empty($product_line_id))
		{
			$this->return_json("error","Invalid Product Line ID.");
			return;
		}
		
		$product_line = $this->items_model->get_product_line_by_id($product_line_id);

		if (!empty($product_line)) {			
			$details_before = array('id' => $product_line_id, 'details' => $product_line);
			$details_before = json_encode($details_before);

			$this->items_model->delete_product_line(array('product_line_id' => $product_line_id));

			$delete_product_line_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PRODUCT LINES',
				'table_name' => 'rf_product_lines',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'details_after' => "",
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $delete_product_line_log_data);
		
		} else {
			$this->return_json("error","This Product Line does not exist.");
			return;
		}
	
		$this->return_json("ok","Product Line deleted.");
		return;
		
	}
	
	public function pdf_view(){
		$this->load->library('mpdf');
		$product_lines = $this->items_model->get_product_lines();

		if (!empty($product_lines))
		{
			$current_year = date('Y');
			$current_month = date('M');
			$current_day = date('d');
			$date = $current_month . ' ' . $current_day . ', ' . $current_year;
			$current_month_num = date('m');
			$date_as_num = $current_month_num . $current_day . $current_year;
			$timestamp = date('Y-m-d H:i:s');
			$data = array( 
				'product_lines' => $product_lines,
				'date' => $date,
				'timestamp' => $timestamp
			);
			$this->mpdf->WriteHTML($this->load->view('product_lines/pdf_view',$data,TRUE));
			$this->mpdf->Output('list_of_product_lines_' . $date_as_num, 'I'); 
		}	
	}

	public function excel_view(){
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
 
        $objPHPExcel->setActiveSheetIndex(0);
		
		$product_lines = $this->items_model->get_product_lines();

		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		
		//show timestamp
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'List of Item Types as of '.date("Y-m-d H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$start_column_num.':F'.$start_column_num);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_column_num.':F'.$start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$start_column_num++;
		
		
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Product Line');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Visible?');
		
		$row = ++$start_column_num;
		foreach ($product_lines as $i)
		{
			$is_visible =  ($i->is_visible == 1) ? "Yes" : "No";
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $i->product_line);
			$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $is_visible);
			
			//centers each entry
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$row++;
		}
			
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_of_product_lines_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}