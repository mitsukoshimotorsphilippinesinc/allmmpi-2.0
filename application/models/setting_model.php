<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setting_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'setting' => 'rf_setting',
			'notification_content' => 'rf_notification_content',
			'page_content'=>'rf_page_content',
		);

	}
	
	
	function get_all_setting($cache = TRUE){
        // set setting 
        $setting = $this->get_setting();
        
        $setting_array = array();
        foreach($setting as $s) {
            $setting_array[$s->slug] = $s->value;
        }
        
        return $setting_array;
    }

	function get_setting($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('setting', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_setting($data) 
	{
		return $this->insert('setting', $data);
	}

	function update_setting($data, $where) 
	{
		return $this->update('setting', $data, $where);
	}

	function delete_setting($where) 
	{
		return $this->delete('setting', $where);
	}
	
	function get_setting_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('setting', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	function get_setting_by_slug($slug) 
	{
		$result = $this->get_setting(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	

	//-------------------------------------------------------
	// rf_notification_content
	
	function get_notification_content($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('notification_content', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_notification_content($data) 
	{
		return $this->insert('notification_content', $data);
	}

	function update_notification_content($data, $where) 
	{
		return $this->update('notification_content', $data, $where);
	}

	function delete_notification_content($where) 
	{
		return $this->delete('notification_content', $where);
	}
	
	function get_notification_content_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('notification_content', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_notification_content_by_slug($slug) 
	{
		$result = $this->get_notification_content(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_content_template($slug, $data = array())
	{
		$_content = $this->get_notification_content_by_slug($slug);

		if (!empty($_content))
		{
			// parse the title & body
			foreach ($data as $key=>$value)
			{
				$_content->title = str_replace("{@=".$key."}", $value, $_content->title);
				$_content->body = str_replace("{@=".$key."}", $value, $_content->body);
			}

		}

		return $_content;
	}

	// ------------------------------------------------
	// rf_page_content
	function get_page_content($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('page_content', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_page_content($data)
	{
		return $this->insert('page_content', $data);
	}

	function update_page_content($data, $where)
	{
		return $this->update('page_content', $data, $where);
	}

	function delete_page_content($where)
	{
		return $this->delete('page_content', $where);
	}

	function get_page_content_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('page_content', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_page_content_by_id($content_id)
	{
		$result = $this->get_contents(array('page_content_id' => $page_content_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_page_content_by_slug($slug)
	{
		$result = $this->get_page_content(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

}