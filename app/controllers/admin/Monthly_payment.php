<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Monthly_payment extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		$this->lang->admin_load('people', $this->Settings->user_language);
		$this->load->library('form_validation');
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
		$this->load->admin_model('users_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('people_model');
    }
	
		
	/*###### users*/
   
	function index($action=false){
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
		$this->data['user'] = $this->users_model->getUser($this->session->userdata('user_id'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_allocated')));
        $meta = array('page_title' => lang('driver_allocated'), 'bc' => $bc);
		
        $this->page_construct('driver_allocated/index', $meta, $this->data);
    }
	
	function getMonthlyPayment(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->load->library('datatables');
		$this->datatables
            ->select("{$this->db->dbprefix('driver_payment')}.id as id, ud.first_name as driver_name, uv.first_name as vendor_name, {$this->db->dbprefix('driver_payment')}.amount, {$this->db->dbprefix('driver_payment')}.payment_type, {$this->db->dbprefix('driver_payment')}.start_date, If({$this->db->dbprefix('driver_payment')}.send_status = 1, '1', '0') as send_status, If({$this->db->dbprefix('driver_payment')}.recived_status = 1, '1', '0') as recived_status, If({$this->db->dbprefix('driver_payment')}.status = 1, '1', '0') as status, country.name as instance_country ")
            ->from("driver_payment")
			->join("countries country", " country.iso = driver_payment.is_country", "left")
			->join("user_profile ud", "ud.user_id = driver_payment.driver_id AND ud.is_country = '".$countryCode."'", 'left')
			->join("user_profile uv", "uv.user_id = driver_payment.vendor_id AND uv.is_country = '".$countryCode."'", 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('send_status', '$1__$2', 'send_status, id')
			->edit_column('recived_status', '$1__$2', 'recived_status, id')
            ->edit_column('status', '$1__$2', 'status, id');
		echo $this->datatables->generate();
	}
	
	
	
}
