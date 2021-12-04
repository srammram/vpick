<?php defined('BASEPATH') OR exit('No direct script access allowed');

    function image_link($type,$name){
		$uploadPath = 'assets/uploads/';
		$link = base_url().$uploadPath.$type.'/'.$name;
		return $link;
    }
	
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
			
			$otp = $sms_rep_arr[0];
			$opts = array(
			  'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
						  "Cookie: foo=bar\r\n"
			  )
			);

			$context = stream_context_create($opts);


			$response = file_get_contents('https://api.msg91.com/api/v5/otp?authkey=226739AMe8Nuyau5d440cfb&template_id=5f0be620d6fc057cf30d7a63&extra_param={%22OTP%22:%22'.$otp.'%22}&mobile='.$sms_phone.'&invisible=1', false, $context);
			$result  = json_decode($response);
			if($result->type == 'success'){
				return 1;
			}else{
				return 0;
			}
				
		 }	
		 	
	}
	
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
			
			$sms_data = array("flow_id" => "5f3e72dcd6fc0579a943ced0", "sender" => "NMNEWS", "recipients" => array(array("mobiles" => $sms_phone, "description" => $sms_msg)));   
			$data_string = json_encode($sms_data);
			
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $data_string,
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_HTTPHEADER => array(
				"authkey: 226739AMe8Nuyau5d440cfb",
				"content-type: application/json"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  //echo "cURL Error #:" . $err;
			  return 0;
			} else {
			  //echo $response;
			  return 1;
			}
			
		 }	
		 	
	}
	
    function clean_title($string) {
		$string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z0-9]/i','-',$string);
        $string = preg_replace("/(-){2,}/",'$1',$string);
		return $string;
    }
	
	function CalculateTime($times) {
        $i = 0;
        foreach ($times as $time) {
            sscanf($time, '%d:%d', $hour, $min);
            $i += $hour * 60 + $min;
        }

        if($h = floor($i / 60)) {
            $i %= 60;
        }

        return sprintf('%02d:%02d', $h, $i);
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


function callAPI($method, $url, $data){
	
   $curl = curl_init();
   switch ($method){
      case "POST":
        // curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
		
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			
         break;
      case "PUT":
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   
   curl_close($curl);
  
   return $result;
}

