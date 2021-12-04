<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;



class Ride extends REST_Controller {
	
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
	
	
	public function notificationtest_post(){
		
		$a = $this->site->traficWaitingTEST(2629, 1, 3, 10);
		print_r($a);
		/*$total_fare = 25.00;
		$admin_percentage = 20;
		$admin_tax_percentage = 7;
		$driver_tax_percentage = 18;
		$discount_percentage = 0;
		
		$discount_fare = number_format($total_fare * $discount_percentage / 100, 2);		
		$remain_fare = number_format($total_fare - $discount_fare, 2);
		
		
		
		
		$admin_fare = number_format($remain_fare * $admin_percentage / 100, 2);
		$admin_tax = $admin_tax_percentage;
		$admin_tax_fare = number_format($admin_fare * $admin_tax / 100, 2);
		$admin_total_fare = number_format($admin_fare + $admin_tax_fare, 2);
		$driver_fare = number_format($remain_fare - $admin_fare, 2);
		$driver_tax = $driver_tax_percentage;
		$driver_tax_fare = number_format($driver_fare * $driver_tax / 100, 2);
		$driver_total_fare = number_format($driver_fare + $driver_tax_fare, 2);
		$net_fare = number_format($admin_total_fare + $driver_total_fare, 2);
		
		$driver_balance_fare = number_format($discount_fare, 2);
		
		
		$o = array(
			'total_fare' => $total_fare,
			'discount_fare' => $discount_fare,
			'remain_fare' => $remain_fare,
			'admin_percentage' => $admin_percentage,
			'admin_fare' => $admin_fare,
			'admin_tax_percentage' => $admin_tax,
			'admin_tax_fare' => $admin_tax_fare,
			'admin_total_fare' => $admin_total_fare,
			'driver_fare' => $driver_fare,
			'driver_tax_percentage' => $driver_tax,
			'driver_tax_fare' => $driver_tax_fare,
			'driver_total_fare' => $driver_total_fare,
			'net_fare' => $net_fare,
			'driver_balance_fare' => $driver_balance_fare,
			
		);
		print_r($o);*/
		
		
		/*echo $admin_fare = number_format($total_fare * $admin_percentage / 100, 2);
		echo '<pre>';
		$admin_tax = $admin_tax_percentage;
		echo $admin_tax_fare = number_format($admin_fare * $admin_tax / 100, 2);
		echo '<pre>';
		$admin_total_fare = number_format($admin_fare + $admin_tax_fare, 2);
		
		echo $driver_fare = number_format($total_fare - $admin_fare, 2);
		echo '<pre>';
		$driver_tax = $driver_tax_percentage;
		echo $driver_tax_fare = number_format($driver_fare * $driver_tax / 100, 2);
		$driver_total_fare = number_format($driver_fare + $driver_tax_fare, 2);
		echo '<pre>';
		echo $net_fare = number_format($admin_total_fare + $driver_total_fare, 2);
		echo '<pre>';
		echo $discount_fare = number_format($net_fare * $discount_percentage / 100, 2);
		echo '<pre>';
		
		
		
		echo $final_fare = number_format($net_fare - $discount_fare, 2);
		echo '@@@';
		if($discount_percentage > $admin_percentage){
			echo $discount_percentage;
			echo '@@@';
			echo ($final_fare - $driver_fare);
		}else{
			echo $admin_percentage;
		}*/
	}
	
	public function acknowledegement_post(){
		$user_id = $this->input->post('userID');
		$user_type_id = $this->input->post('userType');
		$emit_name = $this->input->post('emitName');
		$res = $this->site->acknowledegementUpdate($user_id, $user_type_id, $emit_name);
		if($res == 1){
			return true;	
		}
		return false;
		
	}
	
	public function distance_post(){
		$distance_km = 7;
			$distance_array = $this->site->getFareestimate(11.603623, 79.485902, 56, 1, 'IN');
			
			if($distance_km > $distance_array['min_distance']){
				
				$distance_price = round((($distance_km - $distance_array['min_distance']) * $distance_array['per_distance_price']) + $distance_array['min_distance_price']);
			}else{
				$distance_price = round($distance_array['min_distance_price']);
			}
			
			echo $distance = $distance_price ? (string)$distance_price : '0.00';
	}
	
