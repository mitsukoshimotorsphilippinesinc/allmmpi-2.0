<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_types extends Systems_Controller {

	private $_validation_rule = array(
			array(
							'field' => 'name',
							'label' => 'Name',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'is_visible',
							'label' => 'Visible',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'is_gc_buyable',
							'label' => 'GC Buyable',
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
		$this->set_navigation('product_types');

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
			'pagination_url' => '/admin/product_types/view/',
		    'total_items' => $this->items_model->get_product_type_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);


		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;	 
		
		$this->template->product_types = $this->items_model->get_product_types($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('product_types/list');
	}

	public function add()
	{
		$data = "";

		$html = $this->load->view('/product_types/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}

	public function confirm_add() {
		$name = $this->input->post('name');
		$is_visible = $this->input->post('is_visible');
		$is_regular_buyable = $this->input->post('is_regular_buyable');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		$is_package = $this->input->post('is_package');
		$is_cpoints = $this->input->post('is_cpoints');
		$is_igpsm = $this->input->post('is_igpsm');
		
		$_name = strtoupper($name);
		$_is_visible = ($is_visible == 1) ? "YES" : "NO";
		$_is_gc_buyable = ($is_gc_buyable == 1) ? "YES" : "NO";
		$_is_regular_buyable = ($is_regular_buyable == 1) ? "YES" : "NO";
		$_is_package = ($is_package == 1) ? "YES" : "NO";
		$_is_cpoints = ($is_cpoints == 1) ? "YES" : "NO";
		if($is_igpsm == 1) $_is_igpsm = "YES";
		elseif($is_igpsm == 0) $_is_igpsm = "NO";
		elseif($is_igpsm == 2) $_is_igpsm = "BOTH";
				
		$html = "<p><label>You are about to add a new Product Type with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Visible?</strong></label></td>
							<td><label class=''>{$_is_visible}</label></td>		
						</tr>
						<tr>
							<td style='width:100px'><label><strong>Is Regular Buyable?</strong></label></td>
							<td><label class=''>{$_is_regular_buyable}</label></td>
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is GC Buyable?</strong></label></td>
							<td><label class=''>{$_is_gc_buyable}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Package?</strong></label></td>
							<td><label class=''>{$_is_package}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is C Points?</strong></label></td>
							<td><label class=''>{$_is_cpoints}</label></td>		
						</tr>	
						<tr>
							<td style='width:100px;'><label><strong>Is IGPSM?</strong></label></td>
							<td><label class=''>{$_is_igpsm}</label></td>		
						</tr>
							</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function add_product_type() {
		$name = $this->input->post('name');
		$is_visible = $this->input->post('is_visible');
		$is_regular_buyable = $this->input->post('is_regular_buyable');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		$is_package = $this->input->post('is_package');
		$is_cpoints = $this->input->post('is_cpoints');
		$is_igpsm = $this->input->post('is_igpsm');
		
		$product_type = $this->items_model->get_product_types(array("name" => strtoupper($name)));
		if(!empty($product_type))
		{
			$this->return_json("error","This Product Type already exists.");
			return;
		}
		
		// insert the new item
		$data = array(
			'name' => strtoupper($name),
			'is_visible' => strtoupper($is_visible),
			'is_regular' => strtoupper($is_regular_buyable),
			'is_gc_buyable' => strtoupper($is_gc_buyable),
			'is_package' => strtoupper($is_package),
			'is_cpoints' => strtoupper($is_cpoints),
			'is_igpsm' => strtoupper($is_igpsm)
		);
		
		$this->items_model->insert_product_type($data);
	
		$table_id = $this->items_model->insert_id();
		
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_product_type_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCT TYPES',
			'table_name' => 'rf_product_types',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_product_type_log_data);
	
 		$this->return_json("ok","Product type added successfully");
		return;
	}
	
	public function edit($product_type_id = 0)
	{
		$_product_type_id = $this->input->post('_product_type_id');
		
		$_product_type = $this->items_model->get_product_type_by_id($_product_type_id);

		$data = array(
			'product_type' => $_product_type
		);

		$html = $this->load->view('/product_types/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}

	public function confirm_edit() {
		$name = $this->input->post('name');
		$is_visible = $this->input->post('is_visible');
		$is_regular_buyable = $this->input->post('is_regular_buyable');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		$is_package = $this->input->post('is_package');
		$is_cpoints = $this->input->post('is_cpoints');
		$is_igpsm = $this->input->post('is_igpsm');
		$product_type_id = $this->input->post('product_type_id');

		$_name = strtoupper($name);
		$_is_visible = ($is_visible == 1) ? "YES" : "NO";
		$_is_regular_buyable = ($is_regular_buyable == 1) ? "YES" : "NO";
		$_is_gc_buyable = ($is_gc_buyable == 1) ? "YES" : "NO";
		$_is_package = ($is_package == 1) ? "YES" : "NO";
		$_is_cpoints = ($is_cpoints == 1) ? "YES" : "NO";
		if($is_igpsm == 1) $_is_igpsm = "YES";
		elseif($is_igpsm == 0) $_is_igpsm = "NO";
		elseif($is_igpsm == 2) $_is_igpsm = "BOTH";
			
		$html = "<p><label>You are about to add a new Product Type with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Visible?</strong></label></td>
							<td><label class=''>{$_is_visible}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is Regular Buyable?</strong></label></td>
							<td><label class=''>{$_is_regular_buyable}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is GC Buyable?</strong></label></td>
							<td><label class=''>{$_is_gc_buyable}</label></td>		
						</tr>	
						<tr>
							<td style='width:100px;'><label><strong>Is Package?</strong></label></td>
							<td><label class=''>{$_is_package}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is C Points?</strong></label></td>
							<td><label class=''>{$_is_cpoints}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Is IGPSM?</strong></label></td>
							<td><label class=''>{$_is_igpsm}</label></td>		
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_product_type() {
		
		$name = $this->input->post('name');
		$is_visible = $this->input->post('is_visible');
		$is_regular_buyable = $this->input->post('is_regular_buyable');
		$is_gc_buyable = $this->input->post('is_gc_buyable');
		$is_package = $this->input->post('is_package');
		$is_cpoints = $this->input->post('is_cpoints');
		$is_igpsm = $this->input->post('is_igpsm');
		$product_type_id = $this->input->post('product_type_id');
		
		if(empty($product_type_id))
		{
			$this->return_json("error","Invalid Product Type ID.");
			return;
		}
		
		$product_type = $this->items_model->get_product_type_by_id($product_type_id);
	
		if(empty($product_type) || is_null($product_type))
		{
			$this->return_json("error","This Product Type does not exist.");
			return;
		}
		
		$name = strtoupper($name);
	
		// edit the product_type
		$data = array(
				'name' => $name,
				'is_visible' => $is_visible,
				'is_regular' => $is_regular_buyable,
				'is_gc_buyable' => $is_gc_buyable,
				'is_package' => $is_package,
				'is_cpoints' => $is_cpoints,
				'is_igpsm' => $is_igpsm
		);
		
		$previous_product_type_data = $this->items_model->get_product_type_by_id($product_type_id);
		$details_before = array('id' => $product_type_id, 'details' => $previous_product_type_data);
		$details_before = json_encode($details_before);

		$this->items_model->update_product_type($data, array('product_type_id' => $product_type_id));

		$details_after = array('id' => $product_type_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_product_type_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCT TYPES',
			'table_name' => 'rf_product_types',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_product_type_log_data);
		
		$this->return_json("ok","Product type edited.");
		return;	
	}

	public function delete($product_type_id = 0)
	{
		$product_type_id = $this->input->post('product_type_id');	
		
		$product_type = $this->items_model->get_product_type_by_id($product_type_id);

		$data = array(
			'product_type' => $product_type
		);

		$html = $this->load->view('/product_types/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$product_type_id = $this->input->post('product_type_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_product_type() {	
		$product_type_id = $this->input->post('product_type_id');
		
		if(empty($product_type_id))
		{
			$this->return_json("error","Invalid Product Type ID.");
			return;
		}
		
		$product_type = $this->items_model->get_product_type_by_id($product_type_id);

		if (!empty($product_type)) {			
			$details_before = array('id' => $product_type_id, 'details' => $product_type);
			$details_before = json_encode($details_before);

			$this->items_model->delete_product_type(array('product_type_id' => $product_type_id));

			$delete_product_type_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'Product TYPES',
				'table_name' => 'rf_product_types',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'details_after' => "",
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $delete_product_type_log_data);
		
		} else {
			$this->return_json("error","This Product Type does not exist.");
			return;
		}
	
		$this->return_json("ok","Product type deleted.");
		return;
		
	}
	
	public function pdf_view(){
		$this->load->library('mpdf');
		$product_types = $this->items_model->get_product_types();

		if (!empty($product_types))
		{
			$current_year = date('Y');
			$current_month = date('M');
			$current_day = date('d');
			$date = $current_month . ' ' . $current_day . ', ' . $current_year;
			$current_month_num = date('m');
			$date_as_num = $current_month_num . $current_day . $current_year;
			$timestamp = date('Y-m-d H:i:s');
			$data = array( 
				'product_types' => $product_types,
				'date' => $date,
				'timestamp' => $timestamp
			);
			$this->mpdf->WriteHTML($this->load->view('product_types/pdf_view',$data,TRUE));
			$this->mpdf->Output('list_of_product_types_' . $date_as_num, 'I'); 
		}	
	}

	public function excel_view(){
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
		$start_column_num = 1;
 
        $objPHPExcel->setActiveSheetIndex(0);
		
		$product_types = $this->items_model->get_product_types();

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
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'Product Type');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'Visible?');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'GC Buyable?');
		
		$row = ++$start_column_num;
		foreach ($product_types as $i)
		{
			$is_visible =  ($i->is_visible == 1) ? "Yes" : "No";
			$is_gc_buyable =  ($i->is_gc_buyable == 1) ? "Yes" : "No";
			$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, $i->name);
			$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $is_visible);
			$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $is_gc_buyable);
			
			//centers each entry
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$row++;
		}
			
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		$date = $current_month . $current_day . $current_year;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_of_product_types_'.$date.'.xls"');
		header('Cache-Control: max-age=0');		

		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

	}

}