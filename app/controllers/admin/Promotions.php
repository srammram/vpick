<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotions extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('promotions', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/promotions/';
        $this->thumbs_path = 'assets/uploads/promotions/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->allowed_file_size = '1024';
        $this->load->admin_model('promotions_model');
    }

     function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('promotions')));
        $meta = array('page_title' => lang('promotions'), 'bc' => $bc);
        $this->page_construct('promotions/index', $meta, $this->data);
    }
    function getPromotions(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',id,promotion_title,promotion_code,discount_type,discount_to,status")
            ->from("promotions")
             ->edit_column('status', '$1__$2', 'status, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('promotions/assign/$1') . "' class='tip' title='" . lang("assign_promotion") . "'>assign</a>&nbsp;&nbsp;<a href='" . admin_url('promotions/edit/$1') . "' class='tip' title='" . lang("edit_promotion") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        //->unset_column('id');
        echo $this->datatables->generate();
    }
    function add(){
        $this->sma->checkPermissions();
        $this->form_validation->set_rules('promotion_title', lang("promotion_title"), 'required');
        
        if ($this->form_validation->run() == true) {
           // echo '<pre>';print_R($this->input->post('apply_to'));//exit;
            
            $data = array(
                'promotion_title' => $this->input->post('promotion_title'),
                'promotion_code' =>$this->input->post('promotion_code'),
		'discount_type' => $this->input->post('discount_type'),
		'discount_to' => $this->input->post('discount_to'),
                'discount' => $this->input->post('discount'),
                'max_discount' => $this->input->post('max_discount'),
                'link' => $this->input->post('link'),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
		'apply_to' => implode(',',$this->input->post('apply_to')),
                'description' => $this->input->post('description'),
                'terms_conditions' => $this->input->post('terms_conditions'),
                'no_of_times' => $this->input->post('no_of_times'),
                'created_on' => date('Y-m-d H:i:s'),
                'status' => 1,
            );
            //print_R($data);exit;
            if ($_FILES['photo']['size'] > 0) {
                $config['upload_path'] = $this->upload_path.'images/';
                $config['allowed_types'] = $this->image_types;
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
                $data['image'] = 'images/'.$photo;
                
               
                $config = NULL;
            }
           
        }
		
		
        if ($this->form_validation->run() == true && $this->promotions_model->add_promotion($data)){
			
            $this->session->set_flashdata('message', lang("Promotion_added"));
            admin_redirect('promotions');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promotions'), 'page' => lang('promotions')), array('link' => '#', 'page' => lang('add_promotion')));
            $meta = array('page_title' => lang('add_driver'), 'bc' => $bc);
	    $this->data['countries'] = $this->site->getAllCountries();
            $this->page_construct('promotions/add', $meta, $this->data);
        }
    }
    function edit($id){
	$this->sma->checkPermissions();
	
        $this->form_validation->set_rules('promotion_title', lang("promotion_title"), 'required');
       
           
        
        if ($this->form_validation->run() == true) {
            
            $data = array(
                'promotion_title' => $this->input->post('promotion_title'),
                //'promotion_code' =>$this->input->post('promotion_code'),
		//'discount_type' => $this->input->post('discount_type'),
		//'discount_to' => $this->input->post('discount_to'),
                //'discount' => $this->input->post('discount'),
                //'max_discount' => $this->input->post('max_discount'),
                'link' => $this->input->post('link'),
                //'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
		'apply_to' => implode(',',$this->input->post('apply_to')),
                'description' => $this->input->post('description'),
                'terms_conditions' => $this->input->post('terms_conditions'),
                'no_of_times' => $this->input->post('no_of_times'),
            );
           
            if ($_FILES['photo']['size'] > 0) {
                $config['upload_path'] = $this->upload_path.'images';
                $config['allowed_types'] = $this->image_types;
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
                $data['image'] = 'images/'.$photo;
		if($_POST['exist_photo']!=''){
			$this->site->unlink_images($_POST['exist_photo'],$this->upload_path);
		}
                $config = NULL;
            }
        }
		
		
        if ($this->form_validation->run() == true && $this->promotions_model->update_promotion($id,$data)){
			
            $this->session->set_flashdata('message', lang("Promotion_updated"));
            admin_redirect('promotions/edit/'.$id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promotions'), 'page' => lang('promotions')), array('link' => '#', 'page' => lang('edit_promotions')));
            $meta = array('page_title' => lang('edit_promotions'), 'bc' => $bc);
            $this->data['promotion'] = $this->promotions_model->getPromotionby_ID($id);
	    
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['user_type'] = 'promotions';
            $this->page_construct('promotions/edit', $meta, $this->data);
        }
    }
    function promotion_status($status,$id){
         $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->promotions_model->update_promotion_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function delete_promotion($id){
        $data['status'] = 9;        
        $this->promotions_model->update_promotion_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
    }
    function assign($id){
	$this->sma->checkPermissions();
	$customers = array();
        
        if (isset($_POST['search_customers'])) {
            $data = array();
            if(isset($_POST['uninstalled_app'])) : 
                $data = array(
                    'uninstalled_app' => $this->input->post('uninstalled_app'),
                );
            elseif($this->input->post('customer_email')!='') :
                $data = array(
                    'customer_email' => $this->input->post('customer_email'),
                );
            elseif($this->input->post('customer_mobile')!='') :
                $data = array(
                    'customer_mobile' => $this->input->post('customer_mobile'),
                );
            elseif($_POST['customer_type']!='' || $_POST['no_of_bookings']!='' || $_POST['start_date']!='' || $_POST['end_date']!='') :
                $data = array(
                    'customer_type' => $this->input->post('customer_type'),
                    'no_of_bookings' => $this->input->post('no_of_bookings'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                );
            endif;
            $this->session->set_userdata('p_search_customers',$data);
            admin_redirect('promotions/assign/'.$id);
            //$customers = $this->promotions_model->search_customers($data);
        }else if(isset($_POST['clear_search'])){
            $this->session->unset_userdata('p_search_customers');
            admin_redirect('promotions/assign/'.$id);
        }
		
		
            //print_R($this->session->userdata('p_search_customers'));exit;
            $this->data['customers'] = $customers;
            $this->data['promotion'] = $this->promotions_model->getPromotionby_ID($id);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('promotions'), 'page' => lang('promotions')), array('link' => '#', 'page' => lang('assign_coupon').' - '.$this->data['promotion']->promotion_code));
            $meta = array('page_title' => lang('assign_coupon'), 'bc' => $bc);
            
	    $this->data['countries'] = $this->site->getAllCountries();
	    $this->data['customer_groups'] = $this->site->getAllCustomerGroups();
            $this->data['search'] = $this->session->userdata('p_search_customers');
            $this->page_construct('promotions/assign', $meta, $this->data);        
    }
    function customers(){
        $limit = 10;//$this->input->post('pagelimit');    
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        //echo $this->uri->segment(4);exit;
        if (isset($_POST['search_customers'])) {
            $data = array();
            if(isset($_POST['uninstalled_app'])) : 
                $data = array(
                    'uninstalled_app' => $this->input->post('uninstalled_app'),
                );
            elseif($this->input->post('customer_email')!='') :
                $data = array(
                    'customer_email' => $this->input->post('customer_email'),
                );
            elseif($this->input->post('customer_mobile')!='') :
                $data = array(
                    'customer_mobile' => $this->input->post('customer_mobile'),
                );
            elseif($_POST['customer_type']!='' || $_POST['no_of_bookings']!='' || $_POST['start_date']!='' || $_POST['end_date']!='') :
                $data = array(
                    'customer_type' => $this->input->post('customer_type'),
                    'no_of_bookings' => $this->input->post('no_of_bookings'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                );
            endif;
            $data = $this->promotions_model->search_customers($data,$limit,$offset);
            if(!empty($data['customers'])){
                $customers = $data['customers'];
            }else{
                $customers = 'empty';
            }
            $total = $data['total'];
            $pagination = $this->pagination('promotions/customers',$limit,$offsetSegment,$total);
            echo json_encode(array('customers'=>$customers,'total'=>$total,'pagination'=>$pagination,'all_customers'=>$data['all_customers']));
        }
    }
    function apply_coupon(){
        $promotion_id = $this->input->post('promotion_id');
        $ids = $this->input->post('customers');
        $unselected_ids = (isset($_POST['unselected']))?$this->input->post('unselected'):array();       
        $return = $this->promotions_model->apply_coupon($promotion_id,$ids,$unselected_ids);
        
        
        echo json_encode($return);exit;
    }
    function remove_coupon(){
        $promotion_id = $this->input->post('promotion_id');
        $ids = (isset($_POST['customers']))?$this->input->post('customers'):array();
        //$unselected_ids = (isset($_POST['unselected']))?$this->input->post('unselected'):array();       
        $all =false;
        if(isset($_POST['remove_all'])){
            $all = true;
        }
        $return = $this->promotions_model->remove_coupon($promotion_id,$ids,$all);
        
        
        echo json_encode($return);exit;
    }
    function pagination($url,$per,$segment,$total){
        $config['base_url'] = admin_url($url);
        $config['per_page'] = $per;
        $config['uri_segment'] = $segment;
        $config['total_rows'] = $total;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['prev_link'] = 'Previous';
        $config['next_link'] = 'Next';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
       //$config['num_links'] = 3;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $limit = $config['per_page'];
        $offset = $this->uri->segment($config['uri_segment'],0);
        $offset = ($offset>1)?(($offset-1) * $limit):0;
        
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
   }
}
