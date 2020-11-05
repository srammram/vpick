<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
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
	
	function getUrldata($countryCode){
		$data = array();
		$this->db->select('*')->where('is_edit', 1)->where('is_approved', 0);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			
			foreach(($q->result())  as $row){
				
				if($row->active == 1){
					if($row->group_id == 3){
						$vendor_inactive[] = $row;
					}
					if($row->group_id == 4){
						$driver_inactive[] = $row;
					}
				}
			}
		}
		
		
		$this->db->select('u.id as user_id, ud.*')->from('users u')->join('user_address ud', 'ud.is_edit = 1 AND ud.user_id = u.id');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('u.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('u.is_country', $countryCode);
		}
		$this->db->where('u.is_edit', 1)->group_by('u.id');
		$a = $this->db->get();
		if ($a->num_rows() > 0) {
			
			foreach(($a->result())  as $aow){
				
				if($aow->local_verify == 0 || $aow->permanent_verify == 0){
					$address_inactive[] = $aow;
				}
			}
		}
		
		
		 $this->db->select('u.id as user_id, ud.*')->from('users u')->join('user_bank ud', 'ud.is_edit = 1 AND ud.user_id = u.id');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('u.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('u.is_country', $countryCode);
		}
		$this->db->where('u.is_edit', 1)->group_by('u.id');
		$b = $this->db->get();
		if ($b->num_rows() > 0) {
			
			foreach(($b->result())  as $bow){
				
				if($bow->is_verify == 0){
					$bank_inactive[] = $bow;
				}
			}
		}
		
		 $this->db->select('u.id as user_id, ud.*')->from('users u')->join('user_document ud', 'ud.is_edit = 1 AND ud.user_id = u.id');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('u.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('u.is_country', $countryCode);
		}
		$this->db->where('u.is_edit', 1)->group_by('u.id');
		$d = $this->db->get();
		
		
		if ($d->num_rows() > 0) {
			
			foreach(($d->result())  as $dow){
				
				if($dow->aadhar_verify == 0){
					$aadhar_inactive[] = $dow;
				}
				if($dow->pancard_verify == 0){
					$pancard_inactive[] = $dow;
				}
				if($dow->license_verify == 0){
					$license_inactive[] = $dow;
				}
				if($dow->police_verify == 0){
					$police_inactive[] = $dow;
				}
				if($dow->loan_verify == 0){
					$loan_inactive[] = $dow;				
				}
			}
		}
		
		$this->db->select('*')->where('is_edit', 1)->where('is_verify', 0);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$t = $this->db->get('taxi');
		if ($t->num_rows() > 0) {
			
			foreach(($t->result())  as $tow){
				
				if($tow->is_verify == 0){
					$taxi_inactive[] = $tow;
				}
			}
		}
		
		$this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.is_edit = 1 AND td.taxi_id = t.id', 'left')->where('t.is_edit', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('t.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('t.is_country', $countryCode);
		}
		$this->db->group_by('t.id');
		$tt = $this->db->get();
		
		//$tt = $this->db->select('*')->where('is_edit', 1)->get('taxi_document');
		if ($tt->num_rows() > 0) {
			
			
			foreach(($tt->result())  as $ttow){
				
				if($ttow->taxation_verify == 0){
					$taxation_inactive[] = $ttow;
				}
				if($ttow->insurance_verify == 0){
					$insurance_inactive[] = $ttow;
				}
				if($ttow->permit_verify == 0){
					$permit_inactive[] = $ttow;
				}
				if($ttow->authorisation_verify == 0){
					$authorisation_inactive[] = $ttow;
				}
				if($ttow->fitness_verify == 0){
					$fitness_inactive[] = $ttow;
				}
				if($ttow->speed_verify == 0){
					$speed_inactive[] = $ttow;
				}
				if($ttow->puc_verify == 0){	
					$puc_inactive[] = $ttow;
				}
			}
		}
		
				
		$data['user'][] = array('title' => 'Vendor', 'inactive' => count($vendor_inactive), 'color' => 'bg-aqua',  'link' => admin_url('verification/vendor'),  'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Driver', 'inactive' => count($driver_inactive), 'color' => 'bg-green', 'link' => admin_url('verification/driver'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Address', 'inactive' => count($address_inactive), 'color' => 'bg-yellow', 'link' => admin_url('verification/address'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Bank Account', 'inactive' => count($bank_inactive), 'color' => 'bg-maroon', 'link' => admin_url('verification/account'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Aadhar Card', 'inactive' => count($aadhar_inactive), 'color' => 'bg-teal', 'link' => admin_url('verification/aadhaar'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Pancard Card', 'inactive' => count($pancard_inactive), 'color' => 'bg-orange', 'link' => admin_url('verification/pancard'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'License', 'inactive' => count($license_inactive), 'color' => 'bg-purple', 'link' => admin_url('verification/license'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Loan Information', 'inactive' => count($loan_inactive), 'color' => 'bg-olive', 'link' => admin_url('verification/loan'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Police', 'inactive' => count($police_inactive), 'color' => 'bg-fuchsia', 'link' => admin_url('verification/police'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Taxi', 'inactive' => count($taxi_inactive), 'color' => 'bg-maroon', 'link' => admin_url('verification/taxi'), 'icon' => 'fa-bar-chart');
		
		$data['user'][] = array('title' => 'Taxiation', 'inactive' => count($taxation_inactive), 'color' => 'bg-black', 'link' => admin_url('verification/taxiation'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Insurance', 'inactive' => count($insurance_inactive), 'color' => 'bg-red', 'link' => admin_url('verification/insurance'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Permit', 'inactive' => count($permit_inactive), 'color' => 'bg-yellow', 'link' => admin_url('verification/permit'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Authorisation', 'inactive' => count($authorisation_inactive), 'color' => 'bg-aqua', 'link' => admin_url('verification/authorisation'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Fitness Information', 'inactive' => count($fitness_inactive), 'color' => 'bg-light-blue', 'link' => admin_url('verification/fitness'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'Speed Information', 'inactive' => count($speed_inactive), 'color' => 'bg-purple', 'link' => admin_url('verification/speed'), 'icon' => 'fa-bar-chart');
		$data['user'][] = array('title' => 'PUC Information', 'inactive' => count($puc_inactive), 'color' => 'bg-teal', 'link' => admin_url('verification/puc'), 'icon' => 'fa-bar-chart');
		
	
		return $data;
	}
	
	function getUserDetails($id){
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token, u.country_code, u.is_country, u.email, u.mobile, u.active, up.is_approved, u.devices_imei, u.first_name, u.last_name, u.photo, u.gender, u.dob, g.name as group_name from {$this->db->dbprefix('users')} as u LEFT JOIN {$this->db->dbprefix('user_profile')} as up ON up.user_id = u.id AND up.is_edit = 1  LEFT JOIN {$this->db->dbprefix('groups')} as g ON g.id = u.group_id  where u.id='".$id."'  ";
		
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
			
			return $row;
		}
		
		return false;	
	}
	
	function getUserAddress($id){
		$image_path = base_url('assets/uploads/');
		$query = "select ud.id as address_id, ud.local_verify, ud.permanent_verify, ud.local_image, ud.local_address, ud.same_address, ud.local_pincode, ud.local_continent_id, ud.local_country_id, ud.local_zone_id, ud.local_state_id, ud.local_city_id, ud.local_area_id, ud.permanent_image, ud.permanent_address, ud.permanent_continent_id, ud.permanent_country_id, ud.permanent_pincode, ud.permanent_zone_id, ud.permanent_state_id, ud.permanent_city_id, ud.permanent_area_id, up.first_name  from {$this->db->dbprefix('user_address')} as ud 
		LEFT JOIN {$this->db->dbprefix('user_profile')}  as up ON up.user_id = ud.user_id AND up.is_edit = 1 
		where ud.user_id='".$id."'  ORDER BY ud.id DESC LIMIT 1  ";
		
		$q = $this->db->query($query);
		
		//print_r($this->db->last_query());die;
		
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->permanent_image !=''){
				$row->permanent_image_img = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image_img = $image_path.'no_image.png';
			}
			if($row->local_image !=''){
				$row->local_image_img = $image_path.$row->local_image;
			}else{
				$row->local_image_img = $image_path.'no_image.png';
			}
			return $row;
		}
		return false;	
	}
	
	function getUserDocument($id){
		$image_path = base_url('assets/uploads/');
		$query = "select ud.id as document_id, ud.aadhaar_no, ud.aadhaar_image, ud.aadhar_verify,  ud.pancard_no, ud.pancard_image, ud.pancard_verify, ud.license_image, ud.license_no, ud.license_dob, ud.license_ward_name, ud.license_type, ud.license_country_id,  ud.license_issuing_authority, ud.license_issued_on, ud.license_validity, ud.license_verify, ud.loan_doc, ud.loan_information, ud.loan_verify, ud.police_image, ud.police_on, ud.police_til, ud.police_verify, up.first_name  from {$this->db->dbprefix('user_document')} as ud 
		LEFT JOIN {$this->db->dbprefix('user_profile')}  as up ON up.user_id = ud.user_id AND up.is_edit = 1 
		where ud.user_id='".$id."'  ORDER BY ud.id DESC LIMIT 1  ";
		
		$q = $this->db->query($query);
		
		//print_r($this->db->last_query());die;
		
		if($q->num_rows()>0){
			$row = $q->row();
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
			
			if($row->loan_doc != ''){
				$row->loan_doc_img = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc_img = $image_path.'no_image.png'	;
			}
			
			return $row;
		}
		return false;	
	}
	
	function getUserBank($id){
		
		$query = "select ud.id as bank_id,  ud.account_holder_name, ud.account_no, ud.is_verify, ud.bank_name, ud.branch_name, ud.ifsc_code,  up.first_name  from {$this->db->dbprefix('user_bank')} as ud 
		LEFT JOIN {$this->db->dbprefix('user_profile')}  as up ON up.user_id = ud.user_id AND up.is_edit = 1 
		where ud.user_id='".$id."'  ORDER BY ud.id DESC LIMIT 1  ";
		
		$q = $this->db->query($query);
		
		//print_r($this->db->last_query());die;
		
		if($q->num_rows()>0){
			$row = $q->row();
			
			return $row;
		}
		return false;	
	}
	
	function getTaxi($id){
		$image_path = base_url('assets/uploads/');
		$query = "select t.name, t.make_id, t.is_daily, t.is_country, t.is_rental, t.is_outstation, t.is_hiring, t.is_corporate, t.model, t.number, t.type, t.is_verify, t.status, t.engine_number, t.chassis_number, t.make, t.fuel_type, t.color, t.manufacture_year, t.capacity, t.photo, up.first_name   from {$this->db->dbprefix('taxi')} as t 
		LEFT JOIN {$this->db->dbprefix('user_profile')}  as up ON  up.is_edit = 1  AND up.user_id = t.driver_id OR up.user_id = t.vendor_id
		where t.id='".$id."'  ORDER BY t.id DESC LIMIT 1  ";
		
		$q = $this->db->query($query);
		
		//print_r($this->db->last_query());die;
		
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
			return $row;
		}
		return  false;	
	}
	
	function getTaxiDocument($id){
		$image_path = base_url('assets/uploads/');
		$query = "select td.id as document_id, td.taxi_id, td.user_id, td.group_id, td.reg_image, td.reg_date, td.reg_due_date, td.reg_owner_name, td.reg_owner_address, td.reg_verify,  td.taxation_image, td.taxation_amount_paid, td.taxation_due_date, td.taxation_verify,  td.insurance_image, td.insurance_policy_no, td.insurance_due_date, td.insurance_verify,  td.permit_image, td.permit_no, td.permit_due_date, td.permit_verify,  td.authorisation_image, td.authorisation_no, td.authorisation_due_date, td.authorisation_verify,  td.fitness_image, td.fitness_due_date, td.fitness_verify, td.speed_image, td.speed_due_date, td.speed_verify,  td.puc_image, td.puc_due_date, td.puc_verify, td.puc_verify  from {$this->db->dbprefix('taxi_document')} as td 
		LEFT JOIN {$this->db->dbprefix('taxi')} as t ON t.id = td.taxi_id 
		LEFT JOIN {$this->db->dbprefix('user_profile')}  as up ON up.user_id = td.user_id AND up.is_edit = 1 
		where td.taxi_id='".$id."'  ORDER BY td.id DESC LIMIT 1  ";
		
		$q = $this->db->query($query);
		
		//print_r($this->db->last_query());die;
		
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->reg_image !=''){
				$row->reg_image_img = $image_path.$row->reg_image;
			}else{
				$row->reg_image_img = $image_path.'no_image.png';
			}
			if($row->taxation_image !=''){
				$row->taxation_image_img = $image_path.$row->taxation_image;
			}else{
				$row->taxation_image_img = $image_path.'no_image.png';
			}
			if($row->insurance_image !=''){
				$row->insurance_image_img = $image_path.$row->insurance_image;
			}else{
				$row->insurance_image_img = $image_path.'no_image.png';
			}
			if($row->permit_image !=''){
				$row->permit_image_img = $image_path.$row->permit_image;
			}else{
				$row->permit_image_img = $image_path.'no_image.png';
			}
			if($row->authorisation_image !=''){
				$row->authorisation_image_img = $image_path.$row->authorisation_image;
			}else{
				$row->authorisation_image_img = $image_path.'no_image.png';
			}
			if($row->fitness_image !=''){
				$row->fitness_image_img = $image_path.$row->fitness_image;
			}else{
				$row->fitness_image_img = $image_path.'no_image.png';
			}
			if($row->speed_image !=''){
				$row->speed_image_img = $image_path.$row->speed_image;
			}else{
				$row->speed_image_img = $image_path.'no_image.png';
			}
			if($row->puc_image !=''){
				$row->puc_image_img = $image_path.$row->puc_image;
			}else{
				$row->puc_image_img = $image_path.'no_image.png';
			}
			
			
			return $row;
		}
		return false;	
	}
	
	function update_taxi_status($id, $data, $status, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$status = array_map(function($v){return (is_null($v)) ? "" : $v;},$status);
		
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('taxi', $data);
		//print_r($this->db->last_query());die;
		if($q){
			$this->db->where('taxi_id', $id);
			$this->db->where('allocated_status', 0);
			$this->db->where('is_withoutvendor', 1);
			$this->db->update('driver_current_status', $status);
			return true;
		}
		return false;
	}
	
	function update_taxi_common_status($id, $data, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('taxi_document', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;
	}
	
	function update_vendor_common($id, $data){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('user_vendor', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;
	}

	
	function update_document_common_status($id, $data, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('user_document', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;
	}
	
	function update_account_status($id, $data, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('user_bank', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;
	}
		
	function update_address_status($id, $data, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('user_address', $data);
		//print_r($this->db->last_query());die;
		if($q){
			return true;
		}
		return false;
	}
	
	function update_vendor_status($id, $data, $udata, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$udata = array_map(function($v){return (is_null($v)) ? "" : $v;},$udata);
		$this->db->where('id', $id);	
		$this->db->where('is_edit', 1);
		$q = $this->db->update('users', $data);
		//print_r($this->db->last_query());die;
		if($q){
			$this->db->update('user_profile', $udata, array('user_id' => $id, 'is_edit' => 1));
			return true;
		}
		return false;
	}
    
	function update_driver_status($id, $data, $udata, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$udata = array_map(function($v){return (is_null($v)) ? "" : $v;},$udata);
		$this->db->where('id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->update('users', $data);
		//print_r($this->db->last_query());die;
		if($q){
			$this->db->update('user_profile', $udata, array('user_id' => $id, 'is_edit' => 1));
			return true;
		}
		return false;
	}
	
}
