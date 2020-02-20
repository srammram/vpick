<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct() {
        parent::__construct();
		$this->load->library('ion_auth');
    }
	
	
	/*do {
    $shorturl = createRandomCode();
    $q = $db->query("SELECT * FROM maps WHERE url='".$shorturl."'");
} while(mysqli_num_rows($q) > 0);
*/
	
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
				
				$this->db->insert('wallet', array('user_id' => $u->user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'cash' => $u->amount, 'description' => 'One person using refer code credit wallet amount '.$u->amount.''));
				
				$this->db->insert('wallet', array('user_id' => $user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'cash' => $u->amount, 'description' => 'Thanks for register added credit wallet amount '.$u->amount.''));
				
			}else{
				$this->db->insert('refercode', array('refer_code' => $u->code, 'user_id' => $user_id, 'type' => 1, 'enable' => $u->register_enable, 'status' => 1, 'r_date' => date('Y-m-d'), 'created_on' => date('Y-m-d H:i:s')));
				$this->db->insert('wallet', array('user_id' => $user_id, 'user_type' => '1', 'created' => date('Y-m-d H:i:s'), 'is_country' => $countryCode, 'wallet_type' => $group_id == 5 ? 1 : 2, 'flag' => 8, 'cash' => $u->amount, 'description' => 'Thanks for register added credit wallet amount '.$u->amount.''));
				
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
		$pincode = $this->findLocationPINCODE1($latitude, $longitude);
		
		
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
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
	function findLocationWEB($latitude, $longitude, $countryCode){
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false';
		
		
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
		
		$geocodeFromLatlon = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$latitude.','.$longitude.'&sensor=false');
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
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
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
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN";
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
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN&sensor=false&libraries=geometry";
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
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN";
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
	
	/*function GetDrivingDistanceNew($lat1, $long1, $lat2, $long2)
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
		
		return $response_a;
	}*/
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
		$pincode = $this->findLocationPINCODE1($lat, $lng);
		
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
	
	
	
	function getFareestimate($start_lat, $start_lng, $taxi_type, $ride_type, $countryCode){
		$data = array();
		$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng);
		
		
		$start_q = $this->db->select('p.area_id')->from('pincode p')->where('p.pincode', $start_pincode)->where('p.is_country', $countryCode)->get();
		
		if ($start_q->num_rows() > 0) {
            $start_city_id = $start_q->row('area_id');
        }else{
			$start_city_id = 0;
		}
		
		
		
		if($ride_type == 1){
			
			$start_time = date('Y-m-d H:i:s');
			
			if($start_city_id != 0){
				$daily_withcity = $this->db->select('*')->where('area_id', $start_city_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
				//print_r($this->db->last_query());die;
				
					
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
							
						);
						
						return $fare;
						exit;
					}
				}
			}else{
				
				
				$daily_withoutcity = $this->db->select('*')->where('is_default', 1)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
				$start_time = date('Y-m-d H:i:s');
				
				
					
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
		);
				
		return $fare;
		exit;
		}
	}
	
	function getFare($customer_id, $ride_type, $outstation_type, $outstation_way, $taxi_type, $start_lat, $start_lng, $end_lat, $end_lng, $start_time, $end_time, $estimate_distance, $actual_distance, $total_distance, $waiting_time, $rental_id, $no_of_labour, $countryCode)
	{
		$data = array();
		$start_pincode = $this->findLocationPINCODE1($start_lat, $start_lng);
		$end_pincode = $this->findLocationPINCODE1($end_lat, $end_lng);
		
		
		$start_q = $this->db->select('p.area_id')->from('pincode p')->where('p.is_country', $countryCode)->where('p.pincode', $start_pincode)->get();
		
		if ($start_q->num_rows() > 0) {
            $start_area_id = $start_q->row('area_id');
        }else{
			$start_area_id = 0;
		}
		
		$end_q = $this->db->select('p.area_id')->from('pincode p')->where('p.is_country', $countryCode)->where('p.pincode', $end_pincode)->get();
		if ($end_q->num_rows() > 0) {
            $end_area_id = $start_q->row('area_id');
        }else{
			$end_area_id = 0;
		}
		
		if($rental_id != 0){
			
			if($start_city_id != 0){
				$rental_withcity = $this->db->select('*')->where('area_id', $start_area_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
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
					
					//$total_time = $start_time - $end_time;
					$total_time_minutes = round(abs(strtotime($end_time) - strtotime($start_time)) / 60);
					
					$t = explode(":", $package_time); 
					$h = $t[0]; 
					if (isset($t[1])) { 
						$m = $t[1]; 
					} else { 
						$m = "00"; 
					} 
					$mm = ($h * 60)+$m; 
					
					$package_time_minutes = $mm;
					
					$labour_charge = round($rental_withcity->row('labour_charge') * $no_of_labour);
					
					if($option_type == 1){
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						//$start_time = date("H:i:s",strtotime($start_time));
						//$end_time = date("H:i:s",strtotime($end_time));
						
						//$time1 = strtotime($start_time);
						//$time2 = strtotime($end_time);
						//$difference = round(abs($time2 - $time1) / 3600,2);
						
						$ptime = explode(':',$per_time);
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = 0;
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$actual_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$total_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$round_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = $per_distance_price;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							//$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							//$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = round($per_distance_price);
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
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
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => $actual_time_fare,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
				}else{
					$rental_withoutcity = $this->db->select('*')->where('area_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
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
					
					//$total_time = $start_time - $end_time;
					$total_time_minutes = round(abs(strtotime($end_time) - strtotime($start_time)) / 60);
					
					$t = explode(":", $package_time); 
					$h = $t[0]; 
					if (isset($t[1])) { 
						$m = $t[1]; 
					} else { 
						$m = "00"; 
					} 
					$mm = ($h * 60)+$m; 
					
					$package_time_minutes = $mm;
					
					$labour_charge = round($rental_withoutcity->row('labour_charge') * $no_of_labour);
					
					if($option_type == 1){
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						//$start_time = date("H:i:s",strtotime($start_time));
						//$end_time = date("H:i:s",strtotime($end_time));
						
						//$time1 = strtotime($start_time);
						//$time2 = strtotime($end_time);
						//$difference = round(abs($time2 - $time1) / 3600,2);
						
						$ptime = explode(':',$per_time);
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = 0;
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$actual_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$total_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$round_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = $per_distance_price;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							//$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							//$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = round($per_distance_price);
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
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
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => $actual_time_fare,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					
					}
				}
			}else{
				$rental_withoutcity = $this->db->select('*')->where('area_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('rental_fare');
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
					
					//$total_time = $start_time - $end_time;
					$total_time_minutes = round(abs(strtotime($end_time) - strtotime($start_time)) / 60);
					
					$t = explode(":", $package_time); 
					$h = $t[0]; 
					if (isset($t[1])) { 
						$m = $t[1]; 
					} else { 
						$m = "00"; 
					} 
					$mm = ($h * 60)+$m; 
					
					$package_time_minutes = $mm;
					
					$labour_charge = round($rental_withcity->row('labour_charge') * $no_of_labour);
					
					if($option_type == 1){
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
						}else{
							$min_distance = $package_distance;
							$min_time = 0;
							$min_fare = round($package_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
						
					}elseif($option_type == 2){
						//$start_time = date("H:i:s",strtotime($start_time));
						//$end_time = date("H:i:s",strtotime($end_time));
						
						//$time1 = strtotime($start_time);
						//$time2 = strtotime($end_time);
						//$difference = round(abs($time2 - $time1) / 3600,2);
						
						$ptime = explode(':',$per_time);
						
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = 0;
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$actual_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$total_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							$round_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($difference - $package_time).'mins -  '.$extra_fare;
						}else{
							
							$min_distance = 0;
							$min_time = $package_time_minutes;
							$min_fare = 0;
							$min_time_fare = $per_distance_price;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($package_price);
							$actual_fare = round($package_price);
							$total_fare = round($package_price);
							$round_fare = round($package_price);
							
							$extra_fare = 0;
							$extra_fare_details = 0;
						}
					}else{
						if($total_distance > $package_distance || $total_time_minutes > $package_time_minutes){
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = $per_distance_price;
							
							$additional_distance = ($actual_distance - $package_distance);
							$additional_time = ($total_time_minutes - $package_time_minutes);
							$additional_fare = round(($actual_distance - $package_distance) * $per_distance_price);
							$additional_time_fare = round((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price);
							
							//$estimate_fare = round(((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price) + $package_price);
							//$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price);
							
							$estimate_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$actual_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$total_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							$round_fare = round((($estimate_distance - $package_distance) * $per_distance_price) + $package_price + ((($total_time_minutes - $package_time_minutes)/$ptime[0]) * $per_distance_price));
							
							$extra_fare = ($estimate_distance - $package_distance) * $per_distance_price;
							$extra_fare_details = 'Extra '.($estimate_distance - $package_distance).'Kms -  '.$extra_fare;
							
						}else{
							
							$min_distance = $package_distance;
							$min_time = $package_time_minutes;
							$min_fare = round($package_price);
							$min_time_fare = round($per_distance_price);
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
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
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => $actual_time_fare,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
					
					
						
				}
			}
		
		}else{
			if($ride_type == 1){
				if($start_city_id != 0){
					
				$daily_withcity = $this->db->select('*')->where('area_id', $start_area_id)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
				if ($daily_withcity->num_rows() > 0) {
					
					
					$ride_time = explode(' ', $start_time);
					$this->db->select('*');
					$this->db->where('daily_fare_id', $daily_withcity->row('id'));
					$this->db->where('start_time <=', $ride_time[1])->where('end_time >=', $ride_time[1]);
					$this->db->where('is_country', $countryCode);
					$this->db->limit(1);
					$slot = $this->db->get('daily_slot');					
					if ($slot->num_rows() > 0) {
						//$slot_include_fare = $slot->row('include_fare');
						//$slot_extra_fare = $slot->row('extra_fare');
						//$slot_waiting_price = $slot->row('waiting_price');
						$slot_include_fare = 0;
						$slot_extra_fare = 0;
						$slot_waiting_price = 0;
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
						//$actual_waiting_fare = round($waiting/$fare_waiting) * $waiting_fare;
						//$waiting_price = round($waiting/$fare_waiting) * $slot_waiting_price;
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					}else{
						$actual_waiting_fare = 0 * $waiting_fare;
						$waiting_price = 0 * $slot_waiting_price;
					}
					
					//$start_time;
					$labour_charge = round($daily_withcity->row('labour_charge') * $no_of_labour);
					$base_min_distance = $daily_withcity->row('base_min_distance');
										
					$base_min_distance_price = $daily_withcity->row('base_min_distance_price');
					$base_per_distance = $daily_withcity->row('base_per_distance');
					$base_per_distance_price = $daily_withcity->row('base_per_distance_price');
			
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price );
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						if($slot_type != 'empty'){
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
						}else{
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
					}
					
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => 0,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => 0,
					);
					return $fare;
				}else{
					$daily_withoutcity = $this->db->select('*')->where('area_id', 0)->where('is_country', $countryCode)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('daily_fare');
					
					
					
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
					$labour_charge = round($daily_withoutcity->row('labour_charge') * $no_of_labour);
					$base_min_distance = $daily_withoutcity->row('base_min_distance');
					$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						if($slot_type != 'empty'){
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
						}else{
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => 0,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => 0,
					);
						return $fare;
					}
				}
			}
			else{
				
				
				
				$daily_withoutcity = $this->db->select('*')->where('area_id', 0)->where('taxi_type', $taxi_type)->where('is_country', $countryCode)->where('is_delete', 0)->get('daily_fare');
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
					$labour_charge = round($daily_withoutcity->row('labour_charge') * $no_of_labour);
					$base_min_distance = $daily_withoutcity->row('base_min_distance');
					$base_min_distance_price = $daily_withoutcity->row('base_min_distance_price');
					$base_per_distance = $daily_withoutcity->row('base_per_distance');
					$base_per_distance_price = $daily_withoutcity->row('base_per_distance_price');
					
					if($total_distance > $base_min_distance){
						
						if($slot_type != 'empty'){
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + (($estimate_distance - $base_min_distance) * $slot_extra_fare) + $base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);

						}else{
							
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = ($actual_distance - $base_min_distance);
							$additional_time = 0;
							$additional_fare = round(($actual_distance - $base_min_distance) * $base_per_distance_price);
							$additional_time_fare = 0;
							
							$estimate_fare = round((($estimate_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round((($actual_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round((($total_distance - $base_min_distance) * $base_per_distance_price) + $base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
						
					}else{
						if($slot_type != 'empty'){
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $slot_include_fare + $actual_waiting_fare + $waiting_price);
						}else{
							$min_distance = $base_min_distance;
							$min_time = 0;
							$min_fare = round($base_min_distance_price);
							$min_time_fare = 0;
							
							$additional_distance = 0;
							$additional_time = 0;
							$additional_fare = 0;
							$additional_time_fare = 0;
							
							$estimate_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$actual_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$total_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
							$round_fare = round($base_min_distance_price + $actual_waiting_fare + $waiting_price);
						}
					}
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => 0,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => 0,
					);
					return $fare;	
				}
			}	
			}elseif($ride_type == 3){
				
			if($start_city_id != 0){
				$outstation_withcity = $this->db->select('*')->where('from_city_id', $start_city_id)->where('is_country', $countryCode)->where('to_city_id', $end_city_id)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withcity->num_rows() > 0) {
					
					$labour_charge = round($outstation_withcity->row('labour_charge') * $no_of_labour);
					$is_oneway = $outstation_withcity->row('is_oneway');
					$is_twoway = $outstation_withcity->row('is_twoway');
					$package_name = $outstation_withcity->row('package_name');
					
					$oneway_package_distance = $outstation_withcity->row('oneway_distance');
					$twoway_package_distance = $outstation_withcity->row('twoway_distance');
					
					$oneway_package_price = $outstation_withcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withcity->row('twoway_package_price');
					
					$min_per_distance = $outstation_withcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withcity->row('min_per_distance_price');
					$per_distance = $outstation_withcity->row('per_distance');
					$per_distance_price = $outstation_withcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withcity->row('driver_night_per_day');
					
					
						
						if($outstation_way == 1){
							
							$min_distance = $twoway_package_distance;
							$min_time = 0;
							$min_fare = round($twoway_package_price);
							$min_time_fare = 0;
							if($total_distance > $twoway_package_distance){
								$additional_distance = $total_distance - $twoway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $twoway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$actual_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$total_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$round_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($twoway_package_price);
								$actual_fare = round($twoway_package_price);
								$total_fare = round($twoway_package_price);
								$round_fare = round($twoway_package_price);
							}
							
						}else{
							$min_distance = $oneway_package_distance;
							$min_time = 0;
							$min_fare = round($oneway_package_price);
							$min_time_fare = 0;
							
							if($total_distance > $oneway_package_distance){
								$additional_distance = $total_distance - $oneway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $oneway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$actual_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$total_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$round_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($oneway_package_price);
								$actual_fare = round($oneway_package_price);
								$total_fare = round($oneway_package_price);
								$round_fare = round($oneway_package_price);
							}
							
							
						}
						
						
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;	
				}else{
					
					$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					
					$labour_charge = round($outstation_withoutcity->row('labour_charge') * $no_of_labour);
					
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_distance = $outstation_withoutcity->row('oneway_distance');
					$twoway_package_distance = $outstation_withoutcity->row('twoway_distance');
					
					
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					
						
						if($outstation_way == 1){
							
							$min_distance = $twoway_package_distance;
							$min_time = 0;
							$min_fare = round($twoway_package_price);
							$min_time_fare = 0;
							if($total_distance > $twoway_package_distance){
								$additional_distance = $total_distance - $twoway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $twoway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$actual_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$total_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$round_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($twoway_package_price);
								$actual_fare = round($twoway_package_price);
								$total_fare = round($twoway_package_price);
								$round_fare = round($twoway_package_price);
							}
							
						}else{
							$min_distance = $oneway_package_distance;
							$min_time = 0;
							$min_fare = round($oneway_package_price);
							$min_time_fare = 0;
							
							if($total_distance > $oneway_package_distance){
								$additional_distance = $total_distance - $oneway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $oneway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$actual_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$total_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$round_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($oneway_package_price);
								$actual_fare = round($oneway_package_price);
								$total_fare = round($oneway_package_price);
								$round_fare = round($oneway_package_price);
							}
							
							
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}	
				}
			}else{
				$outstation_withoutcity = $this->db->select('*')->where('from_city_id', 0)->where('is_country', $countryCode)->where('to_city_id', 0)->where('taxi_type', $taxi_type)->where('is_delete', 0)->get('outstation_fare');
				if ($outstation_withoutcity->num_rows() > 0) {
					
					$labour_charge = round($outstation_withoutcity->row('labour_charge') * $no_of_labour);
					
					$is_oneway = $outstation_withoutcity->row('is_oneway');
					$is_twoway = $outstation_withoutcity->row('is_twoway');
					$package_name = $outstation_withoutcity->row('package_name');
					$oneway_package_distance = $outstation_withoutcity->row('oneway_distance');
					$twoway_package_distance = $outstation_withoutcity->row('twoway_distance');
					
					
					$oneway_package_price = $outstation_withoutcity->row('oneway_package_price');
					$twoway_package_price = $outstation_withoutcity->row('twoway_package_price');
					$min_per_distance = $outstation_withoutcity->row('min_per_distance');
					$min_per_distance_price = $outstation_withoutcity->row('min_per_distance_price');
					$per_distance = $outstation_withoutcity->row('per_distance');
					$per_distance_price = $outstation_withoutcity->row('per_distance_price');
					$driver_allowance_per_day = $outstation_withoutcity->row('driver_allowance_per_day');
					$driver_night_per_day = $outstation_withoutcity->row('driver_night_per_day');
					
					
						
						if($outstation_way == 1){
							
							$min_distance = $twoway_package_distance;
							$min_time = 0;
							$min_fare = round($twoway_package_price);
							$min_time_fare = 0;
							if($total_distance > $twoway_package_distance){
								$additional_distance = $total_distance - $twoway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $twoway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$actual_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$total_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								$round_fare = round((($total_distance - $twoway_package_distance) * $per_distance_price) + $twoway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($twoway_package_price);
								$actual_fare = round($twoway_package_price);
								$total_fare = round($twoway_package_price);
								$round_fare = round($twoway_package_price);
							}
							
						}else{
							$min_distance = $oneway_package_distance;
							$min_time = 0;
							$min_fare = round($oneway_package_price);
							$min_time_fare = 0;
							
							if($total_distance > $oneway_package_distance){
								$additional_distance = $total_distance - $oneway_package_distance;
								$additional_time = 0;
								$additional_fare = round(($total_distance - $oneway_package_distance) * $per_distance_price);
								$additional_time_fare = 0;
								
								$estimate_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$actual_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$total_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								$round_fare = round((($total_distance - $oneway_package_distance) * $per_distance_price) + $oneway_package_price);
								
							}else{
								$additional_distance = 0;
								$additional_time = 0;
								$additional_fare = 0;
								$additional_time_fare = 0;
								$estimate_fare = round($oneway_package_price);
								$actual_fare = round($oneway_package_price);
								$total_fare = round($oneway_package_price);
								$round_fare = round($oneway_package_price);
							}
							
							
						}
						
						$extra_fare = $driver_allowance_per_day + $driver_night_per_day;
						$extra_fare_details = 'Driver allowance -  '.$driver_allowance_per_day.' \n Night Stay - '.$driver_night_per_day;
						
					
					
					$fare = array(
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'estimate_time' => 0,
						'estimate_time_fare' => 0,
						'actual_distance' => $actual_distance,
						'actual_fare' => $actual_fare,
						'actual_time' => 0,
						'actual_time_fare' => 0,
						'min_distance' => $min_distance,
						'min_time' => $min_time,
						'min_fare' => $min_fare,
						'min_time_fare' => $min_time_fare,
						'additional_distance' => $additional_distance,
						'additional_time' => $additional_time,
						'additional_fare' => $additional_fare,
						'additional_time_fare' => $additional_time_fare,
						'total_distance' => $total_distance,
						'total_fare' => $total_fare,
						'round_fare' => $round_fare,
						'extra_fare' => $extra_fare,
						'labour_charge' => $labour_charge,
						'extra_fare_details' => $extra_fare_details,
					);
					return $fare;
				}
				
				
			}
				
		
			}
		}
		$fare = array(
			'estimate_distance' => 0,
			'estimate_fare' => 0,
			'estimate_time' => 0,
			'estimate_time_fare' => 0,
			'actual_distance' => 0,
			'actual_fare' => 0,
			'actual_time' => 0,
			'actual_time_fare' => 0,
			'min_distance' => 0,
			'min_time' => 0,
			'min_fare' => 0,
			'min_time_fare' => 0,
			'additional_distance' => 0,
			'additional_time' => 0,
			'additional_fare' => 0,
			'additional_time_fare' => 0,
			'total_distance' => 0,
			'total_fare' => 0,
			'round_fare' => 0,
			'extra_fare' => 0,
			'labour_charge' => 0,
			'extra_fare_details' => 0,
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
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id AND allocated_status = 1 ', 'left');
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

    public function getUser($id = NULL) {
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
	
	function createTonNotification($tons, $type, $user_id, $countryCode){
		$this->db->where('tons', $tons);
		$this->db->where('taxi_type_id', $type);
		$q = $this->db->get('taxi_tons');
		if($q->num_rows() == 0){
			$this->db->insert('tons_notification', array('taxi_type_id' => $type, 'tons' => $tons, 'created_by' => $user_id, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
			return true;
		}
		return false;
	}
	
	function getTons_notification($group_id, $countryCode){
		$this->db->select('tn.id, tt.name as type_name, tn.tons, tn.is_read');
		$this->db->from('tons_notification tn');
		$this->db->join('taxi_type tt', 'tt.id = tn.taxi_type_id');
		if($group_id != 1){
		$this->db->where('tn.is_country', $countryCode);
		}
		$this->db->where('tn.created_status', 0);
		$this->db->order_by('tn.is_read', 'ACS');
		$q = $this->db->get();
		
		if($q->num_rows() > 0){
			
			return $q->result();
		}
		return false;
	}
	
	function checkTons($tons, $type){
		$this->db->select('tn.id, tn.taxi_type_id, tn.tons');
		$this->db->from('taxi_tons tn');
		
		$this->db->where('tn.tons', $tons);
		$this->db->where('tn.taxi_type_id', $type);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows() == 0){
			
			return true;
		}else{
			
			$d =  $this->db->select('id')->where('tons', $q->row('tons'))->where('taxi_type', $q->row('taxi_type_id'))->get('daily_fare');
			if($d->num_rows() > 0){
				$daily_fare = 1;
			}else{
				$daily_fare = 0;
			}
			
			$o = $this->db->select('id')->where('tons', $q->row('tons'))->where('taxi_type', $q->row('taxi_type_id'))->get('outstation_fare');
			if($o->num_rows() > 0){
				$outstation_fare = 1;
			}else{
				$outstation_fare = 0;
			}
			
			$r = $this->db->select('id')->where('tons', $q->row('tons'))->where('taxi_type', $q->row('taxi_type_id'))->get('rental_fare');
			if($r->num_rows() > 0){
				$rental_fare = 1;
			}else{
				$rental_fare = 0;
			}
			
			if($daily_fare == 0 && $outstation_fare == 0 && $rental_fare == 0){				
				return true;
			}else{
				return false;	
			}
		}
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
    
}
