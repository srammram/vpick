<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends MY_Controller
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
		$this->load->helper(array('form', 'url'));
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
		$this->load->admin_model('wallet_model');
		$this->load->admin_model('masters_model');
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	
			
	function index($action=false){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['wallet'] = $this->wallet_model->getWalletTotal($countryCode);
        $this->data['action'] = $action;
		
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('wallet')));
        $meta = array('page_title' => lang('wallet'), 'bc' => $bc);
		
        $this->page_construct('wallet/index', $meta, $this->data);
    }
	
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
		
        $this->page_construct('wallet/customer', $meta, $this->data);
    }
    function getCustomer(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('wallet')}.id as id, {$this->db->dbprefix('wallet')}.created as created_on,  u.first_name as fname, u.last_name as lname, 
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 1 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_cash,
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 2 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_credit,
			
			 {$this->db->dbprefix('wallet')}.description as description,  {$this->db->dbprefix('wallet')}.flag as flag, country.name as instance_country
			")
            ->from("wallet")
			->join("countries country", " country.iso = wallet.is_country", "left")
			->join("users u", "u.id = wallet.user_id ");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("wallet.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("wallet.is_country", $countryCode);
			}
			
           
			
			$this->datatables->where('u.group_id', 5);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			$this->datatables->edit_column('flag', '$1__$2', 'id, flag');
		
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
		
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
       $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('customer wallet');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('cash'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('description'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('type'));
					
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:F1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->wallet_model->getALLCustomerE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->fname);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->lname);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->cash);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->description);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->type);
						
						                       
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
                    $filename = 'customerwallet_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
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
		
        $this->page_construct('wallet/driver', $meta, $this->data);
    }
    function getDriver(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('wallet')}.id as id, {$this->db->dbprefix('wallet')}.created as created_on,  u.first_name as fname, u.last_name as lname, 
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 1 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_cash,
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 2 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_credit,
			
			 {$this->db->dbprefix('wallet')}.description as description,  {$this->db->dbprefix('wallet')}.flag as flag, country.name as instance_country
			")
            ->from("wallet")
			->join("countries country", " country.iso = wallet.is_country", "left")
			->join("users u", "u.id = wallet.user_id ");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("wallet.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("wallet.is_country", $countryCode);
			}
			
            
			
			$this->datatables->where('u.group_id', 4);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			$this->datatables->edit_column('flag', '$1__$2', 'id, flag');
		
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
		
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
       $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('customer wallet');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('cash'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('description'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('type'));
					
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:F1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->wallet_model->getALLDriverE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->fname);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->lname);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->cash);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->description);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->type);
						
						                       
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
                    $filename = 'customerwallet_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
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
		
        $this->page_construct('wallet/vendor', $meta, $this->data);
    }
    function getVendor(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('wallet')}.id as id, {$this->db->dbprefix('wallet')}.created as created_on,  u.first_name as fname, u.last_name as lname, ( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 1 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_cash,
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 2 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_credit,
			 {$this->db->dbprefix('wallet')}.description as description,  {$this->db->dbprefix('wallet')}.flag as flag, country.name as instance_country
			")
            ->from("wallet")
			->join("countries country", " country.iso = wallet.is_country", "left")
			->join("users u", "u.id = wallet.user_id ");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("wallet.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("wallet.is_country", $countryCode);
			}
			
            
			
			$this->datatables->where('u.group_id', 3);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			$this->datatables->edit_column('flag', '$1__$2', 'id, flag');
		
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
		
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
       $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('customer wallet');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('cash'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('description'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('type'));
					
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:F1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->wallet_model->getALLVendorE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->fname);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->lname);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->cash);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->description);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->type);
						
						                       
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
                    $filename = 'customerwallet_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function owner($action=false){
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
		
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('owner')));
        $meta = array('page_title' => lang('owner'), 'bc' => $bc);
		
        $this->page_construct('wallet/owner', $meta, $this->data);
    }
    function getOwner(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('wallet')}.id as id, {$this->db->dbprefix('wallet')}.created as created_on,  u.first_name as fname, u.last_name as lname, ( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 1 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_cash,
			
			( CASE WHEN {$this->db->dbprefix('wallet')}.wallet_type = 2 THEN {$this->db->dbprefix('wallet')}.cash ELSE 0 END) as wallet_credit,
			 {$this->db->dbprefix('wallet')}.description as description,  {$this->db->dbprefix('wallet')}.flag as flag, country.name as instance_country
			")
            ->from("wallet")
			->join("countries country", " country.iso = wallet.is_country", "left")
			->join("users u", "u.id = wallet.user_id ");
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("wallet.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("wallet.is_country", $countryCode);
			}
			
           
			
			$this->datatables->where('u.group_id', 2);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('wallet')}.created) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			$this->datatables->edit_column('flag', '$1__$2', 'id, flag');
		
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
		
    }
	
	
	function owner_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('customer wallet');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('first_name'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('last_name'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('cash'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('description'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('type'));
					
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:F1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->wallet_model->getALLOwnerE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->created);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->fname);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->lname);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->cash);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->description);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->type);
						
						                       
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
                    $filename = 'customerwallet_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	// initialized cURL Request
	private function get_curl_handle($payment_id, $amount)  {
		
        $url = 'https://api.razorpay.com/v1/payments/'.$payment_id.'/capture';
        $key_id = RAZOR_KEY_ID;
        $key_secret = RAZOR_KEY_SECRET;
        $fields_string = "amount=$amount";
        //cURL Request
        $ch = curl_init();
		
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $key_id.':'.$key_secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        //curl_setopt($ch, CURLOPT_CAINFO, site_url().'/ca-bundle.crt');
		
        return $ch;
    }  
	
	public function success() {
		
		$meta = array('page_title' => lang('Razorpay Success | TechArise'), 'bc' => $bc);
		$this->page_construct('wallet/success', $meta, $this->data);

	}
	public function failed() {
		
		$meta = array('page_title' => lang('Razorpay Failed | TechArise'), 'bc' => $bc);
		$this->page_construct('wallet/failed', $meta, $this->data);

	}
	
	function razorpay_addmoney($group_id){
		
		
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
		$group_id = $group_id ? $group_id : $this->input->post('group_id');
		$user_id = $_GET['user_id'] ? $_GET['user_id'] : $this->input->post('merchant_amount');
		$offer = $_GET['offer'] ? $_GET['offer'] : $this->input->post('merchant_amount');
		$paid_amount = $_GET['amount'] ? $_GET['amount'] : $this->input->post('merchant_amount');
		
		//$payment = $this->account_model->getDriverPaymentGateway($id);	
	
		$user_data = $this->site->get_user($user_id, $countryCode);
		
				if (!empty($this->input->post('razorpay_payment_id')) && !empty($this->input->post('merchant_order_id'))) {
					
					$group_id = $this->input->post('group_id');
					$user_id = $this->input->post('user_id');
					$offer = $this->input->post('offer');
					$paid_amount = $this->input->post('paid_amount');
					
						$razorpay_payment_id = $this->input->post('razorpay_payment_id');
						$merchant_order_id = $this->input->post('merchant_order_id');
						$currency_code = 'INR';
						$amount = round($this->input->post('paid_amount'));
						$success = false;
						$error = '';
						try { 
					           
							$ch = $this->get_curl_handle($razorpay_payment_id, $amount.'00');
							//execute post
							$result = curl_exec($ch);
							
							$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
							if ($result === false) {
								
								$success = false;
								$error = 'Curl error: '.curl_error($ch);
							} else {
								$response_array = json_decode($result, true);
							    
									//Check success response
									if ($http_status === 200) {
										$success = true;
										
									} else {
										
										$success = false;
										if (!empty($response_array['error']['code'])) {
											$error = $response_array['error']['code'].':'.$response_array['error']['description'];
										} else {
											$error = 'RAZORPAY_ERROR:Invalid Response <br/>'.$result;
										}
									}
							}
							//close connection
							curl_close($ch);
						} catch (Exception $e) {
							
							$success = false;
							$error = 'OPENCART_ERROR:Request to Razorpay Failed';
						}
						
						if ($success == true) {
							
							$data_array = array(
								'user_id' => $user_id,
								'wallet_type' => 1,
								'flag' => 6, 
								'cash' => $paid_amount,
								'description' => 'addMoney to backend',
								'created' => date('Y-m-d H:i:s'),
								'is_country' => $countryCode,
								'join_id' => $offer != 0 ? $offer : 0,
								'join_table' => $offer != 0 ? 'walletoffer' : '',
							);
							
							$insert = $this->wallet_model->addMoney($data_array);	
							if($insert == TRUE){
								$user_data = $this->site->get_user($user_id, $ountryCode);
								$sms_message = $user_data->first_name.' your addmoney successful added wallet';
								$sms_phone = $user_data->country_code.$driver_data->mobile;
								$sms_country_code = $user_data->country_code;
					
								$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
								
								$notification['title'] = 'Wallet Addmoney - Backend';
								$notification['message'] = $user_data->first_name.' your addmoney successful added wallet';
								
								$notification['user_type'] = $group_id;
								$notification['user_id'] = $user_id;
								$this->site->insertNotification($notification, $countryCode);
								
								
								$this->session->set_flashdata('message', lang("addmoney success"));
								if($group_id == 1 || $group_id == 2){
									admin_redirect('wallet/admin');	
								}elseif($group_id == 3){
									admin_redirect('wallet/vendor');	
								}elseif($group_id == 4){
									admin_redirect('wallet/driver');	
								}elseif($group_id == 5){
									admin_redirect('wallet/customer');	
								}
								
							}
						} else {
							$data_array = array(
								'user_id' => $user_id,
								'wallet_type' => 1,
								'flag' => 6, 
								'cash' => $amount,
								'description' => 'addMoney to backend',
								'created' => date('Y-m-d H:i:s'),
								'is_country' => $countryCode,
								'join_id' => $offer != 0 ? $offer : 0,
								'join_table' => $offer != 0 ? 'walletoffer' : '',
							);	
							$this->session->set_flashdata('error', lang("addmoney failed"));
							if($group_id == 1 || $group_id == 2){
								admin_redirect('wallet/admin');	
							}elseif($group_id == 3){
								admin_redirect('wallet/vendor');	
							}elseif($group_id == 4){
								admin_redirect('wallet/driver');	
							}elseif($group_id == 5){
								admin_redirect('wallet/customer');	
							}
						}
						
					}
		
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['group_id'] = $group_id;
			$this->data['user_id'] = $user_id;
			$this->data['offer'] = $offer;
			$this->data['is_country'] = $countryCode;
			//$this->data['payment_type'] = $this->account_model->getPaymentmode($countryCode);
			//$this->data['payment_gateway'] = $this->account_model->getPaymentgateway($countryCode);
			$this->data['return_url'] = admin_url().'wallet/callback';
			$this->data['surl'] = admin_url().'wallet/success';
			$this->data['furl'] = admin_url().'wallet/failed';
			$this->data['currency_code'] = 'INR';
			$this->data['paid_amount'] = $paid_amount;
			
			$this->data['user_data'] = $user_data;
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_status')));
			$meta = array('page_title' => lang('RazorPay'), 'bc' => $bc);
			$this->page_construct('wallet/razorpay_addmoney', $meta, $this->data);		
	}
	
	function addmoney($group_id){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		//$payment_gateway = $this->account_model->getPaymentgateway($countryCode);
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$this->data['group_id'] = $group_id;
		$this->data['users'] = $this->wallet_model->getUsers($group_id, $countryCode);
		$this->data['walletoffers'] = $this->wallet_model->getWalletOffer($countryCode);
		
		
		//$driver_data = $this->account_model->getDriverBYId($id, $countryCode);
		$payment_gateway = $this->site->getPaymentgateway($countryCode);
		
		$this->form_validation->set_rules('paid_amount', lang("paid_amount"), 'required');
		if ($this->form_validation->run() == true) {
			
			foreach($payment_gateway as $gateway){
				if($gateway->id == $this->input->post('payment_gateway_id')){
					$user_id = $this->input->post('user_id');
					$offer_id = $this->input->post('offer_id') ? $this->input->post('offer_id') : 0;
					$amount  = $this->input->post('paid_amount');
				
					admin_redirect('wallet/'.$gateway->code.'_addmoney/'.$group_id.'/?is_country='.$countryCode.'&user_id='.$user_id.'&offer='.$offer_id.'&amount='.$amount.'');
					die;
					
				}else{
					
				}
			}
			
			
			
		}
		
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['return_url'] = admin_url().'wallet/callback';
        $this->data['surl'] = admin_url().'wallet/success';
        $this->data['furl'] = admin_url().'wallet/failed';
        $this->data['currency_code'] = 'INR';
		$this->data['payment_gateway'] = $payment_gateway;
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('addmoney')));
        $meta = array('page_title' => lang('addmoney'), 'bc' => $bc);
        $this->page_construct('wallet/addmoney', $meta, $this->data);
    }
	
}
