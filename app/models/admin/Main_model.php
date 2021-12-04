<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
    }
	
	function create_ticket($enquiry, $customer_id, $help_department, $countryCode){
		$enquiry['is_country'] = $countryCode;
		$q = $this->db->insert('enquiry', $enquiry);
		
		$enquiry_id = $this->db->insert_id();	
		if($enquiry_id){
			$s = $this->db->insert('enquiry_support', array('help_services' => $help_department, 'customer_id' => $customer_id, 'enquiry_id' => $enquiry_id, 'status' => 0, 'created_on' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'is_country' => $countryCode));
			$enquiry_support_id = $this->db->insert_id();	
			if($enquiry_support_id){
				$this->db->insert('follows', array('enquiryid' => $enquiry_id, 'enquiry_support_id' => $enquiry_support_id, 'calltype' => 'App',  'followup_date_time' => date('Y-m-d H:i:s'), 'created_on' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'is_country' => $countryCode));
				
				return true;
			}
				
		}
		return false;
	}
	
	function getIds($parent_id){
		$this->db->select('hs.parent_id as help_main_id, hm.parent_id as help_id');
		$this->db->from('help_sub hs');
		$this->db->join('help_main hm', 'hm.id = hs.parent_id', 'left');
		$this->db->where('hs.id', $parent_id);
		
		$q = $this->db->get();
		if ($q->num_rows() > 0) {	
			
			return $q->row();	
		}
		return false;	
	}
	function getCustomer($user_id, $ride_id)
	{
		
		$this->db->select('u.first_name, r.booking_no');
		$this->db->from('users u');
		$this->db->join('rides r', 'r.id = '.$ride_id.'', 'left' );
		$this->db->where('u.id', $user_id);
		
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {			
			return $q->row();	
		}
		return false;
	}
	
	function getForms($parent_id){
		$data = array();
		$a = $this->db->select('details')->where('id', $parent_id)->get('help_sub');
		if($a->num_rows()>0){
			$data['sub'] = $a->row('details');
		}
		$this->db->where('parent_id', $parent_id);	
		
		$q = $this->db->get('help_form');
       	if($q->num_rows()>0){
			$data['form'] = $q->result();
			return $data;
		}
		return false;
    }

	function addEnquiry($user){

		$q = $this->db->insert('enquiry_forms', $user);
		//print_r($this->db->last_query()); die;
		$last_id = $this->db->insert_id();

		if($last_id){
			return true;
		}		

		return false;
	}
	
	
}
