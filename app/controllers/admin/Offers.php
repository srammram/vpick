<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Offers extends MY_Controller
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
		$this->load->admin_model('offers_model');
		$this->load->admin_model('masters_model');
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	
	
	
	/*###### Offer*/
    function index($action=false){
		$this->sma->checkPermissions('offers_list-index');
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
		
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('offers')));
        $meta = array('page_title' => lang('offers'), 'bc' => $bc);
		
        $this->page_construct('offers/index', $meta, $this->data);
    }
	
    function getOffers(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('offers')}.id as id, {$this->db->dbprefix('offers')}.created_on,  a.name as area_name, c.name as city_name, s.name as state_name, cc.name as country_name, {$this->db->dbprefix('offers')}.offer_name, {$this->db->dbprefix('offers')}.offer_code,
						
			 {$this->db->dbprefix('offers')}.date_type as date_type,
			 
			 (CASE WHEN {$this->db->dbprefix('offers')}.date_type = '1' THEN  CONCAT({$this->db->dbprefix('offers')}.start_date, ' TO ', {$this->db->dbprefix('offers')}.end_date)  ELSE {$this->db->dbprefix('offers')}.days END) as offers_day_dates, {$this->db->dbprefix('offers')}.start_time, {$this->db->dbprefix('offers')}.end_time,  {$this->db->dbprefix('offers')}.status  as status, country.name as instance_country 
			
			")
            ->from("offers")
			->join("countries country", " country.iso = offers.is_country", "left")
			->join("areas a", "a.id = offers.area_id ", "left")
			->join("cities  c", "c.id = offers.city_id ", 'left')
			->join("states s", "s.id = c.state_id ", 'left')
			->join("zones z", "z.id = s.zone_id ", 'left')
			->join("countries cc", "cc.id = z.country_id ", 'left');
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("offers.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("offers.is_country", $countryCode);
			}
			
           
			
			//(CASE WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '1' THEN  'Alakat' WHEN {$this->db->dbprefix('recipe')}.recipe_standard = '2' THEN  'BBQ' ELSE 'Alakat and BBQ' END) as recipe_standard,
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('offers')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('offers')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'id, status');
			$this->datatables->edit_column('date_type', '$1__$2', 'id, date_type');
			//$this->datatables->edit_column('is_default', '$1__$2', 'id, is_default');
			
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			//$edit = "<a href='" . admin_url('offers/edit_offers/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to Edit'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$edit = "";
			$view = "<a href='" . admin_url('offers/view_offers/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to View'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$view."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	function add_offers(){
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
		$this->form_validation->set_rules('offer_name', lang("offer_name"), 'required');  
		
		
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
				'group_id' => $this->input->post('group_id'),
				'offer_name' => $this->input->post('offer_name'),
				'offer_code' => $this->input->post('offer_code'),
				'date_type' => $this->input->post('date_type'),
				'days' => $this->input->post('days'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'start_time' => $this->input->post('start_hours').':'.$this->input->post('start_minutes').':00',
				
				'end_time' => $this->input->post('end_hours').':'.$this->input->post('end_minutes').':00',
				'offer_description' => $this->input->post('offer_description'),
				'offer_limit' => $this->input->post('offer_limit'),
				'offer_fare_type' => $this->input->post('offer_fare_type'),
				'offer_fare' => $this->input->post('offer_fare_type') == 2 ? '' : $this->input->post('offer_fare'),
				'other_product' => $this->input->post('offer_fare_type') != 2 ? '' : $this->input->post('other_product'),
				'maximum_amount' => $this->input->post('maximum_amount'),
                'status' => 1,
				'is_edit' => 1,
				'is_default' => $is_default,
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),            
				);
				
						
        }
		
		
        if ($this->form_validation->run() == true && $this->offers_model->add_offers($data, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("offers_added"));
            admin_redirect('offers/index');
        } else {
			$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('offers/index'), 'page' => lang('offers')), array('link' => '#', 'page' => lang('add_offers')));
            $meta = array('page_title' => lang('add_offers'), 'bc' => $bc);
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['groups'] = $this->offers_model->getGroups();
            $this->page_construct('offers/add_offers', $meta, $this->data);
			
  
        }
    }
	
	function view_offers($id){
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
		$result = $this->offers_model->getOffersby_ID($id, $countryCode);
		

			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			$this->data['groups'] = $this->offers_model->getGroups();
			//$this->data['groups'] = $this->incentive_model->getALLGroups($result->continent_id, $result->country_id, $result->zone_id, $result->state_id, $result->city_id, $result->area_id, $countryCode);
			
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_daily', $this->data);
			 $this->page_construct('offers/view_offers', $meta, $this->data);
        
    }
	
    function edit_offers($id){
		$result = $this->offers_model->getOffersby_ID($id);
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
		
		if($this->input->post('edit_offers')){
		
		
		if($this->input->post('continent_id') == 0){
			$is_default = 1;
			
		}else{
			$is_default = 0;
			
		}
		$this->form_validation->set_rules('offer_name', lang("offer_name"), 'required');  
				
		
        if ($this->form_validation->run() == true) {
			
			
			  $data = array(
                'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
				'zone_id' => $this->input->post('zone_id'),
				'state_id' => $this->input->post('state_id'),
				'city_id' => $this->input->post('city_id'),
				'area_id' => $this->input->post('area_id'),
				'group_id' => $this->input->post('group_id'),
				'offer_name' => $this->input->post('offer_name'),
				'date_type' => $this->input->post('date_type'),
				'days' => $this->input->post('days'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'start_time' => $this->input->post('start_hours').':'.$this->input->post('start_minutes').':00',
				
				'end_time' => $this->input->post('end_hours').':'.$this->input->post('end_minutes').':00',
				'offer_description' => $this->input->post('offer_description'),
				'offer_limit' => $this->input->post('offer_limit'),
				'offer_fare_type' => $this->input->post('offer_fare_type'),
				'offer_fare' => $this->input->post('offer_fare_type') == 2 ? '' : $this->input->post('offer_fare'),
				'other_product' => $this->input->post('offer_fare_type') != 2 ? '' : $this->input->post('other_product'),
				'maximum_amount' => $this->input->post('maximum_amount'),
                'status' => 1,
				'is_edit' => 1,
				'is_default' => $is_default,
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('user_id'),            
				);
           
			
			
			} elseif ($this->input->post('edit_offers')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect("offers/edit_offers/".$id);
			}
		}

        if ($this->form_validation->run() == true && $this->offers_model->update_offers($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("offers_updated"));
            admin_redirect("offers/index");
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($result->country_id);
			$this->data['states'] = $this->masters_model->getState_byzone($result->zone_id);
			$this->data['citys'] = $this->masters_model->getCity_bystate($result->state_id);
			$this->data['areas'] = $this->masters_model->getArea_bycity($result->city_id);
			
			$this->data['groups'] = $this->offers_model->getGroups();
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            //$this->load->view($this->theme . 'locations/edit_daily', $this->data);
			 $this->page_construct('offers/edit_offers', $meta, $this->data);
        }
    }
	function offers_status($status,$id){
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
        $this->offers_model->update_offers_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function offers_actions($wh = NULL)
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

        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
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

        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
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

        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
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

        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
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

        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['location'][$k]['id'] = $row->id;
                $options['location'][$k]['text'] = $row->name;
            }
        }
		
		
        echo json_encode($options);exit;
    }
	
	
	
}
