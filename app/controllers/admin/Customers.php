<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		$this->module = 'customers';
		$this->module_model = 'customers_model';
        $this->lang->admin_load('customer', $this->Settings->user_language);
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
        $this->load->admin_model($this->module_model);
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang($this->module)));
        $meta = array('page_title' => lang($this->module), 'bc' => $bc);
        $this->page_construct($this->module.'/index', $meta, $this->data);
    }
    function getCustomers(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, first_name name,email,contact_number,gender,status")
            ->from("customers")
             ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url($this->module.'/profile/$1') . "' class='tip' title='" . lang("edit_customer") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add(){
        $this->sma->checkPermissions();
	$model = $this->module_model;
        $this->form_validation->set_rules('username', lang("username"), 'required|is_unique[customers.username]');
        $this->form_validation->set_rules('email', lang("email_address"), 'required|is_unique[customers.email]');
	$this->form_validation->set_rules('contact_number', lang("contact_number"), 'required|is_unique[customers.contact_number]');     
        
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'username' => $this->input->post('username'),
                'password' =>md5($this->input->post('password')),
		'customer_type' => $this->input->post('customer_type'),
		'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'contact_number' => $this->input->post('contact_number'),               
                'dob' => $this->input->post('dob'),
                'gender' => $this->input->post('gender'),
		'country' => $this->input->post('country'),
		'state' => $this->input->post('state'),
		'city' => $this->input->post('city'),
                'address' => $this->input->post('address'),
		'zipcode' => $this->input->post('zipcode'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status'),
		
		'emergency_contact_name' => $this->input->post('emergency_contact_name'),
		'emergency_contact_relationship' => $this->input->post('emergency_contact_relationship'),
		'emergency_contact_number' => $this->input->post('emergency_contact_number'),
		
		'email_verified' => $this->input->post('email_verified'),
		'mobile_verified' => $this->input->post('mobile_verified'),
            );
            
            if ($_FILES['photo']['size'] > 0) {
                $config['upload_path'] = $this->upload_path.'photo/';
                $config['allowed_types'] = $this->photo_types;
                //$config['max_size'] = $this->allowed_file_size;
                //$config['max_width'] = $this->Settings->iwidth;
                //$config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('photo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    //admin_redirect("drivers/add");
                }
                $photo = $this->upload->file_name;
                $data['photo'] = 'photo/'.$photo;
                
               
                $config = NULL;
            }

            
           
        }
		
		
        if ($this->form_validation->run() == true && $this->$model->add_customer($data)){
			
            $this->session->set_flashdata('message', lang("Customer_added"));
            admin_redirect('customers');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module), 'page' => lang($this->module)), array('link' => '#', 'page' => lang('add_customer')));
            $meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['groups'] = $this->site->getAllCustomerGroups();
            $this->page_construct($this->module.'/add', $meta, $this->data);
        }
    }
    function profile($id){
        $this->sma->checkPermissions();
	$model = $this->module_model;           
        
	$this->form_validation->set_rules('username', lang("username"), 'required|callback_my_is_unique[customers.username.'.$id.']');//
        $this->form_validation->set_rules('email', lang("email_address"),'required|callback_my_is_unique[customers.email.'.$id.']');
	$this->form_validation->set_rules('contact_number', lang("contact_number"), 'required|callback_my_is_unique[customers.contact_number.'.$id.']');   
        if ($this->form_validation->run() == true) {
            
            $data = array(
                //'username' => $this->input->post('username'),
                'customer_type' => $this->input->post('customer_type'),
		'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'contact_number' => $this->input->post('contact_number'),               
                'dob' => $this->input->post('dob'),
                'gender' => $this->input->post('gender'),
		'country' => $this->input->post('country'),
		'state' => $this->input->post('state'),
		'city' => $this->input->post('city'),
                'address' => $this->input->post('address'),
		'zipcode' => $this->input->post('zipcode'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status'),
		
		'emergency_contact_name' => $this->input->post('emergency_contact_name'),
		'emergency_contact_relationship' => $this->input->post('emergency_contact_relationship'),
		'emergency_contact_number' => $this->input->post('emergency_contact_number'),
		
		'email_verified' => $this->input->post('email_verified'),
		'mobile_verified' => $this->input->post('mobile_verified'),
            );
            if($_POST['password']!=''){
		$data['password'] = md5($this->input->post('password'));
	    }
            if ($_FILES['photo']['size'] > 0) {
                $config['upload_path'] = $this->upload_path.'photo';
                $config['allowed_types'] = $this->photo_types;
                //$config['max_size'] = $this->allowed_file_size;
                //$config['max_width'] = $this->Settings->iwidth;
                //$config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['max_filename'] = 25;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('photo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    //admin_redirect("drivers/add");
                }
                $photo = $this->upload->file_name;
                $data['photo'] = 'photo/'.$photo;
		if($_POST['exist_photo']!=''){
			$this->site->unlink_images($_POST['exist_photo'],$this->upload_path);
		}
                $config = NULL;
            }
	    
        }
		
		
        if ($this->form_validation->run() == true && $this->$model->update_customer($id,$data)){
			
            $this->session->set_flashdata('message', lang("customer_updated"));
            admin_redirect($this->module.'/profile/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module), 'page' => lang($this->module)), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('profile'), 'bc' => $bc);
            $this->data['customer'] = $this->$model->getCustomerby_ID($id);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['groups'] = $this->site->getAllCustomerGroups();
	    $this->data['user_type'] = 'customers';
	    //echo '<pre>';print_R($this->data['customer']);exit;
            $this->page_construct($this->module.'/profile', $meta, $this->data);
        }
    }
    function customer_status($status,$id){
	$model = $this->module_model;
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->$model->update_customer_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function delete_customer(){
        
    }
    function my_is_unique($value,$id){
        $CI =& get_instance();	
	list($table,$field,$id) = explode('.',$id);
	if($CI->site->my_is_unique($id,$value,$field,$table)){
            $CI->form_validation->set_message('my_is_unique', lang($field." already exists"));
            return FALSE;
        }
        return true;
    }
}
