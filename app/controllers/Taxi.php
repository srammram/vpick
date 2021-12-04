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
		$this->lang->admin_load('people', $this->Settings->user_language);
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->image_types = 'gif|jpg|jpeg|png|tif';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('taxi_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('verification_model');
    }
	
		
	/*###### Taxi*/
    function index($action=false){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
        $meta = array('page_title' => lang('cab'), 'bc' => $bc);
        $this->page_construct('taxi/index', $meta, $this->data);
    }
    function getTaxi(){
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi')}.id as id, {$this->db->dbprefix('taxi')}.name as taxi_name, {$this->db->dbprefix('taxi')}.number, {$this->db->dbprefix('taxi')}.model, {$this->db->dbprefix('taxi')}.engine_number, {$this->db->dbprefix('taxi')}.chassis_number,  {$this->db->dbprefix('taxi')}.fuel_type, tt.name as type_name,  up.first_name, {$this->db->dbprefix('taxi')}.is_verify, If(td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1, '1', '0') as status ")
            ->from("taxi")
			->join("taxi_type tt", 'tt.id = taxi.type', 'left')
			->join("user_profile up", "up.user_id = taxi.user_id AND up.is_edit = 1", 'left')
			->join("taxi_document td", "td.taxi_id = taxi.id AND td.is_edit = 1", 'left')
			->where("taxi.is_edit", 1)
			
			 ->edit_column('is_verify', '$1__$2', 'is_verify, id')
            ->edit_column('status', '$1__$2', 'status, id')
			->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('taxi/edit_taxi/$1') . "' class='tip' title='" . lang("edit_taxi") . "'>edit</a> | <a href='" . admin_url('taxi/view_taxi/$1') . "' class='tip' title='" . lang("view_taxi") . "'>view</a></div>", "id");
			
        echo $this->datatables->generate();
		
    }
	
	
	
	function add_taxi(){
        $this->form_validation->set_rules('engine_number', lang("engine_number"), 'required|is_unique[taxi.engine_number]');  
		$this->form_validation->set_rules('number', lang("number"), 'required|is_unique[taxi.number]');  
		$this->form_validation->set_rules('chassis_number', lang("chassis_number"), 'required|is_unique[taxi.chassis_number]');  
        
		$this->form_validation->set_rules('name', lang("name"), 'required');
		$this->form_validation->set_rules('model', lang("model"), 'required');
		$this->form_validation->set_rules('type', lang("type"), 'required');
		$this->form_validation->set_rules('fuel_type', lang("fuel_type"), 'required');
		$this->form_validation->set_rules('color', lang("color"), 'required');
		$this->form_validation->set_rules('manufacture_year', lang("manufacture_year"), 'required');
		$this->form_validation->set_rules('capacity', lang("capacity"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			$vendor = $this->site->getVendorIDBY($this->input->post('vendor_id'));	
			if($this->input->post('is_daily') == 0 && $this->input->post('is_rental') == 0 && $this->input->post('is_outstation') == 0){
				$is_daily = 1;
			}else{
				$is_daily = $this->input->post('is_daily');
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
				'user_id' => $vendor->id,
				'group_id' => $vendor->group_id,
				'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				'is_verify' => 1,
				'created_on' => date('y-m-d H:i:s'),
		   );
		   
		   
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->image_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$photo;
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
				
		    );
			
			if ($_FILES['reg_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/register/';
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
				$config['allowed_types'] = $this->image_types;
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
			
			//$this->sma->print_arrays($taxi, $taxi_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->taxi_model->add_taxi($taxi, $taxi_document)){
			
            $this->session->set_flashdata('message', lang("taxi_added"));
            admin_redirect('taxi');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('taxi'), 'page' => lang('cab')), array('link' => '#', 'page' => lang('add_taxi')));
            $meta = array('page_title' => lang('add_taxi'), 'bc' => $bc);
			
			$this->data['vendors'] = $this->site->getAllVendor();
			
			$this->data['types'] = $this->masters_model->getALLTaxi_type();
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			
            $this->page_construct('taxi/add_taxi', $meta, $this->data);
        }        
    }
	
	function edit_taxi($id){
		
		$q = $this->db->select("t.id as id, t.name as taxi_name, t.number, t.model, t.engine_number, t.chassis_number,  t.fuel_type, tt.name as type_name,   If(td.reg_verify = 1 && td.taxation_verify = 1 && td.insurance_verify = 1 && td.permit_verify = 1 && td.authorisation_verify = 1 && td.fitness_verify = 1 && td.speed_verify = 1 && td.puc_verify = 1 && t.is_verify = 1, '1', '0') as status ")
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
		
		$result = $this->taxi_model->getTaxiDataedit($id);
		
		$this->form_validation->set_rules('engine_number', lang("engine_number"), 'required');  
		$this->form_validation->set_rules('number', lang("number"), 'required');  
		$this->form_validation->set_rules('chassis_number', lang("chassis_number"), 'required'); 
		
		if($result->engine_number != $this->input->post('engine_number')){
			$this->form_validation->set_rules('engine_number', lang("engine_number"), 'is_unique[taxi.engine_number]');  
		}
		if($result->number != $this->input->post('number')){
			$this->form_validation->set_rules('number', lang("number"), 'is_unique[taxi.number]');  
		}
		if($result->chassis_number != $this->input->post('chassis_number')){
			$this->form_validation->set_rules('chassis_number', lang("chassis_number"), 'is_unique[taxi.chassis_number]');  
		}
		
		$this->form_validation->set_rules('name', lang("name"), 'required');
		$this->form_validation->set_rules('model', lang("model"), 'required');
		$this->form_validation->set_rules('type', lang("type"), 'required');
		$this->form_validation->set_rules('fuel_type', lang("fuel_type"), 'required');
		$this->form_validation->set_rules('color', lang("color"), 'required');
		$this->form_validation->set_rules('manufacture_year', lang("manufacture_year"), 'required');
		$this->form_validation->set_rules('capacity', lang("capacity"), 'required');
		
		
        if ($this->form_validation->run() == true) {
			$vendor = $this->site->getVendorIDBY($this->input->post('vendor_id'));		
			   
		   
		   
		   if($this->input->post('name') == $result->name && $this->input->post('model') == $result->model && $this->input->post('number') == $result->number && $this->input->post('type') == $result->type && $this->input->post('engine_number') == $result->engine_number && $this->input->post('chassis_number') == $result->chassis_number && $this->input->post('make') == $result->make && $this->input->post('fuel_type') == $result->fuel_type && $this->input->post('color') == $result->color && $this->input->post('manufacture_year') == $result->manufacture_year && $this->input->post('capacity') == $result->capacity  && $_FILES['photo']['size'] == 0){
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
				'created_by' => $created_by,
				'user_id' => $vendor->id,
				'group_id' => $vendor->group_id,
				'created_on' => $created_on,
				'is_hiring' => $this->input->post('is_hiring'),
				'is_daily' => $is_daily,
				'is_rental' => $this->input->post('is_rental'),
				'is_outstation' => $this->input->post('is_outstation'),
				
				'is_edit' => 1
		   );
		   
		   if ($_FILES['photo']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi/';
				$config['allowed_types'] = $this->image_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('photo')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$photo = $this->upload->file_name;
				$taxi['photo'] = 'document/taxi/'.$photo;
				$config = NULL;
			}
			
			if($this->input->post('reg_date') == $result->reg_date && $this->input->post('reg_due_date') == $result->reg_due_date && $this->input->post('reg_owner_name') == $result->reg_owner_name && $this->input->post('reg_owner_address') == $result->reg_owner_address && $_FILES['reg_image']['size'] == 0){
				$reg_verify = $result->reg_verify;
				$reg_approved_by = $result->reg_approved_by;
				$reg_approved_on = $result->reg_approved_on;
			}else{
				$reg_verify = 0;
				$reg_approved_by = 0;
				$reg_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('taxation_amount_paid') == $result->taxation_amount_paid && $this->input->post('taxation_due_date') == $result->taxation_due_date && $_FILES['taxation_image']['size'] == 0){
				$taxation_verify = $result->taxation_verify;
				$taxation_approved_by = $result->taxation_approved_by;
				$taxation_approved_on = $result->taxation_approved_on;
			}else{
				$taxation_verify = 0;
				$taxation_approved_by = 0;
				$taxation_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('insurance_policy_no') == $result->insurance_policy_no && $this->input->post('insurance_due_date') == $result->insurance_due_date && $_FILES['insurance_image']['size'] == 0){
				$insurance_verify = $result->insurance_verify;
				$insurance_approved_by = $result->insurance_approved_by;
				$insurance_approved_on = $result->insurance_approved_on;
			}else{
				$insurance_verify = 0;
				$insurance_approved_by = 0;
				$insurance_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('permit_no') == $result->permit_no && $this->input->post('permit_due_date') == $result->permit_due_date && $_FILES['permit_image']['size'] == 0){
				$permit_verify = $result->permit_verify;
				$permit_approved_by = $result->permit_approved_by;
				$permit_approved_on = $result->permit_approved_on;
			}else{
				$permit_verify = 0;
				$permit_approved_by = 0;
				$permit_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('authorisation_no') == $result->authorisation_no && $this->input->post('authorisation_due_date') == $result->authorisation_due_date && $_FILES['authorisation_image']['size'] == 0){
				$authorisation_verify = $result->authorisation_verify;
				$authorisation_approved_by = $result->authorisation_approved_by;
				$authorisation_approved_on = $result->authorisation_approved_on;
			}else{
				$authorisation_verify = 0;
				$authorisation_approved_by = 0;
				$authorisation_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('fitness_due_date') == $result->fitness_due_date && $_FILES['fitness_image']['size'] == 0){
				$fitness_verify = $result->fitness_verify;
				$fitness_approved_by = $result->fitness_approved_by;
				$fitness_approved_on = $result->fitness_approved_on;
			}else{
				$fitness_verify = 0;
				$fitness_approved_by = 0;
				$fitness_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('speed_due_date') == $result->speed_due_date && $_FILES['speed_image']['size'] == 0){
				$speed_verify = $result->speed_verify;
				$speed_approved_by = $result->speed_approved_by;
				$speed_approved_on = $result->speed_approved_on;
			}else{
				$speed_verify = 0;
				$speed_approved_by = 0;
				$speed_approved_on = '0000:00:00 00:00:00';
			}
			
			if($this->input->post('puc_due_date') == $result->puc_due_date && $_FILES['puc_image']['size'] == 0){
				$puc_verify = $result->puc_verify;
				$puc_approved_by = $result->puc_approved_by;
				$puc_approved_on = $result->puc_approved_on;
			}else{
				$puc_verify = 0;
				$puc_approved_by = 0;
				$puc_approved_on = '0000:00:00 00:00:00';
			}
			
			
			$taxi_document = array(
				'user_id' => $vendor->id,
				'group_id' => $vendor->group_id,
		   		'reg_date' => $_FILES['reg_image']['size'] == 0 ? $this->input->post('reg_date') : '0000-00-00',
				'reg_due_date' => $_FILES['reg_image']['size'] == 0 ? $this->input->post('reg_due_date') : '0000-00-00',
				'reg_owner_name' => $_FILES['reg_image']['size'] == 0 ? $this->input->post('reg_owner_name') : '',
				'reg_owner_address' => $_FILES['reg_image']['size'] == 0 ? $this->input->post('reg_owner_address') : '',
				
				
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
				
				'taxation_amount_paid' => $_FILES['taxation_image']['size'] == 0 ? $this->input->post('taxation_amount_paid') : '',
				'taxation_due_date' => $_FILES['taxation_image']['size'] == 0 ? $this->input->post('taxation_due_date') : '0000-00-00',
				'insurance_policy_no' => $_FILES['insurance_image']['size'] == 0 ? $this->input->post('insurance_policy_no') : '',
				'insurance_due_date' => $_FILES['insurance_image']['size'] == 0 ? $this->input->post('insurance_due_date') : '0000-00-00',
				'permit_no' => $_FILES['permit_image']['size'] == 0 ? $this->input->post('permit_no') : '',
				'permit_due_date' => $_FILES['permit_image']['size'] == 0 ? $this->input->post('permit_due_date') : '0000-00-00',
				'authorisation_no' => $_FILES['authorisation_image']['size'] == 0 ? $this->input->post('taxation_amount_paid') : '',
				'authorisation_due_date' => $_FILES['authorisation_image']['size'] == 0 ? $this->input->post('authorisation_due_date') : '0000-00-00',
				'fitness_due_date' => $_FILES['fitness_image']['size'] == 0 ? $this->input->post('fitness_due_date') : '0000-00-00',
				'speed_due_date' => $_FILES['speed_image']['size'] == 0 ? $this->input->post('speed_due_date') : '0000-00-00',
				'puc_due_date' => $_FILES['puc_image']['size'] == 0 ? $this->input->post('puc_due_date') : '0000-00-00',
				'is_edit' => 1
				
		    );
			
			if ($_FILES['reg_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/register/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['reg_image'] = $result->reg_image;
			}
			
			if ($_FILES['taxation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxation/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['taxation_image'] = $result->taxation_image;	
			}
			
			if ($_FILES['insurance_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/insurance/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['insurance_image'] = $result->insurance_image;	
			}
			
			if ($_FILES['permit_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/permit/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['permit_image'] = $result->permit_image;	
			}
			
			if ($_FILES['authorisation_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/authorisation/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['authorisation_image'] = $result->authorisation_image;	
			}
			
			if ($_FILES['fitness_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/fitness/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['fitness_image'] = $result->fitness_image;	
			}
			
			if ($_FILES['speed_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/speed_limit/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['speed_image'] = $result->speed_image;	
			}
			
			if ($_FILES['puc_image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/puc/';
				$config['allowed_types'] = $this->image_types;
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
			}else{
				$taxi_document['puc_image'] = $result->puc_image;	
			}
			
			//$this->sma->print_arrays($taxi, $taxi_document);
			//die;
        }
		
        if ($this->form_validation->run() == true && $this->taxi_model->edit_taxi($taxi, $taxi_document, $id)){
			
            $this->session->set_flashdata('message', lang("taxi_edited"));
            admin_redirect('taxi');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('taxi'), 'page' => lang('cab')), array('link' => '#', 'page' => lang('edit_taxi')));
            $meta = array('page_title' => lang('add_taxi'), 'bc' => $bc);
			
			
			$this->data['types'] = $this->masters_model->getALLTaxi_type();
			
			$this->data['result'] = $result;
			$this->data['id'] = $id;	
			
            $this->page_construct('taxi/edit_taxi', $meta, $this->data);
        }        
    }
	
	
	function view_taxi($id){

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['taxi'] = $this->taxi_model->getTaxiData($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab')));
        $meta = array('page_title' => lang('cab'), 'bc' => $bc);
        $this->page_construct('taxi/view_taxi', $meta, $this->data);
    }
	
	function taxi_status($status, $id){
		
		if($status == 'active'){
			$this->session->set_flashdata('message', 'Your taxi is active!');
            admin_redirect('taxi');
		}
		
		$this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
		$this->form_validation->set_rules('model', $this->lang->line("model"), 'required');
		$this->form_validation->set_rules('number', $this->lang->line("number"), 'required');
		$this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
		$this->form_validation->set_rules('engine_number', $this->lang->line("engine_number"), 'required');
		$this->form_validation->set_rules('chassis_number', $this->lang->line("chassis_number"), 'required');
		$this->form_validation->set_rules('make', $this->lang->line("make"), 'required');
		$this->form_validation->set_rules('fuel_type', $this->lang->line("fuel_type"), 'required');
		$this->form_validation->set_rules('color', $this->lang->line("color"), 'required');
		$this->form_validation->set_rules('manufacture_year', $this->lang->line("manufacture_year"), 'required');
		$this->form_validation->set_rules('capacity', $this->lang->line("capacity"), 'required');
		$this->form_validation->set_rules('reg_date', $this->lang->line("reg_date"), 'required');
		$this->form_validation->set_rules('reg_due_date', $this->lang->line("reg_due_date"), 'required');
		$this->form_validation->set_rules('reg_owner_name', $this->lang->line("reg_owner_name"), 'required');
		$this->form_validation->set_rules('reg_owner_address', $this->lang->line("reg_owner_address"), 'required');	
		
		$this->form_validation->set_rules('taxation_amount_paid', $this->lang->line("taxation_amount_paid"), 'required');
		$this->form_validation->set_rules('taxation_due_date', $this->lang->line("taxation_due_date"), 'required');
		
		$this->form_validation->set_rules('insurance_policy_no', $this->lang->line("insurance_policy_no"), 'required');
		$this->form_validation->set_rules('insurance_due_date', $this->lang->line("insurance_due_date"), 'required');
		$this->form_validation->set_rules('permit_no', $this->lang->line("permit_no"), 'required');
		$this->form_validation->set_rules('permit_due_date', $this->lang->line("permit_due_date"), 'required');
		$this->form_validation->set_rules('authorisation_no', $this->lang->line("authorisation_no"), 'required');
		$this->form_validation->set_rules('authorisation_due_date', $this->lang->line("authorisation_due_date"), 'required');
		$this->form_validation->set_rules('fitness_due_date', $this->lang->line("fitness_due_date"), 'required');
		$this->form_validation->set_rules('speed_due_date', $this->lang->line("speed_due_date"), 'required');
		$this->form_validation->set_rules('puc_due_date', $this->lang->line("puc_due_date"), 'required');
		
		if ($this->form_validation->run() == true) {
		
			$data = array(
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
				'is_verify' => $this->input->post('is_verify'),
				'approved_by' => $this->session->userdata('user_id'),
				'approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_doc = array(
				'reg_date' => $this->input->post('reg_date'),
				'reg_due_date' => $this->input->post('reg_due_date'),
				'reg_owner_name' => $this->input->post('reg_owner_name'),
				'reg_owner_address' => $this->input->post('reg_owner_address'),
				'reg_verify' => $this->input->post('reg_verify'),
				'reg_approved_by' => $this->session->userdata('user_id'),
				'reg_approved_on' => date('Y-m-d H:i:s'),
			);
			
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
				'taxation_verify' => $this->input->post('taxation_verify'),
				'taxation_approved_by' => $this->session->userdata('user_id'),
				'taxation_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_insurance = array(
				'insurance_policy_no' => $this->input->post('insurance_policy_no'),
				'insurance_due_date' => $this->input->post('insurance_due_date'),
				'insurance_verify' => $this->input->post('insurance_verify'),
				'insurance_approved_by' => $this->session->userdata('user_id'),
				'insurance_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_permit = array(
				'permit_no' => $this->input->post('permit_no'),
				'permit_due_date' => $this->input->post('permit_due_date'),
				'permit_verify' => $this->input->post('permit_verify'),
				'permit_approved_by' => $this->session->userdata('user_id'),
				'permit_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_authorisation = array(
				'authorisation_no' => $this->input->post('authorisation_no'),
				'authorisation_due_date' => $this->input->post('authorisation_due_date'),
				'authorisation_verify' => $this->input->post('authorisation_verify'),
				'authorisation_approved_by' => $this->session->userdata('user_id'),
				'authorisation_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_fitness = array(
				'fitness_due_date' => $this->input->post('fitness_due_date'),
				'fitness_verify' => $this->input->post('fitness_verify'),
				'fitness_approved_by' => $this->session->userdata('user_id'),
				'fitness_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_speed = array(
				'speed_due_date' => $this->input->post('speed_due_date'),
				'speed_verify' => $this->input->post('speed_verify'),
				'speed_approved_by' => $this->session->userdata('user_id'),
				'speed_approved_on' => date('Y-m-d H:i:s'),
			);
			
			$data_puc = array(
				'puc_due_date' => $this->input->post('puc_due_date'),
				'puc_verify' => $this->input->post('puc_verify'),
				'puc_approved_by' => $this->session->userdata('user_id'),
				'puc_approved_on' => date('Y-m-d H:i:s'),
			);
			
			
			
			$check_taxi = $this->verification_model->update_taxi_status($this->input->post('taxi_id'), $data, $status) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_doc) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_taxation)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_insurance)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_permit)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_authorisation)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_fitness)  && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_speed) && $this->verification_model->update_taxi_common_status($this->input->post('document_id'), $data_puc);
		
		
		}
		if ($this->form_validation->run() && $check_taxi)
		{
			
			$this->session->set_flashdata('message', $this->input->post('first_name').' details has been verified');
            admin_redirect('taxi');
			
			
		}else{
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('taxi_status')));
			$meta = array('page_title' => lang('taxi_status'), 'bc' => $bc);
			
			$result = $this->verification_model->getTaxi($id);
			$result_doc = $this->verification_model->getTaxiDocument($id);
			$this->data['types'] = $this->masters_model->getALLTaxi_type();
			$this->data['fuel_types'] = $this->masters_model->getALLTaxi_fuel();
			$this->data['result_doc'] = $result_doc;
			$this->data['result'] = $result;
			$this->data['id'] = $id;	
			$this->data['status'] = $status;
			
			$this->page_construct('taxi/taxi_status', $meta, $this->data);
		}
	
	}
	
	function status($status,$id){
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->taxi_model->update_status($data,$id);
		redirect($_SERVER["HTTP_REFERER"]);
    }
   
   
	
}
