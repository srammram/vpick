<?php defined('BASEPATH') OR exit('No direct script access allowed');
class People extends MY_Controller
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
		//$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		//$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('people_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('verification_model');
		$this->load->admin_model('users_model');
		$this->load->admin_model('users_model');
		
    }
	
	function getModelbymake_type(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $make_id = $this->input->post('make_id');
		$type_id = $this->input->post('type_id');
		
        $data = $this->taxi_model->getModelbymake_type($make_id, $type_id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	function add_reason($user_id){
		
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
        $this->form_validation->set_rules('reason', lang("reason"), 'required');    
		$check_reason = $this->people_model->checkReason($user_id, $countryCode);
		
		if($check_reason == true){
			$this->session->set_flashdata('error', lang("reason_status_pending"));
            admin_redirect('people/driver');
		}
		
		
        if ($this->form_validation->run() == true) {
            $data = array(
                'reason' => $this->input->post('reason'),
				'customer_id' => $user_id,
				'support_id' => $this->session->userdata('user_id'),
				'support_status' => 1,
                'support_date' => date('Y-m-d H:i:s'),
            );
			
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_reason($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("reason_added"));
            admin_redirect('people/driver');
        } else {
			$this->data['user_id'] = $user_id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'people/add_reason', $this->data);
        }
    }
	
	/*###### Customer*/
    function customer($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer')));
        $meta = array('page_title' => lang('customer'), 'bc' => $bc);
        $this->page_construct('people/customer', $meta, $this->data);
    }
    function getCustomer(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Customer;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.refer_code, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, country.name as instance_country")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1 ", 'left')
			->join("countries country", " country.iso = users.is_country", "left")
			->where("users.group_id", $group_id);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
			if($approved == 1){
				$this->datatables->where('users.active', 1);
				
			}elseif($approved == 2){
				
				$this->datatables->where('users.active', 0);
				
			}
			$this->datatables->where('users.is_delete', 0);
			//->where("users.is_edit", 1)
			//$this->datatables->edit_column('active', '$1__$2', 'id, active')
            //$this->datatables->edit_column('status', '$1__$2', 'id, status');
			
			$edit = "<a href='" . admin_url('people/customer_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/users/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
			$this->datatables->unset_column('id');
	   echo $this->datatables->generate();
    }
	
	function add_customer(){
		$current_date = date('Y-m-d');
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
        $this->form_validation->set_rules('mobile', lang("mobile_number"), 'required');  
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
			$refer_code = $this->site->refercode('C', $countryCode); 
			
			$check_mobile = $this->people_model->checkMobilecustomer($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("mobile_number_already_exits"));
           		admin_redirect('people/add_customer');
			}
			
			
			if(!empty($this->input->post('reference_no'))){
				$check_reference = $this->people_model->checkCode($this->input->post('reference_no'), 'C');
				
				if($check_reference == 0){
					$this->session->set_flashdata('error', lang("refer_code_is_invaild"));
					admin_redirect('people/add_customer');
				}elseif($check_reference->code_end < $current_date){
					$this->session->set_flashdata('error', lang("refer_code_is_expiry"));
					admin_redirect('people/add_customer');
				}
			}
			
			
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'password' => md5($this->input->post('password')),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Customer,
				'refer_code' => $refer_code,
				'reference_no' => $this->input->post('reference_no') != NULL ? $this->input->post('reference_no') : '',
				'is_edit' => 1,
				'complete_user' => 1,
				'active' => 1,
				'is_approved' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_edit' => 1
			
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'user/customer/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/customer/'.$photo;
				$user['photo'] = 'user/customer/'.$photo;
				$config = NULL;
			}
			
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_pincode' => $this->input->post('local_pincode'),
				'complete_address' => 1,
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
			}
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_customer($user, $user_profile, $user_address, $countryCode, $refer_code, $this->input->post('reference_no'))){
			
			$sms_message = $this->input->post('first_name').' your account has been register successfully.Your refer code : '.$refer_code.'';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("customer_added"));
            admin_redirect('people/customer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/customer'), 'page' => lang('customer')), array('link' => '#', 'page' => lang('add_customer')));
            $meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
            $this->page_construct('people/add_customer', $meta, $this->data);
        }        
    }
	
	function status($status,$id){
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
        if($status=='active'){
            $data['status'] = 1;
        }
		
		$notification['title'] = 'Admin Account Change Status';
		$notification['message'] = ' admin has been '.$status.' account.';
		$notification['user_type'] = 1;
		$notification['user_id'] = $id;
		$this->site->insertNotification($notification, $countryCode);
		
        $this->people_model->update_status($data,$id, $countryCode);
		
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function customer_view($id){
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
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer')));
        $meta = array('page_title' => lang('customer'), 'bc' => $bc);
        $this->page_construct('people/customer_view', $meta, $this->data);
    }
	
	/*###### Employee*/
    function employee($action=false){
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
		/*$res = $this->site->findPincodegetAddress(607802);
		print_r($res);
		die;*/
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employee')));
        $meta = array('page_title' => lang('employee'), 'bc' => $bc);
        $this->page_construct('people/employee', $meta, $this->data);
    }
    function getEmployee(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Employee;	
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		
		
        $this->load->library('datatables');
		if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('people/employee_edit/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = " | <a href='" . admin_url('people/employee_edit/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, ud.name as department, ur.position as designation,   If(up.is_approved = 1 && udd.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && udd.aadhar_verify = 1 , '1', '0') as status, country.name as instance_country ")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1 ", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1 ", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1 ", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1 ", "left")
			->join("user_permission per", 'per.user_id = users.id AND per.is_edit = 1 ', "left")
			->join("user_department ud", 'ud.id = per.department_id ', "left")
			->join("user_roles ur", 'ur.id = per.designation_id ', "left");
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
			if($approved == 1){
				$this->datatables->where('udd.pancard_verify', 1);
				$this->datatables->where('uadd.local_verify', 1);
				$this->datatables->where('uadd.permanent_verify', 1);
				$this->datatables->where('ub.is_verify', 1);
				$this->datatables->where('udd.aadhar_verify', 1);
			}elseif($approved == 2){
				
				
				$this->datatables->where(" ( CASE 
				WHEN udd.pancard_verify = 0 AND {$this->db->dbprefix('users')}.group_id = ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id."      			
				WHEN uadd.local_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN uadd.permanent_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ub.is_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN udd.aadhar_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				
				END) ", NULL, FALSE);
				
			}
			
			$this->datatables->where("users.group_id", $group_id);
			$this->datatables->where('users.is_delete', 0);
			//->where("users.is_edit", 1)
			//$this->datatables->group_by('users.group_id');
           // $this->datatables->edit_column('active', '$1__$2', 'id, active')
            $this->datatables->edit_column('status', '$1__$2', 'id, status');
			
			$edit = "<a href='" . admin_url('people/employee_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$role = "<a href='" . admin_url('people/employee_role_setting/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_roles_settings')."'  ><i class='fa fa-wrench' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/users/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
		
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$role."</div><div>".$delete."</div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo '<pre>';
		//echo $this->db->last_query();
    }
	
	function employee_role_setting($user_id){
		
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
		$this->form_validation->set_rules('user_id', lang("employee"), 'required');
        
        if ($this->form_validation->run() == true) {
		  	$setting = array(
				'help_ride_based' => $this->input->post('help_ride_based') ? $this->input->post('help_ride_based') : '0',
				'help_others' => $this->input->post('help_others') ? $this->input->post('help_others') : '0'
			);
        }
		
        if ($this->form_validation->run() == true && $this->people_model->employee_role_setting($setting, $user_id, $countryCode)){
						
            $this->session->set_flashdata('message', lang("setting_updated"));
            admin_redirect('people/employee_role_setting/'.$user_id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('role_settings')));
            $meta = array('page_title' => lang('role_settings'), 'bc' => $bc);
			$this->data['role_settings'] = $this->people_model->getUserSettings($user_id, $countryCode);
			$this->data['user_id'] = $user_id;
            $this->page_construct('people/employee_role_setting', $meta, $this->data);
        }  
			
	}
	
	
	
	function add_employee(){
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
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        $this->form_validation->set_rules('mobile', lang("mobile"), 'required');     
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
				
		
        if ($this->form_validation->run() == true) {
			
			$check_mobile = $this->people_model->checkMobileemployee($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("mobile_number_already_exits"));
           		admin_redirect('people/add_employee');
			}
			
		   $designation_id = $this->site->getUserroleID($this->input->post('designation_id'));
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Admin,
				'parent_id' => $this->session->userdata('user_id'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Employee,
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'active' => 1,
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
				$config['upload_path'] = $this->upload_path.'user/employee/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$user_profile['photo'] = 'user/employee/'.$photo;
				$user['photo'] = 'user/employee/'.$photo;
				$config = NULL;
			}
			
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
				
				
				'local_address' => $this->input->post('local_address'),
				'complete_address' => 1,
				'permanent_address' => $this->input->post('permanent_address'),
				
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
			}
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'complete_bank' => 1,
				'is_edit' => 1
			);
			
			$user_permission = array(
				'department_id' => $this->input->post('department_id'),
				'designation_id' => $designation_id,
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
				'reporter_id' => $this->input->post('reporter_id'),
				'immediate' => 1,
				'is_edit' => 1
			);
			
			$user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
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
			}
			
			
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_permission, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_employee($user, $user_profile, $user_address, $user_bank, $user_permission, $user_document, $this->Employee, $countryCode)){
			
			$sms_message = $this->input->post('first_name').' your account has been register successfully';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("employee_added"));
            admin_redirect('people/employee');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('add_employee')));
            $meta = array('page_title' => lang('add_employee'), 'bc' => $bc);
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
            $this->page_construct('people/add_employee', $meta, $this->data);
        }        
    }
	
	function employee_status($status, $id){
		$employee_result = $this->verification_model->getUserDetails($id);
		if($this->session->userdata('group_id') == 1){
			if($employee_result->is_country != ''){
				$countryCode = $employee_result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		if($status == 'active'){
			$this->session->set_flashdata('message', lang('account_active'));
            admin_redirect('people/employee');
		}
		
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');

		
        if ($this->form_validation->run() == true) {
		   
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'complete_user' => 1,
				'is_approved' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'approved_on' => date('Y-m-d H:i:s'),
			);
			$udata = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
			
			$data_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
				'local_address' => $this->input->post('local_address'),
				
				'permanent_address' => $this->input->post('permanent_address'),
				'complete_address' => 1,
				
				'permanent_verify' => $this->input->post('is_approved'),
				'local_verify' => $this->input->post('is_approved'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
				$data_address['local_image'] = 'document/local_address/'.$local_image;
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
				$data_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			
			$data_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode,
				
			);
			
			
			
		
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('is_approved'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode,
			);
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('is_approved'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'pancard_approved_on' => date('Y-m-d H:i:s'),
				'is_country' => $countryCode,
			);
			
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data, $udata, $countryCode) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address, $countryCode) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard, $countryCode);
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
			$q = $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", "left")
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", "left")
			->where("u.id", $id)
			->get();
		
			if($q->num_rows()>0){
				if($q->row('status') == 0){
					$this->session->set_flashdata('error', $this->input->post('first_name').lang('details_has_been_not_verified'));
					admin_redirect('people/employee');
				}else{
				$sms_message = $this->input->post('first_name').' your account verified status has been successfully. ';
				$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
				$sms_country_code = $this->input->post('country_code');
	
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$this->session->set_flashdata('message', $this->input->post('first_name').lang('details_has_been_verified'));
				admin_redirect('people/employee');
				}
			}
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('active_employee')));
            $meta = array('page_title' => lang('active_employee'), 'bc' => $bc);
			
			/*user*/
			$this->data['employee_result'] = $employee_result;
			$this->data['user_id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			
			/*address*/
			$result_address = $this->verification_model->getUserAddress($id);
			$this->data['result_address'] = $result_address;
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->local_continent_id);
			
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result_address->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result_address->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result_address->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result_address->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result_address->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result_address->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result_address->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result_address->permanent_city_id);
			/*account*/
			$result_account = $this->verification_model->getUserBank($id);
			$this->data['result_account'] = $result_account;
			/*document*/
			$result_document = $this->verification_model->getUserDocument($id);
			$this->data['result_document'] = $result_document;
			
            $this->page_construct('people/status_employee', $meta, $this->data);
        }        
    }
	
	function employee_view($id){
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
		$this->data['user'] = $this->users_model->getUser($id);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employee')));
        $meta = array('page_title' => lang('employee'), 'bc' => $bc);
        $this->page_construct('people/employee_view', $meta, $this->data);
    }
	
	function employee_edit($user_id, $view){
		$result = $this->users_model->getUserEdit($user_id);
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
		$this->data['view'] = $view;
		$group_id = $this->Employee;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", "left")
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", "left")
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
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/employee');
			}
		}
		
		
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    
			if($result->first_name == $this->input->post('first_name') && $result->last_name == $this->input->post('last_name')){
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
				'is_country' => $countryCode,
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
				'is_complete_profile' => 1,
				'is_country' => $countryCode,
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
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;		
			}
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_pincode') == $result->local_pincode){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $this->input->post('permanent_address'),
				
				'permanent_verify' => $permanent_verify,
				'permanent_approved_by' => $permanent_approved_by,
				'permanent_approved_on' => $permanent_approved_on,
				'is_edit' => 1,
				'is_country' => $countryCode,
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
				'is_country' => $countryCode,
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
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $this->input->post('pancard_no'),
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				'is_country' => $countryCode,		
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
			
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_employee($user_id, $user, $user_profile, $user_address, $user_bank, $user_document)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("employee_updated"));
            admin_redirect('people/employee');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('profile')), array('link' => '#', 'page' => lang('edit_employee')));
            $meta = array('page_title' => lang('edit_employee'), 'bc' => $bc);
			
			
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
			
            $this->page_construct('people/edit_employee', $meta, $this->data);
        }        
    
	
	}
	
	
	
	/*###### Vendor*/
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
        $this->page_construct('people/vendor', $meta, $this->data);
    }
    function getVendor(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		//{$this->db->dbprefix('users')}.active as status
		$group_id = $this->Vendor;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		$cab_id = $_GET['cab'];
		
		$driver_id = $_GET['driver'];
		
		
        $this->load->library('datatables');
		/*if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('people/vendor_edit/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = " | <a href='" . admin_url('people/vendor_edit/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}
		*/
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, {$this->db->dbprefix('users')}.active as active, If(up.is_approved = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1, '1', '0') as status,  {$this->db->dbprefix('users')}.join_type as join_type, country.name as instance_country")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = users.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1 ", 'left')
			->join("user_vendor uven", "uven.user_id = users.id AND uven.is_edit = 1 ", 'left')
			->join("taxi t", "t.vendor_id = users.id AND t.is_edit = 1 ", 'left')
			->join("users d", "d.parent_id = users.id AND d.is_edit = 1 ", 'left')
			->where("users.group_id", $group_id);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
			if($cab_id != 0){
				$this->datatables->where('t.id', $cab_id);
			}
			
			if($driver_id != 0){
				$this->datatables->where('d.id', $driver_id);
			}
			
			if($approved == 1){
				
				$this->datatables->where('ud.pancard_verify', 1);
				$this->datatables->where('uadd.local_verify', 1);
				$this->datatables->where('uadd.permanent_verify', 1);
				$this->datatables->where('ub.is_verify', 1);
				$this->datatables->where('ud.aadhar_verify', 1);
				$this->datatables->where('ud.loan_verify', 1);
				$this->datatables->where('uven.is_verify', 1);
				
			}elseif($approved == 2){
				
				
				$this->datatables->where(" ( CASE 
				WHEN ud.pancard_verify = 0 AND {$this->db->dbprefix('users')}.group_id = ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id."      			
				WHEN uadd.local_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN uadd.permanent_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ub.is_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ud.aadhar_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN uven.is_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ud.loan_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				
				END) ", NULL, FALSE);
				
			}
			
			$this->datatables->where('users.is_delete', 0);
			//->where("users.is_edit", 1)
            //$this->datatables->edit_column('active', '$1__$2', 'id, active')
            $this->datatables->edit_column('status', '$1__$2', 'id, status')			
			->edit_column('join_type', '$1__$2', 'id, join_type');
			
				$edit = "<a href='" . admin_url('people/vendor_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$allocated = "<a href='" . admin_url('people/vendor_allocate/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to zonal allocate'  ><div class='kapplist-path'></div></a>";
				
				$driver = "<a href='" . admin_url('people/driver_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_driver')."'  ><i class='fa fa-users' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$cab = "<a href='" . admin_url('people/edit_taxi/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_cab')."'  ><i class='fa fa-taxi' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
			$delete = "<a href='" . admin_url('welcome/delete/users/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$allocated."</div><div>".$driver."</div><div>".$cab."</div><div>".$delete."</div>", "id");
        
			$this->datatables->unset_column('id');
		echo $this->datatables->generate();
    }
	
	function vendor_role_setting($user_id){
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
		$this->form_validation->set_rules('user_id', lang("vendor"), 'required');
        
        if ($this->form_validation->run() == true) {
		  	$setting = array(
				'help_ride_based' => $this->input->post('help_ride_based') ? $this->input->post('help_ride_based') : '0',
				'help_others' => $this->input->post('help_others') ? $this->input->post('help_others') : '0'
			);
        }
		
        if ($this->form_validation->run() == true && $this->people_model->employee_role_setting($setting, $user_id, $countryCode)){
						
            $this->session->set_flashdata('message', lang("setting_updated"));
            admin_redirect('people/employee_role_setting/'.$user_id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('role_settings')));
            $meta = array('page_title' => lang('role_settings'), 'bc' => $bc);
			$this->data['role_settings'] = $this->people_model->getUserSettings($user_id, $countryCode);
			$this->data['user_id'] = $user_id;
            $this->page_construct('people/employee_role_setting', $meta, $this->data);
        }  
			
	}
	
	function add_vendor(){
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
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			
			$check_mobile = $this->people_model->checkMobilevendor($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("mobile_number_already_use_both_driver_vendor"));
           		admin_redirect('people/vendor');
			}elseif($check_mobile == 2){
				$this->session->set_flashdata('error', lang("mobile_number_already_use_driver"));
           		admin_redirect('people/vendor');
			}elseif($check_mobile == 3){
				$this->session->set_flashdata('error', lang("mobile_number_already_use_vendor"));
           		admin_redirect('people/vendor');
			}
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
			}
		   $operator = $this->input->post('operator');
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $driver_oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $driver_mobile_otp = random_string('numeric', 6);
		   
		   
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Admin,
				'parent_id' => $this->session->userdata('user_id'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Vendor,
				'active' => 1,
				'is_edit' => 1,
				'complete_user' => 1
		   );
		   
		   $driver_user = array(
		   		'oauth_token' => $driver_oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $driver_mobile_otp,
				'parent_type' => $this->Vendor,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Driver,
				'active' => 1,
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
				$user['driver_user'] = 'user/driver/'.$photo;
				
				$config = NULL;
			}
			
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
				'local_address' => $this->input->post('local_address'),
				
				'permanent_address' => $this->input->post('permanent_address'),
				'complete_address' => 1,
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
			}
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			$user_vendor = array(
				'complete_vendor' => 1,
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'is_edit' => 1
			);
			
			$user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				'loan_information' => $this->input->post('loan_information'),
				'is_edit' => 1,
				'complete_document' => 1
				
			);
			
			$driver_user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				
				'license_no' => $this->input->post('license_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),	
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
				$driver_user_document['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
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
				$driver_user_document['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
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
				$loan_doc = $this->upload->file_name;
				$user_document['loan_doc'] = 'document/loan/'.$loan_doc;
				$config = NULL;
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
				$driver_user_document['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
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
				$driver_user_document['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}
			
			$make_name = $this->people_model->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->people_model->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->people_model->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			
			$taxi = array(
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				
				'name' => $this->input->post('taxi_name'),
				'number' => $this->input->post('number'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'category' => $this->input->post('category'),
				'weight' => $this->input->post('weight'),
				'min_weight' => $this->input->post('min_weight'),
				'max_weight' => $this->input->post('max_weight'),
				'length' => $this->input->post('length'),
				'height' => $this->input->post('height'),
				'width' => $this->input->post('width'),
				'capacity' => $this->input->post('capacity') ? $this->input->post('capacity') : 0,
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1,
				'complete_taxi' => 1
		   );
		   
		   
		   
		   if ($_FILES['taxi_photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$taxi_photo;
				$config = NULL;
			}
			
			$taxi_document = array(
				//'vendor_id' => $vendor->id,
				//'group_id' => $vendor->group_id,
		   		'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'taxation_amount_paid' => $this->input->post('taxation_amount_paid'),
				'taxation_due_date' => $this->input->post('taxation_due_date'),
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'speed_due_date' => $this->input->post('speed_due_date'),
				'puc_due_date' => $this->input->post('puc_due_date'),
				'is_edit' => 1,
				'complete_taxidocument' => 1
				
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
				$taxi_document['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}
			
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
				$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			}
			
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
				$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}
			
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
				$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}
			
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
				$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}
			
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
				$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}
			
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
				$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}
			
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
				$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			
			//$this->sma->print_arrays($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_vendor($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document, $this->Vendor, $this->Driver, $operator, $taxi, $taxi_document, $countryCode)){
			
			$sms_message = $this->input->post('first_name').' your account has been register successfully';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("vendor_added"));
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/vendor'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('add_vendor')));
            $meta = array('page_title' => lang('add_vendor'), 'bc' => $bc);
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
			$this->data['license_type'] = $this->masters_model->getALLLicense_type();
            $this->page_construct('people/add_vendor', $meta, $this->data);
        }        
    }
	
	function vendor_status($status, $id){
		$vendor_result = $this->verification_model->getUserDetails($id);
		
		if($this->session->userdata('group_id') == 1){
			if($vendor_result->is_country != ''){
				$countryCode = $vendor_result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		if($status == 'active'){
			$this->session->set_flashdata('message', lang('account_active'));
            admin_redirect('people/vendor');
		}
		
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		
		
        if ($this->form_validation->run() == true) {
		   
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'approved_on' => date('Y-m-d H:i:s')
			);
			$udata = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
			
			$data_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				'local_address' => $this->input->post('local_address'),
				
				'permanent_address' => $this->input->post('permanent_address'),
				
				'permanent_verify' => $this->input->post('permanent_verify'),
				'local_verify' => $this->input->post('local_verify'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
				$data_address['local_image'] = 'document/local_address/'.$local_image;
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
				$data_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			
			$data_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('aadhar_verify'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			
			
			$data_loan = array(
				'loan_information' => $this->input->post('loan_information'),
				'loan_verify' => $this->input->post('loan_verify'),
				'loan_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
				$data_loan['loan_doc'] = 'document/loan/'.$loan_doc;
				$config = NULL;
			}
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('pancard_verify'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_vendor = array(
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'is_country' => $countryCode,
				'legal_entity' => $this->input->post('legal_entity')
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
				$data_aadhar['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
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
				$data_pancard['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
			}
			
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data, $udata, $countryCode) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address, $countryCode) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_loan, $countryCode) && $this->verification_model->update_vendor_common($this->input->post('vendor_id'), $data_vendor, $countryCode);
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
			$q = $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1  && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status ")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 AND up.is_country = '".$countryCode."'", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 AND ub.is_country = '".$countryCode."'", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 AND ud.is_country = '".$countryCode."'", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 AND uadd.is_country = '".$countryCode."'", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1 AND uven.is_country = '".$countryCode."'", 'left')
			->where("u.id", $id)
			->where('u.is_country', $countryCode)
			->get();
		
		if($q->num_rows()>0){
			
			
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your_account_has_not_verified"));
            	admin_redirect('people/vendor');
			}else{
			
			$sms_message = $this->input->post('first_name').' your account verified status has been successfully. ';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
			$notification['title'] = 'Account Verified';
			$notification['message'] = $this->input->post('first_name').' your account verified status has been successfully. ';
			$notification['user_type'] = 3;
			$notification['user_id'] = $id;
			$this->site->insertNotification($notification);
			
			
            $this->session->set_flashdata('message', $this->input->post('first_name').lang('details_has_been_verified'));
            admin_redirect('people/vendor');
			}
		}
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/vendor'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('active_vendor')));
            $meta = array('page_title' => lang('active_vendor'), 'bc' => $bc);
			
			/*user*/
			$this->data['vendor_result'] = $vendor_result; 
			$this->data['vendor_personal_result'] = $this->people_model->getVendorDetails($id, $countryCode);
			$this->data['user_id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			
			/*address*/
			$result_address = $this->verification_model->getUserAddress($id);
			$this->data['result_address'] = $result_address;
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->local_continent_id);
			
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result_address->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result_address->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result_address->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result_address->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result_address->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result_address->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result_address->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result_address->permanent_city_id);
			/*account*/
			$result_account = $this->verification_model->getUserBank($id);
			$this->data['result_account'] = $result_account;
			/*document*/
			$result_document = $this->verification_model->getUserDocument($id);
			$this->data['result_document'] = $result_document;
			$this->data['license_type'] = $this->masters_model->getALLLicense_type();
            $this->page_construct('people/status_vendor', $meta, $this->data);
        }        
    }
	
	function vendor_view($id){
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
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
        $meta = array('page_title' => lang('vendor'), 'bc' => $bc);
        $this->page_construct('people/vendor_view', $meta, $this->data);
    }
	
	function vendor_allocate($id){
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
		$user = $this->users_model->getUser($id);
		if($user->local_verify == 0){
			$this->session->set_flashdata('message', lang('your_account_deactive_not_allocated_zone'));
            admin_redirect('people/vendor');
		}
		
		$this->form_validation->set_rules('associated_id', $this->lang->line("associated"), 'required');
		$associated_id = $this->input->post('associated_id');
        if ($this->form_validation->run() == true) {
			$zonal_details = $this->people_model->getZonalUser($associated_id, $countryCode);
			
			$vendor = array(
				'associated_id' => $this->input->post('associated_id'),
				'continent_id' => $zonal_details->continent_id,
				'country_id' => $zonal_details->country_id,
				'zone_id' => $zonal_details->zone_id,
				'is_verify' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
		}
		
		 if ($this->form_validation->run() == true && $this->people_model->zonal_allocated($vendor, $id, $countryCode)){
			$notification['title'] = 'Zonal Allocated';
			$notification['message'] = 'Admin has been allocated zonal to vendor('.$this->input->post('first_name').'). ';
			$notification['user_type'] = 3;
			$notification['user_id'] = $id;
			$this->site->insertNotification($notification);
			
            $this->session->set_flashdata('message', $this->input->post('first_name').lang('allocated_zonal'));
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['user'] = $user;
			$this->data['zones'] = $this->people_model->getZoneuser($user->local_zone_id, $this->Employee, $countryCode);
			$this->data['id'] = $id;
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor_allocate')));
			$meta = array('page_title' => lang('vendor_allocate'), 'bc' => $bc);
			$this->page_construct('people/vendor_allocate', $meta, $this->data);
		}
    }
	
	function vendor_edit($user_id){
		$result = $this->users_model->getUserEdit($user_id);
		
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
		$group_id = $this->Vendor;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status ")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1 ", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->db->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->db->where("u.is_country", $countryCode);
			}
			
			$q  = $this->db->get();
		
		if($q->num_rows()>0){
			
			
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/vendor');
			}
		}
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		  
		   $user = array(
		   		'join_type' => 0,
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_country' => $countryCode,
				'complete_user' => 1,
				'is_edit' => 1,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_country' => $countryCode,
				'is_edit' => 1,
				'is_complete_profile' => 1
			
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
			
			
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' =>  $this->input->post('local_address'),
				

				
				'permanent_address' =>  $this->input->post('permanent_address'),
				'complete_address' => 1,
				'is_country' => $countryCode,
				'is_edit' => 1,

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
				$user_address['local_image'] =  $result->local_image;
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
			
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_country' => $countryCode,
				'is_edit' => 1,
				'complete_bank' => 1
			);
			
			
			
			
			$user_document = array(
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				
				
				
				'pancard_no' =>  $this->input->post('pancard_no'),
				
				
				
				'loan_information' =>  $this->input->post('loan_information'),
				'is_country' => $countryCode,
				'complete_document' => 1,
				'is_edit' => 1	
				
			);
			
			if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = FALSE;
				$this->upload->initialize($config);
				
				
				
				if (!$this->upload->do_upload('aadhaar_image')) {
					$error .= 'aadhaar document '.$this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
            		//admin_redirect('people/vendor');
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
					$error .= 'pancard document '.$this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
					//$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
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
				'is_country' => $countryCode,
				'is_edit' => 1,
				'complete_vendor' => 1
			);
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor);
			//die;
        }
		
		
		
        if ($this->form_validation->run() == true && $this->users_model->editadmin_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("vendor_updated"));
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('admin_support_vendor')));
            $meta = array('page_title' => lang('admin_support_vendor'), 'bc' => $bc);
			
			
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
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
            $this->page_construct('people/edit_vendor', $meta, $this->data);
        }        
    }
	
	function vendor_adminedit($user_id){
		$result = $this->users_model->getUserEdit($user_id);
		
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
		$group_id = $this->Vendor;
		
		$this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status ")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1 ", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->db->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->db->where("u.is_country", $countryCode);
			}
			$q = $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 1){
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/vendor');
			}
		}
		
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');

		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		  
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->first_name == $this->input->post('first_name') && $result->last_name == $this->input->post('last_name') && $result->gender == $this->input->post('gender') && $result->dob == $this->input->post('dob')){
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
				'is_country' => $countryCode,
				'is_edit' => 1,
				'complete_user' => 1,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_country' => $countryCode,
				'is_edit' => 1,
				'is_complete_profile' => 1
			
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
			
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $this->input->post('permanent_address'),
				'complete_address' => 1,
				'is_country' => $countryCode,
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
				$user_address['local_image'] =  $result->local_image;
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
				'is_country' => $countryCode,
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
				'is_country' => $countryCode,
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
				'is_edit' => 1,
				'complete_document' => 1	
				
			);
			
						
			if ($_FILES['aadhaar_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/aadhaar/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = FALSE;
				$this->upload->initialize($config);
				
				
				
				if (!$this->upload->do_upload('aadhaar_image')) {
					$error .= 'aadhaar document '.$this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
            		//admin_redirect('people/vendor');
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
					$error .= 'pancard document '.$this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
					//$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
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
				'is_country' => $countryCode,
				'complete_vendor' => 1, 
				'is_edit' => 1
			);
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor);
			//die;
        }
		
		
		
        if ($this->form_validation->run() == true && $this->users_model->editadmin_vendor($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("vendor_updated"));
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('admin_support_vendor')));
            $meta = array('page_title' => lang('admin_support_vendor'), 'bc' => $bc);
			
			
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
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
            $this->page_construct('people/editadmin_vendor', $meta, $this->data);
        }        
    }
	
	
	
	/*###### Driver*/
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
        $this->page_construct('people/driver', $meta, $this->data);
    }
	
	function driver_role_setting($user_id){
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
		$this->form_validation->set_rules('user_id', lang("driver"), 'required');
        
        if ($this->form_validation->run() == true) {
		  	$setting = array(
				'incentive_auto_enable' => $this->input->post('incentive_auto_enable') ? $this->input->post('incentive_auto_enable') : '0',
				'ride_stop' => $this->input->post('ride_stop') ? $this->input->post('ride_stop') : '0'
			);
        }
		
        if ($this->form_validation->run() == true && $this->people_model->driver_role_setting($setting, $user_id, $countryCode)){
						
            $this->session->set_flashdata('message', lang("setting_updated"));
            admin_redirect('people/driver_role_setting/'.$user_id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('driver_setting')));
            $meta = array('page_title' => lang('driver_setting'), 'bc' => $bc);
			$this->data['role_settings'] = $this->people_model->getUserSettings($user_id, $countryCode);
			$this->data['user_id'] = $user_id;
            $this->page_construct('people/driver_role_setting', $meta, $this->data);
        }  
			
	}
	
    function getDriver(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		$cab_id = $_GET['cab'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.refer_code, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender,  If(up.is_approved = 1 && t.is_verify = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1, '1', '0') as status, 
			
			If({$this->db->dbprefix('users')}.join_type = 2 && up.is_approved = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1, '1', '0') as join_type, country.name as instance_country
			
			")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = users.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1 ", 'left')
			->join("taxi t", "t.driver_id = users.id AND t.is_edit = 1 ", 'left')
			->where("users.group_id", $group_id);
			
			if($this->Vendor == $this->session->userdata('group_id')){
				$this->datatables->where("users.parent_id", $this->session->userdata("user_id"));
			}
			
			if($cab_id != 0){
				$this->datatables->where('t.id', $cab_id);
			}
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
			
			if($approved == 1){
				
				$this->datatables->where('ud.pancard_verify', 1);
				$this->datatables->where('ud.police_verify', 1);
				$this->datatables->where('uadd.local_verify', 1);
				$this->datatables->where('uadd.permanent_verify', 1);
				$this->datatables->where('ub.is_verify', 1);
				$this->datatables->where('ud.aadhar_verify', 1);
				$this->datatables->where('ud.license_verify', 1);
				
				
			}elseif($approved == 2){
				
				
				$this->datatables->where(" ( CASE 
				WHEN ud.pancard_verify = 0 AND {$this->db->dbprefix('users')}.group_id = ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id."      			
				WHEN uadd.local_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN uadd.permanent_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ub.is_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ud.aadhar_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ud.police_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				WHEN ud.license_verify = 0 AND {$this->db->dbprefix('users')}.group_id =  ".$group_id." THEN {$this->db->dbprefix('users')}.group_id =  ".$group_id." 
				
				END) ", NULL, FALSE);
				
			}
			$this->datatables->where('users.is_delete', 0);
			$this->datatables->group_by("users.id");
            //$this->datatables->edit_column('active', '$1__$2', 'id, active')
            $this->datatables->edit_column('status', '$1__$2', 'id, status')
			->edit_column('join_type', '$1__$2', 'id, join_type');
			
			//if($this->Vendor == $this->session->userdata('group_id')){
				//$edit = "<a href='" . admin_url('people/driver_edit/$1') . "' class='tip' ><div class='kapplist-view1'></div></a> | ";
				
				//$parent_id = '->where("users.group_id", $this->session->userdata("user_id"))'; 
			//}else{
				$edit = "<a href='" . admin_url('people/driver_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$role = "<a href='" . admin_url('people/driver_role_setting/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_settings')."'  ><i class='fa fa-wrench' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$cab = "<a href='" . admin_url('taxi/?driver=$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to cab'  ><i class='fa fa-taxi' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$vendor = "<a href='" . admin_url('people/vendor/?driver=$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_vendor')."'  ><i class='fa fa-users' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
				
				$delete = "<a href='" . admin_url('welcome/delete/users/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
				//$edit = "<a href='" . admin_url('people/driver_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
				//$edit = "<a href='" . admin_url('people/driver_edit/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
				//$parent_id = "";
			//}
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\">".$edit."<a href='" . admin_url('people/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");<div><a href=''><div class='kapplist-path'></div></a></div><div><a href=''><div class='kapplist-edit'></div></a></div><div><a href=''><div class='kapplist-car'></div></a></div>
		
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$role."</div><div>".$cab."</div><div>".$delete."</div>", "id");
		
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	
	function add_driver(){
		$current_date = date('Y-m-d');
		$checkcountry = '';
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
				$checkcountry = $countryCode;
			}else{
				$countryCode = $this->input->post('is_country');	
				$checkcountry = $countryCode;
			}	
		}else{
			$countryCode = $this->countryCode;	
			$checkcountry = $countryCode;
		}
		$this->data['checkcountry'] = $checkcountry;
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        
		
		
		
        if ($this->form_validation->run() == true) {
			
			$refer_code = $this->site->refercode('D', $countryCode); 
			
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   	
			$check_mobile = $this->people_model->checkMobiledriver($this->input->post('mobile'), $this->input->post('country_code'), $countryCode);
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("mobile_number_already_exits"));
           		admin_redirect('people/add_driver');
			}
			if(!empty($this->input->post('reference_no'))){
				if(!ctype_space($this->input->post('reference_no'))){
					$check_reference = $this->people_model->checkCode($this->input->post('reference_no'), 'D');
					if($check_reference == 0){
						$this->session->set_flashdata('error', lang("refer_code_is_invaild"));
						admin_redirect('people/add_driver');
					}elseif($check_reference->code_end < $current_date){
						$this->session->set_flashdata('error', lang("refer_code_is_expiry"));
						admin_redirect('people/add_driver');
					}
				}
			}
			
			
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'password' => md5($this->input->post('password')),
				'text_password' => $this->input->post('password'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Vendor,
				'parent_id' => $this->input->post('parent_id'),
				'refer_code' => $refer_code,
				'reference_no' => $this->input->post('reference_no') != NULL ? $this->input->post('reference_no') : '',
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Driver,
				'active' => 1,
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
			}
			
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $this->input->post('permanent_pincode') ? $this->input->post('permanent_pincode') : '',
				
				'local_address' => $this->input->post('local_address'),
				
				'permanent_address' => $this->input->post('permanent_address'),
				'complete_address' => 1,
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
			}
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'complete_bank' => 1,
				'is_edit' => 1
			);
			
			
			
			
			
			$user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				'license_no' => $this->input->post('license_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),
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
			}
			
			$make_name = $this->people_model->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->people_model->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->people_model->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			
			$taxi = array(
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				
				'name' => $this->input->post('taxi_name'),
				'number' => $this->input->post('number'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'category' => $this->input->post('category'),
				'weight' => $this->input->post('weight'),
				'min_weight' => $this->input->post('min_weight'),
				'max_weight' => $this->input->post('max_weight'),
				'length' => $this->input->post('length'),
				'height' => $this->input->post('height'),
				'width' => $this->input->post('width'),
				'capacity' => $this->input->post('capacity') ? $this->input->post('capacity') : 0,
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1,
				'complete_taxi' => 1
		   );
		   
		   
		   
		   if ($_FILES['taxi_photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$taxi_photo;
				$config = NULL;
			}
			
			$taxi_document = array(
		   		'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'taxation_amount_paid' => $this->input->post('taxation_amount_paid'),
				'taxation_due_date' => $this->input->post('taxation_due_date'),
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'speed_due_date' => $this->input->post('speed_due_date'),
				'puc_due_date' => $this->input->post('puc_due_date'),
				'complete_taxidocument' => 1,
				'is_edit' => 1
				
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
				$taxi_document['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}
			
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
				$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			}
			
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
				$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}
			
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
				$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}
			
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
				$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}
			
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
				$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}
			
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
				$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}
			
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
				$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_driver($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver, $this->input->post('parent_id'), $countryCode, $refer_code, $this->input->post('reference_no'))){
			$sms_message = $this->input->post('first_name').' your account has been register successfully. Waiting for admin approval process. Your refer code : '.$refer_code.'';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
			
			
			
            $this->session->set_flashdata('message', lang("driver_added"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('add_driver')));
            $meta = array('page_title' => lang('add_driver'), 'bc' => $bc);
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor($this->session->userdata('group_id') == 1 ? '' : $countryCode);
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			
			$this->data['categorys'] = $this->masters_model->getALLTaxi_category($countryCode);
			
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel($countryCode);
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry($countryCode);
			$this->data['license_type'] = $this->masters_model->getALLLicense_type();
			
            $this->page_construct('people/add_driver', $meta, $this->data);
        }        
    }
	
	function driver_status($status, $id){
		$driver_result = $this->verification_model->getUserDetails($id);
		if($this->session->userdata('group_id') == 1){
			if($driver_result->is_country != ''){
				$countryCode = $driver_result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$b = $this->db->select("u.id as id, t.is_verify as taxi_verify, If(up.is_approved = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1", 'left')
			->where("u.id", $id)
			->get();
		if($b->num_rows()>0){
			if($b->row('status') == 1){
				$taxi_verify = $b->row('taxi_verify');
			}else{
				$taxi_verify = 1;
			}
		}else{
			$taxi_verify = 1;
		}
		if($status == 'active' && $taxi_verify == 1){
			$this->session->set_flashdata('message', 'account_active');
            admin_redirect('people/driver');
		}elseif($status == 'deactive'  && $taxi_verify == 0 ){
			$this->session->set_flashdata('error', lang('your_account_active_taxi_does_not_active'));
            admin_redirect('people/driver');
		}
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		
		$same_address = $this->input->post('same_address') ? $this->input->post('same_address') : 0;
		
        if ($this->form_validation->run() == true) {
		   
			
			
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'approved_on' => date('Y-m-d H:i:s'),
				'join_type' => 2,
			);
			$udata = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
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
			
			$data_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $same_address == 1 ? $this->input->post('local_pincode') : $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				'is_country' => $countryCode,
				'permanent_address' => $same_address == 1 ? $this->input->post('local_address') : $this->input->post('permanent_address'),
				'same_address' => $same_address,
				'permanent_verify' => $this->input->post('is_approved'),
				'local_verify' => $this->input->post('is_approved'),
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
				$data_address['local_image'] = 'document/local_address/'.$local_image;
				if($same_address == 1){
					$data_address['permanent_image'] = 'document/local_address/'.$local_image;
				}
				$config = NULL;
			}
			
			if($same_address != 1){
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
				$data_address['permanent_image'] = 'document/permanent_address/'.$permanent_image;
				$config = NULL;
			}
			}
			
			$data_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('is_approved'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('is_approved'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_police = array(
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),
				'police_verify' => $this->input->post('is_approved'),
				'police_approved_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode,
				'police_approved_on' => date('Y-m-d H:i:s'),
			);
			
			
			
			$data_license = array(
				'license_no' => $this->input->post('license_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'is_country' => $countryCode,
				'license_verify' => $this->input->post('is_approved'),
				'license_approved_by' => $this->session->userdata('user_id'),
				'license_approved_on' => date('Y-m-d H:i:s'),
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
				$data_aadhar['aadhaar_image'] = 'document/aadhaar/'.$aadhaar_image;
				$config = NULL;
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
				$data_pancard['pancard_image'] = 'document/pancard/'.$pancard_image;
				
				$config = NULL;
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
				$data_license['license_image'] = 'document/license/'.$license_image;
				$config = NULL;
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
				$data_police['police_image'] = 'document/police/'.$police_image;
				$config = NULL;
			}
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data, $udata, $countryCode) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address, $countryCode) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_police, $countryCode) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_license, $countryCode);
			
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
			
			$q = $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile, t.is_verify as taxi_verify,  up.gender, If(up.is_approved = 1 && t.is_verify = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1", 'left')
			->where("u.id", $id)
			->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				if($q->row('taxi_verify') == 0){
					$this->session->set_flashdata('error', lang("your_account_taxi_not_verified"));
				}else{
					$this->session->set_flashdata('error', lang("your_account_has_not_verified"));
				}
            	admin_redirect('people/driver');
			}else{
			$sms_message = $this->input->post('first_name').' your account verified status has been successfully. ';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
			$notification['title'] = 'Verified Status';
			$notification['message'] = $this->input->post('first_name').' your account verified status has been successfully. ';
			$notification['user_type'] = 2;
			$notification['user_id'] = $id;
			$this->site->insertNotification($notification);
			
            $this->session->set_flashdata('message', $this->input->post('first_name').lang('details_has_been_verified'));
            admin_redirect('people/driver');
			}
		}
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('driver_status')));
            $meta = array('page_title' => lang('driver_status'), 'bc' => $bc);
			
			/*user*/
			
			$this->data['driver_result'] = $driver_result;
			$this->data['user_id'] = $id;
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			/*address*/
			$result_address = $this->verification_model->getUserAddress($id);
			$this->data['result_address'] = $result_address;
			$this->data['lcontinents'] = $this->masters_model->getALLContinents();
			$this->data['pcontinents'] = $this->masters_model->getALLContinents();
			
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->local_continent_id);
			
			$this->data['lzones'] = $this->masters_model->getZone_bycountry($result_address->local_country_id);
			$this->data['lstates'] = $this->masters_model->getState_byzone($result_address->local_zone_id);
			$this->data['lcitys'] = $this->masters_model->getCity_bystate($result_address->local_state_id);
			$this->data['lareas'] = $this->masters_model->getArea_bycity($result_address->local_city_id);
			
			$this->data['pcountrys'] = $this->masters_model->getCountry_bycontinent($result_address->permanent_continent_id);
			
			$this->data['pzones'] = $this->masters_model->getZone_bycountry($result_address->permanent_country_id);
			$this->data['pstates'] = $this->masters_model->getState_byzone($result_address->permanent_zone_id);
			$this->data['pcitys'] = $this->masters_model->getCity_bystate($result_address->permanent_state_id);
			$this->data['pareas'] = $this->masters_model->getArea_bycity($result_address->permanent_city_id);
			
			
			/*account*/
			$result_account = $this->verification_model->getUserBank($id);
			$this->data['result_account'] = $result_account;
			/*document*/
			$result_document = $this->verification_model->getUserDocument($id);
			$this->data['result_document'] = $result_document;
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
			$this->data['license_type'] = $this->masters_model->getCountry_bylicensetype($result_document->license_country_id);
			
            $this->page_construct('people/status_driver', $meta, $this->data);
        }        
    }
	
		
	function driver_view($id){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
        $this->page_construct('people/driver_view', $meta, $this->data);
    }
	
	function driver_edit($user_id, $view){
		$result = $this->users_model->getUserEdit($user_id);
		
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
				$countryCode = $result->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['view'] = $view;
		$group_id = $this->Driver;
		
		$this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1 ", 'left')
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
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/driver');
			}
		}
		
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		$same_address = $this->input->post('same_address') ? $this->input->post('same_address') : 0;
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->first_name == $this->input->post('first_name') && $result->last_name == $this->input->post('last_name') && $result->gender == $this->input->post('gender') && $result->dob == $this->input->post('dob')){
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
				'is_country' => $countryCode,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'complete_user' => 1,
				'join_type' => 2,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'is_country' => $countryCode,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'is_complete_profile' => 1
			
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
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_pincode') == $result->local_pincode){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode') ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $same_address == 1 ? $this->input->post('local_pincode') : $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address') ? $this->input->post('local_address') : '',
				'is_country' => $countryCode,
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				'same_address' => $same_address,
				'permanent_address' => $same_address == 1 ? $this->input->post('local_address') : $this->input->post('permanent_address'),
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
				if($same_address == 1){
					$user_address['permanent_image'] = 'document/local_address/'.$local_image;
				}
				$config = NULL;
			}else{
				$user_address['local_image'] =  $result->local_image;
				if($same_address == 1){
					$user_address['permanent_image'] =  $result->local_image;
				}
			}
			
			if($same_address != 0){
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
				'is_country' => $countryCode,
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
			
			if($this->input->post('license_dob') == $result->license_dob && $this->input->post('license_no') == $result->license_no && $this->input->post('license_ward_name') == $result->license_ward_name  && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') == $result->license_issued_on && $this->input->post('license_validity') == $result->license_validity && $_FILES['license_image']['size'] == 0){
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
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				'is_country' => $countryCode,
				'pancard_no' => $this->input->post('pancard_no'),
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				'license_dob' => $this->input->post('license_dob'),
				
				'license_ward_name' => $this->input->post('license_ward_name'),
				
				'license_no' => $this->input->post('license_no'),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),	
				
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
		
        if ($this->form_validation->run() == true && $this->users_model->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("driver_updated"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('driver')));
            $meta = array('page_title' => lang('driver_details'), 'bc' => $bc);
			
			
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
			
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
			$this->data['license_type'] = $this->masters_model->getCountry_bylicensetype($result->license_country_id);
			
            $this->page_construct('people/edit_driver', $meta, $this->data);
        }        
    }
	
	function driver_adminedit($user_id){
		$result = $this->users_model->getUserEdit($user_id);
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
		$group_id = $this->Driver;
		
		//up.is_approved = 1 && t.is_verify = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1, '1', '0'
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile, t.complete_taxi,  up.gender, If(up.is_approved = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1 ", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->db->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->db->where("u.is_country", $countryCode);
			}
			
			$q = $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 1){
				$this->session->set_flashdata('error', lang("your_account_active_not_added"));
            	admin_redirect('people/driver');
			}
		}
		
		$complete_taxi = $q->row('complete_taxi');
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		
		$same_address = $this->input->post('same_address') ? $this->input->post('same_address') : 0;
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		  
		   $user = array(
		   		'join_type' => 2,
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_country' => $countryCode,
				'complete_user' => 1,
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_country' => $countryCode,
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
				$user['photo']  = $result->photo;
			}
			
			
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $same_address == 1 ? $this->input->post('local_pincode') : $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				'same_address' => $same_address,
				'permanent_address' => $same_address == 1 ? $this->input->post('local_address') : $this->input->post('permanent_address'),
				'complete_address' => 1,
				'is_country' => $countryCode,
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
				if($same_address == 1){
					$user_address['permanent_image'] = 'document/local_address/'.$local_image;
				}
				$config = NULL;
			}else{
				$user_address['local_image'] =  $result->local_image;
				if($same_address == 1){
					$user_address['permanent_image'] =  $result->local_image;
				}
			}
			
			if($same_address != 1){
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
			}
			
			
			$user_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_country' => $countryCode,
				'complete_bank' => 1,
				'is_edit' => 1
			);
			
			
			
			$user_document = array(
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				
				'license_dob' =>  $this->input->post('license_dob'),
				'license_ward_name' =>  $this->input->post('license_ward_name'),
				'license_no' =>  $this->input->post('license_no'),
				'license_country_id' =>  $this->input->post('license_country_id'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_issuing_authority' =>  $this->input->post('license_issuing_authority'),
				'license_issued_on' =>  $this->input->post('license_issued_on'),
				'license_validity' =>  $this->input->post('license_validity'),
			'is_country' => $countryCode,
				'police_on' =>  $this->input->post('police_on'),
				'police_til' =>  $this->input->post('police_til'),	
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
			
			if($complete_taxi == 0){
			$make_name = $this->people_model->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->people_model->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->people_model->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			
			$taxi = array(
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				'name' => $this->input->post('taxi_name'),
				'number' => $this->input->post('number'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'category' => $this->input->post('category'),
				'weight' => $this->input->post('weight'),
				'min_weight' => $this->input->post('min_weight'),
				'max_weight' => $this->input->post('max_weight'),
				'length' => $this->input->post('length'),
				'height' => $this->input->post('height'),
				'width' => $this->input->post('width'),
				'capacity' => $this->input->post('capacity') ? $this->input->post('capacity') : 0,
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_country' => $countryCode,
				'is_edit' => 1,
				'complete_taxi' => 1
		   );
		   
		   
		   
		   if ($_FILES['taxi_photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$taxi_photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$taxi_photo;
				$config = NULL;
			}
			
			$taxi_document = array(
				//'user_id' => $vendor->id,
				//'group_id' => $vendor->group_id,
		   		'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'taxation_amount_paid' => $this->input->post('taxation_amount_paid'),
				'taxation_due_date' => $this->input->post('taxation_due_date'),
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'speed_due_date' => $this->input->post('speed_due_date'),
				'puc_due_date' => $this->input->post('puc_due_date'),
				'is_edit' => 1,
				'is_country' => $countryCode,
				'complete_taxidocument' => 1
				
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
				$taxi_document['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}
			
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
				$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			}
			
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
				$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}
			
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
				$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}
			
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
				$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}
			
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
				$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}
			
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
				$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}
			
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
				$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			
			}else{
				$taxi = '';
				$taxi_document = '';
			}
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->editadmin_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("driver_updated"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('admin_support_driver')));
            $meta = array('page_title' => lang('admin_support_driver'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			$this->data['complete_taxi'] = $complete_taxi;
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
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['categorys'] = $this->masters_model->getALLTaxi_category($countryCode);
			$this->data['types'] = $this->masters_model->getcategoryALLTaxi_type($result->category, $countryCode);	
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel($countryCode);
			
			$this->data['license_countrys'] = $this->masters_model->getALLLicenseCountry();
			
			$this->data['license_type'] = $this->masters_model->getCountry_bylicensetype($result->license_country_id);
            $this->page_construct('people/editadmin_driver', $meta, $this->data);
        }        
    }
	
	/*###############*/
	function edit_employee($user_id){
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'));
		
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
		
		$group_id = $this->Employee;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", "left")
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", "left")
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", "left")
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", "left")
			->where("u.group_id", $group_id)
			->where("u.id", $user_id);
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->db->where("u.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->db->where("u.is_country", $countryCode);
			}
			
		$q= $this->db->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/employee/');
			}
		}
		
		
		
		
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
		
		
        if ($this->form_validation->run() == true) {
		   $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		    		   
		   if($result->first_name == $this->input->post('first_name') && $result->last_name == $this->input->post('last_name')){
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
				'is_country' => $countryCode,
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => $is_edit,
				'complete_user' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'is_country' => $countryCode,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => $is_edit,
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
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_pincode') == $result->local_pincode){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $_FILES['local_image']['size'] == 0  ? $this->input->post('local_pincode') : '',
				'permanent_pincode' => $_FILES['permanent_image']['size'] == 0 ?  $this->input->post('permanent_pincode') : '',
				
				'local_address' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_address') : '',
				'is_country' => $countryCode,
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_address') : '',
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
				'is_country' => $countryCode,
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
				'is_country' => $countryCode,
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('pancard_no') : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
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
			
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_employee($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("employee_updated"));
            admin_redirect('people/employee/');
        } else {
            $this->session->set_flashdata('error', lang("employee_not_updated"));
			admin_redirect('people/employee_edit/'.$user_id);
        }        
    
	}
	
	function edit_vendor($user_id){
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'));
		if($this->session->userdata('group_id') == 1){
			if($result->is_country != ''){
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
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1 && u.is_approved = 1, '1', '0') as status ")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("user_vendor uven", "uven.user_id = u.id AND uven.is_edit = 1 ", 'left')
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
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/vendor/');
			}
		}
		
		
		
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
				'is_country' => $countryCode,
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
				'is_country' => $countryCode,
				'approved_by' => $approved_by,
				'is_edit' => 1,
				'is_complete_profile' => 1
			
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
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_pincode') == $result->local_pincode){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				'is_country' => $countryCode,
				'permanent_address' => $this->input->post('permanent_address'),
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
				'is_country' => $countryCode,
				'complete_bank' => 1,
				'is_edit' => 1
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
			
			if($this->input->post('license_dob') == $result->license_dob && $this->input->post('license_no') == $result->license_no && $this->input->post('license_ward_name') == $result->license_ward_name  && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') == $result->license_issued_on && $this->input->post('license_validity') == $result->license_validity && $_FILES['license_image']['size'] == 0){
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
				'is_country' => $countryCode,
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $this->input->post('pancard_no'),
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				'complete_document' => 1,
				
				
				'loan_information' => $this->input->post('loan_information'),
				
				'loan_verify' => $loan_verify,
				'loan_approved_by' => $loan_approved_by,
				'loan_approved_on' => $loan_approved_on,
					
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
				'is_country' => $countryCode,
				'approved_on' => $vendor_approved_on,
				'complete_vendor' => 1, 
				'is_edit' => 1
			);
			
			//$this->sma->print_arrays($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->users_model->edit_vendor($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("vendor_updated"));
            admin_redirect('people/vendor/');
        } else {
           $this->session->set_flashdata('error', lang("vendor_not_updated"));
			admin_redirect('people/vendor_edit/'.$user_id);
        }        
    }
	
	function edit_driver($user_id){
		$result = $this->users_model->getUserEdit($this->session->userdata('user_id'));
		
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
		$group_id = $this->Driver;
		
		 $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(up.is_approved = 1 && t.complete_taxi = 1 && ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1 ", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1 ", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1 ", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1 ", 'left')
			->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1 ", 'left')
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
				$this->session->set_flashdata('error', lang("your_account_has_been_deactive"));
            	admin_redirect('people/driver');
			}
		}
		
		
		
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
				'is_country' => $countryCode,
				'complete_user' => 1,
				'is_edit' => 1
		   );
		   
		   $user_profile = array(
		   		'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'gender' => $this->input->post('gender'),
				'dob' => $this->input->post('dob'),
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_country' => $countryCode,
				'is_edit' => 1,
				'is_complete_profile' => 1
				
			
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
			
			if($this->input->post('local_address') == $result->local_address && $this->input->post('local_pincode') == $result->local_pincode){
				$local_verify = $result->local_verify;
				$local_approved_by = $result->local_approved_by;
				$local_approved_on = $result->local_approved_on;
			}else{
				$local_verify = 0;
				$local_approved_by = 0;
				$local_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permanent_address') == $result->permanent_address && $this->input->post('permanent_pincode') == $result->permanent_pincode){
				$permanent_verify = $result->permanent_verify;
				$permanent_approved_by = $result->permanent_approved_by;
				$permanent_approved_on = $result->permanent_approved_on;
			}else{
				$permanent_verify = 0;
				$permanent_approved_by = 0;
				$permanent_approved_on = '0000:00:00 00:00:00';
			}
			$user_address = array(
				'local_pincode' => $this->input->post('local_pincode'),
				'permanent_pincode' => $this->input->post('permanent_pincode'),
				
				'local_address' => $this->input->post('local_address'),
				
				'local_verify' => $local_verify,
				'is_country' => $countryCode,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				'complete_address' => 1,
				'permanent_address' => $this->input->post('permanent_address'),
				
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
				'is_country' => $countryCode,
				'complete_bank' => 1,
				'is_edit' => 1
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
			
			if($this->input->post('license_dob') == $result->license_dob && $this->input->post('license_no') == $result->license_no && $this->input->post('license_ward_name') == $result->license_ward_name  && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') == $result->license_issued_on && $this->input->post('license_validity') == $result->license_validity && $_FILES['license_image']['size'] == 0){
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
				
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				'is_country' => $countryCode,
				'pancard_no' => $this->input->post('pancard_no'),
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
				
				
				'license_dob' => $this->input->post('license_dob'),
				'license_no' => $this->input->post('license_no'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_type' => $this->input->post('license_type') != NULL ? json_encode($this->input->post('license_type')) : '',
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),	
				
				'police_verify' => $police_verify,
				'police_approved_by' => $police_approved_by,
				'police_approved_on' => $police_approved_on,
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
		
        if ($this->form_validation->run() == true && $this->users_model->edit_driver($this->session->userdata('user_id'), $user, $user_profile, $user_address, $user_bank, $user_document)){
			
			$sms_message = $this->input->post('first_name').' your account edit has been successfully. Waiting for admin approval process';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("driver_updated"));
            admin_redirect('people/driver');
        } else {
			$this->session->set_flashdata('error', lang("driver_not_updated"));
			admin_redirect('people/driver_edit/'.$user_id);
            
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
		$location_id = $this->input->post('location_id');
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
        $data = $this->masters_model->getCountry_bycontinent($continent_id);
        
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
		$location_id = $this->input->post('location_id');
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
        $data = $this->masters_model->getZone_bycountry($country_id);
        
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
		$location_id = $this->input->post('location_id');
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
		
        $data = $this->masters_model->getState_byzone($zone_id);
        
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
		$location_id = $this->input->post('location_id');
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
		
        $data = $this->masters_model->getCity_bystate($state_id);
        
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
		$location_id = $this->input->post('location_id');
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
		
        $data = $this->masters_model->getArea_bycity($city_id);
        
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
		$location_id = $this->input->post('location_id');
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
	/*function status($status,$id){
        $data['is_verify'] = 0;
        if($status=='active'){
            $data['is_verify'] = 1;
        }
		
        $this->people_model->update_status($data,$id);
		redirect($_SERVER["HTTP_REFERER"]);
    }*/
	
	function employee_actions($wh = NULL)
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
        $group_id = $this->Employee;	
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Staff');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('mobile'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('gender'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('department'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('designation'));
                   
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
					$res = $this->people_model->getALLEmployee($group_id, $countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created_on);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->first_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->last_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->email);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->mobile);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->gender);
						$this->excel->getActiveSheet()->SetCellValue('G' . $row, $value->department);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $value->designation);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'staff_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function vendor_actions($wh = NULL)
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
        $group_id = $this->Vendor;	
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Vendor');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('mobile'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('gender'));
                   
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
					$res = $this->people_model->getALLVendor($group_id, $countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created_on);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->first_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->last_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->email);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->mobile);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->gender);
                       
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
                    $filename = 'vendor_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function driver_actions($wh = NULL)
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
        $group_id = $this->Driver;	
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Driver');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('mobile'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('gender'));
                   
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
					$res = $this->people_model->getALLDriver($group_id, $countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created_on);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->first_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->last_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->email);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->mobile);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->gender);
                       
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
                    $filename = 'driver_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function customer_actions($wh = NULL)
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
        $group_id = $this->Customer;	
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Customer');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('mobile'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('gender'));
                   
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
					$res = $this->people_model->getALLCustomer($group_id, $countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created_on);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->first_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->last_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->email);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->mobile);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->gender);
                       
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
                    $filename = 'customer_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
}
