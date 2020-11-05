<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Masters extends MY_Controller
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
		$this->load->admin_model('masters_model');
    }
	
	function index($countryCode){
		if($this->session->userdata('group_id') == 1){
			if(!empty($_GET['countryCode'])){
				$countryCode = $_GET['countryCode'];	
			}else{
				$countryCode = $countryCode;		
			}
			
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		//$this->form_validation->set_rules('driver_ride_accept', lang('driver_ride_accept'), 'trim|required');
		$this->form_validation->set_rules('search_kilometer', lang('search_kilometer'), 'trim|required');
		if ($this->form_validation->run() == true) {
			$data = array(
				'due_month' => $this->input->post('due_month') ? $this->input->post('due_month') : 0,
				'due_year' => $this->input->post('due_year') ? $this->input->post('due_year') : 0,
				//'driver_ride_accept' => $this->input->post('driver_ride_accept'),
				'search_kilometer' => $this->input->post('search_kilometer') ? $this->input->post('search_kilometer') : 20,
				'support_email' => $this->input->post('support_email') ? $this->input->post('support_email') : 'owner@heyycab.com',
				'support_mobile' => $this->input->post('support_mobile') ? $this->input->post('support_mobile') : '',
				'support_whatsapp' => $this->input->post('support_whatsapp') ? $this->input->post('support_whatsapp') : '',
				'outstation_min_kilometer' => $this->input->post('outstation_min_kilometer') ? $this->input->post('outstation_min_kilometer') : 50,
				'cityride_max_kilometer' => $this->input->post('cityride_max_kilometer') ? $this->input->post('cityride_max_kilometer') : 50,
				'rental_max_kilometer' => $this->input->post('rental_max_kilometer') ? $this->input->post('rental_max_kilometer') : 50,
				'driver_admin_payment_option' => $this->input->post('driver_admin_payment_option'),
				'driver_admin_payment_percentage' => $this->input->post('driver_admin_payment_percentage') ? $this->input->post('driver_admin_payment_percentage') : 5,
				//'vendor_admin_payment_option' => $this->input->post('vendor_admin_payment_option'),
				//'vendor_admin_payment_percentage' => $this->input->post('vendor_admin_payment_percentage'),
				//'driver_vendor_payment_option' => $this->input->post('driver_vendor_payment_option'),
				//'driver_vendor_payment_percentage' => $this->input->post('driver_vendor_payment_percentage'),
				'driver_admin_payment_duration' => $this->input->post('driver_admin_payment_duration') ? $this->input->post('driver_admin_payment_duration') : 0,
				//'vendor_admin_payment_duration' => $this->input->post('vendor_admin_payment_duration'),
				//'driver_vendor_payment_duration' => $this->input->post('driver_vendor_payment_duration'),
				'waiting_charges' => $this->input->post('waiting_charges') ? $this->input->post('waiting_charges') : '0.00',
				'waiting_time' => $this->input->post('waiting_time') ? $this->input->post('waiting_time') : '0',
				
				'help_number_one' => $this->input->post('help_number_one') ? $this->input->post('help_number_one') : '',
				'help_number_two' => $this->input->post('help_number_two') ? $this->input->post('help_number_two') : '',
				'help_number_three' => $this->input->post('help_number_three') ? $this->input->post('help_number_three') : '',
				'help_number_four' => $this->input->post('help_number_four') ? $this->input->post('help_number_four') : '',
				'help_number_five' => $this->input->post('help_number_five') ? $this->input->post('help_number_five') : '',
				'camera_enable' => $this->input->post('camera_enable'),
				'timezone' =>  $this->input->post('timezone'),
				'dateofbirth' =>  $this->input->post('dateofbirth'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'register_otp_enable' => $this->input->post('register_otp_enable'),
				/*'outstation_min_balance' => $this->input->post('outstation_min_balance'),
				'rental_min_balance' => $this->input->post('rental_min_balance'),
				'cityride_min_balance' => $this->input->post('cityride_min_balance'),*/
				'customer_user_reg' => $this->input->post('customer_user_reg'),
				'customer_rides' => $this->input->post('customer_rides'),
				'customer_rides_no' => $this->input->post('customer_rides_no'),
				'customer_validation' => $this->input->post('customer_validation'),
				'customer_using_type' => $this->input->post('customer_using_type'),
				'customer_using_members' => $this->input->post('customer_using_members'),
				'customer_amount' => $this->input->post('customer_amount'),
				
				'driver_user_reg' => $this->input->post('driver_user_reg'),
				'driver_rides' => $this->input->post('driver_rides'),
				'driver_rides_no' => $this->input->post('driver_rides_no'),
				'driver_validation' => $this->input->post('driver_validation'),
				'driver_using_type' => $this->input->post('driver_using_type'),
				'driver_using_members' => $this->input->post('driver_using_members'),
				'driver_amount' => $this->input->post('driver_amount'),
				
				
				'wallet_min_add_money' => $this->input->post('wallet_min_add_money'),
				'driver_default_set_payment' => $this->input->post('driver_default_set_payment')
				

			);
			
			
			
		}elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/index?countryCode=".$countryCode);
        }
		
		if ($this->form_validation->run() == true && $this->masters_model->updateSetting($data, $countryCode)) {
			$this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect("masters/index?countryCode=".$countryCode);
		}else{
			$this->data['countryCode'] = $countryCode;
			$this->data['dataSettings'] = $this->masters_model->getSettingscountry($countryCode);
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['action'] = $action;
			$countrylist = $this->site->getcountryCodeID($countryCode);
			if($countryCode != ''){
				$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => $countrylist->name.' Settings'));
				$meta = array('page_title' => $countrylist->name.' Settings', 'bc' => $bc);
			}else{
				$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Settings')));
				$meta = array('page_title' => lang('Settings'), 'bc' => $bc);
			}
			
			$this->page_construct('masters/index', $meta, $this->data);
		}
		
	}
	
	
	
	/*###### Tax*/
    function countrywisesetting($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('tax')));
        $meta = array('page_title' => lang('countrywisesetting'), 'bc' => $bc);
        $this->page_construct('masters/countrywisesetting', $meta, $this->data);
    }
    function getCountrywisesetting(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id, country_name, is_default")
            ->from("countrywisesetting")
			//->where('tax.is_delete', 0)
			->edit_column('is_default', '$1', 'is_default');
             //->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_tax/$1') . "' class='tip' title='" . lang("edit_tax") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_countrywisesetting/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			//$delete = "<a href='" . admin_url('welcome/delete/tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_countrywisesetting(){
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
        $this->form_validation->set_rules('timezone', lang("timezone"), 'required');
		
        if ($this->form_validation->run() == true) {
			
			//if(!empty($this->input->post('country_id'))){
				$check = $this->masters_model->countryChecking($this->input->post('country_id'), $countryCode);
				if($check == true){
					$this->session->set_flashdata('error', lang("already exists setting"));
					admin_redirect('masters/countrywisesetting');
				}
			//}
			
			if($this->input->post('country_id') != ''){
				$is_default = 0;
			}else{
				$is_default = 1;
			}
			
			$country_name = $this->masters_model->getCountryname($this->input->post('country_id'), $countryCode);
            $data = array(
                'country_id' => $this->input->post('country_id'),
				'country_name' => $country_name ? $country_name : 'Default Setting',
				'timezone' =>  $this->input->post('timezone'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'is_default' => $is_default,
               
            );
			
           
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_countrywisesetting($data, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("countrywisesetting_added"));
            admin_redirect('masters/countrywisesetting');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/countrywisesetting'), 'page' => lang('countrywisesetting')), array('link' => '#', 'page' => lang('add_countrywisesetting')));
            $meta = array('page_title' => lang('add_countrywisesetting'), 'bc' => $bc);
			$this->data['country'] = $this->masters_model->getALLCountry();
			
            $this->page_construct('masters/add_countrywisesetting', $meta, $this->data);
        }
    }
    function edit_countrywisesetting($id){
		$result = $this->masters_model->getCountrywisesettingby_ID($id, $countryCode);
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
		
		
		
		 $this->form_validation->set_rules('timezone', lang("timezone"), 'required');
		
        if ($this->form_validation->run() == true) {
			if($result->country_id != $this->input->post('country_id')){
				
				$check = $this->masters_model->countryChecking($this->input->post('country_id'), $countryCode);
				if($check == true){
					$this->session->set_flashdata('error', lang("already exists setting"));
					admin_redirect('masters/countrywisesetting');
				}
			}
			if($this->input->post('country_id') != ''){
				$is_default = 0;
			}else{
				$is_default = 1;
			}
			
			$country_name = $this->masters_model->getCountryname($this->input->post('country_id'), $countryCode);
			
            $data = array(
                'country_id' => $this->input->post('country_id'),
				'country_name' => $country_name ? $country_name : 'Default Setting',
				'timezone' =>  $this->input->post('timezone'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'is_default' => $is_default,
               
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_countrywisesetting($id,$data, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("countrywisesetting_updated"));
            admin_redirect('masters/countrywisesetting');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/countrywisesetting'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_countrywisesetting'), 'bc' => $bc);
            $this->data['setting'] = $result;
			$this->data['id'] = $id;
			$this->data['country'] = $this->masters_model->getALLCountry();
            $this->page_construct('masters/edit_countrywisesetting', $meta, $this->data);
        }
    }
    function countrywisesetting_status($status,$id){
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
		$this->masters_model->update_tax_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### User department*/
    function user_department($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('user_department')));
        $meta = array('page_title' => lang('user_department'), 'bc' => $bc);
        $this->page_construct('masters/user_department', $meta, $this->data);
    }
    function getUser_department(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('user_department')}.id as id, {$this->db->dbprefix('user_department')}.name, {$this->db->dbprefix('user_department')}.status as status")
            ->from("user_department")
			->where('user_department.is_delete', 0)
			//->where('user_department.is_country', $countryCode)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_user_department") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$delete."</div>", "id");
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_user_department(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');  
		
		  
        if ($this->form_validation->run() == true) {
			
			$check = $this->site->masterCheck('user_department', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/user_department");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_user_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/user_department");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_user_department($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("user_department_added"));
            admin_redirect('masters/user_department');
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
            $this->load->view($this->theme . 'masters/add_user_department', $this->data);
        }
    }
    function edit_user_department($id){
		$result = $this->masters_model->getUser_departmentby_ID($id, $countryCode);
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
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
        
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('user_department', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/user_department");
					exit;	
				}
			}

            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_user_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/user_department");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_user_department($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("user_department_updated"));
            admin_redirect("masters/user_department");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_user_department', $this->data);
        }
    }
   
    function user_department_status($status,$id){
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
        $this->masters_model->update_user_department_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Category*/
    function taxi_category($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('taxi_category')));
        $meta = array('page_title' => lang('cab_category'), 'bc' => $bc);
        $this->page_construct('masters/taxi_category', $meta, $this->data);
    }
    function getTaxi_category(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_categorys')}.id,{$this->db->dbprefix('taxi_categorys')}.name,{$this->db->dbprefix('taxi_categorys')}.status as status, country.name as instance_country ")
            ->from("taxi_categorys")
			->join("countries country", " country.iso = taxi_categorys.is_country", "left")
			->where('taxi_categorys.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_categorys.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_categorys.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_category") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_category/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_categorys/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_category(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_categorys', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/taxi_category");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_taxi_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_category");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_category($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("taxi_categorys_added"));
            admin_redirect('masters/taxi_category');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_taxi_category', $this->data);
        }
    }
    function edit_taxi_category($id){
		$result = $this->masters_model->getTaxi_categoryby_ID($id, $countryCode);
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
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
        
		
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_categorys', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/taxi_category");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_taxi_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_category");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_category($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("taxi_category_updated"));
            admin_redirect("masters/taxi_category");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_category', $this->data);
        }
    }
   
    function taxi_category_status($status,$id){
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
        $this->masters_model->update_taxi_category_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Fuel*/
    function taxi_fuel($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('taxi_fuel')));
        $meta = array('page_title' => lang('cab_fuel'), 'bc' => $bc);
        $this->page_construct('masters/taxi_fuel', $meta, $this->data);
    }
    function getTaxi_fuel(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_fuel')}.id as id,{$this->db->dbprefix('taxi_fuel')}.name,{$this->db->dbprefix('taxi_fuel')}.status as status, country.name as instance_country")
            ->from("taxi_fuel")
			->join("countries country", " country.iso = taxi_fuel.is_country", "left")
			->where('taxi_fuel.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_fuel.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_fuel.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_fuel/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_fuel") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_fuel/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_fuel/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_fuel(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_fuel', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/taxi_fuel");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_taxi_fuel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_fuel");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_fuel($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("taxi_fuel_added"));
            admin_redirect('masters/taxi_fuel');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_taxi_fuel', $this->data);
        }
    }
    function edit_taxi_fuel($id){
		$result = $this->masters_model->getTaxi_fuelby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
      
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_fuel', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/taxi_fuel");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_taxi_fuel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_fuel");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_fuel($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("taxi_fuel_updated"));
            admin_redirect("masters/taxi_fuel");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_fuel', $this->data);
        }
    }
   
    function taxi_fuel_status($status,$id){
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
        $this->masters_model->update_taxi_fuel_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Type*/
    function taxi_type($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_type')));
        $meta = array('page_title' => lang('cab_type'), 'bc' => $bc);
        $this->page_construct('masters/taxi_type', $meta, $this->data);
    }
    function getTaxi_type(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_type')}.id as id, {$this->db->dbprefix('taxi_type')}.name,  {$this->db->dbprefix('taxi_type')}.image, {$this->db->dbprefix('taxi_type')}.image_hover, {$this->db->dbprefix('taxi_type')}.mapcar, {$this->db->dbprefix('taxi_type')}.status as status, country.name as instance_country")
            ->from("taxi_type")
			->join("countries country", " country.iso = taxi_type.is_country", "left")
			//->join("taxi_categorys p", "p.id = taxi_type.category_id AND p.is_country = '".$countryCode."'")
			->where('taxi_type.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_type.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1 ){
				$this->datatables->where("taxi_type.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_type(){
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('category_id', lang("category"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_type', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/taxi_type");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
				'category_id' => $this->input->post('category_id'),
                'status' => 1,
            );
			
			if ($_FILES['outstation_image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('outstation_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$outstation_image = $this->upload->file_name;
				$data['outstation_image'] = 'document/taxi_type/'.$outstation_image;
				$config = NULL;
			}
			
			if ($_FILES['image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$image = $this->upload->file_name;
				$data['image'] = 'document/taxi_type/'.$image;
				$config = NULL;
			}
			
			if ($_FILES['image_hover']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image_hover')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$image_hover = $this->upload->file_name;
				$data['image_hover'] = 'document/taxi_type/'.$image_hover;
				$config = NULL;
			}
			
			if ($_FILES['mapcar']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('mapcar')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$mapcar = $this->upload->file_name;
				$data['mapcar'] = 'document/taxi_type/'.$mapcar;
				$config = NULL;
			}
        }elseif ($this->input->post('add_taxi_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_type");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_type($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("taxi_type_added"));
            admin_redirect('masters/taxi_type');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLTaxi_category($countryCode);
            $this->load->view($this->theme . 'masters/add_taxi_type', $this->data);
			
        }
    }
    function edit_taxi_type($id){
		$result = $this->masters_model->getTaxi_typeby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		$this->form_validation->set_rules('category_id', lang("category"), 'required');      
       
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_type', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/taxi_type");
					exit;	
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'category_id' => $this->input->post('category_id'),
            );
			
			if ($_FILES['outstation_image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('outstation_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$outstation_image = $this->upload->file_name;
				$data['outstation_image'] = 'document/taxi_type/'.$outstation_image;
				$config = NULL;
			}
			
			if ($_FILES['image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$image = $this->upload->file_name;
				$data['image'] = 'document/taxi_type/'.$image;
				$config = NULL;
			}
			
			if ($_FILES['image_hover']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image_hover')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$image_hover = $this->upload->file_name;
				$data['image_hover'] = 'document/taxi_type/'.$image_hover;
				$config = NULL;
			}
			
			if ($_FILES['mapcar']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('mapcar')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$mapcar = $this->upload->file_name;
				$data['mapcar'] = 'document/taxi_type/'.$mapcar;
				$config = NULL;
			}
			
        } elseif ($this->input->post('edit_taxi_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_type");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_type($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("taxi_type_updated"));
            admin_redirect("masters/taxi_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLTaxi_category($countryCode);
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_type', $this->data);
        }
    }
   
    function taxi_type_status($status,$id){
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
        $this->masters_model->update_taxi_type_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Make*/
    function taxi_make($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_make')));
        $meta = array('page_title' => lang('cab_make'), 'bc' => $bc);
        $this->page_construct('masters/taxi_make', $meta, $this->data);
    }
    function getTaxi_make(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_make')}.id as id, {$this->db->dbprefix('taxi_make')}.name,  {$this->db->dbprefix('taxi_make')}.status as status, country.name as instance_country")
            ->from("taxi_make")
			->join("countries country", " country.iso = taxi_make.is_country", "left")
			->where('taxi_make.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_make.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_make.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_make/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_make/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_make(){
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_make', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/taxi_make");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
			
        }elseif ($this->input->post('add_taxi_make')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_make");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_make($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("taxi_make_added"));
            admin_redirect('masters/taxi_make');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->load->view($this->theme . 'masters/add_taxi_make', $this->data);
			
        }
    }
    function edit_taxi_make($id){
		$result = $this->masters_model->getTaxi_makeby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		    
       
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_make', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/taxi_make");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
			
		
			
        } elseif ($this->input->post('edit_taxi_make')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_make");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_make($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("taxi_make_updated"));
            admin_redirect("masters/taxi_make");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_make', $this->data);
        }
    }
   
    function taxi_make_status($status,$id){
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
        $this->masters_model->update_taxi_make_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Model*/
    function taxi_model($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_model')));
        $meta = array('page_title' => lang('cab_model'), 'bc' => $bc);
        $this->page_construct('masters/taxi_model', $meta, $this->data);
    }
    function getTaxi_model(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_model')}.id as id, {$this->db->dbprefix('taxi_model')}.name,  tt.name as type_name, tm.name as make_name, {$this->db->dbprefix('taxi_model')}.status as status, country.name as instance_country")
            ->from("taxi_model")
			->join("countries country", " country.iso = taxi_model.is_country", "left")
			->join("taxi_type tt", "tt.id = taxi_model.type_id ", "left")
			->join("taxi_make tm", "tm.id = taxi_model.make_id ", "left")
			->where('taxi_model.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_model.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_model.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_model/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_model/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	
    function add_taxi_model(){
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_model', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/taxi_model");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
				'type_id' => $this->input->post('type_id'),
				'make_id' => $this->input->post('make_id'),
                'status' => 1,
            );
			
        }elseif ($this->input->post('add_taxi_model')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_model");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_model($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("taxi_model_added"));
            admin_redirect('masters/taxi_model');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->load->view($this->theme . 'masters/add_taxi_model', $this->data);
			
        }
    }
    function edit_taxi_model($id){
		$result = $this->masters_model->getTaxi_modelby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		    
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_model', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'name has been already exits');
					admin_redirect("masters/taxi_model");
					exit;	
				}
			}

			
            $data = array(
				'name' => $this->input->post('name'),
				'type_id' => $this->input->post('type_id'),
				'make_id' => $this->input->post('make_id'),
            );
			
		
			
        } elseif ($this->input->post('edit_taxi_model')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_model");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_model($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("taxi_model_updated"));
            admin_redirect("masters/taxi_model");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_model', $this->data);
        }
    }
   
    function taxi_model_status($status,$id){
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
        $this->masters_model->update_taxi_model_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### License Type*/
    function license_type($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('license_type')));
        $meta = array('page_title' => lang('license_type'), 'bc' => $bc);
        $this->page_construct('masters/license_type', $meta, $this->data);
    }
    function getLicense_type(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
       /* $this->datatables
            ->select("id,name, details, status")
            ->from("license_type")
            ->edit_column('status', '$1__$2', 'status, id')*/
		 $this->datatables->select("{$this->db->dbprefix('license_type')}.id as id, c.name as country_name, {$this->db->dbprefix('license_type')}.name, {$this->db->dbprefix('license_type')}.details, {$this->db->dbprefix('license_type')}.status as status, country.name as instance_country")
            ->from("license_type")
			->join("countries country", " country.iso = license_type.is_country", "left")
			->join("countries c", "c.id = license_type.country_id", 'left')	
			->where('license_type.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("license_type.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("license_type.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_license_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_license_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_license_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/license_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_license_type(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			
			$check_name = $this->masters_model->checkLicense($this->input->post('name'), $this->input->post('country_id'), 1, NULL, $countryCode);
			if($check_name == 1){
				$this->session->set_flashdata('error', lang("license_type_already_exit"));
            	admin_redirect('masters/license_type');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'details' => $this->input->post('details'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
                'status' => 1,
            );
			
        }elseif ($this->input->post('add_license_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/license_type");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_license_type($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("license_type_added"));
            admin_redirect('masters/license_type');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_license_type', $this->data);
        }
    }
    function edit_license_type($id){
		$result = $this->masters_model->getLicense_typeby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
       
        if ($this->form_validation->run() == true) {

			$check_name = $this->masters_model->checkLicense($this->input->post('name'), $this->input->post('country_id'), 2, $result->name, $countryCode);
			if($check_name == 1){
				$this->session->set_flashdata('error', lang("license_type_already_exit"));
            	admin_redirect('masters/license_type');
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'details' => $this->input->post('details'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
            );
        } elseif ($this->input->post('edit_license_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/license_type");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_license_type($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("license_type_updated"));
            admin_redirect("masters/license_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id, $countryCode);
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_license_type', $this->data);
        }
    }
   
    function license_type_status($status,$id){
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
        $this->masters_model->update_license_type_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Currency*/
    function bank($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('bank')));
        $meta = array('page_title' => lang('bank'), 'bc' => $bc);
        $this->page_construct('masters/bank', $meta, $this->data);
    }
    function getBank(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('admin_bank')}.id as id, {$this->db->dbprefix('admin_bank')}.account_holder_name,{$this->db->dbprefix('admin_bank')}.account_no, {$this->db->dbprefix('admin_bank')}.bank_name, {$this->db->dbprefix('admin_bank')}.branch_name, {$this->db->dbprefix('admin_bank')}.ifsc_code, {$this->db->dbprefix('admin_bank')}.is_default, {$this->db->dbprefix('admin_bank')}.status as status, country.name as instance_country")
            ->from("admin_bank")
			->join("countries country", " country.iso = admin_bank.is_country", "left")
			->where('admin_bank.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("admin_bank.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("admin_bank.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_default', '$1', 'is_default')
             ->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_bank/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/admin_bank/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_bank(){
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
        $this->form_validation->set_rules('account_no', lang("account_no"), 'required');
        $this->form_validation->set_rules('account_holder_name', lang("account_holder_name"), 'required');    
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');     
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');     
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');     
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'account no has been already exits');
            	admin_redirect("masters/bank");
				exit;	
			}
            $data = array(
                'account_holder_name' => $this->input->post('account_holder_name'),
                'account_no' =>$this->input->post('account_no'),
				'bank_name' =>$this->input->post('bank_name'),
				'ifsc_code' =>$this->input->post('ifsc_code'),
				'branch_name' => $this->input->post('branch_name'),
				'user_id' => $this->session->userdata('user_id'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
                'status' => 1,
            );
           
        }elseif ($this->input->post('add_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/bank");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_bank($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("bank_added"));
            admin_redirect('masters/bank');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('add_bank')));
            $meta = array('page_title' => lang('add_bank'), 'bc' => $bc);
            $this->page_construct('masters/add_bank', $meta, $this->data);
        }
    }
    function edit_bank($id){
		$result = $this->masters_model->getBankby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
        $this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		 
      
		
		$this->form_validation->set_rules('account_holder_name', lang("account_holder_name"), 'required');  
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');  
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');  
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');  
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('account_no') != $result->account_no) {
				$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'account no has been already exits');
					admin_redirect("masters/bank");
					exit;	
				}
			}
			
            $data = array(
                 'account_holder_name' => $this->input->post('account_holder_name'),
                'account_no' =>$this->input->post('account_no'),
				'bank_name' =>$this->input->post('bank_name'),
				'ifsc_code' =>$this->input->post('ifsc_code'),
				'branch_name' => $this->input->post('branch_name'),
				'user_id' => $this->session->userdata('user_id'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_bank($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("bank_updated"));
            admin_redirect('masters/bank');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_bank'), 'bc' => $bc);
            $this->data['bank'] = $result;
            $this->page_construct('masters/edit_bank', $meta, $this->data);
        }
    }
    function bank_status($status,$id){
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
		$this->masters_model->update_bank_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Tax*/
    function tax($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('tax')));
        $meta = array('page_title' => lang('tax'), 'bc' => $bc);
        $this->page_construct('masters/tax', $meta, $this->data);
    }
    function getTax(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('tax')}.id as id, {$this->db->dbprefix('tax')}.tax_name,{$this->db->dbprefix('tax')}.percentage, {$this->db->dbprefix('tax')}.is_default, {$this->db->dbprefix('tax')}.status as status, country.name as instance_country")
            ->from("tax")
			->join("countries country", " country.iso = tax.is_country", "left")
			->where('tax.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("tax.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("tax.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_default', '$1', 'is_default')
             ->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_tax/$1') . "' class='tip' title='" . lang("edit_tax") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_tax(){
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
        $this->form_validation->set_rules('tax_name', lang("tax_name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('tax', array('tax_name' => $this->input->post('tax_name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'tax name has been already exits');
            	admin_redirect("masters/tax");
				exit;	
			}
            $data = array(
                'tax_name' => $this->input->post('tax_name'),
                'percentage' =>$this->input->post('percentage'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
                'status' => 1,
            );
			
           
        }elseif ($this->input->post('add_tax')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/tax");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_tax($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("tax_added"));
            admin_redirect('masters/tax');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/tax'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('add_tax')));
            $meta = array('page_title' => lang('add_tax'), 'bc' => $bc);
            $this->page_construct('masters/add_tax', $meta, $this->data);
        }
    }
    function edit_tax($id){
		$result = $this->masters_model->getTaxby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
        $this->form_validation->set_rules('tax_name', lang("tax_name"), 'required');
       
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('tax_name') != $result->tax_name) {
				$check = $this->site->masterCheck('tax', array('tax_name' => $this->input->post('tax_name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', 'tax name has been already exits');
					admin_redirect("masters/tax");
					exit;	
				}
			}
			
            $data = array(
                'tax_name' => $this->input->post('tax_name'),
                'percentage' =>$this->input->post('percentage'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_tax($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("tax_updated"));
            admin_redirect('masters/tax');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/tax'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_tax'), 'bc' => $bc);
            $this->data['tax'] = $result;
            $this->page_construct('masters/edit_tax', $meta, $this->data);
        }
    }
    function tax_status($status,$id){
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
		$this->masters_model->update_tax_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Wallet Offer*/
    function walletoffer($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('wallet offer')));
        $meta = array('page_title' => lang('wallet offer'), 'bc' => $bc);
        $this->page_construct('masters/walletoffer', $meta, $this->data);
    }
    function getWalletoffer(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('walletoffer')}.id as id, {$this->db->dbprefix('walletoffer')}.name,{$this->db->dbprefix('walletoffer')}.amount, {$this->db->dbprefix('walletoffer')}.offer_amount, {$this->db->dbprefix('walletoffer')}.type as type, {$this->db->dbprefix('walletoffer')}.is_default as is_default, {$this->db->dbprefix('walletoffer')}.status as status, country.name as instance_country")
            ->from("walletoffer")
			->join("countries country", " country.iso = walletoffer.is_country", "left")
			->where('walletoffer.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("walletoffer.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("walletoffer.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('type', '$1__$2', 'type, id');
			
			 $this->datatables->edit_column('is_default', '$1', 'is_default');
              $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_walletoffer/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/walletoffer/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div><div>".$edit."</div><div>".$delete."</div></div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_walletoffer(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('walletoffer', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/walletoffer");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'amount' =>$this->input->post('amount'),
				'offer_amount' => $this->input->post('type') == 0 ? $this->input->post('offer_amount') : $this->input->post('amount'),
				'offer_date' =>$this->input->post('offer_date') ? $this->input->post('offer_date') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'type' => $this->input->post('type'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
            );
			
           
        }elseif ($this->input->post('add_walletoffer')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/walletoffer");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_walletoffer($data, $this->input->post('type'), $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("walletoffer_added"));
            admin_redirect('masters/walletoffer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/walletoffer'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('add_walletoffer')));
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $meta = array('page_title' => lang('add_walletoffer'), 'bc' => $bc);
            $this->page_construct('masters/add_walletoffer', $meta, $this->data);
        }
    }
    function edit_walletoffer($id){
		$result = $this->masters_model->getWalletofferby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
        $this->form_validation->set_rules('name', lang("name"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name && $countryCode != $result->is_country) {
				$check = $this->site->masterCheck('walletoffer', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', ' name has been already exits');
					admin_redirect("masters/walletoffer");
					exit;	
				}
			}
            $data = array(
                  'name' => $this->input->post('name'),
                'amount' =>$this->input->post('amount'),
				'offer_amount' => $this->input->post('type') == 0 ? $this->input->post('offer_amount') : $this->input->post('amount'),
				'offer_date' =>$this->input->post('offer_date') ? $this->input->post('offer_date') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'type' => $this->input->post('type'),
				
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_walletoffer($id,$data, $this->input->post('type'), $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("walletoffer_updated"));
            admin_redirect('masters/walletoffer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/walletoffer'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_walletoffer'), 'bc' => $bc);
            $this->data['walletoffer'] = $result;
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $this->page_construct('masters/edit_walletoffer', $meta, $this->data);
        }
    }
    function walletoffer_status($status,$id){
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
		$this->masters_model->update_walletoffer_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Currency*/
    function currencies($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('currencies')));
        $meta = array('page_title' => lang('currencies'), 'bc' => $bc);
        $this->page_construct('masters/currencies', $meta, $this->data);
    }
    function getCurrencies(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('currencies')}.id as id, {$this->db->dbprefix('currencies')}.name,{$this->db->dbprefix('currencies')}.symbol, {$this->db->dbprefix('currencies')}.unicode_symbol, {$this->db->dbprefix('currencies')}.is_default, {$this->db->dbprefix('currencies')}.status as status, country.name as instance_country")
            ->from("currencies")
			->join("countries country", " country.iso = currencies.is_country", "left")
			->where('currencies.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("currencies.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("currencies.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_default', '$1', 'is_default')
             ->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_currency/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/currencies/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_currency(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('currencies', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/currencies");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
				'unicode_symbol' => $this->input->post('unicode_symbol'),
				'iso_code' =>$this->input->post('iso_code') ? $this->input->post('iso_code') : '',
				'numeric_iso_code' =>$this->input->post('numeric_iso_code') ? $this->input->post('numeric_iso_code') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
            );
			
           
        }elseif ($this->input->post('add_currencies')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/currencies");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_currency($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("currency_added"));
            admin_redirect('masters/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('add_currency')));
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $meta = array('page_title' => lang('add_currency'), 'bc' => $bc);
            $this->page_construct('masters/add_currency', $meta, $this->data);
        }
    }
    function edit_currency($id){
		$result = $this->masters_model->getCurrencyby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
        $this->form_validation->set_rules('name', lang("name"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('currencies', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', ' name has been already exits');
					admin_redirect("masters/currencies");
					exit;	
				}
			}
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
				'unicode_symbol' => $this->input->post('unicode_symbol'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'iso_code' =>$this->input->post('iso_code') ? $this->input->post('iso_code') : '',
				'numeric_iso_code' =>$this->input->post('numeric_iso_code') ? $this->input->post('numeric_iso_code') : ''
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_currency($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("currency_updated"));
            admin_redirect('masters/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_currency'), 'bc' => $bc);
            $this->data['currency'] = $result;
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $this->page_construct('masters/edit_currency', $meta, $this->data);
        }
    }
    function currency_status($status,$id){
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
		$this->masters_model->update_currency_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
    /*###### Continents*/
    function continents($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('continent')));
        $meta = array('page_title' => lang('continent'), 'bc' => $bc);
        $this->page_construct('masters/continents', $meta, $this->data);
    }
    function getContinents(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id,code,name")
            ->from("continents")
			->where('continents.is_delete', 0);
		
			
          //  $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_continents") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/continents/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_continent(){
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
        $this->form_validation->set_rules('name', lang("continent_name"), 'required|is_unique[continents.name]');    
		$this->form_validation->set_rules('code', lang("continent_code"), 'required|is_unique[continents.code]');    
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_continent')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/continents");
        }
        if ($this->form_validation->run() == true && $this->masters_model->add_continent($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("continent_added"));
            admin_redirect('masters/continents');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_continent', $this->data);
        }
    }
    function edit_continent($id){
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
		$result = $this->masters_model->getContinentby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("continent_name"), 'required');    
		$this->form_validation->set_rules('code', lang("continent_code"), 'required'); 
        
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("continent_name"), 'is_unique[continents.name]');
        }
		if ($this->input->post('code') != $result->code) {
            $this->form_validation->set_rules('code', lang("continent_code"), 'is_unique[continents.code]');
        }
        
        if ($this->form_validation->run() == true) {

            $data = array(
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
            );
        } elseif ($this->input->post('edit_continent')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/continents");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_continent($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("continent_updated"));
            admin_redirect("masters/continents");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_continent', $this->data);
        }
    }
   
    function continent_status($status,$id){
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
        $this->masters_model->update_continent_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Country*/
    function country($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('country')));
        $meta = array('page_title' => lang('country'), 'bc' => $bc);
        $this->page_construct('masters/country', $meta, $this->data);
    }
    function getCountry(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('countries')}.id as id, {$this->db->dbprefix('countries')}.name, {$this->db->dbprefix('countries')}.phonecode, p.name as parent_name, {$this->db->dbprefix('countries')}.flag")
            ->from("countries")
			->join("continents p", "p.id = countries.continent_id ")
			->where('countries.is_delete', 0);
			
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_country") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/countries/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_country(){
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
		
        $this->form_validation->set_rules('name', lang("country_name"), 'required|is_unique[countries.name]');    
		$this->form_validation->set_rules('continent_id', lang("continent"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkCountry($this->input->post('name'), $this->input->post('continent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("continent_already_exit"));
            	admin_redirect('masters/country');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'phonecode' => $this->input->post('phonecode'),
				'continent_id' => $this->input->post('continent_id'),
                'status' => 1,
            );
			if ($_FILES['flag']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'flag/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('flag')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$flag = $this->upload->file_name;
				$data['flag'] = 'flag/'.$flag;
				$config = NULL;
			}
        }elseif ($this->input->post('add_country')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/country");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_country($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("country_added"));
            admin_redirect('masters/country');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_country', $this->data);
			
        }
    }
    function edit_country($id){
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
		$result = $this->masters_model->getCountryby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("country_name"), 'required');  
		$this->form_validation->set_rules('continent_id', lang("continent"), 'required');      
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("country_name"), 'is_unique[countries.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkCountry($this->input->post('name'), $this->input->post('continent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("continent_already_exit"));
					admin_redirect('masters/country');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'phonecode' => $this->input->post('phonecode'),
				'continent_id' => $this->input->post('continent_id'),
            );
			if ($_FILES['flag']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'flag/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('flag')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$flag = $this->upload->file_name;
				$data['flag'] = 'flag/'.$flag;
				$config = NULL;
			}
        } elseif ($this->input->post('edit_country')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/country");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_country($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("country_updated"));
            admin_redirect("masters/country");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_country', $this->data);
        }
    }
   
    function country_status($status,$id){
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
        $this->masters_model->update_country_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Zone*/
    function zone($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('zone')));
        $meta = array('page_title' => lang('zone'), 'bc' => $bc);
        $this->page_construct('masters/zone', $meta, $this->data);
    }
    function getZone(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('zones')}.id as id, {$this->db->dbprefix('zones')}.name, p.name as parent_name")
            ->from("zones")
			->join("countries p", "p.id = zones.country_id ")
			->where('zones.is_delete', 0);
			
			
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_zone") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/zones/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_zone(){
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
        $this->form_validation->set_rules('name', lang("zone_name"), 'required|is_unique[zones.name]');    
		$this->form_validation->set_rules('country_id', lang("country"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkZone($this->input->post('name'), $this->input->post('country_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("country_already_exit"));
            	admin_redirect('masters/zone');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'country_id' => $this->input->post('country_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_zone')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/zone");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_zone($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("zone_added"));
            admin_redirect('masters/zone');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_zone', $this->data);
			
        }
    }
    function edit_zone($id){
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
		$result = $this->masters_model->getZoneby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("zone_name"), 'required');    
        $this->form_validation->set_rules('country_id', lang("country"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("zone_name"), 'is_unique[zones.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkZone($this->input->post('name'), $this->input->post('country_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("country_already_exit"));
					admin_redirect('masters/zone');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'country_id' => $this->input->post('country_id'),
            );
        } elseif ($this->input->post('edit_zone')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/zone");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_zone($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("zone_updated"));
            admin_redirect("masters/zone");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			$continent = $this->masters_model->getCountryby_ID($result->country_id, $countryCode);
			$this->data['continent']  = $continent;
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_zone', $this->data);
        }
    }
   
    function zone_status($status,$id){
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
        $this->masters_model->update_zone_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### State*/
    function state($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('state')));
        $meta = array('page_title' => lang('state'), 'bc' => $bc);
        $this->page_construct('masters/state', $meta, $this->data);
    }
    function getState(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('states')}.id as id, {$this->db->dbprefix('states')}.name, p.name as parent_name")
            ->from("states")
			->join("zones p", "p.id = states.zone_id ")
			->where('states.is_delete', 0);
			
			
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_state") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/states/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_state(){
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
		
        $this->form_validation->set_rules('name', lang("states_name"), 'required|is_unique[states.name]');    
		$this->form_validation->set_rules('zone_id', lang("zone"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			$check = $this->masters_model->checkState($this->input->post('name'), $this->input->post('zone_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("zone_already_exit"));
            	admin_redirect('masters/state');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'zone_id' => $this->input->post('zone_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_state')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/state");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_state($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("state_added"));
            admin_redirect('masters/state');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_state', $this->data);
			
        }
    }
    function edit_state($id){
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
		
		$result = $this->masters_model->getStateby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("state_name"), 'required');    
        $this->form_validation->set_rules('zone_id', lang("zone"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("state_name"), 'is_unique[states.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkState($this->input->post('name'), $this->input->post('zone_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("zone_already_exit"));
					admin_redirect('masters/state');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'zone_id' => $this->input->post('zone_id'),
            );
        } elseif ($this->input->post('edit_state')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/state");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_state($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("state_updated"));
            admin_redirect("masters/state");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			$country = $this->masters_model->getZoneby_ID($result->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_state', $this->data);
        }
    }
   
    function state_status($status,$id){
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
        $this->masters_model->update_state_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### City*/
    function city($action=false){
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
		
		$state = $_GET['state'];
		$zone = $_GET['zone'];
		$country = $_GET['country'];
		
		$this->data['countrys'] = $this->masters_model->getALLLicenseCountry();
		$this->data['zones'] = $this->masters_model->getZone_bycountry($country, $countryCode);
		$this->data['states'] = $this->masters_model->getState_byzone($zone, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('city')));
        $meta = array('page_title' => lang('city'), 'bc' => $bc);
        $this->page_construct('masters/city', $meta, $this->data);
    }
    function getCity(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$state = $_GET['state'];
		$zone = $_GET['zone'];
		$country = $_GET['country'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('cities')}.id as id, {$this->db->dbprefix('cities')}.name, p.name as state_name, z.name as zone_name, c.name as country_name")
            ->from("cities")
			->join("states p", "p.id = cities.state_id ")
			->join("zones z", "z.id = p.zone_id ")
			->join("countries c", "c.id = z.country_id ")
			->where('cities.is_delete', 0);
			
			
			
            
			if($country != 0){
				$this->datatables->where('z.country_id',$country);
			}
			if($zone != 0){
				$this->datatables->where('p.zone_id',$zone);
			}
			if($state != 0){
				$this->datatables->where('cities.state_id',$state);
			}
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_city/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_city") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_city/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/cities/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
    }
    function add_city(){
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
		
        $this->form_validation->set_rules('name', lang("city_name"), 'required|is_unique[cities.name]');    
		$this->form_validation->set_rules('state_id', lang("state"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			$check = $this->masters_model->checkCity($this->input->post('name'), $this->input->post('state_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("state_already_exit"));
            	admin_redirect('masters/city');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'state_id' => $this->input->post('state_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_city')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/city");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_city($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("city_added"));
            admin_redirect('masters/city');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_city', $this->data);
			
        }
    }
    function edit_city($id){
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
		
		$result = $this->masters_model->getCityby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("city_name"), 'required');    
        $this->form_validation->set_rules('state_id', lang("state"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("city_name"), 'is_unique[cities.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkCity($this->input->post('name'), $this->input->post('state_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("state_already_exit"));
					admin_redirect('masters/city');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'state_id' => $this->input->post('state_id'),
            );
        } elseif ($this->input->post('edit_city')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/city");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_city($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("city_updated"));
            admin_redirect("masters/city");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$zone = $this->masters_model->getStateby_ID($result->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			
		
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_city', $this->data);
        }
    }
   
    function city_status($status,$id){
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
        $this->masters_model->update_city_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	
	/*###### Area*/
    function area($action=false){
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
		$city = $_GET['city'];
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		$this->data['citys'] = $this->masters_model->AllgetCity();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('area')));
        $meta = array('page_title' => lang('area'), 'bc' => $bc);
        $this->page_construct('masters/area', $meta, $this->data);
    }
    function getArea($city){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$city = $_GET['city'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('areas')}.id as id, {$this->db->dbprefix('areas')}.name,  p.name as parent_name")
            ->from("areas")
			->join("cities p", "p.id = areas.city_id ")
			->where('areas.is_delete', 0);
			
			
			
            
			if($city != 0){
				$this->datatables->where('areas.city_id',$city);
			}
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_area/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_area") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_area/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/areas/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_area(){
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
		
        $this->form_validation->set_rules('name', lang("area_name"), 'required|is_unique[areas.name]');    
		$this->form_validation->set_rules('city_id', lang("city"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'city_id' => $this->input->post('city_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/area");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_area($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("area_added"));
            admin_redirect('masters/area');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_area', $this->data);
			
        }
    }
    function edit_area($id){
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
		
		$result = $this->masters_model->getAreaby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("area_name"), 'required');    
        //$this->form_validation->set_rules('city_id', lang("city"), 'required');   
        
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
				'name' => $this->input->post('name'),
				'city_id' => $this->input->post('city_id'),
            );
        } elseif ($this->input->post('edit_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/area");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_area($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("area_updated"));
            admin_redirect("masters/area");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$state = $this->masters_model->getCityby_ID($result->city_id, $countryCode);
			
			$zone = $this->masters_model->getStateby_ID($state->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			$this->data['state'] = $state;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			$this->data['citys'] = $this->masters_model->getCity_bystate($state->state_id, $countryCode);
			
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_area', $this->data);
        }
    }
   
    function area_status($status,$id){
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
        $this->masters_model->update_area_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Area*/
    function pincode($action=false){
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
		$area = $_GET['area'];
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		//$this->data['area'] = $this->masters_model->getALLArea();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pincode')));
        $meta = array('page_title' => lang('pincode'), 'bc' => $bc);
        $this->page_construct('masters/pincode', $meta, $this->data);
    }
    function getPincode($area){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$area = $_GET['area'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('pincode')}.id as id, {$this->db->dbprefix('pincode')}.name,  {$this->db->dbprefix('pincode')}.pincode,  p.name as parent_name")
            ->from("pincode")
			->join("areas p", "p.id = pincode.area_id")
			->where('pincode.is_delete', 0);
			
			
			
           
			if($area != 0){
				$this->datatables->where('pincode.area_id',$area);
			}
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_pincode/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_pincode") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_pincode/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/pincode/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_pincode(){
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
		
        $this->form_validation->set_rules('pincode', lang("pincode"), 'required|is_unique[pincode.pincode]');    
		$this->form_validation->set_rules('area_id', lang("area"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'pincode' => $this->input->post('pincode'),
				'area_id' => $this->input->post('area_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_pincode')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/pincode");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_pincode($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("pincode_added"));
            admin_redirect('masters/pincode');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_pincode', $this->data);
			
        }
    }
    function edit_pincode($id){
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
		
		$result = $this->masters_model->getPincodeby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('pincode', lang("pincode"), 'required');    
        //$this->form_validation->set_rules('city_id', lang("city"), 'required');   
        
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
				'name' => $this->input->post('name'),
				'pincode' => $this->input->post('pincode'),
				'area_id' => $this->input->post('area_id'),
            );
        } elseif ($this->input->post('edit_pincode')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/pincode");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_pincode($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("pincode_updated"));
            admin_redirect("masters/pincode");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$city = $this->masters_model->getAreaby_ID($result->area_id, $countryCode);
			$state = $this->masters_model->getCityby_ID($city->city_id, $countryCode);
			$zone = $this->masters_model->getStateby_ID($state->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			$this->data['state'] = $state;
			$this->data['city'] = $city;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			$this->data['citys'] = $this->masters_model->getCity_bystate($state->state_id, $countryCode);
			$this->data['areas'] = $this->masters_model->getArea_bycity($city->city_id, $countryCode);
			
			
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_pincode', $this->data);
        }
    }
   
    function pincode_status($status,$id){
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
        $this->masters_model->update_pincode_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
    /*#### Json Country Zone State city area*/
	function getTaxitype_byCountry(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data = $this->masters_model->getTaxitype_byCountry($countryCode);
        $options = array();
        if($data){
            foreach($data['type'] as $k => $rowt){
                $options['type'][$k]['id'] = $rowt->id;
                $options['type'][$k]['text'] = $rowt->name;
            }
			foreach($data['make'] as $k => $rowm){
                $options['make'][$k]['id'] = $rowm->id;
                $options['make'][$k]['text'] = $rowm->name;
            }
        }
        echo json_encode($options);exit;
	}
	
	function getCountry_bylicensetype(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $country_id = $this->input->post('country_id');
        $data = $this->masters_model->getCountry_bylicensetype($country_id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getHelp_main_byhelp(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $parent_id = $this->input->post('parent_id');
        $data = $this->masters_model->getHelp_main_byhelp($parent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getHelp_sub_byhelp_main(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $parent_id = $this->input->post('parent_id');
        $data = $this->masters_model->getHelp_sub_byhelp_main($parent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	/*##*/
	
    function getCountry_bycontinent(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $continent_id = $this->input->post('continent_id');
        $data = $this->masters_model->getCountry_bycontinent($continent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getZone_bycountry(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $country_id = $this->input->post('country_id');
        $data = $this->masters_model->getZone_bycountry($country_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getState_byzone(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $zone_id = $this->input->post('zone_id');
        $data = $this->masters_model->getState_byzone($zone_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getCity_bystate(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $state_id = $this->input->post('state_id');
        $data = $this->masters_model->getCity_bystate($state_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getArea_bycity(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $city_id = $this->input->post('city_id');
        $data = $this->masters_model->getArea_bycity($city_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
    /*#### user groups*/    
    function usergroups($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('groups')));
        $meta = array('page_title' => lang('groups'), 'bc' => $bc);
        $this->page_construct('masters/user_groups', $meta, $this->data);
    }
    function getUserGroups(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,name")
            ->from("groups");
	    	//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_group/$1') . "' class='tip' title='" . lang("edit_user_group") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_user_group/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
			

        //->unset_column('id');
        echo $this->datatables->generate();
    }
	
    function add_user_group(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('groups', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', 'name has been already exits');
            	admin_redirect("masters/usergroups");
				exit;	
			}
			
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
        }
        if ($this->form_validation->run() == true && $this->masters_model->add_user_group($data, $countryCode)){
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('masters/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/usergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('add_user_group')));
            $meta = array('page_title' => lang('add_user_group'), 'bc' => $bc);
            $this->page_construct('masters/add_user_group', $meta, $this->data);
        }
    }
	
    function edit_user_group($id){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->update_user_group($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('masters/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/usergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('edit_user_group')));
            $meta = array('page_title' => lang('edit_user_group'), 'bc' => $bc);
			$this->data['group'] = $this->masters_model->getUserGroupby_ID($id, $countryCode);
            $this->page_construct('masters/edit_user_group', $meta, $this->data);
        }
    }
	
	/*###### Help*/
    function help($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help')));
        $meta = array('page_title' => lang('help'), 'bc' => $bc);
        $this->page_construct('masters/help', $meta, $this->data);
    }
    function getHelp(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id,name,type,status")
            ->from("help");
			
			
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_continents") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_help/$1') . "' data-toggle='modal'  data-target='#myModal' title='Click here to full Edit'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$add = "<a href='" . admin_url('masters/help_main/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[help.name]');    
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'type' => json_encode($this->input->post('type')),
                'status' => 1,
            );
        }elseif ($this->input->post('add_help')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_added"));
            admin_redirect('masters/help');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_help', $this->data);
        }
    }
    function edit_help($id){
		$result = $this->masters_model->getHelpby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("_name"), 'is_unique[help.name]');
        }
		
        
        if ($this->form_validation->run() == true) {

            $data = array(
				'name' => $this->input->post('name'),
				'type' => json_encode($this->input->post('type')),
            );
        } elseif ($this->input->post('edit_help')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_updated"));
            admin_redirect("masters/help");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help', $this->data);
        }
    }
   
    function help_status($status,$id){
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
        $this->masters_model->update_help_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Main*/
    function help_main($id=false){
		
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
		$this->data['parnet_id'] = $id;
		$help_name = $this->masters_model->getHelpby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_main')));
        $meta = array('page_title' => $help_name->name.' - Help Main', 'bc' => $bc);
        $this->page_construct('masters/help_main', $meta, $this->data);
    }
    function getHelp_main($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_main')}.id as id, {$this->db->dbprefix('help_main')}.name, p.name as parent_name, {$this->db->dbprefix('help_main')}.status as status, country.name as instance_country")
            ->from("help_main")
			->join("help p", "p.id = help_main.parent_id ")
			->join("countries country", " country.iso = help_main.is_country", "left")
			->where('help_main.is_delete', 0);
			
			if($parent_id != ''){
				$this->datatables->where("help_main.parent_id", $parent_id);
			}
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_main.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_main.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_country") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_help_main/$1') . "' data-toggle='modal'  data-target='#myModal' title='Click here to edit'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$add = "<a href='" . admin_url('masters/help_sub/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/help_main/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div><div>".$delete."</div>", "id");
			
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help_main($id){
		
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('parent_id', lang("help"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkHelp_main($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("help_already_exit"));
            	admin_redirect('masters/help_main/'.$id);
			}
            $data = array(
                'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
                'status' => 1,
            );

        }elseif ($this->input->post('add_help_main')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_main/".$id);
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_main($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_main_added"));
            admin_redirect('masters/help_main/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/add_help_main', $this->data);
			
        }
    }
    function edit_help_main($id){
		$result = $this->masters_model->getHelp_mainby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		$this->form_validation->set_rules('parent_id', lang("help"), 'required');      
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkHelp_main($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("help_already_exit"));
					admin_redirect('masters/help_main/'.$id);
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
            );

        } elseif ($this->input->post('edit_help_main')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_main/".$id);
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help_main($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_main_updated"));
            admin_redirect("masters/help_main/".$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help_main', $this->data);
        }
    }
   
    function help_main_status($status,$id){
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
        $this->masters_model->update_help_main_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Sub*/
    function help_sub($id=false){
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
		$this->data['parnet_id'] = $id;
		$help_main_name = $this->masters_model->getHelp_mainby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_sub')));
        $meta = array('page_title' => $help_main_name->name.' - Help Sub', 'bc' => $bc);
        $this->page_construct('masters/help_sub', $meta, $this->data);
    }
    function getHelp_sub($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_sub')}.id as id, {$this->db->dbprefix('help_sub')}.name, p.name as parent_name, {$this->db->dbprefix('help_sub')}.status as status, country.name as instance_country")
            ->from("help_sub")
			->join("help_main p", "p.id = help_sub.parent_id ")
			->join("countries country", " country.iso = help_sub.is_country", "left")
			->where('help_sub.is_delete', 0);
			if($parent_id != ''){
				$this->datatables->where("help_sub.parent_id", $parent_id);
			}
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_sub.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_sub.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_zone") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><div class='kapplist-view1'></div></a>";
			///$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_help_sub/$1') . "' data-toggle='modal'  data-target='#myModal' title='Click here to edit'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$add = "<a href='" . admin_url('masters/help_form/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/help_sub/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete' ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help_sub($id){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('parent_id', lang("parent_id"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkHelp_sub($this->input->post('help_sub'), $this->input->post('parent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("parent_already_exit"));
            	admin_redirect('masters/help_sub');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_help_sub')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_sub/".$di);
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_sub($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_sub_added"));
            admin_redirect('masters/help_sub/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['id'] = $id;
			$help = $this->masters_model->getHelp_mainby_ID($id, $countryCode);
			
			$this->data['help_parent_id']  = $help->parent_id;
			$this->data['help_main'] = $this->masters_model->getHelp_main_byhelp($help->parent_id, $countryCode);
			
            $this->load->view($this->theme . 'masters/add_help_sub', $this->data);
			
        }
    }
    function edit_help_sub($id){
		$result = $this->masters_model->getHelp_subby_ID($id);
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        $this->form_validation->set_rules('parent_id', lang("parent"), 'required');   
       
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkHelp_sub($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("parent_already_exit"));
					admin_redirect('masters/help_sub');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
            );
        } elseif ($this->input->post('edit_help_sub')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_sub");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help_sub($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_sub_updated"));
            admin_redirect("masters/help_sub");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['result'] = $result;
			$help = $this->masters_model->getHelp_mainby_ID($result->parent_id, $countryCode);
			$this->data['help']  = $help;
			$this->data['help_main'] = $this->masters_model->getHelp_main_byhelp($help->parent_id, $countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help_sub', $this->data);
        }
    }
   
    function help_sub_status($status,$id){
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
        $this->masters_model->update_help_sub_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Form*/
    function help_form($id=false){
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
		$this->data['parnet_id'] = $id;
		$help_sub_name = $this->masters_model->getHelp_subby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_form')));
        $meta = array('page_title' => $help_sub_name->name.' - Help Form', 'bc' => $bc);
        $this->page_construct('masters/help_form', $meta, $this->data);
    }
    function getHelp_form($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_form')}.id as id, {$this->db->dbprefix('help_form')}.name,   {$this->db->dbprefix('help_form')}.form_type, p.name as parent_name, {$this->db->dbprefix('help_form')}.status as status, country.name as instance_country")
            ->from("help_form")
			->join("countries country", " country.iso = help_form.is_country", "left")
			->join("help_sub p", "p.id = help_form.parent_id ");
			
			if($parent_id != ''){
				$this->datatables->where("help_form.parent_id", $parent_id);
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_form.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_form.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_state") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><div class='kapplist-view1'></div></a>";
			//$this->datatables->add_column("Actions", "<div></div>", "id");
			$delete = "<a href='" . admin_url('welcome/delete/help_form/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete' ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
  
	function add_help_form($id){
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
		$data = array();
		$this->form_validation->set_rules('parent_id', lang("parent"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			$details = $this->input->post('details');
			if(!empty($this->input->post('name'))){
				for($i=0; $i<count($this->input->post('name')); $i++){
					
					$data[$i] = array('name' => $_POST['name'][$i], 'form_type' => $_POST['form_type'][$i], 'form_name' => $_POST['form_name'][$i], 'parent_id' => $this->input->post('parent_id'), 'status' => 1, 'created_on' => date('Y-m-d H:i:s'));
						
				}
				
			}
			
		
        }elseif ($this->input->post('add_help_form')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_form/".$di);
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_form($data, $details, $this->input->post('parent_id'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_form_added"));
            admin_redirect('masters/help_form');
        } else {
			$this->data['id'] = $id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
            $this->load->view($this->theme . 'masters/add_help_form', $this->data);
			
        }
    }
	
	
   
    function help_form_status($status,$id){
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
        $this->masters_model->update_help_form_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function bank_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Bank');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('account_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('bank_name'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('branch_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('ifsc_code'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLBank($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->account_holder_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->account_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->bank_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->branch_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $value-> ifsc_code);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'bank_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function user_department_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('User Department');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLUser_department();
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'user_department_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function tax_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Tax');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('percentage'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTax($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->tax_name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->percentage);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function taxi_type_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Type');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_type($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_type_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	

	function taxi_make_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Make');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_make($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_make_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function taxi_model_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Model');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('Make'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('Type'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:C1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_model($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->make);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->type);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_model_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

	
	function taxi_fuel_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Type');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_fuel($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_fuel_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function continents_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Continents');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLContinents($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->code);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'continents_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function country_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Country');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('phonecode'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLCountry($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->phonecode);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'country_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function zone_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Zone');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLZone($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'zone_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function state_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('State');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLState($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'state_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function city_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('City');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLCity($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'city_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function area_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Area');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLArea($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'area_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function pincode_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Pincode');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLPincode($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'pincode_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

	/*###### Payment Gateway*/
    function payment_gateway($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_make')));
        $meta = array('page_title' => lang('payment_gateway'), 'bc' => $bc);
        $this->page_construct('masters/payment_gateway', $meta, $this->data);
    }
    function getPayment_gateway(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('payment_gateway')}.id as id, {$this->db->dbprefix('payment_gateway')}.name,  {$this->db->dbprefix('payment_gateway')}.status as status, country.name as instance_country")
            ->from("payment_gateway")
			->join("countries country", " country.iso = payment_gateway.is_country", "left")
			->where('payment_gateway.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("payment_gateway.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("payment_gateway.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_payment_gateway/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/payment_gateway/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_payment_gateway(){
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			
            $data = array(
                'name' => $this->input->post('name'),
				'code' => strtolower(str_replace(' ', '', $this->input->post('name'))),
                'status' => 1,
				'created_on' => date('Y-m-d H:i:s')
            );
			
        }elseif ($this->input->post('add_payment_gateway')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/payment_gateway");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_payment_gateway($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("payment_gateway_added"));
            admin_redirect('masters/payment_gateway');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->load->view($this->theme . 'masters/add_payment_gateway', $this->data);
			
        }
    }
    function edit_payment_gateway($id){
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
		$result = $this->masters_model->getPayment_gatewayby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		    
       
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('payment_gateway', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', ' name has been already exits');
					admin_redirect("masters/payment_gateway");
					exit;	
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'code' => strtolower(str_replace(' ', '', $this->input->post('name'))),
				'created_on' => date('Y-m-d H:i:s')
            );
			
		
			
        } elseif ($this->input->post('edit_payment_gateway')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/payment_gateway");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_payment_gateway($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("payment_gateway_updated"));
            admin_redirect("masters/payment_gateway");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_payment_gateway', $this->data);
        }
    }
   
    function payment_gateway_status($status,$id){
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
        $this->masters_model->update_payment_gateway_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	

	function payment_gateway_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Payment gateway');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLPayment_gateway($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'payment_gateway_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	
}
