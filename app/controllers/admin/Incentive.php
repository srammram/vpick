<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Incentive extends MY_Controller
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
		$this->load->admin_model('incentive_model');
		$this->load->admin_model('masters_model');
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	
	
	
	/*###### Driver*/
    function index($action=false){
		$this->sma->checkPermissions('incentive_list-index');
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
		
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('incentive')));
        $meta = array('page_title' => lang('incentive'), 'bc' => $bc);
		
        $this->page_construct('incentive/index', $meta, $this->data);
    }
    function getIncentive(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('incentive')}.id as id, {$this->db->dbprefix('incentive')}.created_on,  a.name as area_name, c.name as city_name, s.name as state_name, cc.name as country_name, {$this->db->dbprefix('incentive')}.type as type,
			
			(CASE WHEN {$this->db->dbprefix('incentive')}.type = '1' THEN  {$this->db->dbprefix('incentive')}.target_fare WHEN {$this->db->dbprefix('incentive')}.type = '2' THEN   {$this->db->dbprefix('incentive')}.target_ride  WHEN {$this->db->dbprefix('incentive')}.type = '3' THEN CONCAT({$this->db->dbprefix('incentive')}.target_fare, ' AND ', {$this->db->dbprefix('incentive')}.target_ride) ELSE '' END) as target_type,
			
			 {$this->db->dbprefix('incentive')}.date_type as date_type,
			 
			 (CASE WHEN {$this->db->dbprefix('incentive')}.date_type = '1' THEN  CONCAT({$this->db->dbprefix('incentive')}.start_date, ' TO ', {$this->db->dbprefix('incentive')}.end_date)  ELSE {$this->db->dbprefix('incentive')}.days END) as incentive_day_dates, {$this->db->dbprefix('incentive')}.start_time, {$this->db->dbprefix('incentive')}.end_time,  {$this->db->dbprefix('incentive')}.fare_type as fare_type,  {$this->db->dbprefix('incentive')}.fare_amount,   {$this->db->dbprefix('incentive')}.status  as status, country.name as instance_country 
			
			")
            ->from("incentive")
			->join("countries country", " country.iso = incentive.is_country", "left")
			->join("areas a", "a.id = incentive.area_id ", "left")
			->join("cities  c", "c.id = incentive.city_id ", 'left')
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("incentive.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("incentive.is_country", $countryCode);
			}
			
           
			
			//(CASE WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '1' THEN  'Alakat' WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '2' THEN  'BBQ' ELSE 'Alakat and BBQ' END) as recipe_standard,
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('incentive')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('incentive')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'id, status');
			$this->datatables->edit_column('type', '$1__$2', 'id, type');
			$this->datatables->edit_column('date_type', '$1__$2', 'id, date_type');
			$this->datatables->edit_column('fare_type', '$1__$2', 'id, fare_type');
			//$this->datatables->edit_column('is_default', '$1__$2', 'id, is_default');
			
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "<a href='" . admin_url('incentive/edit_incentive/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to Edit'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$view = "<a href='" . admin_url('incentive/view_incentive/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to View'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$view."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	function add_incentive(){
		$this->sma->checkPermissions('incentive_list-add');
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
		
		//$this->form_validation->set_rules('city_id', lang("city"), 'required|is_unique[daily_fare.city_id]');  
		$this->form_validation->set_rules('type', lang("type"), 'required');  
		
		
        if ($this->form_validation->run() == true) {
			
			if($this->input->post('continent_id') == 0){
				$is_default = 1;
				
			}else{
				$is_default = 0;
				
			}
			
			
			
            $data = array(
                'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
				'type' => $this->input->post('type'),
				'target_fare' => $this->input->post('target_fare'),
				'target_ride' => $this->input->post('target_ride'),
				'date_type' => $this->input->post('date_type'),
				'days' => $this->input->post('days'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'start_time' => $this->input->post('start_hours').':'.$this->input->post('start_minutes').':00',
				
				'end_time' => $this->input->post('end_hours').':'.$this->input->post('end_minutes').':00',
				'fare_type' => $this->input->post('fare_type'),
				'fare_amount' => $this->input->post('fare_amount'),
                'status' => 1,
				'is_edit' => 1,
				'is_default' => $is_default,
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),            );
						
        }
		
		for($i=0; $i<count($_POST['group_id']); $i++){
			$data_group[] = array(
				'group_id' => $_POST['group_id'][$i],
				'created_on' => date('Y-m-d H:i:s'),
			);
		}
		
        if ($this->form_validation->run() == true && $this->incentive_model->add_incentive($data, $data_group, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("incentive_added"));
            admin_redirect('incentive/index');
        } else {
			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('incentive/index'), 'page' => lang('incentive')), array('link' => '#', 'page' => lang('add_incentive')));
            $meta = array('page_title' => lang('add_incentive'), 'bc' => $bc);
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
            $this->page_construct('incentive/add_incentive', $meta, $this->data);
			
  
        }
    }
	
	function view_incentive($id){
		$this->sma->checkPermissions('incentive_list-view');
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
		$result = $this->incentive_model->getIncentiveby_ID($id, $countryCode);
		if($this->input->post('edit_incentive')){
		
		
		if($this->input->post('continent_id') == 0){
			$is_default = 1;
			
		}else{
			$is_default = 0;
			
		}
		$this->form_validation->set_rules('type', lang("type"), 'required');  
		
				
		
        if ($this->form_validation->run() == true) {
			
			
			
            $data = array(
                'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
				'type' => $this->input->post('type'),
				'target_fare' => $this->input->post('target_fare'),
				'target_ride' => $this->input->post('target_ride'),
				'date_type' => $this->input->post('date_type'),
				'days' => $this->input->post('days'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'start_time' => $this->input->post('start_hours').':'.$this->input->post('start_minutes').':00',
				
				'end_time' => $this->input->post('end_hours').':'.$this->input->post('end_minutes').':00',
				'fare_type' => $this->input->post('fare_type'),
				'fare_amount' => $this->input->post('fare_amount'),
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),
            );
			
			for($i=0; $i<count($_POST['group_id']); $i++){
				$data_group[] = array(
					'group_id' => $_POST['group_id'][$i],
					'created_on' => date('Y-m-d H:i:s'),
				);
			}
			
			} elseif ($this->input->post('edit_incentive')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect("incentive/edit_incentive/".$id);
			}
		}

        if ($this->form_validation->run() == true && $this->incentive_model->update_incentive($id, $data, $data_group, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("incentive_updated"));
            admin_redirect("incentive/index");
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			
			$this->data['groups'] = $this->incentive_model->getALLGroups($result->continent_id, $result->country_id, $result->zone_id, $result->state_id, $result->city_id, $result->area_id, $countryCode);
			
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_daily', $this->data);
			 $this->page_construct('incentive/view_incentive', $meta, $this->data);
        }
    }
	
    function edit_incentive($id){
		$this->sma->checkPermissions('incentive_list-edit');
		$result = $this->incentive_model->getIncentiveby_ID($id);
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
		
		if($this->input->post('edit_incentive')){
		
		
		if($this->input->post('continent_id') == 0){
			$is_default = 1;
			
		}else{
			$is_default = 0;
			
		}
		$this->form_validation->set_rules('type', lang("type"), 'required');  
		
				
		
        if ($this->form_validation->run() == true) {
			
			
			
            $data = array(
                'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
				'type' => $this->input->post('type'),
				'target_fare' => $this->input->post('target_fare'),
				'target_ride' => $this->input->post('target_ride'),
				'date_type' => $this->input->post('date_type'),
				'days' => $this->input->post('days'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'start_time' => $this->input->post('start_hours').':'.$this->input->post('start_minutes').':00',
				
				'end_time' => $this->input->post('end_hours').':'.$this->input->post('end_minutes').':00',
				'fare_type' => $this->input->post('fare_type'),
				'fare_amount' => $this->input->post('fare_amount'),
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),
            );
			
			for($i=0; $i<count($_POST['group_id']); $i++){
				$data_group[] = array(
					'group_id' => $_POST['group_id'][$i],
					'created_on' => date('Y-m-d H:i:s'),
				);
			}
			
			} elseif ($this->input->post('edit_incentive')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect("incentive/edit_incentive/".$id);
			}
		}

        if ($this->form_validation->run() == true && $this->incentive_model->update_incentive($id, $data, $data_group, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("incentive_updated"));
            admin_redirect("incentive/index");
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			
			$this->data['groups'] = $this->incentive_model->getALLGroups($result->continent_id, $result->country_id, $result->zone_id, $result->state_id, $result->city_id, $result->area_id, $countryCode);
			
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_daily', $this->data);
			 $this->page_construct('incentive/edit_incentive', $meta, $this->data);
        }
    }
	function incentive_status($status,$id){
		
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
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->incentive_model->update_incentive_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function incentive_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('incentive');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('area'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('city'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('state'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('country'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('type'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('target'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('days_or_dates'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('start_time'));
					$this->excel->getActiveSheet()->SetCellValue('I1', lang('end_time'));
					$this->excel->getActiveSheet()->SetCellValue('J1', lang('fare_type'));
					$this->excel->getActiveSheet()->SetCellValue('K1', lang('fare_amount'));
					
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:K1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->incentive_model->getALLIncentiveE($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->area_name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->city_name);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->state_name);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->country_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->type);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->target_type);
						$this->excel->getActiveSheet()->SetCellValue('G' . $row, $value->incentive_day_dates);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $value->start_time);
						$this->excel->getActiveSheet()->SetCellValue('I' . $row, $value->end_time);
						$this->excel->getActiveSheet()->SetCellValue('J' . $row, $value->fare_type);
						$this->excel->getActiveSheet()->SetCellValue('K' . $row, $value->fare_amount);
						                       
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
                    $filename = 'daily_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function group($action = NULL)
    {
		$this->sma->checkPermissions('incentive_group-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('group')));
        $meta = array('page_title' => lang('group'), 'bc' => $bc);
        $this->page_construct('incentive/group', $meta, $this->data);
    }
    function getGroup(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('incentive_group')}.id as id, {$this->db->dbprefix('incentive_group')}.name, {$this->db->dbprefix('incentive_group')}.status as status, country.name as instance_country ")
            ->from("incentive_group")
			->join("countries country", " country.iso = incentive_group.is_country", "left")
			->where('incentive_group.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("incentive_group.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("incentive_group.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('incentive/edit_group/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/incentive_group/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_group(){
		$this->sma->checkPermissions('incentive_group-add');
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
        $this->form_validation->set_rules('name', lang("name"), 'is_unique[incentive_group.name]');
       
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
                'status' => 1,
				'created_on' => date('Y-m-d H:i:s')
            );
           for($i=0; $i<count($_POST['user_id']); $i++){
				$data_user[] = array(
					'user_id' => $_POST['user_id'][$i],
					'status' => 1,
					'created_on' => date('Y-m-d H:i:s')
				);   
		   }
        }
		
        if ($this->form_validation->run() == true && $this->incentive_model->add_group($data, $data_user, $countryCode)){
			
            $this->session->set_flashdata('message', lang("group_added"));
            admin_redirect('incentive/group');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('incentive/group'), 'page' => lang('group')), array('link' => '#', 'page' => lang('add_group')));
            $meta = array('page_title' => lang('add_group'), 'bc' => $bc);
			$this->data['drivers'] = $this->incentive_model->getAllDrivers($countryCode);
			$this->data['continents'] = $this->masters_model->getALLContinents();
            $this->page_construct('incentive/add_group', $meta, $this->data);
        }
    }
    function edit_group($id){
		$this->sma->checkPermissions('incentive_group-edit');
		$result = $this->incentive_model->getGroupby_ID($id);
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');
		 
        if ($this->input->post('name') !== $result->name) {
            $this->form_validation->set_rules('name', lang("name"), 'is_unique[incentive_group.name]');
        }
		
		
		
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
                'status' => 1,
				'created_on' => date('Y-m-d H:i:s')
            );
           for($i=0; $i<count($_POST['user_id']); $i++){
				$data_user[] = array(
					'user_id' => $_POST['user_id'][$i],
					'status' => 1,
					'created_on' => date('Y-m-d H:i:s')
				);   
		   }
			
        }
		
		
        if ($this->form_validation->run() == true && $this->incentive_model->update_group($id,$data, $data_user, $countryCode)){
			
            $this->session->set_flashdata('message', lang("group_updated"));
            admin_redirect('incentive/group');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('incentive/group'), 'page' => lang('group')), array('link' => '#', 'page' => lang('group')));
            $meta = array('page_title' => lang('edit_group'), 'bc' => $bc);
            $this->data['group'] = $result;
			$this->data['drivers'] = $this->incentive_model->getAllDrivers($countryCode);
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
            $this->page_construct('incentive/edit_group', $meta, $this->data);
        }
    }
    function group_status($status,$id){
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
		if($status=='activate'){
			$data['status'] = 1;
		}
		$this->incentive_model->update_group_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function getCountry_bycontinent(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $continent_id = $this->input->post('continent_id');
        $data = $this->masters_model->getCountry_bycontinent($continent_id);
		$group = $this->incentive_model->getGroup_bycontinent($continent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getZone_bycountry(){
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
        $data = $this->masters_model->getZone_bycountry($country_id);
		$group = $this->incentive_model->getGroup_bycountry($country_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getState_byzone(){
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
        $data = $this->masters_model->getState_byzone($zone_id);
		$group = $this->incentive_model->getGroup_byzone($zone_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getCity_bystate(){
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
        $data = $this->masters_model->getCity_bystate($state_id);
		$group = $this->incentive_model->getGroup_bystate($state_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getArea_bycity(){
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
        $data = $this->masters_model->getArea_bycity($city_id);
		$group = $this->incentive_model->getGroup_bycity($city_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	function getGroup_byarea(){
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
        //$data = $this->masters_model->getArea_bycity($city_id);
		$group = $this->incentive_model->getGroup_byarea($area_id, $countryCode);
        $options = array();
        
		if($group){
            foreach($group as $i => $gow){
                $options['group'][$i]['id'] = $gow->id;
                $options['group'][$i]['text'] = $gow->name;
            }
        }
        echo json_encode($options);exit;
    }
	
	
}
