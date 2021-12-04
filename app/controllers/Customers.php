<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;


class Customers extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('customer_api');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->library('upload');
		$this->getUserIpAddr = $this->site->getUserIpAddr();
        //$this->upload_path = 'assets/uploads/customers/';
       // $this->thumbs_path = 'assets/uploads/customers/thumbs/';
        $this->image_types = 'gif|jpg|png|jpeg|pdf'; 
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->library('socketemitter');
		$this->load->library('aes');
		

		
		$owner_group = 'owner';
		$this->Owner = $this->site->getUserGroupIDbyname($owner_group);
				
		$vendor_group = 'vendor';
		$this->Vendor = $this->site->getUserGroupIDbyname($vendor_group);
		
		$driver_group = 'driver';
		$this->Driver = $this->site->getUserGroupIDbyname($driver_group);
		
		$employee_group = 'employee';
		$this->Employee = $this->site->getUserGroupIDbyname($employee_group);
		
		$customer_group = 'customer';
		$this->Customer = $this->site->getUserGroupIDbyname($customer_group);
		
		$admin_group = 'admin';
		$this->Admin = $this->site->getUserGroupIDbyname($admin_group);
		
		//$this->lang->load('common', 'english');
		
		//session_start();
		
	}
	
	public function customercancelmaster_get(){
		$countryCode = $this->input->get('is_country');
		$group_id = 5;
		$data = $this->site->getCancelMaster($group_id, $countryCode);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	function commonsetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		//$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			//$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->customer_api->getSettings($countryCode);
			
			$data = array(
				'dateofbirth' => $res->{'dateofbirth'},
				'no_of_waypoints' => $res->{'drop_points'}			
			);
			
			if($data){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Not Update');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	function offerscheck_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('offer_code', $this->lang->line("offer_code"), 'required');
		$this->form_validation->set_rules('estimate_fare', $this->lang->line("estimate_fare"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->customer_api->offerCheck($user_data->id, $this->input->post('offer_code'), $this->input->post('estimate_fare'), $countryCode);
			//print_r($res);
			//die;
			$data = $res;
			if($res->check == 1){				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);			
			}elseif($res->check == 2){
				$result = array( 'status'=> 0 , 'message'=> 'Your offer code limit '.$res->offer_limit.' complete, please change.');
			}elseif($res->check == 3){
				$result = array( 'status'=> 0 , 'message'=> 'Your offer code  amount ('.$res->minimum_amount.') less then  ('.$this->input->post('estimate_fare').') estimate amount, please change.');
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Your offer code deactive');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	function offers_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->customer_api->getOffers($user_data->id, $countryCode);
			
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	function addmoney_cashwallet_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('paid_amount', $this->lang->line("paid_amount"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$user_id = $user_data->id;
			
			$paid_amount = $this->input->post('paid_amount');
			
			
			$paid_amount = $this->input->post('paid_amount');
			$join_id = 0;
			$join_table = '';
			
			$razorpay_payment_id = $this->input->post('razorpay_payment_id');
			//$payment_transaction = $this->input->post('payment_transaction');
			$payment_gateway = $this->input->post('payment_gateway_id');
			$transaction_status = $this->input->post('transaction_status');
						
			$transaction_date = date('Y-m-d H:i:s');
			$is_country = $countryCode;
			$transaction_no = $razorpay_payment_id;
			$created_by = $user_data->id;
			$admin_user = $this->site->getAdminUser($is_country, 2);
			$payment_array = array(
				'transaction_no' => $transaction_no,
				'transaction_amount' => $paid_amount,
				'transaction_status' => $transaction_status,
				'transaction_date' => $transaction_date,
				'transaction_user' => $user_id,
				'payment_gateway' => $payment_gateway,
				'is_country' => $is_country
			);
			$payment_mode = 1;
			$company_id = $this->site->getUserCompany($is_country, 0);
			$company_bank_id = $this->site->onlineBank($is_country, $payment_gateway);
			$cash_array[] = array(
				'type' => 1,
				'payment_mode' => $payment_mode,
				'payment_type' => $payment_gateway,
				'credit' => $paid_amount,
				'debit' => '0.00',
				'account_date' => $transaction_date,
				'account_type' => 1,
				'company_id' => $company_id,
				'company_bank_id' => $company_bank_id,
				'account_status' => 3,
				'account_transaction_no' => $transaction_no,
				'account_transaction_date' => $transaction_date,
				'user_id' => $admin_user,
				'user_type' => 0,
				'account_verify' => 1,
				'account_verify_on' => $transaction_date,
				'account_verify_by' => $created_by,
				'created_on' => $transaction_date,
				'created_by' => $created_by,
				'is_country' => $is_country
			);
			$cash_array[] = array(
				'type' => 1,
				'payment_mode' => $payment_mode,
				'payment_type' => $payment_gateway,
				'credit' => '0.00',
				'debit' => $paid_amount,
				'account_date' => $transaction_date,
				'account_type' => 1,
				'company_id' => $company_id,
				'company_bank_id' => $company_bank_id,
				'account_status' =>  1,
				'account_transaction_no' => $transaction_no,
				'account_transaction_date' => $transaction_date,
				'user_id' => $admin_user,
				'user_type' => 0,
				'account_verify' => 0,
				'account_verify_on' => '',
				'account_verify_by' => 0,
				'created_on' => $transaction_date,
				'created_by' => $created_by,
				'is_country' => $is_country
			);
			
			$cash_array[] = array(
				'type' => 1,
				'payment_mode' => $payment_mode,
				'payment_type' => $payment_gateway,
				'credit' => $paid_amount,
				'debit' => '0.00',
				'account_date' => $transaction_date,
				'account_type' => 0,
				'company_id' => $company_id,
				'company_bank_id' => $company_bank_id,
				'account_status' => 3,
				'account_transaction_no' => $transaction_no,
				'account_transaction_date' => $transaction_date,
				'user_id' => $admin_user,
				'user_type' => 0,
				'account_verify' => 1,
				'account_verify_on' => $transaction_date,
				'account_verify_by' => $created_by,
				'created_on' => $transaction_date,
				'created_by' => $created_by,
				'is_country' => $is_country
			);
			
			$cash_array[] = array(
				'type' => 1,
				'payment_mode' => $payment_mode,
				'payment_type' => $payment_gateway,
				'credit' => '0.00',
				'debit' => $paid_amount,
				'account_date' => $transaction_date,
				'account_type' => 0,
				'company_id' => $company_id,
				'company_bank_id' => $company_bank_id,
				'account_status' => 3,
				'account_transaction_no' => $transaction_no,
				'account_transaction_date' => $transaction_date,
				'user_id' => $admin_user,
				'user_type' => 0,
				'account_verify' => 1,
				'account_verify_on' => $transaction_date,
				'account_verify_by' => $created_by,
				'created_on' => $transaction_date,
				'created_by' => $created_by,
				'is_country' => $is_country
			);
			
			$cash_array[] = array(
				'type' => 1,
				'payment_mode' => $payment_mode,
				'payment_type' => $payment_gateway,
				'credit' => $paid_amount,
				'debit' => '0.00',
				'account_date' => $transaction_date,
				'account_type' => 0,
				'company_id' => $company_id,
				'company_bank_id' => $company_bank_id,
				'account_status' => 3,
				'account_transaction_no' => $transaction_no,
				'account_transaction_date' => $transaction_date,
				'user_id' => $user_id,
				'user_type' => 1,
				'account_verify' => 1,
				'account_verify_on' => $transaction_date,
				'account_verify_by' => $created_by,
				'created_on' => $transaction_date,
				'created_by' => $created_by,
				'is_country' => $is_country
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'flag' => 6,
				'cash' => $paid_amount,
				'description' => 'Add Money - Backend',
				'created' => $transaction_date,
				'is_country' => $is_country
			);
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'flag' => 5,
				'cash' => $paid_amount,
				'description' => 'Transfer Money - Backend',
				'created' => $transaction_date,
				'is_country' => $is_country
			);
			$wallet_array[] = array(
				'user_id' =>  $user_id,
				'user_type' => 1,
				'wallet_type' => 1,
				'flag' => 6,
				'cash' => $paid_amount,
				'description' => 'Add Money - Backend',
				'created' => $transaction_date,
				'is_country' => $is_country
			);
			
			$group_id = 5;
		
			//print_r($payment_array);
			//die;
			$res = $this->customer_api->addMoneyOnlineAccount($group_id, $cash_array, $wallet_array, $payment_array, $transaction_status, $is_country);
			
			if($res == true){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function check_exist($string,$token){
		if(!empty($string)){
			$column=$token;
			$where = array(
			  $column => $string
			);
			
			$this->db->select('id');
			$this->db->from('users');
			$this->db->where($where);
			$this->db->where('group_id', 5);
			$num_results = $this->db->count_all_results();
			
	 
			if($num_results>0){
			  return true;
			}else{
				$this->form_validation->set_message('check_exist', 'The %s value is mismatch.');
			  return false;
			  
			}
		  }
	}
	
	function wallet_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			$wallet_type = 1;
			
			$res = $this->customer_api->getTypeWallets($user_data->id, $wallet_type, $countryCode);
			
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'wallet_total' => $data['wallet_cash'], 'data' => $data['cash_list']);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	function enquirylist_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->customer_api->getTickets($user_data->id, $countryCode);
			
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	function enquiryview_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('enquiry_id', $this->lang->line("enquiry_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->customer_api->getEnquiryView($user_data->id, $this->input->post('enquiry_id'), $countryCode);
			$follow = $this->customer_api->getEnquiryFollow($user_data->id, $this->input->post('enquiry_id'), $countryCode);
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data, 'follow' => $follow);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	function enquiryfeedback_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			
			$data  = array(
				'enquiry_id' => $this->input->post('enquiry_id'),
				'customer_id' => $user_data->id,
				'feedback_rating' => $this->input->post('feedback_rating') ? $this->input->post('feedback_rating') : 0,
				'feedback_msg' => $this->input->post('feedback_msg') ? $this->input->post('feedback_msg') : '',
				'created_on' => date('Y-m-d H:i:s')
			);
			$res = $this->customer_api->addenquiryFeedback($data, $this->input->post('enquiry_id'), $user_data->id, $countryCode);

			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'added');
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not added');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	/*New Changes*/
	
	public function ridebase_main_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->customer_api->getHelpmain($user_data->id, 'Ride based', $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Ridebase', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function general_main_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->customer_api->getHelpmain($user_data->id, 'Login', $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Ridebase', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function helpsub_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('parent_id', $this->lang->line("parent_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->customer_api->getHelpsub($user_data->id, $this->input->post('parent_id'), $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Sub', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function get_driver_location_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->customer_api->get_driver_location($user_data->id, $countryCode);
			if(!empty($res)){
				$result = array( 'status'=> 1 , 'message' => 'Success', 'data' => $res);
			}else{
				$result = array( 'status'=> 0 , 'message' => 'Empty');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
		
	}
	function calcCrow($lat1, $lon1, $lat2, $lon2){
			$R = 6371; // km
			$dLat = $this->toRad($lat2-$lat1);
			$dLon = $this->toRad($lon2-$lon1);
			$lat1 = $this->toRad($lat1);
			$lat2 = $this->toRad($lat2);
	
			$a = sin($dLat/2) * sin($dLat/2) +sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2); 
			$c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
			$d = $R * $c;
			return $d;
	}

	function toRad($Value) 
	{
		return $Value * pi() / 180;
	}
	function test_post(){
		$ride_id = 1644;
		$countryCode = 'IN';
		if($this->input->post('booking_cancel_type') == 1){
			$type_cancel = $this->site->CancelCabType($this->input->post('type_id'), $countryCode);
		}else{
			$type_cancel = $this->site->CancelCabBooking($this->input->post('booking_id'), $countryCode);
		}
		print_r($type_cancel);
		echo $type_cancel->customer_cancel_charge;
		//echo $waypoint_waiting_minutes = $this->site->waypointWaiting($ride_id, 1, 3);
		//echo $pickup_waiting_minutes = $this->site->pickupWaiting($ride_id, 1, 3);
		//echo $trafic_waiting = $this->site->traficWaiting($ride_id, 1, 3, 10);
		//echo '<br>';
		//echo $trafic_waiting_fare = abs(($pickup_waiting_minutes + $waypoint_waiting_minutes) - $trafic_waiting);
		
		//$waiting_charge = $pickup_waiting_minutes + $waypoint_waiting_minutes + $trafic_waiting_fare;
		die;
		
	}

	public function demo_post(){
		
			echo '@@@';
			$d  = array(11.6049461,79.4848087,2,"2020-8-25 18:46:49",11.6049461,79.4848087,2,"2020-8-25 18:46:49",11.6049152,79.4848322,2,"2020-8-25 18:46:53",11.605013,79.4848957,2,"2020-8-25 18:46:58",11.6051153,79.4849518,2,"2020-8-25 18:47:3",11.6051167,79.4849035,2,"2020-8-25 18:47:8",11.6050759,79.4848272,2,"2020-8-25 18:47:12",11.6050665,79.4847929,2,"2020-8-25 18:47:17",11.6050924,79.4847899,2,"2020-8-25 18:47:22",11.6051302,79.484787,2,"2020-8-25 18:47:29",11.6051114,79.4847759,2,"2020-8-25 18:47:32",11.6051238,79.4847647,2,"2020-8-25 18:47:37",11.6051352,79.4847814,2,"2020-8-25 18:47:42",11.6051191,79.4847825,2,"2020-8-25 18:47:47",11.6050908,79.4847801,2,"2020-8-25 18:47:52",11.6050806,79.4847966,2,"2020-8-25 18:47:57",11.6051014,79.4847909,2,"2020-8-25 18:48:3",11.605125,79.4847824,2,"2020-8-25 18:48:7",11.6051355,79.4847847,3,"2020-8-25 18:48:12",11.6051355,79.4847847,3,"2020-8-25 18:48:13",11.6051355,79.4847847,3,"2020-8-25 18:48:13",11.6051569,79.484809,3,"2020-8-25 18:48:17",11.605163,79.484913,3,"2020-8-25 18:48:23",11.6051512,79.4850127,3,"2020-8-25 18:48:29",11.6051817,79.4850219,3,"2020-8-25 18:48:34",11.6051888,79.4850874,3,"2020-8-25 18:48:39",11.604812,79.4853181,3,"2020-8-25 18:48:44",11.6049857,79.48532,3,"2020-8-25 18:48:48",11.6052452,79.4852788,3,"2020-8-25 18:48:54",11.605701,79.4851725,3,"2020-8-25 18:49:1",11.6059201,79.4850993,3,"2020-8-25 18:49:5",11.6060287,79.4849909,3,"2020-8-25 18:49:9",11.6062375,79.4842346,3,"2020-8-25 18:49:22",11.6060413,79.4838518,3,"2020-8-25 18:49:27",11.6061166,79.4830493,3,"2020-8-25 18:49:32",11.6061089,79.4825144,3,"2020-8-25 18:49:41",11.6060625,79.4824891,3,"2020-8-25 18:49:45",11.6058052,79.4823933,3,"2020-8-25 18:49:49",11.6054585,79.4823342,3,"2020-8-25 18:49:54",11.605023,79.4823272,3,"2020-8-25 18:49:59",11.6045547,79.4823275,3,"2020-8-25 18:50:4",11.6039888,79.4823525,3,"2020-8-25 18:50:9",11.6035332,79.4823586,3,"2020-8-25 18:50:14",11.6031241,79.4823696,3,"2020-8-25 18:50:20",11.6028231,79.4823885,3,"2020-8-25 18:50:25",11.6022524,79.4824013,3,"2020-8-25 18:50:32",11.6017488,79.4824022,3,"2020-8-25 18:50:38",11.6014887,79.4823981,3,"2020-8-25 18:50:42",11.6011251,79.4823283,3,"2020-8-25 18:50:46",11.6006034,79.4823238,3,"2020-8-25 18:50:52",11.6001824,79.4823569,3,"2020-8-25 18:50:56",11.5998127,79.4822965,3,"2020-8-25 18:51:1",11.599707,79.4823263,3,"2020-8-25 18:51:6",11.599702,79.4823571,3,"2020-8-25 18:51:11",11.5997795,79.4823103,3,"2020-8-25 18:51:16",11.5998005,79.4822968,3,"2020-8-25 18:51:21",11.5997943,79.48231,3,"2020-8-25 18:51:26",11.599811,79.4823105,3,"2020-8-25 18:51:31",11.5998201,79.4823292,3,"2020-8-25 18:51:36",11.5997978,79.4823695,3,"2020-8-25 18:51:41",11.5997508,79.482388,3,"2020-8-25 18:51:46",11.5997258,79.4823833,3,"2020-8-25 18:51:51",11.599716,79.4823777,3,"2020-8-25 18:51:55",11.5997144,79.4823737,3,"2020-8-25 18:52:1",11.5997169,79.4823742,3,"2020-8-25 18:52:6",11.599712,79.4823692,3,"2020-8-25 18:52:11",11.5997136,79.4823652,3,"2020-8-25 18:52:16",11.5997155,79.4823644,3,"2020-8-25 18:52:21",11.5997148,79.4823651,3,"2020-8-25 18:52:26",11.5997143,79.4823653,3,"2020-8-25 18:52:31",11.5997132,79.4823655,3,"2020-8-25 18:52:36",11.599653,79.4823679,3,"2020-8-25 18:52:41",11.5994801,79.4823135,3,"2020-8-25 18:52:45",11.5992647,79.4819983,3,"2020-8-25 18:52:50",11.5990471,79.4815923,3,"2020-8-25 18:52:56",11.5987721,79.4812372,3,"2020-8-25 18:53:1",11.5983209,79.4808077,3,"2020-8-25 18:53:6",11.5978946,79.4803702,3,"2020-8-25 18:53:11",11.5974665,79.479891,3,"2020-8-25 18:53:16",11.5970409,79.4794213,3,"2020-8-25 18:53:22",11.5968491,79.4791892,3,"2020-8-25 18:53:26",11.5966328,79.4789632,3,"2020-8-25 18:53:31",11.5964007,79.4787751,3,"2020-8-25 18:53:36",11.596199,79.4785908,3,"2020-8-25 18:53:41",11.5959849,79.478543,3,"2020-8-25 18:53:46",11.5958527,79.4785208,3,"2020-8-25 18:53:51",11.5958781,79.4785215,9,"2020-8-25 18:53:57");
			
			$count=1;
			
			foreach ($d as $k => $v) {
				if ($count%4 == 1) {
					$d1[] = $v;
				}elseif ($count%4 == 2) {
					$d2[] = $v;
				}elseif ($count%4 == 3) {
					$d3[] = $v;
				}else{
					$d4[] = $v;
				}
				$count++;
			}
			
			//echo count($d1);
			//print_r($d1);
			//die;
			
			for($i=0; $i<count($d1); $i++){
				if($d3[$i] == 3 || $d3[$i] == 9){
					if(!empty($d1[$i+1]) && $d2[$i+1]){
						$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i], 'start_time' => $d4[$i], 'end_time' => $d4[$i+1]);			
					}
				}
			}
			//echo json_encode($result);
			//die;
			foreach($result as $res){			
				$distance[] = array('meter' => round($this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']) * 1000), 'second' => strtotime($res['end_time']) - strtotime($res['start_time']));	
				
			}
			$second = 0;
			foreach($distance as $value){
				
				if($value['meter'] <= 10){
					$second += $value['second'];
				}
			}
			echo $second;
			print_r($distance);
		
	}
	
	public function toll_parking_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		$this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
		$this->form_validation->set_rules('status', $this->lang->line("status"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			
			$data = $this->customer_api->insertToolparking($user_data->id, $this->input->post('ride_id'), $this->input->post('type'), $this->input->post('status'), $countryCode);
			
			if($data != 0){
				if($data == 2){
					if($this->lang->line("type") == 1){
						$type_name = 'Tool';
					}elseif($this->lang->line("type") == 2){
						$type_name = 'Parking';
					}
					$result = array( 'status'=> 0 , 'message'=> ''.$type_name.' Already insert ');
				}else{
					$result = array( 'status'=> 1 , 'message'=> 'Inserted success');
				}															
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function ridedetails_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			
			$res = $this->customer_api->getRidedetailsNEW($user_data->id, $this->input->post('ride_id'), $countryCode);
			
			if(!empty($res)){
				$data[] = $res;
				$result = array( 'status'=> 1 , 'message'=> 'Data', 'data' =>  $data);
																			
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
		
	public function outstationpackagetype_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('from_lat', $this->lang->line("from_lat"), 'required');
		$this->form_validation->set_rules('from_lng', $this->lang->line("from_lng"), 'required');
		
		$this->form_validation->set_rules('to_lat', $this->lang->line("to_lat"), 'required');
		$this->form_validation->set_rules('to_lng', $this->lang->line("to_lng"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$from_city = $this->site->getCityFare($this->input->post('from_lat'), $this->input->post('from_lng'));
			$to_city = $this->site->getCityFare($this->input->post('to_lat'), $this->input->post('to_lng'));
			
			
			$setting = $this->customer_api->getSettingmode($countryCode);
			
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('from_lat'), $this->input->post('from_lng'), $this->input->post('to_lat'), $this->input->post('to_lng'), $unit = 'km', $decimals = 2,  $countryCode);
			
			if($setting[0]->outstation_min_kilometer <= round($check_distance)){
				$data = $this->customer_api->getOutstationPackagetype($from_city, $to_city, $countryCode);
				if(!empty($data)){
					$result = array( 'status'=> 1 , 'message'=> 'Profile', 'fixed' => $data['fixed'], 'variable' => $data['variable']);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
				}
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'outstation must cover minimum '.$setting[0]->outstation_min_kilometer.'kms');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	
	public function rentalpackage_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('from_lat', $this->lang->line("from_lat"), 'required');
		$this->form_validation->set_rules('from_lng', $this->lang->line("from_lng"), 'required');

		if ($this->form_validation->run() == true) {
			
			$city = $this->site->getCityFare($this->input->post('from_lat'), $this->input->post('from_lng'));
			
			$data = $this->customer_api->getRentalPackage($city, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function rentalpackagetype_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('from_lat', $this->lang->line("from_lat"), 'required');
		$this->form_validation->set_rules('from_lng', $this->lang->line("from_lng"), 'required');
		$this->form_validation->set_rules('package_name', $this->lang->line("package_name"), 'required');
		if ($this->form_validation->run() == true) {
			
			$city = $this->site->getCityFare($this->input->post('from_lat'), $this->input->post('from_lng'));
			$package_name = $this->input->post('package_name');
			
			$data = $this->customer_api->getRentalPackagetype($city, $package_name, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function setting_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->customer_api->getSettingmode();
		
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		$this->response($result);
	}
	
	public function my_credit_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '1'; //1 - Credit, 2- Debit
			$data = $this->customer_api->myprofilebank($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_debit_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->customer_api->myprofilebank($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function add_debit_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		//$this->form_validation->set_rules('post_secure', $this->lang->line("post_secure"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			//$value = file_get_contents('php://input');
			//$enc_value = $this->aes->enc($value, ENCRYPT_API_KEY);
			//$dec_value = $this->aes->dec($value, ENCRYPT_API_KEY);
			//$debit = [];
			//mb_parse_str($dec_value, $debit);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			
			//$post_secure = $this->aes->dec($this->input->post('post_secure'), ENCRYPT_API_KEY);
			//if(POST_SECURE == $post_secure){
				$insert = array(
					'is_debit' => 1,
					'debit_name' => $this->input->post('debit_name'),
					'debit_number' => $this->input->post('debit_number'),
					'debit_month' => $this->input->post('debit_month'),
					'debit_year' => $this->input->post('debit_year'),
					//'debit_name' => $this->aes->enc($debit['debit_name'], ENCRYPT_API_KEY),
					//'debit_number' => $this->aes->enc($debit['debit_number'], ENCRYPT_API_KEY),
					//'debit_month' => $this->aes->enc($debit['debit_month'], ENCRYPT_API_KEY),
					//'debit_year' => $this->aes->enc($debit['debit_year'], ENCRYPT_API_KEY),
				);
				$data = $this->customer_api->insertbank($user_data->id, $this->Customer, $customer_type, $insert, $countryCode);
				
				if(!empty($data)){
					$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
				}
			//}else{
				//$result = array( 'status'=> 0 , 'message'=> 'Your oauth key is in vaild.');
			//}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function add_credit_post(){
		// $s = $this->aes->enc('hello', POST_SECURE);
		
		//echo $b = $this->aes->dec($s, POST_SECURE);
		//die;
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		//$this->form_validation->set_rules('post_secure', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$value = file_get_contents('php://input');
			//$enc_value = $this->aes->enc($value, ENCRYPT_API_KEY);
			//$dec_value = $this->aes->dec($value, ENCRYPT_API_KEY);
			//$credit = [];
			//mb_parse_str($dec_value, $credit);
			
			$customer_type = '1'; //1 - Credit, 2- Debit
			
			//$post_secure = $this->aes->dec($this->input->post('post_secure'), ENCRYPT_API_KEY);
			//if(POST_SECURE == $post_secure){
				$insert = array(
					'is_credit' => 1,
				    'credit_name' => $this->input->post('credit_name'),
					'credit_number' => $this->input->post('credit_number'),
					'credit_month' => $this->input->post('credit_month'),
					'credit_year' => $this->input->post('credit_year'),
					//'credit_name' => $this->aes->enc($debit['credit_name'], ENCRYPT_API_KEY),
					//'credit_number' => $this->aes->enc($debit['credit_number'], ENCRYPT_API_KEY),
					//'credit_month' => $this->aes->enc($debit['credit_month'], ENCRYPT_API_KEY),
					//'credit_year' => $this->aes->enc($debit['credit_year'], ENCRYPT_API_KEY),
				);
				$data = $this->customer_api->insertbank($user_data->id, $this->Customer, $customer_type, $insert, $countryCode);
				
				if(!empty($data)){
					$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
				}
			//}else{
				//$result = array( 'status'=> 0 , 'message'=> 'Your oauth key is in vaild.');
			//}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function payment_mode_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		$types = $this->customer_api->getPaymentmode();
		
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $types);
		$this->response($result);
	}
	
	
	public function multiplerating_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$overall = ((($this->input->post('booking_process_star') + $this->input->post('cab_cleanliness_star') + $this->input->post('drive_comfort_star') + $this->input->post('drive_politeness_star') + $this->input->post('fare_star') + $this->input->post('easy_of_payment_star')) / 30) * 5);
			
			$overall = (($this->input->post('booking_process_star') + $this->input->post('cab_cleanliness_star') + $this->input->post('drive_comfort_star') + $this->input->post('drive_politeness_star') + $this->input->post('fare_star') + $this->input->post('easy_of_payment_star')) / 6);
			
			$t = $this->customer_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			$rate = array(
				'user_id' => $user_data->id,
				'driver_id' => $t->driver_id,
				'booking_id' => $this->input->post('booking_id'),
				'booking_process_star' => $this->input->post('booking_process_star'),
				'cab_cleanliness_star' => $this->input->post('cab_cleanliness_star'),
				'drive_comfort_star' => $this->input->post('drive_comfort_star'),
				'drive_politeness_star' => $this->input->post('drive_politeness_star'),
				'fare_star' => $this->input->post('fare_star'),
				'easy_of_payment_star' => $this->input->post('easy_of_payment_star'),
				'feedback' => $this->input->post('feedback') ? $this->input->post('feedback') : '',
				'overall' => $overall
			);
			
			
			
			$data = $this->customer_api->multipleRatingadd($rate, $countryCode);
			
			if($data == true){
				if($user_data->id != 2){
					$notification1['title'] = 'Ride Rating';
					$notification1['message'] = $user_data->first_name.' has been ride rating.';
					$notification1['user_type'] = 2;
					$notification1['user_id'] = $t->driver_id;
					$this->customer_api->insertNotification($notification1, $countryCode);
				}
				$notification['title'] = 'Ride Rating';
				$notification['message'] = $user_data->first_name.' has been ride rating.';
				$notification['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->customer_api->insertNotification($notification, $countryCode);
				$result = array( 'status'=> true , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'customer rating not added');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function fcmregister_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('device_imei', $this->lang->line("device_imei"), 'required');
		$this->form_validation->set_rules('device_token', $this->lang->line("device_token"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$device['device_imei'] = $this->input->post('device_imei');
			$device['device_token'] = $this->input->post('device_token');
			$device['devices_type'] = $this->input->post('devices_type');
			$device['user_id'] = $this->input->post('user_id') ? $this->input->post('user_id') : 0;
			$device['user_type'] = $this->input->post('user_type') ? $this->input->post('user_type') : 0;
			
			$data = $this->customer_api->fcminsert($device, $countryCode);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Data is empty');
			}
		}else{
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		
		$this->response($result);
	}
	
	
	public function remove_token_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('device_imei', $this->lang->line("device_imei"), 'required');
		$this->form_validation->set_rules('device_token', $this->lang->line("device_token"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$device['device_imei'] = $this->input->post('device_imei');
			$device['devices_type'] = $this->input->post('devices_type');

			$data = $this->customer_api->fcmdelete($device, $countryCode);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Data is empty');
			}
		}else{
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		
		$this->response($result);
	}
	
	function base64ToImage($imageData,$filepath){
		$data = 'data:image/png;base64,AAAFBfj42Pj4';
		list($type, $imageData) = explode(';', $imageData);
		list(,$extension) = explode('/',$type);
		list(,$imageData)      = explode(',', $imageData);
		//$fileName = uniqid().'.'.$extension;
		$imageData = base64_decode($imageData);
		$filename = md5(uniqid(mt_rand())).'.'.$type;
		$file = $filepath.$filename;
		file_put_contents($file, $imageData);
	}
	
	public function login_post(){
		$token = get_random_key(32,'users','oauth_token',$type='alnum');
		$otp = random_string('numeric', 6);
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required|numeric');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');	
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');	
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$login['country_code'] = $this->input->post('country_code');
			$login['mobile'] = $this->input->post('mobile');
			$login['devices_imei'] = $this->input->post('devices_imei');
			$login['password'] = md5($this->input->post('password'));
			$login['otp'] = $otp;
			$countryCode = $this->input->post('is_country');
			$res = $this->customer_api->check_login($login, $countryCode);
			
			$data[]  = $res;
			if($res->check_status == 1){
				/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
					$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
				}else{*/
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				//}
			}elseif($res->check_status == 3){
				/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
					$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
				}else{*/
					if($res->devices_imei != $this->input->post('devices_imei')){
						$socket_id = $this->site->getSocketID($res->id, 1, $countryCode);
						
						$event = 'server_other_login';
						
						$edata = array(
							'socket_id' => $socket_id,
							'devices_imei' => $res->devices_imei,
							'msg' => 'Truncate'
						);
						$emit_login = $this->socketemitter->setEmit($event, $edata);
					
					}
					
					$sms_phone_otp = $otp;
					$sms_phone = $res->country_code.$res->mobile;
					$sms_country_code = $res->country_code;
					
					$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
					if($response_sms){
						$result = array( 'status'=> 3 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
					}else{
						$result = array( 'status'=> 3 , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $res->oauth_token);
					}
				//}
			}elseif($res->check_status == 4){
				/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
					$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
				}else{*/
					if($res->devices_imei != $this->input->post('devices_imei')){
						$socket_id = $this->site->getSocketID($res->id, 1, $countryCode);
						
						$event = 'server_other_login';
						
						$edata = array(
							'socket_id' => $socket_id,
							'devices_imei' => $res->devices_imei,
							'msg' => 'Truncate'
						);
						$emit_login = $this->socketemitter->setEmit($event, $edata);
					
					}
					
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				//}
			}elseif($res->check_status == 2){
				$result = array( 'status'=> 0 , 'message'=> 'Your account has been deactive. please contact admin.');
			}elseif($res->check_status == 0){
				$result = array( 'status'=> 0 , 'message'=> 'Invalid Password');
			}elseif($res->check_status == 5){
				$result = array( 'status'=> 0 , 'message'=> 'Your account not added. please singup...');
			}
			
		}else{
			
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		
		$this->response($result);
		
	}
	
	
	
	public function add_emergency_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('phone1', $this->lang->line("emergency_contact"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['phone1'] = $this->input->post('phone1') ? $this->input->post('phone1') : '';
			$row['phone2'] = $this->input->post('phone2') ? $this->input->post('phone2') : '';
			$row['phone3'] = $this->input->post('phone3') ? $this->input->post('phone3') : '';
			$row['phone4'] = $this->input->post('phone4') ? $this->input->post('phone4') : '';
			$row['phone5'] = $this->input->post('phone5') ? $this->input->post('phone5') : '';
			$row['country_code1'] = $this->input->post('country_code1') ? $this->input->post('country_code1') : '';
			$row['country_code2'] = $this->input->post('country_code2') ? $this->input->post('country_code2') : '';
			$row['country_code3'] = $this->input->post('country_code3') ? $this->input->post('country_code3') : '';
			$row['country_code4'] = $this->input->post('country_code4') ? $this->input->post('country_code4') : '';
			$row['country_code5'] = $this->input->post('country_code5') ? $this->input->post('country_code5') : '';
			$row['user_id'] = $user_data->id;
			$res = $this->customer_api->insertemergency($row, $countryCode);
			
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Inserted!');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Not Insert');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function sosdata_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->customer_api->getEmergencydata($user_data->id, $countryCode);
			
			
			$data[] = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function sos_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			$row['booking_id'] = $this->input->post('booking_id');
			$res = $this->customer_api->getEmergencycontact($row, $countryCode);
			
			$current_ride = $this->customer_api->currentRideSOS($row, $countryCode);
			
			if($res){
				foreach($res as $em_res){
					$pickup = $this->site->findLocationWEB($current_ride->start_lat, $current_ride->start_lng);
					$dropoff = $this->site->findLocationWEB($current_ride->end_lat, $current_ride->end_lng);
					$driverlocation = $this->site->findLocationWEB($current_ride->current_latitude, $current_ride->current_longitude);
					$msg = 'Customer Details : '.$current_ride->customer_name.' Driver Details : '.$current_ride->driver_name.', Driver Location : '.$driverlocation.', Taxi Details : '.$current_ride->taxi_name.', '.$current_ride->taxi_number.', Pickup: '.$pickup.' Dropoff: '.$dropoff.' Click Link : http://heyycab.com/sos?id='.$current_ride->booking;
					
					$sms_phone = $em_res[1].$em_res[0];
					$sms_country_code = $em_res[1];
					$response_sms = $this->sms_sos($msg, $sms_phone, $sms_country_code);
				}
				$result = array( 'status'=> 1 , 'message'=> 'Send Success');
			}else{
				$result = array( 'status'=> 2 , 'message'=> 'Not Added Emergency');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	
	public function verify_changeotp_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['devices_imei'] = $this->input->post('devices_imei');
			$row['customer_id'] = $user_data->id;
			$res = $this->customer_api->devicescheckotp($row, $countryCode);
			$data[] = $res;
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'invaild otp. please check otp');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function verify_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['otp'] = $this->input->post('otp');
			$row['customer_id'] = $user_data->id;
			$res = $this->customer_api->checkotp($row, $countryCode);
			$data[] = $res;
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'invaild otp. please check otp');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function register_resend_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['country_code'] = $this->input->post('country_code');
			$row['mobile'] = $this->input->post('mobile');
			//$mobile_otp = random_string('numeric', 6);
			
			$data = $this->customer_api->registerresendotp($row, $countryCode);
			if($data){
				
				$sms_phone = $this->input->post('country_code') . $this->input->post('mobile');
				$sms_country_code = $this->input->post('country_code');
				$sms_phone_otp = $data->mobile_otp;

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success');
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Invaild mobile and country code');
			}
		} else {
			
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function resend_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['customer_id'] = $user_data->id;
			//$row['mobile_otp'] = random_string('numeric', 6);
			
			$data = $this->customer_api->resendotp($row, $countryCode);
			if($data){
				
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				$sms_phone_otp = $data->mobile_otp;

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success');
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'invaild otp. please check otp');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function forgot_post(){
		$data = array();
		
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		if ($this->form_validation->run() == true) {
			
			$row['mobile'] = $this->input->post('mobile');
			$row['forgot_otp'] = random_string('numeric', 6);
			$countryCode = $this->input->post('is_country');
			
			$data = $this->customer_api->forgototp($row, $countryCode);
			if($data){
				
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				$sms_phone_otp = $row['forgot_otp'];

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'Account does not exist.');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function forgotverify_otp_post(){
		$data = array();
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			$row['forgot_otp'] = $this->input->post('otp');
			$row['customer_id'] = $this->input->post('customer_id');
			$data = $this->customer_api->forgotcheckotp($row, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $this->input->post('customer_id'));
			}else{
				$result = array( 'status'=> false , 'message'=> 'invaild otp. please check otp');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function forgotresend_otp_post(){
		$data = array();
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['customer_id'] = $this->input->post('customer_id');
			//$row['forgot_otp'] = random_string('numeric', 6);
			
			$data = $this->customer_api->forgotresendotp($row, $countryCode);
			if($data){
				
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				$sms_phone_otp = $data->forgot_otp;

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'invaild otp. please check otp');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function resetpassword_post(){
		$data = array();
		$this->form_validation->set_rules('customer_id', $this->lang->line("customer_id"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		if ($this->form_validation->run() == true) {
			
			$customer['password'] = md5($this->input->post('password'));
			$customer['customer_id'] = $this->input->post('customer_id');
			$customer['text_password'] = $this->input->post('password');
			
			$data = $this->customer_api->updatepassword($customer, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Not update password');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}    
	
	public function register_post(){
		$current_date = date('Y-m-d');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->input->post('mobile_verify');
		if ($this->form_validation->run() == true) {
			$countryCode = $this->input->post('is_country');
			$oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		    $mobile_otp = random_string('numeric', 6);
			$check_mobile = $this->customer_api->checkMobile($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			
			$setting = $this->customer_api->getSettings($countryCode);
			
			$refer_code = $this->site->refercode('C', $countryCode); 
			if($setting->register_otp_enable == 0){
				if($this->input->post('mobile_verify') == 0 && $check_mobile == 1){
					$mobile_verify = $this->input->post('mobile_verify');
				}else{
					$mobile_verify = 1;
				}
			}else{
				$mobile_verify = $this->input->post('mobile_verify');	
			}
			
			if($mobile_verify == 0 && $check_mobile == 1){
				$result = array( 'status'=> 0 , 'message'=> 'Mobile number already exit!');	
			}elseif($mobile_verify == 0 && $check_mobile == 0){
				$sms_phone_otp = $mobile_otp;
				$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
				$sms_country_code = $this->input->post('country_code');
				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> 2 , 'message'=> 'OTP has been sent. Check it', 'data' => $mobile_otp);
				}else{
					$result = array( 'status'=> 2 , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $mobile_otp);
				}
			}elseif($mobile_verify == 1 && $check_mobile == 0){
				
				if(!empty($this->input->post('reference_no'))){
					$check_reference = $this->customer_api->checkCode($this->input->post('reference_no'), 'C');
					
					if($check_reference == 0){
						$result = array( 'status'=> 0 , 'message'=> 'Refer code is invaild');	
						$this->response($result);exit;
					}elseif($check_reference->code_end < $current_date){
						$result = array( 'status'=> 0 , 'message'=> 'Refer code is expiry.');	
						$this->response($result);exit;
					}
				}
				
				if($this->input->post('dob') != NULL){
					$dob = $this->input->post('dob');
				}else{
					$dob = '0000-00-00';
				}
				
				$customer['country_code'] = $this->input->post('country_code');
				$customer['mobile'] = $this->input->post('mobile');
				$customer['password'] = md5($this->input->post('password'));
				$customer['text_password'] = $this->input->post('password');
				$customer['group_id'] = $this->Customer;
				$customer['devices_imei'] = $this->input->post('devices_imei');
				$customer['oauth_token'] = $oauth_token;
				$customer['mobile_otp'] = $mobile_otp;
				$customer['refer_code'] = $refer_code;
				$customer['reference_no'] = $this->input->post('reference_no') != NULL ? $this->input->post('reference_no') : '';
				$customer['first_name'] = $this->input->post('first_name');
				$customer['last_name'] = $this->input->post('last_name') ? $this->input->post('last_name') : '';
				$customer['dob'] = $dob;
				$customer['is_edit'] = 1;
				$customer['is_approved'] = 1;
				$customer['active'] = 1;
				
				
				$customer['created_on'] = date('y-m-d H:i:s');
				$data = $this->customer_api->add_customer($customer, $countryCode, $refer_code, $reference_no);
				if(!empty($data)){
					
					$notification['title'] = 'Customer Register';
					$notification['message'] = 'New user('.$this->input->post('first_name').') has been register heyycab.';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification, $countryCode);
					
					$sms_message = $this->input->post('first_name').' your account has been register successfully. Your refer code : '.$refer_code.'';
					$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
					$sms_country_code = $this->input->post('country_code');

					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
					$result = array( 'status'=> 1 , 'message'=> 'Registered Successfully!..', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Your details not register. please try again');
				}
			}
		}else{
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);		
	}
	
	
	public function social_login_post(){
		$token = get_random_key(32,'users','oauth_token',$type='alnum');
		$otp = random_string('numeric', 6);
		$this->form_validation->set_rules('login_key', $this->lang->line("login_key"), 'required');	
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');	
		if ($this->form_validation->run() == true) {
			$login['login_key'] = $this->input->post('login_key');
			$login['devices_imei'] = $this->input->post('devices_imei');
			$login['otp'] = $otp;
			$countryCode = $this->input->post('is_country');
			$res = $this->customer_api->social_check_login($login, $countryCode);
			$data[]  = $res;
			if($res->check_status == 1){
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}elseif($res->check_status == 3){
				$sms_phone_otp = $otp;
				$sms_phone = $res->country_code.$res->mobile;
				$sms_country_code = $res->country_code;
				
				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> 3 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
				}else{
					$result = array( 'status'=> 3 , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $res->oauth_token);
				}
			}elseif($res->check_status == 2){
				$result = array( 'status'=> 0 , 'message'=> 'Your account has been deactive. please contact admin.');
			}elseif($res->check_status == 0){
				$result = array( 'status'=> 0 , 'message'=> 'Invalid credentials');
			}
			
		}else{
			
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		
		$this->response($result);
		
	}
	
	public function social_register_post(){
		
		
		//$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		//$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		$this->form_validation->set_rules('login_key', $this->lang->line("login_key"), 'required');
		
		$this->input->post('mobile_verify');
		$countryCode = $this->input->post('is_country');
		if ($this->form_validation->run() == true) {
			$login_type = $this->input->post('login_type');
			
			$oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		    $mobile_otp = random_string('numeric', 6);
			
			if($this->input->post('mobile_verify') == 1){
				$customer['country_code'] = $this->input->post('country_code');
				$customer['mobile'] = $this->input->post('mobile');
				$customer['login_key'] = $this->input->post('login_key');
				$customer['login_type'] = $this->input->post('login_type');
				
				//$customer['password'] = md5($this->input->post('password'));
				//$customer['text_password'] = $this->input->post('password');
				$customer['group_id'] = $this->Customer;
				$customer['devices_imei'] = $this->input->post('devices_imei');
				$customer['oauth_token'] = $oauth_token;
				$customer['mobile_otp'] = $mobile_otp;
				
				$customer['first_name'] = $this->input->post('first_name');
				$customer['is_edit'] = 1;
				$customer['is_approved'] = 1;
				$customer['active'] = 1;
				
				$customer['created_on'] = date('y-m-d H:i:s');
				$data = $this->customer_api->add_customer($customer, $countryCode);
				if(!empty($data)){
					$result = array( 'status'=> 1 , 'message'=> 'Registered Successfully!..', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Your details not register. please try again');
				}
			
			}else{
				$check_mobile = $this->customer_api->checkMobile($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
				if($this->input->post('mobile_verify') == 0 && $check_mobile == 1){
					$result = array( 'status'=> 0 , 'message'=> 'Mobile number already exit!');	
				}elseif($this->input->post('mobile_verify') == 0 && $check_mobile == 0){
					$sms_phone_otp = $mobile_otp;
					$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
					$sms_country_code = $this->input->post('country_code');
					$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
					if($response_sms){
						$result = array( 'status'=> 2 , 'message'=> 'OTP has been sent. Check it', 'data' => $mobile_otp);
					}else{
						$result = array( 'status'=> 2 , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $mobile_otp);
					}
				}
			}
			
		}else{
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);		
	}
	
	
	public function edit_customer_photo_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			if ($_FILES['photo']['size'] > 0) {
			
				$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
				$user_id = $user_data->id;
				$res = $this->customer_api->getUserEdit($user_data->id);
				
				if ($_FILES['photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'user/customer/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$photo = $this->upload->file_name;
					$user_profile['photo'] = 'user/customer/'.$photo;
					$user['photo'] = 'user/customer/'.$photo;
					$config = NULL;
				}else{
					$user_profile['photo'] = $res->photo;
					$user['photo'] = $res->photo;
				}
				
				$data = $this->customer_api->edit_customer_photo($user_id, $user, $user_profile, $countryCode);
					
				if($data){
					$notification['title'] = 'Customer Photo Change';
					$notification['message'] = $user_data->first_name.' has been photo edited.';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification, $countryCode);
					
					$result = array( 'status'=> 1, 'message' => 'Image upload success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Error uploading image');
				}
			
			}else{
				$result = array( 'status'=> 0, 'message' => 'No image to upload.');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);	
			
	}
	
	public function deactive_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		
		if ($this->form_validation->run() == true) {
			
			   $user = array(
					'active' => 1,
			   );
		   
				$data = $this->customer_api->deactive_customer($user_id, $user, $countryCode);
				
				if($data){
					$result = array( 'status'=> 1, 'message' => 'customer deactive has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'customer  does not deactive.');
				}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function edit_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->customer_api->getUserEdit($user_data->id);
		
		
		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('email')) || !empty($this->input->post('first_name')) || !empty($this->input->post('last_name')) || !empty($this->input->post('gender'))  ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender') ){
				$profile_is_approved = $res->profile_is_approved;
				$profile_approved_by = $res->profile_approved_by;
				$profile_approved_on = $res->profile_approved_on;
			}else{
				$profile_is_approved = 0;
				$profile_approved_on = '0000-00-00 00:00:00';
				$profile_approved_by = 0;
			}
			if($this->input->post('dob') != NULL){
				$dob = $this->input->post('dob');
			}else{
				$dob = '0000-00-00';
			}
			
		   $user = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $dob,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
				'is_edit' => 1
			
		   );
		   
		  
				$data = $this->customer_api->edit_customer($user_id, $user, $user_profile, $countryCode);
				
				if($data){
					$notification['title'] = 'Customer Profile Edit';
					$notification['message'] = $user_data->first_name.' has been profile edited.';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification, $countryCode);
					
					$result = array( 'status'=> 1, 'message' => 'customer edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'customer does not edit.');
				}
				
			}else{
				$result = array( 'status'=> 0 , 'message' => 'Your data has not edit.');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function my_account_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '1'; //1 - basic details, 2- bank details, 3- document
			$data = $this->customer_api->myprofile($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_bank_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - basic details, 2- bank details, 3- document
			$data = $this->customer_api->myprofile($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_document_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '3'; //1 - basic details, 2- bank details, 3- document
			$data = $this->customer_api->myprofile($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$customer_type = '1'; //1 - basic details, 2- bank details, 3- document
			$data = $this->customer_api->myprofile($user_data->id, $this->Customer, $customer_type, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Profile', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
		
	public function sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "user-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_ride_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "ride-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_sos($msg, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MSG]');
        $sms_rep_arr = array($msg);
        $response_sms = send_transaction_sms($sms_template_slug = "sos", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_booking_active($customer_name, $driver_name, $driver_phone, $taxi_number, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[CUSTOMERNAME]', '[DRIVERNAME]', '[DRIVERNUMBER]', '[CABNUMBER]');
        $sms_rep_arr = array($customer_name, $driver_name, $driver_phone, $taxi_number);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-booking-confirmation", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }

	
	
	public function cab_categorytypes_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		$types = $this->customer_api->getCategorytypes();
		
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $types);
		$this->response($result);
	}
	
	
	
	public function cab_typesnew_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$search_data = $this->site->insertSearch($this->input->post('latitude'), $this->input->post('longitude'), $countryCode);
			
			$settings = $this->customer_api->getSettings($countryCode);
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$outstanding_val = $this->customer_api->outstanding_price($user_data->id, $countryCode);
			$outstanding_price = $outstanding_val != null ? $outstanding_val : '0.00';
			
			$distance = $settings->search_kilometer;
			//$distance = 20;
			$radius = 3959;//6371;
			$val['latitude'] = $this->input->post('latitude');
			$val['longitude'] = $this->input->post('longitude');
			$val['distance'] = $distance; 
			
			//$data = $this->customer_api->getDriversnew_radius($val);
			
			$types = $this->customer_api->FaregetAllTaxiTypesnew($this->input->post('latitude'), $this->input->post('longitude'), $distance, $countryCode);
			$default_image = site_url('assets/uploads/no_image.png');
			$default_url = site_url('assets/uploads/');
			
			//$checkZonal = $this->site->checkZonal($this->input->post('latitude'), $this->input->post('longitude'));
			//if($checkZonal == 1){
				if(!empty($types)){
					foreach($types as $type){
						
						if(!empty($type->image)){
							$type->image = $default_url.$type->image;
						} else {
							$type->image = $default_image;
						}
						if(!empty($type->image_hover)){
							$type->image_hover = $default_url.$type->image_hover;
						} else {
							$type->image_hover = $default_image;
						}
						if(!empty($type->mapcar)){
							$type->mapcar = $default_url.$type->image_hover;
						} else {
							$type->mapcar = $default_image;
						}
						
						$data[] = $type;
					}
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'outstation_min_kilometer' => $settings->outstation_min_kilometer, 'cityride_max_kilometer' => $settings->cityride_max_kilometer, 'rental_max_kilometer' => $settings->rental_max_kilometer, 'outstanding_price' => $outstanding_price, 'outstanding_setting' => 1, 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty', 'outstation_min_kilometer' => $settings->outstation_min_kilometer, 'cityride_max_kilometer' => $settings->cityride_max_kilometer, 'rental_max_kilometer' => $settings->rental_max_kilometer, 'outstanding_price' => $outstanding_price, 'outstanding_setting' => 1);
				}
			//}else{
				//$result = array( 'status'=> 2 , 'message'=> 'Service not Available');
			//}
		}else{
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	/*$setting = $this->customer_api->getSettingmode();
			
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('from_lat'), $this->input->post('from_lng'), $this->input->post('to_lat'), $this->input->post('to_lng'));
			
			if($setting[0]->outstation_min_kilometer <= round($check_distance)){
				$data = $this->customer_api->getOutstationPackagetype($from_city, $to_city);
				if(!empty($data)){
					$result = array( 'status'=> 1 , 'message'=> 'Profile', 'fixed' => $data['fixed'], 'variable' => $data['variable']);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty Data');
				}
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'outstation must cover minimum '.$setting[0]->outstation_min_kilometer.'kms');
			}
			*/
	public function cab_types_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		
		$types = $this->customer_api->FaregetAllTaxiTypes($countryCode);
		$default_image = site_url('assets/uploads/no_image.png');
		$default_url = site_url('assets/uploads/');
		if(!empty($types)){
			foreach($types as $type){
				if(!empty($type->image)){
					$type->image = $default_url.$type->image;
				} else {
					$type->image = $default_image;
				}
				if(!empty($type->image_hover)){
					$type->image_hover = $default_url.$type->image_hover;
				} else {
					$type->image_hover = $default_image;
				}
				if(!empty($type->mapcar)){
					$type->mapcar = $default_url.$type->image_hover;
				} else {
					$type->mapcar = $default_image;
				}
				$data[] = $type;
			}
			
		}
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		$this->response($result);
	}
	
	public function countries_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		$countries = $this->site->getAllCountrieswithflags($countryCode);
		
		if(!empty($countries)){
			foreach($countries as $value)
			{	
				$data[] = array('id' => $value->id, 'title' => $value->name, 'phonecode' => $value->phonecode, 'flag' => $value->flag);
			}
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		} else {
			$result = array( 'status'=> false , 'message'=> 'No Countries Available');
		}
		
		$this->response($result);
	}
	
	
	public function searchcabs_new_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		if ($this->form_validation->run() == true) {
			$val['oauth_token'] = $this->input->post('oauth_token');
			$settings = $this->customer_api->getSettings($countryCode);
			$distance = $settings->search_kilometer;
			//$distance = 20;
			$radius = 3959;//6371;
			$val['latitude'] = $this->input->post('latitude');
			$val['longitude'] = $this->input->post('longitude');
			$val['distance'] = $distance; 
			
			$search_data = $this->site->insertSearch($this->input->post('latitude'), $this->input->post('longitude'), $countryCode);
			$data = $this->customer_api->getDriversnew_radius($val, $countryCode);
			
				if(!empty($data)){
					$result = array( 'status'=> true , 'message'=> 'Success', 'outstation_min_kilometer' => $settings->outstation_min_kilometer, 'cityride_max_kilometer' => $settings->cityride_max_kilometer, 'rental_max_kilometer' => $settings->rental_max_kilometer, 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function search_cabs_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		if ($this->form_validation->run() == true) {
			$val['oauth_token'] = $this->input->post('oauth_token');
			$settings = $this->customer_api->getSettings($countryCode);
			
			$distance = 20;
			$radius = 3959;//6371;
			//$lat  = $this->input->post('latitude');//34.0522342;
			//$lng = $this->input->post('longitude');//-118.2436849;
			$val['taxi_type'] = $this->input->post('taxi_type');
			// latitude boundaries
			//$val['maxlat'] = $lat + rad2deg($distance / $radius);
			//$val['minlat'] = $lat - rad2deg($distance / $radius);
			
			// longitude boundaries (longitude gets smaller when latitude increases)
			//$val['maxlng'] = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
			//$val['minlng'] = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
			
			//$_SESSION['number']++;
						
			
			
			$val['latitude'] = $this->input->post('latitude');
			$val['longitude'] = $this->input->post('longitude');
			$val['distance'] = $distance; 
			
			$data = $this->customer_api->getDrivers_radius($val, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Data is empty');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function book_ride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('from_latitude', $this->lang->line("from_latitude"), 'required');
		$this->form_validation->set_rules('from_longitude', $this->lang->line("from_longitude"), 'required');
		//if($this->input->post('booked_type') != 2){
			$this->form_validation->set_rules('to_latitude', $this->lang->line("to_latitude"), 'required');
			$this->form_validation->set_rules('to_longitude', $this->lang->line("to_longitude"), 'required');
		//}
		$this->form_validation->set_rules('cab_type_id', $this->lang->line("cab_type_id"), 'required');
		$this->form_validation->set_rules('ride_type', $this->lang->line("ride_type"), 'required');
		$this->form_validation->set_rules('booked_type', $this->lang->line("booked_type"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$settings = $this->customer_api->getSettings($countryCode);
			$setting = $this->customer_api->getSettingmode($countryCode);
			
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('from_latitude'), $this->input->post('from_longitude'), $this->input->post('to_latitude'), $this->input->post('to_longitude'), $unit = 'km', $decimals = 2,  $countryCode);
			
			if($this->input->post('booked_type') == 1){
				$booked_type_text = 'City Ride';
				//$kilometer = $setting[0]->cityride_max_kilometer;
				$km_msg = 'cityride must cover maximum '.$setting[0]->cityride_max_kilometer.'kms';
				$check_km = $setting[0]->cityride_max_kilometer >= round($check_distance);
			}elseif($this->input->post('booked_type') == 2){
				$booked_type_text = 'Rental Ride';
				//$kilometer = $setting[0]->rental_max_kilometer;
				$km_msg = 'rental must cover maximum '.$setting[0]->rental_max_kilometer.'kms';
				$check_km = $setting[0]->rental_max_kilometer >= round($check_distance);
			}elseif($this->input->post('booked_type') == 3){
				$booked_type_text = 'Outstation Ride';
				//$kilometer = $setting[0]->outstation_min_kilometer;
				$km_msg = 'outstation must cover minimum '.$setting[0]->outstation_min_kilometer.'kms';
				$check_km = $setting[0]->outstation_min_kilometer <= round($check_distance);
			}else{
				$booked_type_text = 'No Ride';
			}
			
			
				
			
			
			$payment_name = $this->site->getPaymentmodeID($this->input->post('payment_id'), $countryCode);
			
			$distance = 20;
			
			
			
			if($this->input->post('booked_type') == 1){
				$package_id = 0;
				$outstation_type = 0;
				$outstation_way = 0;
			}else{
				if($this->input->post('booked_type') == 3){
					$outstation_type  = $this->input->post('outstation_type');	
					$outstation_way = $this->input->post('outstation_way');
				}else{
					$outstation_type = 0;	
					$outstation_way = 0;
				}
				$package_id = $this->input->post('package_id');
			}
			
			$radius = 3959;//6371;
			//$lat  = $this->input->post('from_latitude');//34.0522342;
			//$lng = $this->input->post('from_longitude');//-118.2436849;
			$val['taxi_type'] = $this->input->post('cab_type_id');
			// latitude boundaries
			//$val['maxlat'] = $lat + rad2deg($distance / $radius);
			//$val['minlat'] = $lat - rad2deg($distance / $radius);
			
			// longitude boundaries (longitude gets smaller when latitude increases)
			//$val['maxlng'] = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
			//$val['minlng'] = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
			
			$val['latitude'] = $this->input->post('from_latitude');
			$val['longitude'] = $this->input->post('from_longitude');
			$val['booking_type'] = $this->input->post('booked_type');
			$val['distance'] = $distance; 
			
			if($this->input->post('ride_type') == 1){
				$driver_data = $this->customer_api->getDrivers_radius($val, $countryCode);
			}else{
				$driver_data = 'Ride_later';
			}
			
			
			
			
			if(!empty($driver_data)){
					
					if($check_km){
					
				
						$driver_allocated = 0;
					
					
					$ride_type = $this->input->post('ride_type');
					if($ride_type == 1){
						$ride_otp = random_string('numeric', 6);
						$status = 1;
						$ride_timing = date('Y-m-d H:i:').':00';
						$ride_timing_end = '0000-00-00 00:00:00';
					}else{
						$ride_timing = $this->input->post('ride_timing').':00';
						if($this->input->post('booking_type') == 3){
							$ride_timing_end = $this->input->post('ride_timing_end').':00';
						}else{
							$ride_timing_end = '0000-00-00 00:00:00';
						}
						$status = 7;
						$ride_otp =  0;
					}
					
					if($ride_type == 2 && $ride_timing){
						//echo $timing = date('H', strtotime($ride_timing));
						
						
					}
					if(!empty($_POST['waypoint_start'])){
						for($i=0; $i<count($_POST['waypoint_start']); $i++){
							$waypoint_array[] = array(
								'start' => $_POST['waypoint_start'][$i],
								'start_lat' => $_POST['waypoint_start_lat'][$i],
								'start_lng' => $_POST['waypoint_start_lng'][$i],
								//'end' => $_POST['waypoint_end'][$i],
								//'end_lat' => $_POST['waypoint_end_lat'][$i],
								//'end_lng' => $_POST['waypoint_end_lng'][$i],
							);
						}
					}else{
						$waypoint_array = array();
					}
						$insert = array(
							'customer_id' => $user_data->id,
							'driver_id' => 0,
							'payment_id' => $this->input->post('payment_id'),
							'cab_type_id' => $this->input->post('cab_type_id'),
							'distance_km' => $this->input->post('distance_km') ? $this->input->post('distance_km') : '0',
							'distance_price' => $this->input->post('distance_price') ? $this->input->post('distance_price') : '0',
							'payment_name' => $payment_name,
							'outstation_type' => $outstation_type,
							'outstation_way' => $outstation_way,
							'package_id' => $package_id,
							'booked_by' => $user_data->id,
							'booked_type' => $this->input->post('booked_type'),
							'booked_on' => date('Y-m-d H:i:s'),               
							'booking_timing' => date('Y-m-d H:i').':00',
							'ride_timing' => $ride_timing,
							'ride_timing_end' => $ride_timing_end,
							'ride_type' => $this->input->post('ride_type'),
							'start' => $this->input->post('pick_up'),
							'start_lat' => $this->input->post('from_latitude'),
							'start_lng' => $this->input->post('from_longitude'),
							'end' => $this->input->post('drop_off') ? $this->input->post('drop_off') : '0',
							'end_lat' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
							'end_lng' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0',
							'status' => $status,
							'ride_otp' => $ride_otp
						);    
						
						/*$ride_insert[] = array(
							'location' => $this->input->post('pick_up'),
							'latitude' => $this->input->post('from_latitude'),
							'longitude' => $this->input->post('from_longitude'),
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 1
						);*/
						
						$ride_insert[] = array(
							'location' => $this->input->post('drop_off'),
							'latitude' => $this->input->post('to_latitude'),
							'longitude' => $this->input->post('to_longitude'),
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 7
						);
						
						$check['customer_id'] = $user_data->id;
						$check_status = $this->customer_api->checkbookedcustomer($check, $countryCode);	
						
						
						
										
						//if($check_status == TRUE){
								
							$data[] = $this->customer_api->add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $user_data->id, $this->input->post('offer_code'), $waypoint_array);
							$payment_name = $this->customer_api->getPaymentName($this->input->post('payment_id'), $countryCode);
							if($data[0] != 0){
								
								if($ride_type == 1){
									
									$notification['title'] = 'Ride Booking';
									$notification['message'] = $user_data->first_name.' has been ride booked.';
									$notification['user_type'] = 4;
									$notification['user_id'] = 2;
									$this->customer_api->insertNotification($notification, $countryCode);
									
									$this->site->bookingEmitDriverinsert($data[0], $driver_data[0]->id, $user_data->id);
									
									$waypoint_data = $this->site->getWaypoint($data[0]);
									$socket_id = $this->site->getSocketID($driver_data[0]->id, 2, $countryCode);
									$event = 'server_booking_checking';
									$edata = array(
										'booked_type_text' => $booked_type_text,
										'payment_id' => $this->input->post('payment_id'),
										'payment_name' => $payment_name,
										'distance_km' => $this->input->post('distance_km'),
										'distance_price' => $this->input->post('distance_price'),
										'customer_support' => '0987654321',
									
										'pick_up' => $this->input->post('pick_up'),
										'from_latitude' => $this->input->post('from_latitude'),
										'from_longitude' => $this->input->post('from_longitude'),
										'drop_off' => $this->input->post('drop_off') ? $this->input->post('drop_off') : 'Location not given',
										'to_latitude' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
										'to_longitude' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0',
										'cab_type_id' => $driver_data[0]->type,
										'ride_id' => $data[0],
										'driver_id' => $driver_data[0]->id,
										'driver_oauth_token' => $driver_data[0]->oauth_token,
										'socket_id' => $socket_id,
										'driver_time' => $setting[0]->driver_time,
										'waypoint_data' => $waypoint_data
										
										
									);
									$success = 	$this->socketemitter->setEmit($event, $edata);
									$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $data[0], 'customer_time' => $setting[0]->customer_time);
								}else{
									
									$ride_data = $this->customer_api->getRideBYID($data[0], $countryCode);
									
									$sms_message = ' '.$user_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
									$sms_phone = $user_data->country_code.$user_data->mobile;
									$sms_country_code = $user_data->country_code;
				
									$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
									
									$notification['title'] = 'Ride Booking - Ride Later';
									$notification['message'] = $user_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
									$notification['user_type'] = 4;
									$notification['user_id'] = 2;
									$this->customer_api->insertNotification($notification, $countryCode);
									
									$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $data[0], 'customer_time' => $setting[0]->customer_time);
								}
								
							}else{
								$result = array( 'status'=> false , 'message'=> 'Booking not added');
							}
						//}else{
							//$result = array( 'status'=> false , 'message'=> 'Customer already booked');
						//}
					
				}else{
					$result = array( 'status'=> false, 'message'=> $km_msg);
				}
				
			}else{
				$result = array( 'status'=> false , 'message'=> 'No Driver available');	
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function my_onride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->customer_api->myonrides($user_data->id, $countryCode);
			$loc = $this->site->GetDrivingDistance($data[0]->start_lat, $data[0]->start_lng,  $data[0]->end_lat, $data[0]->end_lng, $countryCode);
			if(!empty($data)){
				$waypoint_data = $this->site->getWaypoint($data[0]->ride_id);
				if($data[0]->status == 2){
					$overall_rating = $this->site->getOveralldriverRating($data[0]->driver_id);
					
					
					$row = array(
						'cab_type_id' => $data[0]->taxi_id,
						'ride_id' => $data[0]->ride_id,
						
						'driver_id' => $data[0]->driver_id,
						'driver_mobile' => $data[0]->driver_country_code.$data[0]->driver_mobile,
						'ride_otp' => $data[0]->ride_otp,
						'overall_rating' => $overall_rating,
						'driver_taxi_name' => $data[0]->taxi_name,
						'driver_taxi_number' => $data[0]->number,
						'driver_latitude' => $data[0]->current_latitude,
						'driver_longitude' => $data[0]->current_longitude,
						'driver_taxi_type' => $data[0]->types,
						'from_longitude' => $data[0]->start_lng,
						'from_latitude' => $data[0]->start_lat,
						'taxi_image' => $data[0]->taxi_photo,
						'driver_photo' => $data[0]->driver_photo,
						'drivername' => $data[0]->driver_name,
						'drivermobile' => $data[0]->driver_mobile,
						'waypoint_data' => $waypoint_data
					);
					$result = array( 'status'=> 1 , 'message'=> 'Ride Booked', 'data' => $row);
				}elseif($data[0]->status == 3){
					$onstatus = array(
						'sos' => "https://35.154.46.42/sos?id=".$data[0]->ride_id,
						'customer_id' => $user_data->id,
						'booking_id' => $data[0]->ride_id,
						'from_location' => $data[0]->pick_up ? $data[0]->pick_up : '',
						'to_location' => $data[0]->drop_off ? $data[0]->drop_off : '',
						'start_lat' => $data[0]->start_lat,
						'start_lng' => $data[0]->start_lng,
						'end_lat' => $data[0]->end_lat,
						'end_lng' => $data[0]->end_lng,
						'total_km' => $loc['distance'] ? $loc['distance'] : 0,	
						'waypoint_data' => $waypoint_data
					);
					$result = array( 'status'=> 2 , 'message'=> 'Onride', 'data' => $onstatus);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Complete or Cancel', 'data' => $data);
				}
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'No Rides');
			}
		} else {
			 $error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_pastrides_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$sdate = $this->input->post('sdate');
		$edate = $this->input->post('edate');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->customer_api->mypastrides($user_data->id, $countryCode,  $sdate, $edate);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No Rides');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function my_upcomingrides_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$sdate = $this->input->post('sdate');
		$edate = $this->input->post('edate');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->customer_api->myupcomingrides($user_data->id, $countryCode, $sdate, $edate);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No Rides');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function my_currentrides_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->customer_api->mycurrentrides($user_data->id, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No Rides');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
	public function rate_driver_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('rating', $this->lang->line("rating"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$rate['customer_id'] = $user_data->id;
			$rate['booking_id'] = $this->input->post('booking_id');
			$rate['rating'] = $this->input->post('rating');
			$rate['feedback'] = $this->input->post('feedback');
			$data = $this->customer_api->customerRating($rate, $countryCode);
			
			if($data == true){
				$result = array( 'status'=> true , 'message'=> 'Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'customer rating not added');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
		
	/*public function customercancel_post(){
		$data = array();
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('cancel_msg', $this->lang->line("cancel_msg"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$setting = $this->customer_api->getSettings($countryCode);
			$current_date = date('Y-m-d');
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$cancel['customer_id'] = $user_data->id;
			
			$check_cancel_limit = $this->site->cancelLimit('customer', $user_data->id, $setting->no_of_customer_cancel, $current_date);
			
			$cancel['booking_id'] = $this->input->post('booking_id');
			$cancel['cancel_msg'] = $this->input->post('cancel_msg');
			$cancel['cancel_id'] = $this->input->post('cancel_id') ? $this->input->post('cancel_id') : 0;
			$rides = $this->customer_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			$cancel['driver_id'] = $rides->driver_id; 
			$cancel['outstation_type'] = $rides->outstation_type;
			$cancel['customer_cancel_charge'] = $setting->customer_cancel_charge; 
			$cancel['ride_cancel_billing_screen_enable'] = $setting->ride_cancel_billing_screen_enable; 
			$cancel['ride_cancel_driver_on_the_way_km_fare_enable'] = $setting->ride_cancel_driver_on_the_way_km_fare_enable;
			
			if($setting->ride_cancel_billing_screen_enable == 1 && $this->input->post('ride_started') == 1){
				$row['booking_id'] = $this->input->post('booking_id');
				$row['customer_id'] = $user_data->id;
				$row['driver_id'] = $rides->driver_id; 
				$ride = $this->customer_api->getRideID($this->input->post('booking_id'), $countryCode);			
				$row['package_id'] = $ride->package_id;
				$row['booked_type'] = $ride->booked_type;
				$row['outstation_type'] = $ride->outstation_type;
				$driver_loc = $this->customer_api->driverLoc($rides->driver_id);
				
				$row['customer_drop_lat'] = $driver_loc->current_latitude;
				$row['customer_drop_lng'] = $driver_loc->current_longitude;
				
				$actual_loc = $this->site->findLocation($driver_loc->current_latitude, $driver_loc->current_longitude, $countryCode);
				$row['actual_loc'] = $actual_loc;
				$row['actual_lat'] = $driver_loc->current_latitude;
				$row['actual_lng'] = $driver_loc->current_longitude;
				$row['total_toll'] =  0;
				$row['total_parking'] = 0;
				$row['travel_distance'] = '0.00';
				
				$row['driver_admin_payment_option'] = $setting->driver_admin_payment_option;
				$row['driver_admin_payment_percentage'] = $setting->driver_admin_payment_percentage;
				$row['driver_admin_payment_duration'] = $setting->driver_admin_payment_duration;
				
				$current_date = date('d/m/Y');
				if($setting->driver_admin_payment_option == 1){
					$ride_start_date = date('d/m/Y', strtotime($current_date. '+1 days'));
				}elseif($setting->driver_admin_payment_option == 2){
					$ride_start_date = date('d/m/Y', strtotime($current_date. '+7 days'));
				}elseif($setting->driver_admin_payment_option == 3){
					$ride_start_date = date('d/m/Y', strtotime($current_date. '+30 days'));
				}
			
				$data = $this->customer_api->onrideCustomerCancel($row, $countryCode);
				$details[] = $data;
				
			}else{
				$data = $this->customer_api->customerCancel($cancel, $countryCode);
				$details[] = $data;
			}
			if(!empty($rides->driver_id)){
				
				$customer_data = $this->customer_api->getCustomerID($user_data->id, $countryCode);
				$driver_data = $this->customer_api->getDriverID($rides->driver_id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				//$driver_phone = $driver_data->country_code.$driver_data->mobile;
				if($setting->ride_cancel_billing_screen_enable == 1 && $this->input->post('ride_started') == 1){
					if($rides->driver_id != 2){
						$notification['title'] = 'Ride Cancel';
						$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'. bililing screen moved. cancel amount :'.$data['total_fare'];
						$notification['user_type'] = 2;
						$notification['user_id'] = $rides->driver_id;
						$this->customer_api->insertNotification($notification, $countryCode);
					}
					$notification1['title'] = 'Ride Cancel';
					$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'. bililing screen moved. cancel amount:'.$data['total_fare'];
					$notification1['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification1, $countryCode);
					
					$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
					$event = 'server_onride_cancel';
					$edata = array(
						
						'title' => 'Ride Cancel, Billing screen move',
						'message' => "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".number_format($data['total_fare'], 2)."",
						'cancel_fare' => number_format($data['total_fare'], 2),
						'data' => $data,					
						'socket_id' => $socket_id
						
					);
					
					
					
					$success = 	$this->socketemitter->setEmit($event, $edata);
					$sms_message = "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".number_format($data['total_fare'], 2)."";
					$sms_phone = $customer_data->mobile;
					$sms_country_code = $customer_data->country_code;
					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
				}else{
					if($rides->driver_id != 2){
						$notification['title'] = 'Ride Cancel';
						$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'. cancel amount :'.$setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $setting->customer_cancel_charge : $data['total_fare'];
						$notification['user_type'] = 2;
						$notification['user_id'] = $rides->driver_id;
						$this->customer_api->insertNotification($notification, $countryCode);
					}
					$notification1['title'] = 'Ride Cancel';
					$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'Cancel amount:'.$setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $setting->customer_cancel_charge : $data['total_fare'];
					$notification1['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification1, $countryCode);
									
					$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
					$customer_cancel_charge = $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $setting->customer_cancel_charge : number_format($data['total_fare'],2);
					
					if($check_cancel_limit != 0){
						$emit_msg = "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".($check_cancel_limit-1)."";
					}else{
						$emit_msg = "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$customer_cancel_charge."";
					}
					
					$event = 'server_ride_cancel';
					$edata = array(
						
						'title' => 'Ride Cancel',
						'message' => $emit_msg,		
						'cancel_fare' => $customer_cancel_charge,
						'socket_id' => $socket_id
						
					);
					
					$success = 	$this->socketemitter->setEmit($event, $edata);
					
					if($check_cancel_limit != 0){
						$sms_msg = "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".($check_cancel_limit-1)."";
					}else{
						$sms_msg = "Ride has been cancelled by customer. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$customer_cancel_charge."";
					}
					
					$sms_message = $sms_msg;
					$sms_phone = $customer_data->mobile;
					$sms_country_code = $customer_data->country_code;
					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				}
			}
			
			if($data == true){
				if($setting->ride_cancel_billing_screen_enable == 1 && $this->input->post('ride_started') == 1){
					
					if($check_cancel_limit != 0){
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".($check_cancel_limit-1)."";
					}else{
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".number_format($data['total_fare'], 2)."";
					}
					
					$result = array( 'status'=> 2 , 'check_cancel_limit' => $check_cancel_limit, 'message'=> $check_msg, 'cancel_fare' => number_format($data['total_fare'], 2), 'data' => $details);
				}else{
					
					$customer_cancel_charge = $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $setting->customer_cancel_charge : number_format($data['total_fare'],2);
					
					if($check_cancel_limit != 0){
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".($check_cancel_limit-1)."";
					}else{
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$customer_cancel_charge."";
					}
					
					$result = array( 'status'=> 1 , 'check_cancel_limit' => $check_cancel_limit, 'message'=> $check_msg, 'cancel_fare' => $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $setting->customer_cancel_charge : number_format($data['total_fare'],2), 'ride_cancel_driver_on_the_way_km_fare_enable' => $setting->ride_cancel_driver_on_the_way_km_fare_enable, 'data' => $details);
				}
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'customer cancel not success');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}*/
	
	public function customercancel_post(){
		$data = array();
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('cancel_msg', $this->lang->line("cancel_msg"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$setting = $this->customer_api->getSettings($countryCode);
			$current_date = date('Y-m-d');
			if($this->input->post('ride_started') == 1){
				$type_cancel = $this->site->CancelCabType($this->input->post('type_id'), $countryCode);
			}else{
				$type_cancel = $this->site->CancelCabBooking($this->input->post('booking_id'), $countryCode);
			}
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$cancel['customer_id'] = $user_data->id;
			
			$check_cancel_limit = $this->site->cancelLimit('customer', $user_data->id, $type_cancel->no_of_customer_cancel, $current_date);
			$check_cancel_limit_increase = $check_cancel_limit + 1;
			
			$cancel['booking_id'] = $this->input->post('booking_id');
			$cancel['cancel_msg'] = $this->input->post('cancel_msg');
			$cancel['cancel_id'] = $this->input->post('cancel_id') ? $this->input->post('cancel_id') : 0;
			$rides = $this->customer_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			$cancel['driver_id'] = $rides->driver_id; 
			$cancel['outstation_type'] = $rides->outstation_type;
			$cancel['customer_cancel_charge'] = $type_cancel->customer_cancel_charge; 
			$cancel['no_of_customer_cancel'] = $type_cancel->no_of_customer_cancel; 
			$cancel['cancel_free_second'] = $type_cancel->cancel_free_second;
			$cancel['ride_cancel_billing_screen_enable'] = $setting->ride_cancel_billing_screen_enable; 
			$cancel['ride_cancel_driver_on_the_way_km_fare_enable'] = $setting->ride_cancel_driver_on_the_way_km_fare_enable;
			$cancel['check_cancel_limit'] = $check_cancel_limit_increase;
			$cancel['ride_started'] = $this->input->post('ride_started');
				$cancel['cancel_on'] = date('Y-m-d H:i:s');
				$cancel['booked_on'] = $rides->booked_on;
				$ride_total_second = strtotime($cancel['cancel_on']) - strtotime($cancel['booked_on']);
				if($ride_total_second > $type_cancel->cancel_free_second){
					$cancel['check_cancel_free_second'] = 1;
				}else{
					$cancel['check_cancel_free_second'] = 0;
				}
				$data = $this->customer_api->customerCancel($cancel, $countryCode);
				$details[] = $data;
				
				
			
			if(!empty($rides->driver_id)){
				
				$customer_data = $this->customer_api->getCustomerID($user_data->id, $countryCode);
				$driver_data = $this->customer_api->getDriverID($rides->driver_id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				$driver_phone = $driver_data->country_code.$driver_data->mobile;
				
					if($rides->driver_id != 2){
						$notification['title'] = 'Ride Cancel';
						if($cancel['check_cancel_free_second'] == 1){
							$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
						}else{
							$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
						}
						$notification['user_type'] = 2;
						$notification['user_id'] = $rides->driver_id;
						$this->customer_api->insertNotification($notification, $countryCode);
					}
					$notification1['title'] = 'Ride Cancel';
					if($cancel['check_cancel_free_second'] == 1){
						$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
					}else{
						$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
					}
					$notification1['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification1, $countryCode);
									
					$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
					$customer_cancel_charge = $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $type_cancel->customer_cancel_charge : number_format($data['total_fare'],2);
					
					if($check_cancel_limit_increase <= $type_cancel->no_of_customer_cancel){
						$remaining = ($type_cancel->no_of_customer_cancel - $check_cancel_limit) - 1;
					}else{
						$remaining = 'Not free cancel... charges applied';
					}
					if($cancel['check_cancel_free_second'] == 1 && $this->input->post('ride_started') == 1){
						if($check_cancel_limit_increase > $type_cancel->no_of_customer_cancel){
							$emit_msg = "Ride has been cancelled by customer (".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
						}else{
							
							$emit_msg = "Ride has been cancelled by customer (".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
							
						}
					}else{
						$emit_msg = "Ride has been cancelled by customer (".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}
					
					$event = 'server_ride_cancel';
					$edata = array(
						
						'title' => 'Ride Cancel',
						'message' => $emit_msg,		
						'cancel_fare' => $customer_cancel_charge,
						'socket_id' => $socket_id
						
					);
					
					$success = 	$this->socketemitter->setEmit($event, $edata);
					
					if($cancel['check_cancel_free_second'] == 1 && $this->input->post('ride_started') == 1){
						if($check_cancel_limit_increase > $type_cancel->no_of_customer_cancel){
							$sms_msg = "Ride has been cancelled by customer(".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
						}else{
							
							$sms_msg = "Ride has been cancelled by customer(".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
						}
					}else{
						$sms_msg = "Ride has been cancelled by customer(".$customer_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}
					
					$sms_message = $sms_msg;
					$sms_phone = $driver_phone;
					$sms_country_code = $driver_data->country_code;
					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
			}
			
			if($data == true){
				
					
					$customer_cancel_charge = $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $type_cancel->customer_cancel_charge : number_format($data['total_fare'],2);
					
					if($cancel['check_cancel_free_second'] == 1 && $this->input->post('ride_started') == 1){
						if($check_cancel_limit_increase > $type_cancel->no_of_customer_cancel){
							$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$customer_cancel_charge."(".$data['customer_cancel_final_km']."Km)";
						}else{
							$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".$remaining."";
							
						}
					}else{
						$check_msg = "Ride has been cancelled.  cancel reason : ".$this->input->post('cancel_msg')."";
					}
					
					$result = array( 'status'=> 1 , 'no_of_customer_cancel' => $type_cancel->no_of_customer_cancel, 'check_cancel_limit' => $check_cancel_limit, 'message'=> $check_msg, 'cancel_fare' => $setting->ride_cancel_driver_on_the_way_km_fare_enable == 0 ? $type_cancel->customer_cancel_charge : number_format($data['total_fare'],2), 'ride_cancel_driver_on_the_way_km_fare_enable' => $setting->ride_cancel_driver_on_the_way_km_fare_enable, 'data' => $details);
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'customer cancel not success');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> 0 , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function notifications_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$data = $this->site->Getnotification($user_data->id, '1', $countryCode);
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message' => 'no data');	
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	public function customerchangedrop_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('change_pickup', $this->lang->line("change_pickup"), 'required');
		$this->form_validation->set_rules('change_pickup_lat', $this->lang->line("change_pickup_lat"), 'required');
		$this->form_validation->set_rules('change_pickup_lng', $this->lang->line("change_pickup_lng"), 'required');
		$this->form_validation->set_rules('change_drop', $this->lang->line("change_drop"), 'required');
		$this->form_validation->set_rules('change_drop_lat', $this->lang->line("change_drop_lat"), 'required');
		$this->form_validation->set_rules('change_drop_lng', $this->lang->line("change_drop_lng"), 'required');
		$this->form_validation->set_rules('change_type', $this->lang->line("change_drop_lng"), 'required');
		$this->form_validation->set_rules('distance_km', $this->lang->line("distance_km"), 'required');
		
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$rides = $this->customer_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			$change['customer_id'] = $user_data->id;
			$change['booking_id'] = $this->input->post('booking_id');
			$change['driver_id'] = $rides->driver_id; 
			$change['change_pickup'] = $this->input->post('change_pickup'); 
			$change['change_pickup_lat'] = $this->input->post('change_pickup_lat'); 
			$change['change_pickup_lng'] = $this->input->post('change_pickup_lng'); 
			$change['change_drop'] = $this->input->post('change_drop'); 
			$change['change_drop_lat'] = $this->input->post('change_drop_lat'); 
			$change['change_drop_lng'] = $this->input->post('change_drop_lng'); 
			$change['change_type'] = $this->input->post('change_type'); 
			$change['end'] = $rides->end; 
			$change['end_lat'] = $rides->end_lat; 
			$change['end_lng'] = $rides->end_lng; 
			
			if(!empty($_POST['waypoint_start'])){
				for($i=0; $i<count($_POST['waypoint_start']); $i++){
					$waypoint_array[] = array(
						'start' => $_POST['waypoint_start'][$i],
						'start_lat' => $_POST['waypoint_start_lat'][$i],
						'start_lng' => $_POST['waypoint_start_lng'][$i],
						//'end' => $_POST['waypoint_end'][$i],
						//'end_lat' => $_POST['waypoint_end_lat'][$i],
						//'end_lng' => $_POST['waypoint_end_lng'][$i],
					);
				}
			}else{
				$waypoint_array = array();
			}
			
			$data = $this->customer_api->customerChangeDrop($change, $this->input->post('distance_km'), $waypoint_array, $countryCode);
			
			if(!empty($rides->driver_id) && !empty($data)){
				
				$customer_data = $this->customer_api->getCustomerID($user_data->id, $countryCode);
				$driver_data = $this->customer_api->getDriverID($rides->driver_id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				$driver_phone = $driver_data->country_code.$driver_data->mobile;
				
				if($rides->driver_id != 2){
					$notification['title'] = 'Ride Change';
					$notification['message'] = 'Ride has been changeed by customer('.$customer_name.').';
					$notification['user_type'] = 2;
					$notification['user_id'] = $rides->driver_id;
					$this->customer_api->insertNotification($notification, $countryCode);
				}
				$notification1['title'] = 'Ride Change';
				$notification1['message'] = 'Ride has been changeed by customer('.$customer_name.').';
				$notification1['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->customer_api->insertNotification($notification1, $countryCode);
								
				$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
				$waypoint_data = $this->site->getWaypoint($this->input->post('booking_id'));
				$event = 'server_ride_change';
				$edata = array(
					
					'title' => 'Ride Change',
					'change_type' => $data->change_type,
					'pickup' => $data->start,
					'pickup_lat' => $data->start_lat,
					'pickup_lng' => $data->start_lng,
					
					'drop' => $data->end,
					'drop_lat' => $data->end_lat,
					'drop_lng' => $data->end_lng,
					
					'middle' => $data->middle,
					'middle_lat' => $data->middle_lat,
					'middle_lng' => $data->middle_lng,
					
					'socket_id' => $socket_id,
					'waypoint_data' => $waypoint_data,
					'distance_price' => $data->distance_price,
					'booking_id' => $this->input->post('booking_id')
					
				);
				
				$success = 	$this->socketemitter->setEmit($event, $edata);
				$sms_message = 'Ride has been cancelled by driver('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				$sms_phone = $customer_data->mobile;
				$sms_country_code = $customer_data->country_code;
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
			}
			
			if($data != false){
				$result = array( 'status'=> true , 'message'=> 'Ride has been changes', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'customer cancel not success');
			}
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}
	
	
}
