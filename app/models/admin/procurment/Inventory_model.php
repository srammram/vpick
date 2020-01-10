<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

   function getSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateSetting($data)
    {
        $this->db->where('pos_id', '1');
        if ($this->db->update('pos_settings', $data)) {
            return true;
        }
        return false;
    }
	
	public function updateAccessPermissions($id, $data)
    {
		$this->db->delete('pro_access_permission', array('user_id' => $id));
        if ($this->db->insert('pro_access_permission', $data)) {
            return true;
        }
        return false;
    }
	
	

	function getUserIDBY($id){
		$this->db->where('id', $id);	
		$q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	
	function getProcurmentBYUser($id){
		$this->db->where('user_id', $id);	
		$q = $this->db->get('pro_access_permission');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}


}
