<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_support_api extends CI_Model
{
	public $tables = array();
	protected $_ion_hooks;
	var $limit;
	public function __construct() {
	    parent::__construct();
	    $this->load->config('ion_auth', TRUE);
	    $this->limit = 10;
	    $this->table = 'customer_support';
	    $this->table_tickets = 'customer_support_tickets';
	}
	function add_query($data){
		$t_data['ticket'] = $data['ticket'];
		$t_data['customer_id'] = $data['customer_id'];
		$t_data['status'] = 1;
		$t_data['created_on'] = date('Y-m-d H:i:s');
		$this->db->insert($this->table_tickets, $t_data);//print_R($this->db->error());exit;
		$id =  $this->db->insert_id();
		if($id){
			$c_data['content'] = $data['content'];
			$c_data['ticket_id'] = $id;
			$c_data['sent_by'] = 'customer';
			$c_data['sent_by_id'] = $data['customer_id'];
			$c_data['created_on'] = date('Y-m-d H:i:s');
			$this->db->insert($this->table, $c_data);//print_R($this->db->error());exit;
			return $cid =  $this->db->insert_id();
		}
		return false;
	}
	function reply_query($data){
		
		$q = $this->db
		->from($this->table_tickets)
		->where('ticket',$data['ticket'])//echo $this->db->get_compiled_select();exit;
		->get();
		$ticket = $q->row();echo $q->num_rows();
		$t = $this->db->get_where($this->table,array('ticket_id'=>$ticket->id,'parent_id'=>0));
		$p_query = $t->row();
		$c_data['content'] = $data['content'];
		$c_data['ticket_id'] = $ticket->id;
		$c_data['parent_id'] = $p_query->id;
		$c_data['sent_by'] = 'customer';
		$c_data['sent_by_id'] = $data['customer_id'];
		$c_data['msg_read'] = 0;
		$c_data['created_on'] = date('Y-m-d H:i:s');
		//print_R($c_data);exit;
		$this->db->insert($this->table, $c_data);//print_R($this->db->error());exit;
		if($cid =  $this->db->insert_id()){
			$update['updated_on'] =  date('Y-m-d H:i:s');
			
			$this->db->where('id',$ticket->id);
			$this->db->update($this->table_tickets,$update);			   
			return true;
		}
		
		return false;
	}
	
	
}
