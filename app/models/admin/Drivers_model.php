<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Drivers_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function add_driver($driver, $taxi){
		$this->db->insert('drivers', $driver);
		$driver_id = $this->db->insert_id();
		
		if(!empty($driver_id)){
			$taxi['driver'] = $driver_id;
			$this->db->insert('taxi', $taxi);	
			$taxi_id = $this->db->insert_id;
			if(!empty($taxi_id)){
				//$this->db->insert('driver_current_status', array('driver_id' => $driver_id, 'taxi_id' => $taxi_id));
				$this->db->insert('driver_current_status', array('driver_id' => $driver_id, 'taxi_id' => $taxi_id, 'is_allocated' => 1, 'allocated_start_date' => date('Y-m-d H:is'), 'allocated_status' => 1));
			}
			return $driver_id;		
		}
		return  FALSE;
		
    }
    function update_driver($id,$driver, $taxi){
	$this->db->where('id',$id);
	if($this->db->update('drivers',$driver)){
		$this->db->update('taxi', $taxi, array('driver' => $id));
	    return true;
	}
	return false;
    }
    function getDriverby_ID($id){
	
	$this->db->select('d.*,co.id country_id,c.id city_id,s.id state_id');
	$this->db->from('drivers d');
	$this->db->join('cities c','d.city=c.id');
	$this->db->join('states s','c.state_id=s.id');
	$this->db->join('countries co','s.country_id=co.id');
	$this->db->where('d.id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    $data = $q->row();
	    $data->states = $this->site->getStates_bycountry($data->country_id);
	    $data->cities = $this->site->getcities_byStates($data->city_id);
	    //print_R($data);exit;
	    return $data;
	}
	return false;
	
    }
	
	function getDriverswithTaxi($id){
		$this->db->select('d.*, t.name as taxi_name, t.photo as taxi_photo, t.insurance, t.taxpaid, t.rcbook,  t.model, t.color, t.number, t.type, t.manufacture_year,  co.id country_id, co.name as country_name, c.id city_id, c.name as city_name, s.id state_id, s.name as state_name, tt.name as type_name');
		$this->db->from('drivers d');
		$this->db->join('cities c','d.city=c.id', 'left');
		$this->db->join('states s','c.state_id=s.id', 'left');
		$this->db->join('countries co','s.country_id=co.id', 'left');
		$this->db->join('taxi t', 't.driver = d.id');
		$this->db->join('taxi_type tt', 'tt.id = t.type');
		$this->db->where('d.id',$id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$data = $q->row();
			//$data->states = $this->site->getStates_bycountry($data->country_id);
			//$data->cities = $this->site->getcities_byStates($data->city_id);
			return $data;
		}
		return false;
	}
	
	function getDriverswithTaxiedit($id){
		$this->db->select('d.*, t.name as taxi_name, t.photo as taxi_photo, t.insurance, t.taxpaid, t.rcbook,  t.model, t.color, t.number, t.type, t.manufacture_year,  co.id country_id, co.name as country_name, c.id city_id, c.name as city_name, s.id state_id, s.name as state_name, tt.name as type_name');
		$this->db->from('drivers d');
		$this->db->join('cities c','d.city=c.id', 'left');
		$this->db->join('states s','c.state_id=s.id', 'left');
		$this->db->join('countries co','s.country_id=co.id', 'left');
		$this->db->join('taxi t', 't.driver = d.id');
		$this->db->join('taxi_type tt', 'tt.id = t.type');
		$this->db->where('d.id',$id);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
			$data = $q->row();
			$data->states = $this->site->getStates_bycountry($data->country_id);
			$data->cities = $this->site->getcities_byStates($data->city_id);
			return $data;
		}
		return false;
	}
	
	function approved_driver($id,$row){
		$this->db->where('id', $id);
		$q = $this->db->update('drivers', $row);
		if($q){
			$this->db->update('taxi', array('status' => 1), array('driver' => $id));
			return true;	
		}
		return false;
	}
	function insertNotification($data){
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s') ));
		if($q){
			
			return true;	
		}
		return false;	
	}
    function update_driver_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('drivers',$data)){
	    return true;
	}
	return false;
    }
}
