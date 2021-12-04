<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	/*function addMoney($data){
		$q  = $this->db->insert('wallet', $data);
		if($q){

			return true;			
		}
		return false;
	}*/
	
	function addMoneyOnlineAccount($group_id, $cash_array, $wallet_array, $payment_array, $transaction_status, $is_country){
		if($transaction_status == 'Success'){
		if(!empty($cash_array)){
			$this->db->insert_batch('account', $cash_array);
			$this->db->insert_batch('wallet', $wallet_array);
			$this->db->insert('onlinepayment', $payment_array);
			return true;			
		}
		}else{
			$this->db->insert('onlinepayment', $payment_array);
			
			return true;	
		}
		return false;
	}
	
	function addMoneyOfflineAccount($group_id, $cash_array, $wallet_array, $is_country){
		
		if(!empty($cash_array)){
			$this->db->insert_batch('account', $cash_array);
			$this->db->insert_batch('wallet', $wallet_array);
			return true;			
		}
		return false;
	}
		
	function addMoneyOffline($group_id, $payment_mode, $payment_gateway, $account_array, $payment_array, $wallet_array,  $countryCode, $user_id){
		
		if(!empty($account_array)){
			$this->db->insert('account', $account_array);
			
			$account_id = $this->db->insert_id();
			if($group_id == 1 || $group_id == 2){
				$this->db->insert('wallet', $wallet_array);
				if($wallet_id = $this->db->insert_id()){
					$payment_array['method_id'] = $wallet_id;
					$this->db->insert('multiple_gateway', $payment_array);
					$this->db->update('account', array('type_id' => $wallet_id, 'account_status' => 3), array('id' => $account_id));
				}
				/*if($group_id == 4 || $group_id == 5){
					$this->site->adminUserDebit($countryCode, 2, 1, $account_array['credit'], $user_id);
				}*/
			}
			return true;
		}
		return false;	
	}

	function addMoneyCashwallet($user_id, $wallet_array, $payment_array,  $countryCode, $transaction_status){
		if($transaction_status == 'Success'){
			
			$this->db->insert('wallet', $wallet_array);
			if($wallet_id = $this->db->insert_id()){
				$payment_array['method_id'] = $wallet_id;
				$this->db->insert('multiple_gateway', $payment_array);
				return true;
			}
		}else{
			$this->db->insert('multiple_gateway', $payment_array);
			return true;
		}
		return false;	
	}
	
	function getWalletTotal($countryCode){
		
		$this->db->select('*');
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get('wallet');
		
		
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				if($row->user_type == 0){
					$owner_credit[] = $row->cash;
				}elseif($row->user_type == 1){
					$customer_credit[] = $row->cash;
				}elseif($row->user_type == 2){
					$driver_credit[] = $row->cash;
				}elseif($row->user_type == 3){
					$vendor_credit[] = $row->cash;
				}
			}
			$data = array(
				'owner' => array_sum($owner_credit) ? array_sum($owner_credit) : 0,
				'customer' => array_sum($customer_credit) ? array_sum($customer_credit) : 0,
				'vendor' => array_sum($vendor_credit) ? array_sum($vendor_credit) : 0,
				'driver' => array_sum($driver_credit) ? array_sum($driver_credit) : 0
			);		
			
			return $data;
			
		}else{
			$data = array(
				'owner' => 0,
				'customer' => 0,
				'vendor' => 0,
				'driver' => 0
			);			
			return $data;
		}
	
		
	}
	
	function getIncentiveby_ID($id, $countryCode){
		$this->db->select('d.*, c.name as city_name, c.state_id, s.zone_id, z.country_id, cc.continent_id');
		$this->db->from('incentive d');
		$this->db->join('cities c', 'c.id = d.city_id', 'left');
		$this->db->join('states s', 's.id = c.state_id', 'left');
		$this->db->join('zones z', 'z.id = s.zone_id', 'left');
		$this->db->join('countries cc', 'cc.id = z.country_id', 'left');
		$this->db->where('d.id',$id);

		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
    }
	
	function getALLCustomerE($countryCode){
		 $this->db
            ->select("w.id as id, w.created,  u.first_name as fname, u.last_name as lname, w.cash as cash, w.description as description, 
			(CASE WHEN w.flag = '1' THEN  'Incentive' WHEN w.flag = '2' THEN   'Rides'  WHEN w.flag = '3' THEN 'Refunded'  WHEN w.flag = '4' THEN 'Deduction'  WHEN w.flag = '5' THEN 'Transfer' ELSE '' END) as type,
			
			 
			
			")
           
			
			 ->from("wallet w")
			->join("users u", "u.id = w.user_id 
			"
)
			->where("u.group_id", 5);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getALLDriverE($countryCode){
		 $this->db
            ->select("w.id as id, w.created,  u.first_name as fname, u.last_name as lname, w.cash as cash, w.description as description, 
			(CASE WHEN w.flag = '1' THEN  'Incentive' WHEN w.flag = '2' THEN   'Rides'  WHEN w.flag = '3' THEN 'Refunded'  WHEN w.flag = '4' THEN 'Deduction'  WHEN w.flag = '5' THEN 'Transfer' ELSE '' END) as type,
			
			 
			
			")
           
			
			 ->from("wallet w")
			->join("users u", "u.id = w.user_id "
)
			->where("u.group_id", 4);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getALLVendorE($countryCode){
		 $this->db
            ->select("w.id as id, w.created,  u.first_name as fname, u.last_name as lname, w.cash as cash, w.description as description, 
			(CASE WHEN w.flag = '1' THEN  'Incentive' WHEN w.flag = '2' THEN   'Rides'  WHEN w.flag = '3' THEN 'Refunded'  WHEN w.flag = '4' THEN 'Deduction'  WHEN w.flag = '5' THEN 'Transfer' ELSE '' END) as type,
			
			 
			
			")
           
			
			 ->from("wallet w")
			->join("users u", "u.id = w.user_id ")
			->where("u.group_id", 3);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getALLOwnerE($countryCode){
		 $this->db
            ->select("w.id as id, w.created,  u.first_name as fname, u.last_name as lname, w.cash as cash, w.description as description, 
			(CASE WHEN w.flag = '1' THEN  'Incentive' WHEN w.flag = '2' THEN   'Rides'  WHEN w.flag = '3' THEN 'Refunded'  WHEN w.flag = '4' THEN 'Deduction'  WHEN w.flag = '5' THEN 'Transfer' ELSE '' END) as type,
			
			 
			
			")
           
			
			 ->from("wallet w")
			->join("users u", "u.id = w.user_id  ")
			->where("u.group_id", 2);	
		if($this->session->userdata('group_id') == 1 && $countryCode != ''){
			$this->db->where('is_country', $countryCode);
		}elseif($this->session->userdata('group_id') != 1){
			$this->db->where('is_country', $countryCode);
		}
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
			if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
					$data[] = $row;
					
				}
				return $data;
			}
		return false;	
	}
	
	function getWalletOffer($countryCode){
		$this->db->select('id, name, amount, offer_amount, type')->where('is_country', $countryCode);
		$q = $this->db->get('walletoffer');
		
		if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					
						$data[] = $row;
					
					
				}
				
				return $data;
			}
		return false;
	}
	
	function getUsers($group_id, $countryCode){
		$this->db->select("u.id, u.first_name, u.country_code, u.mobile, u.email, If(u.complete_user = 1 && t.complete_taxi = 1 , '1', '0') as join_type")->from('users u')->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1 ", 'left')->where('u.active', 1)->where('u.group_id', $group_id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
				foreach (($q->result()) as $row) {
					if($group_id != 4){
						$data[] = $row;
					}else{
					if($row->join_type == 1){
						$data[] = $row;
					}
					}
				}
				
				return $data;
			}
		return false;
	}
	
}
