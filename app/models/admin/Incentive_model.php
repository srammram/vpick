<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Incentive_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	function getGroup_bycontinent($continent_id){
		$q = $this->db->get_where('incentive_group',array('continent_id'=>$continent_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getGroup_bycountry($country_id){
		$q = $this->db->get_where('incentive_group',array('country_id'=>$country_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getGroup_byzone($zone_id){
		$q = $this->db->get_where('incentive_group',array('zone_id'=>$zone_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getGroup_bystate($state_id){
		$q = $this->db->get_where('incentive_group',array('state_id'=>$state_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getGroup_bycity($city_id){
		$q = $this->db->get_where('incentive_group',array('city_id'=>$city_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getGroup_byarea($city_id){
		$q = $this->db->get_where('incentive_group',array('area_id'=>$area_id));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getAllDrivers($countryCode){
		$this->db->select('id, first_name, mobile')->where('group_id', 4);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
	
		$q = $this->db->get('users');	
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	function add_group($data, $data_user, $countryCode){
		$data['is_country'] = $countryCode;
		$this->db->insert('incentive_group', $data);
		$group_id = $this->db->insert_id();
        if(!empty($group_id)){
			
			foreach($data_user as $user){
				$user['group_id'] = $group_id;
				$user['is_country'] = $countryCode;
				$this->db->insert('incentive_users', $user);
				
			}
			
			return true;	
		}
		return false;
    }
    function update_group($id,$data, $data_user, $countryCode){
				
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('incentive_group',$data)){
			$this->db->delete('incentive_users', array('group_id' => $id));
			
			foreach($data_user as $user){
				$user['group_id'] = $id;
				$user['is_country'] = $countryCode;
				$this->db->insert('incentive_users', $user);
			}
	    	return true;
		}
		return false;
    }
    function getGroupby_ID($id){
		$this->db->select('g.id as id, g.is_country as is_country, g.name as name, g.continent_id, g.country_id, g.zone_id, g.state_id, g.city_id, g.area_id, GROUP_CONCAT(u.user_id) as driver_id');
		$this->db->from('incentive_group g');
		$this->db->join('incentive_users u', 'u.group_id = g.id', 'left');
		$this->db->where('g.id',$id);
		
		$q = $this->db->get();
		
		if($q->num_rows()>0){
			
			return $q->row();
		}
		return false;
    }
	function getALLGroup($countryCode){
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->get('incentive_group');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_group_status($data,$id){
		$this->db->where('id',$id);
		
		if($this->db->update('incentive_group',$data)){
			return true;
		}
		return false;
    }
	

	function add_incentive($data, $data_group, $is_default, $countryCode){
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('is_country', $countryCode);
			$this->db->update('incentive', array('is_default' => 0));
		}
		$data['is_country'] = $countryCode;
		$this->db->insert('incentive', $data);//print_r($this->db->last_query());die;
        if($id = $this->db->insert_id()){
			
			foreach($data_group as $user){
				$user['incentive_id'] = $id;
				$user['is_country'] = $countryCode;
				$this->db->insert('incentive_multiple_group', $user);
				
			}
			
	    	return true;
		}
		return false;
    }
	function update_incentive($id,$data, $data_group, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('incentive',$data)){
			$this->db->delete('incentive_multiple_group', array('incentive_id' => $id));
			
			foreach($data_group as $user){
				$user['incentive_id'] = $id;
				$user['is_country'] = $countryCode;
				$this->db->insert('incentive_multiple_group', $user);
			}
	    	return true;
		}
		return false;
    }
	
	function getIncentiveby_ID($id){
		$this->db->select('d.*, GROUP_CONCAT(img.group_id) as group_id');
		$this->db->from('incentive d');
		$this->db->join('incentive_multiple_group img', 'img.incentive_id = d.id', 'left');
		$this->db->where('d.id',$id);
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function getALLGroups($continent_id, $country_id, $zone_id, $state_id, $city_id, $area_id, $countryCode){
		$this->db->select('*');
		if($continent_id != 0){
			$this->db->where('continent_id', $continent_id);
		}
		if($country_id != 0){
			$this->db->where('country_id', $country_id);
		}
		if($zone_id != 0){
			$this->db->where('zone_id', $zone_id);
		}
		if($state_id != 0){
			$this->db->where('state_id', $state_id);
		}
		if($city_id != 0){
			$this->db->where('city_id', $city_id);
		}
		if($area_id != 0){
			$this->db->where('area_id', $area_id);
		}
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->get('incentive_group');
		if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getALLIncentiveE($countryCode){
		 $this->db
            ->select("in.id as id, in.created_on,  a.name as area_name, c.name as city_name, s.name as state_name, cc.name as country_name, 
			(CASE WHEN in.type = '1' THEN  'Fare' WHEN in.type = '2' THEN   'Ride'  WHEN in.type = '3' THEN 'Fare And Ride' ELSE '' END) as type,
			
			(CASE WHEN in.type = '1' THEN  in.target_fare WHEN in.type = '2' THEN   in.target_ride  WHEN in.type = '3' THEN CONCAT(in.target_fare, ' AND ', in.target_ride) ELSE '' END) as target_type,
			
			 (CASE WHEN in.date_type = '1' THEN  'Dates'  ELSE 'Days' END) as date_type,
			 
			 (CASE WHEN in.date_type = '1' THEN  CONCAT(in.start_date, ' TO ', in.end_date)  ELSE in.days END) as incentive_day_dates, in.start_time, in.end_time,  
			 
			 (CASE WHEN in.fare_type = '1' THEN  'Percentage'  ELSE 'Fixed' END) as fare_type, 
			  in.fare_amount
			
			")
           
			
			 ->from("incentive in")
			->join("areas a", "a.id = in.area_id", "left")
			->join("cities  c", "c.id = in.city_id", 'left')
			->join("states s", "s.id = c.state_id", 'left')
			->join("zones z", "z.id = s.zone_id", 'left')
			->join("countries cc", "cc.id = z.country_id", 'left');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
			//->where("df.is_delete !=", 0);
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
	
}
