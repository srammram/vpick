<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rides extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		
        $this->lang->admin_load('rides', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('rides_model');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Rides'));
        $meta = array('page_title' => 'Rides', 'bc' => $bc);
		$this->data['msg'] = 'Rides';
        $this->page_construct('rides/index', $meta, $this->data);
    }
    function getOnRides(){
        //print_R($_GET);exit;
        $this->sma->checkPermissions('index');
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
        
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no, t.code as taxi_code,  t.number, u.username as driver_code,  d.first_name as driver_name,  {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.end, {$this->db->dbprefix('rides')}.status ")
            ->from("rides")
            ->join('user_profile d','d.user_id=rides.driver_id', 'left')
			->join('user_profile c','c.user_id=rides.customer_id', 'left')
			->join('users u','u.id=rides.driver_id', 'left')
            ->join('taxi t','t.id=rides.taxi_id', 'left');
			if($booked_status != 0){
				$this->datatables->where('rides.status',$booked_status);
			}
			if($booked_type != 0){
				$this->datatables->where('rides.booked_type',$booked_type);
			}
            
			$this->datatables->edit_column('status', '$1__$2', 'status, id');
			
            $this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	function track($id = NULL)
    {
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
		}
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['status'] = $booked_status;
		$this->data['rides'] = $this->rides_model->getRides($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang($msg)));
        $meta = array('page_title' => lang($msg), 'bc' => $bc);
		$this->data['msg'] = $msg;
        $this->page_construct('rides/tracking', $meta, $this->data);
    }
    
   
}
