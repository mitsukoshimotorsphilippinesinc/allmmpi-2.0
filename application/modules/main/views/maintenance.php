<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance extends Base_Controller {

    public function before()
    {
        parent::before();

    }

    public function __construct()
    {
        parent::__construct();

        // load contents model
        $this->load->model("contents_model");

    }


    public function index()
    {
        $this->page();
    }

    public function maintenance()
    {

    }


}