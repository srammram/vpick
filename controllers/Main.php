<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller
{

    function __construct() {
        parent::__construct();
		 $this->load->admin_model('main_model');
		 $this->load->admin_model('rides_model');
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
    }

	function sos(){
		
      	//$booked_status = $_GET['status']; 
		$id = $_GET['id']; 
		
		/*if($booked_status == 1){
			$msg = 'Request Ride';
		}elseif($booked_status == 2){
			$msg = 'Booked Ride';
		}elseif($booked_status == 3){
			$msg = 'Onride Ride';
		}elseif($booked_status == 4){
			$msg = 'Waiting Ride';
		}elseif($booked_status == 5){
			$msg = 'Completed Ride';
		}elseif($booked_status == 6){
			$msg = 'Cancelled Ride';
		}elseif($booked_status == 7){
			$msg = 'Ride Later Ride';
		}*/
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		//$this->data['status'] = $booked_status;
		$this->data['rides'] = $this->rides_model->getRides($id);
		$this->load->view($this->theme . 'tracking', $this->data);
	}
	function getpath($id) {
        $this->load->view($this->theme . 'index', $this->data);
    }
	
	function index() {
        $this->load->view($this->theme . 'index', $this->data);
    }
	
	function aboutus() {
        $this->load->view($this->theme . 'aboutus', $this->data);
    }
	function drivewithus() {
        $this->load->view($this->theme . 'drivewithus', $this->data);
    }
	function franchisee() {
        $this->load->view($this->theme . 'franchisee', $this->data);
    }
	function book_ride() {
        $this->load->view($this->theme . 'book_ride', $this->data);
    }
	function faq() {
        $this->load->view($this->theme . 'faq', $this->data);
    }
	function contact() {
        $this->load->view($this->theme . 'contact', $this->data);
    }
	function terms_conditions() {
        $this->load->view($this->theme . 'terms_conditions', $this->data);
    }	
	
	function ridecurl() {
		/*ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();*/
		//print_r($_POST);
		echo $_POST['value'];
        echo 'aaa';
		
    }
	
	function login() {
        admin_redirect('login');
    }

	function privacy_policy() {
        $this->load->view($this->theme . 'privacy_policy', $this->data);
    }
	
	function help_form() {
		
		$customer_id = $this->input->post('customer_id');
		
		foreach ($_POST as $name => $val)
		{
			 if($name != 'token' && $name != 'customer_type' && $name != 'customer_id' && $name != 'help_department' && $name != 'help_main_id' && $name != 'help_sub_id' && $name != 'ticket' && $name != 'ride_id'){
				$res[$name] = $val;
			 }
			 
			 if($name == 'customer_id'){
				$customer_id = $val;
			 }
			 if($name == 'help_department'){
				$help_department = $val;
			 }
			 if($name == 'help_sub_id'){
				$help_id = $val;
			 }
			 
		}
		foreach ($_FILES as $name1 => $val)
		{
			if ($_FILES[$name1]['size'] > 0) {
				$config['upload_path'] = $this->upload_path;
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload($name1)) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$files = $this->upload->file_name;
				$res[$name1] = $files;
				$config = NULL;
			}
		}
		
		$enquiry = array(
			'enquiry_code' => 'ENQ'.date('YmdHis'),
			'enquiry_date' => date('Y-m-d'),
			'enquiry_type' => 'APP',
			'customer_id' => $this->input->post('customer_id'),
			'services_id' => $this->input->post('ride_id'),
			'help_id' => $help_id,
			'help_message' => json_encode($res),
			'help_department' => $help_department,
		);
		
		$insert = $this->main_model->create_ticket($enquiry, $this->input->post('customer_id'), $help_department);
		
		if($insert == TRUE){
			site_redirect('success');
		}else{
			site_redirect('help');
		}
		
       // $this->load->view($this->theme . 'privacy_policy', $this->data);
    }
	
	function help() {
		if( $_GET['customer_type'] != 'undefined' && $_GET['customer_type'] != NULL && !empty($_GET['customer_type'])){
			$customer_type = $_GET['customer_type'];
		}else{
			$customer_type = '0';
		}
		
		if( $_GET['customer_id'] != 'undefined' && $_GET['customer_id'] != NULL && !empty($_GET['customer_id'])){
			$customer_id = $_GET['customer_id'];
		}else{
			$customer_id = '0';
		}
		
		if( $_GET['ride_id'] != 'undefined' && $_GET['ride_id'] != NULL && !empty($_GET['ride_id'])){
			$ride_id = $_GET['ride_id'];
		}else{
			$ride_id = '0';
		}
		
		if( $_GET['parent_id'] != 'undefined' && $_GET['parent_id'] != NULL && !empty($_GET['parent_id'])){
			$parent_id = $_GET['parent_id'];
		}else{
			$parent_id = '0';
		}
		$help_ids = $this->main_model->getIds($parent_id);
		
		//print_r($help_ids);
		
		$this->data['customer_type'] = $customer_type;
		$this->data['customer_id'] = $customer_id;
		$this->data['ride_id'] = $ride_id;
		$this->data['parent_id'] = $parent_id;
		$this->data['help_sub_id'] = $parent_id;
		
		$this->data['help_main_id'] = $help_ids->help_main_id;
		$this->data['help_id'] = $help_ids->help_id;
		
		$this->data['customer_details'] = $this->main_model->getCustomer($customer_id, $ride_id);
		$this->data['help'] = $this->main_model->getForms($parent_id);
		
        $this->load->view($this->theme . 'help', $this->data);
    }
	
	function success() {
		
		
        $this->load->view($this->theme . 'success', $this->data);
    }
	
    

}
