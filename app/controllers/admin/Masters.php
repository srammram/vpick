<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Masters extends MY_Controller
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
		//$this->upload_path = 'assets/uploads/customers/';
        //$this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
		$this->allowed_file_size = '1024';
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('masters_model');
    }
	
	function import_csv_common_cab_setting()
    {
		$this->sma->checkPermissions('import_csv_common_cab-index');
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_common_cab_setting");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
					//echo 'sssss';
                    while (($row = fgetcsv($handle, 5000, ",")) != FALSE) {
						//echo '<pre>';
						//print_r($row);
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('cab_type', 'cab_image', 'cab_make', 'cab_model', 'base_min_distance', 'base_min_distance_price', 'base_price_type', 'base_price_value', 'base_waiting_minute', 'base_waiting_price', 'package_name', 'package_price', 'option_type', 'option_price', 'package_distance', 'package_time', 'per_distance', 'per_distance_price', 'time_type', 'per_time', 'per_time_price', 'day_allowance', 'overnight_allowance', 'outstation_package_name', 'is_oneway', 'is_twoway', 'oneway_package_price', 'oneway_distance', 'twoway_package_price', 'twoway_distance', 'outstation_per_distance', 'outstation_per_distance_price', 'driver_allowance_per_day', 'driver_night_per_day', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					$cab_type_id = $this->masters_model->getCabtypeByName(trim($csv_pr['cab_type']), trim($csv_pr['is_country']));
					$cab_make_id = $this->masters_model->getCabMakeByName(trim($csv_pr['cab_make']), trim($csv_pr['is_country']));
					$cab_image_id = $this->masters_model->getCabtypeImageByName(trim($csv_pr['cab_image']));
					if ($cab_image_id != 0) {
						$cab_model = $this->masters_model->getCabModelByName($cab_type_id, $cab_make_id, trim($csv_pr['cab_model']), trim($csv_pr['is_country']));
						if($cab_model == FALSE){
							$items[] = array (
								'cab_type' => trim($csv_pr['cab_type']),
								'cab_type_id' => $cab_type_id,
								'cab_make' => trim($csv_pr['cab_make']),
								'cab_make_id' => $cab_make_id,
								'cab_image' => trim($csv_pr['cab_image']),
								'cab_image_id' => $cab_image_id,
								'cab_model' => trim($csv_pr['cab_model']),
								'is_base'	=> 1,
								'base_min_distance' => trim($csv_pr['base_min_distance']),
								'base_min_distance_price' => trim($csv_pr['base_min_distance_price']),
								'base_price_type' => trim($csv_pr['base_price_type']),
								'base_price_value' => trim($csv_pr['base_price_value']),
								'base_waiting_minute' => trim($csv_pr['base_waiting_minute']),
								'base_waiting_price' => trim($csv_pr['base_waiting_price']),
								'package_name' => trim($csv_pr['package_name']),
								'package_price' => trim($csv_pr['package_price']),
								'option_type' => trim($csv_pr['option_type']),
								'option_price' => trim($csv_pr['option_price']),
								'package_distance' => trim($csv_pr['package_distance']),
								'package_time' => trim($csv_pr['package_time']),
								'per_distance' => trim($csv_pr['per_distance']),
								'per_distance_price' => trim($csv_pr['per_distance_price']),
								'time_type' => trim($csv_pr['time_type']),
								'per_time' => trim($csv_pr['per_time']),
								'per_time_price' => trim($csv_pr['per_time_price']),
								'day_allowance' => trim($csv_pr['day_allowance']),
								'overnight_allowance' => trim($csv_pr['overnight_allowance']),
								'outstation_package_name' => trim($csv_pr['outstation_package_name']),
								'is_oneway' => trim($csv_pr['is_oneway']),
								'is_twoway' => trim($csv_pr['is_twoway']),
								'oneway_package_price' => trim($csv_pr['oneway_package_price']),
								'oneway_distance' => trim($csv_pr['oneway_distance']),
								'twoway_package_price' => trim($csv_pr['twoway_package_price']),
								'twoway_distance' => trim($csv_pr['twoway_distance']),
								'outstation_per_distance' => trim($csv_pr['outstation_per_distance']),
								'outstation_per_distance_price' => trim($csv_pr['outstation_per_distance_price']),
								'driver_allowance_per_day' => trim($csv_pr['driver_allowance_per_day']),
								'driver_night_per_day' => trim($csv_pr['driver_night_per_day']),
								'is_country' => trim($csv_pr['is_country']),
								'user_id' => $this->session->userdata('user_id'),
								);
						}else{
							$this->session->set_flashdata('error', lang("cab_model_exit")." ".lang("line_no")." ".$rw);
							admin_redirect("masters/import_csv_common_cab_setting");
						}
					} else {
						$this->session->set_flashdata('error', lang("cab_image_data_empty")." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_common_cab_setting");
					}
                    $rw++;
				}
				
		   }
		   
		  // echo '<pre>';
			//print_r($items);die;
        }elseif ($this->input->post('import_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_common_cab_setting");
        }
		
        if ($this->form_validation->run() == true  && $this->masters_model->import_cab($items)){
			
            $this->session->set_flashdata('message', lang("cab_setting_added"));
            admin_redirect('masters/import_csv_common_cab_setting');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_cab')));
            $meta = array('page_title' => lang('import_cab'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_common_cab_setting', $meta, $this->data);
        }
        
    }
	
	function import_csv_common_location()
    {
		$this->sma->checkPermissions('import_csv_common_location-index');
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_common_cab_setting");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
					//echo 'sssss';
                    while (($row = fgetcsv($handle, 5000, ",")) != FALSE) {
						echo '<pre>';
						print_r($row);
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('code', 'continents_name', 'country_iso', 'country_phonecode', 'country_name', 'zone_name', 'state_name', 'city_name', 'area_name', 'pincode',  'post_office');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
										
					$continent_id = $this->site->masterCheck1('continents', array('name' => trim($csv_pr['continents_name'])));
					$country_id = $this->site->masterCheck1('countries', array('name' => trim($csv_pr['country_name'])));
					$zone_id = $this->site->masterCheck1('zones', array('name' => trim($csv_pr['zone_name'])));
					$state_id = $this->site->masterCheck1('states', array('name' => trim($csv_pr['state_name'])));
					$city_id = $this->site->masterCheck1('cities', array('name' => trim($csv_pr['city_name'])));
					$area_id = $this->site->masterCheck1('areas', array('name' => trim($csv_pr['area_name'])));
					
						$cab_model = $this->masters_model->getCabModelByName($cab_type_id, $cab_make_id, trim($csv_pr['cab_model']));
						if($cab_model == FALSE){
							$items[] = array (
								'code' => trim($csv_pr['code']),
								'continents_name' => trim($csv_pr['continents_name']),
								'continent_id' => $continent_id,
								'country_name' => trim($csv_pr['country_name']),
								'country_iso' => trim($csv_pr['country_iso']),
								'country_phonecode' => trim($csv_pr['country_phonecode']),
								'country_id' => $country_id,
								'zone_name' => trim($csv_pr['zone_name']),
								'zone_id' => $zone_id,
								'state_name' => trim($csv_pr['state_name']),
								'state_id' => $state_id,
								'city_name' => trim($csv_pr['city_name']),
								'city_id' => $city_id,
								'area_name' => trim($csv_pr['area_name']),
								'area_id' => $area_id,
								'pincode' => trim($csv_pr['pincode']),
								'post_office' => trim($csv_pr['post_office']),
								
								);
						}else{
							$this->session->set_flashdata('error', lang("location_exit")." ".lang("line_no")." ".$rw);
							admin_redirect("masters/import_csv_common_location");
						}
					
                    $rw++;
				}
				
		   }
		   
		   
			//print_r($items);die;
        }elseif ($this->input->post('import_csv_common_location')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_common_location");
        }
		
        if ($this->form_validation->run() == true  && $this->masters_model->import_location($items)){
			
            $this->session->set_flashdata('message', lang("location_added"));
            admin_redirect('masters/import_csv_common_location');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/import_csv_common_location'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_location')));
            $meta = array('page_title' => lang('import_location'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_common_location', $meta, $this->data);
        }
        
    }
	
	function index($countryCode){
		$this->sma->checkPermissions('settings-index');
		if($this->session->userdata('group_id') == 1){
			if(!empty($_GET['countryCode'])){
				$countryCode = $_GET['countryCode'];	
			}else{
				$countryCode = $countryCode;		
			}
			
		}else{
			$countryCode = $this->countryCode;	
		}
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
		//$this->form_validation->set_rules('driver_ride_accept', lang('driver_ride_accept'), 'trim|required');
		$this->form_validation->set_rules('search_kilometer', lang('search_kilometer'), 'trim|required');
		if ($this->form_validation->run() == true) {
			$data = array(
				'driver_working_hours_limit' => $this->input->post('driver_working_hours_limit') != 0 ?  $this->input->post('driver_working_hours_limit') : 4,
				'due_month' => $this->input->post('due_month') ? $this->input->post('due_month') : 0,
				'estimate_fare_enable' => $this->input->post('estimate_fare_enable'),
				'ride_cancel_allocated_another_driver' => $this->input->post('ride_cancel_allocated_another_driver'),
				'cancel_maximum_fare' => $this->input->post('cancel_maximum_fare'),
				
				//'driver_cancel_charge' => $this->input->post('driver_cancel_charge') ? $this->input->post('driver_cancel_charge') : 0,
				//'customer_cancel_charge' => $this->input->post('customer_cancel_charge') ? $this->input->post('customer_cancel_charge') : 0,
				'due_year' => $this->input->post('due_year') ? $this->input->post('due_year') : 0,
				//'driver_ride_accept' => $this->input->post('driver_ride_accept'),
				'search_kilometer' => $this->input->post('search_kilometer') ? $this->input->post('search_kilometer') : 20,
				'support_email' => $this->input->post('support_email') ? $this->input->post('support_email') : 'owner@heyycab.com',
				'support_mobile' => $this->input->post('support_mobile') ? $this->input->post('support_mobile') : '',
				'support_whatsapp' => $this->input->post('support_whatsapp') ? $this->input->post('support_whatsapp') : '',
				'outstation_min_kilometer' => $this->input->post('cityride_max_kilometer') ? $this->input->post('cityride_max_kilometer') + 1 : 50 + 1,
				'cityride_max_kilometer' => $this->input->post('cityride_max_kilometer') ? $this->input->post('cityride_max_kilometer') : 50,
				'rental_max_kilometer' => $this->input->post('cityride_max_kilometer') ? $this->input->post('cityride_max_kilometer') : 50,
				//'driver_admin_payment_option' => $this->input->post('driver_admin_payment_option'),
				'driver_admin_payment_percentage' => $this->input->post('driver_admin_payment_percentage') ? $this->input->post('driver_admin_payment_percentage') : 5,
				//'vendor_admin_payment_option' => $this->input->post('vendor_admin_payment_option'),
				//'vendor_admin_payment_percentage' => $this->input->post('vendor_admin_payment_percentage'),
				//'driver_vendor_payment_option' => $this->input->post('driver_vendor_payment_option'),
				//'driver_vendor_payment_percentage' => $this->input->post('driver_vendor_payment_percentage'),
			//	'driver_admin_payment_duration' => $this->input->post('driver_admin_payment_duration') ? $this->input->post('driver_admin_payment_duration') : 0,
				//'vendor_admin_payment_duration' => $this->input->post('vendor_admin_payment_duration'),
				//'driver_vendor_payment_duration' => $this->input->post('driver_vendor_payment_duration'),
				'waiting_charges' => $this->input->post('waiting_charges') ? $this->input->post('waiting_charges') : '0.00',
				'waiting_time' => $this->input->post('waiting_time') ? $this->input->post('waiting_time') : '0',
				
				'help_number_one' => $this->input->post('help_number_one') ? $this->input->post('help_number_one') : '',
				'help_number_two' => $this->input->post('help_number_two') ? $this->input->post('help_number_two') : '',
				'help_number_three' => $this->input->post('help_number_three') ? $this->input->post('help_number_three') : '',
				'help_number_four' => $this->input->post('help_number_four') ? $this->input->post('help_number_four') : '',
				'help_number_five' => $this->input->post('help_number_five') ? $this->input->post('help_number_five') : '',
				'camera_enable' => $this->input->post('camera_enable'),
				'timezone' =>  $this->input->post('timezone'),
				'dateofbirth' =>  $this->input->post('dateofbirth'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'register_otp_enable' => $this->input->post('register_otp_enable'),
				/*'outstation_min_balance' => $this->input->post('outstation_min_balance'),
				'rental_min_balance' => $this->input->post('rental_min_balance'),
				'cityride_min_balance' => $this->input->post('cityride_min_balance'),*/
				'customer_user_reg' => $this->input->post('customer_user_reg'),
				'customer_rides' => $this->input->post('customer_rides'),
				'customer_rides_no' => $this->input->post('customer_rides_no'),
				'customer_validation' => $this->input->post('customer_validation'),
				'customer_using_type' => $this->input->post('customer_using_type'),
				'customer_using_members' => $this->input->post('customer_using_members'),
				'customer_amount' => $this->input->post('customer_amount'),
				
				'driver_user_reg' => $this->input->post('driver_user_reg'),
				'driver_rides' => $this->input->post('driver_rides'),
				'driver_rides_no' => $this->input->post('driver_rides_no'),
				'driver_validation' => $this->input->post('driver_validation'),
				'driver_using_type' => $this->input->post('driver_using_type'),
				'driver_using_members' => $this->input->post('driver_using_members'),
				'driver_amount' => $this->input->post('driver_amount'),
				
				'driver_time' => $this->input->post('driver_time'),
				'driver_count' => $this->input->post('driver_count'),
				'customer_time' => $this->input->post('customer_time'),
				//'no_of_driver_cancel' => $this->input->post('no_of_driver_cancel'),
				//'no_of_customer_cancel' => $this->input->post('no_of_customer_cancel'),
				'trafic_distance' => $this->input->post('trafic_distance'),
				'wallet_min_add_money' => $this->input->post('wallet_min_add_money'),
				//'driver_default_set_payment' => $this->input->post('driver_default_set_payment'),
				'drop_points' => $this->input->post('drop_points'),
				
				'badge_enable' => $this->input->post('badge_enable'),
				'training_certificate_enable' => $this->input->post('training_certificate_enable'),
				'experience_certificate_enable' => $this->input->post('experience_certificate_enable'),
				'medical_certificate_enable' => $this->input->post('medical_certificate_enable'),
				'police_verification_enable' => $this->input->post('police_verification_enable'),
				'health_insurance_enable' => $this->input->post('health_insurance_enable'),
				'term_insurance_enable' => $this->input->post('term_insurance_enable'),
				'additional_contact_enable' => $this->input->post('additional_contact_enable'),
				'emission_norms_enable' => $this->input->post('emission_norms_enable'),
				'vehicle_tracking_enable' => $this->input->post('vehicle_tracking_enable'),
				'fire_extinguisher_enable' => $this->input->post('fire_extinguisher_enable'),
				'child_lock_mechanism_enable' => $this->input->post('child_lock_mechanism_enable'),
				'interior_vehicle_enable' => $this->input->post('interior_vehicle_enable'),
				'taxi_roof_sign_enable' => $this->input->post('taxi_roof_sign_enable'),
				'e_challans_clearance_enable' => $this->input->post('e_challans_clearance_enable'),
				
				//'ride_cancel_billing_screen_enable' => $this->input->post('ride_cancel_billing_screen_enable'),
				'ride_cancel_driver_on_the_way_km_fare_enable' => $this->input->post('ride_cancel_driver_on_the_way_km_fare_enable'),
				'ride_cancel_driver_on_the_way_percentage_enable' => $this->input->post('ride_cancel_driver_on_the_way_percentage_enable'),
				'ride_cancel_driver_on_the_way_percentage_value' => $this->input->post('ride_cancel_driver_on_the_way_percentage_value'),

			);
			
			
			
		}elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/index?countryCode=".$countryCode);
        }
		
		if ($this->form_validation->run() == true && $this->masters_model->updateSetting($data, $countryCode)) {
			$this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect("masters/index?countryCode=".$countryCode);
		}else{
			$this->data['countryCode'] = $countryCode;
			$this->data['dataSettings'] = $this->masters_model->getSettingscountry($countryCode);
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$this->data['action'] = $action;
			$countrylist = $this->site->getcountryCodeID($countryCode);
			if($countryCode != ''){
				$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => $countrylist->name.lang('settings')));
				$meta = array('page_title' => $countrylist->name.lang('settings'), 'bc' => $bc);
			}else{
				$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('settings')));
				$meta = array('page_title' => lang('settings'), 'bc' => $bc);
			}
			
			$this->page_construct('masters/index', $meta, $this->data);

		}
		
	}
	
	
	
	/*###### Tax*/
    function countrywisesetting($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('countrywisesetting')));
        $meta = array('page_title' => lang('countrywisesetting'), 'bc' => $bc);
        $this->page_construct('masters/countrywisesetting', $meta, $this->data);
    }
    function getCountrywisesetting(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id, country_name, is_default")
            ->from("countrywisesetting")
			//->where('tax.is_delete', 0)
			->edit_column('is_default', '$1', 'is_default');
             //->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_tax/$1') . "' class='tip' title='" . lang("edit_tax") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_countrywisesetting/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			//$delete = "<a href='" . admin_url('welcome/delete/tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_countrywisesetting(){
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
        $this->form_validation->set_rules('timezone', lang("timezone"), 'required');
		
        if ($this->form_validation->run() == true) {
			
			//if(!empty($this->input->post('country_id'))){
				$check = $this->masters_model->countryChecking($this->input->post('country_id'), $countryCode);
				if($check == true){
					$this->session->set_flashdata('error', lang("already_exists_setting"));
					admin_redirect('masters/countrywisesetting');
				}
			//}
			
			if($this->input->post('country_id') != ''){
				$is_default = 0;
			}else{
				$is_default = 1;
			}
			
			$country_name = $this->masters_model->getCountryname($this->input->post('country_id'), $countryCode);
            $data = array(
                'country_id' => $this->input->post('country_id'),
				'country_name' => $country_name ? $country_name : 'Default Setting',
				'timezone' =>  $this->input->post('timezone'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'is_default' => $is_default,
               
            );
			
           
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_countrywisesetting($data, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("countrywisesetting_added"));
            admin_redirect('masters/countrywisesetting');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/countrywisesetting'), 'page' => lang('countrywisesetting')), array('link' => '#', 'page' => lang('add_countrywisesetting')));
            $meta = array('page_title' => lang('add_countrywisesetting'), 'bc' => $bc);
			$this->data['country'] = $this->masters_model->getALLCountry();
			
            $this->page_construct('masters/add_countrywisesetting', $meta, $this->data);
        }
    }
    function edit_countrywisesetting($id){
		$result = $this->masters_model->getCountrywisesettingby_ID($id, $countryCode);
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
		
		
		
		 $this->form_validation->set_rules('timezone', lang("timezone"), 'required');
		
        if ($this->form_validation->run() == true) {
			if($result->country_id != $this->input->post('country_id')){
				
				$check = $this->masters_model->countryChecking($this->input->post('country_id'), $countryCode);
				if($check == true){
					$this->session->set_flashdata('error', lang("already_exists_setting"));
					admin_redirect('masters/countrywisesetting');
				}
			}
			if($this->input->post('country_id') != ''){
				$is_default = 0;
			}else{
				$is_default = 1;
			}
			
			$country_name = $this->masters_model->getCountryname($this->input->post('country_id'), $countryCode);
			
            $data = array(
                'country_id' => $this->input->post('country_id'),
				'country_name' => $country_name ? $country_name : 'Default Setting',
				'timezone' =>  $this->input->post('timezone'),
				'login_otp_enable' => $this->input->post('login_otp_enable'),
				'device_change_otp_enable' => $this->input->post('device_change_otp_enable'),
				'ride_otp_enable' => $this->input->post('ride_otp_enable'),
				'address_enable' => $this->input->post('address_enable'),
				'account_holder_name_enable' => $this->input->post('account_holder_name_enable'),
				'bank_name_enable' => $this->input->post('bank_name_enable'),
				'branch_name_enable' => $this->input->post('branch_name_enable'),
				'ifsc_code_enable' => $this->input->post('ifsc_code_enable'),
				'aadhaar_enable' => $this->input->post('aadhaar_enable'),
				'pancard_enable' => $this->input->post('pancard_enable'),
				'license_enable' => $this->input->post('license_enable'),
				'police_enable' => $this->input->post('police_enable'),
				'loan_enable' => $this->input->post('loan_enable'),
				'vendor_enable' => $this->input->post('vendor_enable'),
				'cab_registration_enable' => $this->input->post('cab_registration_enable'),
				'taxation_enable' => $this->input->post('taxation_enable'),
				'insurance_enable' => $this->input->post('insurance_enable'),
				'permit_enable' => $this->input->post('permit_enable'),
				'authorisation_enable' => $this->input->post('authorisation_enable'),
				'fitness_enable' => $this->input->post('fitness_enable'),
				'speed_enable' => $this->input->post('speed_enable'),
				'puc_enable' => $this->input->post('puc_enable'),
				'is_default' => $is_default,
               
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_countrywisesetting($id,$data, $is_default, $countryCode)){
			
            $this->session->set_flashdata('message', lang("countrywisesetting_updated"));
            admin_redirect('masters/countrywisesetting');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/countrywisesetting'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('edit_countrywisesetting')));
            $meta = array('page_title' => lang('edit_countrywisesetting'), 'bc' => $bc);
            $this->data['setting'] = $result;
			$this->data['id'] = $id;
			$this->data['country'] = $this->masters_model->getALLCountry();
            $this->page_construct('masters/edit_countrywisesetting', $meta, $this->data);
        }
    }
    function countrywisesetting_status($status,$id){
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
		$this->masters_model->update_tax_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### User department*/
    function user_department($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('user_department')));
        $meta = array('page_title' => lang('user_department'), 'bc' => $bc);
        $this->page_construct('masters/user_department', $meta, $this->data);
    }
    function getUser_department(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('user_department')}.id as id, {$this->db->dbprefix('user_department')}.name, {$this->db->dbprefix('user_department')}.status as status")
            ->from("user_department")
			->where('user_department.is_delete', 0)
			//->where('user_department.is_country', $countryCode)
            ->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_user_department") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/user_department/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$delete."</div>", "id");
			
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_user_department(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');  
		
		  
        if ($this->form_validation->run() == true) {
			
			$check = $this->site->masterCheck('user_department', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/user_department");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_user_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/user_department");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_user_department($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("user_department_added"));
            admin_redirect('masters/user_department');
        } else {
			
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
            $this->load->view($this->theme . 'masters/add_user_department', $this->data);
        }
    }
    function edit_user_department($id){
		$result = $this->masters_model->getUser_departmentby_ID($id, $countryCode);
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
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
        
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('user_department', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/user_department");
					exit;	
				}
			}

            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_user_department')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/user_department");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_user_department($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("user_department_updated"));
            admin_redirect("masters/user_department");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_user_department', $this->data);
        }
    }
   
    function user_department_status($status,$id){
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
        $this->masters_model->update_user_department_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Category*/
    function taxi_category($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_category')));
        $meta = array('page_title' => lang('cab_category'), 'bc' => $bc);
        $this->page_construct('masters/taxi_category', $meta, $this->data);
    }
    function getTaxi_category(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_categorys')}.id,{$this->db->dbprefix('taxi_categorys')}.name,{$this->db->dbprefix('taxi_categorys')}.status as status, country.name as instance_country ")
            ->from("taxi_categorys")
			->join("countries country", " country.iso = taxi_categorys.is_country", "left")
			->where('taxi_categorys.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_categorys.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_categorys.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_category") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_category/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_categorys/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_category(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_categorys', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/taxi_category");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_taxi_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_category");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_category($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cab_categorys_added"));
            admin_redirect('masters/taxi_category');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_taxi_category', $this->data);
        }
    }
    function edit_taxi_category($id){
		$result = $this->masters_model->getTaxi_categoryby_ID($id, $countryCode);
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
		
		$this->form_validation->set_rules('name', lang("name"), 'required');    
        
        
		
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_categorys', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/taxi_category");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_taxi_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_category");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_category($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cab_category_updated"));
            admin_redirect("masters/taxi_category");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_category', $this->data);
        }
    }
   
    function taxi_category_status($status,$id){
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
        $this->masters_model->update_taxi_category_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Fuel*/
    function taxi_fuel($action=false){
		$this->sma->checkPermissions('cab_fuel-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_fuel')));
        $meta = array('page_title' => lang('cab_fuel'), 'bc' => $bc);
        $this->page_construct('masters/taxi_fuel', $meta, $this->data);
    }
    function getTaxi_fuel(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_fuel')}.id as id,{$this->db->dbprefix('taxi_fuel')}.name,{$this->db->dbprefix('taxi_fuel')}.status as status, country.name as instance_country")
            ->from("taxi_fuel")
			->join("countries country", " country.iso = taxi_fuel.is_country", "left")
			->where('taxi_fuel.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_fuel.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_fuel.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_fuel/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_fuel") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_fuel/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_fuel/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_fuel(){
		$this->sma->checkPermissions('cab_fuel-add');
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_fuel', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/taxi_fuel");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_taxi_fuel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_fuel");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_fuel($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cab_fuel_added"));
            admin_redirect('masters/taxi_fuel');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_taxi_fuel', $this->data);
        }
    }
    function edit_taxi_fuel($id){
		$this->sma->checkPermissions('cab_fuel-edit');
		$result = $this->masters_model->getTaxi_fuelby_ID($id);
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
        
      
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_fuel', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/taxi_fuel");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
        } elseif ($this->input->post('edit_taxi_fuel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_fuel");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_fuel($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cab_fuel_updated"));
            admin_redirect("masters/taxi_fuel");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_fuel', $this->data);
        }
    }
   
    function taxi_fuel_status($status,$id){
		$this->sma->checkPermissions('cab_fuel-status');
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
        $this->masters_model->update_taxi_fuel_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Type*/
    function taxi_type($action=false){
		$this->sma->checkPermissions('cab_type-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_type')));
        $meta = array('page_title' => lang('cab_type'), 'bc' => $bc);
        $this->page_construct('masters/taxi_type', $meta, $this->data);
    }
    function getTaxi_type(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_type')}.id as id, {$this->db->dbprefix('taxi_type')}.name,  timage.image, timage.image_hover, timage.mapcar, {$this->db->dbprefix('taxi_type')}.status as status, country.name as instance_country")
            ->from("taxi_type")
			->join("countries country", " country.iso = taxi_type.is_country", "left")
			->join("taxi_image timage", " timage.id = taxi_type.taxi_image_id", "left")
			//->join("taxi_categorys p", "p.id = taxi_type.category_id AND p.is_country = '".$countryCode."'")
			->where('taxi_type.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_type.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1 ){
				$this->datatables->where("taxi_type.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_type(){
		$this->sma->checkPermissions('cab_type-add');
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('category_id', lang("category"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_type', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/taxi_type");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
				'category_id' => $this->input->post('category_id'),
				'taxi_image_id' => $this->input->post('taxi_image_id'),
                'status' => 1,
            );
			
			if ($_FILES['outstation_image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('outstation_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$outstation_image = $this->upload->file_name;
				$data['outstation_image'] = 'document/taxi_type/'.$outstation_image;
				$config = NULL;
			}
			
			if ($_FILES['image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$image = $this->upload->file_name;
				$data['image'] = 'document/taxi_type/'.$image;
				$config = NULL;
			}
			
			if ($_FILES['image_hover']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image_hover')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$image_hover = $this->upload->file_name;
				$data['image_hover'] = 'document/taxi_type/'.$image_hover;
				$config = NULL;
			}
			
			if ($_FILES['mapcar']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('mapcar')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$mapcar = $this->upload->file_name;
				$data['mapcar'] = 'document/taxi_type/'.$mapcar;
				$config = NULL;
			}
        }elseif ($this->input->post('add_taxi_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_type");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_type($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cab_type_added"));
            admin_redirect('masters/taxi_type');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLTaxi_category($countryCode);
			$this->data['typeimage']= $this->masters_model->getTypeImage();
            $this->load->view($this->theme . 'masters/add_taxi_type', $this->data);
			
        }
    }
    function edit_taxi_type($id){
		$this->sma->checkPermissions('cab_type-edit');
		$result = $this->masters_model->getTaxi_typeby_ID($id);
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
		$this->form_validation->set_rules('category_id', lang("category"), 'required');      
       
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_type', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/taxi_type");
					exit;	
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'category_id' => $this->input->post('category_id'),
				'taxi_image_id' => $this->input->post('taxi_image_id'),
            );
			
			if ($_FILES['outstation_image']['size'] > 0) {
				
				
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('outstation_image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$outstation_image = $this->upload->file_name;
				$data['outstation_image'] = 'document/taxi_type/'.$outstation_image;
				$config = NULL;
			}
			
			if ($_FILES['image']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$image = $this->upload->file_name;
				$data['image'] = 'document/taxi_type/'.$image;
				$config = NULL;
			}
			
			if ($_FILES['image_hover']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('image_hover')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$image_hover = $this->upload->file_name;
				$data['image_hover'] = 'document/taxi_type/'.$image_hover;
				$config = NULL;
			}
			
			if ($_FILES['mapcar']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'document/taxi_type/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('mapcar')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$mapcar = $this->upload->file_name;
				$data['mapcar'] = 'document/taxi_type/'.$mapcar;
				$config = NULL;
			}
			
        } elseif ($this->input->post('edit_taxi_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_type");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_type($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cab_type_updated"));
            admin_redirect("masters/taxi_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLTaxi_category($countryCode);
			$this->data['result'] = $result;
			$this->data['typeimage']= $this->masters_model->getTypeImage();
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_type', $this->data);
        }
    }
   
    function taxi_type_status($status,$id){
		$this->sma->checkPermissions('cab_type-status');
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
        $this->masters_model->update_taxi_type_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Make*/
    function taxi_make($action=false){
		$this->sma->checkPermissions('cab_make-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_make')));
        $meta = array('page_title' => lang('cab_make'), 'bc' => $bc);
        $this->page_construct('masters/taxi_make', $meta, $this->data);
    }
    function getTaxi_make(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_make')}.id as id, {$this->db->dbprefix('taxi_make')}.name,  {$this->db->dbprefix('taxi_make')}.status as status, country.name as instance_country")
            ->from("taxi_make")
			->join("countries country", " country.iso = taxi_make.is_country", "left")
			->where('taxi_make.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_make.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_make.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_make/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_make/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_taxi_make(){
		$this->sma->checkPermissions('cab_make-add');
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_make', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/taxi_make");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'status' => 1,
            );
			$data_model = array();
			/*$data_model = array(
				'name' => $this->input->post('taxi_model'),
				'type_id' => $this->input->post('type_id'),
				'status' => 1,
			);*/
			
        }elseif ($this->input->post('add_taxi_make')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_make");
        }
		
        if ($this->form_validation->run() == true && $sid = $this->masters_model->add_taxi_make($data, $data_model, $countryCode)){
			$ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
			if($ref[0] != NULL){
            	admin_redirect($ref[0] . '?make=' . $sid);
			}else{
				$this->session->set_flashdata('message', lang("cab_make_added"));
            	admin_redirect('masters/taxi_make');
			}
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->load->view($this->theme . 'masters/add_taxi_make', $this->data);
			
        }
    }
    function edit_taxi_make($id){
		$this->sma->checkPermissions('cab_make-edit');
		$result = $this->masters_model->getTaxi_makeby_ID($id);
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
		    
       
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_make', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/taxi_make");
					exit;	
				}
			}
            $data = array(
				'name' => $this->input->post('name'),
            );
			
		
			
        } elseif ($this->input->post('edit_taxi_make')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_make");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_make($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cab_make_updated"));
            admin_redirect("masters/taxi_make");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_make', $this->data);
        }
    }
   
    function taxi_make_status($status,$id){
		$this->sma->checkPermissions('cab_make-status');
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
        $this->masters_model->update_taxi_make_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Taxi Model*/
    function taxi_model($action=false){
		$this->sma->checkPermissions('cab_model-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cab_model')));
        $meta = array('page_title' => lang('cab_model'), 'bc' => $bc);
        $this->page_construct('masters/taxi_model', $meta, $this->data);
    }
    function getTaxi_model(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('taxi_model')}.id as id, {$this->db->dbprefix('taxi_model')}.name,  tt.name as type_name, tm.name as make_name, {$this->db->dbprefix('taxi_model')}.status as status, country.name as instance_country")
            ->from("taxi_model")
			->join("countries country", " country.iso = taxi_model.is_country", "left")
			->join("taxi_type tt", "tt.id = taxi_model.type_id ", "left")
			->join("taxi_make tm", "tm.id = taxi_model.make_id ", "left")
			->where('taxi_model.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("taxi_model.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("taxi_model.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_taxi_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_taxi_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_taxi_model/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/taxi_model/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	
    function add_taxi_model(){
		$this->sma->checkPermissions('cab_model-add');
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('taxi_model', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/taxi_model");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
				'type_id' => $this->input->post('type_id'),
				'make_id' => $this->input->post('make_id'),
                'status' => 1,
            );
			
        }elseif ($this->input->post('add_taxi_model')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_model");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_taxi_model($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cab_model_added"));
            admin_redirect('masters/taxi_model');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->load->view($this->theme . 'masters/add_taxi_model', $this->data);
			
        }
    }
    function edit_taxi_model($id){
		$this->sma->checkPermissions('cab_model-edit');
		$result = $this->masters_model->getTaxi_modelby_ID($id);
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
		    
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('taxi_model', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/taxi_model");
					exit;	
				}
			}

			
            $data = array(
				'name' => $this->input->post('name'),
				'type_id' => $this->input->post('type_id'),
				'make_id' => $this->input->post('make_id'),
            );
			
		
			
        } elseif ($this->input->post('edit_taxi_model')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/taxi_model");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_taxi_model($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("cab_model_updated"));
            admin_redirect("masters/taxi_model");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			$this->data['makes'] = $this->masters_model->getALLTaxi_make($countryCode);
			$this->data['types'] = $this->masters_model->getALLTaxi_type($countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_taxi_model', $this->data);
        }
    }
   
    function taxi_model_status($status,$id){
		$this->sma->checkPermissions('cab_model-status');
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
        $this->masters_model->update_taxi_model_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### License Type*/
    function license_type($action=false){
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('license_type')));
        $meta = array('page_title' => lang('license_type'), 'bc' => $bc);
        $this->page_construct('masters/license_type', $meta, $this->data);
    }
    function getLicense_type(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
       /* $this->datatables
            ->select("id,name, details, status")
            ->from("license_type")
            ->edit_column('status', '$1__$2', 'status, id')*/
		 $this->datatables->select("{$this->db->dbprefix('license_type')}.id as id, c.name as country_name, {$this->db->dbprefix('license_type')}.name, {$this->db->dbprefix('license_type')}.details, {$this->db->dbprefix('license_type')}.status as status, country.name as instance_country")
            ->from("license_type")
			->join("countries country", " country.iso = license_type.is_country", "left")
			->join("countries c", "c.id = license_type.country_id", 'left')	
			->where('license_type.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("license_type.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("license_type.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_license_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_license_type") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_license_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/license_type/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_license_type(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			
			$check_name = $this->masters_model->checkLicense($this->input->post('name'), $this->input->post('country_id'), 1, NULL, $countryCode);
			if($check_name == 1){
				$this->session->set_flashdata('error', lang("license_type_already_exit"));
            	admin_redirect('masters/license_type');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'details' => $this->input->post('details'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
                'status' => 1,
            );
			
        }elseif ($this->input->post('add_license_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/license_type");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_license_type($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("license_type_added"));
            admin_redirect('masters/license_type');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_license_type', $this->data);
        }
    }
    function edit_license_type($id){
		$result = $this->masters_model->getLicense_typeby_ID($id);
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
       
        if ($this->form_validation->run() == true) {

			$check_name = $this->masters_model->checkLicense($this->input->post('name'), $this->input->post('country_id'), 2, $result->name, $countryCode);
			if($check_name == 1){
				$this->session->set_flashdata('error', lang("license_type_already_exit"));
            	admin_redirect('masters/license_type');
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'details' => $this->input->post('details'),
				'continent_id' => $this->input->post('continent_id'),
				'country_id' => $this->input->post('country_id'),
            );
        } elseif ($this->input->post('edit_license_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/license_type");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_license_type($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("license_type_updated"));
            admin_redirect("masters/license_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['continents'] = $this->masters_model->getALLContinents();
			$this->data['lcountrys'] = $this->masters_model->getCountry_bycontinent($result->continent_id, $countryCode);
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_license_type', $this->data);
        }
    }
   
    function license_type_status($status,$id){
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
        $this->masters_model->update_license_type_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Company*/
    function company($action = NULL)
    {
		$this->sma->checkPermissions('company-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('company')));
        $meta = array('page_title' => lang('company'), 'bc' => $bc);
        $this->page_construct('masters/company', $meta, $this->data);
    }
    function getCompany(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('company')}.id as id, {$this->db->dbprefix('company')}.code,{$this->db->dbprefix('company')}.name,{$this->db->dbprefix('company')}.address, {$this->db->dbprefix('company')}.is_office as is_office, {$this->db->dbprefix('company')}.email, {$this->db->dbprefix('company')}.telephone, {$this->db->dbprefix('company')}.fax, {$this->db->dbprefix('company')}.register_number, {$this->db->dbprefix('company')}.starting_year, {$this->db->dbprefix('company')}.status as status, country.name as instance_country")
            ->from("company")
			->join("countries country", " country.iso = company.is_country", "left")
			->where('company.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("company.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("company.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_office', '$1', 'is_office')
             ->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_company/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$edit = "";
			$delete = "<a href='" . admin_url('welcome/delete/company/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	
    function add_company(){
		$this->sma->checkPermissions('company-add');
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
        $this->form_validation->set_rules('is_office', lang("is_office"), 'required');
        
		$this->form_validation->set_rules('register_number', lang("register_number"), 'required');    
		if($this->input->post('is_office') == 0){
			$this->form_validation->set_rules('register_number', lang("register_number"), 'required|is_unique[company.register_number]');    
		}
        if ($this->form_validation->run() == true) {
			/*$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('account_already_exits'));
            	admin_redirect("masters/bank");
				exit;	
			}*/
			
            $data = array(
				'code' => date('YmdHis'),
				'name' => $this->input->post('name'),
                'is_office' => $this->input->post('is_office'),
				'branch_id' => $this->input->post('branch_id'),
				'address' => $this->input->post('address'),
				'lat' => $this->input->post('lat'),
				'lng' => $this->input->post('lng'),
				'email' => $this->input->post('email'),
				'telephone' => $this->input->post('telephone'),
				'fax' => $this->input->post('fax'),
				'register_number' => $this->input->post('register_number'),
				'starting_year' => $this->input->post('starting_year'),
                'status' => 1,
            );
			$company_bank = array();
			if(count($_POST['bank_id']) != 0){
				for($i=0; $i<count($_POST['bank_id']); $i++){
					$company_bank[]  = array(
						'bank_id' => $_POST['bank_id'][$i],
						'status' => 1
					);
				}
			}
           
        }elseif ($this->input->post('add_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/bank");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_company($data, $company_bank, $countryCode)){
			
            $this->session->set_flashdata('message', lang("company_added"));
            admin_redirect('masters/company');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/company'), 'page' => lang('company')), array('link' => '#', 'page' => lang('add_company')));
            $meta = array('page_title' => lang('add_company'), 'bc' => $bc);
			$this->data['AllBanks'] = $this->masters_model->getALLBank($countryCode);
            $this->page_construct('masters/add_company', $meta, $this->data);
        }
    }
    function edit_company($id){
		$this->sma->checkPermissions('company-edit');
		$result = $this->masters_model->getCompanyby_ID($id);
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
		
        $this->form_validation->set_rules('is_office', lang("is_office"), 'required');
				
        if ($this->form_validation->run() == true) {
			/*if ($this->input->post('account_no') != $result->account_no) {
				$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('account_already_exits'));
					admin_redirect("masters/bank");
					exit;	
				}
			}*/
			
            
            $data = array(
				'is_office' => $this->input->post('is_office'),
				'name' => $this->input->post('name'),
				'branch_id' => $this->input->post('branch_id'),
				'address' => $this->input->post('address'),
				'lat' => $this->input->post('lat'),
				'lng' => $this->input->post('lng'),
				'email' => $this->input->post('email'),
				'telephone' => $this->input->post('telephone'),
				'fax' => $this->input->post('fax'),
				'register_number' => $this->input->post('register_number'),
				'starting_year' => $this->input->post('starting_year'),
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_company($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("company_updated"));
            admin_redirect('masters/company');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/company'), 'page' => lang('company')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_company'), 'bc' => $bc);
            $this->data['company'] = $result;
            $this->page_construct('masters/edit_company', $meta, $this->data);
        }
    }
    function company_status($status,$id){
		$this->sma->checkPermissions('company-status');
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
		$this->masters_model->update_company_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Bank*/
    function bank($action = NULL)
    {
		$this->sma->checkPermissions('bank-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('bank')));
        $meta = array('page_title' => lang('bank'), 'bc' => $bc);
        $this->page_construct('masters/bank', $meta, $this->data);
    }
    function getBank(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('admin_bank')}.id as id, {$this->db->dbprefix('admin_bank')}.account_holder_name,{$this->db->dbprefix('admin_bank')}.account_no, {$this->db->dbprefix('admin_bank')}.bank_name, {$this->db->dbprefix('admin_bank')}.branch_name, {$this->db->dbprefix('admin_bank')}.ifsc_code, {$this->db->dbprefix('admin_bank')}.account_type, {$this->db->dbprefix('admin_bank')}.status as status, country.name as instance_country")
            ->from("admin_bank")
			->join("countries country", " country.iso = admin_bank.is_country", "left")
			->where('admin_bank.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("admin_bank.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("admin_bank.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('account_type', '$1', 'account_type')
             ->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_bank/$1') . "' class='tip' title='" . lang("edit_bank") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_bank/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/admin_bank/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
	
    function add_bank(){
		$this->sma->checkPermissions('bank-add');
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
        $this->form_validation->set_rules('account_no', lang("account_no"), 'required');
        $this->form_validation->set_rules('account_holder_name', lang("account_holder_name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('account_already_exits'));
            	admin_redirect("masters/bank");
				exit;	
			}
			$admin_user = $this->site->getAdminUser($countryCode, 2);
            $data = array(
				'account_type' => $this->input->post('account_type'),
                'account_holder_name' => $this->input->post('account_holder_name'),
                'account_no' =>$this->input->post('account_no'),
				'bank_name' => $this->input->post('account_type') == 0 ? $this->input->post('bank_name') : '',
				'ifsc_code' =>$this->input->post('account_type') == 0 ? $this->input->post('ifsc_code') : '',
				'branch_name' => $this->input->post('account_type') == 0 ? $this->input->post('branch_name') : '',
				'user_id' => $admin_user,
                'status' => 1,
            );
           
        }elseif ($this->input->post('add_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/bank");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_bank($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("bank_added"));
            admin_redirect('masters/bank');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('add_bank')));
            $meta = array('page_title' => lang('add_bank'), 'bc' => $bc);
            $this->page_construct('masters/add_bank', $meta, $this->data);
        }
    }
    function edit_bank($id){
		$this->sma->checkPermissions('bank-edit');
		$result = $this->masters_model->getBankby_ID($id);
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
		
        $this->form_validation->set_rules('account_no', lang("account_no"), 'required');
		$this->form_validation->set_rules('account_holder_name', lang("account_holder_name"), 'required');  
		
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('account_no') != $result->account_no) {
				$check = $this->site->masterCheck('admin_bank', array('account_no' => $this->input->post('account_no'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('account_already_exits'));
					admin_redirect("masters/bank");
					exit;	
				}
			}
			
            $admin_user = $this->site->getAdminUser($countryCode, 2);
            $data = array(
				'account_type' => $this->input->post('account_type'),
                'account_holder_name' => $this->input->post('account_holder_name'),
                'account_no' =>$this->input->post('account_no'),
				'bank_name' => $this->input->post('account_type') == 0 ? $this->input->post('bank_name') : '',
				'ifsc_code' =>$this->input->post('account_type') == 0 ? $this->input->post('ifsc_code') : '',
				'branch_name' => $this->input->post('account_type') == 0 ? $this->input->post('branch_name') : '',
				'user_id' => $admin_user,
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_bank($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("bank_updated"));
            admin_redirect('masters/bank');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_bank'), 'bc' => $bc);
            $this->data['bank'] = $result;
            $this->page_construct('masters/edit_bank', $meta, $this->data);
        }
    }
    function bank_status($status,$id){
		$this->sma->checkPermissions('bank-status');
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
		$this->masters_model->update_bank_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Tax*/
    function tax($action = NULL)
    {
		$this->sma->checkPermissions('tax-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('tax')));
        $meta = array('page_title' => lang('tax'), 'bc' => $bc);
        $this->page_construct('masters/tax', $meta, $this->data);
    }
    function getTax(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('tax')}.id as id, {$this->db->dbprefix('tax')}.user_type, {$this->db->dbprefix('tax')}.tax_name,{$this->db->dbprefix('tax')}.percentage, {$this->db->dbprefix('tax')}.is_default, {$this->db->dbprefix('tax')}.status as status, country.name as instance_country")
            ->from("tax")
			->join("countries country", " country.iso = tax.is_country", "left")
			->where('tax.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("tax.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("tax.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_default', '$1', 'is_default')
             ->edit_column('status', '$1__$2', 'status, id');
			
            //->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_tax/$1') . "' class='tip' title='" . lang("edit_tax") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/tax/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_tax(){
		$this->sma->checkPermissions('tax-add');
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
        $this->form_validation->set_rules('tax_type', lang("tax_type"), 'required');
		
        if ($this->form_validation->run() == true) {
			
			if($this->input->post('tax_type') == 'all'){
				$dcheck = $this->site->masterCheck('tax', array('tax_name' => $this->input->post('atax_name'), 'is_country' => $countryCode));
				$acheck = $this->site->masterCheck('tax', array('tax_name' => $this->input->post('dtax_name'), 'is_country' => $countryCode));
				if($dcheck == TRUE || $acheck == TRUE){
					$this->session->set_flashdata('error', lang('tax_name_already_exits'));
					admin_redirect("masters/tax");
					exit;
				}
				
				$data[] = array(
					'tax_name' => $this->input->post('atax_name'),
					'user_type' => 0,
					'percentage' =>$this->input->post('apercentage'),
					'start_date' => $this->input->post('is_default') == 0 ? $this->input->post('astart_date') : '0000-00-00',
					'end_date' => $this->input->post('is_default') == 0 ? $this->input->post('aend_date') : '0000-00-00',
					'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
					'status' => 1,
					'is_country' => $countryCode
				);
				
				$data[] = array(
					'tax_name' => $this->input->post('dtax_name'),
					'user_type' => 1,
					'percentage' =>$this->input->post('dpercentage'),
					'start_date' => $this->input->post('is_default') == 0 ? $this->input->post('dstart_date') : '0000-00-00',
					'end_date' => $this->input->post('is_default') == 0 ? $this->input->post('dend_date') : '0000-00-00',
					'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
					'status' => 1,
					'is_country' => $countryCode
				);
				
			}else{
				if($this->input->post('tax_type') == 'driver'){
					$tax_name  = $this->input->post('dtax_name');
					$data[] = array(
						'tax_name' => $this->input->post('dtax_name'),
						'user_type' => 1,
						'percentage' =>$this->input->post('dpercentage'),
						'start_date' => $this->input->post('is_default') == 0 ? $this->input->post('dstart_date') : '0000-00-00',
						'end_date' => $this->input->post('is_default') == 0 ? $this->input->post('dend_date') : '0000-00-00',
						'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
						'status' => 1,
						'is_country' => $countryCode
					);
				}elseif($this->input->post('tax_type') == 'admin'){
					$tax_name = $this->input->post('atax_name');
					$data[] = array(
						'tax_name' => $this->input->post('atax_name'),
						'user_type' => 0,
						'percentage' =>$this->input->post('apercentage'),
						'start_date' => $this->input->post('is_default') == 0 ? $this->input->post('astart_date') : '0000-00-00',
						'end_date' => $this->input->post('is_default') == 0 ? $this->input->post('aend_date') : '0000-00-00',
						'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
						'status' => 1,
						'is_country' => $countryCode
					);
				}
				$check = $this->site->masterCheck('tax', array('tax_name' => $tax_name, 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('tax_name_already_exits'));
					admin_redirect("masters/tax");
					exit;	
				}
			}
			
			
            
			
           
        }elseif ($this->input->post('add_tax')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/tax");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_tax($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("tax_added"));
            admin_redirect('masters/tax');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/tax'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('add_tax')));
            $meta = array('page_title' => lang('add_tax'), 'bc' => $bc);
            $this->page_construct('masters/add_tax', $meta, $this->data);
        }
    }
    function edit_tax($id){
		$this->sma->checkPermissions('tax-edit');
		$result = $this->masters_model->getTaxby_ID($id);
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
		
        $this->form_validation->set_rules('tax_name', lang("tax_name"), 'required');
       
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('tax_name') != $result->tax_name) {
				$check = $this->site->masterCheck('tax', array('tax_name' => $this->input->post('tax_name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('tax_name_already_exits'));
					admin_redirect("masters/tax");
					exit;	
				}
			}
			
            $data = array(
                'tax_name' => $this->input->post('tax_name'),
                'percentage' =>$this->input->post('percentage'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_tax($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("tax_updated"));
            admin_redirect('masters/tax');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/tax'), 'page' => lang('tax')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_tax'), 'bc' => $bc);
            $this->data['tax'] = $result;
            $this->page_construct('masters/edit_tax', $meta, $this->data);
        }
    }
    function tax_status($status,$id){
		$this->sma->checkPermissions('tax-status');
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
		$this->masters_model->update_tax_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Wallet Offer*/
    function walletoffer($action = NULL)
    {
		$this->sma->checkPermissions('wallet_offer-index');
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
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('wallet_offer')));
        $meta = array('page_title' => lang('wallet_offer'), 'bc' => $bc);
        $this->page_construct('masters/walletoffer', $meta, $this->data);
    }
    function getWalletoffer(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('walletoffer')}.id as id, {$this->db->dbprefix('walletoffer')}.name,{$this->db->dbprefix('walletoffer')}.amount, {$this->db->dbprefix('walletoffer')}.offer_amount, {$this->db->dbprefix('walletoffer')}.type as type, {$this->db->dbprefix('walletoffer')}.is_default as is_default, {$this->db->dbprefix('walletoffer')}.status as status, country.name as instance_country")
            ->from("walletoffer")
			->join("countries country", " country.iso = walletoffer.is_country", "left")
			->where('walletoffer.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("walletoffer.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("walletoffer.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('type', '$1__$2', 'type, id');
			
			 $this->datatables->edit_column('is_default', '$1', 'is_default');
              $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_walletoffer/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/walletoffer/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div><div>".$edit."</div><div>".$delete."</div></div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_walletoffer(){
		$this->sma->checkPermissions('wallet_offer-add');
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
        $this->form_validation->set_rules('name', lang("name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('walletoffer', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/walletoffer");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'amount' =>$this->input->post('amount'),
				'offer_amount' => $this->input->post('type') == 0 ? $this->input->post('offer_amount') : $this->input->post('amount'),
				'offer_date' =>$this->input->post('offer_date') ? $this->input->post('offer_date') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'type' => $this->input->post('type'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
            );
			
           
        }elseif ($this->input->post('add_walletoffer')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/walletoffer");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_walletoffer($data, $this->input->post('type'), $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("walletoffer_added"));
            admin_redirect('masters/walletoffer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/walletoffer'), 'page' => lang('wallet_offer')), array('link' => '#', 'page' => lang('add_walletoffer')));
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $meta = array('page_title' => lang('add_walletoffer'), 'bc' => $bc);
            $this->page_construct('masters/add_walletoffer', $meta, $this->data);
        }
    }
    function edit_walletoffer($id){
		$this->sma->checkPermissions('wallet_offer-edit');
		$result = $this->masters_model->getWalletofferby_ID($id);
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
        
		
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name && $countryCode != $result->is_country) {
				$check = $this->site->masterCheck('walletoffer', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/walletoffer");
					exit;	
				}
			}
            $data = array(
                  'name' => $this->input->post('name'),
                'amount' =>$this->input->post('amount'),
				'offer_amount' => $this->input->post('type') == 0 ? $this->input->post('offer_amount') : $this->input->post('amount'),
				'offer_date' =>$this->input->post('offer_date') ? $this->input->post('offer_date') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'type' => $this->input->post('type'),
				
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_walletoffer($id,$data, $this->input->post('type'), $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("walletoffer_updated"));
            admin_redirect('masters/walletoffer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/walletoffer'), 'page' => lang('wallet_offer')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_walletoffer'), 'bc' => $bc);
            $this->data['walletoffer'] = $result;
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $this->page_construct('masters/edit_walletoffer', $meta, $this->data);
        }
    }
    function walletoffer_status($status,$id){
		$this->sma->checkPermissions('wallet_offer-status');
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
		$this->masters_model->update_walletoffer_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Currency*/
    function currencies($action = NULL)
    {
		$this->sma->checkPermissions('currency-index');
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
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('currencies')));
        $meta = array('page_title' => lang('currencies'), 'bc' => $bc);
        $this->page_construct('masters/currencies', $meta, $this->data);
    }
    function getCurrencies(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('currencies')}.id as id, {$this->db->dbprefix('currencies')}.name,{$this->db->dbprefix('currencies')}.symbol, {$this->db->dbprefix('currencies')}.unicode_symbol, {$this->db->dbprefix('currencies')}.is_default, {$this->db->dbprefix('currencies')}.status as status, country.name as instance_country")
            ->from("currencies")
			->join("countries country", " country.iso = currencies.is_country", "left")
			->where('currencies.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("currencies.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("currencies.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('is_default', '$1', 'is_default')
             ->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_currency/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/currencies/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_currency(){
		$this->sma->checkPermissions('currency-add');
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
        $this->form_validation->set_rules('name', lang("name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('currencies', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/currencies");
				exit;	
			}
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
				'unicode_symbol' => $this->input->post('unicode_symbol'),
				'iso_code' =>$this->input->post('iso_code') ? $this->input->post('iso_code') : '',
				'numeric_iso_code' =>$this->input->post('numeric_iso_code') ? $this->input->post('numeric_iso_code') : '',
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
            );
			
           
        }elseif ($this->input->post('add_currencies')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/currencies");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_currency($data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("currency_added"));
            admin_redirect('masters/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('add_currency')));
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $meta = array('page_title' => lang('add_currency'), 'bc' => $bc);
            $this->page_construct('masters/add_currency', $meta, $this->data);
        }
    }
    function edit_currency($id){
		$this->sma->checkPermissions('currency-edit');
		$result = $this->masters_model->getCurrencyby_ID($id);
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
        
		
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('currencies', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/currencies");
					exit;	
				}
			}
            $data = array(
                'name' => $this->input->post('name'),
                'symbol' =>$this->input->post('symbol'),
				'unicode_symbol' => $this->input->post('unicode_symbol'),
				'is_default' => $this->input->post('is_default') ? $this->input->post('is_default') : 0,
				'iso_code' =>$this->input->post('iso_code') ? $this->input->post('iso_code') : '',
				'numeric_iso_code' =>$this->input->post('numeric_iso_code') ? $this->input->post('numeric_iso_code') : ''
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_currency($id,$data, $this->input->post('is_default'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("currency_updated"));
            admin_redirect('masters/currencies');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/currencies'), 'page' => lang('currencies')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_currency'), 'bc' => $bc);
            $this->data['currency'] = $result;
			$this->data['unicodesymbol'] = $this->site->Getunicodesymbol();
            $this->page_construct('masters/edit_currency', $meta, $this->data);
        }
    }
    function currency_status($status,$id){
		$this->sma->checkPermissions('currency-status');
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
		$this->masters_model->update_currency_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
    /*###### Continents*/
    function continents($action=false){
		$this->sma->checkPermissions('continents-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('continent')));
        $meta = array('page_title' => lang('continent'), 'bc' => $bc);
        $this->page_construct('masters/continents', $meta, $this->data);
    }
    function getContinents(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id,code,name")
            ->from("continents")
			->where('continents.is_delete', 0);
		
			
          //  $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_continents") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/continents/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_continent(){
		$this->sma->checkPermissions('continents-add');
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
        $this->form_validation->set_rules('name', lang("continent_name"), 'required|is_unique[continents.name]');    
		$this->form_validation->set_rules('code', lang("continent_code"), 'required|is_unique[continents.code]');    
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_continent')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/continents");
        }
        if ($this->form_validation->run() == true && $this->masters_model->add_continent($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("continent_added"));
            admin_redirect('masters/continents');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_continent', $this->data);
        }
    }
    function edit_continent($id){
		$this->sma->checkPermissions('continents-edit');
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
		$result = $this->masters_model->getContinentby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("continent_name"), 'required');    
		$this->form_validation->set_rules('code', lang("continent_code"), 'required'); 
        
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("continent_name"), 'is_unique[continents.name]');
        }
		if ($this->input->post('code') != $result->code) {
            $this->form_validation->set_rules('code', lang("continent_code"), 'is_unique[continents.code]');
        }
        
        if ($this->form_validation->run() == true) {

            $data = array(
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
            );
        } elseif ($this->input->post('edit_continent')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/continents");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_continent($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("continent_updated"));
            admin_redirect("masters/continents");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_continent', $this->data);
        }
    }
   
    function continent_status($status,$id){
		$this->sma->checkPermissions('continents-status');
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
        $this->masters_model->update_continent_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Country*/
    function country($action=false){
		$this->sma->checkPermissions('countries-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('country')));
        $meta = array('page_title' => lang('country'), 'bc' => $bc);
        $this->page_construct('masters/country', $meta, $this->data);
    }
    function getCountry(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('countries')}.id as id, {$this->db->dbprefix('countries')}.name, {$this->db->dbprefix('countries')}.phonecode, p.name as parent_name, {$this->db->dbprefix('countries')}.flag")
            ->from("countries")
			->join("continents p", "p.id = countries.continent_id ")
			->where('countries.is_delete', 0);
			
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_country") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/countries/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_country(){
		$this->sma->checkPermissions('countries-add');
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
		
        $this->form_validation->set_rules('name', lang("country_name"), 'required|is_unique[countries.name]');    
		$this->form_validation->set_rules('continent_id', lang("continent"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkCountry($this->input->post('name'), $this->input->post('continent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("country_already_exit"));
            	admin_redirect('masters/country');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'phonecode' => $this->input->post('phonecode'),
				'continent_id' => $this->input->post('continent_id'),
                'status' => 1,
            );
			if ($_FILES['flag']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'flag/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('flag')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$flag = $this->upload->file_name;
				$data['flag'] = 'flag/'.$flag;
				$config = NULL;
			}
        }elseif ($this->input->post('add_country')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/country");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_country($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("country_added"));
            admin_redirect('masters/country');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_country', $this->data);
			
        }
    }
    function edit_country($id){
		$this->sma->checkPermissions('countries-edit');
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
		$result = $this->masters_model->getCountryby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("country_name"), 'required');  
		$this->form_validation->set_rules('continent_id', lang("continent"), 'required');      
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("country_name"), 'is_unique[countries.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkCountry($this->input->post('name'), $this->input->post('continent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("country_already_exit"));
					admin_redirect('masters/country');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'phonecode' => $this->input->post('phonecode'),
				'continent_id' => $this->input->post('continent_id'),
            );
			if ($_FILES['flag']['size'] > 0) {
				$config['upload_path'] = $this->upload_path.'flag/';
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('flag')) {
					$result = array( 'status'=> false , 'message'=> lang('image_not_uploaded'));
				}
				$flag = $this->upload->file_name;
				$data['flag'] = 'flag/'.$flag;
				$config = NULL;
			}
        } elseif ($this->input->post('edit_country')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/country");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_country($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("country_updated"));
            admin_redirect("masters/country");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_country', $this->data);
        }
    }
   
    function country_status($status,$id){
		$this->sma->checkPermissions('countries-status');
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
        $this->masters_model->update_country_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Zone*/
    function zone($action=false){
		$this->sma->checkPermissions('zone-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('zone')));
        $meta = array('page_title' => lang('zone'), 'bc' => $bc);
        $this->page_construct('masters/zone', $meta, $this->data);
    }
    function getZone(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('zones')}.id as id, {$this->db->dbprefix('zones')}.name, p.name as parent_name")
            ->from("zones")
			->join("countries p", "p.id = zones.country_id ")
			->where('zones.is_delete', 0);
			
			
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_zone") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/zones/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_zone(){
		$this->sma->checkPermissions('zone-add');
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
        $this->form_validation->set_rules('name', lang("zone_name"), 'required|is_unique[zones.name]');    
		$this->form_validation->set_rules('country_id', lang("country"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkZone($this->input->post('name'), $this->input->post('country_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("zone_already_exit"));
            	admin_redirect('masters/zone');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'country_id' => $this->input->post('country_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_zone')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/zone");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_zone($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("zone_added"));
            admin_redirect('masters/zone');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_zone', $this->data);
			
        }
    }
    function edit_zone($id){
		$this->sma->checkPermissions('zone-edit');
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
		$result = $this->masters_model->getZoneby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("zone_name"), 'required');    
        $this->form_validation->set_rules('country_id', lang("country"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("zone_name"), 'is_unique[zones.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkZone($this->input->post('name'), $this->input->post('country_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("zone_already_exit"));
					admin_redirect('masters/zone');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'country_id' => $this->input->post('country_id'),
            );
        } elseif ($this->input->post('edit_zone')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/zone");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_zone($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("zone_updated"));
            admin_redirect("masters/zone");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			$continent = $this->masters_model->getCountryby_ID($result->country_id, $countryCode);
			$this->data['continent']  = $continent;
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_zone', $this->data);
        }
    }
   
    function zone_status($status,$id){
		$this->sma->checkPermissions('zone-status');
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
        $this->masters_model->update_zone_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### State*/
    function state($action=false){
		$this->sma->checkPermissions('state-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('state')));
        $meta = array('page_title' => lang('state'), 'bc' => $bc);
        $this->page_construct('masters/state', $meta, $this->data);
    }
    function getState(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('states')}.id as id, {$this->db->dbprefix('states')}.name, p.name as zone_name, c.name as country_name")
            ->from("states")
			->join("zones p", "p.id = states.zone_id ")
			->join("countries c", "c.id = p.country_id ")
			->where('states.is_delete', 0);
			
			
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_state") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/states/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_state(){
		$this->sma->checkPermissions('state-add');
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
		
        $this->form_validation->set_rules('name', lang("states_name"), 'required|is_unique[states.name]');    
		$this->form_validation->set_rules('zone_id', lang("zone"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			$check = $this->masters_model->checkState($this->input->post('name'), $this->input->post('zone_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("state_already_exit"));
            	admin_redirect('masters/state');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'zone_id' => $this->input->post('zone_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_state')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/state");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_state($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("state_added"));
            admin_redirect('masters/state');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_state', $this->data);
			
        }
    }
    function edit_state($id){
		$this->sma->checkPermissions('state-edit');
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
		
		$result = $this->masters_model->getStateby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("state_name"), 'required');    
        $this->form_validation->set_rules('zone_id', lang("zone"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("state_name"), 'is_unique[states.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkState($this->input->post('name'), $this->input->post('zone_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("state_already_exit"));
					admin_redirect('masters/state');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'zone_id' => $this->input->post('zone_id'),
            );
        } elseif ($this->input->post('edit_state')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/state");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_state($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("state_updated"));
            admin_redirect("masters/state");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			$country = $this->masters_model->getZoneby_ID($result->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_state', $this->data);
        }
    }
   
    function state_status($status,$id){
		$this->sma->checkPermissions('state-status');
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
        $this->masters_model->update_state_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### City*/
    function city($action=false){
		$this->sma->checkPermissions('city-index');
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
		
		$state = $_GET['state'];
		$zone = $_GET['zone'];
		$country = $_GET['country'];
		
		$this->data['countrys'] = $this->masters_model->getALLLicenseCountry();
		$this->data['zones'] = $this->masters_model->getZone_bycountry($country, $countryCode);
		$this->data['states'] = $this->masters_model->getState_byzone($zone, $countryCode);
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('city')));
        $meta = array('page_title' => lang('city'), 'bc' => $bc);
        $this->page_construct('masters/city', $meta, $this->data);
    }
    function getCity(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$state = $_GET['state'];
		$zone = $_GET['zone'];
		$country = $_GET['country'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('cities')}.id as id, {$this->db->dbprefix('cities')}.name, p.name as state_name, z.name as zone_name, c.name as country_name")
            ->from("cities")
			->join("states p", "p.id = cities.state_id ")
			->join("zones z", "z.id = p.zone_id ")
			->join("countries c", "c.id = z.country_id ")
			->where('cities.is_delete', 0);
			
			
			
            
			if($country != 0){
				$this->datatables->where('z.country_id',$country);
			}
			if($zone != 0){
				$this->datatables->where('p.zone_id',$zone);
			}
			if($state != 0){
				$this->datatables->where('cities.state_id',$state);
			}
			
           // $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_city/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_city") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_city/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/cities/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
    }
    function add_city(){
		$this->sma->checkPermissions('city-add');
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
		
        $this->form_validation->set_rules('name', lang("city_name"), 'required|is_unique[cities.name]');    
		$this->form_validation->set_rules('state_id', lang("state"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			$check = $this->masters_model->checkCity($this->input->post('name'), $this->input->post('state_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("city_already_exit"));
            	admin_redirect('masters/city');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'state_id' => $this->input->post('state_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_city')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/city");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_city($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("city_added"));
            admin_redirect('masters/city');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_city', $this->data);
			
        }
    }
    function edit_city($id){
		$this->sma->checkPermissions('city-edit');
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
		
		$result = $this->masters_model->getCityby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("city_name"), 'required');    
        $this->form_validation->set_rules('state_id', lang("state"), 'required');   
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("city_name"), 'is_unique[cities.name]');
			
        }
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkCity($this->input->post('name'), $this->input->post('state_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("city_already_exit"));
					admin_redirect('masters/city');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'state_id' => $this->input->post('state_id'),
            );
        } elseif ($this->input->post('edit_city')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/city");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_city($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("city_updated"));
            admin_redirect("masters/city");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$zone = $this->masters_model->getStateby_ID($result->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			
		
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_city', $this->data);
        }
    }
   
    function city_status($status,$id){
		$this->sma->checkPermissions('city-status');
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
        $this->masters_model->update_city_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	
	/*###### Area*/
    function area($action=false){
		$this->sma->checkPermissions('areas-index');
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
		$city = $_GET['city'];
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		$this->data['citys'] = $this->masters_model->AllgetCity();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('area')));
        $meta = array('page_title' => lang('area'), 'bc' => $bc);
        $this->page_construct('masters/area', $meta, $this->data);
    }
    function getArea($city){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		$city = $_GET['city'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('areas')}.id as id, {$this->db->dbprefix('areas')}.name,  p.name as city_name, s.name as state_name, z.name as zone_name, c.name as country_name")
            ->from("areas")
			->join("cities p", "p.id = areas.city_id ")
			->join("states s", "s.id = p.state_id ")
			->join("zones z", "z.id = s.zone_id ")
			->join("countries c", "c.id = z.country_id ")
			
			->where('areas.is_delete', 0);
			
			
			
            
			if($city != 0){
				$this->datatables->where('areas.city_id',$city);
			}
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_area/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_area") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_area/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/areas/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_area(){
		$this->sma->checkPermissions('areas-add');
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
		
        $this->form_validation->set_rules('name', lang("area_name"), 'required|is_unique[areas.name]');    
		$this->form_validation->set_rules('city_id', lang("city"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'city_id' => $this->input->post('city_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/area");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_area($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("area_added"));
            admin_redirect('masters/area');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_area', $this->data);
			
        }
    }
    function edit_area($id){
		$this->sma->checkPermissions('areas-edit');
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
		
		$result = $this->masters_model->getAreaby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('name', lang("area_name"), 'required');    
        //$this->form_validation->set_rules('city_id', lang("city"), 'required');   
        
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
				'name' => $this->input->post('name'),
				'city_id' => $this->input->post('city_id'),
            );
        } elseif ($this->input->post('edit_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/area");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_area($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("area_updated"));
            admin_redirect("masters/area");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$state = $this->masters_model->getCityby_ID($result->city_id, $countryCode);
			
			$zone = $this->masters_model->getStateby_ID($state->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			$this->data['state'] = $state;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			$this->data['citys'] = $this->masters_model->getCity_bystate($state->state_id, $countryCode);
			
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_area', $this->data);
        }
    }
   
    function area_status($status,$id){
		$this->sma->checkPermissions('areas-status');
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
        $this->masters_model->update_area_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Area*/
    function pincode($action=false){
		$this->sma->checkPermissions('pincode-index');
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
		$area = $_GET['area'];
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		//$this->data['area'] = $this->masters_model->getALLArea();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('pincode')));
        $meta = array('page_title' => lang('pincode'), 'bc' => $bc);
        $this->page_construct('masters/pincode', $meta, $this->data);
    }
    function getPincode($area){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		$area = $_GET['area'];
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('pincode')}.id as id, {$this->db->dbprefix('pincode')}.name,  {$this->db->dbprefix('pincode')}.pincode,  p.name as parent_name")
            ->from("pincode")
			->join("areas p", "p.id = pincode.area_id")
			->where('pincode.is_delete', 0);
			
			
			
           
			if($area != 0){
				$this->datatables->where('pincode.area_id',$area);
			}
            //$this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_pincode/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_pincode") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_pincode/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			$delete = "<a href='" . admin_url('welcome/delete/pincode/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_pincode(){
		$this->sma->checkPermissions('pincode-add');
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
		
        $this->form_validation->set_rules('pincode', lang("pincode"), 'required|is_unique[pincode.pincode]');    
		$this->form_validation->set_rules('area_id', lang("area"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'pincode' => $this->input->post('pincode'),
				'area_id' => $this->input->post('area_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_pincode')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/pincode");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_pincode($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("pincode_added"));
            admin_redirect('masters/pincode');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
            $this->load->view($this->theme . 'masters/add_pincode', $this->data);
			
        }
    }
    function edit_pincode($id){
		$this->sma->checkPermissions('pincode-edit');
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
		
		$result = $this->masters_model->getPincodeby_ID($id, $countryCode);
		
		$this->form_validation->set_rules('pincode', lang("pincode"), 'required');    
        //$this->form_validation->set_rules('city_id', lang("city"), 'required');   
        
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
				'name' => $this->input->post('name'),
				'pincode' => $this->input->post('pincode'),
				'area_id' => $this->input->post('area_id'),
            );
        } elseif ($this->input->post('edit_pincode')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/pincode");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_pincode($id, $data, $countryCode)) { 
			
            $this->session->set_flashdata('message', lang("pincode_updated"));
            admin_redirect("masters/pincode");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLContinents();
			$this->data['result'] = $result;
			
			$city = $this->masters_model->getAreaby_ID($result->area_id, $countryCode);
			$state = $this->masters_model->getCityby_ID($city->city_id, $countryCode);
			$zone = $this->masters_model->getStateby_ID($state->state_id, $countryCode);
			$country = $this->masters_model->getZoneby_ID($zone->zone_id, $countryCode);
			$continent = $this->masters_model->getCountryby_ID($country->country_id, $countryCode);
			
			$this->data['continent']  = $continent;
			$this->data['country']  = $country;
			$this->data['zone']  = $zone;
			$this->data['state'] = $state;
			$this->data['city'] = $city;
			
			
			$this->data['countrys'] = $this->masters_model->getCountry_bycontinent($continent->continent_id, $countryCode);
			$this->data['zones'] = $this->masters_model->getZone_bycountry($country->country_id, $countryCode);
			$this->data['states'] = $this->masters_model->getState_byzone($zone->zone_id, $countryCode);
			$this->data['citys'] = $this->masters_model->getCity_bystate($state->state_id, $countryCode);
			$this->data['areas'] = $this->masters_model->getArea_bycity($city->city_id, $countryCode);
			
			
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_pincode', $this->data);
        }
    }
   
    function pincode_status($status,$id){
		$this->sma->checkPermissions('pincode-status');
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
        $this->masters_model->update_pincode_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function getCountrysAllData(){
		
        $iso = $this->input->post('iso');
        $data = $this->site->getcountryCodeID($iso);
       
        echo json_encode($data);exit;
    }
	function getCompanyBank(){
		
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        
		$settlement_type = $this->input->post('settlement_type');
		$settlement_date =  date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('settlement_date'))));
		$company_type = $this->input->post('company_type');
		$company_id = $this->input->post('company_id') ? $this->input->post('company_id') : 0;
        $data = $this->masters_model->getCompanyBank($countryCode, $settlement_type, $company_id, $company_type);
		$pending = $this->masters_model->getBranchPending($countryCode, $settlement_date, $company_id, $company_type);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options['bank'][$k]['id'] = $row->bank_id;
				if($row->account_type == 1){
					$options['bank'][$k]['text'] = 'Cash ('.$row->account_no.')';
				}else{
                	$options['bank'][$k]['text'] = $row->bank_name.' ('.$row->account_no.')';
				}
            }
			$options['pending'] = $pending;
        }
		echo json_encode($options);exit;
    }
    /*#### Json Country Zone State city area*/
	function getTaxitype_byCountry(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
		$data = $this->masters_model->getTaxitype_byCountry($countryCode);
        $options = array();
        if($data){
            foreach($data['type'] as $k => $rowt){
                $options['type'][$k]['id'] = $rowt->id;
                $options['type'][$k]['text'] = $rowt->name;
            }
			foreach($data['make'] as $k => $rowm){
                $options['make'][$k]['id'] = $rowm->id;
                $options['make'][$k]['text'] = $rowm->name;
            }
        }
        echo json_encode($options);exit;
	}
	
	
	function getCountry_bylicensetype(){
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
        $data = $this->masters_model->getCountry_bylicensetype($country_id);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getHelp_main_byhelp(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $parent_id = $this->input->post('parent_id');
        $data = $this->masters_model->getHelp_main_byhelp($parent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	function getHelp_sub_byhelp_main(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
        $parent_id = $this->input->post('parent_id');
        $data = $this->masters_model->getHelp_sub_byhelp_main($parent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	/*##*/
	
	function getBranchID(){
		$data = array();
		if($this->input->post('branch_id') != ''){
			$data = $this->masters_model->getCompanyby_ID($this->input->post('branch_id'));
		}
		
        echo json_encode($data);exit;
    }
	
	function getBranch(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
       
        $options = array();
		if($this->input->post('is_office') == 1){
			$data = $this->masters_model->getBranch($countryCode);
			if($data){
				foreach($data as $k => $row){
					$options[$k]['id'] = $row->id;
					$options[$k]['text'] = $row->name;
				}
			}
		}
        echo json_encode($options);exit;
    }
	
	function getCountry_byBank(){
		if($this->session->userdata('group_id') == 1){
			if($this->input->get('is_country') != ''){
				$countryCode = $this->input->get('is_country');	
			}else{
				$countryCode = $this->input->post('is_country');	
			}	
		}else{
			$countryCode = $this->countryCode;	
		}
       
        $data = $this->masters_model->getALLBankOnly($countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->bank_name.'('.$row->account_no.')';
            }
        }
        echo json_encode($options);exit;
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
        $data = $this->masters_model->getCountry_bycontinent($continent_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
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
        $data = $this->masters_model->getZone_bycountry($country_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
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
        $data = $this->masters_model->getState_byzone($zone_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
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
        $data = $this->masters_model->getCity_bystate($state_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
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
        $data = $this->masters_model->getArea_bycity($city_id, $countryCode);
        $options = array();
        if($data){
            foreach($data as $k => $row){
                $options[$k]['id'] = $row->id;
                $options[$k]['text'] = $row->name;
            }
        }
        echo json_encode($options);exit;
    }
	
    /*#### user groups*/    
    function usergroups($action = NULL)
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
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('usergroups')));
        $meta = array('page_title' => lang('usergroups'), 'bc' => $bc);
        $this->page_construct('masters/user_groups', $meta, $this->data);
    }
    function getUserGroups(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,name")
            ->from("groups");
	    	//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_user_group/$1') . "' class='tip' title='" . lang("edit_user_group") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_user_group/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
			

        //->unset_column('id');
        echo $this->datatables->generate();
    }
	
    function add_user_group(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('groups', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
            	admin_redirect("masters/usergroups");
				exit;	
			}
			
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
        }
        if ($this->form_validation->run() == true && $this->masters_model->add_user_group($data, $countryCode)){
            $this->session->set_flashdata('message', lang("usergroup_added"));
            admin_redirect('masters/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/usergroups'), 'page' => lang('usergroups')), array('link' => '#', 'page' => lang('add_user_group')));
            $meta = array('page_title' => lang('add_user_group'), 'bc' => $bc);
            $this->page_construct('masters/add_user_group', $meta, $this->data);
        }
    }
	
    function edit_user_group($id){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->update_user_group($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("usergroup_updated"));
            admin_redirect('masters/usergroups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/usergroups'), 'page' => lang('groups')), array('link' => '#', 'page' => lang('edit_user_group')));
            $meta = array('page_title' => lang('edit_user_group'), 'bc' => $bc);
			$this->data['group'] = $this->masters_model->getUserGroupby_ID($id, $countryCode);
            $this->page_construct('masters/edit_user_group', $meta, $this->data);
        }
    }
	
	/*###### Help*/
    function help($action=false){
		$this->sma->checkPermissions('help-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help')));
        $meta = array('page_title' => lang('help'), 'bc' => $bc);
        $this->page_construct('masters/help', $meta, $this->data);
    }
    function getHelp(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("id,name,status")
            ->from("help");
			
			
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_continent/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_continents") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "";
			
			$add = "<a href='" . admin_url('masters/help_main/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div>", "id");
			
       $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help(){
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
        $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[help.name]');    
        if ($this->form_validation->run() == true) {
			
			
            $data = array(
                'name' => $this->input->post('name'),
				'type' => json_encode($this->input->post('type')),
                'status' => 1,
            );
        }elseif ($this->input->post('add_help')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_added"));
            admin_redirect('masters/help');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->load->view($this->theme . 'masters/add_help', $this->data);
        }
    }
    function edit_help($id){
		$result = $this->masters_model->getHelpby_ID($id);
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
        
        if ($this->input->post('name') != $result->name) {
            $this->form_validation->set_rules('name', lang("_name"), 'is_unique[help.name]');
        }
		
        
        if ($this->form_validation->run() == true) {

            $data = array(
				'name' => $this->input->post('name'),
				'type' => json_encode($this->input->post('type')),
            );
        } elseif ($this->input->post('edit_help')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_updated"));
            admin_redirect("masters/help");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help', $this->data);
        }
    }
   
    function help_status($status,$id){
		$this->sma->checkPermissions('help-status');
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
        $this->masters_model->update_help_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Main*/
    function help_main($id=false){
		
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
		$this->data['parnet_id'] = $id;
		$help_name = $this->masters_model->getHelpby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_main')));
        $meta = array('page_title' => $help_name->name.' - '.lang('help_main'), 'bc' => $bc);
        $this->page_construct('masters/help_main', $meta, $this->data);
    }
    function getHelp_main($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_main')}.id as id, {$this->db->dbprefix('help_main')}.name, p.name as parent_name, {$this->db->dbprefix('help_main')}.status as status, country.name as instance_country")
            ->from("help_main")
			->join("help p", "p.id = help_main.parent_id ")
			->join("countries country", " country.iso = help_main.is_country", "left")
			->where('help_main.is_delete', 0);
			
			if($parent_id != ''){
				$this->datatables->where("help_main.parent_id", $parent_id);
			}
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_main.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_main.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_country/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_country") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_help_main/$1') . "' data-toggle='modal'  data-target='#myModal' title='".lang('click_here_to_edit')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$add = "<a href='" . admin_url('masters/help_sub/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/help_main/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div><div>".$delete."</div>", "id");
			
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help_main($id){
		
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('parent_id', lang("help"), 'required');    
		
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkHelp_main($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("help_already_exit"));
            	admin_redirect('masters/help_main/'.$id);
			}
            $data = array(
                'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
                'status' => 1,
            );

        }elseif ($this->input->post('add_help_main')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_main/".$id);
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_main($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_main_added"));
            admin_redirect('masters/help_main/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/add_help_main', $this->data);
			
        }
    }
    function edit_help_main($id){
		$result = $this->masters_model->getHelp_mainby_ID($id);
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
		$this->form_validation->set_rules('parent_id', lang("help"), 'required');      
        
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkHelp_main($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("help_already_exit"));
					admin_redirect('masters/help_main/'.$id);
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
            );

        } elseif ($this->input->post('edit_help_main')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_main/".$id);
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help_main($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_main_updated"));
            admin_redirect("masters/help_main/".$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['result'] = $result;
			
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help_main', $this->data);
        }
    }
   
    function help_main_status($status,$id){
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
        $this->masters_model->update_help_main_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Sub*/
    function help_sub($id=false){
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
		$this->data['parnet_id'] = $id;
		$help_main_name = $this->masters_model->getHelp_mainby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_sub')));
        $meta = array('page_title' => $help_main_name->name.' - '.lang('help_sub'), 'bc' => $bc);
        $this->page_construct('masters/help_sub', $meta, $this->data);
    }
    function getHelp_sub($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_sub')}.id as id, {$this->db->dbprefix('help_sub')}.name, p.name as parent_name, {$this->db->dbprefix('help_sub')}.status as status, country.name as instance_country")
            ->from("help_sub")
			->join("help_main p", "p.id = help_sub.parent_id ")
			->join("countries country", " country.iso = help_sub.is_country", "left")
			->where('help_sub.is_delete', 0);
			if($parent_id != ''){
				$this->datatables->where("help_sub.parent_id", $parent_id);
			}
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_sub.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_sub.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_zone") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_zone/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
			///$this->datatables->add_column("Actions", "<div>".$edit."</div>", "id");
			$edit = "<a href='" . admin_url('masters/edit_help_sub/$1') . "' data-toggle='modal'  data-target='#myModal' title='".lang('click_here_to_edit')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$add = "<a href='" . admin_url('masters/help_form/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-eye' aria-hidden='true' style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/help_sub/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."' ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$add."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_help_sub($id){
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
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		$this->form_validation->set_rules('parent_id', lang("parent_id"), 'required');    
        if ($this->form_validation->run() == true) {
			$check = $this->masters_model->checkHelp_sub($this->input->post('help_sub'), $this->input->post('parent_id'), $countryCode);
			if($check == TRUE){
				$this->session->set_flashdata('error', lang("help_main_already_exit"));
            	admin_redirect('masters/help_sub');
			}
            $data = array(
                'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
                'status' => 1,
            );
        }elseif ($this->input->post('add_help_sub')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_sub/".$di);
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_sub($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_sub_added"));
            admin_redirect('masters/help_sub/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['id'] = $id;
			$help = $this->masters_model->getHelp_mainby_ID($id, $countryCode);
			
			$this->data['help_parent_id']  = $help->parent_id;
			$this->data['country']  = $help->is_country;
			$this->data['help_main'] = $this->masters_model->getHelp_main_byhelp($help->parent_id, $countryCode);
			
            $this->load->view($this->theme . 'masters/add_help_sub', $this->data);
			
        }
    }
    function edit_help_sub($id){
		$result = $this->masters_model->getHelp_subby_ID($id);
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
        $this->form_validation->set_rules('parent_id', lang("parent"), 'required');   
       
        if ($this->form_validation->run() == true) {
			if ($this->input->post('name') != $result->name) {
				$check = $this->masters_model->checkHelp_sub($this->input->post('name'), $this->input->post('parent_id'), $countryCode);
				if($check == TRUE){
					$this->session->set_flashdata('error', lang("help_main_already_exit"));
					admin_redirect('masters/help_sub');
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'parent_id' => $this->input->post('parent_id'),
            );
        } elseif ($this->input->post('edit_help_sub')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_sub");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_help_sub($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("help_sub_updated"));
            admin_redirect("masters/help_sub");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			$this->data['result'] = $result;
			$help = $this->masters_model->getHelp_mainby_ID($result->parent_id, $countryCode);
			$this->data['help']  = $help;
			$this->data['help_main'] = $this->masters_model->getHelp_main_byhelp($help->parent_id, $countryCode);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_help_sub', $this->data);
        }
    }
   
    function help_sub_status($status,$id){
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
        $this->masters_model->update_help_sub_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	/*###### Help Form*/
    function help_form($id=false){
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
		$this->data['parnet_id'] = $id;
		$help_sub_name = $this->masters_model->getHelp_subby_ID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('help_form')));
        $meta = array('page_title' => $help_sub_name->name.' -'.lang('help_form'), 'bc' => $bc);
        $this->page_construct('masters/help_form', $meta, $this->data);
    }
    function getHelp_form($id){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
		if($id){
			$parent_id = $id;
		}else{
			$parent_id = $this->input->get('parent_id');
		}
		
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('help_form')}.id as id, {$this->db->dbprefix('help_form')}.name,   {$this->db->dbprefix('help_form')}.form_type, p.name as parent_name, {$this->db->dbprefix('help_form')}.status as status, country.name as instance_country")
            ->from("help_form")
			->join("countries country", " country.iso = help_form.is_country", "left")
			->join("help_sub p", "p.id = help_form.parent_id ");
			
			if($parent_id != ''){
				$this->datatables->where("help_form.parent_id", $parent_id);
			}
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("help_form.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("help_form.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			//->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_state") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
			//$edit = "<a href='" . admin_url('masters/edit_state/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><div class='kapplist-view1'></div></a>";
			//$this->datatables->add_column("Actions", "<div></div>", "id");
			$delete = "<a href='" . admin_url('welcome/delete/help_form/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."' ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
	
  
	function add_help_form($id){
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
		$data = array();
		$this->form_validation->set_rules('parent_id', lang("parent"), 'required'); 
		   
        if ($this->form_validation->run() == true) {
			$details = $this->input->post('details');
			if(!empty($this->input->post('name'))){
				for($i=0; $i<count($this->input->post('name')); $i++){
					
					$data[$i] = array('name' => $_POST['name'][$i], 'form_type' => $_POST['form_type'][$i], 'form_name' => $_POST['form_name'][$i], 'parent_id' => $this->input->post('parent_id'), 'status' => 1, 'created_on' => date('Y-m-d H:i:s'));
						
				}
				
			}
			
		
        }elseif ($this->input->post('add_help_form')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/help_form/".$di);
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->add_help_form($data, $details, $this->input->post('parent_id'), $countryCode)){
			
            $this->session->set_flashdata('message', lang("help_form_added"));
            admin_redirect('masters/help_form');
        } else {
			$this->data['id'] = $id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['parent'] = $this->masters_model->getALLHelp($countryCode);
			
            $this->load->view($this->theme . 'masters/add_help_form', $this->data);
			
        }
    }
	
	
   
    function help_form_status($status,$id){
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
        $this->masters_model->update_help_form_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	function bank_actions($wh = NULL)
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
		
		$countryCode = $this->countryCode;	
		
		
        $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Bank');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('account_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('bank_name'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('branch_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('ifsc_code'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLBank($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->account_holder_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->account_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->bank_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->branch_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $value-> ifsc_code);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'bank_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	
	function company_actions($wh = NULL)
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
		
		$countryCode = $this->countryCode;	
		
		
        $this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
		$this->site->users_logs($countryCode,$this->session->userdata('user_id'), $this->getUserIpAddr, json_encode($_POST), $_SERVER['REQUEST_URI']);
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

		
                if ($this->input->post('form_action') == 'export_excel') {
					

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Bank');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('is_office'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('telephone'));
					$this->excel->getActiveSheet()->SetCellValue('F1', lang('fax'));
					$this->excel->getActiveSheet()->SetCellValue('G1', lang('register_number'));
					$this->excel->getActiveSheet()->SetCellValue('H1', lang('starting_year'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:H1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLCompany($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        if($value->is_office == 1){
							$office = "Head Office";
						}else{
							$office = "Branch Office";
						}
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->address);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $office);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $value->email);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $value->telephone);
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $value->fax);
						$this->excel->getActiveSheet()->SetCellValue('G' . $row, $value->register_number);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $value->starting_year);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'company_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	
	function user_department_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('User Department');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLUser_department();
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'user_department_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function tax_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Tax');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('percentage'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTax($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->tax_name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->percentage);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function taxi_type_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Type');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_type($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_type_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	

	function taxi_make_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Make');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_make($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_make_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function taxi_model_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Model');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('Make'));
					$this->excel->getActiveSheet()->SetCellValue('C1', lang('Type'));
                   
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
					$res = $this->masters_model->getALLTaxi_model($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->make);
						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $value->type);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_model_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

	
	function taxi_fuel_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Taxi Type');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLTaxi_fuel($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'taxi_fuel_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function continents_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Continents');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLContinents($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->code);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'continents_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function country_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Country');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
					$this->excel->getActiveSheet()->SetCellValue('B1', lang('phonecode'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLCountry($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $value->phonecode);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'country_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function zone_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Zone');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLZone($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'zone_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function state_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('State');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLState($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'state_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function city_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('City');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLCity($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'city_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function area_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Area');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLArea($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'area_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function pincode_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Pincode');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLPincode($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'pincode_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

	/*###### Payment Gateway*/
    function payment_gateway($action=false){
		$this->sma->checkPermissions('payment_gateway-index');
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('payment_gateway')));
        $meta = array('page_title' => lang('payment_gateway'), 'bc' => $bc);
        $this->page_construct('masters/payment_gateway', $meta, $this->data);
    }
    function getPayment_gateway(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('payment_gateway')}.id as id, {$this->db->dbprefix('payment_gateway')}.name, CONCAT(ab.account_no, '-', ab.bank_name) as bank,  {$this->db->dbprefix('payment_gateway')}.status as status, country.name as instance_country")
            ->from("payment_gateway")
			->join("countries country", " country.iso = payment_gateway.is_country", "left")
			->join("admin_bank ab", " ab.id = payment_gateway.bank_id", "left")
			->where('payment_gateway.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("payment_gateway.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("payment_gateway.is_country", $countryCode);
			}
			
            $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
			$edit = "<a href='" . admin_url('masters/edit_payment_gateway/$1') . "' data-toggle='modal' data-target='#myModal'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
			
			$delete = "<a href='" . admin_url('welcome/delete/payment_gateway/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
			
			$this->datatables->add_column("Actions", "<div>".$edit."</div><div>".$delete."</div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_payment_gateway(){
		$this->sma->checkPermissions('payment_gateway-add');
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
		
        $this->form_validation->set_rules('name', lang("name"), 'required');    
		
        if ($this->form_validation->run() == true) {
			
            $data = array(
                'name' => $this->input->post('name'),
				'bank_id' => $this->input->post('bank_id'),
				'code' => strtolower(str_replace(' ', '', $this->input->post('name'))),
                'status' => 1,
				'created_on' => date('Y-m-d H:i:s')
            );
			
        }elseif ($this->input->post('add_payment_gateway')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/payment_gateway");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_payment_gateway($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("payment_gateway_added"));
            admin_redirect('masters/payment_gateway');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['admin_bank'] = $this->masters_model->getALLBankOnly($countryCode);
            $this->load->view($this->theme . 'masters/add_payment_gateway', $this->data);
			
        }
    }
    function edit_payment_gateway($id){
		$this->sma->checkPermissions('payment_gateway-edit');
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
		$result = $this->masters_model->getPayment_gatewayby_ID($id, $countryCode);
		$this->form_validation->set_rules('name', lang("name"), 'required');  
		    
       
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('name') != $result->name) {
				$check = $this->site->masterCheck('payment_gateway', array('name' => $this->input->post('name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('name_has_been_already_exits'));
					admin_redirect("masters/payment_gateway");
					exit;	
				}
			}
			
            $data = array(
				'name' => $this->input->post('name'),
				'bank_id' => $this->input->post('bank_id'),
				'code' => strtolower(str_replace(' ', '', $this->input->post('name'))),
				'created_on' => date('Y-m-d H:i:s')
            );
			
		
			
        } elseif ($this->input->post('edit_payment_gateway')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/payment_gateway");
        }

        if ($this->form_validation->run() == true && $this->masters_model->update_payment_gateway($id, $data, $countryCode)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("payment_gateway_updated"));
            admin_redirect("masters/payment_gateway");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['result'] = $result;
			$this->data['admin_bank'] = $this->masters_model->getALLBankOnly($countryCode ? $countryCode : $result->is_country);
            $this->data['id'] = $id;
            $this->load->view($this->theme . 'masters/edit_payment_gateway', $this->data);
        }
    }
   
    function payment_gateway_status($status,$id){
		$this->sma->checkPermissions('payment_gateway-status');
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
        $this->masters_model->update_payment_gateway_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	

	function payment_gateway_actions($wh = NULL)
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
                    $this->excel->getActiveSheet()->setTitle('Payment gateway');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                   
					$this->excel->getActiveSheet()
						->getStyle('A1:E1')
						->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F2B818')
								)
							)
						);

                    $row = 2;
					$res = $this->masters_model->getALLPayment_gateway($countryCode);
					if(!empty($res)){
                    foreach ($res as $value) {
                        
                        
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $value->name);
                       
                        $row++;
                    }
					}
					
					

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'payment_gateway_'.date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);

                }
           
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function import_csv_bank()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_bank");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('account_holder_name', 'account_no', 'bank_name', 'branch_name', 'ifsc_code', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					if ($this->masters_model->getBankByAccountNO(trim($csv_pr['account_no'])) == FALSE) {
						$items[] = array (
							'account_holder_name' => trim($csv_pr['account_holder_name']),
							'account_no' => trim($csv_pr['account_no']),
							'bank_name' => trim($csv_pr['bank_name']),
							'branch_name' => trim($csv_pr['branch_name']),
							'ifsc_code' => trim($csv_pr['ifsc_code']),
							'is_country' => trim($csv_pr['is_country']),
							'user_id' => $this->session->userdata('user_id'),
							'status' => 1,
							
							);
					} else {
						$this->session->set_flashdata('error', lang("account_no_exit")." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_bank");
					}
                    

                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_bank");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_bank($items)){
			
            $this->session->set_flashdata('message', lang("bank_added"));
            admin_redirect('masters/bank');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_bank')));
            $meta = array('page_title' => lang('import_bank'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_bank', $meta, $this->data);
        }
        
    }
	
	function import_csv_company()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_company");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('account_holder_name', 'account_no', 'bank_name', 'branch_name', 'ifsc_code', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					if ($this->masters_model->getBankByAccountNO(trim($csv_pr['account_no'])) == FALSE) {
						$items[] = array (
							'account_holder_name' => trim($csv_pr['account_holder_name']),
							'account_no' => trim($csv_pr['account_no']),
							'bank_name' => trim($csv_pr['bank_name']),
							'branch_name' => trim($csv_pr['branch_name']),
							'ifsc_code' => trim($csv_pr['ifsc_code']),
							'is_country' => trim($csv_pr['is_country']),
							'user_id' => $this->session->userdata('user_id'),
							'status' => 1,
							
							);
					} else {
						$this->session->set_flashdata('error', lang("account_no_exit")." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_company");
					}
                    

                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_company')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_company");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_company($items)){
			
            $this->session->set_flashdata('message', lang("company_added"));
            admin_redirect('masters/company');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/company'), 'page' => lang('company')), array('link' => '#', 'page' => lang('import_company')));
            $meta = array('page_title' => lang('import_company'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_company', $meta, $this->data);
        }
        
    }
	
	function import_csv_walletoffer()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_walletoffer");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'amount', 'type', 'offer_amount', 'offer_date', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					 $check = $this->site->masterCheck('walletoffer', array('name' => trim($csv_pr['name']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_walletoffer");
						exit;	
					}
					$items[] = array(
						'name' => trim($csv_pr['name']),
						'amount' => trim($csv_pr['amount']),
						'offer_amount' => trim($csv_pr['type']) == 0 ? trim($csv_pr['offer_amount']) : trim($csv_pr['amount']),
						'offer_date' =>	trim($csv_pr['offer_date']) ? trim($csv_pr['offer_date']) : '',
						'is_default' => trim($csv_pr['type']) == 0 ? trim($csv_pr['is_default']) : 0,
						'type' => trim($csv_pr['type']),
						'created_on' => date('Y-m-d H:i:s'),
						'status' => 1,
						'is_country' => trim($csv_pr['is_country'])
					);
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_walletoffer')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_walletoffer");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_walletoffer($items)){
			
            $this->session->set_flashdata('message', lang("walletoffer_added"));
            admin_redirect('masters/walletoffer');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/walletoffer'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_walletoffer')));
            $meta = array('page_title' => lang('import_walletoffer'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_walletoffer', $meta, $this->data);
        }
        
    }
	
	function import_csv_payment_gateway()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_payment_gateway");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('payment_gateway', array('name' => trim($csv_pr['name']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_payment_gateway");
						exit;	
					}
					$items[] = array(
						'name' => trim($csv_pr['name']),
						'code' => strtolower(str_replace(' ', '', trim($csv_pr['amount']))),
						'created_on' => date('Y-m-d H:i:s'),
						'status' => 1,
						'is_country' => trim($csv_pr['is_country'])
					);
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_payment_gateway')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_payment_gateway");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_payment_gateway($items)){
			
            $this->session->set_flashdata('message', lang("payment_gateway_added"));
            admin_redirect('masters/payment_gateway');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/payment_gateway'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_payment_gateway')));
            $meta = array('page_title' => lang('import_payment_gateway'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_payment_gateway', $meta, $this->data);
        }
        
    }
	
	function import_csv_tax()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_tax");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('tax_name', 'percentage', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('tax', array('tax_name' => trim($csv_pr['tax_name']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_tax");
						exit;	
					}
					
					
					$items[] = array(
						'tax_name' => trim($csv_pr['tax_name']),
						'percentage' => trim($csv_pr['percentage']),
						//'created_on' => date('Y-m-d H:i:s'),
						'status' => 1,
						'is_country' => trim($csv_pr['is_country'])
					);
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_tax')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_tax");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_tax($items)){
			
            $this->session->set_flashdata('message', lang("tax_added"));
            admin_redirect('masters/tax');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/tax'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_tax')));
            $meta = array('page_title' => lang('import_tax'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_tax', $meta, $this->data);
        }
        
    }
	
	function import_csv_taxi_type()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_taxi_type");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('cab_type', 'cab_image', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('taxi_type', array('name' => trim($csv_pr['cab_type']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_taxi_type");
						exit;	
					}
					
					$cab_image_id = $this->masters_model->getCabtypeImageByName(trim($csv_pr['cab_image']));
					if ($cab_image_id != 0) {
					
						$items[] = array(
							'name' => trim($csv_pr['cab_type']),
							'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							'category_id' => 1,
							'status' => 1,
							'is_country' => trim($csv_pr['is_country'])
						);
					
					}else{
						$this->session->set_flashdata('error', lang("cab_image_data_empty")." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_taxi_type");
					}
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_taxi_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_taxi_type");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_taxi_type($items)){
			
            $this->session->set_flashdata('message', lang("taxi_type_added"));
            admin_redirect('masters/taxi_type');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/taxi_type'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_taxi_type')));
            $meta = array('page_title' => lang('import_taxi_type'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_taxi_type', $meta, $this->data);
        }
        
    }
	
	function import_csv_taxi_make()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_taxi_make");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('cab_make', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('taxi_make', array('name' => trim($csv_pr['cab_make']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_taxi_make");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['cab_make']),
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							'is_country' => trim($csv_pr['is_country'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_taxi_make')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_taxi_make");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_taxi_make($items)){
			
            $this->session->set_flashdata('message', lang("taxi_make_added"));
            admin_redirect('masters/taxi_make');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/taxi_make'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_taxi_make')));
            $meta = array('page_title' => lang('import_taxi_make'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_taxi_make', $meta, $this->data);
        }
        
    }
	
	function import_csv_taxi_model()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_taxi_model");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
					//echo 'sssss';
                    while (($row = fgetcsv($handle, 5000, ",")) != FALSE) {
						//echo '<pre>';
						//print_r($row);
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('cab_type', 'cab_image', 'cab_make', 'cab_model', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					$cab_type_id = $this->masters_model->getCabtypeByName(trim($csv_pr['cab_type']));
					$cab_make_id = $this->masters_model->getCabMakeByName(trim($csv_pr['cab_make']));
					$cab_image_id = $this->masters_model->getCabtypeImageByName(trim($csv_pr['cab_image']));
					if ($cab_image_id != 0) {
						$cab_model = $this->masters_model->getCabModelByName($cab_type_id, $cab_make_id, trim($csv_pr['cab_model']));
						if($cab_model == FALSE){
							$items[] = array (
								'cab_type' => trim($csv_pr['cab_type']),
								'cab_type_id' => $cab_type_id,
								'cab_make' => trim($csv_pr['cab_make']),
								'cab_make_id' => $cab_make_id,
								'cab_image' => trim($csv_pr['cab_image']),
								'cab_image_id' => $cab_image_id,
								'cab_model' => trim($csv_pr['cab_model']),
								
								'is_country' => trim($csv_pr['is_country']),
								'user_id' => $this->session->userdata('user_id'),
								);
						}else{
							$this->session->set_flashdata('error', lang("cab_model_exit")." ".lang("line_no")." ".$rw);
							admin_redirect("masters/import_csv_taxi_model");
						}
					} else {
						$this->session->set_flashdata('error', lang("cab_image_data_empty")." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_taxi_model");
					}
                    $rw++;
				}
				
		   }
		   
		   
			//print_r($items);die;
        }elseif ($this->input->post('import_bank')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_taxi_model");
        }
		
        if ($this->form_validation->run() == true  && $this->masters_model->import_taxi_model($items)){
			
            $this->session->set_flashdata('message', lang("cab_model_added"));
            admin_redirect('masters/taxi_model');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/bank'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_cab')));
            $meta = array('page_title' => lang('import_cab'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_taxi_model', $meta, $this->data);
        }
        
    }
	
	function import_csv_taxi_fuel()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_taxi_fuel");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('cab_fuel', 'is_country');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('taxi_fuel', array('name' => trim($csv_pr['cab_fuel']), 'is_country' => trim($csv_pr['is_country'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_taxi_fuel");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['cab_fuel']),
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							'is_country' => trim($csv_pr['is_country'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_taxi_fuel')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_taxi_fuel");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_taxi_fuel($items)){
			
            $this->session->set_flashdata('message', lang("taxi_fuel_added"));
            admin_redirect('masters/taxi_fuel');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/taxi_fuel'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_taxi_fuel')));
            $meta = array('page_title' => lang('import_taxi_fuel'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_taxi_fuel', $meta, $this->data);
        }
        
    }
	
	function import_csv_continents()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_continents");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'code');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check = $this->site->masterCheck('continents', array('name' => trim($csv_pr['name']), 'code' => trim($csv_pr['code'])));
					if($check == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_continents");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_continents')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_continents");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_continents($items)){
			
            $this->session->set_flashdata('message', lang("continents_added"));
            admin_redirect('masters/continents');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/continents'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_continents')));
            $meta = array('page_title' => lang('import_continents'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_continents', $meta, $this->data);
        }
        
    }
	
	function import_csv_country()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_country");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'pnone_code', 'iso', 'continents_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('continents', array('name' => trim($csv_pr['continents_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('continents_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_country");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('countries', array('name' => trim($csv_pr['name'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_country");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'iso' => trim($csv_pr['iso']),
							'phonecode' => trim($csv_pr['phonecode']),
							'continent_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_country')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_country");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_country($items)){
			
            $this->session->set_flashdata('message', lang("country_added"));
            admin_redirect('masters/country');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/country'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_country')));
            $meta = array('page_title' => lang('import_country'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_country', $meta, $this->data);
        }
        
    }
	
	function import_csv_zone()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_zone");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'country_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('countries', array('name' => trim($csv_pr['country_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('country_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_zone");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('zones', array('name' => trim($csv_pr['name'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_zone");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'country_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_zone')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_zone");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_zone($items)){
			
            $this->session->set_flashdata('message', lang("zone_added"));
            admin_redirect('masters/zone');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/zone'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_zone')));
            $meta = array('page_title' => lang('import_zone'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_zone', $meta, $this->data);
        }
        
    }
	
	function import_csv_state()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_state");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'zone_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('zones', array('name' => trim($csv_pr['zone_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('zone_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_state");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('states', array('name' => trim($csv_pr['name'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_state");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'zone_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_state')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_state");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_state($items)){
			
            $this->session->set_flashdata('message', lang("state_added"));
            admin_redirect('masters/state');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/state'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_state')));
            $meta = array('page_title' => lang('import_state'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_state', $meta, $this->data);
        }
        
    }
	
	
	function import_csv_city()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_city");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'state_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('states', array('name' => trim($csv_pr['state_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('state_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_city");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('cities', array('name' => trim($csv_pr['name'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_city");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'state_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_city')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_city");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_city($items)){
			
            $this->session->set_flashdata('message', lang("city_added"));
            admin_redirect('masters/city');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/city'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_city')));
            $meta = array('page_title' => lang('import_city'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_city', $meta, $this->data);
        }
        
    }
	function import_csv_area()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_area");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'city_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('cities', array('name' => trim($csv_pr['city_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('city_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_area");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('areas', array('name' => trim($csv_pr['name'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_area");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'city_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_area");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_area($items)){
			
            $this->session->set_flashdata('message', lang("area_added"));
            admin_redirect('masters/area');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/area'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_area')));
            $meta = array('page_title' => lang('import_area'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_area', $meta, $this->data);
        }
        
    }
	
	function import_csv_pincode()
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
        
		$this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');   
		
        if ($this->form_validation->run() == true) {
			
           if (isset($_FILES["userfile"])) {

				$this->load->library('upload');
				$config['upload_path'] = $this->upload_path.'import/';
				$config['allowed_types'] = 'csv';
				$config['max_size'] = $this->allowed_file_size;
				$config['overwrite'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_filename'] = 25;
				$this->upload->initialize($config);
	
				if (!$this->upload->do_upload()) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					admin_redirect("masters/import_csv_pincode");
				}
				$csv = $this->upload->file_name;
				$arrResult = array();
                $handle = fopen($this->upload_path.'import/'.$csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
				$keys = array('name', 'pincode', 'area_name');
				$final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
				//$this->sma->print_arrays($final);
				$rw = 2; $items = array();
                foreach ($final as $csv_pr) {
					 
					
					$check1 = $this->site->masterCheck1('areas', array('name' => trim($csv_pr['area_name'])));
					if($check1 == 0){
						$this->session->set_flashdata('error', lang('area_has_been_not_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_pincode");
						exit;	
					}else{
						$parent_id = $check1;
					}
					
					$check2 = $this->site->masterCheck('pincode', array('pincode' => trim($csv_pr['pincode'])));
					if($check2 == TRUE){
						$this->session->set_flashdata('error', lang('name_has_been_already_exits')." ".lang("line_no")." ".$rw);
						admin_redirect("masters/import_csv_pincode");
						exit;	
					}
					
					
						$items[] = array(
							'name' => trim($csv_pr['name']),
							'pincode' => trim($csv_pr['pincode']),
							'area_id' => $parent_id,
							//'taxi_image_id' => $cab_image_id,
							//'created_on' => date('Y-m-d H:i:s'),
							//'category_id' => 1,
							'status' => 1,
							//'code' => trim($csv_pr['code'])
						);
					
					
                    $rw++;
				}
				
		   }
			//print_r($items);die;
        }elseif ($this->input->post('import_pincode')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/import_csv_pincode");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->import_pincode($items)){
			
            $this->session->set_flashdata('message', lang("pincode_added"));
            admin_redirect('masters/pincode');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/pincode'), 'page' => lang('bank')), array('link' => '#', 'page' => lang('import_pincode')));
            $meta = array('page_title' => lang('import_pincode'), 'bc' => $bc);
            $this->page_construct('masters/import_csv_pincode', $meta, $this->data);
        }
        
    }
	
	
	/*###### Cancel Master*/
    function cancelmaster($action = NULL)
    {
		$this->sma->checkPermissions('cancel_master-index');
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
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cancelmaster')));
        $meta = array('page_title' => lang('cancelmaster'), 'bc' => $bc);
        $this->page_construct('masters/cancelmaster', $meta, $this->data);
    }
    function getCancelmaster(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('cancelmaster')}.id as id, {$this->db->dbprefix('cancelmaster')}.title,{$this->db->dbprefix('cancelmaster')}.message, group.name as group_name, {$this->db->dbprefix('cancelmaster')}.status as status, country.name as instance_country")
            ->from("cancelmaster")
			->join("groups group", " group.id = cancelmaster.group_id", "left")
			->join("countries country", " country.iso = cancelmaster.is_country", "left")
			->where('cancelmaster.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("cancelmaster.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("cancelmaster.is_country", $countryCode);
			}
			
            			
			
              $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_cancelmaster/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/cancelmaster/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div><div>".$edit."</div><div>".$delete."</div></div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_cancelmaster(){
		$this->sma->checkPermissions('cancel_master-add');
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
        $this->form_validation->set_rules('title', lang("title"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('cancelmaster', array('title' => $this->input->post('title'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
            	admin_redirect("masters/cancelmaster");
				exit;	
			}
            $data = array(
                 'title' => $this->input->post('title'),
                'message' =>$this->input->post('message'),
				'group_id' => $this->input->post('group_id'),
				
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
            );
			
           
        }elseif ($this->input->post('add_cancelmaster')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/cancelmaster");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_cancelmaster($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cancelmaster_added"));
            admin_redirect('masters/cancelmaster');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/cancelmaster'), 'page' => lang('cancelmaster')), array('link' => '#', 'page' => lang('add_cancelmaster')));
			
            $meta = array('page_title' => lang('add_cancelmaster'), 'bc' => $bc);
            $this->page_construct('masters/add_cancelmaster', $meta, $this->data);
        }
    }
    function edit_cancelmaster($id){
		$this->sma->checkPermissions('cancel_master-edit');
		$result = $this->masters_model->getCancelmasterby_ID($id);
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
		
        $this->form_validation->set_rules('title', lang("title"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('title') != $result->title && $countryCode != $result->is_country) {
				$check = $this->site->masterCheck('cancelmaster', array('title' => $this->input->post('title'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
					admin_redirect("masters/cancelmaster");
					exit;	
				}
			}
            $data = array(
                 'title' => $this->input->post('title'),
				 
                'message' =>$this->input->post('message'),
				'group_id' => $this->input->post('group_id'),
				'is_country' => $countryCode
				
				
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_cancelmaster($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("cancelmaster_updated"));
            admin_redirect('masters/cancelmaster');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/cancelmaster'), 'page' => lang('cancelmaster')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('edit_cancelmaster'), 'bc' => $bc);
            $this->data['cancelmaster'] = $result;
			
            $this->page_construct('masters/edit_cancelmaster', $meta, $this->data);
        }
    }
    function cancelmaster_status($status,$id){
		$this->sma->checkPermissions('cancel_master-status');
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
		$this->masters_model->update_cancelmaster_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	
	/*###### Discount Master*/
    function discount($action = NULL)
    {
		$this->sma->checkPermissions('discount-index');
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
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('discount')));
        $meta = array('page_title' => lang('discount'), 'bc' => $bc);
        $this->page_construct('masters/discount', $meta, $this->data);
    }
    function getDiscount(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('discount')}.id as id, {$this->db->dbprefix('discount')}.discount_name, {$this->db->dbprefix('discount')}.discount_percentage,{$this->db->dbprefix('discount')}.discount_type, {$this->db->dbprefix('discount')}.days, CONCAT({$this->db->dbprefix('discount')}.start_date,' ',{$this->db->dbprefix('discount')}.end_date) as dates,  {$this->db->dbprefix('discount')}.status as status, country.name as instance_country")
            ->from("discount")
			
			->join("countries country", " country.iso = discount.is_country", "left")
			->where('discount.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("discount.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("discount.is_country", $countryCode);
			}
			
            			
			
              $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_discount/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/discount/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div><div>".$edit."</div><div>".$delete."</div></div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_discount(){
		$this->sma->checkPermissions('discount-add');
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
        $this->form_validation->set_rules('discount_name', lang("discount_name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('discount', array('discount_name' => $this->input->post('discount_name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
            	admin_redirect("masters/discount");
				exit;	
			}
			
            $data = array(
                'discount_apply_type' => $this->input->post('discount_apply_type'),
                'user_ids' => $this->input->post('discount_apply_type') == 0 ? '' : $this->input->post('user_ids'),
				'discount_type' => $this->input->post('discount_type'),
				'discount_name' => $this->input->post('discount_name'),
				'days' => $this->input->post('discount_type') != 1 ? '' : $this->input->post('days'),
				'start_date' => $this->input->post('discount_type') != 2 ? '' : $this->input->post('start_date'),
				'end_date' => $this->input->post('discount_type') != 2 ? '' : $this->input->post('end_date'),
				'discount_percentage' => $this->input->post('discount_percentage'),
				'created_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
            );
			
           
        }elseif ($this->input->post('add_discount')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/discount");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_discount($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("discount_added"));
            admin_redirect('masters/discount');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/discount'), 'page' => lang('discount')), array('link' => '#', 'page' => lang('add_discount')));
			$this->data['AllUsers'] = $this->masters_model->customerUsers($countryCode);
            $meta = array('page_title' => lang('add_discount'), 'bc' => $bc);
            $this->page_construct('masters/add_discount', $meta, $this->data);
        }
    }
    function edit_discount($id){
		$this->sma->checkPermissions('discount-edit');
		$result = $this->masters_model->getDiscountby_ID($id);
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
		
        $this->form_validation->set_rules('discount_name', lang("discount_name"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('discount_name') != $result->discount_name && $countryCode != $result->is_country) {
				$check = $this->site->masterCheck('discount', array('discount_name' => $this->input->post('discount_name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
					admin_redirect("masters/discount");
					exit;	
				}
			}
            $data = array(
                'discount_apply_type' => $this->input->post('discount_apply_type'),
                'user_ids' => $this->input->post('discount_apply_type') == 0 ? '' : $this->input->post('user_ids'),
				'discount_type' => $this->input->post('discount_type'),
				'discount_name' => $this->input->post('discount_name'),
				'days' => $this->input->post('discount_type') != 1 ? '' : $this->input->post('days'),
				'start_date' => $this->input->post('discount_type') != 2 ? '' : $this->input->post('start_date'),
				'end_date' => $this->input->post('discount_type') != 2 ? '' : $this->input->post('end_date'),
				'discount_percentage' => $this->input->post('discount_percentage'),
				'created_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
				
				
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_discount($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("discount_updated"));
            admin_redirect('masters/discount');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/discount'), 'page' => lang('discount')), array('link' => '#', 'page' => lang('discount')));
            $meta = array('page_title' => lang('edit_discount'), 'bc' => $bc);
            $this->data['discount'] = $result;
			$this->data['AllUsers'] = $this->masters_model->customerUsers($countryCode);
            $this->page_construct('masters/edit_discount', $meta, $this->data);
        }
    }
    function discount_status($status,$id){
		$this->sma->checkPermissions('discount-status');
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
		$this->masters_model->update_discount_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	
	/*###### Health Master*/
    function health($action = NULL)
    {
		//$this->sma->checkPermissions('health-index');
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
		
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('health')));
        $meta = array('page_title' => lang('health'), 'bc' => $bc);
        $this->page_construct('masters/health', $meta, $this->data);
    }
    function getHealth(){
		if($this->session->userdata('group_id') == 1){
			$countryCode = $this->input->get('is_country');	
		}else{
			$countryCode = $this->countryCode;	
		}
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('health')}.id as id, {$this->db->dbprefix('health')}.health_name,  {$this->db->dbprefix('health')}.status as status, country.name as instance_country")
            ->from("health")
			
			->join("countries country", " country.iso = health.is_country", "left")
			->where('health.is_delete', 0);
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("health.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("health.is_country", $countryCode);
			}
			
            			
			
              $this->datatables->edit_column('status', '$1__$2', 'status, id');
			
           // ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('masters/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
		$edit = "<a href='" . admin_url('masters/edit_health/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_full_details')."'  ><i class='fa fa-pencil-square-o' aria-hidden='true'  style='color:#656464; font-size:18px'></i></a>";
		
		$delete = "<a href='" . admin_url('welcome/delete/health/$1') . "' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='".lang('click_here_to_delete')."'  ><i class='fa fa-trash' style='color:#656464; font-size:18px'></i></a>";
		
			$this->datatables->add_column("Actions", "<div><div>".$edit."</div><div>".$delete."</div></div>", "id");
			
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_health(){
		//$this->sma->checkPermissions('health-add');
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
        $this->form_validation->set_rules('health_name', lang("health_name"), 'required');
		
        if ($this->form_validation->run() == true) {
			$check = $this->site->masterCheck('health', array('health_name' => $this->input->post('health_name'), 'is_country' => $countryCode));
			if($check == TRUE){
				$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
            	admin_redirect("masters/health");
				exit;	
			}
			
            $data = array(
               
				'health_name' => $this->input->post('health_name'),				
				'created_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
            );
			
           
        }elseif ($this->input->post('add_health')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("masters/health");
        }
		
        if ($this->form_validation->run() == true && $this->masters_model->add_health($data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("health_added"));
            admin_redirect('masters/health');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/health'), 'page' => lang('health')), array('link' => '#', 'page' => lang('add_health')));
			
            $meta = array('page_title' => lang('add_health'), 'bc' => $bc);
            $this->page_construct('masters/add_health', $meta, $this->data);
        }
    }
    function edit_health($id){
		//$this->sma->checkPermissions('health-edit');
		$result = $this->masters_model->getHealthby_ID($id);
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
		
        $this->form_validation->set_rules('health_name', lang("health_name"), 'required');
        
		
        if ($this->form_validation->run() == true) {
			
			if ($this->input->post('health_name') != $result->health_name && $countryCode != $result->is_country) {
				$check = $this->site->masterCheck('health', array('health_name' => $this->input->post('health_name'), 'is_country' => $countryCode));
				if($check == TRUE){
					$this->session->set_flashdata('error', lang('title_has_been_already_exits'));
					admin_redirect("masters/health");
					exit;	
				}
			}
            $data = array(
                
				'health_name' => $this->input->post('health_name'),
				
				'created_by' => $this->session->userdata('user_id'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
				'is_country' => $countryCode
				
				
            );
			
        }
		
		
        if ($this->form_validation->run() == true && $this->masters_model->update_health($id,$data, $countryCode)){
			
            $this->session->set_flashdata('message', lang("health_updated"));
            admin_redirect('masters/health');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('masters/health'), 'page' => lang('health')), array('link' => '#', 'page' => lang('health')));
            $meta = array('page_title' => lang('edit_health'), 'bc' => $bc);
            $this->data['health'] = $result;
			
            $this->page_construct('masters/edit_health', $meta, $this->data);
        }
    }
    function health_status($status,$id){
		//$this->sma->checkPermissions('health-status');
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
		$this->masters_model->update_health_status($data,$id, $countryCode);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	
	
	
}
