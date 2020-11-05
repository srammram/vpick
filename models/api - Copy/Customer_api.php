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
	/*New Changes*/
	
	function addMoneyCashwallet($user_id, $wallet_array, $payment_array,  $countryCode, $transaction_status){
		if($transaction_status == 'Success'){
			$wallet_array['wallet_type'] = 1;
			$this->db->insert('wallet', $wallet_array);
			if($wallet_id = $this->db->insert_id()){
				$payment_array['method_id'] = $wallet_id;
				$this->db->insert('multiple_gateway', $payment_array);
				return true;
			}
		}else{
			$this->db->insert('multiple_gateway', $payment_array);
			return true;
		}
		return false;	
	}
	
	
	function outstanding_price($customer_id, $countryCode){
		$q = $this->db->select('customer_fare')->where('is_edit', 1)->where('customer_status', 1)->get('outstandingfare');
		if ($q->num_rows() > 0) {
			return $q->row('customer_fare');	
		}
		return '0.00';
	}
	
	function getTypeWallets($user_id, $wallet_type, $countryCode){
		$data = array();
		
		$setting  = $this->site->get_setting($countryCode);
		
		$this->db->select('id as transaction_id, wallet_type, flag, cash, description, created');
		$this->db->where('user_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$this->db->where('wallet_type', $wallet_type);
		$q = $this->db->get('wallet');
		
		$data['wallet'] = "0";
		$data['driverpaid'] = "0";
		$data['list'] = [];
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				//$wallet[] = $row->cash;
				if($row->flag == 1 ){
					$row->flag_name = 'Incentive';
				}elseif($row->flag == 2 ){
					$row->flag_name = 'Rides';
				}elseif($row->flag == 3 ){
					$row->flag_name = 'Refunded';
				}elseif($row->flag == 4 ){
					$row->flag_name = 'Deduction';
				}elseif($row->flag == 5 ){
					$row->flag_name = 'Transfer';
				}elseif($row->flag == 6 ){
					$row->flag_name = 'AddMoney';
				}elseif($row->flag == 7 ){
					$row->flag_name = 'SentMoney';
				}else{
					$row->flag_name = 'No';
				}
				if($row->wallet_type == 1 ){
					
					if($row->flag == 1 ){
						$wallet_cash_Incentive[] = $row->cash;
					}elseif($row->flag == 2 ){
						$wallet_cash_Rides[] = $row->cash;
					}elseif($row->flag == 3 ){
						$wallet_cash_Refunded[] = $row->cash;
					}elseif($row->flag == 4 ){
						$wallet_cash_Deduction[] = $row->cash;
					}elseif($row->flag == 5 ){
						$wallet_cash_Transfer[] = $row->cash;
					}elseif($row->flag == 6 ){
						$wallet_cash_AddMoney[] = $row->cash;
					}elseif($row->flag == 7 ){
						$wallet_cash_SentMoney[] = $row->cash;
					}
					
					//$wallet_cash[] = $row->cash;
					
					$data['cash_list'][] = $row;
				}
				
				$wallet_cash = array_sum($wallet_cash_Rides) + array_sum($wallet_cash_Incentive) + array_sum($wallet_cash_Refunded) + array_sum($wallet_cash_AddMoney) + array_sum($wallet_cash_Transfer) - array_sum($wallet_cash_Deduction) - array_sum($wallet_cash_SentMoney);
				
				
            }
			$data['wallet_cash'] = number_format($wallet_cash, 2);
			
           
		}
		
		
		
	
	
		if(!empty($data)){
			return $data;
		}
		return false;	
	}
	
	/*function getWallets($user_id, $countryCode){
		$data = array();
		$this->db->select('flag, cash, description, created');
		$this->db->where('user_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('wallet');
		$data['wallet'] = "0";
		$data['driverpaid'] = "0";
		$data['list'] = [];
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				$wallet[] = $row->cash;
				
				if($row->flag == 1 ){
					$row->flag_name = 'Incentive';
				}elseif($row->flag == 2 ){
					$row->flag_name = 'Rides';
				}elseif($row->flag == 3 ){
					$row->flag_name = 'Refunded';
				}elseif($row->flag == 4 ){
					$row->flag_name = 'Deduction';
				}elseif($row->flag == 5 ){
					$row->flag_name = 'Transfer';
				}elseif($row->flag == 6 ){
					$row->flag_name = 'AddMoney';
				}elseif($row->flag == 7 ){
					$row->flag_name = 'SentMoney';
				}else{
					$row->flag_name = 'No';
				}
				
                $data['list'][] = $row;
            }
			$data['wallet'] = number_format(array_sum($wallet), 2);
           
		}
		
		$driver = $this->db->select('total_ride_amount')->where('is_country', $countryCode)->where('driver_id', $user_id)->where('is_edit', 1)->get('driver_payment');
		if ($driver->num_rows() > 0)
		{
			$data['driverpaid'] = $driver->row('total_ride_amount');
		}
	
		if(!empty($data)){
			return $data;
		}
		return false;	
	}*/
	
	function getTickets($user_id, $countryCode){
		$this->db->select('e.id as enquiry_id, e.enquiry_type, e.enquiry_code, e.enquiry_date,  IFNULL(h.name, 0) as help_title,  e.is_feedback');
		$this->db->from('enquiry e');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->where('e.customer_id', $user_id);
		
$this->db->where('e.is_country', $countryCode);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				if($row->status == 3 ){
					$row->status = '1';
				}else{
					$row->status = '0';
				}
				
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	function addenquiryFeedback($value, $enquiry_id, $customer_id, $countryCode){
		$this->db->where('id', $enquiry_id);
		$this->db->where('customer_id', $customer_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('enquiry', array('is_feedback' => 1));
		if($q){
			$value['is_country'] = $countryCode;
			$this->db->insert('enquiry_feedback', $value);
			return false;
		}
		return false;
	}
	
	function getEnquiryView($user_id, $enquiry_id, $countryCode){
		$this->db->select('e.id as enquiry_id, e.enquiry_type, e.enquiry_code, e.enquiry_date, e.enquiry_status, e.customer_status as customer_feed_status,  IFNULL(r.booking_no, 0) as booking_no,  IFNULL(hs.name, 0) as crm_sub_name, IFNULL(hm.name, 0) as crm_main_name ');
		$this->db->from('enquiry e');
		$this->db->join('users u', 'u.id = e.customer_id', 'left');
		$this->db->join('groups g', 'g.id = u.group_id', 'left');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->join('rides r', 'r.id = e.services_id', 'left');
		$this->db->join('users rd', 'rd.id = r.driver_id', 'left');
		$this->db->join('users rc', 'rc.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('help_sub hs', 'hs.id = e.help_id', 'left');
		$this->db->join('help_main hm', 'hm.id = hs.parent_id', 'left');
		
		$this->db->where('e.id',$enquiry_id);
		
$this->db->where('e.is_country', $countryCode);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$data =  $q->row();
			if($data->enquiry_status == 0){
				$data->enquiry_status_name = 'Process';
			}elseif($data->enquiry_status == 1){
				$data->enquiry_status_name = 'Open';
			}elseif($data->enquiry_status == 2){
				$data->enquiry_status_name = 'Transfer';
			}elseif($data->enquiry_status == 3){
				$data->enquiry_status_name = 'Close';
			}elseif($data->enquiry_status == 4){
				$data->enquiry_status_name = 'Reopen';
			}
			
			
			return $data;
		}
		return false;
	}
	
	function getEnquiryFollow($user_id, $enquiry_id, $countryCode){
		$this->db->select('IFNULL(u.first_name, 0) as support_name,  es.status, f.created_on,  IFNULL(f.discussion, 0) as discussion, IFNULL(f.remark, 0) as remark, h.name as help_name');
		$this->db->from('enquiry_support es');
		$this->db->join('follows f', 'f.enquiryid = es.enquiry_id AND f.enquiry_support_id = es.id');
		$this->db->join('help h', 'h.id = es.help_services', 'left');
		$this->db->join('users u', 'u.id = es.support_id', 'left');
		$this->db->join('groups g', 'g.id = u.group_id', 'left');
		$this->db->where('es.enquiry_id', $enquiry_id);
		
$this->db->where('es.is_country', $countryCode);
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				
					$row->discussion = strip_tags($row->discussion);
				
					$row->remark = strip_tags($row->remark);
					
					if($row->status == 0){
						$row->status_name = 'Process';
						$row->crm_title = 'Your ticket has been created';
					}elseif($row->status == 1){
						$row->status_name = 'Open';
						$row->crm_title = 'Your ticket has been allowcated to execute team';
					}elseif($row->status == 2){
						$row->status_name = 'Transfer';
						$row->crm_title = 'Your ticket has been transfer to another '.$data->help_name;
					}elseif($row->status == 3){
						$row->status_name = 'Close';
						$row->crm_title = 'Your issues has been closed';
					}elseif($row->status == 4){
						$row->status_name = 'Reopen';
						$row->crm_title = 'Your ticket has been Reopen';
					}
				
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	function getHelpmain($user_id, $help, $countryCode){
		$this->db->select('hm.id as id, hm.name as name');
		$this->db->from('help h');
		$this->db->join('help_main hm', 'hm.parent_id = h.id', 'left');
		$this->db->where('h.name', $help);
		
$this->db->where('h.is_country', $countryCode);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->result();	
		}
		return false;
	}
	
	function getHelpsub($user_id, $parent_id, $countryCode){
		$this->db->select('id, name');
		$this->db->where('parent_id', $parent_id);
		
$this->db->where('is_country', $countryCode);
		$q = $this->db->get('help_sub');
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				$row->weblink = site_url('help');
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	function get_driver_location($user_id, $countryCode){
		$myQuery = "SELECT d.id, IFNULL(dcs.current_latitude, 0) as lat, IFNULL(dcs.current_longitude, 0) as lng,  r.start_lat as pickup_lat, r.start_lng as pickup_lng, r.end_lat as drop_lat, r.end_lng as drop_lng, r.customer_id, r.id AS ride_id, IFNULL(df.location, 0) as location, IFNULL(df.final_distance, 0) as final_distance, IFNULL(df.final_distance_total, 0) as final_distance_total, r.status, us.socket_id, us.id AS usid FROM kapp_users AS d  LEFT  JOIN kapp_rides AS r ON r.customer_id = d.id AND (r.status = 2 OR  r.status = 3) LEFT  JOIN kapp_driver_frequency AS df ON df.ride_id = r.id AND df.driver_id = d.id LEFT  JOIN kapp_user_socket AS us ON us.user_id = r.customer_id AND us.user_type = 1 LEFT JOIN kapp_driver_current_status dcs ON dcs.driver_id = r.driver_id AND dcs.is_connected = 1 AND dcs.mode = 3 AND dcs.allocated_status = 1
		WHERE d.id = ".$user_id." AND d.is_country = '".$countryCode."' ORDER BY d.id DESC LIMIT 1";
		
		$q = $this->db->query($myQuery);
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			$row->actual_lat = 0;
			$row->actual_lng = 0;
			$data = $row;
			return $data;
		}
		
		return false;	
	}
	
	function insertToolparking($customer_id, $ride_id, $type, $status, $countryCode){
		$q = $this->db->select('*')->where('ride_id', $ride_id)->where('is_country', $countryCode)->where('type', $type)->get('tool_parking');
		if ($q->num_rows() > 0) {
			return 2;
		}else{
			
			$this->db->insert('tool_parking', array('ride_id' => $ride_id, 'is_country' => $countryCode, 'type' => $type, 'status' => $status, 'created_on' => date('Y-m-d H:i:s')));
			return 1;
		}
		return 0;	
	}
	
	function edit_customer_photo($user_id, $user, $user_profile, $countryCode){
		if(!empty($user_id)){
			$this->db->update('user_profile', $user_profile, array('user_id' => $user_id, 'is_country' => $countryCode));
			$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $countryCode));
			return true;
		}
		return false;
	}
	
	function getRentalPackage($city_id, $countryCode){
		$this->db->select('package_name');
		$this->db->from('rental_fare');
		$this->db->where('is_country', $countryCode);
		$this->db->group_by('package_name');
		$q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	
	function getOutstationPackagetype($from_city, $to_city, $countryCode){
		
		if($from_city == 0 &&  $to_city != 0){
			$from_city = 0;
			$to_city = 0;
		}elseif($from_city != 0 &&  $to_city == 0){
			$from_city = 0;
			$to_city = 0;
		}else{
			$from_city = $from_city;
			$to_city = $to_city;
		}
		$image_path = base_url('assets/uploads/');
		
		$myQuery = "SELECT O.id, O.from_city_id, O.to_city_id, O.taxi_type, O.is_oneway, O.is_twoway, O.package_name, O.oneway_package_price, O.twoway_package_price, O.min_per_distance, O.min_per_distance_price, O.per_distance, O.per_distance_price, O.driver_allowance_per_day, O.driver_night_per_day, O.is_default, TT.name as taxi_type_name, TT.image, TT.image_hover, TT.mapcar, TT.outstation_image FROM {$this->db->dbprefix('outstation_fare')} AS O JOIN {$this->db->dbprefix('taxi_type')} AS TT ON TT.id = O.taxi_type WHERE O.from_city_id = ".$from_city."  AND O.to_city_id = ".$to_city." AND O.is_country = '".$countryCode."' ";
		
		
		
		$q = $this->db->query($myQuery);
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				if($row->image !=''){
					$row->image = $image_path.$row->image;
				}else{
					$row->image = $image_path.'no_image.png';
				}
				
				if($row->image_hover !=''){
					$row->image_hover = $image_path.$row->image_hover;
				}else{
					$row->image_hover = $image_path.'no_image.png';
				}
				
				if($row->mapcar !=''){
					$row->mapcar = $image_path.$row->mapcar;
				}else{
					$row->mapcar = $image_path.'no_image.png';
				}
				if($row->outstation_image !=''){
					$row->outstation_image = $image_path.$row->outstation_image;
				}else{
					$row->outstation_image = $image_path.'no_image.png';
				}
				
				if($row->is_oneway == 1){
					$fixed = array(
						'taxi_type' => $row->taxi_type,
						'taxi_type_name' => $row->taxi_type_name,
						'package_id' => $row->id,
						'package_name' => $row->package_name,
						'oneway_package_price' => $row->oneway_package_price,
						'twoway_package_price' => $row->twoway_package_price,
						'image' => $row->image,
						'image_hover' => $row->image_hover,
						'mapcar' => $row->mapcar,
						'outstation_image' => $row->outstation_image,
						'driver_allowance_per_day' => $row->driver_allowance_per_day,
						'driver_night_per_day' => $row->driver_night_per_day,
					);	
					$data['fixed'][] = $fixed;
				}
				if($row->is_twoway == 1){
					$variable = array(
						'taxi_type' => $row->taxi_type,
						'taxi_type_name' => $row->taxi_type_name,
						'package_id' => $row->id,
						'package_name' => $row->package_name,
						'per_unit' => 'Kms',
						'per_distance' => $row->per_distance,
						'per_distance_price' => $row->per_distance_price,
						'image' => $row->image,
						'image_hover' => $row->image_hover,
						'mapcar' => $row->mapcar,
						'outstation_image' => $row->outstation_image,
						'driver_allowance_per_day' => $row->driver_allowance_per_day,
						'driver_night_per_day' => $row->driver_night_per_day,
					);
					$data['variable'][] = $variable;
				}
				
				
			}
			
			return $data;
		}else{
			$myQuery_default = "SELECT O.id, O.from_city_id, O.to_city_id, O.taxi_type, O.is_oneway, O.is_twoway, O.package_name, O.oneway_package_price, O.twoway_package_price, O.min_per_distance, O.min_per_distance_price, O.per_distance, O.per_distance_price, O.driver_allowance_per_day, O.driver_night_per_day, O.is_default, TT.name as taxi_type_name, TT.image, TT.image_hover, TT.mapcar, TT.outstation_image FROM {$this->db->dbprefix('outstation_fare')} AS O JOIN {$this->db->dbprefix('taxi_type')} AS TT ON TT.id = O.taxi_type WHERE O.from_city_id = 0  AND O.to_city_id = 0 AND O.is_country = '".$countryCode."' ";
		
			$default = $this->db->query($myQuery_default);
			foreach (($default->result()) as $row_default) {
				if($row_default->image !=''){
					$row_default->image = $image_path.$row_default->image;
				}else{
					$row_default->image = $image_path.'no_image.png';
				}
				
				if($row_default->image_hover !=''){
					$row_default->image_hover = $image_path.$row_default->image_hover;
				}else{
					$row_default->image_hover = $image_path.'no_image.png';
				}
				
				if($row_default->mapcar !=''){
					$row_default->mapcar = $image_path.$row_default->mapcar;
				}else{
					$row_default->mapcar = $image_path.'no_image.png';
				}
				if($row_default->outstation_image !=''){
					$row_default->outstation_image = $image_path.$row_default->outstation_image;
				}else{
					$row_default->outstation_image = $image_path.'no_image.png';
				}
				
				if($row_default->is_oneway == 1){
					$fixed = array(
						'taxi_type' => $row_default->taxi_type,
						'taxi_type_name' => $row_default->taxi_type_name,
						'package_id' => $row_default->id,
						'package_name' => $row_default->package_name,
						'oneway_package_price' => $row_default->oneway_package_price,
						'twoway_package_price' => $row_default->twoway_package_price,
						'image' => $row_default->image,
						'image_hover' => $row_default->image_hover,
						'mapcar' => $row_default->mapcar,
						'outstation_image' => $row_default->outstation_image,
						'driver_allowance_per_day' => $row_default->driver_allowance_per_day,
						'driver_night_per_day' => $row_default->driver_night_per_day,
					);	
					$data['fixed'][] = $fixed;
				}
				if($row_default->is_twoway == 1){
					$variable = array(
						'taxi_type' => $row_default->taxi_type,
						'taxi_type_name' => $row_default->taxi_type_name,
						'package_id' => $row_default->id,
						'package_name' => $row_default->package_name,
						'per_unit' => 'Kms',
						'per_distance' => $row_default->per_distance,
						'per_distance_price' => $row_default->per_distance_price,
						'image' => $row_default->image,
						'image_hover' => $row_default->image_hover,
						'mapcar' => $row_default->mapcar,
						'outstation_image' => $row_default->outstation_image,
						'driver_allowance_per_day' => $row_default->driver_allowance_per_day,
						'driver_night_per_day' => $row_default->driver_night_per_day,
					);
					$data['variable'][] = $variable;
				}
				
				
			}
			
			return $data;
		}
		
		return false;
	}
	
	function getRentalPackagetype($city_id, $package_name, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$myQuery = "SELECT R.id, R.city_id, R.taxi_type, R.per_distance, R.per_distance_price, R.per_time, R.per_time_price, R.option_type, R.option_price, R.package_name, R.package_price, R.time_type, TT.name as taxi_type_name, TT.image, TT.image_hover, TT.mapcar, TT.outstation_image FROM {$this->db->dbprefix('rental_fare')} AS R JOIN {$this->db->dbprefix('taxi_type')} AS TT ON TT.id = R.taxi_type  WHERE R.package_name = '".$package_name."' AND R.is_country = '".$countryCode."' GROUP BY  R.taxi_type ";
		$q = $this->db->query($myQuery);
		
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
				if($row->option_type == 1){
					$row->post_package = 'post package '.$row->per_distance.'Km - '.$row->per_distance_price.'';
				}elseif($row->option_type == 2){
					 if($row->time_type == 1){
						 $time = date("g", strtotime($row->per_time)).'hour';
					 }else{
						 $time = date("i", strtotime($row->per_time)).'min';
					 }
					 
					 
					$row->post_package = 'post package '.$time.' - '.$row->per_time_price.'';
				}elseif($row->option_type == 0){
					$row->post_package = 'Post package apllied as per company norms';
				}
				
				if($row->image !=''){
					$row->image = $image_path.$row->image;
				}else{
					$row->image = $image_path.'no_image.png';
				}
				
				if($row->image_hover !=''){
					$row->image_hover = $image_path.$row->image_hover;
				}else{
					$row->image_hover = $image_path.'no_image.png';
				}
				
				if($row->mapcar !=''){
					$row->mapcar = $image_path.$row->mapcar;
				}else{
					$row->mapcar = $image_path.'no_image.png';
				}
				if($row->outstation_image !=''){
					$row->outstation_image = $image_path.$row->outstation_image;
				}else{
					$row->outstation_image = $image_path.'no_image.png';
				}
				$row->package_id = $row->id;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	function insertbank($user_id, $group_id, $customer_type, $insert, $countryCode){
		$this->db->where('user_id', $user_id);
		$this->db->where('is_edit', 1);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('user_bank', $insert);
		if($q){
			return true;
		}
		return false;
	}
	function getSettingmode($countryCode){
		$q = $this->db->select('site_name, camera_enable, support_email, support_mobile, support_whatsapp, outstation_min_kilometer, rental_max_kilometer, cityride_max_kilometer')->where('is_country', $countryCode)->get('settings');
		if ($q->num_rows() > 0) {
			$data[] = $q->row();
			return $data;	
		}
		return false;
	}
	 function FaregetAllTaxiTypes(){
		 $this->db->select('T.*, DF.base_min_distance_price as min_price');
		 $this->db->from('taxi_type T');
		 $this->db->join('daily_fare DF', 'DF.taxi_type = T.id AND DF.is_default = 1', 'LEFT')->where('T.is_country', $countryCode);
		 
		 $q = $this->db->get();
		
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	/*function FaregetAllTaxiTypesnew($latitude, $longitude, $distance){
		
		$query = "SELECT tt.*  FROM {$this->db->dbprefix('taxi_type')}  AS tt 
		LEFT JOIN {$this->db->dbprefix('daily_fare')} AS df ON df.taxi_type = tt.id  AND df.is_default = 1
		";
		
		
		$q = $this->db->query($query);
		
		
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
				$row->units = 'Km';
				//echo $row->id;
				
				$fare[$row->id] = $this->site->getFareestimate($latitude, $longitude, $row->id, 1);
				
				//print_r($fare[$row->id]);
				$row->min_price = $fare[$row->id]['min_distance_price'];
				$row->min_distance = $fare[$row->id]['min_distance'];
				$row->per_distance = $fare[$row->id]['per_distance'];
				$row->per_distance_price = $fare[$row->id]['per_distance_price'];	
				
				
				$query1 = "SELECT  COUNT(d.id) as available, ( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$longitude}) ) + sin( radians({$latitude}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id 
	 
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE tt.id = ".$row->id." AND  dcs.mode = 1 AND dcs.is_connected = 1 AND dcs.allocated_status = 1 GROUP BY d.id   HAVING distance <= {$distance}  
ORDER BY distance ASC LIMIT 1";
				
				$t = $this->db->query($query1);
				//print_r($this->db->last_query());
				if ($t->num_rows() > 0) {
					$row->available = $t->row('available');					
					
					
					 $data[] = $row;
				}else{
					$row->available = "0";
					//$data[] = $row;
				}
               
			   
				
            }
			//print_r($data);
			//die;
            return $data;
        }
		
		
		return FALSE;
	}*/
	
	function FaregetAllTaxiTypesnew($latitude, $longitude, $distance, $countryCode){
		
		$query = "SELECT tt.*  FROM {$this->db->dbprefix('taxi_type')}  AS tt 
		 JOIN {$this->db->dbprefix('daily_fare')} AS df ON df.taxi_type = tt.id  AND df.is_default = 1 
		 WHERE tt.is_country = '".$countryCode."' GROUP BY tt.id
		";
		
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		
		if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
				
				$row->units = 'Km';
				//echo $row->id;
				
				$fare[$row->id] = $this->site->getFareestimate($latitude, $longitude, $row->id, 1, $countryCode);
				
				//print_r($fare[$row->id]);
				$row->min_price = $fare[$row->id]['min_distance_price'] != NULL ? $fare[$row->id]['min_distance_price'] : '0';
				$row->min_distance = $fare[$row->id]['min_distance'] != NULL ? $fare[$row->id]['min_distance'] : '0';
				$row->per_distance = $fare[$row->id]['per_distance'] != NULL ? $fare[$row->id]['per_distance'] : '0';
				$row->per_distance_price = $fare[$row->id]['per_distance_price'] != NULL ? $fare[$row->id]['per_distance_price'] : '0';	
				
				
				$query1 = "SELECT  COUNT(d.id) as available, ( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$longitude}) ) + sin( radians({$latitude}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	
	JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  AND us.ride_stop = 0
	WHERE tt.id = ".$row->id."  AND  dcs.mode = 1  AND dcs.is_connected = 1 AND dcs.allocated_status = 1 GROUP BY d.id   HAVING distance <= {$distance}  
ORDER BY distance ASC LIMIT 1";
				
				$t = $this->db->query($query1);
				//print_r($this->db->last_query());
				if ($t->num_rows() > 0) {
					$row->available = $t->row('available');					
					
					
					 $data[] = $row;
				}else{
					$row->available = "0";
					//$data[] = $row;
				}
               
			   
				
            }
			//print_r($data);
			//die;
            return $data;
        }
		
		
		return FALSE;
	}
	
	
	public function getPaymentmode(){
		$q = $this->db->select('id, name')->where('name !=', 'Payment Gateway')->where('status', 1)->get('payment_mode');	
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				
				$data[] = $row;
				
			}
			return $data;
		}
		return false;
	}
	public function multipleRatingadd($data, $countryCode){
		$data['is_country'] = $countryCode;
		$q  = $this->db->insert('multiple_rating', $data);
		if($q){
			return true;	
		}
		return false;
		
	}
	
	public function deviceGET($user_id, $user_type, $countryCode){
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
	
	public function getUserEdit($user_id, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, u.email, u.country_code, u.mobile,  u.active, u.is_approved as user_approved, up.is_approved as profile_is_approved, up.approved_by as profile_approved_by, up.approved_on as profile_approved_on,  u.group_id, u.parent_id, ud.local_verify, ud.local_image, ud.local_address, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id, lc.name as local_continent_name, ud.local_country_id, lcc.name as local_country_name, ud.local_zone_id, lz.name as local_zone_name, ud.local_state_id, ls.name as local_state_name, ud.local_city_id, lcity.name as local_city_name, ud.local_area_id, la.name as local_area_name, ud.permanent_verify, ud.permanent_approved_by, ud.permanent_approved_on, ud.permanent_image, ud.permanent_address, ud.permanent_continent_id, pc.name as permanent_continent_name, ud.permanent_country_id, pcc.name as permanent_country_name, ud.permanent_zone_id, pz.name as permanent_zone_name, ud.permanent_state_id, ps.name as permanent_state_name, ud.permanent_city_id, pcity.name as permanent_city_name, ud.permanent_area_id, pa.name as permanent_area_name, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, ub.account_no, ub.is_verify as account_verify, ub.bank_name, ub.branch_name, ub.ifsc_code, udoc.aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  udoc.pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image, udoc.license_approved_by, udoc.license_approved_on, udoc.license_verify, udoc.license_dob, udoc.license_ward_name, udoc.license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, udoc.police_on, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, udoc.loan_information, udoc.loan_verify, u.first_name, up.last_name, up.gender, up.dob, up.photo, ugroup.name as group_name, pgroup.name as parent_group_name, userper.department_id, ur.position,  userper.designation_id, userdep.name as user_department, userper.continent_id, urc.name as continent_name, userper.country_id, urcc.name as country_name, userper.zone_id, urz.name as zone_name, userper.state_id, urs.name as state_name, userper.city_id, urcity.name as city_name, userper.area_id, ura.name as area_name, uv.gst, uv.telephone_number, uv.legal_entity, uv.associated_id, uv.continent_id as vendor_continent_id, uv.country_id as vendor_country_id, uv.zone_id as vendor_zone_id, uv.state_id as vendor_state_id, uv.city_id as vendor_city_id, uv.is_verify as vendor_is_verify, uv.approved_by as vendor_approved_by, uv.approved_on as vendor_approved_on, assoc.first_name as associated_name');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id AND uv.is_edit = 1', 'left');
		$this->db->join('user_profile assoc', 'assoc.user_id = uv.associated_id AND assoc.is_edit = 1', 'left');
		$this->db->join('user_address ud', 'ud.user_id = u.id AND ud.is_edit = 1', 'left');
		$this->db->join('user_bank ub', 'ub.user_id = u.id AND ub.is_edit = 1', 'left');
		$this->db->join('user_document udoc', 'udoc.user_id = u.id AND udoc.is_edit = 1', 'left');
		$this->db->join('user_profile up', 'up.user_id = u.id AND up.is_edit = 1', 'left');
		
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id AND userper.is_edit = 1', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		
		$this->db->join('continents lc', 'lc.id = ud.local_continent_id', 'left');
		$this->db->join('countries lcc', 'lcc.id = ud.local_country_id', 'left');
		$this->db->join('zones lz', 'lz.id = ud.local_zone_id', 'left');
		$this->db->join('states ls', 'ls.id = ud.local_state_id', 'left');
		$this->db->join('cities lcity', 'lcity.id = ud.local_city_id', 'left');
		$this->db->join('areas la', 'la.id = ud.local_area_id', 'left');
		
		$this->db->join('continents pc', 'pc.id = ud.permanent_continent_id', 'left');
		$this->db->join('countries pcc', 'pcc.id = ud.permanent_country_id', 'left');
		$this->db->join('zones pz', 'pz.id = ud.permanent_zone_id', 'left');
		$this->db->join('states ps', 'ps.id = ud.permanent_state_id', 'left');
		$this->db->join('cities pcity', 'pcity.id = ud.permanent_city_id', 'left');
		$this->db->join('areas pa', 'pa.id = ud.permanent_area_id', 'left');
		
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		
		$this->db->where('u.is_edit', 1);
		$this->db->where('u.id', $user_id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row =  $q->row();
			if($row->dob == '0000-00-00' || $row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = date("d/m/Y", strtotime($row->dob));
			}
			$data = $row;
			
			
			
            return $data;
        }
		return false;	
	}
	
	function checkCustomers($user_id, $group_id, $countryCode){
		$q = $this->db->select("u.id as id, u.first_name, up.last_name, u.email, u.mobile,  up.gender, ub.is_verify, ud.aadhar_verify, ud.pancard_verify, uadd.local_verify, uadd.permanent_verify")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id)->where('u.is_country', $countryCode)
			->get();
		
		if($q->num_rows()>0){
			
			if($q->row('is_verify') == NULL && $q->row('aadhar_verify') == NULL && $q->row('pancard_verify') == NULL && $q->row('local_verify') == NULL && $q->row('permanent_verify') == NULL){
				return 3;
			}elseif($q->row('is_verify') == 0 || $q->row('aadhar_verify') == 0 || $q->row('pancard_verify') == 0 || $q->row('local_verify') == 0 || $q->row('permanent_verify') == 0){
				return 2;
			}else{
				return 1;
			}
		}
		return 0;	
	}
	
	function edit_customer($user_id, $user, $user_profile, $countryCode){
		
        if(!empty($user_id)){
			
				$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $countryCode));
				$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $countryCode));
				$user_profile['user_id'] = $user_id;
				$user_profile['is_country'] = $countryCode;
				$this->db->insert('user_profile', $user_profile);
			
	    	return true;
		}
		return false;
    }
	
	public function myprofile($user_id, $customer_group, $customer_type, $countryCode){
		
	
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, IFNULL(u.email, 0) as email, IFNULL(u.country_code, 0) as country_code, IFNULL(u.mobile, 0) as mobile, u.active, IFNULL(u.is_approved, 0) as user_approved,  IFNULL(u.group_id, 0) as group_id, IFNULL(u.parent_id, 0) as parent_id, IFNULL(ud.local_verify, 0) as local_verify, ud.local_image, IFNULL(ud.local_address, 0) as local_address, IFNULL(ud.local_approved_by, 0) as local_approved_by, IFNULL(ud.local_approved_on, 0) as local_approved_on, IFNULL(ud.local_continent_id, 0) as local_continent_id, IFNULL(lc.name, 0) as local_continent_name, ud.local_country_id, IFNULL(lcc.name, 0) as local_country_name, ud.local_zone_id, IFNULL(lz.name, 0) as local_zone_name, ud.local_state_id, IFNULL(ls.name, 0) as local_state_name, ud.local_city_id, IFNULL(lcity.name, 0) as local_city_name, ud.local_area_id, IFNULL(la.name, 0) as local_area_name, ud.permanent_verify, ud.permanent_approved_by, ud.permanent_approved_on, ud.permanent_image, IFNULL(ud.permanent_address, 0) as permanent_address, ud.permanent_continent_id, IFNULL(pc.name, 0) as permanent_continent_name, ud.permanent_country_id, IFNULL(pcc.name, 0) as permanent_country_name, ud.permanent_zone_id, IFNULL(pz.name, 0) as permanent_zone_name, ud.permanent_state_id, IFNULL(ps.name, 0) as permanent_state_name, ud.permanent_city_id, IFNULL(pcity.name, 0) as permanent_city_name, ud.permanent_area_id, IFNULL(pa.name, 0) as permanent_area_name, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, IFNULL(ub.account_no, 0) as account_no, ub.is_verify as account_verify, IFNULL(ub.bank_name, 0) as bank_name, IFNULL(ub.branch_name, 0) as branch_name, IFNULL(ub.ifsc_code, 0) as ifsc_code, IFNULL(udoc.aadhaar_no, 0) as aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  IFNULL(udoc.pancard_no, 0) as pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image, udoc.license_approved_by, udoc.license_approved_on, udoc.license_verify, IFNULL(udoc.license_dob, 0) as license_dob, IFNULL(udoc.license_ward_name, 0) as license_ward_name, IFNULL(udoc.license_type, 0) as license_type, IFNULL(udoc.license_issuing_authority, 0) as license_issuing_authority, IFNULL(udoc.license_issued_on, 0) as license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, IFNULL(udoc.police_on, 0) as police_on, IFNULL(udoc.police_til, 0) as police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, IFNULL(udoc.loan_information, 0) as loan_information, udoc.loan_verify, IFNULL(u.first_name, 0) as first_name, IFNULL(up.last_name, 0) as last_name, IFNULL(up.gender, 0) as gender, IFNULL(up.dob, 0) as dob, u.photo, IFNULL(ugroup.name, 0) as group_name, IFNULL(pgroup.name, 0) as parent_group_name, userper.department_id, IFNULL(ur.position, 0) as position,  userper.designation_id, IFNULL(userdep.name, 0) as user_department, userper.continent_id, IFNULL(urc.name, 0) as continent_name, userper.country_id, IFNULL(urcc.name, 0) as country_name, userper.zone_id, IFNULL(urz.name, 0) as zone_name, userper.state_id, IFNULL(urs.name, 0) as state_name, userper.city_id, IFNULL(urcity.name, 0) as city_name, userper.area_id, IFNULL(ura.name, 0) as area_name, IFNULL(uv.gst, 0) as gst, IFNULL(uv.telephone_number, 0) as telephone_number, IFNULL(uv.legal_entity, 0) as legal_entity, uv.associated_id, IFNULL(assoc.first_name, 0) as associated_name');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id AND uv.is_edit = 1', 'left');
		$this->db->join('user_profile assoc', 'assoc.user_id = uv.associated_id AND assoc.is_edit = 1', 'left');
		$this->db->join('user_address ud', 'ud.user_id = u.id AND ud.is_edit = 1', 'left');
		$this->db->join('user_bank ub', 'ub.user_id = u.id AND ub.is_edit = 1', 'left');
		$this->db->join('user_document udoc', 'udoc.user_id = u.id AND udoc.is_edit = 1', 'left');
		$this->db->join('user_profile up', 'up.user_id = u.id AND up.is_edit = 1', 'left');
		
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id AND userper.is_edit = 1', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		
		$this->db->join('continents lc', 'lc.id = ud.local_continent_id', 'left');
		$this->db->join('countries lcc', 'lcc.id = ud.local_country_id', 'left');
		$this->db->join('zones lz', 'lz.id = ud.local_zone_id', 'left');
		$this->db->join('states ls', 'ls.id = ud.local_state_id', 'left');
		$this->db->join('cities lcity', 'lcity.id = ud.local_city_id', 'left');
		$this->db->join('areas la', 'la.id = ud.local_area_id', 'left');
		
		$this->db->join('continents pc', 'pc.id = ud.permanent_continent_id', 'left');
		$this->db->join('countries pcc', 'pcc.id = ud.permanent_country_id', 'left');
		$this->db->join('zones pz', 'pz.id = ud.permanent_zone_id', 'left');
		$this->db->join('states ps', 'ps.id = ud.permanent_state_id', 'left');
		$this->db->join('cities pcity', 'pcity.id = ud.permanent_city_id', 'left');
		$this->db->join('areas pa', 'pa.id = ud.permanent_area_id', 'left');
		
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		
		$this->db->where('u.group_id', $customer_group);
		$this->db->where('u.id', $user_id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			
			if($row->photo !=''){
				$row->photo = $image_path.$row->photo;
			}else{
				$row->photo = $image_path.'no_image.png';
			}
			
			if($row->local_image !=''){
				$row->local_image = $image_path.$row->local_image;
			}else{
				$row->local_image = $image_path.'no_image.png';
			}
			
			if($row->permanent_image !=''){
				$row->permanent_image = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image !=''){
				$row->aadhaar_image = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image = $image_path.'no_image.png';
			}
			
			if($row->pancard_image !=''){
				$row->pancard_image = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image = $image_path.'no_image.png';
			}
			if($row->dob == '0000-00-00' || $row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = date("d/m/Y", strtotime($row->dob));
			}
			
			if($customer_group == 5 && $customer_type == 1){
				$driver_data = array(
					'user_id' => $row->id,
					'email' => $row->email,
					'country_code' => $row->country_code,
					'mobile' => $row->mobile,
					'active' => $row->active,
					'first_name' => $row->first_name,
					'last_name' => $row->last_name,
					'gender' => $row->gender,
					'dob' => $row->dob,
					'photo' => $row->photo,
					'group_name' => $row->group_name,
					'local_address' => $row->local_address,
					'local_continent_name' => $row->local_continent_name,
					'local_country_name' => $row->local_country_name,
					'local_zone_name' => $row->local_zone_name,
					'local_state_name' => $row->local_state_name,
					'local_city_name' => $row->local_city_name,
					'local_area_name' => $row->local_area_name,
					'permanent_address' => $row->permanent_address,
					'permanent_continent_name' => $row->permanent_continent_name,
					'permanent_country_name' => $row->permanent_country_name,
					'permanent_zone_name' => $row->permanent_zone_name,
					'permanent_state_name' => $row->permanent_state_name,
					'permanent_city_name' => $row->permanent_city_name,
					'permanent_area_name' => $row->permanent_area_name,
					
				);
			}elseif($customer_group == 5 && $customer_type == 2){
				$driver_data = array(
					'user_id' => $row->id,
					'account_no' => $row->account_no,
					'bank_name' => $row->bank_name,
					'branch_name' => $row->branch_name,
					'ifsc_code' => $row->ifsc_code,
				);
			}elseif($customer_group == 5 && $customer_type == 3){
				$driver_data = array(
					'user_id' => $row->id,
					'aadhaar_no' => $row->aadhaar_no,
					'pancard_no' => $row->pancard_no,
					

				);
			}
            return $driver_data;
        }
		return FALSE;
	}
	
	public function myprofilebank($user_id, $customer_group, $customer_type, $countryCode){
		
	
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, IFNULL(ub.is_credit, 0) as is_credit, IFNULL(ub.credit_name, 0) as credit_name, IFNULL(ub.credit_number, 0) as credit_number, IFNULL(ub.credit_month, 0) as credit_month,  IFNULL(ub.credit_year, 0) as credit_year, IFNULL(ub.credit_cvv, 0) as credit_cvv, IFNULL(ub.credit_verify, 0) as credit_verify, IFNULL(ub.is_debit, 0) as is_debit, IFNULL(ub.debit_name, 0) as debit_name, IFNULL(ub.credit_approved_by, 0) as credit_approved_by, IFNULL(ub.credit_approved_on, 0) as credit_approved_on, IFNULL(ub.debit_number, 0) as debit_number,  IFNULL(ub.debit_month, 0) as debit_month,  IFNULL(ub.debit_year, 0) as debit_year, IFNULL(ub.debit_cvv, 0) as debit_cvv,   IFNULL(ub.debit_verify, 0) as debit_verify,   IFNULL(ub.debit_approved_by, 0) as debit_approved_by,  IFNULL(ub.debit_approved_on, 0) as debit_approved_on, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, IFNULL(ub.account_no, 0) as account_no, ub.is_verify as account_verify, IFNULL(ub.bank_name, 0) as bank_name, IFNULL(ub.branch_name, 0) as branch_name, IFNULL(ub.ifsc_code, 0) as ifsc_code');
		$this->db->from('users u');
		
		$this->db->join('user_bank ub', 'ub.user_id = u.id AND ub.is_edit = 1', 'left');
		
		$this->db->where('u.group_id', $customer_group);
		$this->db->where('u.id', $user_id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			
			if($row->photo !=''){
				$row->photo = $image_path.$row->photo;
			}else{
				$row->photo = $image_path.'no_image.png';
			}
			
			if($row->local_image !=''){
				$row->local_image = $image_path.$row->local_image;
			}else{
				$row->local_image = $image_path.'no_image.png';
			}
			
			if($row->permanent_image !=''){
				$row->permanent_image = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image !=''){
				$row->aadhaar_image = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image = $image_path.'no_image.png';
			}
			
			if($row->pancard_image !=''){
				$row->pancard_image = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image = $image_path.'no_image.png';
			}
			
			if($row->license_image !=''){
				$row->license_image = $image_path.$row->license_image;
			}else{
				$row->license_image = $image_path.'no_image.png';
			}
			
			if($row->police_image !=''){
				$row->police_image = $image_path.$row->police_image;
			}else{
				$row->police_image = $image_path.'no_image.png';
			}
			
			if($row->loan_doc !=''){
				$row->loan_doc = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc = $image_path.'no_image.png';
			}
			
			if($customer_group == 5 && $customer_type == 1){
				if($row->credit_name == 0 && $row->credit_number == 0){
					return FALSE;
				}else{
					$customer_data = array(
						
						'is_credit' => $row->is_credit,
						'credit_name' => $row->credit_name,
						'credit_number' => $row->credit_number,
						'credit_month' => $row->credit_month,
						'credit_year' => $row->credit_year,
						'credit_cvv' => $row->credit_cvv,
						
					);
					return $customer_data;
				}
			}elseif($customer_group == 5 && $customer_type == 2){
				if($row->debit_name == 0 && $row->debit_number == 0){
					return FALSE;
				}else{
					$customer_data = array(
						'is_debit' => $row->is_debit,
						'debit_name' => $row->debit_name,
						'debit_number' => $row->debit_number,
						'debit_month' => $row->debit_month,
						'debit_year' => $row->debit_year,
						'debit_cvv' => $row->debit_cvv,
					);
					return $customer_data;
				}
			}
            
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
	
	function fcminsert($data, $countryCode){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->where('is_country', $countryCode);
			$this->db->update('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}else{
			$this->db->insert('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'created_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	function fcmdelete($data, $countryCode){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->where('is_country', $countryCode);
			$this->db->update('devices', array('user_id' => 0, 'user_type' => 0, 'devices_type' => 0, 'device_imei' => '', 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	function getSettings($countryCode){
		$q = $this->db->select('*')->where('is_country', $countryCode)->get('settings');
		if($q->num_rows() > 0){
			return $q->row();	
		}
		return false;
	}
	
	function checkMobile($mobile, $country_code, $countryCode){
		$q = $this->db->select('*')->where('mobile', $mobile)->where('is_country', $countryCode)->where('country_code', $country_code)->where('group_id', 5)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return false;
	}
	
	function checkEmail($oauth_token, $email, $countryCode){
		$q = $this->db->select('*')->where('email', $email)->where('is_country', $countryCode)->where('oauth_token != ', $oauth_token)->get('users');
		if($q->num_rows() > 0){
			return true;	
		}
		return false;
	}
	
	/*function edit_customer($data){
		$this->db->where('oauth_token', $data['oauth_token']);	
		$q = $this->db->update('customers', array('email' => $data['email'], 'first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'dob' => $data['dob'], 'country_code' => $data['country_code'], 'mobile' => $data['mobile'], 'photo' => $data['photo']));
		if($q){
			return true;
		}
		return false;
	}*/
	
	function getCategorytypes($countryCode){
		$default_image = site_url('assets/uploads/car_no_image.png');
		$default_url = site_url('assets/uploads/');
		
		$c = $this->db->select("id, name, 'types'")->where('is_country', $countryCode)->where('status', 1)->get('taxi_categorys');
		
		if($c->num_rows()>0){
			
			foreach (($c->result()) as $cow) {
				$data[] = $cow;
				$q = $this->db->select('id, name, image, image_hover, mapcar')->where('is_country', $countryCode)->where('category_id', $cow->id)->where('status', 1)->get('taxi_type');	
				if($q->num_rows()>0){
			
					foreach (($q->result()) as $row) {
						
						if(!empty($row->image)){
							$row->image = $default_url.$row->image;
						} else {
							$row->image = $default_image;
						}
						if(!empty($row->image_hover)){
							$row->image_hover = $default_url.$row->image_hover;
						} else {
							$row->image_hover = $default_image;
						}
						
						if(!empty($row->mapcar)){
							$row->mapcar = $default_url.$row->mapcar;
						} else {
							$row->mapcar = $default_image;
						}
						
						$types[$cow->id][] = $row;
					}
					$cow->types = $types[$cow->id];
				}
			}
			return $data;
		}
		return false;
	}
	
	function  myrides($customer_id, $countryCode){
		$image_path = base_url('assets/uploads/drivers/photo/');
		
		$this->db->select('r.status, r.pick_up, r.drop_off, r.ride_start_time, r.ride_end_time, t.name taxi_name, t.number, tb.name brands, tc.name colors, tt.name types, d.first_name driver_name');		
		$this->db->from('rides r');
		$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
		$this->db->join('customers c', 'c.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
		$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id)->where('r.is_country', $countryCode);
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
	
	function getRidedetailsNEW($customer_id, $ride_id, $countryCode){
		$this->db->select('r.*, IFNULL(rp.driver_allowance, 0) as driver_allowance, IFNULL(rp.total_night_halt, 0) as total_night_halt, IFNULL(rp.total_toll, 0) as total_toll, IFNULL(rp.total_parking, 0) as total_parking, IFNULL(rp.total_distance, 0) as total_distance, IFNULL(rp.total_fare, 0) as total_fare, IFNULL(rp.extra_fare, 0) as extra_fare, IFNULL(mr.overall, 0) as overall, IFNULL(mr.drive_comfort_star, 0) as drive_comfort_star, IFNULL(mr.booking_process_star, 0) as booking_process_star, IFNULL(mr.cab_cleanliness_star, 0) as cab_cleanliness_star, IFNULL(mr.drive_politeness_star, 0) as drive_politeness_star, IFNULL(mr.fare_star, 0) as fare_star, IFNULL(mr.easy_of_payment_star, 0) as easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, d.first_name as dfname, d.last_name as dlname, d.country_code as dccode, d.mobile as dmobile, IFNULL(v.mobile, 0) as vmobile, IFNULL(v.country_code, 0) as vccode, IFNULL(vp.first_name, 0) as vfname, IFNULL(vp.last_name, 0) as vlname, dcs.current_latitude as driver_latitude, dcs.current_longitude as  driver_longitude');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id', 'left');
		
		$this->db->join('users v', 'v.id = r.vendor_id', 'left');
		$this->db->join('user_profile vp', 'vp.user_id = r.vendor_id', 'left');
		
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id', 'left');
		
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id AND dcs.allocated_status = 1', 'left');
		
		$this->db->where(array('r.id'=>$ride_id, 'r.customer_id' => $customer_id))->where('r.is_country', $countryCode);
		
		$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function  mypastrides($customer_id, $countryCode,  $sdate, $edate){
		
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id,  r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, r.start_lat, r.start_lng, r.end_lat, r.end_lng,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  dp.first_name driver_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
		$this->db->join('users c', 'c.id = '.$customer_id.'', 'left');
		$this->db->join('user_profile cp', 'cp.id = '.$customer_id.'', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		if(!empty($sdate) && !empty($edate)){
			$this->db->where('DATE(r.booked_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
			$this->db->where('DATE(r.booked_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
		}else{
			$this->db->where('DATE(r.booked_on) <=', $current_date);	
		}
		$this->db->where_in('r.status', array('5', '6', '8'))->where('r.is_country', $countryCode);
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng, $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->driver_name ==''){
					$row->driver_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				
				$row->sos = "http://13.233.9.134/sos?id=".$row->ride_id;
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';		
				
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	
	function  myupcomingrides($customer_id, $countryCode,  $sdate, $edate){
		
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id, r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, r.start_lat, r.start_lng, r.end_lat, r.end_lng,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  dp.first_name driver_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
		$this->db->join('users c', 'c.id = '.$customer_id.'', 'left');
		$this->db->join('user_profile cp', 'cp.id = '.$customer_id.'', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		
		if(!empty($sdate) && !empty($edate)){
			$this->db->where('DATE(r.booked_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
			$this->db->where('DATE(r.booked_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
		}else{
			$this->db->where('DATE(r.booked_on) <=', $current_date);	
		}
		$this->db->where_in('r.status', array('7'))->where('r.is_country', $countryCode);
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng, $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->driver_name ==''){
					$row->driver_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				
				$row->sos = "http://13.233.9.134/sos?id=".$row->ride_id;
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';	
				
				
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	
	function  mycurrentrides($customer_id, $countryCode){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id,  r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, t.name taxi_name, t.number, r.start_lat, r.start_lng, r.end_lat, r.end_lng,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms, tt.name types,  dp.first_name driver_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
		$this->db->join('users c', 'c.id = '.$customer_id.'', 'left');
		$this->db->join('user_profile cp', 'cp.id = '.$customer_id.'', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		$this->db->where('DATE(r.booked_on) <=', $current_date);
		$this->db->where_in('r.status', array('2', '3', '4'))->where('r.is_country', $countryCode);
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$this->db->limit(1);
		$q = $this->db->get();
		
		
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng, $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->driver_name ==''){
					$row->driver_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				
				$row->sos = "http://13.233.9.134/sos?id=".$row->ride_id;
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';	
				
                $data[] = $row;
            }
			
            return $data;
			
		}
		return false;	
	}
	
	function  myonrides($customer_id, $countryCode){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.ride_otp, d.mobile as driver_mobile, d.country_code as driver_country_code, r.ride_timing as ride_start_time, r.status,  r.start_lat, r.start_lng, r.start as pick_up, r.end as drop_off, r.end_lat, r.end_lng, r.driver_id, r.customer_id, r.taxi_id,   IFNULL(dcs.current_latitude, 0) current_latitude, IFNULL(dcs.current_longitude, 0) current_longitude, IFNULL(d.mobile, 0) as driver_mobile, IFNULL(dp.first_name, 0) as driver_name, dp.photo as driver_photo, cp.photo as customer_image, dp.first_name as customer_name, c.mobile as customer_mobile,  IFNULL(t.name, 0) taxi_name, IFNULL(t.type, 0) type, t.photo as taxi_photo, IFNULL(t.number, 0) number, IFNULL(tt.name, 0) types,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.user_id = r.driver_id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id AND dcs.taxi_id = r.taxi_id AND allocated_status = 1 AND dcs.is_connected = 1', 'left');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.customer_id', $customer_id);
		$this->db->where('DATE(r.ride_timing)', $current_date);
		//$this->db->where('r.status', 'onride');
		//$this->db->or_where('r.status', 'booked');
		$this->db->order_by('r.id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		//print_r($this->db->last_query());exit;
		
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				
				
				if($row->taxi_photo !=''){
					$row->taxi_photo = $image_path.$row->taxi_photo;
				}else{
					$row->taxi_photo = $image_path.'no_image.png';
				}
				
				
				if($row->driver_photo !=''){
					$row->driver_photo = $image_path.$row->driver_photo;
				}else{
					$row->driver_photo = $image_path.'no_image.png';
				}
				
				
				if($row->customer_image !=''){
					$row->customer_image = $image_path.$row->customer_image;
				}else{
					$row->customer_image = $image_path.'no_image.png';
				}
				
				
				
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	
	
	
	function add_customer($customer, $countryCode){
		$customer['is_edit'] = 1;
		$customer['is_country'] = $countryCode;
		$this->db->insert('users', $customer);//print_R($this->db->error());exit;
		$customer_id = $this->db->insert_id();	
		if($customer_id){
			$username = sprintf("%03d", $customer['country_code']).'1'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $customer_id, 'is_country' => $countryCode));
			$this->db->insert('user_bank', array('user_id' => $customer_id, 'is_edit' => 1, 'is_country' => $countryCode));
			$this->db->insert('user_profile', array('user_id' => $customer_id, 'first_name' => $customer['first_name'], 'last_name' => $customer['last_name'], 'is_edit' => 1, 'is_country' => $countryCode));
			
			$query = "select id, oauth_token from {$this->db->dbprefix('users')} where id='".$customer_id."' ";
			$q = $this->db->query($query);
			if($q->num_rows()>0){
				$data[] = $q->row();
				return $data;
			}
		}
		return false;
		
	}
	
	public function deactive_customer($user_id, $countryCode){
		$q = $this->db->update('users', array('active' => 0), array('id' => $user_id, 'is_country' => $countryCode));
		
		//print_r($this->db->last_query());die;
		
		if($q){
			return true;	
		}
		return false;	
	}
	
	function checkotp($data, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile  from {$this->db->dbprefix('users')} as u 		
		where   u.id='".$data['customer_id']."' AND  u.mobile_otp='".$data['otp']."'  ";
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'active' => 1), array('id' => $data['customer_id'], 'is_country' => $countryCode));
			
			$row = $q->row();
			if($row->photo !=''){
				$row->customer_photo = $image_path.$row->photo;
			}else{
				$row->customer_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;
		
	}
	
	
	function registerresendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."' AND country_code='".$data['country_code']."' AND group_id = 5 AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function resendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $data['customer_id']);
			//$this->db->update('users', array('mobile_otp' =>  $data['mobile_otp']));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgototp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."' AND group_id = 5 AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->where('is_country', $countryCode);
			$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgotcheckotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  forgot_otp='".$data['forgot_otp']."' AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			return true;
		}
		return false;
	}
	
	function forgotresendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND group_id = 5 AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $q->row('id'));
			//$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	
	function updatepassword($data, $countryCode){
		
		$this->db->where('id', $data['customer_id']);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('users', array('password' => $data['password'], 'text_password' => $data['text_password'], 'forgot_otp_verify' => 1));
		if($q){
			return true;	
		}
		return false;
	}
	
	function insertemergency($data, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->delete('emergency', array('user_id' => $data['user_id']));
		$q = $this->db->insert('emergency', $data);
		if($q){
			return true;
		}
		return false;
	}
	
	
	function getEmergencycontact($data, $countryCode){
		
		$res = array();
		$q = $this->db->select('*')->where('is_country', $countryCode)->where('user_id', $data['user_id'])->get('emergency');
		
		if($q->num_rows()>0){
			if(!empty($q->row('phone1'))){
				$res[] = array('0' => $q->row('phone1'), '1' => $q->row('country_code1') );
			}
			if(!empty($q->row('phone2'))){
				$res[] = array('0' => $q->row('phone2'), '1' => $q->row('country_code2') );
			}
			if(!empty($q->row('phone3'))){
				$res[] = array('0' => $q->row('phone3'), '1' => $q->row('country_code3') );
			}
			if(!empty($q->row('phone4'))){
				$res[] = array('0' => $q->row('phone4'), '1' => $q->row('country_code4') );
			}
			if(!empty($q->row('phone5'))){
				$res[] = array('0' => $q->row('phone5'), '1' => $q->row('country_code5') );
			}
			//print_r($res);die;
			return $res;
			
		}
		return false;
	}
	
	function getEmergencydata($id, $countryCode){
		$q = $this->db->select('*')->where('is_country', $countryCode)->where('user_id', $id)->get('emergency');
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;	
	}
	function currentRideSOS($data, $countryCode){
		$this->db->select('r.id as booking, r.booking_no, r.driver_id, r.taxi_id, r.customer_id, r.booked_type, r.status, r.booked_on, r.ride_timing, r.ride_type, r.start_lat, r.start_lng, r.end_lat, r.end_lng, dcs.current_latitude, dcs.current_longitude, c.first_name as customer_name, d.first_name as driver_name, t.name as taxi_name, t.number as taxi_number');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.id = r.driver_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->where('r.id', $data['booking_id']);
		$this->db->where('r.customer_id', $data['user_id'])->where('r.is_country', $countryCode);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;
	}
	
	function devicescheckotp($data, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->where('is_country', $countryCode)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		
		$query = "select u.id, u.oauth_token, u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 		
		where   u.id='".$data['customer_id']."' AND  u.mobile_otp='".$data['otp']."' AND u.active = 1 AND u.is_country = '".$countryCode."'  ";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;

		if($q->num_rows()>0){
			$row = $q->row();
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['customer_id'], 'is_country' => $countryCode));
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['customer_id'], 'user_type' => 1,  'device_token' => $row->oauth_token, 'is_country' => $countryCode));
			
			if($row->photo !=''){
				$row->customer_photo = $image_path.$row->photo;
			}else{
				$row->customer_photo = $image_path.'default.png';
			}	
			$s = $this->db->select('*')->get('settings');
			$row->camera_enable = $s->row('camera_enable');		
			$row->unicode_symbol = $unicode_symbol;
					
			$data =  $row;
			$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $row->mobile, 'time' => time()));
			return $data;
		}
		return false;
	}
	
	function social_check_login($login, $countryCode){
		$data = array();
		$setting = $this->getSettings($countryCode);
		$image_path = base_url('assets/uploads/');
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->where('is_country', $countryCode)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		$query = "select u.id, u.oauth_token, u.first_name, u.last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile, u.mobile_otp, u.active from {$this->db->dbprefix('users')} as u 		
		where u.login_key='".$login['login_key']."' AND u.group_id = 5 AND u.is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->active == 1){
				if($row->photo !=''){
					$row->customer_photo = $image_path.$row->photo;
				}else{
					$row->customer_photo = $image_path.'default.png';
				}
				if($setting->login_otp_enable == 1){
					if($q->row('devices_imei') != $login['devices_imei']){
						$row->check_status = 3;
						
						$this->db->update('users', array('mobile_otp' => $login['otp']), array('id' => $row->id, 'is_country' => $countryCode));
					}else{
						$row->check_status = 1;
					}
				}else{
					$row->check_status = 4;
				}
				
				$s = $this->db->select('*')->get('settings');
				$row->camera_enable = $s->row('camera_enable');
				$row->unicode_symbol = $unicode_symbol;
				$data = $row;
				return $data;
			}else{
				return 	$data->check_status = 2;
			}
		}		
		return 	$data->check_status = 0;
	}
	
	function check_login($login, $countryCode){
		$data = array();
		$setting = $this->getSettings($countryCode);
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->where('is_country', $countryCode)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token, u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile, u.mobile_otp, u.active from {$this->db->dbprefix('users')} as u 		
		where u.password='".$login['password']."' AND  u.mobile='".$login['mobile']."' AND  u.country_code='".$login['country_code']."'  AND u.group_id = 5 AND u.is_country ='".$countryCode."' ";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->active == 1){
				if($row->photo !=''){
					$row->customer_photo = $image_path.$row->photo;
				}else{
					$row->customer_photo = $image_path.'default.png';
				}
				if($setting->login_otp_enable == 1){
					if($q->row('devices_imei') != $login['devices_imei']){
						$row->check_status = 3;
						
						$this->db->update('users', array('mobile_otp' => $login['otp']), array('id' => $row->id, 'is_country' => $countryCode));
						
					}else{
						$row->check_status = 1;
						$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
					}
				}else{
					$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $login['devices_imei']), array('id' => $row->id));
					$this->db->update('user_socket', array('device_imei' => $login['devices_imei']), array('user_id' => $row->id, 'user_type' => 1,  'device_token' => $row->oauth_token));
					$row->check_status = 4;
					
					//$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['customer_id'], 'is_country' => $countryCode));
					//$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
						
					
					
					//$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['customer_id'], 'is_country' => $countryCode));
					
			//$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['customer_id'], 'user_type' => 1,  'device_token' => $row->oauth_token, 'is_country' => $countryCode));
			
					$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
				}
				$s = $this->db->select('*')->get('settings');
				$row->camera_enable = $s->row('camera_enable');
				
				$row->unicode_symbol = $unicode_symbol;
				$data = $row;
				return $data;
			}else{
				$row->unicode_symbol = $unicode_symbol;
				$row->check_status = 2;
				$data = $row;
				return 	$data;
			}
		}	
		$row = new ArrayObject();
		$row->check_status = 0;
		$data = $row;	
		return 	$data;
	}
	
	
	function getCustomer($oauth_token, $countryCode){
		$this->db->select('*');
		$this->db->where('oauth_token', $oauth_token)->where('is_country', $countryCode);
		$q = $this->db->get('users');
		
		if($q->num_rows()>0){
		    $row =  $q->row();
			if($row->dob == '0000-00-00' || $row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = date("d/m/Y", strtotime($row->dob));
			}
			$data = $row;
			return $data;
		}
		return false;	
	}
	
	
	function getDriverID($id, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.mobile, u.email, u.dob, u.devices_imei, u.group_id, u.first_name, up.last_name, up.gender');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->where('u.id', $id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
		    $row =  $q->row();
			
			$data = $row;
			return $data;
		}
		return false;	
	}
	
	function getTaxiID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id)->where('is_country', $countryCode);
		$q = $this->db->get('taxi');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getCustomerID($id, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.dob, u.mobile, u.email, u.devices_imei, u.group_id, u.first_name, up.last_name, up.gender, up.dob, up.photo');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->where('u.id', $id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
		    $row =  $q->row();
			if($row->dob == '0000-00-00' || $row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = date("d/m/Y", strtotime($row->dob));
			}
			$data = $row;
			return $data;
		}
		return false;	
	}
	
	function getDriversnew_radius($data, $countryCode){
	$image_path = base_url('assets/uploads/');
	
	$longitude = $data['longitude'];
	$latitude = $data['latitude'];
	
	
	$search = $this->db->insert('search_location', array('latitude' => $latitude, 'longitude' => $longitude, 'is_country' => $countryCode));
		
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
	}else{
		$where = "  ";
	}
	
	$query = "SELECT  d.id, d.first_name, d.mobile, d.country_code, d.oauth_token, d.is_daily, d.is_rental, d.is_outstation, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, tt.image, tt.image_hover, tt.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id 
	 
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  AND us.ride_stop = 0
	
	WHERE d.is_country = '".$countryCode."' AND  dcs.mode = 1 AND dcs.is_connected = 1 AND dcs.allocated_status = 1  GROUP BY d.id  HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

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
	
	function getDrivers_radius($data, $countryCode){
		
		
		
	$image_path = base_url('assets/uploads/');
	
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
	}else{
		$where = "  ";
	}
	
	
	$query = "SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.id as taxi_id, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, tt.image, tt.image_hover, tt.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id  
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  AND us.ride_stop = 0
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE d.is_country = '".$countryCode."' AND dcs.mode = 1   AND dcs.is_connected = 1  AND dcs.allocated_status = 1  ".$where." GROUP BY d.id HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";


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
		
	
	function getRideBYID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;	
	}
	function customerCancel($data, $countryCode){
		
		$this->db->where('customer_id', $data['customer_id']);
		$this->db->where('id', $data['booking_id']);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('rides', array('cancel_status' => 1, 'cancel_msg' => $data['cancel_msg'], 'cancelled_by' => $data['customer_id'], 'cancelled_type' => 2, 'status' => 6));
		if($q){
			$this->db->where('driver_id', $data['driver_id']);
			$this->db->where('allocated_status', 1);
			$this->db->where('is_country', $countryCode);
			$this->db->update('driver_current_status', array('mode' => 1, 'is_connected' => 1));
			return true;
		}
		return false;	
	}
	
	
	function customerRating($data, $countryCode){
		
		$query = "update {$this->db->dbprefix('rides')} set rating='".$data['rating']."', feedback = '".$data['feedback']."' where  customer_id='".$data['customer_id']."' AND  id='".$data['booking_id']."' ";

		$q = $this->db->query($query);
		

		if($q){
			return true;
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
	
	function drivertraking($data, $countryCode){
		$this->db->select('rides.*, drivers.id as driver_id, driver_current_status.mode as driver_cmode, driver_current_status.current_latitude as driver_clatitude, driver_current_status.current_longitude as driver_clongitude');
		$this->db->join('drivers', 'drivers.id = rides.driver_id', 'left');
		$this->db->join('driver_current_status', 'driver_current_status.driver_id = rides.driver_id AND driver_current_status.taxi_id = rides.taxi_id');
		$this->db->where('rides.id', $data['booking_id']);
		$this->db->where('rides.customer_id', $data['customer_id'])->where('rides.is_country', $countryCode);
		$this->db->order_by('driver_current_status.id', 'DESC');
		$q = $this->db->get('rides');
		
		if($q->num_rows() > 0){
			return $q->row();
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
	
	function add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode){
		
		$image_path = base_url('assets/uploads/');
		$insert['is_country'] = $countryCode;
		$this->db->insert('rides', $insert); //print_r($this->db->last_query());exit;
		if($ride_id = $this->db->insert_id()){
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
}
