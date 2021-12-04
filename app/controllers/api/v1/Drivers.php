<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Drivers extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('drivers_api');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->library('upload');
        //$this->upload_path = 'assets/uploads/customers/';
        //$this->thumbs_path = 'assets/uploads/customers/thumbs/';
        $this->image_types = 'gif|jpg|png|jpeg|pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->library('socketemitter');
		$this->getUserIpAddr = $this->site->getUserIpAddr();
		
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
	}
	
	
	public function language_get(){
		$scanned_lang_dir = array_map(function ($path) {
			return basename($path);
		}, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
		for($i=0; $i<count($scanned_lang_dir); $i++) {
			$data[] = array('lang' => $scanned_lang_dir[$i]);	
		}
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> lang('success'), 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> lang('empty_data'));
		}

		$this->response($result);
	}
	
	function test_post(){
		echo $this->drivers_api->test();	
	}
	
	function demodata_post(){
		$event = 'server_notification';
				$edata = array(
					
					'title' => 'Notif',
					
				);
				
				$success = 	$this->socketemitter->setEmit($event, $edata);	
				if($success){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success');
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Not Update');
			}
			$this->response($result);
	}
	
	function commonsetting_post(){
		
		$countryCode = $this->input->post('is_country');
		//$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			//$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->drivers_api->getSettings($countryCode);
			
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
	
	function registersetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->drivers_api->getSettings($countryCode);
			
			$data = array(
				'address_enable' => $res->{'address_enable'},
				'aadhaar_enable' => $res->{'aadhaar_enable'},
				'pancard_enable' => $res->{'pancard_enable'},
				'license_enable' => $res->{'license_enable'},
				'police_enable' => $res->{'police_enable'},	
				'badge_enable' => $res->{'badge_enable'},
				'training_certificate_enable' => $res->{'training_certificate_enable'},
				'experience_certificate_enable' => $res->{'experience_certificate_enable'},
				'health_insurance_enable' => $res->{'health_insurance_enable'},
				'term_insurance_enable' => $res->{'term_insurance_enable'},
				'additional_contact_enable' => $res->{'additional_contact_enable'},
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
	
	function banksetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->drivers_api->getSettings($countryCode);
			
			$data = array(
				'account_holder_name_enable' => $res->{'account_holder_name_enable'},
				'bank_name_enable' => $res->{'bank_name_enable'},
				'branch_name_enable' => $res->{'branch_name_enable'},
				'ifsc_code_enable' => $res->{'ifsc_code_enable'},
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
	
	function cabsetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->drivers_api->getSettings($countryCode);
			
			$data = array(
				'cab_registration_enable' => $res->{'cab_registration_enable'},
				'taxation_enable' => $res->{'taxation_enable'},
				'insurance_enable' => $res->{'insurance_enable'},
				'permit_enable' => $res->{'permit_enable'},
				'authorisation_enable' => $res->{'authorisation_enable'},
				'fitness_enable' => $res->{'fitness_enable'},
				'puc_enable' => $res->{'puc_enable'},
				'speed_enable' => $res->{'speed_enable'},
				'emission_norms_enable' => $res->{'emission_norms_enable'},
				'vehicle_tracking_enable' => $res->{'vehicle_tracking_enable'},
				'fire_extinguisher_enable' => $res->{'fire_extinguisher_enable'},
				'child_lock_mechanism_enable' => $res->{'child_lock_mechanism_enable'},
				'interior_vehicle_enable' => $res->{'interior_vehicle_enable'},
				'taxi_roof_sign_enable' => $res->{'taxi_roof_sign_enable'},
				'e_challans_clearance_enable' => $res->{'e_challans_clearance_enable'},
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
	
	function updatesetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			//$row['oauth_token'] = $this->input->post('oauth_token');
		
			$res = $this->drivers_api->getUserSettings($user_data->id);
			
			$data_update = array(
				'ride_stop' => $this->input->post('ride_stop'),
				'incentive_auto_enable' => $this->input->post('incentive_auto_enable') != NULL ? $this->input->post('incentive_auto_enable') : $res->{'incentive_auto_enable'}
			);
			
			$s = $this->drivers_api->updateSetting($user_data->id, $data_update, $countryCode);
			if($s){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success');
				
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
	
	function incentivechange_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('incentive_id', $this->lang->line("incentive_id"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$user_id = $user_data->id;
			$incentive_id  = $this->input->post('incentive_id') ? $this->input->post('incentive_id') : 0;
			$incentive_data = $this->drivers_api->getIncentivebyID($incentive_id, $user_id, $countryCode);
			//print_r($incentive_data);
			//for($i=0; $i<count($_POST['payment_status']); $i++){
				$incentive = array(
					'incentive_id' => $_POST['incentive_id'],
					//'incentive_group_id' => $incentive_data->group_id, 
					'driver_id' => $user_id,
					'incentive_type' => $incentive_data->type,
					'target_fare' => $incentive_data->target_fare,
					'target_ride' => $incentive_data->target_ride,
					'status' => 0,
					'accept_date' => date('Y-m-d H:i:s'),
					'is_edit' => 1
				);
			//}
			
			$res = $this->drivers_api->changeIncentive($user_id, $incentive_id, $incentive, $countryCode);
			
			$msg = 'Your payment has been success. your transaction no : '.$transaction_no.', please wait for admin process.';
			$response_sms = $this->sms_sos($msg, $user_data->mobile, $user_data->country_code);
			
			
			if($res){
				
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
	
	function driverpaymentcollect_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('paid_amount', $this->lang->line("paid_amount"), 'required');
		$this->form_validation->set_rules('payment_status', $this->lang->line("payment_status"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			//for($i=0; $i<count($_POST['payment_status']); $i++){
				$transaction_no = 'TRANS'.date('YmdHis');
				$payment = array(
					//'driver_payment_id' => $_POST['driver_payment_id'],
					'driver_status' => 1, 
					'payment_status' => $_POST['payment_status'],
					'paid_amount' => $_POST['paid_amount'],
					'balance_amount' => 0,
					'payment_date' => date('d/m/YY'),
					'transaction_no' => $transaction_no,
					'payment_note' => $this->input->post('payment_note') ? $this->input->post('payment_note') : ''
				);
			//}
			
			$res = $this->drivers_api->getDriverPayment($user_data->id, $payment, $_POST['paid_amount'], $countryCode);
			
			$msg = 'Your payment has been success. your transaction no : '.$transaction_no.', please wait for admin process.';
			$response_sms = $this->sms_sos($msg, $user_data->mobile, $user_data->country_code);
			
			
			if($res){
				
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
	
	function driverpaymentonline_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getDriverPaymentOnline($user_data->id, $countryCode);
			
			
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
	
	function driverpaymentoffline_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getDriverPaymentOffline($user_data->id, $countryCode);
			
			
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
	
	function usersetting_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getUserSettings($user_data->id, $countryCode);
			
			
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
	
	function driverpaymentlist_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getDriverpayment($user_data->id, $countryCode);
			
			
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
	
	
	function incentive_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getIncentive($user_data->id, $countryCode);
			$user = $this->drivers_api->getUserSettings($user_data->id, $countryCode);
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'incentive_auto_enable' => $user->incentive_auto_enable != NULL ? $user->incentive_auto_enable : '0', 'incentive_complete' => $data['complete'], 'incentive_ongoing' => $data['ongoing']);
				
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
	
	function wallet_types_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('wallet_type', $this->lang->line("wallet_type"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$setting = $this->site->RegsiterSettings($countryCode);

			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			$wallet_type = $this->input->post('wallet_type');
			$res = $this->drivers_api->getTypeWallets($user_data->id, $wallet_type, $countryCode);
			
			$online = $this->drivers_api->getDriverPaymentOnline($user_data->id, $countryCode);
			
			$data = $res;
			$wallet_cash = $data['wallet_cash'] == NULL ? '0' : $data['wallet_cash'];
			if($res){
				if($wallet_type == 1){
					
						$result = array( 'status'=> 1 , 'message'=> 'Success', 'wallet_total' => $data['wallet_cash'] == NULL ? '0' : $data['wallet_cash'], 'minimum_wallet' => $setting->wallet_min_add_money, 'remainig_balance' => (string)($wallet_cash - $setting->wallet_min_add_money), 'driver_paid' => (string)$data['driverpaid'], 'data' => $data['cash_list'] == NULL ? '0' : $data['cash_list']);
					
				}elseif($wallet_type == 2){
					
						$result = array( 'status'=> 1 , 'message'=> 'Success', 'wallet_total' => $data['wallet_credit'] == NULL ? '0' : $data['wallet_credit'], 'data' => $data['credit_list'] == NULL ? '0' : $data['credit_list']);
						
				}
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
	/*function wallet_credit_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			$wallet_type = 2;
			$res = $this->drivers_api->getTypeWallets($user_data->id, $wallet_type, $countryCode);
			
			$online = $this->drivers_api->getDriverPaymentOnline($user_data->id, $countryCode);
			
			$data = $res;
			if($res){
				if(!empty($data['credit_list'])){
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'wallet_total' => $data['wallet_credit'] == NULL ? '0' : $data['wallet_credit'], 'data' => $data['credit_list']);
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Empty data');
				}
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
	}*/
	
	function addmoney_cashwallet_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$user_id = $user_data->id;
			
			if($this->input->post('offer_id') == 0){
				$paid_amount = $this->input->post('paid_amount');
				$join_id = 0;
				$join_table = '';
			}else{
				$offers = $this->drivers_api->getOfferWalletamount($this->input->post('offer_id'));
				if($check_wallet == 0){
					$paid_amount = $setting->wallet_min_add_money + $offers->offer_amount;
				}else{
					$paid_amount = $offers->offer_amount;
				}
				$join_id = $this->input->post('offer_id');
				$join_table = 'walletoffer';
			}
			
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
				'user_type' => 2,
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
				'flag_method' => 13,
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
				'flag_method' => 14,
				'cash' => $paid_amount,
				'description' => 'Transfer Money - Backend',
				'created' => $transaction_date,
				'is_country' => $is_country
			);
			$wallet_array[] = array(
				'user_id' =>  $user_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'flag' => 6,
				'flag_method' => 13,
				'cash' => $paid_amount,
				'description' => 'Add Money - Backend',
				'created' => $transaction_date,
				'is_country' => $is_country
			);
			
			$group_id = 4;
		
			//print_r($payment_array);
			//die;
			$res = $this->drivers_api->addMoneyOnlineAccount($group_id, $cash_array, $wallet_array, $payment_array, $transaction_status, $is_country);
			
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
	
	function withdrawmoney_cashwallet_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$paid_amount = $this->input->post('paid_amount');
			
			$payment_array  = array(
				'user_id' => $user_data->id,
				'user_type' => 2, 
				'amount' => $paid_amount,
				'status' => 1,
				'created_on' => date('Y-m-d H:i:s')
			);
			
			$wallet_array = array(
				'user_id' =>  $user_data->id,
				'user_type' => 2,
				'wallet_type' => 1,
				'flag' => 7,
				'flag_method' => 13,
				'cash' => $paid_amount,
				'description' => 'Add Money - Cash Wallet',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$res = $this->drivers_api->addSendCashwallet($user_data->id, $wallet_array, $payment_array,  $countryCode);
		
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
	
	function credit_to_cash_transfer_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('amount', $this->lang->line("amount"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$amount = $this->input->post('amount');
			
			$wallet_cash = array(
				'user_id' =>  $user_data->id,
				'user_type' => 2,
				'wallet_type' => 1,
				'flag' => 5,
				'flag_method' => 14,
				'cash' => $amount,
				'description' => 'Credit to Cash transfer',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			$wallet_credit = array(
				'user_id' =>  $user_data->id,
				'user_type' => 2,
				'wallet_type' => 2,
				'flag' => 4,
				'flag_method' => 14,
				'cash' => $amount,
				'description' => 'Credit Deduction',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$res = $this->drivers_api->transferWallet($user_data->id, $wallet_cash, $wallet_credit,  $countryCode);
		
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
	
	
	function walletlist_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$setting = $this->site->RegsiterSettings($countryCode);
			
			$res = $this->drivers_api->getWalletList($user_data->id, $countryCode);
			
			$wallet_cash = $res['CashPaymentAmount'] == NULL ? '0' : (string)$res['CashPaymentAmount'];
			$data[] = array(
				'wallet_type' => 1,
				'wallet_name' => 'cash',
				'wallet_balance' => $res['CashPaymentAmount'] == NULL ? '0' : (string)$res['CashPaymentAmount'] ,
				'minimum_wallet' => $setting->wallet_min_add_money,
				'remainig_balance' => (string)($wallet_cash - $setting->wallet_min_add_money),
			);
			$data[] = array(
				'wallet_type' => 2,
				'wallet_name' => 'credit',
				'wallet_balance' => $res['CreditPaymentAmount'] == NULL ? '0' : (string)$res['CreditPaymentAmount'] 
			);
			if($data){
				
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
	
	function wallet_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getWallets($user_data->id, $countryCode);
			
			$online = $this->drivers_api->getDriverPaymentOnline($user_data->id, $countryCode);
			
			$data = $res;
			if($res){
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'wallet_total' => $data['wallet'], 'driver_paid' => $data['driverpaid'], 'data' => $data['list'], 'online_history' => $online == true ? $online : array());
				
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getTickets($user_data->id, $countryCode);
			
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('enquiry_id', $this->lang->line("enquiry_id"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getEnquiryView($user_data->id, $this->input->post('enquiry_id'), $countryCode);
			$follow = $this->drivers_api->getEnquiryFollow($user_data->id, $this->input->post('enquiry_id'), $countryCode);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$data  = array(
				'enquiry_id' => $this->input->post('enquiry_id'),
				'customer_id' => $user_data->id,
				'feedback_rating' => $this->input->post('feedback_rating') ? $this->input->post('feedback_rating') : 0,
				'feedback_msg' => $this->input->post('feedback_msg') ? $this->input->post('feedback_msg') : '',
				'created_on' => date('Y-m-d H:i:s')
			);
			
			$res = $this->drivers_api->addenquiryFeedback($data, $this->input->post('enquiry_id'), $user_data->id, $countryCode);
			
			
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
	/*New Changes*/
	public function sosdata_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			
			$res = $this->drivers_api->getEmergencydata($user_data->id, $countryCode);
			
			
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
	
	public function sms_sos($msg, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MSG]');
        $sms_rep_arr = array($msg);
        $response_sms = send_transaction_sms($sms_template_slug = "sos", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sos_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			//$row['oauth_token'] = $this->input->post('oauth_token');
			$row['user_id'] = $user_data->id;
			$row['booking_id'] = $this->input->post('booking_id');
			$res = $this->drivers_api->getEmergencycontact($row, $countryCode);
			
			$current_ride = $this->drivers_api->currentRideSOS($row, $countryCode);
			
			if($res){
				foreach($res as $em_res){
					$pickup = $this->site->findLocationWEB($current_ride->start_lat, $current_ride->start_lng);
					$dropoff = $this->site->findLocationWEB($current_ride->end_lat, $current_ride->end_lng);
					$driverlocation = $this->site->findLocationWEB($current_ride->current_latitude, $current_ride->current_longitude);
					$msg = 'Customer Details : '.$current_ride->customer_name.' Driver Details : '.$current_ride->driver_name.', Driver Location : '.$driverlocation.', Taxi Details : '.$current_ride->taxi_name.', '.$current_ride->taxi_number.', Pickup: '.$pickup.' Dropoff: '.$dropoff.' Click Here : https://35.154.46.42/sos?id='.$current_ride->booking;
					
					
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
	
	
	public function ridebase_main_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->drivers_api->getHelpmain($user_data->id, 'Ride Support', $countryCode);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->drivers_api->getHelpmain($user_data->id, 'General Support', $countryCode);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('parent_id', $this->lang->line("parent_id"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$customer_type = '2'; //1 - Credit, 2- Debit
			$data = $this->drivers_api->getHelpsub($user_data->id, $this->input->post('parent_id'), $countryCode);
			
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
	
	public function driverdatehistory_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$driver_id = $user_data->id;
			
			$res = $this->drivers_api->getDriverdateHistory($driver_id, $countryCode);
			$data[] = $res;
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Setting data', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not data!');
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
	
	public function paymentoffline_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$driver_id = $user_data->id;
			$insert = array(
				'paid_amount' => $this->input->post('paid_amount'),
				'payment_date' => $this->input->post('deposit_date'),
				'transaction_no' => $this->input->post('transaction_no'),
				'payment_status' => 2,
				'admin_account_no' => $this->input->post('admin_account_no'),
				'deposit_bank_name' => $this->input->post('deposit_bank_name'),
				'deposit_branch_name' => $this->input->post('deposit_branch_name'),
				'deposit_ifscode' => $this->input->post('deposit_ifscode'),
				'deposit_date' => $this->input->post('deposit_date'),
				'transaction_date' => $this->input->post('deposit_date'),
				'driver_status' => 1
			);
			$data = $this->drivers_api->paymentoffline($insert, $driver_id, $countryCode);
			
			if(!empty($data)){
				$sms_message = 'Your payment amount ('.$this->input->post('paid_amount').') has been successful. please wait admin approval process.';
				$sms_phone = $user_data->mobile;
				$sms_country_code = $user_data->country_code;
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$notification['title'] = 'Driver Payment Offline';
				$notification['message'] = $user_data->first_name.' payment amount ('.$this->input->post('paid_amount').') has been successful. please wait admin approval process.';
				$notification['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->drivers_api->insertNotification($notification, $countryCode);
				
				$result = array( 'status'=> true , 'message'=> 'Payment offline success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No datas');
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
	
	
	public function paymentdetail_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			$data = $this->drivers_api->paymentdetail($row, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Payment details', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No datas');
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
	
	public function drivercancelmaster_get(){
		$countryCode = $this->input->get('is_country');
		$group_id = 4;
		$data = $this->site->getCancelMaster($group_id, $countryCode);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function driverhealthmaster_get(){
		$countryCode = $this->input->get('is_country');
		$group_id = 4;
		$data = $this->site->getHealthMaster($countryCode);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function adminbank_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->drivers_api->getAdminbank($countryCode);
		if(!empty($data)){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function paymentlist_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			$data = $this->drivers_api->paymentlist($row, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Payment list datas', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No datas');
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
	
	public function paymentview_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('driver_payment', $this->lang->line("driver_payment"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			$row['id'] = $this->input->post('driver_payment');
			$data = $this->drivers_api->paymentview($row, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Payment list datas', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No datas');
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
	
	public function driverwaitingcancel_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('cancel_msg', $this->lang->line("cancel_msg"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$onridecheck = $this->site->RidecancelCheck($this->input->post('booking_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> 0 , 'message' => 'Ride Already Cancel.');
			}else{
			$setting = $this->site->RegsiterSettings($countryCode);
			$current_date = date('Y-m-d');
			
			$type_cancel = $this->site->CancelCabBooking($this->input->post('booking_id'), $countryCode);
				
			
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$latlng = $this->drivers_api->getCancelDriverlocation($user_data->id, $countryCode);
			
			$cancel_location = $this->site->findLocation($latlng->current_latitude, $latlng->current_longitude, $countryCode);
			
			$cancel['driver_id'] = $user_data->id;
			$cancel['booking_id'] = $this->input->post('booking_id');
			$cancel['cancel_msg'] = $this->input->post('cancel_msg');
			$cancel['cancel_id'] = $this->input->post('cancel_id') ? $this->input->post('cancel_id') : 0;
			$cancel['cancel_location'] = $cancel_location ? $cancel_location : '';
			$rides = $this->drivers_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			
			$cancel_distance =  $this->site->GetDrivingDistance_New($rides->start_lat, $rides->start_lng, $latlng->current_latitude, $latlng->current_longitude, 'Km', $countryCode);
			
			//$this->site->getFareestimate($start_lat, $start_lng, $taxi_type, $ride_type, $countryCode);
			
			$check_cancel_limit = $this->site->cancelLimit('driver', $user_data->id, $type_cancel->no_of_driver_cancel, $current_date);
			$check_cancel_limit_increase = $check_cancel_limit + 1;
			
			$cancel['cancel_distance'] = $cancel_distance;
			$cancel['cancel_fare'] = $cancel_distance * 5;
			$cancel['customer_id'] = $rides->customer_id; 
			$cancel['process_type'] = $rides->process_type; 
			$cancel['driver_cancel_charge'] = $type_cancel->driver_cancel_charge;
			
			
			$cancel['cancel_maximum_fare'] = $setting->cancel_maximum_fare; 
			$cancel['no_of_driver_cancel'] = $type_cancel->no_of_driver_cancel; 
			$cancel['ride_cancel_billing_screen_enable'] = $setting->ride_cancel_billing_screen_enable; 
			$cancel['ride_cancel_driver_on_the_way_km_fare_enable'] = $setting->ride_cancel_driver_on_the_way_km_fare_enable;
			
			$cancel['ride_cancel_driver_on_the_way_percentage_enable'] = $setting->ride_cancel_driver_on_the_way_percentage_enable;
			$cancel['ride_cancel_driver_on_the_way_percentage_value'] = $setting->ride_cancel_driver_on_the_way_percentage_value;
			
			$cancel['check_cancel_limit'] = $check_cancel_limit_increase;
			
			$cancel['cancel_on'] = date('Y-m-d H:i:s');
			$cancel['booked_on'] = $rides->booked_on;
			$ride_total_second = strtotime($cancel['cancel_on']) - strtotime($cancel['booked_on']);
			if($ride_total_second > $type_cancel->cancel_free_second){
				$cancel['check_cancel_free_second'] = 1;
			}else{
				$cancel['check_cancel_free_second'] = 0;
			}
			
			
			$data = $this->drivers_api->driverWaitingCancel($cancel, $countryCode);
			
			$details[] = $data;
			if(!empty($rides->customer_id)){
				
				$customer_data = $this->drivers_api->getCustomerID($rides->customer_id, $countryCode);
				$driver_data = $this->drivers_api->getDriverID($user_data->id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				//$driver_phone = $driver_data->country_code.$driver_data->mobile;
				if($rides->customer_id != 2){
				$notification['title'] = 'Ride Cancel';
				//$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				if($cancel['check_cancel_free_second'] == 1){
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}else{
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}
				
				$notification['user_type'] = 1;
				$notification['user_id'] = $rides->customer_id;
				$this->drivers_api->insertNotification($notification, $countryCode);
				}
				$notification1['title'] = 'Ride Cancel';
				//$notification1['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				if($cancel['check_cancel_free_second'] == 1){
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}else{
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}
				
				$notification1['user_type'] = 4;
				$notification1['user_id'] = 2;
				$this->drivers_api->insertNotification($notification1, $countryCode);
								
				$socket_id = $this->site->getSocketID($rides->customer_id, 1, $countryCode);
				
				$driver_cancel_charge = number_format($data['driver_cancel_charge'],2);
				if($check_cancel_limit_increase <= $type_cancel->no_of_driver_cancel){
						$remaining = (($type_cancel->no_of_driver_cancel - $check_cancel_limit) - 1);
					}else{            
						$remaining = 'Not free cancel... charges applied';
					}
				if($cancel['check_cancel_free_second'] == 1){
					if($check_cancel_limit_increase > $type_cancel->no_of_driver_cancel){
						$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}else{
						
						$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
						
					}
				}else{
					$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
				}
					
				
				
				
				
				if($cancel['check_cancel_free_second'] == 1){
					if($check_cancel_limit_increase > $type_cancel->no_of_customer_cancel){
						$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}else{
						
						$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}
				}else{
					$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
				}
					
				$sms_message = $sms_msg;
				$sms_phone = $customer_data->country_code.$customer_data->mobile;
				$sms_country_code = $customer_data->country_code;
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				if($setting->ride_cancel_allocated_another_driver == 1){
					$getride = $this->drivers_api->getRideBYID($this->input->post('booking_id'), $countryCode);
					$waypoint = $this->site->getWaypoint($this->input->post('booking_id'));
					
						$ride_type = $getride->ride_type;
						if($ride_type == 1){
							$ride_otp = random_string('numeric', 6);
							$status = 1;
							$ride_timing = $getride->ride_timing;
							$ride_timing_end = '0000-00-00 00:00:00';
						}else{
							$ride_timing = $getride->ride_timing;
							if($this->input->post('booking_type') == 3){
								$ride_timing_end = $getride->ride_timing;
							}else{
								$ride_timing_end = $getride->ride_timing_end;
							}
							$status = 7;
							$ride_otp =  0;
						}
						if(!empty($waypoint)){
							foreach($waypoint as $way){
								$waypoint_array[] = array(
									'start' => $way->waypoint_start,
									'start_lat' => $way->waypoint_start_lat,
									'start_lng' => $way->waypoint_start_lng,
								);
							}
							
						}else{
							$waypoint_array = array();
						}
						$insert = array(
							'customer_id' => $getride->customer_id,
							'exit_driver' => $user_data->id,
							'driver_id' => 0,
							'payment_id' => $getride->payment_id,
							'cab_type_id' => $getride->cab_type_id,
							'distance_km' => $getride->distance_km ? $getride->distance_km : '0',
							'distance_price' => $getride->distance_price ? $getride->distance_price : '0',
							'payment_name' => $getride->payment_name,
							'outstation_type' => $getride->outstation_type,
							'outstation_way' => $getride->outstation_way,
							'package_id' => $getride->package_id,
							'booked_by' => $getride->customer_id,
							'booked_type' => $getride->booked_type,
							'booked_on' => $getride->booked_on,               
							'booking_timing' => $getride->booking_timing,     
							'ride_timing' => $ride_timing,
							'ride_timing_end' => $ride_timing_end,
							'ride_type' => $getride->ride_type,
							'start' => $getride->start,
							'start_lat' => $getride->start_lat,
							'start_lng' => $getride->start_lng,
							'end' => $getride->end ? $getride->end : '0',
							'end_lat' => $getride->end_lat ? $getride->end_lat : '0',
							'end_lng' => $getride->end_lng ? $getride->end_lng : '0',
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
							'location' => $getride->end,
							'latitude' => $getride->end_lat,
							'longitude' => $getride->end_lng,
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 7
						);	
						$offer_code = '';
						$cancelridedata = $this->drivers_api->add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $getride->customer_id, $offer_code, $waypoint_array);
						
						
						
						if(!empty($cancelridedata)){
							$distance = 20;
						
							$val['taxi_type'] = $getride->cab_type_id;
							$val['latitude'] = $getride->start_lat;
							$val['longitude'] = $getride->start_lng;
							$val['booking_type'] = $getride->booked_type;
							$val['exit_driver'] = $getride->driver_id;
							$val['distance'] = $distance; 
							//print_r($val);
							if($getride->ride_type == 1){
								$driver_radius_data = $this->drivers_api->getDrivers_radiusNEW($val, $countryCode);
							}else{
								$driver_radius_data = 'Ride_later';
							}
							
							if(!empty($driver_radius_data)){
							//print_r($driver_radius_data);
							if($ride_type == 1){
									
								$notification['title'] = 'Ride Booking';
								$notification['message'] = $customer_data->first_name.' has been ride booked.';
								$notification['user_type'] = 4;
								$notification['user_id'] = 2;
								$this->drivers_api->insertNotification($notification, $countryCode);
								
								$this->site->bookingEmitDriverinsert($cancelridedata->booking_id, $driver_radius_data[0]->id, $getride->customer_id);
								
								$waypoint_data = $this->site->getWaypoint($cancelridedata->booking_id);
								$socket_driver_id = $this->site->getSocketID($driver_radius_data[0]->id, 2, $countryCode);
								$event = 'server_booking_checking';
								
								$time = $this->site->GetDrivingDistance_waypoints($getride->start_lat, $getride->start_lng,$getride->end_lat, $getride->end_lng, 'Time', $countryCode);
								
								if($getride->booked_type == 1){
									$booked_type_text = 'City Ride';
								}elseif($getride->booked_type == 2){
									$booked_type_text = 'Rental Ride';
								}elseif($getride->booked_type == 3){
									$booked_type_text = 'Outstation Ride';
								}else{
									$booked_type_text = 'No Ride Type';
								}
				
								$edata = array(
									'booked_type_text' => $booked_type_text,
									'payment_id' => $getride->payment_id,
									'payment_name' => $getride->payment_name,
									'distance_km' => $getride->distance_km,
									'distance_price' => $getride->distance_price,
									'customer_support' => '0987654321',
									'taxi_type' => $this->site->taxitypename($getride->cab_type_id),
									'ride_waiting_time' => $time,
									'pick_up' => $getride->start,
									'from_latitude' => $getride->start_lat,
									'from_longitude' => $getride->start_lng,
									'drop_off' => $getride->end ? $getride->end : 'Location not given',
									'to_latitude' => $getride->end_lat ? $getride->end_lat : '0',
									'to_longitude' => $getride->end_lng ? $getride->end_lng : '0',
									'cab_type_id' => $getride->cab_type_id,
									'ride_id' => $cancelridedata->booking_id,
									'driver_id' => $driver_radius_data[0]->id,
									'driver_oauth_token' => $driver_radius_data[0]->oauth_token,
									'socket_id' => $socket_driver_id,
									'driver_time' => $setting->driver_time,
									'waypoint_data' => $waypoint_data
									
									
								);
								$success = 	$this->socketemitter->setEmit($event, $edata);
								$last_emit_array = array(
									'user_type_id' => 2,
									'user_id' => $driver_radius_data[0]->id,
									'emit_name' => 'driver_available_check',
									'emit_data' => json_encode($edata),
									'created_on' => date('Y-m-d H:i:s')
								);
								$this->site->insertLastEmit($last_emit_array);
								
								$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $cancelridedata->booking_id, 'customer_time' => $setting->customer_time);
							}else{
								
								//$ride_data = $this->drivers_api->getRideBYID($cancelridedata[0], $countryCode);
								
								$sms_message = ' '.$customer_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
								$sms_phone = $driver_data->country_code.$driver_data->mobile;
								$sms_country_code = $driver_data->country_code;
			
								$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
								
								$notification['title'] = 'Ride Booking - Ride Later';
								$notification['message'] = $customer_data->first_name.', Your booking has been success. Booking No : '.$cancelridedata->booking_no.', Booking Time : '.$cancelridedata->booking_timing.', Booking Type : '.$booked_type_text;
								$notification['user_type'] = 4;
								$notification['user_id'] = 2;
								$this->drivers_api->insertNotification($notification, $countryCode);
								
								$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $cancelridedata->booking_id, 'customer_time' => $setting->customer_time);
							}
							
							}
							
						}
						
				}
				//$socket_id = $this->site->getSocketID($driver_radius_data[0]->id, 2, $countryCode);
				$event = 'server_ride_cancel';
				$edata = array(
					'ride_id' => $this->input->post('booking_id'),
					'allocated_driver' => $setting->ride_cancel_allocated_another_driver, 
					'allocated_array' => !empty($cancelridedata) ? $cancelridedata : 0,
					'title' => 'Ride Cancel',
					'message' => $emit_msg,		
					'cancel_fare' => $driver_cancel_charge,					
					'socket_id' => $socket_id
					
				);
				
				$success = 	$this->socketemitter->setEmit($event, $edata);
				
				$last_emit_array = array(
					'user_type_id' => 1,
					'user_id' => $rides->customer_id,
					'emit_name' => 'driver_ride_cancel',
					'emit_data' => json_encode($edata),
					'created_on' => date('Y-m-d H:i:s')
				);
				$this->site->insertLastEmit($last_emit_array);
			}
			
			//print_r($edata);
			//die;
				if($data == true){
					$driver_cancel_charge = number_format($data['driver_cancel_charge'],2);
					if($cancel['check_cancel_free_second'] == 1){
						if($check_cancel_limit_increase > $type_cancel->no_of_driver_cancel){
							$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$driver_cancel_charge."(".$data['drver_cancel_final_km']."Km)";
						}else{
							$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".$remaining."";
							
						}
					}else{
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: 0.00, cancel second:".$ride_total_second."s";
					}
					
					$result = array( 'status'=> true , 'no_of_customer_cancel' => $type_cancel->no_of_driver_cancel, 'check_cancel_limit' => $check_cancel_limit, 'message'=> $check_msg, 'cancel_fare' => number_format($data['driver_cancel_charge'],2), 'ride_cancel_driver_on_the_way_km_fare_enable' => $setting->ride_cancel_driver_on_the_way_km_fare_enable, 'data' => $details);
				}else{
					$result = array( 'status'=> false , 'message'=> 'driver cancel not success');
				}
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
	
	public function drivercancel_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('cancel_msg', $this->lang->line("cancel_msg"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		
		if ($this->form_validation->run() == true) {
			$onridecheck = $this->site->RidecancelCheck($this->input->post('booking_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> false , 'message' => 'Ride Already Cancel.');
			}else{
			$setting = $this->site->RegsiterSettings($countryCode);
			$current_date = date('Y-m-d');
			
			$type_cancel = $this->site->CancelCabBooking($this->input->post('booking_id'), $countryCode);
				
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$latlng = $this->drivers_api->getCancelDriverlocation($user_data->id, $countryCode);
			
			$cancel_location = $this->site->findLocation($latlng->current_latitude, $latlng->current_longitude, $countryCode);
			$check_cancel_limit = $this->site->cancelLimit('driver', $user_data->id, $type_cancel->no_of_driver_cancel, $current_date);
			$check_cancel_limit_increase = $check_cancel_limit + 1;
			$cancel['driver_id'] = $user_data->id;
			$cancel['booking_id'] = $this->input->post('booking_id');
			$cancel['cancel_msg'] = $this->input->post('cancel_msg');
			$cancel['cancel_id'] = $this->input->post('cancel_id') ? $this->input->post('cancel_id') : 0;
			$cancel['cancel_location'] = $cancel_location ? $cancel_location : '';
			
			$cancel['driver_cancel_charge'] = $type_cancel->driver_cancel_charge;
			
			$cancel['no_of_driver_cancel'] = $type_cancel->no_of_driver_cancel; 
			$cancel['ride_cancel_billing_screen_enable'] = $setting->ride_cancel_billing_screen_enable; 
			$cancel['ride_cancel_driver_on_the_way_km_fare_enable'] = $setting->ride_cancel_driver_on_the_way_km_fare_enable;
			
			$cancel['cancel_maximum_fare'] = $setting->cancel_maximum_fare; 
			
			$cancel['ride_cancel_driver_on_the_way_percentage_enable'] = $setting->ride_cancel_driver_on_the_way_percentage_enable;
			$cancel['ride_cancel_driver_on_the_way_percentage_value'] = $setting->ride_cancel_driver_on_the_way_percentage_value;
			
			$cancel['check_cancel_limit'] = $check_cancel_limit_increase;
			
			
			
			$rides = $this->drivers_api->getRideBYID($this->input->post('booking_id'), $countryCode);
			$cancel['customer_id'] = $rides->customer_id; 
			$cancel['process_type'] = $rides->process_type; 
			$cancel['cancel_on'] = date('Y-m-d H:i:s');
			$cancel['booked_on'] = $rides->booked_on;
			$ride_total_second = strtotime($cancel['cancel_on']) - strtotime($cancel['booked_on']);
			if($ride_total_second > $type_cancel->cancel_free_second){
				$cancel['check_cancel_free_second'] = 1;
			}else{
				$cancel['check_cancel_free_second'] = 0;
			}
			
			$data = $this->drivers_api->driverCancel($cancel, $countryCode);
			$details[] = $data;
			
			if(!empty($rides->customer_id)){
				
				$customer_data = $this->drivers_api->getCustomerID($rides->customer_id, $countryCode);
				$driver_data = $this->drivers_api->getDriverID($user_data->id, $countryCode);
				
				
				$customer_name = $customer_data->first_name;
				$driver_name = $driver_data->first_name;
				$driver_phone = $driver_data->country_code.$driver_data->mobile;
				if($rides->customer_id != 2){
				$notification['title'] = 'Ride Cancel';
				if($cancel['check_cancel_free_second'] == 1){
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}else{
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';	
				}
				
				//$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				$notification['user_type'] = 1;
				$notification['user_id'] = $rides->customer_id;
				$this->drivers_api->insertNotification($notification, $countryCode);
				}
				$notification1['title'] = 'Ride Cancel';
				if($cancel['check_cancel_free_second'] == 1){
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				}else{
					$notification['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
					
				}
				
				//$notification1['message'] = 'Ride has been cancelled by driver('.$driver_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
				$notification1['user_type'] = 4;
				$notification1['user_id'] = 2;
				$this->drivers_api->insertNotification($notification1, $countryCode);
							
				$socket_id = $this->site->getSocketID($rides->customer_id, 1, $countryCode);
				$driver_cancel_charge = number_format($data['driver_cancel_charge'],2);
				if($check_cancel_limit_increase <= $type_cancel->no_of_driver_cancel){
						$remaining = (($type_cancel->no_of_driver_cancel - $check_cancel_limit) - 1);
					}else{
						$remaining = 'Not free cancel... charges applied';
					}
				
				if($cancel['check_cancel_free_second'] == 1){
					if($check_cancel_limit_increase > $type_cancel->no_of_driver_cancel){
						$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}else{
						
						$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
						
					}
				}else{
					$emit_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
				}
					
				
				
				if($cancel['check_cancel_free_second'] == 1){
					if($check_cancel_limit_increase > $type_cancel->no_of_customer_cancel){
						$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}else{
						
						$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
					}
				}else{
					$sms_msg = "Ride has been cancelled by driver (".$driver_name."). cancel reason : ".$this->input->post('cancel_msg')."";
				}
					
				$sms_message = $sms_msg;
				$sms_phone = $customer_data->country_code.$customer_data->mobile;
				$sms_country_code = $customer_data->country_code;
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
			
				if($setting->ride_cancel_allocated_another_driver == 1){
					$getride = $this->drivers_api->getRideBYID($this->input->post('booking_id'), $countryCode);
					$waypoint = $this->site->getWaypoint($this->input->post('booking_id'));
					
						$ride_type = $getride->ride_type;
						if($ride_type == 1){
							$ride_otp = random_string('numeric', 6);
							$status = 1;
							$ride_timing = $getride->ride_timing;
							$ride_timing_end = '0000-00-00 00:00:00';
						}else{
							$ride_timing = $getride->ride_timing;
							if($this->input->post('booking_type') == 3){
								$ride_timing_end = $getride->ride_timing;
							}else{
								$ride_timing_end = $getride->ride_timing_end;
							}
							$status = 7;
							$ride_otp =  0;
						}
						if(!empty($waypoint)){
							foreach($waypoint as $way){
								$waypoint_array[] = array(
									'start' => $way->waypoint_start,
									'start_lat' => $way->waypoint_start_lat,
									'start_lng' => $way->waypoint_start_lng,
								);
							}
							
						}else{
							$waypoint_array = array();
						}
						$insert = array(
							'customer_id' => $getride->customer_id,
							'exit_driver' => $user_data->id,
							'driver_id' => 0,
							'payment_id' => $getride->payment_id,
							'cab_type_id' => $getride->cab_type_id,
							
							'distance_km' => $getride->distance_km ? $getride->distance_km : '0',
							'distance_price' => $getride->distance_price ? $getride->distance_price : '0',
							'payment_name' => $getride->payment_name,
							'outstation_type' => $getride->outstation_type,
							'outstation_way' => $getride->outstation_way,
							'package_id' => $getride->package_id,
							'booked_by' => $getride->customer_id,
							'booked_type' => $getride->booked_type,
							'booked_on' => $getride->booked_on,               
							'booking_timing' => $getride->booking_timing,     
							'ride_timing' => $ride_timing,
							'ride_timing_end' => $ride_timing_end,
							'ride_type' => $getride->ride_type,
							'start' => $getride->start,
							'start_lat' => $getride->start_lat,
							'start_lng' => $getride->start_lng,
							'end' => $getride->end ? $getride->end : '0',
							'end_lat' => $getride->end_lat ? $getride->end_lat : '0',
							'end_lng' => $getride->end_lng ? $getride->end_lng : '0',
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
							'location' => $getride->end,
							'latitude' => $getride->end_lat,
							'longitude' => $getride->end_lng,
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 7
						);	
						$offer_code = '';
						
						$cancelridedata = $this->drivers_api->add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $getride->customer_id, $offer_code, $waypoint_array);
						
						
						
						if(!empty($cancelridedata)){
							$distance = 20;
						
							$val['taxi_type'] = $getride->cab_type_id;
							$val['latitude'] = $getride->start_lat;
							$val['longitude'] = $getride->start_lng;
							$val['booking_type'] = $getride->booked_type;
							$val['exit_driver'] = $getride->driver_id;
							$val['distance'] = $distance; 
							//print_r($val);
							if($getride->ride_type == 1){
								$driver_radius_data = $this->drivers_api->getDrivers_radiusNEW($val, $countryCode);
							}else{
								$driver_radius_data = 'Ride_later';
							}
							
							if(!empty($driver_radius_data)){
							//print_r($driver_radius_data);
							if($ride_type == 1){
									
								$notification['title'] = 'Ride Booking';
								$notification['message'] = $customer_data->first_name.' has been ride booked.';
								$notification['user_type'] = 4;
								$notification['user_id'] = 2;
								$this->drivers_api->insertNotification($notification, $countryCode);
								
								$this->site->bookingEmitDriverinsert($cancelridedata->booking_id, $driver_radius_data[0]->id, $getride->customer_id);
								
								$waypoint_data = $this->site->getWaypoint($cancelridedata->booking_id);
								$socket_driver_id = $this->site->getSocketID($driver_radius_data[0]->id, 2, $countryCode);
								$event = 'server_booking_checking';
								
								$time = $this->site->GetDrivingDistance_waypoints($getride->start_lat, $getride->start_lng,$getride->end_lat, $getride->end_lng, 'Time', $countryCode);
								 
								 if($getride->booked_type == 1){
									$booked_type_text = 'City Ride';
								}elseif($getride->booked_type == 2){
									$booked_type_text = 'Rental Ride';
								}elseif($getride->booked_type == 3){
									$booked_type_text = 'Outstation Ride';
								}else{
									$booked_type_text = 'No Ride Type';
								}
								$edata = array(
									'booked_type_text' => $booked_type_text,
									'payment_id' => $getride->payment_id,
									'payment_name' => $getride->payment_name,
									'distance_km' => $getride->distance_km,
									'distance_price' => $getride->distance_price,
									'customer_support' => '0987654321',
									'taxi_type' => $this->site->taxitypename($getride->cab_type_id),
									'ride_waiting_time' => $time,
									'pick_up' => $getride->start,
									'from_latitude' => $getride->start_lat,
									'from_longitude' => $getride->start_lng,
									'drop_off' => $getride->end ? $getride->end : 'Location not given',
									'to_latitude' => $getride->end_lat ? $getride->end_lat : '0',
									'to_longitude' => $getride->end_lng ? $getride->end_lng : '0',
									'cab_type_id' => $getride->cab_type_id,
									'ride_id' => $cancelridedata->booking_id,
									'driver_id' => $driver_radius_data[0]->id,
									'driver_oauth_token' => $driver_radius_data[0]->oauth_token,
									'socket_id' => $socket_driver_id,
									'driver_time' => $setting->driver_time,
									'waypoint_data' => $waypoint_data
									
									
								);
								$success = 	$this->socketemitter->setEmit($event, $edata);
								$last_emit_array = array(
									'user_type_id' => 2,
									'user_id' => $driver_radius_data[0]->id,
									'emit_name' => 'driver_available_check',
									'emit_data' => json_encode($edata),
									'created_on' => date('Y-m-d H:i:s')
								);
								$this->site->insertLastEmit($last_emit_array);
								
								$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $cancelridedata->booking_id, 'customer_time' => $setting->customer_time);
							}else{
								
								//$ride_data = $this->drivers_api->getRideBYID($cancelridedata[0], $countryCode);
								
								$sms_message = ' '.$customer_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
								$sms_phone = $driver_data->country_code.$driver_data->mobile;
								$sms_country_code = $driver_data->country_code;
			
								$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
								
								$notification['title'] = 'Ride Booking - Ride Later';
								$notification['message'] = $customer_data->first_name.', Your booking has been success. Booking No : '.$cancelridedata->booking_no.', Booking Time : '.$cancelridedata->booking_timing.', Booking Type : '.$booked_type_text;
								$notification['user_type'] = 4;
								$notification['user_id'] = 2;
								$this->drivers_api->insertNotification($notification, $countryCode);
								
								$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $cancelridedata->booking_id, 'customer_time' => $setting->customer_time);
							}
							
							}
							
						}
						
				}
				
				$event = 'server_ride_cancel';
				$edata = array(
					'ride_id' => $this->input->post('booking_id'),
					'allocated_driver' => $setting->ride_cancel_allocated_another_driver, 
					'allocated_array' => !empty($cancelridedata) ? $cancelridedata : 0,
					'title' => 'Ride Cancel',
					'message' => $emit_msg,		
					'cancel_fare' => $driver_cancel_charge,				
					'socket_id' => $socket_id
					
				);
				
				$success = 	$this->socketemitter->setEmit($event, $edata);
				$last_emit_array = array(
					'user_type_id' => 1,
					'user_id' => $rides->customer_id,
					'emit_name' => 'driver_ride_cancel',
					'emit_data' => json_encode($edata),
					'created_on' => date('Y-m-d H:i:s')
				);
				$this->site->insertLastEmit($last_emit_array);
			}
			
			if($data == true){
				$driver_cancel_charge = number_format($data['driver_cancel_charge'],2);
				if($cancel['check_cancel_free_second'] == 1){
					if($check_cancel_limit_increase > $type_cancel->no_of_driver_cancel){
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: ".$driver_cancel_charge."(".$data['drver_cancel_final_km']."Km)";
					}else{
						$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel charge free. free remaining count: ".$remaining."";
						
					}
				}else{
					$check_msg = "Ride has been cancelled. cancel reason : ".$this->input->post('cancel_msg').". cancel fare: 0.00, cancel second:".$ride_total_second."s";
					
				}
				
				$result = array( 'status'=> true , 'no_of_customer_cancel' => $type_cancel->no_of_driver_cancel, 'check_cancel_limit' => $check_cancel_limit, 'message'=> $check_msg, 'cancel_fare' => number_format($data['driver_cancel_charge'],2), 'ride_cancel_driver_on_the_way_km_fare_enable' => $setting->ride_cancel_driver_on_the_way_km_fare_enable, 'data' => $details);
			}else{
				$result = array( 'status'=> false , 'message'=> 'driver cancel not success');
			}
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
	
	public function ridedetails_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			
			$res = $this->drivers_api->getRidedetailsNEW($user_data->id, $this->input->post('ride_id'), $countryCode);
			
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
	
	public function register_resend_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'));
			
			$row['country_code'] = $this->input->post('country_code');
			$row['mobile'] = $this->input->post('mobile');
			//$mobile_otp = random_string('numeric', 6);
			
			$data = $this->drivers_api->registerresendotp($row, $countryCode);
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
	
	public function driversettingview_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			$user_data = $this->drivers_api->getDriversettingView($this->input->post('oauth_token'), $countryCode);
			
			
			
			$data[] = $user_data;
			if($user_data){
				$result = array( 'status'=> true , 'message'=> 'Setting data', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not data!');
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
	
	public function driversetting_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_id = $user_data->id;
			$insert = array(
				'is_daily' => $this->input->post('is_daily'),
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'is_hiring' => $this->input->post('is_hiring'),
				'is_corporate' => $this->input->post('is_corporate'), 
				'base_location' => $this->input->post('base_location'),
				'base_area_lat' => $this->input->post('base_area_lat'),
				'base_area_lng' => $this->input->post('base_area_lng')
			);
			
			$data = $this->drivers_api->updateDriver($insert, $driver_id, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Setting updated success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not Updated!');
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
	
	public function preferlocationview_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_id = $user_data->id;
			$res = $this->drivers_api->getPreferLocationView($driver_id, $countryCode);
			$data = $res;
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Setting data', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not data!');
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
	
	public function preferlocation_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('lat', $this->lang->line("lat"), 'required');
		$this->form_validation->set_rules('lng', $this->lang->line("lng"), 'required');
		$this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$prefer_id = $this->input->post('prefer_id');
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_id = $user_data->id;
			$insert = array(
				'user_id' => $driver_id,
				'lat' => $this->input->post('lat'),
				'lng' => $this->input->post('lng'),
				'status' => $this->input->post('status'),
				'title' => $this->input->post('title'),
				'is_edit' => 1,
				'created_on' => date('Y-m-d')
				
			);
			
			$data = $this->drivers_api->updatePreferlocation($insert, $driver_id, $prefer_id, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Setting updated success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not Updated!');
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
	
	public function servicestypeview_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_id = $user_data->id;
			
			$user_data = $this->drivers_api->getServicestypeView($driver_id, $countryCode);
			
			//$data[] = $user_data;
			if($user_data){
				$result = array( 'status'=> true , 'message'=> 'Services data', 'data' => $user_data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Services not data!');
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
	
	public function servicestype_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('taxi_type', $this->lang->line("taxi_type"), 'required');
		$this->form_validation->set_rules('taxi_id', $this->lang->line("taxi_id"), 'required');
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$driver_id = $user_data->id;
			$taxi = $this->drivers_api->getDriverTaxi($driver_id, $countryCode);

			$multiple_type = $taxi->multiple_type;
			
			if($this->input->post('status') == 0){
				$empty = array('0');
				$array1 = Array($this->input->post('taxi_type'));
				$array2 = explode(',', $multiple_type);
				$array3 = array_unique(array_diff($array2, $array1));
				$array3 = array_unique(array_merge($array3, $empty));
				$output = implode(',', $array3);
				
			}else{
				$empty = array('0');
				$array1 = Array($this->input->post('taxi_type'));
				$array2 = explode(',', $multiple_type);
				$array3 = array_unique(array_merge($array2, $array1, $empty));
				$output = implode(',', $array3);
				
			}
			
			
			$data = $this->drivers_api->updateServicestype($driver_id, $this->input->post('taxi_id'), $this->input->post('taxi_type'), $this->input->post('status'), $output, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Setting updated success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Setting not Updated!');
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
	
	public function check_exist($string,$token){
		if(!empty($string)){
			$column=$token;
			$where = array(
			  $column => $string
			);
			
			$this->db->select('id');
			$this->db->from('users');
			$this->db->where($where);
			$this->db->where('group_id', 4);
			$num_results = $this->db->count_all_results();
			
	 
			if($num_results>0){
			  return true;
			}else{
				$this->form_validation->set_message('check_exist', 'The %s value is mismatch.');
			  return false;
			  
			}
		  }
	}

	public function ridetypestatus_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		

		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$setting = $this->site->RegsiterSettings($countryCode);
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
				
			$check_wallet = $this->drivers_api->checkWallet($user_data->id, $countryCode);
			$rideStatus = $this->drivers_api->rideStatus($user_data->id, $countryCode);
			
			
			$cityride_min_balance = $this->site->minimumRide(1, $countryCode);
			$rental_min_balance = $this->site->minimumRide(2, $countryCode);
			$outstation_min_balance = $this->site->minimumRide(3, $countryCode);
			
			if($check_wallet == 0)
			{
				$data = array(
					'cityride_subscribe' => $rideStatus->is_daily,
					'cityride_status' => '0',
					'cityride_price' => $cityride_min_balance + $setting->wallet_min_add_money,
					'rentalride_subscribe' => $rideStatus->is_rental,
					'rentalride_status' => '0',
					'rentalride_price' => $rental_min_balance + $setting->wallet_min_add_money,
					'outstationride_subscribe' => $rideStatus->is_outstation,
					'outstationride_status' => '0',
					'outstationride_price' => $outstation_min_balance + $setting->wallet_min_add_money,
				);	
			}else{
				$cityride_check = $setting->wallet_min_add_money + $cityride_min_balance;
				$rental_check = $setting->wallet_min_add_money + $rental_min_balance;
				$outstation_check = $setting->wallet_min_add_money + $outstation_min_balance;
					
				$data = array(
					'cityride_subscribe' => $rideStatus->is_daily,
					'cityride_status' => $cityride_check > $check_wallet ? '0' : '1',
					'cityride_price' => $cityride_min_balance + $setting->wallet_min_add_money,
					'rentalride_subscribe' => $rideStatus->is_rental,
					'rentalride_status' => $rental_check > $check_wallet ? '0' : '1',
					'rentalride_price' => $rental_min_balance + $setting->wallet_min_add_money,
					'outstationride_subscribe' => $rideStatus->is_outstation,
					'outstationride_status' => $outstation_check > $check_wallet ? '0' : '1',
					'outstationride_price' => $outstation_min_balance + $setting->wallet_min_add_money,
				);
				
			}
			if(!empty($rideStatus)){
				$result = array( 'status'=> 1 , 'message' => 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message' => 'Ride type not active');
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
	public function driverstatus_post(){
		$this->form_validation->set_rules('mode', $this->lang->line("mode"), 'required');
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');

		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$setting = $this->site->RegsiterSettings($countryCode);
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['mode'] = $this->input->post('mode');
			$row['driver_id'] = $user_data->id;
			
			$check_wallet = $this->drivers_api->checkWallet($user_data->id, $countryCode);
			$rideStatus = $this->drivers_api->rideStatus($user_data->id, $countryCode);
			
			$cityride_min_balance = $this->site->minimumRide(1, $countryCode);
			$rental_min_balance = $this->site->minimumRide(2, $countryCode);
			$outstation_min_balance = $this->site->minimumRide(3, $countryCode);
			
			
			if($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 0){
				$total_check = $setting->wallet_min_add_money + $cityride_min_balance;
			}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
				$total_check = $setting->wallet_min_add_money + $rental_min_balance;
			}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
				$total_check = $setting->wallet_min_add_money + $outstation_min_balance;
			}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
				$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $rental_min_balance;
			}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
				$total_check = $setting->wallet_min_add_money + $outstation_min_balance + $rental_min_balance;
			}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
				$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance;
			}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
				$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance + $rental_min_balance;
			}else{
				$total_check = $setting->wallet_min_add_money;
			}
			
			if($total_check > $check_wallet){
				$result = array( 'status'=> 5 , 'message'=> 'Your wallet amount has been low. wallet minimum amount - '.$total_check.'. please addMoney after ride..');
			}else{
			
				$data = $this->drivers_api->driverUpdateStatus($row, $countryCode);
				
				if($data == 1){
					$result = array( 'status'=> 1 , 'message'=> 'Driver status has been updated');
				}elseif($data == 2){
					$result = array( 'status'=> 0, 'message'=> 'Your account details is not verified');
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Driver status has been not updated');
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
	
	public function walletoffer_post(){
		
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');

		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$setting = $this->site->RegsiterSettings($countryCode);
			
			$row['driver_id'] = $user_data->id;
			$data = $this->drivers_api->getWalletoffer($countryCode);
			
			$check_wallet = $this->drivers_api->checkWallet($user_data->id, $countryCode);
			$rideStatus = $this->drivers_api->rideStatus($user_data->id, $countryCode);
			
			$cityride_min_balance = $this->site->minimumRide(1, $countryCode);
			$rental_min_balance = $this->site->minimumRide(2, $countryCode);
			$outstation_min_balance = $this->site->minimumRide(3, $countryCode);
			
			$res = array();
			if(!empty($data)){
				foreach($data as $data_row){
					
					if($check_wallet == 0){
						
						$res[] = array(
							'id' => $data_row->id,
							'name' => $data_row->name,
							'amount' => $data_row->amount,
							'ride_amount' => $data_row->offer_amount,
							'wallet_adding' => $setting->wallet_min_add_money
						);
					}else{
						$res[] = array(
							'id' => $data_row->id,
							'name' => $data_row->name,
							'amount' => $data_row->amount,
							'ride_amount' => $data_row->offer_amount,
							'wallet_adding' => '0'
						);
					}
					
					/*if($data_row->type == 1 && $data_row->is_default == 1){
						
						if($check_wallet == 0){
							
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->wallet_min_add_money + $setting->cityride_min_balance,
								'ride_amount' => $setting->cityride_min_balance,
								'wallet_adding' => $setting->wallet_min_add_money
							);
						}else{
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->cityride_min_balance,
								'ride_amount' => $setting->cityride_min_balance,
								'wallet_adding' => '0'
							);
						}
					}
					
					if($data_row->type == 2 && $data_row->is_default == 1){
						
						if($check_wallet == 0){
							
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->wallet_min_add_money + $setting->rental_min_balance,
								'ride_amount' => $setting->rental_min_balance,
								'wallet_adding' => $setting->wallet_min_add_money
							);
						}else{
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->rental_min_balance,
								'ride_amount' => $setting->rental_min_balance,
								'wallet_adding' => '0'
							);
						}
					}
					
					if($data_row->type == 3 && $data_row->is_default == 1){
						
						if($check_wallet == 0){
							
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->wallet_min_add_money + $setting->outstation_min_balance,
								'ride_amount' => $setting->outstation_min_balance,
								'wallet_adding' => $setting->wallet_min_add_money
							);
						}else{
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $setting->outstation_min_balance,
								'ride_amount' => $setting->outstation_min_balance,
								'wallet_adding' => '0'
							);
						}
					}
					
					if($data_row->type == 0 && $data_row->is_default == 0){
						
						if($check_wallet == 0){
							
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $data_row->amount,
								'ride_amount' => $data_row->offer_amount,
								'wallet_adding' => $setting->wallet_min_add_money
							);
						}else{
							$res[] = array(
								'id' => $data_row->id,
								'name' => $data_row->name,
								'amount' => $data_row->amount,
								'ride_amount' => $data_row->offer_amount,
								'wallet_adding' => '0'
							);
						}
					}*/
					
					
					
				}
				
				$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $res);
			
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Empty data');
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
	
	
	public function taxistatus_post(){
		$this->form_validation->set_rules('mode', $this->lang->line("mode"), 'required');
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');

		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['mode'] = $this->input->post('mode');
			$row['driver_id'] = $user_data->id;
			$data = $this->drivers_api->taxiUpdateStatus($row, $countryCode);
			
			if($data == 1){
				$result = array( 'status'=> 1 , 'message'=> 'Taxi status has been updated');
			}elseif($data == 2){
				$result = array( 'status'=> 2, 'message'=> 'Already Allocated taxi. Please do not changes');
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Taxi status has been not updated');
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
			
			$data = $this->drivers_api->fcminsert($device, $countryCode);
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

			$data = $this->drivers_api->fcmdelete($device, $countryCode);
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
	
	public function mobilenumberverify_post(){
		$this->form_validation->set_rules('mobile_otp', $this->lang->line("mobile_otp"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');		
		if ($this->form_validation->run() == true) {
			
			if($this->input->post('mobile_otp') == $this->input->post('otp')){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => '1');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Mismatch OTP', 'data' => '2');
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
	
	public function upgradevendor_post(){
	
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
				
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
				
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $user_data = $this->drivers_api->getDriverextra($this->input->post('oauth_token'), $countryCode);
		   $driver_id = $user_data->id;
		   
		   $check_mobile = $this->drivers_api->checkMobileVendor($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
		   if($check_mobile == 1){
				$result = array( 'status'=> 0 , 'message'=> 'Mobile number already exit!');
				
		   }else{
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'first_name' => $user_data->first_name,
				'last_name' => $user_data->last_name,
				'gender' => $user_data->gender,
				'dob' => $user_data->dob,
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $user_data->country_code,
				'mobile' => $user_data->mobile,
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Admin,
				'parent_id' => 2,
				//'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Vendor,
				'is_edit' => 1
		   );
		   
		  
		   
		   $user_profile = array(
		   		'first_name' => $user_data->first_name,
				'last_name' => $user_data->last_name,
				'gender' => $user_data->gender,
				'dob' => $user_data->dob,
				'is_edit' => 1
			
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				$user_profile['photo'] = $this->upload_path.$user_data->photo;
			}
			
			$user_address = array(
				'is_edit' => 1
			);
			
			if ($_FILES['local_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/local_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('local_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$local_image = $this->upload->file_name;
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}
			
			if ($_FILES['permanent_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permanent_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permanent_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permanent_image = $this->upload->file_name;
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			
			$user_bank = array(
				'account_holder_name' => $user_data->account_holder_name,
				'account_no' => $user_data->account_no,
				'bank_name' => $user_data->bank_name,
				'branch_name' => $user_data->branch_name,
				'ifsc_code' => $user_data->ifsc_code,
				'is_edit' => 1
			);
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'is_edit' => 1

			);
			
			$user_document = array(
				'is_edit' => 1
				
			);
			
			
			
			if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('aadhaar_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$aadhaar_image = $this->upload->file_name;
				$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
			}
			
			if ($_FILES['pancard_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/pancard/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('pancard_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$pancard_image = $this->upload->file_name;
				$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$loan_doc = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$loan_doc;
				$config = NULL;
			}
			
			
			
			
				
				
				$data = $this->drivers_api->add_vendor($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor,$this->Vendor, $driver_id, $countryCode);
				
				if($data == TRUE){
					
					$result = array( 'status'=> 1 , 'message'=> 'Registered Successfully!. Waiting for admin approval process');
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Data is empty');
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
	
	public function register_post(){
		$current_date = date('Y-m-d');
		
        $this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');  
        $this->input->post('mobile_verify');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		$countryCode = $this->input->post('is_country');
		$operator = $this->input->post('operator') ? $this->input->post('operator') : 0;
				
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		    $mobile_otp = random_string('numeric', 6);
			   
			$check_mobile = $this->drivers_api->checkMobile($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			
			$setting = $this->drivers_api->getSettings($countryCode);
			
			
			$refer_code = $this->site->refercode('D', $countryCode); 
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
				
				/*if(!empty($this->input->post('ref_mobile'))){
			   		$check_ref_mobile = $this->drivers_api->checkRef($this->input->post('ref_mobile'), $countryCode);
					if($check_ref_mobile == 0){
						$result = array( 'status'=> 0 , 'message'=> 'Your ref code mismatch. please enter correct code.');
						$this->response($result);
						exit;
					}
				}*/
				
				if(!empty($this->input->post('reference_no'))){
					$check_reference = $this->drivers_api->checkCode($this->input->post('reference_no'), 'D');
					
					if($check_reference == 0){
						$result = array( 'status'=> 0 , 'message'=> 'Refer code is invaild');	
						$this->response($result);exit;
					}elseif($check_reference->code_end < $current_date){
						$result = array( 'status'=> 0 , 'message'=> 'Refer code is expiry.');	
						$this->response($result);exit;
					}
				}
				
				
				$parent_group = 0;
				$parent_id = 0;
				
			   $operator = $this->input->post('operator') ? $this->input->post('operator') : 0;
			  
			  
			
			   $user = array(
					'oauth_token' => $oauth_token,
					'devices_imei' => $this->input->post('devices_imei'),
					'join_type' => 2,
					'email' => $this->input->post('email') ? $this->input->post('email') : '',
					'password' => md5($this->input->post('password')),
					'text_password' => $this->input->post('password'),
					'country_code' => $this->input->post('country_code'),
					//'ref_mobile' => $this->input->post('ref_mobile') ? $this->input->post('ref_mobile') : 'Directly',
					'mobile' => $this->input->post('mobile'),
					'mobile_otp' => $mobile_otp,
					'parent_type' => $parent_group,
					'parent_id' => $parent_id,
					'refer_code' => $refer_code,
					'reference_no' => $this->input->post('reference_no') != NULL ? $this->input->post('reference_no') : '',
					//'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('y-m-d H:i:s'),
					'group_id' => $this->Driver,
					'active' => 1,
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name') ? $this->input->post('last_name') : '',
					'gender' => $this->input->post('gender') ? $this->input->post('gender') : '',
					/*'is_daily' => $is_daily,
					'is_rental' => $this->input->post('is_rental'),
					'is_outstation' => $this->input->post('is_outstation'),
					'is_hiring' => $this->input->post('is_hiring') ? $this->input->post('is_hiring') : 0,
					'is_corporate' => $this->input->post('is_corporate') ? $this->input->post('is_corporate') : 0,*/
					'is_edit' => 1
			   );
			   
			   $user_profile = array(
					'first_name' => $this->input->post('first_name') ? $this->input->post('first_name') : '',
					'last_name' => $this->input->post('last_name') ? $this->input->post('last_name') : '',
					'gender' => $this->input->post('gender') ? $this->input->post('gender') : '',
					'is_edit' => 1
			   );
			   
			   if ($_FILES['photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'user/driver/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$photo = $this->upload->file_name;
					$user_profile['photo'] = 'user/driver/'.$photo;
					$config = NULL;
				}
				
				$user_address = array(
					'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
					'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
					'is_edit' => 1
				);
				
				if ($_FILES['local_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/local_address/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('local_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$local_image = $this->upload->file_name;
					$user_address['local_image'] = 'document/local_address/'.$local_image;
					$config = NULL;
				}
				
				if ($_FILES['permanent_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/permanent_address/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('permanent_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$permanent_image = $this->upload->file_name;
					$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
					$config = NULL;
				}
				
				$user_bank = array(
					'account_holder_name' => $this->input->post('account_holder_name') ? $this->input->post('account_holder_name') : '',
					'account_no' => $this->input->post('account_no') ? $this->input->post('account_no') : '',
					'bank_name' => $this->input->post('bank_name') ? $this->input->post('bank_name') : '',
					'branch_name' => $this->input->post('branch_name') ? $this->input->post('branch_name') : '',
					'ifsc_code' => $this->input->post('ifsc_code') ? $this->input->post('ifsc_code') : '',
					'is_edit' => 1
				);
				
				
				
				
				
				$user_document = array(
					'is_edit' => 1,
					'license_no' => $this->input->post('license_no') ? $this->input->post('license_no') : '',		
					'additional_contact_one_type' => $this->input->post('additional_contact_one_type') ? $this->input->post('additional_contact_one_type') : '',
					'additional_contact_one_name' => $this->input->post('additional_contact_one_name') ? $this->input->post('additional_contact_one_name') : '',
					'additional_contact_one_phone' => $this->input->post('additional_contact_one_phone') ? $this->input->post('additional_contact_one_phone') : '',
					'additional_contact_one_phonecode' => $this->input->post('additional_contact_one_phonecode') ? $this->input->post('additional_contact_one_phonecode') : '',
					'additional_contact_two_type' => $this->input->post('additional_contact_two_type') ? $this->input->post('additional_contact_two_type') : '',
					'additional_contact_two_name' => $this->input->post('additional_contact_two_name') ? $this->input->post('additional_contact_two_name') : '',
					'additional_contact_two_phone' => $this->input->post('additional_contact_two_phone') ? $this->input->post('additional_contact_two_phone') : '',
					'additional_contact_two_phonecode' => $this->input->post('additional_contact_two_phonecode') ? $this->input->post('additional_contact_two_phonecode') : '',	
					
				);
				
				if ($_FILES['aadhaar_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/aadhaar/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('aadhaar_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$aadhaar_image = $this->upload->file_name;
					$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
					$config = NULL;
				}
				
				
				if ($_FILES['pancard_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/pancard/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('pancard_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$pancard_image = $this->upload->file_name;
					$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
					
					$config = NULL;
				}
				
						
				
				
				if ($_FILES['license_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/license/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('license_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$license_image = $this->upload->file_name;
					$user_document['license_image'] = 'document/license/'.$license_image;
					$config = NULL;
				}
				
				if ($_FILES['police_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/police/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('police_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$police_image = $this->upload->file_name;
					$user_document['police_image'] = 'document/police/'.$police_image;
					$config = NULL;
				}
				if ($_FILES['badge_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/badge/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('badge_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$badge_image = $this->upload->file_name;
				$user_document['badge_image'] = 'document/badge/'.$badge_image;
				$config = NULL;
			}
			
			if ($_FILES['training_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/training_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('training_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$training_certificate_image = $this->upload->file_name;
				$user_document['training_certificate_image'] = 'document/training_certificate/'.$training_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['experience_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/experience_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('experience_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$experience_certificate_image = $this->upload->file_name;
				$user_document['experience_certificate_image'] = 'document/experience_certificate/'.$experience_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['medical_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/medical_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('medical_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$medical_certificate_image = $this->upload->file_name;
				$user_document['medical_certificate_image'] = 'document/medical_certificate/'.$medical_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['health_insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/health_insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('health_insurance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$health_insurance_image = $this->upload->file_name;
				$user_document['health_insurance_image'] = 'document/health_insurance/'.$health_insurance_image;
				$config = NULL;
			}
			
			if ($_FILES['term_insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/term_insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('term_insurance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$term_insurance_image = $this->upload->file_name;
				$user_document['term_insurance_image'] = 'document/term_insurance/'.$term_insurance_image;
				$config = NULL;
			}
				
				
				$make_name = $this->drivers_api->getTaxinameBYID($this->input->post('make'), $countryCode);
				$model_name = $this->drivers_api->getTaximodelBYID($this->input->post('model'), $countryCode);
				$type_name = $this->drivers_api->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			
				$taxi = array(
					'name' => $this->input->post('name'),
					'model' => $model_name,
					'model_id' => $this->input->post('model'),
					'number' => $this->input->post('number'),
					'type' => $this->input->post('type'),
					'type_name' => $type_name,
					'multiple_type' => $this->input->post('type'),
					'engine_number' => $this->input->post('engine_number'),
					'chassis_number' => $this->input->post('chassis_number'),
					'make_id' => $this->input->post('make'),
					'make' => $make_name,
					'fuel_type' => $this->input->post('fuel_type'),
					'color' => $this->input->post('color'),
					'manufacture_year' => $this->input->post('manufacture_year'),
					
					'capacity' => $this->input->post('capacity'),
					'board' => $this->input->post('board'),
					//'ac' => $this->input->post('ac'),
					//'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('y-m-d H:i:s'),
					'is_edit' => 1
			   );
			   
			   
			   
			   if ($_FILES['taxi_photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxi/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_photo')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxi_photo = $this->upload->file_name;
					$taxi['photo'] = 'document/taxi/'.$taxi_photo;
					$config = NULL;
				}
				
				$taxi_document = array(
					'is_edit' => 1,
					'emission_norms' => $this->input->post('emission_norms'),
					
				);
				
				if ($_FILES['reg_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/register/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('reg_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$reg_image = $this->upload->file_name;
					$taxi_document['reg_image'] = 'document/register/'.$reg_image;
					$config = NULL;
				}
				
				if ($_FILES['taxation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxation_image = $this->upload->file_name;
					$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
					$config = NULL;
				}
				
				if ($_FILES['insurance_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/insurance/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('insurance_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$insurance_image = $this->upload->file_name;
					$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
					$config = NULL;
				}
				
				if ($_FILES['permit_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/permit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('permit_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$permit_image = $this->upload->file_name;
					$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
					$config = NULL;
				}
				
				if ($_FILES['authorisation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/authorisation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('authorisation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$authorisation_image = $this->upload->file_name;
					$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
					$config = NULL;
				}
				
				if ($_FILES['fitness_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/fitness/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('fitness_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$fitness_image = $this->upload->file_name;
					$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
					$config = NULL;
				}
				
				if ($_FILES['speed_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/speed_limit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('speed_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$speed_image = $this->upload->file_name;
					$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
					$config = NULL;
				}
				
				if ($_FILES['puc_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/puc/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('puc_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$puc_image = $this->upload->file_name;
					$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
					$config = NULL;
				}
				if ($_FILES['vehicle_tracking_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/vehicle_tracking/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('vehicle_tracking_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$vehicle_tracking_image = $this->upload->file_name;
				$taxi_document['vehicle_tracking_image'] = 'document/vehicle_tracking/'.$vehicle_tracking_image;
				$config = NULL;
			}
			
			if ($_FILES['fire_extinguisher_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fire_extinguisher/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('fire_extinguisher_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$fire_extinguisher_image = $this->upload->file_name;
				$taxi_document['fire_extinguisher_image'] = 'document/fire_extinguisher/'.$fire_extinguisher_image;
				$config = NULL;
			}
			
			
			if ($_FILES['child_lock_mechanism_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/child_lock_mechanism/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('child_lock_mechanism_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$child_lock_mechanism_image = $this->upload->file_name;
				$taxi_document['child_lock_mechanism_image'] = 'document/child_lock_mechanism/'.$child_lock_mechanism_image;
				$config = NULL;
			}
			
			
			if ($_FILES['interior_vehicle_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/interior_vehicle/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('interior_vehicle_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$interior_vehicle_image = $this->upload->file_name;
				$taxi_document['interior_vehicle_image'] = 'document/interior_vehicle/'.$interior_vehicle_image;
				$config = NULL;
			}
			
			
			if ($_FILES['taxi_roof_sign_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_roof_sign/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxi_roof_sign_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_roof_sign_image = $this->upload->file_name;
				$taxi_document['taxi_roof_sign_image'] = 'document/taxi_roof_sign/'.$taxi_roof_sign_image;
				$config = NULL;
			}
			
			if ($_FILES['e_challans_clearance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/e_challans_clearance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('e_challans_clearance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$e_challans_clearance_image = $this->upload->file_name;
				$taxi_document['e_challans_clearance_image'] = 'document/e_challans_clearance/'.$e_challans_clearance_image;
				$config = NULL;
			}
				
					$data = $this->drivers_api->add_driver_new($user, $user_profile, $user_address, $user_bank, $user_document, $this->Driver, $operator, $countryCode, $refer_code, $reference_no);
					
					
					
					if($data == TRUE){
						$notification['title'] = 'Driver Register';
						$notification['message'] = 'New user('.$this->input->post('first_name').') has been register hayycab';
						$notification['user_type'] = 4;
						$notification['user_id'] = 2;
						$this->drivers_api->insertNotification($notification, $countryCode);
						
						$sms_message = $this->input->post('first_name').' your account has been register successfully. Waiting for admin approval process.Your refer code : '.$refer_code.'';
						$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
						$sms_country_code = $this->input->post('country_code');

						$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
						$result = array( 'status'=> 1 , 'message'=> 'Registered Successfully!. Waiting for admin approval process', 'oauth_token' => $oauth_token);
					}else{
						$result = array( 'status'=> 0 , 'message'=> 'Data is empty');
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
	
	
	
	public function verify_allocatedopenotp_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		$this->form_validation->set_rules('taxi_number', $this->lang->line("taxi_number"), 'required');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['taxi_number'] = $this->input->post('taxi_number');
			$row['driver_id'] = $user_data->id;
			$row['devices_imei'] = $this->input->post('devices_imei');
			$first_name = $user_data->first_name;
			$data = $this->drivers_api->checkallocatedopenotp($row, $countryCode);
			$details[] = $this->drivers_api->getDriversIDallocated($user_data->id, $countryCode);
			if($data){
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				
				$response_sms = $this->sms_allocated_driver( $first_name, $this->input->post('taxi_number'), $sms_phone, $sms_country_code);
				if($response_sms){
				 $result = array( 'status'=> true , 'message'=> 'login has been success.', 'data' => $details);
				} else {
					$result = array( 'status'=> true , 'message'=> 'login has been success.', 'data' => $details);
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
	
	public function close_allocatedopen_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			
			$row['driver_id'] = $user_data->id;
			$first_name = $user_data->first_name;
			$taxi_number = $this->drivers_api->getDriverAllocatedTaxi($user_data->id, $countryCode);
			$data = $this->drivers_api->closeallocatedopen($row, $countryCode);
			if($data){
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				
				$response_sms = $this->sms_allocated_closedriver( $first_name, $taxi_number, $sms_phone, $sms_country_code);
				if($response_sms){
				 $result = array( 'status'=> true , 'message'=> 'Your riding has been completed.', 'data' => $details);
				} else {
					$result = array( 'status'=> true , 'message'=> 'Your riding has been completed.', 'data' => $details);
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
	
	public function login_post(){
		$countryCode = $this->input->post('is_country');
		$token = get_random_key(32,'users','oauth_token',$type='alnum');
		$otp = random_string('numeric', 6);
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required|numeric');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');	
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');	
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$setting = $this->site->RegsiterSettings($countryCode);
			
			$login['country_code'] = $this->input->post('country_code');
			$login['mobile'] = $this->input->post('mobile');
			$login['devices_imei'] = $this->input->post('devices_imei');
			$login['password'] = md5($this->input->post('password'));
			$login['otp'] = $otp;
			
			$res = $this->drivers_api->check_login($login, $countryCode);
			$cityride_min_balance = $this->site->minimumRide(1, $countryCode);
			$rental_min_balance = $this->site->minimumRide(2, $countryCode);
			$outstation_min_balance = $this->site->minimumRide(3, $countryCode);
			
				
			$data[]  = $res;
			if($res->check_status == 1){
				
				$this->site->insertLog($data[0]->id, '1', '1');
				
				if($res->taxi_status == 1 && $res->document_status == 1 && $res->bank_status == 1){
					//print_r($data[0]->id);die;
					$check_wallet = $this->drivers_api->checkWallet($data[0]->id, $countryCode);
					$rideStatus = $this->drivers_api->rideStatus($data[0]->id, $countryCode);
					
					
					
					if($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $rental_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $outstation_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $rental_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $outstation_min_balance + $rental_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance + $rental_min_balance;
					}else{
						$total_check = $setting->wallet_min_add_money;
					}
					
					
					
					if($total_check > $check_wallet){
						$result = array( 'status'=> 5 , 'message'=> 'Your wallet amount has been low. wallet minimum amount - '.$setting->wallet_min_add_money.'. please addMoney after ride..', 'data' => $data);
					}else{
						/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
							$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
						}else{*/
							$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
						//}
					}
					
					
				}else{
					$result = array( 'status'=> 4 , 'message'=> 'Taxi Details missing, please provide the taxi details to proceed further', 'data' => $data, 'oauth_token' => $res->oauth_token, 'taxi_status' => $res->taxi_status, 'document_status' => $res->document_status, 'bank_status' => $res->bank_status);
				}
				
				//if($res->taxi_status == 1 && $res->document_status == 1 && $res->bank_status == 1){
					
					//$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				//}else{
					//$result = array( 'status'=> 4 , 'message'=> 'Taxi Details missing, please provide the taxi details to proceed further', 'data' => $data, 'oauth_token' => $res->oauth_token, 'taxi_status' => $res->taxi_status, 'document_status' => $res->document_status, 'bank_status' => $res->bank_status);
				//}
				
			}elseif($res->check_status == 4){
				
				/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
							$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
				}else{*/
				$this->site->insertLog($data[0]->id, '1', '1');
				if($res->devices_imei != $this->input->post('devices_imei')){
					$socket_id = $this->site->getSocketID($res->id, 2, $countryCode);
					
					$event = 'server_other_login';
					
					$edata = array(
						'socket_id' => $socket_id,
						'devices_imei' => $res->devices_imei,
						'msg' => 'Truncate'
					);
					$emit_login = $this->socketemitter->setEmit($event, $edata);
					$last_emit_array = array(
						'user_type_id' => 2,
						'user_id' => $res->id,
						'emit_name' => 'other_login',
						'emit_data' => json_encode($edata),
						'created_on' => date('Y-m-d H:i:s')
					);
					$this->site->insertLastEmit($last_emit_array);
				}
				
				if($res->taxi_status == 1 && $res->document_status == 1 && $res->bank_status == 1){
					//print_r($data[0]->id);die;
					$check_wallet = $this->drivers_api->checkWallet($data[0]->id, $countryCode);
					$rideStatus = $this->drivers_api->rideStatus($data[0]->id, $countryCode);
					
					if($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $rental_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $outstation_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 0){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $rental_min_balance;
					}elseif($rideStatus->is_daily == 0 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $outstation_min_balance + $rental_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 0 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance;
					}elseif($rideStatus->is_daily == 1 && $rideStatus->is_rental == 1 && $rideStatus->is_outstation == 1){
						$total_check = $setting->wallet_min_add_money + $cityride_min_balance + $outstation_min_balance + $rental_min_balance;
					}else{
						$total_check = $setting->wallet_min_add_money;
					}
					//echo $check_wallet;
					//echo $total_check;
					//die;
					
					if($total_check > $check_wallet){
						$result = array( 'status'=> 5 , 'message'=> 'Your wallet amount has been low. wallet minimum amount - '.$setting->wallet_min_add_money.'. please addMoney after ride..', 'data' => $data);
					}else{
						
							$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
						
					}
					
					
					
				}else{
					$result = array( 'status'=> 4 , 'message'=> 'Taxi Details missing, please provide the taxi details to proceed further', 'data' => $data, 'oauth_token' => $res->oauth_token, 'taxi_status' => $res->taxi_status, 'document_status' => $res->document_status, 'bank_status' => $res->bank_status);
				}
				
				//}
				
			}elseif($res->check_status == 3){
				
				$this->site->insertLog($data[0]->id, '1', '1');
				
				/*if($res->devices_imei != $this->input->post('devices_imei') && $res->check_ride != 0){
					$result = array( 'status'=> 0 , 'message'=> 'This device not login.For another device currently ride.');
				}else{*/
				
					if($res->devices_imei != $this->input->post('devices_imei')){
						$socket_id = $this->site->getSocketID($res->id, 2, $countryCode);
						
						$event = 'server_other_login';
						
						$edata = array(
							'socket_id' => $socket_id,
							'devices_imei' => $res->devices_imei,
							'msg' => 'Truncate'
						);
						$emit_login = $this->socketemitter->setEmit($event, $edata);
						$last_emit_array = array(
							'user_type_id' => 2,
							'user_id' => $res->id,
							'emit_name' => 'other_login',
							'emit_data' => json_encode($edata),
							'created_on' => date('Y-m-d H:i:s')
						);
						$this->site->insertLastEmit($last_emit_array);
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
				
				
			}elseif($res->check_status == 2){
				$result = array( 'status'=> 0 , 'message'=> 'Your account has been deactive. please contact admin.');
			}elseif($res->check_status == 0){
				$result = array( 'status'=> 0 , 'message'=> 'Invalid Password!!!');
			}elseif($res->check_status == 5){
				$result = array( 'status'=> 0 , 'message'=> 'Your account not added. please singup...');
			}elseif($res->check_status == 7){
				$result = array( 'status'=> 0 , 'message'=> 'Your Timilimit for today is over. Next ride allocation starts from tomorrow');
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
	
	
	public function verify_firstotp_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['driver_id'] = $user_data->id;
			$row['devices_imei'] = $this->input->post('devices_imei');
			$res = $this->drivers_api->checkfirstotp($row, $countryCode);
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
	
	public function verify_changeotp_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['devices_imei'] = $this->input->post('devices_imei');
			$row['customer_id'] = $user_data->id;
			$res = $this->drivers_api->devicescheckotp($row, $countryCode);
			$data[] = $res;
			if($res){
				
				if($res->devices_imei != $this->input->post('devices_imei')){
					$socket_id = $this->site->getSocketID($res->id, 2, $countryCode);
					
					$event = 'server_other_login';
					
					$edata = array(
						'socket_id' => $socket_id,
						'devices_imei' => $res->devices_imei,
						'msg' => 'Truncate'
					);
					$emit_login = $this->socketemitter->setEmit($event, $edata);
					$last_emit_array = array(
						'user_type_id' => 2,
						'user_id' => $res->id,
						'emit_name' => 'other_login',
						'emit_data' => json_encode($edata),
						'created_on' => date('Y-m-d H:i:s')
					);
					$this->site->insertLastEmit($last_emit_array);
				}
				
				if($res->taxi_status == 1 && $res->document_status == 1 && $res->bank_status == 1){
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> 4 , 'message'=> 'Taxi Details missing, please provide the taxi details to proceed further', 'data' => $data, 'oauth_token' => $res->oauth_token, 'taxi_status' => $res->taxi_status, 'document_status' => $res->document_status, 'bank_status' => $res->bank_status);
				}
				
				//$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'invaild otp. please check otp');
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
	
	public function verify_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['otp'] = $this->input->post('otp');
			$row['driver_id'] = $user_data->id;
			$res = $this->drivers_api->checkotp($row, $countryCode);
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
	
	public function resend_otp_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['driver_id'] = $user_data->id;
			//$row['mobile_otp'] = random_string('numeric', 6);
			
			$data = $this->drivers_api->resendotp($row, $countryCode);
			if($data){
				
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				$sms_phone_otp = $row['mobile_otp'];

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
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$row['mobile'] = $this->input->post('mobile');
			$row['country_code'] = $this->input->post('country_code');
			$row['forgot_otp'] = random_string('numeric', 6);
			
			$data = $this->drivers_api->forgototp($row, $countryCode);
			if($data){
				
				$sms_phone = $data->country_code . $data->mobile;
				$sms_country_code = $data->country_code;
				$sms_phone_otp = $row['forgot_otp'];

				$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
				if($response_sms){
					$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $data);
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
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver_id"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			$row['forgot_otp'] = $this->input->post('otp');
			$row['driver_id'] = $this->input->post('driver_id');
			$data = $this->drivers_api->forgotcheckotp($row, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $this->input->post('driver_id'));
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
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver_id"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['driver_id'] = $this->input->post('driver_id');
			//$row['forgot_otp'] = random_string('numeric', 6);
			
			$data = $this->drivers_api->forgotresendotp($row, $countryCode);
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
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver_id"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$customer['password'] = md5($this->input->post('password'));
			$customer['driver_id'] = $this->input->post('driver_id');
			$customer['text_password'] = $this->input->post('password');
			
			$data = $this->drivers_api->updatepassword($customer, $countryCode);
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
	
	public function my_account_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_type = '1'; //1 - basic details, 2- bank details, 3- document
			$data = $this->drivers_api->myprofile($user_data->id, $this->Driver, $driver_type, $countryCode);
			
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
	
	public function edit_account_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 1;
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('dob')) ||!empty($this->input->post('email')) || !empty($this->input->post('first_name')) || !empty($this->input->post('last_name')) || !empty($this->input->post('gender')) || $_FILES['photo']['size'] > 0 ||   $_FILES['local_image']['size'] > 0 || $_FILES['permanent_image']['size'] > 0 ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender') && $res->dob == $this->input->post('dob')){
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
				$dob = $res->dob;
			}
			
			
			
		   $user = array(
				'email' => $this->input->post('email') != '' ? $this->input->post('email') : $res->email,
				'first_name' => $this->input->post('first_name') != '' ? $this->input->post('first_name') : $res->first_name,
				'last_name' => $this->input->post('last_name') != '' ? $this->input->post('last_name') : $res->last_name,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
				'gender' => $this->input->post('gender') != '' ? $this->input->post('gender') : $res->gender,
				'dob' => $dob,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name') != '' ? $this->input->post('first_name') : $res->first_name,
				'last_name' => $this->input->post('last_name') != '' ? $this->input->post('last_name') : $res->last_name,
				
				'gender' => $this->input->post('gender') != '' ? $this->input->post('gender') : $res->gender,
				'dob' => $dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
				'is_edit' => 1,
				'complete_profile' => 1
			
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/driver/'.$photo;
				$user['photo'] = 'user/driver/'.$photo;
				
				$config = NULL;
			}else{
				$user_profile['photo'] = $res->photo;
				$user['photo'] = $res->photo;
			}
			
			if($_FILES['local_image']['size'] == 0 && $this->input->post('local_pincode') == $res->local_pincode){
				$local_verify = $res->local_verify;
				$local_approved_by = $res->local_approved_by;
				$local_approved_on = $res->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['permanent_image']['size'] == 0  && $this->input->post('permanent_pincode') == $res->permanent_pincode){
				$permanent_verify = $res->permanent_verify;
				$permanent_approved_by = $res->permanent_approved_by;
				$permanent_approved_on = $res->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				
				'local_address' => $this->input->post('local_address') != '' ? $this->input->post('local_address') : $res->local_address,
				'permanent_address' => $this->input->post('permanent_address') != '' ? $this->input->post('permanent_address') : $res->permanent_address,
				
				'local_pincode' => $this->input->post('local_pincode') != '' ? $this->input->post('local_pincode') : $res->local_pincode,
				'permanent_pincode' => $this->input->post('permanent_pincode') != '' ? $this->input->post('permanent_pincode') : $res->permanent_pincode,
					
				'is_edit' => 1,
				'complete_address' => 1
			);
			
			if ($_FILES['local_image']['size'] > 0) {
				
				$config['upload_path'] = $this->upload_path.'document/local_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('local_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$local_image = $this->upload->file_name;
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}else{
				$user_address['local_image'] = $res->local_image;
			}
			
			if ($_FILES['permanent_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permanent_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permanent_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permanent_image = $this->upload->file_name;
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}else{
				$user_address['permanent_image'] = $res->permanent_image;
			}
			
			
			
			$user_bank = array();
			
			
		
			$user_document = array();
			
				
				$data = $this->drivers_api->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $customer_type, $countryCode);
				
				if($data){
					$notification['title'] = 'Driver profile edit';
					$notification['message'] = $user_data->first_name.' has been profile edited';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->drivers_api->insertNotification($notification, $countryCode);
					
					$result = array( 'status'=> 1, 'message' => 'Driver edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'driver does not edit.');
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
	
	public function my_bank_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_type = '2'; //1 - basic details, 2- bank details, 3- document
			$data = $this->drivers_api->myprofile($user_data->id, $this->Driver, $driver_type, $countryCode);
			
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
	
	public function edit_bank_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 2;
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('account_holder_name')) || !empty($this->input->post('account_no')) || !empty($this->input->post('bank_name')) || !empty($this->input->post('branch_name')) || !empty($this->input->post('ifsc_code')) ){	
		    		   
		   
		   $user = array();
		   
		   $user_profile = array();
		   
		   
			$user_address = array();
			
			
			
			if($this->input->post('account_holder_name') == $res->account_holder_name && $this->input->post('account_no') == $res->account_no && $this->input->post('bank_name') == $res->bank_name && $this->input->post('branch_name') == $res->branch_name && $this->input->post('ifsc_code') == $res->ifsc_code){
				$account_verify = $res->account_verify;
				$account_approved_by = $res->account_approved_by;
				$account_approved_on = $res->account_approved_on;
			}else{
				$account_verify = 0;
				$account_approved_by = 0;
				$account_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name') != '' ? $this->input->post('account_holder_name') : $res->account_holder_name,
				'account_no' => $this->input->post('account_no') != '' ? $this->input->post('account_no') : $res->account_no,
				'bank_name' => $this->input->post('bank_name') != '' ? $this->input->post('bank_name') : $res->bank_name,
				'branch_name' => $this->input->post('branch_name') != '' ? $this->input->post('branch_name') : $res->branch_name,
				'ifsc_code' => $this->input->post('ifsc_code') != '' ? $this->input->post('ifsc_code') : $res->ifsc_code,
				'is_verify' => $account_verify,
				'approved_by' => $account_approved_by,
				'approved_on' => $account_approved_on,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			
		
			$user_document = array();
			
				
				
				$data = $this->drivers_api->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $customer_type, $countryCode);
				
				if($data){
					$notification['title'] = 'Driver bank edit';
					$notification['message'] = $user_data->first_name.' has been bank edited';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->drivers_api->insertNotification($notification, $countryCode);
					$result = array( 'status'=> 1, 'message' => 'Driver edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'driver does not edit.');
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
	
	public function my_document_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$driver_type = '3'; //1 - basic details, 2- bank details, 3- document
			$data = $this->drivers_api->myprofile($user_data->id, $this->Driver, $driver_type, $countryCode);
			
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
	
	public function edit_document_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 3;
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if( $_FILES['aadhaar_image']['size'] > 0  || $_FILES['pancard_image']['size'] > 0  || $_FILES['license_image']['size'] > 0  || $_FILES['police_image']['size'] > 0  || !empty($this->input->post('license_no')) ){	
		    		
					  
		  
			
		   $user = array();
		   
		   $user_profile = array();
		   
			$user_address = array();
			
			
			
			if($this->input->post('account_no') == $res->account_no && $this->input->post('bank_name') == $res->bank_name && $this->input->post('branch_name') == $res->branch_name && $this->input->post('ifsc_code') == $res->ifsc_code){
				$account_verify = $res->account_verify;
				$account_approved_by = $res->account_approved_by;
				$account_approved_on = $res->account_approved_on;
			}else{
				$account_verify = 0;
				$account_approved_by = 0;
				$account_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_bank = array();
			
			
			if($_FILES['aadhaar_image']['size'] == 0){
				$aadhar_verify = $res->aadhar_verify;
				$aadhar_approved_by = $res->aadhar_approved_by;
				$aadhar_approved_on = $res->aadhar_approved_on;
			}else{
				$aadhar_verify = 0;
				$aadhar_approved_by = 0;
				$aadhar_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['pancard_image']['size'] == 0){
				$pancard_verify = $res->pancard_verify;
				$pancard_approved_by = $res->pancard_approved_by;
				$pancard_approved_on = $res->pancard_approved_on;
			}else{
				$pancard_verify = 0;
				$pancard_approved_by = 0;
				$pancard_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['license_image']['size'] == 0 && $this->input->post('license_no') == $res->license_no){
				$license_verify = $res->license_verify;
				$license_approved_by = $res->license_approved_by;
				$license_approved_on = $res->license_approved_on;
			}else{
				$license_verify = 0;
				$license_approved_by = 0;
				$license_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['police_image']['size'] == 0){
				$police_verify = $res->police_verify;
				$police_approved_by = $res->police_approved_by;
				$police_approved_on = $res->police_approved_on;
			}else{
				$police_verify = 0;
				$police_approved_by = 0;
				$police_approved_on = '0000:00:00 00:00:00';
			}
		
			$user_document = array(
				
				'aadhaar_no' => $this->input->post('aadhaar_no') != '' ? $this->input->post('aadhaar_no') : $res->aadhaar_no,
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $this->input->post('pancard_no') != '' ? $this->input->post('pancard_no') : $res->pancard_no,
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				'license_no' => $this->input->post('license_no') != '' ? $this->input->post('license_no') : $res->license_no,
				'license_dob' => $this->input->post('license_dob') != '' ? $this->input->post('license_dob') : $res->license_dob,
				'license_ward_name' => $this->input->post('license_ward_name') != '' ? $this->input->post('license_ward_name') : $res->license_ward_name,
				'license_type' => $this->input->post('license_type') != '' ? json_encode($this->input->post('license_type')) : $res->license_type,
				'license_country_id' => $this->input->post('license_country_id') != '' ? $this->input->post('license_country_id') : $res->license_country_id,
				'license_issuing_authority' => $this->input->post('license_issuing_authority') != '' ? $this->input->post('license_issuing_authority') : $res->license_issuing_authority,
				'license_issued_on' => $this->input->post('license_issued_on') != '' ? $this->input->post('license_issued_on') : $res->license_issued_on,
				'license_validity' => $this->input->post('license_validity') != '' ? $this->input->post('license_validity') : $res->license_validity,
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $this->input->post('police_on') != '' ? $this->input->post('police_on') : $res->police_on,
				'police_til' => $this->input->post('police_til') != '' ? $this->input->post('police_til') : $res->police_til,
				
				'police_verify' => $police_verify,
				'police_approved_by' => $police_approved_by,
				'police_approved_on' => $police_approved_on,	
				'is_edit' => 1	,
				'complete_document' => 1	
				
			);
			
				if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('aadhaar_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$aadhaar_image = $this->upload->file_name;
				$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
			}else{
				$user_document['aadhaar_image'] = $res->aadhaar_image;
			}
			
			if ($_FILES['pancard_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/pancard/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('pancard_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$pancard_image = $this->upload->file_name;
				$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}else{
				$user_document['pancard_image'] = $res->pancard_image;
			}
			
					
			
			
			if ($_FILES['license_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/license/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('license_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$license_image = $this->upload->file_name;
				$user_document['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
			}else{
				$user_document['license_image'] = $res->license_image;
			}
			
			if ($_FILES['police_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/police/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('police_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}else{
				$user_document['police_image'] = $res->police_image;
			}
				
				$data = $this->drivers_api->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $customer_type, $countryCode);
				
				if($data){
					$notification['title'] = 'Driver document edit';
					$notification['message'] = $user_data->first_name.' has been document edited';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->drivers_api->insertNotification($notification);
					$result = array( 'status'=> 1, 'message' => 'Driver edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'driver does not edit.');
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
	
	public function my_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->myprofile($user_data->id, $this->Driver, $countryCode);
			
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
	
	public function driverpayment_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->driverpayment($user_data->id, $countryCode);
			
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
	
	public function edit_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('customer_type', $this->lang->line("customer_type"), 'required');
		
		$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'));
		$user_id = $user_data->id;
		$res = $this->customer_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = $this->input->post('customer_type');
		if($customer_type == 1){
			$this->form_validation->set_rules('email', $this->lang->line("email"), 'required');
			$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
			$this->form_validation->set_rules('gender', $this->lang->line("gender"), 'required');
			if ($res->local_image == '' && empty($_FILES['local_image']['name']))
			{
				
				$this->form_validation->set_rules('local_image', 'local_image', 'required');
				
			}
			if ($res->permanent_image == '' && empty($_FILES['permanent_image']['name']))
			{
				$this->form_validation->set_rules('permanent_image', 'permanent_image', 'required');
			}
		}elseif($customer_type == 2){
			$this->form_validation->set_rules('account_no', $this->lang->line("account_no"), 'required');
			//$this->form_validation->set_rules('bank_name', $this->lang->line("bank_name"), 'required');
			//$this->form_validation->set_rules('branch_name', $this->lang->line("branch_name"), 'required');
			//$this->form_validation->set_rules('ifsc_code', $this->lang->line("ifsc_code"), 'required');
			}elseif($customer_type == 3){
			if ($res->aadhaar_image == '' && empty($_FILES['aadhaar_image']['name']))
			{
				$this->form_validation->set_rules('aadhaar_image', 'aadhaar_document', 'required');
			}
			if ($res->pancard_image == '' && empty($_FILES['pancard_image']['name']))
			{
				$this->form_validation->set_rules('pancard_image', 'pancard_document', 'required');
			}
		}
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			
			$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if($check_active == 3 || $check_active == 1 || $check_active == 0 ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender')){
				$profile_is_approved = $res->profile_is_approved;
				$profile_approved_by = $res->profile_approved_by;
				$profile_approved_on = $res->profile_approved_on;
			}else{
				$profile_is_approved = 0;
				$profile_approved_on = '0000-00-00 00:00:00';
				$profile_approved_by = 0;
			}
			
			if(!empty($this->input->post('dob'))){
				$dob = $this->input->post('dob');
			}else{
				$dob = '0000-00-00';
			}
			
		   $user = array(
				'email' => $this->input->post('email'),
				'ref_mobile' => $this->input->post('ref_mobile'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
				'is_edit' => 1,
				'complete_profile' => 1
			
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				$user_profile['photo'] = $res->photo;
			}
			
			if($_FILES['local_image']['size'] == 0){
				$local_verify = $res->local_verify;
				$local_approved_by = $res->local_approved_by;
				$local_approved_on = $res->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['permanent_image']['size'] == 0){
				$permanent_verify = $res->permanent_verify;
				$permanent_approved_by = $res->permanent_approved_by;
				$permanent_approved_on = $res->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_address' => $_FILES['local_image']['size'] == 0 ? $res->local_address : '',
				'local_continent_id' => $_FILES['local_image']['size'] == 0 ? $res->local_continent_id : 0,
				'local_country_id' => $_FILES['local_image']['size'] == 0 ? $res->local_country_id : 0,
				'local_zone_id' => $_FILES['local_image']['size'] == 0 ? $res->local_zone_id : 0,
				'local_state_id' => $_FILES['local_image']['size'] == 0 ? $res->local_state_id : 0,
				'local_city_id' => $_FILES['local_image']['size'] == 0 ? $res->local_city_id : 0,
				'local_area_id' => $_FILES['local_image']['size'] == 0 ? $res->local_area_id : 0,
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_address : '',
				'permanent_continent_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_continent_id : 0,
				'permanent_country_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_country_id : 0,
				'permanent_zone_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_zone_id : 0,
				'permanent_state_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_state_id : 0,
				'permanent_city_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_city_id : 0,
				'permanent_area_id' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_area_id : 0,
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				'is_edit' => 1,
				'complete_address' => 1
			);
			
			if ($_FILES['local_image']['size'] > 0) {
				
				$config['upload_path'] = $this->upload_path.'document/local_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('local_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$local_image = $this->upload->file_name;
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}else{
				$user_address['local_image'] = $res->local_image;
			}
			
			if ($_FILES['permanent_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permanent_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permanent_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permanent_image = $this->upload->file_name;
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}else{
				$user_address['permanent_image'] = $res->permanent_image;
			}
			
			if($this->input->post('account_no') == $res->account_no && $this->input->post('bank_name') == $res->bank_name && $this->input->post('branch_name') == $res->branch_name && $this->input->post('ifsc_code') == $res->ifsc_code){
				$account_verify = $res->account_verify;
				$account_approved_by = $res->account_approved_by;
				$account_approved_on = $res->account_approved_on;
			}else{
				$account_verify = 0;
				$account_approved_by = 0;
				$account_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $account_verify,
				'approved_by' => $account_approved_by,
				'approved_on' => $account_approved_on,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			if($_FILES['aadhaar_image']['size'] == 0){
				$aadhar_verify = $res->aadhar_verify;
				$aadhar_approved_by = $res->aadhar_approved_by;
				$aadhar_approved_on = $res->aadhar_approved_on;
			}else{
				$aadhar_verify = 0;
				$aadhar_approved_by = 0;
				$aadhar_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['pancard_image']['size'] == 0){
				$pancard_verify = $res->pancard_verify;
				$pancard_approved_by = $res->pancard_approved_by;
				$pancard_approved_on = $res->pancard_approved_on;
			}else{
				$pancard_verify = 0;
				$pancard_approved_by = 0;
				$pancard_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['license_image']['size'] == 0){
				$license_verify = $res->license_verify;
				$license_approved_by = $res->license_approved_by;
				$license_approved_on = $res->license_approved_on;
			}else{
				$license_verify = 0;
				$license_approved_by = 0;
				$license_approved_on = '0000:00:00 00:00:00';
			}
			
			if($_FILES['police_image']['size'] == 0){
				$police_verify = $res->police_verify;
				$police_approved_by = $res->police_approved_by;
				$police_approved_on = $res->police_approved_on;
			}else{
				$police_verify = 0;
				$police_approved_by = 0;
				$police_approved_on = '0000:00:00 00:00:00';
			}
		
			$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $res->aadhaar_no : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $res->pancard_no : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				'license_no' => $this->input->post('license_no') ? $this->input->post('license_no') : $res->license_no,
				'license_dob' => $_FILES['license_image']['size'] == 0 ? $res->license_dob : '0000-00-00',
				'license_ward_name' => $_FILES['license_image']['size'] == 0 ? $res->license_ward_name : '',
				'license_type' => $_FILES['license_image']['size'] == 0 ? $res->license_type : '',
				'license_country_id' => $_FILES['license_image']['size'] == 0 ? $res->license_country_id : '',
				'license_issuing_authority' => $_FILES['license_image']['size'] == 0 ? $res->license_issuing_authority : '',
				'license_issued_on' => $_FILES['license_image']['size'] == 0 ? $res->license_issued_on : '0000-00-00',
				'license_validity' => $_FILES['license_image']['size'] == 0 ? $res->license_validity : '0000-00-00',
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $_FILES['police_image']['size'] == 0 ? $res->police_on : '0000-00-00',
				'police_til' => $_FILES['police_image']['size'] == 0 ? $res->police_til : '0000-00-00',	
				
				'police_verify' => $police_verify,
				'police_approved_by' => $police_approved_by,
				'police_approved_on' => $police_approved_on,	
				'is_edit' => 1,
				'complete_document' => 1	
				
			);
			
				if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('aadhaar_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$aadhaar_image = $this->upload->file_name;
				$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
			}else{
				$user_document['aadhaar_image'] = $res->aadhaar_image;
			}
			
			if ($_FILES['pancard_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/pancard/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('pancard_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$pancard_image = $this->upload->file_name;
				$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}else{
				$user_document['pancard_image'] = $res->pancard_image;
			}
			
					
			
			
			if ($_FILES['license_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/license/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('license_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$license_image = $this->upload->file_name;
				$user_document['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
			}else{
				$user_document['license_image'] = $res->license_image;
			}
			
			if ($_FILES['police_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/police/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('police_image')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}else{
				$user_document['police_image'] = $res->police_image;
			}
				
				$data = $this->customer_api->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $customer_type, $countryCode);
				
				if($data){
					$result = array( 'status'=> 1, 'message' => 'Driver edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'driver does not edit.');
				}
				
			}else{
				$result = array( 'status'=> 0 , 'message' => 'your account has been deactive. so if can not edit.');
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
	
	public function modify_profile_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 'profile';
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			if(!empty($this->input->post('dob'))){
				$dob = $this->input->post('dob');
			}else{
				$dob = $res->dob;
			}
			
			
			$user = array(
				'email' => $this->input->post('email')   ? $this->input->post('email') : $res->email,
				'gender' => $this->input->post('gender') ? $this->input->post('gender') : $res->gender,
				'ref_mobile' => $this->input->post('ref_mobile') ? $this->input->post('ref_mobile') : $res->ref_mobile,
				'first_name' => $this->input->post('first_name')  ? $this->input->post('first_name') : $res->first_name,
				'last_name' => $this->input->post('last_name')  ? $this->input->post('last_name') : $res->last_name,
				'gender' => $this->input->post('gender')  ? $this->input->post('gender') : $res->gender,
				'dob' => $dob,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name')  ? $this->input->post('first_name') : $res->first_name,
				'last_name' => $this->input->post('last_name')  ? $this->input->post('last_name') : $res->last_name,
				'gender' => $this->input->post('gender')  ? $this->input->post('gender') : $res->gender,
				'dob' => $dob,
				'is_edit' => 1,
				'complete_profile' => 1
			
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/driver/'.$photo;
				$user['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				
			}
			
			$user_address = array(
			
				'local_address' => $this->input->post('local_address')  ? $this->input->post('local_address') : $res->local_address,
				'local_pincode' => $this->input->post('local_pincode')  ? $this->input->post('local_pincode') : $res->local_pincode,
				
				'complete_address' => 1,
				'permanent_address' => $this->input->post('permanent_address')  ? $this->input->post('permanent_address') : $res->permanent_address,
				'permanent_pincode' => $this->input->post('permanent_pincode')  ? $this->input->post('permanent_pincode') : $res->permanent_pincode,
				
			);
			
			if ($_FILES['local_image']['size'] > 0) {
				
				$config['upload_path'] = $this->upload_path.'document/local_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('local_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$local_image = $this->upload->file_name;
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}
			
			if ($_FILES['permanent_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permanent_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permanent_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permanent_image = $this->upload->file_name;
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name')  ? $this->input->post('account_holder_name') : $res->account_holder_name,
				'account_no' => $this->input->post('account_no')  ? $this->input->post('account_no') : $res->account_no,
				'bank_name' => $this->input->post('bank_name')  ? $this->input->post('bank_name') : $res->bank_name,
				'branch_name' => $this->input->post('branch_name')  ? $this->input->post('branch_name') : $res->branch_name,
				'ifsc_code' => $this->input->post('ifsc_code') ? $this->input->post('ifsc_code') : $res->ifsc_code,
				'complete_bank' => 1
			);
			
			
			
			$user_document = array(
				
				'aadhaar_no' => $this->input->post('aadhaar_no')  ? $this->input->post('aadhaar_no') : $res->aadhaar_no,
				'pancard_no' => $this->input->post('pancard_no')  ? $this->input->post('pancard_no') : $res->pancard_no,
				'license_no' => $this->input->post('license_no')  ? $this->input->post('license_no') : $res->license_no,		
				'police_on' =>  $this->input->post('police_on')  ? $this->input->post('police_on') : $res->police_on,
				'police_til' =>  $this->input->post('police_til')  ? $this->input->post('police_til') : $res->police_til,	
				
				'additional_contact_one_type' => $this->input->post('additional_contact_one_type'),
					'additional_contact_one_name' => $this->input->post('additional_contact_one_name'),
					'additional_contact_one_phone' => $this->input->post('additional_contact_one_phone'),
					'additional_contact_one_phonecode' => $this->input->post('additional_contact_one_phonecode'),
					'additional_contact_two_type' => $this->input->post('additional_contact_two_type'),
					'additional_contact_two_name' => $this->input->post('additional_contact_two_name'),
					'additional_contact_two_phone' => $this->input->post('additional_contact_two_phone'),
					'additional_contact_two_phonecode' => $this->input->post('additional_contact_two_phonecode'),	
					
				'complete_document' => 1
				
			);
			
			if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('aadhaar_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$aadhaar_image = $this->upload->file_name;
				$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
			}
			
			
			
			if ($_FILES['pancard_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/pancard/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('pancard_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$pancard_image = $this->upload->file_name;
				$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}
			
			if ($_FILES['license_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/license/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('license_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$license_image = $this->upload->file_name;
				$user_document['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
			}
			
			
			if ($_FILES['police_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/police/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('police_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}
			
			if ($_FILES['badge_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/badge/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('badge_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$badge_image = $this->upload->file_name;
				$user_document['badge_image'] = 'document/badge/'.$badge_image;
				$config = NULL;
			}
			
			if ($_FILES['training_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/training_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('training_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$training_certificate_image = $this->upload->file_name;
				$user_document['training_certificate_image'] = 'document/training_certificate/'.$training_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['experience_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/experience_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('experience_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$experience_certificate_image = $this->upload->file_name;
				$user_document['experience_certificate_image'] = 'document/experience_certificate/'.$experience_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['medical_certificate_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/medical_certificate/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('medical_certificate_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$medical_certificate_image = $this->upload->file_name;
				$user_document['medical_certificate_image'] = 'document/medical_certificate/'.$medical_certificate_image;
				$config = NULL;
			}
			
			if ($_FILES['health_insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/health_insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('health_insurance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$health_insurance_image = $this->upload->file_name;
				$user_document['health_insurance_image'] = 'document/health_insurance/'.$health_insurance_image;
				$config = NULL;
			}
			
			if ($_FILES['term_insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/term_insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('term_insurance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$term_insurance_image = $this->upload->file_name;
				$user_document['term_insurance_image'] = 'document/term_insurance/'.$term_insurance_image;
				$config = NULL;
			}
			
			$make_name = $this->drivers_api->getTaxinameBYID($this->input->post('make'), $countryCode);
				$model_name = $this->drivers_api->getTaximodelBYID($this->input->post('model'), $countryCode);
				$type_name = $this->drivers_api->getTaxitypeBYID($this->input->post('type'), $countryCode);
				
			$taxi = array(
				'name' => $this->input->post('name'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				'board' => $this->input->post('board'),
				//'ac' => $this->input->post('ac'),
				//'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1,
				'complete_taxi' => 1
				
		   );
			   
			   
			   
			   if ($_FILES['taxi_photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxi/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxi_photo = $this->upload->file_name;
					$taxi['photo'] = 'document/taxi/'.$taxi_photo;
					$config = NULL;
				}
				
				$taxi_document = array(
					'is_edit' => 1,
					'emission_norms' => $this->input->post('emission_norms'),
					'complete_taxidocument' => 1
					
				);
				
				if ($_FILES['reg_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/register/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('reg_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$reg_image = $this->upload->file_name;
					$taxi_document['reg_image'] = 'document/register/'.$reg_image;
					$config = NULL;
				}
				
				if ($_FILES['taxation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxation_image = $this->upload->file_name;
					$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
					$config = NULL;
				}
				
				if ($_FILES['insurance_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/insurance/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('insurance_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$insurance_image = $this->upload->file_name;
					$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
					$config = NULL;
				}
				
				if ($_FILES['permit_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/permit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('permit_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$permit_image = $this->upload->file_name;
					$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
					$config = NULL;
				}
				
				if ($_FILES['authorisation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/authorisation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('authorisation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$authorisation_image = $this->upload->file_name;
					$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
					$config = NULL;
				}
				
				if ($_FILES['fitness_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/fitness/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('fitness_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$fitness_image = $this->upload->file_name;
					$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
					$config = NULL;
				}
				
				if ($_FILES['speed_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/speed_limit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('speed_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$speed_image = $this->upload->file_name;
					$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
					$config = NULL;
				}
				
				if ($_FILES['puc_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/puc/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('puc_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$puc_image = $this->upload->file_name;
					$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
					$config = NULL;
				}
				
				if ($_FILES['vehicle_tracking_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/vehicle_tracking/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('vehicle_tracking_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$vehicle_tracking_image = $this->upload->file_name;
				$taxi_document['vehicle_tracking_image'] = 'document/vehicle_tracking/'.$vehicle_tracking_image;
				$config = NULL;
			}
			
			if ($_FILES['fire_extinguisher_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fire_extinguisher/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('fire_extinguisher_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$fire_extinguisher_image = $this->upload->file_name;
				$taxi_document['fire_extinguisher_image'] = 'document/fire_extinguisher/'.$fire_extinguisher_image;
				$config = NULL;
			}
			
			
			if ($_FILES['child_lock_mechanism_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/child_lock_mechanism/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('child_lock_mechanism_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$child_lock_mechanism_image = $this->upload->file_name;
				$taxi_document['child_lock_mechanism_image'] = 'document/child_lock_mechanism/'.$child_lock_mechanism_image;
				$config = NULL;
			}
			
			
			if ($_FILES['interior_vehicle_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/interior_vehicle/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('interior_vehicle_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$interior_vehicle_image = $this->upload->file_name;
				$taxi_document['interior_vehicle_image'] = 'document/interior_vehicle/'.$interior_vehicle_image;
				$config = NULL;
			}
			
			
			if ($_FILES['taxi_roof_sign_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_roof_sign/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxi_roof_sign_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_roof_sign_image = $this->upload->file_name;
				$taxi_document['taxi_roof_sign_image'] = 'document/taxi_roof_sign/'.$taxi_roof_sign_image;
				$config = NULL;
			}
			
			if ($_FILES['e_challans_clearance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/e_challans_clearance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('e_challans_clearance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$e_challans_clearance_image = $this->upload->file_name;
				$taxi_document['e_challans_clearance_image'] = 'document/e_challans_clearance/'.$e_challans_clearance_image;
				$config = NULL;
			}
			
				$res = $this->drivers_api->modify_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver, $customer_type, $countryCode);
				
				if($res){
					$result = array( 'status'=> true , 'message'=> 'My Profile has been updated!', 'taxi_status' => $res['taxi_status'], 'document_status' => $res['document_status'], 'bank_status' => $res['bank_status']);
				}else{
					$result = array( 'status'=> false , 'message'=> 'My Profile has been not updated!');
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
	
	public function modify_bank_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 'bank';
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			$user = array();
		   
		   $user_profile = array();
		   
		  
			
			$user_address = array();
			
						
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'complete_bank' => 1
			);
			
			
			
			$user_document = array();
			
			
			$taxi = array();
			   
			   
				
				$taxi_document = array();
				
				
				$res = $this->drivers_api->modify_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver, $customer_type, $countryCode);
				if($res){
					$result = array( 'status'=> true , 'message'=> 'My Profile has been updated!', 'taxi_status' => $res['taxi_status'], 'document_status' => $res['document_status'], 'bank_status' => $res['bank_status']);
				}else{
					$result = array( 'status'=> false , 'message'=> 'My Profile has been not updated!');
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
	
	public function modify_taxi_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->drivers_api->getUserEdit($user_data->id, $countryCode);
		
		$customer_type = 'taxi';
		
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			if(!empty($this->input->post('dob'))){
				$dob = $this->input->post('dob');
			}else{
				$dob = '0000-00-00';
			}
			
			$user = array();
		   
		   $user_profile = array();
		   
		  
			
			$user_address = array();
			
			
			
			$user_bank = array();
			
			
			
			$user_document = array();
			
			$make_name = $this->drivers_api->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->drivers_api->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->drivers_api->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			$taxi = array(
				'name' => $this->input->post('name'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				'board' => $this->input->post('board'),
				//'ac' => $this->input->post('ac'),
				//'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1,
				'complete_taxi' => 1
		   );
			   
			   
			   
			   if ($_FILES['photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxi/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxi_photo = $this->upload->file_name;
					$taxi['photo'] = 'document/taxi/'.$taxi_photo;
					$config = NULL;
				}
				
				$taxi_document = array(
					'is_edit' => 1,
					'emission_norms' => $this->input->post('emission_norms'),
					'complete_taxidocument' => 1
					
				);
				
				if ($_FILES['reg_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/register/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('reg_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$reg_image = $this->upload->file_name;
					$taxi_document['reg_image'] = 'document/register/'.$reg_image;
					$config = NULL;
				}
				
				if ($_FILES['taxation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/taxation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$taxation_image = $this->upload->file_name;
					$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
					$config = NULL;
				}
				
				if ($_FILES['insurance_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/insurance/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('insurance_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$insurance_image = $this->upload->file_name;
					$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
					$config = NULL;
				}
				
				if ($_FILES['permit_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/permit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('permit_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$permit_image = $this->upload->file_name;
					$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
					$config = NULL;
				}
				
				if ($_FILES['authorisation_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/authorisation/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('authorisation_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$authorisation_image = $this->upload->file_name;
					$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
					$config = NULL;
				}
				
				if ($_FILES['fitness_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/fitness/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('fitness_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$fitness_image = $this->upload->file_name;
					$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
					$config = NULL;
				}
				
				if ($_FILES['speed_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/speed_limit/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('speed_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$speed_image = $this->upload->file_name;
					$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
					$config = NULL;
				}
				
				if ($_FILES['puc_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/puc/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('puc_image')) {
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
					}
					$puc_image = $this->upload->file_name;
					$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
					$config = NULL;
				}
				
				if ($_FILES['vehicle_tracking_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/vehicle_tracking/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('vehicle_tracking_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$vehicle_tracking_image = $this->upload->file_name;
				$taxi_document['vehicle_tracking_image'] = 'document/vehicle_tracking/'.$vehicle_tracking_image;
				$config = NULL;
			}
			
			if ($_FILES['fire_extinguisher_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fire_extinguisher/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('fire_extinguisher_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$fire_extinguisher_image = $this->upload->file_name;
				$taxi_document['fire_extinguisher_image'] = 'document/fire_extinguisher/'.$fire_extinguisher_image;
				$config = NULL;
			}
			
			
			if ($_FILES['child_lock_mechanism_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/child_lock_mechanism/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('child_lock_mechanism_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$child_lock_mechanism_image = $this->upload->file_name;
				$taxi_document['child_lock_mechanism_image'] = 'document/child_lock_mechanism/'.$child_lock_mechanism_image;
				$config = NULL;
			}
			
			
			if ($_FILES['interior_vehicle_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/interior_vehicle/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('interior_vehicle_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$interior_vehicle_image = $this->upload->file_name;
				$taxi_document['interior_vehicle_image'] = 'document/interior_vehicle/'.$interior_vehicle_image;
				$config = NULL;
			}
			
			
			if ($_FILES['taxi_roof_sign_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_roof_sign/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxi_roof_sign_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_roof_sign_image = $this->upload->file_name;
				$taxi_document['taxi_roof_sign_image'] = 'document/taxi_roof_sign/'.$taxi_roof_sign_image;
				$config = NULL;
			}
			
			if ($_FILES['e_challans_clearance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/e_challans_clearance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('e_challans_clearance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$e_challans_clearance_image = $this->upload->file_name;
				$taxi_document['e_challans_clearance_image'] = 'document/e_challans_clearance/'.$e_challans_clearance_image;
				$config = NULL;
			}
			
				$res = $this->drivers_api->modify_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver, $customer_type, $countryCode);
				if($res){
					$result = array( 'status'=> true , 'message'=> 'My Profile has been updated!', 'taxi_status' => $res['taxi_status'], 'document_status' => $res['document_status'], 'bank_status' => $res['bank_status']);
				}else{
					$result = array( 'status'=> false , 'message'=> 'My Profile has been not updated!');
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
	
	public function taxidetail_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$taxi_id = $this->drivers_api->getDriverTaxiID($user_data->id, $countryCode);
			
			$data = $this->drivers_api->getTaxiDetails($taxi_id, $countryCode);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty taxi');
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$data = $this->site->Getnotification($user_data->id, '2', $countryCode);
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
	
	
	public function sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "user-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_transaction_active($total_amount, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[AMOUNT]');
        $sms_rep_arr = array($total_amount);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-complete", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_allocated_driver($name, $taxi_number,$sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[DRIVERNAME]','[TAXINUMBER]');
        $sms_rep_arr = array($name,$taxi_number);
        $response_sms = send_transaction_sms($sms_template_slug = "allocated-driver", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_allocated_closedriver($name, $taxi_number,$sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[DRIVERNAME]','[TAXINUMBER]');
        $sms_rep_arr = array($name,$taxi_number);
        $response_sms = send_transaction_sms($sms_template_slug = "allocated-driver-close", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	
	
	


	public function my_currentrides_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->mycurrentrides($user_data->id, $countryCode);
			
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
	
	public function my_pastrides_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		
		$sdate = $this->input->post('sdate');
		$edate = $this->input->post('edate');
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->mypastrides($user_data->id, $countryCode, $sdate, $edate);
			
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
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->myupcomingrides($user_data->id, $countryCode, $sdate, $edate);
			
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
	
	public function my_onride_post(){
		$data = array();
		$incentive = '0';
		$credit_balance = '0';
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		
		//print_r($_POST);die;
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->drivers_api->myonrides($user_data->id, $countryCode);
			
			
			$credit_balance = $this->drivers_api->GETcredit_balance($user_data->id, $countryCode);
			$incentive = $this->drivers_api->GETincentive_balance($user_data->id, $countryCode);
			
			if(!empty($data)){
				$payment_id = $data[0]->payment_id;
				$payment_name = $this->drivers_api->getPaymentName($payment_id);
				$waypoint_data = $this->site->getWaypoint($data[0]->ride_id);	
				if($data[0]->status == 2){
					
				
					$row = array(
						'ride_id' => $data[0]->ride_id,						
						
						'cust_location_lat' => $data[0]->start_lat,
						'cust_location_lng' => $data[0]->start_lng,
						'cust_drop_lat' => $data[0]->end_lat,
						'cust_drop_lng' => $data[0]->end_lng,
						'distance_km' => $data[0]->distance_km,
						'distance_price' => $data[0]->distance_price,
						'payment_id' => $payment_id,
						'payment_name' => $payment_name,
						'pick_up' => $data[0]->start ? $data[0]->start : '0',
						'drop_off' => $data[0]->end ? $data[0]->end : '0',
						
						'customer_name' => $data[0]->customer_name,
						'customer_mobile' => $data[0]->customer_mobile,
						'customer_country_code' => $data[0]->cus_code,
						'customer_image' => $data[0]->customer_photo,
						'mapcar' => $data[0]->mapcar,
						'waypoint_data' => $waypoint_data,
						
					);
					$result = array( 'status'=> 1 , 'message'=> 'Ride Booked', 'incentive' => $incentive, 'credit_balance' => $credit_balance, 'data' => $row);
				}elseif($data[0]->status == 3){
					$row = array(
						'booking_id' => $data[0]->ride_id,
						'pickup_lat' => $data[0]->start_lat,
						'pickup_lng' => $data[0]->start_lng,
						'dropoff_lat' => $data[0]->end_lat,
						'dropoff_lng' => $data[0]->end_lng,
						'distance_km' => $data[0]->distance_km,
						'distance_price' => $data[0]->distance_price,
						'payment_id' => $payment_id,
						'payment_name' => $payment_name,
						'pick_up' => $data[0]->start ? $data[0]->start : '0',
						'drop_off' => $data[0]->end ? $data[0]->end : '0',
						'mapcar' => $data[0]->mapcar,
						'waypoint_data' => $waypoint_data,
					);
					$result = array( 'status'=> 2 , 'message'=> 'Onride', 'incentive' => $incentive, 'credit_balance' => $credit_balance, 'data' => $row);
				}elseif($data[0]->status == 9){
					$row = array(json_decode($data[0]->json_data));
					$result = array( 'status'=> 3 , 'message'=> 'Ride Incomplete', 'incentive' => $incentive, 'credit_balance' => $credit_balance, 'data' => $row);
					
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Complete or Cancel', 'incentive' => $incentive, 'credit_balance' => $credit_balance, 'data' => $data);
				}
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'No Rides', 'incentive' => $incentive, 'credit_balance' => $credit_balance,);
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
	
	public function startwithoutotp_ride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		//$this->form_validation->set_rules('ride_otp', $this->lang->line("ride_otp"), 'required');
		//$this->form_validation->set_rules('pickup_lat', $this->lang->line("latitude"), 'required');
		//$this->form_validation->set_rules('pickup_lng', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		if ($this->form_validation->run() == true) {
			$onridecheck = $this->site->RidecancelCheck($this->input->post('booking_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> false , 'message' => 'Ride Already Cancel.');
			}else{
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			
			$row['booking_id'] = $this->input->post('booking_id');
			
			//$row['pickup_lat'] = $this->input->post('pickup_lat');
			//$row['pickup_lng'] = $this->input->post('pickup_lng');
			$row['dropoff_lat'] = $this->input->post('dropoff_lat') ? $this->input->post('dropoff_lat') : 0;
			$row['dropoff_lng'] = $this->input->post('dropoff_lng') ? $this->input->post('dropoff_lng') : 0;
			$row['vendor_id'] = $user_data->parent_id;
			
			$route_array = array(
				'location' => $this->site->findLocation($user_data->current_latitude, $user_data->current_longitude, $countryCode),
				'latitude' => $user_data->current_latitude,
				'longitude' => $user_data->current_longitude,
				'timing' => date('Y-m-d H:i:s'),
				'trip_made' => 3
			);
			
			$data = $this->drivers_api->startridewithoutotp($row, $route_array, $countryCode);
			
			$mapcar = $this->site->mapcar($data->cab_type_id);
			
			//print_r($data);
			//die;
			//$customer_socket_id = $this->site->getSocketID($data->customer_id, 1, $countryCode);
			//$driver_socket_id = $this->site->getSocketID($data->driver_id, 2, $countryCode);
			
			
			$payment_id = $data->payment_id;
			$payment_name = $this->drivers_api->getPaymentName($payment_id, $countryCode);
			$distance_km = $data->distance_km;
			$distance_price = $data->distance_price;
			$pick_up = $data->start ? $data->start : '0';
			$drop_off = $data->end ? $data->end : '0';
					
					
			$cus_data = $this->site->get_customer($data->customer_id, $countryCode);
			$dri_data = $this->site->get_driver($data->driver_id, $countryCode);
			
			$waypoint_data = $this->site->getWaypoint($data->id);
			
			$event = 'server_ride_otp_verify';
			$socket_id = $this->site->getSocketID($data->customer_id, 1, $countryCode);
			$loc = $this->site->GetDrivingDistance($data->start_lat, $data->start_lng,  $data->end_lat, $data->end_lng, $countryCode);
			$edata = array(
				'sos' => "https://35.154.46.42/sos?id=".$data->id,
				'mapcar' => $mapcar,
				'customer_id' => $data->customer_id,
				'booking_id' => $data->id,
				'from_location' => $data->start ? $data->start : '',
				'to_location' => $data->end ? $data->end : '',	
				'start_lat' => $data->start_lat,
				'start_lng' => $data->start_lng,
				'end_lat' => $data->end_lat,
				'end_lng' => $data->end_lng,
				'total_km' => $loc['distance'] ? $loc['distance'] : 0,		
				'socket_id' => $socket_id,
				'msg' => 'Ride OTP is verified.',
				'waypoint_data' => $waypoint_data
				
			);
			$emit_otp = $this->socketemitter->setEmit($event, $edata);
			$last_emit_array = array(
				'user_type_id' => 1,
				'user_id' => $data->customer_id,
				'emit_name' => 'ride_otp_verify',
				'emit_data' => json_encode($edata),
				'created_on' => date('Y-m-d H:i:s')
			);
			$this->site->insertLastEmit($last_emit_array);
			
			$notification['title'] = 'Driver Accepted Ride';
			$notification['message'] = $user_data->first_name.' has been accpted this ride';
			$notification['user_type'] = 1;
			$notification['user_id'] = $data->customer_id;
			$this->drivers_api->insertNotification($notification, $countryCode);
			
			/*$chat_data = array(
				'customer_socket_id' => $customer_socket_id,
				'driver_socket_id' => $driver_socket_id,
				'customer_name' => $cus_data->first_name.' '.$cus_data->last_name,
				'driver_name' => $dri_data->first_name.' '.$dri_data->last_name,
			);
			$chat_event = 'server_chat_complete';
			$chat = $this->socketemitter->setEmit($chat_event, $chat_data);*/
			
			
			
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Your riding has been start', 'mapcar' => $mapcar, 'booking_id' => $data->id, 'pickup_lat' => $data->start_lat, 'pickup_lng' => $data->start_lng, 'dropoff_lat' => $data->end_lat,  'dropoff_lng' => $data->end_lng, 'payment_id' => $payment_id, 'payment_name' => $payment_name, 'distance_km' => $distance_km, 'distance_price' => $distance_price, 'pick_up' => $pick_up, 'drop_off' => $drop_off, 'waypoint_data' => $waypoint_data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'not complete ride');
			}
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
	
	public function start_ride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_otp', $this->lang->line("ride_otp"), 'required');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		//$this->form_validation->set_rules('pickup_lat', $this->lang->line("latitude"), 'required');
		//$this->form_validation->set_rules('pickup_lng', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			$onridecheck = $this->site->RidecancelCheckStartorOTP($user_data->id, $this->input->post('ride_otp'), $this->input->post('booking_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> false , 'message' => 'Ride Already Cancel.');
			}else{
			$row['ride_otp'] = $this->input->post('ride_otp');
			$row['booking_id'] = $this->input->post('booking_id');
			
			//$row['pickup_lat'] = $this->input->post('pickup_lat');
			//$row['pickup_lng'] = $this->input->post('pickup_lng');
			$row['dropoff_lat'] = $this->input->post('dropoff_lat') ? $this->input->post('dropoff_lat') : 0;
			$row['dropoff_lng'] = $this->input->post('dropoff_lng') ? $this->input->post('dropoff_lng') : 0;
			$row['vendor_id'] = $user_data->parent_id;
			
			$route_array = array(
				'location' => $this->site->findLocation($user_data->current_latitude, $user_data->current_longitude, $countryCode),
				'latitude' => $user_data->current_latitude,
				'longitude' => $user_data->current_longitude,
				'timing' => date('Y-m-d H:i:s'),
				'trip_made' => 3
			);
			
			$data = $this->drivers_api->startride($row, $route_array, $countryCode);
			
			$mapcar = $this->site->mapcar($data->cab_type_id);
			//$customer_socket_id = $this->site->getSocketID($data->customer_id, 1, $countryCode);
			//$driver_socket_id = $this->site->getSocketID($data->driver_id, 2, $countryCode);
			
			
			$payment_id = $data->payment_id;
			$payment_name = $this->drivers_api->getPaymentName($payment_id, $countryCode);
			$distance_km = $data->distance_km;
			$distance_price = $data->distance_price;
			$pick_up = $data->start ? $data->start : '0';
			$drop_off = $data->end ? $data->end : '0';
					
					
			$cus_data = $this->site->get_customer($data->customer_id, $countryCode);
			$dri_data = $this->site->get_driver($data->driver_id, $countryCode);
			
			$event = 'server_ride_otp_verify';
			$socket_id = $this->site->getSocketID($data->customer_id, 1, $countryCode);
			$loc = $this->site->GetDrivingDistance($data->start_lat, $data->start_lng,  $data->end_lat, $data->end_lng);
			
			$waypoint_data = $this->site->getWaypoint($data->id);
			
			$edata = array(
				'sos' => "https://35.154.46.42/sos?id=".$data->id,
				'customer_id' => $data->customer_id,
				'booking_id' => $data->id,
				'mapcar' => $mapcar,
				'from_location' => $data->start ? $data->start : '',
				'to_location' => $data->end ? $data->end : '',
				'start_lat' => $data->start_lat,
				'start_lng' => $data->start_lng,
				'end_lat' => $data->end_lat,
				'end_lng' => $data->end_lng,
				'total_km' => $loc['distance'] ? $loc['distance'] : 0,		
				'socket_id' => $socket_id,
				'msg' => 'Ride OTP is verified.',
				'waypoint_data' => $waypoint_data
				
			);
			$emit_otp = $this->socketemitter->setEmit($event, $edata);
			$last_emit_array = array(
				'user_type_id' => 1,
				'user_id' => $data->customer_id,
				'emit_name' => 'ride_otp_verify',
				'emit_data' => json_encode($edata),
				'created_on' => date('Y-m-d H:i:s')
			);
			$this->site->insertLastEmit($last_emit_array);
			
			$notification['title'] = 'Driver Accepted Ride';
			$notification['message'] = $user_data->first_name.' has been accpted this ride';
			$notification['user_type'] = 1;
			$notification['user_id'] = $data->customer_id;
			$this->drivers_api->insertNotification($notification);
			
			/*$chat_data = array(
				'customer_socket_id' => $customer_socket_id,
				'driver_socket_id' => $driver_socket_id,
				'customer_name' => $cus_data->first_name.' '.$cus_data->last_name,
				'driver_name' => $dri_data->first_name.' '.$dri_data->last_name,
			);
			$chat_event = 'server_chat_complete';
			$chat = $this->socketemitter->setEmit($chat_event, $chat_data);*/
			
			
				if($data == TRUE){
					$result = array( 'status'=> true , 'message'=> 'Your riding has been start', 'mapcar' => $mapcar, 'booking_id' => $data->id, 'pickup_lat' => $data->start_lat, 'pickup_lng' => $data->start_lng, 'dropoff_lat' => $data->end_lat,  'dropoff_lng' => $data->end_lng, 'payment_id' => $payment_id, 'payment_name' => $payment_name, 'distance_km' => $distance_km, 'distance_price' => $distance_price, 'pick_up' => $pick_up, 'drop_off' => $drop_off, 'waypoint_data' => $waypoint_data);
				}else{
					$result = array( 'status'=> false , 'message'=> 'ride otp is invaild');
				}
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
	
	public function complete_ride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		//$this->form_validation->set_rules('dropoff_lat', $this->lang->line("latitude"), 'required');
		//$this->form_validation->set_rules('dropoff_lng', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('customer_drop_lat', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('customer_drop_lng', $this->lang->line("longitude"), 'required');
		//$this->form_validation->set_rules('total_toll', $this->lang->line("Toll"), 'required');
		//$this->form_validation->set_rules('total_parking', $this->lang->line("parking"), 'required');
		
		$settings = $this->drivers_api->getSettings($countryCode);
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$onridecheck = $this->site->RidecancelCheck($this->input->post('booking_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> false , 'message' => 'Ride Already Cancel.');
			}else{
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$discount_array = $this->site->checkDiscount($countryCode);
			
			$row['driver_id'] = $user_data->id;
			$row['booking_id'] = $this->input->post('booking_id');
			
			$ride = $this->drivers_api->getRideID($this->input->post('booking_id'), $countryCode);
			
			$row['package_id'] = $ride->package_id;
			$row['booked_type'] = $ride->booked_type;
			$row['outstation_type'] = $ride->outstation_type;
			//$row['pickup_lat'] = $this->input->post('pickup_lat');
			//$row['pickup_lng'] = $this->input->post('pickup_lng');
			//$row['dropoff_lat'] = $this->input->post('dropoff_lat');
			//$row['dropoff_lng'] = $this->input->post('dropoff_lng');
			
			$actual_loc = $this->site->findLocation($this->input->post('customer_drop_lat'), $this->input->post('customer_drop_lng'), $countryCode);
			$row['actual_loc'] = $actual_loc;
			$row['actual_lat'] = $this->input->post('customer_drop_lat');
			$row['actual_lng'] = $this->input->post('customer_drop_lng');
			$row['total_toll'] = $this->input->post('total_toll') != NULL ? $this->input->post('total_toll') : 0;
			$row['total_parking'] = $this->input->post('total_parking') != NULL ? $this->input->post('total_parking') : 0;
			$row['travel_distance'] = $this->input->post('travel_distance') != NULL ? $this->input->post('travel_distance') : 0;
			
			$row['driver_admin_payment_option'] = $settings->driver_admin_payment_option;
			$row['driver_admin_payment_percentage'] = $settings->driver_admin_payment_percentage;
			$row['driver_admin_payment_duration'] = $settings->driver_admin_payment_duration;
			
			$row['discount_id'] = $discount_array->discount_id;
			$row['discount_name'] = $discount_array->discount_name;
			$row['discount_percentage'] = $discount_array->discount_percentage;
			
			$current_date = date('d/m/Y');
			if($settings->driver_admin_payment_option == 1){
				$ride_start_date = date('d/m/Y', strtotime($current_date. '+1 days'));
			}elseif($settings->driver_admin_payment_option == 2){
				$ride_start_date = date('d/m/Y', strtotime($current_date. '+7 days'));
			}elseif($settings->driver_admin_payment_option == 3){
				$ride_start_date = date('d/m/Y', strtotime($current_date. '+30 days'));
			}
			$data = $this->drivers_api->completeride($row, $countryCode);
			if($data == TRUE){
				
				
				$actual_fare = $data['actual_fare'];
				$total_parking = $data['total_parking'];
				$total_toll = $data['total_toll'];
				$extra_fare = $data['extra_fare'];
				$extra_fare_details = $data['extra_fare_details'];
				$total_amount = $data['total_fare'];
				$discounts = $data['discount_fare'];
				$total_tax = $data['total_tax'];
				$customer_id = $data['customer_id'];
				$round = $data['total_fare'];
				
				$details[] = $data;
				
				
				 $result = array( 'status'=> true , 'message'=> 'ride data.', 'data' => $details);
				
			}else{
				$result = array( 'status'=> false , 'message'=> 'not complete ride');
			}
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
	
	public function submitride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		
		$settings = $this->drivers_api->getSettings($countryCode);
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
					
				
			$row['driver_id'] = $user_data->id;
			$row['booking_id'] = $this->input->post('booking_id');
			$row['total_toll'] = $this->input->post('total_toll') != NULL ? $this->input->post('total_toll') : 0;
			$row['total_parking'] = $this->input->post('total_parking') != NULL ? $this->input->post('total_parking') : 0;
			
			$res = $this->drivers_api->submitride($row, $countryCode);
			
			if(!empty($res)){
				
				$customer_id = $res->customer_id;
				$actual_fare = $res->actual_fare;
				//$total_parking = $res->total_parking;
				//$total_toll = $res->total_toll;
				$extra_fare = $res->extra_fare;
				$extra_fare_details = $res->extra_fare_details;
				$total_amount = $res->final_total;
				$discounts = $res->discount_fare;
				$customer_id = $res->customer_id;
				$round = $res->actual_fare;
				$waiting_fare = $res->waiting_fare;
				$pickup_waiting_fare = $res->pickup_waiting_fare;
				$waypoint_waiting_fare = $res->waypoint_waiting_fare;
				$trafic_waiting_fare = $res->trafic_waiting_fare;
				
				$pickup_waiting_min = $res->pickup_waiting_min;
				$waypoint_waiting_min = $res->waypoint_waiting_min;
				$trafic_waiting_min = $res->trafic_waiting_min;
				
				
				$total_toll = $this->input->post('total_toll') != NULL ? $this->input->post('total_toll') : 0;
				$total_parking = $this->input->post('total_parking') != NULL ? $this->input->post('total_parking') : 0;
				$outstanding_from_last_trip = $res->outstanding_from_last_trip;
				$total_tax_fare = $res->total_tax_fare;
				
				if(!empty($user_data->id)){
				
					$customer_data = $this->drivers_api->getCustomerID($customer_id, $countryCode);
					$driver_data = $this->drivers_api->getDriverID($user_data->id, $countryCode);
					
					
					$customer_name = $customer_data->first_name;
					$driver_name = $driver_data->first_name;
					$driver_phone = $driver_data->country_code.$driver_data->mobile;
					
					$sms_phone = $customer_data->country_code . $customer_data->mobile;
					$sms_country_code = $customer_data->country_code;
					
					$notification['title'] = 'Ride Complete';
					$notification['message'] = 'Ride Complete. Your payment amount : '.$total_amount.'';
					$notification['user_type'] = 1;
					$notification['user_id'] = $customer_id;
					$this->drivers_api->insertNotification($notification, $countryCode);
					
					$round = $actual_fare;
					$event = 'server_ride_complete';
					$socket_id = $this->site->getSocketID($customer_id, 1, $countryCode);
					$edata = array(
						'booking_id' => $this->input->post('booking_id'),
						'customer_id' => $customer_id,				
						'socket_id' => $socket_id,
						'trip_fare' => (string)$round,
						'tolls' => (string)$total_toll,
						'parking' => (string)$total_parking,
						'waiting_fare' => (string)$waiting_fare,
										
						'pickup_waiting_fare' => (string)$pickup_waiting_fare,
						'waypoint_waiting_fare' => (string)$waypoint_waiting_fare,
						'trafic_waiting_fare' => (string)$trafic_waiting_fare,
						'pickup_waiting_min' => (string)$pickup_waiting_min,
						'waypoint_waiting_min' => (string)$waypoint_waiting_min,
						'trafic_waiting_min' => (string)$trafic_waiting_min,
				
						'extra_fare' => (string)$extra_fare,
						'extra_fare_details' => $extra_fare_details ? $extra_fare_details : '0',
						'discounts' =>  (string)$discounts,
						
						'total_tax_fare' => (string)$total_tax_fare,
						'outstanding_from_last_trip' =>  (string)$outstanding_from_last_trip,
						'total' => (string)$total_amount,
						'msg' => 'Your Ride has been Completed Successfully. Your Ride Amount is: '.$round.', Toll Amount:'.$total_toll.', Parking Amount: '.$total_parking.', Your Outstanding from last trip Amount is: '.$outstanding_from_last_trip.',  Total Payable Amount:'.$total_amount.''
						
					);
					$this->socketemitter->setEmit($event, $edata);
					$last_emit_array = array(
						'user_type_id' => 1,
						'user_id' => $customer_id,
						'emit_name' => 'driver_ride_complete',
						'emit_data' => json_encode($edata),
						'created_on' => date('Y-m-d H:i:s')
					);
					$this->site->insertLastEmit($last_emit_array);

				}
				$details[] = $res;
				
				$sms_message = 'Your Ride has been Completed Successfully. Your Ride Amount is: '.$round.', Toll Amount:'.$total_toll.', Parking Amount: '.$total_parking.', Waiting Amount: '.$waiting_fare.' Total Payable Amount:'.$total_amount;
				
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$next_ride = $this->site->nextRide($user_data->id);
				
				if(!empty($next_ride)){
					
					if($response_sms){
					 	$result = array( 'status'=> 2 , 'message'=> 'Your ride comepleted and forwarding to next ride.', 'data' => $next_ride);
					} else {
						$result = array( 'status'=> 2 , 'message'=> 'Your ride comepleted and forwarding to next ride.', 'data' => $next_ride);
					}
					
				}else{
					
					if($response_sms){
					 $result = array( 'status'=> 1 , 'message'=> 'Your riding has been completed.', 'data' => $details);
					} else {
						$result = array( 'status'=> 1 , 'message'=> 'Your riding has been completed.', 'data' => $details);
					}
				}
				
				//$result = array( 'status'=> true , 'message'=> 'Your riding has been completed', 'data' => $data);
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not complete ride');
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
	
	public function driver_reached_destination_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		$this->form_validation->set_rules('latitude', $this->lang->line("latitude"), 'required');
		$this->form_validation->set_rules('longitude', $this->lang->line("longitude"), 'required');
		//$this->form_validation->set_rules('pickup_lat', $this->lang->line("latitude"), 'required');
		//$this->form_validation->set_rules('pickup_lng', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$onridecheck = $this->site->RidecancelCheck($this->input->post('ride_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> 0 , 'message' => 'Ride Already Cancel.');
			}else{
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['driver_id'] = $user_data->id;
			$row['ride_id'] = $this->input->post('ride_id');
			$data = $this->drivers_api->reachedlocation($row, $countryCode);
			
			$route_array = array(
				'location' => $this->site->findLocation($this->input->post('latitude'), $this->input->post('longitude')),
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
				'ride_id' => $this->input->post('ride_id'),
				'timing' => date('Y-m-d H:i:s'),
				'trip_made' => 2
			);
			$this->drivers_api->insertRoute($route_array, $countryCode);
			$event = 'server_reached_destination';
			$socket_id = $this->site->getSocketID($data[0]->customer_id, 1, $countryCode);
			$edata = array(
				'ride_id' => $this->input->post('ride_id'),
				'customer_id' => $data[0]->customer_id,		
				'ride_otp' => $data[0]->ride_otp,
				
				'dropoff_lat' => $data[0]->end_lat ? $data[0]->end_lat : '0',
				'dropoff_lng' => $data[0]->end_lng ? $data[0]->end_lng : '0',		
				'socket_id' => $socket_id,
				'msg' => 'Driver reached your location. please check it.'
				
			);
			
			$notification['title'] = 'Driver Reached Location';
			$notification['message'] = $user_data->first_name.' has been reached ride';
			$notification['user_type'] = 1;
			$notification['user_id'] = $data[0]->customer_id;
			$this->drivers_api->insertNotification($notification);
			
			$settings = $this->drivers_api->getSettings($countryCode);
			
			$this->socketemitter->setEmit($event, $edata);
			$last_emit_array = array(
				'user_type_id' => 1,
				'user_id' => $data[0]->customer_id,
				'emit_name' => 'driver_reached_destination',
				'emit_data' => json_encode($edata),
				'created_on' => date('Y-m-d H:i:s')
			);
			$this->site->insertLastEmit($last_emit_array);
			
			
			$customer_data = $this->site->get_customer($data[0]->customer_id, $countryCode);
			$sms_phone = $customer_data->mobile;
			$sms_country_code = $customer_data->country_code;
			$sms_message = 'Driver has been reached. ';
				
			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
			if($data == TRUE){
				if($settings->ride_otp_enable == 1){
					$result = array( 'status'=> 1 , 'message'=> 'Your riding has been start', 'ride_otp' => $data[0]->ride_otp, 'waiting_time' => $settings->waiting_time);
				}else{
					$result = array( 'status'=> 2, 'message'=> 'Your riding has been start', 'waiting_time' => $settings->waiting_time);
				}
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not complete ride');
			}
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
	
	public function collect_payment_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		$this->form_validation->set_rules('payment_mode', $this->lang->line("payment_type"), 'required');
		$this->form_validation->set_rules('payment_id', $this->lang->line("payment_id"), 'required');
		$this->form_validation->set_rules('amount', $this->lang->line("amount"), 'required');
		$this->form_validation->set_rules('amount_paid', $this->lang->line("amount_paid"), 'required');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$res['booking_id'] = $this->input->post('booking_id');
			$res['payment_id'] = $this->input->post('payment_id');
			$res['amount'] = $this->input->post('amount');
			$res['payment_mode'] = $this->input->post('payment_mode');
			$res['amount_paid'] = $this->input->post('amount_paid');
			$res['balance_paid'] = $this->input->post('amount_paid') - $this->input->post('amount');
			
			if($res['balance_paid'] >= 0){
				$data = $this->drivers_api->paymentPaid($res, $countryCode);
				if($data == TRUE){
					$notification['title'] = 'Driver Collect Payment';
					$notification['message'] = $user_data->first_name.' has been ride finish, collect payment';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->drivers_api->insertNotification($notification, $countryCode);
					
					$result = array( 'status'=> true , 'message'=> 'Success');
				}else{
					$result = array( 'status'=> false , 'message'=> 'Data is empty');
				}
			}else{
				$result = array( 'status'=> false , 'message'=> 'your amount is less than!');
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
	
	public function driver_available_response_post(){
		
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		$this->form_validation->set_rules('from_latitude', $this->lang->line("from_latitude"), 'required');
		$this->form_validation->set_rules('from_longitude', $this->lang->line("from_longitude"), 'required');
		$this->form_validation->set_rules('cab_type_id', $this->lang->line("cab_type_id"), 'required');
		//$this->from_validation->set_rules('status', $this->lang->line("status"), 'required');
		
		//$this->form_validation->set_rules('pickup_lat', $this->lang->line("latitude"), 'required');
		//$this->form_validation->set_rules('pickup_lng', $this->lang->line("longitude"), 'required');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$onridecheck = $this->site->RidecancelCheck($this->input->post('ride_id'));
			if($onridecheck == 1){
				$result = array( 'status'=> 0 , 'message' => 'Ride Already Cancel.');
			}else{
				
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$driver_status = $this->drivers_api->getDriverStatus($user_data->id,  $countryCode);
			$settings = $this->drivers_api->getSettings($countryCode);
			
			$distance = 20;
			
			$radius = 3959;//6371;
			$val['taxi_type'] = $this->input->post('cab_type_id');
			$val['latitude'] = $this->input->post('from_latitude');
			$val['longitude'] = $this->input->post('from_longitude');
			$val['distance'] = $distance; 
			
			$val['ride_id'] = $this->input->post('ride_id');
			$status = $this->input->post('status') ? $this->input->post('status') : 0;
			$ride_id = $this->input->post('ride_id');
			$ride_otp = random_string('numeric', 6);
			
			
			if($status == 1){
				
				$check_exit_ride = $this->site->checkRide($user_data->id, 2);
				
				$update_taxi = array(
					'driver_id' => $user_data->id,
					'vendor_id' => $user_data->parent_id,
					'taxi_id' => $driver_status->taxi_id,
					'status' => $check_exit_ride != 0 ? 10 : 2,
					'ride_otp' => $ride_otp,
					'ride_type' => 1
					//'dropoff_lat' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
					//'dropoff_lng' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0'
				); 
				
				
				
				$update_driver = array(
					'driver_id' => $user_data->id,
					'ride_id' => $ride_id,
					'status' => $status,
				);
				
				
				$ride_data = $this->drivers_api->getRideID($ride_id, $countryCode);
				$waypoint_data = $this->site->getWaypoint($ride_id);
				$this->site->bookingEmitDriverupdate($ride_id, $user_data->id, $ride_data->customer_id);
				
				$payment_id = $ride_data->payment_id;
				$payment_name = $this->drivers_api->getPaymentName($payment_id, $countryCode);
				$distance_km = $ride_data->distance_km;
				$distance_price = $ride_data->distance_price;
				if(!empty($ride_data)){
					$location = $this->site->findLocation($this->input->post('from_latitude'), $this->input->post('from_longitude'), $countryCode);
					
					$ride_routes = array(
						'latitude' => $this->input->post('from_latitude'),
						'longitude' => $this->input->post('from_longitude'), 
						'location' => $location,
						'timing' => date('Y-m-d H:i:s'),
						'trip_made' => 1,
						'ride_id' => $ride_id,
					);
					
					$data[] = $this->drivers_api->driveraccept($update_taxi, $update_driver, $ride_routes,  $ride_id, $user_data->id, $countryCode);
					
					$data_value = array(
						'pick_up' => $data[0]->start ? $data[0]->start : '0',
						'drop_off' => $data[0]->end ? $data[0]->end : '0',
						'pick_lat' => $data[0]->start_lat ? $data[0]->start_lat : '0',
						'pick_lng' => $data[0]->start_lng ? $data[0]->start_lng : '0',
						'drop_lat' => $data[0]->end_lat ? $data[0]->end_lat : '0',
						'drop_lng' => $data[0]->end_lng ? $data[0]->end_lng : '0',
						'distance_km' => $distance_km,
						'distance_price' => $distance_price,
						'payment_name' => $payment_name,
						'payment_id' => $payment_id,
						'customer_name' => $data[0]->customer_name ? $data[0]->customer_name : '0',
						'customer_mobile' => $data[0]->customer_mobile ? $data[0]->customer_mobile : '0',
						'customer_country_code' => $data[0]->customer_country_code ? $data[0]->customer_country_code : '0',
						'customer_photo' => $data[0]->customer_photo ? $data[0]->customer_photo : '0',
						'mapcar' => $data[0]->mapcar ? $data[0]->mapcar : '0',
						'waypoint_data' => $waypoint_data
					);
					
					
					if($data[0] != FALSE){
						
						$customer_data = $this->drivers_api->getCustomerID($data[0]->customer_id, $countryCode);
						$driver_data = $this->drivers_api->getDriverID($data[0]->driver_id, $countryCode);
						$taxi_data = $this->drivers_api->getTaxiID($data[0]->taxi_id, $countryCode);
						
						$sms_phone = $customer_data->country_code . $customer_data->mobile;
						$sms_country_code = $customer_data->country_code;
						$sms_phone_otp = $data[0]->ride_otp;
						
						$customer_name = $customer_data->first_name;
						$driver_name = $driver_data->first_name;
						$driver_phone = $driver_data->country_code.$driver_data->mobile;
						$taxi_number = $taxi_data->number;
						
						if(!empty($data[0]->driver_id)){
							
							$notification['title'] = 'Ride Booking';
							$notification['message'] = 'A customer booked for ride. booking id : '.$data[0]->id.' customer details : '.$customer_name.' ('.$customer_data->country_code.' '.$customer_data->mobile.')';
							$notification['user_type'] = 1;
							$notification['user_id'] = $data[0]->customer_id;
							
							$this->drivers_api->insertNotification($notification, $countryCode);
							
							$taxi_data = $this->drivers_api->getTaxiID($data[0]->taxi_id, $countryCode);
							$driver_val = $this->drivers_api->getDriverID($data[0]->driver_id, $countryCode);
							
							if($driver_val->photo !=''){
								$driver_photo = base_url('assets/uploads/').$driver_val->photo;
							}else{
								$driver_photo = base_url('assets/uploads/').'no_image.png';
							}
							
							if($taxi_data->photo !=''){
								$taxi_image = base_url('assets/uploads/').$taxi_data->photo;
							}else{
								$taxi_image = base_url('assets/uploads/').'no_image.png';
							}
							
							
							$taxitype_val = $this->drivers_api->getTaxitypeID($taxi_data->type, $countryCode);
							
							$loc = $this->site->GetDrivingDistance($this->input->post('from_latitude'), $this->input->post('from_longitude'),  $driver_val->current_latitude, $driver_val->current_longitude, $countryCode);
							
							$overall_rating = $this->site->getOveralldriverRating($data[0]->driver_id, $countryCode);
							
							if(!empty($driver_data)){				
								$event = 'server_booking_accept';
								$socket_id = $this->site->getSocketID($data[0]->customer_id, 1, $countryCode);
								$edata = array(
									'id' => 1,
									'msg' => 'Driver has been allocated',
									'payment_id' => $payment_id,
									'payment_name' => $payment_name,
									'distance_km' => $distance_km,
									'distance_price' => $distance_price,
									'customer_support' => '0987654321',
									'pick_up' => $this->input->post('pick_up'),
									'drop_off' => $this->input->post('drop_off'),
									'from_latitude' => $this->input->post('from_latitude'),
									'from_longitude' => $this->input->post('from_longitude'),
									'to_latitude' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
									'to_longitude' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0',
									'driver_latitude' => $driver_val->current_latitude,
									'driver_longitude' => $driver_val->current_longitude,
									'customer_id' => $data[0]->customer_id,
									'cab_type_id' => $this->input->post('cab_type_id'),
									'ride_id' => $ride_id,
									'waypoint_data' => $waypoint_data,
									'driver_id' => $data[0]->driver_id,
									'driver_oauth_token' => $driver_val->oauth_token,
									'driver_mobile' => $driver_val->country_code.$driver_val->mobile,
									'driver_name' => $driver_val->first_name.' '.$driver_val->last_name,
									'driver_photo' => $driver_photo,
									'driver_taxi_name' => $taxi_data->name,
									'taxi_image' => $taxi_image,
									'driver_taxi_number' => $taxi_data->number,
									'driver_taxi_type' => $taxitype_val->name,
									'ride_otp' => $ride_otp,
									'overall_rating' => $overall_rating,
									'distance' => $loc['distance'] ? $loc['distance'] : '0',
									'time' => $loc['time'] ? $loc['time'] : '0',
									'mapcar' => $data[0]->mapcar ? $data[0]->mapcar : '0',
									'socket_id' => $socket_id
									
								);
								
								$success = 	$this->socketemitter->setEmit($event, $edata);
								$last_emit_array = array(
									'user_type_id' => 1,
									'user_id' => $data[0]->customer_id,
									'emit_name' => 'driver_accept_ride',
									'emit_data' => json_encode($edata),
									'created_on' => date('Y-m-d H:i:s')
								);
								$this->site->insertLastEmit($last_emit_array);
								
								/*$customer_socket_id = $this->site->getSocketID($data[0]->customer_id, 1, $countryCode);
								$driver_socket_id = $this->site->getSocketID($data[0]->driver_id, 2, $countryCode);
								
								$cus_data = $this->site->get_customer($data[0]->customer_id);
								$dri_data = $this->site->get_driver($data[0]->driver_id);
								
								$chat_data = array(
									'customer_socket_id' => $customer_socket_id,
									'driver_socket_id' => $driver_socket_id,
									'customer_name' => $cus_data->first_name.' '.$cus_data->last_name,
									'driver_name' => $dri_data->first_name.' '.$dri_data->last_name,
								);
								$chat_event = 'server_chat_join';
								$chat = $this->socketemitter->setEmit($chat_event, $chat_data);*/
							}
						}
						
						if($check_exit_ride != 0){
							$result = array( 'status'=> 2 , 'message'=> 'Next Ride Booked Successfully!', 'data' => $data_value);
						}else{
						
						$this->sms_booking_active($customer_name, $driver_name, $driver_phone, $taxi_number, $sms_phone, $sms_country_code);
						$response_sms = $this->sms_ride_active($sms_phone_otp, $sms_phone, $sms_country_code);
						$result = array( 'status'=> 1 , 'message'=> 'Booked Successfully!', 'data' => $data_value);
						
						}
						
					}else{
						$result = array( 'status'=> 0 , 'message'=> 'not accept ride');
					}
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'customer has been ride canceled.');
				}
				
				
			}
			else{
				
				$update_driver = array(
					'driver_id' => $user_data->id,
					'ride_id' => $ride_id,
					'status' => $status,
				);
				
				
				$ride_data = $this->drivers_api->getRideID($ride_id, $countryCode);
				$payment_id = $ride_data->payment_id;
				$this->site->bookingEmitDriverupdate($ride_id, $user_data->id, $ride_data->customer_id);
				$payment_name = $this->drivers_api->getPaymentName($payment_id, $countryCode);
				$distance_km = $ride_data->distance_km;
				$distance_price = $ride_data->distance_price;
				
				if($ride_data->booked_type == 1){
					$booked_type_text = 'City Ride';
				}elseif($ride_data->booked_type == 2){
					$booked_type_text = 'Rental Ride';
				}elseif($ride_data->booked_type == 3){
					$booked_type_text = 'Outstation Ride';
				}else{
					$booked_type_text = 'No Ride Type';
				}
				
				$data = $this->drivers_api->driverTimeout($update_driver, $countryCode);
				if($data == TRUE){
					$driver_data = $this->drivers_api->getDrivers_radius_limit($val, $ride_id, $countryCode);
					
					$ride_data = $this->drivers_api->getRideID($ride_id, $countryCode);
					if(!empty($ride_data)){
						if(!empty($driver_data)){
							$socket_id = $this->site->getSocketID($driver_data[0]->id, 2, $countryCode);
							$event = 'server_booking_checking';
							
							$edata = array(
								'booked_type_text' => $booked_type_text, 
								'payment_id' => $payment_id,
								'payment_name' => $payment_name,
								'distance_km' => $distance_km,
								'distance_price' => $distance_price,
								'customer_support' => '0987654321',
								'pick_up' => $this->input->post('pick_up'),
								'drop_off' => $this->input->post('drop_off'),
								'from_latitude' => $this->input->post('from_latitude'),
								'from_longitude' => $this->input->post('from_longitude'),
								'to_latitude' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
								'to_longitude' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0',
								'cab_type_id' => $driver_data[0]->type,
								'ride_id' => $ride_id,
								'driver_id' => $driver_data[0]->id,
								'driver_oauth_token' => $driver_data[0]->oauth_token,
								'socket_id' => $socket_id,
								'waypoint_data' => $waypoint_data,
								'driver_time' => $settings->driver_time
								
							);	
							
						$success = 	$this->socketemitter->setEmit($event, $edata);
						
						/*$last_emit_array = array(
							'user_type_id' => 2,
							'user_id' => $driver_data[0]->id,
							'emit_name' => 'driver_available_check',
							'emit_data' => json_encode($edata),
							'created_on' => date('Y-m-d H:i:s')
						);
						$this->site->insertLastEmit($last_emit_array);*/
						
						}else{
							
							$cancel_location = $this->site->findLocation($this->input->post('from_latitude'), $this->input->post('from_longitude'), $countryCode);
							$cancel['driver_id'] = $user_data->id;
							$cancel['booking_id'] = $this->input->post('ride_id');
							$cancel['cancel_msg'] = 'Any one of the driver not accept ride. Please try again.';
							$cancel['cancel_location'] = $cancel_location ? $cancel_location : '';
							$res = $this->drivers_api->nodriverCancel($cancel, $countryCode);
							if($res == TRUE){
							$event = 'server_not_accept_driver';
							$socket_id = $this->site->getSocketID($ride_data->customer_id, 1, $countryCode);
							$edata = array(
								'id' => 2,
								'msg' => 'Driver has been not allocated, please try again another ride',
								'pick_up' => $this->input->post('pick_up'),
								'drop_off' => $this->input->post('drop_off'),
								'from_latitude' => $this->input->post('from_latitude'),
								'from_longitude' => $this->input->post('from_longitude'),
								'to_latitude' => $this->input->post('to_latitude') ? $this->input->post('to_latitude') : '0',
								'to_longitude' => $this->input->post('to_longitude') ? $this->input->post('to_longitude') : '0',
								'driver_latitude' => '0',
								'driver_longitude' => '0',
								'customer_id' => $ride_data->customer_id,
								'cab_type_id' => $this->input->post('cab_type_id'),
								'ride_id' => $ride_id,
								'driver_id' => '0',
								'driver_oauth_token' => '0',
								'driver_mobile' => '0',
								'driver_name' => '0',
								'driver_photo' => '0',
								'driver_taxi_name' => '0',
								'taxi_image' => '0',
								'driver_taxi_number' => '0',
								'driver_taxi_type' => '0',
								'ride_otp' => '0',
								'overall_rating' => '0',
								'distance' => '0',
								'time' => '0',
								'mapcar' => '0',
								'waypoint_data' => $waypoint_data,
								'socket_id' => $socket_id
							);
							$success = 	$this->socketemitter->setEmit($event, $edata);	
							/*$last_emit_array = array(
								'user_type_id' => 1,
								'user_id' => $ride_data->customer_id,
								'emit_name' => 'driver_not_accept_ride',
								'emit_data' => json_encode($edata),
								'created_on' => date('Y-m-d H:i:s')
							);
							$this->site->insertLastEmit($last_emit_array);*/
							
							}
						}
					}
					$result = array( 'status'=> 1 , 'message'=> 'not accept or timeout driver has been insert');
					
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'not accept or timeout driver has been not insert');
				}
				
				
			}
			
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
	
	public function sms_booking_active($customer_name, $driver_name, $driver_phone, $taxi_number, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[CUSTOMERNAME]', '[DRIVERNAME]', '[DRIVERNUMBER]', '[CABNUMBER]');
        $sms_rep_arr = array($customer_name, $driver_name, $driver_phone, $taxi_number);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-booking-confirmation", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_ride_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "ride-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function continents_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->drivers_api->getContinents($countryCode);
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function emission_norms_get(){
		$countryCode = $this->input->get('is_country');
		$data = array(array('name' => 'BS IV'), array('name' => 'BS III'), array('name' => 'BS V'), array('name' => 'BS VI'));
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function additioncontact_type_get(){
		$countryCode = $this->input->get('is_country');
		$data = array(array('name' => 'Family'), array('name' => 'Relative'), array('name' => 'Friend'), array('name' => 'Other'));
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function fuel_type_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->drivers_api->getALLTaxi_fuel($countryCode);
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function taxi_make_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->drivers_api->NewgetTaxi($countryCode);
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function taxi_type_get(){
		$countryCode = $this->input->get('is_country');
		$data = $this->drivers_api->NewgetTaxitype($countryCode);
		if($data == TRUE){
			$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
		}else{
			$result = array( 'status'=> false , 'message'=> 'Data is empty');
		}

		$this->response($result);
	}
	
	public function taxi_model_post(){
		$this->form_validation->set_rules('make_id', $this->lang->line("make_id"), 'required');	
		$this->form_validation->set_rules('type_id', $this->lang->line("type_id"), 'required');	
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$make_id = $this->input->post('make_id');
			$type_id = $this->input->post('type_id');

			$data = $this->drivers_api->getModelbymake_type($make_id, $type_id, $countryCode);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
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
	
	
	public function city_post(){
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			$state_id = $this->input->post('oauth_token');

			$data = $this->drivers_api->getCitieswithstate($countryCode);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
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
	
	public function waypoint_pick_drop_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		$this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
		$this->form_validation->set_rules('waypoint_lat', $this->lang->line("waypoint_lat"), 'required');
		$this->form_validation->set_rules('waypoint_lng', $this->lang->line("waypoint_lng"), 'required');
		
		
		$settings = $this->drivers_api->getSettings($countryCode);
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$waypoint = $this->site->findLocation($this->input->post('waypoint_lat'), $this->input->post('waypoint_lng'), $countryCode);	
			$setting = $this->site->get_setting($countryCode);
			
			$row['waypoint_time'] = date('Y-m-d H:i:s');
			$row['ride_id'] = $this->input->post('ride_id');
			$row['type'] = $this->input->post('type');
			$row['waypoint_lat'] = $this->input->post('waypoint_lat');
			$row['waypoint_lng'] = $this->input->post('waypoint_lng');
			$row['waypoint'] = $waypoint;
			
			if($this->input->post('type') == 2){
				$waypoint_start_time = $this->site->waypointreached_time($this->input->post('ride_id'));
				
				$start = strtotime($waypoint_start_time);
				$end = strtotime(date('Y-m-d H:i:s'));
				$waiting_min = $setting->waiting_time * 60;
				$mins = ($end - $start) / $waiting_min;
				
				$row['waypoint_total_time'] = $mins;
				$row['waypoint_id'] = 0;
				        
			}else{
				$row['waypoint_id'] = $this->input->post('waypoint_id');
				$row['waypoint_total_time'] = 0;
			}
			
			//$ride = $this->drivers_api->getRideID($this->input->post('booking_id'), $countryCode);
			$data = $this->drivers_api->insertWaypointtime($row, $countryCode);
			if($data == TRUE){
				$waypoint_data = $this->site->getWaypoint($this->input->post('ride_id'));
				 $result = array( 'status'=> true , 'message'=> 'success', 'waiting_time' => $settings->waiting_time, 'waypoint_data' => $waypoint_data);
				
			}else{
				$result = array( 'status'=> false , 'message'=> 'faild');
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
	
	
	
	public function waitingride_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('booking_id', $this->lang->line("booking_id"), 'required');
		
		$settings = $this->drivers_api->getSettings($countryCode);
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
					
				
			$row['driver_id'] = $user_data->id;
			$row['ride_id'] = $this->input->post('booking_id');
			$row['waiting_type'] = $this->input->post('waiting_type') != NULL ? $this->input->post('waiting_type') : 0;
			$row['total_minutes'] = $this->input->post('total_minutes') != NULL ? $this->input->post('total_minutes') : 0;
			$row['lat'] = $this->input->post('lat') != NULL ? $this->input->post('lat') : '';
			$row['lng'] = $this->input->post('lng') != NULL ? $this->input->post('lng') : '';
			
			$res = $this->drivers_api->waitingride($row, $countryCode);
			
			if($res == TRUE){				
				
				$result = array( 'status'=> 1 , 'message'=> 'waiting fare added');
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not complete ride');
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
	
	public function my_ridedetails_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		
		$ride_id = $this->input->post('ride_id');
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		$this->form_validation->set_rules('ride_id', $this->lang->line("ride_id"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->site->ridedetails($ride_id, $countryCode);
			
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
	
	
	public function logout_post(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		
		
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');

		if ($this->form_validation->run() == true) {
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$this->site->updateLog($user_data->id, '1', '2');
			
			$result = array( 'status'=> true , 'message'=> 'Logout Success');
			/*$data = $this->site->ridedetails($ride_id, $countryCode);
			
			if(!empty($data)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'No Rides');
			}*/
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
	}

	function health_post(){
		
		$countryCode = $this->input->post('is_country');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required|callback_check_exist[oauth_token]');
		
		$this->form_validation->set_rules('is_country', $this->lang->line("instance"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->drivers_api->getDriver($this->input->post('oauth_token'), $countryCode);
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr,  json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$res = $this->drivers_api->checkHealth($user_data->id);
			
			if($res == FALSE){
				
				$health_name = $this->drivers_api->gethealthname($this->input->post('health_id'));
				
				
				if($this->input->post('type') == 1){

					$minutes=$this->input->post('health_minutes');
					$health_hours = date('H:i:s', mktime(0,$minutes));
					$health_end_datetimestamp = strtotime($this->input->post('health_start_date').' + '.$minutes.' minute');
					$health_end_date = date('Y-m-d H:i:s', $health_end_datetimestamp);

					
					
				}else{
					$health_end_datetimestamp = strtotime($this->input->post('health_start_date'));
					$health_end_date = date('Y-m-d', $health_end_datetimestamp).' 23:59:00';
					
					
					$minutes =  round(abs(strtotime($health_end_date) - $health_end_datetimestamp) / 60);
					
					
					$health_hours = date('H:i:s', mktime(0,$minutes));
					
				}

				$insert = array(
					'driver_id' => $user_data->id,
					'health_id' => $this->input->post('health_id'),
					'health_name' => $health_name,
					'type' => $this->input->post('type'),
					'health_start_date' => $this->input->post('health_start_date'),
					'health_end_date' => $health_end_date,
					'health_hours' => $health_hours,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $countryCode
				);
				
				$data = $this->drivers_api->insertHealth($insert);
				if($data == TRUE){
					
					$result = array( 'status'=> 1 , 'message'=> 'Success', 'data' => $data);
					
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Not Update');
				}
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'Already exit health');
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
	
	function hours_time_get()
	{
		$user_id = 482;
		$current_time = '2021-01-21 17:00:00';
		$current_date = '2021-01-21';
		/*$query = $this->db->query('SELECT m1.user_id, (SELECT SUM( UNIX_TIMESTAMP(log_date_time ))
FROM kapp_user_login_logout as m2
WHERE log_type = 1 AND m2.user_id = m1.user_id
) AS On_Time, (

SELECT SUM( UNIX_TIMESTAMP(log_date_time ))
FROM kapp_user_login_logout as m3
WHERE log_type = 2 AND m3.user_id = m1.user_id
) AS Off_Time FROM kapp_user_login_logout as m1 GROUP BY user_id');
echo 'SELECT m1.user_id, (SELECT SUM( UNIX_TIMESTAMP(log_date_time ))
FROM kapp_user_login_logout as m2
WHERE log_type = 1 AND m2.user_id = m1.user_id
) AS On_Time, (

SELECT SUM( UNIX_TIMESTAMP(log_date_time ))
FROM kapp_user_login_logout as m3
WHERE log_type = 2 AND m3.user_id = m1.user_id
) AS Off_Time FROM kapp_user_login_logout as m1 GROUP BY user_id';*/
	
		$query = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,logout_date)) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'" DATE(login_date) = "'.$current_date.'" AND is_stop != 0 group by date(login_date)');
		if($query->num_rows()>0){
			foreach (($query->result()) as $row) {
				
				
				$data1[]	= $row;
			}
		}
		$query1 = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,"'.$current_time.'")) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'"  DATE(login_date) = "'.$current_date.'" AND is_stop = 0 group by date(login_date)');
		if($query1->num_rows()>0){
			foreach (($query1->result()) as $row1) {
				
				$data2[]	= $row1;
			}
		}
		
		print_r($data1);
		print_r($data2);
		
		$data = array_merge($data1, $data2);
		
		$total_timestamp = 0;
		$total_hours = '00:00:00';
		foreach($data as $key=>$value){
			$total_timestamp += $value->total_timestamp;
		}
		echo $total_hours = gmdate("H:i:s", $total_timestamp);
		echo $total_timestamp;
		
		echo $hours = floor(8 * 3600);
		//print_r($data);
	}
}
