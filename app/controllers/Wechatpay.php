<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wechatpay extends MY_Controller
{

    function __construct() {
        parent::__construct();
		 $this->load->admin_model('main_model');
		 $this->load->admin_model('rides_model');
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
    }

	
	function callbackvisa() {
        $this->load->view($this->theme . 'wechatpay/callbackvisa', $this->data);
    }
	function notify() {
        $this->load->view($this->theme . 'wechatpay/notify', $this->data);
    }
	function unioncallback() {
        $this->load->view($this->theme . 'wechatpay/unioncallback', $this->data);
    }
	function unionnotify() {
        $this->load->view($this->theme . 'wechatpay/unionnotify', $this->data);
    }
	
	
	
    

}
