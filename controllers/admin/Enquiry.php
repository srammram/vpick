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
		$this->load->library('firebase');
		$this->load->library('push');
		$this->load->helper('string');
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
		$this->load->admin_model('enquiry_model');
		$this->load->admin_model('masters_model');
		
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
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
		
		$this->data['url_data'] = $this->enquiry_model->getDashboard($this->session->userdata('user_id'), $countryCode);
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
	
		
	function enquiry_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Enquiry');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('ticket_type'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('ticket'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('services type'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('customer_name'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));
                   
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
					$res = $this->enquiry_model->getALLEnquiry($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->enquiry_type);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->enquiry_code);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->enquiry_date);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->help_department_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->customer_name);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->status);
                       
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
                    $filename = 'enquiry_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function getEnquiry(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$status = $_GET['status'];
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('enquiry')}.id as id,  {$this->db->dbprefix('enquiry')}.enquiry_type, {$this->db->dbprefix('enquiry')}.enquiry_code, {$this->db->dbprefix('enquiry')}.enquiry_date, h.name as help_department_name, u.first_name as customer_name, {$this->db->dbprefix('enquiry')}.enquiry_status as status, country.name as instance_country ")
            ->from("enquiry")
			->join("countries country", " country.iso = enquiry.is_country", "left")
			->join("help h", " h.id = {$this->db->dbprefix('enquiry')}.help_department ", "left")
			->join("users u", " u.id = {$this->db->dbprefix('enquiry')}.customer_id ", "left");
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('enquiry')}.enquiry_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('enquiry')}.enquiry_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("enquiry.is_country", $countryCode);
			}
			
			
           
			if($status != NULL){
				$this->datatables->where('enquiry_status', $status);	
			}
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
			$edit = "<a href='" . admin_url('enquiry/enquiry_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			
			
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_user_department") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><div class='kapplist-view1'></div></a>";
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
				$html .='<input type="text" name="'.$val->form_name.'" value="" class="form-control" required="required">';
			}elseif($val->form_type == 2){
				$html .='<textarea name="'.$val->form_name.'" class="form-control" required="required"></textarea>';
			}elseif($val->form_type == 4){
				$html .='<input type="text" name="'.$val->form_name.'" value="" class="form-control date" required="required">';
				
			}elseif($val->form_type == 3){
				$html .='<input id="'.$val->form_name.'" type="file" data-browse-label="browse" name="'.$val->form_name.'" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*" >';
			}
			
			$html .='</div>';
			//echo $key->name.'----'.$val->name.'<br>';
				
		}
		
		echo $html;	
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
		$this->form_validation->set_rules('customer_id', lang("customer_id"), 'required');    
		
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
			
			$checkride = $this->enquiry_model->checkRides($this->input->post('customer_type'), $this->input->post('customer_id'), $start_date, $end_date, $countryCode);
			
			
			if(!empty($checkride)){
				admin_redirect('enquiry/rides/?sdate='.$checkride['start_date'].'&edate='.$checkride['end_date'].'&customer_type='.$checkride['customer_type'].'&user_id='.$checkride['user_id'].'&is_country='.$countryCode);
			}else{
				admin_redirect('enquiry/create_ticket/?customer_type='.$this->input->post('customer_type').'&customer_id='.$this->input->post('customer_id').'&is_country='.$countryCode);	
			}
			
        }elseif($this->input->post('ticket')){
			$this->session->set_flashdata('error', validation_errors());
            admin_redirect('enquiry');
		}
		
        if ($this->form_validation->run() == true){
			
            //$this->session->set_flashdata('message', lang("country_added"));
            //admin_redirect('enquiry/existing_ticket_list');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['country_code'] = $this->masters_model->getALLCountry();
            $this->load->view($this->theme . 'enquiry/create_customer', $this->data);
			
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
        $this->page_construct('enquiry/rides', $meta, $this->data);
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
			->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no, {$this->db->dbprefix('rides')}.start,  {$this->db->dbprefix('rides')}.end,  {$this->db->dbprefix('rides')}.status, country.name as instance_country  ")
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
			
			$edit = "<a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'><div class='kapplist-view1'></div></a></div>
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
		if( $_GET['ride_id'] != 'undefined' && $_GET['ride_id'] != NULL && !empty($_GET['ride_id'])){
			$ride_id = $_GET['ride_id'];
		}else{
			$ride_id = '0';
		}
        $user_id = $_GET['customer_id'] != 'undefined' ? $_GET['customer_id'] : '0';
		
		$this->form_validation->set_rules('customer_id', lang("user"), 'required');  
		$this->form_validation->set_rules('help_department', lang("Support Services"), 'required');     
		
		$customer_details = $this->enquiry_model->getCustomer($user_id, $ride_id, $countryCode);
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		
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
						$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
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
				'enquiry_type' => 'Telephone',
				'services_id' => $this->input->post('ride_id'),
				'help_id' => $help_id,
				'help_message' => json_encode($res),
				'help_department' => $help_department,
            );
           	
        }elseif($this->input->post('ticket')){
			$this->session->set_flashdata('error', validation_errors());
            admin_redirect('enquiry');
		}
		
        if ($this->form_validation->run() == true && $this->enquiry_model->create_ticket($enquiry, $customer_id, $help_department, $countryCode)){
			
            $this->session->set_flashdata('message', lang("ticket has been created"));
            admin_redirect('enquiry');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/create_ticket'), 'page' => lang('open')), array('link' => '#', 'page' => lang('create_ticket')));
            $meta = array('page_title' => lang('create_ticket'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['user_id'] = $user_id;
			$this->data['ride_id'] = $ride_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['customer_details'] = $customer_details;
			$this->data['helps'] = $this->enquiry_model->getHelp($countryCode);
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
		$this->data['user_details'] = $user_details;
		$this->data['enquiry'] = $this->enquiry_model->getUserEnquiry($user_id, $countryCode);
		$this->page_construct('enquiry/existing_ticket_list', $meta, $this->data);
       
    }
	
	function enquiry_view($enquiry_id){
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		
          if($this->session->userdata('group_id') == 1){
			if($enquiry_details->is_country != ''){
				$countryCode = $enquiry_details->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$follows_details = $this->enquiry_model->getFollows($enquiry_id, $countryCode);
		
       
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/open'), 'page' => lang('View')), array('link' => '#', 'page' => lang('View')));
            $meta = array('page_title' => lang('View'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['follows_details'] = $follows_details;
            $this->page_construct('enquiry/enquiry_view', $meta, $this->data);
        
    }
	
	function getTripHistory(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$customer_type = $_GET['customer_type'];
		$customer_id = $_GET['customer_id'];
		$this->load->library('datatables');
        $this->datatables
           
			->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no, {$this->db->dbprefix('rides')}.start,  {$this->db->dbprefix('rides')}.end, country.name as instance_country  ")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1', 'left')
			->join('users u','u.id=rides.driver_id AND u.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
            ->join('taxi t','t.id=rides.taxi_id AND t.is_edit=1 ', 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			
			
			
			if($customer_type == 'vendor'){
				$this->datatables->where('rides.vendor_id',$customer_id);
			}elseif($customer_type == 'driver'){
				$this->datatables->where('rides.driver_id',$customer_id);
			}elseif($customer_type == 'customer'){
				$this->datatables->where('rides.customer_id',$customer_id);
			}
			
          $this->datatables->unset_column('id');
        echo $this->datatables->generate();
	}
	
	function open($enquiry_id){
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		if($this->session->userdata('group_id') == 1){
			if($enquiry_details->is_country != ''){
				$countryCode = $enquiry_details->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
         $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']); 
		$this->form_validation->set_rules('enquiry_id', lang("enquiry_id"), 'required');     
		
		
		$follows_details = $this->enquiry_model->getFollows($enquiry_id, $countryCode);
		
        if ($this->form_validation->run() == true) {
            $enquiry = array(
                'enquiry_status' => $this->input->post('enquiry_status'),
            );
           	
			
			if($this->input->post('enquiry_status') == 0){
				$sms_enquiry_status = 'Process';
			}elseif($this->input->post('enquiry_status') == 1){
				$sms_enquiry_status = 'Open';
			}elseif($this->input->post('enquiry_status') == 2){
				$sms_enquiry_status = 'Transfer';
			}elseif($this->input->post('enquiry_status') == 3){
				$sms_enquiry_status = 'Close';
			}elseif($this->input->post('enquiry_status') == 4){
				$sms_enquiry_status = 'Reopen';
			}
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
				'discussion' => $this->input->post('discussion') ? $this->input->post('discussion') : 'Follow Open',
				'remark' => $this->input->post('remark') ? $this->input->post('remark') : 'No',
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		$getuser = $this->enquiry_model->userGet($enquiry_details->customer_id, $countryCode);
		
        if ($this->form_validation->run() == true && $this->enquiry_model->openenquiry($enquiry, $enquiry_support, $enquiry_follow, $this->input->post('enquiry_status'), $enquiry_id, $countryCode)){
			
			$sms_message = 'Your ticket has been '.$sms_enquiry_status;
			$sms_phone = $getuser->country_code.$sms_phone->mobile;
			$sms_country_code = $getuser->country_code;
			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				
            $this->session->set_flashdata('message', lang("enquiry_opened"));
            admin_redirect('enquiry/listview');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/open'), 'page' => lang('open')), array('link' => '#', 'page' => lang('open')));
            $meta = array('page_title' => lang('open'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['follows_details'] = $follows_details;
            $this->page_construct('enquiry/open', $meta, $this->data);
        }
    }
	
	function reopen($enquiry_id){
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		if($this->session->userdata('group_id') == 1){
			if($enquiry_details->is_country != ''){
				$countryCode = $enquiry_details->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
          $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$this->form_validation->set_rules('enquiry_id', lang("enquiry_id"), 'required');     
		
		
		$follows_details = $this->enquiry_model->getFollows($enquiry_id, $countryCode);
		
        if ($this->form_validation->run() == true) {
            $enquiry = array(
                'enquiry_status' => $this->input->post('enquiry_status'),
            );
			
			if($this->input->post('enquiry_status') == 0){
				$sms_enquiry_status = 'Process';
			}elseif($this->input->post('enquiry_status') == 1){
				$sms_enquiry_status = 'Open';
			}elseif($this->input->post('enquiry_status') == 2){
				$sms_enquiry_status = 'Transfer';
			}elseif($this->input->post('enquiry_status') == 3){
				$sms_enquiry_status = 'Close';
			}elseif($this->input->post('enquiry_status') == 4){
				$sms_enquiry_status = 'Reopen';
			}
           	
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
				'discussion' => $this->input->post('discussion') ? $this->input->post('discussion') : 'Follow Open',
				'remark' => $this->input->post('remark') ? $this->input->post('remark') : 'No',
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		$getuser = $this->enquiry_model->userGet($enquiry_details->customer_id, $countryCode);
        if ($this->form_validation->run() == true && $this->enquiry_model->reopenenquiry($enquiry, $enquiry_support, $enquiry_follow, $this->input->post('enquiry_status'), $enquiry_id, $countryCode)){
			
			$sms_message = 'Your ticket has been '.$sms_enquiry_status;
			$sms_phone = $getuser->country_code.$sms_phone->mobile;
			$sms_country_code = $getuser->country_code;
			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
            $this->session->set_flashdata('message', lang("enquiry_reopened"));
            admin_redirect('enquiry/listview');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('enquiry/open'), 'page' => lang('reopen')), array('link' => '#', 'page' => lang('open')));
            $meta = array('page_title' => lang('reopen'), 'bc' => $bc);
			$this->data['enquiry_id'] = $enquiry_id;
			$this->data['enquiry_details'] = $enquiry_details;
			$this->data['follows_details'] = $follows_details;
            $this->page_construct('enquiry/reopen', $meta, $this->data);
        }
    }
	
	function close_transfer($enquiry_id){
		$enquiry_details = $this->enquiry_model->getEnquiryID($enquiry_id);
		if($this->session->userdata('group_id') == 1){
			if($enquiry_details->is_country != ''){
				$countryCode = $enquiry_details->is_country;	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
          
		  $follows_details = $this->enquiry_model->getFollows($enquiry_id, $countryCode);
		 
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
           	
			if($this->input->post('enquiry_status') == 0){
				$sms_enquiry_status = 'Process';
			}elseif($this->input->post('enquiry_status') == 1){
				$sms_enquiry_status = 'Open';
			}elseif($this->input->post('enquiry_status') == 2){
				$sms_enquiry_status = 'Transfer';
			}elseif($this->input->post('enquiry_status') == 3){
				$sms_enquiry_status = 'Close';
			}elseif($this->input->post('enquiry_status') == 4){
				$sms_enquiry_status = 'Reopen';
			}
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
				'discussion' => $this->input->post('discussion') ? $this->input->post('discussion') : 'Follow Open',
				'remark' => $this->input->post('remark') ? $this->input->post('remark') : 'No',
				'is_edit' => 1,
				'created_by' => $this->session->userdata('user_id'),
				'created_on' => date('Y-m-d H:i:s')				
			);
			
			
		   
        }
		$getuser = $this->enquiry_model->userGet($enquiry_details->customer_id, $countryCode);
        if ($this->form_validation->run() == true && $this->enquiry_model->closeenquiry($enquiry, $enquiry_support, $enquiry_follow, $enquiry_status, $enquiry_id, $countryCode)){
			
			$sms_message = 'Your ticket has been '.$sms_enquiry_status;
			$sms_phone = $getuser->country_code.$sms_phone->mobile;
			$sms_country_code = $getuser->country_code;
			$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
			
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
			$this->data['follows_details'] = $follows_details;
			$this->data['helps'] = $this->enquiry_model->getHelp($countryCode);
            $this->page_construct('enquiry/close', $meta, $this->data);
        }
    }
    

}
