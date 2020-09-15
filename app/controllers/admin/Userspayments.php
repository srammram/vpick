<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Enquiry extends MY_Controller
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
		$this->load->admin_model('userspayments_model');
		$this->load->admin_model('masters_model');
		
    }
	

	
	/*###### Currency*/
	function index($action = NULL)
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('dashboard')));
        $meta = array('page_title' => lang('dashboard'), 'bc' => $bc);
        $this->page_construct('enquiry/index', $meta, $this->data);
    }
	
    function listview($action = NULL)
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('enquiry')));
        $meta = array('page_title' => lang('enquiry'), 'bc' => $bc);
        $this->page_construct('enquiry/listview', $meta, $this->data);
    }
	
	function getEnquiry(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('enquiry')}.id as id, {$this->db->dbprefix('enquiry')}.enquiry_code, {$this->db->dbprefix('enquiry')}.enquiry_date, h.name as help_department_name, u.first_name as customer_name, {$this->db->dbprefix('enquiry')}.enquiry_status as status, country.name as instance_country")
            ->from("enquiry")
			->join("countries country", " country.iso = enquiry.is_country", "left")
			->join("help h", " h.id = {$this->db->dbprefix('enquiry')}.help_department ", "left")
			->join("users u", " u.id = {$this->db->dbprefix('enquiry')}.customer_id ", "left");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_user_department") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	function getUser_bygroup(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $group_id = $this->input->post('group_id');
        $data = $this->enquiry_model->getUser_bygroup($group_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->first_name.' - ('.$row->country_code.$row->mobile.')';
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
        $data = $this->enquiry_model->getHelp_main_byhelp($parent_id, $countryCode);
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
        $data = $this->enquiry_model->getHelp_sub_byhelp_main($parent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	function getHelp_form_byhelp_sub(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$parent_id = $this->input->get('parent_id');
        $data = $this->enquiry_model->getHelp_form_byhelp_sub($parent_id, $countryCode);
		$html = '';
		$html .= '<div class="form-group all col-md-12 col-xs-12">'.$data['sub'].'</div>';
		foreach($data['form'] as $key => $val){
			$html .= '<div class="form-group all col-md-6 col-xs-12"><label>'.$val->name.'</label>';
			if($val->form_type == 1){
				$html .='<input type="text" name="'.$val->form_name.'" value="" class="form-control">';
			}elseif($val->form_type == 2){
				$html .='<textarea name="'.$val->form_name.'" class="form-control"></textarea>';
			}elseif($val->form_type == 3){
				$html .='<input id="'.$val->form_name.'" type="file" data-browse-label="browse" name="'.$val->form_name.'" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">';
			}
			
			$html .='</div>';
			//echo $key->name.'----'.$val->name.'<br>';
				
		}
		
		echo $html;	
	}
	
	
	
	function create_ticket(){
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
		$this->form_validation->set_rules('customer_id', lang("user"), 'required');  
		$this->form_validation->set_rules('help_department', lang("Support Services"), 'required');     
		
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id, $countryCode);
		
        if ($this->form_validation->run() == true) {
			foreach ($_POST as $name => $val)
			{
				 if($name != 'token' && $name != 'customer_type' && $name != 'customer_id' && $name != 'help_department' && $name != 'help_main_id' && $name != 'help_sub_id' && $name != 'ticket'){
				 	$res[$name] = $val;
				 }
				 
				 if($name == 'customer_id'){
				 	$customer_id = $val;
				 }
				 if($name == 'help_department'){
				 	$help_department = $val;
				 }
				 if($name == 'help_sub_id'){
				 	$help_id = $val;
				 }
				 
			}
			
			foreach ($_FILES as $name1 => $val)
			{
				if ($_FILES[$name1]['size'] > 0) {
					$config['upload_path'] = $this->upload_path;
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload($name1)) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$files = $this->upload->file_name;
					$res[$name1] = $files;
					$config = NULL;
				}
			}
			
            $enquiry = array(
				'enquiry_code' => 'ENQ'.date('YmdHis'),
                'enquiry_date' => date('Y-m-d'),
				'customer_id' => $customer_id,
				'help_id' => $help_id,
				'help_message' => json_encode($res),
				'help_department' => $help_department,
            );
           	
        }
		
        if ($this->form_validation->run() == true && $this->enquiry_model->create_ticket($enquiry, $countryCode)){
			
            $this->session->set_flashdata('message', lang("ticket has been created"));
            admin_redirect('enquiry');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/create_ticket'), 'page' => lang('open')), array('link' => '#', 'page' => lang('create_ticket')));
            $meta = array('page_title' => lang('create_ticket'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['helps'] = $this->enquiry_model->getHelp();
            $this->page_construct('enquiry/create_ticket', $meta, $this->data);
        }
    }
	
	function existing_ticket(){
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
        $this->form_validation->set_rules('customer_type', lang("customer_type"), 'required');    
		$this->form_validation->set_rules('mobile', lang("mobile"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$user_id = $this->enquiry_model->checkuser($this->input->post('mobile'), $this->input->post('country_code'), $this->input->post('customer_type'), $countryCode);
			if($user_id == 0){
				$this->session->set_flashdata('error', lang("your number does not match"));
            	admin_redirect('enquiry/index');
			}else{
				admin_redirect('enquiry/existing_ticket_list/'.$user_id);
			}
            
        }
		
        if ($this->form_validation->run() == true){
			
            //$this->session->set_flashdata('message', lang("country_added"));
            admin_redirect('enquiry/existing_ticket_list');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['country_code'] = $this->masters_model->getALLCountry();
            $this->load->view($this->theme . 'enquiry/existing_ticket', $this->data);
			
        }
    }
	
	function existing_ticket_list($user_id){
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
		$user_details = $this->enquiry_model->getUserID($user_id, $countryCode);
		
        
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/existing_ticket_list'), 'page' => lang('open')), array('link' => '#', 'page' => lang('open')));
		$meta = array('page_title' => lang('User'), 'bc' => $bc);
		$this->data['user_id'] = $user_id;
		$this->data['user_details'] = $enquiry_details;
		$this->page_construct('enquiry/existing_ticket_list', $meta, $this->data);
       
    }
	
	function open($enquiry_id){
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
		$this->form_validation->set_rules('enquiry_id', lang("enquiry_id"), 'required');     
		
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id, $countryCode);
		
        if ($this->form_validation->run() == true) {
            $enquiry = array(
                'enquiry_status' => $this->input->post('enquiry_status'),
            );
           	
			$enquiry_support = array(
				'enquiry_id' => $enquiry_id,
				'customer_id' => $enquiry_details->customer_id,
				'support_id' => $this->session->userdata('user_id'),
				'help_services' => $enquiry_details->help_department,
				'status' => $this->input->post('enquiry_status'),
				'created_on' => date('Y-m-d H:i:s'),
				'is_edit' => 1
			);
			
			$enquiry_follow = array(
				'enquiryid' => $enquiry_id,
				'support_id' => $this->session->userdata('user_id'),
				'followup_date_time' => date('Y-m-d H:i:s'),
				'calltype' => 'App',
				'discussion' => 'Start Followup',
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		
        if ($this->form_validation->run() == true && $this->enquiry_model->openenquiry($enquiry, $enquiry_support, $enquiry_follow, $this->input->post('enquiry_status'), $enquiry_id, $countryCode)){
			
            $this->session->set_flashdata('message', lang("enquiry_opened"));
            admin_redirect('enquiry/listview');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/open'), 'page' => lang('open')), array('link' => '#', 'page' => lang('open')));
            $meta = array('page_title' => lang('open'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
            $this->page_construct('enquiry/open', $meta, $this->data);
        }
    }
	
	function reopen($enquiry_id){
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
		$this->form_validation->set_rules('enquiry_id', lang("enquiry_id"), 'required');     
		
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		
        if ($this->form_validation->run() == true) {
            $enquiry = array(
                'enquiry_status' => $this->input->post('enquiry_status'),
            );
           	
			$enquiry_support = array(
				'enquiry_id' => $enquiry_id,
				'customer_id' => $enquiry_details->customer_id,
				'support_id' => $this->session->userdata('user_id'),
				'help_services' => $enquiry_details->help_department,
				'status' => $this->input->post('enquiry_status'),
				'created_on' => date('Y-m-d H:i:s'),
				'is_edit' => 1
			);
			
			$enquiry_follow = array(
				'enquiryid' => $enquiry_id,
				'support_id' => $this->session->userdata('user_id'),
				'followup_date_time' => date('Y-m-d H:i:s'),
				'calltype' => 'App',
				'discussion' => 'Start Followup',
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		
        if ($this->form_validation->run() == true && $this->enquiry_model->reopenenquiry($enquiry, $enquiry_support, $enquiry_follow, $this->input->post('enquiry_status'), $enquiry_id, $countryCode)){
			
            $this->session->set_flashdata('message', lang("enquiry_reopened"));
            admin_redirect('enquiry/listview');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/open'), 'page' => lang('reopen')), array('link' => '#', 'page' => lang('open')));
            $meta = array('page_title' => lang('reopen'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
            $this->page_construct('enquiry/reopen', $meta, $this->data);
        }
    }
	
	function close_transfer($enquiry_id){
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
          $enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		$this->form_validation->set_rules('enquiry_id', lang("enquiry_id"), 'required');    
		 $enquiry_status = $this->input->post('enquiry_status');
		 if($enquiry_status == 2){
			 $discussion = 'Transfer Ticket';
			$this->form_validation->set_rules('help_department', lang("Transfer Support Team"), 'required');  
			$help_department = $this->input->post('help_department');   
		 }else{
				$discussion = 'Close Followup'; 
				$help_department = $enquiry_details->help_department;   
		 }
		
		

        if ($this->form_validation->run() == true) {
            $enquiry = array(
                'enquiry_status' => $enquiry_status,
				'help_department' => $help_department,
            );
           	
			;
			$enquiry_support = array(
				'enquiry_id' => $enquiry_id,
				'customer_id' => $enquiry_details->customer_id,
				'support_id' => $this->session->userdata('user_id'),
				'help_services' => $help_department,
				'status' => $enquiry_status,
				'created_on' => date('Y-m-d H:i:s'),
				'is_edit' => 1
			);
			
			
			
			$enquiry_follow = array(
				'enquiryid' => $enquiry_id,
				'support_id' => $this->session->userdata('user_id'),
				'followup_date_time' => date('Y-m-d H:i:s'),
				'calltype' => 'Admin',
				'discussion' => $discussion,
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		
        if ($this->form_validation->run() == true && $this->enquiry_model->closeenquiry($enquiry, $enquiry_support, $enquiry_follow, $enquiry_status, $enquiry_id, $countryCode)){
			if($enquiry_status == 2){
				$this->session->set_flashdata('message', lang("enquiry_transfered"));
			}else{
            	$this->session->set_flashdata('message', lang("enquiry_closed"));
			}
            admin_redirect('enquiry/listview');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/close'), 'page' => lang('close')), array('link' => '#', 'page' => lang('open')));
            $meta = array('page_title' => lang('close'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['helps'] = $this->enquiry_model->getHelp();
            $this->page_construct('enquiry/close', $meta, $this->data);
        }
    }
    

}
