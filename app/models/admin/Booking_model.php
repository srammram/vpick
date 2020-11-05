<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function add_booking($data){
	$this->db->insert('rides', $data);//print_R($this->db->error());exit;
        if($id = $this->db->insert_id()){
	    $this->db->where('id',$data['driver_id']);
	    $driver['mode'] = 'booked';
	    $this->db->update('drivers',$driver);		
	    return $id;
	}
    }
}
