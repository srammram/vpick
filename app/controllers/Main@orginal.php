<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller
{

    function __construct() {
        parent::__construct();
		$this->load->admin_model('main_model');
		$this->load->admin_model('rides_model');
        $this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->library('phpmailer_lib');
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
		//$this->to_email = "info@vpickall.com";
		$this->to_email = "smtp@vpickall.com";

		$this->Host = 'mail.vpickall.com';
		$this->SMTPSecure = false;
		$this->SMTPAutoTLS = false;
		$this->Port = 587;
		$this->Username = 'smtp@vpickall.com'; // YOUR gmail email
		$this->Password = 'And345T&'; // YOUR gmail password

    }

	function sos(){
		
      	//$booked_status = $_GET['status']; 
		$id = $_GET['id']; 
		
		/*if($booked_status == 1){
			$msg = 'Request Ride';
		}elseif($booked_status == 2){
			$msg = 'Booked Ride';
		}elseif($booked_status == 3){
			$msg = 'Onride Ride';
		}elseif($booked_status == 4){
			$msg = 'Waiting Ride';
		}elseif($booked_status == 5){
			$msg = 'Completed Ride';
		}elseif($booked_status == 6){
			$msg = 'Cancelled Ride';
		}elseif($booked_status == 7){
			$msg = 'Ride Later Ride';
		}*/
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $id;
		//$this->data['status'] = $booked_status;
		$this->data['rides'] = $this->rides_model->getRides($id);
		$this->data['waypoints'] = $this->site->getWaypoint($id);
		$this->load->view($this->theme . 'tracking', $this->data);
	}
	function getpath($id) {
        $this->load->view($this->theme . 'index', $this->data);
    }
	
	function index() {
        $this->load->view($this->theme . 'index', $this->data);
    }
	
	function aboutus() {
        $this->load->view($this->theme . 'aboutus', $this->data);
    }

	function drivewithus() {

		$this->form_validation->set_rules('driver_name', lang("driver_name"), 'required');
		$this->form_validation->set_rules('driver_email', lang("driver_email"), 'required');
		$this->form_validation->set_rules('driver_phone', lang("driver_phone"), 'required');		
		
        if ($this->form_validation->run() == true) {
		   $user = array(
				'forms_type' => 1,
				'name' => $this->input->post('driver_name'),
				'country_code' => $this->input->post('country_code'),
				'mobile_number' => $this->input->post('driver_phone'),
				'email_address' => $this->input->post('driver_email'),
				'created_on' => date('y-m-d H:i:s')
		   );
        }
		
        if ($this->form_validation->run() == true && $this->main_model->addEnquiry($user)){

			$name = $user['name'];
			$email = $user['email_address'];
			$phone = $user['mobile_number'];
			$country_code = $user['country_code'];

			$subject = 'Vpick Driver Partner Message sent by '.$name;

			$message = "
				Name = $name 
				Email = $email
				Phone = (+$country_code) $phone
			";

			$mail = $this->phpmailer_lib->load();
			try {
				// Server settings
				$mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
				$mail->isSMTP();
				$mail->Host = $this->Host;
				$mail->SMTPAuth = true;
				//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->SMTPSecure = $this->SMTPSecure;
				$mail->Port = $this->Port;

				$mail->Username = $this->Username; // YOUR gmail email
				$mail->Password = $this->Password; // YOUR gmail password

				// Sender and recipient settings
				$mail->setFrom($email, $name); // From
				$mail->addAddress($this->to_email, ''); //To
				//$mail->addReplyTo($email, $name); // to set the reply to

				// Setting the email content
				$mail->IsHTML(true);
				$mail->Subject = $subject;
				$mail->Body = $message;
				//$mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';
				$mail->send();

				echo "Email message sent.";
				$_SESSION["m_ok"] = "1";
				//die;
			} catch (Exception $e) {
				echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
				$_SESSION["m_fail"] = "0";
				//die;
			}
			
			site_redirect('drivewithus');
        }
		else
		{
			$this->data['countrys'] = $this->site->getCountrywithoutparent();
			$this->load->view($this->theme . 'drivewithus', $this->data);
		}
    }

	function franchisee() {
		
		$this->form_validation->set_rules('partner_name', lang("partner_name"), 'required');
		$this->form_validation->set_rules('partner_email', lang("partner_email"), 'required');
		$this->form_validation->set_rules('partner_phone', lang("partner_phone"), 'required');		
		
        if ($this->form_validation->run() == true) {

		   $user = array(
				'forms_type' => 2,
				'name' => $this->input->post('partner_name'),
				'country_code' => $this->input->post('country_code'),
				'mobile_number' => $this->input->post('partner_phone'),
				'email_address' => $this->input->post('partner_email'),
				'created_on' => date('y-m-d H:i:s')
		   );
        }
		
        if ($this->form_validation->run() == true && $this->main_model->addEnquiry($user)){

			$name = $user['name'];
			$email = $user['email_address'];
			$phone = $user['mobile_number'];
			$country_code = $user['country_code'];

			$subject = 'Vpick Franchisee Message sent by '.$name;
			$message = "
				Name = $name 
				Email = $email
				Phone = (+$country_code) $phone
			";

			$mail = $this->phpmailer_lib->load();
			try {
				// Server settings
				$mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
				$mail->isSMTP();
				$mail->Host = $this->Host;
				$mail->SMTPAuth = true;
				//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->SMTPSecure = $this->SMTPSecure;
				$mail->Port = $this->Port;

				$mail->Username = $this->Username; // YOUR gmail email
				$mail->Password = $this->Password; // YOUR gmail password

				// Sender and recipient settings
				$mail->setFrom($email, $name);
				$mail->addAddress($this->to_email, '');
				//$mail->addReplyTo('', 'Sender Name'); // to set the reply to

				// Setting the email content
				$mail->IsHTML(true);
				$mail->Subject = $subject;
				$mail->Body = $message;
				//$mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';
				$mail->send();

				echo "Email message sent.";
				$_SESSION["m_ok"] = "1";
				//die;
			} catch (Exception $e) {
				echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
				$_SESSION["m_fail"] = "0";
				//die;
			}

			site_redirect('franchisee');
		}
		else
		{	
			$this->data['countrys'] = $this->site->getCountrywithoutparent();	
			$this->load->view($this->theme . 'franchisee', $this->data);
		}
    }
	function book_ride() {
        $this->load->view($this->theme . 'book_ride', $this->data);
    }
	function faq() {
        $this->load->view($this->theme . 'faq', $this->data);
    }
	function contact() {

		$this->form_validation->set_rules('contact_name', lang("contact_name"), 'required');		
		$this->form_validation->set_rules('contact_phone', lang("contact_phone"), 'required');
		$this->form_validation->set_rules('contact_email', lang("contact_email"), 'required');
		
        if ($this->form_validation->run() == true) {

		   $user = array(
				'forms_type' => 0,
				'name' => $this->input->post('contact_name'),
				'country_code' => $this->input->post('country_code'),
				'mobile_number' => $this->input->post('contact_phone'),
				'email_address' => $this->input->post('contact_email'),
				'description' => $this->input->post('contact_message'),
				'created_on' => date('y-m-d H:i:s')
		   );

        }

        if ($this->form_validation->run() == true && $this->main_model->addEnquiry($user)){

			$name = $this->input->post('contact_name');
			$email = $this->input->post('contact_email');
			$phone = $this->input->post('contact_phone');
			$country_code = $this->input->post('country_code');
			$message = $this->input->post('contact_message');

			$subject = 'Vpick Contact Message sent by '.$name;
			$message = "
				Name = $name 
				Email = $email
				Phone = (+$country_code) $phone
				Message = $message
			";

			$mail = $this->phpmailer_lib->load();
			try {
				// Server settings
				$mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
				$mail->isSMTP();
				$mail->Host = $this->Host;
				$mail->SMTPAuth = true;
				//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->SMTPSecure = $this->SMTPSecure;
				$mail->SMTPAutoTLS = $this->SMTPAutoTLS;
				$mail->Port = $this->Port;

				$mail->Username = $this->Username; // YOUR gmail email
				$mail->Password = $this->Password; // YOUR gmail password

				// Sender and recipient settings
				$mail->setFrom($email, $name);
				$mail->addAddress($this->to_email, '');
				$mail->addReplyTo($email, $name); // to set the reply to

				// Setting the email content
				$mail->IsHTML(true);
				$mail->Subject = $subject;
				$mail->Body = $message;
				//$mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';
				$mail->send();

				echo "Email message sent.";
				$_SESSION["m_ok"] = "1";
				die;
			} catch (Exception $e) {
				echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
				$_SESSION["m_fail"] = "0";
				die;
			}

			site_redirect('contact');
        }
		else
		{
			$this->data['countrys'] = $this->site->getCountrywithoutparent();
			$this->load->view($this->theme . 'contact', $this->data);
		}
    }
	function terms_conditions() {
        $this->load->view($this->theme . 'terms_conditions', $this->data);
    }
	function activity_report() {
		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
        $this->load->view($this->theme . 'activity_report', $this->data);
    }
	
	function getActivityReport(){
		if($_GET['is_country'] != ''){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$user_type = $_GET['user_type'];
		$user_id = $_GET['user_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('rides')}.id as id, {$this->db->dbprefix('rides')}.booking_timing, {$this->db->dbprefix('rides')}.booking_no,  c.first_name as customer_name, c.email as customer_email, c.mobile as customer_mobile, {$this->db->dbprefix('rides')}.ride_timing, {$this->db->dbprefix('rides')}.start, {$this->db->dbprefix('rides')}.ride_timing_end, {$this->db->dbprefix('rides')}.end, 
			
			{$this->db->dbprefix('rides')}.distance_km, {$this->db->dbprefix('rides')}.distance_price,
			d.first_name as driver_name, d.email as driver_email, d.mobile as driver_mobile, t.name as cab_name, t.number, tt.name as cab_type_name, 
			{$this->db->dbprefix('rides')}.actual_loc, rp.total_distance,  rp.discount_name,  rp.discount_fare,  rp.total_fare, rp.total_tax_fare, rp.total_toll, rp.total_parking, rp.outstanding_from_last_trip, rp.waiting_charge,  rp.final_total,
			country.name as instance_country 
			
			")
            ->from("rides")
			->join("countries country", " country.iso = rides.is_country", "left")
			->join("users c", " c.id = rides.customer_id", "left")
			->join("users d", " d.id = rides.driver_id", "left")
			->join("taxi_type tt", " tt.id = rides.cab_type_id", "left")
			->join("taxi t", " t.id = rides.taxi_id", "left")
			->join("ride_payment rp", " rp.ride_id = rides.id", "left");
			
			
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booking_timing) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('rides')}.booking_timing) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($_GET['is_country'] != ''){
				$this->datatables->where("rides.is_country", $countryCode);
			}
		
			if($user_type == 4){
				$this->datatables->where("rides.driver_id", $user_id);
			}elseif($user_type == 5){
				$this->datatables->where("rides.customer_id", $user_id);
			}
			
			$this->datatables->where("rides.status", 5);
			$this->datatables->group_by("rides.id");
			
            
          
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
	}

	public function health_report(){

		$this->data['commoncountry'] = $this->site->getcountryCodeID($countryCode);
        $this->load->view($this->theme . 'health_report', $this->data);
	}

	function getHealthReport(){
		if($this->session->userdata('group_id') == 1){
			$countryCode =  $_GET['is_country'];	
		}else{
			$countryCode = $this->countryCode;	
		}
		$group_id = $this->Driver;
		
		$sdate = $_GET['sdate'];
		$edate = $_GET['edate'];
		$driver_id = $_GET['driver_id'];
		
        $this->load->library('datatables');
		 
			
		
        $this->datatables
            ->select("{$this->db->dbprefix('health_driver')}.id as id, {$this->db->dbprefix('health_driver')}.created_on, 
			d.first_name as driver_name, d.email as driver_email, d.mobile as driver_mobile, {$this->db->dbprefix('health_driver')}.health_name, {$this->db->dbprefix('health_driver')}.health_hours,
			country.name as instance_country 
			
			")
            ->from("health_driver")
			->join("countries country", " country.iso = health_driver.is_country", "left")
			
			->join("users d", " d.id = health_driver.driver_id", "left");
			
			
			
			
			
			
			if(!empty($sdate) && !empty($edate)){
				$this->datatables->where("DATE({$this->db->dbprefix('health_driver')}.created_on) >=", date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
       			$this->datatables->where("DATE({$this->db->dbprefix('health_driver')}.created_on) <=", date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
			}
			
			
			if($this->session->userdata('group_id') == 1 && $countryCode != ''){
				$this->datatables->where("health_driver.is_country", $countryCode);
			}elseif($this->session->userdata('group_id') != 1){
				$this->datatables->where("health_driver.is_country", $countryCode);
			}
			
			
			$this->datatables->group_by("health_driver.id");
			
            
          
			$this->datatables->unset_column('id');
        echo $this->datatables->generate();
		//echo $this->db->last_query();
		
		
    }
	
	function ridecurl() {
		/*ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo @json_encode($out);
        header("Content-Length: " . ob_get_length());
        @ob_end_flush();
        flush();*/
		//print_r($_POST);
		echo $_POST['value'];
        echo 'aaa';
		
    }
	
	function login() {
        admin_redirect('login');
    }

	function privacy_policy() {
        $this->load->view($this->theme . 'privacy_policy', $this->data);
    }
	
	function help_form() {
		
		$customer_id = $this->input->post('customer_id');
		$countryCode = $this->input->post('is_country');
		foreach ($_POST as $name => $val)
		{
			 if($name != 'token' && $name != 'customer_type' && $name != 'customer_id' && $name != 'help_department' && $name != 'help_main_id' && $name != 'help_sub_id' && $name != 'ticket' && $name != 'ride_id'){
				$res[$name] = $val;
			 }
			 
			 if($name == 'customer_id'){
				$customer_id = $val;
			 }
			 if($name == 'help_department'){
				$help_department = $val;
			 }
			 if($name == 'help_sub_id'){
				$help_id = $val;
			 }
			 
		}
		foreach ($_FILES as $name1 => $val)
		{
			if ($_FILES[$name1]['size'] > 0) {
				$config['upload_path'] = $this->upload_path;
				$config['allowed_types'] = $this->photo_types;
				$config['overwrite'] = FALSE;
				$config['max_filename'] = 25;
				$config['encrypt_name'] = TRUE;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload($name1)) {
					$result = array( 'status'=> false , 'message'=> 'image not uploaded.');
				}
				$files = $this->upload->file_name;
				$res[$name1] = $files;
				$config = NULL;
			}
		}
		
		$enquiry = array(
			'enquiry_code' => 'ENQ'.date('YmdHis'),
			'enquiry_date' => date('Y-m-d'),
			'enquiry_type' => 'APP',
			'customer_id' => $this->input->post('customer_id'),
			'services_id' => $this->input->post('ride_id'),
			'help_id' => $help_id,
			'help_message' => json_encode($res),
			'help_department' => $help_department,
		);

		print_r($enquiry);
		
		$insert = $this->main_model->create_ticket($enquiry, $this->input->post('customer_id'), $help_department, $countryCode);
		
		if($insert == TRUE){
			site_redirect('success');
		}else{
			site_redirect('help');
		}
		
       // $this->load->view($this->theme . 'privacy_policy', $this->data);
    }
	
	function help() {
		if( $_GET['customer_type'] != 'undefined' && $_GET['customer_type'] != NULL && !empty($_GET['customer_type'])){
			$customer_type = $_GET['customer_type'];
		}else{
			$customer_type = '0';
		}
		
		if( $_GET['customer_id'] != 'undefined' && $_GET['customer_id'] != NULL && !empty($_GET['customer_id'])){
			$customer_id = $_GET['customer_id'];
		}else{
			$customer_id = '0';
		}
		
		if( $_GET['ride_id'] != 'undefined' && $_GET['ride_id'] != NULL && !empty($_GET['ride_id'])){
			$ride_id = $_GET['ride_id'];
		}else{
			$ride_id = '0';
		}
		
		if( $_GET['parent_id'] != 'undefined' && $_GET['parent_id'] != NULL && !empty($_GET['parent_id'])){
			$parent_id = $_GET['parent_id'];
		}else{
			$parent_id = '0';
		}

		if( $_GET['is_country'] != 'undefined' && $_GET['is_country'] != NULL && !empty($_GET['is_country'])){
			$is_country = $_GET['is_country'];
		}else{
			$is_country = 'IN';	
		}


		$help_ids = $this->main_model->getIds($parent_id);
		
		
		
		$this->data['customer_type'] = $customer_type;
		$this->data['customer_id'] = $customer_id;
		$this->data['is_country'] = $is_country;
		$this->data['ride_id'] = $ride_id;
		$this->data['parent_id'] = $parent_id;
		$this->data['help_sub_id'] = $parent_id;
		
		$this->data['help_main_id'] = $help_ids->help_main_id;
		$this->data['help_id'] = $help_ids->help_id;
		
		
		$this->data['customer_details'] = $this->main_model->getCustomer($customer_id, $ride_id);
		$this->data['help'] = $this->main_model->getForms($parent_id);
		
        $this->load->view($this->theme . 'help', $this->data);
    }
	
	function success() {
		
		
        $this->load->view($this->theme . 'success', $this->data);
    }
	
	
	function contactform(){

		$email = $_POST['email'];

		$date =date('Y-m-d H:i:s');
	//    $result = mysqli_query($mysqli, "INSERT INTO contactform(name,email,phone,comment,date) VALUES('$contact_name','$contact_email','$contact_phone','$contact_comment','$date')");
		//$to = 'info@vpickall.com';
		
		$to = $this->to_email;
		 
		
		//$to = $contact_email;
		$subject = 'SUBSCRIPTION FORM';
		$body = '<div style="background-color:#fff; width:100%; height:100%; position:relative; float:left;" >
		<div style="width:590px; position:relative; margin:40px auto; padding-bottom:15px; background:#d2d2d2">    
			<div style="width:100%; height:auto; position:relative; text-align:center;  background:#fff; float:left; margin:0; padding:10px 0px;    border: 1px solid #00ac9b;">
				<img src="http://vpickall.com/themes/default/admin/assets/frontend/images/logo.png" style="width:80px;" alt="vpickall">
			</div>
			<div style="width:560px; height:auto; min-height:150px; position:relative; float:left; margin:0; padding:0px 15px; border: 1px solid #898989; background:#fff;">
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; height:30px; position:relative; float:left; margin:0; padding:0; line-height:30px; color:#333;text-align: center;font-size: 18px;">SUBSCRIPTION FORM</div>
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; height:50px; position:relative; float:left; margin:0; padding:0; line-height:50px; color:#333; font-size:18px;">
					Dear Admin,
				</div>
				<div style="width:30%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; font-size:14px; height:auto; position:relative; float:left; margin:0; padding:0; line-height:20px; color:#777;">
					Email :
				</div>
				<div style="width:70%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; font-size:14px; height:auto; position:relative; float:left; margin:0; padding:0; line-height:20px; color:#333;">
					'.$email.'
				</div>
	</div>
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; font-size:14px; height:50px; position:relative; float:left; margin:0; padding:0; line-height:50px; color:#C32143; ">
				</div>
			</div>
		</div>
	</div>';
		
		$headers = "From: " .$to. "\r\n";
		$headers .= "Reply-To: ". "no-reply@mydomain.com" . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		$admin = mail($to, $subject, $body, $headers);
		
		
		//$to = 'info@vpickall.com';
		$to1 = $email;
		$subject1 = 'Thank You';
		$body1 = '<div style="background-color:#fff; width:100%; height:100%; position:relative; float:left;" >
		<div style="width:590px; position:relative; margin:40px auto; padding-bottom:15px; background:#d2d2d2">    
			<div style="width:100%; height:auto; position:relative; text-align:center; color:#163AA0; background:#efefef; float:left; margin:0; padding:0px 0px;border: 1px solid #898989;">
				<img src="http://vpickall.com/themes/default/admin/assets/frontend/images/logo.png" style="width:80px;" alt="vpickall">
			</div>
			<div style="width:560px; height:auto; min-height:10px; position:relative; float:left; margin:0; padding:0px 15px; border:1px solid  #d2d2d2; background:#fff;">
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; height:30px; position:relative; float:left; margin:0; padding:0; line-height:30px; color:#16858F;"></div>
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; height:50px; position:relative; float:left; margin:0; padding:0; line-height:50px; color:#163AA0; font-size:18px;">
					Dear User,
				</div>
				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; font-size:14px; height:auto; position:relative; float:left; margin:0; padding:0; line-height:20px; color:#777;">
					Your mail has been sent successfuly ! Thank you for your feedback
				</div>
				
			   
	</div>

				<div style="width:100%; font-family:Lucida Grande, Lucida Sans Unicode, Lucida Sans, DejaVu Sans, Verdana, sans-serif; font-size:14px; height:50px; position:relative; float:left; margin:0; padding:0; line-height:50px; color:#C32143; ">
				</div>
			</div>
		</div>';
		
		$headers1 = "From: " .$to1. "\r\n";
		$headers1 .=  "Reply-To: ". "no-reply@mydomain.com" . "\r\n";
		$headers1 .= "MIME-Version: 1.0\r\n";
		$headers1 .= "Content-Type: text/html; charset=utf-8\r\n";
		$customer = mail($to1, $subject1, $body1, $headers1);
		if($admin && $customer)
		{
			echo "successful";
		}else{
			echo "error";
		}

	}

}
