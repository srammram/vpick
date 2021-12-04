<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct() {
        parent::__construct();
		$this->load->library('ion_auth');
    }
	
	
	public function getCancelMaster($group_id, $countryCode) {
		$this->db->select('id, title, message');
		$this->db->where('is_country', $countryCode);
		$this->db->where('group_id', $group_id);
        $q = $this->db->get('cancelmaster');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				$row->message = strip_tags($row->message);
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getHealthMaster($countryCode) {
		$this->db->select('id, health_name');
		$this->db->where('is_country', $countryCode);
		
        $q = $this->db->get('health');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	function implementCode($reference_no, $user_id, $group_id, $countryCode){
		$current_date = date('Y-m-d');
		
		$u = $this->getRefercode($reference_no);
		$earlier = new DateTime($u->code_start);
		$later = new DateTime($current_date);
		$total_days = $later->diff($earlier)->format("%a");
		
		if($u->using_type == 2){
			
			
			
			$check_days = ceil($total_days / 7);
			if($check_days == 1){
				$end_date = $check_days * 7;
				$refer_start_date = $u->code_start;
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}else{
				$start_date = ($check_days - 1) * 7;
				$end_date = $check_days * 7;
				$refer_start_date = date('Y-m-d', strtotime($u->code_start. ' + '.$start_date.' days'));
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}
		}elseif($u->using_type == 1){
			
			$check_days = ceil($total_days / 30);
			if($check_days == 1){
				$end_date = $check_days * 30;
				$refer_start_date = $u->code_start;
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}else{
				$start_date = ($check_days - 1) * 30;
				$end_date = $check_days * 30;
				$refer_start_date = date('Y-m-d', strtotime($u->code_start. ' + '.$start_date.' days'));
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}
		}else{
			
			$check_days = ceil($total_days / 365);
			if($check_days == 1){
				$end_date = $check_days * 365;
				$refer_start_date = $u->code_start;
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}else{
				$start_date = ($check_days - 1) * 365;
				$end_date = $check_days * 365;
				$refer_start_date = date('Y-m-d', strtotime($u->code_start. ' + '.$start_date.' days'));
				$refer_end_date = date('Y-m-d', strtotime($u->code_start. ' + '.$end_date.' days'));
			}
		}
		$refer_count = $this->checkReferdate($reference_no, $refer_start_date, $refer_end_date);
		
		if($u->register_enable == 1 && $u->ride_enable == 0){
			
			
			if($u->using_menbers > $refer_count){
				
				$this->db->insert('refercode', array('refer_code' => $u->code, 'refer_id' => $u->user_id, 'user_id' => $user_id, 'type' => 1, 'enable' => $u->register_enable, 'status' => 1, 'r_date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s')));
				
				$this->db->insert('wallet', array('user_id' => $u->user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'flag_method' => 13, 'cash' => $u->amount, 'description' => 'One person using refer code credit wallet amount '.$u->amount.''));
				
				$this->db->insert('wallet', array('user_id' => $user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'flag_method' => 13, 'cash' => $u->amount, 'description' => 'Thanks for register added credit wallet amount '.$u->amount.''));
				
			}else{
				$this->db->insert('refercode', array('refer_code' => $u->code, 'user_id' => $user_id, 'type' => 1, 'enable' => $u->register_enable, 'status' => 1, 'r_date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s')));
				$this->db->insert('wallet', array('user_id' => $user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'flag_method' => 13, 'cash' => $u->amount, 'description' => 'Thanks for register added credit wallet amount '.$u->amount.''));
				
			}
		}else{
			if($u->using_menbers > $refer_count){
				
				$this->db->insert('refercode', array('refer_code' => $u->code, 'refer_id' => $u->user_id, 'user_id' => $user_id, 'type' => 1, 'enable' => $u->register_enable,  'r_date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s')));
				
				
			}else{
				$this->db->insert('refercode', array('refer_code' => $u->code, 'user_id' => $user_id, 'type' => 1, 'enable' => $u->register_enable,  'r_date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s')));

				
			}
		}
		
	}
	
	function checkReferdate($refer_code, $start_date, $end_date){
		$this->db->select("COUNT(id) as id_code");
		$this->db->where('refer_code', $refer_code);
		$this->db->where('type', 1);
		$this->db->where("DATE(r_date) >=", $start_date);
       	$this->db->where("DATE(r_date) <=", $end_date);
		
		$q = $this->db->get('refercode');
		
		if($q->num_rows()>0){
			return $q->row('id_code');
		}
		return 0;
	}
	
	function getRefercode($refer_code){
		$this->db->select('*');
		$this->db->where('code', $refer_code);
		$q  = $this->db->get('user_refercode');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function generatorcode($user_type, $countryCode){
		$s = 3;
		$n = 3;
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$number = '0123456789'; 
		$randomString = ''; 
	  
		for ($i = 0; $i < $s; $i++) { 
			$index = rand(0, strlen($string) - 1); 
			$randomString .= $string[$index]; 
		} 
		
		$randomNumber = ''; 
	  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($number) - 1); 
			$randomNumber .= $number[$index]; 
		} 
		$random = $randomString.$countryCode.$user_type.$randomNumber;
		return $random;
	}
	
	function refercode($user_type, $countryCode)
	{
		do{
			$refer_code = $this->generatorcode($user_type, $countryCode);
			$q = $this->db->select('id')->where('refer_code', $refer_code)->get('users');	
		}while($q->num_rows()>0);
		return $refer_code;
		
	}
	
	function generatorpromocode($user_type){
		$s = 3;
		$n = 3;
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$number = '0123456789'; 
		$randomString = ''; 
	  
		for ($i = 0; $i < $s; $i++) { 
			$index = rand(0, strlen($string) - 1); 
			$randomString .= $string[$index]; 
		} 
		
		$randomNumber = ''; 
	  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($number) - 1); 
			$randomNumber .= $number[$index]; 
		} 
		$random = $randomString.$user_type.$randomNumber;
		return $random;
	}
	
	function promocode($user_type)
	{
		do{
			$refer_code = $this->generatorpromocode($user_type);
			$q = $this->db->select('id')->where('offer_code', $refer_code)->get('offers');	
		}while($q->num_rows()>0);
		return $refer_code;
		
	}
	
	
		
	
	function minimumRide($type, $countryCode){
		$this->db->select('amount, offer_amount');
		$this->db->where('is_country', $countryCode);
		$this->db->where('type', $type);
		$this->db->where('is_default', 1);
		$q = $this->db->get('walletoffer');
		if($q->num_rows()>0){
			return $q->row('amount');
		}
		return '0.00';
	}
	
	function Ridewallet($driver_wallet, $admin_wallet){
		if(!empty($driver_wallet)){
			$this->db->insert('wallet', $driver_wallet);
			$this->db->insert('wallet', $admin_wallet);
			return true;
		}
		return false;	
	}
	function getTaxdefault($countryCode){
		
		$this->db->select('*')	;
		$this->db->where('is_default', 1);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('tax');
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;
	}
	
	function masterCheck($table_name, $data){
		$q = $this->db->get_where($table_name, $data);
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			return true;
		}
		return false;	
	}
	
	function masterCheck1($table_name, $data){
		$this->db->select('id');
		$q = $this->db->get_where($table_name, $data);
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			return $q->row('id');
		}
		return 0;	
	}
	
	function insertSearch($latitude, $longitude, $countryCode){
		$q = $this->db->insert('seach_location', array('is_country' => $countryCode, 'latitude' => $latitude, 'longitude' => $longitude));
		if($q){
			return true;	
		}
		return false;
	}
	
	public function getPaymentgateway($countryCode) {
		$this->db->where('is_country', $countryCode);
        $q = $this->db->get('payment_gateway');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function Getunicodesymbol() {
		
        $q = $this->db->get('unicodesymbol');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	function RegsiterSettings($countryCode){
		$this->db->select('*');
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;	
	}
	
	function CommonSettings($countryCode){
		$this->db->select('*');
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		if ($q->num_rows() > 0) {
			return $q->row('name');
		}
		return false;	
	}
	
	function getUserIpAddr(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	function users_logs($countryCode, $user_id, $ip_address, $post_value, $post_url){
		$q = $this->db->insert('users_logs', array('user_id' => $user_id, 'ip_address' => $ip_address, 'post_value' => $post_value, 'post_url' => $post_url, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));	
		if($q){
			return true;	
		}
		return false;
	}
	
	
	function calcCrow($lat1, $lon1, $lat2, $lon2, $countryCode){
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
	
	function completeLatLngPHP($driver_frquency, $ending_ride_latlng, $countryCode){
		
		
		$count=1;
		foreach ($driver_frquency as $k => $v) {
			if ($count%3 == 1) {
				$d1[] = $v;
			}elseif ($count%3 == 2) {
				$d2[] = $v;
			}else{
				$d3[] = $v;
			}
			$count++;
		}
		$result = array();
		
		for($i=0; $i<count($d1); $i++){
			if($d3[$i] == 3 || $d3[$i] == 2){
				if(!empty($d1[$i+1]) && $d2[$i+1]){
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i]);			
				}else{
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $ending_ride_latlng['latitude'], 'end_lng' => $ending_ride_latlng['longitude'], 'status' => $d3[$i]);				
				}
			}
		}
		
		if(!empty($result)){
			foreach($result as $res){
				$distance+= $this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']);	
			}
			return round($distance, 1);
		}
		return 0;
			
	}
	
	function completeLatLng($driver_frquency, $ending_ride_latlng, $countryCode){
		
		
		$count=1;
		foreach ($driver_frquency as $k => $v) {
			if ($count%3 == 1) {
				$d1[] = $v;
			}elseif ($count%3 == 2) {
				$d2[] = $v;
			}else{
				$d3[] = $v;
			}
			$count++;
		}
		$result = array();
		
		for($i=0; $i<count($d1); $i++){
			if($d3[$i] == 3){
				if(!empty($d1[$i+1]) && $d2[$i+1]){
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i]);			
				}else{
					$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $ending_ride_latlng['latitude'], 'end_lng' => $ending_ride_latlng['longitude'], 'status' => $d3[$i]);				
				}
			}
		}
		
		if(!empty($result)){
			foreach($result as $res){
				$distance+= $this->site->GetDrivingDistance_New($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng'], 'Km');	
			}
			return $distance;
		}
		return 0;
			
	}
	
	function checkZonal($latitude, $longitude, $countryCode){
		$pincode = $this->findLocationPINCODE1($latitude, $longitude, $countryCode);
		
		
		if(!empty($pincode)){
			$q = $this->db->select('*')->where('pincode', $pincode)->get('locations');
			if ($q->num_rows() > 0) {
				return 1;
			}else{
				return 0;
			}
		}
		return 0;
	}
	function getPaymentmodeID($payment_id){
		$this->db->select('*');
		$this->db->where('id', $payment_id);
		$q = $this->db->get('payment_mode');
		if ($q->num_rows() > 0) {
			return $q->row('name');
		}
		return false;
	}
	function getOveralldriverRating($driver_id, $countryCode){
		$query = "SELECT COUNT(id) as ride_count, IFNULL(SUM(drive_comfort_star), 0) as avg FROM {$this->db->dbprefix('multiple_rating')}  WHERE user_id = ".$driver_id."  GROUP BY id ";
		$q = $this->db->query($query);
		
		if ($q->num_rows() > 0) {
			$row = $q->row();
			$total_star = $row->ride_count * 5;
			$row->avg = round((($row->avg / $total_star) * 5), 1);
			$data = (string)$row->avg;
				
			
			return $data;	
		}
		return 0;
	}
	function findLocation($latitude, $longitude, $countryCode){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
	function findLocationWEB($latitude, $longitude, $countryCode){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&latlng='.$geolocation.'&sensor=false';
		
		
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		//print_r($json_decode);
		//die; 
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
	
	function findLocationPINCODE1($latitude, $longitude, $countryCode){
		
		$geocodeFromLatlon = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&latlng='.$latitude.','.$longitude.'&sensor=false');
        $output2 = json_decode($geocodeFromLatlon);
		
        if(!empty($output2)){
            $addressComponents = $output2->results[0]->address_components;
            foreach($addressComponents as $addrComp){
                if($addrComp->types[0] == 'postal_code'){
                    //Return the zipcode
                    return $addrComp->long_name;
                }
            }
            return false;
        }else{
            return false;
        }
			
	}
	
	function findLocationPINCODE($latitude, $longitude, $countryCode){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(!empty($json_decode)){
			
			$json_decode = array_map('address_components', $json_decode);
			return $json_decode;
		}
		return false;
	}
	
	function GetDrivingDistance_waypoints($lat1, $long1, $lat2, $long2, $type, $countryCode)
	{
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		//print_r($response_a);die;
		$dist = str_replace(',', '.', $response_a['rows'][0]['elements'][0]['distance']['text']);
		$dist_val = $response_a['rows'][0]['elements'][0]['distance']['value']/1000;
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
		
		if($type == 'K'){
			$value = $dist;
		}elseif($type == 'Km'){
			$value = $dist_val;
		}elseif($type == 'Time'){
			$value = $time;
		}
		return $value;
	}
	
	//https://maps.google.com/maps/api/js?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&sensor=false&libraries=geometry
	
	function shortDistance($lat1, $long1, $lat2, $long2, $type, $countryCode)
	{
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN&sensor=false&libraries=geometry";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		//print_r($response_a);die;
		$dist = str_replace(',', '.', $response_a['rows'][0]['elements'][0]['distance']['text']);
		$dist_val = $response_a['rows'][0]['elements'][0]['distance']['value']/1000;
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
		
		if($type == 'K'){
			$value = $dist;
		}elseif($type == 'Km'){
			$value = $dist_val;
		}elseif($type == 'Time'){
			$value = $time;
		}
		return $value;
	}
	
	function GetDrivingDistance_New($lat1, $long1, $lat2, $long2, $type, $countryCode)
	{
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		//print_r($response_a);die;
		$dist = str_replace(',', '.', $response_a['rows'][0]['elements'][0]['distance']['text']);
		$dist_val = $response_a['rows'][0]['elements'][0]['distance']['value']/1000;
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
		
		if($type == 'K'){
			$value = $dist;
		}elseif($type == 'Km'){
			$value = $dist_val;
		}elseif($type == 'Time'){
			$value = $time;
		}
		return $value;
	}
	
    function GetDrivingDistance($lat1, $long1, $lat2, $long2, $countryCode)
	{
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
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
	
	
	function GetDrivingDistanceNew($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2, $countryCode) {
	// Calculate the distance in degrees
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
	// Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
	}
	return round($distance, $decimals);
}

function GetDrivingDistanceNew1($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2, $countryCode) {
	// Calculate the distance in degrees
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
	// Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
	}
	return round($distance, $decimals);
}
	
	function getCityFare($lat, $lng, $countryCode){
		$data = array();
		$pincode = $this->findLocationPINCODE1($lat, $lng, $countryCode);
		
		$q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id', 'left')->where('p.pincode', $pincode)->where('p.is_country', $countryCode)->get();
		if ($q->num_rows() > 0) {
            $city_id = $q->row('city_id');
			return $city_id;
        }else{
			$city_id = 0;
			return $city_id;
		}
		
		return false;
	}
	
	function getDefaultTaxDriver($countryCode){
		$this->db->select('id, tax_name, percentage');
		$this->db->from('tax');
		$this->db->where('is_default', 1);
		$this->db->where('user_type', 1);
		$this->db->where('is_country', $countryCode);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
            $percentage = $q->row();
			return $percentage;
        }else{
			$percentage = 0;
			return $percentage;
		}
	}
	
	function getDefaultTaxAdmin($countryCode){
		$this->db->select('id, tax_name, percentage');
		$this->db->from('tax');
		$this->db->where('is_default', 1);
		$this->db->where('user_type', 0);
		$this->db->where('is_country', $countryCode);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
            $percentage = $q->row();
			return $percentage;
        }else{
			$percentage = 0;
			return $percentage;
		}
	}
	
	function getFareestimate($start_lat, $start_lng, $taxi_type, $ride_type, $countryCode){
		$data = array();
		$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng, $countryCode);
		$setting = $this->get_setting($countryCode);
		$driver_percentage = $this->getDefaultTaxDriver($countryCode);
		$admin_percentage = $this->getDefaultTaxAdmin($countryCode);
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
		
		$heycab_commision = $setting->driver_admin_payment_percentage;
		
		
		$start_q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id')->where('p.pincode', $start_pincode)->get();
		
		if ($start_q->num_rows() > 0) {
            $start_city_id = $start_q->row('city_id');
        }else{
			$start_city_id = 0;
		}
		
		
		
		if($ride_type == 1){
			
			$start_time = date('Y-m-d H:i:s');
			//$start_time = date('Y-m-d H:i:s', strtotime("-10 minutes", strtotime(date('Y-m-d H:i:s'))));  
			
			if($start_city_id != 0){
				$daily_withcity = $this->db->select('*')->where('city_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
				//print_r($this->db->last_query());
				
					
				if ($daily_withcity->num_rows() > 0) {
					
					
				
					$ride_time = explode(' ', $start_time);
						$this->db->select('*');
						$this->db->where('daily_fare_id', $daily_withcity->row('id'));
						$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
						$this->db->where('is_country', $countryCode);
						$this->db->limit(1);
						$slot = $this->db->get('daily_slot');	
						//print_r($this->db->last_query());die;
						if ($slot->num_rows() > 0) {
							$slot_include_fare = $slot->row('include_fare');
							$slot_extra_fare = $slot->row('extra_fare');
							if($slot->row('type') == 1){
								$slot_type = 'peek_fare';
							}else{
								$slot_type = 'night_fare';	
							}
							
						}else{
							$slot_include_fare = 0;
							$slot_extra_fare = 0;
							$slot_type = 'empty';	
						}
					
					if($slot_type != 'empty'){
							
							$base_min_distance = $daily_withcity->row('base_min_distance');
							$base_min_distance_price = $daily_withcity->row('base_min_distance_price') + $slot_include_fare;
							$base_per_distance = $daily_withcity->row('base_per_distance');
							$base_per_distance_price = $daily_withcity->row('base_per_distance_price') + $slot_extra_fare;
						}else{
							$base_min_distance = $daily_withcity->row('base_min_distance');
							$base_min_distance_price = $daily_withcity->row('base_min_distance_price');
							$base_per_distance = $daily_withcity->row('base_per_distance');
							$base_per_distance_price = $daily_withcity->row('base_per_distance_price');
						}
						
					$fare = array(
						'min_distance' => $base_min_distance ? $base_min_distance : '0',
						'min_distance_price' => $base_min_distance_price ? $base_min_distance_price : '0',
						'per_distance' => $base_per_distance ? $base_per_distance : '0',
						'per_distance_price' => $base_per_distance_price ? $base_per_distance_price : '0',
						'driver_tax_name' => $driver_tax_name,
						'driver_tax_percentage' => $driver_tax_percentage,
						'admin_tax_name' => $admin_tax_name,
						'admin_tax_percentage' => $admin_tax_percentage,
						'heycab_commision' => $heycab_commision
						
					);
					return $fare;
					exit;
					
				}else{
					$daily_withoutcity = $this->db->select('*')->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_default', 1)->where('is_delete', 0)->get('daily_fare');
					//print_r($this->db->last_query());die;
					
					
					
					
					if ($daily_withoutcity->num_rows() > 0) {
						
						$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withoutcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');	
									
					if ($slot->num_rows() > 0) {
						$slot_include_fare = $slot->row('include_fare');
						$slot_extra_fare = $slot->row('extra_fare');
						if($slot->row('type') == 1){
							$slot_type = 'peek_fare';
						}else{
							$slot_type = 'night_fare';	
						}
						
					}else{
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
					}
						
						if($slot_type != 'empty'){
							$base_min_distance = $daily_withoutcity->row('base_min_distance');
							 $base_min_distance_price = $daily_withoutcity->row('base_min_distance_price') + $slot_include_fare;
							$base_per_distance = $daily_withoutcity->row('base_per_distance');
							 $base_per_distance_price = $daily_withoutcity->row('base_per_distance_price') + $slot_extra_fare;
						}else{
							$base_min_distance = $daily_withoutcity->row('base_min_distance');
							$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
							$base_per_distance = $daily_withoutcity->row('base_per_distance');
							$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
						}
						
						
						$fare = array(
							'min_distance' => $base_min_distance ? $base_min_distance : '0',
							'min_distance_price' => $base_min_distance_price ? $base_min_distance_price : '0',
							'per_distance' => $base_per_distance ? $base_per_distance : '0',
							'per_distance_price' => $base_per_distance_price ? $base_per_distance_price : '0',
							'driver_tax_name' => $driver_tax_name,
							'driver_tax_percentage' => $driver_tax_percentage,
							'admin_tax_name' => $admin_tax_name,
							'admin_tax_percentage' => $admin_tax_percentage,
							'heycab_commision' => $heycab_commision
							
						);
						
						return $fare;
						exit;
					}
				}
			}else{
				
				
				$daily_withoutcity = $this->db->select('*')->where('is_default', 1)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
				//$start_time = date('Y-m-d H:i:s');
				//$start_time = date('Y-m-d H:i:s', strtotime("-10 minutes", strtotime(date('Y-m-d H:i:s'))));  
				
				
					
				//print_r($this->db->last_query());die;
				if ($daily_withoutcity->num_rows() > 0) {
					
					$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withoutcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');	
									
					if ($slot->num_rows() > 0) {
						$slot_include_fare = $slot->row('include_fare');
						$slot_extra_fare = $slot->row('extra_fare');
						if($slot->row('type') == 1){
							$slot_type = 'peek_fare';
						}else{
							$slot_type = 'night_fare';	
						}
						
					}else{
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
					}
					
					if($slot_type != 'empty'){
						$base_min_distance = $daily_withoutcity->row('base_min_distance');
						$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price') + $slot_include_fare;
						$base_per_distance = $daily_withoutcity->row('base_per_distance');
						$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price') + $slot_extra_fare;
					}else{
						$base_min_distance = $daily_withoutcity->row('base_min_distance');
						$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
						$base_per_distance = $daily_withoutcity->row('base_per_distance');
						$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					}
					$fare = array(
						'min_distance' => $base_min_distance ? $base_min_distance : '0',
						'min_distance_price' => $base_min_distance_price ? $base_min_distance_price : '0',
						'per_distance' => $base_per_distance ? $base_per_distance : '0',
						'per_distance_price' => $base_per_distance_price ? $base_per_distance_price : '0',
						'driver_tax_name' => $driver_tax_name,
						'driver_tax_percentage' => $driver_tax_percentage,
						'admin_tax_name' => $admin_tax_name,
						'admin_tax_percentage' => $admin_tax_percentage,
						'heycab_commision' => $heycab_commision
						
					);
					
					return $fare;
					exit;
				}
			}			
						
		}else{
		
		$fare = array(
			'min_distance' => '0',
			'min_distance_price' => '0',
			'per_distance' => '0',
			'per_distance_price' => '0',	
			'driver_tax_name' => $driver_tax_name,
			'driver_tax_percentage' => $driver_tax_percentage,
			'admin_tax_name' => $admin_tax_name,
			'admin_tax_percentage' => $admin_tax_percentage,
			'heycab_commision' => $heycab_commision
		);
				
		return $fare;
		exit;
		}
	}
	
	
	function getFare($customer_id, $ride_type, $outstation_type, $outstation_way, $taxi_type, $start_lat, $start_lng, $end_lat, $end_lng, $start_time, $end_time, $estimate_distance, $actual_distance, $total_distance, $waiting_time, $countryCode){
		$data = array();
		$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng, $countryCode);
		$end_pincode = $this->findLocationPINCODE1($end_lat, $end_lng, $countryCode);
		
		$start_q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id')->where('p.pincode', $start_pincode)->get();
		
		if ($start_q->num_rows() > 0) {
            $start_city_id = $start_q->row('city_id');
        }else{
			$start_city_id = 0;
		}
		
		
		$end_q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id')->where('p.pincode', $end_pincode)->get();
		
		
		if ($end_q->num_rows() > 0) {
            $end_city_id = $start_q->row('city_id');
        }else{
			$end_city_id = 0;
		}
		
		if($ride_type == 1){
			
			if($start_city_id != 0){
				
				$daily_withcity = $this->db->select('*')->where('city_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withcity->num_rows() > 0) {
					
					$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');					
					if ($slot->num_rows() > 0) {
						$slot_include_fare = $slot->row('include_fare');
						$slot_extra_fare = $slot->row('extra_fare');
						$slot_waiting_price = $slot->row('waiting_price');
						if($slot->row('type') == 1){
							$slot_type = 'peek_fare';
						}else{
							$slot_type = 'night_fare';	
						}
						
					}else{
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
						$slot_waiting_price = 0;
					}
					
					$fare_waiting = $daily_withcity->row('base_waiting_minute');
					$waiting_fare = $daily_withcity->row('base_waiting_price');
					
					if($fare_waiting <= $waiting_time){
						$actual_waiting_fare = round($waiting/$fare_waiting) * $waiting_fare;
						$waiting_price = round($waiting/$fare_waiting) * $slot_waiting_price;
						
					}else{
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					}
					
					
					
					$base_min_distance = $daily_withcity->row('base_min_distance');
					$base_min_distance_price = $daily_withcity->row('base_min_distance_price');
					$base_per_distance = $daily_withcity->row('base_per_distance');
					$base_per_distance_price = $daily_withcity->row('base_per_distance_price');
			
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price );
							$actual_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							
							$total_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						
							if($slot_type != 'empty'){
								$estimate_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$actual_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$total_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$round_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
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
				}else{
					$daily_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
					
					
					
					if ($daily_withoutcity->num_rows() > 0) {
						$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withoutcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');					
					if ($slot->num_rows() > 0) {
						$slot_include_fare = $slot->row('include_fare');
						$slot_extra_fare = $slot->row('extra_fare');
						$slot_waiting_price = $slot->row('waiting_price');
						if($slot->row('type') == 1){
							$slot_type = 'peek_fare';
						}else{
							$slot_type = 'night_fare';	
						}
						
					}else{
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
						$slot_waiting_price = 0;
					}
					
					$fare_waiting = $daily_withoutcity->row('base_waiting_minute');
					$waiting_fare = $daily_withoutcity->row('base_waiting_price');
					
					if($fare_waiting <= $waiting_time){
						$actual_waiting_fare = round($waiting/$fare_waiting) * $waiting_fare;
						$waiting_price = round($waiting/$fare_waiting) * $slot_waiting_price;
					}else{
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					}
					
					$base_min_distance = $daily_withoutcity->row('base_min_distance');
					$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						
							if($slot_type != 'empty'){
								$estimate_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$actual_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$total_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$round_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
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
			}else{
				
				
				
				$daily_withoutcity = $this->db->select('*')->where('city_id', 0)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withoutcity->num_rows() > 0) {
					
					$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withoutcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');					
					if ($slot->num_rows() > 0) {
						$slot_include_fare = $slot->row('include_fare');
						$slot_extra_fare = $slot->row('extra_fare');
						$slot_waiting_price = $slot->row('waiting_price');
						if($slot->row('type') == 1){
							$slot_type = 'peek_fare';
						}else{
							$slot_type = 'night_fare';	
						}
						
					}else{
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_type = 'empty';	
						$slot_waiting_price = 0;
					}
					
					$fare_waiting = $daily_withoutcity->row('base_waiting_minute');
					$waiting_fare = $daily_withoutcity->row('base_waiting_price');
					
					if($fare_waiting <= $waiting_time){
						$actual_waiting_fare = round($waiting/$fare_waiting) * $waiting_fare;
						$waiting_price = round($waiting/$fare_waiting) * $slot_waiting_price;
					}else{
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					}
					
					$base_min_distance = $daily_withoutcity->row('base_min_distance');
					$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($estimate_distance - $base_min_distance) * $slot_extra_fare) + (($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $slot_extra_fare) + (($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						
							if($slot_type != 'empty'){
								$estimate_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$actual_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$total_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
								$round_fare = round($slot_include_fare + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
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
						
		}elseif($ride_type == 2){
			if($start_city_id != 0){
				$rental_withcity = $this->db->select('*')->where('city_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
				if ($rental_withcity->num_rows() > 0) {
					
					$package_name = $rental_withcity->row('package_name');
					$package_price = $rental_withcity->row('package_price');
					$package_distance = $rental_withcity->row('package_distance');
					$package_time = $rental_withcity->row('package_time');
					$per_distance = $rental_withcity->row('per_distance');
					$per_distance_price = $rental_withcity->row('per_distance_price');
					$per_time = $rental_withcity->row('per_time');
					$per_time_price = $rental_withcity->row('per_time_price');
					$option_price = $rental_withcity->row('option_price');
					$option_type = $rental_withcity->row('option_type');
					$time_type = $rental_withcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
				}else{
					$rental_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
					if ($rental_withoutcity->num_rows() > 0) {
					
					$package_name = $rental_withoutcity->row('package_name');
					$package_price = $rental_withoutcity->row('package_price');
					$package_distance = $rental_withoutcity->row('package_distance');
					$package_time = $rental_withoutcity->row('package_time');
					$per_distance = $rental_withoutcity->row('per_distance');
					$per_distance_price = $rental_withoutcity->row('per_distance_price');
					$per_time = $rental_withoutcity->row('per_time');
					$per_time_price = $rental_withoutcity->row('per_time_price');
					$option_price = $rental_withoutcity->row('option_price');
					$option_type = $rental_withoutcity->row('option_type');
					$time_type = $rental_withoutcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					}
				}
			}else{
				$rental_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
				if ($rental_withoutcity->num_rows() > 0) {
					
					
					
					$package_name = $rental_withoutcity->row('package_name');
					$package_price = $rental_withoutcity->row('package_price');
					$package_distance = $rental_withoutcity->row('package_distance');
					$package_time = $rental_withoutcity->row('package_time');
					$per_distance = $rental_withoutcity->row('per_distance');
					$per_distance_price = $rental_withoutcity->row('per_distance_price');
					$per_time = $rental_withoutcity->row('per_time');
					$per_time_price = $rental_withoutcity->row('per_time_price');
					$option_price = $rental_withoutcity->row('option_price');
					$option_type = $rental_withoutcity->row('option_type');
					$time_type = $rental_withoutcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					
				}
			}
		}elseif($ride_type == 3){
			if($start_city_id != 0){
				$outstation_withcity = $this->db->select('*')->where('from_city_id', $start_city_id)->where('is_country', $countryCode)->where('to_city_id', $end_city_id)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withcity->num_rows() > 0) {
					
					
					$is_oneway = $outstation_withcity->row('is_oneway');
					$is_twoway = $outstation_withcity->row('is_twoway');
					$package_name = $outstation_withcity->row('package_name');
					$oneway_package_price = $outstation_withcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withcity->row('twoway_package_price');
					$min_per_distance = $outstation_withcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withcity->row('min_per_distance_price');
					$per_distance = $outstation_withcity->row('per_distance');
					$per_distance_price = $outstation_withcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
				}else{
					
					$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}	
				}
			}else{
				$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}
				
				
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
	
    public function get_setting($countryCode) {
        $this->db->select('*');
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;	
    }
	
	public function get_driver($driver_id, $countryCode) {
		
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, up.first_name, up.last_name, up.gender, u.photo, dcs.mode, dcs.current_latitude, dcs.current_longitude');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id 

', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id ', 'left');
		$this->db->where('u.id', $driver_id);
		$this->db->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function get_customer($customer_id, $countryCode) {
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, up.first_name, up.last_name, up.gender, u.photo');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id

', 'left');
		$this->db->where('u.id', $customer_id);
		$this->db->where('u.is_country', $countryCode);
		
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function get_user($user_id, $countryCode) {
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, u.first_name, u.last_name, u.photo');
		$this->db->from('users u');
		$this->db->where('u.id', $user_id);
		$this->db->where('u.is_country', $countryCode);
		
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
			
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getUserPic($user_id){
		$this->db->select('photo');
		$this->db->where('id', $user_id);
		$this->db->limit(1);
		$q = $this->db->get('users');
		if ($q->num_rows() == 1) {
			return $q->row('photo');
    	}
		return false;
   }
	
	
   public function getSocketID($user_id, $user_type, $countryCode){
		$this->db->select('socket_id');
		$this->db->where('user_id', $user_id);
		$this->db->where('user_type', $user_type);
		//$this->db->where('is_country', $countryCode);
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
	
	public function getcountryCodeID($countryCode) {
		
        $q = $this->db->get_where('countries', array('iso' => $countryCode), 1);
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
	
	function getAllCountrieswithflags($countryCode){
		$flags_path = base_url('assets/uploads/');
		//$this->db->where('is_country', $countryCode);
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

    public function getUser($id = NULL, $countryCode = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id, 'is_country' => $countryCode), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
    public function getAllCurrencies($countryCode) {
		$this->db->where('is_country', $countryCode);
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllGroups() {
		
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllTypes($countryCode) {
		$this->db->where('is_country', $countryCode);
        $q = $this->db->get('taxi_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getCurrencyByCode($code, $countryCode) {
        $q = $this->db->get_where('currencies', array('code' => $code, 'is_country' => $countryCode), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
    public function getExchangeCurrency($id, $countryCode) {
		
 	$this->db->select('symbol');
	$this->db->where('is_country', $countryCode);
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
    
	
    public function getCurrencyByID($id, $countryCode) {
        $q = $this->db->get_where('currencies', array('id' => $id, 'is_country' => $countryCode), 1);
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
    function getAllTaxiTypes($countryCode){
	$this->db->where('is_country', $countryCode);
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
	function getAllVendor($countryCode){
		
	$this->db->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name");
	$this->db->join('user_profile up', 'up.user_id = users.id');
	$this->db->where('users.group_id', $this->Vendor);
	if($is_country != ''){
	$this->db->where('users.is_country', $countryCode);
	}
	$q = $this->db->get('users');
	
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	function Getnotification($user_id, $type, $countryCode){
		$this->db->select('*');
		$this->db->where('is_read', 0);
		//$this->db->where('user_type', $type);
		$this->db->where('user_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('notification');
		 if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	function get_booking_cancel_notification($countryCode){
		$this->db->select('b.ticket_code, b.ride_id, b.driver_id, b.customer_id, b.is_country');
		$this->db->from('bookingcrm_notification bn');
		$this->db->join('bookingcrm b', 'b.id = bn.bookingcrm_id');
		$this->db->where('bn.is_read', 0);
		$this->db->where('bn.cancel_notification', 1);
		if(!empty($countryCode)){
		$this->db->where('bn.is_country', $countryCode);
		}
		$q = $this->db->get();
		 if ($q->num_rows() > 0) {
			 $b = 0;
            foreach (($q->result()) as $row) {
                $data['result'][] = $row;
				
				$b++;
            }
			$data['booking_count'] = $b;
            return $data;
        }
        return FALSE;
	}
	
	
    
	function insertNotification($data, $countryCode){
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode ));
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	function getVendorIDBY($user_id, $countryCode){
		$q = $this->db->get_where('users', array('id' => $user_id, 'group_id' => $this->Vendor, 'is_country' => $countryCode));
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function exituserRide($mobile, $phonecode){
		$this->db->select('u.id');
		$this->db->from('users u');
		$this->db->join('rides r', 'r.customer_id = u.id AND (r.status = 1 OR r.status = 2 OR r.status = 3 OR r.status = 9 OR r.status = 10)');
		$this->db->where('u.country_code', $phonecode);
		$this->db->where('u.mobile', $mobile);
		$q = $this->db->get();
		//print_r($this->db->last_query());
		if($q->num_rows()>0){
			return json_encode(array(
				'valid' => false,
			));
		}else{
			return json_encode(array(
				'valid' => true,
			));	
		}
		
	}
	
	function exitUser($mobile, $phonecode){
		$this->db->select('u.first_name');
		$this->db->from('users u');
		$this->db->where('u.country_code', $phonecode);
		$this->db->where('u.mobile', $mobile);
		$q = $this->db->get();
		//print_r($this->db->last_query());
		if($q->num_rows()>0){
			
			return json_encode(array(
				'name' => $q->row('first_name'),
			));
			
		}else{
			return json_encode(array(
				'name' => '',
			));	
		}
		
	}
	
	function getAdminUser($is_country, $group_id){
		$q = $this->db->select('id')->where('is_country', $is_country)->where('group_id', $group_id)->get('users');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return 0;
	}
	
	function getcashBank($company_id, $countryCode){
		$q = $this->db->select('bank_id')->where('company_id', $company_id)->where('is_country', $countryCode)->where('bank_type', 1)->get('company_bank');
		if($q->num_rows()>0){
			return $q->row('bank_id');
		}
		return 0;
	}
	
	function adminUserDebit($is_country, $group_id, $type, $paid_amount, $user_id, $transaction_no){
		
		
		$admin_user = $this->getAdminUser($is_country, $group_id);
		$account_array = array(
			'type' => $type,
			'debit' => $paid_amount,
			'account_date'	=> date('Y-m-d H:i:s'),
			'account_transaction_no' => $transaction_no,
			'account_transaction_date' => date('Y-m-d H:i:s'),
			'user_id' => $admin_user,
			'user_type' => 0,
			'account_verify' => 1,
			'account_verify_on' => date('Y-m-d H:i:s'),
			'account_verify_by' => $user_id,
			'is_country' => $is_country
		 );
		 
		 $payment_array = array(
			'method' => 8,
			'user_id' => $admin_user,
			'amount' => $paid_amount,
			'payment_transaction_id' => $transaction_no,
			'transaction_status' => 'success',
			'transaction_type' => 'Debit',
			'gateway_id' => 0,
			'created_on' => date('Y-m-d H:i:s'),
			'is_country' => $is_country
		);
		
		$wallet_array = array(
			'user_id' =>  $admin_user,
			'user_type' => 0,
			'wallet_type' => 1,
			'flag' => 5,
			'flag_method' => 14,
			'cash' => $paid_amount,
			'description' => 'Transfer Money - Backend',
			'created' => date('Y-m-d H:i:s'),
			'is_country' => $is_country
		);
		
		if(!empty($account_array)){
			$this->db->insert('account', $account_array);
			
			$account_id = $this->db->insert_id();
			if($group_id == 1 || $group_id == 2){
				$this->db->insert('wallet', $wallet_array);
				if($wallet_id = $this->db->insert_id()){
					$payment_array['method_id'] = $wallet_id;
					$this->db->insert('multiple_gateway', $payment_array);
					$this->db->update('account', array('type_id' => $wallet_id, 'account_status' => 3), array('id' => $account_id));
				}
			}
			return true;
		}
		
		return false;	
	}
	
	function adminUserCredit($is_country, $group_id, $type, $paid_amount, $user_id, $transaction_no){
		
		
		$admin_user = $this->getAdminUser($is_country, $group_id);
		$account_array = array(
			'type' => $type,
			'debit' => $paid_amount,
			'account_date'	=> date('Y-m-d H:i:s'),
			'account_transaction_no' => $transaction_no,
			'account_transaction_date' => date('Y-m-d H:i:s'),
			'user_id' => $admin_user,
			'user_type' => 0,
			'account_verify' => 1,
			'account_verify_on' => date('Y-m-d H:i:s'),
			'account_verify_by' => $user_id,
			'is_country' => $is_country
		 );
		 
		 $payment_array = array(
			'method' => 8,
			'user_id' => $admin_user,
			'amount' => $paid_amount,
			'payment_transaction_id' => $transaction_no,
			'transaction_status' => 'success',
			'transaction_type' => 'Credit',
			'gateway_id' => 0,
			'created_on' => date('Y-m-d H:i:s'),
			'is_country' => $is_country
		);
		
		$wallet_array = array(
			'user_id' =>  $admin_user,
			'user_type' => 0,
			'wallet_type' => 1,
			'flag' => 3,
			'flag_method' => 13,
			'cash' => $paid_amount,
			'description' => 'Refunded Money - Backend',
			'created' => date('Y-m-d H:i:s'),
			'is_country' => $is_country
		);
		
		if(!empty($account_array)){
			$this->db->insert('account', $account_array);
			
			$account_id = $this->db->insert_id();
			if($group_id == 1 || $group_id == 2){
				$this->db->insert('wallet', $wallet_array);
				if($wallet_id = $this->db->insert_id()){
					$payment_array['method_id'] = $wallet_id;
					$this->db->insert('multiple_gateway', $payment_array);
					$this->db->update('account', array('type_id' => $wallet_id, 'account_status' => 3), array('id' => $account_id));
				}
			}
			return true;
		}
		
		return false;	
	}
	
	function Ridewallet_new($admin_account_array, $admin_payment_array, $admin_wallet_array, $driver_account_array, $driver_payment_array, $driver_wallet_array){
		
		if(!empty($admin_account_array) && !empty($driver_account_array)){
			
			if(!empty($admin_account_array)){			
				$this->db->insert('account', $admin_account_array);			
				$admin_account_id = $this->db->insert_id();			
				$this->db->insert('wallet', $admin_wallet_array);
				if($admin_wallet_id = $this->db->insert_id()){
					$payment_array['method_id'] = $admin_wallet_id;
					$this->db->insert('multiple_gateway', $admin_payment_array);
					$this->db->update('account', array('type_id' => $wallet_id, 'account_status' => 3), array('id' => $admin_account_id));
				}
			}
			
			if(!empty($driver_account_array)){			
				$this->db->insert('account', $driver_account_array);			
				$driver_account_id = $this->db->insert_id();			
				$this->db->insert('wallet', $driver_wallet_array);
				if($driver_wallet_id = $this->db->insert_id()){
					$payment_array['method_id'] = $driver_wallet_id;
					$this->db->insert('multiple_gateway', $driver_payment_array);
					$this->db->update('account', array('type_id' => $driver_wallet_id, 'account_status' => 3), array('id' => $driver_account_id));
				}
			}
			
			return true;
		}
		return false;	
	}
	
	function updateLastride($user_id){
		$this->db->select('ride_stop');
		$this->db->where('user_id', $user_id);			
		$q = $this->db->get('user_setting');
		if($q->num_rows()>0){
			if($q->row('ride_stop') == 1){
				$this->db->update('user_setting', array('ride_stop' => 2), array('user_id' => $user_id));
				return true;
			}
		}
		return false;
	}
	
	function stopRide($user_id){
		
		$this->db->select('ride_stop');
		$this->db->where('user_id', $user_id);			
		$q = $this->db->get('user_setting');
		if($q->num_rows()>0){
			if($q->row('ride_stop') == 1){
				return '1';
			}elseif($q->row('ride_stop') == 2){
				return '1';
			}elseif($q->row('ride_stop') == 0){
				return '0';
			}
			
		}
		
		return '0';
	}
	
	function checkRide($user_id, $user_type){
		if($user_type == 1){
			$this->db->select('id');
			$this->db->where('customer_id', $user_id);
			$this->db->where('status', 3);
			$q = $this->db->get('rides');
			if($q->num_rows()>0){
				return $q->row('id');
			}
		}elseif($user_type == 2){
			$this->db->select('id');
			$this->db->where('driver_id', $user_id);
			$this->db->where('status', 3);
			$q = $this->db->get('rides');
			if($q->num_rows()>0){
				return $q->row('id');
			}
		}
		return 0;
	}
    
	function getAccountPendingCash($countryCode, $account_ids){
		$this->db->select('SUM(debit) as amount');
		$this->db->where('is_country', $countryCode);
		$this->db->where_in('id', $account_ids);
		$q = $this->db->get('account');
		if($q->num_rows()>0){
			return $q->row('amount');
		}
		return 0;
	}
	
	function checkCompanytype($company_id){
		$q = $this->db->select('is_office')->where('id', $company_id)->get('company');
		if($q->num_rows()>0){
			return $q->row('is_office');
		}
		return 0;
	}
	
	function onlineBank($is_country, $payment_gateway){
		$q = $this->db->select('bank_id')->where('id', $payment_gateway)->get('payment_gateway');
		if($q->num_rows()>0){
			return $q->row('bank_id');
		}
		return 0;
	}
	
	function getUserCompany($is_country, $is_office){
		$q = $this->db->select('id')->where('is_country', $is_country)->where('is_office', $is_office)->get('company');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return 0;
	}
	
	function offlineBank($is_country, $company_id){
		$q = $this->db->select('bank_id')->from('company_bank')->where('company_id', $company_id)->where('bank_type', 1)->where('is_country', $is_country)->limit(1)->get();
		if($q->num_rows()>0){
			return $q->row('bank_id');
		}
		return 0;
	}
	
	function walletRide($wallet_array){
		$q = $this->db->insert_batch('wallet', $wallet_array);
		if($q){
			return true;			
		}
		return false;
	}
	
	function nextRide($driver_id){
		$image_path = base_url('assets/uploads/');
		if($driver_id){
			$this->db->select('r.*,  d.first_name driver_name, d.email, d.mobile, d.country_code as driver_country_code, d.gender, d.photo as driver_photo, d.photo driver_photo, c.first_name customer_name, c.mobile customer_mobile, c.country_code customer_country_code,  t.name taxi_name, 	t.color, t.model, t.number, t.type, tt.name types');		
			$this->db->from('rides r');
			$this->db->join('users d', 'd.id = r.driver_id', 'left');
			$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
			$this->db->join('users c', 'c.id = r.customer_id', 'left');
			$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
			$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
			
			$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
			$this->db->where('r.driver_id', $driver_id);
			$this->db->where('r.status', 10);
			
			$q = $this->db->get();
			
				if($q->num_rows() > 0){
					$this->db->where('status', 10);
					$this->db->where('driver_id', $driver_id);
					$this->db->update('rides', array('status' => 2));
					
					$row = $q->row();
					
					if($row->customer_photo !=''){
						$row->customer_photo = $cus_path.$row->customer_photo;
					}else{
						$row->customer_photo = $image_path.'no_image.png';
					}
					
					$data_value = array(
						'id' => $row->id,
						'pick_up' => $row->start ? $row->start : '0',
						'drop_off' => $row->end ? $row->end : '0',
						'pick_lat' => $row->start_lat ? $row->start_lat : '0',
						'pick_lng' => $row->start_lng ? $row->start_lng : '0',
						'drop_lat' => $row->end_lat ? $row->end_lat : '0',
						'drop_lng' => $row->end_lng ? $row->end_lng : '0',
						'distance_km' => $row->distance_km ? $row->distance_km : '0',
						'distance_price' => $row->distance_price ? $row->distance_price : '0',
						'payment_name' => $row->payment_name ? $row->payment_name : '0',
						'payment_id' => $row->payment_id ? $row->payment_id : '0',
						'customer_name' => $row->customer_name ? $row->customer_name : '0',
						'customer_mobile' => $row->customer_mobile ? $row->customer_mobile : '0',
						'customer_country_code' => $row->customer_country_code ? $row->customer_country_code : '0',
						'customer_photo' => $row->customer_photo ? $row->customer_photo : '0'
					);
					
					return $data_value;
				}
			}
		return false;	
	}
	
	function bookingEmitDriverinsert($ride_id, $driver_id, $customer_id){
		$q = $this->db->insert('emit', array('ride_id' => $ride_id, 'driver_id' => $driver_id, 'customer_id' => $customer_id, 'emit_type' => 1, 'emit_status' => 0, 'created_on' => date('Y-m-d H:i:s')));
		if($q){
			return true;
		}
		return false;
	}
	
	function bookingEmitDriverupdate($ride_id, $driver_id, $customer_id){
		$this->db->where('ride_id', $ride_id);
		$this->db->where('driver_id', $driver_id);
		$this->db->where('customer_id', $customer_id);
		$this->db->where('emit_status', 0);
		$q = $this->db->update('emit', array('emit_status' => 1, 'created_on' => date('Y-m-d H:i:s')));
		if($q){
			return true;
		}
		return false;
	}
	function bookingEmitDriverpending($driver_id){
		
		$this->db->select('e.ride_id, e.customer_id as customer_id, r.cab_type_id, r.start_lat, r.start_lng, r.end_lat, r.end_lng, r.is_country, r.status');
		$this->db->from('emit e');
		$this->db->join('rides r', 'r.id = e.ride_id');
		$this->db->where('e.driver_id', $driver_id);
		$this->db->where('e.emit_status', 0);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function taxiServicesInsert($driver_id, $taxi_id, $taxi_type, $countryCode){
		$this->db->delete('taxi_services', array('user_id' => $driver_id, 'taxi_id' => $taxi_id, 'is_edit' => 1));
		
		$q = $this->db->insert('taxi_services', array('user_id' => $driver_id, 'taxi_id' => $taxi_id, 'taxi_type' => $taxi_type, 'status' => 1, 'is_edit' => 1, 'created_on' => date('Y-m-d'), 'is_country' => $countryCode));
		if($q){
			return true;
		}
		return false;	
	}
	
	function orderCheckTypeimage($services_id, $type, $is_up_down, $is_order){
		$this->db->select('tm.is_up_down, tm.is_order');
		$this->db->from('taxi_type tt');
		$this->db->join('taxi_image tm', 'tm.id = tt.taxi_image_id');
		$this->db->where('tt.id', $type);
		$q = $this->db->get();
		if($q->num_rows()>0){
			if($services_id == $type){
				return true;
			}elseif($services_id != $type){
				if($is_order > $q->row('is_order')){
					return true;
				}
			}
		}
		return false;	
	}
	
	
	public function newBookingRideGET($ride_id){
		$this->db->select('id as booking_id, booking_no, payment_id, payment_name, cab_type_id, customer_id, booked_type, status, booked_on, booking_timing, ride_timing, ride_timing_end, ride_type, start, start_lat, start_lng, end, end_lat, end_lng, distance_km, distance_price, is_country');
		$this->db->from('rides');
		$this->db->where('id', $ride_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$row = $q->row();
			$time = $this->site->GetDrivingDistance_waypoints($row->start_lat, $row->start_lng,$row->end_lat, $row->end_lng, 'Time', $row->is_country);
			$row->taxi_type = $this->site->taxitypename($row->cab_type_id);
			$row->ride_waiting_time = $time;
									
			$row->waypoint = $this->getWaypoint($ride_id);
			return $row;
		}
		return false;
	}
	public function getWaypoint($ride_id) {
		$res = array();
		$this->db->select('id as waypoint_id, start, start_lat, start_lng, end, end_lat, end_lng');
		$this->db->where('ride_id', $ride_id);
        $q = $this->db->get('ride_waypoints');
		//print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				$row->reached_status = $this->checkWaypoint($ride_id, $row->waypoint_id);
                $data[] = $row;
            }
            return $data;
        }
        return $res;
    }
	
	function checkWaypoint($ride_id, $waypoint_id){
		$this->db->select('id');
		$this->db->where('ride_id', $ride_id);
		$this->db->where('waypoint_id', $waypoint_id);
		$this->db->where('type', 1);
		$q = $this->db->get('ride_waypoint_time');
		if ($q->num_rows() > 0) {
			return 1;
		}
		return 0;
	}
	
	public function waypointreached_time($ride_id){
		$this->db->select('waypoint_time');
		$this->db->from('ride_waypoint_time');
		$this->db->where('type',1);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->row('waypoint_time');
		}
		return false;
		
	}
	
	public function cancelLimit($type, $user_id, $no_of_cancel, $current_date){
		$this->db->select('count(id) as cancel_count');
		$this->db->from('rides');
		$this->db->where('cancel_status !=',0);
		$this->db->where('cancel_free_status', 0);
		$this->db->where('cancelled_by',$user_id);
		$this->db->where('DATE(booked_on)', $current_date);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 //if($no_of_cancel > $q->row('cancel_count')){
				$limit = $q->row('cancel_count');
			 //}else{
				 //$limit = 0;
			 //}
			 return $limit;
		}
		return false;
		
	}
	
	
	function traficWaiting($ride_id, $waiting_time, $waiting_charges, $trafic_distance){
		$this->db->select('location');
		$this->db->from('driver_frequency');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 $d = explode(',', $q->row('location'));
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
			
			for($i=0; $i<count($d1); $i++){
				if($d3[$i] == 3){
					if(!empty($d1[$i+1]) && $d2[$i+1]){
						$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i], 'start_time' => $d4[$i], 'end_time' => $d4[$i+1]);			
					}else{
						
					}
				}
			}
			foreach($result as $res){			
				$distance[] = array('meter' => round($this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']) * 1000), 'second' => strtotime(str_replace('"', '', $res['end_time'])) - strtotime(str_replace('"', '', $res['start_time'])));	
				
			}
			
			$second = 0;
			foreach($distance as $value){
				
				if($value['meter'] <= $trafic_distance){
					$second += $value['second'];
				}
			}
			//$waiting_min = $waiting_time * 60;
			//$traficminutes = round($second / $waiting_min, 2);
			//$traficminutes_fare = $traficminutes * $waiting_charges;
			//return number_format($traficminutes_fare, 2);
			
			
			/*$minutes = floor($second/60);
			$secondsleft = $seconds%60;
			if($minutes<10){
				$minutes = "0" . $minutes;
			}
			if($secondsleft<10){
				$secondsleft = "0" . $secondsleft;
			}
			*/
			
			$t_minutes = floor($second/60);
			$t_secondsleft = $second%60;			
			$traficminutes_fare = $t_minutes * $waiting_charges;
			if($t_minutes<10){
				$t_minutes = "0" . $t_minutes;
			}
			if($t_secondsleft<10){
				$t_secondsleft = "0" . $t_secondsleft;
			}
			
			$trafic_wait = array(
				'trafic_min' => $t_minutes.':'.$t_secondsleft,
				'trafic_wait_fare' => number_format($traficminutes_fare, 2)
			);
			return $trafic_wait;
		}
		return '0.00';	
	}
	
	function traficWaitingTEST($ride_id, $waiting_time, $waiting_charges, $trafic_distance){
		$this->db->select('location');
		$this->db->from('driver_frequency');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 $d = explode(',', $q->row('location'));
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
			
			for($i=0; $i<count($d1); $i++){
				if($d3[$i] == 3){
					if(!empty($d1[$i+1]) && $d2[$i+1]){
						$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i], 'start_time' => $d4[$i], 'end_time' => $d4[$i+1]);			
					}else{
						
					}
				}
			}
			print_r($result);
			foreach($result as $res){			
				$distance[] = array('meter' => round($this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']) * 1000), 'second' => strtotime(str_replace('"', '', $res['end_time'])) - strtotime(str_replace('"', '', $res['start_time'])));	
				
			}
			
			$second = 0;
			foreach($distance as $value){
				echo $value['meter'].'<br>';
				echo $trafic_distance.'<br>';
				if($value['meter'] <= $trafic_distance){
					$second += $value['second'];
				}
			}
			//$waiting_min = $waiting_time * 60;
			//$traficminutes = round($second / $waiting_min, 2);
			//$traficminutes_fare = $traficminutes * $waiting_charges;
			//return number_format($traficminutes_fare, 2);
			
			
			/*$minutes = floor($second/60);
			$secondsleft = $seconds%60;
			if($minutes<10){
				$minutes = "0" . $minutes;
			}
			if($secondsleft<10){
				$secondsleft = "0" . $secondsleft;
			}
			*/
			
			$t_minutes = floor($second/60);
			$t_secondsleft = $second%60;			
			$traficminutes_fare = $t_minutes * $waiting_charges;
			if($t_minutes<10){
				$t_minutes = "0" . $t_minutes;
			}
			if($t_secondsleft<10){
				$t_secondsleft = "0" . $t_secondsleft;
			}
			
			$trafic_wait = array(
				'trafic_min' => $t_minutes.':'.$t_secondsleft,
				'trafic_wait_fare' => number_format($traficminutes_fare, 2)
			);
			return $trafic_wait;
		}
		return '0.00';	
	}
	
	
	function traficWaitingMin($ride_id, $countryCode){
		$setting = $this->get_setting($countryCode);
		$this->db->select('location');
		$this->db->from('driver_frequency');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 $d = explode(',', $q->row('location'));
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
			
			
			for($i=0; $i<count($d1); $i++){
				
				if($d3[$i] == 3){
					
					if(!empty($d1[$i+1]) && $d2[$i+1]){
						
						$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i], 'start_time' => $d4[$i], 'end_time' => $d4[$i+1]);	
						
					}else{
						
					}
				}
			}
			
			foreach($result as $res){			
				$distance[] = array('meter' => round($this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']) * 1000), 'second' => strtotime(str_replace('"', '', $res['end_time'])) - strtotime(str_replace('"', '', $res['start_time'])));	
				
			}
			
			$second = 0;
			foreach($distance as $value){
				
				if($value['meter'] <= $trafic_distance){
					$second += $value['second'];
				}
			}
			
			$waiting_time = $setting->waiting_time;
			
			$waiting_min = $waiting_time * 60;
			$traficminutes = $second / $waiting_min;
			
			return $traficminutes;
		}
		return '0';	
	}
	
	function waypointWaiting($ride_id, $waiting_time, $waiting_charges){
		$this->db->select('SUM(waypoint_total_time) as waypoint_total_time');
		$this->db->from('ride_waypoint_time');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 $waypoint_total_time = $q->row('waypoint_total_time');
			 if($waypoint_total_time > '1.00'){
				 $waypoint_total_time = $waypoint_total_time;
			 }else{
				 $waypoint_total_time = 0;
			 }
			 $waypoint_fare = $waypoint_total_time * $waiting_charges;
			 return number_format($waypoint_fare, 2);
		}
		return '0.00';	
	}
	function pickupWaiting($ride_id, $waiting_time, $waiting_charges){
		$this->db->select('timing as start_timing');
		$this->db->from('ride_route');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->where('trip_made',2);
		$this->db->limit(1);
		$s = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($s->num_rows() > 0) {
			$start_timing = $s->row('start_timing');
		}
		$this->db->select('timing as end_timing');
		$this->db->from('ride_route');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->where('trip_made',3);
		$this->db->limit(1);
		$e = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($e->num_rows() > 0) {
			$end_timing = $e->row('end_timing');
		}
		if(!empty($start_timing) && !empty($end_timing)){
			$waiting_second = round($waiting_time * 60);
			
			/*$mins = round((strtotime($end_timing) - strtotime($start_timing)) / $waiting_second,2);
			if($mins > 1){
				$mins = $mins;
			}else{
				$mins = 0;	
			}
			$pickup_fare = $mins * $waiting_charges;*/
			$second = strtotime($end_timing) - strtotime($start_timing);
			//$second = abs(round($diff / 86400)); 
			$p_minutes = floor($second/60);
			$p_secondsleft = $second%60;			
			$pickup_fare = $p_minutes * $waiting_charges;
			if($p_minutes<10){
				$p_minutes = "0" . $p_minutes;
			}
			if($p_secondsleft<10){
				$p_secondsleft = "0" . $p_secondsleft;
			}
			
			$pickup_wait = array(
				'pickup_min' => $p_minutes.':'.$p_secondsleft,
				'pickup_wait_fare' => number_format($pickup_fare, 2)
			);
			
			
			return $pickup_wait;
		}else{
			return '0.00';
		}
		
	}
	
	function CancelCabType($type_id, $countryCode){
		$this->db->select('driver_cancel_charge, no_of_driver_cancel, driver_cancel_type, customer_cancel_charge, no_of_customer_cancel, customer_cancel_type, cancel_free_second');
		$this->db->from('daily_fare');
		$this->db->where('taxi_type', $type_id);
		$this->db->where('is_country', $countryCode);
		$this->db->where('is_delete', 0);
		$this->db->where('is_default', 1);
		$this->db->limit(1);
		$e = $this->db->get();		
		//print_r($this->db->last_query());die;
		if ($e->num_rows() > 0) {
			return $e->row();
		}
		return false;
	}
	
	function CancelCabBooking($booking_id, $countryCode){
		$this->db->select('r.cab_type_id, d.driver_cancel_charge, d.no_of_driver_cancel, d.driver_cancel_type, d.customer_cancel_charge, d.no_of_customer_cancel, d.customer_cancel_type, d.cancel_free_second');
		$this->db->from('rides r');
		$this->db->join('daily_fare d', 'd.taxi_type = r.cab_type_id AND d.is_country = "'.$countryCode.'" AND d.is_default = 1 AND d.is_delete = 0');
		$this->db->where('r.id', $booking_id);
		$this->db->where('r.is_country', $countryCode);
		
		$this->db->limit(1);
		$e = $this->db->get();		
		//print_r($this->db->last_query());die;
		if ($e->num_rows() > 0) {
			return $e->row();
		}
		return false;
	}
	
	function CancellationDistance($ride_id, $countryCode){
		$distance = 0;
		$setting = $this->get_setting($countryCode);
		$this->db->select('location');
		$this->db->from('driver_frequency');		
		$this->db->where('ride_id',$ride_id);		
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			 $d = explode(',', $q->row('location'));
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
			
			
			for($i=0; $i<count($d1); $i++){
				
				
					
					if(!empty($d1[$i+1]) && $d2[$i+1]){
						
						$result[] = array('start_lat' => $d1[$i], 'start_lng' => $d2[$i], 'end_lat' => $d1[$i+1], 'end_lng' => $d2[$i+1], 'status' => $d3[$i], 'start_time' => $d4[$i], 'end_time' => $d4[$i+1]);	
						
					}else{
						
					}
				
			}
			
			foreach($result as $res){			
				$distance += round($this->calcCrow($res['start_lat'], $res['start_lng'], $res['end_lat'], $res['end_lng']) * 1000);	
				
			}
			
			
			
			return $distance;
		}
		return '0';	
	}
	
	function getFareCancellation($ride_type, $taxi_type, $start_lat, $start_lng, $end_lat, $end_lng, $total_distance, $countryCode){
		$data = array();
		$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng, $countryCode);
		$end_pincode = $this->findLocationPINCODE1($end_lat, $end_lng, $countryCode);
		
		$start_q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id')->where('p.pincode', $start_pincode)->get();
		
		if ($start_q->num_rows() > 0) {
            $start_city_id = $start_q->row('city_id');
        }else{
			$start_city_id = 0;
		}
		
		
		$end_q = $this->db->select('a.city_id')->from('pincode p')->join('areas a', 'a.id = p.area_id')->where('p.pincode', $end_pincode)->get();
		
		
		if ($end_q->num_rows() > 0) {
            $end_city_id = $start_q->row('city_id');
        }else{
			$end_city_id = 0;
		}
		
		if($ride_type == 1){
			
			if($start_city_id != 0){
				
				$daily_withcity = $this->db->select('*')->where('city_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withcity->num_rows() > 0) {
					
					
					$base_per_distance = $daily_withcity->row('base_per_distance');
					$base_per_distance_price = $daily_withcity->row('base_per_distance_price');
					$total_fare = round($total_distance * $base_per_distance_price);
					
					
					
					
					$fare = array(
						
						'total_fare' => $total_fare,
						
					);
					return $fare;
				}else{
					$daily_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
					
					
					
					if ($daily_withoutcity->num_rows() > 0) {
						
						$base_per_distance = $daily_withoutcity->row('base_per_distance');
						$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
						$total_fare = round($total_distance * $base_per_distance_price);
					
					
					$fare = array(
						
						'total_fare' => $total_fare,
						
					);
						return $fare;
					}
				}
			}else{
				
				
				
				$daily_withoutcity = $this->db->select('*')->where('city_id', 0)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withoutcity->num_rows() > 0) {
					
					
					
					
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					$total_fare = round($total_distance * $base_per_distance_price);
					
					
					
					$fare = array(
						
						'total_fare' => $total_fare,
						
					);
					return $fare;	
				}
			}			
						
		}elseif($ride_type == 2){
			if($start_city_id != 0){
				$rental_withcity = $this->db->select('*')->where('city_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
				if ($rental_withcity->num_rows() > 0) {
					
					$package_name = $rental_withcity->row('package_name');
					$package_price = $rental_withcity->row('package_price');
					$package_distance = $rental_withcity->row('package_distance');
					$package_time = $rental_withcity->row('package_time');
					$per_distance = $rental_withcity->row('per_distance');
					$per_distance_price = $rental_withcity->row('per_distance_price');
					$per_time = $rental_withcity->row('per_time');
					$per_time_price = $rental_withcity->row('per_time_price');
					$option_price = $rental_withcity->row('option_price');
					$option_type = $rental_withcity->row('option_type');
					$time_type = $rental_withcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
				}else{
					$rental_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
					if ($rental_withoutcity->num_rows() > 0) {
					
					$package_name = $rental_withoutcity->row('package_name');
					$package_price = $rental_withoutcity->row('package_price');
					$package_distance = $rental_withoutcity->row('package_distance');
					$package_time = $rental_withoutcity->row('package_time');
					$per_distance = $rental_withoutcity->row('per_distance');
					$per_distance_price = $rental_withoutcity->row('per_distance_price');
					$per_time = $rental_withoutcity->row('per_time');
					$per_time_price = $rental_withoutcity->row('per_time_price');
					$option_price = $rental_withoutcity->row('option_price');
					$option_type = $rental_withoutcity->row('option_type');
					$time_type = $rental_withoutcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					}
				}
			}else{
				$rental_withoutcity = $this->db->select('*')->where('city_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
				if ($rental_withoutcity->num_rows() > 0) {
					
					
					
					$package_name = $rental_withoutcity->row('package_name');
					$package_price = $rental_withoutcity->row('package_price');
					$package_distance = $rental_withoutcity->row('package_distance');
					$package_time = $rental_withoutcity->row('package_time');
					$per_distance = $rental_withoutcity->row('per_distance');
					$per_distance_price = $rental_withoutcity->row('per_distance_price');
					$per_time = $rental_withoutcity->row('per_time');
					$per_time_price = $rental_withoutcity->row('per_time_price');
					$option_price = $rental_withoutcity->row('option_price');
					$option_type = $rental_withoutcity->row('option_type');
					$time_type = $rental_withoutcity->row('time_type');
					
					$estimate_fare = $package_price;
					$actual_fare = $package_price;
					
					if($option_type == 1){
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						$start_time = date("H:i:s",strtotime($start_time));
						$end_time = date("H:i:s",strtotime($end_time));
						
						$time1 = strtotime($start_time);
						$time2 = strtotime($end_time);
						$difference = round(abs($time2 - $time1) / 3600,2);
						
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance){
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
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
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					
				}
			}
		}elseif($ride_type == 3){
			if($start_city_id != 0){
				$outstation_withcity = $this->db->select('*')->where('from_city_id', $start_city_id)->where('is_country', $countryCode)->where('to_city_id', $end_city_id)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withcity->num_rows() > 0) {
					
					
					$is_oneway = $outstation_withcity->row('is_oneway');
					$is_twoway = $outstation_withcity->row('is_twoway');
					$package_name = $outstation_withcity->row('package_name');
					$oneway_package_price = $outstation_withcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withcity->row('twoway_package_price');
					$min_per_distance = $outstation_withcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withcity->row('min_per_distance_price');
					$per_distance = $outstation_withcity->row('per_distance');
					$per_distance_price = $outstation_withcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
				}else{
					
					$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}	
				}
			}else{
				$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					if($outstation_type == 1){
						
						if($outstation_way == 1){
							
							$estimate_fare = round($twoway_package_price);
							$actual_fare = round($twoway_package_price);
							$total_fare = round($twoway_package_price);
							$round_fare = round($twoway_package_price);
						}else{
							$estimate_fare = round($oneway_package_price);
							$actual_fare = round($oneway_package_price);
							$total_fare = round($oneway_package_price);
							$round_fare = round($oneway_package_price);
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}elseif($outstation_type == 2){
						$estimate_fare = round($estimate_distance * $per_distance_price);
						$actual_fare = round($actual_distance * $per_distance_price);
						$total_fare = round($total_distance * $per_distance_price);
						$round_fare = round($total_distance * $per_distance_price);
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					}else{
						$estimate_fare = 0;
						$actual_fare = 0;
						$total_fare = 0;
						$round_fare = 0;
						$extra_fare = 0;
						$extra_fare_details = 0;
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}
				
				
			}
				
		}
		
		$fare = array(
			
			$total_distance => 0,
			$total_fare => 0,
			
		);
		return $fare;
	}
	
	function ridedetails($ride_id, $countryCode){
		
		
		$this->db->select('r.booking_no, r.booked_type, r.payment_name, r.status, r.booked_on, r.ride_timing_end, r.ride_type, r.start, r.start_lat, r.start_lng, r.end, r.end_lat, r.end_lng, IFNULL(rp.total_distance, 0) as total_distance, IFNULL(rp.waiting_charge, 0) as waiting_charge, IFNULL(rp.total_tax_fare, 0) as total_tax_fare, IFNULL(rp.outstanding_from_last_trip, 0) as outstanding_from_last_trip, IFNULL(rp.total_toll, 0) as total_toll, IFNULL(rp.total_parking, 0) as total_parking, IFNULL(rp.total_fare, 0) as total_fare, IFNULL(rp.final_total, 0) as final_total, IFNULL(rp.driver_allowance, 0) as driver_allowance, IFNULL(rp.discount_fare, 0) as discount_fare,  IFNULL(rp.extra_fare, 0) as extra_fare, IFNULL(mr.overall, 0) as overall, IFNULL(mr.drive_comfort_star, 0) as drive_comfort_star, IFNULL(mr.booking_process_star, 0) as booking_process_star, IFNULL(mr.cab_cleanliness_star, 0) as cab_cleanliness_star, IFNULL(mr.drive_politeness_star, 0) as  drive_politeness_star, IFNULL(mr.fare_star, 0) as  fare_star, IFNULL(mr.easy_of_payment_star, 0) as easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, IFNULL(d.first_name, 0)  as dfname, IFNULL(d.last_name, 0) as dlname, IFNULL(d.country_code, 0) as dccode, IFNULL(d.mobile, 0) as dmobile, d.photo as dphoto, t.photo as tphoto, IFNULL(t.number, 0) as number, IFNULL(t.color, 0) as color, IFNULL(t.type_name, 0) as type_name,  IFNULL(dcs.current_latitude, 0) as driver_latitude, IFNULL(dcs.current_longitude, 0)  as  driver_longitude, IFNULL(df.location, 0)  as location');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id ', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id ', 'left');
		
		
		
		$this->db->join('users d', 'd.id = r.driver_id ', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id ', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id ', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id ', 'left');
		$this->db->join('driver_frequency df', 'df.ride_id = r.id', 'left');
		$this->db->join('taxi t','t.id=r.taxi_id AND t.is_edit=1 ', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id ', 'left');
		
		$this->db->where(array('r.id'=>$ride_id));
		
		$q = $this->db->get();//print_r($this->db->error());exit;
		
       	if($q->num_rows()>0){
			
			$row = $q->row();
			
			
			
			$image_path = base_url('assets/uploads/');
			 if($row->dphoto != ''){
				 $row->dphoto = $image_path.$rides->dphoto;
			 }else{
				 $row->dphoto = $image_path.'default.png';
			 }
			 if($row->tphoto != ''){
				 $row->tphoto = $image_path.$row->tphoto;
			 }else{
				 $row->tphoto = $image_path.'default_taxi.jpg';
			 }
			
			if($row->status == 1){
				$row->booked_status = 'Request Ride';
			}elseif($row->status == 2){
				$row->booked_status = 'Booked Ride';
			}elseif($row->status == 3){
				$row->booked_status = 'Onride Ride';
			}elseif($row->status == 4){
				$row->booked_status = 'Waiting Ride';
			}elseif($row->status == 5){
				$row->booked_status = 'Completed Ride';
			}elseif($row->status == 6){
				$row->booked_status = 'Cancelled Ride';
			}elseif($row->status == 7){
				$row->booked_status = 'Ride Later Ride';
			}elseif($row->status == 8){
				$row->booked_status = 'Ride Rejected';
			}elseif($row->status == 9){
				$row->booked_status = 'Incomplete';
			}elseif($row->status == 10){
				$row->booked_status = 'Next Ride';
			}
			
			if($row->booked_type== 1){ $row->booked_type = 'Cityride'; }elseif($row->booked_type== 2){ $row->booked_type = 'Rental'; }elseif($row->booked_type== 3){ $row->booked_type = 'Outstation'; }
			
			$row->waypoints = $this->getWaypoint($ride_id);
			
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
	
	function ride_commision($driver_id, $customer_id, $ride_id, $fare_type, $total_fare, $admin_percentage, $admin_tax_percentage, $admin_tax_name, $driver_tax_percentage, $driver_tax_name, $discount_name, $discount_percentage, $countryCode){
		$admin_user = $this->getAdminUser($countryCode, 2);
		
		if($fare_type == 1){
			
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
			
			//$discount_fare = number_format($net_fare * $discount_percentage / 100, 2);
			
			
			
			//$final_fare = number_format($net_fare - $discount_fare, 2);
			$final_fare = number_format($net_fare, 2);
			
			$round_final_fare = number_format(round($final_fare), 2);
			$inert_commision = array(
				'driver_id' => $driver_id,
				'customer_id' => $customer_id,
				'ride_id' => $ride_id,
				'fare_type' => $fare_type,
				'total_fare' => $total_fare,
				'discount_reduce_fare' => $remain_fare,
				'admin_ride_percentage' => $admin_percentage,
				'admin_fare' => $admin_fare,
				'admin_tax' => $admin_tax,
				'admin_tax_name' => $admin_tax_name,
				'admin_tax_fare' => $admin_tax_fare,
				'admin_total_fare' => $admin_total_fare,
				'driver_fare' => $driver_fare,
				'driver_tax' => $driver_tax,
				'driver_tax_name' => $driver_tax_name,
				'driver_tax_fare' => $driver_tax_fare,
				'driver_total_fare' => $driver_total_fare,
				'net_fare' => $net_fare,
				'discount_name' => $discount_name,
				'discount_percentage' => $discount_percentage,
				'discount_fare' => $discount_fare,
				'final_fare' => $final_fare,
				'round_final_fare' => $round_final_fare
			);
			
			$commision = $this->db->insert('ride_commision', $inert_commision);
			//print_r($this->db->last_query());die;
			
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 12,
				'cash' => $admin_total_fare,
				'description' => 'Ride Distance Fare - commision amount transfer to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 11,
				'cash' => $admin_total_fare,
				'description' => 'Ride Distance Fare - admin recived commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 4,
				'cash' => $driver_tax_fare,
				'description' => 'Ride Distance Tax Fare - commision amount transfer to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 3,
				'cash' => $driver_tax_fare,
				'description' => 'Ride Distance Tax Fare - admin recived commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 7,
				'cash' => $discount_fare,
				'description' => 'Ride Distance Discount Fare - commision amount recived to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 8,
				'cash' => $discount_fare,
				'description' => 'Ride Distance Discount Fare - admin transfer commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$q = $this->db->insert_batch('wallet', $wallet_array);
			return $commision;
		}elseif($fare_type == 2){
			$admin_fare = number_format($total_fare, 2);
			$admin_tax = $admin_tax_percentage;
			$admin_tax_fare = number_format($admin_fare * $admin_tax / 100, 2);
			$admin_total_fare = number_format($admin_fare + $admin_tax_fare,2);
			$net_fare = $admin_total_fare;
			$final_fare = $admin_total_fare;	
			$round_final_fare = number_format(round($final_fare), 2);
			$inert_commision = array(
				'driver_id' => $driver_id,
				'customer_id' => $customer_id,
				'ride_id' => $ride_id,
				'fare_type' => $fare_type,
				'total_fare' => $total_fare,				
				'admin_fare' => $admin_fare,
				'admin_tax' => $admin_tax,
				'admin_tax_name' => $admin_tax_name,
				'admin_tax_fare' => $admin_tax_fare,
				'admin_total_fare' => $admin_total_fare,	
				'net_fare' => $net_fare,			
				'final_fare' => $final_fare,
				'round_final_fare' => $round_final_fare
			);
			$commision = $this->db->insert('ride_commision', $inert_commision);
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 6,
				'cash' => $admin_total_fare,
				'description' => 'Ride Outstanding Fare- commision amount transfer to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 5,
				'cash' => $admin_total_fare,
				'description' => 'Ride Outstanding Fare- admin recived commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			
			
			$q = $this->db->insert_batch('wallet', $wallet_array);
			return $commision;
		}elseif($fare_type == 3){
			
			$discount_fare = number_format($total_fare * $discount_percentage / 100, 2);		
			$remain_fare = number_format($total_fare - $discount_fare, 2);
			
			$admin_fare = number_format($remain_fare * $admin_percentage / 100, 2);
			$admin_tax = $admin_tax_percentage;
			$admin_tax_fare = number_format($admin_fare * $admin_tax / 100, 2);
			$admin_total_fare = number_format($admin_fare + $admin_tax_fare,2);
			
			$driver_fare = number_format($remain_fare - $admin_fare, 2);
			$driver_tax = $driver_tax_percentage;
			$driver_tax_fare = number_format($driver_fare * $driver_tax / 100, 2);
			$driver_total_fare = number_format($driver_fare + $driver_tax_fare,2);		
			
			$net_fare = number_format($admin_total_fare + $driver_total_fare, 2);	
			//$discount_fare = number_format($net_fare * $discount_percentage / 100, 2);
			//$final_fare = number_format($net_fare - $discount_fare, 2);
			$final_fare = number_format($net_fare, 2);
			
			$round_final_fare = number_format(round($final_fare), 2);
			$inert_commision = array(
				'driver_id' => $driver_id,
				'customer_id' => $customer_id,
				'ride_id' => $ride_id,
				'fare_type' => $fare_type,
				'total_fare' => $total_fare,
				'discount_reduce_fare' => $remain_fare,
				'admin_ride_percentage' => $admin_percentage,
				'admin_fare' => $admin_fare,
				'admin_tax' => $admin_tax,
				'admin_tax_name' => $admin_tax_name,
				'admin_tax_fare' => $admin_tax_fare,
				'admin_total_fare' => $admin_total_fare,
				'driver_fare' => $driver_fare,
				'driver_tax' => $driver_tax,
				'driver_tax_name' => $driver_tax_name,
				'driver_tax_fare' => $driver_tax_fare,
				'driver_total_fare' => $driver_total_fare,
				'net_fare' => $net_fare,	
				'discount_name' => $discount_name,
				'discount_percentage' => $discount_percentage,
				'discount_fare' => $discount_fare,
				'final_fare' => $final_fare,
				'round_final_fare' => $round_final_fare
			);
			$commision = $this->db->insert('ride_commision', $inert_commision);
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 10,
				'cash' => $admin_total_fare,
				'description' => 'Ride Waiting Fare- commision amount transfer to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 9,
				'cash' => $admin_total_fare,
				'description' => 'Ride Waiting Fare- admin recived commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 4,
				'cash' => $driver_tax_fare,
				'description' => 'Ride Waiting Tax Fare- commision amount transfer to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 3,
				'cash' => $driver_tax_fare,
				'description' => 'Ride Waiting Tax Fare- admin recived commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $driver_id,
				'user_type' => 2,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 9,
				'flag_method' => 7,
				'cash' => $discount_fare,
				'description' => 'Ride Waiting Discount Fare - commision amount recived to admin',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			$wallet_array[] = array(
				'user_id' =>  $admin_user,
				'user_type' => 0,
				'wallet_type' => 1,
				'join_id' => $ride_id,
				'join_table' => 'rides',
				'flag' => 4,
				'flag_method' => 8,
				'cash' => $discount_fare,
				'description' => 'Ride Waiting Discount Fare - admin transfer commision amount',
				'created' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode
			);
			
			
			$q = $this->db->insert_batch('wallet', $wallet_array);
			return $commision;
		}else{
			return false;	
		}
		
		return false;
	}
	
	function RidecancelCheck($booking_id){
		$this->db->select('id');
		$this->db->from('rides');
		$this->db->where('id', $booking_id);
		$this->db->where('cancel_status', 1);
		$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	
	function RidecancelCheckStartorOTP($driver_id, $ride_otp, $ride_id){
		$q = $this->db->select('*')->where('cancel_status', 1)->where('driver_id', $driver_id)->where('id', $ride_id)->where('ride_otp', $ride_otp)->get('rides');
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	function taxitypename($cab_type_id){
		$q = $this->db->select('name')->where('id', $cab_type_id)->get('taxi_type');
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			return $q->row('name');
		}
		return false;
	}
	
	
	function insertLastEmit($last_emit_array){
		$q = $this->db->insert('last_emit', $last_emit_array);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;	
	}
	
	
	function getRideCommision($ride_id){
		$this->db->select('*');
		$this->db->from('ride_commision');
		$this->db->where('ride_id', $ride_id);
		$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
			
			$data = array();
			foreach (($q->result()) as $row) {
				if($row->fare_type == 1){
					$data['ride_fare'] = $row->total_fare;
					$data['ride_driver_fare'] = $row->driver_fare;
					$data['ride_driver_tax'] = $row->driver_tax_name;
					$data['ride_driver_tax_fare'] = $row->driver_tax_fare;
					$data['ride_driver_total_fare'] = $row->driver_total_fare;
					$data['ride_commision_percentage'] = $row->admin_ride_percentage;
					$data['ride_commision_fare'] = $row->admin_fare;
					$data['ride_commision_tax'] = $row->admin_tax_name;
					$data['ride_commision_tax_fare'] = $row->admin_tax_fare;
					$data['ride_commision_total_fare'] = $row->admin_total_fare;
					$data['ride_net_fare'] = $row->net_fare;
					$data['ride_discount'] = $row->discount_name;
					$data['ride_discount_fare'] = $row->discount_fare;
					$data['ride_total_fare'] = $row->final_fare;
				}elseif($row->fare_type == 2){
					$data['outstanding_fare'] = $row->total_fare;					
					$data['outstanding_commision_fare'] = $row->admin_fare;
					$data['outstanding_commision_tax'] = $row->admin_tax_name;
					$data['outstanding_commision_tax_fare'] = $row->admin_tax_fare;
					$data['outstanding_commision_total_fare'] = $row->admin_total_fare;
					$data['outstanding_net_fare'] = $row->net_fare;
					$data['outstanding_total_fare'] = $row->final_fare;
				}elseif($row->fare_type == 3){
					$data['waiting_fare'] = $row->total_fare;
					$data['waiting_driver_fare'] = $row->driver_fare;
					$data['waiting_driver_tax'] = $row->driver_tax_name;
					$data['waiting_driver_tax_fare'] = $row->driver_tax_fare;
					$data['waiting_driver_total_fare'] = $row->driver_total_fare;
					$data['waiting_commision_percentage'] = $row->admin_ride_percentage;
					$data['waiting_commision_fare'] = $row->admin_fare;
					$data['waiting_commision_tax'] = $row->admin_tax_name;
					$data['waiting_commision_tax_fare'] = $row->admin_tax_fare;
					$data['waiting_commision_total_fare'] = $row->admin_total_fare;
					$data['waiting_net_fare'] = $row->net_fare;
					$data['waiting_discount'] = $row->discount_name;
					$data['waiting_discount_fare'] = $row->discount_fare;
					$data['waiting_total_fare'] = $row->final_fare;
				}
                
            }
            return $data;	
		}
		return false;
	}
	
	function getRideCommisionPayment($ride_id){
		$this->db->select('*');
		$this->db->from('ride_commision');
		$this->db->where('ride_id', $ride_id);
		$q = $this->db->get();//print_r($this->db->last_query());exit;
       	if($q->num_rows()>0){
			
			$data = array();
			foreach (($q->result()) as $row) {
				if($row->fare_type == 1){
					$data['ride_base_fare'] = $row->total_fare;
					$data['ride_driver_tax_fare'] = $row->driver_tax_fare;
					$data['ride_driver_fare'] = $row->driver_fare;
					$data['ride_commision_tax_fare'] = $row->admin_tax_fare;
					
					$data['ride_net_fare'] = $row->net_fare;
					
					$data['ride_discount_fare'] = $row->discount_fare;
					
					$data['ride_total_fare'] = $row->final_fare;
				}elseif($row->fare_type == 2){
					$data['outstanding_base_fare'] = $row->total_fare;
					
					$data['outstanding_commision_tax_fare'] = $row->admin_tax_fare;
					
					$data['outstanding_net_fare'] = $row->net_fare;
					
					$data['outstanding_total_fare'] = $row->final_fare;
				}elseif($row->fare_type == 3){
					$data['waiting_base_fare'] = $row->total_fare;
					$data['waiting_driver_tax_fare'] = $row->driver_tax_fare;
					$data['waiting_driver_fare'] = $row->driver_fare;
					$data['waiting_commision_tax_fare'] = $row->admin_tax_fare;
					
					$data['waiting_net_fare'] = $row->net_fare;
					
					$data['waiting_discount_fare'] = $row->discount_fare;
					
					$data['waiting_total_fare'] = $row->final_fare;
				}
                
            }
			
			$payment = array(
				'r_total_fare' => number_format($data['ride_base_fare'], 2),
				'r_waiting_charge' => number_format($data['waiting_base_fare'], 2),
				'r_outstanding' => number_format($data['outstanding_base_fare'], 2),
				'r_total_tax' => number_format($data['ride_commision_tax_fare'] + $data['ride_driver_tax_fare'] + $data['waiting_commision_tax_fare'] + $data['waiting_driver_tax_fare'] + $data['outstanding_commision_tax_fare'], 2),
				'r_discount' => number_format($data['ride_discount_fare'] + $data['waiting_discount_fare'], 2),
				'r_final' =>  number_format($data['ride_net_fare'] + $data['outstanding_net_fare'] + $data['waiting_net_fare'], 2),
				'r_driver_fare' =>  number_format($data['ride_driver_fare'] + $data['waiting_driver_fare'], 2)
				
			);
			///print_r($payment);
			//die;
            return $payment;	
		}
		return false;
	}
	
	public function checkDiscount($countryCode){
		$current_date = date('d/m/Y');
		$current_day = date('l');
		
		if(!empty($current_day)){
			$day = $this->db->select('id as discount_id, discount_name, discount_percentage')->from('discount')->where('is_country', $countryCode)->where('days', $current_day)->where('discount_type', 1)->where('is_delete', 0)->order_by('id', 'DESC')->limit(1)->get();
			if($day->num_rows()>0){
				$data[] = $day->row();
			}
		}
		
		if(!empty($current_date)){
			$date = $this->db->select('id as discount_id, discount_name, discount_percentage')->from('discount')->where('is_country', $countryCode)->where('start_date >=', $current_date)->where('end_date <=', $current_date)->where('discount_type', 2)->where('is_delete', 0)->order_by('id', 'DESC')->limit(1)->get();
			if($date->num_rows()>0){
				$data[] = $date->row();
			}
		}
		
		
		$all = $this->db->select('id as discount_id, discount_name, discount_percentage')->from('discount')->where('is_country', $countryCode)->where('discount_type', 0)->where('is_delete', 0)->order_by('id', 'DESC')->limit(1)->get();
		if($all->num_rows()>0){
			$data[] = $all->row();
		}
		
		if(!empty($data))
		{
			return $data[0];
		}
		return false;
	}
	
	function acknowledegementUpdate($user_id, $user_type_id, $emit_name){
        
        $this->db->where('emit_name',$emit_name);
		$this->db->where('user_id',$user_id);
		$this->db->where('user_type_id',$user_type_id);
		$this->db->where('is_status',0);
        $q = $this->db->update('last_emit',array('is_status' => 1));
		//print_r($this->db->last_query());
		if(isset($q)){
			return 1;	
		}
		return 0;
    }
	
	function insertLog($user_id, $user_type, $log_type){
		$c = $this->db->select('id')->from('user_login_logout')->where('user_id', $user_id)->where('DATE(login_date)', date('Y-m-d'))->where('is_stop', 0)->order_by('id', 'DESC')->limit(1)->get();
		if($c->num_rows()>0){
			$this->db->where('id', $c->row('id'));
			$this->db->update('user_login_logout', array('logout' => 1, 'logout_date' => date('Y-m-d H:i:s'), 'is_stop' => 2));
		}
		
		$q = $this->db->insert('user_login_logout', array('user_id' => $user_id, 'user_type' => $user_type, 'login' => 1, 'login_date' => date('Y-m-d H:i:s')));
		if($q){
			return true;	
		}
		return false;
	}
	function updateLog($user_id, $user_type, $log_type){
		$this->db->where('user_id', $user_id);
		$this->db->where('DATE(login_date)', date('Y-m-d'));
		$this->db->where('is_stop', 0);
		$q = $this->db->update('user_login_logout', array('logout' => 1, 'logout_date' => date('Y-m-d H:i:s'), 'is_stop' => 2));
		if($q){
			return true;	
		}
		return false;
	}
	
	public function driverhours($user_id){
		
		
		$is_country = 'IN';
				
		if($user_id){	
			
			$current_time = date('Y-m-d H:i:s');
			$current_date = date('Y-m-d');
			
			$this->db->select('r.id as ride_id');
			$this->db->from('rides r');
			$this->db->where('r.driver_id', $user_id);
			
			$this->db->where_in('r.status', array('2,3,9'));
			$r = $this->db->get();
			if($r->num_rows()>0){
				$on_ride = 1;	
			}else{
				$on_ride = 0;
			}
			
			$query = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,logout_date)) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'" AND DATE(login_date) = "'.$current_date.'" AND is_stop != 0 group by date(login_date)');
			if($query->num_rows()>0){
				$total_timestamp1 = $query->row('total_timestamp');
				
			}
			$query1 = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,"'.$current_time.'")) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'"  AND  DATE(login_date) = "'.$current_date.'" AND is_stop = 0 group by date(login_date)');
			if($query1->num_rows()>0){
				$total_timestamp2 = $query1->row('total_timestamp');
			}
			
			//$data = array_merge($data1, $data2);
			//$total_timestamp = 0;
			$total_hours = '00:00:00';
			$total_timestamp = $total_timestamp1 + $total_timestamp2;
			//foreach($data as $key=>$value){
			//	$total_timestamp += $value->total_timestamp;
			//}
			
			//echo $total_timestamp;
			
			$total_hours = gmdate("H:i:s", $total_timestamp);
			//echo $total_hours = $this->site->driverhours($user_id, $is_country);
			
			$setting = $this->RegsiterSettings($is_country);
			//print_r($setting);
			$driver_working_hours_limit = floor($setting->driver_working_hours_limit * 3600);
			//echo $total_timestamp;
			//echo '@@'.$driver_working_hours_limit;
			//die;
			if($total_timestamp > $driver_working_hours_limit){
				if($on_ride == 0){
					$hour_staus = 1;
				}else{
					$hour_staus = 0;
				}
			}else{
				$hour_staus = 0;
			}
			
			return $hour_staus;
		}else{
			return 0;
		}
		
		
	}

	function getDepartmentRole($user_id, $group_id){
		$this->db->select('department_id, designation_id');
		$this->db->from('user_permission');
		$this->db->where('user_id', $user_id);
		$this->db->where('group_id', $group_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function getALLPermission($group_id, $department_id, $designation_id, $is_country){
		$q = $this->db->select('*')->where('group_id', $group_id)->where('department_id', $department_id)->where('designation_id', $designation_id)->where('is_country', $is_country)->get('all_permission');
		if($q->num_rows()>0){

			
			return $q->row();
		}
	
		return false;
	}

	function getGroupUserID($user_id){
		$q = $this->db->select('group_id')->where('id', $user_id)->get('users');
		if($q->num_rows()>0){
			return $q->row('group_id');
		}
		return false;
	}
}