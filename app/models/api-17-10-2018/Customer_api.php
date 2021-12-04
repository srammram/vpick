<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_api extends CI_Model
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
	
	function insertNotification($data){
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s') ));
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
	
	function getSettings(){
		$q = $this->db->select('*')->where('setting_id', 1)->get('settings');
		if($q->num_rows() > 0){
			return $q->row();	
		}
		return false;
	}
	
	function checkMobile($oauth_token, $mobile){
		$q = $this->db->select('*')->where('mobile', $mobile)->where('oauth_token != ', $oauth_token)->get('customers');
		if($q->num_rows() > 0){
			return true;	
		}
		return false;
	}
	
	function checkEmail($oauth_token, $email){
		$q = $this->db->select('*')->where('email', $email)->where('oauth_token != ', $oauth_token)->get('customers');
		if($q->num_rows() > 0){
			return true;	
		}
		return false;
	}
	
	function edit_customer($data){
		$this->db->where('oauth_token', $data['oauth_token']);	
		$q = $this->db->update('customers', array('email' => $data['email'], 'first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'dob' => $data['dob'], 'country_code' => $data['country_code'], 'mobile' => $data['mobile'], 'photo' => $data['photo']));
		if($q){
			return true;
		}
		return false;
	}
	
	function  myrides($customer_id){
		$image_path = base_url('assets/uploads/drivers/photo/');
		
		$this->db->select('r.status, r.pick_up, r.drop_off, r.ride_start_time, r.ride_end_time, t.name taxi_name, t.number, tb.name brands, tc.name colors, tt.name types, p.cost, p.total_kms, d.first_name driver_name');		
		$this->db->from('rides r');
		$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
		$this->db->join('customers c', 'c.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
		$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		$this->db->order_by('r.id', 'DESC');
		$q = $this->db->get();
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				if($row->brands ==''){
					$row->brands =  '0';
				}
				if($row->colors ==''){
					$row->colors =  '0';
				}
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->driver_name ==''){
					$row->driver_name =  '0';
				}
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
	
	
	/*function  myrides($customer_id){
		$image_path = base_url('assets/uploads/drivers/photo/');
		
		$this->db->select('r.*, d.first_name driver_name, d.email, d.contact_number, d.gender, d.photo, d.mode, c.first_name customer_name,  t.name taxi_name, 	t.brand, t.model, t.number, t.type, t.color, tb.name brands, tc.name colors, tt.name types');		
		$this->db->from('rides r');
		$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
		$this->db->join('customers c', 'c.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
		$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		$this->db->order_by('r.id', 'DESC');
		$q = $this->db->get();
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				$row->driver_photo = "";
				if($row->photo !=''){
					$row->driver_photo = $image_path.$row->photo;
				}
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	*/
	
	
	
	function add_customer($data){
		
		$this->db->insert('customers', $data);//print_R($this->db->error());exit;
		$customer_id = $this->db->insert_id();	
		
		
		$query = "select id,oauth_token from {$this->db->dbprefix('customers')} where id='".$customer_id."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			/*$this->db->where('id', $data['customer_id']);
			$this->db->update('customers', array('mobile_otp' =>  $data['mobile_otp']));*/
			$data = $q->row();
			return $data;
		}
		
	}
	
	
	
	function checkotp($data){
		$query = "select * from {$this->db->dbprefix('customers')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		$q = $this->db->query($query);

		if($q->num_rows()>0){
			$this->db->where('id', $data['customer_id']);
			$this->db->update('customers', array('status' => 1));
			return true;
		}
		return false;
	}
	
	function resendotp($data){
		$query = "select * from {$this->db->dbprefix('customers')} where id='".$data['customer_id']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $data['customer_id']);
			$this->db->update('customers', array('mobile_otp' =>  $data['mobile_otp']));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgototp($data){
		$query = "select * from {$this->db->dbprefix('customers')} where mobile='".$data['mobile']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->update('customers', array('forgot_otp' => $data['forgot_otp'], 'forgot_active' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgotcheckotp($data){
		$query = "select * from {$this->db->dbprefix('customers')} where id='".$data['customer_id']."' AND  forgot_otp='".$data['forgot_otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			return true;
		}
		return false;
	}
	
	function forgotresendotp($data){
		$query = "select * from {$this->db->dbprefix('customers')} where id='".$data['customer_id']."' AND forgot_otp = '".$data['forgot_otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->update('customers', array('forgot_otp' => $data['forgot_otp'], 'forgot_active' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	
	function updatepassword($data){
		
		$this->db->where('id', $data['customer_id']);
		$q = $this->db->update('customers', array('password' => $data['password'], 'forgot_active' => 1));
		if($q){
			return true;	
		}
		return false;
	}
	
	
	function check_login($data){
		$image_path = base_url('assets/uploads/customers/photo/');
		$query = "select * from {$this->db->dbprefix('customers')} where password='".$data['password']."' AND  mobile='".$data['mobile']."' AND  country_code='".$data['country_code']."'  ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
		$check_otp_verify = "select * from {$this->db->dbprefix('customers')} where password='".$data['password']."' AND  mobile='".$data['mobile']."' AND  country_code='".$data['country_code']."'  AND status =1 ";
			$otp = $this->db->query($check_otp_verify);
			if($otp->num_rows()>0){
					$row = $q->row();
					if($row->photo !=''){
						$row->customer_photo = $image_path.$row->photo;
					}else{
						$row->customer_photo = $image_path.'default.png';
					}					
					$data =  $row;
					return $data; //user name password and otp is verified
		     }
			 return 3;// otp not verified
		}
		return 2;//invalid credentials
	}
	
	function getCustomer($oauth_token){
		$this->db->select('*');
		$this->db->where('oauth_token', $oauth_token);
		$q = $this->db->get('customers');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
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
	
	function getDrivers_radius($data){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$taxi_path = base_url('assets/uploads/taxi/');
	$this->db
	    ->select("d.id, d.oauth_token, d.created_on date_created,d.first_name driver_name,d.photo,d.current_latitude latitude,d.current_longitude longitude,d.mode, dcs.taxi_id, t.name, t.number, t.type, tt.name type_name, tt.mapcar type_image ")
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
	    $data = $q->result();
	    foreach($data as $k => $row){
		$row->driver_photo = "";
		if($row->photo !=''){
		    $row->driver_photo = $image_path.$row->photo;
		}
		if($row->type_image !=''){
		    $row->type_image = $taxi_path.$row->type_image;
		}	
		
		/*if($row->mode=="available"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available_hover.png';
		}else if($row->mode=="on ride"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride_hover.png';
		}else if($row->mode=="offline"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline_hover.png';
		}*/
		$data[$k] = $row;
	    }
	    //print_r($data);exit;
	    return $data;
	}
	return false;
    }
	
	
	function getRideBYID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;	
	}
	function customerCancel($data){
		
		$this->db->where('customer_id', $data['customer_id']);
		$this->db->where('id', $data['booking_id']);
		$q = $this->db->update('rides', array('cancel_status' => 1, 'cancelled_by' => 'customer', 'status' => 'cancelled'));
		if($q){
			$this->db->where('id', $data['driver_id']);
			$this->db->update('drivers', array('mode' => 'available'));
			return true;
		}
		return false;	
	}
	
	function customerRating($data){
		
		$query = "update {$this->db->dbprefix('rides')} set rating='".$data['rating']."' where  customer_id='".$data['customer_id']."' AND  id='".$data['booking_id']."' ";

		$q = $this->db->query($query);
		

		if($q){
			return true;
		}
		return false;	
	}
	
	function checkbookedcustomer($data){
		
		$check = $this->db->select('*')->where('customer_id', $data['customer_id'])->where('status', 'booked')->get('rides');
		if($check->num_rows() == 0){
			
			return true;	
		}
		return false;
	}
	
	function drivertraking($data){
		$this->db->select('rides.*, drivers.id as driver_id, driver_current_status.mode as driver_cmode, driver_current_status.current_latitude as driver_clatitude, driver_current_status.current_longitude as driver_clongitude');
		$this->db->join('drivers', 'drivers.id = rides.driver_id', 'left');
		$this->db->join('driver_current_status', 'driver_current_status.driver_id = rides.driver_id AND driver_current_status.taxi_id = rides.taxi_id');
		$this->db->where('rides.id', $data['booking_id']);
		$this->db->where('rides.customer_id', $data['customer_id']);
		$this->db->order_by('driver_current_status.id', 'DESC');
		$q = $this->db->get('rides');
		
		if($q->num_rows() > 0){
			return $q->row();
		}
		return false;	
	}
	
	function add_booking($data){
		
		$image_path = base_url('assets/uploads/drivers/photo/');
		$this->db->insert('rides', $data);//print_R($this->db->error());exit;
		if($id = $this->db->insert_id()){
			
			if($data['driver_id'] != 0){
				$this->db->where('id',$data['driver_id']);
				$driver['mode'] = 'booked';
				$this->db->update('drivers',$driver);
				
				$this->db->select('r.*, d.first_name driver_name, d.email, d.contact_number, d.gender, d.photo, d.mode, c.first_name customer_name,  t.name taxi_name, 	t.brand, t.model, t.number, t.type, t.color, tb.name brands, tc.name colors, tt.name types');		
				$this->db->from('rides r');
				$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
				$this->db->join('customers c', 'c.id = r.customer_id', 'left');
				$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
				$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
				$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
				$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
				$this->db->where('r.id', $id);
				$q = $this->db->get();
				if($q->num_rows()>0){
					$row = $q->row();
					$row->driver_photo = "";
					if($row->photo !=''){
						$row->driver_photo = $image_path.$row->photo;
					}
					
					return $row;
				}
			}else{			
				return $row['booking_id'] = $id;
			}
		}
		
		return false;
    }
}
