<?php defined('BASEPATH') OR exit('No direct script access allowed');

class People_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	function getALLTaxi_make($countryCode){
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('taxi_make');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getALLTaxi_type($countryCode){
			
		/*if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}*/
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getTaxinameBYID($id, $countryCode){
		$this->db->select('name');
		
		$q = $this->db->where('id', $id)->get('taxi_make');
		
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaximodelBYID($id){
		 $this->db->select('name');
		
		$q =$this->db->where('id', $id)->get('taxi_model');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaxitypeBYID($id, $countryCode){
		$this->db->select('name');
		
		$q = $this->db->where('id', $id)->get('taxi_type');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	
	function getModelbymake_type($make_id, $type_id, $countryCode){
		$q = $this->db->select('id, name');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$this->db->where('type_id', $type_id)->where('make_id', $make_id)->get('taxi_model');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxi($countryCode){
		$q = $this->db->select('id, name');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$this->db->get('taxi_make');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxitype($countryCode){
		$q = $this->db->select('id, name');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$this->db->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function add_reason($data, $countryCode){
		$data['is_country'] = $countryCode;
		$q = $this->db->insert('user_verification_reason', $data);
		if($q){
			return true;
		}
		return false;	
	}
	
	function checkReason($user_id){
		$this->db->select('*');
		$this->db->from('user_verification_reason');
		$this->db->where('customer_id', $user_id);
		$this->db->where('support_status !=', 2);
		
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;	
		}
		return false;	
	}
	
	function getALLUser_designation(){
		$q = $this->db->get('user_roles');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getUserSettings($user_id, $countryCode){
		$this->db->select('*')->where('user_id', $user_id);
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('user_setting');
		if($q->num_rows()>0){
			return $q->row();	
		}
		return false;	
	}
	
	function employee_role_setting($data, $id, $countryCode){	
		$this->db->select('*')->where('user_id', $id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$check = $this->db->get('user_setting');
		if($check->num_rows()>0){
			$this->db->where('user_id', $id);
		
			$q = $this->db->update('user_setting', $data);
			return true;
			
		}else{
			$data['user_id'] = $id;
			
			$this->db->insert('user_setting', $data);
			return true;
		}
			
		/*$this->db->where('user_id', $id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->update('user_setting', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}*/
		return false;
	}
	
	function driver_role_setting($data, $id, $countryCode){	
		$this->db->select('*')->where('user_id', $id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$check = $this->db->get('user_setting');
		if($check->num_rows()>0){
			$this->db->where('user_id', $id);
		
			$q = $this->db->update('user_setting', $data);
			return true;
			
		}else{
			$data['user_id'] = $id;
			
			$this->db->insert('user_setting', $data);
			return true;
		}
		return false;
	}
	
	
	function update_status($data, $id){
		$this->db->where('id', $id);
		
		$q = $this->db->update('users', array('active' => $data['status']));
		
		if($q){
			return true;
		}
		return false;
	}
	
	
	/*### Employee*/
	function add_employee($user, $user_profile, $user_address, $user_bank, $user_permission, $user_document, $group_id, $countryCode){
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			//$username = 'EMP'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$username = sprintf("%03d", $customer['country_code']).'4'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_permission['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_permission['group_id'] = $group_id;
			
			$user_profile['is_country'] = $countryCode;
			$user_address['is_country'] = $countryCode;
			$user_bank['is_country'] = $countryCode;
			$user_permission['is_country'] = $countryCode;
			$user_document['is_country'] = $countryCode;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_permission', $user_permission);
			$this->db->insert('user_document', $user_document);
	    	return true;
		}
		return false;
    }
	
	/*### Vendor*/
	function checkMobilevendor($countryCode){
		$v = $this->db->select('*');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$this->db->where('mobile', $mobile);
		
		$this->db->where('group_id', 3)->get('users');
		if($v->num_rows()>0){
			$vendor = 1;
		}else{
			$vendor = 0;	
		}
		$v = $this->db->select('*');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$this->db->where('mobile', $mobile)->where('group_id', 4)->get('users');
		if($v->num_rows()>0){
			$driver = 1;
		}else{
			$driver = 0;	
		}
		if($vendor == 1 && $driver == 1){
			return 1;
		}elseif($vendor == 0 && $driver == 1){
			return 2;
		}elseif($vendor == 1 && $driver == 0){
			return 3;
		}else{
			return 0;
		}
		
	}
	function add_vendor($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document, $vendor_group_id, $driver_group_id, $operator, $taxi, $taxi_document, $countryCode){
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			//$username = 'VEN'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$username = sprintf("%03d", $customer['country_code']).'3'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_vendor['user_id'] = $user_id;
			
			$user_profile['is_country'] = $countryCode;
			$user_address['is_country'] = $countryCode;
			$user_bank['is_country'] = $countryCode;
			$user_document['is_country'] = $countryCode;
			$user_vendor['is_country'] = $countryCode;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			$this->db->insert('user_vendor', $user_vendor);
			
			if($operator == 'vendor_and_driver'){
				$driver_user['parent_id'] = $user_id;
				$driver_user['is_country'] = $countryCode;
				$this->db->insert('users', $driver_user);
				
				if($driver_user_id = $this->db->insert_id()){
					//$username = 'DRI'.str_pad($driver_user_id, 5, 0, STR_PAD_LEFT);
					$username = sprintf("%03d", $customer['country_code']).'2'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
					$this->db->update('users', array('username' => $username), array('id' => $driver_user_id, 'is_country' => $countryCode));
			
					$user_profile['user_id'] = $driver_user_id;
					$user_address['user_id'] = $driver_user_id;
					$user_bank['user_id'] = $driver_user_id;
					$driver_user_document['user_id'] = $driver_user_id;
					$user_profile['is_country'] = $countryCode;
					$user_address['is_country'] = $countryCode;
					$user_bank['is_country'] = $countryCode;
					$driver_user_document['is_country'] = $countryCode;
					
					$this->db->insert('user_profile', $user_profile);
					$this->db->insert('user_address', $user_address);
					$this->db->insert('user_bank', $user_bank);
					$this->db->insert('user_document', $driver_user_document);
				}
			}
			
			if(!empty($taxi)){
				$taxi['vendor_id'] = $user_id;
				
				$taxi['is_country'] = $countryCode;
				$this->db->insert('taxi', $taxi);
				if($taxi_id = $this->db->insert_id()){
					$taxi_document['taxi_id'] = $taxi_id;
					$taxi_document['user_id'] = $user_id;
					$taxi_document['group_id'] = $vendor_group_id;
					$taxi_document['is_country'] = $countryCode;
					$this->db->insert('taxi_document', $taxi_document);
				}
			}	
			
			if(!empty($taxi_id) && !empty($driver_user_id)){
				
				$this->db->insert('driver_current_status', array('driver_id' => $driver_user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $user_id, 'is_allocated' => 1, 'allocated_start_date' => date('Y-m-d H:is'), 'allocated_status' => 1, 'is_country' => $countryCode));
				
			}
			
	    	return true;
		}
		return false;
    }
	
	function getVendorDetails($id){
		$this->db->select('id as vendor_id, gst, telephone_number, legal_entity');
		$this->db->where('user_id', $id);
		
		$q = $this->db->get('user_vendor');
		if($q->num_rows()>0){
			return $q->row();	
		}
		return false;
	}
	
	function getZoneuser($zone_id, $employee_group, $countryCode){
		$this->db->select('up.group_id, up.user_id, up.department_id, up.designation_id, u.first_name, u.mobile, udep.name as department_name, urole.position');
		$this->db->from('user_permission up');
		$this->db->join('user_profile pro', 'pro.user_id = up.user_id', 'left');
		$this->db->join('users u', 'u.id = up.user_id ', 'left');
		$this->db->join('user_roles urole', 'urole.id = up.designation_id', 'left');
		$this->db->join('user_department udep', 'udep.id = up.department_id', 'left');
		$this->db->where('up.zone_id', $zone_id);
		$this->db->where('up.state_id', 0);
		$this->db->where('up.city_id', 0);
		$this->db->where('up.area_id', 0);
		$this->db->where('up.group_id', $employee_group);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;	
	}
	
	function getZonalUser($associated_id, $countryCode){
		$this->db->where('user_id', $associated_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->get('user_permission');
		if($q->num_rows()>0){
			return $q->row();	
		}
		return false;	
	}
	
	function zonal_allocated($vendor, $id){
		$this->db->where('user_id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('user_vendor', $vendor);
		if($q){
			return true;	
		}
		return false;	
	}
	/*### Driver*/
	function checkMobiledriver($mobile, $country_code, $countryCode){
		
		 $this->db->select('*')->where('mobile', $mobile);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$d =$this->db->where('group_id', 4)->get('users');
		if($d->num_rows()>0){
			return 1;
		}else{
			return 0;	
		}
		
		
	}
	
	function checkCode($code, $type){
		$check = substr($code, -4, 1);
		
		if($check == $type){
			$this->db->select('u.refer_code, urf.code_end ')->from('users u')->join('user_refercode urf', 'urf.user_id = u.id', 'left')->where('refer_code', $code);
			$d =$this->db->get();
			
			if($d->num_rows()>0){
				return $d->row();
			}else{
				return 0;	
			}
		}else{
			return 0;
		}
		
	}
	
	function checkMobilecustomer($mobile, $country_code, $countryCode){
		
		 $this->db->select('*')->where('mobile', $mobile);
		/*if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}*/
		$d =$this->db->where('group_id', 5)->get('users');
		if($d->num_rows()>0){
			return 1;
		}else{
			return 0;	
		}
		
		
	}
	
	function checkMobileemployee($mobile, $country_code, $countryCode){
		
		 $this->db->select('*')->where('mobile', $mobile);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$d =$this->db->where('group_id', 6)->get('users');
		if($d->num_rows()>0){
			return 1;
		}else{
			return 0;	
		}
		
		
	}
	
	function add_driver($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $driver_group_id, $parent_id, $countryCode, $refer_code, $reference_no){
		
		$setting = $this->site->RegsiterSettings($countryCode);
		
		$user = array_map(function($v){return (is_null($v)) ? "" : $v;},$user);
		$user_profile = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_profile);
		$user_bank = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_bank);
		$user_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_document);
		$taxi = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi);
		$taxi_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi_document);
	
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			//$username = 'DRI'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$username = sprintf("%03d", $customer['country_code']).'2'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_profile['is_country'] = $countryCode;
			$user_address['is_country'] = $countryCode;
			$user_bank['is_country'] = $countryCode;
			$user_document['is_country'] = $countryCode;
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			
			if(!empty($refer_code)){
				$refer_array = array(
					'code' => $refer_code,
					'amount' => $setting->driver_amount,
					'register_enable' => $setting->driver_user_reg,
					'ride_enable' => $setting->driver_rides,
					'number_of_rides' => $setting->driver_rides_no,
					'code_start' => date('Y-m-d'),
					'code_end' => date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$setting->driver_validation.' days')),
					'using_type' => $setting->driver_using_type,
					'using_menbers' => $setting->driver_using_members,
					'user_id' => $user_id,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $countryCode,
					'user_type' => 1
 				);
				$this->db->insert('user_refercode', $refer_array);
				if(!empty($reference_no)){
					$this->site->implementCode($reference_no, $user_id, 4, $countryCode);
				}
			
			}
			
			if(!empty($taxi)){
				$taxi['driver_id'] = $user_id;
				$taxi['is_country'] = $countryCode;
				$this->db->insert('taxi', $taxi);
				
				if($taxi_id = $this->db->insert_id()){
					$taxi_document['taxi_id'] = $taxi_id;
					$taxi_document['user_id'] = $user_id;
					$taxi_document['group_id'] = $driver_group_id;
					$taxi_document['is_country'] = $countryCode;
					$this->db->insert('taxi_document', $taxi_document);
					
					if(!empty($taxi_id) && !empty($user_id)){
						$this->db->insert('driver_current_status', array('driver_id' => $user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $parent_id, 'is_allocated' => 1, 'allocated_start_date' => date('Y-m-d H:is'), 'allocated_status' => 1, 'is_country' => $countryCode));
						
						//$this->db->insert('driver_current_status', array('driver_id' => $user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $parent_id));
					}
				}
				
				
			}
			
			
	    	return true;
		}
		return false;
    }
	
	/*### Customer*/
	function add_customer($user, $user_profile, $user_address, $countryCode, $refer_code, $reference_no){
		$setting = $this->site->RegsiterSettings($countryCode);
		
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;	
			$user_profile['is_country'] = $countryCode;		
			$user_address['is_country'] = $countryCode;
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			
			if(!empty($refer_code)){
				$refer_array = array(
					'code' => $refer_code,
					'amount' => $setting->customer_amount,
					'register_enable' => $setting->customer_user_reg,
					'ride_enable' => $setting->customer_rides,
					'number_of_rides' => $setting->customer_rides_no,
					'code_start' => date('Y-m-d'),
					'code_end' => date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$setting->customer_validation.' days')),
					'using_type' => $setting->customer_using_type,
					'using_menbers' => $setting->customer_using_members,
					'user_id' => $user_id,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $countryCode,
					'user_type' => 1
 				);
				$this->db->insert('user_refercode', $refer_array);
				if(!empty($reference_no)){
					$this->site->implementCode($reference_no, $user_id, 5, $countryCode);
				}
			}
			
