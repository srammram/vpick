<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
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
		$this->load->admin_model('users_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('people_model');
    }
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
		
	/*###### users*/
    function profile($action=false){
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
		$this->data['user'] = $this->users_model->getUser($this->session->userdata('user_id'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('profile')));
        $meta = array('page_title' => lang('profile'), 'bc' => $bc);
		
        $this->page_construct('users/profile', $meta, $this->data);
    }

	function permissionlist($action = NULL)
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('permissionlist')));
        $meta = array('page_title' => lang('permissionlist'), 'bc' => $bc);
        $this->page_construct('users/permissionlist', $meta, $this->data);
    }
    function getPermission(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('all_permission')}.id as id, {$this->db->dbprefix('all_permission')}.is_country as is_country, {$this->db->dbprefix('all_permission')}.department_id as department_id, {$this->db->dbprefix('all_permission')}.designation_id as designation_id, g.name as group_name, d.name as department_name, r.position, r.access_area, country.name as instance_country")
            ->from("all_permission")
			->join("countries country", " country.iso = all_permission.is_country", "left")
			->join("groups g", " g.id = all_permission.group_id", "left")
			->join("user_department d", " d.id = all_permission.department_id", "left")
			->join("user_roles r", " r.id = all_permission.designation_id", "left");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("all_permission.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("all_permission.is_country", $countryCode);
			}
			
           
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('users/permission/?is_country=$2&department_id=$3&designation_id=$4&id=$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/all_permission/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id, is_country, department_id, designation_id");

        $this->datatables->unset_column('id');
		$this->datatables->unset_column('is_country');
		$this->datatables->unset_column('department_id');
		$this->datatables->unset_column('designation_id');

        echo $this->datatables->generate();
    }

	function permission(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}

		$department_id = $this->input->get('department_id') ? $this->input->get('department_id') : $this->input->post('department');
		$designation_id = $this->input->get('designation_id') ? $this->input->get('designation_id') : $this->input->post('designation');

		$id = $this->input->get('id');

		$result = $this->users_model->getexitPermission($department_id, $designation_id, $this->Employee, $countryCode, $id);
		
		$id = $result->id ? $result->id : $id;

		if($this->input->post('add_permission') == 'Submit'){
			
				$insert = array(
					'group_id' => $this->input->post('group_id'),
					'department_id' => $department_id,
					'designation_id' => $designation_id,
					'statistics-index' => $this->input->post('statistics-index'),
					'live_tracking-index' => $this->input->post('live_tracking-index'),
					'search_heat_map-index' => $this->input->post('search_heat_map-index'),
					'notification-index' => $this->input->post('notification-index'),
					'staff-index' => $this->input->post('staff-index'),
					'staff-add' => $this->input->post('staff-add'),
					'staff-edit' => $this->input->post('staff-edit'),
					'staff-delete' => $this->input->post('staff-delete'),
					'staff-approved' => $this->input->post('staff-approved'),
					'driver-index' => $this->input->post('driver-index'),
					'driver-add' => $this->input->post('driver-add'),
					'driver-edit' => $this->input->post('driver-edit'),
					'driver-delete' => $this->input->post('driver-delete'),
					'driver-approved' => $this->input->post('driver-approved'),
					'cab-index' => $this->input->post('cab-index'),
					'cab-add' => $this->input->post('cab-add'),
					'cab-edit' => $this->input->post('cab-edit'),
					'cab-delete' => $this->input->post('cab-delete'),
					'cab-approved' => $this->input->post('cab-approved'),
					'customer-index' => $this->input->post('customer-index'),
					'customer-add' => $this->input->post('customer-add'),
					'customer-delete' => $this->input->post('customer-delete'),
					'customer-view' => $this->input->post('customer-view'),
					'rides-index' => $this->input->post('rides-index'),
					'rides-delete' => $this->input->post('rides-delete'),
					'rides-view' => $this->input->post('rides-view'),
					'city_rides-index' => $this->input->post('city_rides-index'),
					'city_rides-add' => $this->input->post('city_rides-add'),
					'city_rides-edit' => $this->input->post('city_rides-edit'),
					'city_rides-delete' => $this->input->post('city_rides-delete'),
					'outstation-index' => $this->input->post('outstation-index'),
					'outstation-add' => $this->input->post('outstation-add'),
					'outstation-edit' => $this->input->post('outstation-edit'),
					'outstation-delete' => $this->input->post('outstation-delete'),
					'rentals-index' => $this->input->post('rentals-index'),
					'rentals-add' => $this->input->post('rentals-add'),
					'rentals-edit' => $this->input->post('rentals-edit'),
					'rentals-delete' => $this->input->post('rentals-delete'),
					'settings-index' => $this->input->post('settings-index'),
					'currency-index' => $this->input->post('currency-index'),
					'currency-add' => $this->input->post('currency-add'),
					'currency-edit' => $this->input->post('currency-edit'),
					'currency-delete' => $this->input->post('currency-delete'),
					'currency-status' => $this->input->post('currency-status'),
					'cancel_master-index' => $this->input->post('cancel_master-index'),
					'cancel_master-add' => $this->input->post('cancel_master-add'),
					'cancel_master-edit' => $this->input->post('cancel_master-edit'),
					'cancel_master-delete' => $this->input->post('cancel_master-delete'),
					'cancel_master-status' => $this->input->post('cancel_master-status'),
					'discount-index' => $this->input->post('discount-index'),
					'discount-add' => $this->input->post('discount-add'),
					'discount-edit' => $this->input->post('discount-edit'),
					'discount-delete' => $this->input->post('discount-delete'),
					'discount-status' => $this->input->post('discount-status'),
					'company-index' => $this->input->post('company-index'),
					'company-add' => $this->input->post('company-add'),
					'company-delete' => $this->input->post('company-delete'),
					'company-status' => $this->input->post('company-status'),
					'wallet_offer-index' => $this->input->post('wallet_offer-index'),
					'wallet_offer-add' => $this->input->post('wallet_offer-add'),
					'wallet_offer-edit' => $this->input->post('wallet_offer-edit'),
					'wallet_offer-delete' => $this->input->post('wallet_offer-delete'),
					'wallet_offer-status' => $this->input->post('wallet_offer-status'),
					'bank-index' => $this->input->post('bank-index'),
					'bank-add' => $this->input->post('bank-add'),
					'bank-edit' => $this->input->post('bank-edit'),
					'bank-delete' => $this->input->post('bank-delete'),
					'bank-status' => $this->input->post('bank-status'),
					'payment_gateway-index' => $this->input->post('payment_gateway-index'),
					'payment_gateway-add' => $this->input->post('payment_gateway-add'),
					'payment_gateway-edit' => $this->input->post('payment_gateway-edit'),
					'payment_gateway-delete' => $this->input->post('payment_gateway-delete'),
					'payment_gateway-status' => $this->input->post('payment_gateway-status'),
					'tax-index' => $this->input->post('tax-index'),
					'tax-add' => $this->input->post('tax-add'),
					'tax-edit' => $this->input->post('tax-edit'),
					'tax-delete' => $this->input->post('tax-delete'),
					'tax-status' => $this->input->post('tax-status'),
					'cab_type-index' => $this->input->post('cab_type-index'),
					'cab_type-add' => $this->input->post('cab_type-add'),
					'cab_type-edit' => $this->input->post('cab_type-edit'),
					'cab_type-delete' => $this->input->post('cab_type-delete'),
					'cab_type-status' => $this->input->post('cab_type-status'),
					'cab_make-index' => $this->input->post('cab_make-index'),
					'cab_make-add' => $this->input->post('cab_make-add'),
					'cab_make-edit' => $this->input->post('cab_make-edit'),
					'cab_make-delete' => $this->input->post('cab_make-delete'),
					'cab_make-status' => $this->input->post('cab_make-status'),
					'cab_model-index' => $this->input->post('cab_model-index'),
					'cab_model-add' => $this->input->post('cab_model-add'),
					'cab_model-edit' => $this->input->post('cab_model-edit'),
					'cab_model-delete' => $this->input->post('cab_model-delete'),
					'cab_model-status' => $this->input->post('cab_model-status'),
					'cab_fuel-index' => $this->input->post('cab_fuel-index'),
					'cab_fuel-add' => $this->input->post('cab_fuel-add'),
					'cab_fuel-edit' => $this->input->post('cab_fuel-edit'),
					'cab_fuel-delete' => $this->input->post('cab_fuel-delete'),
					'cab_fuel-status' => $this->input->post('cab_fuel-status'),
					'continents-index' => $this->input->post('continents-index'),
					'continents-add' => $this->input->post('continents-add'),
					'continents-edit' => $this->input->post('continents-edit'),
					'continents-delete' => $this->input->post('continents-delete'),
					'countries-index' => $this->input->post('countries-index'),
					'countries-add' => $this->input->post('countries-add'),
					'countries-edit' => $this->input->post('countries-edit'),
					'countries-delete' => $this->input->post('countries-delete'),
					'zone-index' => $this->input->post('zone-index'),
					'zone-add' => $this->input->post('zone-add'),
					'zone-edit' => $this->input->post('zone-edit'),
					'zone-delete' => $this->input->post('zone-delete'),
					'state-index' => $this->input->post('state-index'),
					'state-add' => $this->input->post('state-add'),
					'state-edit' => $this->input->post('state-edit'),
					'state-delete' => $this->input->post('state-delete'),
					'city-index' => $this->input->post('city-index'),
					'city-add' => $this->input->post('city-add'),
					'city-edit' => $this->input->post('city-edit'),
					'city-delete' => $this->input->post('city-delete'),
					'areas-index' => $this->input->post('areas-index'),
					'areas-add' => $this->input->post('areas-add'),
					'areas-edit' => $this->input->post('areas-edit'),
					'areas-delete' => $this->input->post('areas-delete'),
					'pincode-index' => $this->input->post('pincode-index'),
					'pincode-add' => $this->input->post('pincode-add'),
					'pincode-edit' => $this->input->post('pincode-edit'),
					'pincode-delete' => $this->input->post('pincode-delete'),
					'help-index' => $this->input->post('help-index'),
					'help-add' => $this->input->post('help-add'),
					'help-edit' => $this->input->post('help-edit'),
					'help-status' => $this->input->post('help-status'),
					'import_csv_common_cab-index' => $this->input->post('import_csv_common_cab-index'),
					'import_csv_common_location-index' => $this->input->post('import_csv_common_location-index'),
					'wallets_dashboard-index' => $this->input->post('wallets_dashboard-index'),
					'wallets_customer-index' => $this->input->post('wallets_customer-index'),
					'wallets_driver-index' => $this->input->post('wallets_driver-index'),
					'wallets_owner-index' => $this->input->post('wallets_owner-index'),
					'incentive_list-index' => $this->input->post('incentive_list-index'),
					'incentive_list-add' => $this->input->post('incentive_list-add'),
					'incentive_list-edit' => $this->input->post('incentive_list-edit'),
					'incentive_list-view' => $this->input->post('incentive_list-view'),
					'incentive_group-index' => $this->input->post('incentive_group-index'),
					'incentive_group-add' => $this->input->post('incentive_group-add'),
					'incentive_group-edit' => $this->input->post('incentive_group-edit'),
					'incentive_group-delete' => $this->input->post('incentive_group-delete'),
					'offers_list-index' => $this->input->post('offers_list-index'),
					'crm_dashboard-index' => $this->input->post('crm_dashboard-index'),
					'crm_dashboard-add' => $this->input->post('crm_dashboard-add'),
					'crm_dashboard-edit' => $this->input->post('crm_dashboard-edit'),
					'crm_enquiry-index' => $this->input->post('crm_enquiry-index'),
					'crm_enquiry-status' => $this->input->post('crm_enquiry-status'),
					'crm_enquiry-view' => $this->input->post('crm_enquiry-view'),
					'booking_rides_dashboard-index' => $this->input->post('booking_rides_dashboard-index'),
					'booking_rides_dashboard-add' => $this->input->post('booking_rides_dashboard-add'),
					'accounts_dashboard-index' => $this->input->post('accounts_dashboard-index'),
					'settlement_branch-index' => $this->input->post('settlement_branch-index'),
					'account_settlementlist-index' => $this->input->post('account_settlementlist-index'),
					'account_settlementlist-approved' => $this->input->post('account_settlementlist-approved'),
					'account_owner-index' => $this->input->post('account_owner-index'),
					'bank_excel-index' => $this->input->post('bank_excel-index'),
					'reconcilation-index' => $this->input->post('reconcilation-index'),
					'trip-index' => $this->input->post('trip-index'),
					'driver_payment-index' => $this->input->post('driver_payment-index'),
					'reports_dashboard-index' => $this->input->post('reports_dashboard-index'),
					'is_country' => $countryCode
					

				);
				$q = $this->users_model->insertPermission($insert, $id, $countryCode);
				if($q == TRUE){
					admin_redirect('users/permissionlist');
				}
				//$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				
				
			
		}
		
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['p'] = $result;
		
		$this->data['department_id'] = $department_id;
		$this->data['id'] = $id;
		$this->data['group_id'] = $this->Employee;
		$this->data['country'] = $countryCode;
		$this->data['designation_id'] = $designation_id;
		$this->data['user_department'] = $this->users_model->getALLUser_department();
		$this->data['user_designation'] = $this->users_model->getALLUser_designation();
		$this->data['department_name'] = $this->users_model->getname_department($department_id);
		$this->data['designation_name'] = $this->users_model->getname_designation($designation_id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('permission')));
        $meta = array('page_title' => lang('permission'), 'bc' => $bc);
		
        $this->page_construct('users/permission', $meta, $this->data);
    }
	
	function edit_admin($user_id){
		$image_path = base_url('assets/uploads/');
		
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
		$group_id = $this->Admin;
		
		 $this->db->select("u.id as id, u.first_name, u.last_name, u.email, u.gender, u.dob, u.photo")
            ->from("users u")
			->where("u.id", $user_id);		
			$q = $this->db->get();
			if ($q->num_rows() > 0) {
				$row = $q->row();
				
				if($row->photo !=''){
					$row->photo_img = $image_path.$row->photo;
				}else{
					$row->photo_img = $image_path.'no_image.png';
				}
				
				
			}
				
			
		
		
		$result = $row;
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');		
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
		   
		   $user = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
		   );
		   
		   		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/admin/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				
				$user['photo'] = 'user/admin/'.$photo;
				$config = NULL;
			}else{
				
				$user['photo'] = $result->photo;		
			}
			
			
			
			
			//$this->sma->print_arrays($user);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_admin($this->session->userdata('user_id'), $user)){
			
            $this->session->set_flashdata('message', lang("admin_updated"));
            admin_redirect('users/profile');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('profile')), array('link' => '#', 'page' => lang('edit_admin')));
            $meta = array('page_title' => lang('edit_admin'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			
			$this->data['user_id'] = $user_id;
			
		
            $this->page_construct('users/edit_admin', $meta, $this->data);
        }        
    
	}
	
	function edit_employee($user_id){
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
		$group_id = $this->Employee;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 AND up.is_country = '".$countryCode."'", "left")
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 AND ub.is_country = '".$countryCode."'", "left")
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 AND ud.is_country = '".$countryCode."'", "left")
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 AND uadd.is_country = '".$countryCode."'", "left")
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->db->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->db->where("u.is_country", $countryCode);
			}
			
            
			$q = $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your account has been deactive. so if can not edit."));
            	admin_redirect('users/profile');
			}
		}
		
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'), $countryCode);
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		$this->form_validation->set_rules('mobile', lang("mobile"), 'required');
		if($result->mobile != $this->input->post('mobile')){
        	$this->form_validation->set_rules('mobile', lang("mobile"), 'is_unique[users.mobile]');  
		}
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('local_address', lang("address"), 'required');
		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
		
		$this->form_validation->set_rules('aadhaar_no', lang("aadhaar_no"), 'required');
		$this->form_validation->set_rules('pancard_no', lang("pancard_no"), 'required');
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->mobile == $this->input->post('mobile') && $result->country_code == $this->input->post('country_code')){
				$is_edit = 1;
				$is_approved = 1;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
			}else{
				$is_edit = 0;
				$is_approved = 0;
				$approved_on = '0000-00-00';
				$approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				
				'is_edit' => 1
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
				$user_profile['photo'] = 'user/driver/'.$photo;
				$user['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;	
				$user['photo'] = $result->photo;		
			}
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_continent_id') == $result->local_continent_id && $this->input->post('local_country_id') == $result->local_country_id && $this->input->post('local_zone_id') == $result->local_zone_id && $this->input->post('local_state_id') == $result->local_state_id && $this->input->post('local_city_id') == $result->local_city_id && $this->input->post('local_area_id') == $result->local_area_id && $_FILES['local_image']['size'] == 0){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_continent_id') == $result->permanent_continent_id && $this->input->post('permanent_country_id') == $result->permanent_country_id && $this->input->post('permanent_zone_id') == $result->permanent_zone_id && $this->input->post('permanent_state_id') == $result->permanent_state_id && $this->input->post('permanent_city_id') == $result->permanent_city_id && $this->input->post('permanent_area_id') == $result->permanent_area_id && $_FILES['permanent_image']['size'] == 0){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_address' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_address') : '',
				'local_pincode' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_pincode') : '',
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_address') : '',
				'permanent_pincode' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_pincode') : '',
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				'is_edit' => 1
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
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}else{
				$user_address['local_image'] = $result->local_image;
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
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}else{
				$user_address['permanent_image'] = $result->permanent_image;
			}
			
			if($this->input->post('account_holder_name') == $result->account_holder_name && $this->input->post('account_no') == $result->account_no && $this->input->post('bank_name') == $result->bank_name && $this->input->post('branch_name') == $result->branch_name && $this->input->post('ifsc_code') == $result->ifsc_code){
				$is_verify = $result->account_verify;
				$approved_by = $result->account_approved_by;
				$approved_on = $result->account_approved_on;
			}else{
				$is_verify = 0;
				$approved_by = 0;
				$approved_on = '0000:00:00 00:00:00';
			}
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $is_verify,
				'approved_by' => $approved_by,
				'approved_on' => $approved_on,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			if($this->input->post('aadhaar_no') == $result->aadhaar_no && $_FILES['aadhaar_image']['size'] == 0){
				$aadhar_verify = $result->aadhar_verify;
				$aadhar_approved_by = $result->aadhar_approved_by;
				$aadhar_approved_on = $result->aadhar_approved_on;
			}else{
				$aadhar_verify = 0;
				$aadhar_approved_by = 0;
				$aadhar_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('pancard_no') == $result->pancard_no && $_FILES['pancard_image']['size'] == 0){
				$pancard_verify = $result->pancard_verify;
				$pancard_approved_by = $result->pancard_approved_by;
				$pancard_approved_on = $result->pancard_approved_on;
			}else{
				$pancard_verify = 0;
				$pancard_approved_by = 0;
				$pancard_approved_on = '0000:00:00 00:00:00';
			}
			
				
			$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $this->input->post('aadhaar_no') : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('pancard_no') : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
									
				'is_edit' => 1	,
				'complete_document' => 1	
				
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
				$config = NULL;
			}else{
				$user_document['aadhaar_image'] = $result->aadhaar_image;
			}
			
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
				
				$config = NULL;
			}else{
				$user_document['pancard_image'] = $result->pancard_image;
			}
			
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_employee($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document, $countryCode)){
			
            $this->session->set_flashdata('message', lang("employee_updated"));
            admin_redirect('users/profile');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('profile')), array('link' => '#', 'page' => lang('edit_employee')));
            $meta = array('page_title' => lang('edit_employee'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor($countryCode);
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['user_id'] = $user_id;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->local_continent_id);
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result->permanent_city_id);
			$this->data['license_type'] = $this->masters_model->getALLLicense_type();
            $this->page_construct('users/edit_employee', $meta, $this->data);
        }        
    
	}
	
	function edit_vendor($user_id){
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
		$group_id = $this->Vendor;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status, country.name as instance_country ")
            ->from("users u")
			->join("countries country", " country.iso = u.is_country", "left")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1 ", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("u.is_country", $countryCode);
			}
			
			$q = $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your account has been deactive. so if can not edit."));
            	admin_redirect('users/profile');
			}
		}
		
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'), $countryCode);
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->mobile == $this->input->post('mobile') && $result->country_code == $this->input->post('country_code')){
				$is_edit = 1;
				$is_approved = 1;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
			}else{
				$is_edit = 0;
				$is_approved = 0;
				$approved_on = '0000-00-00';
				$approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1
			
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
				$user_profile['photo'] = 'user/driver/'.$photo;
				$user['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;
				$user['photo'] = $result->photo;
			}
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_continent_id') == $result->local_continent_id && $this->input->post('local_country_id') == $result->local_country_id && $this->input->post('local_zone_id') == $result->local_zone_id && $this->input->post('local_state_id') == $result->local_state_id && $this->input->post('local_city_id') == $result->local_city_id && $this->input->post('local_area_id') == $result->local_area_id && $_FILES['local_image']['size'] == 0){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('local_pincode') == $result->local_pincode && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_pincode' => $this->input->post('local_pincode'),
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				'complete_address' => 1,
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				'is_edit' => 1
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
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}else{
				$user_address['local_image'] = $res->local_image;
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
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}else{
				$user_address['permanent_image'] = $result->permanent_image;
			}
			
			if($this->input->post('account_holder_name') == $result->account_holder_name && $this->input->post('account_no') == $result->account_no && $this->input->post('bank_name') == $result->bank_name && $this->input->post('branch_name') == $result->branch_name && $this->input->post('ifsc_code') == $result->ifsc_code){
				$is_verify = $result->account_verify;
				$approved_by = $result->account_approved_by;
				$approved_on = $result->account_approved_on;
			}else{
				$is_verify = 0;
				$approved_by = 0;
				$approved_on = '0000:00:00 00:00:00';
			}
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $is_verify,
				'approved_by' => $approved_by,
				'approved_on' => $approved_on,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			if($this->input->post('aadhaar_no') == $result->aadhaar_no && $_FILES['aadhaar_image']['size'] == 0){
				$aadhar_verify = $result->aadhar_verify;
				$aadhar_approved_by = $result->aadhar_approved_by;
				$aadhar_approved_on = $result->aadhar_approved_on;
			}else{
				$aadhar_verify = 0;
				$aadhar_approved_by = 0;
				$aadhar_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('pancard_no') == $result->pancard_no && $_FILES['pancard_image']['size'] == 0){
				$pancard_verify = $result->pancard_verify;
				$pancard_approved_by = $result->pancard_approved_by;
				$pancard_approved_on = $result->pancard_approved_on;
			}else{
				$pancard_verify = 0;
				$pancard_approved_by = 0;
				$pancard_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('license_dob') ==$result->license_dob && $this->input->post('license_ward_name') == $result->license_ward_name && $this->input->post('license_type') == $result->license_type && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') ==$result->license_issued_on && $this->input->post('license_validity') ==$result->license_validity && $_FILES['license_image']['size'] == 0){
				$license_verify = $result->license_verify;
				$license_approved_by = $result->license_approved_by;
				$license_approved_on = $result->license_approved_on;
			}else{
				$license_verify = 0;
				$license_approved_by = 0;
				$license_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('loan_information') == $result->loan_information && $_FILES['loan_doc']['size'] == 0){
				$loan_verify = $result->loan_verify;
				$loan_approved_by = $result->loan_approved_by;
				$loan_approved_on = $result->loan_approved_on;
			}else{
				$loan_verify = 0;
				$loan_approved_by = 0;
				$loan_approved_on = '0000:00:00 00:00:00';
			}
			
			
			$user_document = array(
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $this->input->post('pancard_no'),
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				
				'loan_information' => $this->input->post('loan_information'),
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
				'complete_document' => 1,
				'is_edit' => 1		
				
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
				$config = NULL;
			}else{
				$user_document['aadhaar_image'] = $result->aadhaar_image;
			}
			
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
				
				$config = NULL;
			}else{
				$user_document['pancard_image'] = $result->pancard_image;
			}
			
			
			
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
				$police_image = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$police_image;
				$config = NULL;
			}else{
				$user_document['loan_doc'] = $result->loan_doc;
			}
			
			if($this->input->post('gst') == $result->gst && $this->input->post('telephone_number') == $result->telephone_number && $this->input->post('legal_entity') == $result->legal_entity){
				$vendor_is_verify = $result->vendor_is_verify;
				$vendor_approved_by = $result->vendor_approved_by;
				$vendor_approved_on = $result->vendor_approved_on;
			}else{
				$vendor_is_verify = 0;
				$vendor_approved_by = 0;
				$vendor_approved_on = '0000:00:00 00:00:00';
			}
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'associated_id' => $result->associated_id,
				'continent_id' => $result->vendor_continent_id,
				'country_id' => $result->vendor_country_id,
				'zone_id' => $result->vendor_zone_id,
				'state_id' => $result->vendor_state_id,
				'city_id' => $result->vendor_city_id,
				'is_verify' => $vendor_is_verify,
				'approved_by' => $vendor_approved_by,
				'approved_on' => $vendor_approved_on,
				'is_edit' => 1,
				'complete_vendor' => 1
			);
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_vendor($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $countryCode)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("vendor_updated"));
            admin_redirect('users/profile');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('edit_driver')));
            $meta = array('page_title' => lang('edit_vendor'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor($this->session->userdata('group_id') == 1 ? '' : $countryCode);
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['user_id'] = $user_id;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->local_continent_id);
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result->permanent_city_id);
			$this->data['license_type'] = $this->masters_model->getALLLicense_type();
            $this->page_construct('users/edit_vendor', $meta, $this->data);
        }        
    }
	
	function edit_driver($user_id){
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
		$group_id = $this->Driver;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status, country.name as instance_country")
            ->from("users u")
			->join("countries country", " country.iso = u.is_country", "left")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("u.is_country", $countryCode);
			}
			
			$q = $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your account has been deactive. so if can not edit."));
            	admin_redirect('users/profile');
			}
		}
		
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'), $countryCode);
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		$this->form_validation->set_rules('mobile', lang("mobile"), 'required');
		if($result->mobile != $this->input->post('mobile')){
        	$this->form_validation->set_rules('mobile', lang("mobile"), 'is_unique[users.mobile]');  
		}
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('local_address', lang("address"), 'required');
		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
		
		$this->form_validation->set_rules('aadhaar_no', lang("aadhaar_no"), 'required');
		$this->form_validation->set_rules('pancard_no', lang("pancard_no"), 'required');
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->mobile == $this->input->post('mobile') && $result->country_code == $this->input->post('country_code')){
				$is_edit = 1;
				$is_approved = 1;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
			}else{
				$is_edit = 0;
				$is_approved = 0;
				$approved_on = '0000-00-00';
				$approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1
			
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
				$user['photo'] = 'user/driver/'.$photo;
				$user_profile['photo'] = 'user/driver/'.$photo;
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;
				$user['photo'] = $result->photo;
			}
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_continent_id') == $result->local_continent_id && $this->input->post('local_country_id') == $result->local_country_id && $this->input->post('local_zone_id') == $result->local_zone_id && $this->input->post('local_state_id') == $result->local_state_id && $this->input->post('local_city_id') == $result->local_city_id && $this->input->post('local_area_id') == $result->local_area_id && $_FILES['local_image']['size'] == 0){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_continent_id') == $result->permanent_continent_id && $this->input->post('permanent_country_id') == $result->permanent_country_id && $this->input->post('permanent_zone_id') == $result->permanent_zone_id && $this->input->post('permanent_state_id') == $result->permanent_state_id && $this->input->post('permanent_city_id') == $result->permanent_city_id && $this->input->post('permanent_area_id') == $result->permanent_area_id && $_FILES['permanent_image']['size'] == 0){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_address' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_address') : '',
				'local_pincode' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_pincode') : '',
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_address') : '',
				'permanent_pincode' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_pincode') : '',
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				'is_edit' => 1,
				'complete_address' => 1
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
				$user_address['local_image'] = 'document/local_address/'.$local_image;
				$config = NULL;
			}else{
				$user_address['local_image'] = $res->local_image;
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
				$user_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}else{
				$user_address['permanent_image'] = $result->permanent_image;
			}
			
			if($this->input->post('account_holder_name') == $result->account_holder_name && $this->input->post('account_no') == $result->account_no && $this->input->post('bank_name') == $result->bank_name && $this->input->post('branch_name') == $result->branch_name && $this->input->post('ifsc_code') == $result->ifsc_code){
				$is_verify = $result->account_verify;
				$approved_by = $result->account_approved_by;
				$approved_on = $result->account_approved_on;
			}else{
				$is_verify = 0;
				$approved_by = 0;
				$approved_on = '0000:00:00 00:00:00';
			}
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $is_verify,
				'approved_by' => $approved_by,
				'approved_on' => $approved_on,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			if($this->input->post('aadhaar_no') == $result->aadhaar_no && $_FILES['aadhaar_image']['size'] == 0){
				$aadhar_verify = $result->aadhar_verify;
				$aadhar_approved_by = $result->aadhar_approved_by;
				$aadhar_approved_on = $result->aadhar_approved_on;
			}else{
				$aadhar_verify = 0;
				$aadhar_approved_by = 0;
				$aadhar_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('pancard_no') == $result->pancard_no && $_FILES['pancard_image']['size'] == 0){
				$pancard_verify = $result->pancard_verify;
				$pancard_approved_by = $result->pancard_approved_by;
				$pancard_approved_on = $result->pancard_approved_on;
			}else{
				$pancard_verify = 0;
				$pancard_approved_by = 0;
				$pancard_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('license_dob') ==$result->license_dob && $this->input->post('license_ward_name') == $result->license_ward_name && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') ==$result->license_issued_on && $this->input->post('license_validity') ==$result->license_validity && $_FILES['license_image']['size'] == 0){
				$license_verify = $result->license_verify;
				$license_approved_by = $result->license_approved_by;
				$license_approved_on = $result->license_approved_on;
			}else{
				$license_verify = 0;
				$license_approved_by = 0;
				$license_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('police_on') == $result->police_on && $this->input->post('police_til') == $result->police_til && $_FILES['police_image']['size'] == 0){
				$police_verify = $result->police_verify;
				$police_approved_by = $result->police_approved_by;
				$police_approved_on = $result->police_approved_on;
			}else{
				$police_verify = 0;
				$police_approved_by = 0;
				$police_approved_on = '0000:00:00 00:00:00';
			}
			
			
			$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $this->input->post('aadhaar_no') : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('pancard_no') : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				'license_dob' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_dob') : '0000-00-00',
				'license_ward_name' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_ward_name') : '',
				'license_type' => $_FILES['license_image']['size'] == 0 ? json_encode($this->input->post('license_type')) : '',
				'license_country_id' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_country_id') : 0,
				'license_issuing_authority' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_issuing_authority') : '',
				'license_issued_on' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_issued_on') : '0000-00-00',
				'license_validity' => $_FILES['license_image']['size'] == 0 ? $this->input->post('license_validity') : '0000-00-00',
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $_FILES['police_image']['size'] == 0 ? $this->input->post('police_on') : '0000-00-00',
				'police_til' => $_FILES['police_image']['size'] == 0 ? $this->input->post('police_til') : '0000-00-00',	
				
				'police_verify' => $police_verify,
				'police_approved_by' => $police_approved_by,
				'police_approved_on' => $police_approved_on,	
				'is_edit' => 1,
				'complete_document' => 1	
				
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
				$config = NULL;
			}else{
				$user_document['aadhaar_image'] = $result->aadhaar_image;
			}
			
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
				
				$config = NULL;
			}else{
				$user_document['pancard_image'] = $result->pancard_image;
			}
			
					
			
			
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
				$user_document['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
			}else{
				$user_document['license_image'] = $result->license_image;
			}
			
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
				$user_document['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}else{
				$user_document['police_image'] = $result->police_image;
			}
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_driver($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document, $countryCode)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("driver_updated"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('edit_driver')));
            $meta = array('page_title' => lang('edit_driver'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor($countryCode);
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['user_id'] = $user_id;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->local_continent_id);
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result->permanent_city_id);
			
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry($countryCode);
			
			$this->data['license_type'] = $this->masters_model->getCountry_bylicensetype($result->license_country_id);
			
			//$this->data['license_type'] = $this->masters_model->getALLLicense_type();
            $this->page_construct('users/edit_driver', $meta, $this->data);
        }        
    }
    
	
    /*#### Json Country Zone State city area get Reporter*/
	function getContinent_byuser_rep(){
        $data = $this->masters_model->getALLContinents();
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
	}
    function getCountry_bycontinent_rep(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Admin;
        $continent_id = $this->input->post('continent_id');
		$location_id = $this->input->post('continent_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'continents'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
        $data = $this->masters_model->getCountry_bycontinent($continent_id, $countryCode);
        
        if($data){
            foreach($data as $k => $row){
                $options['loc'][$k]['id'] = $row->id;
                $options['loc'][$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getZone_bycountry_rep(){
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
		$group_id = $this->Employee;
		$location_id = $this->input->post('country_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'countries'){
			
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
        $data = $this->masters_model->getZone_bycountry($country_id, $countryCode);
        
        if($data){
            foreach($data as $k => $row){
                $options['loc'][$k]['id'] = $row->id;
                $options['loc'][$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getState_byzone_rep(){
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
		$group_id = $this->Employee;
		$location_id = $this->input->post('zone_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'zones'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
		
        $data = $this->masters_model->getState_byzone($zone_id, $countryCode);
        
        if($data){
            foreach($data as $k => $row){
                $options['loc'][$k]['id'] = $row->id;
                $options['loc'][$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getCity_bystate_rep(){
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
		$group_id = $this->Employee;
		$location_id = $this->input->post('state_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'states'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
		
        $data = $this->masters_model->getCity_bystate($state_id, $countryCode);
        
        if($data){
            foreach($data as $k => $row){
                $options['loc'][$k]['id'] = $row->id;
                $options['loc'][$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getArea_bycity_rep(){
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
		$group_id = $this->Employee;
		$location_id = $this->input->post('city_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'cities'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
		
        $data = $this->masters_model->getArea_bycity($city_id, $countryCode);
        
        if($data){
            foreach($data as $k => $row){
                $options['loc'][$k]['id'] = $row->id;
                $options['loc'][$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	function getReporter_byarea_rep(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $area_id = $this->input->post('area_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('area_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'areas'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id, $countryCode);
			
			if($checkRole){
				foreach($checkRole as $c => $cow){
					$options['rep'][$c]['id'] = $cow->user_id;
					$options['rep'][$c]['text'] = $cow->first_name.' '.$cow->last_name;
				}
			}
		}
		
        
        echo json_encode($options);exit;
    }
	
	
    /*#### Json Country Zone State city area*/
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
        $data = $this->masters_model->getCountry_bycontinent($continent_id);
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
        $data = $this->masters_model->getZone_bycountry($country_id);
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
        $data = $this->masters_model->getState_byzone($zone_id);
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
        $data = $this->masters_model->getCity_bystate($state_id);
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
        $data = $this->masters_model->getArea_bycity($city_id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	function getReporter_byarea(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $area_id = $this->input->post('area_id');
        $data = $this->masters_model->getReporter_byarea($area_id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
}
