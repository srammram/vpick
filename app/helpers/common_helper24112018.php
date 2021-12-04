<?php defined('BASEPATH') OR exit('No direct script access allowed');

    function image_link($type,$name){
	$uploadPath = 'assets/uploads/';
	$link = base_url().$uploadPath.$type.'/'.$name;
	return $link;
    }
    function clean_title($string) {
	$string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z0-9]/i','-',$string);
        $string = preg_replace("/(-){2,}/",'$1',$string);
	return $string;
    }
    function send_email($mail_content){
	
      $CI =& get_instance();
//	$config['protocol']         = 'smtp'; // 'mail', 'sendmail', or 'smtp'
//	$config['mailpath']         = '/usr/sbin/sendmail';
//	$config['smtp_host']        = 'smtp.gmail.com'; // if you are using gmail
//	$config['smtp_user']        = 'atharani19@gmail.com';
//	$config['smtp_pass']        = ''; // App specific password
//	$config['smtp_port']        = 587 ; // for gmail
//	$config['smtp_timeout']     = 500; 
      $CI->load->library('email');
      //$CI->load->library('email', $config);
      $CI->email->set_newline("\r\n");
      $CI->email->from('no-reply@srammram.com'); // change it to yours
      $CI->email->to($mail_content['to']);// change it to yours
      $CI->email->subject($mail_content['subject']);
      $CI->email->message($mail_content['content']);
      if($CI->email->send()){
	echo 'success';
      }else{
	//show_error($CI->email->print_debugger());
      }
    }
	
	/* Get user key */
if(!function_exists('get_random_key'))
{
	function get_random_key($length = 20, $table=null, $field_name=null,$type='alnum')
	{
		$CI =& get_instance();
		$CI->load->helper('string');
		if($table ==""){
		 return random_string($type,$length);
		 }else {
		$randomkey = random_string($type,$length);
		$getdata = $CI->db->get(array($field_name),$table,array($field_name =>trim($randomkey)));
		return $userkey = (!empty($getdata) ? get_random_key() : $randomkey); 
	  }
	}
}

if(!function_exists('send_transaction_sms')){
	function send_transaction_sms($sms_template_slug, $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code){
		
				$CI=& get_instance();
				$data = array();
		$template_table = "sms_templates";
		 $query = "SELECT sms_content , sms_variables FROM  {$CI->db->dbprefix('sms_templates')} WHERE unique_id = '".$sms_template_slug."'";
         $result = $CI->db->query($query);
		 if($result->num_rows()>0){
		    $data =  $result->row();
		}
		
		 
		 if(!empty($data)){
			$sms_content = $data->sms_content;
			$sms_message = str_replace($sms_chk_arr, $sms_rep_arr, $sms_content);
			$sms_msg = str_replace(" ", "%20", $sms_message);
			$otp = $sms_rep_arr[0];
			$curl = curl_init();

			
			$response = file_get_contents("http://api.msg91.com/api/sendhttp.php?authkey=232920Avigv7N9CX935b7bd7be&route=4&mobiles=".$sms_phone."&message=".$sms_message."&sender=SRAMKA");
			$response_array = json_decode($response);
			if(!empty($response_array) && ($response_array->type == 'success')){
				return 1;
			} else {
				return 0;
			}
			
		 }	
		 	
	}
}

if(!function_exists('send_otp_sms')){
	function send_otp_sms($sms_template_slug, $sms_chk_arr, $sms_rep_arr, $sms_phone, $sms_country_code){
		
				$CI=& get_instance();
				$data = array();
		$template_table = "sms_templates";
		 $query = "SELECT sms_content , sms_variables FROM  {$CI->db->dbprefix('sms_templates')} WHERE unique_id = '".$sms_template_slug."'";
         $result = $CI->db->query($query);
		 if($result->num_rows()>0){
		    $data =  $result->row();
		}
		
		 
		 
		 if(!empty($data)){
			 $sms_content = $data->sms_content;
			$sms_message = str_replace($sms_chk_arr, $sms_rep_arr, $sms_content);
			$sms_msg = str_replace(" ", "%20", $sms_message);
			$otp = $sms_rep_arr[0];
			$curl = curl_init();

			$response = file_get_contents("http://api.msg91.com/api/sendotp.php?authkey=232920Avigv7N9CX935b7bd7be&mobile=".$sms_phone."&message=".$sms_message."&sender=SRAMKA&otp=".$otp);
			$response_array = json_decode($response);
			if(!empty($response_array) && ($response_array->type == 'success')){
				return 1;
			} else {
				return 0;
			}
			
		 }	
		 	
	}
}
	
//    function my_is_unique($value,$id){
//        echo 44;exit;
//        $CI =& get_instance();	
//	list($table,$field,$id) = explode('.',$id);
//	if($CI->site->my_is_unique($id,$value,$field,$table)){
//            $CI->form_validation->set_message('my_is_unique', lang($field." already exists"));
//            return FALSE;
//        }
//        return true;
//    }