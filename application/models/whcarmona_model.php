<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Whcarmona_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();
		
		$this->db = $this->load->database('WHCarmona', TRUE);

		// assign the table for this model
		$this->_TABLES = array(
				'matchedckd' => 'MSMatchedCKD',
				'tblmotorcycle' => 'tblMotorCycle',
				);

	}

	function get_matchedckd($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('matchedckd', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_tblmotorcycle($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('tblmotorcycle', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_matchedckd_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('matchedckd', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	

	function insert_matchedckd($data)
	{
		return $this->insert('matchedckd', $data);
	}

	function update_member($data, $where)
	{
		return $this->update('matchedckd', $data, $where);
	}

	function delete_member($where)
	{
		return $this->delete('matchedckd', $where);
	}
}