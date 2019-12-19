<?php

/**
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class Ride {

    public function test(){
		echo 'Welcome';	
	}
    // function makes curl request to firebase servers
    private function sendPushNotification($fields) {
        
       // require_once __DIR__ . '/config.php';

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . API_FIREBASE_ACCESS_KEY,
            'Content-Type: application/json'
        );
		
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
		
        // Close connection
        curl_close($ch);
        return $result;
    }
	 public function ridecurl($feild) {
        $data['date']=date('Y-m-d H:i:s');
		$data['name']='ananthan';
		$data['value'] = $feild;
		$str = http_build_query($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://localhost/kapp/main/ridecurl");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		echo $output;
    }
}

?>