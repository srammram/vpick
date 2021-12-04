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
		$this->load->library('socketemitter');
		
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
		$this->sma->checkPermissions('booking_rides_dashboard-index');
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
	
	function getAvailable(){
		
		$distance = 20;
		$radius = 3959;//6371;
		$countryCode = $this->input->get('is_country');
		$latitude = $this->input->get('pickupLat');
		$longitude = $this->input->get('pickupLng');
		$dlatitude = $this->input->get('dropLat');
		$dlongitude = $this->input->get('dropLng');
		$taxi_type = $this->input->get('cab_type_id');
		$val['distance'] = $distance; 
		$val['latitude'] = $latitude;
		$val['longitude'] = $longitude;
		
		$val['dlatitude'] = $dlatitude;
		$val['dlongitude'] = $dlongitude;
		$val['taxi_type'] = $taxi_type;
		
		$html = '';
		if(!empty($latitude) && !empty($longitude) && !empty($countryCode)){
			$search_data = $this->site->insertSearch($latitude, $longitude, $countryCode);
			$types = $this->booking_crm_model->getDriversnew_radius($val, $countryCode);
			
			
			if(!empty($types['result'])){
				
				//echo '<pre>';
				$html .='<ul class="col-lg-12 list-group">';
				//print_r($types);
				$i=0;
				foreach($types['result'] as $t){
					
					
					$html .= '<li class="row list-group-item marker-link" data-id="'.$i.'">';
					$html .= '<div class="col-lg-3"><img src="'.$t->driver_photo.'"  width="120px" height="120px"><h3>'.$t->taxi_name.'</h3></div>';
					$html .= '<div class="col-lg-9"><h3>'.$t->type_name.'</h3><p>'.$t->first_name.'</p><p>'.$t->mobile.'</p><p>Distance : '.$t->estimate_distance.'</p><p>Fare : '.$t->estimate_fare.'</p></div>';
					$html .= '</li>';
					$res[] = array('latitude' => $t->latitude, 'longitude' => $t->longitude);
					$i++;
				}
				$html .= '<li class="row list-group-item" >';
				if($this->Settings->cityride_max_kilometer > $types['distance']){
					$html .= '<input type="radio" name="booked_type" checked value="1">Cityride';
				}
				if($this->Settings->rental_max_kilometer > $types['distance']){
					$html .= '<input type="radio" name="booked_type" value="2">Rental';
				}
				if($this->Settings->outstation_min_kilometer < $types['distance']){
					$html .= '<input type="radio" name="booked_type" checked value="3">Outstation';
				}
				$html .= '</li>';
				$html .= '<li class="row list-group-item" >';
				$html .= '<input type="radio" name="ride_type" checked value="1">Ride Now';
				$html .= '<input type="radio" name="ride_type" value="2">Ride Later';
				$html .= '<li class="row list-group-item" >';
				
				$html .= '</li>';
				$html .= '</ul>';
				
				
			
			}else{
				$html .= '<div class="well">No Available Drivers</div>';
			}
		}else{
			$html .= '<div class="well">No Available Drivers</div>';
		}
		
		echo $html."###".json_encode($res);
	}
	
	public function book_ride(){
		$data = array();
		$countryCode = $this->input->post('is_country');
		$user_id = $this->input->post('user_id');
		
			$user_data = $this->site->getUser($this->input->post('user_id'), $countryCode);
			
			$this->site->users_logs($countryCode, $user_data->id, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
			$check_distance = $this->site->GetDrivingDistanceNew($this->input->post('pickupLat'), $this->input->post('pickupLng'), $this->input->post('dropLat'), $this->input->post('dropLng'), $unit = 'km', $decimals = 2,  $countryCode);
			
			if($this->input->post('booked_type') == 1){
				$booked_type_text = 'City Ride';
				$km_msg = 'cityride must cover maximum '.$this->Settings->cityride_max_kilometer.'kms';
				$check_km = $this->Settings->cityride_max_kilometer >= round($check_distance);
			}elseif($this->input->post('booked_type') == 2){
				$booked_type_text = 'Rental Ride';
				$km_msg = 'rental must cover maximum '.$this->Settings->rental_max_kilometer.'kms';
				$check_km = $this->Settings->rental_max_kilometer >= round($check_distance);
			}elseif($this->input->post('booked_type') == 3){
				$booked_type_text = 'Outstation Ride';
				$km_msg = 'outstation must cover minimum '.$this->Settings->outstation_min_kilometer.'kms';
				$check_km = $this->Settings->outstation_min_kilometer <= round($check_distance);
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
			$val['taxi_type'] = $this->input->post('cab_type_id');
			
			$val['latitude'] = $this->input->post('pickupLat');
			$val['longitude'] = $this->input->post('pickupLng');

			$val['booking_type'] = $this->input->post('booked_type');
			$val['distance'] = $distance; 
			
			if($this->input->post('ride_type') == 1){
				$driver_data = $this->booking_crm_model->getDrivers_radius($val, $countryCode);
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
						
						$insert = array(
							'customer_id' => $user_data->id,
							'driver_id' => 0,
							'process_type' => 1,
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
							'start' => $this->input->post('pickup'),
							'start_lat' => $this->input->post('pickupLat'),
							'start_lng' => $this->input->post('pickupLng'),
							'end' => $this->input->post('drop') ? $this->input->post('drop') : '0',
							'end_lat' => $this->input->post('dropLat') ? $this->input->post('dropLat') : '0',
							'end_lng' => $this->input->post('dropLng') ? $this->input->post('dropLng') : '0',
							'status' => $status,
							'ride_otp' => $ride_otp
						);    
						
						$bookingcrm = array(
							'ticket_code' => 'RIDE'.date('YmdHis'),
               				'ticket_date' => date('Y-m-d H:i:s'),
							'evalution_number' => $this->input->post('evalution_number'),
							'bookingcrm_status' => 1,
							'staff_id' => $this->session->userdata('user_id'),
							'customer_id' => $user_data->id,
							'customer_name' => $user_data->first_name,
							'customer_mobile' => $user_data->mobile,
							'customer_phonecode' => $user_data->country_code,
							'created_on' => date('Y-m-d H:i:s'),
							'created_by' => $this->session->userdata('user_id'),
							'is_country' => $countryCode
						);
						
						$bookingcrm_follow = array(
							'bookingcrm_staff_id' => $this->session->userdata('user_id'),
							'followup_date_time' => date('Y-m-d H:i:s'),
							'calltype' => 'Mobile',
							'bookingcrm_status' => 1,
							'discussion' => 'Ride Booking for '.$user_data->first_name,
							'is_edit' => 1,
							'created_on' => date('Y-m-d H:i:s'),
							'created_by' => $this->session->userdata('user_id'),
							'is_country' => $countryCode
						);
						
						$ride_insert[] = array(
							'location' => $this->input->post('pickup'),
							'latitude' => $this->input->post('pickupLat'),
							'longitude' => $this->input->post('pickupLng'),
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 1
						);
						
						$ride_insert[] = array(
							'location' => $this->input->post('drop'),
							'latitude' => $this->input->post('dropLat'),
							'longitude' => $this->input->post('dropLng'),
							'timing' => date('Y-m-d H:i:s'),
							'trip_made' => 7
						);
						
						$check['customer_id'] = $user_data->id;
						$check_status = $this->booking_crm_model->checkbookedcustomer($check, $countryCode);	
						
									
						if($check_status == TRUE){
								
							$data[] = $this->booking_crm_model->add_booking($bookingcrm, $bookingcrm_follow, $insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $user_data->id, $this->input->post('offer_code'));
							$payment_name = $this->booking_crm_model->getPaymentName($this->input->post('payment_id'), $countryCode);
							if($data[0] != 0){
								
								if($ride_type == 1){
									
									$notification['title'] = 'Ride Booking';
									$notification['message'] = $user_data->first_name.' has been ride booked.';
									$notification['user_type'] = 4;
									$notification['user_id'] = 2;
									$this->booking_crm_model->insertNotification($notification);
									
									$socket_id = $this->site->getSocketID($driver_data[0]->id, 2, $countryCode);
									$event = 'server_booking_checking';
									$edata = array(
										'booked_type_text' => $booked_type_text,
										'payment_id' => $this->input->post('payment_id'),
										'payment_name' => $payment_name,
										'distance_km' => $this->input->post('distance_km') ? $this->input->post('distance_km') : '0',
										'distance_price' => $this->input->post('distance_price') ? $this->input->post('distance_price') : '0',
										'customer_support' => '0987654321',
									
										'pick_up' => $this->input->post('pickup'),
										'from_latitude' => $this->input->post('pickupLat'),
										'from_longitude' => $this->input->post('pickupLng'),
										'drop_off' => $this->input->post('drop') ? $this->input->post('drop') : 'Location not given',
										'to_latitude' => $this->input->post('dropLat') ? $this->input->post('dropLat') : '0',
										'to_longitude' => $this->input->post('dropLng') ? $this->input->post('dropLng') : '0',
										'cab_type_id' => $driver_data[0]->type,
										'ride_id' => $data[0],
										'driver_id' => $driver_data[0]->id,
										'driver_oauth_token' => $driver_data[0]->oauth_token,
										'socket_id' => $socket_id
										
									);
									$success = 	$this->socketemitter->setEmit($event, $edata);
									//$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait', 'booking_id' => $data[0]);
									
									$ride_data = $this->booking_crm_model->getRideBYID($data[0], $countryCode);
									
									$sms_message = ' '.$user_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
									$sms_phone = $user_data->country_code.$user_data->mobile;
									$sms_country_code = $user_data->country_code;
				
									$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
									
									$this->session->set_flashdata('message', 'customer booking has been sent drivers. please wait');
									admin_redirect("booking_crm/waiting_driver/?user_id=".$user_id."&is_country=".$countryCode."&booking_id=".$data[0]);
								}else{
									
									$ride_data = $this->booking_crm_model->getRideBYID($data[0], $countryCode);
									
									$sms_message = ' '.$user_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
									$sms_phone = $user_data->country_code.$user_data->mobile;
									$sms_country_code = $user_data->country_code;
				
									$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
									
									$notification['title'] = 'Ride Booking - Ride Later';
									$notification['message'] = $user_data->first_name.', Your booking has been success. Booking No : '.$ride_data->booking_no.', Booking Time : '.$ride_data->booking_timing.', Booking Type : '.$booked_type_text;
									$notification['user_type'] = 4;
									$notification['user_id'] = 2;
									$this->booking_crm_model->insertNotification($notification, $countryCode);
									
									//$result = array( 'status'=> true , 'message'=> 'customer booking has been sent drivers. please wait');
									$this->session->set_flashdata('message', 'customer booking has been sent drivers. please wait');
									admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");
								}
								
							}else{
								//$result = array( 'status'=> false , 'message'=> 'Booking not added');
								$this->session->set_flashdata('error', 'Booking not added');
								admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");
							}
						}else{
							//$result = array( 'status'=> false , 'message'=> 'Customer already booked');
							$this->session->set_flashdata('error', 'Customer already booked');
							admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");
						}
					
				}else{
					
					$this->session->set_flashdata('error', 'Not accept your KM:'.$km_msg);
					admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");
				}
				
			}else{
				$this->session->set_flashdata('error', 'No Driver available');
				admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");
			}
			
		
	}
	
	function exituserRide(){
		
         $mobile = $this->input->post('mobile');
		 $phonecode = $this->input->post('phonecode');
         $data = $this->site->exituserRide($mobile, $phonecode);
       
        echo $data;exit;
    }
	
	function exitUser(){
		
         $mobile = $this->input->post('mobile');
		 $phonecode = $this->input->post('phonecode');
         $data = $this->site->exitUser($mobile, $phonecode);
       
        echo $data;exit;
    }
	
	function create_customer(){
		$this->sma->checkPermissions('booking_rides_dashboard-add');
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
			$this->form_validation->set_rules('mobile', lang("mobile"), 'required');
			
			if ($this->form_validation->run() == true) {
				$check = $this->booking_crm_model->userCheck($this->input->post('mobile'), $this->input->post('phonecode'), $countryCode);
				if($check != FALSE){
					 $this->session->set_flashdata('message', lang("exit_customer"));
					 admin_redirect('booking_crm/booking/?user_id='.$check->id.'&is_country='.$countryCode);
				}else{
					$oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
					$mobile_otp = random_string('numeric', 6);
					$refer_code = $this->site->refercode('C', $countryCode); 
					$users = array(
						'first_name' => $this->input->post('name'),
						'country_code' =>$this->input->post('phonecode'),
						'mobile' => $this->input->post('mobile'),
						'password' => md5('123456'),
						'text_password' => '123456',
						'created_on' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode,
						'oauth_token' => $oauth_token,
						'refer_code' => $refer_code,
						'mobile_otp' => $mobile_otp,
						'is_approved' => 1,
						'is_edit' => 1, 
						'active' => 1,
						'group_id' => 5
					);
				}
				
			   
			}elseif ($this->input->post('create_customer')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect("booking_crm");
			}
		
		
        if ($this->form_validation->run() == true && $customer_id = $this->booking_crm_model->create_customer($users, $countryCode)){
			
            $this->session->set_flashdata('message', lang("new_customer"));
            admin_redirect('booking_crm/booking/?user_id='.$customer_id.'&is_country='.$countryCode);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('booking_crm'), 'page' => lang('booking_crm')), array('link' => '#', 'page' => lang('create_customer')));
			
            $meta = array('page_title' => lang('create_customer'), 'bc' => $bc);
            $this->page_construct('booking_crm/create_customer', $meta, $this->data);
        }
	}
	
	
	function booking(){
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
		
		
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('booking_crm'), 'page' => lang('booking_crm')), array('link' => '#', 'page' => lang('booking')));
			$this->data['cabtypes'] = $this->booking_crm_model->getcabTypes($countryCode);
            $meta = array('page_title' => lang('booking'), 'bc' => $bc);
            $this->page_construct('booking_crm/booking', $meta, $this->data);
        
	}
	
	function waiting_driver(){
			if($this->session->userdata('group_id') == 1){
				if($this->input->get('is_country') != ''){
					$countryCode = $this->input->get('is_country');	
				}else{
					$countryCode = $this->input->post('is_country');	
				}	
			}else{
				$countryCode = $this->countryCode;	
			}
			$user_id = $this->input->get('user_id') ? $this->input->get('user_id') : $this->input->post('user_id');
			$booking_id = $this->input->get('booking_id') ? $this->input->get('booking_id') : $this->input->post('booking_id');
			
			if($this->input->post('timeout') == 1){
				$timeout_array = array(
					'cancelled_type' => 1,
					'cancelled_by' => $this->session->userdata('user_id'),
					'cancel_status' => 1,
					'cancel_msg' => 'Driver Not Available. Timeout closing rides',
					'cancel_on' => date('Y-m-d H:i:s'),
					'status' => 6
				);
				$timeout = $this->booking_crm_model->timeoutCustomer($timeout_array, $booking_id, $countryCode);
				if($timeout == TRUE){
					$this->session->set_flashdata('message', 'Timeout Close Ride. Try another rides.');
					admin_redirect("booking_crm/booking/?user_id=".$user_id."&is_country=".$countryCode."");	
				}else{
					$this->session->set_flashdata('error', 'Try Again Drivers');
					admin_redirect("booking_crm/waiting_driver/?user_id=".$user_id."&is_country=".$countryCode."&booking_id=".$booking_id);	
				}
			}elseif($this->input->post('timeout') == 2){
				$cancel_array = array(
					'cancelled_type' => 1,
					'cancelled_by' => $this->session->userdata('user_id'),
					'cancel_status' => 1,
					'cancel_msg' => $this->input->post('cancel_msg'),
					'cancel_on' => date('Y-m-d H:i:s'),
					'status' => 6
				);
				$rides = $this->booking_crm_model->getRideBYID($booking_id);
				$driver_id = $rides->driver_id; 
				$cancel = $this->booking_crm_model->cancelCustomer($cancel_array, $booking_id, $driver_id, $countryCode);
				
				if($rides->driver_id != 0){
				
					$customer_data = $this->site->getUser($user_id, $countryCode);
					$driver_data = $this->site->getUser($rides->driver_id, $countryCode);
					
					
					$customer_name = $customer_data->first_name;
					$driver_name = $driver_data->first_name;
					//$driver_phone = $driver_data->country_code.$driver_data->mobile;
					
					if($rides->driver_id != 2){
						$notification['title'] = 'Ride Cancel';
						$notification['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
						$notification['user_type'] = 2;
						$notification['user_id'] = $rides->driver_id;
						$this->booking_crm_model->insertNotification($notification);
					}
					$notification1['title'] = 'Ride Cancel';
					$notification1['message'] = 'Ride has been cancelled by customer('.$customer_name.'). cancel reason : '.$this->input->post('cancel_msg').'';
					$notification1['user_type'] = 4;
					$notification['user_id'] = 2;
					$this->booking_crm_model->insertNotification($notification1, $countryCode);
									
					$socket_id = $this->site->getSocketID($rides->driver_id, 2, $countryCode);
					$event = 'server_ride_cancel';
					$edata = array(
						'ride_id' => $booking_id,
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
			
				
				
				if($cancel == TRUE){
					$this->session->set_flashdata('message', 'Cancel Ride. Try another rides.');
					admin_redirect("booking_crm/");	
				}else{
					$this->session->set_flashdata('error', 'Try Again Drivers');
					admin_redirect("booking_crm/waiting_driver/?user_id=".$user_id."&is_country=".$countryCode."&booking_id=".$booking_id);	
				}
			}
			
			$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
			$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('booking_crm'), 'page' => lang('booking_crm')), array('link' => '#', 'page' => lang('waiting_driver')));
			$this->data['rides'] = $this->booking_crm_model->getRidesCustomer($booking_id, $countryCode);
            $meta = array('page_title' => lang('waiting_driver'), 'bc' => $bc);
            $this->page_construct('booking_crm/waiting_driver', $meta, $this->data);
        
	}
	
	function tracking(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$user_id = $this->input->get('user_id') ? $this->input->get('user_id') : $this->input->post('user_id');
		$booking_id = $this->input->get('booking_id') ? $this->input->get('booking_id') : $this->input->post('booking_id');
		$is_read = $this->input->get('is_read') ? $this->input->get('is_read') : 0;
		$booking_details = $this->booking_crm_model->getRidesBooking($booking_id, $countryCode);
		if($is_read == 1){
			
			$this->db->update('bookingcrm_notification', array('staff_id' => $this->session->userdata('user_id'), 'is_read' => 1, 'updated_on' => date('Y-m-d H:i:s')), array('bookingcrm_id' => $booking_details->id, 'ride_id' => $booking_id, 'is_country' => $countryCode));
			
		}
		
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
	
		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('booking_crm'), 'page' => lang('booking_crm')), array('link' => '#', 'page' => lang('ride_tracking')));
		$this->data['rides'] = $this->booking_crm_model->getRidesCustomer($booking_id, $countryCode);
		
		
		
		
		$this->data['booking_details'] = $booking_details;
		$this->data['follows_details'] = $this->booking_crm_model->getRidesFollow($booking_details->id, $booking_id, $countryCode);
		$this->data['id'] = $booking_id;
		$this->data['is_country'] = $countryCode;
		$this->data['booking_crm_id'] = $booking_details->id;
		
		$meta = array('page_title' => lang('ride_tracking'), 'bc' => $bc);
		$this->page_construct('booking_crm/tracking', $meta, $this->data);
        
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
	
	function ride_close(){
		$booking_crm_id = $this->input->post('booking_crm_id');	
		$discussion = $this->input->post('discussion');
		$remark = $this->input->post('remark');
		$is_country = $this->input->post('is_country');
		$booking_id = $this->input->post('booking_id');
		$data = $this->booking_crm_model->ride_close($booking_crm_id, $discussion, $remark, $this->session->userdata('user_id'), $is_country);
		if(!empty($data)){
			$this->session->set_flashdata('message', 'Ticket has been Closed...');
			admin_redirect("booking_crm/listview");
		}else{
			$this->session->set_flashdata('error', '');
			admin_redirect("booking_crm/tracking/?booking_id=".$booking_id."&is_country=".$is_country);
		}
	}
	
	function getBookings(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$status = $_GET['status'];
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('bookingcrm')}.id as id,  {$this->db->dbprefix('bookingcrm')}.ticket_code, {$this->db->dbprefix('bookingcrm')}.ticket_date, {$this->db->dbprefix('bookingcrm')}.evalution_number,  r.status as bookingcrm_status, {$this->db->dbprefix('bookingcrm')}.status as status, country.name as instance_country, {$this->db->dbprefix('bookingcrm')}.ride_id as ride_id, {$this->db->dbprefix('bookingcrm')}.is_country as is_country")
            ->from("bookingcrm")
			->join("countries country", " country.iso = bookingcrm.is_country", "left")
			->join("rides r", " r.id = bookingcrm.ride_id", "left");
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('bookingcrm')}.ticket_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('bookingcrm')}.ticket_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("bookingcrm.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("bookingcrm.is_country", $countryCode);
			}
			
			
           
			if($status != NULL){
				$this->datatables->where('status', $status);	
			}
			
			
			$edit = "<a href='" . admin_url('booking_crm/tracking/?booking_id=$2&is_country=$3') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id, ride_id, is_country");
			
			$this->datatables->unset_column('id');
			$this->datatables->unset_column('is_country');
			$this->datatables->unset_column('ride_id');
			
        echo $this->datatables->generate();	
	}
		
    

}
