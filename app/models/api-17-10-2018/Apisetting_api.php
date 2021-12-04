<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Apisetting_api extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

	public function checkDevices($api_key){
		$q = $this->db->get_where('api_keys', array('key' => $api_key), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	
	public function updateDevices($api_key, $devices_key, $devices_type, $api_type){
		$this->db->where('key', $api_key);
		$q = $this->db->update('api_keys', array('devices_type' => $devices_type, 'devices_key' => $devices_key, 'api_type' => $api_type));
        if ($q) {
            return TRUE;
        }
		return FALSE;
	}
	
	public function GetAllapitype(){
		$q = $this->db->get('group_api');
		if ($q->num_rows() > 0) {			
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
        return FALSE;
	}

}
