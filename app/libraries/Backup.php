<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *  ==============================================================================
 *  Author  : Vijayabalan
 *  Email   : info@srampos.com
 *  ==============================================================================
 */

class Backup{
    public function initiate(){
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, site_url('autobackup/autobackdbfile'));
	    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_VERBOSE, true);
	     $output = curl_exec($ch);
		curl_close($ch);
		
	}
    
}
