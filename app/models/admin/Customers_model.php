<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
	$this->table = 'customers';
    }
    function add_customer($data){
	$this->db->insert($this->table, $data);//print_R($this->db->error());
        return $this->db->insert_id();	
    }
    function update_customer($id,$data){
	$this->db->where('id',$id);
	if($this->db->update($this->table,$data)){
	    return true;
	}
	return false;
    }
    function getCustomerby_ID($id){
	
	$this->db->select('cu.*,co.id country_id,c.id city_id,s.id state_id');
	$this->db->from($this->table.' cu');
	$this->db->join('cities c','cu.city=c.id');
	$this->db->join('states s','c.state_id=s.id');
	$this->db->join('countries co','s.country_id=co.id');
	$this->db->where('cu.id',$id);
	$q = $this->db->get();//print_R($q->row());exit;
	if($q->num_rows()>0){
	    $data =  $q->row();
	    $data->states = $this->site->getStates_bycountry($data->country_id);
	    $data->cities = $this->site->getcities_byStates($data->city_id);
	    return $data;
	}
	return false;
	
    }
    function update_customer_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->table,$data)){
	    return true;
	}
	return false;
    }
}
