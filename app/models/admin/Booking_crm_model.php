<?php defined('BASEPATH') OR exit('No direct script access allowed');

class booking_crm_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
    }
	
		
}
