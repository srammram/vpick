<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_allocated extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
		//$this->lang->admin_load('people', $this->Settings->user_language);
		$this->lang->admin_load('common', $this->Settings->user_language);
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
		$this->upload_path = 'assets/uploads/';
		$this->image_path = base_url('assets/uploads/');
		$this->load->admin_model('users_model');
		$this->load->admin_model('masters_model');
		$this->load->admin_model('people_model');
    }
	
		
	/*###### users*/
   
	function index($action=false){
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
		$this->data['user'] = $this->users_model->getUser($this->session->userdata('user_id'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('driver_allocated')));
        $meta = array('page_title' => lang('driver_allocated'), 'bc' => $bc);
		
        $this->page_construct('driver_allocated/index', $meta, $this->data);
    }
	
	function getAllocatedDrivers(){
		$this->load->library('datatables');
		$this->datatables
            ->select("{$this->db->dbprefix('driver_current_status')}.id as id, up.first_name, up.last_name, u.mobile, t.name as taxi_name, t.number, If({$this->db->dbprefix('driver_current_status')}.allocated_status = 1, '1', '0') as status ")
            ->from("driver_current_status")
			->join("user_profile up", "up.user_id = driver_current_status.driver_id")
			->join("users u", "u.id = driver_current_status.driver_id")
			->join("taxi t", "t.id = driver_current_status.taxi_id")
			
			//->where("users.group_id", $group_id)
            ->edit_column('status', '$1__$2', 'status, id');
		echo $this->datatables->generate();
	}
	
	
	
}
