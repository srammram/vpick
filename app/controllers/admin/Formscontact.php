<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Formscontact extends MY_Controller
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
		$this->load->admin_model('masters_model');
		$this->load->admin_model('formscontact_model');
    }
	

	
	/*###### Formscontact*/
    function index($action = NULL)
    {
		$this->sma->checkPermissions('formscontact-index');
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
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Formscontact')));
        $meta = array('page_title' => lang('Formscontact'), 'bc' => $bc);
        $this->page_construct('forms/contact', $meta, $this->data);
    }
	 function formscontact_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Formscontact');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('mobile_number'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('email'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:C1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->formscontact_model->getALLFormscontact($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->mobile_number);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->email_address);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'notification_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
    function getFormscontact(){
		
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$sdate = $this->input->get('sdate');
		$edate = $this->input->get('edate');
		
		//$countryCode = $this->session->userdata('group_id') == 1 && $countryCode != '' ? $this->input->post('is_country') : $countryCode;
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('enquiry_forms')}.id as id, {$this->db->dbprefix('enquiry_forms')}.name, {$this->db->dbprefix('enquiry_forms')}.mobile_number, {$this->db->dbprefix('enquiry_forms')}.email_address, {$this->db->dbprefix('enquiry_forms')}.description, country.name as instance_country ")
            ->from("enquiry_forms")
			->join("countries country", " country.iso = enquiry_forms.is_country", "left")
			->where("enquiry_forms.forms_type = 0");
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('enquiry_forms')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('enquiry_forms')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("enquiry_forms.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("enquiry_forms.is_country", $countryCode);
			}
			//->edit_column('is_default', '$1', 'is_default')
            // ->edit_column('status', '$1__$2', 'status, id')
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
    }

}
