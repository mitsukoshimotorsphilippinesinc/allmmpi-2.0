<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class S4s extends Admin_Controller
{
	private $_validation_rule = array(
		array(
			'field' => 'pp_name',
			'label' => 'Policy Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'pp_description',
			'label' => 'Description',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'department_id',
			'label' => 'Department Name',
			'rules' => 'abs|required'
		),
		array(
			'field' => 'document_sequence',
			'label' => 'Document Sequence',
			'rules' => 'abs'
		),
		array(
			'field' => 'reference_number',
			'label' => 'Reference Code',
			'rules' => 'trim|required'
		)
	);
	
	private $_months = array(
		'0' => 'Month',
		'1' => 'January',
		'2' => 'February',
		'3' => 'March',
		'4' => 'April',
		'5' => 'May',
		'6' => 'June',
		'7' => 'July',
		'8' => 'August',
		'9' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);
	
	function __construct()
	{
  		parent::__construct();

		$this->load->model('human_relations_model');
		// load pager library
		$this->load->library('pager');
		$this->load->helper('breadcrumb_helper');				
	}

	public function index()
	{
		$this->page();
	}
	
	public function page()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		$department_id = abs($this->input->get("department_id"));

		$search_url = "";	

		if (($search_text == "") || empty($search_text)) {
			$where = null;

			if ($department_id > 0) {
				$where .= "department_id = {$department_id}";
			} 

		} else {			
			$where = "`{$search_by}` LIKE LOWER('%{$search_text}%')";

			if (department_id > 0) {
				$where .= " AND department_id = {$department_id}";
			} 
		}



		$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text . "&department_id=" . $department_id;
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/s4s/page",
		    'total_items' => $this->human_relations_model->get_s4s_count($where),
		    'per_page' => 30,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->department_id = $department_id;

		$this->template->s4s_list = $this->human_relations_model->get_s4s($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'document_sequence, reference_number');
		$this->template->view('s4s/list');


	}
	
	public function add()
	{
		
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				
				$is_active = set_value('is_active');
			
				$data = array(
					'reference_number' => set_value('reference_number'),
					'pp_name' => set_value('pp_name'),
					'pp_description' => set_value('pp_description'),
					'department_id' => set_value('department_id'),
					'document_sequence' => set_value('document_sequence'),
					'is_active' => $is_active
				);
				$this->human_relations_model->insert_s4s($data);
				
				$insert_id = $this->human_relations_model->insert_id();
				
				/*//logging of action
				*/

				redirect('/operations/s4s');
				return;
			}
		}
		
		$this->template->view('s4s/add');
	}
	
	public function delete($s4s_id = 0)
	{
		$s4s_details = $this->human_relations_model->get_s4s_by_id($s4s_id);

		if ($_POST and !empty($s4s_details))
		{
			$_s4s_id = $this->input->post('s4s_id');
			if (!empty($_s4s_id)) if ($_s4s_id == $s4s_id)
			{
		
				$this->human_relations_model->delete_s4s(array('s4s_id' => $s4s_id));
				
				/*/logging of action
				*/
				
				redirect('/operations/s4s');
				return;
			}
		}

		$this->template->s4s_details = $s4s_details;
		$this->template->view('operations/s4s/delete');

	}

	public function edit($s4s_id = 0)
	{
		$s4s_details = $this->human_relations_model->get_s4s_by_id($s4s_id);


		if ($_POST and !empty($s4s_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{				
				// insert the new results
				$data = array(					
					'pp_name' => set_value('pp_name'),
					'pp_description' => set_value('pp_description'),
					'department_id' => set_value('department_id'),
					'document_sequence' => set_value('document_sequence'),
					'is_active' => $is_active
				);

				$this->spare_parts_model->update_s4s($data, array('s4s_id' => $s4s_id));
				
				//logging of action
				
				redirect('/operations/s4s');
				return;
			}
		}

		$this->template->s4s_details = $s4s_details;
		$this->template->view('operations/s4s/edit');
	}

	//public function privileges()
	//{		
	//	$this->template->view('operations/s4s/privilege');
    //
	//}	

	public function position_view()
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		$where = "parent_position_id = 0";

		if (($search_text == "") || empty($search_text)) {
			$where .= null;
		} else {			
			$where .= " AND `{$search_by}` LIKE LOWER('%{$search_text}%')";
		}
		
		$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/s4s/position_view/page",
		    'total_items' => $this->human_relations_model->get_position_count($where),
		    'per_page' => 2,
		    'uri_segment' => 5
		);

		$this->pager->set_config($config);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->position_list = $this->human_relations_model->get_position($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		$this->template->view('s4s/position_list');

	}

	public function comments()
	{
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/operations/s4s/comments",
		    'total_items' => $this->human_relations_model->get_s4s_count(),
		    'per_page' => 30,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);
		$this->template->s4s = $this->human_relations_model->get_s4s(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");
		$this->template->view('s4s/comments');
	}

	public function view_with_comments($s4s_id = 0)
	{
		$s4s = $this->human_relations_model->get_s4s_by_id($s4s_id);

		$config = array(
		    'pagination_url' => "/operations/s4s/view_with_comments",
		    'total_items' => $this->human_relations_model->get_s4s_count(),
		    'per_page' => 10,
		    'uri_segment' => 4
		);
		
		$this->pager->set_config($config);
		$this->template->comments = $this->human_relations_model->get_s4s_message("s4s_id = " . $s4s_id, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp");
		$this->template->s4s = $s4s;		
		$this->template->view('s4s/view_with_comments');		
	}

	public function reply_comment()
	{
		$s4s_message_id = $this->input->post("s4s_message_id");

		$html = "<p>Please enter your Admin comment here:<br/><br/>
					<label><strong>ADMIN:</strong></label>
					<textarea style='width:400px;' id='admin_comment_box'></textarea>
					<br/>
					<label id='admin_comment_box-error' style='color:red;display:none;'>Error! Comment message should not be blank.</label>
				</p>";

		$this->return_json("1", "Reply Comment", array("html" => $html));

	}

	public function reply_comment_proceed()
	{
		$s4s_message_id = abs($this->input->post("s4s_message_id"));
		$admin_message = trim($this->input->post("admin_message"));
		$s4s_id = abs($this->input->post("s4s_id"));

		$s4s_message_details = $this->human_relations_model->get_s4s_message_by_id($s4s_message_id);

		$data = array(
				"from_id_number" => 'n/a',
				"to_id_number" =>  $s4s_message_details->from_id_number,
				"message" => $admin_message,
				"s4s_id" => $s4s_id
			);

		$this->human_relations_model->insert_s4s_message($data);

		$html = "You have successfully posted an Admin comment.";

		$this->return_json("1", "Proceed Admin Comment", array("html" => $html));
	}
	
	public function delete_comment()
	{
		$s4s_message_id = $this->input->post("s4s_message_id");

		$setting_details = $this->setting_model->get_setting_by_slug("s4s_comment_message_removal");
		$setting_details->value;

		// update
		$data = array(
				"is_removed" => 1
			);
		$where = "s4s_message_id = " . $s4s_message_id;
		$this->human_relations_model->update_s4s_message($data, $where);

		$html = "You have successfully removed the comment message.";

		$this->return_json("1", "Delete Comment", array("html" => $html));

	}

	public function update_asset()
	{
		$filename = $this->input->post('filename');
		$s4s_id = $this->input->post('s4s_id');
		$department_id = $this->input->post('department_id');

		$data = array("image_filename"=>$filename);

		var_dump("update or insert");

		//$this->spare_parts_model->update_dealer($data, "s4s_id = {$id}");
		
		////logging of action
		//$details_before = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		//$details_after = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		
		//log_to_db("spare_parts", $this->user->id_number, "MAINTENANCE-DEALERS", "rf_dealer", "UPDATE", $details_before, $details_after);
		
		$this->return_json('ok','');
	}


	public function privilege($s4s_id = 0)
	{
		if ($_POST)
		{
			$_s4s_id = $this->input->post('s4s_id');
			$_positions = $this->input->post('positions');
			
			// remove all s4s _positions
			$this->human_relations_model->delete_s4s_position("s4s_id = {$_s4s_id}");

			//var_dump($_positions);

			// add new _positions
			if ($_positions === false) {				
				echo json_encode(array("status"=>1));				
			} else {	
				foreach($_positions as $p)
				{
					$data = array("s4s_id"=>$_s4s_id, "position_id"=>$p, "is_active"=>1);
					$this->human_relations_model->insert_s4s_position($data);
				}
				
				echo json_encode(array("status"=>1));				
			}
			return;
		}
				
		if (($s4s_id == 0) || ($s4s_id == NULL)) {
			$s4s = $this->human_relations_model->get_s4s_by_id($_s4s_id);				
		} else {		
			$s4s = $this->human_relations_model->get_s4s_by_id($s4s_id);
		}
			
		$s4s_positions = $this->human_relations_model->get_s4s_position_view("s4s_id = {$s4s_id}");		
		
		if (!empty($s4s_positions))
		{
			$up_array = array();
			foreach ($s4s_positions as $up) $up_array[] = $up->position_id;			
			$where = "parent_position_id = 0 AND position_id NOT IN (".implode(",",$up_array).")";
		}
		else
			$where = "parent_position_id = 0";
		
		$positions = $this->human_relations_model->get_position($where);

		$this->template->s4s = $s4s;
		$this->template->s4s_positions = $s4s_positions;
		$this->template->positions = $positions;
		$this->template->view('s4s/privilege2');
		
	}	
	
	public function view($course_id = 0) {

		// get course_id data and assets
		$course_details = $this->human_relations_model->get_s4s_by_id($course_id);

		$where = "s4s_id = " . $course_id;
		$asset_details = $this->human_relations_model->get_s4s_asset($where);

		$this->template->course_details = $course_details;
		$this->template->asset_details = $asset_details;
		$this->template->current_page = 's4s';
		$this->template->view('s4s/display_s4s');
	}	
}