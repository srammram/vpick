<?php defined('BASEPATH') OR exit('No direct script access allowed');

class booking_crm_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
    }
	
	function getDrivers_radius($data, $countryCode){
		
		
		
	$image_path = base_url('assets/uploads/');
	
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
	}else{
		$where = "  ";
	}
	
	
	$query = "SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.id as taxi_id, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, ti.image, ti.image_hover, ti.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id  
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type
	LEFT JOIN {$this->db->dbprefix('taxi_image')} AS ti ON ti.id = tt.taxi_image_id 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  AND us.ride_stop = 0
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE d.is_country = '".$countryCode."' AND (dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1  AND dcs.allocated_status = 1    ".$where." GROUP BY d.id HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

//AND (dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1  AND dcs.allocated_status = 1

	$q = $this->db->query($query);
	
	//print_r($this->db->last_query());die;
	
	if($q->num_rows()>0){
	    $r = $q->result();
	    foreach($r as $row){
			
			if($row->driver_photo !=''){
				$row->driver_photo = $image_path.$row->driver_photo;
			}else{
				$row->driver_photo = $image_path.'no_image.png';
			}
			
			if($row->taxi_photo !=''){
				$row->taxi_photo = $image_path.$row->taxi_photo;
			}else{
				$row->taxi_photo = $image_path.'no_image.png';
			}	
			
			if($row->image !=''){
				$row->image = $image_path.$row->image;
			}else{
				$row->image = '';
			}
			
			if($row->image_hover !=''){
				$row->image_hover = $image_path.$row->image_hover;
			}else{
				$row->image_hover = '';
			}
			
			if($row->type_image !=''){
				$row->type_image = $image_path.$row->type_image;
			}else{
				$row->type_image = '';
			}
			
			
		
		
			$d[] = $row;
	    }
	    
	    return $d;
	}
	return false;
    }
	
	function checkbookedcustomer($data, $countryCode){
		
		$check = $this->db->select('*')->where('customer_id', $data['customer_id'])->where('status !=', 5)->where('status !=', 7)->where('cancel_status', 0)->where('is_country', $countryCode)->get('rides');
		
		if($check->num_rows() == 0){
			return true;	
		}
		return false;
	}
	
	function getDriversnew_radius($data, $countryCode){
	$image_path = base_url('assets/uploads/');
	
	$longitude = $data['longitude'];
	$latitude = $data['latitude'];
	
	
	
	$estimate_distance =  $this->site->GetDrivingDistance_New($data['latitude'], $data['longitude'], $data['dlatitude'], $data['dlongitude'], 'Km', $countryCode);	
	$estimate_distance = round($estimate_distance,1);
	
	$search = $this->db->insert('search_location', array('latitude' => $latitude, 'longitude' => $longitude, 'is_country' => $countryCode));
		
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
	}else{
		$where = "  ";
	}
	
	$query = "SELECT  d.id, d.first_name, d.mobile, d.country_code, d.oauth_token, d.is_daily, d.is_rental, d.is_outstation, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, ti.image, ti.image_hover, ti.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id 
	 
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('taxi_image')} AS ti ON ti.id = tt.taxi_image_id 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  AND us.ride_stop = 0
	
	WHERE d.is_country = '".$countryCode."'   AND  (dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1 AND dcs.allocated_status = 1   ".$where."  GROUP BY d.id  HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

	//AND  (dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1 AND dcs.allocated_status = 1 
	$q = $this->db->query($query);

	//print_r($this->db->last_query());die;
	if($q->num_rows()>0){
	    $r = $q->result();
	    foreach($r as $row){
			
			$row->units = 'Km';
			$fare[$row->type] = $this->site->getFareestimate($data['latitude'], $data['longitude'], $row->type, 1, $countryCode);
			//print_r($fare[$row->type]);
			$row->min_price = $fare[$row->type]['min_distance_price'] != NULL ? $fare[$row->type]['min_distance_price'] : '0';
			$row->min_distance = $fare[$row->type]['min_distance'] != NULL ? $fare[$row->type]['min_distance'] : '0';
			$row->per_distance = $fare[$row->type]['per_distance'] != NULL ? $fare[$row->type]['per_distance'] : '0';
			$row->per_distance_price = $fare[$row->type]['per_distance_price'] != NULL ? $fare[$row->type]['per_distance_price'] : '0';	
			
			if($row->driver_photo !=''){
				$row->driver_photo = $image_path.$row->driver_photo;
			}else{
				$row->driver_photo = $image_path.'no_image.png';
			}
			
			if($row->taxi_photo !=''){
				$row->taxi_photo = $image_path.$row->taxi_photo;
			}else{
				$row->taxi_photo = $image_path.'no_image.png';
			}	
			
			if($row->image !=''){
				$row->image = $image_path.$row->image;
			}else{
				$row->image = '';
			}
			
			if($row->image_hover !=''){
				$row->image_hover = $image_path.$row->image_hover;
			}else{
				$row->image_hover = '';
			}
			
			if($row->type_image !=''){
				$row->type_image = $image_path.$row->type_image;
			}else{
				$row->type_image = '';
			}
			$row->estimate_distance = $estimate_distance;
			if($row->min_distance > $row->estimate_distance){
				$row->estimate_fare = $row->min_price;
			}else{
				$row->estimate_fare = round((($row->estimate_distance - $row->min_distance) * $row->per_distance_price) + $row->min_price);
			}
			$d['result'][] = $row;
	    }
		$d['distance'] = $estimate_distance;
	    
	    return $d;
	}
	return false;
    }
	
	function create_customer($data, $countryCode){
		$this->db->insert('users', $data);
		if($customer_id = $this->db->insert_id()){
			return $customer_id;
		}
		return 0;	
	}
	
	function getcabTypes($countryCode){
		$this->db->select('id, name');
		$this->db->where('is_country', $countryCode);
		$q  = $this->db->get('taxi_type');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;	
	}
	
	function userCheck($mobile, $phonecode, $countryCode){
		$this->db->select('id');
		$this->db->where('mobile', $mobile);
		$this->db->where('country_code', $phonecode);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('users');
		if($q->num_rows()>0){
			return $q->row();
		}
		return FALSE;
	}
	
	function getRideBYID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;	
	}
	
	function insertNotification($data, $countryCode){
		
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode ));
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	function getPaymentName($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('payment_mode');
		
		if($q->num_rows()>0){
		    $name =  $q->row('name');
			return $name;
		}
		return false;	
	}
	
	function add_booking($bookingcrm, $bookingcrm_follow, $insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $customer_id, $offer_code){
		
		$image_path = base_url('assets/uploads/');
		$insert['is_country'] = $countryCode;
		$this->db->insert('rides', $insert); //print_r($this->db->last_query());exit;
		if($ride_id = $this->db->insert_id()){
			 $bookingcrm['ride_id'] = $ride_id;
			 $this->db->insert('bookingcrm', $bookingcrm);
			 if($bookingcrm_id = $this->db->insert_id()){
				 $bookingcrm_follow['bookingcrm_id'] = $bookingcrm_id;
				 $this->db->insert('bookingcrm_follow', $bookingcrm_follow);
				 
				 $this->db->insert('bookingcrm_notification', array('bookingcrm_id' => $bookingcrm_id, 'ride_id' => $ride_id, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
			 }
			 
			
			if($offer_code != ''){
				$offer = $this->db->select('offer_fare_type, offer_fare')->where('offer_code', $offer_code)->get('offers');
				
				$this->db->insert('offers_user', array('user_id' => $customer_id, 'ride_id' => $ride_id, 'offer_code' => $offer_code, 'offer_type' => $offer->offer_fare_type, 'discount' => $offer->offer_fare, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
				
			}
			$booking_no = 'BK'.str_pad($ride_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('rides', array('booking_no' => $booking_no), array('id' => $ride_id, 'is_country' => $countryCode));
			foreach($ride_insert as $ride){
				$ride['ride_id'] = $ride_id;
				$ride['is_country'] = $countryCode;
				$this->db->insert('ride_route', $ride);
				if($ride_type == 2){
					$this->db->insert('ride_later', array('ride_id' => $ride_id, 'timing' => $ride_timing, 'is_country' => $countryCode));	
				}
			}
			return $ride_id;
		}
		
		return 0;
    }
	
	
	function getRidesCustomer($booking_id, $countryCode){
		$this->db->select('r.*, rp.driver_allowance, rp.total_night_halt, rp.total_toll, rp.total_parking, rp.total_distance, rp.total_fare, rp.extra_fare, mr.overall, mr.drive_comfort_star, mr.booking_process_star, mr.cab_cleanliness_star, mr.drive_politeness_star, mr.fare_star, mr.easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, d.first_name as dfname, d.last_name as dlname, d.country_code as dccode, d.mobile as dmobile, v.mobile as vmobile, v.country_code as vccode, vp.first_name as vfname, vp.last_name as vlname, dcs.current_latitude as driver_latitude, dcs.current_longitude as  driver_longitude, df.location');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id ', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id ', 'left');
		
		$this->db->join('users v', 'v.id = r.vendor_id ', 'left');
		$this->db->join('user_profile vp', 'vp.user_id = r.vendor_id ', 'left');
		
		$this->db->join('users d', 'd.id = r.driver_id ', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id ', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id ', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id ', 'left');
		$this->db->join('driver_frequency df', 'df.ride_id = r.id', 'left');
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id AND dcs.allocated_status = 1 ', 'left');
		
		$this->db->where(array('r.id'=>$booking_id, 'r.is_country' => $countryCode));
		
		$q = $this->db->get();//print_r($this->db->error());exit;
		
       	if($q->num_rows()>0){
			
			$row = $q->row();
			$location = explode(',', $row->location);
			
			if(!empty($location)){
				for($i=0; $i<count($location); $i++){
					if($location[$i] != 0){
						$lat[] = array('lat' => $location[$i], 'lng' => $location[$i+1]);
						$loc[] = array('plat' => $location[$i], 'plng' => $location[$i+1], 'dlat' => $location[$i+3] ? $location[$i+3] : $location[$i], 'dlng' => $location[$i+4] ? $location[$i+4] : $location[$i+1]);	
						
						$test[] = array($location[$i], $location[$i+1], $location[$i+3] ? $location[$i+3] : $location[$i], $location[$i+4] ? $location[$i+4] : $location[$i+1]);	
						
						$i = $i + 2;
					}
				}
			}else{
				$lat[] = array();
			}
			
			//echo '<pre>';
			//print_r(json_encode($test, JSON_NUMERIC_CHECK));
			
			$row->location_loc = json_encode($loc, JSON_NUMERIC_CHECK);
			$row->location = json_encode($lat, JSON_NUMERIC_CHECK);
			//die;
			
			return $row;
		}
		return false;
	}
	
	function getRidesBooking($booking_id, $countryCode){
		$this->db->select('b.id, b.ticket_code, b.ticket_date, b.evalution_number, b.status, bn.is_read, bn.cancel_notification');
		$this->db->from('bookingcrm b');
		$this->db->join('bookingcrm_notification bn', 'bn.bookingcrm_id = b.id', 'left');
		$this->db->where('b.ride_id', $booking_id);
		$this->db->where('b.is_country', $countryCode);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();	
		}
		return false;
		
	}
	
	function ride_close($booking_crm_id, $discussion, $remark, $user_id, $is_country){
		$this->db->where('id', $booking_crm_id);
		$q = $this->db->update('bookingcrm', array('status' => 1));
		
		if($q){
			$this->db->insert('bookingcrm_follow', array('bookingcrm_id' => $booking_crm_id, 'bookingcrm_staff_id' => $user_id, 'followup_date_time' => date('Y-m-d H:i:s'), 'status' => '1', 'discussion' => $discussion, 'remark' => $remark, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $is_country));
				
			return true;
		}
		return false;
	}
	
	function getRidesFollow($booking_crm_id, $booking_id, $countryCode){
		$this->db->select('bf.id, bf.followup_date_time, bf.status, bf.discussion, bf.remark, u.first_name');
		$this->db->from('bookingcrm_follow bf');
		$this->db->join('users u', 'u.id = bf.bookingcrm_staff_id', 'left');
		$this->db->where('bf.bookingcrm_id', $booking_crm_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->result();	
		}
		return false;
		
	}
	
	function timeoutCustomer($timeout_array, $booking_id, $countryCode){
		$this->db->where('id', $booking_id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('rides', $timeout_array)){
			$q = $this->db->select('staff_id, id, customer_id')->where('ride_id', $booking_id)->get('bookingcrm');
			if($q->num_rows()>0){
			
				$this->db->update('bookingcrm', array('bookingcrm_status' => 6, 'customer_cancel' => 1, 'customer_cancel_msg' => 'Driver Not Available. Timeout closing rides'), array('ride_id' => $booking_id, 'is_country' => $countryCode));
				$this->db->insert('bookingcrm_follow', array('bookingcrm_id' => $q->row('id'), 'bookingcrm_staff_id' => $q->row('staff_id'), 'followup_date_time' => date('Y-m-d H:i:s'), 'bookingcrm_status' => 6, 'discussion' => 'Driver Not Available. Timeout closing rides', 'is_edit' => 1, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
				
				$this->db->update('bookingcrm', array('bookingcrm_status' => 8, 'customer_cancel' => '1', 'customer_cancel_msg' => 'Driver Not Available. Timeout closing rides'), array('ride_id' => $booking_id));
				$this->db->update('bookingcrm_notification', array('cancel_notification' => 1), array('ride_id' => $booking_id));
				
			}
			return TRUE;
		}
		return false;	
	}
	
	function cancelCustomer($cancel_array, $booking_id, $driver_id, $countryCode){
		$this->db->where('id', $booking_id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('rides', $cancel_array)){
			if($driver_id != 0){
			$this->db->where('driver_id', $driver_id);
			$this->db->where('allocated_status', 1);
			$this->db->where('is_country', $countryCode);
			$this->db->update('driver_current_status', array('mode' => 1, 'is_connected' => 1));
			}
			$q = $this->db->select('staff_id, id, customer_id')->where('ride_id', $booking_id)->get('bookingcrm');
			if($q->num_rows()>0){
			
				$this->db->update('bookingcrm', array('bookingcrm_status' => 6, 'customer_cancel' => 1, 'customer_cancel_msg' => 'Customer has been cancelled. '), array('ride_id' => $booking_id, 'is_country' => $countryCode));
				$this->db->insert('bookingcrm_follow', array('bookingcrm_id' => $q->row('id'), 'bookingcrm_staff_id' => $q->row('staff_id'), 'followup_date_time' => date('Y-m-d H:i:s'), 'bookingcrm_status' => 6, 'discussion' => 'Driver Not Available. Timeout closing rides', 'is_edit' => 1, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
				
				$this->db->update('bookingcrm', array('bookingcrm_status' => 8, 'customer_cancel' => '1', 'customer_cancel_msg' => 'Customer has been cancelled. '), array('ride_id' => $booking_id));
				$this->db->update('bookingcrm_notification', array('cancel_notification' => 1), array('ride_id' => $booking_id));
				
			}
			return TRUE;
		}
		return false;	
	}
	
}
