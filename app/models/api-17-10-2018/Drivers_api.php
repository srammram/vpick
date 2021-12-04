<?php defined('BASEPATH') OR exit('No direct script access allowed');

class drivers_api extends CI_Model
{
	public $tables = array();
	protected $_ion_hooks;
	var $limit;
    public function __construct() {
        parent::__construct();
    	$this->load->config('ion_auth', TRUE);
	$this->limit = 10;
    }
	
	public function deviceGET($user_id, $user_type){
		$this->db->select('devices.*');
		$this->db->where('devices.user_id', $user_id);
		$this->db->where('devices.user_type', $user_type);
		$q = $this->db->get('devices');
		if ($q->num_rows() > 0) {
			$data = $q->row('device_token');
			return $data;
		}
		return FALSE;
	}
	
	function paymentPaid($data){
		$q = $this->db->insert('payment', array('ride_id' => $data['booking_id'], 'cost' => $data['amount'], 'payment_mode' => $data['payment_mode'], 'status' => 'Paid', 'amount_paid' => $data['amount_paid'], 'balance_paid' => $data['balance_paid'], 'total_kms' => $data['total_kms']));	
		if($q){
			return true;
		}
		return false;
	}
	
	function getSettings(){
		$q = $this->db->select('*')->where('setting_id', 1)->get('settings');
		if($q->num_rows() > 0){
			return $q->row();	
		}
		return false;
	}
	
