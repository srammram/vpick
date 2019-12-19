<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_api extends CI_Model
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
	
	function modify_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type){
		
        if(!empty($user_id) && !empty($customer_type)){
			
			if($customer_type == 1){
				$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_vendor', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $this->countryCode));
				
				$user_profile['user_id'] = $user_id;
				$user_address['user_id'] = $user_id;
				$user_vendor['user_id'] = $user_id;
				$user_bank['user_id'] = $user_id;
				$user_document['user_id'] = $user_id;
				$user_profile['is_country'] = $this->countryCode;
				$user_address['is_country'] = $this->countryCode;
				$user_vendor['is_country'] = $this->countryCode;
				$user_bank['is_country'] = $this->countryCode;
				$user_document['is_country'] = $this->countryCode;
				$this->db->insert('user_profile', $user_profile);
				$this->db->insert('user_address', $user_address);
				$this->db->insert('user_vendor', $user_vendor);
				$this->db->insert('user_bank', $user_bank);
				$this->db->insert('user_document', $user_document);
			}
			
	    	return true;
		}
		return false;
    }
	
	function edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type){
		
        if(!empty($user_id) && !empty($customer_type)){
			
			if($customer_type == 1){
				$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_vendor', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $this->countryCode));
				$user_profile['user_id'] = $user_id;
				$user_address['user_id'] = $user_id;
				$user_vendor['user_id'] = $user_id;
				$user_profile['is_country'] = $this->countryCode;
				$user_address['is_country'] = $this->countryCode;
				$user_vendor['is_country'] = $this->countryCode;
				$this->db->insert('user_profile', $user_profile);
				$this->db->insert('user_address', $user_address);
				$this->db->insert('user_vendor', $user_vendor);
				
			}elseif($customer_type == 2){
				$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$user_bank['user_id'] = $user_id;
				$user_bank['is_country'] = $this->countryCode;
				$this->db->insert('user_bank', $user_bank);
			}elseif($customer_type == 3){
				$user_document['user_id'] = $user_id;
				$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$user_document['is_country'] = $this->countryCode;
				$this->db->insert('user_document', $user_document);
			}
			
	    	return true;
		}
		return false;
    }
	
	
	function registerresendotp($data){
		$query = "select * from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."' AND country_code='".$data['country_code']."' AND group_id = 3 ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	public function updateDriver($insert, $driver_id){
		$this->db->where('id', $driver_id);
		$this->db->where('is_country', $this->countryCode);
		$q = $this->db->update('users', $insert);
		if($q){
			return true;	
		}
		return false;
	}
	
	function getDriversettingView($driver_id){
		$this->db->select('u.id, u.oauth_token, u.group_id, u.is_daily, u.is_rental, u.is_outstation, u.is_hiring, u.is_corporate, u.base_location, IFNULL(c.name, 0) as base_location_name');
		$this->db->from('users u');
		$this->db->join('cities c', 'c.id = u.base_location', 'left');
		$this->db->where('u.id', $driver_id);
		$q = $this->db->get();
				
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	public function Getallvehicles($vendor_id){
		$query = "SELECT T.id, T.name as taxi_name, T.number as taxi_number, TT.name as taxi_type, COUNT(R.id) as ride_count, IFNULL(SUM(P.overall), 0) as avg FROM {$this->db->dbprefix('taxi')}  AS T LEFT JOIN {$this->db->dbprefix('rides')}  AS R ON R.taxi_id = T.id  LEFT JOIN {$this->db->dbprefix('multiple_rating')}  AS P ON P.booking_id = R.id LEFT JOIN {$this->db->dbprefix('taxi_type')} AS TT ON TT.id = T.type WHERE T.vendor_id = ".$vendor_id." GROUP BY T.id ";
		$q = $this->db->query($query);
		
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$total_star = $row->ride_count * 5;
				$row->avg = (string)round((($row->avg / $total_star) * 5), 1);
				$data[] = $row;
				
			}
			return $data;	
		}
		return false;
	}
	
	public function Getallaccount($vendor_id){
		$query = "SELECT T.id, T.name as taxi_name, T.number as taxi_number, COUNT(R.id) as ride_count, 
		
		SUM(CASE WHEN (RP.payment_type = 1)  THEN RP.total_fare ELSE 0 END) AS cash,  SUM(CASE WHEN (RP.payment_type = 2)  THEN RP.total_fare ELSE 0 END) AS mobile_wallet, SUM(CASE WHEN (RP.payment_type = 3)  THEN RP.total_fare ELSE 0 END) AS credit_card, SUM(CASE WHEN (RP.payment_type = 4)  THEN RP.total_fare ELSE 0 END) AS debit_card, SUM(RP.total_fare) AS total
		
		 FROM {$this->db->dbprefix('taxi')}  AS T LEFT JOIN {$this->db->dbprefix('rides')}  AS R ON R.taxi_id = T.id LEFT JOIN {$this->db->dbprefix('ride_payment')} AS RP ON RP.ride_id = R.id   WHERE T.driver_id = ".$vendor_id." GROUP BY T.id ";
		$q = $this->db->query($query);
		
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				
				$data[] = $row;
				
			}
			return $data;	
		}
		return false;
	}
	
	public function Getalldrivers($vendor_id){
		$query = "SELECT U.id, U.first_name as driver_name, COUNT(R.id) as ride_count, IFNULL(SUM(P.overall), 0) as avg FROM {$this->db->dbprefix('users')}  AS U LEFT JOIN {$this->db->dbprefix('rides')}  AS R ON R.driver_id = U.id  LEFT JOIN {$this->db->dbprefix('multiple_rating')}  AS P ON P.booking_id = R.id  WHERE U.parent_id = ".$vendor_id." GROUP BY U.id ";
		$q = $this->db->query($query);
		
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$total_star = $row->ride_count * 5;
				$row->avg = (string)round((($row->avg / $total_star) * 5), 1);
				$data[] = $row;
				
			}
			return $data;	
		}
		return false;
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
	
	function getSettings(){
		$q = $this->db->select('*')->where('setting_id', 1)->get('settings');
		if($q->num_rows() > 0){
			return $q->row();	
		}
		return false;
	}
	
	function check_mobile($mobile, $country_code){
		$q = $this->db->select('*')	->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', 3)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return 0;
	}
		
	function insertNotification($data){
		
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $this->countryCode ));
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	
	function getContinents($country_id){
		$this->db->select('id, name');
		$q = $this->db->get('continents');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getCountries($continent_id){
		$this->db->select('id, name');
		$this->db->where('continent_id', $continent_id);
		$q = $this->db->get('countries');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getZones($country_id){
		$this->db->select('id, name');
		$this->db->where('country_id', $country_id);
		$q = $this->db->get('zones');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getState($zone_id){
		$this->db->select('id, name');
		$this->db->where('zone_id', $zone_id);
		$q = $this->db->get('states');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getCities($state_id){
		$this->db->select('id, name');
		$this->db->where('state_id', $state_id);
		$q = $this->db->get('cities');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getAreas($city_id){
		$this->db->select('id, name');
		$this->db->where('city_id', $city_id);
		$q = $this->db->get('areas');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	public function myprofile($user_id, $vendor_group, $vendor_type){
		
	
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, IFNULL(u.email, 0) as email, IFNULL(u.country_code, 0) as country_code, IFNULL(u.ref_mobile, 0) as ref_mobile, IFNULL(u.mobile, 0) as mobile, u.active, u.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, IFNULL(ud.local_image, 0) as local_image, IFNULL(ud.local_address, 0) as local_address, IFNULL(ud.local_pincode, 0) as local_pincode, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id,  ud.local_country_id,  ud.local_zone_id,  ud.local_state_id,  ud.local_city_id, ud.local_area_id,   ud.permanent_verify, ud.permanent_approved_by, ud.permanent_approved_on, IFNULL(ud.permanent_image, 0) as permanent_image, IFNULL(ud.permanent_address, 0) as permanent_address, ud.permanent_continent_id,  ud.permanent_country_id,  ud.permanent_zone_id, IFNULL(ud.permanent_pincode, 0) as permanent_pincode,  ud.permanent_state_id,  ud.permanent_city_id,  ud.permanent_area_id,  ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, IFNULL(ub.account_no, 0) account_no, ub.is_verify as account_verify, IFNULL(ub.account_holder_name, 0) as account_holder_name, IFNULL(ub.bank_name, 0) as bank_name, IFNULL(ub.branch_name, 0) as branch_name, IFNULL(ub.ifsc_code, 0) as ifsc_code, IFNULL(udoc.aadhaar_no, 0) as aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  IFNULL(udoc.aadhaar_image, 0) as aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  IFNULL(udoc.pancard_no, 0) as pancard_no, udoc.pancard_verify, IFNULL(udoc.pancard_image, 0) as pancard_image, IFNULL(udoc.license_image, 0) as license_image, udoc.license_approved_by, udoc.license_approved_on, IFNULL(udoc.license_no, 0) as license_no, udoc.license_verify, udoc.license_dob, IFNULL(udoc.license_ward_name, 0) as license_ward_name, IFNULL(lt.name, 0) as license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, IFNULL(udoc.police_image, 0) as police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, IFNULL(udoc.police_on, 0) as police_no, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, IFNULL(udoc.loan_information, 0) as loan_information, udoc.loan_verify, IFNULL(u.first_name, 0) as first_name, IFNULL(u.last_name, 0) as last_name, IFNULL(u.gender, 0) as gender, u.dob, IFNULL(u.photo, 0) as photo, IFNULL(ugroup.name, 0) as group_name, IFNULL(pgroup.name, 0)  as parent_group_name, userper.department_id, IFNULL(ur.position, 0) as position,  userper.designation_id, IFNULL(userdep.name, 0) as user_department, userper.continent_id, IFNULL(urc.name, 0) as continent_name, userper.country_id, IFNULL(urcc.name, 0) as country_name, userper.zone_id, IFNULL(urz.name, 0) as zone_name, userper.state_id, IFNULL(urs.name, 0) as state_name, userper.city_id, IFNULL(urcity.name, 0) as city_name, userper.area_id, IFNULL(ura.name, 0) as area_name, IFNULL(uv.gst, 0) as gst, IFNULL(uv.telephone_number, 0) as telephone_number, IFNULL(uv.legal_entity, 0) as legal_entity, uv.associated_id, IFNULL(assoc.first_name, 0) as associated_name');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id', 'left');
		$this->db->join('user_profile assoc', 'assoc.user_id = uv.associated_id AND assoc.is_edit = 1', 'left');
		$this->db->join('user_address ud', 'ud.is_edit = 1 AND ud.user_id = u.id', 'left');
		$this->db->join('user_bank ub', 'ub.is_edit = 1 AND ub.user_id = u.id', 'left');
		$this->db->join('user_document udoc', 'udoc.is_edit = 1 AND udoc.user_id = u.id', 'left');
		$this->db->join('user_profile up', 'up.is_edit = 1 AND up.user_id = u.id', 'left');
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		$this->db->join('license_type lt', 'lt.id = udoc.license_type', 'left');
		$this->db->where('u.id', $user_id);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			
			if($row->photo != ''){
				$row->photo = $image_path.$row->photo;
			}else{
				$row->photo = $image_path.'no_image.png';
			}
			
			if($row->local_image != ''){
				$row->local_image = $image_path.$row->local_image;
			}else{
				$row->local_image = $image_path.'no_image.png';
			}
			
			if($row->permanent_image != ''){
				$row->permanent_image = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image != ''){
				$row->aadhaar_image = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image = $image_path.'no_image.png';
			}
			
			if($row->pancard_image != ''){
				$row->pancard_image = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image = $image_path.'no_image.png';
			}
			
			if($row->license_image != ''){
				$row->license_image = $image_path.$row->license_image;
			}else{
				$row->license_image = $image_path.'no_image.png';
			}
			
			if($row->police_image !=''){
				$row->police_image = $image_path.$row->police_image;
			}else{
				$row->police_image = $image_path.'no_image.png';
			}
			
			if($row->loan_doc != ''){
				$row->loan_doc = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc = $image_path.'no_image.png';
			}
			
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			
			
			
			if($vendor_group == 3 && $vendor_type == 1){
				$vendor_data = array(
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
					'local_image' => $row->local_image,
					'local_address' => $row->local_address,
					'local_pincode' => $row->local_pincode,
					
					'local_verify' => $row->local_verify,
					'permanent_image' => $row->permanent_image,
					'permanent_address' => $row->permanent_address,
					'permanent_pincode' => $row->permanent_pincode,
					'permanent_verify' => $row->permanent_verify,
					'gst' => $row->gst,
					'telephone_number' => $row->telephone_number,
					'legal_entity' => $row->legal_entity
					
				);
			}elseif($vendor_group == 3 && $vendor_type == 2){
				$vendor_data = array(
					'user_id' => $row->id,
					'account_holder_name' => $row->account_holder_name,
					'account_no' => $row->account_no,
					'bank_name' => $row->bank_name,
					'branch_name' => $row->branch_name,
					'ifsc_code' => $row->ifsc_code,
					'account_verify' => $row->account_verify
				);
			}elseif($vendor_group == 3 && $vendor_type == 3){
				$vendor_data = array(
					'user_id' => $row->id,
					'aadhaar_no' => $row->aadhaar_no,
					'aadhaar_image' => $row->aadhaar_image,
					'aadhar_verify' => $row->aadhar_verify,
					'pancard_no' => $row->pancard_no,
					'pancard_image' => $row->pancard_image,
					'pancard_verify' => $row->pancard_verify,
					'loan_information' => $row->loan_information,
					'loan_doc' => $row->loan_doc,
					'loan_verify' => $row->loan_verify

				);
			}
            return $vendor_data;
        }
		return FALSE;
	}
	
	
	
	function vendorUpdateStatus($data){
		
		$this->db->where('id', $data['vendor_id']);
		$this->db->where('is_country', $this->countryCode);
		$q = $this->db->update('users', array('mode' => $data['mode'], 'updated_on' => date('Y-m-d H:i:s')));
		if($q){
			return true;
		}
		return false;	
	}
	
	function fcminsert($data){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->where('is_country', $this->countryCode);
			$this->db->update('devices', array('user_id' => $data['user_id'], 'group_id' => $data['group_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'updated_on' => date('Y-m-d H:i:s')));
			
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
			$this->db->where('is_country', $this->countryCode);
			$this->db->update('devices', array('user_id' => 0, 'user_type' => 0, 'devices_type' => 0, 'device_imei' => '', 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	/*### Vendor*/
	/*function add_vendor($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document, $vendor_group_id, $driver_group_id, $operator){
		
		$this->db->insert('users', $user);
		
        if($user_id = $this->db->insert_id()){
			$username = 'VEN'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id));
			
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_vendor['user_id'] = $user_id;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);			
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			$this->db->insert('user_vendor', $user_vendor);
			
			if($operator == '1'){
				$driver_user['parent_id'] = $user_id;
				$this->db->insert('users', $driver_user);
				
				if($driver_user_id = $this->db->insert_id()){
					$user_profile['user_id'] = $driver_user_id;
					$user_address['user_id'] = $driver_user_id;
					$user_bank['user_id'] = $driver_user_id;
					$driver_user_document['user_id'] = $driver_user_id;
					$this->db->insert('user_profile', $user_profile);
					$this->db->insert('user_address', $user_address);
					$this->db->insert('user_bank', $user_bank);
					$this->db->insert('user_document', $driver_user_document);
				}
			}
			
	    	return true;
		}
		return false;
    }*/
	
	/*function add_vendor($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $vendor_group_id){
		
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			$username = 'VEN'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_vendor['user_id'] = $user_id;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			$this->db->insert('user_vendor', $user_vendor);
			
			
			
			
	    	return true;
		}
		return false;
    }
	*/
	
	function add_vendor($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $vendor_group_id){
		$user['is_country'] = $this->countryCode;
		$this->db->insert('users', $user);
		$user_id = $this->db->insert_id();
        if(!empty($user_id)){
			$username = sprintf("%03d", $user['country_code']).'3'.str_pad($user_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $this->countryCode));
			
			if(!empty($user_profile)){
				$user_profile['user_id'] = $user_id;
				$user_profile['is_country'] = $this->countryCode;
				$this->db->insert('user_profile', $user_profile);
			}
			
			if(!empty($user_address)){
				$user_address['user_id'] = $user_id;
				$user_address['is_country'] = $this->countryCode;
				$this->db->insert('user_address', $user_address);
			}
			
			if(!empty($user_bank)){
				$user_bank['user_id'] = $user_id;
				$user_bank['is_country'] = $this->countryCode;
				$this->db->insert('user_bank', $user_bank);
			}
			
			if(!empty($user_document)){
				$user_document['user_id'] = $user_id;
				$user_document['is_country'] = $this->countryCode;
				$this->db->insert('user_document', $user_document);
			}
			
			if(!empty($user_vendor)){
				$user_vendor['user_id'] = $user_id;
				$user_vendor['is_country'] = $this->countryCode;
				$this->db->insert('user_vendor', $user_vendor);
			}
			
			
	    	return true;
		}
		return false;
    }
	
	
	
	function checkMobile($mobile, $country_code){
		$q = $this->db->select('*')	->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', 3)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return 0;
	}
	
	function notallocatedtaxi($user_id){
		$query = "select t.id, t.name, t.number, t.model, tt.name as taxi_type from {$this->db->dbprefix('taxi')} as t
		LEFT JOIN {$this->db->dbprefix('taxi_type')} as tt ON tt.id = t.type where t.vendor_id='".$user_id."' AND t.id NOT IN (SELECT taxi_id FROM {$this->db->dbprefix('driver_current_status')} WHERE allocated_status != 2) ";
		
		
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return false;	
	}
	
	/*function add_vendor($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document, $vendor_group_id, $driver_group_id, $operator, $taxi, $taxi_document){
		
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			$username = 'VEN'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_vendor['user_id'] = $user_id;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			$this->db->insert('user_vendor', $user_vendor);
			
			if($operator == 'vendor_and_driver'){
				$driver_user['parent_id'] = $user_id;
				$this->db->insert('users', $driver_user);
				
				if($driver_user_id = $this->db->insert_id()){
					$username = 'DRI'.str_pad($driver_user_id, 5, 0, STR_PAD_LEFT);
					$this->db->update('users', array('username' => $username), array('id' => $driver_user_id));
			
					$user_profile['user_id'] = $driver_user_id;
					$user_address['user_id'] = $driver_user_id;
					$user_bank['user_id'] = $driver_user_id;
					$driver_user_document['user_id'] = $driver_user_id;
					$this->db->insert('user_profile', $user_profile);
					$this->db->insert('user_address', $user_address);
					$this->db->insert('user_bank', $user_bank);
					$this->db->insert('user_document', $driver_user_document);
				}
			}
			
			if(!empty($taxi)){
				$taxi['user_id'] = $user_id;
				$taxi['group_id'] = $vendor_group_id;
				$this->db->insert('taxi', $taxi);
				if($taxi_id = $this->db->insert_id()){
					$taxi_document['taxi_id'] = $taxi_id;
					$taxi_document['user_id'] = $user_id;
					$taxi_document['group_id'] = $vendor_group_id;
					$this->db->insert('taxi_document', $taxi_document);
				}
			}	
			
			if(!empty($taxi_id) && !empty($driver_user_id)){
				$this->db->insert('driver_current_status', array('driver_id' => $driver_user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $user_id));
			}
			
	    	return true;
		}
		return false;
    }*/
	
	public function getUserEdit($user_id){
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, u.email, u.country_code, u.mobile, u.active, u.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, ud.local_image, ud.local_address, ud.local_pincode, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id,  ud.local_country_id,  ud.local_zone_id,  ud.local_state_id,  ud.local_city_id,  ud.local_area_id,  ud.permanent_verify, ud.permanent_approved_by, ud.permanent_pincode, ud.permanent_approved_on, ud.permanent_image, ud.permanent_address,  ud.permanent_continent_id,  ud.permanent_country_id,  ud.permanent_zone_id,  ud.permanent_state_id,  ud.permanent_city_id,  ud.permanent_area_id, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, ub.account_no, ub.is_verify as account_verify, ub.bank_name, ub.account_holder_name, ub.branch_name, ub.ifsc_code, udoc.aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  udoc.pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image,  udoc.license_no, udoc.license_country_id, udoc.license_type, udoc.license_approved_by, udoc.license_approved_on, udoc.license_verify, udoc.license_dob, udoc.license_ward_name, udoc.license_no,  udoc.license_country_id, udoc.license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, udoc.police_on, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, udoc.loan_information, udoc.loan_verify, u.first_name, u.last_name, u.gender, u.dob, u.photo, ugroup.name as group_name, pgroup.name as parent_group_name, userper.department_id, ur.position,  userper.designation_id, userdep.name as user_department, userper.continent_id, urc.name as continent_name, userper.country_id, urcc.name as country_name, userper.zone_id, urz.name as zone_name, userper.state_id, urs.name as state_name, userper.city_id, urcity.name as city_name, userper.area_id, ura.name as area_name, uv.gst, uv.telephone_number, uv.legal_entity, uv.associated_id, uv.continent_id as vendor_continent_id, uv.country_id as vendor_country_id, uv.zone_id as vendor_zone_id, uv.state_id as vendor_state_id, uv.city_id as vendor_city_id, uv.is_verify as vendor_is_verify, uv.approved_by as vendor_approved_by, uv.approved_on as vendor_approved_on, assoc.first_name as associated_name');
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
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		$this->db->join('license_type lt', 'lt.id = udoc.license_type', 'left');
		$this->db->where('u.is_edit', 1);
		$this->db->where('u.id', $user_id);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			
            return $row;
        }
		return false;	
	}
	
	function edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $customer_type){
		
        if(!empty($user_id) && !empty($customer_type)){
			
			if($customer_type == 1){
				$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('users', $user, array('id' => $user_id));
				$user_profile['user_id'] = $user_id;
				$user_address['user_id'] = $user_id;
				$user_profile['is_country'] = $this->countryCode;
				$user_address['is_country'] = $this->countryCode;
				$this->db->insert('user_profile', $user_profile);
				$this->db->insert('user_address', $user_address);
			}elseif($customer_type == 2){
				$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$user_bank['user_id'] = $user_id;
				$user_bank['is_country'] = $this->countryCode;
				$this->db->insert('user_bank', $user_bank);
			}elseif($customer_type == 3){
				
				$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$this->db->update('user_vendor', array('is_edit' => 0), array('user_id' => $user_id, 'is_country' => $this->countryCode));
				$user_vendor['user_id'] = $user_id;
				$user_document['user_id'] = $user_id;
				$user_vendor['is_country'] = $this->countryCode;
				$user_document['is_country'] = $this->countryCode;
				$this->db->insert('user_document', $user_document);
				$this->db->insert('user_vendor', $user_vendor);	
				
			}
			
	    	return true;
		}
		return false;
    }
	
	
	function checkVendor($user_id, $group_id){
		
		$q = $this->db->select("u.id as id, u.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status ")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id)
			->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				return true;
			}
		}
		return false;	
	}
	
	function allocatedopen($data, $driver_id){
		$data['is_country'] = $this->countryCode;
		$this->db->insert('driver_current_status', $data);
		if($driver_id){
			$this->db->select('oauth_token, country_code, mobile');
			$this->db->where('id', $driver_id);
			$q = $this->db->get('users');
			if($q->num_rows()>0){
				$data =  $q->row();
				return $data;
			}			
		}
		return false;
	}
	
	function getDriverID($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('users');
		
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
	
	/*function check_login($login){
		$data = array();
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token, u.first_name, u.last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile, u.mobile_otp from {$this->db->dbprefix('users')} as u 		
		where u.password='".$login['password']."' AND  u.mobile='".$login['mobile']."' AND  u.country_code='".$login['country_code']."'  AND u.group_id = 3  AND u.active = 1";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->photo !=''){
				$row->vendor_photo = $image_path.$row->photo;
			}else{
				$row->vendor_photo = $image_path.'default.png';
			}
			if($q->row('devices_imei') != $login['devices_imei']){
				$row->check_status = 3;
			}else{
				$row->check_status = 1;
			}
			$data = $row;
			return $data;
		}		
		return 	$data->check_status = 0;
	}
	*/
	function check_login($data){
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token,  u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile, u.active from {$this->db->dbprefix('users')} as u   where u.password='".$data['password']."'  AND u.mobile='".$data['mobile']."' AND u.country_code='".$data['country_code']."'  AND u.group_id = 3 ";
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
		    $row = $q->row();
			
				if($q->row('devices_imei') == 'first_time' && $q->row('active') == 1){
					$row->check_status = 'first_time_otp';
				}elseif($q->row('devices_imei') == $data['devices_imei']  && $q->row('active') == 1){
					$row->check_status = 'login';
					$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $data['mobile'], 'time' => time()));
				}elseif($q->row('devices_imei') != $data['devices_imei']  && $q->row('active') == 1){
					$row->check_status = 'change_otp';
					$this->db->update('users', array('mobile_otp' => $data['otp'], 'mobile_otp_verify' => 0), array('id' => $row->id, 'is_country' => $this->countryCode));
				}else{
					$row->check_status = 'notactive';
				}
				
				if($row->photo !=''){
					$row->vendor_photo = $image_path.$row->photo;
				}else{
					$row->vendor_photo = $image_path.'no_image.png';
				}
				$s = $this->db->select('*')->get('settings');
					$row->camera_enable = $s->row('camera_enable');
					
				$row->unicode_symbol = $unicode_symbol;
				
				
				$userCheck = $this->db->select("u.id as id, u.created_on, u.first_name, up.last_name, u.email, u.mobile,  up.gender, u.active as active, If(up.is_approved = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1, '1', '0') as status")
				->from("users u")
				->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
				->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
				->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
				->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
				->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1", 'left')
					->where("u.id", $row->id)
					->get();
					
					if($userCheck->num_rows()>0){
						$row->is_userverified = $userCheck->row('status');
					}else{
						$row->is_userverified = '0';
					}
					
				$data =  $row;
				return $data;
		}
		return false;
	}
    
	function devicescheckotp($data){
		$image_path = base_url('assets/uploads/');
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		$query = "select u.id, u.oauth_token,  u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.email, u.dob, u.devices_imei, u.username, u.country_code, u.mobile, u.active from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$data['vendor_id']."' AND  u.mobile_otp='".$data['otp']."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		//$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['vendor_id'], 'is_country' => $this->countryCode));
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['vendor_id'], 'user_type' => 3,  'device_token' => $row->oauth_token, 'is_country' => $this->countryCode));
			
			if($row->photo !=''){
				$row->vendor_photo = $image_path.$row->photo;
			}else{
				$row->vendor_photo = $image_path.'default.png';
			}	
			$s = $this->db->select('*')->get('settings');
				$row->camera_enable = $s->row('camera_enable');	
				
			$row->unicode_symbol = $unicode_symbol;
			
			
			$userCheck = $this->db->select("u.id as id, u.created_on, u.first_name, up.last_name, u.email, u.mobile,  up.gender, u.active as active, If(up.is_approved = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1", 'left')
				->where("u.id", $row->id)
				->get();
				
				if($userCheck->num_rows()>0){
					$row->is_userverified = $userCheck->row('status');
				}else{
					$row->is_userverified = '0';
				}
						
			$data =  $row;
			$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $row->mobile, 'time' => time()));
			return $data;
		}
		return false;
		
	}
	
	function checkfirstotp($data){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$data['vendor_id']."' AND  u.mobile_otp='".$data['otp']."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		//$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['vendor_id'], 'is_country' => $this->countryCode));
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['vendor_id'], 'user_type' => 3,  'device_token' => $row->oauth_token, 'is_country' => $this->countryCode));
			
			if($row->photo !=''){
				$row->vendor_photo = $image_path.$row->photo;
			}else{
				$row->vendor_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;
	}
	
	function checkotp($data){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$data['vendor_id']."' AND  u.mobile_otp='".$data['otp']."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		//$q = $this->db->query($query);

		if($q->num_rows()>0){
			
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'active' => 1), array('id' => $data['vendor_id'], 'is_country' => $this->countryCode));
			
			$row = $q->row();
			if($row->photo !=''){
				$row->vendor_photo = $image_path.$row->photo;
			}else{
				$row->vendor_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;
	}
	
	function resendotp($data){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['vendor_id']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $data['vendor_id']);
			//$this->db->update('users', array('mobile_otp' =>  $data['mobile_otp']));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgototp($data){
		$query = "select id, oauth_token, country_code, email, mobile, active, devices_imei from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."' AND group_id = 3 ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->where('is_country', $this->countryCode);
			$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgotcheckotp($data){
		$query = "select id, oauth_token, country_code, email, mobile, active, devices_imei from {$this->db->dbprefix('users')} where id='".$data['vendor_id']."' AND  forgot_otp='".$data['forgot_otp']."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			return true;
		}
		return false;
	}
	
	function forgotresendotp($data){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['vendor_id']."'  AND group_id = 3 ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $q->row('id'));
			//$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function updatepassword($data){
		
		$this->db->where('id', $data['vendor_id']);
		$this->db->where('is_country', $this->countryCode);
		$q = $this->db->update('users', array('password' => $data['password'], 'text_password' => $data['text_password'], 'forgot_otp_verify' => 1));
		if($q){
			return true;	
		}
		return false;
	}
	
	function getVendor($oauth_token){
		$this->db->select('*');
		$this->db->where('oauth_token', $oauth_token);
		$q = $this->db->get('users');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	
	/*### Taxi*/
	function getAllvendorwiseTaxi($parent_id){
		$image_path = base_url('assets/uploads/');
		$query = "select t.id, t.name, t.model, t.number, tt.name as taxi_type, t.engine_number, t.chassis_number, t.make, tf.name as fuel_name, t.color, t.manufacture_year, t.capacity, t.photo, t.mode, t.is_verify from {$this->db->dbprefix('taxi')} as t LEFT JOIN {$this->db->dbprefix('taxi_fuel')} as tf ON tf.id= t.fuel_type LEFT JOIN {$this->db->dbprefix('taxi_type')} as tt ON tt.id = t.type  where t.vendor_id='".$parent_id."' ";
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				if($row->photo ==''){
					$row->photo =  $image_path.$row->photo;
				}else{
					$row->photo =  $image_path.'no_image.png';
				}
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	function getTaxiDetails($id){
		$image_path = base_url('assets/uploads/');
				
		$query = "select t.id, t.name, t.model, t.number, tt.name as taxi_type, t.engine_number, t.chassis_number, t.make, tf.name as fuel_name, t.color, t.manufacture_year, t.capacity, t.photo, t.mode, t.is_verify, td.reg_image, td.reg_date, td.reg_due_date, td.reg_owner_name, td.reg_owner_address, td.reg_verify, td.taxation_image, td.taxation_amount_paid, td.taxation_due_date, td.taxation_verify, td.insurance_image, td.insurance_policy_no, td.insurance_due_date, td.insurance_verify, td.permit_image, td.permit_no, td.permit_due_date, td.permit_verify, td.authorisation_image, td.authorisation_no, td.authorisation_due_date, td.authorisation_verify, td.fitness_image, td.fitness_due_date, td.fitness_verify, td.speed_image, td.speed_due_date, td.puc_image, td.puc_due_date, td.puc_verify from {$this->db->dbprefix('taxi')} as t 
		LEFT JOIN {$this->db->dbprefix('taxi_fuel')} as tf ON tf.id= t.fuel_type 
		LEFT JOIN {$this->db->dbprefix('taxi_type')} as tt ON tt.id = t.type  
		LEFT JOIN {$this->db->dbprefix('taxi_document')} as td ON td.taxi_id = t.id
		where t.id='".$id."' ";
		
		
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				
				if($row->photo !=''){
					$row->photo =  $image_path.$row->photo;
				}else{
					$row->photo =  $image_path.'no_image.png';
				}
				
				if($row->reg_image !=''){
					$row->reg_image =  $image_path.$row->reg_image;
				}else{
					$row->reg_image =  $image_path.'no_image.png';
				}
				if($row->taxation_image !=''){
					$row->taxation_image =  $image_path.$row->taxation_image;
				}else{
					$row->taxation_image =  $image_path.'no_image.png';
				}
				if($row->insurance_image !=''){
					$row->insurance_image =  $image_path.$row->insurance_image;
				}else{
					$row->insurance_image =  $image_path.'no_image.png';
				}
				if($row->permit_image !=''){
					$row->permit_image =  $image_path.$row->permit_image;
				}else{
					$row->permit_image =  $image_path.'no_image.png';
				}
				if($row->authorisation_image !=''){
					$row->authorisation_image =  $image_path.$row->authorisation_image;
				}else{
					$row->authorisation_image =  $image_path.'no_image.png';
				}
				if($row->fitness_image !=''){
					$row->fitness_image =  $image_path.$row->fitness_image;
				}else{
					$row->fitness_image =  $image_path.'no_image.png';
				}
				if($row->speed_image !=''){
					$row->speed_image =  $image_path.$row->speed_image;
				}else{
					$row->speed_image =  $image_path.'no_image.png';
				}
				if($row->puc_image !=''){
					$row->puc_image =  $image_path.$row->puc_image;
				}else{
					$row->puc_image =  $image_path.'no_image.png';
				}
				
				
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	
	function add_taxi($taxi, $taxi_document){
		$taxi['is_country'] = $this->countryCode;
		$this->db->insert('taxi', $taxi);
        if($id = $this->db->insert_id()){
			
			$taxi_document['taxi_id'] = $id;
			$taxi_document['is_country'] = $this->countryCode;
			$this->db->insert('taxi_document', $taxi_document);
	    	return true;
		}
		return false;
    }
	
	/*### Driver*/
	
	function  mycurrentrides($vendor_id){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, t.name taxi_name, t.number,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms,  tt.name types,  cp.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.parent_id = '.$vendor_id.'');
		$this->db->join('user_profile dp', 'dp.id = d.id');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		//$this->db->where('r.driver_id', $driver_id);
		$this->db->where('DATE(r.booked_on) <=', $current_date);
		$this->db->where_in('r.status', array('3', '4'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		//$this->db->limit(1);
		$q = $this->db->get();
		
		
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later');
				
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
				if($row->customer_name ==''){
					$row->customer_name =  '0';
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
				
                $data[] = $row;
            }
			
            return $data;
			
		}
		
		
		return false;	
	}
	
	function  mypastrides($vendor_id){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  cp.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.parent_id = '.$vendor_id.'');
		$this->db->join('user_profile dp', 'dp.id = d.id');
		$this->db->join('users c', 'c.id = r.customer_id');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		//$this->db->where('r.driver_id', $driver_id);
		$this->db->where('DATE(r.booked_on) <=', $current_date);
		$this->db->where_in('r.status', array('5', '6', '8'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later');
				
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
				if($row->customer_name ==''){
					$row->customer_name =  '0';
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
				
				
                $data[] = $row;
            }
            return $data;
			
		}
		
		
		return false;	
	}
	
	function  myupcomingrides($vendor_id){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  cp.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.parent_id = '.$vendor_id.'');
		$this->db->join('user_profile dp', 'dp.id = d.id');
		$this->db->join('users c', 'c.id = r.customer_id');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		//$this->db->where('r.driver_id', $driver_id);
		$this->db->where('DATE(r.booked_on) >', $current_date);
		$this->db->where_in('r.status', array('5', '6'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later');
				
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
				if($row->customer_name ==''){
					$row->customer_name =  '0';
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
				
				
                $data[] = $row;
            }
            return $data;
			
		}
		
		
		return false;	
	}
	
	function getAllvendortruckDriver($user_id){
		$image_path = base_url('assets/uploads/');
		$this->db->select('u.id, up.first_name, dc.taxi_id, dc.mode, dc.current_latitude, dc.current_longitude, t.name as taxi_name, tt.name as taxi_type, tt.mapcar');
		$this->db->from('users u');
		$this->db->join('driver_current_status dc', 'dc.driver_id = u.id AND dc.allocated_status != 2 AND dc.mode != 0 AND dc.is_connected = 1');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->join('taxi t', 't.id = dc.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->where('u.parent_id', $user_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				if($row->mapcar != ''){
					$row->mapcar =  $image_path.$row->mapcar;
				}else{
					$row->mapcar =  $image_path.'no_image.png';
				}
				$data[] = $row;
			}
			return $data;
		}
		return false;	
	}
	
	function getAllvendorwiseDriver($parent_id){
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token, IFNULL(dc.is_allocated, 0) as is_allocated, IFNULL(dc.taxi_id, 0) as taxi_id, IFNULL(t.name, 0) as taxi_name, IFNULL(t.type, 0) as type, IFNULL(tt.name, 0) as taxi_type, u.country_code, u.email, u.mobile, u.active, u.devices_imei, u.first_name, up.last_name, up.photo, g.name as group_name from {$this->db->dbprefix('users')} as u LEFT JOIN {$this->db->dbprefix('user_profile')} as up ON up.user_id = u.id
		LEFT JOIN {$this->db->dbprefix('driver_current_status')} as dc ON dc.driver_id = u.id
		LEFT JOIN {$this->db->dbprefix('taxi')} as t ON t.id = dc.taxi_id
		LEFT JOIN {$this->db->dbprefix('taxi_type')} as tt ON tt.id = t.type
		 LEFT JOIN {$this->db->dbprefix('groups')} as g ON g.id = u.group_id  where u.parent_id='".$parent_id."' ";
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				
				if($row->photo != ''){
					$row->photo =  $image_path.$row->photo;
				}else{
					$row->photo =  $image_path.'no_image.png';
				}
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	function getDriverDetails($id){
		$image_path = base_url('assets/uploads/');
				
		$query = "select u.oauth_token, u.devices_imei, u.email, u.country_code, u.mobile, u.mobile_otp_verify, u.active, u.group_id, u.is_approved, ud.local_image, ud.local_address, IFNULL(lcc.name, 0) as local_continent, IFNULL(lc.name, 0) as local_country, IFNULL(lz.name, 0) as local_zone, IFNULL(ls.name, 0) as local_state, IFNULL(lci.name, 0) as local_city, IFNULL(la.name, 0) as local_area, ud.local_verify, ud.permanent_image, ud.permanent_address, IFNULL(pcc.name, 0) as permanent_continent, IFNULL(pc.name, 0) as permanent_country, IFNULL(pz.name, 0) as permanent_zone, IFNULL(ps.name, 0) as permanent_state, IFNULL(pci.name, 0) as permanent_city, IFNULL(pa.name, 0) as permanent_area, ud.permanent_verify, ub.account_no, ub.bank_name, ub.branch_name, ub.ifsc_code, ub.is_verify, udoc.aadhaar_no, udoc.aadhaar_image, udoc.aadhar_verify, udoc.pancard_no, udoc.pancard_image, udoc.pancard_verify, udoc.license_image, udoc.license_dob, udoc.license_ward_name, udoc.license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.license_verify, udoc.police_image, udoc.police_on, udoc.police_til, udoc.police_verify, u.first_name, up.last_name, up.gender, up.dob, up.photo from {$this->db->dbprefix('users')} as u
		
		LEFT JOIN {$this->db->dbprefix('user_address')} as ud ON ud.user_id = u.id
		
		LEFT JOIN {$this->db->dbprefix('continents')} as lcc ON lcc.id = ud.local_continent_id
		LEFT JOIN {$this->db->dbprefix('countries')} as lc ON lc.id = ud.local_country_id
		LEFT JOIN {$this->db->dbprefix('zones')} as lz ON lz.id = ud.local_zone_id
		LEFT JOIN {$this->db->dbprefix('states')} as ls ON ls.id = ud.local_state_id
		LEFT JOIN {$this->db->dbprefix('cities')} as lci ON lci.id = ud.local_city_id
		LEFT JOIN {$this->db->dbprefix('areas')} as la ON la.id = ud.local_area_id
		
		LEFT JOIN {$this->db->dbprefix('continents')} as pcc ON pcc.id = ud.permanent_continent_id
		LEFT JOIN {$this->db->dbprefix('countries')} as pc ON pc.id = ud.permanent_country_id
		LEFT JOIN {$this->db->dbprefix('zones')} as pz ON pz.id = ud.permanent_zone_id
		LEFT JOIN {$this->db->dbprefix('states')} as ps ON ps.id = ud.permanent_state_id
		LEFT JOIN {$this->db->dbprefix('cities')} as pci ON pci.id = ud.permanent_city_id
		LEFT JOIN {$this->db->dbprefix('areas')} as pa ON pa.id = ud.permanent_area_id
		
		
		LEFT JOIN {$this->db->dbprefix('user_profile')} as up ON up.user_id = u.id
		LEFT JOIN {$this->db->dbprefix('user_bank')} as ub ON ub.user_id = u.id
		LEFT JOIN {$this->db->dbprefix('user_document')} as udoc ON udoc.user_id = u.id
		where u.id='".$id."'
		";
		
		
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				
				if($row->photo ==''){
					$row->photo =  $image_path.$row->photo;
				}else{
					$row->photo =  $image_path.'no_image.png';
				}
				
				if($row->aadhaar_image !=''){
					$row->aadhaar_image =  $image_path.$row->aadhaar_image;
				}else{
					$row->aadhaar_image =  $image_path.'no_image.png';
				}
				if($row->pancard_image !=''){
					$row->pancard_image =  $image_path.$row->pancard_image;
				}else{
					$row->pancard_image =  $image_path.'no_image.png';
				}
				if($row->license_image !=''){
					$row->license_image =  $image_path.$row->license_image;
				}else{
					$row->license_image =  $image_path.'no_image.png';
				}
				if($row->police_image !=''){
					$row->police_image =  $image_path.$row->police_image;
				}else{
					$row->police_image =  $image_path.'no_image.png';
				}
				
				if($row->dob == '0000-00-00' || $row->dob == NULL){
					$row->dob = '0';
				}else{
					$row->dob = date("d/m/Y", strtotime($row->dob));
				}
				
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	function add_driver($user, $user_profile, $user_address, $user_bank, $user_document){
		$user['is_country'] = $this->countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_document['is_country'] = $this->countryCode;
			$user_bank['is_country'] = $this->countryCode;
			$user_address['is_country'] = $this->countryCode;
			$user_profile['is_country'] = $this->countryCode;
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			
			
	    	return true;
		}
		return false;
    }
		
	
    
}
