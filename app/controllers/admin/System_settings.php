<?php defined('BASEPATH') OR exit('No direct script access allowed');

class system_settings extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            //$this->session->set_flashdata('warning', lang('access_denied'));
            //redirect('admin');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('settings_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

    function index()
    {
        $this->load->library('gst');
        $this->form_validation->set_rules('site_name', lang('site_name'), 'trim|required');
        $this->form_validation->set_rules('dateformat', lang('dateformat'), 'trim|required');
        $this->form_validation->set_rules('timezone', lang('timezone'), 'trim|required');
        $this->form_validation->set_rules('mmode', lang('maintenance_mode'), 'trim|required');
        //$this->form_validation->set_rules('logo', lang('logo'), 'trim');
        $this->form_validation->set_rules('iwidth', lang('image_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('iheight', lang('image_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('twidth', lang('thumbnail_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('theight', lang('thumbnail_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('display_all_products', lang('display_all_products'), 'trim|numeric|required');
        $this->form_validation->set_rules('watermark', lang('watermark'), 'trim|required');
        $this->form_validation->set_rules('currency', lang('default_currency'), 'trim|required');
        $this->form_validation->set_rules('email', lang('default_email'), 'trim|required');
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('default_warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('default_biller'), 'trim|required');
        $this->form_validation->set_rules('tax_rate', lang('product_tax'), 'trim|required');
        $this->form_validation->set_rules('tax_rate2', lang('invoice_tax'), 'trim|required');
        $this->form_validation->set_rules('sales_prefix', lang('sales_prefix'), 'trim');
        $this->form_validation->set_rules('quote_prefix', lang('quote_prefix'), 'trim');
        $this->form_validation->set_rules('purchase_prefix', lang('purchase_prefix'), 'trim');
        $this->form_validation->set_rules('transfer_prefix', lang('transfer_prefix'), 'trim');
        $this->form_validation->set_rules('delivery_prefix', lang('delivery_prefix'), 'trim');
        $this->form_validation->set_rules('payment_prefix', lang('payment_prefix'), 'trim');
        $this->form_validation->set_rules('return_prefix', lang('return_prefix'), 'trim');
        $this->form_validation->set_rules('expense_prefix', lang('expense_prefix'), 'trim');
        $this->form_validation->set_rules('detect_barcode', lang('detect_barcode'), 'trim|required');
        $this->form_validation->set_rules('theme', lang('theme'), 'trim|required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'trim|required|greater_than[9]|less_than[501]');
        $this->form_validation->set_rules('accounting_method', lang('accounting_method'), 'trim|required');
        $this->form_validation->set_rules('product_serial', lang('product_serial'), 'trim|required');
        $this->form_validation->set_rules('product_discount', lang('product_discount'), 'trim|required');
        $this->form_validation->set_rules('bc_fix', lang('bc_fix'), 'trim|numeric|required');
        $this->form_validation->set_rules('protocol', lang('email_protocol'), 'trim|required');
	$this->form_validation->set_rules('backup_path', lang('backup_path'), 'trim|required');
        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_pass', lang('smtp_pass'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('protocol') == 'sendmail') {
            $this->form_validation->set_rules('mailpath', lang('mailpath'), 'required');
        }
        $this->form_validation->set_rules('decimals', lang('decimals'), 'trim|required');
        $this->form_validation->set_rules('decimals_sep', lang('decimals_sep'), 'trim|required');
        $this->form_validation->set_rules('thousands_sep', lang('thousands_sep'), 'trim|required');
        if ($this->Settings->indian_gst) {
            $this->form_validation->set_rules('state', lang('state'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {

            $language = $this->input->post('language');

            if ((file_exists(APPPATH.'language'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'sma_lang.php') && is_dir(APPPATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                admin_redirect("system_settings");
                $lang = 'english';
            }

            $tax1 = ($this->input->post('tax_rate') != 0) ? 1 : 0;
            $tax2 = ($this->input->post('tax_rate2') != 0) ? 1 : 0;
			
			$timezone = explode(',', $this->input->post('timezone'));
			
            $data = array('site_name' => DEMO ? 'SRAM POS' : $this->input->post('site_name'),
				'qsr' => $this->input->post('qsr') ? $this->input->post('qsr') : 0,
                'rows_per_page' => $this->input->post('rows_per_page'),
                'dateformat' => $this->input->post('dateformat'),
                'timezone' => DEMO ? 'Asia/Kuala_Lumpur' : $timezone[0],
				 'timezone_gmt' => DEMO ? 'GMT+08:00' : $timezone[1],
                'mmode' => trim($this->input->post('mmode')),
                'iwidth' => $this->input->post('iwidth'),
                'iheight' => $this->input->post('iheight'),
                'twidth' => $this->input->post('twidth'),
                'theight' => $this->input->post('theight'),
                'watermark' => $this->input->post('watermark'),
                'procurment' => $this->input->post('procurment'),
                // 'allow_reg' => $this->input->post('allow_reg'),
                // 'reg_notification' => $this->input->post('reg_notification'),
                'accounting_method' => $this->input->post('accounting_method'),
                'default_email' => DEMO ? 'info@srampos.com' : $this->input->post('email'),
                'language' => $lang,
                'default_warehouse' => $this->input->post('warehouse'),
                'default_tax_rate' => $this->input->post('tax_rate'),
                'default_tax_rate2' => $this->input->post('tax_rate2'),
                'sales_prefix' => $this->input->post('sales_prefix'),
                'quote_prefix' => $this->input->post('quote_prefix'),
                'purchase_prefix' => $this->input->post('purchase_prefix'),
                'transfer_prefix' => $this->input->post('transfer_prefix'),
                'delivery_prefix' => $this->input->post('delivery_prefix'),
                'payment_prefix' => $this->input->post('payment_prefix'),
                'ppayment_prefix' => $this->input->post('ppayment_prefix'),
                'qa_prefix' => $this->input->post('qa_prefix'),
                'return_prefix' => $this->input->post('return_prefix'),
                'returnp_prefix' => $this->input->post('returnp_prefix'),
                'expense_prefix' => $this->input->post('expense_prefix'),
                'auto_detect_barcode' => trim($this->input->post('detect_barcode')),
                'theme' => trim($this->input->post('theme')),
                'product_serial' => $this->input->post('product_serial'),
                'customer_group' => $this->input->post('customer_group'),
                'product_expiry' => $this->input->post('product_expiry'),
                'product_discount' => $this->input->post('product_discount'),
                'default_currency' => $this->input->post('currency'),
                'bc_fix' => $this->input->post('bc_fix'),
                'tax1' => $tax1,
                'tax2' => $tax2,
                'overselling' => $this->input->post('restrict_sale'),
                'reference_format' => $this->input->post('reference_format'),
                'racks' => $this->input->post('racks'),
                'attributes' => $this->input->post('attributes'),
                'restrict_calendar' => $this->input->post('restrict_calendar'),
                'captcha' => $this->input->post('captcha'),
                'item_addition' => $this->input->post('item_addition'),
                'protocol' => DEMO ? 'mail' : $this->input->post('protocol'),
                'mailpath' => $this->input->post('mailpath'),
                'smtp_host' => $this->input->post('smtp_host'),
                'smtp_user' => $this->input->post('smtp_user'),
                'smtp_port' => $this->input->post('smtp_port'),
                'smtp_crypto' => $this->input->post('smtp_crypto') ? $this->input->post('smtp_crypto') : NULL,
                'decimals' => $this->input->post('decimals'),
                'decimals_sep' => $this->input->post('decimals_sep'),
                'thousands_sep' => $this->input->post('thousands_sep'),
                'default_biller' => $this->input->post('biller'),
                'invoice_view' => $this->input->post('invoice_view'),
                'rtl' => $this->input->post('rtl'),
                'each_spent' => $this->input->post('each_spent') ? $this->input->post('each_spent') : NULL,
                'ca_point' => $this->input->post('ca_point') ? $this->input->post('ca_point') : NULL,
                'each_sale' => $this->input->post('each_sale') ? $this->input->post('each_sale') : NULL,
                'sa_point' => $this->input->post('sa_point') ? $this->input->post('sa_point') : NULL,
                'sac' => $this->input->post('sac'),
                'qty_decimals' => $this->input->post('qty_decimals'),
                'display_all_products' => $this->input->post('display_all_products'),
                'display_symbol' => $this->input->post('display_symbol'),
                'symbol' => $this->input->post('symbol'),
                'remove_expired' => $this->input->post('remove_expired'),
                'barcode_separator' => $this->input->post('barcode_separator'),
                'set_focus' => $this->input->post('set_focus'),
                'disable_editing' => $this->input->post('disable_editing'),
                'price_group' => $this->input->post('price_group'),
                'barcode_img' => $this->input->post('barcode_renderer'),
                'update_cost' => $this->input->post('update_cost'),
                'apis' => $this->input->post('apis'),
                'pdf_lib' => $this->input->post('pdf_lib'),
                'dine_in' => $this->input->post('dine_in'),
                'take_away' => $this->input->post('take_away'),
                'door_delivery' => $this->input->post('door_delivery'),
				'first_level' => $this->input->post('first_level'),
				'second_level' => $this->input->post('second_level'),
                'state' => $this->input->post('state'),
                'customer_discount_request' => $this->input->post('customer_discount_request'),
                'nagative_stock_production' => $this->input->post('nagative_stock_production'),
                'nagative_stock_sale' => $this->input->post('nagative_stock_sale'),
                'excel_header_color' => $this->input->post('excel_header_color'),
                'excel_footer_color' => $this->input->post('excel_footer_color'),
                'billnumber_reset' => $this->input->post('billnumber_reset'),
                'recipe_time_management' => $this->input->post('recipe_time_management'),
                'default_preparation_time' => $this->input->post('default_preparation_time'),
                'night_audit_rights' => $this->input->post('night_audit_rights'),
		'bill_number_start_from' => $this->input->post('bill_number_start_from'),
		'enable_qrcode' => $this->input->post('enable_qrcode'),
		'enable_barcode' => $this->input->post('enable_barcode'),
		'customer_discount' => $this->input->post('customer_discount'),
		'bbq_enable' => $this->input->post('bbq_enable'),
		'bbq_discount' => $this->input->post('bbq_discount'),
		'bbq_adult_price' => $this->input->post('bbq_enable') ? $this->input->post('bbq_adult_price') : '0',
		'bbq_child_price' => $this->input->post('bbq_enable') ? $this->input->post('bbq_child_price') : '0',
		'bbq_kids_price' => $this->input->post('bbq_enable') ? $this->input->post('bbq_kids_price') : '0',
		'bbq_display_items' => $this->input->post('bbq_display_items') ? $this->input->post('bbq_display_items') : '0',
		'order_request_stewardapp' => $this->input->post('order_request_stewardapp') ? $this->input->post('order_request_stewardapp') : 0,
		
		'fb_app_id' => $this->input->post('fb_app_id') ? $this->input->post('fb_app_id') : '0',
		'fb_secret_token' => $this->input->post('fb_secret_token') ? $this->input->post('fb_secret_token') : '0',
		'fb_page_access_token' => $this->input->post('fb_page_access_token') ? $this->input->post('fb_page_access_token') : '0',
		'fb_page_id' => $this->input->post('fb_page_id') ? $this->input->post('fb_page_id') : '0',
		'recipe_time_management' => $this->input->post('recipe_time_management'),
                'notification_time_interval' => $this->input->post('notification_time_interval'),
		'socket_port' => $this->input->post('socket_port'),
		'socket_host' => $this->input->post('socket_host'),
		'socket_enable' => $this->input->post('socket_enable'),
		'backup_path' => $this->input->post('backup_path'),
		'bbq_covers_limit' => $this->input->post('bbq_covers_limit'),
		'ftp_instance_name'=>$this->input->post('ftp_instance_name'),
		'ftp_autobackup_enable' => $this->input->post('ftp_autobackup_enable'),
		'bbq_notify_no_of_times' => $this->input->post('bbq_notify_no_of_times'),
		'bbq_return_notify_no_of_times' => $this->input->post('bbq_return_notify_no_of_times'),
		'bill_request_notify_no_of_times' => $this->input->post('bill_request_notify_no_of_times'),
		'financial_yr_from'=>$this->input->post('financial_yr_from'),
		'financial_yr_to' => $this->input->post('financial_yr_to'),
                'default_store' => $this->input->post('store'),
		);
            file_put_contents('themes\default\admin\assets\js\socket\socket_configuration.js','var socket_port='.$data['socket_port'].';var socket_host="'.$data['socket_host'].'";var socket_enable="'.$data['socket_enable'].'";');
            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }
        }

	
        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
	    $ftp_data['ftp_db_backup_path'] = 'srampos/'.$this->input->post('ftp_instance_name').'/database';
	    $ftp_data['ftp_files_backup_path'] = 'srampos/'.$this->input->post('ftp_instance_name').'/files';
	    $this->site->update_ftpbackup($ftp_data);
            if ( ! DEMO && TIMEZONE != $data['timezone']) {
                if ( ! $this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('system_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect("system_settings");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
			$this->data['level'] = $this->settings_model->getAllGroups();
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
	    $this->data['stores'] = $this->settings_model->getAllStores();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('system_settings')));
            $meta = array('page_title' => lang('system_settings'), 'bc' => $bc);
            $this->page_construct('settings/index', $meta, $this->data);
        }
    }
	
	/*BBQ*/
	
	public function bbqsearch(){
		$category_id = $this->input->post('category_id');	
		$recipe_search = $this->input->post('recipe_search');
		$id = $this->input->post('id');
		
		$bbq = $this->settings_model->getbbqCategoryByID($id);
		$items = $this->settings_model->getCategoryItemsSearch($category_id, $recipe_search);
			
	}
	public function bbqitems($id = NULL)
    {
        
		
        $this->form_validation->set_rules('items[]', lang("items"), 'required');
        if ($this->form_validation->run() == true) {

           $data = array(
		   		'items' => implode(',', $_POST['items'])
		   );
			
        }


        if ($this->form_validation->run() == true && $this->settings_model->updateBBQItems($id, $data)) {
            
            $this->session->set_flashdata('message', lang("BBQ_Items_updated"));
            admin_redirect("system_settings/bbqitems/".$id);
        } else {
             

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id'] = $id;
			$this->data['bbq'] = $this->settings_model->getbbqCategoryByID($id);
            $this->data['items'] = $this->settings_model->getCategoryItems();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('BBQ_items')));
            $meta = array('page_title' => lang('BBQ_items'), 'bc' => $bc);
            $this->page_construct('settings/bbqitems', $meta, $this->data);
        }
    }
	
	public function bbqbuyxgetx()
    {
         
		$this->data['buyxgetx'] = $this->settings_model->getBBQbuyxgetx();
		 
        $this->form_validation->set_rules('days[]', lang("days"), 'required');
        if ($this->form_validation->run() == true) {
			if(!empty(implode(',', $this->input->post('adult_buy')))){
				
				for($i=0; $i<count($this->input->post('days')); $i++){
					$array_update[] = array(
						'days' => $_POST['days'][$i],
						'adult_buy' => $_POST['adult_buy'][$i],
						'adult_get' => $_POST['adult_get'][$i],
						'child_buy' => $_POST['child_buy'][$i],
						'child_get' => $_POST['child_get'][$i],
						'kids_buy' => $_POST['kids_buy'][$i],
						'kids_get' => $_POST['kids_get'][$i],
						'created_by' => $this->session->userdata('user_id'),
						'status' => 1,
						'created_at' => date('Y-m-d H:i:s')
					); 
				}
			}else{
				
				$this->data['error'] = $this->session->set_flashdata('error', 'test');
				 admin_redirect("system_settings/bbqbuyxgetx/");
			}
          
			
        }


        if ($this->form_validation->run() == true && $this->settings_model->updateBBQBUY($array_update)) {
            
            $this->session->set_flashdata('message', lang("BBQ_buy_x_get_x_updated"));
            admin_redirect("system_settings/bbqbuyxgetx/");
        } else {
             

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');


            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('BBQ_buy_x_get_x')));
            $meta = array('page_title' => lang('BBQ_buy_x_get_x'), 'bc' => $bc);
            $this->page_construct('settings/bbqbuyxgetx', $meta, $this->data);
        }
    }
	
	function bbqcategories()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('BBQ Groups')));
        $meta = array('page_title' => lang('BBQ Groups'), 'bc' => $bc);
        $this->page_construct('settings/bbqcategories', $meta, $this->data);
    }
	
	function getbbqCategories()
    {
	//$this->sma->checkPermissions('recipecategories');
       // $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="'.lang('print_barcodes').'" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('bbq_categories')}.id as id, {$this->db->dbprefix('bbq_categories')}.image, {$this->db->dbprefix('bbq_categories')}.code, {$this->db->dbprefix('bbq_categories')}.name", FALSE)
            ->from("bbq_categories")            
            ->group_by('bbq_categories.id')
             ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/bbqitems/$1') . "' class='tip' title='" . lang("BBQ_Items") . "'><i class=\"fa fa-list\"></i></a> <a href='" . admin_url('system_settings/edit_bbqcategory/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_BBQ_Groups") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_BBQ_Groups") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_bbqcategory/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
            /*->add_column("Actions", "<div class=\"text-center\">".$print_barcode." <a href='" . admin_url('system_settings/edit_recipecategory/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_Recipe_Groups") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Recipe_Groups") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_recipecategory/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");*/

        echo $this->datatables->generate();
    }
	
	function add_bbqcategory()
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[bbq_categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]');
       // $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
			
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'adult_price' => $this->input->post('adult_price'),
				'child_price' => $this->input->post('child_price')
                );
			for($i=0; $i<count($this->input->post('active_day')); $i++){					
				$active_data[] = array(
					'active_day' => $_POST['active_day'][$i],
					'discount' => $_POST['discount'][$i] ? $_POST['discount'][$i] : 0,
					'discount_type' => $_POST['discount_type'][$i]
				);
			}
			
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                   
                    $this->session->set_flashdata('error', $error);
                   redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    //echo $this->image_lib->display_errors();
                    $error = $this->image_lib->display_errors();
                    
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } 
		
		

        if ($this->form_validation->run() == true && $sid = $this->settings_model->addbbqCategory($data, $active_data)) {
            $this->session->set_flashdata('message', lang("bbq_category_added"));
            admin_redirect("system_settings/bbqcategories");
            
        } else {


            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_bbqcategory', $this->data);

        }
    }
	
	function edit_bbqcategory($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|required');
        $pr_details = $this->settings_model->getbbqCategoryByID($id);
		
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("category_code"), 'required|is_unique[bbq_categories.code]');
        }
       /* $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
        if ($this->input->post('slug') != $pr_details->slug) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash|is_unique[categories.slug]');
        }*/
        $this->form_validation->set_rules('name', lang("category_name"), 'required|min_length[3]');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {

           $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'adult_price' => $this->input->post('adult_price'),
				'child_price' => $this->input->post('child_price')
                );
			for($i=0; $i<count($this->input->post('active_day')); $i++){					
				$active_data[] = array(
					'active_day' => $_POST['active_day'][$i],
					'discount' => $_POST['discount'][$i] ? $_POST['discount'][$i] : 0,
					'discount_type' => $_POST['discount_type'][$i]
				);
			}

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } elseif ($this->input->post('edit_bbqcategory')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/bbqcategories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatebbqCategory($id, $data, $active_data)) {
            $this->session->set_flashdata('message', lang("BBQ_category_updated"));
            admin_redirect("system_settings/bbqcategories");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $this->settings_model->getbbqCategoryByID($id);
			$this->data['category_day'] = $this->settings_model->getbbqCategoryDay($id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_bbqcategory', $this->data);

        }
    }
	
	function delete_bbqcategory($id = NULL)
    {
	$this->sma->checkPermissions();
       
        if ($this->settings_model->deletebbqCategory($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("bbq_category_deleted")));
        }
    }
	
	function bbqcategory_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deletebbqCategory($id);
                    }
                    $this->session->set_flashdata('message', lang("bbq_categories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getbbqCategoryByID($id);
                       
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->image);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	function bbq_discounts()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('BBQ_discount')));
        $meta = array('page_title' => lang('BBQ_discount'), 'bc' => $bc);
        $this->page_construct('settings/bbq_discount', $meta, $this->data);
    }	

	function get_bbq_discount()
    {

	$this->sma->checkPermissions('customer_discounts');
        $this->load->library('datatables');
        $this->datatables
            //->select("id, name, discount_type, value, created_dt")
	    ->select("'sno',id, name, discount, discount_type, created_dt,status")
            ->from("diccounts_for_bbq")
            ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("edit_BBQ_discount") . "' href='" . admin_url('system_settings/edit_bbq_discount/$1') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_BBQ_discount") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_bbq_discount/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
	    ->edit_column('status', '$1__$2', 'status, id');
	echo $this->datatables->generate();
    }  
	
    function add_bbq_discounts()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('discount_type', lang("discount_type"), 'required');
		$this->form_validation->set_rules('discount', lang("discount"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'discount_type' => $this->input->post('discount_type'),
                'discount' => $this->input->post('discount'),
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
                'created_dt' =>  date("Y-m-d-H-i-s"),
				'status' => 1
            );


        } elseif ($this->input->post('add_bbq_discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/bbq_discounts");
        }
	
        if ($this->form_validation->run() == true && $this->settings_model->addBBQDiscount($data)) {
            $this->session->set_flashdata('message', lang("BBQ_discount_added"));
            admin_redirect("system_settings/bbq_discounts");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings/bbq_discounts'), 'page' => lang('BBQ_discounts')), array('link' => '#', 'page' => lang('BBQ_discounts')));
			$meta = array('page_title' => lang('BBQ_discount'), 'bc' => $bc);
			$this->page_construct('settings/add_bbq_discount',$meta,$this->data);
        }
    }
    function edit_bbq_discount($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('discount_type', lang("discount_type"), 'required');
        $this->form_validation->set_rules('discount', lang("discount"), 'required');

        $bbq_discounts = $this->settings_model->getBBQDiscount($id);
        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'discount_type' => $this->input->post('discount_type'),
                'discount' => $this->input->post('discount'),
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
                'created_dt' =>  date("Y-m-d-H-i-s"),
            );

        } elseif ($this->input->post('edit_bbq_discount')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/bbq_discounts");
        }

	
        if ($this->form_validation->run() == true && $this->settings_model->updateBBQDiscount($id, $data)) {
            $this->session->set_flashdata('message', lang("BBQ_discount_updated"));
            admin_redirect("system_settings/bbq_discounts");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['bbq_discounts'] = $bbq_discounts;
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings/bbq_discounts'), 'page' => lang('BBQ_discounts')), array('link' => '#', 'page' => lang('BBQ_discounts')));
			$meta = array('page_title' => lang('BBQ_discount'), 'bc' => $bc);
			$this->page_construct('settings/edit_bbq_discount',$meta,$this->data);
			
        }
    }
   
    function delete_bbq_discount($id = NULL)
    {
	$this->sma->checkPermissions();
       
        if ($this->settings_model->deleteBBQDiscount($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("bbq_discount_deleted")));
        }
    }
	
    function bbq_discount_deactivate($id){
		$this->sma->checkPermissions('cus_dis_status');
		$this->settings_model->updateBBQDiscount_status($id,0);
		redirect($_SERVER["HTTP_REFERER"]);
    }
    function bbq_discount_activate($id){
		$this->sma->checkPermissions('cus_dis_status');
		$this->settings_model->updateBBQDiscount_status($id,1);
		redirect($_SERVER["HTTP_REFERER"]);
    }
	/*BBQ END*/

    function paypal()
    {

        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {

            $data = array('active' => $this->input->post('active'),
                'account_email' => $this->input->post('account_email'),
                'fixed_charges' => $this->input->post('fixed_charges'),
                'extra_charges_my' => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other')
            );
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePaypal($data)) {
            $this->session->set_flashdata('message', $this->lang->line('paypal_setting_updated'));
            admin_redirect("system_settings/paypal");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['paypal'] = $this->settings_model->getPaypalSettings();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('paypal_settings')));
            $meta = array('page_title' => lang('paypal_settings'), 'bc' => $bc);
            $this->page_construct('settings/paypal', $meta, $this->data);
        }
    }

    function skrill()
    {

        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {

            $data = array('active' => $this->input->post('active'),
                'account_email' => $this->input->post('account_email'),
                'fixed_charges' => $this->input->post('fixed_charges'),
                'extra_charges_my' => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other')
            );
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSkrill($data)) {
            $this->session->set_flashdata('message', $this->lang->line('skrill_setting_updated'));
            admin_redirect("system_settings/skrill");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['skrill'] = $this->settings_model->getSkrillSettings();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('skrill_settings')));
            $meta = array('page_title' => lang('skrill_settings'), 'bc' => $bc);
            $this->page_construct('settings/skrill', $meta, $this->data);
        }
    }

    function change_logo()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            $this->sma->md();
        }
	$this->sma->checkPermissions();
        $this->load->helper('security');
        $this->form_validation->set_rules('site_logo', lang("site_logo"), 'xss_clean');
        $this->form_validation->set_rules('login_logo', lang("login_logo"), 'xss_clean');
        $this->form_validation->set_rules('biller_logo', lang("biller_logo"), 'xss_clean');
        if ($this->form_validation->run() == true) {

            if ($_FILES['site_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = 300;
                $config['max_height'] = 80;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('site_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $site_logo = $this->upload->file_name;
                $this->db->update('settings', array('logo' => $site_logo), array('setting_id' => 1));
            }

            if ($_FILES['login_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = 300;
                $config['max_height'] = 80;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('login_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $login_logo = $this->upload->file_name;
                $this->db->update('settings', array('logo2' => $login_logo), array('setting_id' => 1));
            }

            if ($_FILES['biller_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = 300;
                $config['max_height'] = 80;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('biller_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
            }

            $this->session->set_flashdata('message', lang('logo_uploaded'));
            redirect($_SERVER["HTTP_REFERER"]);

        } elseif ($this->input->post('upload_logo')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/change_logo', $this->data);
        }
    }
	
	function recipe_category_suggestions($term = NULL, $limit = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->settings_model->getrecipeCategorySuggestions($term, $limit);
        $this->sma->send_json($rows);
    }
	function recipe_item_suggestions($term = NULL, $limit = NULL)
    {
        // $this->sma->checkPermissions('index');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->settings_model->getrecipeItemSuggestions($term, $limit);
        $this->sma->send_json($rows);
    }

    public function write_index($timezone)
    {

        $template_path = './assets/config_dumps/index.php';
        $output_path = SELF;
        $index_file = file_get_contents($template_path);
        $new = str_replace("%TIMEZONE%", $timezone, $index_file);
        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new)) {
                @chmod($output_path, 0644);
                return true;
            } else {
                @chmod($output_path, 0644);
                return false;
            }
        } else {
            @chmod($output_path, 0644);
            return false;
        }
    }

    function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->form_validation->set_rules('purchase_code', lang("purchase_code"), 'required');
        $this->form_validation->set_rules('srampos_username', lang("srampos_username"), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('settings', array('purchase_code' => $this->input->post('purchase_code', TRUE), 'srampos_username' => $this->input->post('srampos_username', TRUE)), array('setting_id' => 1));
            admin_redirect('system_settings/updates');
        } else {
            $fields = array('version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->srampos_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol.'api.srampos.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('updates')));
            $meta = array('page_title' => lang('updates'), 'bc' => $bc);
            $this->page_construct('settings/updates', $meta, $this->data);
        }
    }

    function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->sma->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                admin_redirect("system_settings/updates");
            }
        }
        $this->db->update('settings', array('version' => $version, 'update' => 0), array('setting_id' => 1));
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect("system_settings/updates");
    }

    function backups()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();


        /*$this->load->dbutil();
        $prefs = array(     
                'format'      => 'zip',             
                'filename'    => 'my_db_backup.sql'
              );
        $backup =& $this->dbutil->backup($prefs); 
        $db_name = 'backup-on-'. date("Y-m-d-H-i-s") .'.zip';
        $save = '/upload/_tmp/'.$db_name;
        $this->load->helper('file');
        write_file($save, $backup); 
        $this->load->helper('download');
        force_download($db_name, $backup);*/ 
        
        $this->data['files'] = glob('./files/backups/*.zip', GLOB_BRACE);
        $this->data['dbs'] = glob('./files/backups/*.txt', GLOB_BRACE);
        krsort($this->data['files']); krsort($this->data['dbs']);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('backups')));
        $meta = array('page_title' => lang('backups'), 'bc' => $bc);
        $this->page_construct('settings/backups', $meta, $this->data);
    }

    function backup_database()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        $this->load->dbutil();
        $prefs = array(
            'format' => 'txt',
            'filename' => 'sma_db_backup.sql'
        );
        $back = $this->dbutil->backup($prefs);
        $backup =& $back;
        $db_name = 'db-backup-on-' . date("Y-m-d-H-i-s") . '.txt';
        $save = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('messgae', lang('db_saved'));
        admin_redirect("system_settings/backups");
    }

    function backup_files()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        $name = 'file-backup-' . date("Y-m-d-H-i-s");
        $this->sma->zip("./", './files/backups/', $name);
        $this->session->set_flashdata('messgae', lang('backup_saved'));
        admin_redirect("system_settings/backups");
        exit();
    }

    function restore_database($dbfile)
    {
	$this->sma->checkPermissions();
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        $file = file_get_contents('./files/backups/' . $dbfile . '.txt');
        // $this->db->conn_id->multi_query($file);
        mysqli_multi_query($this->db->conn_id, $file);
        $this->db->conn_id->close();
        admin_redirect('logout/db');
    }

    function download_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
           // $this->session->set_flashdata('error', lang('access_denied'));
           // admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        $this->load->library('zip');
        $this->zip->read_file('./files/backups/' . $dbfile . '.txt');
        $name = $dbfile . '.zip';
        $this->zip->download($name);
        exit();
    }

    function download_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        $this->load->helper('download');
        force_download('./files/backups/' . $zipfile . '.zip', NULL);
        exit();
    }

    function restore_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        $file = './files/backups/' . $zipfile . '.zip';
        $this->sma->unzip($file, './');
        $this->session->set_flashdata('success', lang('files_restored'));
        admin_redirect("system_settings/backups");
        exit();
    }

    function delete_database($dbfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            //$this->session->set_flashdata('error', lang('access_denied'));
            //admin_redirect("welcome");
        }
	$this->sma->checkPermissions();
        unlink('./files/backups/' . $dbfile . '.txt');
        $this->session->set_flashdata('messgae', lang('db_deleted'));
        admin_redirect("system_settings/backups");
    }

    function delete_backup($zipfile)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect("welcome");
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('messgae', lang('backup_deleted'));
        admin_redirect("system_settings/backups");
    }

    function email_templates($template = "credentials")
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('mail_body', lang('mail_message'), 'trim|required');
        $this->load->helper('file');
        $temp_path = is_dir('./themes/' . $this->theme . 'email_templates/');
        $theme = $temp_path ? $this->theme : 'default';
        if ($this->form_validation->run() == true) {
            $data = $_POST["mail_body"];
            if (write_file('./themes/' . $this->theme . 'email_templates/' . $template . '.html', $data)) {
                $this->session->set_flashdata('message', lang('message_successfully_saved'));
                admin_redirect('system_settings/email_templates#' . $template);
            } else {
                $this->session->set_flashdata('error', lang('failed_to_save_message'));
                admin_redirect('system_settings/email_templates#' . $template);
            }
        } else {

            $this->data['credentials'] = file_get_contents('./themes/' . $this->theme . 'email_templates/credentials.html');
            $this->data['sale'] = file_get_contents('./themes/' . $this->theme . 'email_templates/sale.html');
            $this->data['quote'] = file_get_contents('./themes/' . $this->theme . 'email_templates/quote.html');
            $this->data['purchase'] = file_get_contents('./themes/' . $this->theme . 'email_templates/purchase.html');
            $this->data['transfer'] = file_get_contents('./themes/' . $this->theme . 'email_templates/transfer.html');
            $this->data['payment'] = file_get_contents('./themes/' . $this->theme . 'email_templates/payment.html');
            $this->data['forgot_password'] = file_get_contents('./themes/' . $this->theme . 'email_templates/forgot_password.html');
            $this->data['activate_email'] = file_get_contents('./themes/' . $this->theme . 'email_templates/activate_email.html');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('email_templates')));
            $meta = array('page_title' => lang('email_templates'), 'bc' => $bc);
            $this->page_construct('settings/email_templates', $meta, $this->data);
        }
    }

    function create_group()
    {

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash|is_unique[groups.name]');

        if ($this->form_validation->run() == TRUE) {
            $data = array('name' => $this->input->post('group_name'), 'description' => $this->input->post('description'));
        } elseif ($this->input->post('create_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/user_groups");
        }

        if ($this->form_validation->run() == TRUE && ($new_group_id = $this->settings_model->addGroup($data))) {
            $this->session->set_flashdata('message', lang('group_added'));
            admin_redirect("system_settings/permissions/" . $new_group_id);

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name'),
            );
            $this->data['description'] = array(
                'name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('description'),
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/create_group', $this->data);
        }
    }

    function edit_group($id)
    {

        if (!$id || empty($id)) {
            admin_redirect('system_settings/user_groups');
        }

        $group = $this->settings_model->getGroupByID($id);

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash');

        if ($this->form_validation->run() === TRUE) {
            $data = array('name' => $this->input->post('group_name'), 'description' => $this->input->post('description'));
            $group_update = $this->settings_model->updateGroup($id, $data);

            if ($group_update) {
                $this->session->set_flashdata('message', lang('group_udpated'));
            } else {
                $this->session->set_flashdata('error', lang('attempt_failed'));
            }
            admin_redirect("system_settings/user_groups");
        } else {


            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group'] = $group;

            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name', $group->name),
            );
            $this->data['group_description'] = array(
                'name' => 'group_description',
                'id' => 'group_description',
                'type' => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_description', $group->description),
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_group', $this->data);
        }
    }

    function permissions($id = NULL)
    {
            
        $this->form_validation->set_rules('group', lang("group"), 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {

            $data = array(
                'products-index' => $this->input->post('products-index'),
                'products-edit' => $this->input->post('products-edit'),
                'products-add' => $this->input->post('products-add'),
                'products-delete' => $this->input->post('products-delete'),
                'products-cost' => $this->input->post('products-cost'),
                'products-price' => $this->input->post('products-price'),
                'customers-index' => $this->input->post('customers-index'),
                'customers-edit' => $this->input->post('customers-edit'),
                'customers-add' => $this->input->post('customers-add'),
                'customers-delete' => $this->input->post('customers-delete'),
                'suppliers-index' => $this->input->post('suppliers-index'),
                'suppliers-edit' => $this->input->post('suppliers-edit'),
                'suppliers-add' => $this->input->post('suppliers-add'),
                'suppliers-delete' => $this->input->post('suppliers-delete'),
                'sales-index' => $this->input->post('sales-index'),
                'sales-edit' => $this->input->post('sales-edit'),
                'sales-add' => $this->input->post('sales-add'),
                'sales-delete' => $this->input->post('sales-delete'),
                'sales-email' => $this->input->post('sales-email'),
                'sales-pdf' => $this->input->post('sales-pdf'),
                'sales-deliveries' => $this->input->post('sales-deliveries'),
                'sales-edit_delivery' => $this->input->post('sales-edit_delivery'),
                'sales-add_delivery' => $this->input->post('sales-add_delivery'),
                'sales-delete_delivery' => $this->input->post('sales-delete_delivery'),
                'sales-email_delivery' => $this->input->post('sales-email_delivery'),
                'sales-pdf_delivery' => $this->input->post('sales-pdf_delivery'),
                'sales-gift_cards' => $this->input->post('sales-gift_cards'),
                'sales-edit_gift_card' => $this->input->post('sales-edit_gift_card'),
                'sales-add_gift_card' => $this->input->post('sales-add_gift_card'),
                'sales-delete_gift_card' => $this->input->post('sales-delete_gift_card'),
                'quotes-index' => $this->input->post('quotes-index'),
                'quotes-edit' => $this->input->post('quotes-edit'),
                'quotes-add' => $this->input->post('quotes-add'),
                'quotes-delete' => $this->input->post('quotes-delete'),
                'quotes-email' => $this->input->post('quotes-email'),
                'quotes-pdf' => $this->input->post('quotes-pdf'),
                'purchases-index' => $this->input->post('purchases-index'),
                'purchases-edit' => $this->input->post('purchases-edit'),
                'purchases-add' => $this->input->post('purchases-add'),
                'purchases-delete' => $this->input->post('purchases-delete'),
                'purchases-email' => $this->input->post('purchases-email'),
                'purchases-pdf' => $this->input->post('purchases-pdf'),
				
				'purchases_order-index' => $this->input->post('purchases_order-index'),
                'purchases_order-edit' => $this->input->post('purchases_order-edit'),
                'purchases_order-add' => $this->input->post('purchases_order-add'),
                'purchases_order-delete' => $this->input->post('purchases_order-delete'),
                'purchases_order-email' => $this->input->post('hases_order-email'),
                'purchases_order-pdf' => $this->input->post('purchases_order-pdf'),
				
                'transfers-index' => $this->input->post('transfers-index'),
                'transfers-edit' => $this->input->post('transfers-edit'),
                'transfers-add' => $this->input->post('transfers-add'),
                'transfers-delete' => $this->input->post('transfers-delete'),
                'transfers-email' => $this->input->post('transfers-email'),
                'transfers-pdf' => $this->input->post('transfers-pdf'),
                'sales-return_sales' => $this->input->post('sales-return_sales'),
                
                'sales-payments' => $this->input->post('sales-payments'),
                'purchases-payments' => $this->input->post('purchases-payments'),
                'purchases-expenses' => $this->input->post('purchases-expenses'),
				
				'purchases-order-payments' => $this->input->post('purchases-order-payments'),
                'purchases-order-expenses' => $this->input->post('purchases-order-expenses'),
				
                'products-adjustments' => $this->input->post('products-adjustments'),
                'bulk_actions' => $this->input->post('bulk_actions'),
                'customers-deposits' => $this->input->post('customers-deposits'),
                'customers-delete_deposit' => $this->input->post('customers-delete_deposit'),
                'products-barcode' => $this->input->post('products-barcode'),
                'purchases-return_purchases' => $this->input->post('purchases-return_purchases'),
				
				'purchases-order-return' => $this->input->post('purchases-order-return'),
				
                
                'products-stock_count' => $this->input->post('products-stock_count'),
                'edit_price' => $this->input->post('edit_price'),
				
				 'pos-dinein' => $this->input->post('pos-dinein'),
				 'pos-takeaway' => $this->input->post('pos-takeaway'),
				 'pos-door_delivery' => $this->input->post('pos-door_delivery'),
				 'pos-orders' => $this->input->post('pos-orders'),
				 'pos-kitchens' => $this->input->post('pos-kitchens'),
				 'pos-billing' => $this->input->post('pos-billing'),
				 'pos-table_view' => $this->input->post('pos-table_view'),
				 'pos-table_add' => $this->input->post('pos-table_add'),
				 'pos-table_edit' => $this->input->post('pos-table_edit'),
				 'pos-quantity_edit' => $this->input->post('pos-quantity_edit'),
				 'pos-orders_cancel' => $this->input->post('pos-orders_cancel'),
				 'pos-sendtokitchen' => $this->input->post('pos-sendtokitchen'),
				 'pos-dinein_orders' => $this->input->post('pos-dinein_orders'),
				 'pos-takeaway_orders' => $this->input->post('pos-takeaway_orders'),
				 'pos-door_delivery_orders' => $this->input->post('pos-door_delivery_orders'),
				 'pos-change_single_status' => $this->input->post('pos-change_single_status'),
				 'pos-change_multiple_status' => $this->input->post('pos-change_multiple_status'),
				 'pos-cancel_order_items' => $this->input->post('pos-cancel_order_items'),
				 'pos-new_order_create' => $this->input->post('pos-new_order_create'),
				 'pos-new_split_create' => $this->input->post('pos-new_split_create'),
				 'pos-bil_generator' => $this->input->post('pos-bil_generator'),
				 'pos-auto_bil' => $this->input->post('pos-auto_bil'),
				 'pos-no_discount' => $this->input->post('pos-no_discount'),
				 'pos-no_tax' => $this->input->post('pos-no_tax'),
				 'pos-kitchen_view' => $this->input->post('pos-kitchen_view'),
				 'pos-kitchen_change_single_status' => $this->input->post('pos-kitchen_change_single_status'),
				 'pos-kitchen_change_multiple_status' => $this->input->post('pos-kitchen_change_multiple_status'),
				 'pos-kitchen_cancel_order_items' => $this->input->post('pos-kitchen_cancel_order_items'),
				 'pos-kot_print' => $this->input->post('pos-kot_print'),
				 'pos-dinein_bils' => $this->input->post('pos-dinein_bils'),
				 'pos-takeaway_bils' => $this->input->post('pos-takeaway_bils'),
				 'pos-door_delivery_bils' => $this->input->post('pos-door_delivery_bils'),
				 'pos-bil_cancel' => $this->input->post('pos-bil_cancel'),
				 'pos-bil_payment' => $this->input->post('pos-bil_payment'),
				 'pos-bil_print' => $this->input->post('pos-bil_print'),
				 'pos-report_view' => $this->input->post('pos-report_view'),
				 'pos-today_item_report' => $this->input->post('pos-today_item_report'),
				 'pos-daywise_report' => $this->input->post('pos-daywise_report'),
				 'pos-cashierwise_report' => $this->input->post('pos-cashierwise_report'),
                 'pos-open_sale_register' => $this->input->post('pos-open_sale_register'),
				/*'pos-waiter' => $this->input->post('pos-waiter'),
				'pos-kitchen' => $this->input->post('pos-kitchen'),
				'pos-cashier' => $this->input->post('pos-cashier'),
				'pos-report' => $this->input->post('pos-report'),*/
                'nightaudit-index' => $this->input->post('nightaudit-index'),
                'nightaudit-edit' => $this->input->post('nightaudit-edit'),
                'nightaudit-add' => $this->input->post('nightaudit-add'),
                'nightaudit-delete' => $this->input->post('nightaudit-delete'),
                'nightaudit-pdf' => $this->input->post('nightaudit-pdf'),
                'blind_night_audit' => $this->input->post('blind_night_audit'),
               
                'recipe-index' => $this->input->post('recipe-index'),
                'recipe-edit' => $this->input->post('recipe-edit'),
                'recipe-add' => $this->input->post('recipe-add'),
                'recipe-delete' => $this->input->post('recipe-delete'),
                'recipe-csv' => $this->input->post('recipe-csv'),
                
                'reports-warehouse_stock' => $this->input->post('reports-warehouse_stock'),
                'reports-quantity_alerts' => $this->input->post('reports-quantity_alerts'),
                'reports-expiry_alerts' => $this->input->post('reports-expiry_alerts'),
                'reports-products' => $this->input->post('reports-products'),
                'reports-daily_sales' => $this->input->post('reports-daily_sales'),
                'reports-monthly_sales' => $this->input->post('reports-monthly_sales'),
                'reports-sales' => $this->input->post('reports-sales'),
                'reports-payments' => $this->input->post('reports-payments'),
                'reports-expenses' => $this->input->post('reports-expenses'),
                'reports-daily_purchases' => $this->input->post('reports-daily_purchases'),
                'reports-monthly_purchases' => $this->input->post('reports-monthly_purchases'),
                'reports-purchases' => $this->input->post('reports-purchases'),	
                'reports-customers' => $this->input->post('reports-customers'),
                'reports-suppliers' => $this->input->post('reports-suppliers'),
                'reports-users' => $this->input->post('reports-users'),
                'reports-profit_loss' => $this->input->post('reports-profit_loss'),
		'reports-brands' => $this->input->post('reports-brands'),
                'reports-categories' => $this->input->post('reports-categories'),
                'reports-adjustments' => $this->input->post('reports-adjustments'),
                'reports-stock_audit' => $this->input->post('reports-stock_audit'),
                'reports-cover_analysis' => $this->input->post('reports-cover_analysis'),
                
                
                'reports-tax_reports' => $this->input->post('reports-tax_reports'),
                'reports-best_sellers' => $this->input->post('reports-best_sellers'),
                'reports-recipe' => $this->input->post('reports-recipe'),
                'reports-pos_settlement' => $this->input->post('reports-pos_settlement'),
                'reports-kot_details' => $this->input->post('reports-kot_details'),
                'reports-user_reports' => $this->input->post('reports-user_reports'),
                'reports-home_delivery' => $this->input->post('reports-home_delivery'),
                'reports-take_away' => $this->input->post('reports-take_away'),
                'reports-bill_details' => $this->input->post('reports-bill_details'),
                
                'reports-hourly_wise' => $this->input->post('reports-hourly_wise'),
                'reports-discount_summary' => $this->input->post('reports-discount_summary'),
                'reports-void_bills' => $this->input->post('reports-void_bills'),
                'reports-popular_analysis' => $this->input->post('reports-popular_analysis'),
                
		'reports-purchases-order' => $this->input->post('reports-purchases-order'),   
        'reports-postpaid_bills' => $this->input->post('reports-postpaid_bills'), 
                
                'products-import_csv' => $this->input->post('products-import_csv'),
                
                
                'production-index' => $this->input->post('production-index'),
                'production-add' => $this->input->post('production-add'),
                'production-edit' => $this->input->post('production-edit'),
                'production-delete' => $this->input->post('production-delete'),
                'production-balance' => $this->input->post('production-balance'),
                'production-balance_edit' => $this->input->post('production-balance_edit'),
                
                
                'saleitem_to_purchasesitem-index' => $this->input->post('saleitem_to_purchasesitem-index'),
                
                'tables-index' => $this->input->post('tables-index'),
                'tables-add' => $this->input->post('tables-add'),
                'tables-edit' => $this->input->post('tables-edit'),
                'tables-delete' => $this->input->post('tables-delete'),
                
                'tables-kitchens' => $this->input->post('tables-kitchens'),
                'tables-add_kitchen' => $this->input->post('tables-add_kitchen'),
                'tables-edit_kitchen' => $this->input->post('tables-edit_kitchen'),
                'tables-delete_kitchen' => $this->input->post('tables-delete_kitchen'),
                
                'tables-areas' => $this->input->post('tables-areas'),
                'tables-add_area' => $this->input->post('tables-add_area'),
                'tables-edit_area' => $this->input->post('tables-edit_area'),
                'tables-delete_area' => $this->input->post('tables-delete_area'),
                
                'system_settings-warehouses' => $this->input->post('system_settings-warehouses'),
                'system_settings-add_warehouse' => $this->input->post('system_settings-add_warehouse'),
                'system_settings-edit_warehouse' => $this->input->post('system_settings-edit_warehouse'),
                'system_settings-delete_warehouse' => $this->input->post('system_settings-delete_warehouse'),

                'system_settings_categories' => $this->input->post('system_settings_categories'),
                'system_settings_categories_add' => $this->input->post('system_settings_categories_add'),
                'system_settings_categories_edit' => $this->input->post('system_settings_categories_edit'),
                'system_settings_categories_delete' => $this->input->post('system_settings_categories_delete'),
                
                'material_request-index' => $this->input->post('material_request-index'),
                'material_request-add' => $this->input->post('material_request-add'),
                'material_request-edit' => $this->input->post('material_request-edit'),
                'material_request-delete' => $this->input->post('material_request-delete'),
                
                
                'auth-users' => $this->input->post('auth-users'),
                'auth-create_user' => $this->input->post('auth-create_user'),
                'auth-profile' => $this->input->post('auth-profile'),
                'auth-delete' => $this->input->post('auth-delete'),
                'auth-excel' => $this->input->post('auth-excel'),
                
                'billers-index' => $this->input->post('billers-index'),
                'billers-add' => $this->input->post('billers-add'),
                'billers-edit' => $this->input->post('billers-edit'),
                'billers-delete' => $this->input->post('billers-delete'),
                'billers-excel' => $this->input->post('billers-excel'),
                'pos-cancel_order_remarks' => $this->input->post('pos-cancel_order_remarks'),
                'pos-view_allusers_orders' => $this->input->post('pos-view_allusers_orders'),
                'pos-add_printer'=>$this->input->post('pos-add_printer'),
		'pos-edit_printer'=>$this->input->post('pos-edit_printer'),
		'pos-printers'=>$this->input->post('pos-printers'),
		'pos-delete_printer'=>$this->input->post('pos-delete_printer'),
		
		
		'system_settings-payment_methods'=>$this->input->post('system_settings-payment_methods'),
		'system_settings-add_payment_method'=>$this->input->post('system_settings-add_payment_method'),
		'system_settings-tender_type_status'=>$this->input->post('system_settings-tender_type_status'),
		'system_settings-customfeedback'=>$this->input->post('system_settings-customfeedback'),
		'system_settings-add_customfeedback'=>$this->input->post('system_settings-add_customfeedback'),
		'system_settings-edit_customfeedback'=>$this->input->post('system_settings-edit_customfeedback'),
		'system_settings-delete_customfeedback'=>$this->input->post('system_settings-delete_customfeedback'),
		'system_settings-change_logo'=>$this->input->post('system_settings-change_logo'),
		
		'system_settings-currencies'=>$this->input->post('system_settings-currencies'),
		'system_settings-add_currency'=>$this->input->post('system_settings-add_currency'),
		'system_settings-edit_currency'=>$this->input->post('system_settings-edit_currency'),
		'system_settings-delete_currency'=>$this->input->post('system_settings-delete_currency'),
		
		'system_settings-customer_groups'=>$this->input->post('system_settings-customer_groups'),
		'system_settings-add_customer_group'=>$this->input->post('system_settings-add_customer_group'),
		'system_settings-edit_customer_group'=>$this->input->post('system_settings-edit_customer_group'),
		'system_settings-delete_customer_group'=>$this->input->post('system_settings-delete_customer_group'),
		
		'system_settings-categories'=>$this->input->post('system_settings-categories'),
		'system_settings-add_category'=>$this->input->post('system_settings-add_category'),
		'system_settings-edit_category'=>$this->input->post('system_settings-edit_category'),
		'system_settings-delete_category'=>$this->input->post('system_settings-delete_category'),
		//'products-barcode'=>$this->input->post('products-barcode'),
		
		'system_settings-recipecategories'=>$this->input->post('system_settings-recipecategories'),
		'system_settings-add_recipecategory'=>$this->input->post('system_settings-add_recipecategory'),
		'system_settings-edit_recipecategory'=>$this->input->post('system_settings-edit_recipecategory'),
		'system_settings-delete_recipecategory'=>$this->input->post('system_settings-delete_recipecategory'),
		
		'system_settings-expense_categories'=>$this->input->post('system_settings-expense_categories'),
		'system_settings-add_expense_category'=>$this->input->post('system_settings-add_expense_category'),
		'system_settings-edit_expense_category'=>$this->input->post('system_settings-edit_expense_category'),
		'system_settings-delete_expense_category'=>$this->input->post('system_settings-delete_expense_category'),
		
		'system_settings-units'=>$this->input->post('system_settings-units'),
		'system_settings-add_unit'=>$this->input->post('system_settings-add_unit'),
		'system_settings-edit_unit'=>$this->input->post('system_settings-edit_unit'),
		'system_settings-delete_unit'=>$this->input->post('system_settings-delete_unit'),
		
		'system_settings-brands'=>$this->input->post('system_settings-brands'),
		'system_settings-add_brand'=>$this->input->post('system_settings-add_brand'),
		'system_settings-edit_brand'=>$this->input->post('system_settings-edit_brand'),
		'system_settings-delete_brand'=>$this->input->post('system_settings-delete_brand'),
		
		'system_settings-sales_type'=>$this->input->post('system_settings-sales_type'),
		'system_settings-add_sales_type'=>$this->input->post('system_settings-add_sales_type'),
		'system_settings-edit_sales_type'=>$this->input->post('system_settings-edit_sales_type'),
		'system_settings-delete_sales_type'=>$this->input->post('system_settings-delete_sales_type'),
		
		'system_settings-tax_rates'=>$this->input->post('system_settings-tax_rates'),
		'system_settings-add_tax_rate'=>$this->input->post('system_settings-add_tax_rate'),
		'system_settings-edit_tax_rate'=>$this->input->post('system_settings-edit_tax_rate'),
		'system_settings-delete_tax_rate'=>$this->input->post('system_settings-delete_tax_rate'),
		
		'system_settings-discounts'=>$this->input->post('system_settings-discounts'),
		'system_settings-add_discount'=>$this->input->post('system_settings-add_discount'),
		'system_settings-delete_discount'=>$this->input->post('system_settings-delete_discount'),
		
		'system_settings-customer_discounts'=>$this->input->post('system_settings-customer_discounts'),
		'system_settings-add_customer_discounts'=>$this->input->post('system_settings-add_customer_discounts'),
		'system_settings-edit_customer_discount'=>$this->input->post('system_settings-edit_customer_discount'),
		'system_settings-delete_customer_discount'=>$this->input->post('system_settings-delete_customer_discount'),
		'system_settings-cus_dis_status'=>$this->input->post('system_settings-cus_dis_status'),
		
		
		'system_settings-buy_get'=>$this->input->post('system_settings-buy_get'),
		'system_settings-add_buy'=>$this->input->post('system_settings-add_buy'),
		'system_settings-edit_buy'=>$this->input->post('system_settings-edit_buy'),
		'system_settings-delete_buy'=>$this->input->post('system_settings-delete_buy'),
		
		'system_settings-email_templates'=>$this->input->post('system_settings-email_templates'),
		
		'system_settings-backups'=>$this->input->post('system_settings-backups'),
		'system_settings-backup_database'=>$this->input->post('system_settings-backup_database'),
		'system_settings-download_database'=>$this->input->post('system_settings-download_database'),
		'system_settings-restore_database'=>$this->input->post('system_settings-restore_database'),
		'system_settings-delete_database'=>$this->input->post('system_settings-delete_database'),
		'reports-feedback' => $this->input->post('reports-feedback'),
		
		
            );

            if (POS) {
                $data['pos-index'] = $this->input->post('pos-index');
            }

            //$this->sma->print_arrays($data);
			
        }


        if ($this->form_validation->run() == true && $this->settings_model->updatePermissions($id, $data)) {
            
            $this->session->set_flashdata('message', lang("group_permissions_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
             

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id'] = $id;
            $this->data['p'] = $this->settings_model->getGroupPermissions($id);
            $this->data['group'] = $this->settings_model->getGroupByID($id);

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('group_permissions')));
            $meta = array('page_title' => lang('group_permissions'), 'bc' => $bc);
            $this->page_construct('settings/permissions', $meta, $this->data);
        }
    }

    function user_groups()
    {

        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang("access_denied"));
            admin_redirect('auth');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['groups'] = $this->settings_model->getGroups();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('groups')));
        $meta = array('page_title' => lang('groups'), 'bc' => $bc);
        $this->page_construct('settings/user_groups', $meta, $this->data);
    }

    function delete_group($id = NULL)
    {

        if ($this->settings_model->checkGroupUsers($id)) {
            $this->session->set_flashdata('error', lang("group_x_b_deleted"));
            admin_redirect("system_settings/user_groups");
        }

        if ($this->settings_model->deleteGroup($id)) {
            $this->session->set_flashdata('message', lang("group_deleted"));
            admin_redirect("system_settings/user_groups");
        }
    }

    function currencies()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('currencies')));
        $meta = array('page_title' => lang('currencies'), 'bc' => $bc);
        $this->page_construct('settings/currencies', $meta, $this->data);
    }

    function getCurrencies()
    {
	$this->sma->checkPermissions('currencies');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, code, name, rate, symbol")
            ->from("currencies")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang("edit_currency") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_currency") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_currency()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("currency_code"), 'trim|is_unique[currencies.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('rate', lang("exchange_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'rate' => $this->input->post('rate'),
                'symbol' => $this->input->post('symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            );
        } elseif ($this->input->post('add_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/currencies");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrency($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang("currency_added"));
            admin_redirect("system_settings/currencies");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['page_title'] = lang("new_currency");
            $this->load->view($this->theme . 'settings/add_currency', $this->data);
        }
    }

    function edit_currency($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("currency_code"), 'trim|required');
        $cur_details = $this->settings_model->getCurrencyByID($id);
        if ($this->input->post('code') != $cur_details->code) {
            $this->form_validation->set_rules('code', lang("currency_code"), 'required|is_unique[currencies.code]');
        }
        $this->form_validation->set_rules('name', lang("currency_name"), 'required');
        $this->form_validation->set_rules('rate', lang("exchange_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'rate' => $this->input->post('rate'),
                'symbol' => $this->input->post('symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            );
        } elseif ($this->input->post('edit_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/currencies");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCurrency($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("currency_updated"));
            admin_redirect("system_settings/currencies");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['currency'] = $this->settings_model->getCurrencyByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_currency', $this->data);
        }
    }

    function delete_currency($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->deleteCurrency($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("currency_deleted")));
        }
    }

    function currency_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCurrency($id);
                    }
                    $this->session->set_flashdata('message', lang("currencies_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('currencies'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('rate'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCurrencyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->rate);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'currencies_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function categories()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('categories')));
        $meta = array('page_title' => lang('categories'), 'bc' => $bc);
        $this->page_construct('settings/categories', $meta, $this->data);
    }
	

    function getCategories()
    {
	$this->sma->checkPermissions('categories');
        $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="'.lang('print_barcodes').'" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('categories')}.id as id, {$this->db->dbprefix('categories')}.image, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name,  c.name as parent", FALSE)
            ->from("categories")
            ->join("categories c", 'c.id=categories.parent_id', 'left')
            ->group_by('categories.id')
            ->add_column("Actions", "<div class=\"text-center\">".$print_barcode." <a href='" . admin_url('system_settings/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_category") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_category") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }
	
    function add_category()
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
	$pid = $this->input->post('parent');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]|callback_is_unique_category['.$pid.']');
       // $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'khmer_name' => $this->input->post('khmer_name'),
                //'slug' => $this->input->post('slug'),
                'parent_id' => $this->input->post('parent'),
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    //$this->session->set_flashdata('error', $error);
                    //redirect($_SERVER["HTTP_REFERER"]);
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    //echo $this->image_lib->display_errors();
                    $error = $this->image_lib->display_errors();
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } elseif ($this->input->post('add_category')) {
            $error = validation_errors();
            $response['error'] = $error;
            echo json_encode($response);exit;
            //$this->session->set_flashdata('error', validation_errors());
            //admin_redirect("system_settings/categories");
        }

        if ($this->form_validation->run() == true && $sid = $this->settings_model->addCategory($data)) {
            /*$this->session->set_flashdata('message', lang("category_added"));
	    $ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            admin_redirect($ref[0] . '?category=' . $sid);*/
            
            $data['id'] = $sid;
            $response['category'] = $data;
            echo json_encode($response);exit;
            //admin_redirect("system_settings/categories");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_category', $this->data);

        }
    }
	
    function edit_category($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
	$pid = $this->input->post('parent');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|required');
        $pr_details = $this->settings_model->getCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("category_code"), 'required|is_unique[categories.code]');
        }
       /* $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
        if ($this->input->post('slug') != $pr_details->slug) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash|is_unique[categories.slug]');
        }*/
        $this->form_validation->set_rules('name', lang("category_name"), 'required|min_length[3]|callback_is_unique_category['.$pid.'.'.$id.']');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'khmer_name' => $this->input->post('khmer_name'),
               // 'slug' => $this->input->post('slug'),
                'parent_id' => $this->input->post('parent'),
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } elseif ($this->input->post('edit_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/categories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCategory($id, $data)) {
            $this->session->set_flashdata('message', lang("category_updated"));
            admin_redirect("system_settings/categories");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $this->settings_model->getCategoryByID($id);
            $this->data['categories'] = $this->settings_model->getParentCategories();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_category', $this->data);

        }
    }
	
    function delete_category($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->site->getSubCategories($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("category_has_subcategory")));
        }

        if ($this->settings_model->deleteCategory($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("category_deleted")));
        }
    }
	
    function category_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang("categories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('parent_actegory'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $parent_actegory = '';
                        if ($sc->parent_id) {
                            $pc = $this->settings_model->getCategoryByID($sc->parent_id);
                            $parent_actegory = $pc->code;
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->image);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $parent_actegory);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function recipecategories()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Recipe Groups')));
        $meta = array('page_title' => lang('Recipe Groups'), 'bc' => $bc);
        $this->page_construct('settings/recipecategories', $meta, $this->data);
    }
	
	function getrecipeCategories()
    {
	$this->sma->checkPermissions('recipecategories');
        $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="'.lang('print_barcodes').'" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('recipe_categories')}.id as id, {$this->db->dbprefix('recipe_categories')}.image, {$this->db->dbprefix('recipe_categories')}.code, {$this->db->dbprefix('recipe_categories')}.name,  c.name as parent", FALSE)
            ->from("recipe_categories")
            ->join("recipe_categories c", 'c.id=recipe_categories.parent_id', 'left')
            ->group_by('recipe_categories.id')
             ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_recipecategory/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_Recipe_Groups") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Recipe_Groups") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_recipecategory/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
            /*->add_column("Actions", "<div class=\"text-center\">".$print_barcode." <a href='" . admin_url('system_settings/edit_recipecategory/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_Recipe_Groups") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_Recipe_Groups") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_recipecategory/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");*/

        echo $this->datatables->generate();
    }
	
	function add_recipecategory()
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
	$pid = $this->input->post('parent');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[recipe_categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]|callback_is_unique_recipeCategories['.$pid.']');
       // $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'khmer_name' => $this->input->post('khmer_name'),
                //'slug' => $this->input->post('slug'),
                'parent_id' => $this->input->post('parent'),
				'kitchens_id' => $this->input->post('kitchens_id') ? $this->input->post('kitchens_id') : 0,
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                    //$this->session->set_flashdata('error', $error);
                    //redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    //echo $this->image_lib->display_errors();
                    $error = $this->image_lib->display_errors();
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } elseif ($this->input->post('add_recipecategory')) {
            $error = validation_errors();
            $response['error'] = $error;
            echo json_encode($response);exit;
            //$this->session->set_flashdata('error', validation_errors());
            //admin_redirect("system_settings/recipecategories");
        }

        if ($this->form_validation->run() == true && $sid = $this->settings_model->addrecipeCategory($data)) {
            //$this->session->set_flashdata('message', lang("recipe_category_added"));
            //admin_redirect("system_settings/recipecategories");
            $data['id'] = $sid;
            $response['category'] = $data;
            echo json_encode($response);exit;
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['categories'] = $this->settings_model->getParentrecipeCategories();
			$this->data['reskitchen'] = $this->site->getAllResKitchen();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_recipecategory', $this->data);

        }
    }
	
	function edit_recipecategory($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->load->helper('security');
	$pid = $this->input->post('parent');
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|required');
        $pr_details = $this->settings_model->getrecipeCategoryByID($id);
		
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("category_code"), 'required|is_unique[categories.code]');
        }
       /* $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
        if ($this->input->post('slug') != $pr_details->slug) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash|is_unique[categories.slug]');
        }*/
        $this->form_validation->set_rules('name', lang("category_name"), 'required|min_length[3]|callback_is_unique_recipeCategories['.$pid.'.'.$id.']');
        $this->form_validation->set_rules('userfile', lang("category_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
				'khmer_name' => $this->input->post('khmer_name'),
               // 'slug' => $this->input->post('slug'),
                'parent_id' => $this->input->post('parent'),
				'kitchens_id' => $this->input->post('kitchens_id') ? $this->input->post('kitchens_id') :0,
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

        } elseif ($this->input->post('edit_recipecategory')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/recipecategories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updaterecipeCategory($id, $data)) {
            $this->session->set_flashdata('message', lang("recipe_category_updated"));
            admin_redirect("system_settings/recipecategories");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $this->settings_model->getrecipeCategoryByID($id);
            $this->data['categories'] = $this->settings_model->getParentrecipeCategories();
			$this->data['reskitchen'] = $this->site->getAllResKitchen();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_recipecategory', $this->data);

        }
    }
	
	function delete_recipecategory($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->site->getrecipeSubCategories($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("recipe_category_has_subcategory")));
        }

        if ($this->settings_model->deleterecipeCategory($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("recipe_category_deleted")));
        }
    }
	
	function recipecategory_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleterecipeCategory($id);
                    }
                    $this->session->set_flashdata('message', lang("recipe_categories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('parent_actegory'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getrecipeCategoryByID($id);
                        $parent_actegory = '';
                        if ($sc->parent_id) {
                            $pc = $this->settings_model->getrecipeCategoryByID($sc->parent_id);
                            $parent_actegory = $pc->code;
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->image);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $parent_actegory);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function tax_rates()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('tax_rates')));
        $meta = array('page_title' => lang('tax_rates'), 'bc' => $bc);
        $this->page_construct('settings/tax_rates', $meta, $this->data);
    }
	
	
    function getTaxRates()
    {
	$this->sma->checkPermissions('tax_rates');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, name, code, rate, type")
            ->from("tax_rates")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_tax_rate/$1') . "' class='tip' title='" . lang("edit_tax_rate") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_tax_rate") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_tax_rate/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
	function add_tax_rate()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[tax_rates.name]|required');
        $this->form_validation->set_rules('type', lang("type"), 'required');
        $this->form_validation->set_rules('rate', lang("tax_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'rate' => $this->input->post('rate'),
            );
        } elseif ($this->input->post('add_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/tax_rates");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTaxRate($data)) {
            $this->session->set_flashdata('message', lang("tax_rate_added"));
            admin_redirect("system_settings/tax_rates");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_tax_rate', $this->data);
        }
    }
	
	function edit_tax_rate($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $tax_details = $this->settings_model->getTaxRateByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('type', lang("type"), 'required');
        $this->form_validation->set_rules('rate', lang("tax_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'rate' => $this->input->post('rate'),
            );
        } elseif ($this->input->post('edit_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/tax_rates");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateTaxRate($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("tax_rate_updated"));
            admin_redirect("system_settings/tax_rates");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_tax_rate', $this->data);
        }
    }
	
	 function delete_tax_rate($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->deleteTaxRate($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("tax_rate_deleted")));
        }
    }
	
	function discounts()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('discounts')));
        $meta = array('page_title' => lang('Discounts'), 'bc' => $bc);
        $this->page_construct('settings/discounts', $meta, $this->data);
    }

	
	
	function getDiscounts()
    {
	$this->sma->checkPermissions('discounts');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('discounts')}.id as id, {$this->db->dbprefix('discounts')}.name, {$this->db->dbprefix('discounts')}.type, {$this->db->dbprefix('discounts')}.discount_type, {$this->db->dbprefix('discounts')}.discount,CONCAT(DATE(DC.from_date),' - ',DATE(DC.to_date)),CONCAT(DC2.from_time,' - ',DC2.to_time),DC1.days,unique_discount,{$this->db->dbprefix('discounts')}.discount_status as discount_status ")
            ->from("discounts")
	    ->join('discount_conditions as DC', 'discounts.id = DC.discount_id AND DC.condition_method ="condition_date"', 'left')
	    ->join('discount_conditions DC1', 'discounts.id = DC1.discount_id AND DC1.condition_method ="condition_days"', 'left')
	    ->join('discount_conditions DC2', 'discounts.id = DC2.discount_id AND DC2.condition_method ="condition_time"', 'left')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_discount/$1') . "' >" . lang("edit")."</a> <a href='#' class='tip po' title='<b>" . lang("delete_discount") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_discount/$1') . "'>" . lang('i_m_sure') . "</a>  <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div> ", "id")
	    ->edit_column('discount_status', '$1__$2', 'discount_status, id');
            /*->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_discount/$1') . "' class='tip' title='" . lang("edit_discount") . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_discount") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_discount/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");*/
        //->unset_column('id');
