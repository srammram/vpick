<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Enquiry_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
    }
	
	function getALLEnquiry($countryCode){
		$this->db
            ->select("e.id as id, e.enquiry_type, e.enquiry_code, e.enquiry_date, h.name as help_department_name, u.first_name as customer_name, e.enquiry_status")
            ->from("enquiry e")
			->join("help h", " h.id = e.help_department "

, "left")
			->join("users u", " u.id = e.customer_id "

, "left");
			
;
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('e.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('e.is_country', $countryCode);
		}
		
			$q = $this->db->get();
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					if($row->enquiry_status == 0){
						$row->status = 'Process';
					}elseif($row->enquiry_status == 1){
						$row->status = 'Open';
					}elseif($row->enquiry_status == 2){
						$row->status = 'Transfer';
					}elseif($row->enquiry_status == 3){
						$row->status = 'Close';
					}elseif($row->enquiry_status == 4){
						$row->status = 'Reopen';
					}
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getCustomer($user_id, $ride_id, $countryCode)
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
	function userGet($user_id, $countryCode)
	{
		
		$this->db->select('u.*');
		$this->db->from('users u');
		$this->db->where('u.id', $user_id);
		
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {			
			return $q->row();	
		}
		return false;
	}
	
	
	
	function checkRides($customer_type, $user_id, $start_date, $end_date, $countryCode){
		
		
		$this->db->select('id');
		$this->db->from('rides');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		if($customer_type == 3){
			$this->db->where('vendor_id',$user_id);
		}elseif($customer_type == 4){
			$this->db->where('driver_id',$user_id);
		}elseif($customer_type == 5){
			$this->db->where('customer_id',$user_id);
		}
		
		if(!empty($start_date) && !empty($end_date)){
			$this->db->where("DATE(booked_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $start_date))));
			$this->db->where("DATE(booked_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $end_date))));
		}
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$data = array('customer_type' => $customer_type, 'user_id' => $user_id, 'start_date' => $start_date, 'end_date' => $end_date);
			return $data;
		}else{
			$this->db->select('id');
			$this->db->from('rides');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
			if($customer_type == 3){
				$this->db->where('vendor_id',$user_id);
			}elseif($customer_type == 4){
				$this->db->where('driver_id',$user_id);
			}elseif($customer_type == 5){
				$this->db->where('customer_id',$user_id);
			}
			
			if(!empty($start_date) && !empty($end_date)){
				$this->db->where("DATE(booked_on) >=", date("Y-m-d"));
				$this->db->where("DATE(booked_on) <=", date("Y-m-d"));
			}
			$c = $this->db->get();
			if ($c->num_rows() > 0) {
				$data = array('customer_type' => $customer_type, 'user_id' => $user_id, 'start_date' => date("Y/m/d"), 'end_date' => date("Y/m/d"));
				return $data;
			}else{
				return false;	
			}
		}
		return false;
	}
	function getDashboard($user_id, $countryCode){
		
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		
		$q = $this->db->get('enquiry');
		 if ($q->num_rows() > 0) {
			
			foreach(($q->result())  as $row){
				
				
					if($row->enquiry_status == 0){
						if($row->enquiry_type == 'APP'){
							$process_app[] = $row;
						}elseif($row->enquiry_type == 'Telephone'){
							$process_telephone[] = $row;
						}elseif($row->enquiry_type == 'Website'){
							$process_web[] = $row;
						}
						//$process[] = $row;
					}elseif($row->enquiry_status == 1){
						if($row->enquiry_type == 'APP'){
							$open_app[] = $row;
						}elseif($row->enquiry_type == 'Telephone'){
							$open_telephone[] = $row;
						}elseif($row->enquiry_type == 'Website'){
							$open_web[] = $row;
						}
						//$open[] = $row;
					}elseif($row->enquiry_status == 2){
						if($row->enquiry_type == 'APP'){
							$transfer_app[] = $row;
						}elseif($row->enquiry_type == 'Telephone'){
							$transfer_telephone[] = $row;
						}elseif($row->enquiry_type == 'Website'){
							$transfer_web[] = $row;
						}
						//$transfer[] = $row;
					}elseif($row->enquiry_status == 3){
						if($row->enquiry_type == 'APP'){
							$close_app[] = $row;
						}elseif($row->enquiry_type == 'Telephone'){
							$close_telephone[] = $row;
						}elseif($row->enquiry_type == 'Website'){
							$close_web[] = $row;
						}
						//$close[] = $row;
					}elseif($row->enquiry_status == 4){
						if($row->enquiry_type == 'APP'){
							$reopen_app[] = $row;
						}elseif($row->enquiry_type == 'Telephone'){
							$reopen_telephone[] = $row;
						}elseif($row->enquiry_type == 'Website'){
							$reopen_web[] = $row;
						}
						//$reopen[] = $row;
					}
				
			}
			
		}
		
		
		
		$data['enquiry'][] = array('title' => 'Close', 'app_count' => count($close_app), 'app_title' => 'App',  'telephone_count' => count($close_telephone), 'telephone_title' => 'Telephone', 'web_count' => count($close_web), 'web_title' => 'Web',  'color' => 'col_green', 'circle' => 'dashboardboxbox1',  'link' => admin_url('enquiry/listview/?enquiry_status=3'),  'icon' => 'kappclose');
		
		$data['enquiry'][] = array('title' => 'Open',  'app_count' => count($open_app), 'app_title' => 'App',  'telephone_count' => count($open_telephone), 'telephone_title' => 'Telephone', 'web_count' => count($open_web), 'web_title' => 'Web',  'color' => 'col_blue', 'circle' => 'dashboardboxbox1',  'link' => admin_url('enquiry/listview/?enquiry_status=1'),  'icon' => 'kappopen');
		
		$data['enquiry'][] = array('title' => 'Process',  'app_count' => count($process_app), 'app_title' => 'App',  'telephone_count' => count($process_telephone), 'telephone_title' => 'Telephone', 'web_count' => count($process_web), 'web_title' => 'Web',   'color' => 'col_darkbrown', 'circle' => 'dashboardboxbox1',  'link' => admin_url('enquiry/listview/?enquiry_status=0'),  'icon' => 'kappprocess');
		
		$data['enquiry'][] = array('title' => 'Reopen',  'app_count' => count($reopen_app), 'app_title' => 'App',  'telephone_count' => count($reopen_telephone), 'telephone_title' => 'Telephone', 'web_count' => count($reopen_web), 'web_title' => 'Web',   'color' => 'col_darkblue', 'circle' => 'dashboardboxbox1',  'link' => admin_url('enquiry/listview/?enquiry_status=4'),  'icon' => 'kappreopen');
		
		$data['enquiry'][] = array('title' => 'Transfer',  'app_count' => count($transfer_app), 'app_title' => 'App',  'telephone_count' => count($transfer_telephone), 'telephone_title' => 'Telephone', 'web_count' => count($transfer_web), 'web_title' => 'Web',   'color' => 'col_violet', 'circle' => 'dashboardboxbox1',  'link' => admin_url('enquiry/listview/?enquiry_status=2'),  'icon' => 'kapptransfer');
			
			
			return $data;
	}
	function checkUser($mobile, $country_code, $customer_type, $countryCode){
		$q = $this->db->select('id')->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', $customer_type)->get('users');
		if($q->num_rows()>0){
			return $q->row('id');
		}
		return false;	
	}
    
	function getUserEnquiry($user_id, $countryCode){
		$this->db->select('e.id as id, e.enquiry_type, e.enquiry_date, e.enquiry_code, e.customer_id, e.help_department, e.services_id, e.help_id, e.help_message, e.suggestion, e.enquiry_status, e.customer_status, h.name as help');
		$this->db->from('enquiry e');
		
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->where('customer_id', $user_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('e.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('e.is_country', $countryCode);
		}
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->result();	
		}
		return false;
	}
	function getUserID($user_id, $countryCode){
		$this->db->select('u.first_name, u.email, u.mobile, u.country_code, u.gender, g.name as group_name');
		$this->db->from('users u');
		$this->db->join('groups g', 'g.id = u.group_id', 'left');
		$this->db->where('u.id', $user_id);
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;	
	}
	
	function getFollows($enquiry_id, $countryCode){
		$this->db->select('es.support_id, u.first_name, u.group_id, g.name as group_name, es.help_services, es.status, es.created_on, f.calltype, f.discussion, f.remark, h.name as help_name');
		$this->db->from('enquiry_support es');
		$this->db->join('follows f', 'f.enquiryid = es.enquiry_id AND f.enquiry_support_id = es.id ');
		$this->db->join('help h', 'h.id = es.help_services ', 'left');
		$this->db->join('users u', 'u.id = es.support_id ', 'left');
		$this->db->join('groups g', 'g.id = u.group_id', 'left');
		$this->db->where('es.enquiry_id', $enquiry_id);
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			
			return $q->result();
		}
		return false;	
	}
    
   function getEnquiryID($id){
		$this->db->select('e.*, g.name as group_name, u.first_name as customer_name, h.name as help_services, r.booking_no, r.payment_id, r.payment_name, r.driver_id, rd.first_name as ride_driver_name, rc.first_name as ride_customer_name, t.name as taxi_name, tt.name as taxi_type_name, r.booked_type, r.outstation_way, r.cancelled_type, r.cancel_status, r.cancel_msg, r.cancel_on, r.status, r.booked_on, r.ride_timing, r.ride_timing_end, r.ride_type, r.start, r.start_lat, r.start_lng, r.end, r.end_lat, r.end_lng, r.distance_km, r.distance_price, r.driver_final_distance, hs.name as sub_help_name, hs.details, hm.name as main_help_name ');
		$this->db->from('enquiry e');
		$this->db->join('users u', 'u.id = e.customer_id', 'left');
		$this->db->join('groups g', 'g.id = u.group_id', 'left');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->join('rides r', 'r.id = e.services_id', 'left');
		$this->db->join('users rd', 'rd.id = r.driver_id', 'left');
		$this->db->join('users rc', 'rc.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('help_sub hs', 'hs.id = e.help_id', 'left');
		$this->db->join('help_main hm', 'hm.id = hs.parent_id', 'left');
		
		$this->db->where('e.id',$id);
		
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
	
	function create_ticket($enquiry, $customer_id, $help_department, $countryCode){
		$enquiry['is_country'] = $countryCode;
		$q = $this->db->insert('enquiry', $enquiry);
		$enquiry_id = $this->db->insert_id();	
		if($enquiry_id){
			$s = $this->db->insert('enquiry_support', array('help_services' => $help_department, 'customer_id' => $customer_id, 'enquiry_id' => $enquiry_id, 'status' => 0, 'created_on' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'is_country' => $countryCode));
			$enquiry_support_id = $this->db->insert_id();	
			if($enquiry_support_id){
				$this->db->insert('follows', array('enquiryid' => $enquiry_id, 'enquiry_support_id' => $enquiry_support_id, 'calltype' => 'Telephone',  'followup_date_time' => date('Y-m-d H:i:s'), 'created_on' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'is_country' => $countryCode));
				
				return true;
			}
				
		}
		return false;
	}
	
	function getHelp($countryCode){
		$this->db->select('id, name');
		/*if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}*/
		$this->db->where('status', 1);
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
		$this->db->where('group_id', $group_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('users');
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	function getHelp_main_byhelp($parent_id, $countryCode){
		$this->db->where('parent_id', $parent_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('help_main');
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getHelp_sub_byhelp_main($parent_id, $countryCode){
		$this->db->where('parent_id', $parent_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('help_sub');
       	if($q->num_rows()>0){
			return $q->result();
		}
		return false;
    }
	
	function getHelp_form_byhelp_sub($parent_id, $countryCode){
		$data = array();
		 $this->db->select('details')->where('id', $parent_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$a = $this->db->get('help_sub');
		if($a->num_rows()>0){
			$data['sub'] = $a->row('details');
		}
		$this->db->where('parent_id', $parent_id);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('help_form');
       	if($q->num_rows()>0){
			$data['form'] = $q->result();
			
			return $data;
		}
		return false;
    }
	
	
	
}
