<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Drivers extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        //$this->lang->admin_load('drivers', $this->Settings->user_language);
        $this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/drivers/';
		$this->upload_taxi = 'assets/uploads/taxi/';
        $this->thumbs_path = 'assets/uploads/drivers/thumbs/';
        $this->image_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->load->admin_model('drivers_model');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('drivers')));
        $meta = array('page_title' => lang('drivers'), 'bc' => $bc);
        $this->page_construct('drivers/index', $meta, $this->data);
    }
    function getDrivers(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, first_name name,email,mobile,gender,status")
            ->from("drivers")
             ->edit_column('status', '$1__$2', 'status, id')
            /*->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('drivers/update_kyc_documents/$1') . "' class='tip' title='" . lang("update_kyc_documents") . "'>" . lang("update_docs") . "</a>&nbsp;&nbsp;&nbsp;<a href='" . admin_url('drivers/profile/$1') . "' class='tip' title='" . lang("edit_user") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");*/
			->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('drivers/profile/$1') . "' class='tip' title='" . lang("edit_user") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add(){
       //$this->form_validation->set_rules('username', $this->lang->line("username"), 'required|is_unique[drivers.username]');
		$this->form_validation->set_rules('password', $this->lang->line("password"), 'required');
		$this->form_validation->set_rules('email', $this->lang->line("email"), 'required|is_unique[drivers.email]');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		
		$this->form_validation->set_rules('gender', $this->lang->line("gender"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		$this->form_validation->set_rules('mobile', $this->lang->line("mobile"), 'required|is_unique[drivers.mobile]');
		
		
		$this->form_validation->set_rules('license_number', $this->lang->line("license_number"), 'required');
		//$this->form_validation->set_rules('license_valid_from', $this->lang->line("license_valid_from"), 'required');
		//$this->form_validation->set_rules('license_expiry', $this->lang->line("license_expiry"), 'required');
		
		$this->form_validation->set_rules('taxi_name', $this->lang->line("taxi_name"), 'required');
		$this->form_validation->set_rules('taxi_model', $this->lang->line("taxi_model"), 'required');
		$this->form_validation->set_rules('taxi_number', $this->lang->line("taxi_number"), 'required');
		$this->form_validation->set_rules('taxi_type', $this->lang->line("taxi_type"), 'required');
		$this->form_validation->set_rules('taxi_color', $this->lang->line("taxi_color"), 'required');
		$this->form_validation->set_rules('manufacture_year', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('capacity', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('ac', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('license_front', $this->lang->line("license_front"), 'required');
		//$this->form_validation->set_rules('license_back', $this->lang->line("license_back"), 'required');
		
        
        if ($this->form_validation->run() == true) {
            
            $driver['email'] = $this->input->post('email');
				$driver['first_name'] = $this->input->post('first_name');
				$driver['last_name'] = $this->input->post('last_name');
				$driver['gender'] = $this->input->post('gender');
				$driver['mobile'] = $this->input->post('mobile');
				$driver['country_code'] = $this->input->post('country_code');
				$driver['license_number'] = $this->input->post('license_number');
				$driver['address'] = $this->input->post('address');
				$driver['country'] = $this->input->post('country_id');
				$driver['state'] = $this->input->post('state_id');
				$driver['city'] = $this->input->post('city');
				$driver['zipcode'] = $this->input->post('zipcode');
				$driver['status'] = 1;
				
				$driver['password'] = md5($this->input->post('password'));
				$driver['text_password'] = $this->input->post('password');
				$driver['devices_imei'] = 'first_time';
				$driver['created_on'] = date('y-m-d H:i:s');
				
				$token = get_random_key(32,'customers','oauth_token',$type='alnum');
				$driver['oauth_token'] = $token;
				$driver['mobile_otp'] = random_string('numeric', 6);
				
				$taxi['name'] = $this->input->post('taxi_name');
				$taxi['model'] = $this->input->post('taxi_model');
				$taxi['number'] = $this->input->post('taxi_number');
				$taxi['type'] = $this->input->post('taxi_type');
				$taxi['color'] = $this->input->post('taxi_color');
				$taxi['manufacture_year'] = $this->input->post('manufacture_year');
				$taxi['status'] = 1;
				
				
				if ($_FILES['photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'photo/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$photo = $this->upload->file_name;
					$driver['photo'] = 'photo/'.$photo;
					$config = NULL;
				}
				
				if ($_FILES['taxi_photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'taxi/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_photo')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_photo = $this->upload->file_name;
					$taxi['photo'] = 'taxi/'.$taxi_photo;
					$config = NULL;
				}
				
				if ($_FILES['taxi_insurance']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'insurance/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_insurance')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_insurance = $this->upload->file_name;
					$taxi['insurance'] = 'insurance/'.$taxi_insurance;
					$config = NULL;
				}
				
				if ($_FILES['taxi_taxpaid']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'taxpaid/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_taxpaid')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_taxpaid = $this->upload->file_name;
					$taxi['taxpaid'] = 'taxpaid/'.$taxi_taxpaid;
					$config = NULL;
				}
				
				if ($_FILES['taxi_rcbook']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'rcbook/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_rcbook')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_rcbook = $this->upload->file_name;
					$taxi['rcbook'] = 'rcbook/'.$taxi_rcbook;
					$config = NULL;
				}
				
				
				
				if ($_FILES['license']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'license/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('license')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$license = $this->upload->file_name;
					$driver['license'] = 'license/'.$license;
					$config = NULL;
				}
				
				if ($_FILES['aadhar']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'aadhar/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('aadhar')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$aadhar = $this->upload->file_name;
					$driver['aadhar'] = 'aadhar/'.$license_back;
					$config = NULL;
				}
				
				if ($_FILES['other']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'other/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('other')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$other = $this->upload->file_name;
					$driver['other'] = 'other/'.$license_back;
					$config = NULL;
				}
				
           
        }
		
		
        if ($this->form_validation->run() == true && $this->drivers_model->add_driver($driver, $taxi)){
			
            $this->session->set_flashdata('message', lang("Driver_added"));
            admin_redirect('drivers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('drivers'), 'page' => lang('drivers')), array('link' => '#', 'page' => lang('add_driver')));
            $meta = array('page_title' => lang('add_driver'), 'bc' => $bc);
	    	$this->data['countries'] = $this->site->getAllCountries();
	    	$this->data['types'] = $this->site->getAllTypes();
            $this->page_construct('drivers/add', $meta, $this->data);
        }
    }
    function profile($id){
		
	 	$driver = $this->drivers_model->getDriverswithTaxiedit($id);
		
        $this->form_validation->set_rules('email', lang("email_address"),'required|callback_my_is_unique[drivers.email.'.$id.']');
		$this->form_validation->set_rules('mobile', lang("mobile"), 'required|callback_my_is_unique[drivers.mobile.'.$id.']');   
           
        //$this->form_validation->set_rules('username', $this->lang->line("username"), 'required|is_unique[drivers.username]');
		$this->form_validation->set_rules('first_name', $this->lang->line("first_name"), 'required');
		
		$this->form_validation->set_rules('gender', $this->lang->line("gender"), 'required');
		$this->form_validation->set_rules('country_code', $this->lang->line("country_code"), 'required');
		
		
		
		$this->form_validation->set_rules('license_number', $this->lang->line("license_number"), 'required');
		//$this->form_validation->set_rules('license_valid_from', $this->lang->line("license_valid_from"), 'required');
		//$this->form_validation->set_rules('license_expiry', $this->lang->line("license_expiry"), 'required');
		
		$this->form_validation->set_rules('taxi_name', $this->lang->line("taxi_name"), 'required');
		$this->form_validation->set_rules('taxi_model', $this->lang->line("taxi_model"), 'required');
		$this->form_validation->set_rules('taxi_number', $this->lang->line("taxi_number"), 'required');
		$this->form_validation->set_rules('taxi_type', $this->lang->line("taxi_type"), 'required');
		$this->form_validation->set_rules('taxi_color', $this->lang->line("taxi_color"), 'required');
		$this->form_validation->set_rules('manufacture_year', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('capacity', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('ac', $this->lang->line("address"), 'required');
		//$this->form_validation->set_rules('license_front', $this->lang->line("license_front"), 'required');
		//$this->form_validation->set_rules('license_back', $this->lang->line("license_back"), 'required');
		
        if ($this->form_validation->run() == true) {
            
           		$dri['email'] = $this->input->post('email');
				$dri['first_name'] = $this->input->post('first_name');
				$dri['last_name'] = $this->input->post('last_name');
				$dri['gender'] = $this->input->post('gender');
				$dri['mobile'] = $this->input->post('mobile');
				$dri['country_code'] = $this->input->post('country_code');
				$dri['license_number'] = $this->input->post('license_number');
				$dri['address'] = $this->input->post('address');
				$dri['country'] = $this->input->post('country_id');
				$dri['state'] = $this->input->post('state_id');
				$dri['city'] = $this->input->post('city');
				$dri['zipcode'] = $this->input->post('zipcode');
				
				$dri['devices_imei'] = 'first_time';
				$dri['created_on'] = date('y-m-d H:i:s');
				
				
				$taxi['name'] = $this->input->post('taxi_name');
				$taxi['model'] = $this->input->post('taxi_model');
				$taxi['number'] = $this->input->post('taxi_number');
				$taxi['type'] = $this->input->post('taxi_type');
				$taxi['color'] = $this->input->post('taxi_color');
				$taxi['manufacture_year'] = $this->input->post('manufacture_year');
				
				
				if ($_FILES['photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'photo/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('photo')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$photo = $this->upload->file_name;
					$dri['photo'] = 'photo/'.$photo;
					$config = NULL;
				}
				
				if ($_FILES['taxi_photo']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'taxi/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_photo')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_photo = $this->upload->file_name;
					$taxi['photo'] = 'taxi/'.$taxi_photo;
					$config = NULL;
				}
				
				if ($_FILES['taxi_insurance']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'insurance/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_insurance')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_insurance = $this->upload->file_name;
					$taxi['insurance'] = 'insurance/'.$taxi_insurance;
					$config = NULL;
				}
				
				if ($_FILES['taxi_taxpaid']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'taxpaid/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_taxpaid')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_taxpaid = $this->upload->file_name;
					$taxi['taxpaid'] = 'taxpaid/'.$taxi_taxpaid;
					$config = NULL;
				}
				
				if ($_FILES['taxi_rcbook']['size'] > 0) {
					$config['upload_path'] = $this->upload_taxi.'rcbook/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('taxi_rcbook')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$taxi_rcbook = $this->upload->file_name;
					$taxi['rcbook'] = 'rcbook/'.$taxi_rcbook;
					$config = NULL;
				}
				
				
				
				if ($_FILES['license']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'license/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('license')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$license = $this->upload->file_name;
					$dri['license'] = 'license/'.$license;
					$config = NULL;
				}
				
				if ($_FILES['aadhar']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'aadhar/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('aadhar')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$aadhar = $this->upload->file_name;
					$dri['aadhar'] = 'aadhar/'.$license_back;
					$config = NULL;
				}
				
				if ($_FILES['other']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'other/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('other')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$other = $this->upload->file_name;
					$dri['other'] = 'other/'.$license_back;
					$config = NULL;
				}
				
				
				
        }
		
		
        if ($this->form_validation->run() == true && $this->drivers_model->update_driver($id,$dri, $taxi)){
			
            $this->session->set_flashdata('message', lang("Driver_updated"));
            admin_redirect('drivers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('drivers'), 'page' => lang('drivers')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('profile'), 'bc' => $bc);
            $this->data['driver'] = $driver;
	    
	    $this->data['countries'] = $this->site->getAllCountries();
	     $this->data['types'] = $this->site->getAllTypes();
	     $this->data['user_type'] = 'drivers';
            $this->page_construct('drivers/profile', $meta, $this->data);
        }
    }
    function driver_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->drivers_model->update_driver_status($data,$id);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	function approved_status($id){
       	
		
		$drivers = $this->drivers_model->getDriverswithTaxi($id);
		
		$this->form_validation->set_rules('status', lang("status"), 'required');
		if ($this->form_validation->run() == true){
			$row = array(
				'status' => 1,
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s')
			);
		}

		
		if ($this->form_validation->run() == true && $this->drivers_model->approved_driver($id,$row)){
			
			$notification['title'] = 'Admin Approved';
			$notification['message'] = 'Admin has been approved your account.';
			$notification['user_type'] = 1;
			$notification['user_id'] = $drivers->id;
			
			
			$this->drivers_model->insertNotification($notification);
			
			$driver_name = $drivers->first_name;
			$driver_phone = $drivers->mobile;
			$driver_pass = $drivers->text_password;
			$sms_phone = $drivers->mobile;
			$sms_country_code = $drivers->country_code;
			$sms_phone_otp = $drivers->mobile_otp;
			
			$this->sms_driver_approved($driver_name, $driver_phone, $driver_pass, $sms_phone, $sms_country_code);
			//$this->sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code);
			
			$this->session->set_flashdata('message', lang("Driver_approved"));
            admin_redirect('drivers');
			
		}else{
			$this->data['drivers'] = $drivers;
			$this->page_construct('drivers/approved', $meta, $this->data);	
		}
	
    }
	
	public function sms_user_active($sms_phone_otp, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[MOBILE_OTP]');
        $sms_rep_arr = array($sms_phone_otp);
        $response_sms = send_otp_sms($sms_template_slug = "user-mobile-active", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	public function sms_driver_approved($driver_name, $driver_phone, $driver_pass, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[DRIVERNAME]', '[DRIVERNUMBER]', '[DRIVERPASS]');
        $sms_rep_arr = array($driver_name, $driver_phone, $driver_pass);
        $response_sms = send_transaction_sms($sms_template_slug = "driver-approved", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
    function delete_driver(){
        
    }
    function update_kyc_documents($id){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('user_id', lang("user_id"), 'required');
        
        if ($this->form_validation->run() == true) {
            //echo '<pre>';print_R($_POST);exit;

	    $data = array();
	    foreach($_POST['document_type_id'] as $k => $row){
		$data[$k]['document_type_id'] = $row;
		$data[$k]['id'] = $_POST['document_id'][$k];
		$data[$k]['document_type_name'] = $_POST['document_type_name'][$k];
		$data[$k]['verification_status'] = $_POST['verification_status'][$k];
		$data[$k]['user_type'] = 'driver';
		$data[$k]['user_id'] = $_POST['user_id'];
		$data[$k]['fields'] = $_POST['doc_type_fields'][$row];
	    
		if ($_FILES['photo']['name'][$k] != "") {
		    $config['upload_path'] = $this->upload_path.'documents';
		    $config['allowed_types'] = $this->pdf_types;
		    //$config['max_size'] = $this->allowed_file_size;
		    //$config['max_width'] = $this->Settings->iwidth;
		    //$config['max_height'] = $this->Settings->iheight;
		    $config['overwrite'] = FALSE;
		    $config['max_filename'] = 25;
		    $config['encrypt_name'] = TRUE;
		    
		    $_FILES['document_photo']['name'] = $_FILES['photo']['name'][$k];
                    $_FILES['document_photo']['type'] = $_FILES['photo']['type'][$k];
                    $_FILES['document_photo']['tmp_name'] = $_FILES['photo']['tmp_name'][$k];
                    $_FILES['document_photo']['error'] = $_FILES['photo']['error'][$k];
                    $_FILES['document_photo']['size'] = $_FILES['photo']['size'][$k];
		    //print_R($_FILES['document_photo']);exit;
		    $this->upload->initialize($config);
		    if (!$this->upload->do_upload('document_photo')) {
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('error', $error);
			admin_redirect("drivers/update_kyc_documents/".$id);
		    }
		    $photo = $this->upload->file_name;
		    $data[$k]['document_photo'] = 'documents/'.$photo;
		    if($_POST['exist_photo'][$k]!=''){
			$this->site->unlink_images($_POST['exist_photo'][$k],$this->upload_path);
		    }
		    $config = NULL;
		}
	    }
        }
		
		
        if ($this->form_validation->run() == true && $this->site->add_kyc_documents($data)){
			
            $this->session->set_flashdata('message', lang("driver_added"));
            admin_redirect("drivers/update_kyc_documents/".$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('drivers'), 'page' => lang('drivers')), array('link' => '#', 'page' => lang('add_kyc_documents')));
            $meta = array('page_title' => lang('add_kyc_documents'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['doc_types'] = $this->site->getKycDouments_type($id,'driver');
	    $this->data['id'] = $id;
	    $this->data['user_type'] = 'drivers';
	    $this->data['action'] = "drivers/update_kyc_documents/".$id;
	    //$this->data['documents'] = $this->site->getKycDouments($id,'operator');
            $this->page_construct('kyc_documents/add_kyc_documents', $meta, $this->data);
        }
    }
    function my_is_unique($value,$id){
        $CI =& get_instance();	
	list($table,$field,$id) = explode('.',$id);
	if($CI->site->my_is_unique($id,$value,$field,$table)){
            $CI->form_validation->set_message('my_is_unique', lang($field." already exists"));
            return FALSE;
        }
        return true;
    }
}
