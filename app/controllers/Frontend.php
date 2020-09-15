<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends MY_Controller
{

    function __construct() {
        parent::__construct();
		
		$this->lang->admin_load('posnew', $this->Settings->user_language);
		$this->load->model('frontend_model');
		$this->load->library('ion_auth');
        $this->load->library('form_validation');
    }

    function index() {
        if (!SHOP) { redirect('admin'); }
        $this->data['featured_products'] = $this->shop_model->getFeaturedProducts();
        $this->data['slider'] = json_decode($this->shop_settings->slider);
        $this->data['page_title'] = $this->shop_settings->shop_name;
        $this->data['page_desc'] = $this->shop_settings->description;
        $this->page_construct('index', $this->data);
    }

    function profile($act = NULL) {
        if (!$this->loggedIn) { redirect('/'); }
        if (!SHOP || $this->Staff) { redirect('admin/users/profile/'.$this->session->userdata('user_id')); }
        $user = $this->ion_auth->user()->row();
        if ($act == 'user') {

            $this->form_validation->set_rules('first_name', lang("first_name"), 'required');
            $this->form_validation->set_rules('last_name', lang("last_name"), 'required');
            $this->form_validation->set_rules('phone', lang("phone"), 'required');
            $this->form_validation->set_rules('email', lang("email_address"), 'required|valid_email');
            $this->form_validation->set_rules('company', lang("company"), 'trim');
            $this->form_validation->set_rules('vat_no', lang("vat_no"), 'trim');
            $this->form_validation->set_rules('address', lang("billing_address"), 'required');
            $this->form_validation->set_rules('city', lang("city"), 'required');
            $this->form_validation->set_rules('state', lang("state"), 'required');
            $this->form_validation->set_rules('postal_code', lang("postal_code"), 'required');
            $this->form_validation->set_rules('country', lang("country"), 'required');
            if ($user->email != $this->input->post('email')) {
                $this->form_validation->set_rules('email', lang("email_address"), 'trim|is_unique[users.email]');
            }

            if ($this->form_validation->run() === TRUE) {

                $bdata = [
                    'name' => $this->input->post('first_name').' '.$this->input->post('last_name'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'company' => $this->input->post('company'),
                    'vat_no' => $this->input->post('vat_no'),
                    'address' => $this->input->post('address'),
                    'city' => $this->input->post('city'),
                    'state' => $this->input->post('state'),
                    'postal_code' => $this->input->post('postal_code'),
                    'country' => $this->input->post('country'),
                ];

                $udata = [
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                ];

                if ($this->ion_auth->update($user->id, $udata) && $this->shop_model->updateCompany($user->company_id, $bdata)) {
                    $this->session->set_flashdata('message', lang('user_updated'));
                    $this->session->set_flashdata('message', lang('billing_data_updated'));
                    redirect("profile");
                }

            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } elseif ($act == 'password') {

            $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
            $this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length[8]|max_length[25]');
            $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('profile');
            } else {
                if (DEMO) {
                    $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));
                $change = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

                if ($change) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->logout('m');
                } else {
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    redirect('profile');
                }
            }

        }

        $this->data['featured_products'] = $this->shop_model->getFeaturedProducts();
        $this->data['customer'] = $this->site->getCompanyByID($this->session->userdata('company_id'));
        $this->data['user'] = $this->site->getUser();
        $this->data['page_title'] = lang('profile');
        $this->data['page_desc'] = $this->shop_settings->description;
        $this->page_construct('user/profile', $this->data);
    }

    function login() {
		
		
        $date_now = date('Y-m-d');
        $site_expiry_date    = $this->Settings->site_expiry_date;

        if($site_expiry_date != '0000-00-00'){
           if ($date_now > $site_expiry_date) {
               $this->load->view($this->theme . 'auth/expiry_date');
               return false;
            } 
        } 

		$this->form_validation->set_rules('user_number', $this->lang->line("passcode"), 'required');
		$this->form_validation->set_rules('user_group', $this->lang->line("group"), 'required');
		$this->form_validation->set_rules('user_warehouse', $this->lang->line("warehouse"), 'required');
		
		
        if ($this->form_validation->run() == true) {

           $user_number = $this->input->post('user_number');
		   $details = $this->frontend_model->getUsers($user_number);
		   $user_group = $this->input->post('user_group');
		   $user_warehouse = $details->warehouse_id; 
		   
		  

            if (!empty($user_number))  {
               
			  
		   $check_audit = $this->site->getPreviousDayNightAudit($user_warehouse);
           if($this->Settings->night_audit_rights == 1){
                if(!$check_audit) {
                    $this->session->set_flashdata('message', 'Night audit not complete.');  
                    $this->session->set_flashdata('nightaudit_alert', 'There are some pending orders. Please close the orders and do Night audit to enable further orders');
		    $login = $this->frontend_model->login($user_number,$user_warehouse);
                    //redirect('frontend/login');   
                }else{
                 $login = $this->frontend_model->login($user_number);
             }
           }
           else{
                $login = $this->frontend_model->login($user_number);
           }
			
			if($login == TRUE){
						
						$this->session->set_flashdata('message', 'Success');
						if($this->Settings->qsr){
							redirect('admin/qsr');
						}else{
							redirect('admin/pos');
							
						}
						
					
				}else{
					$this->session->set_flashdata('message', 'Your details is not exit.');	
					redirect('frontend/login');
				}
                
				
                
            } else {
                $this->session->set_flashdata('error', 'All feild empty');
                redirect('frontend/login');
            }

        } else {
			
			
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['message'] = $this->session->flashdata('message') ? $this->session->flashdata('message') : '';
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['groups'] = $this->site->getAllGroups(true);
            $this->load->view($this->theme . 'frontend/login', $this->data);

        }
    }

    function logout($m = NULL) {
       
        $logout = $this->ion_auth->logout();
        $referrer = (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '/');
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect('login');
    }
    function create_nightauditDate(){
	$this->load->model('site');
	//$day = $this->input->post('day');
	$this->site->create_nightauditDate();
	echo 1;
    }
    function update_nightauditDate(){
	$this->load->model('site');
	$day = $this->input->post('day');
	$date = $this->site->update_nightauditDate($day);
	echo date('d-m-Y',strtotime($date));
    }
    function check_nightauditDate(){
	$this->load->model('site');
	$this->site->check_nightauditDate();
    }
  

   /* function language($lang) {
        $folder = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            set_cookie('shop_language', $lang, 31536000);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function currency($currency) {
        set_cookie('shop_currency', $currency, 31536000);
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function cookie($val) {
        set_cookie('shop_use_cookie', $val, 31536000);
        redirect($_SERVER["HTTP_REFERER"]);
    }*/

}
