<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nightaudit extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
           // $this->session->set_flashdata('warning', lang('access_denied'));
          //  redirect('admin');
        }
		$this->lang->admin_load('sma', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('nightaudit_model');
        
    }

	/* Tables*/
	
	function index($dates = NULL, $warehouses_id = NULL)
    {
    	if($this->Settings->night_audit_rights == 0){
    		admin_redirect('welcome');
    	}
	$this->sma->checkPermissions();
		$this->session->userdata('user_id');		
		$id = $this->session->userdata('user_id');

     	$dates = $this->input->get('dates');  
		$warehouses_id = $this->input->get('warehouses_id');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('nightaudit'), 'page' => lang('night_audit')), array('link' => '#', 'page' => lang('night_audit')));
        $meta = array('page_title' => lang('night_audit'), 'bc' => $bc);
		$warehouses = $this->site->getAllWarehouses();
		$this->data['sales'] = $this->nightaudit_model->getDataviewSales($dates, $warehouses_id); 
		$this->data['status'] = $this->nightaudit_model->checkNightaudit($dates, $warehouses_id);
		$this->data['dates'] = $this->nightaudit_model->Check_Not_Closed_Nightaudit();
		$this->data['last_date'] = $this->nightaudit_model->Last_Nightaudit();
		$group_id = $this->nightaudit_model->getUserGroupid($id);

		$this->data['p'] = $this->nightaudit_model->getGroupPermissions($group_id->group_id);
						
		$this->data['warehouses'] = $warehouses;
		
        $this->page_construct('nightaudit', $meta, $this->data);
    }
	
	function getNightauditData($dates = NULL, $warehouses_id = NULL){
		
		$dates = $this->input->get('dates');
		$warehouses_id = $this->input->get('warehouses_id');

		$Last_Nightaudit =  $this->nightaudit_model->Last_Nightaudit();

		$before_date = date('Y-m-d', strtotime($dates . ' -1 day'));
		$before_status = $this->nightaudit_model->checkbeforedate($before_date, $warehouses_id);

		$sales = $this->nightaudit_model->getDataviewSales($dates, $warehouses_id); 
		
		$status = $this->nightaudit_model->checkNightaudit($dates, $warehouses_id);

		$total_sales = 0;
		$complete_sales = 0;
		$pending_sales = 0;
		foreach($sales as $sales_row){
			$total_sales++;
			if($sales_row->sale_status == 'Closed'){
				$complete[] = $sales_row->grand_total;
				$complete_sales++;
			}elseif($sales_row->sale_status == 'Process'){
				$pending[] = $sales_row->grand_total;
				$pending_sales++;
			}
			$total[] = $sales_row->grand_total;
		}
		$complete_sales;
		$pending_sales;
		$total_amount = array_sum($total);
		$complete_amount = array_sum($complete);
		$pending_amount = array_sum($pending);
		
		$row['total_sales'] = $total_sales;	
		$row['complete_sales'] = $complete_sales;	
		$row['pending_sales'] = $pending_sales;	
		$row['total_amount'] = $total_amount ? $total_amount : 0;	
		$row['complete_amount'] = $complete_amount ? $complete_amount : 0;	
		$row['pending_amount'] = $pending_amount ? $pending_amount : 0;	
		$row['status'] = $status;	
		$row['before_status'] = $before_status;	
		
		echo json_encode($row);
		exit;
	}
	
	public function actions(){
		$this->sma->checkPermissions('index');
		$data = array(
			'nightaudit_date' => $this->input->post('nightaudit_date'),
			'warehouse_id' => $this->input->post('warehouses_id'),
			'total_sales' => $this->input->post('total_sales'),
			'total_amount' => $this->input->post('total_amount'),
			'complete_sales' => $this->input->post('complete_sales'),
			'complete_amount' => $this->input->post('complete_amount'),
			'pending_sales' => $this->input->post('pending_sales'),
			'pending_amount' => $this->input->post('pending_amount'),
			'nightaudit' => $this->input->post('nightaudit'),
			'created' => date('Y-m-d H:m:s'),
			'created_by' => $this->session->userdata('user_id'),
		);	
		
		if ($this->nightaudit_model->addNightaudit($data)) {
			
            $this->session->set_flashdata('message', lang("Night Audit process complete"));
            admin_redirect('nightaudit');
        } else {
			$this->session->set_flashdata('error', lang("Unable to Do Night Audit Now"));
            admin_redirect('nightaudit');
		}
	}

}
