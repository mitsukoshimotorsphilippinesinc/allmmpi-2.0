<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'settings' => 'rf_settings'
		);

	}
	
	
	function get_all_settings($cache = TRUE){
        // set settings 
        $settings = $this->get_settings();
        
        $setting_array = array();
        foreach($settings as $s) {
            $setting_array[$s->slug] = $s->value;
        }
        
        return $setting_array;
    }

	function get_settings($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('settings', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_setting($data) 
	{
		return $this->insert('settings', $data);
	}

	function update_settings($data, $where) 
	{
		return $this->update('settings', $data, $where);
	}

	function delete_settings($where) 
	{
		return $this->delete('settings', $where);
	}
	
	function get_setting_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('settings', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	function get_setting_by_slug($slug) 
	{
		$result = $this->get_settings(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}


}