	function getDrivers_radius($data){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$taxi_path = base_url('assets/uploads/taxi/');
	$this->db
	    ->select("d.id,d.created_on date_created,d.first_name driver_name,d.photo,d.current_latitude latitude,d.current_longitude longitude,d.mode, dcs.taxi_id, t.name, t.number, t.type, tt.name type_name, tt.mapcar type_image ")
            ->from("drivers d");
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = d.id', 'left');
		$this->db->join('taxi t', 't.id = dcs.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
	    $this->db->where('d.current_latitude BETWEEN "'. $data['minlat']. '" and "'. $data['maxlat'].'"');
	    $this->db->where('d.current_longitude BETWEEN "'. $data['minlng']. '" and "'. $data['maxlng'].'"');
		if(!empty($data['taxi_type'])){
		$this->db->where('tt.id', $data['taxi_type']);
		}
		$this->db->where('d.mode', 'available');
		$this->db->group_by('d.id');
	$q=$this->db->get();
	if($q->num_rows()>0){
		
		$b = $this->db->select('driver_id')->where('ride_id', $data['ride_id'])->group_by('driver_id')->get('driver_booking');
		if($b->num_rows() > 0){
			foreach($b->result() as $kow){
				$driver_id[] = $kow->driver_id;
			}
		}
		if(!empty($driver_id)){
			
			foreach($q->result() as $k => $row){
				if (!in_array($row->id, $driver_id)) 
				  { 
					 $result[$k] = $row;
				  }else{
					  $result[$k] = '';
				  }
			}
		}else{
			 $result[$k] = $row;
		}
		
	    return $result;
	}
	return false;
    }
	
	function insertNotification($data){
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s') ));
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	function driverUpdateStatus($data){
		
		$this->db->where('id', $data['driver_id']);
		$q = $this->db->update('drivers', array('mode' => $data['mode'], 'updated_on' => date('Y-m-d H:i:s')));
		if($q){
			return true;
		}
		return false;	
	}
	function fcminsert($data){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->update('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}else{
			$this->db->insert('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'created_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	function fcmdelete($data){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->update('devices', array('user_id' => 0, 'user_type' => 0, 'devices_type' => 0, 'device_imei' => '', 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	function add_driver($data){
		$this->db->insert('drivers', $data);//print_R($this->db->error());
		return $this->db->insert_id();	
	}
	
	function currentlocationdriver($data){
		$this->db->where('id', $data['driver_id']);
		$q = $this->db->update('drivers', array('mode' => $data['mode'], 'current_latitude' => $data['latitude'], 'current_longitude' => $data['longitude']));
		if($q){
			return true;
		}
		return false;
	}
	
	function frequencylocationdriver($data){
		
		$q = $this->db->insert('driver_current_status', array('mode' => 'on ride', 'driver_id' => $data['driver_id'], 'taxi_id' => $data['taxi_id'], 'current_latitude' => $data['latitude'], 'current_longitude' => $data['longitude']));
		if($q){
			return true;
		}
		return false;
	}
	
	function getDriverID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('drivers');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getTaxitypeID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('taxi_type');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getTaxiID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('taxi');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getCustomerID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('customers');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getRideID($id){
		$this->db->select('*');
		$this->db->where('cancel_status', 0);
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function check_login($data){
		$image_path = base_url('assets/uploads/drivers/photo/');
		$query = "select * from {$this->db->dbprefix('drivers')} where password='".$data['password']."'  AND mobile='".$data['mobile']."'  ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
		    $row = $q->row();
			if($row->photo !=''){
				$row->driver_photo = $image_path.$row->photo;
			}else{
				$row->driver_photo = $image_path.'default.png';
			}
			
		    $data =  $row;
			return $data;
		}
		return false;
	}
    
	function checkotp($data){
		$query = "select * from {$this->db->dbprefix('drivers')} where id='".$data['driver_id']."' AND  mobile_otp='".$data['otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $data['driver_id']);
			$this->db->update('drivers', array('status', 1));
			return true;
		}
		return false;
	}
	
	function resendotp($data){
		$query = "select * from {$this->db->dbprefix('drivers')} where id='".$data['driver_id']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $data['driver_id']);
			$this->db->update('drivers', array('mobile_otp' =>  $data['mobile_otp']));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgototp($data){
		$query = "select * from {$this->db->dbprefix('drivers')} where mobile='".$data['mobile']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->update('drivers', array('forgot_otp' => $data['forgot_otp'], 'forgot_active' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgotcheckotp($data){
		$query = "select * from {$this->db->dbprefix('drivers')} where id='".$data['driver_id']."' AND  forgot_otp='".$data['forgot_otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			return true;
		}
		return false;
	}
	
	function forgotresendotp($data){
		$query = "select * from {$this->db->dbprefix('drivers')} where id='".$data['driver_id']."' AND forgot_otp = '".$data['forgot_otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->update('drivers', array('forgot_otp' => $data['forgot_otp'], 'forgot_active' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	
	function updatepassword($data){
		
		$this->db->where('id', $data['driver_id']);
		$q = $this->db->update('drivers', array('password' => $data['password'], 'forgot_active' => 1));
		if($q){
			return true;	
		}
		return false;
	}
	
	function getDriver($oauth_token){
		$this->db->select('*');
		$this->db->where('oauth_token', $oauth_token);
		$q = $this->db->get('drivers');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function startride($data){
		$q = $this->db->select('*')->where('driver_id', $data['driver_id'])->where('ride_otp', $data['ride_otp'])->get('rides');
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->update('rides', array('ride_start_time' => date('Y-m-d H:i:s'),  'dropoff_lat' => $data['dropoff_lat'], 'dropoff_lng' => $data['dropoff_lng'], 'status' => 'onride'));
			if(!empty($data['driver_id'])){
				$this->db->update('drivers', array('mode' => 'on ride'), array('id' => $data['driver_id']));	
			}
			return $q->row();	
		}
		return false;	
	}
	function  completeride($data){
		$this->db->where('driver_id', $data['driver_id']);
		$this->db->where('id', $data['booking_id']);
		$q = $this->db->update('rides', array('ride_end_time' => date('Y-m-d H:i:s'), 'dropoff_lat' => $data['dropoff_lat'], 'dropoff_lng' => $data['dropoff_lng'], 'actual_loc' => $data['actual_loc'], 'actual_lat' => $data['actual_lat'], 'actual_lng' => $data['actual_lng'], 'status' => 'completed'));
		if($q){
			if(!empty($data['driver_id'])){
				$d = $this->db->update('drivers', array('mode' => 'available', 'current_latitude' => $data['actual_lng'], 'current_longitude' => $data['actual_lng']), array('id' => $data['driver_id']));	
										
			}
			
			$this->db->select('rides.*, taxi.type, location_fare.base_ride_distance, location_fare.base_ride_charge, location_fare.base_extra_charge_per_kilometer, location_fare.distance_unit, location_fare.currency, customers.mobile, customers.country_code, customers.id as customer_id');
			$this->db->join('taxi', 'taxi.id = rides.taxi_id', 'left');
			$this->db->join('location_fare', 'location_fare.taxi_type = taxi.type');
			$this->db->join('customers', 'customers.id = rides.customer_id', 'left');
			$this->db->where('rides.id', $data['booking_id']);
			$r = $this->db->get('rides');
			
			if($r->num_rows()>0){
				$row = $r->row();
				
				$google_distance =  $this->GetDrivingDistance($row->pickup_lat, $row->pickup_lng, $row->actual_lat, $row->actual_lng);
				$total_distance = $google_distance['distance'] / 1000;
				
				if($total_distance <= $row->base_ride_distance){
					$round_cost = round($row->base_ride_charge);
					$cost = $row->base_ride_charge;
					
				}else{
					
					$cost = $row->base_ride_charge;
					$bal_distance = $total_distance - $row->base_ride_distance;
					$extra_charge = $bal_distance * $row->base_extra_charge_per_kilometer;
					$round_cost = round($cost + $extra_charge);
					$cost = $cost + $extra_charge;
					
				}
				$country_code = $row->country_code;
				$customer_mobile = $row->mobile;
				$customer_id = $row->customer_id;
				
				
				//$this->db->insert('payment', array('ride_id' => $row->id, 'cost' => $cost, 'round_cost' => $round_cost, 'payment_mode' => 1, 'total_kms' => $total_distance, 'status' => 'Paid', 'amount_paid' => $round_cost, 'balance_paid' => 0));
				
			}
			
			$result = array(
				'booking_id' => $data['booking_id'],
				'total_fare' => $round_cost,
				'total_distance' => round($total_distance, 1),
				'trip_fare' => $round_cost,
				'tolls' => 0,
				'dicounts' => 0,
				'outstanding_from_last_trip' => 0,
				'total' => $round_cost, 
				'customer_mobile' => $customer_mobile, 
				'country_code' => $country_code, 
				'customer_id' => $customer_id,
				
			);	
		
			return $result;	
		}
		return false;	
	}
	
	
	function reachedlocation($data){
		$query = "select * from {$this->db->dbprefix('rides')} where driver_id='".$data['driver_id']."' AND  id='".$data['ride_id']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$data[] = $q->row();
			return  $data;
		}
		return false;
	}
	
	function GetDrivingDistance($lat1, $long1, $lat2, $long2)
	{
		/* $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		$dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
		$time = $response_a['rows'][0]['elements'][0]['duration']['value'];
	
		return array('distance' => $dist, 'time' => $time);*/
		
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		$dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
		$time = $response_a['rows'][0]['elements'][0]['duration']['value'];
	
		return array('distance' => $dist, 'time' => $time);
	}

	/*function distance($lat1, $lon1, $lat2, $lon2, $unit) {

	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);
	
	  if ($unit == "K") {
		return ($miles * 1.609344);
	  } else if ($unit == "N") {
		  return ($miles * 0.8684);
		} else {
			return $miles;
		  }
	}*/
	
	function  myrides($driver_id){
		
		$image_path = base_url('assets/uploads/drivers/photo/');
		
		$this->db->select('r.status, r.pick_up, r.drop_off, r.ride_start_time, r.ride_end_time, t.name taxi_name, t.number, tb.name brands, tc.name colors, tt.name types, p.cost, p.total_kms');		
		$this->db->from('rides r');
		$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
		$this->db->join('customers c', 'c.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
		$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.driver_id', $driver_id);
		$this->db->order_by('r.id', 'DESC');
		$q = $this->db->get();
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				if($row->cost ==''){
					$row->cost =  '0';
				}
				if($row->total_kms ==''){
					$row->total_kms =  '0';
				}
				if($row->drop_off ==''){
					$row->drop_off =  '0';
				}
				if($row->ride_start_time == '0000-00-00 00:00:00'){
					$row->ride_start_time =  '0';
				}
				if($row->ride_end_time == '0000-00-00 00:00:00'){
					$row->ride_end_time =  '0';
				}
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	
	function getDriverTaxi($driver_id){
		$this->db->select('*');
		$this->db->where('driver_id', $driver_id);
		$q = $this->db->get('taxi');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;		
	}
	
	function driverTimeout($data){
		if($this->db->insert('driver_booking', $data)){
		
			return true;
		}
		return false;	
	}
	
	function driveraccept($update_taxi, $update_driver, $ride_id, $driver_id){
		
		$image_path = base_url('assets/uploads/drivers/photo/');
		$cus_path = base_url('assets/uploads/customers/photo/');
		$this->db->where('id', $ride_id);
		$d = $this->db->update('rides', $update_taxi);
		if($d){
			if(!empty($update_driver)){
				$this->db->insert('driver_booking', $update_driver);
			}
			if($driver_id){
			$this->db->where('id',$driver_id);
			$this->db->update('drivers', array('mode' => 'booked'));
			}
			
			if($ride_id){
			$this->db->select('r.*, d.first_name driver_name, d.email, d.contact_number, d.gender, d.photo, d.mode, c.photo cphoto, c.first_name customer_name, c.mobile customer_mobile, c.country_code customer_country_code,  t.name taxi_name, 	t.brand, t.model, t.number, t.type, t.color, tb.name brands, tc.name colors, tt.name types');		
			$this->db->from('rides r');
			$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
			$this->db->join('customers c', 'c.id = r.customer_id', 'left');
			$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
			$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
			$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
			$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
			$this->db->where('r.id', $ride_id);
			
			$q = $this->db->get();
			
				if($q->num_rows() > 0){
					$row = $q->row();
					$row->driver_photo = "";
					if($row->photo !=''){
						$row->driver_photo = $image_path.$row->photo;
					}
					
					$row->customer_photo = "";
					if($row->cphoto !=''){
						$row->customer_photo = $cus_path.$row->cphoto;
					}else{
						$row->customer_photo = $cus_path.'default.png';
					}
					
					return $row;
				}
			}
		
		}
		
		return false;
		
	}
    
}
