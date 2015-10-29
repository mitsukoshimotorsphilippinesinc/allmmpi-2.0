<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcement extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');

		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{							
		$this->template->current_page = 'announcement';
		$this->template->view('announcement/dashboard');
	}

	
	public function get_announcement_list()
	{
		$page = $this->input->post('page');
		$search_data = trim($this->input->post('search_data'));

		if(empty($page)) $page = 1;

		$where = "is_published = 1";

		$add_where = "";
		if (strlen($search_data) > 0) {
			$add_where = " AND (title like '%{$search_data}%' OR insert_timestamp LIKE '%{$search_data}%')";
			$where .= $add_where;
		}

		$announcements_count = $this->asset_model->get_announcement_count($where);

		$records_per_page = 3;
		$offset = ($page - 1) * $records_per_page;
        $offset = ($offset < 0 ? 0 : $offset);

		$this->pager2->set_config(array(
            'total_items' => $announcements_count,
            'per_page' => $records_per_page,
            'offset' => $offset,
            'adjacents' => 1,
            'type' => 'ajax'
        ));

        $pagination = $this->pager2->create_links();
       
		$limit = array("rows"=>$records_per_page,"offset"=>$offset);
		$announcements = $this->asset_model->get_announcement($where, $limit,'insert_timestamp DESC');

		$html = "";

		foreach($announcements as $a) {
					
			$proper_date = date("jS F Y - h:i:s a", strtotime($a->insert_timestamp));
			
			$html .= "<h2 style='float:left;'>{$a->title}</h2><div style='clear:both;'></div><span style='float:left;margin-top:-15px;'><i>{$proper_date}</i></span><div style='clear:both;'></div><br/>{$a->body}";

			$data = array(
					"announcement_id" => $a->announcement_id,					
				);

			$html .= $this->load->view("announcement/display_comments", $data, TRUE);

			$html .= "<textarea class='span12 new-comment-{$a->announcement_id}'></textarea>
					<button class='button-post btn btn-primary pull-right' style='margin-right: 20px;margin-bottom:10px;' data='{$a->announcement_id}' title='Post'>Post</button>
					<div class='announcement-comments-{$a->announcement_id}'></div>
					<div style='width: 100%; height: 2px; background: #F87431; overflow: hidden;''></div>";		
		}

		$this->return_json(1, 'Success', array('html' => $html, 'pagination' => $pagination, 'result_count' => $announcements_count . " RESULT/S"));
		return;
	}

	public function display_comments()
	{
		$announcement_id = $this->input->post("_announcement_id");
		$comment = $this->input->post("_comment");

		// insert to am_announcement_message
		$data = array(
				"announcement_id" => $announcement_id,
				"from_id_number" => $this->employee->id_number,
				"message" => trim($comment)
			);

		$this->asset_model->insert_announcement_message($data);

		$this->return_json("1", "Ok.");
	}
	
}
