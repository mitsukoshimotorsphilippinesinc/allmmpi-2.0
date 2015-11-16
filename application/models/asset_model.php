<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asset_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(								
			'employee_login_ad'=>'am_employee_login_ad',
			'alert_message'=>'am_alert_message',
			'announcement'=>'am_announcement',
			'media_upload'=>'am_media_upload',
			'announcement_message'=>'am_announcement_message',			
		);
	}

	function get_employee_login_ad($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('employee_login_ad', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_employee_login_ad($data)
	{
		return $this->insert('employee_login_ad', $data);
	}

	function update_employee_login_ad($data, $where)
	{
		return $this->update('employee_login_ad', $data, $where);
	}

	function delete_employee_login_ad($where)
	{
		return $this->delete('employee_login_ad', $where);
	}

	function get_employee_login_ad_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('employee_login_ad', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_employee_login_ad_by_id($employee_login_ad_id)
	{
		$result = $this->get_employee_login_ad(array('employee_login_ad_id' => $employee_login_ad_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	// --------------------------------
	// am_alert_message
	function get_alert_message($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('alert_message', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_alert_message_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('alert_message', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_alert_message_by_id($message_id)
	{
		$result = $this->get_alert_message(array('message_id' => $message_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function insert_alert_message($data)
	{
		return $this->insert('alert_message', $data);
	}

	function update_alert_message($data, $where)
	{
		return $this->update('alert_message', $data, $where);
	}

	function delete_alert_message($where)
	{
		return $this->delete('alert_message', $where);
	}

	// --------------------------------
	// am_announcement
	function get_announcement($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('announcement', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_announcement($data)
	{
		return $this->insert('announcement', $data);
	}

	function update_announcement($data, $where)
	{
		return $this->update('announcement', $data, $where);
	}

	function delete_announcement($where)
	{
		return $this->delete('announcement', $where);
	}

	function get_announcement_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('announcement', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_announcement_by_id($announcement_id)
	{
		$result = $this->get_announcement(array('announcement_id' => $announcement_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	// --------------------------------
	// media upload
	function get_media_upload($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('media_upload', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_media_upload($data)
	{
		return $this->insert('media_upload', $data);
	}

	function update_media_upload($data, $where)
	{
		return $this->update('media_upload', $data, $where);
	}

	function delete_media_upload($where)
	{
		return $this->delete('media_upload', $where);
	}

	function get_media_upload_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('media_upload', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_media_upload_by_id($media_upload_id)
	{
		$result = $this->get_media_upload(array('media_upload_id' => $media_upload_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	// --------------------------------
	// am_announcement_message
	function get_announcement_message($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('announcement_message', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_announcement_message($data)
	{
		return $this->insert('announcement_message', $data);
	}

	function update_announcement_message($data, $where)
	{
		return $this->update('announcement_message', $data, $where);
	}

	function delete_announcement_message($where)
	{
		return $this->delete('announcement_message', $where);
	}

	function get_announcement_message_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('announcement_message', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_announcement_message_by_id($announcement_message_id)
	{
		$result = $this->get_announcement_message(array('announcement_message_id' => $announcement_message_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	
}    