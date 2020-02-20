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
	
	// callback method
    /*public function callback() {        
        if (!empty($this->input->post('razorpay_payment_id')) && !empty($this->input->post('merchant_order_id'))) {
			
            $razorpay_payment_id = $this->input->post('razorpay_payment_id');
            $merchant_order_id = $this->input->post('merchant_order_id');
            $currency_code = 'INR';
            $amount = 11000;
            $success = false;
            $error = '';
            try {   
			            
                $ch = $this->get_curl_handle($razorpay_payment_id, $amount);
                //execute post
                $result = curl_exec($ch);
				
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($result === false) {
					
                    $success = false;
                    $error = 'Curl error: '.curl_error($ch);
                } else {
					
                    $response_array = json_decode($result, true);
                    //echo "<pre>";print_r($response_array);exit;
                        //Check success response
                        if ($http_status === 200 and isset($response_array['error']) === false) {
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
            if ($success === true) {
                if(!empty($this->session->userdata('ci_subscription_keys'))) {
                    $this->session->unset_userdata('ci_subscription_keys');
                 }
                if (!$order_info['order_status_id']) {
                    admin_redirect('account/success');
                } else {
                     admin_redirect('account/success');
                }
 
            } else {
                 admin_redirect('account/failed');
            }
        } else {
            echo 'An error occured. Contact site administrator, please!';
        }
    }  */
	
	public function dashboard() {
		
		$meta = array('page_title' => lang('dashboard'), 'bc' => $bc);
		$this->page_construct('account/dashboard', $meta, $this->data);

	}
	
	public function account_owner() {
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
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		$meta = array('page_title' => lang('owner'), 'bc' => $bc);
		$this->page_construct('account/account_owner', $meta, $this->data);

	}
	
    function getAccountOwner(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('account')}.id as id, {$this->db->dbprefix('account')}.account_date, c.name as company_name, c.is_office, {$this->db->dbprefix('account')}.user_type as user_type, u.first_name, {$this->db->dbprefix('account')}.account_type, {$this->db->dbprefix('account')}.payment_mode, pg.name as payment_type, {$this->db->dbprefix('account')}.account_transaction_no, {$this->db->dbprefix('account')}.account_transaction_date, {$this->db->dbprefix('account')}.credit, {$this->db->dbprefix('account')}.debit, {$this->db->dbprefix('account')}.account_status as account_status, {$this->db->dbprefix('account')}.account_bank_status as account_bank_status, {$this->db->dbprefix('account')}.account_reconciliation as account_reconciliation, {$this->db->dbprefix('account')}.account_verify as account_verify,  country.name as instance_country 
			
			")
            ->from("account")
			->join("countries country", " country.iso = account.is_country", "left")
			->join("users u", "u.id = account.user_id ", 'left')
			->join("company c", "c.id = account.company_id ", 'left')
			->join("payment_gateway pg", "pg.id = account.payment_type ", 'left');
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("account.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("account.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("account.id");
            $this->datatables->edit_column('user_type', '$1', 'user_type');
			$this->datatables->edit_column('account_status', '$1', 'account_status');
			$this->datatables->edit_column('account_bank_status', '$1', 'account_bank_status');
			$this->datatables->edit_column('account_reconciliation', '$1', 'account_reconciliation');
			$this->datatables->edit_column('account_verify', '$1', 'account_verify');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			//$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	public function account_customer() {
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
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		$meta = array('page_title' => lang('customer'), 'bc' => $bc);
		$this->page_construct('account/account_customer', $meta, $this->data);

	}
	
    function getAccountCustomer(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('account')}.id as id, {$this->db->dbprefix('account')}.account_date, c.name as company_name, c.is_office, u.first_name, {$this->db->dbprefix('account')}.account_type, {$this->db->dbprefix('account')}.payment_mode, pg.name as payment_type, {$this->db->dbprefix('account')}.account_transaction_no, {$this->db->dbprefix('account')}.account_transaction_date, {$this->db->dbprefix('account')}.credit, {$this->db->dbprefix('account')}.debit, {$this->db->dbprefix('account')}.account_status as account_status, {$this->db->dbprefix('account')}.account_bank_status as account_bank_status, {$this->db->dbprefix('account')}.account_reconciliation as account_reconciliation, {$this->db->dbprefix('account')}.account_verify as account_verify,  country.name as instance_country 
			
			")
            ->from("account")
			->join("countries country", " country.iso = account.is_country", "left")
			->join("users u", "u.id = account.user_id ", 'left')
			->join("company c", "c.id = account.company_id ", 'left')
			->join("payment_gateway pg", "pg.id = account.payment_type ", 'left')
			->where("account.user_type", 1);
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("account.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("account.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("account.id");
            $this->datatables->edit_column('account_status', '$1', 'account_status');
			$this->datatables->edit_column('account_bank_status', '$1', 'account_bank_status');
			$this->datatables->edit_column('account_reconciliation', '$1', 'account_reconciliation');
			$this->datatables->edit_column('account_verify', '$1', 'account_verify');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			//$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	public function account_driver() {
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
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		$meta = array('page_title' => lang('driver'), 'bc' => $bc);
		$this->page_construct('account/account_driver', $meta, $this->data);

	}
	
    function getAccountDriver(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('account')}.id as id, {$this->db->dbprefix('account')}.account_date, c.name as company_name, c.is_office, u.first_name, {$this->db->dbprefix('account')}.account_type, {$this->db->dbprefix('account')}.payment_mode, pg.name as payment_type, {$this->db->dbprefix('account')}.account_transaction_no, {$this->db->dbprefix('account')}.account_transaction_date, {$this->db->dbprefix('account')}.credit, {$this->db->dbprefix('account')}.debit, {$this->db->dbprefix('account')}.account_status as account_status, {$this->db->dbprefix('account')}.account_bank_status as account_bank_status, {$this->db->dbprefix('account')}.account_reconciliation as account_reconciliation, {$this->db->dbprefix('account')}.account_verify as account_verify,  country.name as instance_country 
			
			")
            ->from("account")
			->join("countries country", " country.iso = account.is_country", "left")
			->join("users u", "u.id = account.user_id ", 'left')
			->join("company c", "c.id = account.company_id ", 'left')
			->join("payment_gateway pg", "pg.id = account.payment_type ", 'left')
			->where("account.user_type", 2);
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('account')}.account_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("account.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("account.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("account.id");
            $this->datatables->edit_column('account_status', '$1', 'account_status');
			$this->datatables->edit_column('account_bank_status', '$1', 'account_bank_status');
			$this->datatables->edit_column('account_reconciliation', '$1', 'account_reconciliation');
			$this->datatables->edit_column('account_verify', '$1', 'account_verify');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			//$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	
	public function account_settlementlist() {
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
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
		$meta = array('page_title' => lang('driver'), 'bc' => $bc);
		$this->page_construct('account/account_settlementlist', $meta, $this->data);

	}
	
    function getAccountSettlement(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('settlement')}.id as id, {$this->db->dbprefix('settlement')}.settlement_date,   {$this->db->dbprefix('settlement')}.settlement_code, fu.first_name as from_user, fc.name as from_company, fb.account_no as from_bank, {$this->db->dbprefix('settlement')}.settlement_type,  tu.first_name as to_user, tc.name as to_company, tb.account_no as to_bank, {$this->db->dbprefix('settlement')}.settlement_amount, {$this->db->dbprefix('settlement')}.settlement_status as status,    {$this->db->dbprefix('settlement')}.to_verify as verify,  country.name as instance_country 
			
			")
            ->from("settlement")
			->join("countries country", " country.iso = settlement.is_country", "left")
			->join("admin_bank fb", "fb.id = settlement.from_bank_id ", 'left')
			->join("users fu", "fu.id = settlement.from_user_id ", 'left')
			->join("company fc", "fc.id = settlement.from_company_id ", 'left')
			->join("admin_bank tb", "tb.id = settlement.to_bank_id ", 'left')
			->join("users tu", "tu.id = settlement.to_user_id ", 'left')
			->join("company tc", "tc.id = settlement.to_company_id ", 'left')
			;
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('settlement')}.settlement_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('settlement')}.settlement_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("settlement.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("settlement.is_country", $countryCode);
			}
			
			
			//$this->datatables->group_by("settlement.id");
            $this->datatables->edit_column('status', '$1', 'status');
			$this->datatables->edit_column('verify', '$1__$2', 'id, verify');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			//$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	function offline_account_verify($account_id){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$account_id = $account_id ? $account_id : $this->input->post('account_id');
		
		
		//$payment_gateway = $this->account_model->getPaymentgateway($countryCode);
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->data['account_id'] = $account_id;
		//$driver_data = $this->account_model->getDriverBYId($id, $countryCode);
		$result = $this->account_model->getOfflineaccount($account_id);
		if($this->input->post('offline_submit')){
			
			
			$this->form_validation->set_rules('account_verify', lang("account_verify"), 'required');
			
				if($this->input->post('account_verify') == 0){
					$this->session->set_flashdata('error', (validation_errors()) ? validation_errors() : lang("please select verify button"));
					admin_redirect('account/offline_account_verify/'.$account_id);
				}
				
				 $account_array = array(
					'account_status' => $result->payment_type != 0 ? 1 : 3,
					'account_bank_status' => $result->payment_type != 0 ? 1 : 0,
					'account_verify' => $this->input->post('account_verify'),
					'account_verify_on'	=> date('Y-m-d H:i:s'),
					'account_verify_by' => $this->session->userdata('user_id'),
				 );
				 
				 if($result->credit != '0.00'){
					 $paid_amount = $result->credit;
					 $transaction_type = 'Credit';
				 }elseif($result->debit != '0.00'){
					  $paid_amount = $result->debit;
					  $transaction_type = 'Debit';
				 }
				 
				 
				 $payment_array = array(
					'method' => 8,
					'user_id' => $result->user_id,
					'amount' => $paid_amount,
					'payment_transaction_id' => $result->account_transaction_no,
					'transaction_status' => 'success',
					'transaction_type' => $transaction_type,
					'gateway_id' => $result->payment_type,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $result->is_country
				);
				
				$wallet_array = array(
					'user_id' =>  $result->user_id,
					'user_type' => $result->user_type,
					'wallet_type' => 1,
					'flag' => 6,
					'cash' => $paid_amount,
					'description' => 'Add Money - Backend',
					'created' => date('Y-m-d H:i:s'),
					'is_country' => $result->is_country
				);
				
			$adminUser = $this->site->adminUserDebit($result->is_country, 2, $result->type, $paid_amount, $this->session->userdata('user_id'), $result->account_transaction_no);
				
			$insert = $this->account_model->addMoneyOffline($account_id, $account_array, $payment_array, $wallet_array,  $countryCode);	
			if($insert == TRUE && $adminUser == TRUE){
				//wallet/owner
				
				if($result->user_type == 0){
					$this->session->set_flashdata('message', lang("offline_addmoney_verify_success"));
					admin_redirect('account/account_owner/');
				}elseif($result->user_type == 1){
					$this->session->set_flashdata('message', lang("offline_addmoney_verify_success"));
					admin_redirect('account/account_customer/');
				}elseif($result->user_type == 2){
					$this->session->set_flashdata('message', lang("offline_addmoney_verify_success"));
					admin_redirect('account/account_driver/');
				}
			}else{
				$this->session->set_flashdata('error', (validation_errors()) ? validation_errors() : lang("offline_addmoney_verify_faild"));
				admin_redirect('account/offline_account_verify/'.$account_id);
			}
			
		
		}
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['result'] = $result;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('offline_account_verify')));
        $meta = array('page_title' => lang('offline_account_verify'), 'bc' => $bc);
        $this->page_construct('account/offline_account_verify', $meta, $this->data);
    }
	
	function offline_account_reconciliation($account_id){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$account_id = $account_id ? $account_id : $this->input->post('account_id');
		
		
		//$payment_gateway = $this->account_model->getPaymentgateway($countryCode);
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		
		$this->data['account_id'] = $account_id;
		//$driver_data = $this->account_model->getDriverBYId($id, $countryCode);
		$result = $this->account_model->getOfflineaccount($account_id);
		if($this->input->post('offline_submit')){
			
			
			$this->form_validation->set_rules('account_verify', lang("account_verify"), 'required');
			
				if($this->input->post('account_verify') == 0){
					$this->session->set_flashdata('error', (validation_errors()) ? validation_errors() : lang("please select verify button"));
					admin_redirect('account/offline_account_verify/'.$account_id);
				}
				
				 $account_array = array(
					'account_status' => $result->payment_type != 0 ? 1 : 3,
					'account_bank_status' => $result->payment_type != 0 ? 1 : 0,
					'account_verify' => $this->input->post('account_verify'),
					'account_verify_on'	=> date('Y-m-d H:i:s'),
					'account_verify_by' => $this->session->userdata('user_id'),
				 );
				 
				 if($result->credit != '0.00'){
					 $paid_amount = $result->credit;
					 $transaction_type = 'Credit';
				 }elseif($result->debit != '0.00'){
					  $paid_amount = $result->debit;
					  $transaction_type = 'Debit';
				 }
				 
				 
				 $payment_array = array(
					'method' => 8,
					'user_id' => $result->user_id,
					'amount' => $paid_amount,
					'payment_transaction_id' => $result->account_transaction_no,
					'transaction_status' => 'success',
					'transaction_type' => $transaction_type,
					'gateway_id' => $result->payment_type,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $result->is_country
				);
				
				$wallet_array = array(
					'user_id' =>  $result->user_id,
					'user_type' => $result->user_type,
					'wallet_type' => 1,
					'flag' => 6,
					'cash' => $paid_amount,
					'description' => 'Add Money - Backend',
					'created' => date('Y-m-d H:i:s'),
					'is_country' => $result->is_country
				);
				
			$adminUser = $this->site->adminUserDebit($result->is_country, 2, $result->type, $paid_amount, $this->session->userdata('user_id'), $result->account_transaction_no);
				
			$insert = $this->account_model->addMoneyOffline($account_id, $account_array, $payment_array, $wallet_array,  $countryCode);	
			if($insert == TRUE && $adminUser == TRUE){
				//wallet/owner
				
				if($result->user_type == 0){
					$this->session->set_flashdata('message', lang("offline_account_reconciliation_success"));
					admin_redirect('account/account_owner/');
				}elseif($result->user_type == 1){
					$this->session->set_flashdata('message', lang("offline_account_reconciliation_success"));
					admin_redirect('account/account_customer/');
				}elseif($result->user_type == 2){
					$this->session->set_flashdata('message', lang("offline_account_reconciliation_success"));
					admin_redirect('account/account_driver/');
				}
			}else{
				$this->session->set_flashdata('error', (validation_errors()) ? validation_errors() : lang("offline_account_reconciliation_faild"));
				admin_redirect('account/offline_account_reconciliation/'.$account_id);
			}
			
		
		}
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['result'] = $result;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('offline_account_reconciliation')));
        $meta = array('page_title' => lang('offline_account_reconciliation'), 'bc' => $bc);
        $this->page_construct('account/offline_account_reconciliation', $meta, $this->data);
    }
	
	
	public function account_settlement_verify($id) {
		$result = $this->account_model->getSettlement($id);
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $result->is_country ? $result->is_country : $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		
		
		if($this->input->post('settlement_branch')){
			if($this->input->post('to_verify') == 0){
				$this->session->set_flashdata('error', lang("please select verify option"));
				admin_redirect('account/account_settlement_verify/'.$id);
			}
			$admin_user = $this->site->getAdminUser($countryCode, 2);
			$settlement = array(
				'settlement_status' => 3,
				'to_verify' => $this->input->post('to_verify'),
				'to_verify_by' => $this->session->userdata('user_id'),
				'to_verify_on' => date('Y-m-d H:i:s'),
			);
			
			$cash_array = array(
				'type' => 1,
				'credit' => $result->settlement_amount,
				'settlement_id' => $result->id,
				'account_date' => date('Y-m-d'),
				'account_type' => 1,
				'company_id' => $result->to_company_id,
				'company_bank_id' => $result->to_bank_id,
				'account_status' => 3,
				'account_transaction_no' => 'TRANS'.date('YmdHis'),
				'account_transaction_date' => date('Y-m-d'),
				'user_id' => $admin_user,
				'user_type' => 0,
				'account_verify' => 1,
				'account_verify_on' => date('Y-m-d'),
				'account_verify_by' => $this->session->userdata('user_id'),
				'created_on' =>  date('Y-m-d'),
				'created_by' => $this->session->userdata('user_id'),
				'is_country' => $countryCode
			);
			
			if ($this->account_model->branchSettlementverify($settlement, $cash_array, $id, $countryCode)){
				$this->session->set_flashdata('message', lang("settlement verified"));
				admin_redirect('account/account_settlementlist');
			}
		}
		
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['result'] = $result;
		$meta = array('page_title' => lang('settlement_verify'), 'bc' => $bc);
		$this->page_construct('account/account_settlement_verify', $meta, $this->data);

	}
	
	
	
	public function reconcilation() {
		
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		if($this->input->post('reconcilation')){
			$this->form_validation->set_rules('account_id[]', "account_id", 'required');   
			if ($this->form_validation->run() == true) {
				print_r($_POST);
				die;
				$admin_user = $this->site->getAdminUser($countryCode, 2);
				$amount = $this->site->getAccountPendingCash($countryCode, $this->input->post('account_id'));
				$account_ids = $this->input->post('account_id');
			}
			
			if ($this->form_validation->run() == true && $this->account_model->reconcilation($settlement, $account_ids, $countryCode)){
				$this->session->set_flashdata('message', lang("reconcilation has been success"));
				admin_redirect('account/account_owner');
			}
		}
		$this->data['reconcilation'] = $this->account_model->getReconcilation($countryCode);
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');		
		$meta = array('page_title' => lang('reconcilation'), 'bc' => $bc);
		$this->page_construct('account/reconcilation', $meta, $this->data);

	}
	
	public function settlement_branch() {
		
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		if($this->input->post('settlement_branch')){
			$this->form_validation->set_rules('settlement_date', lang("settlement_date"), 'required');
			$this->form_validation->set_rules('from_company_id', lang("from_company_id"), 'required');
			$this->form_validation->set_rules('from_bank_id', lang("from_bank_id"), 'required');
			$this->form_validation->set_rules('to_company_id', lang("to_company_id"), 'required');
			$this->form_validation->set_rules('to_bank_id', lang("to_bank_id"), 'required');
			
			if ($this->form_validation->run() == true) {
				$admin_user = $this->site->getAdminUser($countryCode, 2);
				$amount = $this->site->getAccountPendingCash($countryCode, $this->input->post('account_id'));
				$settlement = array(
					'settlement_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('settlement_date')))),
					'settlement_status' => 0,
					'settlement_type' => $this->input->post('settlement_type'),
					'settlement_code' => 'SET'.date('YmdHis'),
					'settlement_amount' => $amount,
					'from_user_id' => $this->session->userdata('user_id'),
					'to_user_id' => $admin_user ? $admin_user : 0,
					'from_company_id' => $this->input->post('from_company_id'),
					'from_bank_id' => $this->input->post('from_bank_id'),
					'to_company_id' => $this->input->post('to_company_id'),
					'to_bank_id' => $this->input->post('to_bank_id'),
					'created_by' =>  $this->session->userdata('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $countryCode
				);
				
				if ($_FILES['bank_challan']['size'] > 0) {
					$config['upload_path'] = $this->upload_path.'document/bank_challan/';
					$config['allowed_types'] = $this->photo_types;
					$config['overwrite'] = FALSE;
					$config['max_filename'] = 25;
					$config['encrypt_name'] = TRUE;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('bank_challan')) {
						$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
					}
					$bank_challan = $this->upload->file_name;
					$settlement['bank_challan'] = 'document/bank_challan/'.$bank_challan;
					$config = NULL;
				}	
				
				$account_ids = $this->input->post('account_id');
			}
			if ($this->form_validation->run() == true && $this->account_model->branchSettlement($settlement, $account_ids, $countryCode)){
				$this->session->set_flashdata('message', lang("branch has been paid to head office"));
				admin_redirect('account/account_settlementlist');
			}
		}
		
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$this->data['branch_company'] = $this->account_model->getBranch($countryCode);
		$this->data['head_company'] = $this->account_model->getHeadOffice($countryCode);
		
		$meta = array('page_title' => lang('settlement_branch'), 'bc' => $bc);
		$this->page_construct('account/settlement_branch', $meta, $this->data);

	}
	
	public function bank_excel() {
		
		
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		if($this->input->post('import_files')){
			$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
			if ($this->form_validation->run() == true) {
				if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'bankexcel/';
				$config['allowed_types'] = 'xlsx|csv|xls';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("account/bank_excel");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'bankexcel/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				if($this->input->post('payment_gateway') == 1){
					$keys = array('col1', 'col2', 'col3');
				}elseif($this->input->post('payment_gateway') == 3){
					$keys = array('col1', 'col2', 'col3', 'col4');
				}
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
								
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					if($this->input->post('payment_gateway') == 1 || $this->input->post('payment_gateway') == 3){
						$items[] = array(
							
							'transaction_date' => trim(date('Y-m-d H:i:s', strtotime($csv_pr['col1']))),
							'transaction_no' => trim($csv_pr['col2']),
							'amount' => trim($csv_pr['col3']),
						);
						$transaction_no[] = trim($csv_pr['col2']);
					}elseif($this->input->post('payment_gateway') == 2){
						$items[] = array(
							
							'transaction_date' =>  trim(date('Y-m-d H:i:s', strtotime($csv_pr['col1']))),
							'transaction_no' => trim($csv_pr['col2']),
							'amount' => trim($csv_pr['col4']),
						);
						$transaction_no[] = trim($csv_pr['col2']);
					}
					
                    $rw++;
				}
				
				$bank_array = array(
					'import_date' => date('Y-m-d'),
					'import_files' => $csv,
					'payment_mode' => $this->input->post('payment_mode'),
					'payment_type' => $this->input->post('payment_gateway'),
					'created_by' => $this->session->userdata('user_id'),
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $this->input->post('is_country')
				);
				$checkExite = $this->account_model->checkImport($transaction_no);
				if($checkExite == FALSE){
					$res = $this->account_model->import_bank_excel($bank_array, $items, $this->input->post('is_country'));
					if($res == TRUE){
						$this->session->set_flashdata('message', 'Success');
           				admin_redirect("account/bank_excel");
					}else{
						$this->session->set_flashdata('error', 'Faild');
           				admin_redirect("account/bank_excel");
					}
				}else{
					$this->session->set_flashdata('error', 'Already import transaction files');
           			admin_redirect("account/bank_excel");	
				}
					
				
		   		}
			}else{
				$this->session->set_flashdata('error', validation_errors());
           		admin_redirect("account/bank_excel");
			}
		}
		$this->data['payment_gateway'] = $this->site->getPaymentgateway($countryCode);
		$meta = array('page_title' => lang('bank_excel'), 'bc' => $bc);
		$this->page_construct('account/bank_excel', $meta, $this->data);

	}
	
	public function success() {
		
		$meta = array('page_title' => lang('Razorpay Success | TechArise'), 'bc' => $bc);
		$this->page_construct('account/success', $meta, $this->data);

	}
	public function failed() {
		
		$meta = array('page_title' => lang('Razorpay Failed | TechArise'), 'bc' => $bc);
		$this->page_construct('account/failed', $meta, $this->data);

	}
	
	public function sms_ride_later($sms_message, $sms_phone, $sms_country_code) {

        $sms_chk_arr = array('[SMSMESSAGE]');
        $sms_rep_arr = array($sms_message);
        $response_sms = send_transaction_sms($sms_template_slug = "ride-later", $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code);
        return $response_sms;
    }
	
	function index(){
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
		$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('account')));
		$meta = array('page_title' => lang('account'), 'bc' => $bc);
		$this->page_construct('account/index', $meta, $this->data);

	}
	
	function trip($action=false){
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
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$this->data['account_setting'] = $this->site->CommonSettings($countryCode);
        $bc = array(array('link' => base_url(), 'page' => lang('account')), array('link' => '#', 'page' => lang('Per Trip Accounting')));
        $meta = array('page_title' => lang('per_trip_accounting'), 'bc' => $bc);
		//$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver);
        $this->page_construct('account/trip', $meta, $this->data);
    }
	
	function getTrip(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        //print_R($_GET);exit;
       // $this->sma->checkPermissions('index');
		//$booked_status = $_GET['status'];
        //$booked_type = $_GET['booked_type'];
        $sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.booked_on as booked_on, {$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_no as booking_no,   du.first_name as driver_name,  vu.first_name as vendor_name, cu.mobile as customer_mobile, rp.total_fare as total_fare, rp.total_fare as heyycab_fare, rp.total_fare as driver_fare, pm.name as payment_name, rp.paid_amount as paid_amount, country.name as instance_country ")
            ->from("rides")
            ->join('user_profile d','d.user_id=rides.driver_id AND d.is_edit=1 ', 'left')
			->join('user_profile c','c.user_id=rides.customer_id AND c.is_edit=1 ', 'left')
			->join('user_profile v','v.user_id=rides.vendor_id AND v.is_edit=1 ', 'left')
			->join('users du','du.id=rides.driver_id AND du.is_edit=1 ', 'left')
			->join('users cu','cu.id=rides.customer_id AND cu.is_edit=1 ', 'left')
			->join('users vu','vu.id=rides.vendor_id AND vu.is_edit=1 ', 'left')
			->join('ride_payment rp','rp.ride_id=rides.id')
            ->join('payment_mode pm','pm.id=rp.payment_type ', 'left')
			->join("countries country", " country.iso = rides.is_country", "left")
			->where('rides.status', 5)
			->where('rides.is_delete', 0);
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booked_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("rides.is_country", $countryCode);
			}
			$this->datatables->group_by('rides.id');
			
            //$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' class='tip' title='" . lang("Track") . "'><i class=\"fa fa-car\"></i></a></div>", "id");
			
			//$edit = "<a href='" . admin_url('rides/track/$1?status='.$booked_status) . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-path'></div></a>";
			
			//$pdf = "<a href='" . admin_url('rides/pdf/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-file-pdf-o' aria-hidden='true'  style='color:#656464; font-size:18px'  style='color:#656464; font-size:18px'></i></a>";
			
			//$delete = "<a href='" . admin_url('welcome/delete/rides/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
		/*$this->datatables->add_column("Actions", "<div><a href='' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'><div class='kapplist-view1'></div></a></div>
			<div><a href=''><div class='kapplist-edit'></div></a></div>
			<div><a href=''><div class='kapplist-car'></div></a></div>
			<div><a href=''><div class='kapplist-path'></div></a></div>
			
			");*/
			$view = "<a href='" . admin_url('acount/tripview/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-path'></div></a>";
		//$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$pdf."</div><div>".$delete."</div>", "id");
		//$this->datatables->add_column("Actions", "<div></div>", "id");
        //$this->datatables->unset_column('id');
        $this->datatables->unset_column('id');
		
        echo $this->datatables->generate();
		//echo $this->db->last_query();die;
    }

	
	function withdraw($action=false){
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
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customer Settlement')));
        $meta = array('page_title' => lang('customer_settlement'), 'bc' => $bc);
		$this->data['drivers'] = $this->account_model->getUsersAllwithoutgroup($countryCode);
        $this->page_construct('account/withdraw', $meta, $this->data);
    }
	
	function getWithdraw(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		 $this->load->library('datatables');
		 $this->datatables
            ->select("{$this->db->dbprefix('withdraw')}.id as id, {$this->db->dbprefix('withdraw')}.created_on, u.first_name, u.last_name, u.mobile, {$this->db->dbprefix('withdraw')}.amount, {$this->db->dbprefix('withdraw')}.status as status, country.name as instance_country ")
			 ->from("withdraw")
			 ->join("countries country", " country.iso = withdraw.is_country", "left")
			 ->join("users u", "u.id = withdraw.user_id AND u.is_edit != 0 ");
			 $this->datatables->where("withdraw.status !=", 3);
			 	
			 if(!empty($driver_id)){
				$this->datatables->where("withdraw.user_id", $driver_id);	
			}
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('withdraw')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('withdraw')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("withdraw.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("withdraw.is_country", $countryCode);
			}
			
			$this->datatables->edit_column('status', '$1__$2', 'id, status');
			
			$view = "<a href='" . admin_url('account/driver_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$view."</div>", "id");
			
			$this->datatables->unset_column('id');
			
       	 echo $this->datatables->generate();
	}
	
	/*###### Driver*/
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
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver'), 'bc' => $bc);
		$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver, $countryCode);
        $this->page_construct('account/driver', $meta, $this->data);
    }
    function getDriver(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile, {$this->db->dbprefix('users')}.active as active, IFNULL(dp.ride_end_date, '0000-00-00') as ride_end_date, IFNULL(dp.duration_date, '0000-00-00') as duration_date, IFNULL(dp.driver_status, 0) as driver_status, IFNULL(dp.payment_status, 0) as payment_status, IFNULL(dp.admin_status, 0) as admin_status, country.name as instance_country 
			
			")
            ->from("users")
			->join("countries country", " country.iso = users.is_country")
			->join("driver_payment dp", "dp.driver_id = users.id AND dp.is_edit != 0 ")
			->where("users.group_id", $group_id);
			
			if($this->Vendor == $this->session->userdata('group_id')){
				$this->datatables->where("users.parent_id", $this->session->userdata("user_id"));
			}
			
			if(!empty($driver_id)){
				$this->datatables->where("users.id", $driver_id);	
			}
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("users.id");
            $this->datatables->edit_column('active', '$1__$2', 'id, active');
			$this->datatables->edit_column('driver_status', '$1__$2', 'id, driver_status');
			$this->datatables->edit_column('payment_status', '$1__$2', 'id, payment_status');
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            //->edit_column('status', '$1__$2', 'id, status')
			//->edit_column('join_type', '$1__$2', 'id, join_type');
			$edit = "<a href='" . admin_url('account/driver_view/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
		
			
			//$this->datatables->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('account/driver_view/$1') . "' class='tip' title='" . lang("view") . "'>view</a></div>", "id");
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	function driver_view($id){
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
        $this->data['id'] = $id;
		$this->data['user'] = $this->account_model->getDriverBYId($id, $countryCode);
		$this->data['payment'] = $this->account_model->getPaymentBYId($id, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver')));
        $meta = array('page_title' => lang('driver_view'), 'bc' => $bc);
        $this->page_construct('account/driver_view', $meta, $this->data);
    }
	
	
	function getDriverpayment(){
		
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('driver_payment')}.id as id, {$this->db->dbprefix('driver_payment')}.ride_start_date, {$this->db->dbprefix('driver_payment')}.ride_end_date, {$this->db->dbprefix('driver_payment')}.total_ride, {$this->db->dbprefix('driver_payment')}.total_ride_amount, pm.name as payment_name, {$this->db->dbprefix('driver_payment')}.payment_date, {$this->db->dbprefix('driver_payment')}.transaction_no, {$this->db->dbprefix('driver_payment')}.admin_status as admin_status, country.name as instance_country 	
			")
            ->from("driver_payment")
			->join("countries country", " country.iso = driver_payment.is_country", "left")
			->join("payment_mode pm", 'pm.id = driver_payment.payment_id ', 'left')
			->where("driver_payment.is_edit !=", 0)
			->where("driver_payment.driver_id", $driver_id);
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}
			
			
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	function complete_payment($action=false){
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
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('complete_payment')));
        $meta = array('page_title' => lang('complete_payment'), 'bc' => $bc);
		$this->data['drivers'] = $this->account_model->getUsersAll($this->Driver, $countryCode);
        $this->page_construct('account/complete_payment', $meta, $this->data);
    }
	
	function getCompletepayment(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('driver_payment')}.id as id, {$this->db->dbprefix('driver_payment')}.ride_start_date, {$this->db->dbprefix('driver_payment')}.ride_end_date, {$this->db->dbprefix('driver_payment')}.total_ride, {$this->db->dbprefix('driver_payment')}.total_ride_amount, {$this->db->dbprefix('driver_payment')}.payment_percentage, {$this->db->dbprefix('driver_payment')}.payment_amount, {$this->db->dbprefix('driver_payment')}.paid_amount, {$this->db->dbprefix('driver_payment')}.balance_amount, pm.name as payment_name, {$this->db->dbprefix('driver_payment')}.payment_date, {$this->db->dbprefix('driver_payment')}.transaction_no, {$this->db->dbprefix('driver_payment')}.admin_status as admin_status, country.name as instance_country 	
			")
            ->from("driver_payment")
			->join("countries country", " country.iso = driver_payment.is_country", "left")
			->join("payment_mode pm", 'pm.id = driver_payment.payment_id ', 'left')
			->where("driver_payment.is_edit", 0);
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("driver_payment.is_country", $countryCode);
			}
			
			if(!empty($driver_id)){
				$this->datatables->where("driver_payment.driver_id", $driver_id);	
			}
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.payment_date) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('driver_payment')}.payment_date) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			$this->datatables->edit_column('admin_status', '$1__$2', 'id, admin_status');
            
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		
		
    }
	
	
	
	
	function getBank(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$res = array();
		$account_no = $this->input->post('account_no');	
		$q = $this->db->select('bank_name, branch_name, ifsc_code')->where('account_no', $account_no)->get('admin_bank');
		if($q->num_rows()>0){
			$res = $q->row();			
		}
		echo json_encode($res);
	}
	function admin_to_driver($status, $id){
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
		$driver_data = $this->account_model->getDriverBYId($id, $countryCode);
		
		
		if($status == 'deposit'){
			$this->form_validation->set_rules('transaction_no', lang("transaction_no"), 'required');
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
					$update['account_transaction_image'] = 'document/transaction/'.$transaction_image;
					$config = NULL;
				}
				
			}
			
			if ($this->form_validation->run() == true && $this->account_model->updateVerifiedpayment($this->input->post('driver_payment_id'), $update, $id, $countryCode)){
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
			$this->form_validation->set_rules('transaction_no', lang("transaction_no"), 'required');
			if ($this->form_validation->run() == true) {
				$update = array(
					'admin_account_no' => $this->input->post('admin_account_no') ? $this->input->post('admin_account_no') : '',
					'deposit_bank_name' => $this->input->post('deposit_bank_name') ? $this->input->post('deposit_bank_name') : '',
					'deposit_branch_name' => $this->input->post('deposit_branch_name') ? $this->input->post('deposit_branch_name') : '',
					'deposit_ifscode' => $this->input->post('deposit_ifscode') ? $this->input->post('deposit_ifscode') : '',
					'deposit_date' => $this->input->post('deposit_date'),
					'transaction_no' => $this->input->post('transaction_no'),
					'transaction_date' => $this->input->post('deposit_date'),
					'admin_status' => 1,
					'is_edit' => 0
				);
				
				
				
			}
			if ($this->form_validation->run() == true && $this->account_model->updateVerifiedpayment($this->input->post('driver_payment_id'), $update, $id, $countryCode)){
				
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
		$this->data['payment_type'] = $this->account_model->getPaymentmode($countryCode);
		$this->data['banks'] = $this->account_model->getBanks($countryCode);
		$this->data['payment'] = $this->account_model->getPayment($id, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('admin_status')));
        $meta = array('page_title' => lang('admin_status'), 'bc' => $bc);
        $this->page_construct('account/admin_to_driver', $meta, $this->data);
    }
	/*###### Vendor*/
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
        $this->page_construct('account/vendor', $meta, $this->data);
    }
	
	function getVendor(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$group_id = $this->Vendor;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$approved = $_GET['approved'];
		
        $this->load->library('datatables');
		
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.created_on, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email, {$this->db->dbprefix('users')}.mobile, {$this->db->dbprefix('users')}.active as active, IFNULL(dp.ride_end_date, '0000-00-00') as ride_end_date, IFNULL(dp.driver_status, 0) as driver_status, IFNULL(dp.payment_status, 0) as payment_status, IFNULL(dp.admin_status, 0) as admin_status, country.name as instance_country 
			
			")
            ->from("users")
			->join("countries country", " country.iso = users.is_country", "left")
			->join("driver_payment dp", "dp.vendor_id = users.id AND dp.is_edit = 1 AND dp.is_country = '".$countryCode."'", 'left')
			->where("users.group_id", $group_id)
			->where('users.is_country', $countryCode);
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('users')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			

			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("users.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("users.is_country", $countryCode);
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
	
	function razorpay_driver_to_admin($id){
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
		$payment = $this->account_model->getDriverPaymentGateway($id);	
		$driver_data = $this->site->get_driver($payment->driver_id, $countryCode);
				if (!empty($this->input->post('razorpay_payment_id')) && !empty($this->input->post('merchant_order_id'))) {
						$razorpay_payment_id = $this->input->post('razorpay_payment_id');
						$merchant_order_id = $this->input->post('merchant_order_id');
						$currency_code = 'INR';
						$amount = round($payment->paid_amount);
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
										$update = array(
											'driver_status' => 1,
											'is_edit' => 2,
											'admin_status' => 3										
										);
										
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
						
						
						if ($success === true) {
							/*if(!empty($this->session->userdata('ci_subscription_keys'))) {
								$this->session->unset_userdata('ci_subscription_keys');
							 }
							if (!$order_info['order_status_id']) {
								admin_redirect('account/success');
							} else {
								 admin_redirect('account/success');
							}*/
							$payment_array = array(
								'method' => 4,
								'user_id' => $payment->driver_id,
								'method_id' => $id,
								'payment_transaction_id' => $razorpay_payment_id,
								'amount' => $payment->paid_amount,
								'transaction_status' => 'Success',
								'transaction_type' => 'Debit',
								'gateway_id' => $payment->payment_gateway_id,
								'created_on' => date('Y-m-d H:i:s')
							);
							$wallet_array = array(
								'user_id' => $payment->driver_id,
								'type' => 1,
								'flag' => 4,
								'cash' => $payment->paid_amount,
								'description' => 'Driver monthly payment has been done.',
								'created' => date('Y-m-d H:i:s')
							);
							
							$driver_wallet = array(
								'user_id' => $payment->driver_id,
								'user_type' => 2,
								'wallet_type' => 1, 
								'flag' => 4,
								'cash' => $payment->paid_amount,
								'description' => 'Driver payment for admin',
								'created' => date('Y-m-d H:i:s'),
								'is_country' => $countryCode
							);
							$admin_wallet = array(
								'user_id' => 1,
								'user_type' => 0,
								'wallet_type' => 1, 
								'flag' => 2,
								'cash' => $payment->paid_amount,
								'description' => 'Driver payment for admin',
								'created' => date('Y-m-d H:i:s'),
								'is_country' => $countryCode
							);
							if($this->account_model->updateCashpayment($this->input->post('driver_payment_id'), $update, $payment_array, $driver_wallet, $admin_wallet, $countryCode)){
								$driver_data = $this->site->get_driver($payment->driver_id, $ountryCode);
								$sms_message = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
								$sms_phone = $driver_data->country_code.$driver_data->mobile;
								$sms_country_code = $driver_data->country_code;
					
								$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
								
								$notification['title'] = 'Driver Payment Online - Backend';
								$notification['message'] = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
								$notification['user_type'] = 2;
								$notification['user_id'] = $driver_data->id;
								$this->site->insertNotification($notification, $countryCode);
								
								
								$this->session->set_flashdata('message', lang("admin_driver_payment_status"));
								admin_redirect('account/driver');
							}
			 
						} else {
							$payment_array = array(
								'method' => 4,
								'user_id' => $payment->driver_id,
								'method_id' => $id,
								'payment_transaction_id' => $razorpay_payment_id,
								'amount' => $payment->paid_amount,
								'transaction_status' => 'Faild',
								'transaction_type' => 'Debit',
								'gateway_id' => $payment->payment_gateway_id,
								'created_on' => date('Y-m-d H:i:s')
							);
							
							$update = array(
								'driver_status' => 0,
								'is_edit' => 1,
								'admin_status' => 0
							);
							
							
							$this->account_model->updateCashpayment($id, $update, $payment_array, $driver_wallet = array(), $admin_wallet = array(), $countryCode);
							
							
							$this->session->set_flashdata('error', lang("your_online_payment_faild"));
							 admin_redirect('account/driver');
						}
					}
		
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['id'] = $id;
			$this->data['payment_type'] = $this->account_model->getPaymentmode($countryCode);
			$this->data['payment_gateway'] = $this->account_model->getPaymentgateway($countryCode);
			$this->data['return_url'] = admin_url().'account/callback';
			$this->data['surl'] = admin_url().'account/success';
			$this->data['furl'] = admin_url().'account/failed';
			$this->data['currency_code'] = 'INR';
			
			$this->data['payment'] = $payment;
			$this->data['driver_data'] = $driver_data;
			
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_status')));
			$meta = array('page_title' => lang('razorpay'), 'bc' => $bc);
			$this->page_construct('account/razorpay_driver_to_admin', $meta, $this->data);		
	}
	
	function driver_to_admin($status, $id){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$payment_gateway = $this->account_model->getPaymentgateway($countryCode);
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		$driver_data = $this->account_model->getDriverBYId($id, $countryCode);
		//$payment_gateway = $this->site->getPaymentgateway($countryCode);
		if($status == 'active'){
			$this->form_validation->set_rules('paid_amount', lang("paid_amount"), 'required');
			if ($this->form_validation->run() == true) {
				
				if($this->input->post('payment_status') == 1){
					/*##########*/
					
					$tax = $this->account_model->getTaxdefault($countryCode);
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
							'driver_status' => 2,
							'is_edit' => 2,
							'admin_status' => 0
						);
					$p = $this->account_model->updateCashpayment($this->input->post('driver_payment_id'), $update, $payment_array = array(), $wallet_array = array(), $countryCode);
						
					
					if($p){
						foreach($payment_gateway as $gateway){
							if($gateway->id == $this->input->post('payment_gateway_id')){
								admin_redirect('account/'.$gateway->code.'_driver_to_admin/'.$this->input->post('driver_payment_id').'');
								die;
							}else{
								
							}
						}
					}
					
					
					/*############*/
				}else{
					$tax = $this->account_model->getTaxdefault($countryCode);
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
					$wallet_array = array(
						'user_id' => $id,
						'type' => 1,
						'flag' => 4,
						'cash' => $this->input->post('paid_amount'),
						'description' => 'Driver monthly payment has been done.',
						'created' => date('Y-m-d H:i:s')
					);
					
					$driver_wallet = array(
						'user_id' => $id,
						'user_type' => 2,
						'wallet_type' => 1, 
						'flag' => 4,
						'cash' => $this->input->post('paid_amount'),
						'description' => 'Driver payment for admin',
						'created' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode
					);
					$admin_wallet = array(
						'user_id' => 1,
						'user_type' => 0,
						'wallet_type' => 1, 
						'flag' => 2,
						'cash' => $this->input->post('paid_amount'),
						'description' => 'Driver payment for admin',
						'created' => date('Y-m-d H:i:s'),
						'is_country' => $countryCode
					);
					
					
					if($this->account_model->updateCashpayment($this->input->post('driver_payment_id'), $update, $payment_array, $driver_wallet, $admin_wallet, $countryCode)){
						
						$sms_message = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
						$sms_phone = $driver_data->country_code.$driver_data->mobile;
						$sms_country_code = $driver_data->country_code;
			
						$response_sms = $this->sms_ride_later($sms_message, $sms_phone, $sms_country_code);
						
						$notification['title'] = 'Driver Payment Offline - Backend';
						$notification['message'] = $driver_data->first_name.' your payment has been paid verification checking success. admin has been verified';
						$notification['user_type'] = 2;
						$notification['user_id'] = $driver_data->id;
						$this->site->insertNotification($notification);
						
						
						$this->session->set_flashdata('message', lang("admin_driver_payment_status"));
						admin_redirect('account/driver');
					}
				}
				
				
				
			}
			
		}
		
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		//$this->data['payment_type'] = $this->account_model->getPaymentmode($countryCode);
		//$this->data['payment'] = $payment;
		
		$this->data['return_url'] = admin_url().'account/callback';
        $this->data['surl'] = admin_url().'account/success';
        $this->data['furl'] = admin_url().'account/failed';
        $this->data['currency_code'] = 'INR';
		$this->data['payment'] = $this->account_model->getPayment($id, $countryCode);
		//print_r($this->data['payment']);die;
		$this->data['payment_type'] = $this->account_model->getPaymentmode($countryCode);
			$this->data['payment_gateway'] = $this->account_model->getPaymentgateway($countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_status')));
        $meta = array('page_title' => lang('driver_status'), 'bc' => $bc);
        $this->page_construct('account/driver_to_admin', $meta, $this->data);
    }
	
	
}
