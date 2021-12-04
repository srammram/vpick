<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Saleitem_to_purchasesitem extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->lang->admin_load('tables', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('saleitem_to_purchasesitem_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

	/* Tables*/
	
	function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('saleitem_to_purchasesitem'), 'page' => lang('Bill Of Material')));
        $meta = array('page_title' => lang('Bill Of Material'), 'bc' => $bc);
		$this->data['sale_purchase'] = $this->saleitem_to_purchasesitem_model->getAllrecipe();
		
        $this->page_construct('saleitempurchasesitem/index', $meta, $this->data);
    }

   

}
