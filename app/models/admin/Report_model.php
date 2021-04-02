<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
    function getUserHealth($countryCode){
        $this->db->select('u.first_name as stage, SUM(health_hours) as duration');
        $this->db->from('users u');
        $this->db->join('health_driver hd', 'hd.driver_id = u.id', 'left');
        $this->db->where('u.group_id', 4);
        if($countryCode != ''){
            $this->db->where('u.group_id', 4);
        }
        $this->db->group_by('u.id');
        $q = $this->db->get();
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row->duration = $row->duration ? $row->duration : 0;
                $data[] = $row;
            }
           
            return $data;
        }
        return false;
    }
	
}
