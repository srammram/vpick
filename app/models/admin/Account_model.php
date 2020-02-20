<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
	function checkImport($transaction_no){
		
		$this->db->where_in('transaction_no', $transaction_no);
		$q = $this->db->get('account_bankexcel_list');
		if($q->num_rows()>0){
			return true;
		}
		return false; 
	}
	function import_bank_excel($bank_array, $items, $is_country){
		$this->db->insert('account_bankexcel', $bank_array);
		if($bankexcel_id = $this->db->insert_id()){
			foreach($items as $val){
				
				$val['bankexcel_id'] = $bankexcel_id;
				$this->db->insert('account_bankexcel_list', $val);
				
				$this->db->update('account', array('account_status' => 3), array('payment_mode' => 1, 'payment_type' => $bank_array['payment_type'], 'account_transaction_no' => $val['transaction_no'], 'account_status' => 1, 'account_type' => 1));
				
			}
			$q = $this->db->select('GROUP_CONCAT(id) as id')->where('is_country', $is_country)->where('account_status', 1)->where('account_type', 1)->where('payment_mode', 1)->where('payment_type', $bank_array['payment_type'])->get('account');
			if($q->num_rows()>0){	
				$this->db->where('account_type', 1);
				$this->db->where_in('id', explode(',', $q->row('id')));			
				$this->db->update('account', array('account_status' => 2));
			}
			
			return true;	
		}
		return false;	
	}
	
	function addMoneyOffline($account_id, $account_array, $payment_array, $wallet_array,  $countryCode){
		
		if(!empty($account_array)){
			$this->db->update('account', $account_array, array('id' => $account_id));
			
			
			$this->db->insert('wallet', $wallet_array);
			if($wallet_id = $this->db->insert_id()){
				$payment_array['method_id'] = $wallet_id;
				$this->db->insert('multiple_gateway', $payment_array);
				$this->db->update('account', array('type_id' => $wallet_id), array('id' => $account_id));
			}
			
				
			
			return true;
		}
		return false;	
	}
	
	function getOfflineaccount($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('account');
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;
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
	
	function getBranch($countryCode){
		$this->db->select('*');
		$this->db->where('is_office', 1);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('company');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	function getHeadOffice($countryCode){
		$this->db->select('*');
		$this->db->where('is_office', 0);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('company');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
		
	}
	
	function getReconcilation($countryCode){
		$this->db->select('id, account_transaction_no, account_transaction_date, debit');
		$this->db->where('payment_mode', 1);
		$this->db->where('account_type', 1);
		$this->db->where('account_status', 2);
		$this->db->where('user_type', 0);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('account');
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	}
	
	function branchSettlement($settlement, $account_ids, $countryCode){
		
		$this->db->insert('settlement', $settlement);
		if($id = $this->db->insert_id()){
			$this->db->where_in('id', $account_ids);
			$this->db->update('account', array('account_status' => 4, 'settlement_id' => $id));
	    	return true;
		}
		return false;
	}
	
	function getSettlement($id){
		$this->db->select('s.id, s.is_country,s.from_company_id, s.from_bank_id, s.to_company_id, s.to_bank_id, s.settlement_date, s.settlement_status, s.settlement_code, s.settlement_type, s.settlement_amount, fu.first_name as from_user, fc.name as from_company, fb.account_no as from_account_no, fb.bank_name as from_bank_name, tu.first_name as to_user, tc.name as to_company, tb.account_no as to_account_no, tb.bank_name as to_bank_name, s.bank_challan');
		$this->db->from('settlement s')
			->join("admin_bank fb", "fb.id = s.from_bank_id ", 'left')
			->join("users fu", "fu.id = s.from_user_id ", 'left')
			->join("company fc", "fc.id = s.from_company_id ", 'left')
			->join("admin_bank tb", "tb.id = s.to_bank_id ", 'left')
			->join("users tu", "tu.id = s.to_user_id ", 'left')
			->join("company tc", "tc.id = s.to_company_id ", 'left');
		$this->db->where('s.id', $id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
			
	}
	
	function branchSettlementverify($settlement, $cash_array, $id, $countryCode){
		$this->db->where('id', $id);
		if($this->db->update('settlement', $settlement)){
			$this->db->insert('account', $cash_array);
			$this->db->where('settlement_id', $id);
			$this->db->update('account', array('account_status' => 3));
			return true;	
		}
		return false;	
	}
	
	
}
