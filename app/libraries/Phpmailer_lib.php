<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailer_Lib
{
    public function __construct()
    {
    }	
	
	public function load()
	{		
		require_once APPPATH . 'third_party/PHPMailer/Exception.php';
		require_once APPPATH . 'third_party/PHPMailer/PHPMailer.php';
		require_once APPPATH . 'third_party/PHPMailer/SMTP.php';
		
		$mail = new PHPMailer;
		return $mail;
		
	}

}