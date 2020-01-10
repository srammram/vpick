<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operators_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function add_operator($data){
	$this->db->insert('operators', $data);//print_R($this->db->error());
        return $this->db->insert_id();	
    }
    function update_operator($id,$data){
	$this->db->where('id',$id);
	if($this->db->update('operators',$data)){
	    return true;
	}
	return false;
    }
    function getOperatorby_ID($id){
	
	$this->db->select('o.*,co.id country_id,c.id city_id,s.id state_id');
	$this->db->from('operators o');
	$this->db->join('cities c','o.city=c.id');
	$this->db->join('states s','c.state_id=s.id');
	$this->db->join('countries co','s.country_id=co.id');
	$this->db->where('o.id',$id);
	$q = $this->db->get();//print_R($this->db->error());
	if($q->num_rows()>0){
	    $data = $q->row();
	    $data->states = $this->site->getStates_bycountry($data->country_id);
	    $data->cities = $this->site->getcities_byStates($data->city_id);
	    //print_R($data);exit;
	    return $data;
	}
	return false;
	
    }
    function update_operator_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('operators',$data)){
	    return true;
	}
	return false;
    }
   
}
