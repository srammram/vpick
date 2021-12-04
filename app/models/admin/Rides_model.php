<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rides_model extends CI_Model
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
	
	function getALLRides($countryCode){
		$this->db
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no,   t.number, cu.first_name as customer_name,  cu.mobile as customer_mobile, u.first_name as driver_name,  u.mobile as driver_mobile,  {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.end, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.status ")
            ->from("rides")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1 ', 'left')
			->join('users u','u.id=rides.driver_id AND u.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
            ->join('taxi t','t.id=rides.taxi_id AND t.is_edit=1 ', 'left');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('rides.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('rides.is_country', $countryCode);
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
	function getRides($id){
		
		$this->db->select('r.*, rp.driver_allowance, rp.id as ride_payment_id, rp.discount_fare, rp.total_distance, rp.waiting_charge, rp.outstanding_from_last_trip, rp.total_night_halt, rp.total_toll, rp.total_parking, rp.total_distance, rp.pickup_waiting_min, rp.trafic_waiting_min, rp.total_fare, rp.extra_fare, mr.overall, mr.drive_comfort_star, mr.booking_process_star, mr.cab_cleanliness_star, mr.drive_politeness_star, mr.fare_star, mr.easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, d.first_name as dfname, d.last_name as dlname, d.country_code as dccode, d.mobile as dmobile, d.photo as dphoto, t.photo as tphoto, t.number, t.color, t.type_name, v.mobile as vmobile, v.country_code as vccode, vp.first_name as vfname, vp.last_name as vlname, dcs.current_latitude as driver_latitude, dcs.current_longitude as  driver_longitude, df.location');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id ', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id ', 'left');
		
		$this->db->join('users v', 'v.id = r.vendor_id ', 'left');
		$this->db->join('user_profile vp', 'vp.user_id = r.vendor_id ', 'left');
		
		$this->db->join('users d', 'd.id = r.driver_id ', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id ', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id ', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id ', 'left');
		$this->db->join('driver_frequency df', 'df.ride_id = r.id', 'left');
		$this->db->join('taxi t','t.id=r.taxi_id AND t.is_edit=1 ', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id ', 'left');
		
		$this->db->where(array('r.id'=>$id));
		
		$q = $this->db->get();//print_r($this->db->error());exit;
		
       	if($q->num_rows()>0){
			
			$row = $q->row();
			$location = explode(',', $row->location);
			//print_r($location);
			if(!empty($location)){
				$count=1;
			
				foreach ($location as $k => $v) {
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
					$lat[] = array('lat' => $d1[$i], 'lng' => $d2[$i]);			
				}
			}
			
			/*if(!empty($location)){
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
			}*/
			
			
			
			
			//$row->location_loc = json_encode($loc, JSON_NUMERIC_CHECK);
			$row->location = json_encode($lat, JSON_NUMERIC_CHECK);
			//echo '<pre>';
			//print_r($row->location);
			//die;
			
			return $row;
		}
		return false;
	}
	
    function assign_driver($data,$id, $countryCode){
	$this->db->where('id',$id);
	$this->db->where('is_country', $countryCode);
	if($this->db->update('rides',$data)){
	    return true;
	}
	return false;
    }
	
	
	
}
