<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->email_table = 'mail_templates';
		$this->sms_table = 'sms_templates';
		
    }
    function add_email_notification($data){
		$data['is_country'] = $countryCode;
	$this->db->insert($this->email_table, $data);//print_r($this->db->error());exit;
        return $id = $this->db->insert_id();
    }
    function update_email_notification_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->email_table,$data)){
	    return true;
	}
	return false;
    }
    function get_email_template($id){
	$q = $this->db->get_where($this->email_table,array('id'=>$id));//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
    function update_email_notification($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->email_table,$data)){
	    return true;
	}
	return false;
    }
    /********************** SMS notifications ***********************/
    function add_sms_notification($data){
		$data['is_country'] = $countryCode;
	$this->db->insert($this->sms_table, $data);//print_r($this->db->error());exit;
        return $id = $this->db->insert_id();
    }
    function update_sms_notification_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->sms_table,$data)){
	    return true;
	}
	return false;
    }
    function get_sms_template($id){
	$q = $this->db->get_where($this->sms_table,array('id'=>$id));//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
    function update_sms_notification($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->sms_table,$data)){
	    return true;
	}
	return false;
    }
    function my_is_unique($type,$value,$field,$table){
	$q = $this->db->get_where($table,array('user_type'=>$type,$field=>$value));
	if($q->num_rows()>0){
	    return true;
	}
	return false;
    }
    
	function getALLNotification(){
		$this->db->select(" u.first_name, n.title, n.message")
            ->from("notification n")
			->join("users u", " u.id = n.user_id AND u.is_country = '".$countryCode."'", "left")
			->where("n.user_type = 4");	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
	
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;
	}
   
	
}
