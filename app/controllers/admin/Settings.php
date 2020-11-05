<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
	$this->load->library('upload');
        $this->upload_path = 'assets/uploads/settings/';
        $this->thumbs_path = 'assets/uploads/settings/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->allowed_file_size = '1024';
	$this->image_path = base_url('assets/uploads/settings/');
        $this->load->admin_model('settings_model');
    }

    function currencies($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('drivers')));
        $meta = array('page_title' => lang('drivers'), 'bc' => $bc);
        $this->page_construct('settings/currencies', $meta, $this->data);
    }
    function getCurrencies(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, name,symbol,status")
            ->from("currencies")
             ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_currency(){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'is_unique[currencies.name]');
        $this->form_validation->set_rules('symbol', lang("symbol"), 'is_unique[currencies.symbol]');     
        
        if ($this->form_validation->run() == true) {
            
          //  print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
		'iso_code' =>$this->input->post('iso_code'),
		'numeric_iso_code' =>$this->input->post('numeric_iso_code'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status'),
            );
           
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_currency($data)){
			
            $this->session->set_flashdata('message', lang("currency_added"));
            admin_redirect('settings/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('add_currency')));
            $meta = array('page_title' => lang('add_currency'), 'bc' => $bc);
            $this->page_construct('settings/add_currency', $meta, $this->data);
        }
    }
    function edit_currency($id){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('symbol', lang("symbol"), 'required');      
        
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
		'iso_code' =>$this->input->post('iso_code'),
		'numeric_iso_code' =>$this->input->post('numeric_iso_code')
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->update_currency($id,$data)){
			
            $this->session->set_flashdata('message', lang("currency_updated"));
            admin_redirect('settings/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_currency'), 'bc' => $bc);
            $this->data['currency'] = $this->settings_model->getCurrencyby_ID($id);
            $this->page_construct('settings/edit_currency', $meta, $this->data);
        }
    }
    function currency_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_currency_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    /***************** locations *********************/
    function continents($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('continent')));
        $meta = array('page_title' => lang('continent'), 'bc' => $bc);
        $this->page_construct('settings/continents', $meta, $this->data);
    }
    function getContinents(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,continent_name,status")
            ->from("continents")
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
        //print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_continent(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("continent_name"), 'is_unique[continents.continent_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'continent_name' => $this->input->post('continent_name'),
                'status' => 1,
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_continent($data)){
			
            $this->session->set_flashdata('message', lang("continent_added"));
            admin_redirect('settings/continents');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
          
            $this->load->view($this->theme . 'settings/add_continent', $this->data);
        }
    }
    function edit_continent(){
        
    }
    function delete_continent($id){
        $data['status'] = 9;
        $this->settings_model->update_continent_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function continent_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_continent_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function countries($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('models')));
        $meta = array('page_title' => lang('models'), 'bc' => $bc);
        $this->page_construct('settings/countries', $meta, $this->data);
    }
    function getCountries(){
	
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
	//CASE WHEN (flag!='') THEN CONCAT($flag_path,flag) END as flagf,
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('countries')}.id as id,flag,continent_name,country_name,country_code,{$this->db->dbprefix('countries')}.status as status")
            ->from("countries")
	    ->join("continents",'continents.id=countries.continent_id')
	    ->edit_column('flag', '$1__'.$this->image_path  , 'flag')
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
       // print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_country(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("country_name"), 'is_unique[countries.country_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
		'continent_id' => $this->input->post('continent_id'),
                'country_name' => $this->input->post('country_name'),
                'country_code' => $this->input->post('country_code'),
		'call_prefix' => $this->input->post('call_prefix'),
		'currency' => $this->input->post('currency'),
                'status' => 1,
            );
	     if ($_FILES['photo']['size'] > 0) {
                $config['upload_path'] = $this->upload_path.'country/flag';
                $config['allowed_types'] = $this->image_types;
                //$config['max_size'] = $this->allowed_file_size;
                //$config['max_width'] = $this->Settings->iwidth;
                //$config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('photo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    //admin_redirect("drivers/add");
                }
                $photo = $this->upload->file_name;
                $data['flag'] = 'country/flag/'.$photo;
		
                $config = NULL;
            }
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_country($data)){
			
            $this->session->set_flashdata('message', lang("country_added"));
            admin_redirect('settings/countries');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	    $this->data['continents'] = $this->site->getAllContinents();
	    $this->data['currencies'] = $this->site->getAllCurrencies();
            $this->load->view($this->theme . 'settings/add_country', $this->data);
        }
    }
    function edit_country(){
        
    }
    function delete_country($id){
        $data['status'] = 9;
        $this->settings_model->update_model_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function country_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_country_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function zones($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('zone')));
        $meta = array('page_title' => lang('zone'), 'bc' => $bc);
        $this->page_construct('settings/zones', $meta, $this->data);
    }
    function getZones(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('zones')}.id as id,country_name,zone_name,{$this->db->dbprefix('zones')}.status as status")
            ->from("zones")
	     ->join("countries",'countries.id=zones.country_id')
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
        //print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_zone(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("zone_name"), 'is_unique[zones.zone_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'zone_name' => $this->input->post('zone_name'),
		'country_id' => $this->input->post('country_id'),
                'status' => 1,
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_zone($data)){
			
            $this->session->set_flashdata('message', lang("zone_added"));
            admin_redirect('settings/zones');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['countries'] = $this->site->getAllCountries();
            $this->load->view($this->theme . 'settings/add_zone', $this->data);
        }
    }
    function edit_zone(){
        
    }
    function delete_zone($id){
        $data['status'] = 9;
        $this->settings_model->update_zone_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function zone_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_zone_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function states($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('models')));
        $meta = array('page_title' => lang('models'), 'bc' => $bc);
        $this->page_construct('settings/states', $meta, $this->data);
    }
    function getStates(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('states')}.id as id,state_name,country_name,{$this->db->dbprefix('states')}.status as status")
            ->from("states")
            ->join("countries",'countries.id=states.country_id')
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
        //print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_state(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("state_name"), 'is_unique[states.state_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'state_name' => $this->input->post('state_name'),
                'country_id' => $this->input->post('country_id'),
		'zone_id' => $this->input->post('zone_id'),
                'status' => 1,
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_state($data)){
			
            $this->session->set_flashdata('message', lang("state_added"));
            admin_redirect('settings/states');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['countries'] = $this->site->getAllCountries();
            $this->load->view($this->theme . 'settings/add_state', $this->data);
        }
    }
    function state_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_state_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    
    /******************** cities **************************/
    function cities($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('models')));
        $meta = array('page_title' => lang('models'), 'bc' => $bc);
        $this->page_construct('settings/cities', $meta, $this->data);
    }
    function getCities(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('cities')}.id as id,city_name,state_name,country_name,{$this->db->dbprefix('cities')}.status as status")
            ->from("cities")
            ->join("states",'states.id=cities.state_id')
            ->join("countries",'countries.id=states.country_id')
            
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
        //print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_city(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('city_name', lang("city_name"), 'is_unique[cities.city_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'city_name' => $this->input->post('city_name'),
                'state_id' => $this->input->post('state_id'),
		'latitude' => $this->input->post('latitude'),
		'longitude' => $this->input->post('longitude'),
                'status' => 1,
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_city($data)){
			
            $this->session->set_flashdata('message', lang("city_added"));
            admin_redirect('settings/cities');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['countries'] = $this->site->getAllCountries();
            $this->load->view($this->theme . 'settings/add_city', $this->data);
        }
    }
    function city_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_city_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function getStates_bycountry(){
        $id = $this->input->post('country');
        $data = $this->site->getStates_bycountry($id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->state_name;
            }
        }
        
        echo json_encode($options);exit;
    }
    function getZones_bycountry(){
	$id = $this->input->post('country');
        $data = $this->site->getZones_bycountry($id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->zone_name;
            }
        }
        
        echo json_encode($options);exit;
    }
    
    /******************** areas **************************/
    function areas($action=false){
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('models')));
        $meta = array('page_title' => lang('models'), 'bc' => $bc);
        $this->page_construct('settings/areas', $meta, $this->data);
    }
    function getAreas(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('cities')}.id as id,city_name,state_name,country_name,{$this->db->dbprefix('cities')}.status as status")
            ->from("areas")
            ->join("cities",'cities.id=areas.city_id')
            ->join("states",'states.id=cities.state_id')
            ->join("countries",'countries.id=states.country_id')
            
            ->edit_column('status', '$1__$2', 'status, id');

        //->unset_column('id');
        //print_R($this->datatables);
        echo $this->datatables->generate();
    }
    function add_area(){
        //$this->sma->checkPermissions();
        $this->form_validation->set_rules('area_name', lang("area_name"), 'is_unique[areas.area_name]');    
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'area_name' => $this->input->post('area_name'),
                'city_id' => $this->input->post('city_id'),
                'status' => 1,
            );
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_area($data)){
			
            $this->session->set_flashdata('message', lang("area_added"));
            admin_redirect('settings/areas');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['countries'] = $this->site->getAllCountries();
            $this->load->view($this->theme . 'settings/add_area', $this->data);
        }
    }
    function area_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_area_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function getcities_byStates(){
        $id = $this->input->post('state');
        $data = $this->site->getcities_byStates($id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->city_name;
            }
        }
        
        echo json_encode($options);exit;
    }
    
    
    function kyc_doc_types($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('kyc_doc_types')));
        $meta = array('page_title' => lang('kyc_doc_types'), 'bc' => $bc);
        $this->page_construct('settings/kyc_doc_types', $meta, $this->data);
    }
    function getkyc_doc_types(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,type,status")
            ->from("kyc_doc_types")
             ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('settings/edit_kyc_doc_type/$1') . "' class='tip' title='" . lang("edit_document_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_kyc_doc_type(){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('type', lang("document_type"), 'is_unique[kyc_doc_types.type]');    
        
        if ($this->form_validation->run() == true) {
            
            //echo '<pre>';print_R($_POST);
            $data = array(
                'type' => $this->input->post('type'),
                'created_on' => date('Y-m-d H:i:s'),
		'user_type' => $this->input->post('user_type'),
                'status' => $this->input->post('status')
            );
            $fields = array();
	    foreach($_POST['label_name'] as $k => $row){
		$fields[$k]['label_name'] = $row;
		$fields[$k]['status'] = 1;
		$fields[$k]['input_type'] = $_POST['input_type'][$k];
		$fields[$k]['options'] = $_POST['options'][$k];
	    }
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_kyc_doc_type($data,$fields)){
			
            $this->session->set_flashdata('message', lang("currency_added"));
            admin_redirect('settings/kyc_doc_types');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/kyc_doc_types'), 'page' => lang('kyc_doc_types')), array('link' => '#', 'page' => lang('add_kyc_doc_type')));
            $meta = array('page_title' => lang('add_kyc_doc_type'), 'bc' => $bc);
            $this->page_construct('settings/add_kyc_doc_type', $meta, $this->data);
        }
    }
    function edit_kyc_doc_type($id){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('type', lang("document_type"), 'required');   
        
        if ($this->form_validation->run() == true) {
            //echo '<pre>';print_R($_POST);exit;
            $data = array(
                'type' => $this->input->post('type'),
		'user_type' => $this->input->post('user_type'),
                'status' => $this->input->post('status')
            );
	    $fields = array();
	    foreach($_POST['label_name'] as $k => $row){
		$fields[$k]['label_name'] = $row;
		$fields[$k]['status'] = $_POST['field_status'][$k];;
		$fields[$k]['id'] = @$_POST['field_id'][$k];
		$fields[$k]['input_type'] = $_POST['input_type'][$k];
		$fields[$k]['options'] = $_POST['options'][$k];
	    }
	    //print_R($fields);exit;
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->update_kyc_doc_type($id,$data,$fields)){
			
            $this->session->set_flashdata('message', lang("document_type_updated"));
            admin_redirect('settings/kyc_doc_types');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/kyc_doc_types'), 'page' => lang('kyc_doc_types')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_kyc_doc_type'), 'bc' => $bc);
            $this->data['doc'] = $this->settings_model->getKycDoctypeby_ID($id);
	    $this->data['fields'] = $this->settings_model->getKycDoctypeFieldsby_ID($id);
            $this->page_construct('settings/edit_kyc_doc_type', $meta, $this->data);
        }
    }
    function kyc_doc_type_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->settings_model->update_doc_type_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    
    function usergroups($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('groups')));
        $meta = array('page_title' => lang('groups'), 'bc' => $bc);
        $this->page_construct('settings/user_groups', $meta, $this->data);
    }
    function getUserGroups(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,name")
            ->from("groups")
	    ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('settings/edit_user_group/$1') . "' class='tip' title='" . lang("edit_user_group") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_user_group(){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'is_unique[groups.name]');    
        
        if ($this->form_validation->run() == true) {
            
          //  print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
           
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_user_group($data)){
			
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('settings/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/usergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('add_user_group')));
            $meta = array('page_title' => lang('add_user_group'), 'bc' => $bc);
            $this->page_construct('settings/add_user_group', $meta, $this->data);
        }
    }
    function edit_user_group($id){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        
        if ($this->form_validation->run() == true) {
            
          //  print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
           
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->update_user_group($id,$data)){
			
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('settings/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/usergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('edit_user_group')));
            $meta = array('page_title' => lang('edit_user_group'), 'bc' => $bc);
	    $this->data['group'] = $this->settings_model->getUserGroupby_ID($id);
            $this->page_construct('settings/edit_user_group', $meta, $this->data);
        }
    }
    /************************** customer groups ********************/
    function customergroups($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customer_groups')));
        $meta = array('page_title' => lang('customer_groups'), 'bc' => $bc);
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }
    function getCustomerGroups(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,name")
            ->from("customer_groups")
	    ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('settings/edit_customer_group/$1') . "' class='tip' title='" . lang("edit_customer_group") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_customer_group(){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[customer_groups.name]');    
        
        if ($this->form_validation->run() == true) {
            
          //  print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
           
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->add_customer_group($data)){
			
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('settings/customergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/customergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('add_customer_group')));
            $meta = array('page_title' => lang('add_customer_group'), 'bc' => $bc);
            $this->page_construct('settings/add_customer_group', $meta, $this->data);
        }
    }
    function edit_customer_group($id){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        
        if ($this->form_validation->run() == true) {
            
          //  print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
           
        }
		
		
        if ($this->form_validation->run() == true && $this->settings_model->update_customer_group($id,$data)){
			
            $this->session->set_flashdata('message', lang("customer_group_updated"));
            admin_redirect('settings/edit_customer_group/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings/customergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('edit_customer_group')));
            $meta = array('page_title' => lang('edit_customer_group'), 'bc' => $bc);
	    $this->data['group'] = $this->settings_model->getCustomerGroupby_ID($id);
            $this->page_construct('settings/edit_customer_group', $meta, $this->data);
        }
    }
}
