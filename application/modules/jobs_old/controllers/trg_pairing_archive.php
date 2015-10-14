<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Trg_pairing_archive extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
	}

	public function index() 
	{
		echo "Process trg_pairing_before_after_movement archive\n";
	}

	public function archive()
	{
		$this->load->helper('url');	
		$this->load->helper("notifications_helper");
		$this->load->library('zip');
		
		$fileds_str = '';
		$tmp_str = '';
		$fields = array();
		$data_type = array();

		$tbl_fields = $this->db->field_data('trg_pairing_before_after_movement');
		foreach($tbl_fields as $field)
		{
			$fields[] = "`".$field->name."`";
			$data_type[$field->name] = $field->type;
		}

		$record_count = $this->db->count_all('trg_pairing_before_after_movement');
		echo $record_count." records has been found\n\n";

		$loop_count = ceil($record_count/2000);
		echo $loop_count." loops will be done\n\n";

		for($i = 0 ; $i < $loop_count; $i++)
		{
			$export_str = '';
			$offset = $i*2000;
			$ctr = 0;

			$query = $this->db->get('trg_pairing_before_after_movement',2000,$offset);

			foreach($query->result() as $row)
			{
				$data = array();

				foreach($row as $key=>$value)
				{	
					if($data_type[$key] == 'varchar' || $data_type[$key] == 'timestamp')
						$data[] = "'".$value."'";
					else
						$data[] = $value;
				}
					
				if($ctr % 2000 == 0)
				{	
					$export_str .= "INSERT INTO `trg_pairing_before_after_movement` (";
					$export_str .= implode(', ',$fields);
					$export_str .= ") VALUES \n";
				}

				$export_str .= "\t\t(";
				$export_str .= implode(", ",$data);
				$export_str .= ")";
									
				$ctr++;

				if($ctr % 2000 == 0 || $ctr == $query->num_rows())
					$export_str.= ";\n";
				else
					$export_str.= ",\n";

			}
			echo "loop {$i}\n\n";
			$tmp_str .= $export_str;
		}

		$date = date('y-m-d-his');
		$filename = $date."-trg_pairing_before_after_movement.sql";
		$zipname = $date."-trg_pairing_before_after_movement.zip";

		$this->zip->add_data($filename,$tmp_str);
		echo "Archiving data\n\n";
		$this->zip->archive(FCPATH . "assets/media/tmp/".$zipname);
		echo "Saving zip\n\n";

		echo "Delete from database\n\n";
		$this->db->empty_table('trg_pairing_before_after_movement');

		echo "Sending Email\n\n";

		$data['email'] = 'xdivina@gobeyondstudios.com';
		$title = 'trg_pairing_before_after_movement backup for '.$date;

		$base_url = $this->get_http_host();
		
		$message = "trg_pairing_before_after_movement backup for ".$date;
		$message .= "<br /><br />";
		$message .= "<a href=\"".$base_url."assets/media/tmp/".$filename."\">".$base_url."assets/media/tmp/".$zipname."</a>";

		$username = 'odie.miranda@gmail.com';
		$api_key = '5e75d891-37be-415d-985f-c1eda6b147c7';

		$email_group = $this->settings->trg_archive_recipient;
		$emails = explode(',',$email_group);

		if(isset($emails))
		{
			foreach($emails as $email)
			{	
				//send_elastic_email($username,
				//				$api_key,
				//				$email,
				//				$title,
				//				$message,
				//				$message,
				//				$this->settings->email_from,
				//				$this->settings->email_from
				//);

				// set params
				$params = array(
					"from"=>$this->settings->email_from,
					"to"=>$email,
					"subject"=>$title,
					"message"=>$message
				);

				// load contents_model
				$this->load->helper("notifications_helper");

				return send_email($params);
				sleep(2000);

			}
		}
		echo "email sent.. finished\n\n";		
	}

	private function get_http_host()
	{
		switch (ENVIRONMENT)
		{
			case 'local':
				return "http://cp.vitalc.dev/";
			case 'development':
				return "http://cp.vitalc.dev.gobeyondstudios.com/";
			case 'staging':
				return "http://cp.vitalc.staging.gobeyondstudios.com/";
		
			case 'testing':
				return "http://cp.vitalc.qa.gobeyondstudios.com/";
			default:
				return "http://cp.vital-c.com/";
			
		}
	}

}