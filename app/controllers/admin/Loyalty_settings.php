<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Loyalty_settings extends MY_Controller
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
        $this->load->admin_model('loyalty_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

    function index($warehouse_id = null)
    {

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouse'] = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse_id;
        $this->data['settings'] = $this->loyalty_model->getSettings();           
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('loyalty_settings')));
        $meta = array('page_title' => lang('loyalty_settings'), 'bc' => $bc);            
        $this->page_construct('loyalty/index', $meta, $this->data);

        /*$this->load->library('gst');
       

        if ($this->form_validation->run() == true) {

            $language = $this->input->post('language');

            if ((file_exists(APPPATH.'language'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'sma_lang.php') && is_dir(APPPATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                admin_redirect("loyalty_settings");
                $lang = 'english';
            }
            			
			$timezone = explode(',', $this->input->post('timezone'));			
            
            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
            if ( ! DEMO && TIMEZONE != $data['timezone']) {
                if ( ! $this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('loyalty_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect("loyalty_settings");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['warehouse'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['settings'] = $this->loyalty_model->getSettings();           
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('loyalty_settings')));
            $meta = array('page_title' => lang('loyalty_settings'), 'bc' => $bc);            
            $this->page_construct('loyalty/index', $meta, $this->data);
        }*/
    }
    function getLoyaltys()
    {

    $this->sma->checkPermissions('customer_discounts');
        $this->load->library('datatables');
        $this->datatables
            //->select("id, name, discount_type, value, created_dt")
        /*name,DATE_FORMAT(from_date, '%Y-%m-%d') as from_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date, eligibity_point, status, prefix, serial_number*/
        ->select("'sno',id, name, DATE_FORMAT(from_date, '%d-%m-%Y') as from_date, DATE_FORMAT(end_date, '%d-%m-%Y') as end_date,eligibity_point, prefix,serial_number,status")
            ->from("loyalty_settings")
            ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("edit_BBQ_discount") . "' href='" . admin_url('loyalty_settings/edit/$1') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_loyalty") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('loyalty_settings/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id")
        ->edit_column('status', '$1__$2', 'status, id');
    echo $this->datatables->generate();
    }

    public function add()
    {

        $this->sma->checkPermissions();               
        $this->form_validation->set_rules('from_date', $this->lang->line("from_date"), 'required');        
        if ($this->form_validation->run() == true) {           
           
            $date = date('Y-m-d H:i:s');
            
            $data = array('name' => $this->input->post('name'),
                'from_date' => $this->input->post('from_date'),
                'end_date' => $this->input->post('to_date'),
                'prefix' => $this->input->post('prefix'),
                'serial_number' => $this->input->post('card_number'),
                'eligibity_point' => $this->input->post('eligibity_point'),
                 'status' => $this->input->post('loyalty_status')
            );

            foreach ($this->input->post('start_price') as $key => $split) {
               $accumulation[] = array(                    
                    'start_amount' => $this->input->post('start_price')[$key],
                    'end_amount' => $this->input->post('end_price')[$key],
                    'per_amounts' => $this->input->post('per_amount')[$key],
                    'per_points' => $this->input->post('per_point')[$key],
               );
             }           
           
             foreach ($this->input->post('reedemption_per_point') as $key1 => $split) {
               $reedemption[] = array(
                    'points' => $this->input->post('reedemption_per_point')[$key1],
                    'amount' => $this->input->post('reedemption_per_amount')[$key1],
             );
             }
           
		  
        }
        if ($this->form_validation->run() == true && $this->loyalty_model->addLoyalty($data, $accumulation, $reedemption)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("loyalty_added"));
            admin_redirect('loyalty_settings');
        } else {            

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));                    
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('loyalty_settings'), 'page' => lang('loyalty')), array('link' => '#', 'page' => lang('loyalty_configuration')));
            $meta = array('page_title' => lang('loyalty_configuration'), 'bc' => $bc);
            $this->page_construct('loyalty/add', $meta, $this->data);
        }
    }

    public function edit($id = null)
    {
        
         $this->sma->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('from_date', $this->lang->line("from_date"), 'required');

        if ($this->form_validation->run() == true) {
            $date = date('Y-m-d H:i:s');
            
            $data = array('name' => $this->input->post('name'),
                'from_date' => $this->input->post('from_date'),
                'end_date' => $this->input->post('to_date'),
                'prefix' => $this->input->post('prefix'),
                'serial_number' => $this->input->post('card_number'),
                'eligibity_point' => $this->input->post('eligibity_point'),
                'status' => $this->input->post('loyalty_status')
            );
            
            $accumulation = array();           
             foreach ($this->input->post('start_price') as $key => $split) {
               $accumulation[] = array(                    
                    'start_amount' => $this->input->post('start_price')[$key],
                    'end_amount' => $this->input->post('end_price')[$key],
                    'per_amounts' => $this->input->post('per_amount')[$key],
                    'per_points' => $this->input->post('per_point')[$key],                    
               );
             }
           
             foreach ($this->input->post('reedemption_per_point') as $key1 => $split) {
               $reedemption[] = array(
                    'points' => $this->input->post('reedemption_per_point')[$key1],
                    'amount' => $this->input->post('reedemption_per_amount')[$key1],
             );             
           }           
        }
        if ($this->form_validation->run() == true && $this->loyalty_model->updateLoyalty($id,$data, $accumulation, $reedemption)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("loyalty_updated"));
            admin_redirect('loyalty_settings');
        } else {
            
            $this->data['id'] = $id;            
            $this->data['loyalty'] = $this->loyalty_model->getLoyaltyByID($id);
            if(empty($this->data['loyalty'])){
                admin_redirect('loyalty_settings');
            }
            $this->data['accumulation'] = $this->loyalty_model->getAccumulation($id);            
            $this->data['reedemption'] = $this->loyalty_model->getReedemption($id);
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['loyalty_id'] = $id;                                    
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');            
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('loyalty_settings'), 'page' => lang('loyalty_settings')), array('link' => '#', 'page' => lang('edit_loyalty_settings')));
            $meta = array('page_title' => lang('edit_loyalty_settings'), 'bc' => $bc);
            $this->page_construct('loyalty/edit', $meta, $this->data);
        }
    }   
    function delete($id = NULL)
    {
        // $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $delete_check = $this->loyalty_model->checkLoyalused($id);
        if($delete_check == FALSE){            
            
        if ($this->loyalty_model->deleteLoyal($id)) {
            if($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("loyalty_deleted")));
            }
            $this->session->set_flashdata('message', lang('loyalty_deleted'));
            admin_redirect('loyalty_settings');
        }
        }else{            
            $this->sma->send_json(array('error' => 1, 'msg' => lang("could_not_be_delete_loyalty_issued_to_user")));  
        }

    }
    function loyalty_card()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('loyalty_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('loyalty_card')));
        $meta = array('page_title' => lang('loyalty_card'), 'bc' => $bc);
        $this->page_construct('loyalty/card_list', $meta, $this->data);
    }

    function getLoyaltyCards()
    {
        //{$this->db->dbprefix('loyalty_cards')}.status as status
       // $this->sma->checkPermissions('customer_discounts');(CASE WHEN(srampos_loyalty_points_details.identify =1) THEN 'Accumulation' ELSE 'Redemtion'  END) AS table_status
        $current_date = date('Y-m-d');
        $this->load->library('datatables');
        $this->datatables        
        ->select("'sno',{$this->db->dbprefix('loyalty_cards')}.id as id,{$this->db->dbprefix('loyalty_settings')}.name as name, card_no,  (CASE WHEN(DATE_FORMAT({$this->db->dbprefix('loyalty_cards')}.expiry_date,'%Y-%m-%d') < '$current_date') THEN 1 ELSE {$this->db->dbprefix('loyalty_cards')}.status  END) AS status ")
            ->from("loyalty_cards")
            ->join("loyalty_settings", 'loyalty_settings.id=loyalty_cards.loyalty_id', 'left')
            ->order_by('loyalty_cards.id','ASC')                       
        ->edit_column('status', '$1__$2', 'status, id');           
        echo $this->datatables->generate();
    }
    public function loyalty_card_add()
    {
        // $this->sma->checkPermissions();                       
        $this->data['loyalty'] = $this->loyalty_model->getLoyalty();
        $this->form_validation->set_rules('number_of_cards', $this->lang->line("number_of_cards"), 'required');        
        if ($this->form_validation->run() == true) {                                  

            $no_of_cards = $this->input->post('number_of_cards');
            $prefix = $this->input->post('prefix');
            $card_number = $this->input->post('card_number');            
            
            if($no_of_cards != 0 ){            
                // for($i = 0, $i = $no_of_cards; $i <=  $no_of_cards; $i++) {
                $no_of_cards =$no_of_cards-1;
                for($i= 0; $i<=$no_of_cards; ++$i) {                   
                   $cardno = sprintf('%04d',$card_number+$i);
                    $card_array[] = array(
                                'card_no' =>$prefix.$cardno,                                
                                'loyalty_id' => $this->input->post('loyalty_name'),
                                'status' => 1,
                            );
                }
            } 
			                             
        }
		
		
        
        if ($this->form_validation->run() == true && $this->loyalty_model->addLoyaltyCards($card_array)) {
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', $this->lang->line("loyalty_added"));
            admin_redirect('loyalty_settings/loyalty_card');
        } else {            

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));                    
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('loyalty_settings'), 'page' => lang('loyalty')), array('link' => '#', 'page' => lang('loyalty_card_generation')));
            $meta = array('page_title' => lang('loyalty_card_generation'), 'bc' => $bc);
            $this->page_construct('loyalty/loyalty_card', $meta, $this->data);
        }
    }
