<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_series extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('cards_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('card_series');
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
		$searchBy = "number series";
			
		if (($search_text == "") || empty($search_text)) {
			$where = NULL;					
		} else {	
											
			if (($search_by == "code") || ($search_by == "name")) {
				$searchBy = $search_by;
				$where = "LOWER(code) LIKE LOWER('%{$search_text}%') OR LOWER(name) LIKE LOWER('%{$search_text}%')";
				$card_type_details = $this->cards_model->get_card_types($where);

				if (count($card_type_details) > 0) {	
					$card_type_details = $card_type_details[0];
					$type_id = $card_type_details->card_type_id;			
					$search_by = 'card_type_id';				
				}  				
			} 
													
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;			
		}
	
				
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/admin/card_series/index/",
		    'total_items' => $this->cards_model->get_card_series_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$card_series = $this->cards_model->get_card_series($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'insert_timestamp DESC');
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->searchBy = $searchBy;
	
		$this->template->card_series = $card_series;
		$this->template->view('card_series/list');
	}

	public function add() {
	
		$card_types = $this->cards_model->get_card_types(null,null,"code ASC");
	
		$data = array(
			'card_types' => $card_types
		);

		$html = $this->load->view('/card_series/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_series_number = trim($this->input->post('_series_number'));
		$_card_type_id = abs($this->input->post('_card_type_id'));
		$_description = trim($this->input->post('_description'));
		$_sales_package_option = abs($this->input->post('_sales_package_option'));
	
		if (strlen($_series_number) == 1) {
			$_series_number = "0" . $_series_number;
		}
		
		$_description = strtoupper($_description);
		
		$ctype_details = "";
		$card_type_details = $this->cards_model->get_card_type_by_id($_card_type_id);
		if (count($card_type_details) == 0) {

			echo json_encode(array("status"=>"0","html"=>"<p>Card Type not found.</p>"));
			return;
		} else {
			$ctype_details = $card_type_details->code . " - " . $card_type_details->name;
		}
		
		// check if series_number is already taken
		$card_series_details = $this->cards_model->get_card_series_by_series_number($_series_number);
		
		if (count($card_series_details) > 0)  {
			// already taken
			
			$card_type_details = $this->cards_model->get_card_type_by_id($card_series_details->card_type_id);
			$cseries_details = $card_type_details->code . " - " . $card_type_details->name;
			
			$html = "<p>
						<label>Sorry, the Card Series is already taken. See the details below:</label>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Series Number</strong></label></td>
									<td><label style='color:#990000;'>{$card_series_details->series_number}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Name - Code</strong></label></td>
									<td><label style='color:#990000;'>{$cseries_details}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label style='color:#990000;'><strong>Description</strong></label></td>
									<td><label style='color:#990000;'>{$card_series_details->description}</label></td>		
								</tr>											
							</tbody>
						</table>
					</p>";
				
			echo json_encode(array("status"=>"0","html"=>$html));
			
		} else {
			// available series
			$html = "<p><label>You are about to add a new Card Series with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:120px;'><label><strong>Series Number</strong></label></td>
								<td><label class=''>{$_series_number}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Name - Code</strong></label></td>
								<td><label class=''>{$ctype_details}</label></td>		
							</tr>	
							<tr>
								<td style='width:120px;'><label><strong>Description</strong></label></td>
								<td><label class=''>{$_description}</label></td>		
							</tr>				
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		}
		
		return;
		
	}
	
	public function add_card_series() {
		$_series_number = trim($this->input->post('_series_number'));
		$_card_type_id = abs($this->input->post('_card_type_id'));
		$_description = trim($this->input->post('_description'));

		if (strlen($_series_number) == 1) {
			$_series_number = "0" . $_series_number;
		}
		
		// insert the new product
		$data = array(
			'series_number' => $_series_number,
			'card_type_id' => $_card_type_id,
			'description' => strtoupper($_description)
		);
		$this->cards_model->insert_card_series($data);
		
		$insert_id = $this->cards_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $insert_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_card_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARDS SERIES',
			'table_name' => 'rf_card_series',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);
									
		$this->tracking_model->insert_logs('admin', $add_card_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}
	
	public function edit()
	
	{
		$_card_series_id = $this->input->post('_card_series_id');
		
		$card_series = $this->cards_model->get_card_series_by_id($_card_series_id);

		$card_types = $this->cards_model->get_card_types(null,null,"code ASC");

		$data = array(		
			'card_types' => $card_types,
			'card_series' => $card_series
		);

		$html = $this->load->view('/card_series/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function confirm_edit() {
		$_series_number = trim($this->input->post('_series_number'));
		$_card_type_id = abs($this->input->post('_card_type_id'));
		$_description = trim($this->input->post('_description'));
		$_card_series_id = abs($this->input->post('_card_series_id'));
						
		$card_series_details = $this->cards_model->get_card_series_by_id($_card_series_id);		
		
		$card_type_details = $this->cards_model->get_card_type_by_id($card_series_details->card_type_id);
		
		$ctype_details = $card_type_details->code . " - " . $card_type_details->name;	
	
		$html = "<p><label>You are about to edit a Card Series with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Series Number</strong></label></td>
							<td><label class=''>{$_series_number}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Code - Name</strong></label></td>
							<td><label class=''>{$ctype_details}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Description</strong></label></td>
							<td><label class=''>{$_description}</label></td>		
						</tr>						
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}


	public function update_card_series() {
		$_series_number = trim($this->input->post('_series_number'));
		$_card_type_id = abs($this->input->post('_card_type_id'));
		$_description = trim($this->input->post('_description'));
		$_card_series_id = abs($this->input->post('_card_series_id'));
	
		$_details = $this->cards_model->get_card_series_by_id($_card_series_id);
	
		// insert the new product
		$data = array(
			'series_number' => $_series_number,
			'card_type_id' => $_card_type_id,
			'description' => $_description
		);
		$this->cards_model->update_card_series($data, array('card_series_id' => $_card_series_id));
		
		$details_before = array('id' => $_card_series_id, 'details' => array('series_number' => $_details->series_number,'card_type_id' => $_details->card_type_id,'description' => $_details->description));
		$details_before = json_encode($details_before);
			
		//logging of action
		$details_after = array('id' => $_card_series_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_card_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARDS SERIES',
			'table_name' => 'rf_card_series',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);
									
		$this->tracking_model->insert_logs('admin', $update_card_log_data);
	
		echo json_encode(array("status"=>"1"));
		return;		
	}
	

	public function delete()
	{
		$_card_series_id = abs($this->input->post('_card_series_id'));			
		
		$card_series = $this->cards_model->get_cards_series_by_id($_card_series_id);

		$card_type_details = $this->cards_model->get_card_type_by_id($card_series->card_type_id);

		$voucher_type_text = $card_type_details->code . " - " . $card_type_details->name;

		$data = array(
			'card_series' => $card_series,
			'voucher_type_text' => $voucher_type_text
		);		
		$html = $this->load->view('/card_series/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function confirm_delete() {
		$_card_series_id = abs($this->input->post('_card_series_id'));		
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_card_series() {	
		$_card_series_id = abs($this->input->post('_card_series_id'));	
			
		if (!empty($_product_id)) {
			$_details = $this->cards_model->get_card_series_by_id($_card_series_id);
			
			$this->cards_model->delete_card_series(array('card_series_id' => $_card_series_id));

			$details_before = array('id' => $_card_series_id, 'details' => array('series_number' => $_details->series_number,'card_type_id' => $_details->card_type_id,'description' => $_details->description));
			$details_before = json_encode($details_before);

			$delete_card_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'CARDS SERIES',
				'table_name' => 'rf_card_series',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_card_log_data);
			
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid Card Series ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1"));
		return;
		
	}

	public function create() {	
		$_card_series_id = $this->input->post('_card_series_id');
		
		
		// get series_number for the card_type
		
		// get current series of card type
		$where = "card_series_id = '{$_card_series_id}'";
		$limit = array('rows' => 1, 'offset' => 0);
		$card_series_details = $this->cards_model->get_card_series($where,$limit,"series_number ASC");
		
		$first_card_series_id = 0;
		
		if (empty($card_series_details)) {
			// no series for this card type
			
			// show the available series 
			// ------------------------------
			// TO_DO:
			// ------------------------------
			
		} else {
			$first_card_series_id = $card_series_details[0]->card_series_id;
		}

		$data = "";
		
		$last_card_count = $this->get_last_card_number($first_card_series_id);
	
		$data = array(		
			'last_card_count' => $last_card_count,
			'card_series_details' => $card_series_details			
		);
			
		$html = $this->load->view('/card_series/create_card', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html, "last_card_count" => $last_card_count));
		return;		
	}

	public function get_last() {
		$_card_series_id = abs($this->input->post('_card_series_id'));
		
		$last_card_count = $this->get_last_card_number($_card_series_id);
		
		echo json_encode(array("status"=>"1","last_number"=>$last_card_count));
		return;
	}

	private function get_last_card_number($card_series_id=0) {
		$data = "";
		
		// get last card number from series	
		$card_series_data = $this->cards_model->get_card_series_by_id($card_series_id);
		
		$card_type_details = $this->cards_model->get_card_type_by_id($card_series_data->card_type_id);
				
		// get the last record from master table with the same series
		$where = "card_id LIKE '{$card_series_data->series_number}%'";
				
		if ($card_type_details->is_package == 0) {	
			$limit = array('rows' => 1, 'offset' => 0);
			$master_table = $this->cards_model->get_rs_card($where, $limit, "card_id DESC");
		} else {
			$limit = array('rows' => 1, 'offset' => 0);
			$master_table = $this->cards_model->get_sp_card($where, $limit, "card_id DESC");
		}
		
		if(count($master_table) == 0) {
			// no record yet
			$last_card_count = "(No cards generated for this series yet)";
		} else {
			$last_card_count = $master_table[0]->card_id;
		}
		
		return $last_card_count;
	}

	public function confirm_create() {	
		$_quantity = abs($this->input->post('_quantity'));
		$_card_series_id = abs($this->input->post('_card_series_id'));
		$_last_card_number = abs($this->input->post('_last_card_number'));
		$_released_to = trim($this->input->post('_released_to'));
		//$_card_type_id = abs($this->input->post('_card_type_id'));
		$_rn_prefix = strtoupper(trim($this->input->post('_rn_prefix')));
	
		$_released_to = strtoupper($_released_to);
	
		$card_series_details = $this->cards_model->get_card_series_by_id($_card_series_id);
		
		if (count($card_series_details) == 0) {		
			echo json_encode(array("status"=>"0","html"=>"<p>Error: Card Series not Found.</p>"));
			return;
		} else {			
			$card_type_details = $this->cards_model->get_card_type_by_id($card_series_details->card_type_id);
			//$card_type_details = $this->cards_model->get_card_type_by_id($_card_type_id);
			
			if (count($card_type_details) == 0) {			
				echo json_encode(array("status"=>"0","html"=>"<p>Error: Card Type for Card Series: <strong>" . $card_series_details->series_number . "</strong> not Found.</p>"));
				return;
			}
			
			$type_details = $card_type_details->code . " - " .  $card_type_details->name;
		
			$html = "<p><label>You are about to create new Card Series with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:120px;'><label><strong>Series Number</strong></label></td>
								<td><label class=''>{$card_series_details->series_number}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Code - Name</strong></label></td>
								<td><label class=''>{$type_details}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Quantity</strong></label></td>
								<td><label class=''>{$_quantity}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>RN Prefix</strong></label></td>
								<td><label class=''>{$_rn_prefix}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Released To</strong></label></td>
								<td><label class=''>{$_released_to}</label></td>		
							</tr>
							
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
	
			echo json_encode(array("status"=>"1","html"=>$html));
			return;
		}		
	}
	
	public function create_card_series() {
		
		$_quantity = abs($this->input->post('_quantity'));
		$_card_series_id = abs($this->input->post('_card_series_id'));
		$_rn_prefix = trim($this->input->post('_rn_prefix'));
		$_released_to = trim($this->input->post('_released_to'));
		
		$card_series_details = $this->cards_model->get_card_series_by_id($_card_series_id);
		$card_type_details = $this->cards_model->get_card_type_by_id($card_series_details->card_type_id);	
		$type_details = $card_type_details->code . " - " .  $card_type_details->name;

		$this->load->model('jobs_model');
		$params = array(
				'quantity' => $_quantity,
				'card_series_id' => $_card_series_id,
				'released_to' => $_released_to,
				'rn_prefix' => $_rn_prefix
			);
		$job_data = array(
				'job_type_id' => 2, // card_series
				'parameters' => json_encode($params)
			);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();
		
		//job_exec($job_id);
		job_exec($job_id);

		$html = "<p><label>New Card(s) is/are now processing with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Series Number</strong></label></td>
							<td><label class=''>{$card_series_details->series_number}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Code - Name</strong></label></td>
							<td><label class=''>{$type_details}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Quantity</strong></label></td>
							<td><label class=''>{$_quantity}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>RN Prefix</strong></label></td>
							<td><label class=''>{$_rn_prefix}</label></td>		
						</tr>
						<!-- Send Via Email generated_ids_description
						<tr>
							<td style='width:120px;'><label><strong>Card Code/s</strong></label></td>
							<td><label class=''></label></td>		
						</tr> -->
					</tbody>
				</table>				
				
			</p>";
		
	  	echo json_encode(array("status"=>"1", "html"=>$html));
		return;
	
	}

}