<?php defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('procurment/purchase_invoices', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('procurment/purchase_invoices_model');
	$this->load->admin_model('procurment/supplier_model');
        $this->digital_upload_path = 'assets/uploads/procurment/invoice/';
	if (!file_exists($this->digital_upload_path)) {
		mkdir($this->digital_upload_path, 0777, true);
	}
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
		
		$this->Muser_id = $this->session->userdata('user_id');
		$this->Maccess_id = 8;
    }
    function add()
    {
        $this->sma->checkPermissions(false, true);
	$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'is_unique[companies.email]');

        if ($this->form_validation->run('companies/add') == true) {

            $data = array(
		'name' => $this->input->post('name'),                
                'email' => $this->input->post('email'),
		'ref_id' => 'SUP-'.date('YmdHis'),
                'group_id' => '4',
                'group_name' => 'supplier',                
		'mobile_number' => $this->input->post('mobile_number'),				
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
		'is_status' => $this->input->post('is_status') ? $this->input->post('is_status') : 1,
            );
        } elseif ($this->input->post('name')) {
            $error = validation_errors();
            $response['error'] = $error;
            echo json_encode($response);exit;
        }

        if ($this->form_validation->run() == true && $sid = $this->supplier_model->addSupplier($data)) {
            $data['id'] = $sid;
            $response['supplier'] = $data;
            echo json_encode($response);exit;
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
	    $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->load->view($this->theme . 'procurment/supplier/add', $this->data);
        }
    }
	
}
