<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
	$this->model = 'booking_model';
        $this->lang->admin_load('booking', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model($this->model);
    }

    function index(){
        $this->sma->checkPermissions();
	$model = $this->model;
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('email', lang("email_address"), 'required');     
        
        if ($this->form_validation->run() == true) {
	    
           
            $data = array(
                'driver_id' => $this->input->post('driver_id'),
                'taxi_id' =>$this->site->getTaxi_bydriver($this->input->post('driver_id')),
		'customer_id' => $this->input->post('customer_id'),
                'booked_by' => 'manual',
		'booked_type' => $this->input->post('booked_type'),
                'booked_by_id' => $this->session->userdata('user_id'),
                'booked_on' => date('Y-m-d H:i:s'),               
                'booking_timing' => $this->input->post('booking_timing').':00',
                'ride_type' => $this->input->post('ride_type'),
		'pick_up' => $this->input->post('pick_up_location'),
		'drop_off' => $this->input->post('drop_off_location'),
		'pickup_lat' => $this->input->post('orig_latitude'),
                'pickup_lng' => $this->input->post('orig_longitude'),
		'dropoff_lat' => $this->input->post('dest_latitude'),
                'dropoff_lng' => $this->input->post('dest_longitude'),
                'status' => 'booked',
            );     

$this->$model->add_booking($data);			
        }
		//echo '<pre>';print_r($data);exit;
		
		
		
        if ($this->form_validation->run() == true && $this->$model->add_booking($data)){
			
            $this->session->set_flashdata('message', lang("Booked"));
            admin_redirect('booking/index');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('book_a_ride')));
            $meta = array('page_title' => lang('book_a_ride'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['taxi_types'] = $this->site->getAllTaxiTypes();
            $this->page_construct('booking/add', $meta, $this->data);
        }
    }
    function getNearestDrivers(){
	$data['origin_lat'] = $this->input->post('orig_latitude');
	$data['origin_lng'] = $this->input->post('orig_longitude');
	$data['taxi_type'] = $this->input->post('taxi_type');
	//$destination_lat = $this->input->post('dest_latitude');
	//$destination_lng = $this->input->post('dest_longitude');
	$drivers['data'] = $this->site->getNearestDrivers($data);
	echo json_encode($drivers);exit;
	
    }
    function getCustomerDetails(){
	$phone = $this->input->post('phone');
	$customers = $this->site->getCustomerDetails_byPhone($phone);
	echo json_encode($customers);exit;
    }
    
}
