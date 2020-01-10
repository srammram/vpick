<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function add_currency($data){
	$this->db->insert('currencies', $data);//print_R($this->db->error());
        return $this->db->insert_id();	
    }
    function update_currency($id,$data){
	$this->db->where('id',$id);
	if($this->db->update('currencies',$data)){
	    return true;
	}
	return false;
    }
    function getCurrencyby_ID($id){
	
	$this->db->select('*');
	$this->db->from('currencies');
	$this->db->where('id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }
    function update_currency_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('currencies',$data)){
	    return true;
	}
	return false;
    }
    function add_country($data){
	$this->db->insert('countries', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function update_country_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('countries',$data)){
	    return true;
	}
	return false;
    }
    function add_continent($data){
	$this->db->insert('continents', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function update_continent_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('continents',$data)){
	    return true;
	}
	return false;
    }
    function add_zone($data){
	$this->db->insert('zones', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function update_zone_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('zones',$data)){
	    return true;
	}
	return false;
    }
   
    
    
    
    function add_state($data){
	$this->db->insert('states', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function update_state_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('states',$data)){
	    return true;
	}
	return false;
    }
    
    
    
    
    function add_city($data){
	$this->db->insert('cities', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function update_city_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('cities',$data)){
	    return true;
	}
	return false;
    }
    function getLocation($table,$id){
	if($table == "countries"){
	    $returnField = 'country_name';
	}else if($table == "states"){
	    $returnField = 'state_name';
	}else if($table == "cities"){
	    $returnField = 'city_name';
	}
	$q = $this->db->get_where($table,array('id'=>$id));//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
	    return $q->row($returnField);
	}
	return false;
    }
    function add_kyc_doc_type($data,$fields){
	$this->db->insert('kyc_doc_types', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    if(!empty($fields)){
		foreach($fields as $k => $row){
		    $fieldData = $row;
		    $fieldData['doc_id'] = $id;
		    $this->db->insert('kycdocument_type_fields', $fieldData);//print_r($this->db->error());exit;
		}
	    }
	    
	    return true;
	}
	return false;
    }
    function update_doc_type_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update('kyc_doc_types',$data)){
	    return true;
	}
	return false;
    }
    function getKycDoctypeby_ID($id){
	
	$this->db->select('*');
	$this->db->from('kyc_doc_types');
	$this->db->where('id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }
     function update_kyc_doc_type($id,$data,$fields){
	$this->db->where('id',$id);
	if($this->db->update('kyc_doc_types',$data)){
	    if(!empty($fields)){
		foreach($fields as $k => $row){
		    $fieldData = $row;
		    $fieldData['doc_id'] = $id;
		    if($row['id']==0){
			$this->db->insert('kycdocument_type_fields', $fieldData);
		    }else{
			$this->db->where('id',$row['id']);
			$this->db->update('kycdocument_type_fields',$row);
		    }
		    //print_r($this->db->error());exit;
		}
	    }
	    return true;
	}
	return false;
    }
    function add_user_group($data){
	$this->db->insert('groups', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function getUserGroupby_ID($id){
	
	$this->db->select('*');
	$this->db->from('groups');
	$this->db->where('id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }
    function update_user_group($id,$data){
	$this->db->where('id',$id);
	if($this->db->update('groups',$data)){
	    return true;
	}
	return false;
    }
    function getKycDoctypeFieldsby_ID($id){
	$this->db->select('*');
	$this->db->from('kycdocument_type_fields');
	$this->db->where('doc_id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
    }
    
    function add_customer_group($data){
	$this->db->insert('customer_groups', $data);//print_r($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    return true;
	}
	return false;
    }
    function getCustomerGroupby_ID($id){
	
	$this->db->select('*');
	$this->db->from('customer_groups');
	$this->db->where('id',$id);
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
	
    }
    function update_customer_group($id,$data){
	$this->db->where('id',$id);
	if($this->db->update('customer_groups',$data)){
	    return true;
	}
	return false;
    }
}
