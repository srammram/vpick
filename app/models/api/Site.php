<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct() {
        parent::__construct();
	$this->load->library('ion_auth');
    }
	
	function findLocation($latitude, $longitude){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
	function findLocationWEB($latitude, $longitude){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
	function findLocationPINCODE($latitude, $longitude){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[3]->address_components[0]->long_name)){
			return $json_decode->results[3]->address_components[0]->long_name;
		}
		return false;
	}
	
	
	
    function GetDrivingDistance($lat1, $long1, $lat2, $long2)
	{
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
		$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
	
		return array('distance' => $dist, 'time' => $time);
	}
	
	
	function getFare($ride_type, $taxi_type, $total_distance, $estimate_distance, $actual_distance, $lat, $lng){
		$data = array();
		$pincode = $this->findLocationPINCODE($lat, $lng);
		
		$q = $this->db->select('city_id')->where('pincode', $pincode)->get('areas');
		if ($q->num_rows() > 0) {
            $city_id = $q->row('city_id');
        }else{
			$city_id = 0;
		}
		if($ride_type == 1){
			$c = $this->db->select('df.base_min_distance, df.base_min_distance_price, df.base_per_distance, df.base_per_distance_price')->from('daily_fare df')->where('city_id', $city_id)->where('taxi_type', $taxi_type)->get();
			if ($c->num_rows() > 0) {
				
			}else{
				$d = $this->db->select('df.base_min_distance, df.base_min_distance_price, df.base_per_distance, df.base_per_distance_price')->from('daily_fare df')->where('is_default', 1)->get();
			}
			
		}elseif($ride_type == 2){
			
		}elseif($ride_type == 3){
			
		}
		return false;
	}
	
    public function get_setting() {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function get_driver($driver_id) {
		
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, up.first_name, up.last_name, up.gender, up.photo, dcs.mode, dcs.current_latitude, dcs.current_longitude');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id AND allocated_status = 1', 'left');
		$this->db->where('u.id', $driver_id);
		$q = $this->db->get();
		
		
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function get_customer($customer_id) {
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, up.first_name, up.last_name, up.gender, up.photo');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->where('u.id', $customer_id);
		
		
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
   public function getSocketID($user_id, $user_type){
		$this->db->select('socket_id');
		$this->db->where('user_id', $user_id);
		$this->db->where('user_type', $user_type);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get('user_socket');
		if ($q->num_rows() == 1) {
			return $q->row('socket_id');
    	}
		return false;
   }
	
    public function devicesCheck($api_key){
	    $q = $this->db->get_where('api_keys', array('key' => $api_key), 1);
    if ($q->num_rows() == 1) {
		    
	return $q->row('devices_key');
    }
	    return FALSE;
    }
	
    public function getDateFormat($id) {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	function getCountrywithoutparent(){
		$flags_path = base_url('assets/uploads/');
		 $q = $this->db->get('countries');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				if($row->flag != ''){
					$row->flag = $flags_path.$row->flag;
				}else{
					$row->flag = $flags_path.'no_image.png';
				}
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	function getAllCountrieswithflags(){
		$flags_path = base_url('assets/uploads/');
		 $q = $this->db->get('countries');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				if($row->flag != ''){
					$row->flag = $flags_path.$row->flag;
				}else{
					$row->flag = $flags_path.'no_image.png';
				}
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	/*function getAllCountrieswithflags(){
		$flags_path = base_url('assets/uploads/country_flags/');
		 $q = $this->db->get('countries');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				$row->flag_name = str_replace(' ', '-', $row->name);
				$row->flag = $flags_path.'flag-of-'.$row->flag_name.'.png';
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}*/

    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
    public function getAllCurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	
	public function getAllTypes() {
        $q = $this->db->get('taxi_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getCurrencyByCode($code) {
        $q = $this->db->get_where('currencies', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
    public function getExchangeCurrency($id) {
 	$this->db->select('symbol');
 	$this->db->where_not_in('id', array($id));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->symbol;
            }
            return $data;
        }
        return FALSE;
    }
    
	
    public function getCurrencyByID($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	

    public function modal_js() {
        return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function checkPermissions() {
        $q = $this->db->get_where('permissions', array('group_id' => $this->session->userdata('group_id')), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    

    public function getWarehouseProductsVariants($option_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

      
    
  function isloggeddIn($user){
        
        $q = $this->db
        ->select()
        ->from('user_logins')
        //->where("username ='$user' or email = '$user' ")
	->where("login_type='A' AND (username ='$user' or email = '$user' )")
        ->order_by('id','DESC')
        ->get();
        $data = $q->row_array();
        if($q->num_rows() > 0){
            if($data['status']=="logged_out"){
                return false;
            }else if(time()>strtotime($data['expiry'])){
               return false;
            }else if(time()>strtotime($data['last_activity'])+300){
               return false;
            }else{
               return true;
            }
        }
       return false;
    }
    public function getUserGroup($user_id = false) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q = $this->db->get_where('groups', array('id' => $group_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getUserGroupIDbyname($group_name = false) {

        $q = $this->db->get_where('groups', array('name' => $group_name), 1);
        if ($q->num_rows() > 0) {
            return $q->row('id');
        }
        return FALSE;
    }
	
    public function getUserGroupID($user_id = false) {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }
    function updateLoginStatus($data){
        $session_id = $this->session->userdata('session_id');
        $this->db->where('session_id',$session_id);
        $this->db->update('user_logins',$data);
    }
    function isActiveUser(){
	if($this->router->fetch_method()=="logout"){return true;}
	$session_id = $this->session->userdata('session_id');
	$login_user = $this->session->userdata('username');
        $login_email = $this->session->userdata('email');
        $q = $this->db
        ->select()
        ->from('user_logins')
        ->where("login_type='A' AND (username ='$login_user' or email = '$login_email' )")
	
        ->order_by('id','DESC')
        ->get();
	
	//print_R($q->row());
        if($q->num_rows()>0){
            $row = $q->row();//print_r($row);
	    //echo $session_id.'=='.$row->session_id;exit;
            if($session_id!=$row->session_id) {
		
		$data['status'] = "inactive";		
		$this->updateLoginStatus($data);
		$this->session->set_flashdata(lang('someone has logged in'));
		$this->ion_auth->logout();
		admin_redirect('login');
	    }
        }
    }
    /**** one login at a time - End****/
    public function getAllPrinters() {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getAllTaxiTypes(){
	$q = $this->db->get('taxi_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	function getAllPaymentMode(){
	$q = $this->db->get('payment_mode');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getAlldistanceUnits(){
	$q = $this->db->get_where('distance_units');
	if($q->num_rows()>0){
	    
	    return $q->result();
	}
	return false;
    }
     
	 
    function getNearestDrivers($data,$except_driver=false){
	
	
	$distance = 10;
        $radius = 3959;//6371;
        $lat  = $data['origin_lat'];//34.0522342;
        $lng = $data['origin_lng'];//-118.2436849;
        // latitude boundaries
        $data['maxlat'] = $lat + rad2deg($distance / $radius);
        $data['minlat'] = $lat - rad2deg($distance / $radius);
        
        // longitude boundaries (longitude gets smaller when latitude increases)
        $data['maxlng'] = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
        $data['minlng'] = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
	
	
	$image_path = base_url('assets/uploads/drivers/photo/');
	$this->db
	    ->select("d.id,t.id taxi_id,d.first_name name,d.created_on date_created,d.first_name title,d.photo,current_latitude latitude,current_longitude longitude,d.mode")
            ->from("drivers d")
            //->join('drivers d','d.id=c.driver_id')
	    ->join('taxi t','d.id=t.driver')
	    ->join('taxi_type tt','tt.id=t.type');
	    $this->db->where('current_latitude BETWEEN "'. $data['minlat']. '" and "'. $data['maxlat'].'"');
	    $this->db->where('current_longitude BETWEEN "'. $data['minlng']. '" and "'. $data['maxlng'].'"');
	    if($data['taxi_type']){
		$this->db->where('tt.id',$data['taxi_type']);
	    }
	    if($except_driver){
		$this->db->where('d.id !=',$except_driver);
	    }
	    $this->db->where('d.mode','available');
	$q=$this->db->get();//print_R($this->db->error());
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		$row->gallery = [];
		if($row->photo !=''){
		    $row->gallery[] = $image_path.$row->photo;
		}
		
		if($row->mode=="available"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available_hover.png';
		}else if($row->mode=="on ride"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride_hover.png';
		}else if($row->mode=="offline"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline_hover.png';
		}
		$data[$k] = $row;
	    }
	   // print_R($data);exit;
	    return $data;
	}
	return false;
    }
    
    
    
   
    function unlink_images($name,$path){
		unlink($path.$name);
		return true;
    }
    function my_is_unique($id,$value,$field,$table){
		$q = $this->db->get_where($table,array('id !='=>$id,$field=>$value));
		if($q->num_rows()>0){
			return true;
		}
		return false;
    }
   
    /*#### Admin Panel */
    
	function getUserroleID($role_access_name){
		$q = $this->db->get_where('user_roles', array('access_area' => $role_access_name));
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return false;
	}
	function getAllVendor(){
		
	$this->db->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name");
	$this->db->join('user_profile up', 'up.user_id = users.id');
	$this->db->where('users.group_id', $this->Vendor);
	
	$q = $this->db->get('users');
	
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
	function getVendorIDBY($user_id){
		$q = $this->db->get_where('users', array('id' => $user_id, 'group_id' => $this->Vendor));
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
    
}
