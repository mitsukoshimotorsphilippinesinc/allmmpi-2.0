<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cards_releasing extends Admin_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
	}
	
	public function index() 
	{
		$this->template->view('releasing');
	}
	
	public function release_cards()
	{
		$test = "";
		
		$this->load->model('cards_model');
		$this->load->model('logs_model');
		
		$transaction_id = $this->input->get_post("transaction_id");
		$released_to = $this->input->get_post("released_to");
		$sp_cards_list = $this->input->get_post("sp_cards_list");
		$rs_cards_list = $this->input->get_post("rs_cards_list");
		$rfid_cards_list = $this->input->get_post("rfid_cards_list");
		$pay_cards_list = $this->input->get_post("pay_cards_list");
		
		
		//remove all white space
		$sp_cards_list = str_replace(" ", "", $sp_cards_list);
		$rs_cards_list = str_replace(" ", "", $rs_cards_list);
		$rfid_cards_list = str_replace(" ", "", $rfid_cards_list);
		$pay_cards_list = str_replace(" ", "", $pay_cards_list);
		
		//validate entries, check if the cards follow the correct pattern, all cards are numeric and unique; and dashed entries are correct
		
		$syntax_error = array();
		
		$syntax_error["sp_results"] = $this->_check_pattern("SP",$sp_cards_list);
		$syntax_error["rs_results"] = $this->_check_pattern("RS",$rs_cards_list);
		$syntax_error["rf_results"] = $this->_check_pattern("RF",$rfid_cards_list);
		$syntax_error["metrobank_results"] = $this->_check_pattern("METROBANK",$pay_cards_list);
		
		
		if(!empty($syntax_error["sp_results"]) || !empty($syntax_error["rs_results"]) || !empty($syntax_error["rf_results"]) || !empty($syntax_error["metrobank_results"]))
		{
			$this->return_json("error","",$syntax_error);
			return;
		}
		
		$range_error = array();
		
		$range_error["sp_results"] = $this->_check_range("SP",$sp_cards_list);
		$range_error["rs_results"] = $this->_check_range("RS",$rs_cards_list);
		$range_error["rf_results"] = $this->_check_range("RF",$rfid_cards_list);
		$range_error["metrobank_results"] = $this->_check_range("METROBANK",$pay_cards_list);
		
		
		if(!empty($range_error["sp_results"]) || !empty($range_error["rs_results"]) || !empty($range_error["rf_results"]) || !empty($range_error["metrobank_results"]))
		{
			$this->return_json("error","",$range_error);
			return;
		}
		
		//parse the card entry for comma-delimited entries and expanding dashed inputs
		$sp_cards_list = $this->cards_model->parse_rs_sp_cards($sp_cards_list);
		$rs_cards_list = $this->cards_model->parse_rs_sp_cards($rs_cards_list);
		$rfid_cards_list = $this->cards_model->parse_rfid_cards($rfid_cards_list);
		$pay_cards_list = $this->cards_model->parse_pay_cards($pay_cards_list);
		
		$uniqueness_error = array();
		
		$uniqueness_error["sp_results"] = $this->_check_uniqueness($sp_cards_list);
		$uniqueness_error["rs_results"] = $this->_check_uniqueness($rs_cards_list);
		$uniqueness_error["rf_results"] = $this->_check_uniqueness($rfid_cards_list);
		$uniqueness_error["metrobank_results"] = $this->_check_uniqueness($pay_cards_list);
		
		
		if(!empty($uniqueness_error["sp_results"]) || !empty($uniqueness_error["rs_results"]) || !empty($uniqueness_error["rf_results"]) || !empty($uniqueness_error["metrobank_results"]))
		{
			$this->return_json("error","",$uniqueness_error);
			return;
		}
		
		//checks that all sp/rs cards are valid for releasing
		$sp_results = $this->cards_model->check_sp_cards($sp_cards_list);
		$rs_results = $this->cards_model->check_rs_cards($rs_cards_list);
		
		
		if(strcmp($sp_results["status"],"error") == 0 || strcmp($rs_results["status"],"error") == 0)
		{
			$this->return_json("error","",array("sp_results" => $sp_results["errors"],"rs_results" => $rs_results["errors"]));
			return;
		}
		
		/*$this->cards_model->release_sp_cards($released_to,$sp_cards_list);
		$this->cards_model->release_rs_cards($released_to,$rs_cards_list);
		
		//log the cards
		$cards_entered = array(
			"SP" => $sp_cards_list,
			"RS" => $rs_cards_list,
			"RF" => $rfid_cards_list,
			"METROBANK" => $pay_cards_list
		);
		$this->logs_model->insert_cards_log($transaction_id,$cards_entered);*/
		
		$this->return_json("ok","Cards released successfully");
		return;
	}
	
	private function _check_pattern($type, $card_list = "")
	{
		$error = array();
		
		if(strcmp($type,"SP") == 0 || strcmp($type,"RS") == 0)
		{
			if(!preg_match("/^$|^([0-9]{10}(-[0-9]{10})?(,[0-9]{10}(-[0-9]{10})?)*)$/", $card_list))
			{
				array_push($error,(object) array("card_id" => "","error"=>"There was an error in your syntax. Numbers must be numeric and 10 characters long; and must have no other special characters other than dashes(-) and commas(,)."));
			}
		}
		elseif(strcmp($type,"RF") == 0)
		{
			if(!preg_match("/^$|^([0-9]{10}(-[0-9]{10})?(,[0-9]{10}(-[0-9]{10})?)*)$/", $card_list))
			{
				array_push($error,(object) array("card_id" => "","error"=>"There was an error in your syntax. Numbers must be numeric and 10 characters long; and must have no other special characters other than dashes(-) and commas(,)."));
			}
		}
		elseif(strcmp($type,"METROBANK") == 0)
		{
			if(!preg_match("/^$|^([0-9]{10}(-[0-9]{10})?(,[0-9]{10}(-[0-9]{10})?)*)$/", $card_list))
			{
				array_push($error,(object) array("card_id" => "","error"=>"There was an error in your syntax. Numbers must be numeric and 10 characters long; and must have no other special characters other than dashes(-) and commas(,)."));
			}
		}
		
		return $error;
	}
	
	private function _check_range($type, $card_list = "")
	{
		if(strcmp($card_list,"") == 0) return array();
		
		$error = array();
		
		$card_list = explode(",", $card_list);
		
		foreach($card_list as $cards)
		{
			$delimiter = "-";
			if(strcmp($type,"SP") == 0 || strcmp($type,"RS") == 0)
			{
				$delimiter = "-";
			}
			elseif(strcmp($type,"RF") == 0)
			{
				$delimiter = "-";
			}
			elseif(strcmp($type,"METROBANK") == 0)
			{
				$delimiter = "-";
			}
			
			if(strpos($cards,$delimiter) != false)
			{
				$first = substr($cards, 0,strpos($cards,$delimiter));
				$last = substr($cards,strpos($cards,$delimiter)+1);
				
				if($first > $last)
				{
					array_push($error,(object) array("card_id" => "{$cards}","error"=>"Ranges must be in ascending order."));
				}
			}
		}
		
		return $error;
	}
	
	private function _check_uniqueness($card_array = array())
	{
		if(empty($card_array)) return array();
		
		$error = array();
		
		foreach(array_count_values($card_array) as $k => $v)
		{
			if($v > 1)
			{
				array_push($error,(object) array("card_id" => "{$k}","error"=>"This card appears more than once."));
			}
		}
		
		return $error;
	}
}