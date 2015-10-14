<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar extends Site_Controller
{
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("contents_model");
	}

	public function index() 
	{
		$this->template->eventsArray = $this->calendar_of_events();
		$this->template->view("calendar");
	}
	
	public function calendar_of_events()
	{
		$month = $this->input->post("month");
		$year = $this->input->post("year");
		$is_post = true;
		$eventsArray = array();
		if($month == false)
		{
			$is_post = false;
			$month = date("n");
		}
		
		if($year == false)
		{
			$is_post = false;
			$year = date("Y");
		}
		
		$featured_events = $this->contents_model->get_featured("`type` = 'event' AND (MONTH(`start_date`) = '{$month}' AND YEAR(`start_date`) = '{$year}') OR (MONTH(`end_date`) = '{$month}' AND YEAR(`end_date`) = '{$year}')",null,"start_date ASC","start_date,end_date");
		
		if(!empty($featured_events) && !is_null($featured_events))
		{
			foreach($featured_events as $fe)
			{
				if($fe->end_date == "0000-00-00 00:00:00")
				{	
					$start_date = date("Y-n-j",strtotime($fe->start_date));
					if(!in_array($start_date,$eventsArray)) array_push($eventsArray,date("Y-n-j",strtotime($start_date)));
				}
				else
				{
					$start_date = date("Y-n-j",strtotime($fe->start_date));
					$end_date = date("Y-n-j",strtotime($fe->end_date));
					
					if($start_date == $end_date)
					{
						if(!in_array($start_date,$eventsArray)) array_push($eventsArray,date("Y-n-j",strtotime($start_date)));
					}
					else
					{
						while(strtotime($start_date) <= strtotime($end_date))
						{
							if(!in_array($start_date,$eventsArray)) array_push($eventsArray,date("Y-n-j",strtotime($start_date)));
							
							$date = new DateTime($start_date);
							$date->modify('+1 day');
 							$start_date = $date->format('Y-n-j');
						}
					}
				}
			}
		}
		
		if($is_post)
		{
			echo $this->return_json("ok","",array("eventsArray" => $eventsArray));
			return;
		}
		else
		{
			return $eventsArray;
		}
	}
}