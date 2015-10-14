<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setting_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'setting' => 'rf_setting'
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


}