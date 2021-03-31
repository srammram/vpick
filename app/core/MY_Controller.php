<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    function __construct()
    {
        parent::__construct();
		
        
		$ip = $_SERVER['REMOTE_ADDR'];
		$Setting_Country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"));
		$this->countryCode = $Setting_Country->geoplugin_countryCode;
		//$this->countryCode = 'IN';
        if($this->session->userdata('group_id') == 6){
            $deprole = $this->site->getDepartmentRole($this->session->userdata('user_id'), $this->session->userdata('group_id'));
            $this->Department = $deprole->department_id;
            $this->Designation = $deprole->designation_id;
        }else{
            $this->Department = 0;
            $this->Designation = 0;
        }


		$this->Settings = $this->site->get_setting($this->countryCode);
        $this->Bnotify = $this->site->get_booking_cancel_notification($this->countryCode);
		$this->getUserIpAddr = $this->site->getUserIpAddr();
		
		if($this->session->userdata('value', TRUE)) {
            $this->config->set_item('language', $this->session->userdata('value'));
            $this->lang->admin_load('sma', $this->session->userdata('value'));
            $this->Settings->user_language = $this->session->userdata('value');
        } else {
            $this->config->set_item('language', $this->Settings->language);
            $this->lang->admin_load('sma', $this->Settings->language);
            $this->Settings->user_language = $this->Settings->language;
        }
        /*if($sma_language = $this->input->cookie('sma_language', TRUE)) {
            $this->config->set_item('language', $sma_language);
            $this->lang->admin_load('sma', $sma_language);
            $this->Settings->user_language = $sma_language;
        } else {
            $this->config->set_item('language', $this->Settings->language);
            $this->lang->admin_load('sma', $this->Settings->language);
            $this->Settings->user_language = $this->Settings->language;
        }*/
		
        if($rtl_support = $this->input->cookie('sma_rtl_support', TRUE)) {
            $this->Settings->user_rtl = $rtl_support;
        } else {
            $this->Settings->user_rtl = $this->Settings->rtl;
        }
        
		$this->theme = $this->Settings->theme.'/admin/views/';
		
        if(is_dir(VIEWPATH.$this->Settings->theme.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR)) {
            $this->data['assets'] = base_url() . 'themes/' . $this->Settings->theme . '/assets/';
        } else {
            $this->data['assets'] = base_url() . 'themes/default/admin/assets/';
        }
        if(empty($this->Settings->excel_header_color)) {
            $this->Settings->excel_header_color = 'd28f16';
        } 
        if(empty($this->Settings->excel_footer_color)) {
            $this->Settings->excel_footer_color = 'ffc000';
        }
        
        $this->data['Settings'] = $this->Settings;
		
		$this->data['due_month'] = $this->Settings->due_month;
		$this->data['due_year'] = $this->Settings->due_year;
		
        $this->loggedIn = $this->sma->logged_in();
		
		$this->AllCountrys = $this->site->getCountrywithoutparent($this->countryCode);
		
        if($this->loggedIn) {
            
            $owner = 'owner';
            $this->Owner = $this->site->getUserGroupIDbyname($owner);
            $this->data['Owner'] = $this->Owner;
			$this->data['AllCountrys'] = $this->AllCountrys;
			
			$this->Supplier = NULL;
            $this->data['Supplier'] = $this->Supplier;
			
			$vendor = 'vendor';
            $this->Vendor = $this->site->getUserGroupIDbyname($vendor);
            $this->data['Vendor'] = $this->Vendor;
			$driver = 'driver';
			$this->Driver = $this->site->getUserGroupIDbyname($driver);
            $this->data['Driver'] = $this->Driver;
			$employee = 'employee';
			$this->Employee = $this->site->getUserGroupIDbyname($employee);
            $this->data['Employee'] = $this->Employee;
			$customer = 'customer';
			$this->Customer = $this->site->getUserGroupIDbyname($customer);
            $this->data['Customer'] = $this->Customer;
			
			$admin = 'admin';
            $this->Admin = $this->site->getUserGroupIDbyname($admin);
            $this->data['Admin'] = $this->Admin;

            if($sd = $this->site->getDateFormat($this->Settings->dateformat)) {
                $dateFormats = array(
                    'js_sdate' => $sd->js,
                    'php_sdate' => $sd->php,
                    'mysq_sdate' => $sd->sql,
                    'js_ldate' => $sd->js . ' hh:ii',
                    'php_ldate' => $sd->php . ' H:i',
                    'mysql_ldate' => $sd->sql . ' %H:%i'
                    );
            } else {
                $dateFormats = array(
                    'js_sdate' => 'mm-dd-yyyy',
                    'php_sdate' => 'm-d-Y',
                    'mysq_sdate' => '%m-%d-%Y',
                    'js_ldate' => 'mm-dd-yyyy hh:ii:ss',
                    'php_ldate' => 'm-d-Y H:i:s',
                    'mysql_ldate' => '%m-%d-%Y %T'
                    );
            }
            if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'Pos.php')) {
                define("POS", 1);
            } else {
                define("POS", 0);
            }
            if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'shop'.DIRECTORY_SEPARATOR.'Shop.php')) {
                define("SHOP", 1);
            } else {
                define("SHOP", 0);
            }

             if(file_exists(APPPATH.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'System_settings.php')) {
                define("WAREHOUSES", 1);
            } else {
                define("WAREHOUSES", 0);
            }
            if(!$this->Owner && !$this->Admin) {
                $this->data['GP'] = NULL;
            } else {
                $this->data['GP'] = NULL;
            }
            $this->dateFormats = $dateFormats;
            $this->data['dateFormats'] = $dateFormats;
            $this->data['Bnotify'] = $this->Bnotify;
            $this->load->language('calendar');
            //$this->default_currency = $this->Settings->currency_code;
            //$this->data['default_currency'] = $this->default_currency;
            $this->m = strtolower($this->router->fetch_class());
            $this->v = strtolower($this->router->fetch_method());
            $this->data['m']= $this->m;
            $this->data['v'] = $this->v;
            $this->data['dt_lang'] = json_encode(lang('datatables_lang'));
            $this->data['dp_lang'] = json_encode(array('days' => array(lang('cal_sunday'), lang('cal_monday'), lang('cal_tuesday'), lang('cal_wednesday'), lang('cal_thursday'), lang('cal_friday'), lang('cal_saturday'), lang('cal_sunday')), 'daysShort' => array(lang('cal_sun'), lang('cal_mon'), lang('cal_tue'), lang('cal_wed'), lang('cal_thu'), lang('cal_fri'), lang('cal_sat'), lang('cal_sun')), 'daysMin' => array(lang('cal_su'), lang('cal_mo'), lang('cal_tu'), lang('cal_we'), lang('cal_th'), lang('cal_fr'), lang('cal_sa'), lang('cal_su')), 'months' => array(lang('cal_january'), lang('cal_february'), lang('cal_march'), lang('cal_april'), lang('cal_may'), lang('cal_june'), lang('cal_july'), lang('cal_august'), lang('cal_september'), lang('cal_october'), lang('cal_november'), lang('cal_december')), 'monthsShort' => array(lang('cal_jan'), lang('cal_feb'), lang('cal_mar'), lang('cal_apr'), lang('cal_may'), lang('cal_jun'), lang('cal_jul'), lang('cal_aug'), lang('cal_sep'), lang('cal_oct'), lang('cal_nov'), lang('cal_dec')), 'today' => lang('today'), 'suffix' => array(), 'meridiem' => array()));
            $this->Settings->indian_gst = FALSE;
            if ($this->Settings->invoice_view > 0) {
                $this->Settings->indian_gst = $this->Settings->invoice_view == 2 ? TRUE : FALSE;
                $this->Settings->format_gst = TRUE;
                $this->load->library('gst');
            }
            
            if($this->session->userdata('admin_panel')){ 
                /**** session last activity ****/
                $time_since = time() - $this->session->userdata('last_activity');
                $interval = 60;
                if ($time_since > $interval) {
                    $loginData['last_activity'] = date('Y-m-d H:i:s');
                    $loginData['status'] = 'active';
                    $this->site->updateLoginStatus($loginData);
                    $this->session->set_userdata('last_activity',time());
                }            
                /**** check current user activity ***/
                //$this->site->isActiveUser();
            }
        }else{
            $this->data['AllCountrys'] = $this->AllCountrys;
        }
    }

    function page_construct($page, $meta = array(), $data = array()) {
        $meta['message'] = isset($data['message']) ? $data['message'] : $this->session->flashdata('message');
        $meta['error'] = isset($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $meta['warning'] = isset($data['warning']) ? $data['warning'] : $this->session->flashdata('warning');
        
        $meta['ip_address'] = $this->input->ip_address();
        $meta['Owner'] = $data['Owner'];
        $meta['Admin'] = $data['Admin'];
		
		$meta['Vendor'] = $data['Vendor'];
		$meta['Driver'] = $data['Driver'];
		$meta['Customer'] = $data['Customer'];
		$meta['Employee'] = $data['Employee'];
        
        $meta['Settings'] = $data['Settings'];
        $meta['dateFormats'] = $data['dateFormats'];
        $meta['assets'] = $data['assets'];
        $meta['GP'] = $data['GP'];
        
        $meta['navigation'] = $this->load->view($this->theme . 'navigation', $meta,true);
        $this->load->view($this->theme . 'header', $meta);
        $this->load->view($this->theme . 'top_navigation', $meta);
        $this->load->view($this->theme . $page, $data);
        $this->load->view($this->theme . 'footer');
    }

}