	    	return true;
		}
		return false;
    }
	
	function getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode){
		
		$this->db->select('up.user_id, up.group_id, up.is_all, up.designation_id, up.reporter_id, u.email, u.mobile, u.active, u.first_name, u.last_name');		
		$this->db->from('user_permission up');
		$this->db->join('users u', 'u.id = up.user_id');
		$this->db->where('up.group_id', $group_id);
		if($group_id == 1 || $group_id == 2){
			$this->db->where('up.is_all', 1);
		}else{
			$this->db->where('up.is_all', 0);
			$this->db->where('up.department_id', $department_id);
			
			if($designation_id == 'continents'){
				$this->db->where('up.continent_id', 0);
				$this->db->where('up.country_id', 0);
				$this->db->where('up.zone_id', 0);
				$this->db->where('up.state_id', 0);
				$this->db->where('up.city_id', 0);
				$this->db->where('up.area_id', 0);
			}elseif($designation_id == 'countries'){
				$this->db->where('up.continent_id', $location_id);
				$this->db->where('up.country_id', 0);
				$this->db->where('up.zone_id', 0);
				$this->db->where('up.state_id', 0);
				$this->db->where('up.city_id', 0);
				$this->db->where('up.area_id', 0);
			}elseif($designation_id == 'zones'){
				$this->db->where('up.country_id', $location_id);
				$this->db->where('up.zone_id', 0);
				$this->db->where('up.state_id', 0);
				$this->db->where('up.city_id', 0);
				$this->db->where('up.area_id', 0);
			}elseif($designation_id == 'states'){
				$this->db->where('up.zone_id', $location_id);
				$this->db->where('up.state_id', 0);
				$this->db->where('up.city_id', 0);
				$this->db->where('up.area_id', 0);
			}elseif($designation_id == 'cities'){
				$this->db->where('up.state_id', $location_id);
				$this->db->where('up.city_id', 0);
				$this->db->where('up.area_id', 0);
			}elseif($designation_id == 'areas'){
				$this->db->where('up.city_id', $location_id);
				$this->db->where('up.area_id', 0);
			}
		}
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('up.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('up.is_country', $countryCode);
		}
		
		 $q = $this->db->get();
		
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				
				$data[] = $row;
				
            }
			return $data;
		}
		return FALSE;	
	}
	
	function getALLEmployee($group_id, $countryCode){
		$this->db
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, ud.name as department, ur.position as designation ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", "left")
			->join("user_permission per", 'per.user_id = users.id AND per.is_edit = 1', "left")
			->join("user_department ud", 'ud.id = per.department_id', "left")
			->join("user_roles ur", 'ur.id = per.designation_id', "left");
			$this->db->where("users.group_id", $group_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('users.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('users.is_country', $countryCode);
		}
		
			$q = $this->db->get();
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;
	}
	
	function getALLVendor($group_id, $countryCode){
		$this->db
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", "left");
			$this->db->where("users.group_id", $group_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('users.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('users.is_country', $countryCode);
		}

			$q = $this->db->get();
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;
	}
	
	function getALLDriver($group_id, $countryCode){
		$this->db
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", "left");
			$this->db->where("users.group_id", $group_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('users.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('users.is_country', $countryCode);
		}
		
			$q = $this->db->get();
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;
	}
	
	function getALLCustomer($group_id, $countryCode){
		$this->db
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", "left");
			$this->db->where("users.group_id", $group_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('users.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('users.is_country', $countryCode);
		}
		
			$q = $this->db->get();
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;
	}
	
	
	
    
}
