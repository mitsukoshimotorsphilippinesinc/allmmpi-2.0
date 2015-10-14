<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generate_vpn_keys extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db = $this->load->database('default', TRUE);
	}

	public function index()
	{
		
		$this->template->view('genvpnkeys');
	}

	public function process() 
	{

		// drop create table
		$sql_drop = "DROP TABLE IF EXISTS tmp_generated_vpn";
		$this->db->query($sql_drop);

		$sql_create = "CREATE TABLE tmp_generated_vpn (
						`vpn_id`			int(11) AUTO_INCREMENT,
						`key` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
						`insert_timestamp` 	timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY (vpn_id)			
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$this->db->query($sql_create);

		for ($ctr = 0; $ctr <= 827; $ctr++) {

			$characters = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';	
		    $charactersLength = strlen($characters);

		    $random_string = '';
		    for ($i = 0; $i < 10; $i++) {
		        $random_string .= $characters[rand(0, $charactersLength - 1)];
		    }

		    $sql_insert = "INSERT INTO tmp_generated_vpn(`key`) VALUES ('{$random_string}')";

		    $this->db->query($sql_insert);
		}    

		echo "json_encode('status' => '1')";

		return;
	}
}