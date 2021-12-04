<?php

/**
 * @author Ananthan
 * @link URL Tutorial link
 */


class Firstdata {



    function __construct() {
        
    }
	
	

	function createHash($chargetotal, $currency) {
		date_default_timezone_set('Asia/Kolkata');
		$dateTime = date("Y:m:d-H:i:s");
		
		$storeId = "3344004067";
		$sharedSecret = "T~|i2Z7QmZ";
		$stringToHash = $storeId.$dateTime.$chargetotal.$currency.$sharedSecret;
		$ascii = bin2hex($stringToHash);
		return hash("sha256", $ascii);
		//return sha1($ascii);
	}
   

}
