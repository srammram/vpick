<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Userspayments_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
    }
	
	function checkUser($mobile, $country_code, $customer_type, $countryCode){
		$q = $this->db->select('id')->where('is_country', $countryCode)->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', $customer_type)->get('users');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return false;	
	}
    
	function getUserID($user_id, $countryCode){
		
	}
    
   function getEnquiryID($id, $countryCode){
		$this->db->select('e.*, u.first_name as customer_name, h.name as help_services, r.booking_no, r.payment_id, r.payment_name, r.driver_id, rd.first_name as ride_driver_name, rc.first_name as ride_customer_name, t.name as taxi_name, tt.name as taxi_type_name, r.booked_type, r.outstation_way, r.cancelled_type, r.cancel_status, r.cancel_msg, r.cancel_on, r.status, r.booked_on, r.ride_timing, r.ride_timing_end, r.ride_type, r.start, r.start_lat, r.start_lng, r.end, r.end_lat, r.end_lng, r.distance_km, r.distance_price, r.driver_final_distance, hs.name as sub_help_name, hm.name as main_help_name ');
		$this->db->from('enquiry e');
		$this->db->join('users u', 'u.id = e.customer_id', 'left');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->join('rides r', 'r.id = e.services_id', 'left');
		$this->db->join('users rd', 'rd.id = r.driver_id', 'left');
		$this->db->join('users rc', 'rc.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('help_sub hs', 'hs.id = e.help_id', 'left');
		$this->db->join('help_main hm', 'hm.id = hs.parent_id', 'left');
		
		$this->db->where('e.id',$id);
		$this->db->where('e.is_country', $countryCode);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function openenquiry($enquiry, $enquiry_support, $enquiry_follow, $enquiry_status, $enquiry_id, $countryCode){
		
		if($enquiry_status == 1){
			
			$q = $this->db->update('enquiry', $enquiry, array('id' => $enquiry_id, 'is_country' => $countryCode ));
			$enquiry_support['is_country'] = $countryCode;
			$this->db->insert('enquiry_support', $enquiry_support);
			$enquiry_support_id = $this->db->insert_id();
			if(!empty($enquiry_support_id)){
				$enquiry_follow['enquiry_support_id'] = $enquiry_support_id;
				$enquiry_follow['is_country'] = $countryCode;
				$this->db->insert('follows', $enquiry_follow);
			}
			
			return true;	
		}
		return false;
	}
	
	function reopenenquiry($enquiry, $enquiry_support, $enquiry_follow, $enquiry_status, $enquiry_id, $countryCode){
		
		if($enquiry_status == 4){
			
			$q = $this->db->update('enquiry', $enquiry, array('id' => $enquiry_id, 'is_country' => $countryCode ));
			$enquiry_support['is_country'] = $countryCode;
			$this->db->insert('enquiry_support', $enquiry_support);
			$enquiry_support_id = $this->db->insert_id();
			if(!empty($enquiry_support_id)){
				$enquiry_follow['enquiry_support_id'] = $enquiry_support_id;
				$enquiry_follow['is_country'] = $countryCode;
				$this->db->insert('follows', $enquiry_follow);
			}
			
			return true;	
		}
		return false;
	}
	
	function closeenquiry($enquiry, $enquiry_support, $enquiry_follow, $enquiry_status, $enquiry_id, $countryCode){
		
		if($enquiry_status != 1){
			
			$q = $this->db->update('enquiry', $enquiry, array('id' => $enquiry_id, 'is_country' => $countryCode ));
			$enquiry_support['is_country'] = $countryCode;
			$this->db->insert('enquiry_support', $enquiry_support);
			$enquiry_support_id = $this->db->insert_id();
			if(!empty($enquiry_support_id)){
				$enquiry_follow['enquiry_support_id'] = $enquiry_support_id;
				$enquiry_follow['is_country'] = $countryCode;
				$this->db->insert('follows', $enquiry_follow);
			}
			
			return true;	
		}
		return false;
	}
	
	function create_ticket($enquiry, $countryCode){
		$enquiry['is_country'] = $countryCode;
		$q = $this->db->insert('enquiry', $enquiry);
		if($q){
			return true;	
		}
		return false;
	}
	
	function getHelp($countryCode){
		$this->db->select('id, name');
		$this->db->where('is_country', $countryCode);
		$q  = $this->db->get('help');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getUser_bygroup($group_id, $countryCode){
		$q = $this->db->get_where('users',array('group_id'=>$group_id, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	function getHelp_main_byhelp($parent_id, $countryCode){
		$q = $this->db->get_where('help_main',array('parent_id'=>$parent_id, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getHelp_sub_byhelp_main($parent_id, $countryCode){
		$q = $this->db->get_where('help_sub',array('parent_id'=>$parent_id, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getHelp_form_byhelp_sub($parent_id, $countryCode){
		$data = array();
		$a = $this->db->select('details')->where('is_country', $countryCode)->where('id', $parent_id)->get('help_sub');
		if($a->num_rows()>0){
			$data['sub'] = $a->row('details');
		}
		$q = $this->db->get_where('help_form',array('parent_id'=>$parent_id, 'is_country' => $countryCode));
       	if($q->num_rows()>0){
			$data['form'] = $q->result();
			
			return $data;
		}
		return false;
    }
	
	
	
}
