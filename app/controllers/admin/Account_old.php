<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller
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
		$this->load->admin_model('account_model');
    }
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	function index(){
		$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('account')));
		$meta = array('page_title' => lang('account'), 'bc' => $bc);
		$this->page_construct('account/index', $meta, $this->data);

	}
	
	function trip($action=false){
		$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('account')), array('link' => '#', 'page' => lang('Per Trip Accounting')));
        $meta = array('page_title' => lang('Per Trip Accounting'), 'bc' => $bc);
		//$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver);
        $this->page_construct('account/trip', $meta, $this->data);
    }
	
	function getTrip(){
        //print_R($_GET);exit;
       // $this->sma->checkPermissions('index');
		//$booked_status = $_GET['status'];
        //$booked_type = $_GET['booked_type'];
        
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_no as booking_no,   du.first_name as driver_name,  vu.first_name as vendor_name, cu.mobile as customer_mobile, rp.total_fare as total_fare, 'N/A', 'Nill', pm.name as payment_name, rp.paid_amount as paid_amount")
            ->from("rides")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1', 'left')
			->join('user_profile v','v.user_id=rides.vendor_id AND v.is_edit=1', 'left')
			->join('users du','du.id=rides.driver_id AND du.is_edit=1', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1', 'left')
			->join('users vu','vu.id=rides.vendor_id AND vu.is_edit=1', 'left')
			->join('ride_payment rp','rp.ride_id=rides.id', 'left')
            ->join('payment_mode pm','pm.id=rp.payment_type', 'left')
			->where('rides.status', 5)
			->where('rides.is_delete', 0);
			
			
            //$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");
			
			//$edit = "<a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-path'></div></a>";
			
			//$pdf = "<a href='" . admin_url('rides/pdf/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-file-pdf-o' aria-hidden='true'  style='color:#656464'></i></a>";
			
			//$delete = "<a href='" . admin_url('welcome/delete/rides/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'><div class='kapplist-view1'></div></a></div>
			<div><a href=''><div class='kapplist-edit'></div></a></div>
			<div><a href=''><div class='kapplist-car'></div></a></div>
			<div><a href=''><div class='kapplist-path'></div></a></div>
			
			");*/
			$view = "<a href='" . admin_url('acount/tripview/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-path'></div></a>";
		//$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$pdf."</div><div>".$delete."</div>", "id");
		$this->datatables->add_column("Actions", "<div></div>", "id");
        //$this->datatables->unset_column('id');
        $this->datatables->unset_column('id');
		
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }

	
	/*###### Driver*/
    function driver($action=false){
$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
		$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver);
        $this->page_construct('account/driver', $meta, $this->data);
    }
    function getDriver(){
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile, {$this->db->dbprefix('users')}.active as active, IFNULL(dp.ride_end_date, '0000-00-00') as ride_end_date, IFNULL(dp.duration_date, '0000-00-00') as duration_date, IFNULL(dp.driver_status, 0) as driver_status, IFNULL(dp.payment_status, 0) as payment_status, IFNULL(dp.admin_status, 0) as admin_status
			
			")
            ->from("users")
			->join("driver_payment dp", "dp.driver_id = users.id AND dp.is_edit != 0")
			->where("users.group_id", $group_id);
			if($this->Vendor == $this->session->userdata('group_id')){
				$this->datatables->where("users.parent_id", $this->session->userdata("user_id"));
			}
			
			if(!empty($driver_id)){
				$this->datatables->where("users.id", $driver_id);	
			}
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where('DATE(created_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where('DATE(created_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			
			
			$this->datatables->group_by("users.id");
            $this->datatables->edit_column('active', '$1__$2', 'id, active');
			$this->datatables->edit_column('driver_status', '$1__$2', 'id, driver_status');
			$this->datatables->edit_column('payment_status', '$1__$2', 'id, payment_status');
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "<a href='" . admin_url('account/driver_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	function driver_view($id){
$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['user'] = $this->account_model->getDriverBYId($id);
		$this->data['payment'] = $this->account_model->getPaymentBYId($id);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver_view'), 'bc' => $bc);
        $this->page_construct('account/driver_view', $meta, $this->data);
    }
	
	
	function getDriverpayment(){
		
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('driver_payment')}.id as id, {$this->db->dbprefix('driver_payment')}.ride_start_date, {$this->db->dbprefix('driver_payment')}.ride_end_date, {$this->db->dbprefix('driver_payment')}.total_ride, {$this->db->dbprefix('driver_payment')}.total_ride_amount, pm.name as payment_name, {$this->db->dbprefix('driver_payment')}.payment_date, {$this->db->dbprefix('driver_payment')}.transaction_no, {$this->db->dbprefix('driver_payment')}.admin_status as admin_status	
			")
            ->from("driver_payment")
			->join("payment_mode pm", 'pm.id = driver_payment.payment_id', 'left')
			->where("driver_payment.is_edit !=", 0)
			->where("driver_payment.driver_id", $driver_id);
			
			
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	function complete_payment($action=false){
$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('complete_payment')));
        $meta = array('page_title' => lang('complete_payment'), 'bc' => $bc);
		$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver);
        $this->page_construct('account/complete_payment', $meta, $this->data);
    }
	
	function getCompletepayment(){
		
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('driver_payment')}.id as id, {$this->db->dbprefix('driver_payment')}.ride_start_date, {$this->db->dbprefix('driver_payment')}.ride_end_date, {$this->db->dbprefix('driver_payment')}.total_ride, {$this->db->dbprefix('driver_payment')}.total_ride_amount, {$this->db->dbprefix('driver_payment')}.payment_percentage, {$this->db->dbprefix('driver_payment')}.payment_amount, {$this->db->dbprefix('driver_payment')}.paid_amount, {$this->db->dbprefix('driver_payment')}.balance_amount, pm.name as payment_name, {$this->db->dbprefix('driver_payment')}.payment_date, {$this->db->dbprefix('driver_payment')}.transaction_no, {$this->db->dbprefix('driver_payment')}.admin_status as admin_status	
			")
            ->from("driver_payment")
			->join("payment_mode pm", 'pm.id = driver_payment.payment_id', 'left')
			->where("driver_payment.is_edit", 0);
			if(!empty($driver_id)){
				$this->datatables->where("driver_payment.driver_id", $driver_id);	
			}
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where('DATE(payment_date) >=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where('DATE(payment_date) <=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	
	function driver_to_admin($status, $id){
		$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$driver_data = $this->account_model->getDriverBYId($id);
		
		if($status == 'active'){
			$this->form_validation->set_rules('paid_amount', lang("paid_amount"), 'required');
			if ($this->form_validation->run() == true) {
				
				$tax = $this->account_model->getTaxdefault();
				$unit_amount = $this->input->post('payment_amount');
				$net_amount  = ($unit_amount/(($tax->percentage/100)+1)); 
				$tax_amount = $unit_amount - $net_amount;
				$update = array(
					'paid_amount' => $this->input->post('paid_amount'),
					'balance_amount' => $this->input->post('balance_amount'),
					'payment_date' => $this->input->post('payment_date'),
					'payment_status' => $this->input->post('payment_status'),
					'payment_id' => $this->input->post('payment_id'),
					'unit_amount' => $unit_amount,
					'tax_amount' => $tax_amount,
					'net_amount' => $net_amount,
					'tax_name' => $tax->tax_name,
					'tax_percentage' => $tax->percentage,
					'payment_amount' => $this->input->post('payment_amount'),
					'offline_paid' => 1,
					'driver_status' => 1,
					'is_edit' => 2,
					'admin_status' => 2
				);
				
				
			}
			if ($this->form_validation->run() == true && $this->account_model->updateCashpayment($this->input->post('driver_payment_id'), $update)){
				$sms_message = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
				$sms_phone = $driver_data->country_code.$driver_data->mobile;
				$sms_country_code = $driver_data->country_code;
	
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$notification['title'] = 'Driver Payment Offline - Backend';
				$notification['message'] = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
				$notification['user_type'] = 2;
				$notification['user_id'] = $driver_data->id;
				$this->site->insertNotification($notification);
				
				
				$this->session->set_flashdata('message', lang("admin has been verified driver payment status"));
				admin_redirect('account/driver');
			}
		}
		
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		$this->data['payment_type'] = $this->account_model->getPaymentmode();
		
		
		$this->data['payment'] = $this->account_model->getPayment($id);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_status')));
        $meta = array('page_title' => lang('driver_status'), 'bc' => $bc);
        $this->page_construct('account/driver_to_admin', $meta, $this->data);
    }
	
	function getBank(){
		$res = array();
		$account_no = $this->input->post('account_no');	
		$q = $this->db->select('bank_name, branch_name, ifsc_code')->where('account_no', $account_no)->get('admin_bank');
		if($q->num_rows()>0){
			$res = $q->row();			
		}
		echo json_encode($res);
	}
	function admin_to_driver($status, $id){
		$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$driver_data = $this->account_model->getDriverBYId($id);
		
		
		if($status == 'deposit'){
			$this->form_validation->set_rules('admin_account_no', lang("admin_account_no"), 'required');
			if ($this->form_validation->run() == true) {
				$update = array(
					'admin_account_no' => $this->input->post('admin_account_no'),
					'deposit_bank_name' => $this->input->post('deposit_bank_name'),
					'deposit_branch_name' => $this->input->post('deposit_branch_name'),
					'deposit_ifscode' => $this->input->post('deposit_ifscode'),
					'deposit_date' => $this->input->post('deposit_date'),
					'transaction_no' => $this->input->post('transaction_no'),
					'transaction_date' => $this->input->post('deposit_date'),
					'admin_status' => 1,
					'is_edit' => 0
				);
				
				if ($_FILES['transaction_image']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/transaction/';
					$config['allowed_types'] = $this->pdf_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('transaction_image')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$transaction_image = $this->upload->file_name;
					$update['transaction_image'] = 'document/transaction/'.$transaction_image;
					$config = NULL;
				}
				
			}
			
			if ($this->form_validation->run() == true && $this->account_model->updateVerifiedpayment($this->input->post('driver_payment_id'), $update, $id)){
				$sms_message = $driver_data->first_name.' your payment has been paid. Waiting for admin approval process';
				$sms_phone = $driver_data->country_code.$driver_data->mobile;
				$sms_country_code = $driver_data->country_code;
	
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$notification['title'] = 'Admin Payment Offline - Backend';
				$notification['message'] = $driver_data->first_name.' your payment has been paid. Waiting for admin approval process';
				$notification['user_type'] = 2;
				$notification['user_id'] = $driver_data->id;
				$this->site->insertNotification($notification);
				
				$this->session->set_flashdata('message', lang("driver has been direct paid success"));
				admin_redirect('account/driver');
			}
		}elseif($status == 'credit'){
			$this->form_validation->set_rules('admin_account_no', lang("admin_account_no"), 'required');
			if ($this->form_validation->run() == true) {
				$update = array(
					'admin_account_no' => $this->input->post('admin_account_no'),
					'deposit_bank_name' => $this->input->post('deposit_bank_name'),
					'deposit_branch_name' => $this->input->post('deposit_branch_name'),
					'deposit_ifscode' => $this->input->post('deposit_ifscode'),
					'deposit_date' => $this->input->post('deposit_date'),
					'transaction_no' => $this->input->post('transaction_no'),
					'transaction_date' => $this->input->post('deposit_date'),
					'admin_status' => 1,
					'is_edit' => 0
				);
				
				
				
			}
			if ($this->form_validation->run() == true && $this->account_model->updateVerifiedpayment($this->input->post('driver_payment_id'), $update, $id)){
				$sms_message = $driver_data->first_name.' your payment has been paid. Waiting for admin approval process';
				$sms_phone = $driver_data->country_code.$driver_data->mobile;
				$sms_country_code = $driver_data->country_code;
	
				$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
				
				$notification['title'] = 'Admin Payment Offline - Backend';
				$notification['message'] = $driver_data->first_name.' your payment has been paid. Waiting for admin approval process';
				$notification['user_type'] = 2;
				$notification['user_id'] = $driver_data->id;
				$this->site->insertNotification($notification);
				
				$this->session->set_flashdata('message', lang("driver has been direct paid success"));
				admin_redirect('account/driver');
			}
		}
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		 $this->data['status'] = $status;
		$this->data['payment_type'] = $this->account_model->getPaymentmode();
		$this->data['banks'] = $this->account_model->getBanks();
		$this->data['payment'] = $this->account_model->getPayment($id);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('admin_status')));
        $meta = array('page_title' => lang('admin_status'), 'bc' => $bc);
        $this->page_construct('account/admin_to_driver', $meta, $this->data);
    }
	/*###### Vendor*/
    function vendor($action=false){
$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('vendor')));
        $meta = array('page_title' => lang('vendor'), 'bc' => $bc);
        $this->page_construct('account/vendor', $meta, $this->data);
    }
	
	function getVendor(){
		$this->site->users_logs($this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$group_id = $this->Vendor;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile, {$this->db->dbprefix('users')}.active as active, IFNULL(dp.ride_end_date, '0000-00-00') as ride_end_date, IFNULL(dp.driver_status, 0) as driver_status, IFNULL(dp.payment_status, 0) as payment_status, IFNULL(dp.admin_status, 0) as admin_status
			
			")
            ->from("users")
			->join("driver_payment dp", "dp.vendor_id = users.id AND dp.is_edit = 1", 'left')
			->where("users.group_id", $group_id);
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where('DATE(created_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where('DATE(created_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			
			
			$this->datatables->group_by("users.id");
            $this->datatables->edit_column('active', '$1__$2', 'id, active');
			$this->datatables->edit_column('driver_status', '$1__$2', 'id, driver_status');
			$this->datatables->edit_column('payment_status', '$1__$2', 'id, payment_status');
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			
		
			
			$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('people/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	
}
