<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rides extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		
        //$this->lang->admin_load('rides', $this->Settings->user_language);
        $this->lang->admin_load('common', $this->Settings->user_language);
		$this->load->library('form_validation');
		
        $this->load->admin_model('rides_model');
    }

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
        $this->sma->checkPermissions();
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('rides')));
        $meta = array('page_title' => lang('rides'), 'bc' => $bc);
		$this->data['msg'] = lang('rides');
        $this->page_construct('rides/index', $meta, $this->data);
    }
	
	function pdf($id = NULL, $view = NULL)
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
        //$this->sma->checkPermissions('index');
		//$this->load->library('Tcpdf');
        $pr_details = $this->rides_model->getRides($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
       
        $name = "ride.pdf";
        if ($view) {
			$this->data['rides'] = $pr_details;
            $this->load->view($this->theme . 'rides/pdf', $this->data);
        } else {
			
			$this->data['rides'] = $pr_details;
			//$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
			//$pdf->Write(5, 'CodeIgniter TCPDF Integration');
			//$pdf->Output('pdfexample.pdf', 'I');
            $html = $this->load->view($this->theme . 'rides/pdf', $this->data, TRUE);
            //if (! $this->Settings->barcode_img) {
               // $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            //}
            $this->sma->generate_pdf($html, $name);
        }
    }
	
    function getOnRides(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        //print_R($_GET);exit;
        $this->sma->checkPermissions('index');
		$booked_status = $_GET['status'];
        $booked_type = $_GET['booked_type'];
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
        
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.process_type, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no as booking_no,   t.number, cu.first_name as customer_name,  cu.mobile as customer_mobile, u.first_name as driver_name,  u.mobile as driver_mobile,  {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.end, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.status, country.name as instance_country ")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1 ', 'left')
			->join('users u','u.id=rides.driver_id AND u.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
            ->join('taxi t','t.id=rides.taxi_id AND t.is_edit=1 ', 'left')
			->where('rides.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
           
			if($booked_status != 0){
				$this->datatables->where('rides.status',$booked_status);
			}
			if($booked_type != 0){
				$this->datatables->where('rides.booked_type',$booked_type);
			}
            
			$this->datatables->edit_column('status', '$1__$2', 'status, id');
			
            //$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");
			
			$edit = "<a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			//$pdf = "<a href='" . admin_url('rides/pdf/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-file-pdf-o' aria-hidden='true'  style='color:#656464; font-size:18px'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/rides/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'><div class='kapplist-view1'></div></a></div>
			<div><a href=''><div class='kapplist-edit'></div></a></div>
			<div><a href=''><div class='kapplist-car'></div></a></div>
			<div><a href=''><div class='kapplist-path'></div></a></div>
			
			");*/
		//$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$pdf."</div><div>".$delete."</div>", "id");
		$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
        //$this->datatables->unset_column('id');
        $this->datatables->unset_column('id');
		
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }
	
	function track($id = NULL)
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
       $booked_status = $_GET['status']; 
		
		if($booked_status == 1){
			$msg = 'Request Ride';
		}elseif($booked_status == 2){
			$msg = 'Booked Ride';
		}elseif($booked_status == 3){
			$msg = 'Onride Ride';
		}elseif($booked_status == 4){
			$msg = 'Waiting Ride';
		}elseif($booked_status == 5){
			$msg = 'Completed Ride';
		}elseif($booked_status == 6){
			$msg = 'Cancelled Ride';
		}elseif($booked_status == 7){
			$msg = 'Ride Later Ride';
		}elseif($booked_status == 8){
			$msg = 'Ride Rejected';
		}elseif($booked_status == 9){
			$msg = 'Incomplete';
		}elseif($booked_status == 10){
			$msg = 'Next Ride';
		}
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['status'] = $booked_status;
		$this->data['rides'] = $this->rides_model->getRides($id, $countryCode);
		/*echo '<pre>';
		print_r($this->data['rides']);
		die;*/
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'tracking'));
        $meta = array('page_title' => 'tracking', 'bc' => $bc);
		$this->data['msg'] = $msg;
        $this->page_construct('rides/tracking', $meta, $this->data);
    }
	
	function rides_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Rides');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('Booking Timestamp'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('Trip Code'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('Registration number'));
					$this->excel->getActiveSheet()->SetCellValue('D1', lang('Customer Name'));
					$this->excel->getActiveSheet()->SetCellValue('E1', lang('Customer Mobile'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('Driver Name'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('Driver Mobile'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('Pickup Location'));
					$this->excel->getActiveSheet()->SetCellValue('I1', lang('Pickup Timing'));
					$this->excel->getActiveSheet()->SetCellValue('J1', lang('Dropoff Location'));
					$this->excel->getActiveSheet()->SetCellValue('K1', lang('Dropoff Timing'));
                   
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
					$res = $this->rides_model->getALLRides($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->booking_timing);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->booking_no);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->number);
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->customer_name);
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->customer_mobile);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->driver_name);
						$this->excel->getActiveSheet()->SetCellValue('G' . $row, $value->driver_mobile);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $value->start);
						$this->excel->getActiveSheet()->SetCellValue('I' . $row, $value->ride_timing);
						$this->excel->getActiveSheet()->SetCellValue('J' . $row, $value->end);
						$this->excel->getActiveSheet()->SetCellValue('K' . $row, $value->ride_timing_end);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'rides_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
   
}
