<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	public function getUrlData($group_id){
		if(!empty($group_id)){
			$data = array();
			
			if($group_id == 1 || $group_id == 2){
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
				
				$r = $this->db->select('*')->get('rides');
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
				
				$t = $this->db->select('*')->get('taxi_type');
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
				 
				 $tt = $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1)->get();
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
				
				
				
			}elseif($group_id == 6){
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
				
				$r = $this->db->select('*')->get('rides');
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
				
				$t = $this->db->select('*')->get('taxi_type');
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
				 
				 $tt = $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1)->get();
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
				
				$r = $this->db->select('*')->where('driver_id', $this->session->userdata('user_id'))->get('rides');
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
				
				$t = $this->db->select('*')->get('taxi_type');
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
				 
				 $tt = $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1)->get();
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
				
				$r = $this->db->select('*')->where('customer_id', $this->session->userdata('user_id'))->get('rides');
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
				
				$t = $this->db->select('*')->get('taxi_type');
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
				 
				 $tt = $this->db->select('t.id as taxi_id, td.*')->from('taxi t')->join('taxi_document td', 'td.taxi_id = t.id AND td.is_edit = 1', 'left')->where('t.is_edit', 1)->get();
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
			}
			
			return $data;
		}
		return false;
	}
    

}
