<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Formsfranchisee_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		
		
    }
   
    
	function getALLFormsfranchisee(){
		$this->db->select(" n.name, n.mobile_number, n.email_address, n.description")
            ->from("enquiry_forms n")
			
			->where("n.forms_type = 2");	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('n.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('n.is_country', $countryCode);
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
   
	
}
