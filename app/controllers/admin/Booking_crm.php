<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_crm extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$this->lang->admin_load('masters', $this->Settings->user_language);
		$this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->library('upload');
		$this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('booking_crm_model');
		$this->load->admin_model('masters_model');
		
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	/*###### Currency*/
	function index($action = NULL)
    {
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('dashboard')));
        $meta = array('page_title' => lang('dashboard'), 'bc' => $bc);
        $this->page_construct('booking_crm/index', $meta, $this->data);
    }
	
	function create_customer(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('name', lang("name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('currencies', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("booking_crm/create_customer");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
				'unicode_symbol' => $this->input->post('unicode_symbol'),
				'iso_code' =>$this->input->post('iso_code') ? $this->input->post('iso_code') : '',
				'numeric_iso_code' =>$this->input->post('numeric_iso_code') ? $this->input->post('numeric_iso_code') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
            );
			
           
        }elseif ($this->input->post('add_currencies')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("booking_crm");
        }
		
        if ($this->form_validation->run() == true && $this->booking_crm_model->create_customer($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("customer_added"));
            admin_redirect('booking_crm');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('booking_crm'), 'page' => lang('booking_crm')), array('link' => '#', 'page' => lang('create_customer')));
			
            $meta = array('page_title' => lang('create_customer'), 'bc' => $bc);
            $this->page_construct('booking_crm/create_customer', $meta, $this->data);
        }
	}
	
    function listview($action = NULL)
    {
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('booking_crm')));
        $meta = array('page_title' => lang('booking_crm'), 'bc' => $bc);
        $this->page_construct('booking_crm/listview', $meta, $this->data);
    }
	
		
    

}
