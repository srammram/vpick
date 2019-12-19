<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
	function getUsersAllwithoutgroup($countryCode){
		$this->db->select('id, first_name, mobile');
		$this->db->where_in('group_id', array('3', '4', '5'));	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('users');
		if($q->num_rows()>0){
			$row = $q->result();
			return $row;
		}
		return false;
	}
	
	function getUsersAll($group_id, $countryCode){
		$this->db->select('id, first_name');
		$this->db->where('group_id', $group_id);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('users');
		if($q->num_rows()>0){
			$row = $q->result();
			return $row;
		}
		return false;
	}
	
	function getTaxdefault($countryCode){
		
		$this->db->select('*')	;
		$this->db->where('is_default', 1);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('tax');
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;
	}
	
	function getPayment($driver_id, $countryCode){
		
		$image_path = base_url('assets/uploads/');
		$this->db->select('*')	;
		$this->db->where('driver_id', $driver_id);
		$this->db->where('is_edit !=', 0);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('driver_payment');
		
		if($q->num_rows()>0){
			$row = $q->row();
			
			if($row->transaction_image !=''){
				$row->transaction_image = $image_path.$row->transaction_image;
			}else{
				$row->transaction_image = $image_path.'no_image.png';
			}
			return $row;
		}
		return false;
	}
	
	
	
	function updateVerifiedpayment($driver_payment_id, $update, $driver_id, $countryCode){
		$this->db->where('id', $driver_payment_id);
		
		$q = $this->db->update('driver_payment', $update);
		if($q){
			$this->db->where('id', $driver_id);
			
			$this->db->update('users', array('driver_payment_status', 0));
			return true;
		}
		return false;
	}
	
	function updateCashpayment($driver_payment_id, $update, $payment_array, $driver_wallet, $admin_wallet, $countryCode){
		$this->db->where('id', $driver_payment_id);
		
		$q = $this->db->update('driver_payment', $update);
		
		if($q){
			if(!empty($payment_array)){
				$payment_array['is_country'] = $countryCode;
				$this->db->insert('multiple_gateway', $payment_array);
				
			}
			if(!empty($driver_wallet)){
				$this->site->Ridewallet($driver_wallet, $admin_wallet);
			}
			return true;
		}
		return false;
	}
	function updateCashpayment_live($driver_payment_id, $update, $payment_array, $driver_wallet, $admin_wallet, $countryCode){
		$this->db->where('id', $driver_payment_id);
		
		$q = $this->db->update('driver_payment', $update);
		
		if($q){
			if(!empty($payment_array)){
				$payment_array['is_country'] = $countryCode;
				$this->db->insert('multiple_gateway', $payment_array);
				
			}
			if(!empty($driver_wallet)){
				$this->site->Ridewallet($driver_wallet, $admin_wallet);
			}
			return true;
		}
		return false;
	}
	
	function getDriverBYId($id, $countryCode){
		$image_path = base_url('assets/uploads/');
		$this->db->select('*');
		$this->db->where('id', $id);
		
		$q = $this->db->get('users');
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->photo !=''){
				$row->photo_img = $image_path.$row->photo;
			}else{
				$row->photo_img = $image_path.'no_image.png';
			}
			return $row;	
		}
		return false;
	}
	
	
	function getBanks($countryCode){
		$this->db->select('*');	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('admin_bank');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	function getPaymentmode($countryCode){
		$this->db->select('*');
		$q = $this->db->get('payment_mode');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	function getPaymentgateway($countryCode){
		$this->db->select('*');
		$q = $this->db->get('payment_gateway');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	function getDriverPaymentGateway($id){
		$image_path = base_url('assets/uploads/');
		$this->db->select('*')	;
		$this->db->where('id', $id);
		$this->db->where('driver_status', 2);
		$q = $this->db->get('driver_payment');
		if($q->num_rows()>0){
			$row = $q->row();
			
			
			return $row;
		}
		return false;
	}
	
	function getPaymentBYId($id, $countryCode){
		$image_path = base_url('assets/uploads/');
		$this->db->select('*');
		$this->db->where('driver_id', $id);
		$this->db->where('is_edit', 1);
		$q = $this->db->get('driver_payment');
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->transaction_image !=''){
				$row->transaction_image = $image_path.$row->transaction_image;
			}else{
				$row->transaction_image = $image_path.'no_image.png';
			}
			return $row;	
		}
		return false;
	}
	
}
