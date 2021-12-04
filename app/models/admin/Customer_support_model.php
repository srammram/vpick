<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_support_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
	$this->table = 'customer_support';
	$this->table_tickets = 'customer_support_tickets';
    }
    function getCustomerQueries($limit,$offset){
	$this->db->start_cache();
	$this->db
	    ->select("st.id as id,ticket,c.first_name as name,content,st.status as status")
            ->from("customer_support_tickets st")
	    ->join("customer_support s",'s.ticket_id=st.id')
	    ->join("customers c",'c.id=st.customer_id')
	    ->where("parent_id",0);
	$this->db->stop_cache();
	$t = $this->db->get()->num_rows();
	$this->db->limit($limit,$offset);
	$q = $this->db->get();
	$this->db->flush_cache();
	if($q->num_rows()>0){
	    $data = $q->result();	    
	    foreach($data as $k => $row){
		
		$data[$k]->unread = 0;
		$unread = $this->db->get_where($this->table,array('ticket_id'=>$row->id,'msg_read'=>0,'sent_by'=>'customer'))->num_rows();
		if($unread){
		    $data[$k]->unread = 1;
		}
	    }
	    return array('data'=>$q->result(),'total'=>$t);
	}
	return false;
    }
    function update_ticket_status($data,$id){
	$this->db->where('id',$id);
	if($this->db->update($this->table_tickets,$data)){
	    return true;
	}
	return false;	
    }
    function update_query_status($id){
	$data['msg_read'] = 1;
	$this->db->where('ticket_id',$id);
	if($this->db->update($this->table,$data)){
	    return true;
	}
	return false;	
    }
     function add_reply($data){
	
	$this->db->insert($this->table, $data);//print_R($this->db->error());
        if( $this->db->insert_id()){
	    $update['updated_on'] =  date('Y-m-d H:i:s');	    
	    $this->db->where('id',$data['ticket_id']);
	    $this->db->update($this->table_tickets,$update);
	    return true;
	}
	
    }
    function getTicket($ticket){
	$q = $this->db
	    ->select('*')
	    ->from($this->table_tickets)
	    ->where('ticket',$ticket)
	    ->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
    function getCustomerQueries_byticket($ticket_id){
	$q = $this->db
	    ->select('*')
	    ->from($this->table)
	    ->where('ticket_id',$ticket_id)
	    ->get();
	if($q->num_rows()>0){
	    return $q->result();
	}
	return false;
    }
    function getfirstQuery_ticket($ticket_id){
	$q = $this->db
	    ->select('*')
	    ->from($this->table)
	    ->where('ticket_id',$ticket_id)
	    ->where('parent_id',0)
	    ->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
}
