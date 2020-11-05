<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Incentives extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('incentives', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('incentives_model');
    }

    function index($action = NULL)
    {
    }
}
