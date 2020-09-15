<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usersrides extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		
        //$this->lang->admin_load('rides', $this->Settings->user_language);
        $this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
        $this->load->admin_model('usersrides_model');
    }

    function index($action = NULL)
    {
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
       
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Rides'));
        $meta = array('page_title' => 'Rides', 'bc' => $bc);
		$this->data['msg'] = 'Rides';
        $this->page_construct('usersrides/index', $meta, $this->data);
    }
    function getOnRides(){
        //print_R($_GET);exit;
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->sma->checkPermissions('index');
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
        
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no,   t.number, cu.first_name as customer_name,  cu.mobile as customer_mobile, u.first_name as driver_name,  u.mobile as driver_mobile,  {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.end, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.status, country.name as instance_country ")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1 ', 'left')
			->join('users u','u.id=rides.driver_id AND u.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
            ->join('taxi t','t.id=rides.taxi_id AND t.is_edit=1 ', 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			
          
			if($booked_status != 0){
				$this->datatables->where('rides.status',$booked_status);
			}
			if($booked_type != 0){
				$this->datatables->where('rides.booked_type',$booked_type);
			}
			
			if($this->session->userdata('group_id') == 5){
				$this->datatables->where('rides.customer_id',$this->session->userdata('user_id'));
			}elseif($this->session->userdata('group_id') == 4){
				$this->datatables->where('rides.driver_id',$this->session->userdata('user_id'));
			}
            
			$this->datatables->edit_column('status', '$1__$2', 'status, id');
			
            //$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");
			
			$edit = "<a href='" . admin_url('usersrides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'><div class='kapplist-view1'></div></a></div>
			<div><a href=''><div class='kapplist-edit'></div></a></div>
			<div><a href=''><div class='kapplist-car'></div></a></div>
			<div><a href=''><div class='kapplist-path'></div></a></div>
			
			");*/
		$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
        //$this->datatables->unset_column('id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	function track($id = NULL)
    {
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
       $booked_status = $_GET['status']; 
		
		if($booked_status == 1){
			$msg = 'Request Ride';
		}elseif($booked_status == 2){
			$msg = 'Booked Ride';
		}elseif($booked_status == 3){
			$msg = 'Onride Ride';
		}elseif($booked_status == 4){
			$msg = 'Waiting Ride';
		}elseif($booked_status == 5){
			$msg = 'Completed Ride';
		}elseif($booked_status == 6){
			$msg = 'Cancelled Ride';
		}elseif($booked_status == 7){
			$msg = 'Ride Later Ride';
		}elseif($booked_status == 8){
			$msg = 'Ride Rejected';
		}
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['status'] = $booked_status;
		$this->data['rides'] = $this->usersrides_model->getRides($id, $countryCode);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'tracking'));
        $meta = array('page_title' => 'tracking', 'bc' => $bc);
		$this->data['msg'] = $msg;
        $this->page_construct('usersrides/tracking', $meta, $this->data);
    }
    
   
}
