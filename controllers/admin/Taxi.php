<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Taxi extends MY_Controller
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
       	//$this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		
		//$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		//$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		
		//$this->pdf_types = 'pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('taxi_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('verification_model');
    }
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
		
	/*###### Taxi*/
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
        $meta = array('page_title' => lang('cab'), 'bc' => $bc);
        $this->page_construct('taxi/index', $meta, $this->data);
    }
    function getTaxi(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		//echo 'fghgf';echo $countryCode;
		$driver_id = $_GET['driver'];
		$vendor_id = $_GET['vendor'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, up.first_name, {$this->db->dbprefix('taxi')}.name as taxi_name, {$this->db->dbprefix('taxi')}.number, {$this->db->dbprefix('taxi')}.model,   tf.name as fuel_type, tt.name as type_name,   If({$this->db->dbprefix('taxi')}.is_verify = 1 && td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1, '1', '0') as status, country.name as instance_country ")
            ->from("taxi")
			->join("countries country", " country.iso = taxi.is_country", "left")
			->join("taxi_type tt", 'tt.id = taxi.type ', 'left')
			->join("taxi_fuel tf", 'tf.id = taxi.fuel_type ', 'left')
			->join("users up", "up.id = taxi.driver_id AND up.is_edit = 1 ", 'left')
			->join("taxi_document td", "td.taxi_id = taxi.id AND td.is_edit = 1 ", 'left')
			->where('taxi.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi.is_country", $countryCode);
			}
			
            $this->datatables->where("taxi.is_edit", 1);
			if($driver_id != 0){
				$this->datatables->where('driver_id', $driver_id);
			}
			if($vendor_id != 0){
				$this->datatables->where('vendor_id', $vendor_id);
			}
			$this->datatables->group_by("taxi.id");
			 //$this->datatables->edit_column('is_verify', '$1__$2', 'id, is_verify')
             $this->datatables->edit_column('status', '$1___$2', 'id, status');
			//$this->datatables->edit_column('is_verify', '<a href="profiles/edit/$1">$2</a>', 'id, is_verify');
			//$this->datatables->edit_column('status', '<a href="profiles/edit/$1">$2</a>', 'id, status');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('taxi/edit_taxi/$1') . "' class='tip' title='" . lang("edit_taxi") . "'>edit</a> | <a href='" . admin_url('taxi/view_taxi/$1') . "' class='tip' title='" . lang("view_taxi") . "'>view</a></div>", "id");
			$edit = "<a href='" . admin_url('taxi/edit_taxi/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$driver = "<a href='" . admin_url('people/driver/?cab=$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to driver'  ><i class='fa fa-users' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$vendor = "<a href='" . admin_url('people/vendor/?cab=$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to vendor'  ><i class='fa fa-users' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/taxi/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Delete'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$driver."</div><div>".$delete."</div>", "id");
			 $this->datatables->unset_column('id');
			 
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
    }
	
	public function esNulo($value){
     	$result= is_null($value) ?  "Yes" :  "NO";
     	echo $result;
    }
	
	function add_taxi(){
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
		$this->form_validation->set_rules('taxi_name', lang("taxi_name"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			$vendor = $this->site->getVendorIDBY($this->input->post('vendor_id'));	
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
			}
		   $taxi = array(
				'name' => $this->input->post('taxi_name'),
				'model' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'multiple_type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				//'ac' => $this->input->post('ac'),
				'created_by' => $this->session->userdata('user_id'),
				'vendor_id' => $vendor->id ? $vendor->id : $this->session->userdata('user_id'),
				//'group_id' => $vendor->group_id,
				//'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental') ? $this->input->post('is_rental') : 0,
				'is_outstation' => $this->input->post('is_outstation') ? $this->input->post('is_outstation') : 0,
				'is_verify' => 1,
				'is_edit' => 1,
				'complete_taxi' => 1,
				'created_on' => date('y-m-d H:i:s'),
		   );
		   
		   
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$photo;
				$config = NULL;
			}
			
			$taxi_document = array(
				'user_id' => $vendor->id ? $vendor->id : $this->session->userdata('user_id'),
				
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$puc_image = $this->upload->file_name;
				$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			
			//$this->sma->print_arrays($taxi, $taxi_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->taxi_model->add_taxi($taxi, $taxi_document, $countryCode)){
			
			$sms_message = $this->input->post('first_name').' your account and taxi has been added successfully.';
			$sms_phone = $this->input->post('country_code').$this->input->post('mobile');
			$sms_country_code = $this->input->post('country_code');

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("taxi_added"));
            admin_redirect('taxi');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('taxi'), 'page' => lang('cab')), array('link' => '#', 'page' => lang('add_taxi')));
            $meta = array('page_title' => lang('add_taxi'), 'bc' => $bc);
			
			$this->data['vendors'] = $this->site->getAllVendor($countryCode);
			
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel($countryCode);
			
            $this->page_construct('taxi/add_taxi', $meta, $this->data);
        }        
    }
	
	function edit_taxi($id, $view){
			$result = $this->taxi_model->getTaxiDataedit($id, $countryCode);
			
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
		
		
		$q = $this->db->select("t.id as id, t.name as taxi_name, t.number, t.model, t.engine_number, t.chassis_number,  t.fuel_type, tt.name as type_name,   If(t.is_verify = 1 && td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1 && t.is_verify = 1, '1', '0') as status ")
            ->from("taxi t")
			->join("taxi_type tt", "tt.id = t.type", "left")
			->join("taxi_document td", "td.taxi_id = t.id AND td.is_edit = 1", "left")
			->where("t.id", $id)
			->where("t.is_edit", 1)
			->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				$this->session->set_flashdata('error', lang("your taxi has been deactive. so if can not edit."));
            	admin_redirect('taxi');
			}
		}
		
	
		
		if($result->driver_id != 0 && $result->vendor_id == 0){
			$user_value = $this->taxi_model->getUservalues($result->driver_id, $countryCode);
		}elseif($result->driver_id == 0 && $result->vendor_id != 0){
			$user_value = $this->taxi_model->getUservalues($result->vendor_id, $countryCode);
		}else{
			$user_value = $this->taxi_model->getUservalues($this->session->userdata('user_id'), $countryCode);
		}
		
		$this->form_validation->set_rules('taxi_name', lang("taxi_name"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			//$vendor = $this->site->getVendorIDBY($result->vendor_id);		
			  
		   
		   if($this->input->post('taxi_name') == $result->name && $this->input->post('model') == $result->model && $this->input->post('number') == $result->number && $this->input->post('type') == $result->type && $this->input->post('engine_number') == $result->engine_number && $this->input->post('chassis_number') == $result->chassis_number && $this->input->post('make') == $result->make && $this->input->post('fuel_type') == $result->fuel_type && $this->input->post('color') == $result->color && $this->input->post('manufacture_year') == $result->manufacture_year && $this->input->post('capacity') == $result->capacity  && $_FILES['photo']['size'] == 0){
				$is_verify = $result->is_verify;
				$approved_by = $result->approved_by;
				$approved_on = $result->approved_on;
				$created_on = $result->created_on;
				$created_by = $result->created_by;
			}else{
				$is_verify = 0;
				$approved_by = 0;
				$approved_on = '0000:00:00 00:00:00';
				$created_on = date('y-m-d H:i:s');
				$created_by = $this->session->userdata('user_id');
			}
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
			}
			
			if($result->vendor_id == NULL || $result->vendor_id == 0){
				$vendor_id = 0;
			}else{
				$vendor_id = $result->vendor_id;
			}
			
			if($result->driver_id == NULL || $result->driver_id == 0){
				$driver_id = 0;
			}else{
				$driver_id = $result->driver_id;
			}
			
			$make_name = $this->taxi_model->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->taxi_model->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->taxi_model->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			$taxi = array(
				'name' => $this->input->post('taxi_name'),
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				//'ac' => $this->input->post('ac'),
				'created_by' => $created_by,
				'vendor_id' => $vendor_id,
				'driver_id' => $driver_id,
				//'group_id' => $vendor->group_id,
				'created_on' => $created_on,
				//'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental') ? $this->input->post('is_rental') : 0,
				'is_outstation' => $this->input->post('is_outstation') ? $this->input->post('is_outstation') : 0,
				'complete_taxi' => 1,
				'is_verify' => $is_verify,
				'approved_by' => $approved_by,
				'approved_on' => $approved_on,
				'is_edit' => 1
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$photo;
				$config = NULL;
			}else{
				$taxi['photo'] = $result->photo;
			}
			
			if($this->input->post('reg_date') ==$result->reg_date && $this->input->post('reg_due_date') ==$result->reg_due_date && $this->input->post('reg_owner_name') == $result->reg_owner_name && $this->input->post('reg_owner_address') == $result->reg_owner_address && $_FILES['reg_image']['size'] == 0){
				$reg_verify = $result->reg_verify;
				$reg_approved_by = $result->reg_approved_by;
				$reg_approved_on = $result->reg_approved_on;
			}else{
				$reg_verify = 0;
				$reg_approved_by = 0;
				$reg_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('taxation_amount_paid') == $result->taxation_amount_paid && $this->input->post('taxation_due_date') ==$result->taxation_due_date && $_FILES['taxation_image']['size'] == 0){
				$taxation_verify = $result->taxation_verify;
				$taxation_approved_by = $result->taxation_approved_by;
				$taxation_approved_on = $result->taxation_approved_on;
			}else{
				$taxation_verify = 0;
				$taxation_approved_by = 0;
				$taxation_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('insurance_policy_no') == $result->insurance_policy_no && $this->input->post('insurance_due_date') ==$result->insurance_due_date && $_FILES['insurance_image']['size'] == 0){
				$insurance_verify = $result->insurance_verify;
				$insurance_approved_by = $result->insurance_approved_by;
				$insurance_approved_on = $result->insurance_approved_on;
			}else{
				$insurance_verify = 0;
				$insurance_approved_by = 0;
				$insurance_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permit_no') == $result->permit_no && $this->input->post('permit_due_date') ==$result->permit_due_date && $_FILES['permit_image']['size'] == 0){
				$permit_verify = $result->permit_verify;
				$permit_approved_by = $result->permit_approved_by;
				$permit_approved_on = $result->permit_approved_on;
			}else{
				$permit_verify = 0;
				$permit_approved_by = 0;
				$permit_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('authorisation_no') == $result->authorisation_no && $this->input->post('authorisation_due_date') ==$result->authorisation_due_date && $_FILES['authorisation_image']['size'] == 0){
				$authorisation_verify = $result->authorisation_verify;
				$authorisation_approved_by = $result->authorisation_approved_by;
				$authorisation_approved_on = $result->authorisation_approved_on;
			}else{
				$authorisation_verify = 0;
				$authorisation_approved_by = 0;
				$authorisation_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('fitness_due_date') ==$result->fitness_due_date && $_FILES['fitness_image']['size'] == 0){
				$fitness_verify = $result->fitness_verify;
				$fitness_approved_by = $result->fitness_approved_by;
				$fitness_approved_on = $result->fitness_approved_on;
			}else{
				$fitness_verify = 0;
				$fitness_approved_by = 0;
				$fitness_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('speed_due_date') ==$result->speed_due_date && $_FILES['speed_image']['size'] == 0){
				$speed_verify = $result->speed_verify;
				$speed_approved_by = $result->speed_approved_by;
				$speed_approved_on = $result->speed_approved_on;
			}else{
				$speed_verify = 0;
				$speed_approved_by = 0;
				$speed_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('puc_due_date') ==$result->puc_due_date && $_FILES['puc_image']['size'] == 0){
				$puc_verify = $result->puc_verify;
				$puc_approved_by = $result->puc_approved_by;
				$puc_approved_on = $result->puc_approved_on;
			}else{
				$puc_verify = 0;
				$puc_approved_by = 0;
				$puc_approved_on = '0000:00:00 00:00:00';
			}
			
			
			$taxi_document = array(
				
				
		   		'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				
				
				'puc_verify' => $puc_verify,
				'puc_approved_by' => $puc_approved_by,
				'puc_approved_on' => $puc_approved_on,
				
				'speed_verify' => $speed_verify,
				'speed_approved_by' => $speed_approved_by,
				'speed_approved_on' => $speed_approved_on,
				
				'fitness_verify' => $fitness_verify,
				'fitness_approved_by' => $fitness_approved_by,
				'fitness_approved_on' => $fitness_approved_on,
				
				'authorisation_verify' => $authorisation_verify,
				'authorisation_approved_by' => $authorisation_approved_by,
				'authorisation_approved_on' => $authorisation_approved_on,
				
				'permit_verify' => $permit_verify,
				'permit_approved_by' => $permit_approved_by,
				'permit_approved_on' => $permit_approved_on,
				
				'insurance_verify' => $insurance_verify,
				'insurance_approved_by' => $insurance_approved_by,
				'insurance_approved_on' => $insurance_approved_on,
				
				'reg_verify' => $reg_verify,
				'reg_approved_by' => $reg_approved_by,
				'reg_approved_on' => $reg_approved_on,
				
				'taxation_verify' => $taxation_verify,
				'taxation_approved_by' => $taxation_approved_by,
				'taxation_approved_on' => $taxation_approved_on,
				
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$reg_image = $this->upload->file_name;
				$taxi_document['reg_image'] = 'document/register/'.$reg_image;
				$config = NULL;
			}else{
				$taxi_document['reg_image'] = $result->reg_image;
			}
			
			
			if ($_FILES['taxation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxation/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxation_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$taxation_image = $this->upload->file_name;
				$taxi_document['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			}else{
				$taxi_document['taxation_image'] = $result->taxation_image;	
			}
			
			if ($_FILES['insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/insurance/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('insurance_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$insurance_image = $this->upload->file_name;
				$taxi_document['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}else{
				$taxi_document['insurance_image'] = $result->insurance_image;	
			}
			
			if ($_FILES['permit_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permit/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('permit_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permit_image = $this->upload->file_name;
				$taxi_document['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}else{
				$taxi_document['permit_image'] = $result->permit_image;	
			}
			
			if ($_FILES['authorisation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/authorisation/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('authorisation_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$authorisation_image = $this->upload->file_name;
				$taxi_document['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}else{
				$taxi_document['authorisation_image'] = $result->authorisation_image;	
			}
			
			if ($_FILES['fitness_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fitness/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('fitness_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$fitness_image = $this->upload->file_name;
				$taxi_document['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}else{
				$taxi_document['fitness_image'] = $result->fitness_image;	
			}
			
			if ($_FILES['speed_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/speed_limit/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('speed_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$speed_image = $this->upload->file_name;
				$taxi_document['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}else{
				$taxi_document['speed_image'] = $result->speed_image;	
			}
			
			if ($_FILES['puc_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/puc/';
				$config['allowed_types'] = $this->pdf_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('puc_image')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$puc_image = $this->upload->file_name;
				$taxi_document['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}else{
				$taxi_document['puc_image'] = $result->puc_image;	
			}
			
			//$this->sma->print_arrays($taxi, $taxi_document);
			//die;
        }
		
		//$this->taxi_model->edit_taxi($taxi, $taxi_document, $id);die;
        if ($this->form_validation->run() == true && $this->taxi_model->edit_taxi($taxi, $taxi_document, $id, $countryCode)){
			
			
			$sms_message = $user_value->first_name.' your taxi has been edited successfully. Waiting for admin approval process';
			$sms_phone = $user_value->country_code.$user_value->mobile;
			$sms_country_code = $user_value->country_code;

			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("taxi_edited"));
            admin_redirect('taxi');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('taxi'), 'page' => lang('cab')), array('link' => '#', 'page' => lang('edit_taxi')));
            $meta = array('page_title' => lang('add_taxi'), 'bc' => $bc);
			
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			$this->data['models'] = $this->taxi_model->getModelbymake_type($result->make_id, $result->type, $countryCode);	
			
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel($countryCode);
			
			
			$this->data['result'] = $result;
			$this->data['id'] = $id;	
			
            $this->page_construct('taxi/edit_taxi', $meta, $this->data);
        }        
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
		
        $data = $this->taxi_model->getModelbymake_type($make_id, $type_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	function view_taxi($id){
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
		$this->data['taxi'] = $this->taxi_model->getTaxiData($id, $countryCode);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
        $meta = array('page_title' => lang('cab'), 'bc' => $bc);
        $this->page_construct('taxi/view_taxi', $meta, $this->data);
    }
	
	function taxi_status($status, $id){
		$result = $this->taxi_model->getTaxiDataedit($id, $countryCode);
		
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
		if($status == 'active'){
			$this->session->set_flashdata('message', 'Your taxi is active!');
            admin_redirect('taxi');
		}
		
		
		
		
		if($result->driver_id != 0 && $result->vendor_id == 0){
			$user_value = $this->taxi_model->getUservalues($result->driver_id, $countryCode);
		}elseif($result->driver_id == 0 && $result->vendor_id != 0){
			$user_value = $this->taxi_model->getUservalues($result->vendor_id, $countryCode);
		}else{
			$user_value = $this->taxi_model->getUservalues($this->session->userdata('user_id'), $countryCode);
		}
		
		$this->form_validation->set_rules('taxi_name', $this->lang->line("taxi_name"), 'required');
		
		
		if ($this->form_validation->run() == true) {
			
			$make_name = $this->taxi_model->getTaxinameBYID($this->input->post('make'), $countryCode);
			$model_name = $this->taxi_model->getTaximodelBYID($this->input->post('model'), $countryCode);
			$type_name = $this->taxi_model->getTaxitypeBYID($this->input->post('type'), $countryCode);
			
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
			}
			
			$data = array(
				'name' => $this->input->post('taxi_name'),
				'make' => $make_name,
				'make_id' => $this->input->post('make'),
				'model' => $model_name,
				'model_id' => $this->input->post('model'),
				'number' => $this->input->post('number'),
				'type' => $this->input->post('type'),
				'type_name' => $type_name,
				'multiple_type' => $this->input->post('type'),
				'engine_number' => $this->input->post('engine_number'),
				'chassis_number' => $this->input->post('chassis_number'),
				'make' => $this->input->post('make'),
				'fuel_type' => $this->input->post('fuel_type'),
				'color' => $this->input->post('color'),
				'manufacture_year' => $this->input->post('manufacture_year'),
				'capacity' => $this->input->post('capacity'),
				'is_verify' => $this->input->post('is_approved'),
				'approved_by' => $this->session->userdata('user_id'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental') ? $this->input->post('is_rental') : 0,
				'is_outstation' => $this->input->post('is_outstation') ? $this->input->post('is_outstation') : 0,
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			if ($_FILES['taxi_photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('taxi_photo')) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$taxi_photo = $this->upload->file_name;
				$data['photo'] = 'document/taxi/'.$taxi_photo;
				$config = NULL;
			}
			
			
			
			$data_doc = array(
				'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'reg_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
			
			$data_taxation = array(
				'taxation_amount_paid' => $this->input->post('taxation_amount_paid'),
				'taxation_due_date' => $this->input->post('taxation_due_date'),
				'taxation_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$taxation_image = $this->upload->file_name;
				$data_taxation['taxation_image'] = 'document/taxation/'.$taxation_image;
				$config = NULL;
			
			}
			
			
			
			$data_insurance = array(
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'insurance_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$insurance_image = $this->upload->file_name;
				$data_insurance['insurance_image'] = 'document/insurance/'.$insurance_image;
				$config = NULL;
			}
			
			$data_permit = array(
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'permit_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$permit_image = $this->upload->file_name;
				$data_permit['permit_image'] = 'document/permit/'.$permit_image;
				$config = NULL;
			}
			
			$data_authorisation = array(
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'authorisation_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$authorisation_image = $this->upload->file_name;
				$data_authorisation['authorisation_image'] = 'document/authorisation/'.$authorisation_image;
				$config = NULL;
			}
			
			$data_fitness = array(
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'fitness_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$fitness_image = $this->upload->file_name;
				$data_fitness['fitness_image'] = 'document/fitness/'.$fitness_image;
				$config = NULL;
			}
			
			$data_speed = array(
				'speed_due_date' => $this->input->post('speed_due_date'),
				'speed_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$speed_image = $this->upload->file_name;
				$data_speed['speed_image'] = 'document/speed_limit/'.$speed_image;
				$config = NULL;
			}
			
			
			$data_puc = array(
				'puc_due_date' => $this->input->post('puc_due_date'),
				
				'puc_verify' => $this->input->post('is_approved'),
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
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$puc_image = $this->upload->file_name;
				$data_puc['puc_image'] = 'document/puc/'.$puc_image;
				$config = NULL;
			}
			
			
			$check_taxi = $this->verification_model->update_taxi_status($this->input->post('taxi_id'), $data, $status, $countryCode) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_doc, $countryCode) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_taxation, $countryCode)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_insurance, $countryCode)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_permit, $countryCode)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_authorisation, $countryCode)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_fitness, $countryCode)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_speed, $countryCode) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_puc, $countryCode);
			
			
		
		}
		if ($this->form_validation->run() && $check_taxi)
		{
			
			
			
			$q = $this->db->select("t.id as id, t.name as taxi_name, t.number, t.model, t.engine_number, t.chassis_number,  t.fuel_type, tt.name as type_name,   If(t.is_verify = 1 && td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1 , '1', '0') as status ")
            ->from("taxi t")
			->join("taxi_type tt", "tt.id = t.type ", "left")
			->join("taxi_document td", "td.taxi_id = t.id AND td.is_edit = 1 ", "left")
			->where("t.id", $id)
			->where('t.is_country', $countryCode)
			->where("t.is_edit", 1)
			->get();
		
			if($q->num_rows()>0){
				if($q->row('status') == 0){
					$this->session->set_flashdata('error', $this->input->post('first_name_hidden').' details has been not verified. ');
					admin_redirect('taxi');
				}else{
					
					$sms_message = $user_value->first_name.' your account and taxi has been verified successfully.';
					$sms_phone = $user_value->country_code.$user_value->mobile;
					$sms_country_code = $user_value->country_code;
		
					$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
					
					$notification['title'] = 'Taxi Verified Status';
				$notification['message'] =  $user_value->first_name.' your account and  taxi has been verified successfully. ';
				$notification['user_type'] = 0;
				$notification['user_id'] = $id;
				$this->site->insertNotification($notification);
				
					
					$this->session->set_flashdata('message', $this->input->post('first_name_hidden').' details has been verified');
            		admin_redirect('taxi');
				}
			}
			
		}else{
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_status')));
			$meta = array('page_title' => lang('cab_status'), 'bc' => $bc);
			
			$result = $this->verification_model->getTaxi($id, $countryCode);
			$result_doc = $this->verification_model->getTaxiDocument($id, $countryCode);
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel($countryCode);
			
			$this->data['result_doc'] = $result_doc;
			$this->data['result'] = $result;
			$this->data['id'] = $id;	
			$this->data['status'] = $status;
			
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);	
			//echo $result->make_id;
			//echo $result->type;
			$this->data['models'] = $this->taxi_model->getModelbymake_type($result->make_id, $result->type, $countryCode);
			
			$this->page_construct('taxi/taxi_status', $meta, $this->data);
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
        $data['is_verify'] = 0;
        if($status=='active'){
            $data['is_verify'] = 1;
        }
		
        $this->taxi_model->update_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
   
   function taxi_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('owner_name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('cab_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('vehicle number'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('model'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('fuel_type'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('cab_type'));
                   
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
					$res = $this->taxi_model->getALLTaxi($group_id, $countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->first_name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->taxi_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->number);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->model);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->fuel_type);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->type_name);
                       
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
                    $filename = 'cab_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
   
	
}