function activate($id)
        {
           $this->loyalty_model->activate($id);
           redirect($_SERVER["HTTP_REFERER"]);
        }    
    function deactivate($id = NULL)
    {
        // $this->sma->checkPermissions('products', TRUE);       
        $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            } else {

                $this->data['products'] = $this->loyalty_model->getLoyaltyByID($id);
                $this->data['modal_js'] = $this->site->modal_js();                
                $this->load->view($this->theme . 'loyalty/deactivate', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }
                if ($this->Owner) {
                    $this->loyalty_model->deactivate($id);
                    /*$this->session->set_flashdata('message', $this->recipe_model->messages());*/
                }
            }
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }    
       function card_activate($id)
        {
           $this->loyalty_model->card_activate($id);
           redirect($_SERVER["HTTP_REFERER"]);
        }    
    function card_deactivate($id = NULL)
    {
        // $this->sma->checkPermissions('products', TRUE);       
        $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->data['products'] = $this->loyalty_model->getLoyaltyCardByID($id);
                $this->data['modal_js'] = $this->site->modal_js();                
                $this->load->view($this->theme . 'loyalty/card_deactivate', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }
                if ($this->Owner) {
                    $this->loyalty_model->card_deactivate($id);
                    /*$this->session->set_flashdata('message', $this->recipe_model->messages());*/
                }
            }
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
}
