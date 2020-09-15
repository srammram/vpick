
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Heatmap_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	function getTracking($data){
		
		$query = "SELECT  d.id, d.first_name, d.last_name, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude as current_latitude, dcs.current_longitude as current_longitude, dcs.mode, up.first_name, up.last_name, up.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, tt.image, tt.image_hover, tt.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id 
	 
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE dcs.mode != 0 AND dcs.is_connected = 1  AND dcs.allocated_status = 1   HAVING distance <= 2000 
ORDER BY distance ASC";
	
		
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			$r = $q->result();
			foreach($r as $row){
				
				$val[] = $row;
			}
			return $val;
		}
	return false;
    }
	
	function getSearch($data){
		
		$query = "SELECT  d.id, d.latitude as current_latitude, d.longitude as current_longitude,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( d.latitude ) ) * cos( radians( d.longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( d.latitude ) ) ) ) AS distance 
		FROM {$this->db->dbprefix('seach_location')}  AS d 
	
	
	   HAVING distance <= 2000 
ORDER BY distance ASC";
	
		
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			$r = $q->result();
			foreach($r as $row){
				
				$val[] = $row;
			}
			
			return $val;
		}
	return false;
    }
	
	
    function getMostavailable_taxis($data){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$this->db
	    ->select("d.id,d.created_on date_created,d.first_name title,d.photo,current_latitude latitude,current_longitude longitude,mode")
            ->from("drivers d");
	    //$this->db->where('current_latitude BETWEEN "'. $data['minlat']. '" and "'. $data['maxlat'].'"');
	    //$this->db->where('current_longitude BETWEEN "'. $data['minlng']. '" and "'. $data['maxlng'].'"');
	    $this->db->where('d.mode','available');
	$q=$this->db->get();//print_R($q->result());exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		$row->gallery = [];
		if($row->photo !=''){
		    $row->gallery[] = $image_path.$row->photo;
		}
		
		if($row->mode=="available"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_available_hover.png';
		}else if($row->mode=="on ride"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_on_ride_hover.png';
		}else if($row->mode=="offline"){
		    $row->type_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline.png';
		    $row->type_hover_icon = base_url('themes/default/admin/assets/').'images/map/taxi_offline_hover.png';
		}
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
    function getMostBooking_locations($data, $countryCode){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$this->db
	    ->select("*")
            ->from("rides");
	    $this->db->where_in('status',array('booked','completed','onride'));
		$this->db->where('is_country', $countryCode);
	    //$this->db->order_by("field(status, 'General Manager', 'Owner', 'President')");
	  // echo $this->db->get_compiled_select();
	$q=$this->db->get();//echo $this->db->get_compiled_select();exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
    function getMostRentalBooking_locations($data, $countryCode){
	$this->db
	    ->select("*")
            ->from("rides");
	    $this->db->where('booked_type','rental');
	    $this->db->where_in('status',array('cancelled'));
		$this->db->where('is_country', $countryCode);
	    //$this->db->order_by("field(status, 'General Manager', 'Owner', 'President')");
	  // echo $this->db->get_compiled_select();
	$q=$this->db->get();//echo $this->db->get_compiled_select();exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
    function getMostTraffic_locations($data, $countryCode){
	$this->db
	    ->select("*")
            ->from("rides");
			$this->db->where('is_country', $countryCode);
	    //$this->db->where_in('status',array('cancelled'));
	    //$this->db->order_by("field(status, 'General Manager', 'Owner', 'President')");
	  // echo $this->db->get_compiled_select();
	$q=$this->db->get();//echo $this->db->get_compiled_select();exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
    function getMostBookingRequests_withnotaxi_locations($data, $countryCode){
	$this->db
	    ->select("*")
            ->from("rides");
	    $this->db->where_in('status',array('booked'));
		$this->db->where('is_country', $countryCode);
	$q=$this->db->get();//echo $this->db->get_compiled_select();exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
    function getMostbooking_cancellation_locations($data, $countryCode){
	$this->db
	    ->select("*")
            ->from("rides");
	    $this->db->where_in('status',array('cancelled'));
		$this->db->where('is_country', $countryCode);
	    //$this->db->order_by("field(status, 'General Manager', 'Owner', 'President')");
	  // echo $this->db->get_compiled_select();
	$q=$this->db->get();//echo $this->db->get_compiled_select();exit;
	if($q->num_rows()>0){
	    $data = $q->result();
	    foreach($data as $k => $row){
		
		$data[$k] = $row;
	    }
	    ///print_R($data);exit;
	    return $data;
	}
	return false;
    }
	
}
