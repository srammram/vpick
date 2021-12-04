<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notify extends CI_Controller
{

    function __construct() {
        parent::__construct();
        
    }
	function acknowledegementUpdate(){
		$this->load->model('site');		
		
		$user_id = $this->input->post('userID');
		$user_type_id = $this->input->post('userType');
		$emit_name = $this->input->post('emitName');
		echo $this->site->acknowledegementUpdate($user_id, $user_type_id, $emit_name);
    }
	
	function driverhours(){
		$this->load->model('site');	
			
		$user_id = $this->input->post('user_id');
		$ride_id = $this->input->post('ride_id');
		$is_country = 'IN';
		
		
		if($user_id){	
			
			$current_time = date('Y-m-d H:i:s');
			$current_date = date('Y-m-d');
			
			if($ride_id !=0){
				$h = $this->db->query('select  id, status from kapp_user_ride_hour where user_id = "'.$user_id.'" ORDER BY id DESC LIMIT 1');
				if($h->num_rows()>0){
					if($h->row('status') != 0){
						$on_ride = 0;
					}else{
						$on_ride = 1;
						$this->db->where('id', $h->row('id'));
						$this->db->update('user_ride_hour', array('status'=>2));
					}
				}else{
					$this->db->insert('user_ride_hour', array('user_id'=>$user_id, 'ride_id'=>$ride_id));
					$on_ride = 0;
				}
			}else{
				$on_ride = 0;
			}
			
			$query = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,logout_date)) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'" AND DATE(login_date) = "'.$current_date.'" AND is_stop != 0 group by date(login_date)');
			if($query->num_rows()>0){
				$total_timestamp1 = $query->row('total_timestamp');
				
			}
			$query1 = $this->db->query('select sum(TIMESTAMPDIFF(SECOND,login_date,"'.$current_time.'")) as total_timestamp from kapp_user_login_logout where user_id = "'.$user_id.'"  AND  DATE(login_date) = "'.$current_date.'" AND is_stop = 0 group by date(login_date)');
			if($query1->num_rows()>0){
				$total_timestamp2 = $query1->row('total_timestamp');
			}
			
			//$data = array_merge($data1, $data2);
			//$total_timestamp = 0;
			$total_hours = '00:00:00';
			$total_timestamp = $total_timestamp1 + $total_timestamp2;
			//foreach($data as $key=>$value){
			//	$total_timestamp += $value->total_timestamp;
			//}
			
			//echo $total_timestamp;
			
			$total_hours = gmdate("H:i:s", $total_timestamp);
			//echo $total_hours = $this->site->driverhours($user_id, $is_country);
			
			$setting = $this->site->RegsiterSettings($is_country);
			$driver_working_hours_limit = floor($setting->driver_working_hours_limit * 3600);
		
			if($total_timestamp > $driver_working_hours_limit){
				if($on_ride == 0){
					$hour_staus = 1;
				}else{
					$hour_staus = 0;
				}
			}else{
				$hour_staus = 0;
			}
			
			$s = $this->db->query('select  socket_id from kapp_user_socket where user_id = "'.$user_id.'" ');
			if($s->num_rows()>0){
				$socket_id = $s->row('socket_id');
			}
			
			echo json_encode(array('total_hours'=> $total_hours, 'hour_staus' => $hour_staus, 'socket_id' => $socket_id));			
			exit;
		}
		echo  2;	
	}
	

}
