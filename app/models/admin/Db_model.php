<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	public function getUrlData($group_id, $countryCode){
		if(!empty($group_id)){
			$data = array();
			
			if($group_id == 1 || $group_id == 2){
				
				if($this->session->userdata('group_id') == 1 && $countryCode != ''){
					$this->db->where('is_country', $countryCode);
				}elseif($this->session->userdata('group_id') != 1){
					$this->db->where('is_country', $countryCode);
				}
				$q = $this->db->get('users');
				 if ($q->num_rows() > 0) {
					
					foreach(($q->result())  as $row){
						
						 if($row->is_connected == 1){
							$user_connect[] = $row;
						}else{
							$user_disconnect[] = $row;
						}
						
						if($row->is_approved == 1){
							if($row->group_id == 3){
								$vendor_active[] = $row;
							}elseif($row->group_id == 4){
								$driver_active[] = $row;
							}elseif($row->group_id == 5){
								$customer_active[] = $row;
							}
						}else{
							if($row->group_id == 3){
								$vendor_inactive[] = $row;
							}elseif($row->group_id == 4){
								$driver_inactive[] = $row;
							}elseif($row->group_id == 5){
								$customer_inactive[] = $row;
							}
						}
					}
				}
				
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$r = $this->db->get('rides');
				 if ($r->num_rows() > 0) {
					
					foreach(($r->result())  as $ride){
						
						
						if($ride->booked_type == 1){
							$cityride[] = $ride;
						}elseif($ride->booked_type == 2){
							$rental[] = $ride;
						}elseif($ride->booked_type == 3){
							$outstation[] = $ride;
						}
						
						if($ride->status == 1){
							$request[] = $ride;
						}elseif($ride->status == 2){
							$booked[] = $ride;
						}elseif($ride->status == 3){
							$onride[] = $ride;
						}elseif($ride->status == 4){
							$waiting[] = $ride;
						}elseif($ride->status == 5){
							$completed[] = $ride;
						}elseif($ride->status == 6){
							$cancelled[] = $ride;
						}elseif($ride->status == 7){
							$ride_later[] = $ride;
						}
					}
					
				}
				
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$t = $this->db->get('taxi_type');
				 if ($t->num_rows() > 0) {
					
					foreach(($t->result())  as $ttype){
						$taxi[$ttype->id]  = $this->db->select('*')->where('type', $ttype->id)->where('is_country', $countryCode)->get('taxi');
						if ($taxi[$ttype->id]->num_rows() > 0) {
							foreach(($taxi[$ttype->id]->result()) as $taxi_val){
								$taxi[$ttype->name][] = $taxi_val;
							}
						}
						
						$data['taxi'][] = array('title' => $ttype->name, 'total' => count($taxi[$ttype->name]));
					}
					
					
					
				 }
				 
				  $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('t.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('t.is_country', $countryCode);
		}
		$tt = $this->db->get();
				 if ($tt->num_rows() > 0) {
					
					foreach(($tt->result())  as $te){
						 if($te->reg_verify == 1 && $te->taxation_verify == 1 && $te->insurance_verify == 1 && $te->permit_verify == 1 && $te->authorisation_verify == 1 && $te->fitness_verify == 1 && $te->speed_verify == 1 && $te->puc_verify == 1){
							$cab_active[] = $te;
						}else{
							$cab_inactive[] = $te;
						}
					}
					
					
					
				 }
				 
				
				 
				 $this->db->select('e.customer_id, u.group_id')->from('enquiry e')->join('users u', 'u.id = e.customer_id', 'left');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('e.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('e.is_country', $countryCode);
		}
		$tick = $this->db->get();
				 if ($tick->num_rows() > 0) {
					
					foreach(($tick->result())  as $tickets){
						if($tickets->group_id == 5){
							$user_customer[] = $tickets;
						}elseif($tickets->group_id == 4){
							$user_driver[] = $tickets;
						}elseif($tickets->group_id == 3){
							$user_vendor[] = $tickets;
						}
						
					}
				 }
				 
				 $this->db->select('e.id,e.enquiry_status')->from('enquiry e');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('e.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('e.is_country', $countryCode);
		}
		$enquiry = $this->db->get();
				 if ($enquiry->num_rows() > 0) {
					
					foreach(($enquiry->result())  as $enq){
						
						if($enq->enquiry_status == 0){
							$enquiry_process[] = $enq;
						}elseif($enq->enquiry_status == 1){
							$enquiry_open[] = $enq;
						}elseif($enq->enquiry_status == 2){
							$enquiry_transfer[] = $enq;
						}elseif($enq->enquiry_status == 3){
							$enquiry_close[] = $enq;
						}elseif($enq->enquiry_status == 4){
							$enquiry_reopen[] = $enq;
						}
						
						$enquiry_total[] = $enq;
					}
				 }
				 
				  $this->db->select('feedback_rating');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$feedback = $this->db->get('enquiry_feedback');
				 if ($feedback->num_rows() > 0) {
					 foreach(($feedback->result())  as $feed){
						 if($feed->feedback_rating == 1){
							$one_star[] = $feed;
						}elseif($feed->feedback_rating == 2){
							$two_star[] = $enq;
						}elseif($feed->feedback_rating == 3){
							$three_star[] = $enq;
						}elseif($feed->feedback_rating == 4){
							$four_star[] = $enq;
						}elseif($feed->feedback_rating == 5){
							$five_star[] = $enq;
						}
						
					 }
				 }
				 
				$data['user'][] = array('title' => 'Vendor', 'active' => count($vendor_active), 'inactive' => count($vendor_inactive), 'color' => 'col_brown', 'circle' => 'dashboardboxbox1',  'link' => admin_url('people/vendor'),  'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Driver', 'active' => count($driver_active), 'inactive' => count($driver_inactive), 'color' => 'col_blue', 'circle' => 'dashboardboxbox2', 'link' => admin_url('people/driver'), 'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Customer', 'active' => count($customer_active), 'inactive' => count($customer_inactive), 'color' => 'col_red', 'circle' => 'dashboardboxbox3', 'link' => admin_url('people/customer'),  'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Cab', 'active' => count($cab_active), 'inactive' => count($cab_inactive), 'color' => 'col_green', 'circle' => 'dashboardboxbox4', 'link' => admin_url('taxi'), 'icon' => 'fa-bar-chart');
				
				$data['ride'][] = array('category' => 'Request Ride', 'value' => count($request));
				$data['ride'][] = array('category' => 'Booked Ride', 'value' => count($booked));
				$data['ride'][] = array('category' => 'Onride', 'value' => count($onride));
				$data['ride'][] = array('category' => 'Waiting Ride', 'value' => count($waiting));
				$data['ride'][] = array('category' => 'Completed Ride', 'value' => count($completed));
				$data['ride'][] = array('category' => 'Cancelled Ride', 'value' => count($cancelled));
				$data['ride'][] = array('category' => 'Ride Later', 'value' => count($ride_later));
				
				$data['booked_ride'][] = array('name' => 'City Ride', 'steps' => count($cityride));
				$data['booked_ride'][] = array('name' => 'Rental Ride', 'steps' => count($rental));
				$data['booked_ride'][] = array('name' => 'Outstation', 'steps' => count($outstation));
				$data['booked_ride'][] = array('name' => 'Driver Only', 'steps' => 0);
				$data['booked_ride'][] = array('name' => 'Corporate Information', 'steps' => 0);
				
				
				$data['cabs'][] = array('type' => 'Online', 'percent' => count($user_connect));
				$data['cabs'][] = array('type' => 'Offline', 'percent' => count($user_disconnect));
				
				$data['enquiry'][] = array('category' => 'Process', 'value' => count($enquiry_process), 'full' => count($enquiry_total));
				$data['enquiry'][] = array('category' => 'Open', 'value' => count($enquiry_open), 'full' => count($enquiry_total));
				$data['enquiry'][] = array('category' => 'Transfer', 'value' => count($enquiry_transfer), 'full' => count($enquiry_total));
				$data['enquiry'][] = array('category' => 'Close', 'value' => count($enquiry_close), 'full' => count($enquiry_total));
				$data['enquiry'][] = array('category' => 'Reopen', 'value' => count($enquiry_reopen), 'full' => count($enquiry_total));
				
				$data['tickets'][] = array('country' => 'Customer', 'litres' => count($user_customer));
				$data['tickets'][] = array('country' => 'Driver', 'litres' => count($user_driver));
				$data['tickets'][] = array('country' => 'Vendor', 'litres' => count($user_vendor));
				
				$data['rating'][] = array('star' => '5', 'total' => count($five_star));
				$data['rating'][] = array('star' => '4', 'total' => count($four_star));
				$data['rating'][] = array('star' => '3', 'total' => count($three_star));
				$data['rating'][] = array('star' => '2', 'total' => count($two_star));
				$data['rating'][] = array('star' => '1', 'total' => count($one_star));
				
				
				$data['average_ride'][] = array('title' => 'Trip distance', 'total' => count($cityride), 'color' => 'red');
				$data['average_ride'][] = array('title' => 'Trip fare', 'total' => count($rental), 'color' => 'red');
				$data['average_ride'][] = array('title' => 'Waiting time', 'total' => count($outstation), 'color' => 'red');
				
			}elseif($group_id == 6){
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$q = $this->db->get('users');
				 if ($q->num_rows() > 0) {
					
					foreach(($q->result())  as $row){
						
						if($row->is_approved == 1){
							if($row->group_id == 3){
								$vendor_active[] = $row;
							}elseif($row->group_id == 4){
								$driver_active[] = $row;
							}elseif($row->group_id == 5){
								$customer_active[] = $row;
							}
						}else{
							if($row->group_id == 3){
								$vendor_inactive[] = $row;
							}elseif($row->group_id == 4){
								$driver_inactive[] = $row;
							}elseif($row->group_id == 5){
								$customer_inactive[] = $row;
							}
						}
					}
				}
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$r = $this->db->get('rides');
				 if ($r->num_rows() > 0) {
					
					foreach(($r->result())  as $ride){
						
						
						if($ride->booked_type == 1){
							$cityride[] = $ride;
						}elseif($ride->booked_type == 2){
							$rental[] = $ride;
						}elseif($ride->booked_type == 3){
							$outstation[] = $ride;
						}
						
						if($ride->status == 1){
							$request[] = $ride;
						}elseif($ride->status == 2){
							$booked[] = $ride;
						}elseif($ride->status == 3){
							$onride[] = $ride;
						}elseif($ride->status == 4){
							$waiting[] = $ride;
						}elseif($ride->status == 5){
							$completed[] = $ride;
						}elseif($ride->status == 6){
							$cancelled[] = $ride;
						}elseif($ride->status == 7){
							$ride_later[] = $ride;
						}
					}
					
				}
				
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$t = $this->db->get('taxi_type');
				 if ($t->num_rows() > 0) {
					
					foreach(($t->result())  as $ttype){
						$taxi[$ttype->id]  = $this->db->select('*')->where('type', $ttype->id)->where('is_country', $countryCode)->get('taxi');
						if ($taxi[$ttype->id]->num_rows() > 0) {
							foreach(($taxi[$ttype->id]->result()) as $taxi_val){
								$taxi[$ttype->name][] = $taxi_val;
							}
						}
						
						$data['taxi'][] = array('title' => $ttype->name, 'total' => count($taxi[$ttype->name]));
					}
					
					
					
				 }
				 
				  $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1);
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$tt = $this->db->get();
				 if ($tt->num_rows() > 0) {
					
					foreach(($tt->result())  as $te){
						 if($te->reg_verify == 1 && $te->taxation_verify == 1 && $te->insurance_verify == 1 && $te->permit_verify == 1 && $te->authorisation_verify == 1 && $te->fitness_verify == 1 && $te->speed_verify == 1 && $te->puc_verify == 1){
							$cab_active[] = $te;
						}else{
							$cab_inactive[] = $te;
						}
					}
					
					
					
				 }
				 
				
				 
				$data['user'][] = array('title' => 'Vendor', 'active' => count($vendor_active), 'inactive' => count($vendor_inactive), 'color' => 'col_brown', 'circle' => 'dashboardboxbox1',  'link' => admin_url('people/vendor'),  'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Driver', 'active' => count($driver_active), 'inactive' => count($driver_inactive), 'color' => 'col_blue', 'circle' => 'dashboardboxbox2', 'link' => admin_url('people/driver'), 'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Customer', 'active' => count($customer_active), 'inactive' => count($customer_inactive), 'color' => 'col_red', 'circle' => 'dashboardboxbox3', 'link' => admin_url('people/customer'),  'icon' => 'fa-bar-chart');
				$data['user'][] = array('title' => 'Cab', 'active' => count($cab_active), 'inactive' => count($cab_inactive), 'color' => 'col_green', 'circle' => 'dashboardboxbox4', 'link' => admin_url('taxi'), 'icon' => 'fa-bar-chart');
				
				$data['ride'][] = array('category' => 'Request Ride', 'value' => count($request));
				$data['ride'][] = array('category' => 'Booked Ride', 'value' => count($booked));
				$data['ride'][] = array('category' => 'Onride', 'value' => count($onride));
				$data['ride'][] = array('category' => 'Waiting Ride', 'value' => count($waiting));
				$data['ride'][] = array('category' => 'Completed Ride', 'value' => count($completed));

				$data['ride'][] = array('category' => 'Cancelled Ride', 'value' => count($cancelled));
				$data['ride'][] = array('category' => 'Ride Later', 'value' => count($ride_later));
				
				$data['booked_ride'][] = array('name' => 'City Ride', 'steps' => count($cityride));
				$data['booked_ride'][] = array('name' => 'Rental Ride', 'steps' => count($rental));
				$data['booked_ride'][] = array('name' => 'Outstation', 'steps' => count($outstation));
				$data['booked_ride'][] = array('name' => 'Driver Only', 'steps' => 0);
				$data['booked_ride'][] = array('name' => 'Corporate Information', 'steps' => 0);	
			}elseif($group_id == 4){
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$q = $this->db->get('users');
				 if ($q->num_rows() > 0) {
					
					foreach(($q->result())  as $row){
						
						if($row->is_approved == 1){
							if($row->group_id == 3){
								$vendor_active[] = $row;
							}elseif($row->group_id == 4){
								$driver_active[] = $row;
							}elseif($row->group_id == 5){
								$customer_active[] = $row;
							}
						}else{
							if($row->group_id == 3){
								$vendor_inactive[] = $row;
							}elseif($row->group_id == 4){
								$driver_inactive[] = $row;
							}elseif($row->group_id == 5){
								$customer_inactive[] = $row;
							}
						}
					}
				}
				
				 $this->db->select('*')->where('driver_id', $this->session->userdata('user_id'));
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$r = $this->db->get('rides');
				 if ($r->num_rows() > 0) {
					
					foreach(($r->result())  as $ride){
						
						
						if($ride->booked_type == 1){
							$cityride[] = $ride;
						}elseif($ride->booked_type == 2){
							$rental[] = $ride;
						}elseif($ride->booked_type == 3){
							$outstation[] = $ride;
						}
						
						if($ride->status == 1){
							$request[] = $ride;
						}elseif($ride->status == 2){
							$booked[] = $ride;
						}elseif($ride->status == 3){
							$onride[] = $ride;
						}elseif($ride->status == 4){
							$waiting[] = $ride;
						}elseif($ride->status == 5){
							$completed[] = $ride;
						}elseif($ride->status == 6){
							$cancelled[] = $ride;
						}elseif($ride->status == 7){
							$ride_later[] = $ride;
						}
					}
					
				}
			$this->db->select('*');	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$t = $this->db->get('taxi_type');
				 if ($t->num_rows() > 0) {
					
					foreach(($t->result())  as $ttype){
						$taxi[$ttype->id]  = $this->db->select('*')->where('type', $ttype->id)->get('taxi');
						if ($taxi[$ttype->id]->num_rows() > 0) {
							foreach(($taxi[$ttype->id]->result()) as $taxi_val){
								$taxi[$ttype->name][] = $taxi_val;
							}
						}
						
						$data['taxi'][] = array('title' => $ttype->name, 'total' => count($taxi[$ttype->name]));
					}
					
					
					
				 }
				 
				  $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('t.is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('t.is_country', $countryCode);
		}
		$tt = $this->db->get();
				 if ($tt->num_rows() > 0) {
					
					foreach(($tt->result())  as $te){
						 if($te->reg_verify == 1 && $te->taxation_verify == 1 && $te->insurance_verify == 1 && $te->permit_verify == 1 && $te->authorisation_verify == 1 && $te->fitness_verify == 1 && $te->speed_verify == 1 && $te->puc_verify == 1){
							$cab_active[] = $te;
						}else{
							$cab_inactive[] = $te;
						}
					}
					
					
					
				 }
				 
				
				 
				
				
				$data['ride'][] = array('category' => 'Request Ride', 'value' => count($request));
				$data['ride'][] = array('category' => 'Booked Ride', 'value' => count($booked));
				$data['ride'][] = array('category' => 'Onride', 'value' => count($onride));
				$data['ride'][] = array('category' => 'Waiting Ride', 'value' => count($waiting));
				$data['ride'][] = array('category' => 'Completed Ride', 'value' => count($completed));


				$data['ride'][] = array('category' => 'Cancelled Ride', 'value' => count($cancelled));
				$data['ride'][] = array('category' => 'Ride Later', 'value' => count($ride_later));
				
				$data['booked_ride'][] = array('name' => 'City Ride', 'steps' => count($cityride));
				$data['booked_ride'][] = array('name' => 'Rental Ride', 'steps' => count($rental));
				$data['booked_ride'][] = array('name' => 'Outstation', 'steps' => count($outstation));
				$data['booked_ride'][] = array('name' => 'Driver Only', 'steps' => 0);
				$data['booked_ride'][] = array('name' => 'Corporate Information', 'steps' => 0);		
			}elseif($group_id == 5){
					
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
				$q = $this->db->select('*')->get('users');
				 if ($q->num_rows() > 0) {
					
					foreach(($q->result())  as $row){
						
						if($row->is_approved == 1){
							if($row->group_id == 3){
								$vendor_active[] = $row;
							}elseif($row->group_id == 4){
								$driver_active[] = $row;
							}elseif($row->group_id == 5){
								$customer_active[] = $row;
							}
						}else{
							if($row->group_id == 3){
								$vendor_inactive[] = $row;
							}elseif($row->group_id == 4){
								$driver_inactive[] = $row;
							}elseif($row->group_id == 5){
								$customer_inactive[] = $row;
							}
						}
					}
				}
				
				 $this->db->select('*')->where('customer_id', $this->session->userdata('user_id'));
				
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$r = $this->db->get('rides');
				 if ($r->num_rows() > 0) {
					
					foreach(($r->result())  as $ride){
						
						
						if($ride->booked_type == 1){
							$cityride[] = $ride;
						}elseif($ride->booked_type == 2){
							$rental[] = $ride;
						}elseif($ride->booked_type == 3){
							$outstation[] = $ride;
						}
						
						if($ride->status == 1){
							$request[] = $ride;
						}elseif($ride->status == 2){
							$booked[] = $ride;
						}elseif($ride->status == 3){
							$onride[] = $ride;
						}elseif($ride->status == 4){
							$waiting[] = $ride;
						}elseif($ride->status == 5){
							$completed[] = $ride;
						}elseif($ride->status == 6){
							$cancelled[] = $ride;
						}elseif($ride->status == 7){
							$ride_later[] = $ride;
						}
					}
					
				}
				
				
				 
				 
				
				 
				
				
				$data['ride'][] = array('category' => 'Request Ride', 'value' => count($request));
				$data['ride'][] = array('category' => 'Booked Ride', 'value' => count($booked));
				$data['ride'][] = array('category' => 'Onride', 'value' => count($onride));
				$data['ride'][] = array('category' => 'Waiting Ride', 'value' => count($waiting));
				$data['ride'][] = array('category' => 'Completed Ride', 'value' => count($completed));


				$data['ride'][] = array('category' => 'Cancelled Ride', 'value' => count($cancelled));
				$data['ride'][] = array('category' => 'Ride Later', 'value' => count($ride_later));
				
				$data['booked_ride'][] = array('name' => 'City Ride', 'steps' => count($cityride));
				$data['booked_ride'][] = array('name' => 'Rental Ride', 'steps' => count($rental));
				$data['booked_ride'][] = array('name' => 'Outstation', 'steps' => count($outstation));
				$data['booked_ride'][] = array('name' => 'Driver Only', 'steps' => 0);
				$data['booked_ride'][] = array('name' => 'Corporate Information', 'steps' => 0);	
			}
			
			return $data;
		}
		return false;
	}
    

}
