<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Walk_in extends Admin_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('spare_parts_model');
		$this->load->library('pager');			
		$this->load->helper("breadcrumb_helper");	

		$this->db_dpr = $this->load->database('spare_parts', TRUE);

	}

	public function index()
	{
		$this->template->view('/walk_in/dashboard');
	}

	public function add_new_sales($order_code = "")
	{
		$current_year = substr(date("Y"),2,2);
		if (!($order_code == "")){
			$where = "order_code = '{$order_code}'";
			$counter_summary_last = $this->spare_parts_model->get_counter_summary($where,null,'counter_order_summary_id DESC');
			$where = "counter_order_summary_id = '{$counter_summary_last[0]->counter_order_summary_id}'";
			$counter_detail = $this->spare_parts_model->get_counter_detail($where);
		}else{
			$where = "order_code_year = '{$current_year}'";
			$counter_summary_last = $this->spare_parts_model->get_counter_summary($where);
			var_dump(count($counter_summary_last));
			if (count($counter_summary_last) >= "1"){
				$order_code = "WI" . $current_year . "-" . str_pad($counter_summary_last[0]->order_code_series + 1,5,"0",STR_PAD_LEFT);	
				$where = "order_code = '{$order_code}'";
				$counter_summary_last = $this->spare_parts_model->get_counter_summary($where,null,'counter_order_summary_id DESC');
				$where = "counter_order_summary_id = '{$counter_summary_last[0]->counter_order_summary_id}'";
				$counter_detail = $this->spare_parts_model->get_counter_detail($where);	
			}else{
				$order_code = "WI" . $current_year . "-" . "00001";
				$counter_detail = "";	
			}
			
		}

		$this->template->order_code = $order_code;
		$this->template->counter_detail = $counter_detail;
		$this->template->view('spare_parts/walk_in/sales/sales');	
	}

	public function add_new_item()
	{
		$order_code = $this->input->post('order_code');
		$data = "";
		$data = array('order_code' => $order_code);
		$html = $this->load->view("spare_parts/walk_in/sales/add_item",$data,TRUE);

		$this->return_json("1","Search item/Parts", array('html' => $html));
	}

	public function search_item()
	{
		$search_by = $this->input->post('search_by');
		
		$sql = "SELECT a.item_id,
		a.sku,
		b.description,
		b.model_name,
		b.part_number,
		b.unit,
		a.good_quantity,
		b.srp,
		case when(sum(c.good_quantity)) is null then 0 else sum(c.good_quantity) end as xres,
		a.rack_location 
		from is_item a 
		LEFT JOIN rf_spare_part b on a.sku = b.sku
    	LEFT JOIN is_item_reserved_view c on a.item_id = c.item_id 
    	where (a.sku like '%{$search_by}%' or b.description like '%{$search_by}%' or b.model_name like '%{$search_by}%' or b.part_number like 
    	'%{$search_by}%' or b.unit like '%{$search_by}%') and a.good_quantity >=  1 GROUP BY a.item_id";

		$inventory_list = $this->db_dpr->query($sql);
		$inventory_list = $inventory_list->result(); 

		$html = "";
		//var_dump($inventory_list);
       	foreach ($inventory_list as $il) {

       		$item_id = $il->item_id;
       		$sku = $il->sku;
       		$description = $il->description;
       		$model = $il->model_name;
       		$part_number = $il->part_number;
       		$unit = $il->unit;
       		$quantity = $il->good_quantity;
       		$srp = $il->srp;
       		$res = $il->xres;
       		$location = $il->rack_location;
       		
       		$html .= "<tr>			
					<td style = 'text-align:center;'>{$sku}</td>
					<td>{$description}</td>
					<td>{$model}</td>
					<td>{$part_number}</td>
					<td>{$unit}</td>		
					<td style = 'text-align:right;'>{$quantity}</td>
					<td style = 'text-align:right;'>{$srp}</td>
					<td style = 'text-align:right;'>{$res}</td>		
					<td>{$location}</td>
					<td><a class = 'btn add_item' data2 = '{$srp}' data = '{$item_id}'>Add</a></td>
				  	</tr>";

       	};

		$this->return_json("1","Search item/Parts",array('html' => $html));
	}

	public function add_new_parts()
	{
		$order_code = $this->input->post('order_code');
		$item_id = $this->input->post('item_id');
		$discount = $this->input->post('discount');
		$quantity = $this->input->post('quantity');
		$srp = $this->input->post('srp');

		//var_dump($order_code);
		$where = "order_code = '{$order_code}'";
		$counter_summary_last = $this->spare_parts_model->get_counter_summary($where);

		$current_year = substr(date("Y"),2,2);
		$order_series = substr($order_code, 5);

		//var_dump($order_series);
		if (count($counter_summary_last) == "1"){
			$last_insert_id = $counter_summary_last[0]->counter_order_summary_id;
		}else{
			$data_summary=array(
			'order_code_year' => $current_year,
			'order_code_series' => abs($order_series),
			'order_code' => $order_code
			);
			$this->spare_parts_model->insert_counter_summary($data_summary);
			$last_insert_id = $this->spare_parts_model->insert_id();
		}

		$discount_amt = ($srp * $discount) / 100;
		$total_amt = (($srp- $discount_amt) * $quantity);
		$data_detail=array(
			'counter_order_summary_id' => $last_insert_id,
			'item_id' => $item_id,
			'srp' => $srp,
			'discount' => $discount,
			'discount_amount' => $discount_amt,
			'good_quantity' => $quantity,
			'total_amount' => $total_amt
			);

		$this->spare_parts_model->insert_counter_detail($data_detail);

		$this->return_json("1","Successfully add item/Parts");

	}

}