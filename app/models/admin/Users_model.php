<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function getUser($user_id){
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, u.email, u.is_country, u.country_code, u.mobile, u.is_daily, u.is_rental, u.is_outstation, u.is_hiring, u.is_corporate, u.active, u.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, ud.same_address, ud.local_image,  ud.local_address, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id, lc.name as local_continent_name, ud.local_country_id, lcc.name as local_country_name, ud.local_zone_id, lz.name as local_zone_name, ud.local_state_id, ls.name as local_state_name, ud.local_city_id, lcity.name as local_city_name, ud.local_area_id, ud.local_pincode, la.name as local_area_name, ud.permanent_verify, ud.permanent_approved_by, ud.permanent_approved_on, ud.permanent_image, ud.permanent_address, ud.permanent_continent_id, pc.name as permanent_continent_name, ud.permanent_country_id, pcc.name as permanent_country_name, ud.permanent_zone_id, ud.permanent_pincode, pz.name as permanent_zone_name, ud.permanent_state_id, ps.name as permanent_state_name, ud.permanent_city_id, pcity.name as permanent_city_name, ud.permanent_area_id, pa.name as permanent_area_name, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, ub.account_no, ub.is_verify as account_verify, ub.account_holder_name, ub.bank_name, ub.branch_name, ub.ifsc_code, udoc.aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  udoc.pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image, udoc.license_approved_by, udoc.license_approved_on, udoc.license_no, udoc.license_verify, udoc.license_dob, udoc.license_ward_name, lt.name as license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, udoc.police_on, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, udoc.loan_information, udoc.loan_verify, u.first_name, u.last_name, u.gender, u.dob, u.photo, ugroup.name as group_name, pgroup.name as parent_group_name, userper.department_id, ur.position,  userper.designation_id, userdep.name as user_department, userper.continent_id, urc.name as continent_name, userper.country_id, urcc.name as country_name, userper.zone_id, urz.name as zone_name, userper.state_id, urs.name as state_name, userper.city_id, urcity.name as city_name, userper.area_id, ura.name as area_name, uv.gst, uv.telephone_number, uv.legal_entity, uv.associated_id, assoc.first_name as associated_name');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id AND uv.is_edit  = 1', 'left');
		$this->db->join('user_profile assoc', 'assoc.user_id = uv.associated_id AND assoc.is_edit  = 1', 'left');
		$this->db->join('user_address ud', 'ud.user_id = u.id AND ud.is_edit  = 1', 'left');
		$this->db->join('user_bank ub', 'ub.user_id = u.id AND ub.is_edit  = 1', 'left');
		$this->db->join('user_document udoc', 'udoc.user_id = u.id AND udoc.is_edit  = 1', 'left');
		$this->db->join('user_profile up', 'up.user_id = u.id AND up.is_edit  = 1', 'left');
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		$this->db->join('pincode lin', 'lin.pincode = ud.local_pincode', 'left');
		$this->db->join('areas la', 'la.id = lin.area_id', 'left');
		$this->db->join('cities lcity', 'lcity.id = la.city_id', 'left');
		$this->db->join('states ls', 'ls.id = lcity.state_id', 'left');
		$this->db->join('zones lz', 'lz.id = ls.zone_id', 'left');
		$this->db->join('countries lcc', 'lcc.id = lz.country_id', 'left');
		$this->db->join('continents lc', 'lc.id = lcc.continent_id', 'left');
		$this->db->join('pincode pin', 'pin.pincode = ud.permanent_pincode', 'left');
		$this->db->join('areas pa', 'pa.id = pin.area_id', 'left');
		$this->db->join('cities pcity', 'pcity.id = pa.city_id', 'left');
		$this->db->join('states ps', 'ps.id = pcity.state_id', 'left');
		$this->db->join('zones pz', 'pz.id = ps.zone_id', 'left');
		$this->db->join('countries pcc', 'pcc.id = pz.country_id', 'left');
		$this->db->join('continents pc', 'pc.id = pcc.continent_id', 'left');
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
			
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
			
			if($row->local_image !=''){
				$row->local_image_img = $image_path.$row->local_image;
			}else{
				$row->local_image_img = $image_path.'no_image.png';
			}
			
			if($row->permanent_image !=''){
				$row->permanent_image_img = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image_img = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image !=''){
				$row->aadhaar_image_img = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image_img = $image_path.'no_image.png';
			}
			
			if($row->pancard_image !=''){
				$row->pancard_image_img = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image_img = $image_path.'no_image.png';
			}
			
			if($row->license_image !=''){
				$row->license_image_img = $image_path.$row->license_image;
			}else{
				$row->license_image_img = $image_path.'no_image.png';
			}
			
			if($row->police_image !=''){
				$row->police_image_img = $image_path.$row->police_image;
			}else{
				$row->police_image_img = $image_path.'no_image.png';
			}
			
			if($row->loan_doc !=''){
				$row->loan_doc_img = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc_img = $image_path.'no_image.png';
			}
			
            return $row;
        }
		return false;	
	}
	
	public function getUserEdit($user_id){
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, u.email, u.is_country, u.country_code, up.approved_on, up.approved_by, up.is_approved, u.mobile,  u.is_daily, u.is_rental, u.is_outstation, u.is_hiring, u.is_corporate, u.active, u.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, ud.local_image, ud.local_address, ud.same_address, ud.local_pincode, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id, lc.name as local_continent_name, ud.local_country_id, lcc.name as local_country_name, ud.local_zone_id, lz.name as local_zone_name, ud.local_state_id, ls.name as local_state_name, ud.local_city_id, lcity.name as local_city_name, ud.local_area_id, ud.local_pincode, la.name as local_area_name, ud.permanent_verify, ud.permanent_approved_by, ud.permanent_pincode, ud.permanent_approved_on, ud.permanent_image, ud.permanent_address, ud.permanent_pincode, ud.permanent_continent_id, pc.name as permanent_continent_name, ud.permanent_country_id, pcc.name as permanent_country_name, ud.permanent_zone_id, pz.name as permanent_zone_name, ud.permanent_state_id, ps.name as permanent_state_name, ud.permanent_city_id, pcity.name as permanent_city_name, ud.permanent_area_id, pa.name as permanent_area_name, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, ub.account_no, ub.is_verify as account_verify, ub.bank_name, ub.account_holder_name, ub.branch_name, ub.ifsc_code, udoc.aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  udoc.pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image,  udoc.license_no, udoc.license_country_id, udoc.license_type, udoc.license_approved_by, udoc.license_approved_on, udoc.license_verify, udoc.license_dob, udoc.license_ward_name, udoc.license_no,  udoc.license_country_id, udoc.license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, udoc.police_on, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, udoc.loan_information, udoc.loan_verify, u.first_name, u.last_name, u.gender, u.dob, u.photo, ugroup.name as group_name, pgroup.name as parent_group_name, userper.department_id, ur.position,  userper.designation_id, userdep.name as user_department, userper.continent_id, urc.name as continent_name, userper.country_id, urcc.name as country_name, userper.zone_id, urz.name as zone_name, userper.state_id, urs.name as state_name, userper.city_id, urcity.name as city_name, userper.area_id, ura.name as area_name, uv.gst, uv.telephone_number, uv.legal_entity, uv.associated_id, uv.continent_id as vendor_continent_id, uv.country_id as vendor_country_id, uv.zone_id as vendor_zone_id, uv.state_id as vendor_state_id, uv.city_id as vendor_city_id, uv.is_verify as vendor_is_verify, uv.approved_by as vendor_approved_by, uv.approved_on as vendor_approved_on, assoc.first_name as associated_name');
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
		$this->db->join('pincode lin', 'lin.pincode = ud.local_pincode', 'left');
		$this->db->join('areas la', 'la.id = lin.area_id', 'left');
		$this->db->join('cities lcity', 'lcity.id = la.city_id', 'left');
		$this->db->join('states ls', 'ls.id = lcity.state_id', 'left');
		$this->db->join('zones lz', 'lz.id = ls.zone_id', 'left');
		$this->db->join('countries lcc', 'lcc.id = lz.country_id', 'left');
		$this->db->join('continents lc', 'lc.id = lcc.continent_id', 'left');
		$this->db->join('pincode pin', 'pin.pincode = ud.permanent_pincode', 'left');
		$this->db->join('areas pa', 'pa.id = pin.area_id', 'left');
		$this->db->join('cities pcity', 'pcity.id = pa.city_id', 'left');
		$this->db->join('states ps', 'ps.id = pcity.state_id', 'left');
		$this->db->join('zones pz', 'pz.id = ps.zone_id', 'left');
		$this->db->join('countries pcc', 'pcc.id = pz.country_id', 'left');
		$this->db->join('continents pc', 'pc.id = pcc.continent_id', 'left');
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
			
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
			
			if($row->local_image !=''){
				$row->local_image_img = $image_path.$row->local_image;
			}else{
				$row->local_image_img = $image_path.'no_image.png';
			}
			
			if($row->permanent_image !=''){
				$row->permanent_image_img = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image_img = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image !=''){
				$row->aadhaar_image_img = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image_img = $image_path.'no_image.png';
			}
			
			if($row->pancard_image !=''){
				$row->pancard_image_img = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image_img = $image_path.'no_image.png';
			}
			
			if($row->license_image !=''){
				$row->license_image_img = $image_path.$row->license_image;
			}else{
				$row->license_image_img = $image_path.'no_image.png';
			}
			
			if($row->police_image !=''){
				$row->police_image_img = $image_path.$row->police_image;
			}else{
				$row->police_image_img = $image_path.'no_image.png';
			}
			
			if($row->loan_doc !=''){
				$row->loan_doc_img = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc_img = $image_path.'no_image.png';
			}
			
            return $row;
        }
		return false;	
	}
	
	/*### Employee*/
	
	function edit_employee($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $countryCode){
		$user = array_map(function($v){return (is_null($v)) ? "" : $v;},$user);
		$user_profile = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_profile);
		$user_address = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_address);
		$user_bank = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_bank);
		$user_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_document);
		
		if($user_id){
			//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
			$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id));
		}
		$this->db->update('users', $user, array('id' => $user_id));
        if($user_id){
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);			
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			
		
	    	return true;
		}
		return false;
    }
	
	
	function edit_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $countryCode){
		
		if($user_id){
			//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
			$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_vendor', array('is_edit' => 0), array('user_id' => $user_id));
		}
		$this->db->update('users', $user, array('id' => $user_id));
        if($user_id){

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
	
	
	function editadmin_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $countryCode){
		
		
		if($user_id){
			//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
			$this->db->update('user_document', $user_document, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('user_address', $user_address, array('user_id' => $user_id, 'is_edit' => 1));
			
			$this->db->update('user_bank', $user_bank, array('user_id' => $user_id, 'is_edit' => 1));
			
			$this->db->update('user_profile', $user_profile, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('user_vendor', $user_vendor, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('users', $user, array('id' => $user_id));
			return true;
		}
		
        
		return false;
    }
	
	function edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $countryCode){
		$user = array_map(function($v){return (is_null($v)) ? "" : $v;},$user);
		$user_profile = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_profile);
		$user_address = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_address);
		$user_bank = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_bank);
		$user_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_document);
		
		if($user_id){
			//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
			$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id));
			$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id));
		}
		$this->db->update('users', $user, array('id' => $user_id));
        if($user_id){
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;

			$this->db->insert('user_profile', $user_profile);
			
			$this->db->insert('user_address', $user_address);
			
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			
			
			
	    	return true;
		}
		return false;
    }
	
	function editadmin_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $driver_group_id, $countryCode){
		
		$user = array_map(function($v){return (is_null($v)) ? "" : $v;},$user);
		$user_profile = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_profile);
		$user_address = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_address);
		$user_bank = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_bank);
		$user_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$user_document);
		$taxi = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi);
		$taxi_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi_document);
		
		if(!empty($taxi)){
			$taxi['driver_id'] = $user_id;
			$this->db->insert('taxi', $taxi);
			
			if($taxi_id = $this->db->insert_id()){
				$taxi_document['taxi_id'] = $taxi_id;
				$taxi_document['user_id'] = $user_id;
				$taxi_document['group_id'] = $driver_group_id;
				$this->db->insert('taxi_document', $taxi_document);
				
				if(!empty($taxi_id) && !empty($user_id)){
					$this->db->insert('driver_current_status', array('driver_id' => $user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $parent_id, 'is_allocated' => 1, 'allocated_start_date' => date('Y-m-d H:is'), 'allocated_status' => 1, 'is_country' => $countryCode));
				}
				
			}
			
			
		}
		
		
		if($user_id){
			//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
			$this->db->update('user_document', $user_document, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('user_address', $user_address, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('user_bank', $user_bank, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('user_profile', $user_profile, array('user_id' => $user_id, 'is_edit' => 1));
			$this->db->update('users', $user, array('id' => $user_id));
			return true;
		}
			
		return false;
    }
	
}
