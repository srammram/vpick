<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
	$this->model = 'master_model';
        $this->load->admin_model($this->model);
    }
    function getApproxFare(){
	$model = $this->model;
	$pickup_lat = $this->input->post('orig_latitude');//$this->input->post('latitude');//'12.830551';
	$pickup_lng = $this->input->post('orig_longitude');//$this->input->post('longitude');//'80.048280';	
	$dropoff_lat = $this->input->post('dest_latitude');//'12.973731';
	$dropoff_lng = $this->input->post('dest_longitude');//'80.221726';
	$taxi_type = $this->input->post('taxi_type');
	$ride_type = $this->input->post('ride_type');
	$dis_duration = $this->calc_distance($pickup_lat,$pickup_lng,$dropoff_lat,$dropoff_lng);
	//print_R($dis_duration);
	if($ride_type=='cityrides') :
	$result = $this->$model->getApproxFare_cityrides($pickup_lat,$pickup_lng,$taxi_type,$dis_duration['distance_km'],$dis_duration['distance_mi']);
	elseif($ride_type=='rental') :
	$result = $this->$model->getApproxFare_rental($pickup_lat,$pickup_lng,$taxi_type,$dis_duration['distance_km'],$dis_duration['distance_mi']);
	elseif($ride_type=='outstation') :
	$trip_type = "oneway";$result = $this->$model->getApproxFare_outstation($pickup_lat,$pickup_lng,$taxi_type,$dis_duration['distance_km'],$dis_duration['distance_mi'],$trip_type,$dis_duration['dest_addr']);
	endif; 
	echo json_encode($result);
	//print_R($result);
	//SELECT id, ( 3959 * acos( cos( radians(10.856775) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(78.696497) ) + sin( radians(10.856775) ) * sin( radians( latitude ) ) ) ) AS distance FROM kapp_cities HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
    }
    function calc_distance($lat1,$lng1,$lat2,$lng2){
	$origin = $lat1.','.$lng1;
	$dest = $lat2.','.$lng2;
	$response = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.$origin.'&destinations='.$dest);
	$response = json_decode($response);
	//echo '<pre>';print_R($response);
	$return['distance_km'] = false;
	$return['distance_mi'] = (isset($response->rows[0]->elements[0]->distance))?$response->rows[0]->elements[0]->distance->text:false;
	$return['duration'] = (isset($response->rows[0]->elements[0]->duration))?$response->rows[0]->elements[0]->duration->text:false;
	if($return['distance_mi']){
	    $return['distance_km'] = trim($return['distance_mi'],' mi')/0.62137;
	}
	$address = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$dest);
	$address = json_decode($address);//echo '<pre>';print_R($address);
	$return['dest_addr'] = false;
	if(!empty($address)){ //echo '<pre>';print_R($address->results[0]);
	    foreach($address->results[0]->address_components as $k => $row){
		if($row->types[0]=="locality"){
		    $return['dest_addr'][] = $row->long_name;
		    $return['dest_addr'][] = $row->short_name;
		}
	    }
	}
	
	return $return;
    }
    function promotions(){
	
    }
    function send_emailNotification(){
	
    }
    function send_smsNotification(){
	
    }

    
}
