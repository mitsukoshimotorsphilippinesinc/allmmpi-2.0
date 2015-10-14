<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers extends Systems_Controller {

    private $_validation_rule = array(
            array(
                            'field' => 'supplier_name',
                            'label' => 'Supplier\'s Name',
                            'rules' => 'trim|required|min_length[4]'
            ),
            array(
                            'field' => 'supplier_description',
                            'label' => 'Supplier\'s Description',
                            'rules' => 'trim'
            ),
            array(
                            'field' => 'supplier_address',
                            'label' => 'Supplier\'s Address',
                            'rules' => 'trim'
            ),
            array(
                            'field' => 'supplier_contact_details',
                            'label' => 'Supplier\'s Contact Details',
                            'rules' => 'trim'
            ),

    );

	public function __construct() 
	{
		parent::__construct();
		// load model needed by this controller and loaded view
		$this->load->model('suppliers_model');
	}

    public function index() 
	{
        $this->template->suppliers = $this->suppliers_model->get_suppliers();
        $this->template->view('suppliers/list');
    }

    public function add() {
        if ($_POST) {
            // post done here

            $this->form_validation->set_rules($this->_validation_rule);

            if ($this->form_validation->run()) {

                // insert the new supplier
                $data = array(
                        'supplier_name' => set_value('supplier_name'),
                        'supplier_description' => set_value('supplier_description'),
                        'supplier_address' => set_value('supplier_address'),
                        'supplier_contact_details' => set_value('supplier_contact_details'),

                );
                $this->suppliers_model->insert_supplier($data);

                redirect('/admin/suppliers');
                return;
            }
        }
        $this->template->view('suppliers/add');

    }

    public function edit($supplier_id = 0) {
        $supplier = $this->suppliers_model->get_supplier_by_id($supplier_id);

        if ($_POST and !empty($supplier)) {
            // post done here
            $this->form_validation->set_rules($this->_validation_rule);

            if ($this->form_validation->run()) {

                // edit the supplier
                $data = array(
                        'supplier_name' => set_value('supplier_name'),
                        'supplier_description' => set_value('supplier_description'),
                        'supplier_address' => set_value('supplier_address'),
                        'supplier_contact_details' => set_value('supplier_contact_details'),

                );

                $this->suppliers_model->update_supplier($data, array('supplier_id' => $supplier_id));

                redirect('/admin/suppliers');
                return;
            }
        }

        $this->template->supplier = $supplier;
        $this->template->view('suppliers/edit');

    }

    public function delete($supplier_id = 0) {

        $supplier = $this->suppliers_model->get_supplier_by_id($supplier_id);

        if ($_POST and !empty($supplier)) {
            $_supplier_id = $this->input->post('supplier_id');
            if (!empty($_supplier_id)) if ($_supplier_id == $supplier_id) {
                    $this->suppliers_model->delete_supplier(array('supplier_id' => $supplier_id));
                    redirect('/admin/suppliers');
                    return;
                }
        }

        $this->template->supplier = $supplier;
        $this->template->view('suppliers/delete');

    }

}