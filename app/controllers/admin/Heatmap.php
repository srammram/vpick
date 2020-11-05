<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Heatmap extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        //$this->lang->admin_load('map', $this->Settings->user_language);
        $this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
        $this->load->admin_model('heatmap_model');
    }

    function available_map()
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
		
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('heatmap/available_map'), 'page' => lang('available_taxis')));
        $meta = array('page_title' => lang('available_taxis'), 'bc' => $bc);    
        $this->page_construct('heatmap/available_map', $meta, $this->data);
    }
	
    function getTracking(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data['distance'] = $this->Settings->search_kilometer;
		$data['latitude'] = $_GET['lat'];
		$data['longitude'] = $_GET['lng'];
         
        $locations = $this->heatmap_model->getTracking($data);
		if(!empty($locations) && $locations != NULL){
			foreach($locations as $loc){
				
				$val[] = array(
					'driver_id' => $loc->id,
					'lat' => $loc->current_latitude,
					'lng' => $loc->current_longitude,
					'address' => 'Driver Name : '.$loc->first_name.' '.$loc->last_name.' , Address : '.$this->findLocationWEB($loc->current_latitude, $loc->current_longitude),
					'icon' => 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
				);
				
			}
		}else{
			$val = array();
		}
        echo json_encode($val);exit;
    }
	/*#### City Ride cab */
	function available_cityride()
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
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('heatmap/available_cityride'), 'page' => lang('city_ride_cab_available')));
        $meta = array('page_title' => lang('tracking'), 'bc' => $bc);    
        $this->page_construct('heatmap/available_cityride', $meta, $this->data);
    }
	
    function getTrackingCityride(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data['distance'] = $this->Settings->search_kilometer;
		$data['latitude'] = $_GET['lat'];
		$data['longitude'] = $_GET['lng'];
         
        $locations = $this->heatmap_model->getTracking($data);
		//print_r($locations);die;
		if(!empty($locations) && $locations != NULL){
			foreach($locations as $loc){
				
				$val[] = array(
					'driver_id' => $loc->id,
					'lat' => $loc->current_latitude,
					'lng' => $loc->current_longitude,
					'address' => $this->findLocationWEB($loc->current_latitude, $loc->current_longitude),
					'icon' => 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
				);
				
			}
		}else{
			$val = array();
		}
        echo json_encode($val);exit;
    }
	
	function search_heatmap()
    {
		$markers = [];
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
		
		
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('heatmap/search_heatmap'), 'page' => lang('city_ride_cab_available')));
        $meta = array('page_title' => lang('search_heatmap'), 'bc' => $bc);    
        $this->page_construct('heatmap/search_heatmap', $meta, $this->data);
    }
	
	function getSearch(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data['distance'] = $this->Settings->search_kilometer;
		$data['latitude'] = $_GET['lat'];
		$data['longitude'] = $_GET['lng'];
         
        $locations = $this->heatmap_model->getSearch($data);
		if(!empty($locations) && $locations != NULL){
			foreach($locations as $loc){
				
				$val[] = array(
					//'driver_id' => $loc->id,
					'lat' => (float)$loc->current_latitude,
					'lng' => (float)$loc->current_longitude,
					//'address' => 'test',
					//'icon' => 'http://13.233.9.134/themes/default/admin/assets/images/search.png'
				);
				
			}
		}else{
			$val = array();
		}
        echo json_encode($val);exit;
    }
	
	/*#### Rental cab */
	function available_rental()
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
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('heatmap/available_rental'), 'page' => lang('Rental - Cab Available')));
        $meta = array('page_title' => lang('Rental - Cab Available'), 'bc' => $bc);    
        $this->page_construct('heatmap/available_rental', $meta, $this->data);
    }
	
    function getTrackingRental(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data['distance'] = $this->Settings->search_kilometer;
		$data['latitude'] = $_GET['lat'];
		$data['longitude'] = $_GET['lng'];
         
        $locations = $this->heatmap_model->getTracking($data);
		if(!empty($locations) && $locations != NULL){
			foreach($locations as $loc){
				
				$val[] = array(
					'driver_id' => $loc->id,
					'lat' => $loc->current_latitude,
					'lng' => $loc->current_longitude,
					'address' => $this->findLocationWEB($loc->current_latitude, $loc->current_longitude),
					'icon' => 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
				);
				
			}
		}else{
			$val = array();
		}
        echo json_encode($val);exit;
    }
	
	/*#### Outstation cab */
	function available_outstation()
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
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('heatmap/available_outstation'), 'page' => lang('Outstation - Cab Available')));
        $meta = array('page_title' => lang('Outstation - Cab Available'), 'bc' => $bc);    
        $this->page_construct('heatmap/available_outstation', $meta, $this->data);
    }
	
    function getTrackingOutstation(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data['distance'] = $this->Settings->search_kilometer;
		$data['latitude'] = $_GET['lat'];
		$data['longitude'] = $_GET['lng'];
         
        $locations = $this->heatmap_model->getTracking($data);
		if(!empty($locations) && $locations != NULL){
			foreach($locations as $loc){
				
				$val[] = array(
					'driver_id' => $loc->id,
					'lat' => $loc->current_latitude,
					'lng' => $loc->current_longitude,
					'address' => $this->findLocationWEB($loc->current_latitude, $loc->current_longitude),
					'icon' => 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
				);
				
			}
		}else{
			$val = array();
		}
        echo json_encode($val);exit;
    }
	
	function findLocationWEB($latitude, $longitude){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$geolocation = $latitude.','.$longitude;
		$request = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8&latlng='.$geolocation.'&sensor=false'; 
		$file_contents = file_get_contents($request);
		$json_decode = json_decode($file_contents);
		if(isset($json_decode->results[0]->formatted_address)){
			return $json_decode->results[0]->formatted_address;
		}
		return false;
	}
	
  
}
