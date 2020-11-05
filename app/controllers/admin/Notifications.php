<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->module = 'notifications';$this->model = 'notifications_model';
        $this->lang->admin_load('taxi', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/notifications/';
        $this->thumbs_path = 'assets/uploads/notifications/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->allowed_file_size = '1024';
        $this->image_path = base_url('assets/uploads/notifications/');
        $this->load->admin_model($this->model);
        
        
    }

    function email_templates($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('email_notifications')));
        $meta = array('page_title' => lang('email_notifications'), 'bc' => $bc);
        $this->page_construct('notifications/index', $meta, $this->data);
    }
    function getMail_templates(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('mail_templates')}.id as id,user_type,unique_id,title,subject,sender_email,status")
            ->from("mail_templates")
            ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('notifications/edit_email_notification/$1') . "' class='tip' title='" . lang("edit_template") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_email_notification(){
        //$this->sma->checkPermissions();
        $model = $this->model;
        
        $this->form_validation->set_rules('title', lang("title"), 'required|callback_my_is_unique[mail_templates.title.'.@$_POST['user_type'].']');
        $this->form_validation->set_rules('subject', lang("subject"), 'required');
        $this->form_validation->set_rules('content', lang("content"), 'required');
        $this->form_validation->set_rules('sender_email', lang("sender_email"), 'valid_email'); 
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'title' => $this->input->post('title'),
                'user_type' => $this->input->post('user_type'),
                'unique_id'=>clean_title($this->input->post('title')),
                'subject' => $this->input->post('subject'),
                'content' => $this->input->post('content'),
                'sender_email' => $this->input->post('sender_email'),
                'created_on'=>date('y-m-d H:i:s'),
                'status' => 1,
            );           
        }
		//print_R($data);
                //print_R($data_images);exit;
	 if ($this->form_validation->run() == true && $this->$model->add_email_notification($data)){
			
            $this->session->set_flashdata('message', lang("notification_added"));
            admin_redirect($this->module.'/email_templates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module.'/email_templates'), 'page' => lang('email_'.$this->module)), array('link' => '#', 'page' => lang('add_email_notification')));
            $meta = array('page_title' => lang('add_email_notification'), 'bc' => $bc);
            $this->page_construct($this->module.'/add_email_notification', $meta, $this->data);
        }	
       
    }
    function edit_email_notification($id){
        $model = $this->model;
        $this->form_validation->set_rules('title', lang("title"), 'required');
        $this->form_validation->set_rules('subject', lang("subject"), 'required');
        $this->form_validation->set_rules('content', lang("content"), 'required'); 
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                //'unique_id'=>clean_title($this->input->post('title')),
                'subject' => $this->input->post('subject'),
                'content' => $this->input->post('content'),
                'sender_email' => $this->input->post('sender_email')
            );                
        }
	
               // print_R($data);exit;
	 if ($this->form_validation->run() == true && $this->$model->update_email_notification($data,$id)){
			
            $this->session->set_flashdata('message', lang("email_notification_updated"));
            admin_redirect($this->module.'/edit_email_notification/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module.'/email_templates'), 'page' => lang('email_notifications')), array('link' => '#', 'page' => lang('edit_email_notification')));
            $meta = array('page_title' => lang('edit_email_notification'), 'bc' => $bc);
            $this->data['notification'] = $this->$model->get_email_template($id);
            $this->data['id'] = $id;
            $this->page_construct('notifications/edit_email_notification', $meta, $this->data);
        }
    }
    function email_notification_status($status,$id){
        $model = $this->model;
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->$model->update_email_notification_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function delete_email_notification(){
        $model = $this->model;
        $data['status'] = 9;
        $this->$model->update_email_notification_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    /********************** SMS notifications ***********************/
    function sms_templates($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('sms_notifications')));
        $meta = array('page_title' => lang('sms_notifications'), 'bc' => $bc);
        $this->page_construct('notifications/sms_notifications', $meta, $this->data);
    }
    function getSMS_templates(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        
        $this->datatables
            ->select("'sno',{$this->db->dbprefix('sms_templates')}.id as id,user_type,unique_id,title,sender,status")
            ->from("sms_templates")
            ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('notifications/edit_sms_notification/$1') . "' class='tip' title='" . lang("edit_template") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add_sms_notification(){
        //$this->sma->checkPermissions();
        $model = $this->model;
         $this->form_validation->set_rules('title', lang("title"), 'required|callback_my_is_unique[sms_templates.title.'.@$_POST['user_type'].']');
        $this->form_validation->set_rules('content', lang("content"), 'required'); 
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'title' => $this->input->post('title'),
                'user_type' => $this->input->post('user_type'),
                'unique_id'=>clean_title($this->input->post('title')),
                'content' => $this->input->post('content'),
                'sender' => $this->input->post('sender'),
                'created_on'=>date('y-m-d H:i:s'),
                'status' => 1,
            );           
        }
		//print_R($data);
                //print_R($data_images);exit;
	 if ($this->form_validation->run() == true && $this->$model->add_sms_notification($data)){
			
            $this->session->set_flashdata('message', lang("notification_added"));
            admin_redirect($this->module.'/sms_templates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module.'/sms_templates'), 'page' => lang('sms_'.$this->module)), array('link' => '#', 'page' => lang('add_sms_notification')));
            $meta = array('page_title' => lang('add_sms_notification'), 'bc' => $bc);
            $this->page_construct($this->module.'/add_sms_notification', $meta, $this->data);
        }	
       
    }
    function edit_sms_notification($id){
        $model = $this->model;
        $this->form_validation->set_rules('title', lang("title"), 'required');
        $this->form_validation->set_rules('content', lang("content"), 'required'); 
        
        if ($this->form_validation->run() == true) {
            
            
            $data = array(
                'content' => $this->input->post('content'),
                'sender' => $this->input->post('sender')
            );                
        }
	
               // print_R($data);exit;
	 if ($this->form_validation->run() == true && $this->$model->update_sms_notification($data,$id)){
			
            $this->session->set_flashdata('message', lang("sms_notification_updated"));
            admin_redirect($this->module.'/edit_sms_notification/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module.'/sms_templates'), 'page' => lang('sms_notifications')), array('link' => '#', 'page' => lang('edit_sms_notification')));
            $meta = array('page_title' => lang('edit_sms_notification'), 'bc' => $bc);
            $this->data['notification'] = $this->$model->get_sms_template($id);
            $this->data['id'] = $id;
            $this->page_construct('notifications/edit_sms_notification', $meta, $this->data);
        }
    }
    function sms_notification_status($status,$id){
        $model = $this->model;
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->$model->update_sms_notification_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function delete_sms_notification(){
        $model = $this->model;
        $data['status'] = 9;
        $this->$model->update_sms_notification_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function my_is_unique($value,$id){
        $model = $this->model;
	list($table,$field,$id) = explode('.',$id);
	if($this->$model->my_is_unique($id,$value,$field,$table)){
            $this->form_validation->set_message('my_is_unique', lang($field." already exists"));
            return FALSE;
        }
        return true;
    }
    
}
