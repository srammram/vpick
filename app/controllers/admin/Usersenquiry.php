<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usersenquiry extends MY_Controller
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
		$this->load->admin_model('usersenquiry_model');
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
		$this->data['url_data'] = $this->usersenquiry_model->getDashboard($this->session->userdata('user_id'));
        $this->page_construct('usersenquiry/index', $meta, $this->data);
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
        $this->page_construct('usersenquiry/listview', $meta, $this->data);
    }
	
	function getUsersenquiry(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$status = $_GET['status'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('enquiry')}.id as id, {$this->db->dbprefix('enquiry')}.enquiry_type, {$this->db->dbprefix('enquiry')}.enquiry_code, {$this->db->dbprefix('enquiry')}.enquiry_date, h.name as help_department_name, u.first_name as customer_name, {$this->db->dbprefix('enquiry')}.enquiry_status as status, country.name as instance_country")
            ->from("enquiry")
			->join("countries country", " country.iso = enquiry.is_country", "left")
			->join("help h", " h.id = {$this->db->dbprefix('enquiry')}.help_department ", "left")
			->join("users u", " u.id = {$this->db->dbprefix('enquiry')}.customer_id ", "left")
			->where('enquiry.customer_id', $this->session->userdata('user_id'));
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}
			
           
			if($status != NULL){
				$this->datatables->where('enquiry_status', $status);	
			}
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_user_department") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('usersenquiry/enquiry_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
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
        $data = $this->usersenquiry_model->getUser_bygroup($group_id, $countryCode);
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
        $data = $this->usersenquiry_model->getHelp_main_byhelp($parent_id, $countryCode);
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
        $data = $this->usersenquiry_model->getHelp_sub_byhelp_main($parent_id, $countryCode);
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
        $data = $this->usersenquiry_model->getHelp_form_byhelp_sub($parent_id, $countryCode);
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
	
	function enquiry_view($enquiry_id){
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
		$enquiry_details = $this->usersenquiry_model->getEnquiryID($enquiry_id, $countryCode);
		$follows_details = $this->usersenquiry_model->getFollows($enquiry_id, $countryCode);
		
       
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('usersenquiry/open'), 'page' => lang('view')), array('link' => '#', 'page' => lang('view')));
            $meta = array('page_title' => lang('view'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['follows_details'] = $follows_details;
            $this->page_construct('usersenquiry/enquiry_view', $meta, $this->data);
        
    }
	
	function create_customer(){
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
		$this->form_validation->set_rules('customer_id', lang("customer"), 'required');    
		
        if ($this->form_validation->run() == true) {
			
			if(!empty($this->input->post('start_date'))){
				$start_date = $this->input->post('start_date');
			}else{
				$start_date = date('Y/m/d')	;
			}
			
			if(!empty($this->input->post('end_date'))){
				$end_date  = $this->input->post('end_date');
			}else{
				$end_date = date('Y/m/d');
			}
			$checkride = $this->usersenquiry_model->checkRides($this->input->post('customer_type'), $this->input->post('customer_id'), $start_date, $end_date, $countryCode);
			
			
			if(!empty($checkride)){
				admin_redirect('usersenquiry/rides/?sdate='.$checkride['start_date'].'&edate='.$checkride['end_date'].'&customer_type='.$checkride['customer_type'].'&user_id='.$checkride['user_id'].'');
			}else{
				admin_redirect('usersenquiry/create_ticket/?customer_type='.$this->input->post('customer_type').'&user_id='.$this->input->post('customer_id').'');	
			}
			
        }
		
        if ($this->form_validation->run() == true){
			
            //$this->session->set_flashdata('message', lang("country_added"));
            //admin_redirect('enquiry/existing_ticket_list');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['country_code'] = $this->masters_model->getALLCountry();
            $this->load->view($this->theme . 'usersenquiry/create_customer', $this->data);
			
        }
    }
	
	function rides($action = NULL)
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
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$customer_type = $_GET['customer_type'];
        $user_id = $_GET['user_id'];
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Rides'));
        $meta = array('page_title' => 'Rides', 'bc' => $bc);
		$this->data['msg'] = 'Rides';
        $this->page_construct('usersenquiry/rides', $meta, $this->data);
    }
    function getOnRides(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        //print_R($_GET);exit;
        $sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$customer_type = $_GET['customer_type'];
        $user_id = $_GET['user_id'];
        
        $this->load->library('datatables');
        $this->datatables
            //->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no,   t.number, cu.first_name as customer_name,  cu.mobile as customer_mobile, u.first_name as driver_name,  u.mobile as driver_mobile,  {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.end, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.status ")
			->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no, {$this->db->dbprefix('rides')}.start,  {$this->db->dbprefix('rides')}.end,  {$this->db->dbprefix('rides')}.status, country.name as instance_country ")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1 ', 'left')
			->join('users u','u.id=rides.driver_id AND u.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
            ->join('taxi t','t.id=rides.taxi_id AND t.is_edit=1 ', 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			
            
			
			if($customer_type == 3){
				$this->datatables->where('rides.vendor_id',$user_id);
			}elseif($customer_type == 4){
				$this->datatables->where('rides.driver_id',$user_id);
			}elseif($customer_type == 5){
				$this->datatables->where('rides.customer_id',$user_id);
			}
			
            
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			//$this->datatables->edit_column('status', '$1__$2', 'id, status');
			
            //$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");
			
			$edit = "<a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'><div class='kapplist-view1'></div></a></div>
			<div><a href=''><div class='kapplist-edit'></div></a></div>
			<div><a href=''><div class='kapplist-car'></div></a></div>
			<div><a href=''><div class='kapplist-path'></div></a></div>
			
			");*/
		//$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
        //$this->datatables->unset_column('id');
        //$this->datatables->unset_column('id');
        echo $this->datatables->generate();
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
		$user_id = $_GET['customer_id'] != 'undefined' ? $_GET['customer_id'] : $this->session->userdata('user_id');
		$ride_id = $_GET['ride_id'] != 'undefined' ? $_GET['ride_id'] : '0';
		
		$this->form_validation->set_rules('customer_id', lang("user"), 'required');  
		$this->form_validation->set_rules('help_department', lang("services_support"), 'required');     
		
		$enquiry_details = $this->usersenquiry_model->getEnquiryID($enquiry_id, $countryCode);
		
        if ($this->form_validation->run() == true) {
			foreach ($_POST as $name => $val)
			{
				 if($name != 'token' && $name != 'customer_type' && $name != 'customer_id' && $name != 'help_department' && $name != 'help_main_id' && $name != 'help_sub_id' && $name != 'ticket' && $name != 'ride_id'){
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
				'services_id' => $this->input->post('ride_id'),
				'help_id' => $help_id,
				'enquiry_type' => 'Website',
				'help_message' => json_encode($res),
				'help_department' => $help_department,
            );
           	
        }elseif($this->input->post('ticket')){
			$this->session->set_flashdata('error', validation_errors());
            admin_redirect('enquiry');
		}
		
        if ($this->form_validation->run() == true && $this->usersenquiry_model->create_ticket($enquiry, $customer_id, $help_department, $countryCode)){
			
            $this->session->set_flashdata('message', lang("ticket_has_been_created"));
            admin_redirect('usersenquiry');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('usersenquiry/create_ticket'), 'page' => lang('open')), array('link' => '#', 'page' => lang('create_ticket')));
            $meta = array('page_title' => lang('create_ticket'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			
			$this->data['user_id'] = $user_id;
			$this->data['ride_id'] = $ride_id;
			$this->data['helps'] = $this->usersenquiry_model->getHelp();
            $this->page_construct('usersenquiry/create_ticket', $meta, $this->data);
        }
    }
	
	
	function existing_ticket_list($enquiry_id){
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
         $user_id = $this->session->userdata('user_id');
		$user_details = $this->usersenquiry_model->getUserID($user_id, $countryCode);
		
        
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('usersenquiry/existing_ticket_list'), 'page' => lang('open')), array('link' => '#', 'page' => lang('open')));
		$meta = array('page_title' => lang('User'), 'bc' => $bc);
		$this->data['user_id'] = $user_id;
		$this->data['user_details'] = $enquiry_details;
		$this->page_construct('usersenquiry/existing_ticket_list', $meta, $this->data);
       
    }

    

}
