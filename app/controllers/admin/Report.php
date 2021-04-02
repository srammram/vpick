<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$this->lang->admin_load('masters', $this->Settings->user_language);
		$this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->helper(array('form', 'url'));
		$this->load->library('upload');
		$this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
        $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('report_model');
    }
	
	public function dashboard() {
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->sma->checkPermissions('reports_dashboard-index');
		$meta = array('page_title' => lang('dashboard'), 'bc' => $bc);
		$this->data['health'] = $this->report_model->getUserHealth($countryCode);
		
		$this->page_construct('report/dashboard', $meta, $this->data);

	}

	public function activity_report(){

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
		$meta = array('page_title' => lang('activity_report'), 'bc' => $bc);
		$this->page_construct('report/activity_report', $meta, $this->data);
	}

	function getActivityReport(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no,  c.first_name as customer_name, c.email as customer_email, c.mobile as customer_mobile, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.end, 
			
			{$this->db->dbprefix('rides')}.distance_km, {$this->db->dbprefix('rides')}.distance_price,
			d.first_name as driver_name, d.email as driver_email, d.mobile as driver_mobile, t.name as cab_name, t.number, tt.name as cab_type_name, 
			{$this->db->dbprefix('rides')}.actual_loc, rp.total_distance,  rp.discount_name,  rp.discount_fare,  rp.total_fare, rp.total_tax_fare, rp.total_toll, rp.total_parking, rp.outstanding_from_last_trip, rp.waiting_charge,  rp.final_total,
			country.name as instance_country 
			
			")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
			->join("users c", " c.id = rides.customer_id", "left")
			->join("users d", " d.id = rides.driver_id", "left")
			->join("taxi_type tt", " tt.id = rides.cab_type_id", "left")
			->join("taxi t", " t.id = rides.taxi_id", "left")
			->join("ride_payment rp", " rp.ride_id = rides.id", "left");
			
			
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booking_timing) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booking_timing) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			
			$this->datatables->where("rides.status", 5);
			$this->datatables->group_by("rides.id");
			
            
          
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }

	public function health_report(){

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
		$meta = array('page_title' => lang('health_report'), 'bc' => $bc);
		$this->page_construct('report/health_report', $meta, $this->data);
	}

	function getHealthReport(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('health_driver')}.id as id, {$this->db->dbprefix('health_driver')}.created_on, 
			d.first_name as driver_name, d.email as driver_email, d.mobile as driver_mobile, {$this->db->dbprefix('health_driver')}.health_name, {$this->db->dbprefix('health_driver')}.health_hours,
			country.name as instance_country 
			
			")
            ->from("health_driver")
			->join("countries country", " country.iso = health_driver.is_country", "left")
			
			->join("users d", " d.id = health_driver.driver_id", "left");
			
			
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('health_driver')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('health_driver')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("health_driver.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("health_driver.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("health_driver.id");
			
            
          
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	
}
