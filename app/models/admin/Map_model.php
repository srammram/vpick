<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Map_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function getDrivers_radius($data){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$this->db
	    ->select("d.id,d.created_on date_created,d.first_name title,d.photo,current_latitude latitude,current_longitude longitude,mode")
            ->from("drivers d");
	    $this->db->where('current_latitude BETWEEN "'. $data['minlat']. '" and "'. $data['maxlat'].'"');
	    $this->db->where('current_longitude BETWEEN "'. $data['minlng']. '" and "'. $data['maxlng'].'"');
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
	   // print_R($data);exit;
	    return $data;
	}
	return false;
    }
	
	
}
