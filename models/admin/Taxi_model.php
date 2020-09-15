<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Taxi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	function getTaxinameBYID($id, $countryCode){
		$q = $this->db->select('name')->where('is_country', $countryCode)->where('id', $id)->get('taxi_make');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaximodelBYID($id, $countryCode){
		$q = $this->db->select('name')->where('id', $id)->get('taxi_model');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaxitypeBYID($id, $countryCode){
		$q = $this->db->select('name')->where('is_country', $countryCode)->where('id', $id)->get('taxi_type');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	
	function getModelbymake_type($make_id, $type_id, $countryCode){
		$q = $this->db->select('id, name')->where('is_country', $countryCode)->where('type_id', $type_id)->where('make_id', $make_id)->get('taxi_model');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxi($countryCode){
		$q = $this->db->select('id, name')->where('is_country', $countryCode)->get('taxi_make');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxitype($countryCode){
		$q = $this->db->select('id, name')->where('is_country', $countryCode)->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	public function updateSetting($data, $countryCode)
    {
        $this->db->where('setting_id', '1');
		$this->db->where('is_country', $countryCode);
        if ($this->db->update('settings', $data)) {
            return true;
        }
        return false;
    }
	
	public function getUservalues($user_id, $countryCode) {
		
		$this->db->select('u.id, u.first_name, u.country_code, u.mobile, u.email');
		$this->db->from('users u');
		$this->db->where('u.id', $user_id);
		$this->db->where('u.is_country', $countryCode);
		$q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	/*### Taxi*/
	function add_taxi($taxi, $taxi_document, $countryCode){
		$taxi['is_country'] = $countryCode;
		$this->db->insert('taxi', $taxi);
		
        if($id = $this->db->insert_id()){
			$code = '4'.str_pad($id, 9, 0, STR_PAD_LEFT);
			$this->db->update('taxi', array('code' => $code), array('id' => $id, 'is_country' => $countryCode));
			$taxi_document['taxi_id'] = $id;
			$taxi_document['is_country'] = $countryCode;
			$this->db->insert('taxi_document', $taxi_document);
			//print_r($this->db->last_query());die;
			
	    	return $id;
		}
		return false;
    }
	
	function update_status($data, $id, $countryCode){
		$data = array_map(function($v){return (is_null($v)) ? "" : $v;},$data);
		$this->db->where('id', $id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('taxi', $data);
		if($q){
			return true;
		}
		return false;
	}
	
	function edit_taxi($taxi, $taxi_document, $id, $countryCode){
		$taxi = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi);
		$taxi_document = array_map(function($v){return (is_null($v)) ? "" : $v;},$taxi_document);
		
		$taxi['is_country'] = $countryCode;
		if($id){
			//$this->db->update('taxi', array('is_edit' => 0), array('id' => $id, 'is_edit' => 1));
			$this->db->update('taxi_document', array('is_edit' => 0), array('taxi_id' => $id, 'is_edit' => 1));
			$this->db->update('taxi', $taxi, array('id' => $id));
			
		}
		//$taxi['is_country'] = $countryCode;
		//$this->db->insert('taxi', $taxi);
		//print_r($this->db->last_query());
       if($id){
			$taxi_document['taxi_id'] = $id;
			$taxi_document['is_country'] = $countryCode;
			$this->db->insert('taxi_document', $taxi_document);
			
	    	return true;
		}
		return false;
    }
	
	function getTaxiData($id, $countryCode){
		$image_path = base_url('assets/uploads/');
		$this->db->select('t.id as taxi_id, t.driver_id, t.is_country as is_country, t.vendor_id, t.is_daily, t.is_rental, t.is_outstation, t.is_hiring, t.is_corporate, t.vendor_id, t.name, t.model, t.number, t.type, t.engine_number, t.chassis_number, t.make, tf.name as fuel_type, t.color, t.manufacture_year, t.capacity, t.photo, t.mode, t.is_verify, td.reg_image, td.reg_date, td.reg_due_date, td.reg_owner_name, td.reg_owner_address, td.reg_verify, td.taxation_image, td.taxation_amount_paid, td.taxation_due_date, td.taxation_verify, td.insurance_image, td.insurance_policy_no, td.insurance_due_date, td.insurance_verify, td.permit_image, td.permit_no, td.permit_due_date, td.permit_verify, td.authorisation_image, td.authorisation_no, td.authorisation_due_date, td.authorisation_verify, td.fitness_image, td.fitness_due_date, td.fitness_verify, td.speed_image, td.speed_due_date, td.speed_verify, td.puc_image, td.puc_due_date, td.puc_verify, up.first_name, up.last_name, u.mobile, u.country_code, u.email, up.gender, tt.name as type_name ');
		$this->db->from('taxi t');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('taxi_fuel tf', 'tf.id = t.fuel_type', 'left');
		$this->db->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left');
		$this->db->join('users u', '(u.id = t.driver_id OR u.id = vendor_id) AND u.is_edit = 1', 'left');
		$this->db->join('user_profile up', ' (up.user_id = t.driver_id OR up.user_id = vendor_id) AND   up.is_edit = 1', 'left');
		$this->db->where('t.id', $id);
		$this->db->where('t.is_edit', 1);
		$this->db->group_by('t.id');
		$q = $this->db->get();
		//print_r($this->db->error());exit;
		if ($q->num_rows() > 0) {
			
			$row = $q->row();
			
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
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
				$row->authorisation_image = $image_path.$row->authorisation_image;
			}else{
				$row->authorisation_image = $image_path.'no_image.png';
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
        return FALSE;	
	}
	
	function getTaxiDataedit($id, $countryCode){
		$image_path = base_url('assets/uploads/');
		$this->db->select('t.id as id, t.make_id, t.driver_id, t.is_country as is_country, t.vendor_id, t.is_daily, t.is_rental, t.is_outstation, t.is_hiring, t.is_corporate, t.code, t.name, t.model, t.number, t.type, t.engine_number,  t.chassis_number, t.make, t.fuel_type, tf.name as fuel_type_name, t.color, t.manufacture_year, t.capacity, t.is_corporate, t.is_hiring, t.is_outstation, t.is_rental, t.is_daily, t.created_by, t.created_on, t.ac, t.photo, t.status, t.mode, t.is_verify, t.approved_by, t.approved_on, td.taxi_id,   td.reg_image, td.reg_date, td.reg_due_date, td.reg_owner_name, td.reg_owner_address, td.reg_verify, td.reg_approved_by, td.reg_approved_on, td.taxation_image, td.taxation_amount_paid, td.taxation_due_date, td.taxation_verify, td.taxation_approved_by, td.taxation_approved_on, td.insurance_image, td.insurance_policy_no, td.insurance_due_date, td.insurance_verify, td.insurance_approved_by, td.insurance_approved_on, td.permit_image, td.permit_no, td.permit_due_date, td.permit_verify, td.permit_approved_by, td.permit_approved_on, td.authorisation_image, td.authorisation_no, td.authorisation_due_date, td.authorisation_verify, td.authorisation_approved_by, td.authorisation_approved_on, td.fitness_image, td.fitness_due_date, td.fitness_verify, td.fitness_approved_by, td.fitness_approved_on, td.speed_image, td.speed_due_date, td.speed_verify, td.speed_approved_by, td.speed_approved_on, td.puc_image, td.puc_due_date, td.puc_verify, td.puc_approved_by, td.puc_approved_on');
		$this->db->from('taxi t');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('taxi_fuel tf', 'tf.id = t.fuel_type', 'left');
		$this->db->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left');
		$this->db->where('t.id', $id);
		$this->db->where('t.is_edit', 1);
		$q = $this->db->get();

		if ($q->num_rows() > 0) {
			
			$row = $q->row();
			
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
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
        return FALSE;	
	}
	
	function getALLTaxi($countryCode){
		   $this->db->select("{$this->db->dbprefix('taxi')}.id as id, up.first_name, {$this->db->dbprefix('taxi')}.name as taxi_name, {$this->db->dbprefix('taxi')}.number, {$this->db->dbprefix('taxi')}.model,   tf.name as fuel_type, tt.name as type_name,   If({$this->db->dbprefix('taxi')}.is_verify = 1 && td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1, '1', '0') as status ")
            ->from("taxi")
			->join("taxi_type tt", 'tt.id = taxi.type', 'left')
			->join("taxi_fuel tf", 'tf.id = taxi.fuel_type', 'left')
			->join("users up", "up.id = taxi.driver_id AND up.is_edit = 1", 'left')
			->join("taxi_document td", "td.taxi_id = taxi.id AND td.is_edit = 1", 'left')
			->where("taxi.is_edit", 1);
			$this->db->where('taxi.is_country', $countryCode);
			$this->db->group_by("taxi.id");
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
