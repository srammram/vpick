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
	
	function commonsetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->customer_api->getSettings($countryCode);
			
			$data = array(
				'dateofbirth' => $res->{'dateofbirth'},
							
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
			$row['user_id'] = $user_data->id;
			
			$paid_amount = $this->input->post('paid_amount');
			
			
			$paid_amount = $this->input->post('paid_amount');
			$join_id = 0;
			$join_table = '';
			
			$razorpay_payment_id = $this->input->post('razorpay_payment_id');
			
			$payment_gateway_id = $this->input->post('payment_gateway_id');
			$transaction_status = $this->input->post('transaction_status');
			
			$payment_array = array(
				'method' => 8,
				'join_id' => $join_id,
				'join_table' => $join_table,
				'user_id' => $user_data->id,
				'payment_transaction_id' => $razorpay_payment_id,
				'amount' => $paid_amount,
				'transaction_status' => $transaction_status,
				'transaction_type' => 'Credit',
				'gateway_id' => $payment->payment_gateway_id,
				'created_on' => date('Y-m-d H:i:s')
			);
			
			$wallet_array = array(
				'user_id' =>  $user_data->id,
				'user_type' => 2,
				'wallet_type' => 1,
				'flag' => 6,
				'cash' => $paid_amount,
				'description' => 'Add Money - Cash Wallet',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$res = $this->customer_api->addMoneyCashwallet($user_data->id, $wallet_array, $payment_array,  $countryCode, $transaction_status);
		
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


	public function demo_post(){
		
		$d  = array(13.1233889,80.191529,2,13.1233889,80.191529,2,13.1233652,80.1915374,2,13.1233532,80.1915387,2,13.1233459,80.1915451,2,13.1233377,80.191558,2,13.1233346,80.1915652,2,13.1233405,80.191567,2,13.1233483,80.1915759,2,13.1233956,80.1916086,2,13.123446,80.1916122,3,13.1234481,80.1916123,3,13.1234481,80.1916123,3,13.1235529,80.1915925,3,13.1236416,80.1915873,3,13.1238133,80.1916133,3,13.1238983,80.1916517,3,13.123965,80.19178,3,13.1239867,80.1918867,3,13.123995,80.19207,3,13.1239983,80.1921833,3,13.1240117,80.192285,3,13.124015,80.192405,3,13.1240083,80.19253,3,13.1240017,80.1927467,3,13.1239917,80.1929533,3,13.1239867,80.1931283,3,13.124006,80.1932467,3,13.1240042,80.1933016,3,13.1239985,80.1933489,3,13.1239662,80.1933749,3,13.123885,80.1933967,3,13.1237867,80.193395,3,13.1236833,80.1933883,3,13.1235233,80.1933917,3,13.123435,80.1933867,3,13.1233,80.1933767,3,13.1232152,80.1933482,3,13.1230567,80.1933317,3,13.1228867,80.1933233,3,13.1226983,80.1933217,3,13.1225683,80.1933217,3,13.1224083,80.1933267,3,13.12228,80.19333,3,13.122045,80.1933317,3,13.1218933,80.1933283,3,13.121745,80.1933317,3,13.12154,80.1933417,3,13.121365,80.1933417,3,13.12127,80.19334,3,13.1211683,80.1933333,3,13.1209417,80.1933267,3,13.1207767,80.1933217,3,13.1205517,80.1933317,3,13.1204217,80.19335,3,13.1202917,80.193395,3,13.1202533,80.1935217,3,13.1202583,80.1936533,3,13.1202683,80.1938617,3,13.1202817,80.1940333,3,13.1202567,80.1941333,3,13.1200817,80.1941183,3,13.1198783,80.1941183,3,13.1196583,80.19414,3,13.1195383,80.194135,3,13.1194333,80.1941317,3,13.1193453,80.1941321,3,13.1193288,80.194136,3,13.1193064,80.194178,3,13.1193026,80.1942211,3,13.11932,80.194335,3,13.119335,80.1945067,3,13.1193233,80.19474,3,13.119315,80.1949083,3,13.119315,80.19515,3,13.119315,80.1954183,3,13.1193233,80.1956667,3,13.1193567,80.1958533,3,13.1193333,80.1960683,3,13.119315,80.19623,3,13.1192883,80.1964467,3,13.11922,80.19665,3,13.11916,80.1968033,3,13.1190917,80.1969333,3,13.1190017,80.197085,3,13.1189317,80.1972817,3,13.1189033,80.197435,3,13.1188683,80.1976967,3,13.118845,80.197865,3,13.11883,80.1980183,3,13.1187933,80.1982183,3,13.1187779,80.1983006,3,13.1187755,80.1983421,3,13.1188783,80.1984267,3,13.1189983,80.1984883,3,13.119125,80.1985617,3,13.1193267,80.1986817,3,13.11951,80.1987883,3,13.1196533,80.19887,3,13.1197917,80.19896,3,13.1199067,80.1990367,3,13.1200617,80.1991267,3,13.1202083,80.1992033,3,13.1204633,80.19933,3,13.1206467,80.199435,3,13.12101,80.1995833,3,13.12138,80.199785,3,13.1216367,80.1999217,3,13.1218683,80.20008,3,13.1221633,80.2003117,3,13.1223533,80.200455,3,13.12251,80.2005733,3,13.122625,80.2006817,3,13.1227567,80.2008417,3,13.12287,80.2009883,3,13.122945,80.2010867,3,13.1230567,80.2012467,3,13.123175,80.2014233,3,13.12327,80.2015633,3,13.1233983,80.2017267,3,13.123525,80.2020933,3,13.1236583,80.2023233,3,13.123845,80.2026783,3,13.1239817,80.20289,3,13.12417,80.2032317,3,13.1242867,80.2034617,3,13.1244717,80.20379,3,13.1246617,80.2041267,3,13.1248383,80.2044417,3,13.1252617,80.2051333,3,13.1253633,80.2053233,3,13.1254567,80.2055583,3,13.12556,80.2057617,3,13.125865,80.2062767,3,13.1259933,80.206495,3,13.126195,80.206805,3,13.126325,80.20702,3,13.1265067,80.2073533,3,13.1266917,80.2077,3,13.1267967,80.207905,3,13.1269683,80.2081833,3,13.1270633,80.2083767,3,13.1271883,80.2086567,3,13.1273017,80.20882,3,13.1274383,80.2091033,3,13.1275383,80.20928,3,13.127725,80.2095583,3,13.1278283,80.2097767,3,13.12803,80.2100883,3,13.1282133,80.2103817,3,13.1283567,80.2106233,3,13.1284667,80.2107833,3,13.1285483,80.2109683,3,13.12871,80.211265,3,13.1288717,80.21158,3,13.1290767,80.2119083,3,13.1292583,80.212225,3,13.12944,80.2125533,3,13.1296117,80.21289,3,13.1297817,80.213185,3,13.1299983,80.213455,3,13.1301517,80.2136033,3,13.1303917,80.2138483,3,13.130665,80.2140533,3,13.1309617,80.2142183,3,13.13118,80.21433,3,13.1315267,80.2145017,3,13.1317617,80.2146067,3,13.1321317,80.2147667,3,13.1324733,80.2148983,3,13.1327767,80.2150267,3,13.1330417,80.2151517,3,13.1333583,80.2152617,3,13.1335717,80.21537,3,13.1338867,80.21551,3,13.1341967,80.2156533,3,13.1344017,80.2157433,3,13.134665,80.2158617,3,13.1348367,80.21593,3,13.1350617,80.2160233,3,13.13527,80.21611,3,13.1354017,80.2161683,3,13.1356033,80.21627,3,13.1357567,80.2163517,3,13.13598,80.2164667,3,13.13612,80.2165267,3,13.1362567,80.2165833,3,13.136395,80.2166417,3,13.136615,80.21674,3,13.1367667,80.2168067,3,13.1370317,80.21693,3,13.1372017,80.2170183,3,13.13742,80.2171017,3,13.137765,80.21723,3,13.1379583,80.2173017,3,13.138275,80.2174517,3,13.1386033,80.21759,3,13.1388233,80.2176933,3,13.1391433,80.21783,3,13.1393783,80.2179183,3,13.13972,80.2180533,3,13.14007,80.2181967,3,13.1404167,80.2183483,3,13.140755,80.21851,3,13.14114,80.2186567,3,13.1413967,80.218775,3,13.1417817,80.2189517,3,13.1421733,80.2191217,3,13.14253,80.2192417,3,13.1431217,80.2194883,3,13.1433567,80.219605,3,13.1436083,80.2197117,3,13.143975,80.219875,3,13.1443433,80.220015,3,13.1445917,80.2201183,3,13.1449533,80.2202783,3,13.1451833,80.2203817,3,13.1455,80.2205217,3,13.1458467,80.2206683,3,13.1462167,80.2208083,3,13.1466017,80.22098,3,13.14698,80.2211533,3,13.147335,80.2213133,3,13.1476883,80.221455,3,13.1479133,80.221535,3,13.1483567,80.2217267,3,13.1486783,80.2218633,3,13.148885,80.22196,3,13.1490883,80.222045,3,13.149395,80.22218,3,13.14958,80.2222567,3,13.1498917,80.2223833,3,13.1501117,80.2224717,3,13.1503333,80.22254,3,13.1506267,80.2226533,3,13.15093,80.2227783,3,13.1514483,80.222985,3,13.1519767,80.2232183,3,13.152415,80.2234167,3,13.15267,80.2234983,3,13.1530317,80.22361,3,13.1532483,80.2237183,3,13.1534833,80.22381,3,13.1538683,80.22395,3,13.1541233,80.224075,3,13.1545133,80.2242167,3,13.154765,80.224335,3,13.15512,80.2244867,3,13.1554283,80.2246083,3,13.1557367,80.2247283,3,13.15595,80.224805,3,13.156255,80.224925,3,13.1565633,80.22504,3,13.1567783,80.22512,3,13.1570767,80.225235,3,13.1572317,80.2253017,3,13.157365,80.2253617,3,13.1575967,80.2254533,3,13.1578317,80.22555,3,13.158055,80.22563,3,13.158225,80.2256867,3,13.1583042,80.2257525,3,13.1582948,80.225799,3,13.1582818,80.225816,3,13.1582797,80.2258184,3,13.1582776,80.2258207,3,13.1582772,80.2258211,3,13.1582769,80.2258215,3,13.1582768,80.2258216,3,13.1582768,80.2258216,3,13.1582077,80.2258319,3,13.1580767,80.2257583,3,13.1579133,80.2256883,3,13.15769,80.2255983,3,13.157525,80.225535,3,13.1573533,80.225465,3,13.15707,80.2253817,3,13.1567883,80.2252367,3,13.1566133,80.2251617,3,13.1563767,80.225055,3,13.156215,80.22499,3,13.15607,80.2249333,3,13.1559567,80.22489,3,13.1558778,80.2248642,3,13.1558,80.2248267,3,13.1557133,80.2247883,3,13.1555183,80.22471,3,13.155365,80.2246583,3,13.1551333,80.22456,3,13.1549033,80.2244733,3,13.1547417,80.2244033,3,13.154575,80.2243283,3,13.1543233,80.2242217,3,13.15416,80.2241517,3,13.153885,80.2240367,3,13.1536317,80.22393,3,13.15336,80.2238267,3,13.1531883,80.2237633,3,13.1528967,80.22364,3,13.1526833,80.2235433,3,13.152355,80.22343,3,13.1521483,80.2233233,3,13.151915,80.2232467,3,13.1516983,80.2231583,3,13.15142,80.2230417,3,13.1511433,80.22294,3,13.150895,80.2228417,3,13.150735,80.2227767,3,13.150495,80.2226717,3,13.150325,80.2225983,3,13.1500483,80.2225033,3,13.1497717,80.2223767,3,13.1495867,80.2222867,3,13.1493983,80.2222167,3,13.14909,80.2221,3,13.1487067,80.22195,3,13.1482533,80.2217583,3,13.14795,80.2216333,3,13.1474817,80.2214283,3,13.147175,80.22129,3,13.146705,80.2211067,3,13.1464183,80.220985,3,13.1460233,80.2208233,3,13.1457817,80.22073,3,13.1455517,80.2206383,3,13.1452617,80.22052,3,13.1449833,80.2203617,3,13.1446483,80.2202383,3,13.1444183,80.2201667,3,13.144045,80.22001,3,13.1438417,80.219925,3,13.1435333,80.219765,3,13.143185,80.21961,3,13.1429283,80.21951,3,13.1426567,80.219395,3,13.14225,80.2192217,3,13.141835,80.2190267,3,13.14138,80.21884,3,13.14094,80.2186667,3,13.1404783,80.2184983,3,13.1401933,80.2183867,3,13.139935,80.21825,3,13.1396783,80.21814,3,13.13931,80.2179817,3,13.1389867,80.2178417,3,13.138685,80.217705,3,13.1383683,80.2175683,3,13.1380617,80.2174283,3,13.1378483,80.2173417,3,13.13751,80.2171867,3,13.1371733,80.217065,3,13.136885,80.2169267,3,13.13668,80.2168333,3,13.1363533,80.2166933,3,13.136095,80.2165983,3,13.1357717,80.2164533,3,13.1354983,80.2163317,3,13.1352317,80.2162183,3,13.1350633,80.21615,3,13.1348917,80.216055,3,13.1347233,80.2159883,3,13.1345767,80.21593,3,13.13437,80.215845,3,13.13425,80.2157967,3,13.1341,80.2157333,3,13.1340103,80.215697,3,13.1339033,80.2156583,3,13.1337917,80.2156117,3,13.1336367,80.2155433,3,13.1333567,80.2154233,3,13.1330683,80.2152883,3,13.1328,80.21517,3,13.1326067,80.215085,3,13.13233,80.214955,3,13.1320017,80.214845,3,13.1317233,80.214705,3,13.1315433,80.21464,3,13.131265,80.21454,3,13.1311217,80.2144583,3,13.1309317,80.2143333,3,13.1307483,80.2142167,3,13.1306167,80.2141367,3,13.13049,80.2140417,3,13.13037,80.2139467,3,13.1302367,80.2138183,3,13.13014,80.21373,3,13.1300783,80.2136633,3,13.1300233,80.21358,3,13.1299083,80.2134467,3,13.12976,80.2132633,3,13.1296583,80.2131233,3,13.1294933,80.2128533,3,13.1293383,80.2125767,3,13.1292633,80.212385,3,13.1291383,80.2121367,3,13.1290367,80.2119733,3,13.128935,80.2118133,3,13.12883,80.2115883,3,13.1287567,80.21145,3,13.1286967,80.2113133,3,13.128605,80.21113,3,13.128515,80.2109633,3,13.1284697,80.2108617,3,13.1284456,80.2108286,3,13.128406,80.2107981,3,13.1283791,80.2107934,3,13.1283611,80.2107853,3,13.1283419,80.2107791,3,13.1283178,80.2107613,3,13.12823,80.2106683,3,13.1281517,80.210525,3,13.1280367,80.21032,3,13.1279517,80.21017,3,13.1278233,80.20992,3,13.127655,80.2096533,3,13.1275367,80.2094817,3,13.127415,80.2092,3,13.127255,80.2089217,3,13.1271133,80.208735,3,13.1269617,80.2084617,3,13.126855,80.2082833,3,13.1267167,80.2079967,3,13.126585,80.2077117,3,13.126485,80.207535,3,13.1263267,80.20727,3,13.126215,80.2070467,3,13.12611,80.20685,3,13.1260517,80.2067467,3,13.1259867,80.2066033,3,13.1259117,80.2065167,3,13.1258433,80.2064383,3,13.125745,80.2062367,3,13.12566,80.2060933,3,13.1255233,80.2058633,3,13.1254483,80.205715,3,13.1253633,80.2055517,3,13.1253345,80.2054982,3,13.1253181,80.2054681,3,13.1253101,80.205462,3,13.1253051,80.2054588,3,13.1253031,80.2054575,3,13.1253024,80.2054571,3,13.1253018,80.2054568,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1253017,80.2054567,3,13.1252975,80.2054433,3,13.1252217,80.2052917,3,13.12516,80.20518,3,13.1250367,80.20496,3,13.1249333,80.2047883,3,13.124765,80.2045117,3,13.1246067,80.2042033,3,13.1244333,80.20388,3,13.1242933,80.2036567,3,13.1241767,80.2034417,3,13.12403,80.20317,3,13.1239167,80.2029583,3,13.1237333,80.202665,3,13.1235967,80.2024083,3,13.1234683,80.202185,3,13.1233983,80.202045,3,13.1233283,80.2019017,3,13.123245,80.201755,3,13.1231533,80.2015933,3,13.1230183,80.2013883,3,13.12293,80.20126,3,13.1228033,80.20107,3,13.1227133,80.2009517,3,13.1226233,80.200845,3,13.1224883,80.2006817,3,13.1223817,80.2005867,3,13.1221967,80.2004517,3,13.1220033,80.2003083,3,13.1217933,80.2001717,3,13.1215283,80.200025,3,13.121315,80.1998517,3,13.1210417,80.1997333,3,13.1207933,80.1996183,3,13.12056,80.19952,3,13.120325,80.1994317,3,13.1200833,80.1993233,3,13.1199317,80.19924,3,13.119705,80.1991133,3,13.1194567,80.1989617,3,13.1193033,80.19887,3,13.1191,80.19873,3,13.11896,80.1986367,3,13.1187917,80.1985333,3,13.1187431,80.198515,3,13.11873,80.1985338,3,13.1187333,80.1985471,3,13.1187354,80.198553,3,13.1187362,80.1985552,3,13.1187364,80.1985559,3,13.1187366,80.1985564,3,13.1187367,80.1985566,3,13.1187367,80.1985566,3,13.1187367,80.1985567,3,13.1187367,80.1985567,3,13.1187367,80.1985567,3,13.1187303,80.1984989,3,13.1188,80.198245,3,13.11881,80.1980733,3,13.11886,80.1978983,3,13.118905,80.197765,3,13.11892,80.1975333,3,13.1189283,80.1973267,3,13.11897,80.1971983,3,13.11904,80.1970033,3,13.1191317,80.1968017,3,13.1191733,80.1966883,3,13.1192133,80.196545,3,13.1192383,80.196445,3,13.119275,80.1962933,3,13.1193117,80.1961083,3,13.119325,80.195965,3,13.1193267,80.1957167,3,13.1193167,80.195555,3,13.11929,80.1953367,3,13.1192967,80.1952183,3,13.1193,80.1950583,3,13.11931,80.194885,3,13.119325,80.1947667,3,13.119335,80.1946517,3,13.1193517,80.19448,3,13.11937,80.1943883,3,13.1193709,80.1943112,3,13.119363,80.1942299,3,13.1193681,80.1941553,3,13.119385,80.1941007,3,13.11948,80.193995,3,13.1196517,80.1939917,3,13.1197633,80.1940067,3,13.1198933,80.19403,3,13.120035,80.194075,3,13.120145,80.1940983,3,13.1202338,80.1940664,3,13.120275,80.1940175,3,13.1203083,80.19382,3,13.1203383,80.1936383,3,13.1203333,80.19355,3,13.1203333,80.1934383,3,13.1203783,80.1933933,3,13.120535,80.1933817,3,13.1206617,80.19336,3,13.1208617,80.1933233,3,13.120995,80.1933283,3,13.1211017,80.1933317,3,13.12128,80.1933217,3,13.12136,80.193325,3,13.1215383,80.193315,3,13.1216567,80.1933133,3,13.1217983,80.1933133,3,13.1220217,80.1932983,3,13.1221517,80.19329,3,13.12233,80.1932717,3,13.1224217,80.1932617,3,13.1225083,80.1932567,3,13.1227217,80.1932617,3,13.1228633,80.1932683,3,13.1230617,80.193275,3,13.123185,80.1932917,3,13.1233183,80.1933233,3,13.1234183,80.1933367,3,13.1235883,80.1933733,3,13.12378,80.1933767,3,13.1238933,80.1933483,3,13.1240324,80.1932971,3,13.1240448,80.1932227,3,13.1240283,80.1931233,3,13.1240317,80.192945,3,13.12403,80.19274,3,13.1240183,80.1924733,3,13.124015,80.192255,3,13.12401,80.1920833,3,13.1240083,80.1919883,3,13.1239767,80.1918317,3,13.1239467,80.1917317,3,13.123885,80.191655,3,13.123745,80.1915733,3,13.1236383,80.1915583,3,13.1235134,80.1915456,3,13.1234301,80.1915417,3,13.123353,80.1915381,3,13.1233048,80.1915209,3,13.1232786,80.1915223,3,13.1232656,80.1915335,3,13.123263,80.1915301,3,13.123262,80.1915287,3,13.1232618,80.1915284,3,13.1232617,80.1915284,3,13.1232617,80.1915283,3,13.1232617,80.1915283,3,13.1232617,80.1915283,3,13.1232617,80.1915283,3,13.1232617,80.1915283,3,13.1233136,80.1915276,3,13.123331,80.1915408,3);
		$count=1;
		foreach ($d as $k => $v) {
			if ($count%3 == 1) {
				$d1[] = $v;
			}elseif ($count%3 == 2) {
				$d2[] = $v;
			}else{
				$d3[] = $v;
			}
			$count++;
		}
		//$result_new = '';
		$lat = '';
		$lng = '';
		$result_new = array();
		for($i=0; $i<count($d1); $i++){
			if($d3[$i] == 3){
				
				if(!empty($d1[$i+1]) && $d2[$i+1]){
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i]);			
				}else{
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => 13.122213, 'end_lng' => 80.190119, 'status' => $d3[$i]);				
				}
				$result_new[] = array('lat' => $d1[$i], 'lng' => $d2[$i]);	
				$lat .= '<item>'.$d1[$i].'</item>';	
				$lng .= '<item>'.$d2[$i].'</item>';		
				//$result_n .= '"'.$d1[$i].','.$d2[$i].'",';
				//echo $i.'<br>';
				//$result_new .= '%7C'.$d1[$i].'%2C'.$d2[$i];
				
				//$result_new .= $d2[$i].','.$d1[$i].';';
				
			}
		}
		//echo $lat;
		//die;
		echo json_encode($result_new);
		die;
		foreach($result as $res){
			
			$distance += $this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']);	
		}
		echo round($distance, 1);
		//echo $result_n;
		die;
		//echo $result_new;die;
		//80.191368,13.123521;-122.45,37.91;-122.48,37.73
		
		
		//foreach($result as $res){
			
			//$distance[] = $this->site->GetDrivingDistanceNew1($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng'], $unit = 'km', $decimals = 10);	
		//}
			//echo 'https://api.mapbox.com/directions-matrix/v1/mapbox/driving/78.0778515,11.1860923;78.0778515,11.1860923;78.0778515,11.1860923;78.0778515,11.1860923;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0706045,11.1797535;78.0705858,11.1797745;78.070566,11.1797967;78.0700946,11.1802958;78.0689122,11.1815436;78.0688869,11.1815703;78.0687136,11.1817531;78.0183898,11.1334078;78.0181718,11.1323991;78.0180028,11.1313705;78.0177959,11.1302881;78.0174716,11.1292082;78.0171119,11.1281593;78.0168029,11.1271755;78.0166673,11.126207;78.0166845,11.1252141;78.0168452,11.1242038;78.0170041,11.1231494;78.0171534,11.1220734;78.0173027,11.1209957;78.0174788,11.1199247;78.0176154,11.1188174;78.0176718,11.1176926;78.0175065,11.1165796;78.0174467,11.1154389;78.0173024,11.1143539;78.0171596,11.1132369;78.0168969,11.112126;78.0167246,11.11098;78.016561,11.1098684;78.0163949,11.1088128;78.016153,11.1077773;78.0158581,11.1067624;78.0153348,11.105785;78.0146548,11.1048692;78.0138197,11.1040714;78.0128418,11.1033821;78.0117583,11.1028721;78.0106095,11.1025154;78.0094207,11.102274;78.0082529,11.1019854;78.007087,11.1017184;78.006072,11.1012847;78.0053905,11.1006853;78.0049057,11.0998988;78.0045962,11.0988992;78.004326,11.0978162;78.0040559,11.0967206;78.0037894,11.0956104;78.0035315,11.0944893;78.0032629,11.0934034;78.0029915,11.0923244;78.0027364,11.091255;78.0025465,11.0902087;78.0025105,11.089221;78.0026369,11.0884116;78.0027312,11.0879269;78.002816,11.0874751;78.0029699,11.0868751;78.0030836,11.0860999;78.0031313,11.0852013;78.0031879,11.084262;78.0032315,11.0835094;78.0032902,11.0830824;78.0033114,11.0828266;78.0034654,11.0824659;78.0037348,11.0819561;78.004186,11.0813264;78.0047696,11.0806394;78.0053789,11.079881;78.0060557,11.079068;78.006791,11.0782163;78.0075243,11.0773049;78.0080101,11.0765354;78.0082486,11.0757516;78.0083333,11.0747984;78.0082903,11.0737399;78.0082754,11.0726176;78.0082818,11.0714769;78.0082619,11.0704105;78.0085825,11.0695116;78.0091065,11.0687134;78.0097783,11.0681266;78.0105956,11.067597;78.0115175,11.0670631;78.0124705,11.0664976;78.0134595,11.0659276;78.0144266,11.0653712;78.0153781,11.0648062;78.0163469,11.0642372;78.017303,11.0636693;78.0182913,11.0631701;78.0193252,11.0627539;78.0203403,11.0623251;78.0213409,11.0619371;78.0223469,11.0614935;78.0233566,11.0610516;78.0243215,11.0605999;78.0253151,11.0601196;78.0263489,11.0596599;78.0273659,11.0591618;78.0284153,11.05865;78.0294295,11.0581674;78.0303195,11.057734;78.0312265,11.0572862;78.0321573,11.0567988;78.0331316,11.0563217;78.0341468,11.0558495;78.035185,11.0554324;78.0362606,11.0551056;78.0373734,11.0548587;78.0384948,11.054665;78.0395967,11.0544901;78.0406923,11.0543348;78.0417817,11.0541814;78.0428653,11.0539987;78.04396,11.0538726;78.0450531,11.053694;78.0461514,11.0535261;78.047274,11.0533622;78.0483,11.0531732;78.0493503,11.0530128;78.0504148,11.0528666;78.0513608,11.0526132;78.0522088,11.0521304;78.0529233,11.0514108;78.0535476,11.0505323;78.0540925,11.049551;78.0547294,11.048602;78.0553809,11.0476869;78.0558335,11.0467561;78.0563051,11.045811;78.0567674,11.0448443;78.057215,11.0440261;78.0575764,11.04325;78.0580198,11.0423785;78.0585304,11.0414577;78.0590762,11.0405393;78.0596285,11.039591;78.0601151,11.0385735;78.0606454,11.0376391;78.0610084,11.0367061;78.061358,11.0357158;78.0619259,11.034796;78.0626509,11.0339694;78.0633667,11.0331287;78.0641034,11.0322532;78.0646947,11.031354;78.0649473,11.0304193;78.065137,11.0294021;78.065311,11.0283072;78.0654769,11.0273156;78.06562,11.0263932;78.0657875,11.0254056;78.0659581,11.0243286;78.0661069,11.0231939;78.0661208,11.0220986;78.0661459,11.0209966;78.0661822,11.0198884;78.0661892,11.0188001;78.0663001,11.0177464;78.0666416,11.0167645;78.0670179,11.0157825;78.0674039,11.0147868;78.0678057,11.013784;78.0681883,11.0128057;78.068523,11.0119536;78.0687986,11.0111201;78.068948,11.0102686;78.0689353,11.0094407;78.068788,11.0086393;78.0686756,11.0078402;78.0685357,11.0070298;78.0684132,11.0061804;78.0682714,11.0053004;78.0681443,11.0043574;78.067814,11.0023874;78.0676414,11.0013693;78.0674983,11.000344;78.0673802,10.999286;78.0671816,10.998228;78.0670401,10.9971488;78.0668578,10.9960545;78.0667014,10.9949471;78.0665393,10.9938569;78.0663633,10.9927676;78.0661764,10.9916529;78.065996,10.9905273;78.0658024,10.9893999;78.0656001,10.988259;78.0653925,10.9871064;78.0651849,10.9859646;78.0649764,10.9848039;78.0648057,10.9836214;78.0646091,10.9824673;78.0643874,10.9813631;78.0642132,10.9802987;78.0640536,10.9793184;78.0638756,10.978342;78.0636925,10.9773055;78.0635257,10.9763002;78.0634057,10.9755469;78.0633003,10.9748798;78.0631471,10.974094;78.0629783,10.9731839;78.0628285,10.9722756;78.062676,10.9714765;78.0625932,10.9707443;78.062498,10.9700617;78.0623447,10.9693116;78.0621581,10.968529;78.0620146,10.9677165;78.0618673,10.96691;78.0617214,10.9660437;78.0615536,10.9651337;78.0613794,10.9642245;78.0612279,10.96331;78.0610579,10.9624037;78.0608606,10.9614747;78.060654,10.9605467;78.0604551,10.9596323;78.0602433,10.9587259;78.0600534,10.9578004;78.0598686,10.9568834;78.0597079,10.9559623;78.0595232,10.9550513;78.0593515,10.9542193;78.0592027,10.9533235;78.0590302,10.9524949;78.058908,10.9518134;78.058816,10.951201;78.0586539,10.9505329;78.0585243,10.9499474;78.0584216,10.9494159;78.0583047,10.9487636;78.058228,10.9481135;78.0580945,10.9473583;78.0579537,10.9465156;78.057815,10.945613;78.0576244,10.9447006;78.0574794,10.9438491;78.0571942,10.9425636;78.057043,10.9419211;78.0568778,10.9413052;78.056772,10.9407743;78.0566825,10.940275;78.0565463,10.9398438;78.0563909,10.9392096;78.0562165,10.9385045;78.0560731,10.9377831;78.0558507,10.9369956;78.055679,10.9362929;78.0555235,10.9355418;78.0553393,10.934763;78.0551812,10.9340571;78.0549857,10.9332621;78.0548704,10.9325178;78.0547223,10.9316058;78.0545481,10.9307517;78.054377,10.9298828;78.0539618,10.9290867;78.0534649,10.9283739;78.0529484,10.9277014;78.0522615,10.9270827;78.0514444,10.9265407;78.0505635,10.926057;78.0497044,10.9255867;78.0489257,10.9251813;78.0483251,10.9247568;78.0476674,10.9241902;78.0469626,10.9235104;78.0461422,10.9229001;78.0455132,10.9221113;78.0450541,10.9212004;78.0446259,10.9203512;78.044168,10.9195052;78.0437517,10.9187871;78.0430569,10.9174071;78.0429106,10.9167258;78.0427847,10.9158232;78.0425375,10.9148816;78.0420849,10.9140888;78.041602,10.9132476;78.0409875,10.9123483;78.0404404,10.91147;78.0398885,10.9106694;78.0392688,10.9098449;78.0386353,10.908998;78.0379994,10.9081384;78.0373685,10.9072758;78.0368045,10.9063962;78.0363218,10.9055006;78.0358393,10.9046215;78.035352,10.9037601;78.0348717,10.9029089;78.0343973,10.9021493;78.0338772,10.9014179;78.0331531,10.9008322;78.0323003,10.9003513;78.0314041,10.8998755;78.0307189,10.899468;78.0302117,10.8990733;78.0298394,10.8986487;78.0295629,10.8981507;78.0292446,10.8976062;78.0288778,10.8970224;78.0285462,10.8964758;78.028313,10.8960681;78.0282426,10.8959142;78.0282098,10.8958754;78.0282052,10.895865;78.0282053,10.8958659;78.0282061,10.8958665;78.0282061,10.8958667;78.0282062,10.8958669;78.0282075,10.895864;78.028211,10.8958636;78.0282127,10.8958645;78.0282135,10.8958648;78.0282145,10.8958674;78.0282147,10.8958675;78.0282148,10.8958675;78.0282148,10.8958676;78.0282148,10.8958676;78.0282148,10.8958676?approaches=curb;curb;curb&access_token=pk.eyJ1Ijoic3BpZHluZXRzIiwiYSI6ImNqdWdpOGl5YzBpZDQ0NHJ1Z2ljcHlscmMifQ.wWAHuLIjfN8MFVo1b0gJWA';
		//$data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=11.1860923,78.0778515&destinations=".$result_new."&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8");
		
		//https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=YOUR_API_KEY
		//https://maps.googleapis.com/maps/api/place/autocomplete/xml?input=Amoeba&types=establishment&location=37.76999,-122.44696&radius=500&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8
		
		//$this->site->GetDrivingDistanceNew();
		//$data = file_get_contents('https://api.mapbox.com/directions-matrix/v1/mapbox/driving/78.0778515,11.1860923;78.0778515,11.1860923;78.0778515,11.1860923;78.0778515,11.1860923;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0715586,11.1786831;78.0706045,11.1797535;78.0705858,11.1797745;78.070566,11.1797967;78.0700946,11.1802958;78.0689122,11.1815436;78.0688869,11.1815703;78.0687136,11.1817531;78.0687136,11.1817531;78.0687136,11.1817531;78.0687136,11.1817531;78.0661975,11.180804;78.0653559,11.1803527;78.0646204,11.1800125;78.0638844,11.1796337;78.0631297,11.1792436;78.0623466,11.1788361;78.0615995,11.1784707;78.0608315,11.178156;78.0600594,11.1778318;78.0592199,11.1774989;78.0583802,11.1771821;78.0575277,11.1768302;78.0567147,11.1764841;78.0560014,11.1761838;78.0553013,11.175913;78.0545693,11.1755596;78.0538194,11.1752333;78.0530602,11.1748856;78.0522547,11.1745595;78.0514483,11.1742587;78.050637,11.1739376;78.049922,11.1736662;78.049127,11.1733425;78.0483075,11.1729551;78.047485,11.1725758;78.0466248,11.1721917;78.0457326,11.171798;78.044798,11.1714827;78.043821,11.1712809;78.0428024,11.1711463;78.0417372,11.1709302;78.0406672,11.1706376;78.0396752,11.1702875;78.0387283,11.1698978;78.0377729,11.1694575;78.0367997,11.1690309;78.0357436,11.1686508;78.034681,11.1682911;78.0336419,11.1680063;78.0326165,11.1676795;78.0317306,11.1671954;78.030978,11.1665244;78.0303598,11.1656981;78.0297307,11.1648814;78.0290462,11.1641474;78.0283594,11.1633963;78.0276611,11.1626501;78.0271117,11.1618844;78.0267143,11.1611779;78.0264282,11.1604878;78.0261509,11.1598159;78.0259062,11.1591837;78.0256323,11.1585398;78.0253655,11.1578849;78.0250927,11.1572142;78.0248288,11.1565087;78.0246792,11.1558442;78.0246175,11.155154;78.0245965,11.1545163;78.024579,11.1539271;78.0245752,11.1533861;78.0245789,11.1529261;78.0245628,11.1524819;78.024537,11.1521214;78.0244684,11.1516871;78.0244948,11.1511205;78.0244977,11.1505353;78.0244953,11.1499407;78.0244691,11.1492977;78.0244574,11.1486373;78.0244241,11.147918;78.0243008,11.1471531;78.0240533,11.1463784;78.0235831,11.145607;78.0229869,11.1448629;78.0223806,11.1440983;78.0218267,11.1433278;78.0213658,11.1425118;78.0210691,11.1416528;78.020784,11.1407521;78.0203948,11.1398148;78.0199721,11.1388327;78.0195443,11.1378553;78.0192098,11.1369064;78.0190188,11.136229;78.018921,11.1357297;78.0187917,11.1351019;78.0186154,11.1343107;78.0183898,11.1334078;78.0181718,11.1323991;78.0180028,11.1313705;78.0177959,11.1302881;78.0174716,11.1292082;78.0171119,11.1281593;78.0168029,11.1271755;78.0166673,11.126207;78.0166845,11.1252141;78.0168452,11.1242038;78.0170041,11.1231494;78.0171534,11.1220734;78.0173027,11.1209957;78.0174788,11.1199247;78.0176154,11.1188174;78.0176718,11.1176926;78.0175065,11.1165796;78.0174467,11.1154389;78.0173024,11.1143539;78.0171596,11.1132369;78.0168969,11.112126;78.0167246,11.11098;78.016561,11.1098684;78.0163949,11.1088128;78.016153,11.1077773;78.0158581,11.1067624;78.0153348,11.105785;78.0146548,11.1048692;78.0138197,11.1040714;78.0128418,11.1033821;78.0117583,11.1028721;78.0106095,11.1025154;78.0094207,11.102274;78.0082529,11.1019854;78.007087,11.1017184;78.006072,11.1012847;78.0053905,11.1006853;78.0049057,11.0998988;78.0045962,11.0988992;78.004326,11.0978162;78.0040559,11.0967206;78.0037894,11.0956104;78.0035315,11.0944893;78.0032629,11.0934034;78.0029915,11.0923244;78.0027364,11.091255;78.0025465,11.0902087;78.0025105,11.089221;78.0026369,11.0884116;78.0027312,11.0879269;78.002816,11.0874751;78.0029699,11.0868751;78.0030836,11.0860999;78.0031313,11.0852013;78.0031879,11.084262;78.0032315,11.0835094;78.0032902,11.0830824;78.0033114,11.0828266;78.0034654,11.0824659;78.0037348,11.0819561;78.004186,11.0813264;78.0047696,11.0806394;78.0053789,11.079881;78.0060557,11.079068;78.006791,11.0782163;78.0075243,11.0773049;78.0080101,11.0765354;78.0082486,11.0757516;78.0083333,11.0747984;78.0082903,11.0737399;78.0082754,11.0726176;78.0082818,11.0714769;78.0082619,11.0704105;78.0085825,11.0695116;78.0091065,11.0687134;78.0097783,11.0681266;78.0105956,11.067597;78.0115175,11.0670631;78.0124705,11.0664976;78.0134595,11.0659276;78.0144266,11.0653712;78.0153781,11.0648062;78.0163469,11.0642372;78.017303,11.0636693;78.0182913,11.0631701;78.0193252,11.0627539;78.0203403,11.0623251;78.0213409,11.0619371;78.0223469,11.0614935;78.0233566,11.0610516;78.0243215,11.0605999;78.0253151,11.0601196;78.0263489,11.0596599;78.0273659,11.0591618;78.0284153,11.05865;78.0294295,11.0581674;78.0303195,11.057734;78.0312265,11.0572862;78.0321573,11.0567988;78.0331316,11.0563217;78.0341468,11.0558495;78.035185,11.0554324;78.0362606,11.0551056;78.0373734,11.0548587;78.0384948,11.054665;78.0395967,11.0544901;78.0406923,11.0543348;78.0417817,11.0541814;78.0428653,11.0539987;78.04396,11.0538726;78.0450531,11.053694;78.0461514,11.0535261;78.047274,11.0533622;78.0483,11.0531732;78.0493503,11.0530128;78.0504148,11.0528666;78.0513608,11.0526132;78.0522088,11.0521304;78.0529233,11.0514108;78.0535476,11.0505323;78.0540925,11.049551;78.0547294,11.048602;78.0553809,11.0476869;78.0558335,11.0467561;78.0563051,11.045811;78.0567674,11.0448443;78.057215,11.0440261;78.0575764,11.04325;78.0580198,11.0423785;78.0585304,11.0414577;78.0590762,11.0405393;78.0596285,11.039591;78.0601151,11.0385735;78.0606454,11.0376391;78.0610084,11.0367061;78.061358,11.0357158;78.0619259,11.034796;78.0626509,11.0339694;78.0633667,11.0331287;78.0641034,11.0322532;78.0646947,11.031354;78.0649473,11.0304193;78.065137,11.0294021;78.065311,11.0283072;78.0654769,11.0273156;78.06562,11.0263932;78.0657875,11.0254056;78.0659581,11.0243286;78.0661069,11.0231939;78.0661208,11.0220986;78.0661459,11.0209966;78.0661822,11.0198884;78.0661892,11.0188001;78.0663001,11.0177464;78.0666416,11.0167645;78.0670179,11.0157825;78.0674039,11.0147868;78.0678057,11.013784;78.0681883,11.0128057;78.068523,11.0119536;78.0687986,11.0111201;78.068948,11.0102686;78.0689353,11.0094407;78.068788,11.0086393;78.0686756,11.0078402;78.0685357,11.0070298;78.0684132,11.0061804;78.0682714,11.0053004;78.0681443,11.0043574;78.067814,11.0023874;78.0676414,11.0013693;78.0674983,11.000344;78.0673802,10.999286;78.0671816,10.998228;78.0670401,10.9971488;78.0668578,10.9960545;78.0667014,10.9949471;78.0665393,10.9938569;78.0663633,10.9927676;78.0661764,10.9916529;78.065996,10.9905273;78.0658024,10.9893999;78.0656001,10.988259;78.0653925,10.9871064;78.0651849,10.9859646;78.0649764,10.9848039;78.0648057,10.9836214;78.0646091,10.9824673;78.0643874,10.9813631;78.0642132,10.9802987;78.0640536,10.9793184;78.0638756,10.978342;78.0636925,10.9773055;78.0635257,10.9763002;78.0634057,10.9755469;78.0633003,10.9748798;78.0631471,10.974094;78.0629783,10.9731839;78.0628285,10.9722756;78.062676,10.9714765;78.0625932,10.9707443;78.062498,10.9700617;78.0623447,10.9693116;78.0621581,10.968529;78.0620146,10.9677165;78.0618673,10.96691;78.0617214,10.9660437;78.0615536,10.9651337;78.0613794,10.9642245;78.0612279,10.96331;78.0610579,10.9624037;78.0608606,10.9614747;78.060654,10.9605467;78.0604551,10.9596323;78.0602433,10.9587259;78.0600534,10.9578004;78.0598686,10.9568834;78.0597079,10.9559623;78.0595232,10.9550513;78.0593515,10.9542193;78.0592027,10.9533235;78.0590302,10.9524949;78.058908,10.9518134;78.058816,10.951201;78.0586539,10.9505329;78.0585243,10.9499474;78.0584216,10.9494159;78.0583047,10.9487636;78.058228,10.9481135;78.0580945,10.9473583;78.0579537,10.9465156;78.057815,10.945613;78.0576244,10.9447006;78.0574794,10.9438491;78.0571942,10.9425636;78.057043,10.9419211;78.0568778,10.9413052;78.056772,10.9407743;78.0566825,10.940275;78.0565463,10.9398438;78.0563909,10.9392096;78.0562165,10.9385045;78.0560731,10.9377831;78.0558507,10.9369956;78.055679,10.9362929;78.0555235,10.9355418;78.0553393,10.934763;78.0551812,10.9340571;78.0549857,10.9332621;78.0548704,10.9325178;78.0547223,10.9316058;78.0545481,10.9307517;78.054377,10.9298828;78.0539618,10.9290867;78.0534649,10.9283739;78.0529484,10.9277014;78.0522615,10.9270827;78.0514444,10.9265407;78.0505635,10.926057;78.0497044,10.9255867;78.0489257,10.9251813;78.0483251,10.9247568;78.0476674,10.9241902;78.0469626,10.9235104;78.0461422,10.9229001;78.0455132,10.9221113;78.0450541,10.9212004;78.0446259,10.9203512;78.044168,10.9195052;78.0437517,10.9187871;78.0430569,10.9174071;78.0429106,10.9167258;78.0427847,10.9158232;78.0425375,10.9148816;78.0420849,10.9140888;78.041602,10.9132476;78.0409875,10.9123483;78.0404404,10.91147;78.0398885,10.9106694;78.0392688,10.9098449;78.0386353,10.908998;78.0379994,10.9081384;78.0373685,10.9072758;78.0368045,10.9063962;78.0363218,10.9055006;78.0358393,10.9046215;78.035352,10.9037601;78.0348717,10.9029089;78.0343973,10.9021493;78.0338772,10.9014179;78.0331531,10.9008322;78.0323003,10.9003513;78.0314041,10.8998755;78.0307189,10.899468;78.0302117,10.8990733;78.0298394,10.8986487;78.0295629,10.8981507;78.0292446,10.8976062;78.0288778,10.8970224;78.0285462,10.8964758;78.028313,10.8960681;78.0282426,10.8959142;78.0282098,10.8958754;78.0282052,10.895865;78.0282053,10.8958659;78.0282061,10.8958665;78.0282061,10.8958667;78.0282062,10.8958669;78.0282075,10.895864;78.028211,10.8958636;78.0282127,10.8958645;78.0282135,10.8958648;78.0282145,10.8958674;78.0282147,10.8958675;78.0282148,10.8958675;78.0282148,10.8958676;78.0282148,10.8958676;78.0282148,10.8958676?approaches=curb;curb;curb&access_token=pk.eyJ1Ijoic3BpZHluZXRzIiwiYSI6ImNqdWdpOGl5YzBpZDQ0NHJ1Z2ljcHlscmMifQ.wWAHuLIjfN8MFVo1b0gJWA');
		//echo '<pre>';
		//var_dump($data);
		// header('Location: http://localhost/kapp/main/getpath/'.$var);
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
		$this->form_validation->set_rules('from_lat', $this->lang->line("from_lat"), 'required');
		$this->form_validation->set_rules('from_lng', $this->lang->line("from_lng"), 'required');
		
		$this->form_validation->set_rules('to_lat', $this->lang->line("to_lat"), 'required');
		$this->form_validation->set_rules('to_lng', $this->lang->line("to_lng"), 'required');
		
		if ($this->form_validation->run() == true) {
			
			$from_city = $this->site->getCityFare($this->input->post('from_lat'), $this->input->post('from_lng'));
			$to_city = $this->site->getCityFare($this->input->post('to_lat'), $this->input->post('to_lng'));
			
			
			$setting = $this->customer_api->getSettingmode($countryCode);
			
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('from_lat'), $this->input->post('from_lng'), $this->input->post('to_lat'), $this->input->post('to_lng'), $countryCode);
			
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
	
	
	public function truckwiserental_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('from_lat', $this->lang->line("from_lat"), 'required');
		$this->form_validation->set_rules('from_lng', $this->lang->line("from_lng"), 'required');
		$this->form_validation->set_rules('cab_type_id', $this->lang->line("cab_type"), 'required');

		if ($this->form_validation->run() == true) {
			
			$city = $this->site->getCityFare($this->input->post('from_lat'), $this->input->post('from_lng'));
			
			$data = $this->customer_api->getTruckRental($city, $this->input->post('cab_type_id'), $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
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
	
	
	public function rentalpackage_post(){
		$data = array();
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
	
	public function shifting_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		$types = $this->customer_api->getShifting($countryCode);
		
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $types);
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
					$this->customer_api->insertNotification($notification1);
				}
				$notification['title'] = 'Ride Rating';
				$notification['message'] = $user_data->first_name.' has been ride rating.';
				$notification['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->customer_api->insertNotification($notification);
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
		$this->form_validation->set_rules('device_imei', $this->lang->line("device_imei"), 'required');
		$this->form_validation->set_rules('device_token', $this->lang->line("device_token"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');		
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
		$this->form_validation->set_rules('device_imei', $this->lang->line("device_imei"), 'required');
		$this->form_validation->set_rules('device_token', $this->lang->line("device_token"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');		
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
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}elseif($res->check_status == 3){
				$sms_phone_otp = $otp;
				$sms_phone = $res->country_code.$res->mobile;
				$sms_country_code = $res->country_code;
				
				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> 3 , 'message'=> 'OTP has been sent. Check it', 'data' => array('oauth_token' => $res->oauth_token));
				}else{
					$result = array( 'status'=> 3 , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => array('oauth_token' => $res->oauth_token));
				}
			}elseif($res->check_status == 4){
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
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
					$msg = 'Customer Details : '.$current_ride->customer_name.' Driver Details : '.$current_ride->driver_name.', Driver Location : '.$driverlocation.', Taxi Details : '.$current_ride->taxi_name.', '.$current_ride->taxi_number.', Pickup: '.$pickup.' Dropoff: '.$dropoff.' Click Here : http://13.233.109.60/sos?id='.$current_ride->booking;
					
					
					$response_sms = $this->sms_sos($msg, $em_res[0], $em_res[1]);
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
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'invaild otp. please check otp');
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
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['country_code'] = $this->input->post('country_code');
			$row['mobile'] = $this->input->post('mobile');
			$mobile_otp = random_string('numeric', 6);
			
			//$data = $this->customer_api->registerresendotp($row, $countryCode);
			if($mobile_otp){
				
				$sms_phone = $this->input->post('country_code') . $this->input->post('mobile');
				$sms_country_code = $this->input->post('country_code');
				$sms_phone_otp = $data->mobile_otp;

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $mobile_otp);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $mobile_otp);
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
					$notification['message'] = 'New user('.$this->input->post('first_name').') has been register vpiok.';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->customer_api->insertNotification($notification);
					
					$sms_message = $this->input->post('first_name').' your account has been register successfully. Your refer code : '.$refer_code.'';
					$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
					$sms_country_code = $this->input->post('country_code');

					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
					$result = array( 'status'=> 1 , 'message'=> 'Registered Successfully!..', 'data' => '1');
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
					$this->customer_api->insertNotification($notification);
					
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
					$this->customer_api->insertNotification($notification);
					
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

	
	
	public function truck_categorytypes_get(){
		$countryCode = $this->input->get('is_country');
		$data = array();
		$types = $this->customer_api->getCategorytypes();
		
		$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $types);
		$this->response($result);
	}
	
	
	public function truck_category_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		//$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$search_data = $this->site->insertSearch($this->input->post('latitude'), $this->input->post('longitude'), $countryCode);
			
			$settings = $this->customer_api->getSettings($countryCode);
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$outstanding_price = $this->customer_api->outstanding_price($user_data->id, $countryCode);
			
			$distance = 20;
			$radius = 3959;//6371;
			$val['latitude'] = $this->input->post('latitude');
			$val['longitude'] = $this->input->post('longitude');
			$val['distance'] = $distance; 
			
			//$data = $this->customer_api->getDriversnew_radius($val);
			
			$types = $this->customer_api->FaregetAllCatgeorytaxitype($this->input->post('latitude'), $this->input->post('longitude'), $this->input->post('category_id'), $distance, $countryCode);
			$default_image = site_url('assets/uploads/no_image.png');
			$default_url = site_url('assets/uploads/');
			
			//$checkZonal = $this->site->checkZonal($this->input->post('latitude'), $this->input->post('longitude'));
			//if($checkZonal == 1){
				if(!empty($types)){
					$data[0] = array(
						'category_id' => 0,
						'category_name' => 'All',
					);
					
					foreach($types as $type){
						
						
						$res = array(
							'category_id' => $type->category_id,
							'category_name' => $type->category_name,
						);
						
						$data[] = $res;
					}
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty');
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
	
	public function truck_types_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		//$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		if ($this->form_validation->run() == true) {
			
			$search_data = $this->site->insertSearch($this->input->post('latitude'), $this->input->post('longitude'), $countryCode);
			
			$settings = $this->customer_api->getSettings($countryCode);
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$outstanding_price = $this->customer_api->outstanding_price($user_data->id, $countryCode);
			
			$distance = 20;
			$radius = 3959;//6371;
			$val['latitude'] = $this->input->post('latitude');
			$val['longitude'] = $this->input->post('longitude');
			$val['distance'] = $distance; 
			
			//$data = $this->customer_api->getDriversnew_radius($val);
			
			$types = $this->customer_api->FaregetAllCatgeorytaxitype($this->input->post('latitude'), $this->input->post('longitude'), $this->input->post('category_id'), $distance, $countryCode);
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
						
						$res = array(
							'type_id' => $type->id,
							'name' => $type->name,
							'image' => $type->image,
							'image_hover' => $type->image_hover,
							'available' => $type->available,
							'current_longitude' => $type->current_longitude,
							'current_latitude' => $type->current_latitude,
							'min_price' => $type->min_price,
							'min_distance' => $type->min_distance,
							'per_distance' => $type->per_distance,
							'per_distance_price' => $type->per_distance_price
						);
						
						$data[] = $res;
					}
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty');
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
	
	
	public function truck_typesnew_post(){
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
			
			$outstanding_price = $this->customer_api->outstanding_price($user_data->id, $countryCode);
			
			$distance = 20;
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
	/*public function truck_types_get(){
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
	}*/
	
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
	
	
	public function searchtrucks_post(){
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
	
	
	/*public function search_trucks_post(){
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
	}*/
	
	public function book_ride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('from_latitude', $this->lang->line("from_latitude"), 'required');
		$this->form_validation->set_rules('from_longitude', $this->lang->line("from_longitude"), 'required');
		if($this->input->post('booked_type') != 2){
			$this->form_validation->set_rules('to_latitude', $this->lang->line("to_latitude"), 'required');
			$this->form_validation->set_rules('to_longitude', $this->lang->line("to_longitude"), 'required');
		}
		$this->form_validation->set_rules('cab_type_id', $this->lang->line("cab_type_id"), 'required');
		//$this->form_validation->set_rules('ride_type', $this->lang->line("ride_type"), 'required');
		$this->form_validation->set_rules('booked_type', $this->lang->line("booked_type"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$settings = $this->customer_api->getSettings($countryCode);
			$setting = $this->customer_api->getSettingmode($countryCode);
			
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('from_latitude'), $this->input->post('from_longitude'), $this->input->post('to_latitude'), $this->input->post('to_longitude'), $countryCode);
			
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
				$outstation_type = 0;
				$outstation_way = 0;
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
			
			//if($this->input->post('ride_type') == 1){
				$driver_data = $this->customer_api->getDrivers_radius($val, $countryCode);
			//}else{
				//$driver_data = 'Ride_later';
			//}
			
			
			
			
			if(!empty($driver_data)){
					
					if($check_km){
					
				
						$driver_allocated = 0;
					
					
					$ride_type = 1;
					if($ride_type == 1){
						$ride_otp = random_string('numeric', 6);
						$status = 1;
						$ride_timing = date('Y-m-d H:i:').':00';
						$ride_timing_end = '0000-00-00 00:00:00';
					}else{
						$ride_timing = $this->input->post('ride_timing').':00';
						if($this->input->post('booking_type') == 3){
							//$ride_timing_end = $this->input->post('ride_timing_end').':00';
							$ride_timing_end = '0000-00-00 00:00:00';
						}else{
							$ride_timing_end = '0000-00-00 00:00:00';
						}
						$status = 7;
						$ride_otp =  0;
					}
					
					if($ride_type == 2 && $ride_timing){
						//echo $timing = date('H', strtotime($ride_timing));
						
						
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
							'rental_id' => $package_id,
							'no_of_labour' => $this->input->post('no_of_labour'),
							'shifting_id' => $this->input->post('shifting_id'),
							'booked_by' => $user_data->id,
							'booked_type' => $this->input->post('booked_type'),
							'booked_on' => date('Y-m-d H:i:s'),               
							'booking_timing' => date('Y-m-d H:i').':00',
							'ride_timing' => $ride_timing,
							'ride_timing_end' => $ride_timing_end,
							'ride_type' => 1,
							'start' => $this->input->post('pick_up') ? $this->input->post('pick_up') : '0',
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
						$offer_code = $this->input->post('offer_code') ? $this->input->post('offer_code') : '';
						
										
						//if($check_status == TRUE){
								
							$data[] = $this->customer_api->add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $user_data->id, $offer_code);
							$payment_name = $this->customer_api->getPaymentName($this->input->post('payment_id'), $countryCode);
							if($data[0] != 0){
								
								if($ride_type == 1){
									
									$notification['title'] = 'Ride Booking';
									$notification['message'] = $user_data->first_name.' has been ride booked.';
									$notification['user_type'] = 4;
									$notification['user_id'] = 2;
									$this->customer_api->insertNotification($notification);
									
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
										'socket_id' => $socket_id
										
									);
									$success = 	$this->socketemitter->setEmit($event, $edata);
									$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $data[0]);
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
									
									$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait');
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
					);
					$result = array( 'status'=> 1 , 'message'=> 'Ride Booked', 'data' => $row);
				}elseif($data[0]->status == 3){
					$onstatus = array(
						'sos' => "http://13.233.109.60/sos?id=".$data[0]->ride_id,
						'customer_id' => $user_data->id,
						'booking_id' => $data[0]->ride_id,
						'from_location' => $this->site->findLocation($data[0]->start_lat, $data[0]->start_lng),
						'to_location' => $this->site->findLocation($data[0]->end_lat, $data[0]->end_lng),	
						'start_lat' => $data[0]->start_lat,
						'start_lng' => $data[0]->start_lng,
						'end_lat' => $data[0]->end_lat,
						'end_lng' => $data[0]->end_lng,
						'total_km' => $loc['distance'] ? $loc['distance'] : 0,	
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
	
		
	public function customercancel_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('cancel_msg', $this->lang->line("cancel_msg"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$cancel['customer_id'] = $user_data->id;
			$cancel['booking_id'] = $this->input->post('booking_id');
			$cancel['cancel_msg'] = $this->input->post('cancel_msg');
			$rides = $this->customer_api->getRideBYID($this->input->post('booking_id'));
			$cancel['driver_id'] = $rides->driver_id; 
			$data = $this->customer_api->customerCancel($cancel, $countryCode);
			
			if(!empty($rides->driver_id)){
				
				$customer_data = $this->customer_api->getCustomerID($user_data->id, $countryCode);
				$driver_data = $this->customer_api->getDriverID($rides->driver_id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				//$driver_phone = $driver_data->country_code.$driver_data->mobile;
				
				if($rides->driver_id != 2){
					$notification['title'] = 'Ride Cancel';
					$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
					$notification['user_type'] = 2;
					$notification['user_id'] = $rides->driver_id;
					$this->customer_api->insertNotification($notification);
				}
				$notification1['title'] = 'Ride Cancel';
				$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				$notification1['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->customer_api->insertNotification($notification1, $countryCode);
								
				$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
				$event = 'server_ride_cancel';
				$edata = array(
					
					'title' => 'Ride Cancel',
					'message' => 'Ride has been cancelled by customer. cancel reason : '.$this->input->post('cancel_msg').'',					
					'socket_id' => $socket_id
					
				);
				
				$success = 	$this->socketemitter->setEmit($event, $edata);
				$sms_message = 'Ride has been cancelled by driver('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				$sms_phone = $customer_data->mobile;
				$sms_country_code = $customer_data->country_code;
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
			}
			
			if($data == true){
				$result = array( 'status'=> true , 'message'=> 'Ride has been cancelled');
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
	
	
}
