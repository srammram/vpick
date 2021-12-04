<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usersrides_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function getRideDetails($id){
	$this->db->select('*,r.id id,tt.id taxi_type_id');
	$this->db->from('rides r');
	$this->db->join('ride_route Ro','Ro.ride_id=r.id','left');
	$this->db->join('taxi t','t.id=r.taxi_id');
	$this->db->join('taxi_type tt','tt.id=t.type');
	$this->db->where(array('r.id'=>$id));
	$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
	
	function getRides($id, $countryCode){
		$this->db->select('r.*, rp.driver_allowance, rp.total_night_halt, rp.total_toll, rp.total_parking, rp.total_distance, rp.total_fare, rp.extra_fare, mr.overall, mr.drive_comfort_star, mr.booking_process_star, mr.cab_cleanliness_star, mr.drive_politeness_star, mr.fare_star, mr.easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, d.first_name as dfname, d.last_name as dlname, d.country_code as dccode, d.mobile as dmobile, v.mobile as vmobile, v.country_code as vccode, vp.first_name as vfname, vp.last_name as vlname, dcs.current_latitude as driver_latitude, dcs.current_longitude as  driver_longitude');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id ', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id ', 'left');
		
		$this->db->join('users v', 'v.id = r.vendor_id ', 'left');
		$this->db->join('user_profile vp', 'vp.user_id = r.vendor_id ', 'left');
		
		$this->db->join('users d', 'd.id = r.driver_id ', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id ', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id ', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id ', 'left');
		
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id', 'left');
		
		$this->db->where(array('r.id'=>$id));
		
		$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
    function assign_driver($data,$id){
	$this->db->where('id',$id);
	$this->db->where('is_country', $countryCode);
	if($this->db->update('rides',$data)){
	    return true;
	}
	return false;
    }
	
	
	
}
