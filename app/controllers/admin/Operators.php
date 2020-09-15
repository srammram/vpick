<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operators extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('operators', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/operators/';
        $this->thumbs_path = 'assets/uploads/operators/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->allowed_file_size = '1024';
        $this->load->admin_model('operators_model');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('operators')));
        $meta = array('page_title' => lang('operators'), 'bc' => $bc);
        $this->page_construct('operators/index', $meta, $this->data);
    }
    function getOperators(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, first_name name,email,contact_number,gender,status")
            ->from("operators")
             ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('operators/update_kyc_documents/$1') . "' class='tip' title='" . lang("update_kyc_documents") . "'>" . lang("update_docs") . "</a>&nbsp;&nbsp;&nbsp;<a href='" . admin_url('operators/profile/$1') . "' class='tip' title='" . lang("edit_operator") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add(){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('username', lang("username"), 'is_unique[operators.username]');
        $this->form_validation->set_rules('email', lang("email_address"), 'is_unique[operators.email]');
	$this->form_validation->set_rules('contact_number', lang("contact_number"), 'is_unique[operators.contact_number]'); 
        
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'username' => $this->input->post('username'),
                'password' =>md5($this->input->post('password')),
		'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'contact_number' => $this->input->post('contact_number'),               
                'dob' => $this->input->post('dob'),
                'gender' => $this->input->post('gender'),
		'city' => $this->input->post('city'),
                'address' => $this->input->post('address'),
		'zipcode' => $this->input->post('zipcode'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status'),
		'operator_type' => $this->input->post('operator_type'),
            );
            if($_POST['operator_type']=='company'){
		$data['company_name'] = $this->input->post('company_name');
	    }
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
                    admin_redirect("operators/add");
                }
                $photo = $this->upload->file_name;
                $data['photo'] = 'photo/'.$photo;
                
               
                $config = NULL;
            }

            
           
        }
		
		
        if ($this->form_validation->run() == true && $this->operators_model->add_operator($data)){
			
            $this->session->set_flashdata('message', lang("Operator_added"));
            admin_redirect('operators');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('operators'), 'page' => lang('operators')), array('link' => '#', 'page' => lang('add_operator')));
            $meta = array('page_title' => lang('add_operator'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
            $this->page_construct('operators/add', $meta, $this->data);
        }
    }
    function profile($id){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('username', lang("username"), 'required|callback_my_is_unique[operators.username.'.$id.']');//
        $this->form_validation->set_rules('email', lang("email_address"),'required|callback_my_is_unique[operators.email.'.$id.']');
	$this->form_validation->set_rules('contact_number', lang("contact_number"), 'required|callback_my_is_unique[operators.contact_number.'.$id.']');   
        
        if ($this->form_validation->run() == true) {
            $data = array(
                //'username' => $this->input->post('username'),
                
		'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'contact_number' => $this->input->post('contact_number'),
                'dob' => $this->input->post('dob'),
                'gender' => $this->input->post('gender'),
		'address' => $this->input->post('address'),
		'zipcode' => $this->input->post('zipcode'),
                'city' => $this->input->post('city'),
                'status' => $this->input->post('status'),
            );
            if($_POST['password']!=''){
		$data['password'] = md5($this->input->post('password'));
	    }
	    if($_POST['operator_type']=='company'){
		$data['company_name'] = $this->input->post('company_name');
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
                    admin_redirect("operators/add");
                }
                $photo = $this->upload->file_name;
                $data['photo'] = 'photo/'.$photo;
		if($_POST['exist_photo']!=''){
			$this->site->unlink_images($_POST['exist_photo'],$this->upload_path);
		}
                $config = NULL;
            }
	    
        }
		
		
        if ($this->form_validation->run() == true && $this->operators_model->update_operator($id,$data)){
			
            $this->session->set_flashdata('message', lang("Operator_updated"));
            admin_redirect('operators');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('operators'), 'page' => lang('operators')), array('link' => '#', 'page' => lang('profile')));
            $meta = array('page_title' => lang('profile'), 'bc' => $bc);
            $this->data['operator'] = $this->operators_model->getOperatorby_ID($id);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['user_type'] = 'operators';
            $this->page_construct('operators/profile', $meta, $this->data);
        }
    }
    function operator_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->operators_model->update_operator_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function delete_operator(){
        
    }
    function update_kyc_documents($id){
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('user_id', lang("user_id"), 'required');
        
        if ($this->form_validation->run() == true) {
            //echo '<pre>';print_R($_POST);exit;

	    $data = array();
	    foreach($_POST['document_type_id'] as $k => $row){
		$data[$k]['document_type_id'] = $row;
		$data[$k]['id'] = $_POST['document_id'][$k];
		$data[$k]['document_type_name'] = $_POST['document_type_name'][$k];
		$data[$k]['verification_status'] = $_POST['verification_status'][$k];
		$data[$k]['user_type'] = 'operator';
		$data[$k]['user_id'] = $_POST['user_id'];
		$data[$k]['fields'] = $_POST['doc_type_fields'][$row];
	    
		if ($_FILES['photo']['name'][$k] != "") {
		    $config['upload_path'] = $this->upload_path.'documents';
		    $config['allowed_types'] = $this->pdf_types;
		    //$config['max_size'] = $this->allowed_file_size;
		    //$config['max_width'] = $this->Settings->iwidth;
		    //$config['max_height'] = $this->Settings->iheight;
		    $config['overwrite'] = FALSE;
		    $config['max_filename'] = 25;
		    $config['encrypt_name'] = TRUE;
		    
		    $_FILES['document_photo']['name'] = $_FILES['photo']['name'][$k];
                    $_FILES['document_photo']['type'] = $_FILES['photo']['type'][$k];
                    $_FILES['document_photo']['tmp_name'] = $_FILES['photo']['tmp_name'][$k];
                    $_FILES['document_photo']['error'] = $_FILES['photo']['error'][$k];
                    $_FILES['document_photo']['size'] = $_FILES['photo']['size'][$k];
		    //print_R($_FILES['document_photo']);exit;
		    $this->upload->initialize($config);
		    if (!$this->upload->do_upload('document_photo')) {
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('error', $error);
			admin_redirect("operators/update_kyc_documents/".$id);
		    }
		    $photo = $this->upload->file_name;
		    $data[$k]['document_photo'] = 'documents/'.$photo;
		    if($_POST['exist_photo'][$k]!=''){
			$this->site->unlink_images($_POST['exist_photo'][$k],$this->upload_path);
		    }
		    $config = NULL;
		}
	    }
        }
		
		
        if ($this->form_validation->run() == true && $this->site->add_kyc_documents($data)){
			
            $this->session->set_flashdata('message', lang("Operator_added"));
            admin_redirect("operators/update_kyc_documents/".$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('operators'), 'page' => lang('operators')), array('link' => '#', 'page' => lang('add_kyc_documents')));
            $meta = array('page_title' => lang('add_kyc_documents'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['doc_types'] = $this->site->getKycDouments_type($id,'operator');
	    $this->data['id'] = $id;
	    $this->data['user_type'] = 'operators';
	    $this->data['action'] = "operators/update_kyc_documents/".$id;
	    //$this->data['documents'] = $this->site->getKycDouments($id,'operator');
            $this->page_construct('kyc_documents/add_kyc_documents', $meta, $this->data);
        }
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
