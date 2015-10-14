<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Items_model extends Base_Model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
				'items' => 'is_items',
				'item_builds' => 'is_item_builds',
				'item_types' => 'rf_item_types',
				'item_sub_types' => 'rf_item_sub_types',
				'product_types' => 'rf_product_types',
				'package_types' => 'rf_package_types',
				'product_lines' => 'rf_product_lines',
				'product_cards' => 'rf_product_cards',
				'units' => 'is_units',
				'unit_conversions' => 'is_unit_conversion',
				'packages' => 'is_packages',
				'package_products' => 'is_package_products',
				'package_products_view' => 'is_package_products_view',
				'product_products' => 'is_product_products',
				'product_products_view' => 'is_product_products_view',
				'products' => 'is_products',
				'products_view' => 'is_products_view',
				'payment_methods' => 'rf_payment_methods',
				'raffle_products' => 'is_raffle_products'
		);

	}

	function get_items($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('items', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_items_by_ids($item_ids, $limit = null) {
		
		$this->db->select("*");
		$this->db->where_in('item_id', $item_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['items']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function insert_item($data)
	{
		return $this->insert('items', $data);
	}

	function update_item($data, $where)
	{
		return $this->update('items', $data, $where);
	}

	function delete_item($where)
	{
		return $this->delete('items', $where);
	}

	function get_item_by_id($item_id)
	{
		$result = $this->get_items(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_item_by_name($item_name)
	{
		$result = $this->get_items(array('item_name' => $item_name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function item_search($item_name)
	{

		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->select('item_id,item_name');
		$this->db->like('item_name',$item_name,'both');


		// set table to use
		$this->db->from($this->_TABLES['items']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}

	function get_item_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('items', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_available_items()
	{
		$products = $this->items_model->get_products(array("item_id >" => 0),null,"item_id ASC","item_id");
		$item_id_array = array();

		if (!empty($products))
			foreach($products as $p) array_push($item_id_array, $p->item_id);
		else
			array_push($item_id_array, 0);
			
		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->where_not_in("item_id",$item_id_array);

		$this->db->order_by("item_name");

		// set table to use
		$this->db->from($this->_TABLES['items']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}

	function get_item_sub_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('item_sub_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_item_sub_types_by_ids($item_sub_type_ids, $limit = null) {
		
		$this->db->select("*");
		$this->db->where_in('item_sub_type_id', $item_sub_type_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['item_sub_types']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function insert_item_sub_type($data)
	{
		return $this->insert('item_sub_types', $data);
	}

	function update_item_sub_type($data, $where)
	{
		return $this->update('item_sub_types', $data, $where);
	}

	function delete_item_sub_type($where)
	{
		return $this->delete('item_sub_types', $where);
	}

	function get_item_sub_type_by_id($item_sub_type_id)
	{
		$result = $this->get_item_sub_types(array('item_sub_type_id' => $item_sub_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_item_sub_type_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('item_sub_types', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_item_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('item_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_item_type($data)
	{
		return $this->insert('item_types', $data);
	}

	function update_item_type($data, $where)
	{
		return $this->update('item_types', $data, $where);
	}

	function delete_item_type($where)
	{
		return $this->delete('item_types', $where);
	}

	function get_item_type_by_id($item_type_id)
	{
		$result = $this->get_item_types(array('item_type_id' => $item_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_item_type_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('item_types', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_units($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('units', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_unit($data)
	{
		return $this->insert('units', $data);
	}

	function update_unit($data, $where)
	{
		return $this->update('units', $data, $where);
	}

	function delete_unit($where)
	{
		return $this->delete('units', $where);
	}

	function get_unit_by_id($unit_id)
	{
		$result = $this->get_units(array('unit_id' => $unit_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_unit_by_code($unit_code)
	{
		$result = $this->get_units(array('unit_code' => $unit_code));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_unit_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('units', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_unit_conversions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('unit_conversions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_unit_conversion($data)
	{
		return $this->insert('unit_conversions', $data);
	}

	function update_unit_conversion($data, $where)
	{
		return $this->update('unit_conversions', $data, $where);
	}

	function delete_unit_conversion($where)
	{
		return $this->delete('unit_conversions', $where);
	}
	
	function get_unit_conversion_by_codes($convert_from, $item_id)
	{
		$result = $this->get_unit_conversions(array('unit_code' => $convert_from, 'item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_unit_conversion_by_id($unit_conversion_id)
	{
		$result = $this->get_unit_conversions(array('unit_conversion_id' => $unit_conversion_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_unit_conversion_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('unit_conversions', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('products_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_product($data)
	{
		return $this->insert('products', $data);
	}

	function update_product($data, $where)
	{
		return $this->update('products', $data, $where);
	}

	function delete_product($where)
	{
		return $this->delete('products', $where);
	}

	function get_lower_product_from_ids($product_id_1, $product_id_2, $mode = 'standard_retail')
	{
		$price = $mode . '_price';

		$product_1 = $this->get_products(array('product_id' => $product_id_1), null, null, array($price));

	    $product_2 = $this->get_products(array('product_id' => $product_id_2), null, null, array($price));

	    if($product_1[0]->$price < $product_2[0]->$price)
			return $product_id_1;
	    else
			return $product_id_2;
	}


	function get_product_by_product_name($product_name)
	{
		$result = $this->get_products(array('product_name' => $product_name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_product_by_id($product_id)
	{
		$result = $this->get_products(array('product_id' => $product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_product_by_item_id($item_id)
	{
		$result = $this->get_products(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_product_by_product_code($product_code)
	{
		$result = $this->get_products(array('product_code' => $product_code));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_product_by_ids($product_ids, $limit = null) {
		
		$this->db->select("*");
		$this->db->where_in('product_id', $product_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['products_view']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function get_product_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('products_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_packages($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('packages', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_package($data)
	{
		return $this->insert('packages', $data);
	}

	function update_package($data, $where)
	{
		return $this->update('packages', $data, $where);
	}

	function delete_package($where)
	{
		return $this->delete('packages', $where);
	}

	function get_package_by_id($package_id)
	{
		$result = $this->get_packages(array('package_id' => $package_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_package_by_item_id($item_id)
	{
		$result = $this->get_packages(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_package_by_name($package_name)
	{
		$result = $this->get_packages(array('package_name' => $package_name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_package_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('packages', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_package_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('package_products_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_package_products_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('package_products_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function insert_package_product($data)
	{
		return $this->insert('package_products', $data);
	}

	function update_package_product($data, $where)
	{
		return $this->update('package_products', $data, $where);
	}

	function delete_package_product($where)
	{
		return $this->delete('package_products', $where);
	}

	function get_package_product_by_id($package_product_id)
	{
		$result = $this->get_package_products(array('package_product_id' => $package_product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_package_product_by_package_id($package_id)
	{
		$result = $this->get_package_products(array('package_id' => $package_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_payment_methods($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_methods', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
		
	// rf_package_types
	function get_package_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('package_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_package_type($data)
	{
		return $this->insert('package_types', $data);
	}

	function update_package_type($data, $where)
	{
		return $this->update('package_types', $data, $where);
	}

	function delete_package_type($where)
	{
		return $this->delete('package_types', $where);
	}

	function get_package_type_by_id($package_type_id)
	{
		$result = $this->get_package_types(array('package_type_id' => $package_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_package_type_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('package_types', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	// rf_product_lines
	function get_product_lines($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_lines', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_product_line($data)
	{
		return $this->insert('product_lines', $data);
	}

	function update_product_line($data, $where)
	{
		return $this->update('product_lines', $data, $where);
	}

	function delete_product_line($where)
	{
		return $this->delete('product_lines', $where);
	}

	function get_product_line_by_id($product_line_id)
	{
		$result = $this->get_product_lines(array('product_line_id' => $product_line_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_product_line_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('product_lines', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_item_builds($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('item_builds', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_item_build($data)
	{
		return $this->insert('item_builds', $data);
	}

	function update_item_build($data, $where)
	{
		return $this->update('item_builds', $data, $where);
	}

	function delete_item_build($where)
	{
		return $this->delete('item_builds', $where);
	}

	function get_item_build_by_item_id($item_id)
	{
		$where = "item_id = {$item_id}";
		$rows = $this->get_item_builds($where);
		return $rows;
	}

	function get_product_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_product_type_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('product_types', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function insert_product_type($data)
	{
		return $this->insert('product_types', $data);
	}

	function update_product_type($data, $where)
	{
		return $this->update('product_types', $data, $where);
	}

	function delete_product_type($where)
	{
		return $this->delete('product_types', $where);
	}
	
	function get_product_type_by_id($product_type_id)
	{
		$result = $this->get_product_types(array('product_type_id' => $product_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_product_type_array($where = null, $limit = null, $orderby = null)
	{
		
		$result = $this->get_product_types($where, $limit, $orderby);
		
		$rows = array();
		if (count($result) > 0)
		{
			foreach($result as $r)
			{
				$rows[$r->product_type_id] = $r;
			}
		}
		return $rows;
	}

	function get_product_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_products_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_product_products2($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_product_products_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('product_products_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function insert_product_product($data)
	{
		return $this->insert('product_products', $data);
	}

	function update_product_product($data, $where)
	{
		return $this->update('product_products', $data, $where);
	}

	function delete_product_product($where)
	{
		return $this->delete('product_products', $where);
	}

	function get_product_product_by_child_product_id($child_product_id)
	{
		$result = $this->get_product_products(array('child_product_id' => $child_product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_product_product_by_product_id($product_id)
	{
		$result = $this->get_product_products(array('product_id' => $product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_product_product_by_product_id_and_child_product_id($product_id,$child_product_id)
	{
		$result = $this->get_product_products(array('product_id' => $product_id,'child_product_id' => $child_product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_product_cards($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_cards', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_product_cards_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('product_cards', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function insert_product_cards($data)
	{
		return $this->insert('product_cards', $data);
	}

	function update_product_cards($data, $where)
	{
		return $this->update('product_cards', $data, $where);
	}

	function delete_product_cards($where)
	{
		return $this->delete('product_cards', $where);
	}
	
	function get_raffle_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('raffle_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_raffle_products($data)
	{
		return $this->insert('raffle_products', $data);
	}
	
	function update_raffle_products($data, $where)
	{
		return $this->update('raffle_products', $data, $where);
	}
	
	function get_raffle_product_by_product_id($product_id)
	{
		$result = $this->get_raffle_products(array('product_id' => $product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
}