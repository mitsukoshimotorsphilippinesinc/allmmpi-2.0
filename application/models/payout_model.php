<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payout_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'tmp_commissions' => 'tmp_cm_member_commissions',
			'tmp_commissions_igpsm' => 'tmp_cm_member_commissions_igpsm_view',
			'tmp_commissions_unilevel' => 'tmp_cm_member_commissions_unilevel_view',
			'member_payouts' => 'po_member_payouts',
			'member_commissions' => 'po_member_commissions',
			'member_commissions_igpsm' => 'po_member_commissions_igpsm_view',
			'member_commissions_unilevel' => 'po_member_commissions_unilevel_view',
			'member_commissions_report' => 'po_member_commissions_report',
			'member_deductions' => 'po_member_deductions',
			'member_deduction_conflicts' => 'po_member_deduction_conflicts',
			'member_account_commissions' => 'po_member_account_commissions',
			'payout_download_sheets' => 'rf_payout_download_sheets',
			'download_sheet_processing' => 'po_download_sheet_processing',
			'member_account_commissions_history' => 'pe_member_account_commissions_history',
			'member_commissions_history' => 'pe_member_commissions_history',
			'payout_periods' => 'po_payout_periods', 
			'funds_to_paycard' => 'ph_funds_to_paycard',
			'member_adjustments' => 'po_member_adjustments',
			'member_gcep_deductions' => 'po_member_gcep_deductions',
            'member_sms_deductions' => 'po_member_sms_deductions',
			'payout_crediting_fields' => 'rf_payout_crediting_fields',
			'member_gcep_commissions' => 'ph_member_gcep_commissions',
			'member_gc_commissions' => 'ph_member_gc_commissions',
			'member_gcep_commissions_backups' => 'ph_member_gcep_commissions_backups',
			'member_gc_commissions_backups' => 'ph_member_gc_commissions_backups'
		);

	}

	function get_tmp_commissions($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('tmp_commissions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_tmp_commissions_igpsm($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('tmp_commissions_igpsm', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_tmp_commissions_unilevel($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('tmp_commissions_unilevel', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    // po_member_payouts
    function get_member_payouts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_payouts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_payout($data)
	{
		return $this->insert('member_payouts', $data);
	}

	function update_member_payouts($data, $where)
	{
		return $this->update('member_payouts', $data, $where);
	}

	function delete_member_payouts($where)
	{
		return $this->delete('member_payouts', $where);
	}

	function get_member_payouts_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_payouts', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

    // po_member_commissions
    function get_member_commissions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_commissions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_commission($data)
	{
		return $this->insert('member_commissions', $data);
	}

	function update_member_commissions($data, $where)
	{
		return $this->update('member_commissions', $data, $where);
	}

	function delete_member_commissions($where)
	{
		return $this->delete('member_commissions', $where);
	}

	function get_member_commissions_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_commissions', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_member_commissions_igpsm($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('member_commissions_igpsm', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_member_commissions_unilevel($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('member_commissions_unilevel', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    // tmp_po_member_commissions_report
    function get_member_commissions_report($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_commissions_report', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_member_commissions_report_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_commissions_report', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function insert_member_commissions_report($data)
	{
		return $this->insert('member_commissions_report', $data);
	}

	function update_member_commissions_report($data, $where)
	{
		return $this->update('member_commissions_report', $data, $where);
	}

	function delete_member_commissions_report($where)
	{
		return $this->delete('member_commissions_report', $where);
	}

	// member_deductions
	
	function get_member_deductions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_deductions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_deductions($data)
	{
		return $this->insert('member_deductions', $data);
	}

	function update_member_deductions($data, $where)
	{
		return $this->update('member_deductions', $data, $where);
	}

	function delete_member_deductions($where)
	{
		return $this->delete('member_deductions', $where);
	}

	// member_deduction_conflicts

	function get_member_deduction_conflicts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_deduction_conflicts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_deduction_conflicts($data)
	{
		return $this->insert('member_deduction_conflicts', $data);
	}

	function update_member_deduction_conflicts($data, $where)
	{
		return $this->update('member_deduction_conflicts', $data, $where);
	}

	function delete_member_deduction_conflicts($where)
	{
		return $this->delete('member_deduction_conflicts', $where);
	}

	// member_account_commissions

	function get_member_account_commissions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_account_commissions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_account_commissions($data)
	{
		return $this->insert('member_account_commissions', $data);
	}

	function update_member_account_commissions($data, $where)
	{
		return $this->update('member_account_commissions', $data, $where);
	}

	function delete_member_account_commissions($where)
	{
		return $this->delete('member_account_commissions', $where);
	}

	// rf_payout_download_sheets

	function get_payout_download_sheets($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payout_download_sheets', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	// download_sheet_processing

	function get_download_sheet_processing($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('download_sheet_processing', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_download_sheet_processing($data)
	{
		return $this->insert('download_sheet_processing', $data);
	}

	function update_download_sheet_processing($data, $where)
	{
		return $this->update('download_sheet_processing', $data, $where);
	}	

	// ph_funds_to_paycard
    function get_funds_to_paycard($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('funds_to_paycard', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_funds_to_paycard($data)
	{
		return $this->insert('funds_to_paycard', $data);
	}

	function update_funds_to_paycard($data, $where)
	{
		return $this->update('funds_to_paycard', $data, $where);
	}

	function delete_funds_to_paycard($where)
	{
		return $this->delete('funds_to_paycard', $where);
	}

	function get_funds_to_paycard_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('funds_to_paycard', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_funds_to_paycard_by_id($funds_to_paycard_id)
	{
		$result = $this->get_funds_to_paycard(array('funds_to_paycard_id' => $funds_to_paycard_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	// member_commissions
	function insert_member_commissions_history($data)
	{
		return $this->insert('member_commissions_history', $data);
	}

	function update_member_commissions_history($data, $where)
	{
		return $this->update('member_commissions_history', $data, $where);
	}

	function delete_member_commissions_history($where)
	{
		return $this->delete('member_commissions_history', $where);
	}
	
	
	// po_payout_periods 
    function get_payout_periods($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payout_periods', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_payout_periods_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payout_periods', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function insert_payout_period($data)
	{
		return $this->insert('payout_periods', $data);
	}

	function update_payout_period($data, $where)
	{
		return $this->update('payout_periods', $data, $where);
	}

	function delete_payout_period($where)
	{
		return $this->delete('payout_periods', $where);
	}

	
	
	function get_payout_period_by_id($payout_period_id)
	{
		$result = $this->get_payout_periods(array('payout_period_id' => $payout_period_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	

	// member_adjustments	
	function get_member_adjustments($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_adjustments', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_adjustments($data)
	{
		return $this->insert('member_adjustments', $data);
	}

	function update_member_adjustments($data, $where)
	{
		return $this->update('member_adjustments', $data, $where);
	}

	function delete_member_adjustments($where)
	{
		return $this->delete('member_adjustments', $where);
	} 

	// member_gcep_deductions	
	function get_member_gcep_deductions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_gcep_deductions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_gcep_deductions($data)
	{
		return $this->insert('member_gcep_deductions', $data);
	}

	function update_member_gcep_deductions($data, $where)
	{
		return $this->update('member_gcep_deductions', $data, $where);
	}

	function delete_member_gcep_deductions($where)
	{
		return $this->delete('member_gcep_deductions', $where);
	}
    
    function get_member_sms_deductions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_sms_deductions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_sms_deductions($data)
	{
		return $this->insert('member_sms_deductions', $data);
	}

	function update_member_sms_deductions($data, $where)
	{
		return $this->update('member_sms_deductions', $data, $where);
	}

	function delete_member_sms_deductions($where)
	{
		return $this->delete('member_sms_deductions', $where);
	}

	// payout crediting fields
	function get_payout_crediting_fields($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payout_crediting_fields', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_payout_crediting_field($data)
	{
		return $this->insert('payout_crediting_fields', $data);
	}

	function update_payout_crediting_fields($data, $where)
	{
		return $this->update('payout_crediting_fields', $data, $where);
	}

	function delete_payout_crediting_fields($where)
	{
		return $this->delete('payout_crediting_fields', $where);
	}

	function get_payout_crediting_fields_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payout_crediting_fields', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_payout_crediting_field_by_name($name)
	{
		$result = $this->get_payout_crediting_fields(array('name' => $name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	// gcep_commissions
	function get_member_gcep_commissions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_gcep_commissions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_gcep_commissions($data)
	{
		return $this->insert('member_gcep_commissions', $data);
	}

	function update_member_gcep_commissions($data, $where)
	{
		return $this->update('member_gcep_commissions', $data, $where);
	}

	function delete_member_gcep_commissions($where)
	{
		return $this->delete('member_gcep_commissions', $where);
	}

	function get_member_gcep_commissions_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_gcep_commissions', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// gc_commissions
	function get_member_gc_commissions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_gc_commissions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_gc_commissions($data)
	{
		return $this->insert('member_gc_commissions', $data);
	}

	function update_member_gc_commissions($data, $where)
	{
		return $this->update('member_gc_commissions', $data, $where);
	}

	function delete_member_gc_commissions($where)
	{
		return $this->delete('member_gc_commissions', $where);
	}

	function get_member_gc_commissions_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_gc_commissions', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// gcep_commissions backups
	function get_member_gcep_commissions_backups($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_gcep_commissions_backups', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_gcep_commissions_backups($data)
	{
		return $this->insert('member_gcep_commissions_backups', $data);
	}

	function get_member_gcep_commissions_backups_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_gcep_commissions_backups', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// gc_commissions backups
	function get_member_gc_commissions_backups($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_gc_commissions_backups', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_gc_commissions_backups($data)
	{
		return $this->insert('member_gc_commissions_backups', $data);
	}

	function get_member_gc_commissions_backups_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_gc_commissions_backups', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
}