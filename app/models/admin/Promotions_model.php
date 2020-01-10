<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotions_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    function add_promotion($data){
	$this->db->insert('promotions', $data);//print_R($this->db->error());exit;
        return $id = $this->db->insert_id();
    }
    function getPromotionby_ID($id){
	$this->db
	    ->select('p.*')
	    ->from('promotions p')
	    ->where(array('id'=>$id));
	$q = $this->db->get();
	if($q->num_rows()>0){
	    return $q->row();
	}
	return false;
    }
    function update_promotion($id,$data){
	$this->db->where('id',$id);
	$this->db->update('promotions',$data);
	return true;
    }
    function update_promotion_status($data,$id){//echo $id;exit;
	$this->db->where('id',$id);
	if($this->db->update('promotions',$data)){
	    return true;
	}
	return false;
    }
    function search_customers($data,$limit=false,$offset=false){
	//print_R($data);exit;
	$this->db->start_cache();
	$this->db
	    ->select('c.*,pc.id applied')
	    ->from('customers c')
	    ->join('rides r','r.customer_id=c.id','left')
	    ->join('promotion_customers pc','pc.customer_id=c.id','left');
	if(isset($data['uninstalled_app'])) : 
	    $this->db->where('uninstalled_app',1);
	elseif(@$data['customer_email']!='') :
	    $this->db->where('c.email',$data['customer_email']);
	elseif(@$data['customer_mobile']) :
	     $this->db->where('c.contact_number',$data['customer_mobile']);
	elseif(@$data['customer_type']!='' || @$data['no_of_bookings']!='' || @$data['start_date']!='' || @$data['end_date']!='') :
	    if(@$data['customer_type']!=''){
		$this->db->where('c.customer_type',$data['customer_type']);
	    }
	    if(@$data['no_of_bookings']!=''){
		$this->db->where('c.no_of_bookings',$data['no_of_bookings']);
	    }
	    if(@$data['start_date']!=''){
		$this->db->where('DATE(r.booked_on) >=',$data['start_date']);
	    }
	    if(@$data['end_date']!=''){
		$this->db->where('DATE(r.booked_on) <=',$data['end_date']);
	    }
	endif;
	$this->db->group_by('c.id');
	$this->db->stop_cache();
	$t = $this->db->get();
	if($limit){
	    $this->db->limit($limit,$offset);
	}
	
	$q = $this->db->get();
	$this->db->flush_cache();
	//if($q->num_rows()>0){
	    $ids = array_column($t->result_array(), 'id');
	    return array('customers'=>$q->result(),'total'=>$t->num_rows(),'all_customers'=>$ids);
	//}
	//return false;
	
    }
    function apply_coupon($promotion_id,$ids,$unselected_ids){
	$return = array();
	if($promotion_id!='' && !empty($ids)){
	    foreach($ids as $val){
		$data['promotion_id'] = $promotion_id;
		$data['customer_id'] = $val;
		$query = 'INSERT INTO '.$this->db->dbprefix("promotion_customers").' (promotion_id,customer_id) VALUES ('.$promotion_id.','.$val.')
  ON DUPLICATE KEY UPDATE customer_id='.$val;
  $this->db->query($query);//print_R($this->db->error());
		array_push($return,$val);
	    }
	}
	if(!empty($unselected_ids)){
	    $this->db->where_in('customer_id',$unselected_ids);
	    $this->db->where('promotion_id',$promotion_id);
	    $this->db->delete('promotion_customers');
	}
	
	return $return;
    }
    function remove_coupon($promotion_id,$ids,$all){
	if($all){
	    $this->db->where('promotion_id',$promotion_id);
	    $this->db->delete('promotion_customers');
	}else{
	    if(!empty($ids)){
		$this->db->where_in('customer_id',$ids);
		$this->db->where('promotion_id',$promotion_id);
		$this->db->delete('promotion_customers');
	    }
	}
	return true;
    }
    
	
}