//print_R($this->datatables);exit;
        echo $this->datatables->generate();
    }
    
    function add_discount()
    {
	$this->sma->checkPermissions('discounts');
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[discounts.name]|required');
        $this->form_validation->set_rules('description', lang("description"), 'required');
        $this->form_validation->set_rules('discount_method', lang("discount_method"), 'required');
	$error = false;	
		
        if ($this->form_validation->run() == true) {
   
			if($this->input->post('discount_method') == 'discount_simple'){
				$buy_quantity = '';
				$get_quantity = '';
				$amount = 0;
			}elseif($this->input->post('discount_method') == 'discount_on_total'){
				$buy_quantity = '';
				$get_quantity = '';
				$amount = $this->input->post('amount');
			}elseif($this->input->post('discount_method') == 'discount_buy_x_get_x' || $this->input->post('discount_method') == 'discount_buy_x_get_y'){
				$buy_quantity = $this->input->post('buy_quantity');
				$get_quantity = $this->input->post('get_quantity');
				$amount = 0;
			}
			
            $discount_array = array('name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
		'discount_status' => 1,
                'type' => $this->input->post('discount_method'),
		'unique_discount' => (isset($_POST['unique_discount']))?$_POST['unique_discount']:0,
                'buy_quantity' => $buy_quantity,
				'get_quantity' => $get_quantity,
				'amount' => $amount,
				'discount_type' => $this->input->post('discount_type'),
				'discount' => $this->input->post('discount'),
                'created' =>date('Y-m-d-H-i-s'),
            );
			foreach($this->input->post('item') as $item){
				if($this->input->post('discount_method') == 'discount_simple' || $this->input->post('discount_method') == 'discount_on_total' || $this->input->post('discount_method') == 'discount_buy_x_get_x'){
                    
					$item_method = $item['item_method'];
					$item_type = $item['item_type'];
					
					if($item['item_method'] == 'item_product'){

                        $item_type_id =  implode(',',$item['recipe_item']);
                        $item_type_id1[] =  implode(',',$item['recipe_item']);

					}elseif($item['item_method'] == 'item_category'){
                        $item_type_id =  implode(',',$item['recipe_category_item']);
						$item_type_id1[] =  implode(',',$item['recipe_category_item']);
					}
					
					$item_array[] = array(
						'item_method' => $item_method,
                        'item_type' => $item_type,
						'item_type_id' => $item_type_id,
                        'created' =>date("Y-m-d-H-i-s"),
					);
                        									
				}elseif($this->input->post('discount_method') == 'discount_buy_x_get_y'){
                    
					$item_method = $item['item_methodx'];
					$item_type = $item['item_typex'];
					$recipe_item_getx = implode(',',$item['recipe_item_getx']);
					
					if($item['item_methodx'] == 'item_product'){
                        $item_type_id = implode(',',$item['recipe_itemx']);
						$item_type_id1[] = implode(',',$item['recipe_itemx']);
					}elseif($item['item_methodx'] == 'item_category'){
                        $item_type_id = implode(',',$item['recipe_category_itemx']);
						$item_type_id1[] = implode(',',$item['recipe_category_itemx']);
					}
					$item_array[] = array(
						'item_method' => $item_method,
						'item_type' => $item_type,
						'item_type_id' => $item_type_id,
						'item_get_id' => $recipe_item_getx,
                        'created' =>date("Y-m-d-H-i-s"),
					);
				}
			}
			$d1 ='';$d2='';$days ='';$from_time='';$to_time='';
			foreach($this->input->post('condition') as $condition){
				$condition_method = $condition['condition_method'];
				/*$condition_type = $condition['condition_type'];*/
				if($condition['condition_method'] == 'condition_date' || $condition['condition_method'] == 'condition_time'){
					if($condition['condition_method'] == 'condition_date' ){
						/*$from_date = $condition['from_date'];*/
                        $from_date = date('Y-m-d H:i:s', strtotime($condition['from_date']));
                        $to_date = date('Y-m-d H:i:s', strtotime($condition['to_date']));  
						/*$to_date = $condition['to_date'];*/
						
						$d1 = $from_date;
						$d2 = $to_date;
                        $from_time = '';
                        $to_time = '';
					}elseif($condition['condition_method'] == 'condition_time' ){
						$from_time = $condition['from_time'];
						$to_time = $condition['to_time'];
                        $from_date = '';
                        $to_date = '';
					}
					$value = '';
				}elseif($condition['condition_method'] == 'condition_days'){
					$from_date = '';
                    $to_date = '';
                    $from_time = '';
                    $to_time = '';
					$value = implode(',',$condition['condition_days']);
					$days = $value;
				}
				$condition_array[] = array(
					'condition_method' => $condition_method,
					/*'condition_type' => $condition_type,*/
					'from_date' => $from_date,
                    'to_date' => $to_date,
                    'from_time' => $from_time,
					'to_time' => $to_time,
					'days' => $value,
				);
			}
	    if(isset($d1) && isset($d2) && isset($days) && isset($from_time) && isset($to_time) && isset($_POST['unique_discount'])){
		//exit;
		$days_array = explode(',',$days);
		$error_days = '';		
		    if($days!=''){ //echo 66;exit;
			foreach($days_array as $k => $day){
			    $existedDate = $this->settings_model->UniqueDisExist($d1,$d2,$day,$from_time,$to_time);
			    if($existedDate){
				$from1 = date('Y-m-d',strtotime($existedDate->from_date));
				$to1 = date('Y-m-d',strtotime($existedDate->to_date));
				$error_days .= 'Unique Discount Exist for '.$day.' between '.$from1.' -- '.$to1;
				if($existedDate->from_time!='00:00:00'){
				 $error_days .='Time between '.$existedDate->from_time.' - '.$existedDate->to_time;
				}
				$error_days .= '<br>';
			    }
			}
		    }else{
			$existedDate = $this->settings_model->UniqueDisExist($d1,$d2,$day='',$from_time,$to_time);
			if($existedDate){
			    $from1 = date('Y-m-d',strtotime($existedDate->from_date));
			    $to1 = date('Y-m-d',strtotime($existedDate->to_date));
			    $error_days .= 'Unique Discount Exist for the days between '.$from1.' -- '.$to1;
			    if($existedDate->from_time!='00:00:00'){
				 $error_days .='Time between '.$existedDate->from_time.' - '.$existedDate->to_time;
			    }
			    $error_days .= '<br>';
			}
		    }
		   
		
		$error_days = trim($error_days,',');
		
		if($error_days!=''){
		    
		    $error = $error_days;
		    $this->session->set_flashdata('error', $error);
		    admin_redirect("system_settings/discounts");
		}
		
	    }	
        } elseif ($this->input->post('add_discount')) {
	    $error = validation_errors();
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/discounts");
        }
		//echo '<pre>';print_R($discount_array);print_R($item_array);print_R($item_type_id1);exit;
        if (!$error && $this->form_validation->run() == true && $this->settings_model->addDiscount($discount_array, $item_array, $item_type_id1, $condition_array)) {
            $this->session->set_flashdata('message', lang("discount_added"));
            admin_redirect("system_settings/discounts");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['recipe'] = $this->site->getAllRecipes();
			$this->data['recipe_category'] = $this->site->getAllrecipeCategories();
			
            //$this->data['modal_js'] = $this->site->modal_js();
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')),array('link' => admin_url('system_settings/discounts'), 'page' => lang('discounts')), array('link' => '#', 'page' => lang('add_discounts')));
        	$meta = array('page_title' => lang('Add Discount'), 'bc' => $bc);
		
		
			$this->page_construct('settings/add_discount', $meta, $this->data);
            //$this->load->view($this->theme . 'settings/add_discount', $this->data);
        }
    }
	
	
	function edit_discount($id = NULL)
	{
	    $this->sma->checkPermissions('discounts');
	    $this->form_validation->set_rules('name', lang("name"), 'trim|required');
	    $discount_details = $this->settings_model->getDiscountByID($id); 
	    if ($this->input->post('name') != $discount_details->name) {
		$this->form_validation->set_rules('name', lang("name"), 'required|is_unique[discounts.name]');
	    }
		
        $this->form_validation->set_rules('description', lang("description"), 'required');
        $this->form_validation->set_rules('discount_method', lang("discount_method"), 'required');

        if ($this->form_validation->run() == true) {
	    //echo '<pre>';print_R($_POST);exit;
			
			if($this->input->post('discount_method') == 'discount_simple'){
				$buy_quantity = '';
				$get_quantity = '';
				$amount = 0;
			}elseif($this->input->post('discount_method') == 'discount_on_total'){
				$buy_quantity = '';
				$get_quantity = '';
				$amount = $this->input->post('amount');
			}elseif($this->input->post('discount_method') == 'discount_buy_x_get_x' || $this->input->post('discount_method') == 'discount_buy_x_get_y'){
				$buy_quantity = $this->input->post('buy_quantity');
				$get_quantity = $this->input->post('get_quantity');
				$amount = 0;
			}
			//echo '<pre>';
            $discount_array = array('name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'type' => $this->input->post('discount_method'),
		'unique_discount' => (isset($_POST['unique_discount']))?$_POST['unique_discount']:0,
                'buy_quantity' => $buy_quantity,
				'get_quantity' => $get_quantity,
				'amount' => $amount,
				'discount_type' => $this->input->post('discount_type'),
				'discount' => $this->input->post('discount'),
                //'created' =>date('Y-m-d-H-i-s'),
            );//print_R($discount_array);exit;
			foreach($this->input->post('item') as $item){
				if($this->input->post('discount_method') == 'discount_simple' || $this->input->post('discount_method') == 'discount_on_total' || $this->input->post('discount_method') == 'discount_buy_x_get_x'){
                    
					$item_method = $item['item_method'];
					$item_type = $item['item_type'];
					
					if($item['item_method'] == 'item_product'){

                        $item_type_id =  implode(',',$item['recipe_item']);
                        $item_type_id1[] =  implode(',',$item['recipe_item']);

					}elseif($item['item_method'] == 'item_category'){
                        $item_type_id =  implode(',',$item['recipe_category_item']);
						$item_type_id1[] =  implode(',',$item['recipe_category_item']);
					}
					
					$item_array[] = array(
						'item_method' => $item_method,
                        'item_type' => $item_type,
						'item_type_id' => $item_type_id,
                        'created' =>date("Y-m-d-H-i-s"),
					);
                        									
				}elseif($this->input->post('discount_method') == 'discount_buy_x_get_y'){
                    
					$item_method = $item['item_methodx'];
					$item_type = $item['item_typex'];
					$recipe_item_getx = implode(',',$item['recipe_item_getx']);
					
					if($item['item_methodx'] == 'item_product'){
                        $item_type_id = implode(',',$item['recipe_itemx']);
						$item_type_id1[] = implode(',',$item['recipe_itemx']);
					}elseif($item['item_methodx'] == 'item_category'){
                        $item_type_id = implode(',',$item['recipe_category_itemx']);
						$item_type_id1[] = implode(',',$item['recipe_category_itemx']);
					}
					$item_array[] = array(
						'item_method' => $item_method,
						'item_type' => $item_type,
						'item_type_id' => $item_type_id,
						'item_get_id' => $recipe_item_getx,
                        'created' =>date("Y-m-d-H-i-s"),
					);
				}
			}
			$d1='';$d2='';$day='';$from_time='';$to_time='';
			foreach($this->input->post('condition') as $condition){
				$condition_method = $condition['condition_method'];
				/*$condition_type = $condition['condition_type'];*/
				if($condition['condition_method'] == 'condition_date' || $condition['condition_method'] == 'condition_time'){
					if($condition['condition_method'] == 'condition_date' ){
						/*$from_date = $condition['from_date'];*/
                        $from_date = date('Y-m-d H:i:s', strtotime($condition['from_date']));
                        $to_date = date('Y-m-d H:i:s', strtotime($condition['to_date']));  
						/*$to_date = $condition['to_date'];*/
						
						$d1 = $from_date;
						$d2 = $to_date;
                        $from_time = '';
                        $to_time = '';
					}elseif($condition['condition_method'] == 'condition_time' ){
						$from_time = $condition['from_time'];
						$to_time = $condition['to_time'];
                        $from_date = '';
                        $to_date = '';
					}
					$value = '';
				}elseif($condition['condition_method'] == 'condition_days'){
					$from_date = '';
                    $to_date = '';
                    $from_time = '';
                    $to_time = '';
					$value = implode(',',$condition['condition_days']);
					$days = $value;
				}
				$condition_array[] = array(
					'condition_method' => $condition_method,
					/*'condition_type' => $condition_type,*/
					'from_date' => $from_date,
                    'to_date' => $to_date,
                    'from_time' => $from_time,
					'to_time' => $to_time,
					'days' => $value,
				);
			}
	    if(isset($d1) && isset($d2) && isset($days) && isset($_POST['unique_discount'])){
		//exit;
		$days_array = explode(',',$days);
		$error_days = '';		
		    if($days!=''){ //echo 66;exit;
			foreach($days_array as $k => $day){
			    $existedDate = $this->settings_model->UniqueDisExist($d1,$d2,$day,$from_time,$to_time,$id);
			    if($existedDate){
				$from1 = date('Y-m-d',strtotime($existedDate->from_date));
				$to1 = date('Y-m-d',strtotime($existedDate->to_date));
				$error_days .= 'Unique Discount Exist for '.$day.' between '.$from1.' -- '.$to1;
				if($existedDate->from_time!='00:00:00'){
				 $error_days .='Time between '.$existedDate->from_time.' - '.$existedDate->to_time;
				}
				$error_days .= '<br>';
			    }
			}
		    }else{
			$existedDate = $this->settings_model->UniqueDisExist($d1,$d2,$day='',$from_time,$to_time,$id);
			if($existedDate){
			    $from1 = date('Y-m-d',strtotime($existedDate->from_date));
			    $to1 = date('Y-m-d',strtotime($existedDate->to_date));
			    $error_days .= 'Unique Discount Exist for the days between '.$from1.' -- '.$to1;
			    if($existedDate->from_time!='00:00:00'){
				 $error_days .='Time between '.$existedDate->from_time.' - '.$existedDate->to_time;
			    }
			    $error_days .= '<br>';
			}
		    }
		
		$error_days = trim($error_days,',');
		
		if($error_days!=''){
		    
		    $error = $error_days;
		    $this->session->set_flashdata('error', $error);
		   // admin_redirect("system_settings/discounts");
		}
		
	    }		
        } elseif ($this->input->post('edit_discount')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/edit_discount/".$id);
        }
//echo '<pre>';print_R($discount_array);print_R($item_array);print_R($condition_array);exit;
        if ($this->form_validation->run() == true && $this->settings_model->editDiscount($discount_array, $item_array, $condition_array, $id)) {

            $this->session->set_flashdata('message', lang("discount_updated"));
            admin_redirect("system_settings/edit_discount/".$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['recipe'] = $this->site->getAllRecipes();
			$this->data['recipe_category'] = $this->site->getAllrecipeCategories();
			//echo '<pre>';print_R($this->data['recipe']);exit;
            //$this->data['modal_js'] = $this->site->modal_js();
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => admin_url('system_settings/discounts'), 'page' => lang('discounts')),array('link' => '#', 'page' => lang('discounts')));
        	$meta = array('page_title' => lang('Edit Discount'), 'bc' => $bc);
			$this->data['id'] = $id;
			$this->data['discount_data'] = $this->settings_model->getDiscountByID($id);
			//echo '<pre>';print_R($this->data['discount_data']);//exit;
			$this->page_construct('settings/edit_discount', $meta, $this->data);
            //$this->load->view($this->theme . 'settings/add_discount', $this->data);
			
        }
    }
	
	function delete_discount($id = NULL)
    {
	$this->sma->checkPermissions('discounts');
        if ($this->settings_model->deleteDiscount($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("discount_deleted")));
        }
    }

    
    /************************ customer- discount ***********************/
    
    function customer_discounts()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('customer_discount')));
        $meta = array('page_title' => lang('customer_discount'), 'bc' => $bc);
        $this->page_construct('settings/list_customer_discount', $meta, $this->data);
    }	

    function get_customer_discount()
    {
	$this->sma->checkPermissions('customer_discounts');
        $this->load->library('datatables');
        $this->datatables
            //->select("id, name, discount_type, value, created_dt")
	    ->select("'sno',id, name, created_dt,status")
            ->from("diccounts_for_customer")
            ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("edit_customer_discount") . "' href='" . admin_url('system_settings/edit_customer_discount/$1') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer_discount") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_customer_discount/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
	    ->edit_column('status', '$1__$2', 'status, id');
	echo $this->datatables->generate();
    }  
    function add_customer_discounts()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');
        //$this->form_validation->set_rules('discount_type', lang("discount_type"), 'required');
