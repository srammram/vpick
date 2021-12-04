<?php

/**
 * @author Ananthan
 * @link URL Tutorial link
 */
 
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;

require APPPATH . '/libraries/vendor/autoload.php';

class Socketemitter {



    function __construct() {
        
    }

    public function setEmit($event, $edata) {
		//http://13.233.9.134:9000
       $client = new Client(new Version2X('https://35.154.46.42:9000', [
    'headers' => [
        'X-My-Header: websocket rocks',
        'Authorization: Bearer 12b3c4d5e6f7g8h9i'
    ]
]));
	   $client->initialize();
	  
	   $result = $client->emit($event, $edata);
	   if($result){
			return true;   
	   }
	   return false;
    }

   

}