	public function driversocket_disconnect_post(){
		
		$res = $this->site->bookingEmitDriverpending($this->input->post('driver_id'));
		if(!empty($res)){
			$countryCode = $res->is_country;
			$settings = $this->drivers_api->getSettings($countryCode);
			$customer_id = $res->customer_id;
			$ride_id = $res->ride_id;
			$cab_type_id = $res->cab_type_id;
			$driver_id = $this->input->post('driver_id');
			$from_latitude =  $res->start_lat;
			$from_longitude =  $res->start_lng;
			$distance = 20;
			$radius = 3959;//6371;
			$val['taxi_type'] = $cab_type_id;
			$val['latitude'] = $from_latitude;
			$val['longitude'] = $from_longitude;
			$val['distance'] = $distance; 
			$val['ride_id'] = $ride_id;
			
			$update_driver = array(
				'driver_id' => $driver_id,
				'ride_id' => $ride_id,
				'status' => 0,
			);
			
			$ride_data = $this->drivers_api->getRideID($ride_id, $countryCode);
			$payment_id = $ride_data->payment_id;
			
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
				$this->site->bookingEmitDriverupdate($ride_id, $driver_id, $customer_id);
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
							'distance_km' => $ride_data->distance_km,
							'distance_price' => $ride_data->distance_price,
							'customer_support' => '0987654321',
							'pick_up' => $ride_data->start,
							'drop_off' => $ride_data->end,
							'from_latitude' => $ride_data->start_lat,
							'from_longitude' => $ride_data->start_lng,
							'to_latitude' => $ride_data->end_lat,
							'to_longitude' => $ride_data->end_lng,
							'cab_type_id' => $driver_data[0]->type,
							'ride_id' => $ride_id,
							'driver_id' => $driver_data[0]->id,
							'driver_oauth_token' => $driver_data[0]->oauth_token,
							'socket_id' => $socket_id,
							'driver_time' => $settings->driver_time
							
						);	
						
					$success = 	$this->socketemitter->setEmit($event, $edata);
					
					}else{
						
						$cancel_location = $this->site->findLocation($ride_data->from_latitude, $ride_data->to_longitude, $countryCode);
						$cancel['driver_id'] = $driver_id;
						$cancel['booking_id'] = $ride_id;
						$cancel['cancel_msg'] = 'Any one of the driver not accept ride. Please try again.';
						$cancel['cancel_location'] = $cancel_location ? $cancel_location : '';
						$res = $this->drivers_api->nodriverCancel($cancel, $countryCode);
						if($res == TRUE){
						$event = 'server_not_accept_driver';
						$socket_id = $this->site->getSocketID($customer_id, 1, $countryCode);
						$edata = array(
							'id' => 2,
							'msg' => 'Driver has been not allocated, please try again another ride',
							'pick_up' => $ride_data->start,
							'drop_off' => $ride_data->end,
							'from_latitude' => $ride_data->start_lat,
							'from_longitude' => $ride_data->start_lng,
							'to_latitude' => $ride_data->end_lat,
							'to_longitude' => $ride_data->end_lng,
							'driver_latitude' => '0',
							'driver_longitude' => '0',
							'customer_id' => $customer_id,
							'cab_type_id' => $cab_type_id,
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
							'socket_id' => $socket_id
						);
						$success = 	$this->socketemitter->setEmit($event, $edata);	
						}
					}
				}
				$result = array( 'status'=> 1 , 'message'=> 'not accept or timeout driver has been insert');
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not accept or timeout driver has been not insert');
			}
			
		}else{
			$result = array( 'status'=> 0);
		}
		
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
						'customer_photo' => $data[0]->customer_photo ? $data[0]->customer_photo : '0'
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
									'socket_id' => $socket_id
									
								);
								
								$success = 	$this->socketemitter->setEmit($event, $edata);
								
								
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
								'driver_time' => $settings->driver_time
								
							);	
							
						$success = 	$this->socketemitter->setEmit($event, $edata);
						
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
								'socket_id' => $socket_id
							);
							$success = 	$this->socketemitter->setEmit($event, $edata);	
							}
						}
					}
					$result = array( 'status'=> 1 , 'message'=> 'not accept or timeout driver has been insert');
					
				}else{
					$result = array( 'status'=> 0 , 'message'=> 'not accept or timeout driver has been not insert');
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
	
	function test_post(){
		
		$res = $this->site->bookingEmitDriverpending($this->input->post('driver_id'));

		
		if(!empty($res)){
			$countryCode = $res->is_country;
			$settings = $this->drivers_api->getSettings($countryCode);
			$customer_id = $res->customer_id;
			$ride_id = $res->ride_id;
			$cab_type_id = $res->cab_type_id;
			$driver_id = $this->input->post('driver_id');
			$from_latitude =  $res->start_lat;
			$from_longitude =  $res->start_lng;
			$distance = 20;
			$radius = 3959;//6371;
			$val['taxi_type'] = $cab_type_id;
			$val['latitude'] = $from_latitude;
			$val['longitude'] = $from_longitude;
			$val['distance'] = $distance; 
			
			$val['ride_id'] = $ride_id;
			
			$update_driver = array(
				'driver_id' => $driver_id,
				'ride_id' => $ride_id,
				'status' => 6,
			);
			
			$ride_data = $this->drivers_api->getRideID($ride_id, $countryCode);
			$payment_id = $ride_data->payment_id;
			$this->site->bookingEmitDriverupdate($ride_id, $driver_id, $customer_id);
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
							'distance_km' => $ride_data->distance_km,
							'distance_price' => $ride_data->distance_price,
							'customer_support' => '0987654321',
							'pick_up' => $ride_data->pick_up,
							'drop_off' => $ride_data->drop_off,
							'from_latitude' => $ride_data->from_latitude,
							'from_longitude' => $ride_data->from_longitude,
							'to_latitude' => $ride_data->to_latitude,
							'to_longitude' => $ride_data->to_longitude,
							'cab_type_id' => $driver_data[0]->type,
							'ride_id' => $ride_id,
							'driver_id' => $driver_data[0]->id,
							'driver_oauth_token' => $driver_data[0]->oauth_token,
							'socket_id' => $socket_id,
							'driver_time' => $settings->driver_time
							
						);	
						
					$success = 	$this->socketemitter->setEmit($event, $edata);
					
					}else{
						
						$cancel_location = $this->site->findLocation($ride_data->from_latitude, $ride_data->to_longitude, $countryCode);
						$cancel['driver_id'] = $driver_id;
						$cancel['booking_id'] = $ride_id;
						$cancel['cancel_msg'] = 'Any one of the driver not accept ride. Please try again.';
						$cancel['cancel_location'] = $cancel_location ? $cancel_location : '';
						$res = $this->drivers_api->nodriverCancel($cancel, $countryCode);
						if($res == TRUE){
						$event = 'server_not_accept_driver';
						$socket_id = $this->site->getSocketID($customer_id, 1, $countryCode);
						$edata = array(
							'id' => 2,
							'msg' => 'Driver has been not allocated, please try again another ride',
							'pick_up' => $ride_data->pick_up,
							'drop_off' => $ride_data->drop_off,
							'from_latitude' => $ride_data->from_latitude,
							'from_longitude' => $ride_data->from_longitude,
							'to_latitude' => $ride_data->to_latitude,
							'to_longitude' => $ride_data->to_longitude,
							'driver_latitude' => '0',
							'driver_longitude' => '0',
							'customer_id' => $customer_id,
							'cab_type_id' => $cab_type_id,
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
							'socket_id' => $socket_id
						);
						$success = 	$this->socketemitter->setEmit($event, $edata);	
						}
					}
				}
				$result = array( 'status'=> 1 , 'message'=> 'not accept or timeout driver has been insert');
				
			}else{
				$result = array( 'status'=> 0 , 'message'=> 'not accept or timeout driver has been not insert');
			}
			
		}else{
			$result = array( 'status'=> 0);
		}
		echo json_encode($result);
		/*$total_fare = 100;
		$countryCode = 'IN';
		$setting = $this->site->get_setting($countryCode);
		$driver_percentage = $this->site->getDefaultTaxDriver($countryCode);
		$admin_percentage = $this->site->getDefaultTaxAdmin($countryCode);
		if($driver_percentage != 0){
			$driver_tax_name = $driver_percentage->tax_name;
			$driver_tax_percentage = $driver_percentage->percentage;
		}else{
			$driver_tax_name = '0';
			$driver_tax_percentage = '0';
		}
		
		if($admin_percentage != 0){
			$admin_tax_name = $admin_percentage->tax_name;
			$admin_tax_percentage = $admin_percentage->percentage;
		}else{
			$admin_tax_name = '0';
			$admin_tax_percentage = '0';
		}
		
		$admin_percentage = $setting->driver_admin_payment_percentage;
		$admin_fare = $total_fare * $admin_percentage / 100;
		$admin_tax = $admin_tax_percentage;
		$admin_tax_fare = $admin_fare * $admin_tax / 100;
		$admin_total_fare = $admin_fare + $admin_tax_fare;
		
		$driver_fare = $total_fare - $admin_fare;
		$driver_tax = $driver_tax_percentage;
		$driver_tax_fare = $driver_fare * $driver_tax / 100;
		$driver_total_fare = $driver_fare + $driver_tax_fare;
		$ride_final_fare = $admin_total_fare + $driver_total_fare;
		
		$payment = array(
					'created_on' => date('Y-m-d H:i:s'),
					
					'admin_percentage' => $admin_percentage,
					'admin_fare' => $admin_fare,
					'admin_tax' => $admin_tax,
					'admin_tax_fare' => $admin_tax_fare,
					'admin_total_fare' => $admin_total_fare,
					'driver_fare' => $driver_fare,
					'driver_tax' => $driver_tax,
					'driver_tax_fare' => $driver_tax_fare,
					'driver_total_fare' => $driver_total_fare,
					'ride_final_fare' => $ride_final_fare,
					
					
					);
			print_r($payment);*/
		
	}
	
	function getFare($customer_id, $ride_type, $outstation_type, $outstation_way, $taxi_type, $start_lat, $start_lng, $end_lat, $end_lng, $start_time, $end_time, $estimate_distance, $actual_distance, $total_distance, $waiting_time, $countryCode){
		$data = array();
		//$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng);
		//$end_pincode = $this->findLocationPINCODE1($end_lat, $end_lng);
		
		
		
		
		if($ride_type == 1){
			
			
				
				
				
				$daily_withoutcity = $this->db->select('*')->where('area_id', 0)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withoutcity->num_rows() > 0) {
					
					
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
						$slot_waiting_price = 0;
					
					
					$fare_waiting = $daily_withoutcity->row('base_waiting_minute');
					$waiting_fare = $daily_withoutcity->row('base_waiting_price');
					
					
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					
					
					$base_min_distance = $daily_withoutcity->row('base_min_distance');
					$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					
					echo $total_distance;
					echo $base_min_distance;
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						if($slot_type != 'empty'){
							$estimate_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
						}else{
							$estimate_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => 0,
						'extra_fare_details' => 0,
					);
					return $fare;	
				}
						
						
		}
		
		$fare = array(
			$estimate_distance => 0,
			$estimate_fare => 0,
			$actual_distance => 0,
			$actual_fare => 0,
			$total_distance => 0,
			$total_fare => 0,
			$round_fare => 0,
			$extra_fare => 0,
			$extra_fare_details => 0,
		);
		return $fare;
	}
	
	function ridecurl_get(){
		
		$data =  json_decode($_GET['data'], true);
		$booking_id = $data['booking_id'];
		$total_fare = $data['total_fare'];
		$driver_id = $data['driver_id'];
		$countryCode = $data['countryCode'];
		$admin_total_fare = $data['admin_total_fare'];
		
		//$driver_total_amount = round($total_fare);
		$driver_total_amount = round($data['driver_fare']);
		
		$incentive = $this->db->select('inc.fare_type, inc.fare_amount, indr.incentive_type as type, indr.target_fare, indr.target_ride, indr.complete_fare, indr.complete_ride')->from('incentive_driver indr')->join('incentive inc', 'inc.id = indr.incentive_id', 'left')->where('indr.cancel_status', 0)->where('indr.is_edit', 1)->where('indr.is_country', $countryCode)->where('indr.status', 0)->where('indr.driver_id', $driver_id)->get();
		
		if($incentive->num_rows()>0){
			$incentive_row = $incentive->row();
			$complete_fare = $incentive_row->complete_fare + $driver_total_amount;
			$complete_ride = $incentive_row->complete_ride + 1;
			if($incentive_row->fare_type == 1){
				$incentive_amount = $incentive_row->fare_amount;
			}else{
				$incentive_amount = $incentive_row->fare_amount;
			}


			if($incentive_row->type == 1){
				 $incentive_row->complete_percentage = round((($complete_fare / $incentive_row->target_fare ) * 100));
				 
				 if($incentive_row->complete_percentage >= 100){
					$this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					
											$this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1,  'cash' => round($incentive_amount), 'description' => 'Incentive added', 'wallet_type' => 2, 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'is_country' => $countryCode));
					
				 }else{
					 
					 
					 $this->db->update('incentive_driver', array('complete_fare' => $complete_fare), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
				 
			 }
			 elseif($incentive_row->type == 2){
				 $incentive_row->complete_percentage = round( (($complete_ride / $incentive_row->target_ride ) * 100));
				 
				 
				 if($incentive_row->complete_percentage >= 100){
					
					 $this->db->update('incentive_driver', array('complete_ride' => $complete_ride, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					 
					 $this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1, 'cash' => round($incentive_amount), 'description' => 'Incentive added', 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'wallet_type' => 2, 'is_country' => $countryCode));
					 
				 }else{
					 
											
					 $this->db->update('incentive_driver', array('complete_ride' => $complete_ride), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
				 
				
			 }
			 elseif($incentive_row->type == 3){
				 $incentive_row->complete_percentage = round((($complete_fare / $incentive_row->target_fare ) * 50) + (($complete_ride / $incentive_row->target_ride ) * 50));
				 if($incentive_row->complete_percentage >= 100){
					
					 $this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'complete_ride' => $complete_ride, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					 
					 $this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1,  'cash' => round($incentive_amount), 'description' => 'Incentive added', 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'wallet_type' => 2, 'is_country' => $countryCode));
					 
				 }else{
					 
										
					 //$this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'complete_ride' => $complete_ride), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
			 }
		}
		
		
		
		return true;
		
	}
	
	
	function ridecurldata_get(){
		
		$data =  json_decode($_GET['data'], true);
		$booking_id = $data['booking_id'];
		$total_fare = $data['total_fare'];
		$driver_id = $data['driver_id'];
		$countryCode = $data['countryCode'];
		$admin_total_fare = $data['admin_total_fare'];
		
		//$driver_total_amount = round($total_fare);
		$driver_total_amount = round($data['driver_fare']);
		
		$incentive = $this->db->select('inc.fare_type, inc.fare_amount, indr.incentive_type as type, indr.target_fare, indr.target_ride, indr.complete_fare, indr.complete_ride')->from('incentive_driver indr')->join('incentive inc', 'inc.id = indr.incentive_id', 'left')->where('indr.cancel_status', 0)->where('indr.is_edit', 1)->where('indr.is_country', $countryCode)->where('indr.status', 0)->where('indr.driver_id', $driver_id)->get();
		
		if($incentive->num_rows()>0){
			$incentive_row = $incentive->row();
			$complete_fare = $incentive_row->complete_fare + $driver_total_amount;
			$complete_ride = $incentive_row->complete_ride + 1;
			if($incentive_row->fare_type == 1){
				$incentive_amount = $incentive_row->fare_amount;
			}else{
				$incentive_amount = $incentive_row->fare_amount;
			}


			if($incentive_row->type == 1){
				 $incentive_row->complete_percentage = round((($complete_fare / $incentive_row->target_fare ) * 100));
				 
				 if($incentive_row->complete_percentage >= 100){
					$this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					
											$this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1,  'cash' => round($incentive_amount), 'description' => 'Incentive added', 'wallet_type' => 2, 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'is_country' => $countryCode));
					
				 }else{
					 
					 
					 $this->db->update('incentive_driver', array('complete_fare' => $complete_fare), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
				 
			 }
			 elseif($incentive_row->type == 2){
				 $incentive_row->complete_percentage = round( (($complete_ride / $incentive_row->target_ride ) * 100));
				 
				 
				 if($incentive_row->complete_percentage >= 100){
					
					 $this->db->update('incentive_driver', array('complete_ride' => $complete_ride, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					 
					 $this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1, 'cash' => round($incentive_amount), 'description' => 'Incentive added', 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'wallet_type' => 2, 'is_country' => $countryCode));
					 
				 }else{
					 
											
					 $this->db->update('incentive_driver', array('complete_ride' => $complete_ride), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
				 
				
			 }
			 elseif($incentive_row->type == 3){
				 $incentive_row->complete_percentage = round((($complete_fare / $incentive_row->target_fare ) * 50) + (($complete_ride / $incentive_row->target_ride ) * 50));
				 if($incentive_row->complete_percentage >= 100){
					
					 $this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'complete_ride' => $complete_ride, 'status' => 1, 'is_edit' => 0, 'complete_date' => date('Y-m-d H:i:s')), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
					 
					 $this->db->insert('wallet', array('user_id' => $driver_id, 'flag' => 1, 'flag_method' => 1,  'cash' => round($incentive_amount), 'description' => 'Incentive added', 'created' => date('Y-m-d H:i:s'), 'user_type' => 2, 'wallet_type' => 2, 'is_country' => $countryCode));
					 
				 }else{
					 
										
					 //$this->db->update('incentive_driver', array('complete_fare' => $complete_fare, 'complete_ride' => $complete_ride), array('driver_id' => $driver_id, 'is_edit' => 1, 'cancel_status' => 0, 'status' => 0, 'is_country' => $countryCode));
				 }
			 }
		}
		
		$setting_account = $this->db->select('*')->where('is_country', $countryCode)->get('settings');
		//print_r($setting_account->row());
		if($setting_account->row('driver_default_set_payment') == 0){
			
			/*$admin_user = $this->site->getAdminUser($countryCode, 2);
			$company_id = $this->site->getUserCompany($countryCode, 0);
			$company_bank_id = $this->site->offlineBank($countryCode, $company_id);
			$transaction_no = 'TRANS'.date('YmdHis');
			$transaction_date = date('Y-m-d H:i:s');
			$created_by = $driver_id;
			
			
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'flag' => 5,
				'cash' => $admin_total_fare,
				'description' => 'Ride - commision amount transfer to admin',
				'created' => $transaction_date,
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'flag' => 9,
				'cash' => $admin_total_fare,
				'description' => 'Ride - admin recived commision amount',
				'created' => $transaction_date,
				'is_country' => $countryCode
			);
			
			$this->site->walletRide($wallet_array);*/
				
		}
		else{
			$dp = $this->db->select('id as driver_payment_id, driver_id, is_edit, total_ride, total_ride_amount, ride_start_date, ride_end_date, payment_duration, payment_percentage, payment_amount')->from('driver_payment')->where('is_country', $countryCode)->where('is_edit ', 1)->where('driver_id', $driver_id)->order_by('id', 'DESC')->limit(1)->get();
			if($dp->num_rows()>0){
				
					$this->db->insert('driver_payment_rides', array('driver_payment_id' => $dp->row('driver_payment_id'), 'driver_id' => $driver_id, 'ride_id' => $booking_id, 'ride_amount' => $driver_total_amount, 'is_country' => $countryCode));
					
					$total_ride = $dp->row('total_ride') + 1;
					$total_ride_amount = $dp->row('total_ride_amount') + $driver_total_amount;
					$this->db->update('driver_payment', array('total_ride' => $total_ride, 'total_ride_amount' => $total_ride_amount), array('driver_id' => $driver_id, 'id' => $dp->row('driver_payment_id'), 'is_edit' => 1, 'is_country' => $countryCode));
				
				
			}else{
				
				$dp1 = $this->db->select('driver_id, is_edit, total_ride, total_ride_amount, ride_start_date, ride_end_date, payment_duration, payment_percentage, payment_amount')->from('driver_payment')->where('is_edit ', 2)->where('is_country', $countryCode)->where('driver_id', $driver_id)->order_by('id', 'DESC')->limit(1)->get();
				
				if($dp->num_rows()>0){
					if($setting_account->row('driver_admin_payment_option') == 1){
						$end_date = date('Y-m-d', strtotime("+1 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 1;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}elseif($setting_account->row('driver_admin_payment_option') == 2){
						$end_date = date('Y-m-d', strtotime("+7 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 7;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}elseif($setting_account->row('driver_admin_payment_option') == 3){
						$end_date = date('Y-m-d', strtotime("+30 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 30;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}
					
					$driverpay = $this->db->insert('driver_payment', array('driver_id' => $driver_id, 'total_ride' => 1, 'total_ride_amount' => $driver_total_amount, 'ride_start_date' => date('Y-m-d'), 'ride_end_date' => $end_date, 'duration_date' => $duration_date, 'payment_duration' => $setting_account->row('driver_admin_payment_duration'), 'payment_percentage' => $setting_account->row('driver_admin_payment_percentage'), 'is_edit' => 1, 'is_country' => $countryCode, 'created_on' => date('Y-m-d H:i:s')));	
					
					if($driverpay){
						$driver_payment_id = $this->db->insert_id();
						$this->db->insert('driver_payment_rides', array('driver_payment_id' => $driver_payment_id, 'driver_id' => $driver_id, 'ride_id' => $booking_id, 'ride_amount' => $driver_total_amount, 'is_country' => $countryCode));
					}
					
				}else{
					if($setting_account->row('driver_admin_payment_option') == 1){
						$end_date = date('Y-m-d', strtotime("+1 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 1;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}elseif($setting_account->row('driver_admin_payment_option') == 2){
						$end_date = date('Y-m-d', strtotime("+7 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 7;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}elseif($setting_account->row('driver_admin_payment_option') == 3){
						$end_date = date('Y-m-d', strtotime("+30 days"));
						$duration = $setting_account->row('driver_admin_payment_duration') + 30;
						$duration_date = date('Y-m-d', strtotime("+".$duration." days"));
					}
					
					$driverpay1 = $this->db->insert('driver_payment', array('driver_id' => $driver_id, 'total_ride' => 1, 'total_ride_amount' => $driver_total_amount, 'ride_start_date' => date('Y-m-d'), 'ride_end_date' => $end_date, 'duration_date' => $duration_date, 'payment_duration' => $setting_account->row('driver_admin_payment_duration'), 'payment_percentage' => $setting_account->row('driver_admin_payment_percentage'), 'is_edit' => 1, 'is_country' => $countryCode, 'created_on' => date('Y-m-d H:i:s')));	
					if($driverpay1){
						$driver_payment_id = $this->db->insert_id();
						$this->db->insert('driver_payment_rides', array('driver_payment_id' => $driver_payment_id, 'driver_id' => $driver_id, 'ride_id' => $booking_id, 'ride_amount' => $driver_total_amount, 'is_country' => $countryCode));
					}
					
				}
			}
		}
		
		return true;
		
	}
	
	
	
	
}