//$this->form_validation->set_rules('discount', lang("discount"), 'required');

        if ($this->form_validation->run() == true) {
//echo '<pre>';print_R($_POST);exit;

            $data = array(
                'name' => $this->input->post('name'),
                //'discount_type' => $this->input->post('discount_type'),
                //'value' => $this->input->post('discount'),
                'created_dt' =>  date("Y-m-d-H-i-s"),
            );


        } elseif ($this->input->post('add_customer_discounts')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customer_discounts");
        }
	$data = $_POST;
        if ($this->form_validation->run() == true && $this->settings_model->addCustomerDiscount($data)) {
            $this->session->set_flashdata('message', lang("customer_discount_added"));
            admin_redirect("system_settings/customer_discounts");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	    $this->data['recipe_groups'] = $this->site->getAllrecipeCategories_items();
	    $this->data['recipe_groups_json'] = json_encode($this->data['recipe_groups']);
	    //print_R($this->data['recipe_groups']);exit;
            $this->data['modal_js'] = $this->site->modal_js();
            //$this->load->view($this->theme . 'settings/add_customer_discount', $this->data);
	    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings/customer_discounts'), 'page' => lang('customer_discounts')), array('link' => '#', 'page' => lang('customer_discounts')));
            $meta = array('page_title' => lang('customer_discount'), 'bc' => $bc);
            $this->page_construct('settings/add_customer_discount',$meta,$this->data);
        }
    }
    function edit_customer_discount($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'required');
        //$this->form_validation->set_rules('discount_type', lang("discount_type"), 'required');
        //$this->form_validation->set_rules('discount', lang("discount"), 'required');

        $customer_discounts = $this->settings_model->getCustomerDiscount($id);
        if ($this->form_validation->run() == true) {
//echo '<pre>';print_R($_POST);exit;
            $data = array(
                'name' => $this->input->post('name'),
                //'discount_type' => $this->input->post('discount_type'),
                'value' => $this->input->post('discount'),
                'created_dt' =>  date("Y-m-d-H-i-s"),
            );

        } elseif ($this->input->post('edit_customer_discount')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customer_discounts");
        }

	$data = $_POST;
        if ($this->form_validation->run() == true && $this->settings_model->updateCustomerDiscount($id, $data)) {
            $this->session->set_flashdata('message', lang("customer_discount_updated"));
            admin_redirect("system_settings/edit_customer_discount/".$id);
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['customer_discounts'] = $customer_discounts;
	    //print_R($customer_discounts);exit;
            $this->data['recipe_groups'] = $this->site->getAllrecipeCategories_items();
	    $this->data['recipe_groups_json'] = json_encode($this->data['recipe_groups']);
	    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings/customer_discounts'), 'page' => lang('customer_discounts')), array('link' => '#', 'page' => lang('customer_discounts')));
        $meta = array('page_title' => lang('customer_discount'), 'bc' => $bc);
            $this->page_construct('settings/edit_customer_discount',$meta,$this->data);

        }
    }
    function delete_customer_discount($id = NULL)
    {
	$this->sma->checkPermissions();
        /*if ($this->settings_model->getUnitChildren($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("unit_has_subunit")));
        }*/

        if ($this->settings_model->deleteCustomerDiscount($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("customer_discount_deleted")));
        }
    }
    function customer_discount_deactivate($id){
	$this->sma->checkPermissions('cus_dis_status');
	$this->settings_model->updateCusDiscount_status($id,0);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function customer_discount_activate($id){
	$this->sma->checkPermissions('cus_dis_status');
	$this->settings_model->updateCusDiscount_status($id,1);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    
    

    
    /*********************** customer discount end *********************/
    
    
    function buy_get()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Buy & Get')));
        $meta = array('page_title' => lang('Buy & Get'), 'bc' => $bc);
        $this->page_construct('settings/buy', $meta, $this->data);
    }
	
	
	function getBuy()
    {
	$this->sma->checkPermissions('buy_get');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, name, buy_method, start_date, end_date, start_time, end_time, status")
            ->from("buy_get")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_buy/$1') . "' class='tip' title='" . lang("edit_buy & get") . "' ><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_buy & get") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_buy/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
	function add_buy()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[buy_get.name]|required');
        $this->form_validation->set_rules('buy_method', lang("buy_method"), 'required');
		$this->form_validation->set_rules('start_date', lang("start_date"), 'required');
		$this->form_validation->set_rules('end_date', lang("end_date"), 'required');
		$this->form_validation->set_rules('start_time', lang("start_time"), 'required');
		$this->form_validation->set_rules('end_time', lang("end_time"), 'required');
		$this->form_validation->set_rules('buy_quantity', lang("buy_quantity"), 'required');
		$this->form_validation->set_rules('get_quantity', lang("get_quantity"), 'required');

			
        if ($this->form_validation->run() == true) {

						
            $buy_array = array('name' => $this->input->post('name'),
                'buy_method' => $this->input->post('buy_method'),
                'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
				'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
				'start_time' => $this->input->post('start_time'),
				'end_time' => $this->input->post('end_time'),
				'buy_quantity' => $this->input->post('buy_quantity'),
				'get_quantity' => $this->input->post('get_quantity'),
				'status' => 'Open',
                'created_on' =>date('Y-m-d-H-i-s'),
            );
			
			for($i=0; $i<count($this->input->post('buy_type')); $i++){
				$item_array[] = array(
					'buy_type' => $_POST['buy_type'][$i],
					'buy_item' => $_POST['buy_item'][$i],
					'get_item' => $_POST['get_item'][$i],
				);
			}
			
           // $this->sma->print_arrays($buy_array, $item_array);
			//die;
        } elseif ($this->input->post('add_buy')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/buy_get");
        }
		
        if ($this->form_validation->run() == true && $this->settings_model->addBuy($buy_array, $item_array)) {
            $this->session->set_flashdata('message', lang("buy & get_added"));
            admin_redirect("system_settings/buy_get");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['recipe'] = $this->site->getAllRecipes();
			$this->data['recipe_category'] = $this->site->getAllrecipeCategories();
			

			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Buy & get')));
        	$meta = array('page_title' => lang('Add Buy & get'), 'bc' => $bc);
			$this->page_construct('settings/add_buy', $meta, $this->data);
        }
    }
    
	
	function edit_buy($id = NULL)
	{
	    $this->sma->checkPermissions();
		$this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $buy_details = $this->settings_model->getBuyByID($id); 
		
        if ($this->input->post('name') != $buy_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[buy_get.name]');
        }
		
        $this->form_validation->set_rules('buy_method', lang("buy_method"), 'required');
		$this->form_validation->set_rules('start_date', lang("start_date"), 'required');
		$this->form_validation->set_rules('end_date', lang("end_date"), 'required');
		$this->form_validation->set_rules('start_time', lang("start_time"), 'required');
		$this->form_validation->set_rules('end_time', lang("end_time"), 'required');
		$this->form_validation->set_rules('buy_quantity', lang("buy_quantity"), 'required');
		$this->form_validation->set_rules('get_quantity', lang("get_quantity"), 'required');

        if ($this->form_validation->run() == true) {
			
						
            $buy_array = array('name' => $this->input->post('name'),
                'buy_method' => $this->input->post('buy_method'),
                'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
				'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
				'start_time' => $this->input->post('start_time'),
				'end_time' => $this->input->post('end_time'),
				'buy_quantity' => $this->input->post('buy_quantity'),
				'get_quantity' => $this->input->post('get_quantity'),
                'created_on' =>date('Y-m-d-H-i-s'),
            );
			for($i=0; $i<count($this->input->post('buy_type')); $i++){
				$item_array[] = array(
					'buy_type' => $_POST['buy_type'][$i],
					'buy_item' => $_POST['buy_item'][$i],
					'get_item' => $_POST['get_item'][$i],
				);
			}
			
           
        } elseif ($this->input->post('edit_buy')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/buy_get");
        }

        if ($this->form_validation->run() == true && $this->settings_model->editDiscount($buy_array, $item_array, $id)) {

            $this->session->set_flashdata('message', lang("discount_edited"));
            admin_redirect("system_settings/buy_get");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			
			$this->data['recipe'] = $this->site->getAllRecipes();
			$this->data['recipe_category'] = $this->site->getAllrecipeCategories();
			
            //$this->data['modal_js'] = $this->site->modal_js();
			$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Buy & Get')));
        	$meta = array('page_title' => lang('Edit Buy & Get'), 'bc' => $bc);
			$this->data['id'] = $id;
			$this->data['buy'] = $buy_details;
			$this->data['item'] = $this->settings_model->getBuyItems($id);
			
			$this->page_construct('settings/edit_buy', $meta, $this->data);
            //$this->load->view($this->theme . 'settings/add_discount', $this->data);
			
        }
    }

   
	
	function delete_buy($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->deleteDiscount($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("discount_deleted")));
        }
    }

    function tax_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTaxRate($id);
                    }
                    $this->session->set_flashdata('message', lang("tax_rates_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tax = $this->settings_model->getTaxRateByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $tax->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tax->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tax->rate);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($tax->type == 1) ? lang('percentage') : lang('fixed'));
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_rates_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	function discount_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTaxRate($id);
                    }
                    $this->session->set_flashdata('message', lang("tax_rates_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tax = $this->settings_model->getTaxRateByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $tax->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tax->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tax->rate);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($tax->type == 1) ? lang('percentage') : lang('fixed'));
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'tax_rates_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function customer_groups()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('customer_groups')));
        $meta = array('page_title' => lang('customer_groups'), 'bc' => $bc);
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }

    function getCustomerGroups()
    {
	$this->sma->checkPermissions('customer_groups');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, name, code, loayltypoints, discount_type, percent")
            ->from("customer_groups")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_customer_group/$1') . "' class='tip' title='" . lang("edit_customer_group") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_customer_group") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_customer_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_customer_group()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("group_name"), 'trim|is_unique[customer_groups.name]|required');

        $this->form_validation->set_rules('code', lang("group_code"), 'trim|is_unique[customer_groups.code]|required');

        $this->form_validation->set_rules('percent', lang("group_percentage"), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array(
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'loayltypoints' => $this->input->post('loayltypoints'),
				'discount_type' => $this->input->post('discount_type'),
                'percent' => $this->input->post('percent'),
            );
        } elseif ($this->input->post('add_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customer_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCustomerGroup($data)) {
            $this->session->set_flashdata('message', lang("customer_group_added"));
            admin_redirect("system_settings/customer_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_customer_group', $this->data);
        }
    }

    function edit_customer_group($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("group_name"), 'trim|required');

        $pg_details = $this->settings_model->getCustomerGroupByID($id);
        
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang("group_name"), 'required|is_unique[customer_groups.name]');
        }
        if ($this->input->post('code') != $pg_details->code) {
            $this->form_validation->set_rules('code', lang("group_code"), 'required|is_unique[customer_groups.code]');
        }
        $this->form_validation->set_rules('percent', lang("group_percentage"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'loayltypoints' => $this->input->post('loayltypoints'),
				'discount_type' => $this->input->post('discount_type'),
                'percent' => $this->input->post('percent'),
            );
        } elseif ($this->input->post('edit_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customer_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCustomerGroup($id, $data)) {
            $this->session->set_flashdata('message', lang("customer_group_updated"));
            admin_redirect("system_settings/customer_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getCustomerGroupByID($id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_customer_group', $this->data);
        }
    }

    function delete_customer_group($id = NULL)
    {	$this->sma->checkPermissions();
        if ($this->settings_model->deleteCustomerGroup($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("customer_group_deleted")));
        }
    }

    function customer_group_actions()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCustomerGroup($id);
                    }
                    $this->session->set_flashdata('message', lang("customer_groups_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('group_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('group_percentage'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $pg = $this->settings_model->getCustomerGroupByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pg->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pg->percent);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'customer_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_customer_group_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function warehouses()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('warehouses')));
        $meta = array('page_title' => lang('warehouses'), 'bc' => $bc);
        $this->page_construct('settings/warehouses', $meta, $this->data);
    }

    function getWarehouses()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('warehouses')}.id as id, map, code, {$this->db->dbprefix('warehouses')}.name as name,  phone, email, address")
            ->from("warehouses")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_warehouse/$1') . "' class='tip' title='" . lang("edit_warehouse") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_warehouse") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_warehouse/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_warehouse()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("code"), 'trim|is_unique[warehouses.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required');
        $this->form_validation->set_rules('address', lang("address"), 'required');
        $this->form_validation->set_rules('userfile', lang("map_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = '2000';
                $config['max_height'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect("system_settings/warehouses");
                }

                $map = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/' . $map;
                $config['new_image'] = 'assets/uploads/thumbs/' . $map;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 76;
                $config['height'] = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $map = NULL;
            }
            $data = array('code' => $this->input->post('code'),
		'type' => $this->input->post('type'),
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'parent_warehouses'=>(isset($_POST['parent_warehouses']))?implode(',',$_POST['parent_warehouses']):'',
                'same_store'=>(isset($_POST['same_store']))?1:0,
                'map' => $map,
            );
        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/warehouses");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang("warehouse_added"));
            admin_redirect("system_settings/warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_warehouse', $this->data);
        }
    }

    function edit_warehouse($id = NULL)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang("code"), 'trim|required');
        $wh_details = $this->settings_model->getWarehouseByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[warehouses.code]');
        }
        $this->form_validation->set_rules('address', lang("address"), 'required');
        $this->form_validation->set_rules('map', lang("map_image"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = array('code' => $this->input->post('code'),
			  'type' => $wh_details->type,
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'parent_warehouses'=>(isset($_POST['parent_warehouses']))?implode(',',$_POST['parent_warehouses']):'',
		'map' => $map,
            );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = '2000';
                $config['max_height'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect("system_settings/warehouses");
                }

                $data['map'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/' . $data['map'];
                $config['new_image'] = 'assets/uploads/thumbs/' . $data['map'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 76;
                $config['height'] = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        } elseif ($this->input->post('edit_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/warehouses");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWarehouse($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("warehouse_updated"));
            admin_redirect("system_settings/warehouses");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouse'] = $this->settings_model->getWarehouseByID($id);
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_warehouse', $this->data);
        }
    }

    function delete_warehouse($id = NULL)
    {
        if ($this->settings_model->deleteWarehouse($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("warehouse_deleted")));
        }
    }

    function warehouse_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteWarehouse($id);
                    }
                    $this->session->set_flashdata('message', lang("warehouses_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('warehouses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('city'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $wh = $this->settings_model->getWarehouseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->address);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->city);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'warehouses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_warehouse_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function variants()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('variants')));
        $meta = array('page_title' => lang('variants'), 'bc' => $bc);
        $this->page_construct('settings/variants', $meta, $this->data);
    }

    function getVariants()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, name")
            ->from("variants")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_variant/$1') . "' class='tip' title='" . lang("edit_variant") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_variant") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_variant/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_variant()
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[variants.name]|required');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('add_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/variants");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addVariant($data)) {
            $this->session->set_flashdata('message', lang("variant_added"));
            admin_redirect("system_settings/variants");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_variant', $this->data);
        }
    }

    function edit_variant($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $tax_details = $this->settings_model->getVariantByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[variants.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('edit_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/variants");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateVariant($id, $data)) {
            $this->session->set_flashdata('message', lang("variant_updated"));
            admin_redirect("system_settings/variants");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['variant'] = $tax_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_variant', $this->data);
        }
    }

    function delete_variant($id = NULL)
    {
        if ($this->settings_model->deleteVariant($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("variant_deleted")));
        }
    }
	
	function sales_type()
    {
$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('sales_type')));
        $meta = array('page_title' => lang('sales_type'), 'bc' => $bc);
        $this->page_construct('settings/sales_type', $meta, $this->data);
    }

    function getSales_type()
    {
$this->sma->checkPermissions('sales_type');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, name")
            ->from("sales_type")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_sales_type/$1') . "' class='tip' title='" . lang("edit_sales_type") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_sales_type") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_sales_type/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_sales_type()
    {
$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|is_unique[sales_type.name]|required');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('add_sales_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/sales_type");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addSales_type($data)) {
            $this->session->set_flashdata('message', lang("sales_type_added"));
            admin_redirect("system_settings/sales_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_sales_type', $this->data);
        }
    }

    function edit_sales_type($id = NULL)
    {
$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $sales_type_details = $this->settings_model->getSales_typeByID($id);
        if ($this->input->post('name') != $sales_type_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[sales_type.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('edit_sales_type')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/sales_type");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSales_type($id, $data)) {
            $this->session->set_flashdata('message', lang("sales_type_updated"));
            admin_redirect("system_settings/sales_type");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['sales_type'] = $sales_type_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_sales_type', $this->data);
        }
    }

    function delete_sales_type($id = NULL)
    {$this->sma->checkPermissions();
        if ($this->settings_model->deleteSales_type($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("sales_type_deleted")));
        }
    }

    function expense_categories()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('expense_categories')));
        $meta = array('page_title' => lang('categories'), 'bc' => $bc);
        $this->page_construct('settings/expense_categories', $meta, $this->data);
    }

    function getExpenseCategories()
    {
	$this->sma->checkPermissions('expense_categories');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, code, name")
            ->from("expense_categories")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_expense_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_expense_category") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_expense_category") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_expense_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_expense_category()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
            );

        } elseif ($this->input->post('add_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/expense_categories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategory($data)) {
            $this->session->set_flashdata('message', lang("expense_category_added"));
            admin_redirect("system_settings/expense_categories");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_expense_category', $this->data);
        }
    }

    function edit_expense_category($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("category_code"), 'trim|required');
        $category = $this->settings_model->getExpenseCategoryByID($id);
        if ($this->input->post('code') != $category->code) {
            $this->form_validation->set_rules('code', lang("category_code"), 'required|is_unique[expense_categories.code]');
        }
        $this->form_validation->set_rules('name', lang("category_name"), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {

            $data = array(
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name')
            );

        } elseif ($this->input->post('edit_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/expense_categories");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateExpenseCategory($id, $data, $photo)) {
            $this->session->set_flashdata('message', lang("expense_category_updated"));
            admin_redirect("system_settings/expense_categories");
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $category;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_expense_category', $this->data);
        }
    }

    function delete_expense_category($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->hasExpenseCategoryRecord($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("category_has_expenses")));
        }

        if ($this->settings_model->deleteExpenseCategory($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("expense_category_deleted")));
        }
    }

    function expense_category_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang("categories_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'expense_categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function import_categories()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("system_settings/categories");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen('files/' . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('code', 'name', 'image', 'pcode');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if ( ! $this->settings_model->getCategoryByCode(trim($csv_ct['code']))) {
                        $pcat = NULL;
                        $pcode = trim($csv_ct['pcode']);
                        if (!empty($pcode)) {
                            if ($pcategory = $this->settings_model->getCategoryByCode(trim($csv_ct['pcode']))) {
                                $data[] = array(
                                    'code' => trim($csv_ct['code']),
                                    'name' => trim($csv_ct['name']),
                                    'image' => trim($csv_ct['image']),
                                    'parent_id' => $pcategory->id,
                                    );
                            }
                        } else {
                            $data[] = array(
                                'code' => trim($csv_ct['code']),
                                'name' => trim($csv_ct['name']),
                                'image' => trim($csv_ct['image']),
                                );
                        }
                    }
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategories($data)) {
            $this->session->set_flashdata('message', lang("categories_added"));
            admin_redirect('system_settings/categories');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'settings/import_categories', $this->data);

        }
    }

    function import_subcategories()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("system_settings/categories");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen('files/' . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('code', 'name', 'category_code', 'image');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                $rw = 2;
                foreach ($final as $csv_ct) {
                    if ( ! $this->settings_model->getSubcategoryByCode(trim($csv_ct['code']))) {
                        if ($parent_actegory = $this->settings_model->getCategoryByCode(trim($csv_ct['category_code']))) {
                            $data[] = array(
                                'code' => trim($csv_ct['code']),
                                'name' => trim($csv_ct['name']),
                                'image' => trim($csv_ct['image']),
                                'category_id' => $parent_actegory->id,
                                );
                        } else {
                            $this->session->set_flashdata('error', lang("check_category_code") . " (" . $csv_ct['category_code'] . "). " . lang("category_code_x_exist") . " " . lang("line_no") . " " . $rw);
                            admin_redirect("system_settings/categories");
                        }
                    }
                    $rw++;
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addSubCategories($data)) {
            $this->session->set_flashdata('message', lang("subcategories_added"));
            admin_redirect('system_settings/categories');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'settings/import_subcategories', $this->data);

        }
    }

    function import_expense_categories()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("system_settings/expense_categories");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen('files/' . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('code', 'name');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if ( ! $this->settings_model->getExpenseCategoryByCode(trim($csv_ct['code']))) {
                        $data[] = array(
                            'code' => trim($csv_ct['code']),
                            'name' => trim($csv_ct['name']),
                            );
                    }
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategories($data)) {
            $this->session->set_flashdata('message', lang("categories_added"));
            admin_redirect('system_settings/expense_categories');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'settings/import_expense_categories', $this->data);

        }
    }

    function units()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('units')));
        $meta = array('page_title' => lang('units'), 'bc' => $bc);
        $this->page_construct('settings/units', $meta, $this->data);
    }

    function getUnits()
    {
	$this->sma->checkPermissions('units');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('units')}.id as id, {$this->db->dbprefix('units')}.code, {$this->db->dbprefix('units')}.name, b.name as base_unit, {$this->db->dbprefix('units')}.operator, {$this->db->dbprefix('units')}.operation_value", FALSE)
            ->from("units")
            ->join("units b", 'b.id=units.base_unit', 'left')
            ->group_by('units.id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_unit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_unit") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_unit") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_unit()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("unit_code"), 'trim|is_unique[units.code]|required');
        $this->form_validation->set_rules('name', lang("unit_name"), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang("operator"), 'required');
            $this->form_validation->set_rules('operation_value', lang("operation_value"), 'trim|required');
        }

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'base_unit' => $this->input->post('base_unit') ? $this->input->post('base_unit') : NULL,
                'operator' => $this->input->post('base_unit') ? $this->input->post('operator') : NULL,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : NULL,
                );

        } elseif ($this->input->post('add_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/units");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addUnit($data)) {
            $this->session->set_flashdata('message', lang("unit_added"));
            admin_redirect("system_settings/units");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_unit', $this->data);

        }
    }

    function edit_unit($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("code"), 'trim|required');
        $unit_details = $this->site->getUnitByID($id);
        if ($this->input->post('code') != $unit_details->code) {
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[units.code]');
        }
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang("operator"), 'required');
            $this->form_validation->set_rules('operation_value', lang("operation_value"), 'trim|required');
        }

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'base_unit' => $this->input->post('base_unit') ? $this->input->post('base_unit') : NULL,
                'operator' => $this->input->post('base_unit') ? $this->input->post('operator') : NULL,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : NULL,
                );

        } elseif ($this->input->post('edit_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/units");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateUnit($id, $data)) {
            $this->session->set_flashdata('message', lang("unit_updated"));
            admin_redirect("system_settings/units");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['unit'] = $unit_details;
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->load->view($this->theme . 'settings/edit_unit', $this->data);

        }
    }

    function delete_unit($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->getUnitChildren($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("unit_has_subunit")));
        }

        if ($this->settings_model->deleteUnit($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("unit_deleted")));
        }
    }

    function unit_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteUnit($id);
                    }
                    $this->session->set_flashdata('message', lang("units_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('base_unit'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('operator'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('operation_value'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $unit = $this->site->getUnitByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $unit->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $unit->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $unit->base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $unit->operator);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $unit->operation_value);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'units_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function price_groups()
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('price_groups')));
        $meta = array('page_title' => lang('price_groups'), 'bc' => $bc);
        $this->page_construct('settings/price_groups', $meta, $this->data);
    }

    function getPriceGroups()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select("id, name")
            ->from("price_groups")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/group_product_prices/$1') . "' class='tip' title='" . lang("group_product_prices") . "'><i class=\"fa fa-eye\"></i></a>  <a href='" . admin_url('system_settings/edit_price_group/$1') . "' class='tip' title='" . lang("edit_price_group") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_price_group") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_price_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_price_group()
    {

        $this->form_validation->set_rules('name', lang("group_name"), 'trim|is_unique[price_groups.name]|required|alpha_numeric_spaces');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('add_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/price_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addPriceGroup($data)) {
            $this->session->set_flashdata('message', lang("price_group_added"));
            admin_redirect("system_settings/price_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_price_group', $this->data);
        }
    }

    function edit_price_group($id = NULL)
    {

        $this->form_validation->set_rules('name', lang("group_name"), 'trim|required|alpha_numeric_spaces');
        $pg_details = $this->settings_model->getPriceGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang("group_name"), 'required|is_unique[price_groups.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'));
        } elseif ($this->input->post('edit_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/price_groups");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePriceGroup($id, $data)) {
            $this->session->set_flashdata('message', lang("price_group_updated"));
            admin_redirect("system_settings/price_groups");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['price_group'] = $pg_details;
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_price_group', $this->data);
        }
    }

    function delete_price_group($id = NULL)
    {
        if ($this->settings_model->deletePriceGroup($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("price_group_deleted")));
        }
    }

    function product_group_price_actions($group_id)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'update_price') {

                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->setProductPriceForPriceGroup($id, $group_id, $this->input->post('price'.$id));
                    }
                    $this->session->set_flashdata('message', lang("products_group_price_updated"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteProductGroupPrice($id, $group_id);
                    }
                    $this->session->set_flashdata('message', lang("products_group_price_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('group_name'));
                    $row = 2;
                    $group = $this->settings_model->getPriceGroupByID($group_id);
                    foreach ($_POST['val'] as $id) {
                        $pgp = $this->settings_model->getProductGroupPriceByPID($id, $group_id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pgp->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pgp->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pgp->price);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $group->name);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'price_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_price_group_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function group_product_prices($group_id = NULL)
    {

        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->data['price_group'] = $this->settings_model->getPriceGroupByID($group_id);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')),  array('link' => admin_url('system_settings/price_groups'), 'page' => lang('price_groups')), array('link' => '#', 'page' => lang('group_product_prices')));
        $meta = array('page_title' => lang('group_product_prices'), 'bc' => $bc);
        $this->page_construct('settings/group_product_prices', $meta, $this->data);
    }

    function getProductPrices($group_id = NULL)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $pp = "( SELECT {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price as price FROM {$this->db->dbprefix('product_prices')} WHERE price_group_id = {$group_id} ) PP";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as product_code, {$this->db->dbprefix('products')}.name as product_name, PP.price as price ")
            ->from("products")
            ->join($pp, 'PP.product_id=products.id', 'left')
            ->edit_column("price", "$1__$2", 'id, price')
            ->add_column("Actions", "<div class=\"text-center\"><button class=\"btn btn-primary btn-xs form-submit\" type=\"button\"><i class=\"fa fa-check\"></i></button></div>", "id");

        echo $this->datatables->generate();
    }

    function update_product_group_price($group_id = NULL)
    {
        if (!$group_id) {
            $this->sma->send_json(array('status' => 0));
        }

        $product_id = $this->input->post('product_id', TRUE);
        $price = $this->input->post('price', TRUE);
        if (!empty($product_id) && !empty($price)) {
            if ($this->settings_model->setProductPriceForPriceGroup($product_id, $group_id, $price)) {
                $this->sma->send_json(array('status' => 1));
            }
        }

        $this->sma->send_json(array('status' => 0));
    }

    function update_prices_csv($group_id = NULL)
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('message', lang("disabled_in_demo"));
                admin_redirect('welcome');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("system_settings/group_product_prices/".$group_id);
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen('files/' . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'price');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if ($product = $this->site->getProductByCode(trim($csv_pr['code']))) {
                    $data[] = array(
                        'product_id' => $product->id,
                        'price' => $csv_pr['price'],
                        'price_group_id' => $group_id
                        );
                    } else {
                        $this->session->set_flashdata('message', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_x_exist") . " " . lang("line_no") . " " . $rw);
                        admin_redirect("system_settings/group_product_prices/".$group_id);
                    }
                    $rw++;
                }
            }

        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/group_product_prices/".$group_id);
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            $this->settings_model->updateGroupPrices($data);
            $this->session->set_flashdata('message', lang("price_updated"));
            admin_redirect("system_settings/group_product_prices/".$group_id);
        } else {

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['group'] = $this->site->getPriceGroupByID($group_id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'settings/update_price', $this->data);

        }
    }

    function brands()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('brands')));
        $meta = array('page_title' => lang('brands'), 'bc' => $bc);
        $this->page_construct('settings/brands', $meta, $this->data);
    }

    function getBrands()
    {
	$this->sma->checkPermissions('brands');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, image, code, name")
            ->from("brands")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_brand/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_brand") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_brand") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_brand/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_brand()
    {
	$this->sma->checkPermissions();

        $this->form_validation->set_rules('name', lang("brand_name"), 'trim|required|is_unique[brands.name]|alpha_numeric_spaces');

        $this->form_validation->set_rules('code', lang("brand_code"), 'is_unique[brands.code]|alpha_dash');

      //  $this->form_validation->set_rules('slug', lang("slug"), 'trim|required|is_unique[brands.slug]|alpha_dash');

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
              //  'slug' => $this->input->post('slug'),
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                    //$this->session->set_flashdata('error', $error);
                    //redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    //echo $this->image_lib->display_errors();
                    $error = $this->image_lib->display_errors();
                    $response['error'] = $error;
                    echo json_encode($response);exit;
                }
                $this->image_lib->clear();
            }

        } elseif ($this->input->post('add_brand')) {
            $error = validation_errors();
            $response['error'] = $error;
            echo json_encode($response);exit;
            //$this->session->set_flashdata('error', validation_errors());
            //admin_redirect("system_settings/brands");
        }

        if ($this->form_validation->run() == true && $sid = $this->settings_model->addBrand($data)) {
            //$this->session->set_flashdata('message', lang("brand_added"));
	    //$ref = isset($_SERVER["HTTP_REFERER"]) ? explode('?', $_SERVER["HTTP_REFERER"]) : NULL;
            //admin_redirect($ref[0] . '?brand=' . $sid);
           // admin_redirect("system_settings/brands");
           
           $data['id'] = $sid;
           $response['brands'] = $data;
           echo json_encode($response);exit;
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_brand', $this->data);

        }
    }

    function edit_brand($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("brand_name"), 'trim|required|alpha_numeric_spaces');
         
        $brand_details = $this->site->getBrandByID($id);
        if ($this->input->post('name') != $brand_details->name) {
            $this->form_validation->set_rules('name', lang("brand_name"), 'required|is_unique[brands.name]');
        }
        if ($this->input->post('code') != $brand_details->code) {
            $this->form_validation->set_rules('code', lang("brand_name"), 'required|is_unique[brands.code]');
        }
       /* $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
        if ($this->input->post('slug') != $brand_details->slug) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash|is_unique[brands.slug]');
        }*/

        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
               // 'slug' => $this->input->post('slug'),
                );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }

        } elseif ($this->input->post('edit_brand')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/brands");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateBrand($id, $data)) {
            $this->session->set_flashdata('message', lang("brand_updated"));
            admin_redirect("system_settings/brands");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brand'] = $brand_details;
            $this->load->view($this->theme . 'settings/edit_brand', $this->data);

        }
    }

    function delete_brand($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->brandHasProducts($id)) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("brand_has_products")));
        }

        if ($this->settings_model->deleteBrand($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("brand_deleted")));
        }
    }

    function import_brands()
    {

        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("system_settings/brands");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen('files/' . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('name', 'code', 'image');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if ( ! $this->settings_model->getBrandByName(trim($csv_ct['name']))) {
                        $data[] = array(
                            'code' => trim($csv_ct['code']),
                            'name' => trim($csv_ct['name']),
                            'image' => trim($csv_ct['image']),
                            );
                    }
                }
            }

            // $this->sma->print_arrays($data);
        }

        if ($this->form_validation->run() == true && !empty($data) && $this->settings_model->addBrands($data)) {
            $this->session->set_flashdata('message', lang("brands_added"));
            admin_redirect('system_settings/brands');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme.'settings/import_brands', $this->data);

        }
    }

    function brand_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteBrand($id);
                    }
                    $this->session->set_flashdata('message', lang("brands_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('brands'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $brand = $this->site->getBrandByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $brand->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $brand->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $brand->image);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'brands_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
	
	 function customfeedback()
    {    $this->sma->checkPermissions();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('custom_feedback')));
        $meta = array('page_title' => lang('custom_feedback'), 'bc' => $bc);
        $this->page_construct('settings/customfeedback', $meta, $this->data);
    }

    function getcustomfeedback()
    {
	 $this->sma->checkPermissions('customfeedback');

        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, question")
            ->from("customfeedback")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_customfeedback/$1') . "' class='tip' title='" . lang("edit_custom_feedback") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_custom_feedback") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_customfeedback/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    function add_customfeedback()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('question', lang("question"), 'trim|is_unique[customfeedback.question]|required');
        $this->form_validation->set_rules('question_type', lang("question_type"), 'required');
        $this->form_validation->set_rules('number_answer', lang("number_answer"), 'required|numeric');
		

        if ($this->form_validation->run() == true) {
			
			
            $data = array('question' => $this->input->post('question'),
                'question_type' => $this->input->post('question_type'),
                'number_answer' => $this->input->post('number_answer'),
            );
			for($i=0; $i<$this->input->post('number_answer'); $i++){
				$data_answer[] = array(
					'answer' => $_POST['answer'][$i],
				);
			}
        } elseif ($this->input->post('add_customfeedback')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customfeedback");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCustomFeedback($data, $data_answer)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang("custom_feedback_added"));
            admin_redirect("system_settings/customfeedback");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['page_title'] = lang("new_custom_feedback");
            $this->load->view($this->theme . 'settings/add_customfeedback', $this->data);
        }
    }

    function edit_customfeedback($id = NULL)
    {
	$this->sma->checkPermissions();

        $this->form_validation->set_rules('code', lang("currency_code"), 'trim|required');
        $cur_details = $this->settings_model->getCurrencyByID($id);
        if ($this->input->post('code') != $cur_details->code) {
            $this->form_validation->set_rules('code', lang("currency_code"), 'required|is_unique[currencies.code]');
        }
        $this->form_validation->set_rules('name', lang("currency_name"), 'required');
        $this->form_validation->set_rules('rate', lang("exchange_rate"), 'required|numeric');

        if ($this->form_validation->run() == true) {

            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'rate' => $this->input->post('rate'),
                'symbol' => $this->input->post('symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            );
        } elseif ($this->input->post('edit_customfeedback')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/customfeedback");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCurrency($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("custom_feedback_updated"));
            admin_redirect("system_settings/customfeedback");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['customfeedback'] = $this->settings_model->getCustomFeedbackByID($id);
			$this->data['customfeedback_answer'] = $this->settings_model->getCustomFeedbackAnswer($id);
			
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_customfeedback', $this->data);
        }
    }

    function delete_customfeedback($id = NULL)
    {
	$this->sma->checkPermissions();
        if ($this->settings_model->deleteCustomFeedback($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("custom_feedback_deleted")));
        }
    }

    function customfeedback_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCurrency($id);
                    }
                    $this->session->set_flashdata('message', lang("currencies_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('currencies'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('rate'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCurrencyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->rate);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'currencies_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
  
    function payment_methods()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('payment_methods')));
        $meta = array('page_title' => lang('payment_methods'), 'bc' => $bc);
        $this->page_construct('settings/payment_methods', $meta, $this->data);
    }

    function get_payment_methods()
    {
	$this->sma->checkPermissions('payment_methods');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, payment_type,display_name,status")
            ->from("payment_methods")
            ->edit_column('status', '$1__$2', 'status, id');

        echo $this->datatables->generate();
    }
    function add_payment_method()
    {
	 $this->sma->checkPermissions();
        $this->form_validation->set_rules('payment_type', lang("payment_type"), 'trim|required|is_unique[payment_methods.payment_type]');
        if ($this->form_validation->run() == true) {

            $data = array(
                'payment_type' => $this->input->post('payment_type'),
                'display_name' => $this->input->post('display_name'),
		          'status'=>1
                //'code' => $this->input->post('code'),
              //  'slug' => $this->input->post('slug'),
                );

            

        } elseif ($this->input->post('add_payment_method')) {
	    $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/payment_methods");
        }

        if ($this->form_validation->run() == true && $sid = $this->settings_model->addPayment_method($data)) {
           
	    $this->session->set_flashdata('message', lang("payment_method_added"));
	   
            admin_redirect("system_settings/payment_methods");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_payment_method', $this->data);

        }
    }
    function payment_method_deactivate($id){
	$this->sma->checkPermissions('tender_type_status');
	$this->settings_model->updatePayment_method_status($id,0);
	//echo $_SERVER["HTTP_REFERER"];exit;
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function payment_method_activate($id){
	$this->sma->checkPermissions('tender_type_status');
	$this->settings_model->updatePayment_method_status($id,1);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function discount_deactivate($id){
	$this->sma->checkPermissions('discounts');
	$this->settings_model->updateDiscount_status($id,0);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function discount_activate($id){
	$this->sma->checkPermissions('discounts');
	$this->settings_model->updateDiscount_status($id,1);
	redirect($_SERVER["HTTP_REFERER"]);
    }   
    function recipe_feedback_mapping(){
	$this->sma->checkPermissions();
	if ($this->input->post('update')) {
         //echo '<pre>';//print_R($_POST);//exit;

           
	    $data = $_POST;
	    $recipe_array = array();
	    $index = 0;
	    foreach($data['group'][0]['recipe_group_id'] as $k => $group){
		foreach($group['sub_category'] as $s_k => $subgroup){
		    foreach($subgroup['recipes'] as $r_k => $recipe){
			$recipe_array[$index]['recipe_id'] = $recipe;$index++;
		    }
		}
	    }
	    
	    $return = $this->settings_model->updateRecipe_feedbackMapping($recipe_array);
	    if ($return) {
		$this->session->set_flashdata('message', lang("Feedback_mapping_updated"));
		admin_redirect("system_settings/recipe_feedback_mapping");
	    }
        } 
	$this->data['recipe_groups'] = $this->site->getAllrecipeCategories_items();
	$this->data['recipe_groups_json'] = json_encode($this->data['recipe_groups']);
	$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	$bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('recipe_feedback_mapping')));
	$meta = array('page_title' => lang('payment_methods'), 'bc' => $bc);
	$this->data['mapped_rids'] = $this->settings_model->getRecipe_feedbackMapping();
	$this->page_construct('settings/recipe_feedback_mapping', $meta, $this->data);
	
	
    }
    function is_unique_category($value,$id){
	$CI =& get_instance();	
	list($pid,$id) = explode('.',$id);
	if($CI->site->is_unique_category($pid,$value,$id)){
	    $error = ($pid!=0)?'sub_group_already_exists_for_this_parent_group!':'group_already_exist!';
            $CI->form_validation->set_message('is_unique_category', lang($error));
            return FALSE;
        }
        return true;
    }
    function is_unique_recipeCategories($value,$id){
	$CI =& get_instance();	
	list($pid,$id) = explode('.',$id);
	if($CI->site->is_unique_recipeCategories($pid,$value,$id)){
	    $error = ($pid!=0)?'sub_group_already_exists_for_this_parent_group!':'group_already_exist!';
            $CI->form_validation->set_message('is_unique_recipeCategories', lang($error));
            return FALSE;
        }
        return true;
    }
    
    
function stores()
    {

    // $this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('stores')));
        $meta = array('page_title' => lang('stores'), 'bc' => $bc);
        $this->page_construct('settings/stores', $meta, $this->data);
    }

    function getStores()
    {
         // $this->sma->checkPermissions('stores');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id, code, name")
            ->from("pro_stores")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_store/$1') . "' class='tip' title='" . lang("edit_store") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_store") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_stores/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        //->unset_column('id');
        echo $this->datatables->generate();
    }

    function add_store()
    {
        
    // $this->sma->checkPermissions();
        $this->form_validation->set_rules('code', lang("code"), 'trim|is_unique[pro_stores.code]|required');
        $this->form_validation->set_rules('name', lang("name"), 'required');
        if ($this->form_validation->run() == true) {
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),                
                'is_default_store' => $this->input->post('is_defalut') ? $this->input->post('is_defalut') : 0,
            );
        } elseif ($this->input->post('add_store')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/store");
        }

        if ($this->form_validation->run() == true && $this->settings_model->addStore($data)) { //check to see if we are creating the customer

            $this->session->set_flashdata('message', lang("store_added"));
            admin_redirect("system_settings/stores");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['page_title'] = lang("new_store");
            $this->load->view($this->theme . 'settings/add_store', $this->data);
        }
    }

    function edit_store($id = NULL)
    {
    // $this->sma->checkPermissions();
        /*echo "<pre>";
        print_r($this->input->post());die;*/
        $this->form_validation->set_rules('code', lang("store_code"), 'trim|required');
        $store_details = $this->settings_model->getStoreByID($id);        
        if ($this->input->post('code') != $store_details->code) {
            $this->form_validation->set_rules('code', lang("store_code"), 'required|is_unique[stores.code]');
        }
        $this->form_validation->set_rules('name', lang("store_name"), 'required');        

        if ($this->form_validation->run() == true) {
            $data = array('code' => $this->input->post('code'),
                'name' => $this->input->post('name'),                
                'is_default_store' => $this->input->post('is_defalut') ? $this->input->post('is_defalut') : 0,
            );
        } elseif ($this->input->post('edit_store')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/stores");
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateStore($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang("store_updated"));
            admin_redirect("system_settings/stores");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['store'] = $this->settings_model->getStoreByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_store', $this->data);
        }
    }

    function delete_store($id = NULL)
    {
    // $this->sma->checkPermissions();
        if ($this->settings_model->deleteCurrency($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("store_deleted")));
        }
    }
    function gb(){
	echo $this->site->generate_bill_number(false);
    }

}
