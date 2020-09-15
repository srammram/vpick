<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Map extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
       // $this->lang->admin_load('map', $this->Settings->user_language);
        $this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
        $this->load->admin_model('map_model');
    }

    function drivers($action = NULL)
    {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('map/drivers'), 'page' => lang('drivers')), array('link' => '#', 'page' => lang('add_taxi')));
        $meta = array('page_title' => lang('drivers'), 'bc' => $bc);       
        $this->page_construct('map/drivers', $meta, $this->data);
        
        //$drivers = $this->map_model->getDrivers();
        //$this->load->view($this->theme . 'map/drivers', $meta, $this->data);
    }
    function getDrivers(){
        $distance = 10;
        $radius = 3959;//6371;
        $lat  = $this->input->post('lat');//34.0522342;
        $lng = $this->input->post('lng');//-118.2436849;
        // latitude boundaries
        $data['maxlat'] = $lat + rad2deg($distance / $radius);
        $data['minlat'] = $lat - rad2deg($distance / $radius);
        
        // longitude boundaries (longitude gets smaller when latitude increases)
        $data['maxlng'] = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
        $data['minlng'] = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
        
        //echo '$maxlat'.$maxlat;echo '$minlat'.$minlat;echo '$maxlng'.$maxlng;echo '$minlng'.$minlng;
        //$maxlat 13.152347160592 $minlat 12.972482839408 $maxlng 80.254531018944 $minlng 80.069888981056
        //$maxlat34.142166360592 $minlat 33.962302039408 $maxlng -118.13514032224 $minlng -118.35222947776
        
        
        
        //$latN = rad2deg(asin(sin(deg2rad($lat)) * cos($distance / $radius)
        //+ cos(deg2rad($lat)) * sin($distance / $radius) * cos(deg2rad(0))));
        //
        //$latS = rad2deg(asin(sin(deg2rad($lat)) * cos($distance / $radius)
        //        + cos(deg2rad($lat)) * sin($distance / $radius) * cos(deg2rad(180))));
        //
        //$lonE = rad2deg(deg2rad($lng) + atan2(sin(deg2rad(90))
        //        * sin($distance / $radius) * cos(deg2rad($lat)), cos($distance / $radius)
        //        - sin(deg2rad($lat)) * sin(deg2rad($latN))));
        //
        //$lonW = rad2deg(deg2rad($lng) + atan2(sin(deg2rad(270))
        //* sin($distance / $radius) * cos(deg2rad($lat)), cos($distance / $radius)
        //- sin(deg2rad($lat)) * sin(deg2rad($latN))));
        //
        //$data['maxlat'] = $latN;
        //$data['minlat'] = $latS;
        //
        //$data['maxlng'] = $lonE;
        //$data['minlng'] = $lonW;
        echo '<pre>';
		print_r($data);
		die;
        $drivers['data'] = $this->map_model->getDrivers_radius($data);
        echo json_encode($drivers);exit;
    }
    //function drivers_b($action = NULL)
    //{
    //    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
    //    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('map/drivers'), 'page' => lang('drivers')), array('link' => '#', 'page' => lang('add_taxi')));
    //    $meta = array('page_title' => lang('drivers'), 'bc' => $bc);       
    //    $this->page_construct('map/drivers_b', $meta, $this->data);
    //    
    //    //$drivers = $this->map_model->getDrivers();
    //    //$this->load->view($this->theme . 'map/drivers_b', $meta, $this->data);
    //}
}
