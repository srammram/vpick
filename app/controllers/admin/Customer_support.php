<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_support extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
	$this->module = 'customer_support';
	$this->module_model = 'customer_support_model';
        $this->lang->admin_load('customer', $this->Settings->user_language);
        $this->load->library('form_validation');
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
    function getCustomerQueries(){
	$model = $this->module_model;
	$this->sma->checkPermissions('index');
        $limit = 10;       
        $offsetSegment = 4;
        $offset = $this->uri->segment($offsetSegment,0);
        $data= '';
        
            $data = $this->$model->getCustomerQueries($limit,$offset);
            if (!empty($data['data'])) {
                 
                 $queries = $data['data'];
             }
             else{
                
                $queries = 'empty';
             }
        
       
        $total = $data['total'];
        $pagination = $this->pagination($this->module.'/getCustomerQueries',$limit,$offsetSegment,$total);
        //echo $daysummary;
        $this->sma->send_json(array('queries' => $queries,'pagination'=>$pagination));

	
//        $this->sma->checkPermissions('index');
//        $this->load->library('datatables');
//        $this->datatables
//            ->select("'sno',{$this->db->dbprefix('customer_support_tickets')}.id as id,ticket,{$this->db->dbprefix('customers')}.first_name as name,content,{$this->db->dbprefix('customer_support_tickets')}.status as status")
//            ->from("customer_support_tickets")
//	    ->join("customer_support",'customer_support.ticket_id=customer_support_tickets.id')
//	    ->join("customers",'customers.id=customer_support_tickets.customer_id')
//	    ->where("parent_id",0)
//            ->edit_column('status', '$1__$2', 'status, id')
//            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url($this->module.'/reply/$1') . "' class='tip' title='" . lang("view") . "'><i class=\"fa fa-edit\"></i></a></div>", "ticket");
//
//        //->unset_column('id');
//        echo $this->datatables->generate();
    }
    function reply($ticket){
        $this->sma->checkPermissions();
	$model = $this->module_model;
	$this->data['ticket'] = $this->$model->getTicket($ticket);
	$ticket_id = $this->data['ticket']->id;
	$this->$model->update_query_status($ticket_id);
	$this->data['first_ticket'] = $this->$model->getfirstQuery_ticket($ticket_id);
	//echo '<pre>';print_R($this->session->userdata());exit;
	$this->form_validation->set_rules('content', lang("content"), 'required');
        //$this->form_validation->set_rules('taxi_type', lang("taxi_type"), 'required|callback_taxi_type_unique['.$_POST["location_id"].']');  
       
        if (isset($_POST['content']) && $this->form_validation->run() == true) {
            
            
            $data = array(
                //'location_id' =>$this->input->post('location_id'),
                //'taxi_type' =>$this->input->post('taxi_type'),
                'ticket_id' => $ticket_id,
		'parent_id'=>@$this->data['first_ticket']->id,
		'content' => $this->input->post('content'),
                'sent_by' => 'support team',
                'sent_by_id' => $this->session->userdata('user_id'),
		
            );
            
           
           
        }	
		//print_R($data);exit;
        
        if (isset($_POST['content']) && $this->form_validation->run() == true && $return = $this->$model->add_reply($data)){
	    $mail_content['subject'] = $this->data['ticket']->ticket;
	    $mail_content['content'] = $data['content'];
	    $mail_content['to'] = 'atharani19@gmail.com';
	    send_email($mail_content);
                $this->session->set_flashdata('message', lang("msg_sent"));
                admin_redirect($this->module.'/reply/'.$ticket);
        } else { 	
	    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
	    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url($this->module), 'page' => lang($this->module)), array('link' => '#', 'page' => lang('profile')));
	    $meta = array('page_title' => lang('profile'), 'bc' => $bc);
	    
	    $this->data['queries'] = $this->$model->getCustomerQueries_byticket($ticket_id);
	    //echo '<pre>';print_R( $this->data['queries']);exit;
	    $this->page_construct($this->module.'/view', $meta, $this->data);
	}
        
    }
    function ticket_status($status,$id){
	$model = $this->module_model;
        $data['status'] = 0;
        if($status=='activate'){
            $data['status'] = 1;
        }
        $this->$model->update_ticket_status($data,$id);
	redirect($_SERVER["HTTP_REFERER"]);
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
