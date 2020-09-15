<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Vendor extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->api_model('vendor_api');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->library('upload');
		$this->getUserIpAddr = $this->site->getUserIpAddr();
        //$this->upload_path = 'assets/uploads/customers/';
        //$this->thumbs_path = 'assets/uploads/customers/thumbs/';
        $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->library('socketemitter');
		
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
	
	/*New Changes*/
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	
	public function register_resend_otp_post(){
		$data = array();
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->customer_api->getCustomer($this->input->post('oauth_token'));
			
			$row['country_code'] = $this->input->post('country_code');
			$row['mobile'] = $this->input->post('mobile');
			//$mobile_otp = random_string('numeric', 6);
			
			$data = $this->vendor_api->registerresendotp($row);
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
		
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver_id"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getDriversettingView($this->input->post('driver_id'));
			
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
	
	
	public function vendorsetting_post(){
		
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver_id"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$driver_id = $this->input->post('driver_id');
			$insert = array(
				'is_daily' => $this->input->post('is_daily'),
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'is_hiring' => $this->input->post('is_hiring'),
				'is_corporate' => $this->input->post('is_corporate'), 
				'base_location' => $this->input->post('base_location'),
			);
			
			$data = $this->vendor_api->updateDriver($insert, $driver_id);
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
	
	public function allvehicles_post(){
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');

		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$vendor_id = $user_data->id;
			$res = $this->vendor_api->Getallvehicles($vendor_id);
			
			$data = $res;
			if(!empty($res)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty data');
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
	
	public function alldrivers_post(){
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');

		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$vendor_id = $user_data->id;
			$res = $this->vendor_api->Getalldrivers($vendor_id);
			
			$data = $res;
			if(!empty($res)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty data');
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
	
	public function allaccount_post(){
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');

		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$vendor_id = $user_data->id;
			$res = $this->vendor_api->Getallaccount($vendor_id);
			
			$data = $res;
			if(!empty($res)){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty data');
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
	
	public function sms_booking_active($customer_name, $vendor_name, $vendor_phone, $taxi_number, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[CUSTOMERNAME]', '[DRIVERNAME]', '[DRIVERNUMBER]', '[CABNUMBER]');
        $sms_rep_arr = array($customer_name, $vendor_name, $vendor_phone, $taxi_number);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-booking-confirmation", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_ride_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "ride-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function vendortatus_post(){
		$this->form_validation->set_rules('mode', $this->lang->line("mode"), 'required');
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');

		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['mode'] = $this->input->post('mode');
			$row['vendor_id'] = $user_data->id;
			$data = $this->vendor_api->vendorUpdateStatus($row);
			
			$data = $this->vendor_api->fcminsert($device);
			if($data == TRUE){
				$result = array( 'status'=> true , 'message'=> 'Update Success');
			}else{
				$result = array( 'status'=> false , 'message'=> 'Not update status');
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
		$this->form_validation->set_rules('device_imei', $this->lang->line("device_imei"), 'required');
		$this->form_validation->set_rules('device_token', $this->lang->line("device_token"), 'required');
		$this->form_validation->set_rules('devices_type', $this->lang->line("devices_type"), 'required');		
		if ($this->form_validation->run() == true) {
			$device['device_imei'] = $this->input->post('device_imei');
			$device['device_token'] = $this->input->post('device_token');
			$device['devices_type'] = $this->input->post('devices_type');
			$device['user_id'] = $this->input->post('user_id') ? $this->input->post('user_id') : 0;
			$device['group_id'] = $this->Vendor;
			$device['user_type'] = $this->input->post('user_type') ? $this->input->post('user_type') : 0;
			
			$data = $this->vendor_api->fcminsert($device);
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

			$data = $this->vendor_api->fcmdelete($device);
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
	
	
	public function register_post(){
	
		
        $this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');  
        $this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		//$this->form_validation->set_rules('join_type', $this->lang->line("join_type"), 'required');
		
		if ($this->form_validation->run() == true) {
				
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $check_mobile = $this->vendor_api->checkMobile($this->input->post('mobile'), $this->input->post('country_code'));
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
				
				
			}elseif($this->input->post('mobile_verify') == 1 && $check_mobile == 0){
				
						
			
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => $this->input->post('devices_imei'),
				'join_type' => 2,
				'email' => $this->input->post('email') ? $this->input->post('email') : '',
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name') ? $this->input->post('last_name') : '',
				'gender' => $this->input->post('gender') ? $this->input->post('gender') : '',
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Admin,
				'parent_id' => 2,
				//'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Vendor,
				'is_edit' => 1,
				'created_type' => 1,
				'active' => 1
		   );
		   
		  
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name') ? $this->input->post('first_name') : '',
				'last_name' => $this->input->post('last_name') ? $this->input->post('last_name') : '',
				'gender' => $this->input->post('gender') ? $this->input->post('gender') : '',
				'is_edit' => 1,
				'created_type' => 1,
			
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
				'is_edit' => 1,
				'created_type' => 1
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
				'is_edit' => 1,
				'created_type' => 1
			);
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst') ? $this->input->post('gst') : '',
				'telephone_number' => $this->input->post('telephone_number') ? $this->input->post('telephone_number') : '',
				'legal_entity' => $this->input->post('legal_entity') ? $this->input->post('legal_entity') : '',
				'is_edit' => 1,
				'created_type' => 1

			);
			
			$user_document = array(
				'is_edit' => 1,
				'created_type' => 1
				
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
			
			
			
			
				
				
				$data = $this->vendor_api->add_vendor($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor,$this->Vendor);
				
				if($data == TRUE){
					$sms_message = $this->input->post('first_name').' your account has been register successfully. Waiting for admin approval process';
					$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
					$sms_country_code = $this->input->post('country_code');

					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
					$notification['title'] = 'Vendor Register';
					$notification['message'] = 'New User ('.$this->input->post('first_name').') has been register heyycab';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->vendor_api->insertNotification($notification);
					
					
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
	
	public function modify_profile_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		
		$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->vendor_api->getUserEdit($user_data->id);
		
		$customer_type = 1;

		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('email')) || !empty($this->input->post('first_name')) || !empty($this->input->post('last_name')) || !empty($this->input->post('gender'))  || $_FILES['photo']['size'] > 0 ||   $_FILES['local_image']['size'] > 0 || $_FILES['permanent_image']['size'] > 0 ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender')){
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
				'dob' =>$dob,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
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
				'local_pincode' => $_FILES['local_image']['size'] == 0 ? $res->local_pincode : 0,
				
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
				'permanent_pincode' => $_FILES['permanent_image']['size'] == 0 ? $res->permanent_pincode : 0,
				
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
			
			if($_FILES['loan_doc']['size'] == 0){
				$loan_verify = $res->loan_verify;
				$loan_approved_by = $res->loan_approved_by;
				$loan_approved_on = $res->loan_approved_on;
			}else{
				$loan_verify = 0;
				$loan_approved_by = 0;
				$loan_approved_on = '0000:00:00 00:00:00';
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
				
				
				
				'loan_information' => $_FILES['loan_doc']['size'] == 0 ? $res->loan_information : '',
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
				'is_edit' => 1,
				'complete_document'	 => 1
				
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
			
			
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $res->loan_doc;
			}
			
			if($this->input->post('gst') == $res->gst && $this->input->post('telephone_number') == $res->telephone_number && $this->input->post('legal_entity') == $res->legal_entity){
				$vendor_is_verify = $res->vendor_is_verify;
				$vendor_approved_by = $res->vendor_approved_by;
				$vendor_approved_on = $res->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst') ? $this->input->post('gst') : $res->gst,
				'telephone_number' => $this->input->post('telephone_number') ? $this->input->post('telephone_number') : $res->telephone_number,
				'legal_entity' => $this->input->post('legal_entity') ? $this->input->post('legal_entity') : $res->legal_entity,
				'associated_id' => $res->associated_id,
				'continent_id' => $res->vendor_continent_id,
				'country_id' => $res->vendor_country_id,
				'zone_id' => $res->vendor_zone_id,
				'state_id' => $res->vendor_state_id,
				'city_id' => $res->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1,
				'complete_vendor' => 1
			);
				
				$data = $this->vendor_api->modify_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type);
				
				if($data){
					$result = array( 'status'=> 1, 'message' => 'Vendor edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Vendor does not edit.');
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
	
	public function login_post(){

		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');	
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');		
		if ($this->form_validation->run() == true) {
			
			$otp = random_string('numeric', 6);
			
			$login['country_code'] = $this->input->post('country_code');
			$login['mobile'] = $this->input->post('mobile');
			$login['password'] = md5($this->input->post('password'));
			$login['devices_imei'] = $this->input->post('devices_imei');
			$login['otp'] = $otp;
			//$devices_check = $this->site->devicesCheck($api_key);
			//if($devices_check == $devices_key){
				$res = $this->vendor_api->check_login($login);
				$data[] = $res;
				if(!empty($res)){
					if($res->check_status == 'first_time_otp'){
						
						$sms_phone_otp = $res->mobile_otp;
						$sms_phone = $res->country_code.$res->mobile;
						$sms_country_code = $res->country_code;
						
						$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
						if($response_sms){
							$result = array( 'status'=> 2 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
						}else{
							$result = array( 'status'=> 2 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
						}
						
					}elseif($res->check_status == 'login'){
						$result = array( 'status'=> 1 , 'message'=> 'Login Success', 'data' => $data);
					}elseif($res->check_status == 'change_otp'){
						$sms_phone_otp = $otp;
						$sms_phone = $res->country_code.$res->mobile;
						$sms_country_code = $res->country_code;
						
						$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
						if($response_sms){
							$result = array( 'status'=> 3 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
						}else{
							$result = array( 'status'=> 3 , 'message'=> 'OTP has been sent. Check it', 'data' => $res->oauth_token);
						}
						
					}elseif($res->check_status == 'notactive'){
						$result = array( 'status'=> 0 , 'message'=> 'Your account has been deactive. please contact admin.');
					}
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'Invalid credentials');
				}
			//}else{
				//$result = array( 'status'=> false , 'message'=> 'Devices Key does not matche. please check your devices key');	
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
	
	public function verify_firstotp_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['vendor_id'] = $user_data->id;
			$row['devices_imei'] = $this->input->post('devices_imei');
			$res = $this->vendor_api->checkfirstotp($row);
			$data[] = $res;
			
			if(!empty($res)){
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
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		$this->form_validation->set_rules('devices_imei', $this->lang->line("devices_imei"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['oauth_token'] = $this->input->post('oauth_token');
			$row['otp'] = $this->input->post('otp');
			$row['devices_imei'] = $this->input->post('devices_imei');
			$row['vendor_id'] = $user_data->id;
			$res = $this->vendor_api->devicescheckotp($row);
			$data[] = $res;
			if(!empty($res)){
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$row['otp'] = $this->input->post('otp');
			$row['vendor_id'] = $user_data->id;
			$res = $this->vendor_api->checkotp($row);
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$row['vendor_id'] = $user_data->id;
			$row['mobile_otp'] = random_string('numeric', 6);
			
			$data = $this->vendor_api->resendotp($row);
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
		if ($this->form_validation->run() == true) {
			
			$row['mobile'] = $this->input->post('mobile');
			$row['forgot_otp'] = random_string('numeric', 6);
			
			$data = $this->vendor_api->forgototp($row);
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
		$this->form_validation->set_rules('vendor_id', $this->lang->line("vendor_id"), 'required');
		$this->form_validation->set_rules('otp', $this->lang->line("otp"), 'required');
		if ($this->form_validation->run() == true) {
			
			
			$row['forgot_otp'] = $this->input->post('otp');
			$row['vendor_id'] = $this->input->post('vendor_id');
			$data = $this->vendor_api->forgotcheckotp($row);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $this->input->post('vendor_id'));
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
		$this->form_validation->set_rules('vendor_id', $this->lang->line("vendor_id"), 'required');
		if ($this->form_validation->run() == true) {
			
			//$user_data = $this->vendor_api->getDriver($this->input->post('oauth_token'));
			
			$row['vendor_id'] = $this->input->post('vendor_id');
			//$row['forgot_otp'] = random_string('numeric', 6);
			
			$data = $this->vendor_api->forgotresendotp($row);
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
		$this->form_validation->set_rules('vendor_id', $this->lang->line("vendor_id"), 'required');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		if ($this->form_validation->run() == true) {
			
			$customer['password'] = md5($this->input->post('password'));
			$customer['vendor_id'] = $this->input->post('vendor_id');
			$customer['text_password'] = $this->input->post('password');
			
			$data = $this->vendor_api->updatepassword($customer);
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$vendor_type = '1'; //1 - basic details, 2- bank details, 3- document
			$data = $this->vendor_api->myprofile($user_data->id, $this->Vendor, $vendor_type);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		
		$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->vendor_api->getUserEdit($user_data->id);
		
		$customer_type = 1;

		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('email')) || !empty($this->input->post('gst')) || !empty($this->input->post('telephone_number')) || !empty($this->input->post('legal_entity')) || !empty($this->input->post('first_name')) || !empty($this->input->post('last_name')) || !empty($this->input->post('gender'))  || $_FILES['photo']['size'] > 0 ||   $_FILES['local_image']['size'] > 0 || $_FILES['permanent_image']['size'] > 0 ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender') && $res->dob ==$this->input->post('dob')){
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
				'dob' =>$dob,
				
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
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
				
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : $res->local_pincode,
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : $res->permanent_pincode,
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
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
				'is_edit' => 1
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
			
		$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $res->aadhaar_no : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $res->pancard_no : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				
				'loan_information' => $_FILES['loan_doc']['size'] == 0 ? $res->loan_information : '',
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
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
			
			
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $res->loan_doc;
			}
			
			if($this->input->post('gst') == $res->gst && $this->input->post('telephone_number') == $res->telephone_number && $this->input->post('legal_entity') == $res->legal_entity){
				$vendor_is_verify = $res->vendor_is_verify;
				$vendor_approved_by = $res->vendor_approved_by;
				$vendor_approved_on = $res->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'associated_id' => $res->associated_id,
				'continent_id' => $res->vendor_continent_id,
				'country_id' => $res->vendor_country_id,
				'zone_id' => $res->vendor_zone_id,
				'state_id' => $res->vendor_state_id,
				'city_id' => $res->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1
			);
				
				$data = $this->vendor_api->edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type);
				
				if($data){
					$notification['title'] = 'Vendor Profile Edit';
					$notification['message'] = $user_data->first_name.' has been profile edit';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->vendor_api->insertNotification($notification);
					
					$result = array( 'status'=> 1, 'message' => 'Vendor edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Vendor does not edit.');
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$vendor_type = '2'; //1 - basic details, 2- bank details, 3- document
			$data = $this->vendor_api->myprofile($user_data->id, $this->Vendor, $vendor_type);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		
		$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->vendor_api->getUserEdit($user_data->id);
		
		$customer_type = 2;

		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('account_holder_name')) || !empty($this->input->post('account_no')) || !empty($this->input->post('bank_name')) || !empty($this->input->post('branch_name')) || !empty($this->input->post('ifsc_code')) ){	
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender') ){
				$profile_is_approved = $res->profile_is_approved;
				$profile_approved_by = $res->profile_approved_by;
				$profile_approved_on = $res->profile_approved_on;
			}else{
				$profile_is_approved = 0;
				$profile_approved_on = '0000-00-00 00:00:00';
				$profile_approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
		   );
		   
		   if($this->input->post('dob') != NULL){
				$dob = $this->input->post('dob');
			}else{
				$dob = '0000-00-00';
			}
			
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
				
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
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
				'is_edit' => 1
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
			
		$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $res->aadhaar_no : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $res->pancard_no : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				
				'loan_information' => $_FILES['loan_doc']['size'] == 0 ? $res->loan_information : '',
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
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
			
			
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $res->loan_doc;
			}
			
			if($this->input->post('gst') == $res->gst && $this->input->post('telephone_number') == $res->telephone_number && $this->input->post('legal_entity') == $res->legal_entity){
				$vendor_is_verify = $res->vendor_is_verify;
				$vendor_approved_by = $res->vendor_approved_by;
				$vendor_approved_on = $res->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'associated_id' => $res->associated_id,
				'continent_id' => $res->vendor_continent_id,
				'country_id' => $res->vendor_country_id,
				'zone_id' => $res->vendor_zone_id,
				'state_id' => $res->vendor_state_id,
				'city_id' => $res->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1
			);
				
				$data = $this->vendor_api->edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type);
				
				if($data){
					$notification['title'] = 'Vendor Bank Edit';
					$notification['message'] = $user_data->first_name.' has been bank edit';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->vendor_api->insertNotification($notification);
					
					$result = array( 'status'=> 1, 'message' => 'Vendor edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Vendor does not edit.');
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$vendor_type = '3'; //1 - basic details, 2- bank details, 3- document
			$data = $this->vendor_api->myprofile($user_data->id, $this->Vendor, $vendor_type);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		
		$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->vendor_api->getUserEdit($user_data->id);
		
		$customer_type = 3;

		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if( $_FILES['aadhaar_image']['size'] > 0  || $_FILES['pancard_image']['size'] > 0  || $_FILES['loan_doc']['size'] > 0   ){	
				
		    		   
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
				'dob' =>$dob,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
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
				'is_edit' => 1
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
			
			if($_FILES['loan_doc']['size'] == 0){
				$loan_verify = $res->loan_verify;
				$loan_approved_by = $res->loan_approved_by;
				$loan_approved_on = $res->loan_approved_on;
			}else{
				$loan_verify = 0;
				$loan_approved_by = 0;
				$loan_approved_on = '0000:00:00 00:00:00';
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
				
				
				
				'loan_information' => $_FILES['loan_doc']['size'] == 0 ? $res->loan_information : '',
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
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
			
			
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $res->loan_doc;
			}
			
			if($this->input->post('gst') == $res->gst && $this->input->post('telephone_number') == $res->telephone_number && $this->input->post('legal_entity') == $res->legal_entity){
				$vendor_is_verify = $res->vendor_is_verify;
				$vendor_approved_by = $res->vendor_approved_by;
				$vendor_approved_on = $res->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'associated_id' => $res->associated_id,
				'continent_id' => $res->vendor_continent_id,
				'country_id' => $res->vendor_country_id,
				'zone_id' => $res->vendor_zone_id,
				'state_id' => $res->vendor_state_id,
				'city_id' => $res->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1
			);
				
				$data = $this->vendor_api->edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type);
				
				if($data){
					$notification['title'] = 'Vendor Document Edit';
					$notification['message'] = $user_data->first_name.' has been document edit';
					$notification['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->vendor_api->insertNotification($notification);
					
					$result = array( 'status'=> 1, 'message' => 'Vendor edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Vendor does not edit.');
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->vendor_api->myprofile($user_data->id, $this->Vendor);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		
		$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$user_id = $user_data->id;
		$res = $this->vendor_api->getUserEdit($user_data->id);
		
		$customer_type = 1;

		
		if ($this->form_validation->run() == true) {
			
			
			
			//$check_active = $this->customer_api->checkCustomers($user_data->id, $this->Customer);
			
			if(!empty($this->input->post('email')) || !empty($this->input->post('ref_mobile')) || !empty($this->input->post('first_name')) || !empty($this->input->post('last_name')) || !empty($this->input->post('gender'))  || $_FILES['photo']['size'] > 0 ||   $_FILES['local_image']['size'] > 0 || $_FILES['permanent_image']['size'] > 0 ){
				
		    		   
		   if($res->first_name == $this->input->post('first_name') && $res->last_name == $this->input->post('last_name') && $res->gender == $this->input->post('gender')){
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
				'dob' =>$dob,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
				'is_approved' => $profile_is_approved,
				'approved_on' => $profile_approved_on,
				'approved_by' => $profile_approved_by,
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
				'is_edit' => 1
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
			
		$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $res->aadhaar_no : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $res->pancard_no : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				
				'loan_information' => $_FILES['loan_doc']['size'] == 0 ? $res->loan_information : '',
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
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
			
			
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$res = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $res->loan_doc;
			}
			
			if($this->input->post('gst') == $res->gst && $this->input->post('telephone_number') == $res->telephone_number && $this->input->post('legal_entity') == $res->legal_entity){
				$vendor_is_verify = $res->vendor_is_verify;
				$vendor_approved_by = $res->vendor_approved_by;
				$vendor_approved_on = $res->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'associated_id' => $res->associated_id,
				'continent_id' => $res->vendor_continent_id,
				'country_id' => $res->vendor_country_id,
				'zone_id' => $res->vendor_zone_id,
				'state_id' => $res->vendor_state_id,
				'city_id' => $res->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1
			);
				
				$data = $this->vendor_api->edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type);
				
				if($data){
					$result = array( 'status'=> 1, 'message' => 'Vendor edit has been success');
				}else{
					$result = array( 'status'=> 0, 'message' => 'Vendor does not edit.');
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
	
	
	public function notifications_post(){
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$data = $this->site->Getnotification($user_data->id, '3');
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
	
	
	
	public function notallocatedtaxi_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
				$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
				$res = $this->vendor_api->notallocatedtaxi($user_data->id);
				$data = $res;
			if($res){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty drivers');
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
	
	public function allocatedopen_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			
				$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
				$driver_otp = random_string('numeric', 6);
				$row['driver_id'] = $this->input->post('driver_id');
				$row['taxi_id'] = $this->input->post('taxi_id');
				$row['is_allocated'] = 1;
				$row['allocated_start_date'] = date('Y-m-d H:i:s');
				$row['driver_otp'] = $driver_otp;
				$row['vendor_id'] = $user_data->id;	
				
				
						
				$data = $this->vendor_api->allocatedopen($row, $this->input->post('driver_id'));
				if($data){
					
					$sms_phone = $data->country_code . $data->mobile;
					$sms_country_code = $data->country_code;
					$sms_phone_otp = $driver_otp;
	
					$response_sms = $this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
					if($response_sms){
						$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $driver_otp);
					}else{
						$result = array( 'status'=> false , 'message'=> 'Unable to Send Mobile Verification Code', 'data' => $driver_otp);
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
	
	public function drivertracking_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$parent_id = $user_data->id;
			
			$data = $this->vendor_api->getAllvendortruckDriver($parent_id);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty drivers');
			}
			
		} else {
			$error = $this->form_validation->error_array();
			 foreach($error as $key => $val){
				 $errors[] = $val;
			 }
			 $result = array( 'status'=> false , 'message' => $errors[0]);
		}
		$this->response($result);
		return false;	
	}
	
	public function driver_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$parent_id = $user_data->id;
			
			$data = $this->vendor_api->getAllvendorwiseDriver($parent_id);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty drivers');
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
	
	public function driverdetail_post(){
		$data = array();
		$this->form_validation->set_rules('driver_id', $this->lang->line("driver"), 'required');
		if ($this->form_validation->run() == true) {
			$driver_id = $this->input->post('driver_id');
			
			
			$data = $this->vendor_api->getDriverDetails($driver_id);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty drivers');
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->vendor_api->mycurrentrides($user_data->id);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->vendor_api->mypastrides($user_data->id);
			
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
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			
			$data = $this->vendor_api->myupcomingrides($user_data->id);
			
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
	
	public function driveradd_post(){
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
        $this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required');  
        
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		
		if ($this->form_validation->run() == true) {
				
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$parent_id = $user_data->id;
			
			$oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $driver_oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $driver_mobile_otp = random_string('numeric', 6);
		   if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				   $is_daily = 1;
			   }else{
				   $is_daily = 0;
			   }
			   
			if($this->input->post('dob') != NULL){
				$dob = $this->input->post('dob');
			}else{
				$dob = '0000-00-00';
			}
			
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$this->input->post('dob'),
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Vendor,
				'parent_id' => $parent_id,
				'created_by' => $parent_id,
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Driver,
				'is_edit' => 1,
				'active' => 1,
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $dob,
				/*'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'is_hiring' => $this->input->post('is_hiring') ? $this->input->post('is_hiring') : 0,*/
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' =>$dob,
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
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
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
				
			$data = $this->vendor_api->add_driver($user, $user_profile, $user_address, $user_bank, $user_document);
			
			if($data == TRUE){
				$notification['title'] = 'Vendor - Create Driver';
				$notification['message'] = $user_data->first_name.' has been create driver';
				$notification['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->vendor_api->insertNotification($notification);
				
				$result = array( 'status'=> true , 'message'=> 'Driver Registered Successfully!. Waiting for admin approval process');
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
	
	public function taxi_post(){
		$data = array();
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		if ($this->form_validation->run() == true) {
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$parent_id = $user_data->id;
			
			$data = $this->vendor_api->getAllvendorwiseTaxi($parent_id);
			if($data){
				$result = array( 'status'=> true , 'message'=> 'Success', 'data' => $data);
			}else{
				$result = array( 'status'=> false , 'message'=> 'Empty taxis');
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
	
	public function taxidetail_post(){
		$data = array();
		$this->form_validation->set_rules('taxi_id', $this->lang->line("taxi"), 'required');
		if ($this->form_validation->run() == true) {
			$taxi_id = $this->input->post('taxi_id');
			
			
			$data = $this->vendor_api->getTaxiDetails($taxi_id);
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
	
	public function taxiadd_post(){
		
		$this->form_validation->set_rules('oauth_token', $this->lang->line("oauth_token"), 'required');
		$this->form_validation->set_rules('engine_number', $this->lang->line("engine_number"), 'required|is_unique[taxi.engine_number]');  
		$this->form_validation->set_rules('number', $this->lang->line("number"), 'required|is_unique[taxi.number]');  
		$this->form_validation->set_rules('chassis_number', $this->lang->line("chassis_number"), 'required|is_unique[taxi.chassis_number]');  
        
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('model', $this->lang->line("model"), 'required');
		$this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
		$this->form_validation->set_rules('fuel_type', $this->lang->line("fuel_type"), 'required');
		$this->form_validation->set_rules('color', $this->lang->line("color"), 'required');
		$this->form_validation->set_rules('manufacture_year', $this->lang->line("manufacture_year"), 'required');
		$this->form_validation->set_rules('capacity', $this->lang->line("capacity"), 'required');
		
		if ($this->form_validation->run() == true) {
				
			$user_data = $this->vendor_api->getVendor($this->input->post('oauth_token'));
			$this->site->users_logs($user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$user_id = $user_data->id;
			$group_id = $user_data->group_id;
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				   $is_daily = 1;
			   }else{
				   $is_daily = 0;
			   }
			$taxi = array(
				'name' => $this->input->post('name'),
				'model' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'multiple_type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				//'ac' => $this->input->post('ac'),
				'created_by' => $user_id,
				'user_id' => $user_id,
				'group_id' => $group_id,
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1,
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'is_hiring' => $this->input->post('is_hiring') ? $this->input->post('is_hiring') : 0,		
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
				$photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$photo;
				$config = NULL;
			}
			
			$taxi_document = array(
				'user_id' => $user_id,
				'group_id' => $group_id,
				'is_edit' => 1			
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
				
			$data = $this->vendor_api->add_taxi($taxi, $taxi_document);
			
			if($data == TRUE){
				$notification['title'] = 'Vendor - taxi Create';
				$notification['message'] = $user_data->first_name.' has been create taxi';
				$notification['user_type'] = 4;
				$notification['user_id'] = 2;
				$this->vendor_api->insertNotification($notification);
					
				$result = array( 'status'=> true , 'message'=> 'Taxi Added Successfully!. Waiting for admin approval process');
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
	
	
	
}
