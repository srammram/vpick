<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Locations_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
   
   	/*### Daily*/
	function add_daily($data, $slot_array, $area_id, $is_default, $taxi_type, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('taxi_type', $taxi_type);
			$this->db->where('is_country', $countryCode);
			$this->db->update('daily_fare', array('is_default' => 0));
		}
		if(!empty($data)){
			if(!empty($area_id)){
				$q = $this->db->select('id')->where_in('area_id', $area_id)->get('daily_fare');
				if($q->num_rows()>0){
					foreach (($q->result()) as $row) {
						$this->db->delete('daily_fare', array('id' => $row->id));
						$this->db->delete('daily_slot', array('daily_fare_id' => $row->id));
					}
				}
			}
			foreach($data as $row){
				$this->db->insert('daily_fare', $row);//print_r($this->db->last_query());die;
				if($id = $this->db->insert_id()){
					if(!empty($slot_array)){
						foreach($slot_array as $slot){
							$slot['daily_fare_id'] = $id;
							$this->db->insert('daily_slot', $slot);
							//print_r($this->db->last_query());die;
						}
					}
				}
			}
			return true;
		}
		return false;
    }
	function update_daily($id,$data, $slot_array, $countryCode){
		//print_r($data);
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		//$this->db->update('daily_fare',$data[0]);
		//print_r($this->db->last_query());die;
		
		if($this->db->update('daily_fare',$data[0])){
			
			
			$this->db->delete('daily_slot', array('daily_fare_id' => $id));
			if(!empty($slot_array)){
				foreach($slot_array as $slot){
					$slot['daily_fare_id'] = $id;
					$slot['is_country'] = $countryCode;
					$this->db->insert('daily_slot', $slot);
					
				}
			}
	    	return true;
		}
		return false;
    }
	
	function getPeekfare($id, $countryCode){
		$this->db->select('*')->where('daily_fare_id', $id)->where('type', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('daily_slot');
		
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					$data[] = $row;
				}
				return $data;
			}
		
		return false;	
	}
	function getNightfare($id, $countryCode){
		 $this->db->select('*')->where('daily_fare_id', $id)->where('type', 2);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('daily_slot');
		
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					$data[] = $row;
				}
				return $data;
			}
		
		return false;	
	}
	
    function getDailyby_ID($id){
		$this->db->select('d.*, tt.name as type_name, c.name as city_name, c.state_id, s.zone_id, z.country_id, cc.continent_id');
		$this->db->from('daily_fare d');
		$this->db->join('taxi_type tt', 'tt.id = d.taxi_type ', 'left');
		$this->db->join('cities c', 'c.id = d.city_id ', 'left');
		$this->db->join('states s', 's.id = c.state_id ', 'left');
		$this->db->join('zones z', 'z.id = s.zone_id ', 'left');
		$this->db->join('countries cc', 'cc.id = z.country_id', 'left');
		$this->db->where('d.id',$id);
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function checkTypewiseCityDaily($taxi_type, $area_id, $countryCode){
		$this->db->select('*');
		$this->db->from('daily_fare');
		$this->db->where('area_id',$area_id);
		$this->db->where('taxi_type',$taxi_type);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	
	function getALLDaily($countryCode){
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('daily_fare');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_daily_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('daily_fare',$data)){
			return true;
		}
		return false;
    }
	
	/*### Rental*/
	
	
	function add_rental($data, $slot_array, $area_id, $is_default, $taxi_type, $countryCode){
		
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('taxi_type', $taxi_type);
			$this->db->where('is_country', $countryCode);
			$this->db->update('rental_fare', array('is_default' => 0));
		}
		if(!empty($data)){
			if(!empty($area_id)){
				$q = $this->db->select('id')->where_in('area_id', $area_id)->get('rental_fare');
				if($q->num_rows()>0){
					foreach (($q->result()) as $row) {
						$this->db->delete('rental_fare', array('id' => $row->id));
						$this->db->delete('rental_slot', array('rental_fare_id' => $row->id));
					}
				}
			}
			foreach($data as $row){
				$this->db->insert('rental_fare', $row);//print_r($this->db->last_query());die;
				if($id = $this->db->insert_id()){
					if(!empty($slot_array)){
						foreach($slot_array as $slot){
							$slot['rental_fare_id'] = $id;
							$this->db->insert('rental_slot', $slot);
							//print_r($this->db->last_query());die;
						}
					}
				}
			}
			return true;
		}
		return false;
    }
	
	function update_rental($id,$data, $slot_array, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('rental_fare',$data)){
			
			$this->db->delete('rental_slot', array('rental_fare_id' => $id));
			if(!empty($slot_array)){
				foreach($slot_array as $slot){
					$slot['rental_fare_id'] = $id;
					$slot['is_country'] = $countryCode;
					$this->db->insert('rental_slot', $slot);
				}
			}
	    	return true;
		}
		return false;
    }
	
	
    function getRentalby_ID($id){
		$this->db->select('r.*, tt.name as type_name, c.name as city_name, c.state_id, s.zone_id, z.country_id, cc.continent_id');
		$this->db->from('rental_fare r');
		$this->db->join('taxi_type tt', 'tt.id = r.taxi_type ', 'left');
		$this->db->join('cities c', 'c.id = r.city_id ', 'left');
		$this->db->join('states s', 's.id = c.state_id ', 'left');
		$this->db->join('zones z', 'z.id = s.zone_id ', 'left');
		$this->db->join('countries cc', 'cc.id = z.country_id ', 'left');
		$this->db->where('r.id',$id);
		

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function checkTypewiseCityRental($taxi_type, $area_id, $package_name, $countryCode){
		$this->db->select('*');
		$this->db->from('rental_fare');
		$this->db->where('area_id',$area_id);
		$this->db->where('taxi_type',$taxi_type);
		$this->db->where('package_name',$package_name);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	function getRentalPeekfare($id, $countryCode){
		$this->db->select('*')->where('rental_fare_id', $id)->where('type', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('rental_slot');
		
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					$data[] = $row;
				}
				return $data;
			}
		
		return false;	
	}
	function getRentalNightfare($id, $countryCode){
		 $this->db->select('*')->where('rental_fare_id', $id)->where('type', 2);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('rental_slot');
		
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					$data[] = $row;
				}
				return $data;
			}
		
		return false;	
	}
	
	
	function getALLRental($countryCode){
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('rental_fare');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_rental_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('rental_fare',$data)){
			return true;
		}
		return false;
    }
	
	/*### Outstation*/
	function add_outstation($data, $slot_array, $is_default, $taxi_type, $countryCode){
		if($is_default == 1){
			$this->db->where('is_default', $is_default);
			$this->db->where('taxi_type', $taxi_type);
			$this->db->where('is_country', $countryCode);
			$this->db->update('outstation_fare', array('is_default' => 0));
		}
		$data['is_country'] = $countryCode;
		$this->db->insert('outstation_fare', $data);
		//print_r($this->db->last_query());die;
        if($id = $this->db->insert_id()){
			if(!empty($slot_array)){
				foreach($slot_array as $slot){
					$slot['outstation_fare_id'] = $id;
					$this->db->insert('outstation_slot', $slot);
					//print_r($this->db->last_query());die;
				}
			}
	    	return true;
		}
		return false;
    }
	function getOutstationPeekfare($id, $countryCode){
		$this->db->select('*')->where('outstation_fare_id', $id)->where('type', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('outstation_slot');
		
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					$data[] = $row;
				}
				return $data;
			}
		
		return false;	
	}
	
	function update_outstation($id,$data, $slot_array, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('outstation_fare',$data)){
			$this->db->delete('outstation_slot', array('outstation_fare_id' => $id));
			if(!empty($slot_array)){
				foreach($slot_array as $slot){
					$slot['outstation_fare_id'] = $id;
					$this->db->insert('outstation_slot', $slot);
				}
			}
	    	return true;
		}
		return false;
    }
    function getOutstationby_ID($id){
		$this->db->select('o.*, tt.name as type_name, lc.name as from_city_name,  lc.state_id as local_state_id, ls.zone_id as local_zone_id, lz.country_id as local_country_id, lcc.continent_id as local_continent_id, pc.name as to_city_name, pc.state_id as permanent_state_id, ps.zone_id as permanent_zone_id, pz.country_id as permanent_country_id, pcc.continent_id as permanent_continent_id');
		$this->db->from('outstation_fare o');
		$this->db->join('taxi_type tt', 'tt.id = o.taxi_type ', 'left');
		
		$this->db->join('cities lc', 'lc.id = o.from_city_id ', 'left');
		$this->db->join('states ls', 'ls.id = lc.state_id ', 'left');
		$this->db->join('zones lz', 'lz.id = ls.zone_id ', 'left');
		$this->db->join('countries lcc', 'lcc.id = lz.country_id ', 'left');
		
		$this->db->join('cities pc', 'pc.id = o.to_city_id ', 'left');
		$this->db->join('states ps', 'ps.id = pc.state_id ', 'left');
		$this->db->join('zones pz', 'pz.id = ps.zone_id ', 'left');
		$this->db->join('countries pcc', 'pcc.id = pz.country_id ', 'left');
		
		$this->db->where('o.id',$id);
		
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	function checkTypewiseCityOutstation($taxi_type, $from_city_id, $from_city_id, $countryCode){
		$this->db->select('*');
		$this->db->from('outstation_fare');
		$this->db->where('from_city_id',$from_city_id);
		$this->db->where('to_city_id',$to_city_id);
		$this->db->where('taxi_type',$taxi_type);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	
	function checkTypewiseCityOutstationFixed($taxi_type, $from_city_id, $from_city_id, $package_name, $type, $countryCode){
		$this->db->select('*');
		$this->db->from('outstation_fare');
		$this->db->where('from_city_id',$from_city_id);
		$this->db->where('to_city_id',$to_city_id);
		$this->db->where('package_name',$package_name);
		$this->db->where('type',$type);
		$this->db->where('taxi_type',$taxi_type);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	
	function checkTypewiseCityOutstationVariable($taxi_type, $from_city_id, $from_city_id, $package_name, $type, $countryCode){
		$this->db->select('*');
		$this->db->from('outstation_fare');
		$this->db->where('from_city_id',$from_city_id);
		$this->db->where('to_city_id',$to_city_id);
		$this->db->where('taxi_type',$taxi_type);
		$this->db->where('package_name',$package_name);
		$this->db->where('type',$type);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;
	}
	
	function getALLOutstation($countryCode){
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('outstation_fare');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
    function update_outstation_status($data,$id, $countryCode){
		$this->db->where('id',$id);
		$this->db->where('is_country', $countryCode);
		if($this->db->update('outstation_fare',$data)){
			return true;
		}
		return false;
    }
	
	function getALLDailyE($countryCode){
		 $this->db
            ->select("df.id as id, tt.name as taxi_type_name, c.name as city_name, s.name as state_name, cc.name as country_name, df.base_min_distance_price, df.base_per_distance_price, df.status as status, df.is_default as is_default")
            ->from("daily_fare df")
			
			->join("cities  c", "c.id = df.city_id ", 'left')
			->join("taxi_type  tt", "tt.id = df.taxi_type ")
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left')
			->where("df.is_default !=", 2);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('df.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('df.is_country', $countryCode);
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
	
	function getALLRentalE($countryCode){
		$this->db
            ->select("rf.id as id, tt.name as taxi_type,  c.name as city_name, s.name as state_name, cc.name as country_name,  rf.package_name,   rf.package_price, rf.status as status, rf.is_default as is_default")
            ->from("rental_fare rf")
			
			->join("cities  c", "c.id = rf.city_id ", 'left')
			->join("taxi_type  tt", "tt.id = rf.taxi_type ")
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left')
			->where("rf.is_default !=", 2);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('rf.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('rf.is_country', $countryCode);
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
	
	function getALLOutstationE($countryCode){
		 $this->db
            ->select("of.id as id, tt.name as taxi_type, c.name as city_name, s.name as state_name, cc.name as country_name,  of.package_name,   of.status as status, of.is_default as is_default")
            ->from("outstation_fare of")
			
			->join("cities  c", "c.id = of.from_city_id ", 'left')
			->join("taxi_type  tt", "tt.id = of.taxi_type  ")
			->join("states s", "s.id = c.state_id  ", 'left')
			->join("zones z", "z.id = s.zone_id  ", 'left')
			->join("countries cc", "cc.id = z.country_id  ", 'left')
			->where("of.is_default !=", 2);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('of.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('of.is_country', $countryCode);
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
	
	function getALLZonebyCountry($countryCode){
		$this->db->select('z.id as id, z.name as name');
		$this->db->from('countries c');
		$this->db->join('zones  z', 'z.country_id = c.id');
		$this->db->where('c.iso', $countryCode);
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
