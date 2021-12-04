<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verification extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$this->lang->admin_load('people', $this->Settings->user_language);
		$this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
		$this->load->helper(array('form', 'url'));
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->load->library('upload');
		$this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('verification_model');
		$this->load->admin_model('masters_model');
    }
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
    function index($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('verification')));
		$this->data['url_data'] = $this->verification_model->getUrldata();
        $meta = array('page_title' => lang('verification'), 'bc' => $bc);
        $this->page_construct('verification/index', $meta, $this->data);
    }
	
	function driver($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
        $this->page_construct('verification/driver', $meta, $this->data);
    }
	
	function getDriver(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, If({$this->db->dbprefix('users')}.is_approved = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ")
			->where("users.group_id", $group_id)
			->where("users.is_approved", 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
        echo $this->datatables->generate();
    }
	
	function driver_status($id){
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
		$this->form_validation->set_rules('is_approved', $this->lang->line("is_approved"), 'required');
		if ($this->form_validation->run() == true) {
			
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			$udata = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			
			if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$data['photo'] = 'user/driver/'.$photo;
				$udata['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}
			
			
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_driver_status($id, $data, $udata, $countryCode)){
			if($this->input->post('is_approved') == 1){
				$notification['title'] = 'Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 2;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
				$this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            	admin_redirect('verification/driver');
			}else{
            	$this->session->set_flashdata('error', $this->input->post('verification_first_name').' details has been not verified');
            	admin_redirect('verification/driver');
			}
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['action'] = $action;
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
			$meta = array('page_title' => lang('driver'), 'bc' => $bc);
			$this->data['result'] = $this->verification_model->getUserDetails($id);
			$this->data['id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->page_construct('verification/driver_status', $meta, $this->data);
		}
    }
	
	function vendor($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
        $meta = array('page_title' => lang('vendor'), 'bc' => $bc);
        $this->page_construct('verification/vendor', $meta, $this->data);
    }
	
	function getVendor(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Vendor;
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, {$this->db->dbprefix('users')}.is_approved as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ")
			->where("users.group_id", $group_id)
			->where("users.is_approved", 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
        echo $this->datatables->generate();
    }
	
	function vendor_status($id){
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
		$this->form_validation->set_rules('is_approved', $this->lang->line("is_approved"), 'required');
		if ($this->form_validation->run() == true) {
			
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			$udata = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			
			if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/driver/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$data['photo'] = 'user/driver/'.$photo;
				$udata['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}
			
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_vendor_status($id, $data, $udata, $countryCode)){
			if($this->input->post('is_approved') == 1){
				$notification['title'] = 'Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 3;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
				$this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            	admin_redirect('verification/vendor');
			}else{
            	$this->session->set_flashdata('error', $this->input->post('verification_first_name').' details has been not verified');
            	admin_redirect('verification/vendor');
			}
			
           
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['action'] = $action;
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
			$meta = array('page_title' => lang('vendor'), 'bc' => $bc);
			$this->data['result'] = $this->verification_model->getUserDetails($id);
			$this->data['id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->page_construct('verification/vendor_status', $meta, $this->data);
		}
    }
	
	function address($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('address')));
        $meta = array('page_title' => lang('address'), 'bc' => $bc);
        $this->page_construct('verification/address', $meta, $this->data);
    }
	
	function getAddress(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name, CONCAT(ud.local_address, ' ', la.name, ' ', lcity.name, ' ', ls.name, ' ', lz.name, ' ', lcc.name, ' ', lc.name) as local_address, CONCAT(ud.permanent_address, ' ', pa.name, ' ', pcity.name, ' ', ps.name, ' ', pz.name, ' ', pcc.name, ' ', pc.name) as permanent_address,   
			If(ud.permanent_verify = 1 AND ud.local_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_address ud", "ud.user_id = users.id ", 'left')
			
			->join('pincode lin', 'lin.pincode = ud.local_pincode ', 'left')
		->join('areas la', 'la.id = lin.area_id ', 'left')
		->join('cities lcity', 'lcity.id = la.city_id ', 'left')
		->join('states ls', 'ls.id = lcity.state_id ', 'left')
		->join('zones lz', 'lz.id = ls.zone_id ', 'left')
		->join('countries lcc', 'lcc.id = lz.country_id ', 'left')
		->join('continents lc', 'lc.id = lcc.continent_id ', 'left')
		
		->join('pincode pin', 'pin.pincode = ud.permanent_pincode ', 'left')
		->join('areas pa', 'pa.id = pin.area_id ', 'left')
		->join('cities pcity', 'pcity.id = pa.city_id ', 'left')
		->join('states ps', 'ps.id = pcity.state_id ', 'left')
		->join('zones pz', 'pz.id = ps.zone_id ', 'left')
		->join('countries pcc', 'pcc.id = pz.country_id ', 'left')
		->join('continents pc', 'pc.id = pcc.continent_id ', 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("ud.is_edit", 1)
			->where("ud.permanent_verify !=", 1)
			->or_where("ud.local_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function address_status($id){
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
		$this->form_validation->set_rules('local_address', $this->lang->line("local_address"), 'required');
		
		$this->form_validation->set_rules('permanent_address', $this->lang->line("permanent_address"), 'required');
		
		$this->form_validation->set_rules('local_pincode', $this->lang->line("local_pincode"), 'required');
		
		$this->form_validation->set_rules('permanent_pincode', $this->lang->line("permanent_pincode"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				
				'permanent_address' => $this->input->post('permanent_address'),
				
				'permanent_verify' => $this->input->post('permanent_verify'),
				'local_verify' => $this->input->post('local_verify'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'local_approved_on' => date('Y-m-d H:i:s')
			);
			
			if ($_FILES['local_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/local_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('local_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$local_image = $this->upload->file_name;
				$data['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}
			
			if ($_FILES['permanent_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permanent_address/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permanent_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$permanent_image = $this->upload->file_name;
				$data['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_address_status($this->input->post('address_id'), $data, $countryCode)){
			
			if($this->input->post('permanent_verify') == 1 && $this->input->post('local_verify') == 1){
				$notification['title'] = 'Address Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
				$this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            	admin_redirect('verification/address');
			}else{
            	$this->session->set_flashdata('error', $this->input->post('verification_first_name').' details has been not verified');
            	admin_redirect('verification/address');
			}
			
           
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('address')));
			$meta = array('page_title' => lang('vendor'), 'bc' => $bc);
			$result = $this->verification_model->getUserAddress($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->local_continent_id);
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result->premanent_continent_id);
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result->premanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result->premanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result->premanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result->premanent_city_id);
			
			
			$this->page_construct('verification/address_status', $meta, $this->data);
		}
    }
	
	function account($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('account')));
        $meta = array('page_title' => lang('account'), 'bc' => $bc);
        $this->page_construct('verification/account', $meta, $this->data);
    }
	
	function getAccount(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ub.account_no,  ub.ifsc_code,
			If(ub.is_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_bank ub", "ub.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("ub.is_edit", 1)
			->where("ub.is_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function account_status($id){
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
		$this->form_validation->set_rules('account_holder_name', $this->lang->line("account_holder_name"), 'required');
		$this->form_validation->set_rules('account_no', $this->lang->line("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', $this->lang->line("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', $this->lang->line("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', $this->lang->line("ifsc_code"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_account_status($this->input->post('bank_id'), $data, $countryCode)){
			
			if($this->input->post('is_verify') == 1){
				$notification['title'] = 'Account Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
				$this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            	admin_redirect('verification/account');
			}else{
            	$this->session->set_flashdata('error', $this->input->post('verification_first_name').' details has been not verified');
            	admin_redirect('verification/account');
			}
			
           
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('account')));
			$meta = array('page_title' => lang('account'), 'bc' => $bc);
			$result = $this->verification_model->getUserBank($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/account_status', $meta, $this->data);
		}
    }
	
	
	function police($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('police')));
        $meta = array('page_title' => lang('police'), 'bc' => $bc);
        $this->page_construct('verification/police', $meta, $this->data);
    }
	
	function getPolice(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ud.police_on,  ud.police_til,
			If(ud.pancard_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_document ud", "ud.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("users.group_id !=", $this->Vendor)
			->where("ud.is_edit", 1)
			->where("ud.police_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function police_status($id){
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
		$this->form_validation->set_rules('police_on', $this->lang->line("police_on"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),
				'police_verify' => $this->input->post('police_verify'),
				'police_approved_by' => $this->session->userdata('user_id'),
				'police_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['police_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/police/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('police_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$police_image = $this->upload->file_name;
				$data['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Police Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/police');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('police')));
			$meta = array('page_title' => lang('police'), 'bc' => $bc);
			$result = $this->verification_model->getUserDocument($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/police_status', $meta, $this->data);
		}
    }
	
	function license($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('license')));
        $meta = array('page_title' => lang('license'), 'bc' => $bc);
        $this->page_construct('verification/license', $meta, $this->data);
    }
	
	function getLicense(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ud.license_ward_name,  
			If(ud.license_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_document ud", "ud.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("users.group_id !=", $this->Vendor)
			->where("ud.license_verify !=", 1)
			->where("ud.is_edit", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function license_status($id){
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
		
		$this->form_validation->set_rules('license_ward_name', $this->lang->line("license_ward_name"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'license_no' => $this->input->post('license_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => json_encode($this->input->post('license_type')),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'license_verify' => $this->input->post('license_verify'),
				'license_approved_by' => $this->session->userdata('user_id'),
				'license_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['license_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/license/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('license_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$license_image = $this->upload->file_name;
				$data['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data, $countryCode)){
			
			$notification['title'] = 'License Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification);
				
			
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/license');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('license')));
			$meta = array('page_title' => lang('license'), 'bc' => $bc);
			$result = $this->verification_model->getUserDocument($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			//$this->data['license_type'] = $this->masters_model->getALLLicense_type();
			
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
			
			$this->data['license_type'] = $this->masters_model->getCountry_bylicensetype($result->license_country_id);
			
			$this->page_construct('verification/license_status', $meta, $this->data);
		}
    }
	
	function pancard($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pancard')));
        $meta = array('page_title' => lang('pancard'), 'bc' => $bc);
        $this->page_construct('verification/pancard', $meta, $this->data);
    }
	
	function getPancard(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ud.pancard_no,  
			If(ud.pancard_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_document ud", "ud.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("ud.pancard_verify !=", 1)
			->where("ud.is_edit", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function pancard_status($id){
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
		$this->form_validation->set_rules('pancard_no', $this->lang->line("pancard_no"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('pancard_verify'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['pancard_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/pancard/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('pancard_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$pancard_image = $this->upload->file_name;
				$user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				$data['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Pancard Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/pancard');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pancard')));
			$meta = array('page_title' => lang('pancard'), 'bc' => $bc);
			$result = $this->verification_model->getUserDocument($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/pancard_status', $meta, $this->data);
		}
    }
	
	function loan($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('loan')));
        $meta = array('page_title' => lang('loan'), 'bc' => $bc);
        $this->page_construct('verification/loan', $meta, $this->data);
    }
	
	function getLoan(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ud.pancard_no,  
			If(ud.loan_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_document ud", "ud.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("ud.loan_verify !=", 1)
			->where("ud.is_edit", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function loan_status($id){
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
		$this->form_validation->set_rules('loan_information', $this->lang->line("loan_information"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'loan_information' => $this->input->post('loan_information'),
				'loan_verify' => $this->input->post('loan_verify'),
				'loan_approved_by' => $this->session->userdata('user_id'),
				'loan_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['loan_doc']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/loan/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('loan_doc')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$loan_doc = $this->upload->file_name;
				$data['loan_doc'] = 'document/loan/'.$loan_doc;
				$config = NULL;
			}
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Loan Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
			
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/loan');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('loan')));
			$meta = array('page_title' => lang('loan'), 'bc' => $bc);
			$result = $this->verification_model->getUserDocument($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/loan_status', $meta, $this->data);
		}
    }
	
	function aadhaar($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('aadhaar')));
        $meta = array('page_title' => lang('aadhaar'), 'bc' => $bc);
        $this->page_construct('verification/aadhaar', $meta, $this->data);
    }
	
	function getAadhaar(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, CONCAT(up.first_name, ' ', up.last_name) as name,  ud.aadhaar_no,  
			If(ud.aadhar_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id ", 'left')
			->join("user_document ud", "ud.user_id = users.id ", 'left')
			
			->where("users.group_id !=", $this->Admin)
			->where("users.group_id !=", $this->Owner)
			->where("ud.aadhar_verify !=", 1)
			->where("ud.is_edit", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
            $this->datatables->group_by('users.id')
			
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function aadhaar_status($id){
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
		$this->form_validation->set_rules('aadhaar_no', $this->lang->line("aadhaar_no"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('aadhar_verify'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('aadhaar_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$aadhaar_image = $this->upload->file_name;
				$user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$data['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
			}
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Aadhar Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/aadhaar');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('address')));
			$meta = array('page_title' => lang('vendor'), 'bc' => $bc);
			$result = $this->verification_model->getUserDocument($id);
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/aadhaar_status', $meta, $this->data);
		}
    }
	
	/*### taxi*/
	function taxi($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
        $meta = array('page_title' => lang('cab'), 'bc' => $bc);
        $this->page_construct('verification/taxi', $meta, $this->data);
    }
	
	function getTaxi(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If({$this->db->dbprefix('taxi')}.is_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("user_profile up", "up.user_id = taxi.driver_id OR up.user_id = taxi.vendor_id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("taxi.is_verify !=", 1)
			->where("taxi.is_edit", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function taxi_status($id){
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
		$this->form_validation->set_rules('taxi_name', $this->lang->line("taxi_name"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'name' => $this->input->post('taxi_name'),
				'model' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$data['photo'] = 'document/taxi/'.$photo;
				$config = NULL;
			}
			
			
			
			$data_doc = array(
				'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'reg_verify' => $this->input->post('reg_verify'),
				'reg_approved_by' => $this->session->userdata('user_id'),
				'reg_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['reg_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/register/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('reg_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$reg_image = $this->upload->file_name;
				$data_doc['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}
			
			$status = array(
				'is_allocated' => 1,
				'allocated_start_date' => date('Y-m-d H:i:s'),
				'allocated_status' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_status($this->input->post('taxi_id'), $data, $status) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_doc, $countryCode)){
			
			$notification['title'] = 'Taxi Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/taxi');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
			$meta = array('page_title' => lang('cab'), 'bc' => $bc);
			$result = $this->verification_model->getTaxi($id);
			$result_doc = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['result_doc'] = $result_doc;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/taxi_status', $meta, $this->data);
		}
    }
	
	function taxi_document($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_document')));
        $meta = array('page_title' => lang('cab_document'), 'bc' => $bc);
        $this->page_construct('verification/taxi_document', $meta, $this->data);
    }
	
	function getTaxi_document(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.reg_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.reg_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function taxi_document_status($id){
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
		$this->form_validation->set_rules('reg_date', $this->lang->line("reg_date"), 'required');
		$this->form_validation->set_rules('reg_due_date', $this->lang->line("reg_due_date"), 'required');
		$this->form_validation->set_rules('reg_owner_name', $this->lang->line("reg_owner_name"), 'required');
		$this->form_validation->set_rules('reg_owner_address', $this->lang->line("reg_owner_address"), 'required');		
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'reg_verify' => $this->input->post('reg_verify'),
				'reg_approved_by' => $this->session->userdata('user_id'),
				'reg_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['reg_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/register/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('reg_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$reg_image = $this->upload->file_name;
				$data['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_document_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Taxi registation Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
				
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/taxi_document');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
			$meta = array('page_title' => lang('cab'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/taxi_document_status', $meta, $this->data);
		}
    }
	
	function taxiation($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('taxiation')));
        $meta = array('page_title' => lang('taxiation'), 'bc' => $bc);
        $this->page_construct('verification/taxation', $meta, $this->data);
    }
	
	function getTaxation(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.taxation_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.taxation_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function taxation_status($id){
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
		$this->form_validation->set_rules('taxation_amount_paid', $this->lang->line("taxation_amount_paid"), 'required');
		$this->form_validation->set_rules('taxation_due_date', $this->lang->line("taxation_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'taxation_amount_paid' => $this->input->post('taxation_amount_paid'),
				'taxation_due_date' => $this->input->post('taxation_due_date'),
				'taxation_verify' => $this->input->post('taxation_verify'),
				'taxation_approved_by' => $this->session->userdata('user_id'),
				'taxation_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['taxation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxation/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxation_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxation_image = $this->upload->file_name;
				$data['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Taxt Taxation Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/taxation');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('taxation')));
			$meta = array('page_title' => lang('taxation'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/taxation_status', $meta, $this->data);
		}
    }
	
	function insurance($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('insurance')));
        $meta = array('page_title' => lang('insurance'), 'bc' => $bc);
        $this->page_construct('verification/insurance', $meta, $this->data);
    }
	
	function getInsurance(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.insurance_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.insurance_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function insurance_status($id){
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
		$this->form_validation->set_rules('insurance_policy_no', $this->lang->line("insurance_policy_no"), 'required');
		$this->form_validation->set_rules('insurance_due_date', $this->lang->line("insurance_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'insurance_verify' => $this->input->post('insurance_verify'),
				'insurance_approved_by' => $this->session->userdata('user_id'),
				'insurance_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('insurance_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$insurance_image = $this->upload->file_name;
				$data['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Insurance Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/insurance');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('insurance')));
			$meta = array('page_title' => lang('insurance'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/insurance_status', $meta, $this->data);
		}
    }
	
	function permit($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('permit')));
        $meta = array('page_title' => lang('permit'), 'bc' => $bc);
        $this->page_construct('verification/permit', $meta, $this->data);
    }
	
	function getPermit(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.permit_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.permit_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function permit_status($id){
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
		$this->form_validation->set_rules('permit_no', $this->lang->line("permit_no"), 'required');
		$this->form_validation->set_rules('permit_due_date', $this->lang->line("permit_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'permit_verify' => $this->input->post('permit_verify'),
				'permit_approved_by' => $this->session->userdata('user_id'),
				'permit_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['permit_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permit/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permit_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$permit_image = $this->upload->file_name;
				$data['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Permit Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/permit');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('permit')));
			$meta = array('page_title' => lang('permit'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/permit_status', $meta, $this->data);
		}
    }
	
	function authorisation($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('authorisation')));
        $meta = array('page_title' => lang('authorisation'), 'bc' => $bc);
        $this->page_construct('verification/authorisation', $meta, $this->data);
    }
	
	function getAuthorisation(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.authorisation_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.authorisation_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function authorisation_status($id){
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
		$this->form_validation->set_rules('authorisation_no', $this->lang->line("authorisation_no"), 'required');
		$this->form_validation->set_rules('authorisation_due_date', $this->lang->line("authorisation_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'authorisation_verify' => $this->input->post('authorisation_verify'),
				'authorisation_approved_by' => $this->session->userdata('user_id'),
				'authorisation_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['authorisation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/authorisation/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('authorisation_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$authorisation_image = $this->upload->file_name;
				$data['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Authorisation Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification, $countryCode);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/authorisation');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('authorisation')));
			$meta = array('page_title' => lang('authorisation'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/authorisation_status', $meta, $this->data);
		}
    }
	
	function fitness($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('fitness')));
        $meta = array('page_title' => lang('fitness'), 'bc' => $bc);
        $this->page_construct('verification/fitness', $meta, $this->data);
    }
	
	function getFitness(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.fitness_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.fitness_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function fitness_status($id){
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
		$this->form_validation->set_rules('fitness_due_date', $this->lang->line("fitness_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'fitness_verify' => $this->input->post('fitness_verify'),
				'fitness_approved_by' => $this->session->userdata('user_id'),
				'fitness_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['fitness_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fitness/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('fitness_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$fitness_image = $this->upload->file_name;
				$data['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Fitness Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/fitness');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('fitness')));
			$meta = array('page_title' => lang('fitness'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/fitness_status', $meta, $this->data);
		}
    }
	
	function speed($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('speed')));
        $meta = array('page_title' => lang('speed'), 'bc' => $bc);
        $this->page_construct('verification/speed', $meta, $this->data);
    }
	
	function getSpeed(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.speed_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.speed_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function speed_status($id){
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
		$this->form_validation->set_rules('speed_due_date', $this->lang->line("speed_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'speed_due_date' => $this->input->post('speed_due_date'),
				'speed_verify' => $this->input->post('speed_verify'),
				'speed_approved_by' => $this->session->userdata('user_id'),
				'speed_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['speed_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/speed_limit/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('speed_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$speed_image = $this->upload->file_name;
				$data['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'Speed Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/speed');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('speed')));
			$meta = array('page_title' => lang('speed'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/speed_status', $meta, $this->data);
		}
    }
	
	function puc($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('puc')));
        $meta = array('page_title' => lang('puc'), 'bc' => $bc);
        $this->page_construct('verification/puc', $meta, $this->data);
    }
	
	function getPuc(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
        $this->load->library('datatables');
        
			 $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name, {$this->db->dbprefix('taxi')}.number,    
			If(td.puc_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("users u", "u.id = taxi.driver_id OR u.id = taxi.vendor_id ", "left")
			->join("taxi_document td", "td.taxi_id = taxi.id ", 'left')
			
			//->where("u.group_id !=", $this->Admin)
			//->where("u.group_id !=", $this->Owner)
			//->where("u.group_id !=", $this->Employee)
			->where("td.puc_verify !=", 1);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->group_by("taxi.id")
			//->where("ud.local_verify", 0)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function puc_status($id){
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
		$this->form_validation->set_rules('puc_due_date', $this->lang->line("puc_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
			$data = array(
				'puc_due_date' => $this->input->post('puc_due_date'),
				'puc_verify' => $this->input->post('puc_verify'),
				'puc_approved_by' => $this->session->userdata('user_id'),
				'puc_approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['puc_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/puc/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('puc_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$puc_image = $this->upload->file_name;
				$data['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			
		}
		if ($this->form_validation->run() == true && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data, $countryCode)){
			$notification['title'] = 'PUC Verified Status';
				$notification['message'] = $this->input->post('verification_first_name').' your account verified status has been successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification);
            $this->session->set_flashdata('message', $this->input->post('verification_first_name').' details has been verified');
            admin_redirect('verification/puc');
			
		}else{
			
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('puc')));
			$meta = array('page_title' => lang('puc'), 'bc' => $bc);
			$result = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result'] = $result;
			$this->data['id'] = $id;			
			
			$this->page_construct('verification/puc_status', $meta, $this->data);
		}
    }
}
