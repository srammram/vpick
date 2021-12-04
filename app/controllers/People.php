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
		$this->lang->admin_load('people', $this->Settings->user_language);
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
		$this->load->admin_model('people_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('verification_model');
		$this->load->admin_model('users_model');
		
    }
	
	/*###### Customer*/
    function customer($action=false){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer')));
        $meta = array('page_title' => lang('customer'), 'bc' => $bc);
        $this->page_construct('people/customer', $meta, $this->data);
    }
    function getCustomer(){
		$group_id = $this->Customer;
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, {$this->db->dbprefix('users')}.active as status")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", 'left')
			
			->where("users.group_id", $group_id)
			->where("users.is_edit", 1)
            ->edit_column('status', '$1__$2', 'status, id')
			->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('people/customer_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
        echo $this->datatables->generate();
    }
	
	function add_customer(){
        $this->form_validation->set_rules('mobile', lang("mobile"), 'required|is_unique[users.mobile]');  
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('local_address', lang("address"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
				'password' => md5($this->input->post('password')),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Customer,
				'is_edit' => 1
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
				$user_profile['photo'] = 'user/customer/'.$photo;
				$config = NULL;
			}
			
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
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
		
        if ($this->form_validation->run() == true && $this->people_model->add_customer($user, $user_profile, $user_address)){
			
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
	
	function customer_status($status,$id){
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->people_model->update_customer_status($data,$id);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function customer_view($id){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer')));
        $meta = array('page_title' => lang('customer'), 'bc' => $bc);
        $this->page_construct('people/customer_view', $meta, $this->data);
    }
	
	/*###### Employee*/
    function employee($action=false){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employee')));
        $meta = array('page_title' => lang('employee'), 'bc' => $bc);
        $this->page_construct('people/employee', $meta, $this->data);
    }
    function getEmployee(){
		$group_id = $this->Employee;	
		
        $this->load->library('datatables');
		if($this->Admin == $this->session->userdata('group_id') || $this->Owner == $this->session->userdata('group_id')){
			$edit = " | <a href='" . admin_url('people/employee_edit/$1') . "' class='tip' title='" . lang("edit") . "'>edit</a>";
		}else{
			$edit = "";
		}
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, ud.name as department, ur.position as designation, {$this->db->dbprefix('users')}.active as active,   If(udd.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && udd.aadhar_verify = 1 , '1', '0') as status ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", "left")
			->join("user_document udd", "udd.user_id = users.id AND udd.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", "left")
			->join("user_permission per", 'per.user_id = users.id AND per.is_edit = 1', "left")
			->join("user_department ud", 'ud.id = per.department_id', "left")
			->join("user_roles ur", 'ur.id = per.designation_id', "left")
			->where("users.group_id", $group_id)
			//->where("users.is_edit", 1)
            ->edit_column('active', '$1__$2', 'id, active')
            ->edit_column('status', '$1___$2', 'id, status')
			
			->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('people/employee_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a> ".$edit."</div>", "id");
        echo $this->datatables->generate();
    }
	
	function add_employee(){
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        $this->form_validation->set_rules('mobile', lang("mobile"), 'required|is_unique[users.mobile]');     
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('department_id', lang("department"), 'required');
		$this->form_validation->set_rules('designation_id', lang("designation"), 'required');
		$this->form_validation->set_rules('reporter_id', lang("reporter"), 'required');
		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
				
		
        if ($this->form_validation->run() == true) {
		   $designation_id = $this->site->getUserroleID($this->input->post('designation_id'));
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   $user = array(
		   		'oauth_token' => $oauth_token,
				'email' => $this->input->post('email'),
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
				'is_edit' => 1
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
				$config = NULL;
			}
			
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
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
				'aadhaar_no' => $aadhaar_no,
				'pancard_no' => $pancard_no,
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
		
        if ($this->form_validation->run() == true && $this->people_model->add_employee($user, $user_profile, $user_address, $user_bank, $user_permission, $user_document, $this->Employee)){
			
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
		
		if($status == 'active'){
			$this->session->set_flashdata('message', 'Your account is active!');
            admin_redirect('people/employee');
		}
		
		$this->form_validation->set_rules('is_approved', $this->lang->line("is_approved"), 'required');
		$this->form_validation->set_rules('local_address', $this->lang->line("local_address"), 'required');
		$this->form_validation->set_rules('local_continent_id', $this->lang->line("local_continent_id"), 'required');
		$this->form_validation->set_rules('local_country_id', $this->lang->line("local_country_id"), 'required');
		$this->form_validation->set_rules('local_zone_id', $this->lang->line("local_zone_id"), 'required');
		$this->form_validation->set_rules('local_state_id', $this->lang->line("local_state_id"), 'required');
		$this->form_validation->set_rules('local_city_id', $this->lang->line("local_city_id"), 'required');
		$this->form_validation->set_rules('local_area_id', $this->lang->line("local_area_id"), 'required');
		$this->form_validation->set_rules('permanent_address', $this->lang->line("permanent_address"), 'required');
		$this->form_validation->set_rules('permanent_continent_id', $this->lang->line("permanent_continent_id"), 'required');
		$this->form_validation->set_rules('permanent_country_id', $this->lang->line("permanent_country_id"), 'required');
		$this->form_validation->set_rules('permanent_zone_id', $this->lang->line("permanent_zone_id"), 'required');
		$this->form_validation->set_rules('permanent_state_id', $this->lang->line("permanent_state_id"), 'required');
		$this->form_validation->set_rules('permanent_city_id', $this->lang->line("permanent_city_id"), 'required');
		$this->form_validation->set_rules('permanent_area_id', $this->lang->line("permanent_area_id"), 'required');
		
		$this->form_validation->set_rules('account_no', $this->lang->line("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', $this->lang->line("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', $this->lang->line("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', $this->lang->line("ifsc_code"), 'required');
		
		
		$this->form_validation->set_rules('aadhaar_no', $this->lang->line("aadhaar_no"), 'required');
		$this->form_validation->set_rules('pancard_no', $this->lang->line("pancard_no"), 'required');
		
        if ($this->form_validation->run() == true) {
		   
			$data = array(
			
				'active' => 1,
				'is_approved' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
				'permanent_verify' => $this->input->post('permanent_verify'),
				'local_verify' => $this->input->post('local_verify'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'local_approved_on' => date('Y-m-d H:i:s')
			);
			
			$data_bank = array(
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('aadhar_verify'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('pancard_verify'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
		
			
			
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard);
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
            $this->session->set_flashdata('message', $this->input->post('first_name').' details has been verified');
            admin_redirect('people/employee');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('active_employee')));
            $meta = array('page_title' => lang('active_employee'), 'bc' => $bc);
			
			/*user*/
			$this->data['employee_result'] = $this->verification_model->getUserDetails($id);
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

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->users_model->getUser($id);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employee')));
        $meta = array('page_title' => lang('employee'), 'bc' => $bc);
        $this->page_construct('people/employee_view', $meta, $this->data);
    }
	
	function employee_edit($user_id){
		
		
		$group_id = $this->Employee;
		
		$q = $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", "left")
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", "left")
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", "left")
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", "left")
			->where("u.group_id", $group_id)
			->where("u.id", $user_id)
			->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your account has been deactive. so if can not edit."));
            	admin_redirect('people/employee');
			}
		}
		
		$result = $this->users_model->getUserEdit($user_id);
		
		
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
				$active = 1;
				$is_approved = 1;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
			}else{
				$is_edit = 0;
				$active = 0;
				$is_approved = 0;
				$approved_on = '0000-00-00';
				$approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'active' => $active,
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => $is_edit
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
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;		
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
				'local_continent_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_continent_id') : 0,
				'local_country_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_country_id') : 0,
				'local_zone_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_zone_id') : 0,
				'local_state_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_state_id') : 0,
				'local_city_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_city_id') : 0,
				'local_area_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_area_id') : 0,
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_address') : '',
				'permanent_continent_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_continent_id') : 0,
				'permanent_country_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_country_id') : 0,
				'permanent_zone_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_zone_id') : 0,
				'permanent_state_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_state_id') : 0,
				'permanent_city_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_city_id') : 0,
				'permanent_area_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_area_id') : 0,
				
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
			
			if($this->input->post('account_no') == $result->account_no && $this->input->post('bank_name') == $result->bank_name && $this->input->post('branch_name') == $result->branch_name && $this->input->post('ifsc_code') == $result->ifsc_code){
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
			
				
			$user_document = array(
				
				'aadhaar_no' => $_FILES['aadhaar_image']['size'] == 0 ? $this->input->post('aadhaar_no') : '',
				
				'aadhar_verify' => $aadhar_verify,
				'aadhar_approved_by' => $aadhar_approved_by,
				'aadhar_approved_on' => $aadhar_approved_on,
				
				
				'pancard_no' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('pancard_no') : '',
				
				'pancard_verify' => $pancard_verify,
				'pancard_approved_by' => $pancard_approved_by,
				'pancard_approved_on' => $pancard_approved_on,
									
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
		
        if ($this->form_validation->run() == true && $this->users_model->edit_employee($user_id, $user, $user_profile, $user_address, $user_bank, $user_document)){
			
            $this->session->set_flashdata('message', lang("employee_updated"));
            admin_redirect('people/employee');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('users/profile'), 'page' => lang('profile')), array('link' => '#', 'page' => lang('edit_employee')));
            $meta = array('page_title' => lang('edit_employee'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor();
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

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
        $meta = array('page_title' => lang('vendor'), 'bc' => $bc);
        $this->page_construct('people/vendor', $meta, $this->data);
    }
	
    function getVendor(){
		//{$this->db->dbprefix('users')}.active as status
		$group_id = $this->Vendor;
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, {$this->db->dbprefix('users')}.active as active, If(ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.loan_verify = 1 && uven.is_verify = 1, '1', '0') as status ")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = users.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", 'left')
			->join("user_vendor uven", "uven.user_id = users.id AND uven.is_edit = 1", 'left')
			->where("users.group_id", $group_id)
			//->where("users.is_edit", 1)
            ->edit_column('active', '$1__$2', 'id, active')
            ->edit_column('status', '$1___$2', 'id, status')
			
			->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('people/vendor_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a> | <a href='" . admin_url('people/vendor_allocate/$1') . "' class='tip' title='" . lang("Zone_allocate") . "'>Zone Allocate</a></div>", "id");
        echo $this->datatables->generate();
    }
	
	function add_vendor(){
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        $this->form_validation->set_rules('mobile', lang("mobile"), 'required|is_unique[users.mobile]');  
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
			
			$check_mobile = $this->people_model->checkMobilevendor($this->input->post('mobile'), $this->input->post('country_code'));
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("Mobile number already use both driver and vendor"));
           		admin_redirect('people/vendor');
			}elseif($check_mobile == 2){
				$this->session->set_flashdata('error', lang("Mobile number already use driver"));
           		admin_redirect('people/vendor');
			}elseif($check_mobile == 3){
				$this->session->set_flashdata('error', lang("Mobile number already use vendor"));
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
				'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'parent_type' => $this->Admin,
				'parent_id' => $this->session->userdata('user_id'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Vendor,
				'active' => 1,
				'is_edit' => 1
		   );
		   
		   $driver_user = array(
		   		'oauth_token' => $driver_oauth_token,
				'devices_imei' => 'first_time',
				'email' => $this->input->post('email'),
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
				'is_edit' => 1
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
				$config = NULL;
			}
			
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
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
				'is_edit' => 1
			);
			
			$user_vendor = array(
				
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity'),
				'is_edit' => 1
			);
			
			$user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				'loan_information' => $this->input->post('loan_information'),
				'is_edit' => 1
				
			);
			
			$driver_user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type'),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),	
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
			
			$taxi = array(
				'name' => $this->input->post('name'),
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
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1
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
				'user_id' => $vendor->id,
				'group_id' => $vendor->group_id,
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
			
			//$this->sma->print_arrays($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->people_model->add_vendor($user, $driver_user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $driver_user_document, $this->Vendor, $this->Driver, $operator, $taxi, $taxi_document)){
			
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
			$this->data['types'] = $this->masters_model->getALLTaxi_type();
            $this->page_construct('people/add_vendor', $meta, $this->data);
        }        
    }
	
	function vendor_status($status, $id){
		
		if($status == 'active'){
			$this->session->set_flashdata('message', 'Your account is active!');
            admin_redirect('people/vendor');
		}
		
		$this->form_validation->set_rules('is_approved', $this->lang->line("is_approved"), 'required');
		$this->form_validation->set_rules('local_address', $this->lang->line("local_address"), 'required');
		$this->form_validation->set_rules('local_continent_id', $this->lang->line("local_continent_id"), 'required');
		$this->form_validation->set_rules('local_country_id', $this->lang->line("local_country_id"), 'required');
		$this->form_validation->set_rules('local_zone_id', $this->lang->line("local_zone_id"), 'required');
		$this->form_validation->set_rules('local_state_id', $this->lang->line("local_state_id"), 'required');
		$this->form_validation->set_rules('local_city_id', $this->lang->line("local_city_id"), 'required');
		$this->form_validation->set_rules('local_area_id', $this->lang->line("local_area_id"), 'required');
		$this->form_validation->set_rules('permanent_address', $this->lang->line("permanent_address"), 'required');
		$this->form_validation->set_rules('permanent_continent_id', $this->lang->line("permanent_continent_id"), 'required');
		$this->form_validation->set_rules('permanent_country_id', $this->lang->line("permanent_country_id"), 'required');
		$this->form_validation->set_rules('permanent_zone_id', $this->lang->line("permanent_zone_id"), 'required');
		$this->form_validation->set_rules('permanent_state_id', $this->lang->line("permanent_state_id"), 'required');
		$this->form_validation->set_rules('permanent_city_id', $this->lang->line("permanent_city_id"), 'required');
		$this->form_validation->set_rules('permanent_area_id', $this->lang->line("permanent_area_id"), 'required');
		
		$this->form_validation->set_rules('account_no', $this->lang->line("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', $this->lang->line("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', $this->lang->line("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', $this->lang->line("ifsc_code"), 'required');
		
		
		$this->form_validation->set_rules('aadhaar_no', $this->lang->line("aadhaar_no"), 'required');
		$this->form_validation->set_rules('loan_information', $this->lang->line("loan_information"), 'required');
		$this->form_validation->set_rules('pancard_no', $this->lang->line("pancard_no"), 'required');
		
		$this->form_validation->set_rules('gst', $this->lang->line("gst"), 'required');
		$this->form_validation->set_rules('telephone_number', $this->lang->line("telephone_number"), 'required');
		$this->form_validation->set_rules('legal_entity', $this->lang->line("legal_entity"), 'required');
		
        if ($this->form_validation->run() == true) {
		   
			$data = array(
			
				'active' => 1,
				'is_approved' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			
			$data_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
				'permanent_verify' => $this->input->post('permanent_verify'),
				'local_verify' => $this->input->post('local_verify'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'local_approved_on' => date('Y-m-d H:i:s')
			);
			
			$data_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('aadhar_verify'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_loan = array(
				'loan_information' => $this->input->post('loan_information'),
				'loan_verify' => $this->input->post('loan_verify'),
				'loan_approved_by' => $this->session->userdata('user_id'),
				'loan_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('pancard_verify'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_vendor = array(
				'gst' => $this->input->post('gst'),
				'telephone_number' => $this->input->post('telephone_number'),
				'legal_entity' => $this->input->post('legal_entity')
			);
			
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_loan) && $this->verification_model->update_vendor_common($this->input->post('vendor_id'), $data_vendor);
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
            $this->session->set_flashdata('message', $this->input->post('first_name').' details has been verified');
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/vendor'), 'page' => lang('vendor')), array('link' => '#', 'page' => lang('active_vendor')));
            $meta = array('page_title' => lang('active_vendor'), 'bc' => $bc);
			
			/*user*/
			$this->data['vendor_result'] = $this->verification_model->getUserDetails($id);
			$this->data['vendor_personal_result'] = $this->people_model->getVendorDetails($id);
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
			
            $this->page_construct('people/status_vendor', $meta, $this->data);
        }        
    }
	
	function vendor_view($id){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
        $meta = array('page_title' => lang('vendor'), 'bc' => $bc);
        $this->page_construct('people/vendor_view', $meta, $this->data);
    }
	
	function vendor_allocate($id){
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$user = $this->users_model->getUser($id);
		if($user->local_verify == 0){
			$this->session->set_flashdata('message', 'Your address has been deactive. do not allocated zone!');
            admin_redirect('people/vendor');
		}
		
		$this->form_validation->set_rules('associated_id', $this->lang->line("associated"), 'required');
		$associated_id = $this->input->post('associated_id');
        if ($this->form_validation->run() == true) {
			$zonal_details = $this->people_model->getZonalUser($associated_id);
			
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
		
		 if ($this->form_validation->run() == true && $this->people_model->zonal_allocated($vendor, $id)){
			
            $this->session->set_flashdata('message', $this->input->post('first_name').' allocated zonal');
            admin_redirect('people/vendor');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['user'] = $user;
			$this->data['zones'] = $this->people_model->getZoneuser($user->local_zone_id, $this->Employee);
			$this->data['id'] = $id;
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
			$meta = array('page_title' => lang('vendor'), 'bc' => $bc);
			$this->page_construct('people/vendor_allocate', $meta, $this->data);
		}
    }
	
	/*###### Driver*/
    function driver($action=false){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
        $this->page_construct('people/driver', $meta, $this->data);
    }
    function getDriver(){
		$group_id = $this->Driver;
		
        $this->load->library('datatables');
		/*if($this->Vendor == $this->session->userdata('group_id')){
			$edit = "<a href='" . admin_url('people/driver_edit/$1') . "' class='tip' title='" . lang("edit") . "'>Edit</a> | ";
			
			$parent_id = '->where("users.group_id", $this->session->userdata("user_id"))'; 
		}else{
			$edit = " ";
			$parent_id = "";
		}*/
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, up.first_name, up.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile,  up.gender, {$this->db->dbprefix('users')}.active = 1, If(ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1, '1', '0') as status")
            ->from("users")
			->join("user_profile up", "up.user_id = users.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = users.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = users.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = users.id AND uadd.is_edit = 1", 'left')
			->where("users.group_id", $group_id);
			/*if($this->Vendor == $this->session->userdata('group_id')){
				$this->datatables->where("users.parent_id", $this->session->userdata("user_id"));
			}*/
			$this->datatables->where("users.is_edit", 1);
            //->edit_column('active', '$1__$2', 'id, active')
           // ->edit_column('status', '$1___$2', 'id, status')
			$this->datatables->add_column("Actions", "<div class=\"text-center\">".$edit."<a href='" . admin_url('people/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			
        echo $this->datatables->generate();
		
		
    }
	
	function add_driver(){
		$this->form_validation->set_rules('email', lang("email_address"), 'required');
        $this->form_validation->set_rules('mobile', lang("mobile"), 'required|is_unique[users.mobile]');  
        
		$this->form_validation->set_rules('first_name', lang("first_name"), 'required');
		$this->form_validation->set_rules('gender', lang("gender"), 'required');
		$this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', lang("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', lang("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', lang("ifsc_code"), 'required');
		
		
		
        if ($this->form_validation->run() == true) {
			
           $oauth_token = get_random_key(32,'users','oauth_token',$type='alnum');
		   $mobile_otp = random_string('numeric', 6);
		   	
			$check_mobile = $this->people_model->checkMobiledriver($this->input->post('mobile'), $this->input->post('country_code'));
			if($check_mobile == 1){
				$this->session->set_flashdata('error', lang("Mobile number already use driver"));
           		admin_redirect('people/driver');
			}
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
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
				'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'group_id' => $this->Driver,
				'is_edit' => 1
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
				$config = NULL;
			}
			
			$user_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
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
				'is_edit' => 1
			);
			
			
			
			
			
			$user_document = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'pancard_no' => $this->input->post('pancard_no'),
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type'),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),
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
			
			$taxi = array(
				'name' => $this->input->post('name'),
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
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('y-m-d H:i:s'),
				'is_edit' => 1
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
		
        if ($this->form_validation->run() == true && $this->people_model->add_driver($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $this->Driver, $this->input->post('parent_id'))){
			
            $this->session->set_flashdata('message', lang("driver_added"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('add_vendor')));
            $meta = array('page_title' => lang('add_driver'), 'bc' => $bc);
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor();
			$this->data['user_department'] = $this->masters_model->getALLUser_department();
			$this->data['user_designation'] = $this->people_model->getALLUser_designation();
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['types'] = $this->masters_model->getALLTaxi_type();
			
            $this->page_construct('people/add_driver', $meta, $this->data);
        }        
    }
	
	function driver_status($status, $id){
		
		
		if($status == 'active'){
			$this->session->set_flashdata('message', 'Your account is active!');
            admin_redirect('people/driver');
		}
		$this->form_validation->set_rules('is_approved', $this->lang->line("is_approved"), 'required');
		$this->form_validation->set_rules('local_address', $this->lang->line("local_address"), 'required');
		$this->form_validation->set_rules('local_continent_id', $this->lang->line("local_continent_id"), 'required');
		$this->form_validation->set_rules('local_country_id', $this->lang->line("local_country_id"), 'required');
		$this->form_validation->set_rules('local_zone_id', $this->lang->line("local_zone_id"), 'required');
		$this->form_validation->set_rules('local_state_id', $this->lang->line("local_state_id"), 'required');
		$this->form_validation->set_rules('local_city_id', $this->lang->line("local_city_id"), 'required');
		$this->form_validation->set_rules('local_area_id', $this->lang->line("local_area_id"), 'required');
		$this->form_validation->set_rules('permanent_address', $this->lang->line("permanent_address"), 'required');
		$this->form_validation->set_rules('permanent_continent_id', $this->lang->line("permanent_continent_id"), 'required');
		$this->form_validation->set_rules('permanent_country_id', $this->lang->line("permanent_country_id"), 'required');
		$this->form_validation->set_rules('permanent_zone_id', $this->lang->line("permanent_zone_id"), 'required');
		$this->form_validation->set_rules('permanent_state_id', $this->lang->line("permanent_state_id"), 'required');
		$this->form_validation->set_rules('permanent_city_id', $this->lang->line("permanent_city_id"), 'required');
		$this->form_validation->set_rules('permanent_area_id', $this->lang->line("permanent_area_id"), 'required');
		
		$this->form_validation->set_rules('account_no', $this->lang->line("account_no"), 'required');
		$this->form_validation->set_rules('bank_name', $this->lang->line("bank_name"), 'required');
		$this->form_validation->set_rules('branch_name', $this->lang->line("branch_name"), 'required');
		$this->form_validation->set_rules('ifsc_code', $this->lang->line("ifsc_code"), 'required');
		
		
		$this->form_validation->set_rules('aadhaar_no', $this->lang->line("aadhaar_no"), 'required');
		$this->form_validation->set_rules('pancard_no', $this->lang->line("pancard_no"), 'required');
		$this->form_validation->set_rules('police_on', $this->lang->line("police_on"), 'required');
		
		$this->form_validation->set_rules('license_dob', $this->lang->line("license_dob"), 'required');
		$this->form_validation->set_rules('license_ward_name', $this->lang->line("license_ward_name"), 'required');
		$this->form_validation->set_rules('license_type', $this->lang->line("license_type"), 'required');
		$this->form_validation->set_rules('license_issuing_authority', $this->lang->line("license_issuing_authority"), 'required');
		$this->form_validation->set_rules('license_issued_on', $this->lang->line("license_issued_on"), 'required');
		$this->form_validation->set_rules('license_validity', $this->lang->line("license_validity"), 'required');
		$this->form_validation->set_rules('license_verify', $this->lang->line("license_verify"), 'required');
		
        if ($this->form_validation->run() == true) {
		   
			$data = array(
			
				'active' => 1,
				'is_approved' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
			
			$data_address = array(
				'local_address' => $this->input->post('local_address'),
				'local_continent_id' => $this->input->post('local_continent_id'),
				'local_country_id' => $this->input->post('local_country_id'),
				'local_zone_id' => $this->input->post('local_zone_id'),
				'local_state_id' => $this->input->post('local_state_id'),
				'local_city_id' => $this->input->post('local_city_id'),
				'local_area_id' => $this->input->post('local_area_id'),
				'permanent_address' => $this->input->post('permanent_address'),
				'permanent_continent_id' => $this->input->post('permanent_continent_id'),
				'permanent_country_id' => $this->input->post('permanent_country_id'),
				'permanent_zone_id' => $this->input->post('permanent_zone_id'),
				'permanent_state_id' => $this->input->post('permanent_state_id'),
				'permanent_city_id' => $this->input->post('permanent_city_id'),
				'permanent_area_id' => $this->input->post('permanent_area_id'),
				'permanent_verify' => $this->input->post('permanent_verify'),
				'local_verify' => $this->input->post('local_verify'),
				'permanent_approved_by' => $this->session->userdata('user_id'),
				'permanent_approved_on' => date('Y-m-d H:i:s'),
				'local_approved_by' => $this->session->userdata('user_id'),
				'local_approved_on' => date('Y-m-d H:i:s')
			);
			
			$data_bank = array(
				'account_holder_name' => $this->input->post('account_holder_name'),
				'account_no' => $this->input->post('account_no'),
				'bank_name' => $this->input->post('bank_name'),
				'branch_name' => $this->input->post('branch_name'),
				'ifsc_code' => $this->input->post('ifsc_code'),
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_aadhar = array(
				'aadhaar_no' => $this->input->post('aadhaar_no'),
				'aadhar_verify' => $this->input->post('aadhar_verify'),
				'aadhar_approved_by' => $this->session->userdata('user_id'),
				'aadhar_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_pancard = array(
				'pancard_no' => $this->input->post('pancard_no'),
				'pancard_verify' => $this->input->post('pancard_verify'),
				'pancard_approved_by' => $this->session->userdata('user_id'),
				'pancard_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_police = array(
				'police_on' => $this->input->post('police_on'),
				'police_til' => $this->input->post('police_til'),
				'police_verify' => $this->input->post('police_verify'),
				'police_approved_by' => $this->session->userdata('user_id'),
				'police_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_license = array(
				'license_dob' => $this->input->post('license_dob'),
				'license_ward_name' => $this->input->post('license_ward_name'),
				'license_type' => $this->input->post('license_type'),
				'license_country_id' => $this->input->post('license_country_id'),
				'license_issuing_authority' => $this->input->post('license_issuing_authority'),
				'license_issued_on' => $this->input->post('license_issued_on'),
				'license_validity' => $this->input->post('license_validity'),
				'license_verify' => $this->input->post('license_verify'),
				'license_approved_by' => $this->session->userdata('user_id'),
				'license_approved_on' => date('Y-m-d H:i:s'),
			);
			
			
			$check_verify = $this->verification_model->update_vendor_status($this->input->post('user_id'), $data) && $this->verification_model->update_address_status($this->input->post('address_id'), $data_address) && $this->verification_model->update_account_status($this->input->post('bank_id'), $data_bank) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_aadhar) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_pancard) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_police) && $this->verification_model->update_document_common_status($this->input->post('document_id'), $data_license);
        }
		
        if ($this->form_validation->run() == true && $check_verify){
			
            $this->session->set_flashdata('message', $this->input->post('first_name').' details has been verified');
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('active_driver')));
            $meta = array('page_title' => lang('active_driver'), 'bc' => $bc);
			
			/*user*/
			
			$this->data['driver_result'] = $this->verification_model->getUserDetails($id);
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
			
            $this->page_construct('people/status_driver', $meta, $this->data);
        }        
    }
	
		
	function driver_view($id){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->users_model->getUser($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
        $this->page_construct('people/driver_view', $meta, $this->data);
    }
	
	function driver_edit($user_id){
		$group_id = $this->Driver;
		
		$q = $this->db->select("u.id as id, up.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id)
			->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your account has been deactive. so if can not edit."));
            	admin_redirect('people/driver');
			}
		}
		
		$result = $this->users_model->getUserEdit($user_id);
		
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
				$active = 1;
				$is_approved = 1;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
			}else{
				$is_edit = 0;
				$active = 0;
				$is_approved = 0;
				$approved_on = '0000-00-00';
				$approved_by = 0;
			}
		   $user = array(
				'email' => $this->input->post('email'),
				'country_code' => $this->input->post('country_code'),
				'mobile' => $this->input->post('mobile'),
				'mobile_otp' => $mobile_otp,
				'active' => $active,
				'is_approved' => $is_approved,
				'approved_on' => $approved_on,
				'approved_by' => $approved_by,
				'is_edit' => $is_edit
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
				$config = NULL;
			}else{
				$user_profile['photo'] = $result->photo;
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
				'local_continent_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_continent_id') : 0,
				'local_country_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_country_id') : 0,
				'local_zone_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_zone_id') : 0,
				'local_state_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_state_id') : 0,
				'local_city_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_city_id') : 0,
				'local_area_id' => $_FILES['local_image']['size'] == 0 ? $this->input->post('local_area_id') : 0,
				
				'local_verify' => $local_verify,
				'local_approved_by' => $local_approved_by,
				'local_approved_on' => $local_approved_on,
				
				'permanent_address' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_address') : '',
				'permanent_continent_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_continent_id') : 0,
				'permanent_country_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_country_id') : 0,
				'permanent_zone_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_zone_id') : 0,
				'permanent_state_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_state_id') : 0,
				'permanent_city_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_city_id') : 0,
				'permanent_area_id' => $_FILES['permanent_image']['size'] == 0 ? $this->input->post('permanent_area_id') : 0,
				
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
			
			if($this->input->post('account_no') == $result->account_no && $this->input->post('bank_name') == $result->bank_name && $this->input->post('branch_name') == $result->branch_name && $this->input->post('ifsc_code') == $result->ifsc_code){
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
			
			if($this->input->post('license_dob') == $result->license_dob && $this->input->post('license_ward_name') == $result->license_ward_name && $this->input->post('license_type') == $result->license_type && $this->input->post('license_issuing_authority') == $result->license_issuing_authority && $this->input->post('license_issued_on') == $result->license_issued_on && $this->input->post('license_validity') == $result->license_validity && $_FILES['license_image']['size'] == 0){
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
				
				
				'license_dob' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_dob') : '0000-00-00',
				'license_ward_name' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_ward_name') : '',
				'license_type' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_type') : '',
				'license_issuing_authority' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_issuing_authority') : '',
				'license_issued_on' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_issued_on') : '0000-00-00',
				'license_validity' => $_FILES['pancard_image']['size'] == 0 ? $this->input->post('license_validity') : '0000-00-00',
				
				'license_verify' => $license_verify,
				'license_approved_by' => $license_approved_by,
				'license_approved_on' => $license_approved_on,
				
				'police_on' => $_FILES['police_image']['size'] == 0 ? $this->input->post('police_on') : '0000-00-00',
				'police_til' => $_FILES['police_image']['size'] == 0 ? $this->input->post('police_til') : '0000-00-00',	
				
				'police_verify' => $police_verify,
				'police_approved_by' => $police_approved_by,
				'police_approved_on' => $police_approved_on,	
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
		
        if ($this->form_validation->run() == true && $this->users_model->edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document)){
			
            $this->session->set_flashdata('message', lang("driver_updated"));
            admin_redirect('people/driver');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('people/driver'), 'page' => lang('driver')), array('link' => '#', 'page' => lang('edit_driver')));
            $meta = array('page_title' => lang('edit_driver'), 'bc' => $bc);
			
			
			$this->data['result'] = $result;
			
			$this->data['country_code'] = $this->masters_model->getALLCountry();
			$this->data['vendors'] = $this->site->getAllVendor();
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
			
            $this->page_construct('people/edit_driver', $meta, $this->data);
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
		$group_id = $this->Admin;
        $continent_id = $this->input->post('continent_id');
		$location_id = $this->input->post('continent_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'continents'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
        $country_id = $this->input->post('country_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('country_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'countries'){
			
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
        $zone_id = $this->input->post('zone_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('zone_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'zones'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
        $state_id = $this->input->post('state_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('state_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'states'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
        $city_id = $this->input->post('city_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('city_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'cities'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
        $area_id = $this->input->post('area_id');
		$group_id = $this->Employee;
		$location_id = $this->input->post('area_id');
		$designation_id = $this->input->post('designation_id');
		$department_id = $this->input->post('department_id');
		
		$options['rep'] = array();
		$options['loc'] = array();
		if($designation_id == 'areas'){
			$checkRole = $this->people_model->getRole_byuser($designation_id, $department_id, $group_id, $location_id);
			
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
	function status($status,$id){
        $data['is_verify'] = 0;
        if($status=='active'){
            $data['is_verify'] = 1;
        }
		
        $this->people_model->update_status($data,$id);
		redirect($_SERVER["HTTP_REFERER"]);
    }
}