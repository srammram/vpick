<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$this->lang->admin_load('locations', $this->Settings->user_language);
		$this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
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
		$this->load->admin_model('locations_model');
		$this->load->admin_model('masters_model');
    }
	
	/*###### Daily*/
    function daily($action=false){
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('city_ride_fares')));
        $meta = array('page_title' => lang('city_ride_fares'), 'bc' => $bc);
        $this->page_construct('locations/daily', $meta, $this->data);
    }
    function getDailyFares(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		/*if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('locations/edit_daily/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = "";
		}*/
        $this->datatables
            ->select("{$this->db->dbprefix('daily_fare')}.id as id, tt.name as taxi_type_name, a.name as area_name, c.name as city_name, s.name as state_name, cc.name as country_name, {$this->db->dbprefix('daily_fare')}.base_min_distance_price, {$this->db->dbprefix('daily_fare')}.base_per_distance_price, {$this->db->dbprefix('daily_fare')}.no_of_driver_cancel, {$this->db->dbprefix('daily_fare')}.driver_cancel_charge, {$this->db->dbprefix('daily_fare')}.no_of_customer_cancel, {$this->db->dbprefix('daily_fare')}.customer_cancel_charge, {$this->db->dbprefix('daily_fare')}.cancel_free_second, {$this->db->dbprefix('daily_fare')}.status as status, {$this->db->dbprefix('daily_fare')}.is_default as is_default, country.name as instance_country ")
            ->from("daily_fare")
			->join("countries country", " country.iso = daily_fare.is_country", "left")
			->join("areas a", "a.id = daily_fare.area_id ", "left")
			->join("cities  c", "c.id = daily_fare.city_id ", 'left')
			->join("taxi_type  tt", "tt.id = daily_fare.taxi_type ")
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left')
			->where('daily_fare.is_delete', 0)
			->where("daily_fare.is_default !=", 2);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('daily_fare')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('daily_fare')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("daily_fare.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("daily_fare.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('locations/view_daily/$1') . "' class='tip' title='" . lang("view") . "'>view</a> ".$edit."</div>", "id");
			$edit = "<a href='" . admin_url('locations/edit_daily/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/daily_fare/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
        
		$this->datatables->unset_column('id');
		echo $this->datatables->generate();
    }
    function add_daily(){
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
		
		//$this->form_validation->set_rules('city_id', lang("city"), 'required|is_unique[daily_fare.city_id]');  
		$this->form_validation->set_rules('taxi_type', lang("cab_type"), 'required');  
		
		$taxi_type = $this->input->post('taxi_type');
		
		
		
		if($this->input->post('is_base') == 1){
			$this->form_validation->set_rules('base_min_distance', lang("base_min_distance"), 'required');  
			$this->form_validation->set_rules('base_min_distance_price', lang("base_min_distance_price"), 'required');  
			$this->form_validation->set_rules('base_per_distance', lang("base_per_distance"), 'required');  
			$this->form_validation->set_rules('base_per_distance_price', lang("base_per_distance_price"), 'required');  
		}
		
		
		
		
        if ($this->form_validation->run() == true) {
			
			if($this->input->post('city_id') == 0){
				$is_default = 1;
			}else{
				$is_default = 0;
			}
			
			if($this->input->post('is_base') == 0 && $this->input->post('is_peak') == 0 && $this->input->post('is_night') == 0){
				$this->session->set_flashdata('error', lang("fare_time_not_selected_please_selected_any_one"));
				admin_redirect('locations/add_daily');
			}
			
			
			
			if(!empty($_POST['area_id'])){
				for($i=0; $i<count($_POST['area_id']); $i++){
					$data[] = array(
						'city_id' => $this->input->post('city_id'),
						'area_id' => $_POST['area_id'][$i],
						'taxi_type' => $this->input->post('taxi_type'),
						'base_min_distance' => $this->input->post('base_min_distance'),
						'base_min_distance_price' => $this->input->post('base_min_distance_price'),
						'base_price_type' => $this->input->post('base_price_type'),
						'base_price_value' => $this->input->post('base_percentage_value'),
						'base_per_distance' => $this->input->post('base_per_distance'),
						'base_per_distance_price' => $this->input->post('base_per_distance_price'),
						
						'no_of_driver_cancel' => $this->input->post('no_of_driver_cancel'),
						'driver_cancel_charge' => $this->input->post('driver_cancel_charge'),
						'no_of_customer_cancel' => $this->input->post('no_of_customer_cancel'),
						'customer_cancel_charge' => $this->input->post('customer_cancel_charge'),
						'cancel_free_second' => $this->input->post('cancel_free_second'),
						
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'created_on' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode,
					);
				}
			}else{
				$data[] = array(
						'city_id' => 0,
						'area_id' => 0,
						'taxi_type' => $this->input->post('taxi_type'),
						'base_min_distance' => $this->input->post('base_min_distance'),
						'base_min_distance_price' => $this->input->post('base_min_distance_price'),
						'base_price_type' => $this->input->post('base_price_type'),
						'base_price_value' => $this->input->post('base_percentage_value'),
						'base_per_distance' => $this->input->post('base_per_distance'),
						'base_per_distance_price' => $this->input->post('base_per_distance_price'),
						
						'no_of_driver_cancel' => $this->input->post('no_of_driver_cancel'),
						'driver_cancel_charge' => $this->input->post('driver_cancel_charge'),
						'no_of_customer_cancel' => $this->input->post('no_of_customer_cancel'),
						'customer_cancel_charge' => $this->input->post('customer_cancel_charge'),
						'cancel_free_second' => $this->input->post('cancel_free_second'),
						
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'is_country' => $countryCode,
						'created_on' => date('Y-m-d H:i:s')
					);
			}
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_price_type']); $i++){
					
					if($_POST['peak_price_type'][$i] == 1){
						$peek_per_distance_price = $_POST['peak_min_distance_price'][$i] * ($_POST['peak_price_value'][$i] / 100);
					}else{
						$peek_per_distance_price = $_POST['peak_price_value'][$i];
					}
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'price_type' => $_POST['peak_price_type'][$i],
						'min_fare' => $_POST['peak_percentage_value'][$i],
						'include_fare' => $_POST['peak_min_distance_price'][$i],
						'per_fare' => $_POST['peak_percentage_value'][$i],
						'extra_fare' => $_POST['peek_per_distance_price'][$i],
						
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
			if($this->input->post('is_night') == 1){
				for($i=0; $i<count($_POST['night_price_type']); $i++){
					
					if($_POST['night_price_type'][$i] == 1){
						$night_per_distance_price = $_POST['night_min_distance_price'][$i] * ($_POST['night_price_value'][$i] / 100);
					}else{
						$night_per_distance_price = $_POST['night_price_value'][$i];
					}
					
					$slot_array[] = array(
						'start_time' => $_POST['night_start_hours'][$i].':'.$_POST['night_start_minutes'][$i].':00',
						'end_time' => $_POST['night_end_hours'][$i].':'.$_POST['night_end_minutes'][$i].':00',
						'price_type' => $_POST['night_price_type'][$i],
						'min_fare' => $_POST['night_percentage_value'][$i],
						'include_fare' => $_POST['night_min_distance_price'][$i],
						'per_fare' => $_POST['night_percentage_value'][$i],
						'extra_fare' => $_POST['night_per_distance_price'][$i],
						
						'type' => 2,
						'is_country' => $countryCode,
					);
				}
			}
						
        }
		
		
        if ($this->form_validation->run() == true && $this->locations_model->add_daily($data, $slot_array, $_POST['area_id'], $is_default, $taxi_type, $countryCode)){
			
            $this->session->set_flashdata('message', lang("city_ride_added"));
            admin_redirect('locations/daily');
        } else {
			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('location/daily'), 'page' => lang('city_ride_fares')), array('link' => '#', 'page' => lang('add_city_ride_fares')));
            $meta = array('page_title' => lang('add_city_ride_fares'), 'bc' => $bc);
			$this->data['zones'] = $this->locations_model->getALLZonebyCountry($countryCode);
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->page_construct('locations/add_daily', $meta, $this->data);
			
  
        }
    }
    function edit_daily($id){
		$result = $this->locations_model->getDailyby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $result->is_country ? $result->is_country : $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		if($this->input->post('edit_daily')){
		
		$this->form_validation->set_rules('taxi_type', lang("cab_type"), 'required');  
		
		if($this->input->post('city_id') == 0 ){
			$is_default = 1;
		}else{
			$is_default = 0;
			
		}
		
		if($this->input->post('is_base') == 0 && $this->input->post('is_peak') == 0 && $this->input->post('is_night') == 0){
			$this->session->set_flashdata('error', lang("fare_time_not_selected_please_selected_any_one"));
			admin_redirect('locations/edit_daily/'.$id.'');
		}
		
		if($this->input->post('is_base') == 1){
			$this->form_validation->set_rules('base_min_distance', lang("base_min_distance"), 'required');  
			$this->form_validation->set_rules('base_min_distance_price', lang("base_min_distance_price"), 'required');  
			$this->form_validation->set_rules('base_per_distance', lang("base_per_distance"), 'required');  
			$this->form_validation->set_rules('base_per_distance_price', lang("base_per_distance_price"), 'required');  
		}
		
		
		
        if ($this->form_validation->run() == true) {
			
			
			
			
           if(!empty($_POST['area_id'])){
				
					$data[] = array(
						'city_id' => $this->input->post('city_id'),
						'area_id' => $this->input->post('area_id'),
						'taxi_type' => $this->input->post('taxi_type'),
						'base_min_distance' => $this->input->post('base_min_distance'),
						'base_min_distance_price' => $this->input->post('base_min_distance_price'),
						'base_price_type' => $this->input->post('base_price_type'),
						'base_price_value' => $this->input->post('base_percentage_value'),
						'base_per_distance' => $this->input->post('base_per_distance'),
						'base_per_distance_price' => $this->input->post('base_per_distance_price'),
						
						'no_of_driver_cancel' => $this->input->post('no_of_driver_cancel'),
						'driver_cancel_charge' => $this->input->post('driver_cancel_charge'),
						'no_of_customer_cancel' => $this->input->post('no_of_customer_cancel'),
						'customer_cancel_charge' => $this->input->post('customer_cancel_charge'),
						'cancel_free_second' => $this->input->post('cancel_free_second'),
						
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'created_on' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode,
					);
				
			}else{
				$data[] = array(
						'city_id' => 0,
						'area_id' => 0,
						'taxi_type' => $this->input->post('taxi_type'),
						'base_min_distance' => $this->input->post('base_min_distance'),
						'base_min_distance_price' => $this->input->post('base_min_distance_price'),
						'base_price_type' => $this->input->post('base_price_type'),
						'base_price_value' => $this->input->post('base_percentage_value'),
						'base_per_distance' => $this->input->post('base_per_distance'),
						'base_per_distance_price' => $this->input->post('base_per_distance_price'),
						
						'no_of_driver_cancel' => $this->input->post('no_of_driver_cancel'),
						'driver_cancel_charge' => $this->input->post('driver_cancel_charge'),
						'no_of_customer_cancel' => $this->input->post('no_of_customer_cancel'),
						'customer_cancel_charge' => $this->input->post('customer_cancel_charge'),
						'cancel_free_second' => $this->input->post('cancel_free_second'),
						
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'is_country' => $countryCode,
						'created_on' => date('Y-m-d H:i:s')
					);
			}
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_price_type']); $i++){
					
					if($_POST['peak_price_type'][$i] == 1){
						$peek_per_distance_price = $_POST['peak_min_distance_price'][$i] * ($_POST['peak_price_value'][$i] / 100);
					}else{
						$peek_per_distance_price = $_POST['peak_price_value'][$i];
					}
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'price_type' => $_POST['peak_price_type'][$i],
						'min_fare' => $_POST['peak_percentage_value'][$i],
						'include_fare' => $_POST['peak_min_distance_price'][$i],
						'per_fare' => $_POST['peak_percentage_value'][$i],
						'extra_fare' => $_POST['peek_per_distance_price'][$i],
						
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
			if($this->input->post('is_night') == 1){
				for($i=0; $i<count($_POST['night_price_type']); $i++){
					
					
					$slot_array[] = array(
						'start_time' => $_POST['night_start_hours'][$i].':'.$_POST['night_start_minutes'][$i].':00',
						'end_time' => $_POST['night_end_hours'][$i].':'.$_POST['night_end_minutes'][$i].':00',
						'price_type' => $_POST['night_price_type'][$i],
						'min_fare' => $_POST['night_percentage_value'][$i],
						'include_fare' => $_POST['night_min_distance_price'][$i],
						'per_fare' => $_POST['night_percentage_value'][$i],
						'extra_fare' => $_POST['night_per_distance_price'][$i],
						
						'type' => 2,
						'is_country' => $countryCode,
					);
				}
			}
			
			
        } elseif ($this->input->post('edit_daily')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("locations/edit_daily/".$id);
        }
		}

        if ($this->form_validation->run() == true && $this->locations_model->update_daily($id, $data, $slot_array, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("city_ride_updated"));
            admin_redirect("locations/daily");
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			if($result->country_id != 0){
				$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			}else{
				$this->data['zones'] = $this->locations_model->getALLZonebyCountry($countryCode);
			}
			
			
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['result'] = $result;
			$this->data['peek_slot'] = $this->locations_model->getPeekfare($id, $countryCode);
			$this->data['night_slot'] = $this->locations_model->getNightfare($id, $countryCode);
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_daily', $this->data);
			 $this->page_construct('locations/edit_daily', $meta, $this->data);
        }
    }
	
	function getPeek(){
		$this->data['peek_fare_count'] = $this->input->get('peek_fare_count');
		$html = '';
		$html .= $this->load->view($this->theme . 'locations/peek_fare', $this->data);
		echo $html;			
	}
	function getNight(){
		$this->data['night_fare_count'] = $this->input->get('night_fare_count');
		$html = '';
		$html .= $this->load->view($this->theme . 'locations/night_fare', $this->data);
		echo $html;			
	}
	
	
	function view_daily($id){
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
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['result'] = $this->locations_model->getDailyby_ID($id, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('city_ride_fares')));
        $meta = array('page_title' => lang('city_ride_fares'), 'bc' => $bc);
        $this->page_construct('locations/view_daily', $meta, $this->data);
	}
    function daily_status($status,$id){
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
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->locations_model->update_daily_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Rental*/
    function rental($action=false){
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('rental')));
        $meta = array('page_title' => lang('rental'), 'bc' => $bc);
        $this->page_construct('locations/rental', $meta, $this->data);
    }
    function getRentalFares(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
        $this->load->library('datatables');
		/*if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('locations/edit_rental/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = "";
		}*/
        $this->datatables
            ->select("{$this->db->dbprefix('rental_fare')}.id as id, tt.name as taxi_type, a.name as area_name,  c.name as city_name, s.name as state_name, cc.name as country_name,  {$this->db->dbprefix('rental_fare')}.package_name,   {$this->db->dbprefix('rental_fare')}.package_price, {$this->db->dbprefix('rental_fare')}.status as status, {$this->db->dbprefix('rental_fare')}.is_default as is_default, country.name as instance_country ")
            ->from("rental_fare")
			->join("countries country", " country.iso = rental_fare.is_country", "left")
			->join("areas a", "a.id = rental_fare.area_id ", 'left')
			->join("cities  c", "c.id = rental_fare.city_id ", 'left')
			->join("taxi_type  tt", "tt.id = rental_fare.taxi_type ")
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left')
			->where('rental_fare.is_delete', 0)
			->where("rental_fare.is_default !=", 2);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rental_fare')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rental_fare')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rental_fare.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rental_fare.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('locations/view_rental/$1') . "' class='tip' title='" . lang("view") . "'>view</a> ".$edit."</div>", "id");
			$edit = "<a href='" . admin_url('locations/edit_rental/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/rental_fare/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
		echo $this->datatables->generate();
    }
    function add_rental(){
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
		//$this->form_validation->set_rules('city_id', lang("city"), 'required|is_unique[rental_fare.city_id]');  
		
		$this->form_validation->set_rules('taxi_type', lang("cab_type"), 'required');  
		$taxi_type = $this->input->post('taxi_type');
		if($this->input->post('city_id') == 0){
			$is_default = 1;
		}else{
			$is_default = 0;
			
		}
		

		
		
        if ($this->form_validation->run() == true) {
			
			
			if($this->input->post('city_id') == 0){
				$is_default = 1;
			}else{
				$is_default = 0;
			}
			
			if($this->input->post('is_base') == 0 && $this->input->post('is_peak') == 0 && $this->input->post('is_night') == 0){
				$this->session->set_flashdata('error', lang("fare_time_not_selected_please_selected_any_one"));
				admin_redirect('locations/add_rental');
			}
			
			
			
			if(!empty($_POST['area_id'])){
				for($i=0; $i<count($_POST['area_id']); $i++){
					$data[] = array(
						'city_id' => $this->input->post('city_id'),
						'area_id' => $_POST['area_id'][$i],
						'taxi_type' => $this->input->post('taxi_type'),
						'package_name' => $this->input->post('package_name'),
						'package_price' => $this->input->post('package_price'),
						'package_distance' => $this->input->post('package_distance'),
						'package_time' => $this->input->post('package_time'),
						'option_type' => $this->input->post('option_type'),
						'per_distance' => 1,
						'per_distance_price' => $this->input->post('per_distance_price'),
						'per_time' => '01:00:00',
						'per_time_price' => $this->input->post('per_time_price'),
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'created_on' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode,
					);
				}
			}else{
				$data[] = array(
						'city_id' => 0,
						'area_id' => 0,
						'taxi_type' => $this->input->post('taxi_type'),
						'package_name' => $this->input->post('package_name'),
						'package_price' => $this->input->post('package_price'),
						'package_distance' => $this->input->post('package_distance'),
						'package_time' => $this->input->post('package_time'),
						'option_type' => $this->input->post('option_type'),
						'per_distance' => 1,
						'per_distance_price' => $this->input->post('per_distance_price'),
						'per_time' => '01:00:00',
						'per_time_price' => $this->input->post('per_time_price'),
						'is_base' => $this->input->post('is_base'),
						'is_peak' => $this->input->post('is_peak'),
						'is_night' => $this->input->post('is_night'),
						'status' => 1,
						'is_default' => $is_default,
						'is_country' => $countryCode,
						'created_on' => date('Y-m-d H:i:s')
					);
			}
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_package_type']); $i++){
					
					
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'package_type' => $_POST['peak_package_type'][$i],
						'include_fare' => $_POST['peak_package_price'][$i],
						'include_time_fare' => $_POST['peak_package_price'][$i],
						'extra_fare' => $_POST['peak_pre_distance_price'][$i],
						'extra_time_fare' => $_POST['peak_pre_time_price'][$i],
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
			if($this->input->post('is_night') == 1){
				for($i=0; $i<count($_POST['night_package_type']); $i++){
					
					
					$slot_array[] = array(
						'start_time' => $_POST['night_start_hours'][$i].':'.$_POST['night_start_minutes'][$i].':00',
						'end_time' => $_POST['night_end_hours'][$i].':'.$_POST['night_end_minutes'][$i].':00',
						'package_type' => $_POST['night_package_type'][$i],
						'include_fare' => $_POST['night_package_price'][$i],
						'include_time_fare' => $_POST['night_package_price'][$i],
						'extra_fare' => $_POST['night_pre_distance_price'][$i],
						'extra_time_fare' => $_POST['night_pre_time_price'][$i],
						'type' => 2,
						'is_country' => $countryCode,
					);
				}
			}
			
           
			
        }
		
        if ($this->form_validation->run() == true && $this->locations_model->add_rental($data, $slot_array, $_POST['area_id'], $is_default, $taxi_type, $countryCode)){
			
            $this->session->set_flashdata('message', lang("rental_added"));
            admin_redirect('locations/rental');
        } else {
			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('location/rental'), 'page' => lang('rental')), array('link' => '#', 'page' => lang('add_rental')));
            $meta = array('page_title' => lang('add_rental'), 'bc' => $bc);
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->page_construct('locations/add_rental', $meta, $this->data);
			
  
        }
    }
    function edit_rental($id){
		$result = $this->locations_model->getRentalby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $result->is_country ? $result->is_country : $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		
		
		
		if($this->input->post('city_id') == 0){
			$is_default = 1;
		}else{
			$is_default = 0;
			
		}
		
		$this->form_validation->set_rules('taxi_type', lang("cab_type"), 'required');  
		
		
        if ($this->form_validation->run() == true) {
			
			if($this->input->post('is_base') == 0 && $this->input->post('is_peak') == 0 && $this->input->post('is_night') == 0){
				$this->session->set_flashdata('error', lang("fare_time_not_selected_please_selected_any_one"));
				admin_redirect('locations/edit_rental/'.$id);
			}
			
			$data = array(
                'area_id' => $this->input->post('area_id'),
                'city_id' => $this->input->post('city_id'),
				'taxi_type' => $this->input->post('taxi_type'),
				'package_name' => $this->input->post('package_name'),
				'package_price' => $this->input->post('package_price'),
				'package_distance' => $this->input->post('package_distance'),
				'package_time' => $this->input->post('package_time'),
				'option_type' => $this->input->post('option_type'),
				'per_distance' => 1,
				'per_distance_price' => $this->input->post('per_distance_price'),
				'per_time' => '01:00:00',
				'per_time_price' => $this->input->post('per_time_price'),
				'is_base' => $this->input->post('is_base'),
				'is_peak' => $this->input->post('is_peak'),
				'is_night' => $this->input->post('is_night'),
				'is_default' => $is_default
            );
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_package_type']); $i++){
					
					
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'package_type' => $_POST['peak_package_type'][$i],
						'include_fare' => $_POST['peak_package_price'][$i],
						'include_time_fare' => $_POST['peak_package_price'][$i],
						'extra_fare' => $_POST['peak_pre_distance_price'][$i],
						'extra_time_fare' => $_POST['peak_pre_time_price'][$i],
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
			if($this->input->post('is_night') == 1){
				for($i=0; $i<count($_POST['night_package_type']); $i++){
					
					
					$slot_array[] = array(
						'start_time' => $_POST['night_start_hours'][$i].':'.$_POST['night_start_minutes'][$i].':00',
						'end_time' => $_POST['night_end_hours'][$i].':'.$_POST['night_end_minutes'][$i].':00',
						'package_type' => $_POST['night_package_type'][$i],
						'include_fare' => $_POST['night_package_price'][$i],
						'include_time_fare' => $_POST['night_package_price'][$i],
						'extra_fare' => $_POST['night_pre_distance_price'][$i],
						'extra_time_fare' => $_POST['night_pre_time_price'][$i],
						'type' => 2,
						'is_country' => $countryCode,
					);
				}
			}
			
        } elseif ($this->input->post('edit_rental')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("locations/edit_rental/".$id);
        }

        if ($this->form_validation->run() == true && $this->locations_model->update_rental($id, $data, $slot_array, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("rental_updated"));
            admin_redirect("locations/rental");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['result'] = $result;
			$this->data['peek_slot'] = $this->locations_model->getRentalPeekfare($id, $countryCode);
			$this->data['night_slot'] = $this->locations_model->getRentalNightfare($id, $countryCode);
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_rental', $this->data);
			$this->page_construct('locations/edit_rental', $meta, $this->data);
        }
    }
	
	function getrentalPeek(){
		$html = '';
		$html .= $this->load->view($this->theme . 'locations/rental_peek_fare', $this->data);
		echo $html;			
	}
	function getrentalNight(){
		$html = '';
		$html .= $this->load->view($this->theme . 'locations/rental_night_fare', $this->data);
		echo $html;			
	}
	
	function view_rental($id){
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
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['result'] = $this->locations_model->getRentalby_ID($id, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('rental')));
        $meta = array('page_title' => lang('rental'), 'bc' => $bc);
        $this->page_construct('locations/view_rental', $meta, $this->data);
	}
    function rental_status($status,$id){
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
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->locations_model->update_rental_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Outstation*/
    function outstation($action=false){
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('outstation')));
        $meta = array('page_title' => lang('outstation'), 'bc' => $bc);
        $this->page_construct('locations/outstation', $meta, $this->data);
    }
    function getOutstationFares(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
        $this->load->library('datatables');
		/*if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('locations/edit_outstation/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = "";
		}*/
        $this->datatables
            ->select("{$this->db->dbprefix('outstation_fare')}.id as id, tt.name as taxi_type, c.name as city_name, s.name as state_name, cc.name as country_name,    {$this->db->dbprefix('outstation_fare')}.package_name,   {$this->db->dbprefix('outstation_fare')}.status as status, {$this->db->dbprefix('outstation_fare')}.is_default as is_default, country.name as instance_country ")
            ->from("outstation_fare")
			->join("countries country", " country.iso = outstation_fare.is_country", "left")
			->join("cities  c", "c.id = outstation_fare.from_city_id ", 'left')
			->join("taxi_type  tt", "tt.id = outstation_fare.taxi_type ")
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left')
			->where('outstation_fare.is_delete', 0)
			->where("outstation_fare.is_default !=", 2);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('outstation_fare')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('outstation_fare')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("outstation_fare.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("outstation_fare.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('locations/view_outstation/$1') . "' class='tip' title='" . lang("view") . "'>view</a> ".$edit."</div>", "id");
			$edit = "<a href='" . admin_url('locations/edit_outstation/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/outstation_fare/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
		echo $this->datatables->generate();
    }
    function add_outstation(){
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
		//$this->form_validation->set_rules('city_id', lang("city"), 'required|is_unique[daily_fare.city_id]');  
		$this->form_validation->set_rules('taxi_type', lang("cab_type"), 'required'); 
		 
		$taxi_type = $this->input->post('taxi_type');
		
		
			if($this->input->post('from_city_id') == 0){
				$is_default = 1;
			}else{
				$is_default = 0;
				
			}
	 
		
		
		$this->form_validation->set_rules('package_name', lang("package_name"), 'required');  
		//$this->form_validation->set_rules('per_distance', lang("per_distance"), 'required');  
		//$this->form_validation->set_rules('per_distance_price', lang("per_distance_price"), 'required');  
		//$this->form_validation->set_rules('driver_allowance_per_day', lang("driver_allowance_per_day"), 'required');  
		//$this->form_validation->set_rules('driver_night_per_day', lang("driver_night_per_day"), 'required');  
		
        if ($this->form_validation->run() == true) {
			$is_way = $this->input->post('is_way');
			$is_oneway = $is_way == 1 ? 1 : 0;
			$is_twoway = $is_way == 2 ? 1 : 0;
			
			if($is_way == 1){
				$per_distance = $this->input->post('oneway_per_distance');
				$per_distance_price = $this->input->post('oneway_per_distance_price');
				$driver_allowance_per_day = $this->input->post('oneway_driver_allowance_per_day');
				$driver_night_per_day = $this->input->post('oneway_driver_night_per_day');
			}elseif($is_way == 2){
				$per_distance = $this->input->post('twoway_per_distance');
				$per_distance_price = $this->input->post('twoway_per_distance_price');
				$driver_allowance_per_day = $this->input->post('twoway_driver_allowance_per_day');
				$driver_night_per_day = $this->input->post('twoway_driver_night_per_day');
			}
			
            $data = array(
				
                'from_city_id' => $this->input->post('from_city_id'),
				'to_city_id' => $this->input->post('to_city_id') ? $this->input->post('to_city_id') : 0,
				'taxi_type' => $this->input->post('taxi_type'),
				'package_name' => $this->input->post('package_name') ? $this->input->post('package_name') : '',
				
				'oneway_package_price' => $is_way == 1 ? $this->input->post('oneway_package_price')  : '',
				'twoway_package_price' => $is_way == 2 ? $this->input->post('twoway_package_price')  : '',
				'oneway_distance' => $is_way == 1 ? $this->input->post('oneway_distance') : '',
				'twoway_distance' => $is_way == 2 ? $this->input->post('twoway_distance') : '',
				
				'is_oneway' => $is_oneway,
				'is_twoway' => $is_twoway,
				
				
				'per_distance' => $per_distance,
				'per_distance_price' => $per_distance_price,
				
				'driver_allowance_per_day' => $driver_allowance_per_day,
				'driver_night_per_day' => $driver_night_per_day,
                'status' => 1,
				'is_peak' => $this->input->post('is_peak'),
				'is_base' => $this->input->post('is_base'),
				'is_default' => $is_default,
				'created_on' => date('Y-m-d H:i:s')
            );
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_price_type']); $i++){
					
					
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'price_type' => $_POST['peak_price_type'][$i],
						'min_fare' => $_POST['peak_percentage_value'][$i],
						'include_fare' => $_POST['peak_min_distance_price'][$i],
						'per_fare' => $_POST['peak_percentage_value'][$i],
						'extra_fare' => $_POST['peek_per_distance_price'][$i],
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
        }
		
        if ($this->form_validation->run() == true && $this->locations_model->add_outstation($data, $slot_array, $is_default, $taxi_type, $countryCode)){
			
            $this->session->set_flashdata('message', lang("outstation_added"));
            admin_redirect('locations/outstation');
        } else {
			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('location/outstation'), 'page' => lang('outstation')), array('link' => '#', 'page' => lang('add_outstation')));
            $meta = array('page_title' => lang('add_outstation'), 'bc' => $bc);
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->page_construct('locations/add_outstation', $meta, $this->data);
			
  
        }
    }
	
	function getoutstationPeek(){
		$this->data['peek_fare_count'] = $this->input->get('peek_fare_count');
		$html = '';
		$html .= $this->load->view($this->theme . 'locations/outstation_peek_fare', $this->data);
		echo $html;			
	}
	
    function edit_outstation($id){
		$result = $this->locations_model->getOutstationby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $result->is_country ? $result->is_country : $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		
		
		if($this->input->post('from_city_id') == 0){
			$is_default = 1;
		}else{
			$is_default = 0;
			$check_daily = $this->locations_model->checkTypewiseCityOutstation($this->input->post('taxi_type'), $this->input->post('from_city_id'), $this->input->post('to_city_id'), $countryCode);
			if($check_daily == 1){
				$this->session->set_flashdata('error', lang("already_exit_cab_type_in_same_city"));
				admin_redirect('locations/edit_outstation/'.$id);
			}
		}
		
		$this->form_validation->set_rules('taxi_type', lang("taxi_type"), 'required');  
				
        if ($this->form_validation->run() == true) {
			
			$is_way = $this->input->post('is_way');
			$is_oneway = $is_way == 1 ? 1 : 0;
			$is_twoway = $is_way == 2 ? 1 : 0;
			
			if($is_way == 1){
				$per_distance = $this->input->post('oneway_per_distance');
				$per_distance_price = $this->input->post('oneway_per_distance_price');
				$driver_allowance_per_day = $this->input->post('oneway_driver_allowance_per_day');
				$driver_night_per_day = $this->input->post('oneway_driver_night_per_day');
			}elseif($is_way == 2){
				$per_distance = $this->input->post('twoway_per_distance');
				$per_distance_price = $this->input->post('twoway_per_distance_price');
				$driver_allowance_per_day = $this->input->post('twoway_driver_allowance_per_day');
				$driver_night_per_day = $this->input->post('twoway_driver_night_per_day');
			}
			
            $data = array(
				
                'from_city_id' => $this->input->post('from_city_id'),
				'to_city_id' => $this->input->post('to_city_id') ? $this->input->post('to_city_id') : 0,
				'taxi_type' => $this->input->post('taxi_type'),
				'package_name' => $this->input->post('package_name') ? $this->input->post('package_name') : '',
				
				'oneway_package_price' => $is_way == 1 ? $this->input->post('oneway_package_price')  : '',
				'twoway_package_price' => $is_way == 2 ? $this->input->post('twoway_package_price')  : '',
				'oneway_distance' => $is_way == 1 ? $this->input->post('oneway_distance') : '',
				'twoway_distance' => $is_way == 2 ? $this->input->post('twoway_distance') : '',
				
				'is_oneway' => $is_oneway,
				'is_twoway' => $is_twoway,
				
				
				'per_distance' => $per_distance,
				'per_distance_price' => $per_distance_price,
				'is_peak' => $this->input->post('is_peak'),
				'is_base' => $this->input->post('is_base'),
				'driver_allowance_per_day' => $driver_allowance_per_day,
				'driver_night_per_day' => $driver_night_per_day,
				'is_default' => $is_default,
				'created_on' => date('Y-m-d H:i:s')
            );
			
			if($this->input->post('is_peak') == 1){
				for($i=0; $i<count($_POST['peak_price_type']); $i++){
					
					
					
					$slot_array[] = array(
						'start_time' => $_POST['peak_start_hours'][$i].':'.$_POST['peak_start_minutes'][$i].':00',
						'end_time' => $_POST['peak_end_hours'][$i].':'.$_POST['peak_end_minutes'][$i].':00',
						'price_type' => $_POST['peak_price_type'][$i],
						'min_fare' => $_POST['peak_percentage_value'][$i],
						'include_fare' => $_POST['peak_min_distance_price'][$i],
						'per_fare' => $_POST['peak_percentage_value'][$i],
						'extra_fare' => $_POST['peek_per_distance_price'][$i],
						'type' => 1,
						'is_country' => $countryCode,
					);
				}
			}
			
        } elseif ($this->input->post('edit_outstation')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("locations/edit_outstation/".$id);
        }

        if ($this->form_validation->run() == true && $this->locations_model->update_outstation($id, $data, $slot_array, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("outstation_updated"));
            admin_redirect("locations/outstation");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			/*$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);*/
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->local_continent_id);
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result->local_state_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result->permanent_state_id);
			
			$this->data['taxi_types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['result'] = $result;
			$this->data['peek_slot'] = $this->locations_model->getOutstationPeekfare($id, $countryCode);
			
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_outstation', $this->data);
			 $this->page_construct('locations/edit_outstation', $meta, $this->data);
        }
    }
   	
	function view_outstation($id){
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
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['result'] = $this->locations_model->getOutstationby_ID($id, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('outstation')));
        $meta = array('page_title' => lang('outstation'), 'bc' => $bc);
        $this->page_construct('locations/view_outstation', $meta, $this->data);
	}
    function outstation_status($status,$id){
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
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->locations_model->update_outstation_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
   
    function daily_actions($wh = NULL)
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
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('daily');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('cab_type'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('city'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('state'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('package_name'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('package_price'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:H1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->locations_model->getALLDailyE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->taxi_type_name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->city_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->state_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->country_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->base_min_distance_price);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->base_per_distance_price);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'daily_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function rental_actions($wh = NULL)
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
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Rental');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('cab_type'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('city'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('state'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('package_name'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('package_price'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:H1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->locations_model->getALLRentalE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->taxi_type);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->city_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->state_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->country_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->package_name);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->package_price);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'rental_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function outstation_actions($wh = NULL)
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
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Outstation');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('cab_type'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('city'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('state'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('package_name'));
					
					$this->excel->getActiveSheet()
						->getStyle('A1:H1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->locations_model->getALLOutstationE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->taxi_type);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->city_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->state_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->country_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->package_name);
						
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
					 
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'outstation_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
}
