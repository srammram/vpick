<?php defined('BASEPATH') OR exit('No direct script access allowed');

class drivers_api extends CI_Model
{
	public $tables = array();
	protected $_ion_hooks;
	var $limit;
    public function __construct() {
        parent::__construct();
    	$this->load->config('ion_auth', TRUE);
		$this->limit = 10;
		$this->load->library('ride');
    }
	
	function test(){
		$this->ride->ridecurl('welcome');
	}
	
	function GETcredit_balance($user_id, $countryCode){
		$query = "SELECT coalesce(SUM(cash),0) as cash   FROM {$this->db->dbprefix('wallet')} WHERE user_id = ".$user_id." AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			return $q->row('cash');
		}
		return false;	
	}
	
	function GETincentive_balance($user_id, $countryCode){
		$query = "SELECT coalesce(SUM(cash),0) as cash   FROM {$this->db->dbprefix('wallet')} WHERE user_id = ".$user_id." AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			return $q->row('cash');
		}
		return false;
	}
	
	function getTaxinameBYID($id, $countryCode){
		$q = $this->db->select('name')->where('id', $id)->where('is_country', $countryCode)->get('taxi_make');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaximodelBYID($id, $countryCode){
		$q = $this->db->select('name')->where('id', $id)->where('is_country', $countryCode)->get('taxi_model');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	function getTaxitypeBYID($id, $countryCode){
		$q = $this->db->select('name')->where('id', $id)->where('is_country', $countryCode)->get('taxi_type');
		if($q->num_rows()>0){
			return $q->row('name');	
		}
		return false;
	}
	
	function getModelbymake_type($make_id, $type_id, $countryCode){
		$q = $this->db->select('id, name')->where('type_id', $type_id)->where('is_country', $countryCode)->where('make_id', $make_id)->get('taxi_model');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxi($countryCode){
		$q = $this->db->select('id, name')->where('is_country', $countryCode)->get('taxi_make');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function NewgetTaxitype($countryCode){
		$q = $this->db->select('id, name')->where('is_country', $countryCode)->get('taxi_type');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	function getIncentivebyID($incentive_id, $user_id, $countryCode){
		$this->db->select('*')->where('id', $incentive_id)->where('is_country', $countryCode);
		$q = $this->db->get('incentive');
		if ($q->num_rows() > 0) {
			return $q->row();	
		}
		return false;	
	}
	
	function changeIncentive($user_id, $incentive_id, $incentive, $countryCode){
		$this->db->select('id');
		$this->db->where('is_edit', 1);
		$this->db->where('is_country', $countryCode);
		$this->db->where('driver_id', $user_id);
		$q = $this->db->get('incentive_driver');
		if ($q->num_rows() > 0) {
			$this->db->update('incentive_driver', array('is_edit' => 0, 'cancel_status' => 1), array('id' => $q->row('id'), 'is_country' => $countryCode));
			$incentive['is_country'] = $countryCode;
			$this->db->insert('incentive_driver', $incentive);
			return true;
		}else{
			$incentive['is_country'] = $countryCode;
			$this->db->insert('incentive_driver', $incentive);
			
			return true;
		}
		return false;
	}
	function getDriverPayment($user_id, $payment, $paid_amount, $countryCode){
		$this->db->where('is_edit', 1);
		$this->db->where('driver_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('driver_payment', $payment);
		if($q){
			
			$this->db->insert('wallet', array('user_id' => $user_id, 'flag' => 4, 'flag_method' => 14, 'cash' => round($paid_amount), 'description' => 'Driver Payment to Admin', 'created_on' => date('Y-m-d H:i:s'), 'user_type' => 2, 'wallet_type' => 1, 'is_country' => $countryCode));
			return true;	
		}
		return false;
	}
			
	function getDriverPaymentOnline($user_id, $countryCode){
		$this->db->select('id as driver_payment_id, transaction_no, payment_date, payment_note, payment_amount, ride_start_date as due_date');
		$this->db->from('driver_payment');
		$this->db->where('driver_id', $user_id);
		$this->db->where('is_edit', 0);
		$this->db->where('is_country', $countryCode);
		$this->db->where_in('payment_status', array('1', '3'));
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				 if(empty($row->payment_note)){
					$row->payment_note = 'due date('.$row->due_date.') has been paid';
				 }
				 $data[]  = $row;
			 }
			 return $data;
		}
		return false;
	}
	
	function getDriverPaymentOffline($user_id, $countryCode){
		$this->db->select('id as driver_payment_id, transaction_no, payment_date, payment_note, payment_amount, ride_start_date as due_date');
		$this->db->from('driver_payment');
		$this->db->where('driver_id', $user_id);
		$this->db->where('is_edit', 0);
		$this->db->where('is_country', $countryCode);
		$this->db->where('payment_status', 2);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				  if(empty($row->payment_note)){
					 $row->payment_note = 'due date('.$row->due_date.') has been paid';
				 }
				 $data[]  = $row;
			 }
			 return $data;
		}
		return false;
	}
	
	
	function getIncentive($user_id, $countryCode){
		$data = array();
		$data['ongoing'] = array();
		$data['complete'] = array();
		
		$this->db->select('i.incentive_name, d.incentive_id,   d.incentive_type as type, d.target_fare, d.target_ride, d.complete_fare,  d.complete_ride, d.status, d.complete_date as created_on, i.end_date, d.is_edit, i.start_time, i.end_time');
		$this->db->from('incentive_driver d');
		$this->db->join('incentive i', 'i.id = d.incentive_id AND i.is_country = "'.$countryCode.'"', 'left');
		$this->db->where('d.driver_id', $user_id);
		$this->db->where('d.is_country', $countryCode);
		//$this->db->where('d.is_edit', 0);
		$this->db->where('d.cancel_status', 0);
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$ongoing_count = 0;
			 foreach (($q->result()) as $row) {
				 $incentive_ids[] = $row->incentive_id;
				 if($row->is_edit == 1){
					 if($row->type == 1){
					 $row->complete_percentage = round((($row->complete_fare / $row->target_fare ) * 100));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 
					 $row->type_name = 'Fare';
					 
				 }elseif($row->type == 2){
					 $row->complete_percentage = round( (($row->complete_ride / $row->target_ride ) * 100));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 
					 $row->type_name = 'Ride';
				 }elseif($row->type == 3){
					 $row->complete_percentage = round((($row->complete_fare / $row->target_fare ) * 50) + (($row->complete_ride / $row->target_ride ) * 50));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 $row->type_name = 'Fare and Ride';
				 }
				 if($row->status == 1){
					 
					 $row->status_name = 'Complete';
					 $row->description = 'Complete your incentive';
					$data['complete'][] = $row;
					
				 }else{
					 $ongoing_count++;
					 $row->status_name = 'Ongoing';
					  $row->description = 'Ongoing your incentive';
					 $data['ongoing'][] = $row;
				 }
				 }else{
				
				 if($row->type == 1){
					 $row->complete_percentage = round((($row->complete_fare / $row->target_fare ) * 100));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 
					 $row->type_name = 'Fare';
					 
				 }elseif($row->type == 2){
					 $row->complete_percentage = round( (($row->complete_ride / $row->target_ride ) * 100));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 
					 $row->type_name = 'Ride';
				 }elseif($row->type == 3){
					 $row->complete_percentage = round((($row->complete_fare / $row->target_fare ) * 50) + (($row->complete_ride / $row->target_ride ) * 50));
					 if($row->complete_percentage >= 100){
						 $row->target_complete = '100';
						 $row->target_pending = '0';
					 }else{
						 $row->target_complete = (string)$row->complete_percentage;
						 $row->target_pending = (string)(100 - $row->complete_percentage);
					 }
					 $row->type_name = 'Fare and Ride';
				 }
				 if($row->status == 1){
					 
					 $row->status_name = 'Complete';
					 $row->description = 'Complete your incentive';
					$data['complete'][] = $row;
					
				 }else{
					 
					 $ongoing_count++;
					 $row->status_name = 'Ongoing';
					  $row->description = 'Ongoing your incentive';
					 $data['ongoing'][] = $row;
				 }
				 
				 }
				 
			 }
		}
		
		$current_date = date('Y-m-d');
		$current_day = date('D').'day';
		
		if(!empty($current_date)){
			$this->db->select('id as incentive_id, incentive_name, type,  target_fare, target_ride, created_on, end_date,  start_time, end_time');
			$this->db->where('date_type', 1);
			$this->db->where('start_date <=', $current_date);
			$this->db->where('end_date >=', $current_date);
			$this->db->where('is_country', $countryCode);
			$this->db->where_not_in('id', $incentive_ids);
			$d = $this->db->get('incentive');
			//print_r($this->db->last_query());die;
			if ($d->num_rows() > 0) {
			 foreach (($d->result()) as $dow) {
				 $dow->complete_fare = '0.00';
				 $dow->complete_ride = '0';
				 if($dow->type == 1){
					 $dow->complete_percentage = round((($dow->complete_fare / $dow->target_fare ) * 100));
					 if($dow->complete_percentage >= 100){
						 $dow->target_complete = '100';
						 $dow->target_pending = '0';
					 }else{
						 $dow->target_complete = (string)$dow->complete_percentage;
						 $dow->target_pending = (string)(100 - $dow->complete_percentage);
					 }
					 
					 $dow->type_name = 'Fare';
					 
				 }elseif($dow->type == 2){
					 $dow->complete_percentage = round( (($dow->complete_ride / $dow->target_ride ) * 100));
					 if($dow->complete_percentage >= 100){
						 $dow->target_complete = '100';
						 $dow->target_pending = '0';
					 }else{
						 $dow->target_complete = (string)$dow->complete_percentage;
						 $dow->target_pending = (string)(100 - $dow->complete_percentage);
					 }
					 
					 $dow->type_name = 'Ride';
				 }elseif($dow->type == 3){
					 $dow->complete_percentage = round((($dow->complete_fare / $dow->target_fare ) * 50) + (($dow->complete_ride / $dow->target_ride ) * 50));
					 if($dow->complete_percentage >= 100){
						 $dow->target_complete = '100';
						 $dow->target_pending = '0';
					 }else{
						 $dow->target_complete = (string)$dow->complete_percentage;
						 $dow->target_pending = (string)(100 - $dow->complete_percentage);
					 }
					 $dow->type_name = 'Fare and Ride';
				 }
				 
				 
				 $dow->status = '0';
				 if($ongoing_count == 0){
				 		$dow->status_name = 'Available';
						$dow->description = 'Available your incentive';
				 }else{
					 $dow->status_name = 'Waiting';
					 $dow->description = 'Waiting your incentive';
				 }
					  
					 $data['ongoing'][] = $dow;
			 }
			}
		}
		
		if(!empty($current_day)){
			
			$this->db->select('id as incentive_id, incentive_name, type,  target_fare, target_ride, created_on,  start_time, end_time');
			$this->db->where('date_type', 0);
			$this->db->where('days', $current_day);
			$this->db->where('is_country', $countryCode);
			$this->db->where_not_in('id', $incentive_ids);
			$dd = $this->db->get('incentive');
			//print_r($this->db->last_query());die;
			if ($dd->num_rows() > 0) {
			 foreach (($dd->result()) as $ddow) {
				 $ddow->end_date = $current_date;
				  $ddow->complete_fare = '0.00';
				 $ddow->complete_ride = '0';
				 if($ddow->type == 1){
					 $ddow->complete_percentage = round((($ddow->complete_fare / $ddow->target_fare ) * 100));
					 if($ddow->complete_percentage >= 100){
						 $ddow->target_complete = '100';
						 $ddow->target_pending = '0';
					 }else{
						 $ddow->target_complete = (string)$ddow->complete_percentage;
						 $ddow->target_pending = (string)(100 - $ddow->complete_percentage);
					 }
					 
					 $ddow->type_name = 'Fare';
					 
				 }elseif($ddow->type == 2){
					 $ddow->complete_percentage = round( (($ddow->complete_ride / $ddow->target_ride ) * 50));
					 if($ddow->complete_percentage >= 100){
						 $ddow->target_complete = '100';
						 $ddow->target_pending = '0';
					 }else{
						 $ddow->target_complete = (string)$ddow->complete_percentage;
						 $ddow->target_pending = (string)(100 - $ddow->complete_percentage);
					 }
					 
					 $ddow->type_name = 'Ride';
				 }elseif($ddow->type == 3){
					 $ddow->complete_percentage = round((($ddow->complete_fare / $ddow->target_fare ) * 50) + (($ddow->complete_ride / $ddow->target_ride ) * 50));
					 if($ddow->complete_percentage >= 100){
						 $ddow->target_complete = '100';
						 $ddow->target_pending = '0';
					 }else{
						 $ddow->target_complete = (string)$ddow->complete_percentage;
						 $ddow->target_pending = (string)(100 - $ddow->complete_percentage);
					 }
					 $ddow->type_name = 'Fare and Ride';
				 }
				 
				 $ddow->status = '0';
				 		$ddow->status_name = 'Available';
					  $ddow->description = 'Available your incentive';
					  
					  
					  
					 $data['ongoing'][] = $ddow;
			 }
			}
		}
		
		if(!empty($data)){
			return $data;
		}
		return false;
		
	}
	function getUserSettings($user_id, $countryCode){
		$q = $this->db->select('incentive_auto_enable, ride_stop')->where('user_id', $user_id)->get('user_setting');
		if($q->num_rows()>0){
			return $q->row();	
		}
		return false;	
	}
	
	function updateSetting($user_id, $data, $countryCode){
		$check = $this->db->select('*')->where('user_id', $user_id)->get('user_setting');
		if($check->num_rows()>0){
			//$data['is_country'] = $countryCode;
			$this->db->where('user_id', $user_id);
			
			$q = $this->db->update('user_setting', $data);
			if($q){
				return true;	
			}
		}else{
			$data['user_id'] = $user_id;
			$array2 = array_map(function($value) { return $value === NULL ? 0 : $value;}, $data);
			$q = $this->db->insert('user_setting', $array2);
			if($q){
				return true;	
			}
		}
		
		return false;	
	}
	
	function addMoneyCashwallet($user_id, $wallet_array, $payment_array,  $countryCode, $transaction_status){
		if($transaction_status == 'Success'){
			$wallet_array['wallet_type'] = 1;
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
	
	function addSendCashwallet($user_id, $wallet_array, $payment_array,  $countryCode){
		$wallet_array['wallet_type'] = 1;
		$this->db->insert('wallet', $wallet_array);
        if($wallet_id = $this->db->insert_id()){
			$payment_array['wallet_id'] = $wallet_id;
			$payment_array['is_country'] = $countryCode;
			$this->db->insert('withdraw', $payment_array);
			return true;
		}
		return false;	
	}
	
	function transferWallet($user_id, $wallet_cash, $wallet_credit,  $countryCode){
		$wallet_cash['wallet_type'] = 1;
		$this->db->insert('wallet', $wallet_cash);
        if($wallet_id = $this->db->insert_id()){
			$wallet_credit['wallet_type'] = 2;
			$this->db->insert('wallet', $wallet_credit);
			return true;
		}
		return false;	
	}
	
	
	function getWalletList($user_id, $countryCode){
		$query = "Select 
		SUM(CASE When wallet_type='1' AND flag = '1' Then cash Else 0 End ) as CashIncentive,
		SUM(CASE When wallet_type='1' AND flag = '2' Then cash Else 0 End ) as CashRides,
		SUM(CASE When wallet_type='1' AND flag = '3' Then cash Else 0 End ) as CashRefunded,
		SUM(CASE When wallet_type='1' AND flag = '4' Then cash Else 0 End ) as CashDeduction,
		SUM(CASE When wallet_type='1' AND flag = '5' Then cash Else 0 End ) as CashTransfer,
		SUM(CASE When wallet_type='1' AND flag = '6' Then cash Else 0 End ) as CashAddMoney,
		SUM(CASE When wallet_type='1' AND flag = '7' Then cash Else 0 End ) as CashSentMoney,
		SUM(CASE When wallet_type='1' AND flag = '8' Then cash Else 0 End ) as CashOffer,
		SUM(CASE When wallet_type='1' AND flag = '9' Then cash Else 0 End ) as CashCommission,
		
		SUM(CASE When wallet_type='2' AND flag = '1' Then cash Else 0 End ) as CreditIncentive,
		SUM(CASE When wallet_type='2' AND flag = '2' Then cash Else 0 End ) as CreditRides,
		SUM(CASE When wallet_type='2' AND flag = '3' Then cash Else 0 End ) as CreditRefunded,
		SUM(CASE When wallet_type='2' AND flag = '4' Then cash Else 0 End ) as CreditDeduction,
		SUM(CASE When wallet_type='2' AND flag = '5' Then cash Else 0 End ) as CreditTransfer,
		SUM(CASE When wallet_type='2' AND flag = '6' Then cash Else 0 End ) as CreditAddMoney,
		SUM(CASE When wallet_type='2' AND flag = '7' Then cash Else 0 End ) as CreditSentMoney,
		SUM(CASE When wallet_type='2' AND flag = '8' Then cash Else 0 End ) as CreditOffer,
		SUM(CASE When wallet_type='2' AND flag = '9' Then cash Else 0 End ) as CreditCommission
		
		from {$this->db->dbprefix('wallet')}
		Where ( wallet_type='1' Or wallet_type='2' ) AND user_type = 2 AND user_id = ".$user_id." ";
		$q = $this->db->query($query);
		
		if($q->num_rows()>0){
			$CashPaymentAmount = $q->row('CashRides') + $q->row('CashRefunded') + $q->row('CashAddMoney') + $q->row('CashIncentive') +  $q->row('CashTransfer') - $q->row('CashDeduction') - $q->row('CashSentMoney') + $q->row('CashOffer') + $q->row('CashCommission');
			
			$CreditPaymentAmount = $q->row('CreditRides') + $q->row('CreditRefunded') + $q->row('CreditAddMoney') + $q->row('CreditIncentive') + $q->row('CreditTransfer') - $q->row('CreditDeduction') - $q->row('CreditSentMoney') + $q->row('CreditOffer') + $q->row('CreditCommission'); 
			
			$data = array(
				'CashPaymentAmount' => $CashPaymentAmount,
				'CreditPaymentAmount' => $CreditPaymentAmount
			);
			//CashPaymentAmount
			//CreditPaymentAmount
			return $data;
		}
		return false;	
	}
	function getTypeWallets($user_id, $wallet_type, $countryCode){
		$data = array();
		
		$setting  = $this->site->get_setting($countryCode);
		
		$this->db->select('id as transaction_id, wallet_type, flag, cash, description, created');
		$this->db->where('user_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$this->db->where('wallet_type', $wallet_type);
		$q = $this->db->get('wallet');
		$data['wallet'] = "0";
		$data['driverpaid'] = "0";
		$data['list'] = [];
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				//$wallet[] = $row->cash;
				if($row->flag == 1 ){
					$row->flag_name = 'Incentive';
				}elseif($row->flag == 2 ){
					$row->flag_name = 'Rides';
				}elseif($row->flag == 3 ){
					$row->flag_name = 'Refunded';
				}elseif($row->flag == 4 ){
					$row->flag_name = 'Deduction';
				}elseif($row->flag == 5 ){
					$row->flag_name = 'Transfer';
				}elseif($row->flag == 6 ){
					$row->flag_name = 'AddMoney';
				}elseif($row->flag == 7 ){
					$row->flag_name = 'SentMoney';
				}elseif($row->flag == 8 ){
					$row->flag_name = 'Offer';
				}elseif($row->flag == 9 ){
					$row->flag_name = 'Commision';
				}else{
					$row->flag_name = 'No';
				}
				
				if($row->wallet_type == 1 ){
					
					if($row->flag == 1 ){
						$wallet_cash_Incentive[] = $row->cash;
					}elseif($row->flag == 2 ){
						$wallet_cash_Rides[] = $row->cash;
					}elseif($row->flag == 3 ){
						$wallet_cash_Refunded[] = $row->cash;
					}elseif($row->flag == 4 ){
						$wallet_cash_Deduction[] = $row->cash;
					}elseif($row->flag == 5 ){
						$wallet_cash_Transfer[] = $row->cash;
					}elseif($row->flag == 6 ){
						$wallet_cash_AddMoney[] = $row->cash;
					}elseif($row->flag == 7 ){
						$wallet_cash_SentMoney[] = $row->cash;
					}elseif($row->flag == 8 ){
						$wallet_cash_Offer[] = $row->cash;
					}elseif($row->flag == 9 ){
						$wallet_cash_Commision[] = $row->cash;
					}
					
					//$wallet_cash[] = $row->cash;
					
					$data['cash_list'][] = $row;
				}elseif($row->wallet_type == 2 ){
					//$wallet_credit[] = $row->cash;
					
					if($row->flag == 1 ){
						$wallet_credit_Incentive[] = $row->cash;
					}elseif($row->flag == 2 ){
						$wallet_credit_Rides[] = $row->cash;
					}elseif($row->flag == 3 ){
						$wallet_credit_Refunded[] = $row->cash;
					}elseif($row->flag == 4 ){
						$wallet_credit_Deduction[] = $row->cash;
					}elseif($row->flag == 5 ){
						$wallet_credit_Transfer[] = $row->cash;
					}elseif($row->flag == 6 ){
						$wallet_credit_AddMoney[] = $row->cash;
					}elseif($row->flag == 7 ){
						$wallet_credit_SentMoney[] = $row->cash;
					}
					
					$data['credit_list'][] = $row;
				}
				
				$wallet_cash = array_sum($wallet_cash_Rides) + array_sum($wallet_cash_Incentive) + array_sum($wallet_cash_Refunded) + array_sum($wallet_cash_AddMoney) - array_sum($wallet_cash_Transfer) + array_sum($wallet_cash_Offer) + array_sum($wallet_cash_Commision) - array_sum($wallet_cash_Deduction) - array_sum($wallet_cash_SentMoney);
				
				$wallet_credit = array_sum($wallet_credit_Rides) + array_sum($wallet_credit_Incentive) + array_sum($wallet_credit_Refunded) + array_sum($wallet_credit_AddMoney) - array_sum($wallet_credit_Transfer) - array_sum($wallet_credit_Deduction) - array_sum($wallet_credit_SentMoney);
				
                
            }
			$data['wallet_cash'] = number_format($wallet_cash, 2);
			$data['wallet_credit'] = number_format($wallet_credit, 2);
           
		}
		
		$driver = $this->db->select('total_ride_amount')->where('driver_id', $user_id)->where('is_edit', 1)->where('driver_status', 0)->get('driver_payment');
		if ($driver->num_rows() > 0)
		{
			$data['driverpaid'] = ($driver->row('total_ride_amount') * $setting->driver_admin_payment_percentage) / 100;
			//$data['driverpaid'] = $driver->row('total_ride_amount');
		}
		
		//print_r($data);
		//die;
	
		if(!empty($data)){
			return $data;
		}
		return false;	
	}
	
	
	function getWallets($user_id, $countryCode){
		$data = array();
		$this->db->select('id as transaction_id, flag, cash, description, created');
		$this->db->where('user_id', $user_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('wallet');
		$data['wallet'] = "0";
		$data['driverpaid'] = "0";
		$data['list'] = [];
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				$wallet[] = $row->cash;
				if($row->flag == 1 ){
					$row->flag_name = 'Incentive';
				}elseif($row->flag == 2 ){
					$row->flag_name = 'Rides';
				}elseif($row->flag == 3 ){
					$row->flag_name = 'Refunded';
				}elseif($row->flag == 4 ){
					$row->flag_name = 'Deduction';
				}elseif($row->flag == 5 ){
					$row->flag_name = 'Transfer';
				}else{
					$row->flag_name = 'No';
				}
				
                $data['list'][] = $row;
            }
			$data['wallet'] = number_format(array_sum($wallet), 2);
           
		}
		
		$driver = $this->db->select('total_ride_amount')->where('driver_id', $user_id)->where('is_country', $countryCode)->where('is_edit', 1)->where('driver_status', 0)->get('driver_payment');
		if ($driver->num_rows() > 0)
		{
			$data['driverpaid'] = $driver->row('total_ride_amount');
		}
	
		if(!empty($data)){
			return $data;
		}
		return false;	
	}
	
	function getTickets($user_id, $countryCode){
		$this->db->select('e.id as enquiry_id, e.enquiry_status as status, e.enquiry_type, e.enquiry_code, e.enquiry_date,  IFNULL(h.name, 0) as help_title,  e.is_feedback');
		$this->db->from('enquiry e');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->where('e.customer_id', $user_id);
		$this->db->where('e.is_country', $countryCode);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				if($row->status == 3 ){
					$row->status = '1';
				}else{
					$row->status = '0';
				}
				
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	function addenquiryFeedback($value, $enquiry_id, $customer_id, $countryCode){
		$this->db->where('id', $enquiry_id);
		$this->db->where('customer_id', $customer_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('enquiry', array('is_feedback' => 1));
		if($q){
			$value['is_country'] = $countryCode;
			$this->db->insert('enquiry_feedback', $value);
			return false;
		}
		return false;
	}
	
	function getEnquiryView($user_id, $enquiry_id, $countryCode){
		$this->db->select('e.id as enquiry_id, e.enquiry_type, e.enquiry_code, e.enquiry_date, e.enquiry_status, e.customer_status as customer_feed_status,  IFNULL(r.booking_no, 0) as booking_no,  IFNULL(hs.name, 0) as crm_sub_name, IFNULL(hm.name, 0) as crm_main_name ');
		$this->db->from('enquiry e');
		$this->db->join('users u', 'u.id = e.customer_id', 'left');
		$this->db->join('groups g', 'g.id = u.group_id ', 'left');
		$this->db->join('help h', 'h.id = e.help_department', 'left');
		$this->db->join('rides r', 'r.id = e.services_id ', 'left');
		$this->db->join('users rd', 'rd.id = r.driver_id ', 'left');
		$this->db->join('users rc', 'rc.id = r.customer_id ', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id ', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id ', 'left');
		$this->db->join('help_sub hs', 'hs.id = e.help_id ', 'left');
		$this->db->join('help_main hm', 'hm.id = hs.parent_id ', 'left');
		
		$this->db->where('e.id',$enquiry_id);
		
		$q = $this->db->get();
		if($q->num_rows()>0){
			$data =  $q->row();
			if($data->enquiry_status == 0){
				$data->enquiry_status_name = 'Process';
			}elseif($data->enquiry_status == 1){
				$data->enquiry_status_name = 'Open';
			}elseif($data->enquiry_status == 2){
				$data->enquiry_status_name = 'Transfer';
			}elseif($data->enquiry_status == 3){
				$data->enquiry_status_name = 'Close';
			}elseif($data->enquiry_status == 4){
				$data->enquiry_status_name = 'Reopen';
			}
			
			
			return $data;
		}
		return false;
	}
	
	function getEnquiryFollow($user_id, $enquiry_id, $countryCode){
		$this->db->select('IFNULL(u.first_name, 0) as support_name,  es.status, f.created_on,  IFNULL(f.discussion, 0) as discussion, IFNULL(f.remark, 0) as remark, h.name as help_name');
		$this->db->from('enquiry_support es');
		$this->db->join('follows f', 'f.enquiryid = es.enquiry_id AND f.enquiry_support_id = es.id');
		$this->db->join('help h', 'h.id = es.help_services ', 'left');
		$this->db->join('users u', 'u.id = es.support_id ', 'left');
		$this->db->join('groups g', 'g.id = u.group_id ', 'left');
		$this->db->where('es.enquiry_id', $enquiry_id);
		
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				
					$row->discussion = strip_tags($row->discussion);
				
					$row->remark = strip_tags($row->remark);
					
					if($row->status == 0){
						$row->status_name = 'Process';
						$row->crm_title = 'Your ticket has been created';
					}elseif($row->status == 1){
						$row->status_name = 'Open';
						$row->crm_title = 'Your ticket has been allowcated to execute team';
					}elseif($row->status == 2){
						$row->status_name = 'Transfer';
						$row->crm_title = 'Your ticket has been transfer to another '.$data->help_name;
					}elseif($row->status == 3){
						$row->status_name = 'Close';
						$row->crm_title = 'Your issues has been closed';
					}elseif($row->status == 4){
						$row->status_name = 'Reopen';
						$row->crm_title = 'Your ticket has been Reopen';
					}
				
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	/**/
	function getEmergencycontact($data, $countryCode){
		
		$res = array();
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		
		if($q->num_rows()>0){
			if(!empty($q->row('help_number_one'))){
				$res[] = array('0' => $q->row('help_number_one'), '1' => '91' );
			}
			if(!empty($q->row('help_number_two'))){
				$res[] = array('0' => $q->row('help_number_two'), '1' => '91' );
			}
			if(!empty($q->row('help_number_three'))){
				$res[] = array('0' => $q->row('help_number_three'), '1' => '91' );
			}
			if(!empty($q->row('help_number_four'))){
				$res[] = array('0' => $q->row('help_number_four'), '1' => '91' );
			}
			if(!empty($q->row('help_number_five'))){
				$res[] = array('0' => $q->row('help_number_five'), '1' => '91' );
			}
			//print_r($res);die;
			return $res;
			
		}
		return false;
	}
	
	function currentRideSOS($data, $countryCode){
		$this->db->select('r.id as booking, r.booking_no, r.driver_id, r.taxi_id, r.customer_id, r.booked_type, r.status, r.booked_on, r.ride_timing, r.ride_type, r.start_lat, r.start_lng, r.end_lat, r.end_lng, dcs.current_latitude, dcs.current_longitude, c.first_name as customer_name, d.first_name as driver_name, t.name as taxi_name, t.number as taxi_number');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id ', 'left');
		$this->db->join('users d', 'd.id = r.driver_id ', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.id = r.driver_id ', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id ', 'left');
		$this->db->where('r.id', $data['booking_id']);
		
		//$this->db->where('r.customer_id', $data['user_id']);
		$q = $this->db->get();
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;
		}
		return false;
	}
	
	function getEmergencydata($id, $countryCode){
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('settings');
		if($q->num_rows()>0){
			$row[] = array('help_number_one' => $q->row('help_number_one'), 'help_number_two' => $q->row('help_number_two'), 'help_number_three' => $q->row('help_number_three'), 'help_number_four' => $q->row('help_number_four'), 'help_number_five' => $q->row('help_number_five'));
			return $row;
		}
		return false;	
	}
	
	function getHelpmain($user_id, $help, $countryCode){
		$this->db->select('hm.id as id, hm.name as name');
		$this->db->from('help h');
		$this->db->join('help_main hm', 'hm.parent_id = h.id AND hm.is_country = "'.$countryCode.'"', 'left');
		$this->db->where('h.name', $help);
		//$this->db->where('h.is_country', $countryCode);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->result();	
		}
		return false;
	}
	
	function getHelpsub($user_id, $parent_id, $countryCode){
		$this->db->select('id, name');
		$this->db->where('parent_id', $parent_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('help_sub');
		if ($q->num_rows() > 0) {
			 foreach (($q->result()) as $row) {
				$row->weblink = site_url('help');
                $data[] = $row;
            }
            return $data;
		}
		return false;
	}
	
	
	function getCancelDriverlocation($driver_id, $countryCode){
		$q = $this->db->select('current_latitude, current_longitude')->where('is_country', $countryCode)->where('driver_id', $driver_id)->where('is_connected', 1)->get('driver_current_status');
		//$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row = $q->row();
			return $row;	
		}
		return false;	
	}
	function getDriverdateHistory($driver_id, $countryCode){
		$current_date = date('Y-m-d');
		$week_date = date('Y-m-d', strtotime($current_date. ' - 7 days'));
		$month_date = date('Y-m-d', strtotime($current_date. ' - 30 days'));
		
		/*Rides*/
		$daily_rides = 0;
		$query_daily_rides = "SELECT COUNT(id) as total_rides FROM {$this->db->dbprefix('rides')} WHERE ride_timing BETWEEN '".$current_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_daily_rides = $this->db->query($query_daily_rides);
		if($q_daily_rides->num_rows()>0){
			$daily_rides = $q_daily_rides->row('total_rides');
		}
		$weekly_rides = 0;
		$query_weekly_rides = "SELECT COUNT(id) as total_rides FROM {$this->db->dbprefix('rides')} WHERE ride_timing BETWEEN '".$week_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_weekly_rides = $this->db->query($query_weekly_rides);
		if($q_weekly_rides->num_rows()>0){
			$weekly_rides = $q_weekly_rides->row('total_rides');
		}
		$monthly_rides = 0;
		$query_monthly_rides = "SELECT COUNT(id) as total_rides FROM {$this->db->dbprefix('rides')} WHERE ride_timing BETWEEN '".$month_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_monthly_rides = $this->db->query($query_monthly_rides);
		if($q_monthly_rides->num_rows()>0){
			$monthly_rides = $q_monthly_rides->row('total_rides');
		}
		
		/*Distance*/
		$daily_distance = 0;
		$daily_fare = 0;
		
		$query_daily_distance = "SELECT coalesce(SUM(total_distance),0) as total_distance, coalesce(SUM(total_fare),0) as total_fare  FROM {$this->db->dbprefix('ride_payment')} WHERE created_on BETWEEN '".$current_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_daily_distance = $this->db->query($query_daily_distance);
		if($q_daily_distance->num_rows()>0){
			$daily_distance = $q_daily_distance->row('total_distance');
			$daily_fare = $q_daily_distance->row('total_fare');
		}
		
		$weekly_distance = 0;
		$weekly_fare = 0;
		
		$query_weekly_distance = "SELECT coalesce(SUM(total_distance),0) as total_distance, coalesce(SUM(total_fare),0) as total_fare   FROM {$this->db->dbprefix('ride_payment')} WHERE created_on BETWEEN '".$week_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_weekly_distance = $this->db->query($query_weekly_distance);
		if($q_weekly_distance->num_rows()>0){
			$weekly_distance = $q_weekly_distance->row('total_distance');
			$weekly_fare = $q_weekly_distance->row('total_fare');
		}
		
		$monthly_distance = 0;
		$monthly_fare = 0;
		
		$query_monthly_distance = "SELECT coalesce(SUM(total_distance),0) as total_distance, coalesce(SUM(total_fare),0) as total_fare   FROM {$this->db->dbprefix('ride_payment')} WHERE created_on BETWEEN '".$month_date." 00:00:00' and '".$current_date." 23:59:59' AND driver_id = ".$driver_id." AND is_country = '".$countryCode."' ";
		$q_monthly_distance = $this->db->query($query_monthly_distance);
		if($q_monthly_distance->num_rows()>0){
			$monthly_distance = $q_monthly_distance->row('total_distance');
			$monthly_fare = $q_monthly_distance->row('total_fare');
		}
		
		
		$daily_hours = 0;
		$weekly_hours = 0;
		$monthly_hours = 0;
		
		$data = array(
			'daily_rides' => $daily_rides, 
			'daily_distance' => round($daily_distance), 
			'daily_fare' => $daily_fare, 
			'daily_hours' => $daily_hours, 
			'weekly_rides' => $weekly_rides, 
			'weekly_distance' => round($weekly_distance), 
			'weekly_fare' => $weekly_fare, 
			'weekly_hours' => $weekly_hours,
			'monthly_rides' => $monthly_rides, 
			'monthly_distance' => round($monthly_distance), 
			'monthly_fare' => $monthly_fare, 
			'monthly_hours' => $monthly_hours
			//'weekly' => array('weekly_rides' => $weekly_rides, 'weekly_distance' => $weekly_distance, 'weekly_fare' => $weekly_fare, 'weekly_hours' => $weekly_hours),
			//'monthly' => array('monthly_rides' => $monthly_rides, 'monthly_distance' => $monthly_distance, 'monthly_fare' => $monthly_fare, 'monthly_hours' => $monthly_hours)
		);
		if($data){
			
			return $data;
		}
		return false;	
	}
	
	function getPreferLocationView($driver_id, $countryCode){
		$this->db->select('pl.id, pl.lat, pl.lng, pl.status, pl.title');
		$this->db->from('preferlocation pl');
		$this->db->where('pl.user_id', $driver_id)->where('is_country', $countryCode);
		$q = $this->db->get();

		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
			
				$row->pincode = $this->site->findLocationPINCODE1($row->lat, $row->lng, $countryCode);
				$row->address = $this->site->findLocationWEB($row->lat, $row->lng, $countryCode);
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getServicestypeView($driver_id, $countryCode){
		$this->db->select('tt.name as services_name, tt.id as services_id, IFNULL(ts.status, 0) as status, IFNULL(t.id, 0) as taxi_id, t.type, tm.is_up_down, tm.is_order');
		$this->db->from('taxi_type tt');
		$this->db->join('taxi_services ts','ts.user_id = '.$driver_id.' AND ts.taxi_type = tt.id', 'left');
		$this->db->join('taxi t', 't.driver_id = '.$driver_id.'', 'left');
		$this->db->join('taxi_image tm', 'tm.id = tt.taxi_image_id', 'left');
		$this->db->where('tt.is_country', $countryCode);
		$this->db->order_by('tt.id', 'ASC');
		$q = $this->db->get();

		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$check = $this->site->orderCheckTypeimage($row->services_id, $row->type, $row->is_up_down, $row->is_order);
				if($check == TRUE){
				$data[] = $row;	
				}
			}
			return $data;
		}
		return FALSE;
	}
	
	
	
	function updatePreferlocation($insert, $driver_id, $prefer_id, $countryCode){
		if($prefer_id != 0){
			$q = $this->db->delete('preferlocation', array('user_id' => $driver_id, 'id' => $prefer_id, 'is_edit' => 1));
		}
		if(!empty($driver_id)){
			$insert['is_country'] = $countryCode;
			$this->db->insert('preferlocation', $insert);
			return true;
		}
		return false;	
	}
	
	function updateServicestype($driver_id, $taxi_id, $taxi_type, $status, $multiple_type, $countryCode){
		
		$q = $this->db->delete('taxi_services', array('user_id' => $driver_id, 'taxi_id' => $taxi_id, 'taxi_type' => $taxi_type, 'is_edit' => 1));
		if($q){
			$this->db->insert('taxi_services', array('user_id' => $driver_id, 'taxi_id' => $taxi_id, 'taxi_type' => $taxi_type, 'status' => $status, 'is_edit' => 1, 'created_on' => date('Y-m-d'), 'is_country' => $countryCode));
			$this->db->update('taxi', array('multiple_type' => $multiple_type), array('driver_id' => $driver_id, 'id' => $taxi_id, 'is_country' => $countryCode ));
			return true;
		}
		return false;	
	}
	
	function paymentoffline($insert, $driver_id, $countryCode){
		$this->db->where('is_edit', 1);
		$this->db->where('driver_id', $driver_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('driver_payment', $insert);
		if($q){
			return true;	
		}
		return false;	
	}
	
	function paymentdetail($val, $countryCode){
		$ex_pay = $this->db->select('*')->from('driver_payment')->where('is_country', $countryCode)->where('driver_id', $val['driver_id'])->where('is_edit', 0)->order_by('id', 'DESC')->limit(1)->get();
		if($ex_pay->num_rows()>0){
			$ex_pay_row = $ex_pay->row();
			$data1 = array(
				'ex_paid' => $ex_pay_row->payment_amount,
				'ex_paid_date' => $ex_pay_row->payment_date
			);
		}else{
			$data1 = array(
				'ex_paid' => '0',
				'ex_paid_date' => '0'
			);
		}
		
		$pay = $this->db->select('*')->from('driver_payment')->where('driver_id', $val['driver_id'])
->where('is_country', $countryCode)->where('is_edit', 1)->order_by('id', 'DESC')->limit(1)->get();
		if($pay->num_rows()>0){
			
			$pay_row = $pay->row();
			$data2 = array(
				'paid' => $pay_row->payment_amount,
				'paid_date' => $pay_row->ride_start_date,
				'paid_last_date' => $pay_row->ride_end_date
			);
		}else{
			$pay_row = '';
			$data2 = array(
				'paid' => '0',
				'paid_date' => '0',
				'paid_last_date' => '0'
			);
		}
		$data[] = array_merge($data1, $data2);
		if(!empty($data)){
			return $data;	
		}
		return false;
		
	}
	
	function getAdminbank($country_id, $countryCode){
		$this->db->select('*');		
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('admin_bank');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function paymentlist($val, $countryCode){
		$this->db->select('dp.*, u.first_name');
		$this->db->from('driver_payment dp');
		$this->db->join('users u', 'u.id = dp.driver_id AND u.is_country = "'.$countryCode.'"', 'left');
		$this->db->where('dp.driver_id', $val['driver_id']);
		$this->db->where('dp.is_country', $countryCode);
		$q = $this->db->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				if($row->is_edit == 1){
					$row->status = 'Pending';
				}else{
					$row->status = 'Complete';
				}
				$data[] = $row;	
			}
			return $data;
		}
		return false;
	}
	
	function paymentview($val, $countryCode){
		$this->db->select('dp.*, IFNULL(u.first_name, 0) as driver_name, IFNULL(a.first_name, 0) as admin_name');
		$this->db->from('driver_payment dp');
		$this->db->join('users u', 'u.id = dp.driver_id AND u.is_country = "'.$countryCode.'"', 'left');
		$this->db->join('users a', 'a.id = dp.admin_id AND a.is_country = "'.$countryCode.'"', 'left');
		$this->db->where('dp.driver_id', $val['driver_id']);
		$this->db->where('dp.id', $val['id']);
		$this->db->where('dp.is_country', $countryCode);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row = $q->row();
			if($row->is_edit == 1){
				$row->status = 'Pending';
			}else{
				$row->status = 'Complete';
			}
			
			if($row->driver_status == 1){
				$row->driver_status_name = 'Complete';
			}else{
				$row->driver_status_name = 'Pending';
			}
			
			if($row->admin_status == 1){
				$row->admin_status_name = 'Verified';
			
			}elseif($row->admin_status == 2){
				$row->admin_status_name = 'Deposit Process';
			
			}elseif($row->admin_status == 3){
				$row->admin_status_name = 'Credit Process ';
			
			}else{
				$row->admin_status_name = 'Process';
			}
			
			if($row->payment_status == 1){
				$row->payment_status_name = 'Online';
			
			}elseif($row->payment_status == 2){
				$row->payment_status_name = 'Offline';
						
			}else{
				$row->payment_status_name = 'Process';
			}
			
			$data[] = $row;	
			
			return $data;
		}
		return false;
	}
	
	function getRideBYID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;	
	}
	
	function driverWaitingCancel($data, $countryCode){
		if($data['check_cancel_free_second'] == 0){
			$cancel_free_status = 1;
		}else{
			$cancel_free_status = 0;
		}
		$this->db->where('driver_id', $data['driver_id']);
		$this->db->where('id', $data['booking_id']);
		
		$q = $this->db->update('rides', array('cancel_status' => 1,  'cancel_id' => $data['cancel_id'], 'cancel_msg' => $data['cancel_msg'], 'cancel_distance' => $data['cancel_distance'], 'cancelled_by' => $data['customer_id'], 'cancelled_type' => 4, 'cancel_free_status' => $cancel_free_status, 'cancel_on' => $data['cancel_on'], 'status' => 8));
		if($q){
			//$o = $this->db->insert('outstandingfare', array('customer_id' => $data['customer_id'], 'cancel_distance' => $data['cancel_distance'], 'customer_fare' => $data['customer_fare'], 'customer_status' => 1, 'customer_created' => date('Y-m-d H:i:s'), 'is_edit' => 1));
			
			if($data['ride_cancel_driver_on_the_way_km_fare_enable'] == 1){
					$this->db->select('location');
					$this->db->where('ride_id', $data['booking_id']);
					$dr = $this->db->get('driver_frequency');
					$driver_frquency = explode(',', $dr->row('location'));
					$driver_final_distance = $this->site->CancellationDistance($data['booking_id'], $countryCode);
					
					$distance_meter = $driver_final_distance;
					$distance_kilometer = round($distance_meter/1000, 1);
					$final_km = $distance_kilometer;
					$total_distance = $final_km;
				
				
				
				
				$this->db->select('rides.*, taxi.type, c.mobile, c.country_code, c.id as customer_id');
				$this->db->join('taxi', 'taxi.id = rides.taxi_id', 'left');
				$this->db->join('users c', 'c.id = rides.customer_id', 'left');
				$this->db->where('rides.id', $data['booking_id']);
				
				$r = $this->db->get('rides');
				
				
				
				if($r->num_rows()>0){
					
					
					
					$riderow = $r->row();
					//$estimate_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, 'Km', $countryCode);	
				$estimate_distance = $final_km;
				
				//$actual_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->actual_lat, $riderow->actual_lng, 'Km');	
				$actual_distance = $final_km;
				
				$country_code = $riderow->country_code;
				$customer_mobile = $riderow->mobile;
				$customer_id = $riderow->customer_id;
				$driver_id = $riderow->driver_id;
				$vendor_id = $riderow->vendor_id;
				
				
				$fare_estimate = $this->site->getFareCancellation($riderow->booked_type ? $riderow->booked_type : 1, $riderow->type ? $riderow->type : 1, $riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, $total_distance, $countryCode);
				
				 
					
					if(!empty($fare_estimate)){
						if($data['ride_cancel_driver_on_the_way_percentage_enable'] == 1){
							
							$t_fare = round($fare_estimate['total_fare'] * $data['ride_cancel_driver_on_the_way_percentage_value'] / 100);
						}else{
							$t_fare = $fare_estimate['total_fare'];
						}
						
						if($t_fare > $data['cancel_maximum_fare']){
							$total_fare = $data['cancel_maximum_fare'];
						}else{
							$total_fare = $t_fare;
						}
						
						
						
					}else{
						
						$total_fare = 0;
						
					}
				}
					$driver_cancel_charge = $total_fare;
					$drver_cancel_final_km = $final_km;
				}else{
					$driver_final_distance = $this->site->CancellationDistance($data['booking_id'], $countryCode);
					
					$distance_meter = $driver_final_distance;
					$distance_kilometer = round($distance_meter/1000, 1);
					$final_km = $distance_kilometer;
					$total_distance = $final_km;
					
					
					
					if($data['customer_cancel_charge'] > $data['cancel_maximum_fare']){
						$driver_cancel_charge = $data['cancel_maximum_fare'];
					}else{
						$driver_cancel_charge = $data['driver_cancel_charge'];
					}
					
					$drver_cancel_final_km = $final_km;
				}
			//if($data['check_cancel_free_second'] == 1){
				//if($data['check_cancel_limit'] > $data['no_of_driver_cancel']){
					//$admin_user = $this->site->getAdminUser($countryCode, 2);
					
					//$this->db->insert('outstandingfare', array('customer_id' => $data['customer_id'], 'customer_created' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'customer_status' => 1, 'customer_fare' => $driver_cancel_charge));
					
					/*$wallet_array[] = array(
						'user_id' =>  $data['driver_id'],
						'user_type' => 2,
						'wallet_type' => 1,
						'flag' => 4,
						'flag_method' => 16,
						'cash' => $driver_cancel_charge,
						'description' => 'Driver Cancel Ride Deduction wallet amount',
						'created' => date('Y-m-d H:i:s'),
						'cash_type' => 1,
						'is_country' => $countryCode
					);
					$wallet_array[] = array(
						'user_id' =>  $admin_user,
						'user_type' => 0,
						'wallet_type' => 1,
						'flag' => 9,
						'flag_method' => 15,
						'cash' => $driver_cancel_charge,
						'description' => 'Driver Cancel Ride Received wallet amount',
						'created' => date('Y-m-d H:i:s'),
						'cash_type' => 1,
						'is_country' => $countryCode
					);
					$this->db->insert_batch('wallet', $wallet_array);*/
					
					
				//}
			//}
			
			$this->db->insert('outstandingfare', array('customer_id' => $data['customer_id'], 'customer_created' => date('Y-m-d H:i:s'), 'is_edit' => 1, 'customer_status' => 1, 'customer_fare' => $driver_cancel_charge));
			
			//print_r($this->db->last_query());die;
			
			$this->db->where('driver_id', $data['driver_id']);
			
			
			$this->db->update('driver_current_status', array('mode' => 1, 'is_connected' => 1));
			if($data['process_type'] == 1){
				$this->db->update('bookingcrm', array('bookingcrm_status' => 8, 'driver_cancel' => '1', 'driver_cancel_msg' => $data['cancel_msg']), array('ride_id' => $data['booking_id']));
				$this->db->update('bookingcrm_notification', array('cancel_notification' => 1), array('ride_id' => $data['booking_id']));
			}
			$data_array =  array(
				
				"booking_id" => $data['booking_id'],
				"driver_cancel_charge" => $driver_cancel_charge,
				"drver_cancel_final_km" => $drver_cancel_final_km !='' ? $drver_cancel_final_km : 0,
				"countryCode" => $countryCode,
			);
			return $data_array;
			
		}
		return false;	
	}
	
	function nodriverCancel($data, $countryCode){
		
		$this->db->where('id', $data['booking_id']);
		//$q = $this->db->update('rides', array('cancel_status' => 1,  'cancel_msg' => $data['cancel_msg'], 'cancelled_by' => $data['driver_id'], 'cancelled_type' => 2, 'status' => 8));
		$q = $this->db->update('rides', array('cancel_status' => 1,  'cancel_msg' => $data['cancel_msg'], 'cancelled_type' => 2, 'cancel_on' => date('Y-m-d H:i:s'), 'cancel_free_status' => 1, 'status' => 8));
		//print_r($this->db->last_query());exit;
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	function driverCancel($data, $countryCode){
		if($data['check_cancel_free_second'] == 0){
			$cancel_free_status = 1;
		}else{
			$cancel_free_status = 0;
		}
		$this->db->where('driver_id', $data['driver_id']);
		$this->db->where('id', $data['booking_id']);
		
		$q = $this->db->update('rides', array('cancel_status' => 1, 'cancel_id' => $data['cancel_id'], 'cancel_msg' => $data['cancel_msg'], 'cancelled_by' => $data['driver_id'], 'cancelled_type' => 3, 'cancel_free_status' => $cancel_free_status, 'cancel_on' => $data['cancel_on'], 'status' => 8));
		//print_r($this->db->last_query());exit;
		if($q){
			if($data['ride_cancel_driver_on_the_way_km_fare_enable'] == 1){
				
					$this->db->select('location');
					$this->db->where('ride_id', $data['booking_id']);
					$dr = $this->db->get('driver_frequency');
					$driver_frquency = explode(',', $dr->row('location'));
					$driver_final_distance = $this->site->CancellationDistance($data['booking_id'], $countryCode);
					
					$distance_meter = $driver_final_distance;
					$distance_kilometer = round($distance_meter/1000, 1);
					$final_km = $distance_kilometer;
					$total_distance = $final_km;
				
				
				
				
				$this->db->select('rides.*, taxi.type, c.mobile, c.country_code, c.id as customer_id');
				$this->db->join('taxi', 'taxi.id = rides.taxi_id', 'left');
				$this->db->join('users c', 'c.id = rides.customer_id', 'left');
				$this->db->where('rides.id', $data['booking_id']);
				
				$r = $this->db->get('rides');
				
				
				
				if($r->num_rows()>0){
					
					
					
					$riderow = $r->row();
					//$estimate_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, 'Km', $countryCode);	
				$estimate_distance = $final_km;
				
				//$actual_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->actual_lat, $riderow->actual_lng, 'Km');	
				$actual_distance = $final_km;
				
				$country_code = $riderow->country_code;
				$customer_mobile = $riderow->mobile;
				$customer_id = $riderow->customer_id;
				$driver_id = $riderow->driver_id;
				$vendor_id = $riderow->vendor_id;
				
				
				$fare_estimate = $this->site->getFareCancellation($riderow->booked_type ? $riderow->booked_type : 1, $riderow->type ? $riderow->type : 1, $riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, $total_distance, $countryCode);
				
				 
					
					if(!empty($fare_estimate)){
						
						if($data['ride_cancel_driver_on_the_way_percentage_enable'] == 1){
							
							$t_fare = round($fare_estimate['total_fare'] * $data['ride_cancel_driver_on_the_way_percentage_value'] / 100);
						}else{
							$t_fare = $fare_estimate['total_fare'];
						}
						
						if($t_fare > $data['cancel_maximum_fare']){
							$total_fare = $data['cancel_maximum_fare'];
						}else{
							$total_fare = $t_fare;
						}
						
					}else{
						
						$total_fare = 0;
						
					}
				}
					$driver_cancel_charge = $total_fare;
					$drver_cancel_final_km = $final_km;
				
			}else{
					if($data['customer_cancel_charge'] > $data['cancel_maximum_fare']){
						$driver_cancel_charge = $data['cancel_maximum_fare'];
					}else{
						$driver_cancel_charge = $data['driver_cancel_charge'];
					}
					$drver_cancel_final_km = $final_km;
				}
				if($data['check_cancel_free_second'] == 1){
					if($data['check_cancel_limit'] > $data['no_of_driver_cancel']){
						$admin_user = $this->site->getAdminUser($countryCode, 2);
						$wallet_array[] = array(
							'user_id' =>  $data['driver_id'],
							'user_type' => 2,
							'wallet_type' => 1,
							'flag' => 4,
							'flag_method' => 16,
							'cash' => $driver_cancel_charge,
							'description' => 'Driver Cancel Ride Deduction wallet amount',
							'created' => date('Y-m-d H:i:s'),
							'cash_type' => 1,
							'is_country' => $countryCode
						);
						$wallet_array[] = array(
							'user_id' =>  $admin_user,
							'user_type' => 0,
							'wallet_type' => 1,
							'flag' => 9,
							'flag_method' => 15,
							'cash' => $driver_cancel_charge,
							'description' => 'Driver Cancel Ride Received wallet amount',
							'created' => date('Y-m-d H:i:s'),
							'cash_type' => 1,
							'is_country' => $countryCode
						);
						$this->db->insert_batch('wallet', $wallet_array);
					}
				}
			
			$this->db->where('driver_id', $data['driver_id']);
			
			
			$this->db->update('driver_current_status', array('mode' => 1, 'is_connected' => 1));
			if($data['process_type'] == 1){
				$this->db->update('bookingcrm', array('bookingcrm_status' => 8, 'driver_cancel' => '1', 'driver_cancel_msg' => $data['cancel_msg']), array('ride_id' => $data['booking_id']));
				$this->db->update('bookingcrm_notification', array('cancel_notification' => 1), array('ride_id' => $data['booking_id']));
			}
			$data_array =  array(
				"booking_id" => $data['booking_id'],
				"driver_cancel_charge" => $driver_cancel_charge,	
				"drver_cancel_final_km" => $drver_cancel_final_km != '' ? $drver_cancel_final_km : 0,
				"countryCode" => $countryCode,
			);
			return $data_array;
		}
		return false;	
	}
	
	function getALLTaxi_fuel($countryCode){
		$this->db->where('is_country', $countryCode);
		$q = $this->db->get('taxi_fuel');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			return $data;
		}
		return false;
	}
	
	/*New Chages*/
	
	function getRidedetailsNEW($driver_id, $ride_id, $countryCode){
		$this->db->select('r.*, IFNULL(rp.driver_allowance, 0) as driver_allowance, IFNULL(rp.total_night_halt, 0) as total_night_halt, IFNULL(rp.total_toll, 0) as total_toll, IFNULL(rp.total_parking, 0) as total_parking, IFNULL(rp.total_distance, 0) as total_distance, IFNULL(rp.total_fare, 0) as total_fare, IFNULL(rp.extra_fare, 0) as extra_fare, IFNULL(mr.overall, 0) as overall, IFNULL(mr.drive_comfort_star, 0) as drive_comfort_star, IFNULL(mr.booking_process_star, 0) as booking_process_star, IFNULL(mr.cab_cleanliness_star, 0) as cab_cleanliness_star, IFNULL(mr.drive_politeness_star, 0) as drive_politeness_star, IFNULL(mr.fare_star, 0) as fare_star, IFNULL(mr.easy_of_payment_star, 0) as easy_of_payment_star, c.mobile as cmobile, c.first_name as cfname, c.last_name as clname, c.country_code as cccode, d.first_name as dfname, d.last_name as dlname, d.country_code as dccode, d.mobile as dmobile, IFNULL(v.mobile, 0) as vmobile, IFNULL(v.country_code, 0) as vccode, IFNULL(vp.first_name, 0) as vfname, IFNULL(vp.last_name, 0) as vlname, dcs.current_latitude as driver_latitude, dcs.current_longitude as  driver_longitude');
		$this->db->from('rides r');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id', 'left');
		
		$this->db->join('users v', 'v.id = r.vendor_id', 'left');
		$this->db->join('user_profile vp', 'vp.user_id = r.vendor_id', 'left');
		
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
		$this->db->join('multiple_rating mr', 'mr.booking_id = r.id', 'left');
		$this->db->join('ride_payment rp', 'rp.ride_id = r.id', 'left');
		
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id ', 'left');
		
		$this->db->where(array('r.id'=>$ride_id, 'r.driver_id' => $driver_id))->where('r.is_country', $countryCode);
		
		$q = $this->db->get();//print_r($this->db->error());exit;
       	if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function registerresendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."' AND country_code='".$data['country_code']."' AND group_id = 4 AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	public function updateDriver($insert, $driver_id, $countryCode){
		$this->db->where('id', $driver_id);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('users', $insert);
		if($q){

			return true;	
		}
		return false;
	}
	
	
	public function deviceGET($user_id, $user_type, $countryCode){
		$this->db->select('devices.*');
		$this->db->where('devices.user_id', $user_id);
		$this->db->where('devices.user_type', $user_type);
		$q = $this->db->get('devices');
		if ($q->num_rows() > 0) {
			$data = $q->row('device_token');
			return $data;
		}
		return FALSE;
	}
	
	function paymentPaid($data, $countryCode){
		$this->db->where('ride_id', $data['booking_id']);
		$this->db->where('id', $data['payment_id']);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('ride_payment', array('payment_type' => $data['payment_mode'], 'payment_status' => 1, 'paid_amount' => $data['amount_paid'], 'balance_amount' => $data['balance_paid'], 'created_on' => date('Y-m-d H:i:s')));	
		if($q){
			
			return true;
		}
		return false;
	}
	
	function getSettings($countryCode){
		$q = $this->db->select('*')->where('is_country', $countryCode)->get('settings');
		if($q->num_rows() > 0){
			return $q->row();	
		}
		return false;
	}
	
	function add_vendor($user, $user_profile, $user_address, $user_bank, $user_document, $user_vendor, $vendor_group_id, $driver_id, $countryCode){
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			$username = sprintf("%03d", $customer['country_code']).'3'.str_pad($customer_id, 6, 0, STR_PAD_LEFT);
			//$username = 'VEN'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			$user_vendor['user_id'] = $user_id;
			$user_profile['is_country'] = $countryCode;
			$user_address['is_country'] = $countryCode;
			$user_bank['is_country'] = $countryCode;
			$user_document['is_country'] = $countryCode;
			$user_vendor['is_country'] = $countryCode;
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			$this->db->insert('user_vendor', $user_vendor);
			$this->db->update('users', array('parent_id' => $user_id), array('id' => $driver_id, 'is_country' => $countryCode));
			$this->db->update('driver_current_status', array('vendor_id' => $user_id), array('driver_id' => $driver_id, 'is_country' => $countryCode));
			
			
	    	return true;
		}
		return false;
    }
	
	public function myprofile($user_id, $driver_group, $driver_type, $countryCode){
		
	
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, IFNULL(u.email, 0) as email,  IFNULL(u.ref_mobile, 0) as driver_ref_code, IFNULL(u.ref_driver, 0) as own_ref_code, IFNULL(u.country_code, 0) as country_code, IFNULL(u.ref_mobile, 0) as ref_mobile, IFNULL(u.mobile, 0) as mobile, u.active, u.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, IFNULL(ud.local_image, 0) as local_image, IFNULL(ud.local_address, 0) as local_address, IFNULL(ud.local_pincode, 0) as local_pincode, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id,  ud.local_country_id,  ud.local_zone_id,  ud.local_state_id,  ud.local_city_id,  ud.local_area_id,   ud.permanent_verify, ud.permanent_approved_by, ud.permanent_approved_on, IFNULL(ud.permanent_image, 0) as permanent_image, IFNULL(ud.permanent_address, 0) as permanent_address, ud.permanent_continent_id,  ud.permanent_country_id,  ud.permanent_zone_id, IFNULL(ud.permanent_pincode, 0) as permanent_pincode,  ud.permanent_state_id,  ud.permanent_city_id,  ud.permanent_area_id,  ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, IFNULL(ub.account_no, 0) account_no, ub.is_verify as account_verify, IFNULL(ub.account_holder_name, 0) as account_holder_name, IFNULL(ub.bank_name, 0) as bank_name, IFNULL(ub.branch_name, 0) as branch_name, IFNULL(ub.ifsc_code, 0) as ifsc_code, IFNULL(udoc.aadhaar_no, 0) as aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  IFNULL(udoc.aadhaar_image, 0) as aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  IFNULL(udoc.pancard_no, 0) as pancard_no, udoc.pancard_verify, IFNULL(udoc.pancard_image, 0) as pancard_image, IFNULL(udoc.license_image, 0) as license_image, udoc.license_approved_by, udoc.license_approved_on, IFNULL(udoc.license_no, 0) as license_no, udoc.license_verify, udoc.license_dob, IFNULL(udoc.license_ward_name, 0) as license_ward_name, IFNULL(lt.name, 0) as license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, IFNULL(udoc.police_image, 0) as police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, IFNULL(udoc.police_on, 0) as police_no, udoc.police_til, udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, IFNULL(udoc.loan_information, 0) as loan_information, udoc.loan_verify, IFNULL(u.first_name, 0) as first_name, IFNULL(u.last_name, 0) as last_name, IFNULL(u.gender, 0) as gender, u.dob, IFNULL(u.photo, 0) as photo, up.is_approved as profile_verify, IFNULL(ugroup.name, 0) as group_name, IFNULL(pgroup.name, 0)  as parent_group_name, userper.department_id, IFNULL(ur.position, 0) as position,  userper.designation_id, IFNULL(userdep.name, 0) as user_department, userper.continent_id, IFNULL(urc.name, 0) as continent_name, userper.country_id, IFNULL(urcc.name, 0) as country_name, userper.zone_id, IFNULL(urz.name, 0) as zone_name, userper.state_id, IFNULL(urs.name, 0) as state_name, userper.city_id, IFNULL(urcity.name, 0) as city_name, userper.area_id, IFNULL(ura.name, 0) as area_name, IFNULL(uv.gst, 0) as gst, IFNULL(uv.telephone_number, 0) as telephone_number, IFNULL(uv.legal_entity, 0) as legal_entity, uv.associated_id, IFNULL(assoc.first_name, 0) as associated_name, up.is_approved as profile_is_approved, up.approved_by as profile_approved_by, up.approved_on as profile_approved_on');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id', 'left');
		$this->db->join('user_profile assoc', 'assoc.is_edit = 1 AND assoc.user_id = uv.associated_id', 'left');
		$this->db->join('user_address ud', 'ud.is_edit = 1 AND ud.user_id = u.id', 'left');
		$this->db->join('user_bank ub', 'ub.is_edit = 1 AND ub.user_id = u.id', 'left');
		$this->db->join('user_document udoc', 'udoc.is_edit = 1 AND udoc.user_id = u.id', 'left');
		$this->db->join('user_profile up', 'up.is_edit = 1 AND up.user_id = u.id', 'left');
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		$this->db->join('license_type lt', 'lt.id = udoc.license_type', 'left');
		$this->db->where('u.id', $user_id)->where('u.is_country', $countryCode);
		$this->db->group_by('u.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			
			if($row->photo != ''){
				$row->photo = $image_path.$row->photo;
			}else{
				$row->photo = $image_path.'no_image.png';
			}
			
			if($row->local_image != ''){
				$row->local_image = $image_path.$row->local_image;
			}else{
				$row->local_image = $image_path.'no_image.png';
			}
			
			if($row->permanent_image !=''){
				$row->permanent_image = $image_path.$row->permanent_image;
			}else{
				$row->permanent_image = $image_path.'no_image.png';
			}
			
			if($row->aadhaar_image !=''){
				$row->aadhaar_image = $image_path.$row->aadhaar_image;
			}else{
				$row->aadhaar_image = $image_path.'no_image.png';
			}
			
			if($row->pancard_image !=''){
				$row->pancard_image = $image_path.$row->pancard_image;
			}else{
				$row->pancard_image = $image_path.'no_image.png';
			}
			
			if($row->license_image !=''){
				$row->license_image = $image_path.$row->license_image;
			}else{
				$row->license_image = $image_path.'no_image.png';
			}
			
			if($row->police_image !=''){
				$row->police_image = $image_path.$row->police_image;
			}else{
				$row->police_image = $image_path.'no_image.png';
			}
			
			if($row->loan_doc !=''){
				$row->loan_doc = $image_path.$row->loan_doc;
			}else{
				$row->loan_doc = $image_path.'no_image.png';
			}
			
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			
			
			
			if($row->police_til == NULL){
				$row->police_til = '0';
			}else{
				$row->police_til = $row->police_til;
			}
			
			if($row->police_on == NULL){
				$row->police_on = '0';
			}else{
				$row->police_on = $row->police_on;
			}
			
			
			$ride = $this->db->select('COUNT(id) as ride_count')->where('driver_id', $row->id)->get('rides');	
			
			if ($ride->num_rows() > 0) {
				$row->ride_count = $ride->row('ride_count');
			}else{
				$row->ride_count = '0';
			}
			$row->years = '0';
			
			$star = $this->db->select('*')->where('driver_id', $row->id)->get('multiple_rating');	
			
			$booking_process_star = array();
			$cab_cleanliness_star = array();
			$drive_comfort_star = array();
			$drive_politeness_star = array();
			$fare_star = array();
			$easy_of_payment_star = array();
			$overall_star = array();
			
			if ($star->num_rows() > 0) {
				$i=0;
				foreach (($star->result()) as $s) {
					$booking_process_star[] = $s->booking_process_star;
					$cab_cleanliness_star[] = $s->cab_cleanliness_star;
					$drive_comfort_star[] = $s->drive_comfort_star;
					$drive_politeness_star[] = $s->drive_politeness_star;
					$fare_star[] = $s->fare_star;
					$easy_of_payment_star[] = $s->easy_of_payment_star;
					$overall_star[] = $s->overall;
					$i++;
				}
					$total_star = $i * 5;
					//$row->avg = round(((array_sum($booking_process_star / $total_star) * 5), 1);
			
				$row->driver_stars = array(
					'booking_process_star' => round(((array_sum($booking_process_star) / $total_star) * 5), 1),
					'cab_cleanliness_star' => round(((array_sum($cab_cleanliness_star) / $total_star) * 5), 1),
					'drive_comfort_star' => round(((array_sum($drive_comfort_star) / $total_star) * 5), 1),
					'drive_politeness_star' => round(((array_sum($drive_politeness_star) / $total_star) * 5), 1),
					'fare_star' => round(((array_sum($fare_star) / $total_star) * 5), 1),
					'easy_of_payment_star' => round(((array_sum($easy_of_payment_star) / $total_star) * 5), 1),
				);
				
				$row->overall_star = round(((array_sum($overall_star) / $total_star) * 5), 1);
				
			}else{
				$row->driver_stars = array(
					'booking_process_star' => 0,
					'cab_cleanliness_star' => 0,
					'drive_comfort_star' => 0,
					'drive_politeness_star' => 0,
					'fare_star' => 0,
					'easy_of_payment_star' => 0
				);
				$row->overall_star = round(((array_sum($overall_star) / $total_star) * 5), 1);
			}
			
			
			 
			if($driver_group == 4 && $driver_type == 1){
				$driver_data = array(
					'user_id' => $row->id,
					'email' => $row->email,
					'country_code' => $row->country_code,
					'mobile' => $row->mobile,
					'ref_mobile' => $row->ref_mobile,
					'driver_ref_code' => $row->driver_ref_code,
					'own_ref_code' => $row->own_ref_code,
					'active' => $row->active,
					'first_name' => $row->first_name,
					'last_name' => $row->last_name,
					'gender' => $row->gender,
					'dob' => $row->dob,
					'photo' => $row->photo,
					'profile_verify' => $row->profile_verify,
					'local_image' => $row->local_image,
					'local_address' => $row->local_address,
					'local_pincode' => $row->local_pincode,
					
					'local_verify' => $row->local_verify,
					'permanent_image' => $row->permanent_image,
					'permanent_address' => $row->permanent_address,
					'permanent_pincode' => $row->permanent_pincode,
					'permanent_verify' => $row->permanent_verify,
					'driver_stars' => $row->driver_stars,
					'years' => $row->years,
					'ride_count' => $row->ride_count,
					'overall_star' => $row->overall_star
					
					
				);
			}elseif($driver_group == 4 && $driver_type == 2){
				$driver_data = array(
					'user_id' => $row->id,
					'account_holder_name' => $row->account_holder_name,
					'account_no' => $row->account_no,
					'bank_name' => $row->bank_name,
					'branch_name' => $row->branch_name,
					'ifsc_code' => $row->ifsc_code,
					'account_verify' => $row->account_verify
				);
			}elseif($driver_group == 4 && $driver_type == 3){
				$driver_data = array(
					'user_id' => $row->id,
					'aadhaar_no' => $row->aadhaar_no,
					'aadhaar_image' => $row->aadhaar_image,
					'aadhar_verify' => $row->aadhar_verify,
					'pancard_no' => $row->pancard_no,
					'pancard_image' => $row->pancard_image,
					'pancard_verify' => $row->pancard_verify,
					'license_no' => $row->license_no,
					'license_image' => $row->license_image,
					'license_verify' => $row->license_verify,
					'police_image' => $row->police_image,
					'police_on' => $row->police_on,
					'police_til' => $row->police_til,
					'police_verify' => $row->police_verify

				);
			}
            return $driver_data;
        }
		return FALSE;
	}
	
	
	function getDrivers_radius($data, $countryCode){
	$image_path = base_url('assets/uploads/');
	
	if($data['taxi_type'] != ''){
		$where = "  AND t.type = ".$data['taxi_type']."  ";
	}else{
		$where = "  ";
	}
	$query = "SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, u.first_name, up.last_name, up.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id  
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type 
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE d.is_country = '".$countryCode."' AND (dcs.mode = 1 OR dcs.mode = 3) AND dcs.is_connected = 1   ".$where." HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

	$q = $this->db->query($query);

	
	if($q->num_rows()>0){
	    $r = $q->result();
	    foreach($r as $row){
			
			if($row->driver_photo !=''){
				$row->driver_photo = $image_path.$row->driver_photo;
			}else{
				$row->driver_photo = $image_path.'no_image.png';
			}
			
			if($row->taxi_photo !=''){
				$row->taxi_photo = $image_path.$row->taxi_photo;
			}else{
				$row->taxi_photo = $image_path.'no_image.png';
			}	
		
		
			$d[] = $row;
	    }
		
		
	    
	    return $d;
	}
	return false;
    }
	
	function getDrivers_radius_limit($data, $ride_id, $countryCode){
	$image_path = base_url('assets/uploads/');
	
	/*if($data['taxi_type'] != ''){
		$where = "  AND t.type = ".$data['taxi_type']."  ";
	}else{
		$where = "  ";
	}*/
	
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
		//$where = "  ts.taxi_type = ".$data['taxi_type']." AND AND ts.status = 1 AND ";
		//$where = "  AND ".$data['taxi_type']." IN t.multiple_type";
	}else{
		//AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)
		$where = "  ";
	}
	
	$query = "SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, d.last_name, d.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id 
	JOIN {$this->db->dbprefix('taxi_services')} AS ts ON ts.taxi_id = dcs.taxi_id AND ts.user_id = d.id AND ts.status = 1 AND ts.taxi_type = ".$data['taxi_type']."
	JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = ".$data['taxi_type']."
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	
	WHERE d.is_country = '".$countryCode."'  AND   (dcs.mode = 1 OR dcs.mode = 3) AND dcs.is_connected = 1  AND    d.id NOT IN (SELECT driver_id FROM {$this->db->dbprefix('driver_booking')} AS db WHERE db.ride_id = ".$ride_id.")  GROUP BY d.id HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

	$q = $this->db->query($query);

	
	if($q->num_rows()>0){
	    $r = $q->result();
		
	    foreach($r as $row){
			
			if($row->driver_photo !=''){
				$row->driver_photo = $image_path.$row->driver_photo;
			}else{
				$row->driver_photo = $image_path.'no_image.png';
			}
			
			if($row->taxi_photo !=''){
				$row->taxi_photo = $image_path.$row->taxi_photo;
			}else{
				$row->taxi_photo = $image_path.'no_image.png';
			}	
		
		
			$d[] = $row;
	    }
		
		
	    
	    return $d;
	}
	return false;
    }
	
	function getDrivers1_radius($data, $countryCode){
	$image_path = base_url('assets/uploads/drivers/photo/');
	$taxi_path = base_url('assets/uploads/taxi/');
	$this->db
	    ->select("d.id,d.created_on date_created,d.first_name driver_name,d.photo,d.current_latitude latitude,d.current_longitude longitude,d.mode, dcs.taxi_id, t.name, t.number, t.type, tt.name type_name, tt.mapcar type_image ")
            ->from("drivers d");
		
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = d.id', 'left');
		$this->db->join('taxi t', 't.id = dcs.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
	    $this->db->where('d.current_latitude BETWEEN "'. $data['minlat']. '" and "'. $data['maxlat'].'"');
	    $this->db->where('d.current_longitude BETWEEN "'. $data['minlng']. '" and "'. $data['maxlng'].'"');
		if(!empty($data['taxi_type'])){
		$this->db->where('tt.id', $data['taxi_type']);
		}
		$this->db->where('d.mode', 'available')->where('u.is_country', $countryCode);
		$this->db->group_by('d.id');
	$q=$this->db->get();
	if($q->num_rows()>0){
		
		$b = $this->db->select('driver_id')->where('ride_id', $data['ride_id'])->group_by('driver_id')->get('driver_booking');
		if($b->num_rows() > 0){
			foreach($b->result() as $kow){
				$driver_id[] = $kow->driver_id;
			}
		}
		if(!empty($driver_id)){
			
			foreach($q->result() as $k => $row){
				if (!in_array($row->id, $driver_id)) 
				  { 
					 $result[$k] = $row;
				  }else{
					  $result[$k] = '';
				  }
			}
		}else{
			 $result[$k] = $row;
		}
		
	    return $result;
	}
	return false;
    }
	
	function insertNotification($data, $countryCode){
		$q = $this->db->insert('notification', array('user_type' => $data['user_type'], 'user_id' => $data['user_id'], 'title' => $data['title'], 'message' => $data['message'], 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode ));
		if($q){
			
			return true;	
		}
		return false;	
	}
	
	function driverUpdateStatus($data, $countryCode){
		
		$u = $this->db->select('u.id as user_id, IFNULL(t.id, 0) as taxi_id, IFNULL(dcs.id, 0) as current_driver_or_taxi_id')->from('users u')->join('taxi t', 't.driver_id = u.id', 'left')->join('driver_current_status dcs', 'dcs.driver_id = u.id AND dcs.taxi_id = t.id AND (dcs.mode = 2 OR dcs.mode = 3)', 'left')->where('u.id', $data['driver_id'])->get();
		
		if($u->num_rows() > 0){
			
			
		
			if($data['mode'] == 1){
				$q = $this->db->select('*')->from('rides')->where('driver_id', $data['driver_id'])->order_by('id', 'DESC')->limit(1)->get();
				if($q->num_rows() > 0){
					$status = $q->row('status');
					if($status == 2){
						$mode = 2;
					}elseif($status == 3){
						$mode = 3;
					}elseif($status == 4){
						$mode = 3;
					}else{
						$mode = 1;
					}
				}else{
					$mode = 1;
				}
			}else{
				$mode = 0;	
			}
			
			$this->db->select("u.id as id, u.created_on, u.first_name, up.last_name, u.email, u.mobile,  up.gender, u.active as active, If(up.is_approved = 1 &&  ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1, '1', '0') as status")
			->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->where("u.id", $data['driver_id'])->where('u.is_country', $countryCode);
			$userCheck = $this->db->get();
			
			//print_r($this->db->last_query());die;
			
			if($userCheck->num_rows()>0){
				$status = $userCheck->row('status');
			}else{
				$status = '0';
			}
			
			
			if($mode == 1){
				
				if($status == 1){
				
				
					$t = $this->db->update('taxi', array('taxi_mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
					$q = $this->db->update('users', array('mode' => $data['mode']), array('id' => $data['driver_id'], 'is_country' => $countryCode));
					
					//$checkMode = $this->db->seledt('*')->where('')
					
					$dcu = $this->db->update('driver_current_status', array('mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
					
					//$dc = $this->db->insert('driver_current_status', array('is_allocated' => 1, 'is_connected' => 1, 'driver_id' => $data['driver_id'], 'taxi_id' => $u->row('taxi_id'), 'allocated_start_date' => date('Y-m-d H:i:s'), 'allocated_status' => 1, 'is_withoutvendor' => 1, 'mode' => $data['mode'], 'is_country' => $countryCode));
					
					
			
					return 1;
				
				}else{
					
					return 2;
				}
			}elseif($mode == 0){
				
				
				$t = $this->db->update('taxi', array('taxi_mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
				$q = $this->db->update('users', array('mode' => $data['mode']), array('id' => $data['driver_id'], 'is_country' => $countryCode));
				$dc = $this->db->update('driver_current_status', array( 'mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'taxi_id' => $u->row('taxi_id'), 'is_country' => $countryCode));
				return 1;
			
			
			}else{
				
				
				$t = $this->db->update('taxi', array('taxi_mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
				$q = $this->db->update('users', array('mode' => $data['mode']), array('id' => $data['driver_id'], 'is_country' => $countryCode));
				$dc = $this->db->update('driver_current_status', array('mode' => $data['mode']), array('driver_id' => $data['driver_id'], 'taxi_id' => $u->row('taxi_id'), 'is_country' => $countryCode));
				return 1;
			}
		
		}
		
		/*$this->db->where('driver_id', $data['driver_id']);
		$this->db->where('allocated_status', 1);
		$q = $this->db->update('driver_current_status', array('mode' => $mode));
		if($q){
			return 1;
		}*/
		return 0;	
	}
	
	
	
	function fcminsert($data, $countryCode){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->where('is_country', $countryCode);
			$this->db->update('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}else{
			$this->db->insert('devices', array('user_id' => $data['user_id'], 'user_type' => $data['user_type'], 'devices_type' => $data['devices_type'], 'device_imei' => $data['device_imei'], 'device_token' => $data['device_token'], 'created_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
	function fcmdelete($data, $countryCode){
		$q = $this->db->select('*')->where('device_imei', $data['device_imei'])->get('devices');
		if($q->num_rows() > 0){
			$this->db->where('device_imei', $data['device_imei']);
			$this->db->where('is_country', $countryCode);
			$this->db->update('devices', array('user_id' => 0, 'user_type' => 0, 'devices_type' => 0, 'device_imei' => '', 'updated_on' => date('Y-m-d H:i:s')));
			
			return true;
			
		}
		return false;
	}
	
		function add_driver_new($user, $user_profile, $user_address, $user_bank, $user_document, $driver_group_id, $operator, $countryCode, $refer_code, $reference_no){
			$setting = $this->site->RegsiterSettings($countryCode);
			
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
		
		
		$user_id = $this->db->insert_id();
		
		$ref_driver = 'DRI'.date('YmdHis');
		$this->db->where('id', $user_id);
		$this->db->where('is_country', $countryCode);
		$this->db->update('users', array('ref_driver' => $ref_driver));
		
        if(!empty($user_id)){
			$this->db->insert('user_setting', array('user_id' => $user_id, 'created_on' => date('Y-m-d'), 'is_country' => $countryCode));
			if(!empty($user_profile)){
				$user_profile['user_id'] = $user_id;
				$user_profile['is_country'] = $countryCode;
				$this->db->insert('user_profile', $user_profile);
				
			}
			
			if(!empty($user_address)){
				$user_address['user_id'] = $user_id;
				$user_address['is_country'] = $countryCode;
				$this->db->insert('user_address', $user_address);
			}
			
			if(!empty($user_bank)){
				$user_bank['user_id'] = $user_id;
				$user_bank['is_country'] = $countryCode;
				$this->db->insert('user_bank', $user_bank);
				
			}
			
			if(!empty($user_document)){
				$user_document['user_id'] = $user_id;
				$user_document['is_country'] = $countryCode;
				$this->db->insert('user_document', $user_document);
			}
			
			
			if(!empty($setting)){
				$refer_array = array(
					'code' => $refer_code,
					'amount' => $setting->driver_amount,
					'register_enable' => $setting->driver_user_reg,
					'ride_enable' => $setting->driver_rides,
					'number_of_rides' => $setting->driver_rides_no,
					'code_start' => date('Y-m-d'),
					'code_end' => date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$setting->driver_validation.' days')),
					'using_type' => $setting->driver_using_type,
					'using_menbers' => $setting->driver_using_members,
					'user_id' => $user_id,
					'created_on' => date('Y-m-d H:i:s'),
					'is_country' => $countryCode,
					'user_type' => 1
 				);
				$this->db->insert('user_refercode', $refer_array);
				if(!empty($reference_no)){
					$this->site->implementCode($reference_no, $user_id, 4, $countryCode);
				}
			}
			
			
			$username = sprintf("%03d", $user['country_code']).'2'.str_pad($user_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			
			
			
			
			
	    	return true;
		}
		return false;
    }
	
	function checkCode($code, $type){
		$check = substr($code, -4, 1);
		
		if($check == $type){
			$this->db->select('u.refer_code, urf.code_end ')->from('users u')->join('user_refercode urf', 'urf.user_id = u.id', 'left')->where('refer_code', $code);
			$d =$this->db->get();
			
			if($d->num_rows()>0){
				return $d->row();
			}else{
				return 0;	
			}
		}else{
			return 0;
		}
		
	}
	
	function add_driver($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $driver_group_id, $operator, $countryCode){
		$user['is_country'] = $countryCode;
		$this->db->insert('users', $user);
		
		$user_id = $this->db->insert_id();
		
        if(!empty($user_id)){
			
			$username = sprintf("%03d", $user['country_code']).'2'.str_pad($user_id, 6, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id, 'is_country' => $countryCode));
			
			if(!empty($user_profile)){
				$user_profile['user_id'] = $user_id;
				$user_profile['is_country'] = $countryCode;
				$this->db->insert('user_profile', $user_profile);
			}
			
			if(!empty($user_address)){
				$user_address['user_id'] = $user_id;
				$user_address['is_country'] = $countryCode;
				$this->db->insert('user_address', $user_address);
			}
			
			if(!empty($user_bank)){
				$user_bank['user_id'] = $user_id;
				$user_bank['is_country'] = $countryCode;
				$this->db->insert('user_bank', $user_bank);
			}
			
			if(!empty($user_document)){
				$user_document['user_id'] = $user_id;
				$user_document['is_country'] = $countryCode;
				$this->db->insert('user_document', $user_document);
			}
			
			
			
			
			
			if($operator == 1){
				if(!empty($taxi)){
					$taxi['is_country'] = $countryCode;
					$taxi['driver_id'] = $user_id;
					
					$this->db->insert('taxi', $taxi);
					if($taxi_id = $this->db->insert_id()){
						$taxi_document['taxi_id'] = $taxi_id;
						$taxi_document['user_id'] = $user_id;
						$taxi_document['group_id'] = $driver_group_id;
						$taxi_document['is_country'] = $countryCode;
						$this->db->insert('taxi_document', $taxi_document);
						
					}
				}
			}
			
	    	return true;
		}
		return false;
    }
	
	
	/*function add_driver($user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $driver_group_id, $parent_id){
		
		$this->db->insert('users', $user);
        if($user_id = $this->db->insert_id()){
			$username = 'DRI'.str_pad($user_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('users', array('username' => $username), array('id' => $user_id));
			$user_profile['user_id'] = $user_id;
			$user_address['user_id'] = $user_id;
			$user_bank['user_id'] = $user_id;
			$user_document['user_id'] = $user_id;
			
			$this->db->insert('user_profile', $user_profile);
			$this->db->insert('user_address', $user_address);
			$this->db->insert('user_bank', $user_bank);
			$this->db->insert('user_document', $user_document);
			
			
			if(!empty($taxi)){
				$taxi['user_id'] = $user_id;
				$taxi['group_id'] = $driver_group_id;
				$this->db->insert('taxi', $taxi);
				
				if($taxi_id = $this->db->insert_id()){
					$taxi_document['taxi_id'] = $taxi_id;
					$taxi_document['user_id'] = $user_id;
					$taxi_document['group_id'] = $driver_group_id;
					$this->db->insert('taxi_document', $taxi_document);
					
					if(!empty($taxi_id) && !empty($user_id)){
						$this->db->insert('driver_current_status', array('driver_id' => $user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $parent_id));
					}
				}
				
				
			}
	    	return true;
		}
		return false;
    }
	*/
	function getTaxiDetails($id, $countryCode){
		$image_path = base_url('assets/uploads/');
				
		$query = "select t.id, t.name, t.model, t.number, tt.name as taxi_type, t.engine_number, t.chassis_number, t.make, tf.name as fuel_name, t.color, t.manufacture_year, t.capacity, t.photo, t.mode, t.is_verify, td.reg_image, td.reg_date, td.reg_due_date, td.reg_owner_name, td.reg_owner_address, td.reg_verify, td.taxation_image, td.taxation_amount_paid, td.taxation_due_date, td.taxation_verify, td.insurance_image, td.insurance_policy_no, td.insurance_due_date, td.insurance_verify, td.permit_image, td.permit_no, td.permit_due_date, td.permit_verify, td.authorisation_image, td.authorisation_no, td.authorisation_due_date, td.authorisation_verify, td.fitness_image, td.fitness_due_date, td.fitness_verify, td.speed_image, td.speed_due_date, td.puc_image, td.puc_due_date, td.puc_verify from {$this->db->dbprefix('taxi')} as t 
		LEFT JOIN {$this->db->dbprefix('taxi_fuel')} as tf ON tf.id= t.fuel_type 
		LEFT JOIN {$this->db->dbprefix('taxi_type')} as tt ON tt.id = t.type  
		LEFT JOIN {$this->db->dbprefix('taxi_document')} as td ON td.taxi_id = t.id
		where t.id='".$id."' AND t.is_country = '".$countryCode."' ";
		
		
		
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				
				if($row->photo ==''){
					$row->photo =  $image_path.$row->photo;
				}else{
					$row->photo =  $image_path.'no_image.png';
				}
				
				if($row->reg_image ==''){
					$row->reg_image =  $image_path.$row->reg_image;
				}else{
					$row->reg_image =  $image_path.'no_image.png';
				}
				if($row->taxation_image ==''){
					$row->taxation_image =  $image_path.$row->taxation_image;
				}else{
					$row->taxation_image =  $image_path.'no_image.png';
				}
				if($row->insurance_image ==''){
					$row->insurance_image =  $image_path.$row->insurance_image;
				}else{
					$row->insurance_image =  $image_path.'no_image.png';
				}
				if($row->permit_image ==''){
					$row->permit_image =  $image_path.$row->permit_image;
				}else{
					$row->permit_image =  $image_path.'no_image.png';
				}
				if($row->authorisation_image ==''){
					$row->authorisation_image =  $image_path.$row->authorisation_image;
				}else{
					$row->authorisation_image =  $image_path.'no_image.png';
				}
				if($row->fitness_image ==''){
					$row->fitness_image =  $image_path.$row->fitness_image;
				}else{
					$row->fitness_image =  $image_path.'no_image.png';
				}
				if($row->speed_image ==''){
					$row->speed_image =  $image_path.$row->speed_image;
				}else{
					$row->speed_image =  $image_path.'no_image.png';
				}
				if($row->puc_image ==''){
					$row->puc_image =  $image_path.$row->puc_image;
				}else{
					$row->puc_image =  $image_path.'no_image.png';
				}
				
				
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	
	
	
	function checkRef($ref_mobile, $countryCode){
		$q = $this->db->select('*')->where('is_country', $countryCode)->where('ref_mobile', $ref_mobile)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return 0;
	}
	
	function checkMobile($mobile, $country_code, $countryCode){
		$q = $this->db->select('*')->where('is_country', $countryCode)->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', 4)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return 0;
	}
	function checkMobileVendor($mobile, $country_code){
		$q = $this->db->select('*')->where('is_country', $countryCode)->where('mobile', $mobile)->where('country_code', $country_code)->where('group_id', 3)->get('users');
		if($q->num_rows()>0){
			return 1;
		}
		
		return 0;
	}
	function getDriverTaxiID($user_id, $countryCode){
		$q = $this->db->select('taxi_id')->where('is_country', $countryCode)->where('driver_id', $user_id)->get('driver_current_status');
		if($q->num_rows()>0){
			return $q->row('taxi_id');
		}
		
		return false;
	}
	
	function edit_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $customer_type, $countryCode){
		
        if(!empty($user_id) && !empty($customer_type)){
			
			if($customer_type == 1){
				$this->db->update('user_profile', array('is_edit' => 0), array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$this->db->update('user_address', array('is_edit' => 0), array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $countryCode));
				$user_profile['user_id'] = $user_id;
				$user_address['user_id'] = $user_id;
				$user_profile['is_country'] = $countryCode;
				$user_address['is_country'] = $countryCode;
				$this->db->insert('user_profile', $user_profile);
				$this->db->insert('user_address', $user_address);
				
			}elseif($customer_type == 2){
				$this->db->update('user_bank', array('is_edit' => 0), array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$user_bank['is_country'] = $countryCode;
				$user_bank['user_id'] = $user_id;
				$this->db->insert('user_bank', $user_bank);
			}elseif($customer_type == 3){
				$user_document['user_id'] = $user_id;
				$this->db->update('user_document', array('is_edit' => 0), array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$user_document['is_country'] = $countryCode;
				$this->db->insert('user_document', $user_document);
			}
			
	    	return true;
		}
		return false;
    }
	
	
	
	public function getUserEdit($user_id, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('u.id, u.email, u.country_code, u.mobile, u.is_daily, u.is_rental, u.is_outstation, u.is_hiring, u.is_corporate, u.active, up.is_approved as user_approved,  u.group_id, u.parent_id, ud.local_verify, ud.local_image, ud.local_address, ud.local_pincode, ud.local_approved_by, ud.local_approved_on, ud.local_continent_id,  ud.local_country_id,  ud.local_zone_id,  ud.local_state_id,  ud.local_city_id,  ud.local_area_id,  ud.permanent_verify, ud.permanent_approved_by, ud.permanent_pincode, ud.permanent_approved_on, ud.permanent_image, ud.permanent_address,  ud.permanent_continent_id,  ud.permanent_country_id,  ud.permanent_zone_id,  ud.permanent_state_id,  ud.permanent_city_id,  ud.permanent_area_id, ub.approved_by as account_approved_by, ub.approved_on as account_approved_on, ub.account_no, ub.is_verify as account_verify, ub.bank_name, ub.account_holder_name, ub.branch_name, ub.ifsc_code, udoc.aadhaar_no, udoc.aadhar_verify, udoc.aadhar_approved_by, udoc.aadhar_approved_on,  udoc.aadhaar_image, udoc.pancard_approved_by, udoc.pancard_approved_on,  udoc.pancard_no, udoc.pancard_verify, udoc.pancard_image, udoc.license_image,  udoc.license_no, udoc.license_country_id, udoc.license_type, udoc.license_approved_by, udoc.license_approved_on, udoc.license_verify, udoc.license_dob, udoc.license_ward_name, udoc.license_no,  udoc.license_country_id, udoc.license_type, udoc.license_issuing_authority, udoc.license_issued_on, udoc.license_validity, udoc.police_image, udoc.police_approved_by, udoc.police_approved_on,  udoc.police_verify, udoc.police_on, udoc.police_til,
		
		udoc.badge_no, udoc.badge_image, udoc.badge_approved_by, udoc.badge_approved_on,  udoc.badge_verify, udoc.badge_on, udoc.badge_til, 
		udoc.training_certificate_no, udoc.training_certificate_image, udoc.training_certificate_approved_by, udoc.training_certificate_approved_on,  udoc.training_certificate_verify, udoc.training_certificate_on, udoc.training_certificate_til, 
		udoc.experience_certificate_no, udoc.experience_certificate_image, udoc.experience_certificate_approved_by, udoc.experience_certificate_approved_on,  udoc.experience_certificate_verify, udoc.experience_certificate_on, udoc.experience_certificate_til, 
		udoc.medical_certificate_no, udoc.medical_certificate_image, udoc.medical_certificate_approved_by, udoc.medical_certificate_approved_on,  udoc.medical_certificate_verify, udoc.medical_certificate_on, udoc.medical_certificate_til, 
		udoc.health_insurance_no, udoc.health_insurance_image, udoc.health_insurance_approved_by, udoc.health_insurance_approved_on,  udoc.health_insurance_verify, udoc.health_insurance_on, udoc.health_insurance_til, 
		udoc.term_insurance_no, udoc.term_insurance_image, udoc.term_insurance_approved_by, udoc.term_insurance_approved_on,  udoc.term_insurance_verify, udoc.term_insurance_on, udoc.term_insurance_til, 
		udoc.additional_contact_one_type, udoc.additional_contact_one_name, udoc.additional_contact_one_phone, udoc.additional_contact_one_phonecode,  udoc.additional_contact_two_type, udoc.additional_contact_two_name, udoc.additional_contact_two_phone, 
		udoc.additional_contact_two_phonecode, udoc.additional_contact_verify, udoc.additional_contact_approved_by, udoc.additional_contact_approved_on, 
		 udoc.loan_doc, udoc.loan_approved_by, udoc.loan_approved_on, udoc.loan_information, udoc.loan_verify, u.first_name, u.last_name, u.gender, u.dob, u.photo, ugroup.name as group_name, pgroup.name as parent_group_name, userper.department_id, ur.position,  userper.designation_id, userdep.name as user_department, userper.continent_id, urc.name as continent_name, userper.country_id, urcc.name as country_name, userper.zone_id, urz.name as zone_name, userper.state_id, urs.name as state_name, userper.city_id, urcity.name as city_name, userper.area_id, ura.name as area_name, uv.gst, uv.telephone_number, uv.legal_entity, uv.associated_id, uv.continent_id as vendor_continent_id, uv.country_id as vendor_country_id, uv.zone_id as vendor_zone_id, uv.state_id as vendor_state_id, uv.city_id as vendor_city_id, uv.is_verify as vendor_is_verify, uv.approved_by as vendor_approved_by, uv.approved_on as vendor_approved_on, assoc.first_name as associated_name, up.is_approved as profile_is_approved, up.approved_by as profile_approved_by, up.approved_on as profile_approved_on');
		$this->db->from('users u');
		$this->db->join('user_vendor uv', 'uv.user_id = u.id AND uv.is_edit = 1', 'left');
		$this->db->join('user_profile assoc', 'assoc.user_id = uv.associated_id AND assoc.is_edit = 1', 'left');
		$this->db->join('user_address ud', 'ud.user_id = u.id AND ud.is_edit = 1', 'left');
		$this->db->join('user_bank ub', 'ub.user_id = u.id AND ub.is_edit = 1', 'left');
		$this->db->join('user_document udoc', 'udoc.user_id = u.id AND udoc.is_edit = 1', 'left');
		$this->db->join('user_profile up', 'up.user_id = u.id AND up.is_edit = 1', 'left');
		$this->db->join('groups ugroup', 'ugroup.id = u.group_id', 'left');
		$this->db->join('groups pgroup', 'pgroup.id = u.parent_id', 'left');
		$this->db->join('user_permission userper', 'userper.user_id = u.id AND userper.is_edit = 1', 'left');
		$this->db->join('user_roles ur', 'ur.id = userper.designation_id', 'left');
		$this->db->join('user_department userdep', 'userdep.id = userper.department_id', 'left');
		$this->db->join('continents urc', 'urc.id = userper.continent_id', 'left');
		$this->db->join('countries urcc', 'urcc.id = userper.country_id', 'left');
		$this->db->join('zones urz', 'urz.id = userper.zone_id', 'left');
		$this->db->join('states urs', 'urs.id = userper.state_id', 'left');
		$this->db->join('cities urcity', 'urcity.id = userper.city_id', 'left');
		$this->db->join('areas ura', 'ura.id = userper.area_id', 'left');
		$this->db->join('license_type lt', 'lt.id = udoc.license_type', 'left');
		$this->db->where('u.is_edit', 1);
		$this->db->where('u.id', $user_id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if ($q->num_rows() > 0) {
			$row = $q->row();
			
			
			
            return $row;
        }
		return false;	
	}
	
	function checkDrivers($user_id, $group_id, $countryCode){
		$q = $this->db->select("u.id as id, u.first_name, up.last_name, u.email, u.mobile,  up.gender, If(ud.pancard_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && u.is_approved = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1, '1', '0') as status")
            ->from("users u")
			->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
			->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
			->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
			->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
			->where("u.group_id", $group_id)
			->where("u.id", $user_id)->where('u.is_country', $countryCode)
			->get();
		
		if($q->num_rows()>0){
			if($q->row('status') == 0){
				return true;
			}
		}
		return false;	
	}
	
	function currentlocationdriver($data, $countryCode){
		$this->db->where('id', $data['driver_id']);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('users', array('mode' => $data['mode'], 'current_latitude' => $data['latitude'], 'current_longitude' => $data['longitude']));
		if($q){
			return true;
		}
		return false;
	}
	
	function frequencylocationdriver($data, $countryCode){
		$q = $this->db->update('driver_current_status', array('mode' => 3, 'current_latitude' => $data['latitude'], 'current_longitude' => $data['longitude']), array('driver_id' => $data['driver_id'], 'taxi_id' => $data['taxi_id']));
		//$q = $this->db->insert('driver_current_status', array('mode' => 3, 'driver_id' => $data['driver_id'], 'taxi_id' => $data['taxi_id'], 'current_latitude' => $data['latitude'], 'current_longitude' => $data['longitude'], 'is_country' => $countryCode));
		if($q){
			return true;
		}
		return false;
	}
	
	function getDriverID($id, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.mobile, u.dob, u.email, u.devices_imei, u.group_id, u.first_name, u.last_name, up.gender, up.photo, dcs.mode, dcs.current_latitude, dcs.current_longitude');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id', 'left');
		$this->db->where('u.id', $id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		if($q->num_rows()>0){
			$row =  $q->row();
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			
		    $data = $row;
			return $data;
		}
		return false;	
	}
	
	function getDriverStatus($id, $countryCode){
		$this->db->select('*');
		$this->db->where('driver_id', $id);
		$this->db->where('is_country', $countryCode);
		//$this->db->where('mode', 1);
		$q = $this->db->get('driver_current_status');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getTaxitypeID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id)->where('is_country', $countryCode);
		$q = $this->db->get('taxi_type');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getTaxiID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id)->where('is_country', $countryCode);
		$q = $this->db->get('taxi');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function getCustomerID($id, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.mobile, u.email, u.devices_imei, u.group_id, u.first_name, up.last_name, up.gender, up.dob, up.photo');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->where('u.id', $id)->where('u.is_country', $countryCode);
		$q = $this->db->get();
		
		
		if($q->num_rows()>0){
		    $row =  $q->row();
			if($row->dob == NULL){
				$row->dob = '0';
			}else{
				$row->dob = $row->dob;
			}
			$data  = $row;
			return $data;
		}
		return false;	
	}
	
	function getRideID($id, $countryCode){
		$this->db->select('*');
		$this->db->where('cancel_status', 0);
		$this->db->where('id', $id);
		$q = $this->db->get('rides');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	function getPaymentName($id, $countryCode){
		$this->db->select('*');
		$this->db->where('id', $id);
		$q = $this->db->get('payment_mode');
		
		if($q->num_rows()>0){
		    $name =  $q->row('name');
			return $name;
		}
		return false;	
	}
	
	
	function check_login($login, $countryCode){
		$data = array();
		
		$setting = $this->getSettings($countryCode);
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->where('is_country', $countryCode)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		$image_path = base_url('assets/uploads/');
		$query = "select u.id, u.oauth_token, u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile, u.mobile_otp, u.password, IFNULL(u.ref_mobile, 0) as driver_ref_code, IFNULL(u.ref_driver, 0) as own_ref_code,  u.active from {$this->db->dbprefix('users')} as u 		
		where  u.mobile='".$login['mobile']."' AND  u.country_code='".$login['country_code']."'  AND u.group_id = 4 AND u.is_country = '".$countryCode."'";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			$row = $q->row();
			if ($row->password === $login['password']) {
				
				if($row->active == 1){
					
					$row->check_ride = $this->site->checkRide($row->id, 2);
					$row->ride_stop = $this->site->stopRide($row->id);
					
					$r = $this->db->select('GROUP_CONCAT(id) as ride_id')->where('driver_id', $row->id)->where('is_country', $countryCode)->get('rides');
					if($r->num_rows()>0){
						$ride_id = explode(',', $r->row('ride_id'));
						$rates = $this->db->select('drive_comfort_star')->where_in('booking_id', $ride_id)->where('is_country', $countryCode)->get('multiple_rating');
							if($rates->num_rows()>0){
							$i=0;
							$over = 0;
							foreach (($rates->result()) as $roww) {
								$over+= $roww->drive_comfort_star;
								$i++;
							}
							
							$over_sum = (string)round($over / $i, 1);
						}else{
							$over_sum = '0';
						}
					}else{
						$over_sum = '0';	
					}
					
					if($row->photo !=''){
						$row->driver_photo = $image_path.$row->photo;
					}else{
						$row->driver_photo = $image_path.'default.png';
					}
					
					if($setting->login_otp_enable == 1){
						if($q->row('devices_imei') != $login['devices_imei']){
							$row->check_status = 3;
							
							$this->db->update('users', array('mobile_otp' => $login['otp']), array('id' => $row->id, 'is_country' => $countryCode));
							
						}else{
							$row->check_status = 1;
							$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
						}
					}else{
						$row->check_status = 4;
						
						$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $login['devices_imei']), array('id' => $row->id, 'is_country' => $countryCode));					
						$this->db->update('user_socket', array('device_imei' => $login['devices_imei']), array('user_id' => $row->id, 'user_type' => 2,  'device_token' => $row->oauth_token));
				
						$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
					}
					/*
					if($q->row('devices_imei') != $login['devices_imei']){
						$row->check_status = 3;
						if($login['mobile'] == '5432154321'){
							$this->db->update('users', array('mobile_otp' => 123456), array('id' => $row->id));
						}else{
							$this->db->update('users', array('mobile_otp' => $login['otp']), array('id' => $row->id));
						}
					}else{
						$row->check_status = 1;
						$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $login['mobile'], 'time' => time()));
					}*/
					
					$s = $this->db->select('*')->get('settings');
					$row->camera_enable = $s->row('camera_enable');
					
					$b = $this->db->select('account_holder_name, account_no, bank_name, branch_name, ifsc_code, complete_bank, is_verify')
					->where('user_id', $row->id)->where('is_edit', 1)->where('is_country', $countryCode)->get('user_bank');
					
					if($b->num_rows()>0){
						if($b->row('complete_bank') == 1){
							$row->bank_status = '1';	
						}else{
							$row->bank_status = '0';	
						}
					}else{
						$row->bank_status = '0';
					}
					
					$d = $this->db->select('ud.user_id, ud.aadhaar_image, ud.pancard_image, ud.complete_document')->from('user_document ud')->join('user_profile up', 'up.user_id = ud.user_id AND up.is_edit = 1')
					->where('ud.user_id', $row->id)->where('ud.is_country', $countryCode)->where('ud.is_edit', 1)->get();
					
					if($d->num_rows()>0){
						if($d->row('complete_document') == 1){
							$row->document_status = '1';	
						}else{
							$row->document_status = '0';	
						}
					}else{
						$row->document_status = '0';
					}
					
					$t = $this->db->select('*')->where('driver_id', $row->id)->where('is_country', $countryCode)->where('is_edit', 1)->get('taxi');
					
					if($t->num_rows()>0){
						if($t->row('complete_taxi') == 1){
							$row->taxi_status = '1';
						}else{
							$row->taxi_status = '0';
						}
					}else{
						$row->taxi_status = '0';
					}
					
					$row->unicode_symbol = $unicode_symbol;
					
					
					
					$this->db->select("u.id as id, u.created_on, u.first_name, up.last_name, u.email, u.mobile,  up.gender, u.active as active, If(up.is_approved = 1 &&  ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && t.complete_taxi = 1, '1', '0') as status")
					->from("users u")
					->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
					->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
					->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
					->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
					->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1")
					->where("u.id", $row->id)->where('u.is_country', $countryCode);
					$userCheck = $this->db->get();
					
					//print_r($this->db->last_query());die;
					
					if($userCheck->num_rows()>0){
						$row->is_userverified = $userCheck->row('status');
					}else{
						$row->is_userverified = '0';
					}
					$row->overall_star = $over_sum;
					
					$data = $row;
					$check_hour = $this->site->driverhours($row->id);
					//echo $check_hour;
					//die;
					if($check_hour == 1){
						$data->check_status = 7;
						//print_r($data);
						//echo '@@';die;
						return 	$data;
					}else{
						
						return $data;
					}
				}else{
					return 	$data->check_status = 2;
				}
			}else{
				return 	$data->check_status = 0;
			}
		}else{		
			return 	$data->check_status = 5;
		}
	}
	
		function devicescheckotp($data, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$c = $this->db->select('unicode_symbol')->where('is_default', 1)->where('is_country', $countryCode)->get('currencies');
		if($c->num_rows()>0){
			$unicode_symbol = $c->row('unicode_symbol');
		}else{
			$unicode_symbol = '0';
		}
		
		
		$query = "select u.id, u.oauth_token, u.devices_imei, u.first_name, IFNULL(u.last_name, 0) as last_name, u.photo, u.devices_imei, u.username, u.country_code, u.mobile,  IFNULL(u.ref_mobile, 0) as driver_ref_code, IFNULL(u.ref_driver, 0) as own_ref_code from {$this->db->dbprefix('users')} as u 		
		where   u.id='".$data['customer_id']."' AND  u.mobile_otp='".$data['otp']."' AND u.active = 1 AND u.is_country = '".$countryCode."'  ";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());die;

		if($q->num_rows()>0){
			$row = $q->row();
			
			$r = $this->db->select('GROUP_CONCAT(id) as ride_id')->where('driver_id', $row->id)->where('is_country', $countryCode)->get('rides');
				if($r->num_rows()>0){
					$ride_id = explode(',', $r->row('ride_id'));
					$rates = $this->db->select('drive_comfort_star')->where_in('booking_id', $ride_id)->where('is_country', $countryCode)->get('multiple_rating');
					if($rates->num_rows()>0){
						$i=0;
						
						foreach (($rates->result()) as $roww) {
							$over+= $roww->drive_comfort_star;
							$i++;
						}
						
						$over_sum = (string)round($over / $i, 1);
					}else{
						$over_sum = '0';
					}
				}else{
					$over_sum = '0';	
				}
			
			
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['customer_id'], 'is_country' => $countryCode));
			
			
			
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['customer_id'], 'user_type' => 2,  'device_token' => $row->oauth_token, 'is_country' => $countryCode));
			
			if($row->photo !=''){
				$row->driver_photo = $image_path.$row->photo;
			}else{
				$row->driver_photo = $image_path.'default.png';
			}	
			
			
			$s = $this->db->select('*')->where('is_country', $countryCode)->get('settings');
				$row->camera_enable = $s->row('camera_enable');
				
				$b = $this->db->select('account_holder_name, account_no, bank_name, branch_name, ifsc_code, complete_bank, is_verify')
				->where('user_id', $row->id)->where('is_edit', 1)->where('is_country', $countryCode)->get('user_bank');
				
				if($b->num_rows()>0){
					if($b->row('complete_bank') == 1){
						$row->bank_status = '1';	
					}else{
						$row->bank_status = '0';	
					}
				}else{
					$row->bank_status = '0';
				}
				
				$d = $this->db->select('ud.user_id, ud.aadhaar_image, ud.pancard_image, ud.complete_document')->from('user_document ud')->join('user_profile up', 'up.user_id = ud.user_id AND up.is_edit = 1')
				->where('ud.user_id', $row->id)->where('ud.is_country', $countryCode)->where('ud.is_edit', 1)->get();
				
				if($d->num_rows()>0){
					if($d->row('complete_document') == 1){
						$row->document_status = '1';	
					}else{
						$row->document_status = '0';	
					}
				}else{
					$row->document_status = '0';
				}
				
				$t = $this->db->select('*')->where('driver_id', $row->id)->where('is_country', $countryCode)->where('is_edit', 1)->get('taxi');
				
				if($t->num_rows()>0){
					if($t->row('complete_taxi') == 1){
						$row->taxi_status = '1';	
					}else{
						$row->taxi_status = '0';	
					}
					
				}else{
					$row->taxi_status = '0';
				}
			
			
			$row->unicode_symbol = $unicode_symbol;
			
			
			$this->db->select("u.id as id, u.created_on, u.first_name, up.last_name, u.email, u.mobile,  up.gender, u.active as active, If(up.is_approved = 1 &&  ud.pancard_verify = 1 && uadd.local_verify = 1 && uadd.permanent_verify = 1 && ub.is_verify = 1 && ud.aadhar_verify = 1 && ud.license_verify = 1 && ud.police_verify = 1 && t.complete_taxi = 1, '1', '0') as status")
				->from("users u")
				->join("user_profile up", "up.user_id = u.id AND up.is_edit = 1", 'left')
				->join("user_bank ub", "ub.user_id = u.id AND ub.is_edit = 1", 'left')
				->join("user_document ud", "ud.user_id = u.id AND ud.is_edit = 1", 'left')
				->join("user_address uadd", "uadd.user_id = u.id AND uadd.is_edit = 1", 'left')
				->join("taxi t", "t.driver_id = u.id AND t.is_edit = 1")
				->where("u.id", $row->id)->where('u.is_country', $countryCode);
				
				$userCheck = $this->db->get();
				if($userCheck->num_rows()>0){
					$row->is_userverified = $userCheck->row('status');
				}else{
					$row->is_userverified = '0';
				}
					
				$row->overall_star = $over_sum;
			$data =  $row;
			$this->db->insert('login_attempts', array('type' => 'driver', 'login' => $row->mobile, 'time' => time()));
			return $data;
		}
		return false;
	}
	
	
	
	function getDriversIDallocated($driver_id, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$driver_id."' AND u.is_country = '".$countryCode."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		//$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			
			
			if($row->photo !=''){
				$row->driver_photo = $image_path.$row->photo;
			}else{
				$row->driver_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;	
	}
	function checkallocatedopenotp($data, $countryCode){
		
		$query = "select * from {$this->db->dbprefix('driver_current_status')} where driver_id='".$data['driver_id']."' AND  driver_otp='".$data['otp']."' AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);

		if($q->num_rows()>0){
			
			$row = $q->row();
			$this->db->update('driver_current_status', array('mode' => 1), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
			
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['driver_id'], 'is_country' => $countryCode));
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['driver_id'], 'user_type' => 2,  'device_token' => $row->oauth_token, 'is_country' => $countryCode));
			
			
			$this->db->select('oauth_token, country_code, mobile');
			$this->db->where('id', $row->vendor_id);
			$v = $this->db->get('users');
			if($q->num_rows()>0){
				$data =  $v->row();
				return $data;
			}	
			
			
		}
		return false;
	}
	
	function closeallocatedopen($data, $countryCode){
		
		$query = "select * from {$this->db->dbprefix('driver_current_status')} where is_country = '".$countryCode."' AND driver_id='".$data['driver_id']."'  ";
		$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			
			$this->db->update('driver_current_status', array('mode' => 1), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));
			
						
			$this->db->select('oauth_token, country_code, mobile');
			$this->db->where('id',  $row->vendor_id);
			$v = $this->db->get('users');
			if($q->num_rows()>0){
				$data =  $v->row();
				return $data;
			}	
			
			
		}
		return false;
	}
	
	function getDriverAllocatedTaxi($driver_id, $countryCode){
		$query = "select dc.*, t.number from {$this->db->dbprefix('driver_current_status')} as dc LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dc.taxi_id  where dc.driver_id='".$data['driver_id']."'  AND dc.is_country = '".$countryCode."' ";
		$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			
			return $row->number;	
		}
		return 0;	
	}
	
	function checkfirstotp($data, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$data['driver_id']."' AND  u.mobile_otp='".$data['otp']."' AND u.is_coutry = '".$countryCode."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		//$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'devices_imei' => $data['devices_imei']), array('id' => $data['driver_id'], 'is_country' => $countryCode));
			$this->db->update('user_socket', array('device_imei' => $data['devices_imei']), array('user_id' => $data['driver_id'], 'user_type' => 2,  'device_token' => $row->oauth_token, 'is_country' => $countryCode));
			
			if($row->photo !=''){
				$row->driver_photo = $image_path.$row->photo;
			}else{
				$row->driver_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;
		
	}
	
	function checkotp($data, $countryCode){
		$image_path = base_url('assets/uploads/');
		
		$query = "select u.oauth_token, u.username, u.country_code, u.mobile from {$this->db->dbprefix('users')} as u 
		
		where   u.id='".$data['driver_id']."' AND  u.mobile_otp='".$data['otp']."' AND u.is_country = '".$countryCode."'  ";
		$q = $this->db->query($query);
		
		//$query = "select * from {$this->db->dbprefix('users')} where id='".$data['customer_id']."' AND  mobile_otp='".$data['otp']."' ";
		///$q = $this->db->query($query);

		if($q->num_rows()>0){
			$row = $q->row();
			$this->db->update('users', array('mobile_otp_verify' => 1, 'mobile_otp' => 0, 'active' => 1), array('id' => $data['driver_id'], 'is_country' => $countryCode));
			
			
			if($row->photo !=''){
				$row->driver_photo = $image_path.$row->photo;
			}else{
				$row->driver_photo = $image_path.'default.png';
			}					
			$data =  $row;
			return $data;
		}
		return false;
		
	}
	
	function resendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['driver_id']."' AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $data['driver_id']);
			//$this->db->update('users', array('mobile_otp' =>  $data['mobile_otp']));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgototp($data, $countryCode){
		$query = "select id, oauth_token, country_code, email, mobile, active, devices_imei from {$this->db->dbprefix('users')} where mobile='".$data['mobile']."'  AND group_id = 4  AND is_country = '".$countryCode."'";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			$this->db->where('is_country', $countryCode);
			$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function forgotcheckotp($data, $countryCode){
		$query = "select id, oauth_token, country_code, email, mobile, active, devices_imei from {$this->db->dbprefix('users')} where id='".$data['driver_id']."' AND  forgot_otp='".$data['forgot_otp']."'  AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			
			return true;
		}
		return false;
	}
	
	function forgotresendotp($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('users')} where id='".$data['driver_id']."'  AND group_id = 4  AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			//$this->db->where('id', $q->row('id'));
			//$this->db->update('users', array('forgot_otp' => $data['forgot_otp'], 'forgot_otp_verify' => 0));
			$data = $q->row();
			return $data;
		}
		return false;
	}
	
	function updatepassword($data, $countryCode){
		
		$this->db->where('id', $data['driver_id']);
		$this->db->where('is_country', $countryCode);
		$q = $this->db->update('users', array('password' => $data['password'], 'text_password' => $data['text_password'], 'forgot_otp_verify' => 1));
		if($q){
			return true;	
		}
		return false;
	}
	
	function getDriverextra($oauth_token, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, u.first_name, IFNULL((up.last_name, 0) as last_name, up.gender, up.photo, ub.account_no, ub.bank_name, ub.branch_name, ub.ifsc_code, dcs.mode, dcs.current_latitude, dcs.current_longitude');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->join('user_bank ub', 'ub.user_id = u.id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id ', 'left');
		$this->db->where('u.oauth_token', $oauth_token)->where('u.is_country', $countryCode);
		$q = $this->db->get();
				
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	
	function getDriversettingView($oauth_token, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.group_id, u.is_daily, u.is_rental, u.is_outstation, u.parent_id, u.is_hiring, u.is_corporate, u.base_location, u.base_area_lat, u.base_area_lng, IFNULL(u.base_location, 0) as base_location_name');
		$this->db->from('users u');
		//$this->db->join('cities c', 'c.id = u.base_location', 'left');
		$this->db->where('u.oauth_token', $oauth_token)->where('u.is_country', $countryCode);
		$q = $this->db->get();
				
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	
	
	function getDriver($oauth_token, $countryCode){
		$this->db->select('u.id, u.oauth_token, u.country_code, u.parent_id, u.mobile, u.email, u.devices_imei, u.group_id, u.is_daily, u.is_rental, u.is_outstation, u.is_hiring, u.is_corporate, u.base_location, IFNULL(c.name, 0) as base_location_name, u.first_name, up.last_name, up.gender, up.photo, dcs.mode, dcs.current_latitude, dcs.current_longitude');
		$this->db->from('users u');
		$this->db->join('user_profile up', 'up.user_id = u.id', 'left');
		$this->db->join('cities c', 'c.id = u.base_location', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = u.id ', 'left');
		$this->db->where('u.oauth_token', $oauth_token)->where('u.is_country', $countryCode);
		$q = $this->db->get();
				
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;	
	}
	
	function startride($data, $route_array, $countryCode){
		$q = $this->db->select('*')->where('driver_id', $data['driver_id'])->where('id', $data['booking_id'])->where('ride_otp', $data['ride_otp'])->get('rides');
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			
			$this->db->update('rides', array('status' => 3));
			
			if(!empty($data['driver_id'])){
				$this->db->update('driver_current_status', array('mode' => 3), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));	
				$route_array['ride_id'] = $q->row('id');
				
				$this->db->insert('ride_route', $route_array);	
			}
			return $q->row();	
		}
		return false;	
	}
	
	function startridewithoutotp($data, $route_array, $countryCode){
		$q = $this->db->select('*')->where('driver_id', $data['driver_id'])->where('id', $data['booking_id'])->where('status', 2)->order_by('id', 'DESC')->get('rides');
		if($q->num_rows()>0){
			$this->db->where('id', $q->row('id'));
			
			$this->db->update('rides', array('status' => 3));
			
			if(!empty($data['driver_id'])){
				$this->db->update('driver_current_status', array('mode' => 3), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));	
				$route_array['ride_id'] = $q->row('id');
				
				$this->db->insert('ride_route', $route_array);	
			}
			return $q->row();	
		}
		return false;	
	}
	
	function submitride($data, $countryCode){
		$this->db->select('r.id as id, IFNULL(b.id, 0) as bookingcrm_id, IFNULL(b.staff_id, 0) as staff_id, r.process_type, r.booking_no, r.payment_id, r.payment_name, r.driver_id as driver_id, r.customer_id as customer_id, rp.estimate_distance, rp.estimate_fare, rp.actual_distance, rp.actual_fare, rp.driver_allowance, rp.total_night_halt, rp.total_toll, rp.total_parking, rp.promotion_code, rp.promotion_fare, rp.discount_code, rp.discount_fare, rp.total_tax_fare, rp.total_distance, rp.total_fare, rp.round_fare, rp.waiting_charge as waiting_fare, rp.pickup_waiting_fare, rp.pickup_waiting_min, rp.waypoint_waiting_fare, rp.waypoint_waiting_min, rp.trafic_waiting_fare, rp.trafic_waiting_min, rp.extra_fare, rp.extra_fare_details, rp.outstanding_from_last_trip, rp.final_total');
		$this->db->from('rides r');
		$this->db->join('ride_payment rp', 'rp.ride_id = '.$data['booking_id'].'');
		$this->db->join('bookingcrm b', 'b.ride_id = '.$data['booking_id'].'', 'left');
		$this->db->where('r.status', 9);
		$this->db->where('r.id', $data['booking_id']);
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			$row =  $q->row();
			$incompete_delete = $this->db->delete('incomplete_ride', array('booking_id' => $data['booking_id']));
			$this->db->where('status', 9);
			$this->db->where('id', $data['booking_id']);
			$u = $this->db->update('rides', array('status' => 5));
			if($u){
			

				$this->site->updateLastride($row->driver_id);
				
				$row->total_toll = $data['total_toll'];
				$row->total_parking = $data['total_parking'];
				//$row->waiting_fare = $row->waiting_fare;
				
				$row->final_total = $row->final_total +  $data['total_toll'] + $data['total_parking'];
				
				$row->final_total = number_format($row->final_total, 2);
				
				$this->db->where('ride_id', $data['booking_id']);
				$this->db->update('ride_payment', array('total_toll' => $data['total_toll'], 'total_parking' => $data['total_parking'], 'final_total' => $row->final_total, 'is_complete' => 1));
				//print_r($this->db->last_query());die;
				if($row->process_type == 1){
					$this->db->where('id', $row->bookingcrm_id);
					$b = $this->db->update('bookingcrm', array('status' => 1));
					
					if($b){
						$this->db->insert('bookingcrm_follow', array('bookingcrm_id' => $row->bookingcrm_id, 'bookingcrm_staff_id' => $row->staff_id, 'followup_date_time' => date('Y-m-d H:i:s'), 'status' => '1', 'discussion' => 'Ride has been closed', 'remark' => 'Ride has been closed', 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
							
						
					}
				}
				return $row;
			}
		}
		return false;	
	}
	
	function completeride($data, $countryCode){
		
		$distance_meter = $data['travel_distance'];
		$distance_kilometer = round($data['travel_distance']/1000, 1);
		
				
		$this->db->where('driver_id', $data['driver_id']);
		$this->db->where('id', $data['booking_id']);
		$q = $this->db->update('rides', array('status' => 9, 'actual_loc' => $data['actual_loc'], 'actual_lat' => $data['actual_lat'], 'actual_lng' => $data['actual_lng'], 'travel_distance' => $distance_kilometer, 'driver_final_distance' => $distance_meter, 'ride_timing_end' => date('Y-m-d H:i:s')));
		if($q){
			
			if(!empty($data['driver_id'])){
				
				$h = $this->db->delete('user_ride_hour', array('user_id' => $data['driver_id']));
				
				$d = $this->db->update('driver_current_status', array('mode' => 1, 'current_latitude' => $data['actual_lat'], 'current_longitude' => $data['actual_lng']), array('driver_id' => $data['driver_id'], 'is_country' => $countryCode));	
				
				
				$this->db->insert('ride_route', array('ride_id' => $data['booking_id'], 'is_country' => $countryCode, 'location' => $data['actual_loc'], 'latitude' => $data['actual_lat'], 'longitude' => $data['actual_lng'],  'timing' => date('Y-m-d  H:i:s'), 'trip_made' => 6));
										
			}
			
			$this->db->select('rides.*, taxi.type, c.mobile, c.country_code, c.id as customer_id');
			$this->db->join('taxi', 'taxi.id = rides.taxi_id', 'left');
			$this->db->join('users c', 'c.id = rides.customer_id', 'left');
			$this->db->where('rides.id', $data['booking_id']);
			
			$r = $this->db->get('rides');
			
			if($r->num_rows()>0){
				
				$riderow = $r->row();
				
				$outstanding_check = $this->db->select('SUM(customer_fare) as customer_fare')->where('is_edit', 1)->where('customer_id', $riderow->customer_id)->where('customer_status', 1)->get('outstandingfare');
				if($outstanding_check->num_rows()>0){
					$outstanding_from_last_trip = $outstanding_check->row('customer_fare') != NULL ? $outstanding_check->row('customer_fare') : '0.00';
					$this->db->update('outstandingfare', array('customer_status' => 2, 'is_edit' => 0), array('customer_id' => $riderow->customer_id, 'customer_status' => 1));		
				}else{
					$outstanding_from_last_trip = '0.00';	
				}
				$this->db->select('*');
				$this->db->where('ride_id', $data['booking_id'])->where('is_country', $countryCode);
				$this->db->where('trip_made', 6);
				$rr = $this->db->get('ride_route');
				
				
				$final_km = $distance_kilometer;
				$total_distance = $final_km;
				
				$estimate_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, 'Km', $countryCode);	
				$estimate_distance = round($estimate_distance,1);
				
				//$actual_distance =  $this->site->GetDrivingDistance_New($riderow->start_lat, $riderow->start_lng, $riderow->actual_lat, $riderow->actual_lng, 'Km');	
				//$actual_distance = $final_km;
				
				$country_code = $riderow->country_code;
				$customer_mobile = $riderow->mobile;
				$customer_id = $riderow->customer_id;
				$driver_id = $riderow->driver_id;
				$vendor_id = $riderow->vendor_id;
				
				
				
				$fare_estimate = $this->site->getFare($riderow->customer_id, $riderow->booked_type ? $riderow->booked_type : 1, $data['outstation_type'], $riderow->outstation_way, $riderow->type ? $riderow->type : 1, $riderow->start_lat, $riderow->start_lng, $riderow->end_lat, $riderow->end_lng, $riderow->ride_timing, $riderow->ride_timing_end, $estimate_distance, $total_distance, $total_distance, $riderow->waiting_time, $countryCode);
				
				 
				
				if(!empty($fare_estimate)){
					$estimate_distance = $fare_estimate['estimate_distance'];
					$estimate_fare = $fare_estimate['estimate_fare'];
					$actual_distance = $fare_estimate['actual_distance'];
					$actual_fare = $fare_estimate['actual_fare'];
					$total_distance = $fare_estimate['total_distance'];
					$total_fare = $fare_estimate['total_fare'];
					$round_fare = $fare_estimate['round_fare'];
					 $extra_fare = $fare_estimate['extra_fare'];
					$extra_fare_details = $fare_estimate['extra_fare_details'];
					
				}else{
					$estimate_distance = 0;
					$estimate_fare = 0;
					$actual_distance = 0;
					$actual_fare = 0;
					$total_distance = 0;
					$total_fare = 0;
					$round_fare = 0;
					$extra_fare = 0;
					$extra_fare_details = 0;
				}
				
				/*$offer = $this->db->select('*')->where('ride_id', $riderow->id)->where('user_id', $riderow->customer_id)->where('offer_status', 0)->where('is_country', $countryCode)->get('offers_user');
				
				if($offer->num_rows()>0){
					$discount_code = $offer->row('offer_code');
					if($offer->row('offer_type') == 1){
						$ride_fare = $total_fare;
						$discount_fare = ($offer->row('discount') / 100) * $total_fare;
						$offer_total = $total_fare - $discount_fare;
						$discount_fare = $discount_fare;
					}else{
						$ride_fare = $total_fare;
						$discount_fare = $offer->row('discount');
						$offer_total = $total_fare - $offer->row('discount');
						$discount_fare = $offer->row('discount');
					}
					$this->db->update('offers_user', array('ride_fare' => $ride_fare, 'discount_fare' => $discount_fare, 'total_fare' => $offer_total, 'offer_status' => 1));
				}else{
					$discount_fare = '0.00';
					$discount_code = '';
				}*/
				
				
				
				
				$setting = $this->site->get_setting($countryCode);
				$driver_percentage = $this->site->getDefaultTaxDriver($countryCode);
				$admin_percentage = $this->site->getDefaultTaxAdmin($countryCode);
				
				
				
				if($driver_percentage != 0){
					$driver_tax_name = $driver_percentage->tax_name;
					$driver_tax_percentage = $driver_percentage->percentage;
				}else{
					$driver_tax_name = '';
					$driver_tax_percentage = '0';
				}
				
				if($admin_percentage != 0){
					$admin_tax_name = $admin_percentage->tax_name;
					$admin_tax_percentage = $admin_percentage->percentage;
				}else{
					$admin_tax_name = '';
					$admin_tax_percentage = '0';
				}
				
				$driver_admin_payment_percentage = $setting->driver_default_set_payment == 0 ? $setting->driver_admin_payment_percentage : 0;
				
				$pickup_waiting_fare = $this->site->pickupWaiting($data['booking_id'], $setting->waiting_time, $setting->waiting_charges);
				$waypoint_waiting_fare = $this->site->waypointWaiting($data['booking_id'], $setting->waiting_time, $setting->waiting_charges);				
				
				$trafic_waiting = $this->site->traficWaiting($data['booking_id'], $setting->waiting_time, $setting->waiting_charges, $setting->trafic_distance);
				
				
				$check_pickup_drop_fare = $pickup_waiting_fare['pickup_wait_fare'] + $waypoint_waiting_fare;
				$trafic_waiting_fare = $trafic_waiting['trafic_wait_fare'];
				$trafic_waiting_min = $trafic_waiting['trafic_min'];
				
				$waiting_charge = number_format(round($pickup_waiting_fare['pickup_wait_fare'] +  $trafic_waiting_fare), 2);
				
				$b = $this->site->ride_commision($driver_id, $customer_id, $data['booking_id'], 1, $total_fare, $driver_admin_payment_percentage, $admin_tax_percentage, $admin_tax_name, $driver_tax_percentage, $driver_tax_name, $data['discount_name'], $data['discount_percentage'], $countryCode);
				
				$w = $this->site->ride_commision($driver_id, $customer_id, $data['booking_id'], 2, $outstanding_from_last_trip, $driver_admin_payment_percentage, $admin_tax_percentage, $admin_tax_name, $driver_tax_percentage, $driver_tax_name, $data['discount_name'], $data['discount_percentage'], $countryCode);
				
				$o = $this->site->ride_commision($driver_id, $customer_id, $data['booking_id'], 3, $waiting_charge, $driver_admin_payment_percentage, $admin_tax_percentage, $admin_tax_name, $driver_tax_percentage, $driver_tax_name, $data['discount_name'], $data['discount_percentage'], $countryCode);
				
				
				$paid = $this->site->getRideCommisionPayment($data['booking_id']);
				
				//print_r($paid);die;
				$insert_payment = array(
					'driver_id' => $driver_id, 
					'customer_id' => $customer_id, 
					'ride_id' => $data['booking_id'], 
					'is_country' => $countryCode,
					'estimate_distance' => $estimate_distance, 
					'estimate_fare' => $estimate_fare, 
					'actual_distance' => $actual_distance, 
					'actual_fare' => $actual_fare, 
					
					'total_distance' => $total_distance, 
					'total_fare' => $paid['r_total_fare'],
					
					'pickup_waiting_fare' => $pickup_waiting_fare['pickup_wait_fare'],
					'pickup_waiting_min' => $pickup_waiting_fare['pickup_min'],
					'trafic_waiting_fare' => $trafic_waiting_fare,
					'trafic_waiting_min' => $trafic_waiting_min,
										
					'waiting_charge' => $paid['r_waiting_charge'],
					
					
					
					
					'driver_allowance' => 0, 
					'total_night_halt' => 0, 
					'total_toll' => 0, 
					'total_parking' => 0, 
					
					'total_tax_fare' => $paid['r_total_tax'],
					
					'discount_fare' => $paid['r_discount'],
					'discount_id' => $data['discount_id'],
					'discount_name' => $data['discount_name'],
					'discount_percentage' => $data['discount_percentage'],
					
					
					
					'outstanding_from_last_trip' => $paid['r_outstanding'],
					
					'created_on' => date('Y-m-d H:i:s'),
					
					
					'ride_final_fare' => $paid['r_final'],
					
					'final_total' => $paid['r_final'],
					);
					
					$payment = $this->db->insert('ride_payment',$insert_payment);
					
					//print_r($this->db->last_query());die;
					$payment_id = $this->db->insert_id();
					$result = array(
						'driver_id' => $driver_id,
						'payment_id' => $payment_id,
						'vendor_id' => $vendor_id,
						'customer_mobile' => $customer_mobile, 
						'country_code' => $country_code, 
						'customer_id' => $customer_id,
						'ride_id' => $data['booking_id'],
						'estimate_distance' => $estimate_distance,
						'estimate_fare' => $estimate_fare,
						'actual_distance' => $total_distance,
						'actual_fare' => $paid['r_total_fare'],
						'driver_allowance' => 0,
						'total_night_halt' => 0,
						'total_parking' => '0.00',
						'total_toll' => '0.00',
						'pickup_waiting_fare' => $pickup_waiting_fare['pickup_wait_fare'],
						'pickup_waiting_min' => $pickup_waiting_fare['pickup_min'],
						'waypoint_waiting_fare' => '0.00',
						'waypoint_waiting_min' => 0,
						'trafic_waiting_fare' => $trafic_waiting_fare,
						'trafic_waiting_min' => $trafic_waiting_min,
						'waiting_charge' => $paid['r_waiting_charge'],
						'promotion_code' => $data['discount_name'] ? $data['discount_name'] : '',
						'promotion_fare' => $paid['r_discount'],
						'discount_name' => $data['discount_name'] ? $data['discount_name'] : '',
						'discount_fare' => $paid['r_discount'],
						'total_tax' => $paid['r_total_tax'],
						'total_distance' => $total_distance,
						'total_fare' => $paid['r_final'],
						'round_fare' => $paid['r_final'],
						'extra_fare' => $paid['r_outstanding'],
						'extra_fare_details' => $extra_fare_details,
						'outstanding_from_last_trip' => $paid['r_outstanding'],
					);
					$this->db->insert('incomplete_ride', array('booking_id' => $data['booking_id'], 'json_data' => json_encode($result), 'status' => 1));
					//print_r($result);
					//die;
					$data_array =  array(
						"booking_id" => $data['booking_id'],
						"total_fare" => $paid['r_final'],
						"driver_fare" => $paid['r_driver_fare'],
						"driver_id" => $driver_id,
						"countryCode" => $countryCode,
					);
			
					callAPI('GET', 'https://35.154.46.42/api/v1/ride/ridecurl?data='.json_encode($data_array));	
					return $result;	
					
			}
			
			
			
			
			
			
			
		}
		return false;	
	}
	
	
	
	function get_distance_between_points($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
	// Calculate the distance in degrees
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
	// Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
	}
	return round($distance, $decimals);
}
	
	/*function get_distance_between_points($latitude1, $longitude1, $latitude2, $longitude2) {
		$meters = $this->get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2);
		$kilometers = $meters / 1000;
		$miles = $meters / 1609.34;
		$yards = $miles * 1760;
		$feet = $miles * 5280;
		//return compact('miles','feet','yards','kilometers','meters');
		return $kilometers;
	}
	
	function get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2) {
		if (($latitude1 == $latitude2) && ($longitude1 == $longitude2)) { return 0; } // distance is zero because they're the same point
		$p1 = deg2rad($latitude1);
		$p2 = deg2rad($latitude2);
		$dp = deg2rad($latitude2 - $latitude1);
		$dl = deg2rad($longitude2 - $longitude1);
		$a = (sin($dp/2) * sin($dp/2)) + (cos($p1) * cos($p2) * sin($dl/2) * sin($dl/2));
		$c = 2 * atan2(sqrt($a),sqrt(1-$a));
		$r = 6371008; // Earth's average radius, in meters
		$d = $r * $c;
		return $d; // distance, in meters
	}*/
	
	function insertRoute($data, $countryCode){
		$data['is_country'] = $countryCode;
		if($this->db->insert('ride_route', $data)){
			return true;	
		}
		return false;	
	}
	function reachedlocation($data, $countryCode){
		$query = "select * from {$this->db->dbprefix('rides')} where driver_id='".$data['driver_id']."' AND  id='".$data['ride_id']."' AND is_country = '".$countryCode."' ";
		$q = $this->db->query($query);
		if($q->num_rows()>0){
			$data[] = $q->row();
			return  $data;
		}
		return false;
	}
	
	function GetDrivingDistance($lat1, $long1, $lat2, $long2)
	{
		/* $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		$dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
		$time = $response_a['rows'][0]['elements'][0]['duration']['value'];
	
		return array('distance' => $dist, 'time' => $time);*/
		
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyCQwIPSGiDNn_Pp3lefyrunuxx9EY7YrXQ&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		$dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
		$time = $response_a['rows'][0]['elements'][0]['duration']['value'];
	
		return array('distance' => $dist, 'time' => $time);
	}

	/*function distance($lat1, $lon1, $lat2, $lon2, $unit) {

	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);
	
	  if ($unit == "K") {
		return ($miles * 1.609344);
	  } else if ($unit == "N") {
		  return ($miles * 0.8684);
		} else {
			return $miles;
		  }
	}*/
	
	function  myrides($driver_id, $countryCode){
		
		$image_path = base_url('assets/uploads/drivers/photo/');
		
		$this->db->select('r.status, r.pick_up, r.drop_off, r.ride_start_time, r.ride_end_time, t.name taxi_name, t.number, tb.name brands, tc.name colors, tt.name types, p.cost, p.total_kms');		
		$this->db->from('rides r');
		$this->db->join('drivers d', 'd.id = r.driver_id', 'left');
		$this->db->join('customers c', 'c.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_brand tb', 'tb.id = t.brand', 'left');
		$this->db->join('taxi_colors tc', 'tc.id = t.color', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.driver_id', $driver_id)->where('r.is_country', $countryCode);
		$this->db->order_by('r.id', 'DESC');
		$q = $this->db->get();
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				if($row->cost ==''){
					$row->cost =  '0';
				}
				if($row->total_kms ==''){
					$row->total_kms =  '0';
				}
				if($row->drop_off ==''){
					$row->drop_off =  '0';
				}
				if($row->ride_start_time == '0000-00-00 00:00:00'){
					$row->ride_start_time =  '0';
				}
				if($row->ride_end_time == '0000-00-00 00:00:00'){
					$row->ride_end_time =  '0';
				}
                $data[] = $row;
            }
            return $data;
			
		}
		return false;	
	}
	
	function  mycurrentrides($driver_id, $countryCode){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id,  r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off, r.start_lat, r.start_lng, r.end_lat, r.end_lng,  t.name taxi_name, t.number,  IFNULL(p.total_fare, 0) as cost, IFNULL(p.total_distance, 0) as total_kms,  tt.name types,  d.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = '.$driver_id.'', 'left');
		$this->db->join('user_profile dp', 'dp.id = '.$driver_id.'', 'left');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.driver_id', $driver_id);
		$this->db->where('DATE(r.booked_on) <=', $current_date)->where('r.is_country', $countryCode);
		$this->db->where_in('r.status', array('2', '3', '4'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$this->db->limit(1);
		$q = $this->db->get();
		
		
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng, $unit = 'km', $decimals = 2,  $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->customer_name ==''){
					$row->customer_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				
				$row->sos = site_url('sos?id='.$row->ride_id);
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';	
				$waypoint_data = $this->site->getWaypoint($row->ride_id);
				$row->waypoint_data = $waypoint_data;
                $data[] = $row;
            }
			
            return $data;
			
		}
		
		
		return false;	
	}
	
	function  myonrides($driver_id, $countryCode){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.status, r.distance_km, r.distance_price, r.ride_timing as ride_start_time, r.start, r.end, r.payment_id,  r.start_lat, r.start_lng, r.start as pick_up, r.end as drop_off, r.end_lat, r.end_lng, r.driver_id, r.customer_id, r.taxi_id,   IFNULL(dcs.current_latitude, 0) current_latitude, IFNULL(dcs.current_longitude, 0) current_longitude, IFNULL(d.mobile, 0) as driver_mobile, IFNULL(d.first_name, 0) as driver_name, d.photo as driver_image, c.photo as customer_image, c.first_name as customer_name, c.mobile as customer_mobile, c.country_code as cus_code, c.photo as customer_photo,  IFNULL(t.name, 0) taxi_name, IFNULL(t.type, 0) type, t.photo as taxi_image, IFNULL(t.number, 0) number, IFNULL(tt.name, 0) types,  IFNULL(p.outstanding_from_last_trip, 0) as outstanding_from_last_trip, IFNULL(p.total_toll, 0) as total_toll, IFNULL(p.total_parking, 0) as total_parking, IFNULL(p.total_fare, 0) as total_fare, IFNULL(p.waiting_charge, 0) as waiting_charge,    IFNULL(p.final_total, 0) as final_total, IFNULL(p.total_distance, 0) as total_kms, i.json_data');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = r.driver_id', 'left');
		$this->db->join('user_profile dp', 'dp.user_id = r.driver_id', 'left');
		$this->db->join('driver_current_status dcs', 'dcs.driver_id = r.driver_id AND dcs.taxi_id = r.taxi_id', 'left');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.user_id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->join('incomplete_ride i', 'i.booking_id = r.id', 'left');
		
		$this->db->where('r.driver_id', $driver_id);
		$this->db->where('DATE(r.ride_timing)', $current_date);
		//$this->db->where('r.status', 'onride');
		//$this->db->or_where('r.status', 'booked');
		$this->db->where_in('r.status', array('1', '2', '3', '9'));
		$this->db->order_by('r.id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		
		//print_r($this->db->last_query());exit;
		
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$row->cost = number_format($row->final_total, 2);
				
				if($row->taxi_image !=''){
					$row->taxi_image = $tax_path.$row->taxi_image;
				}else{
					$row->taxi_image = $image_path.'no_image.png';
				}
				
				
				if($row->driver_image !=''){
					$row->driver_image = $image_path.$row->driver_image;
				}else{
					$row->driver_image = $image_path.'no_image.png';
				}
				
				
				if($row->customer_photo !=''){
					$row->customer_photo = $image_path.$row->customer_photo;
				}else{
					$row->customer_photo = $image_path.'no_image.png';
				}
				
				
				
                $data[] = $row;
            }
            return $data;
			
		}
		
		
		return false;	
	}
	
	
	function  mypastrides($driver_id, $countryCode,  $sdate, $edate){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id,  r.status, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off,  r.start_lat, r.start_lng, r.end_lat, r.end_lng,  IFNULL(p.outstanding_from_last_trip, 0) as outstanding_from_last_trip, IFNULL(p.total_toll, 0) as total_toll, IFNULL(p.total_parking, 0) as total_parking, IFNULL(p.total_fare, 0) as total_fare, IFNULL(p.waiting_charge, 0) as waiting_charge,    IFNULL(p.final_total, 0) as final_total, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  d.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = '.$driver_id.'', 'left');
		$this->db->join('user_profile dp', 'dp.id = '.$driver_id.'', 'left');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.driver_id', $driver_id);
		
		
		if(!empty($sdate) && !empty($edate)){
			$this->db->where('DATE(r.booked_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
			$this->db->where('DATE(r.booked_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
		}else{
			$this->db->where('DATE(r.booked_on) <=', $current_date);	
		}
			
		$this->db->where('r.is_country', $countryCode);
		$this->db->where_in('r.status', array('5', '6', '8'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				
				$row->cost = number_format($row->final_total, 2);
				
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng,  $unit = 'km', $decimals = 2, $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->customer_name ==''){
					$row->customer_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				$row->sos =  site_url('sos?id='.$row->ride_id);
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';	
				$waypoint_data = $this->site->getWaypoint($row->ride_id);
				$row->waypoint_data = $waypoint_data;
                $data[] = $row;
            }
            return $data;
			
		}
		
		
		return false;	
	}
	
	function  myupcomingrides($driver_id, $countryCode,  $sdate, $edate){
		$current_date = date('Y-m-d');
		$image_path = base_url('assets/uploads/');
		
		$this->db->select('r.id as ride_id, r.cab_type_id,  r.status, r.cab_type_id, r.ride_timing as ride_start_time, r.estimated_distance, r.estimated_fare, r.actual_distance, r.actual_fare, r.rating, r.start as pick_up, r.end as drop_off,  r.start_lat, r.start_lng, r.end_lat, r.end_lng,  IFNULL(p.outstanding_from_last_trip, 0) as outstanding_from_last_trip, IFNULL(p.total_toll, 0) as total_toll, IFNULL(p.total_parking, 0) as total_parking, IFNULL(p.total_fare, 0) as total_fare, IFNULL(p.waiting_charge, 0) as waiting_charge,    IFNULL(p.final_total, 0) as final_total, IFNULL(p.total_distance, 0) as total_kms, t.name taxi_name, t.number,  tt.name types,  d.first_name customer_name');		
		$this->db->from('rides r');
		$this->db->join('users d', 'd.id = '.$driver_id.'', 'left');
		$this->db->join('user_profile dp', 'dp.id = '.$driver_id.'', 'left');
		$this->db->join('users c', 'c.id = r.customer_id', 'left');
		$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
		$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
		$this->db->join('taxi_type tt', 'tt.id = r.cab_type_id', 'left');
		$this->db->join('ride_payment p', 'p.ride_id = r.id', 'left');
		$this->db->where('r.driver_id', $driver_id);
		
		if(!empty($sdate) && !empty($edate)){
			$this->db->where('DATE(r.booked_on) >=', date("Y-m-d", strtotime(str_replace('/', '-', $sdate))));
			$this->db->where('DATE(r.booked_on) <=', date("Y-m-d", strtotime(str_replace('/', '-', $edate))));
		}else{
			$this->db->where('DATE(r.booked_on) <=', $current_date);	
		}
		$this->db->where('r.is_country', $countryCode);
		$this->db->where_in('r.status', array('7'));
		//$this->db->or_where('r.status', 'cancelled');
		$this->db->order_by('r.id', 'DESC');
		$this->db->group_by('r.id');
		$q = $this->db->get();
		//print_r($this->db->last_query());die;
		if($q->num_rows()>0){
			
			foreach (($q->result()) as $row) {
				$row->cost = number_format($row->final_total, 2);
				
				if($row->end_lat != 0 && $row->end_lng != 0){
					$loc[$row->ride_id] = $this->site->GetDrivingDistanceNew($row->start_lat, $row->start_lng,  $row->end_lat, $row->end_lng, $unit = 'km', $decimals = 2,  $countryCode);
				}else{
					$loc[$row->ride_id] = '0';
				}
				
				$ride_status_array = array('1' => 'Request', '2' => 'Booked', '3' => 'Onride', '4' => 'Waiting', '5' => 'Completed', '6' => 'Cancelled', '7' => 'Ride Later', '8' => 'Rejected');
				
				if(array_key_exists($row->status, $ride_status_array)){
					
					$row->ride_status = $ride_status_array[$row->status];
				}
				
				if($row->taxi_name ==''){
					$row->taxi_name =  '0';
				}
				if($row->number ==''){
					$row->number =  '0';
				}
				
				if($row->types ==''){
					$row->types =  '0';
				}
				if($row->customer_name ==''){
					$row->customer_name =  '0';
				}
				if($row->estimated_fare ==''){
					$row->estimated_fare =  '0.00';
				}
				if($row->actual_fare ==''){
					$row->actual_fare =  '0.00';
				}
				if($row->actual_distance ==''){
					$row->actual_distance =  '0';
				}
				if($row->estimated_distance ==''){
					$row->estimated_distance =  '0';
				}
				
				$to_location = $this->site->findLocation($row->end_lat, $row->end_lng, $countryCode);
				
				$from_location = $this->site->findLocation($row->start_lat, $row->start_lng, $countryCode);
				
				$row->sos = site_url('sos?id='.$row->ride_id);
				$row->booking_id = $row->ride_id;
				$row->from_location = $from_location != FALSE ? $from_location : '0';
				$row->to_location = $to_location != FALSE ? $to_location : '0';	
				$row->start_lat = $row->start_lat;
				$row->start_lng = $row->start_lng;
				$row->end_lat = $row->end_lat;
				$row->end_lng = $row->end_lng;
				$row->total_km = $loc[$row->ride_id] ? $loc[$row->ride_id] : '0';	
				$waypoint_data = $this->site->getWaypoint($row->ride_id);
				$row->waypoint_data = $waypoint_data;
                $data[] = $row;
            }
            return $data;
			
		}
		
		
		return false;	
	}
	
	
	
	function getContinents($country_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$q = $this->db->get('continents');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getCountries($continent_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$this->db->where('continent_id', $continent_id);
		$q = $this->db->get('countries');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getZones($country_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$this->db->where('country_id', $country_id);
		$q = $this->db->get('zones');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getState($zone_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$this->db->where('zone_id', $zone_id);
		$q = $this->db->get('states');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getCities($state_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$this->db->where('state_id', $state_id);
		$q = $this->db->get('cities');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getAreas($city_id, $countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$this->db->where('city_id', $city_id);
		$q = $this->db->get('areas');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	function getCitieswithstate($countryCode){
		$this->db->select('id, name')->where('is_country', $countryCode);
		$q = $this->db->get('cities');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	
	
	function driverpayment($user_id, $countryCode){
		$this->db->select('amount, payment_type, transaction_no, send_status, recived_status, status, payment_date');
		$this->db->where('driver_id', $user_id)->where('is_country', $countryCode);
		$q = $this->db->get('driver_payment');
		if($q->num_rows()>0){
			foreach (($q->result()) as $row) {
				$data[] = $row;	
			}
			return $data;
		}
		return FALSE;
	}
	
	
		
	function getDriverTaxi($driver_id, $countryCode){
		$this->db->select('*');
		$this->db->where('driver_id', $driver_id)->where('is_country', $countryCode);
		$q = $this->db->get('taxi');
		
		if($q->num_rows()>0){
		    $data =  $q->row();
			return $data;
		}
		return false;		
	}
	
	function driverTimeout($data, $countryCode){
		$data['is_country'] = $countryCode;
		if($this->db->insert('driver_booking', $data)){
		
			return true;
		}
		return false;	
	}
	
	function driveraccept($update_taxi, $update_driver, $ride_routes, $ride_id, $driver_id, $countryCode){
		
		$image_path = base_url('assets/uploads/');
		//$cus_path = base_url('assets/uploads/customers/photo/');
		$this->db->where('id', $ride_id);
		
		$d = $this->db->update('rides', $update_taxi);
		if($d){
			if(!empty($driver_id)){
				$this->db->where('ride_id', $ride_id);
				$this->db->update('bookingcrm', array('driver_id' => $driver_id));
			}
			if(!empty($update_driver)){
				$update_driver['is_country'] = $countryCode;
				$this->db->insert('driver_booking', $update_driver);
			}
			if(!empty($ride_routes)){
				$ride_routes['is_country'] = $countryCode;
				$this->db->insert('ride_route', $ride_routes);	
			}
			if($driver_id){
				$this->db->where('driver_id',$driver_id);
				
				$this->db->update('driver_current_status', array('mode' => 2));
			}
			
			if($ride_id){
			$this->db->select('r.*,  d.first_name driver_name, d.email, d.mobile, d.country_code as driver_country_code, d.gender, d.photo as driver_photo, d.photo customer_photo, c.first_name customer_name, c.mobile customer_mobile, c.country_code customer_country_code,  t.name taxi_name, 	t.color, t.model, t.number, t.type, tt.name types');		
			$this->db->from('rides r');
			$this->db->join('users d', 'd.id = r.driver_id', 'left');
			$this->db->join('user_profile dp', 'dp.id = r.driver_id', 'left');
			$this->db->join('users c', 'c.id = r.customer_id', 'left');
			$this->db->join('user_profile cp', 'cp.id = r.customer_id', 'left');
			$this->db->join('taxi t', 't.id = r.taxi_id', 'left');
			
			$this->db->join('taxi_type tt', 'tt.id = t.type', 'left');
			$this->db->where('r.id', $ride_id);
			
			$q = $this->db->get();
			
				if($q->num_rows() > 0){
					$row = $q->row();
					
					if($row->driver_photo !=''){
						$row->driver_photo = $image_path.$row->driver_photo;
					}else{
						$row->driver_photo = $image_path.'no_image.png';
					}
					
					
					if($row->customer_photo !=''){
						$row->customer_photo = $cus_path.$row->customer_photo;
					}else{
						$row->customer_photo = $image_path.'no_image.png';
					}
					
					return $row;
				}
			}
		
		}
		
		return false;
		
	}
	
		function modify_driver($user_id, $user, $user_profile, $user_address, $user_bank, $user_document, $taxi, $taxi_document, $driver_group_id, $customer_type, $countryCode){
		
		
		if($customer_type == 'taxi'){
			if(!empty($taxi)){
				$taxi['driver_id'] = $user_id;
				$taxi['is_country'] = $countryCode;
				$this->db->insert('taxi', $taxi);
				
				if($taxi_id = $this->db->insert_id()){
					$taxi_document['taxi_id'] = $taxi_id;
					$taxi_document['user_id'] = $user_id;
					$taxi_document['group_id'] = $driver_group_id;
					$taxi_document['is_country'] = $countryCode;
					$this->db->insert('taxi_document', $taxi_document);
					
					if(!empty($taxi_id) && !empty($user_id)){
						$this->db->insert('driver_current_status', array('driver_id' => $user_id, 'taxi_id' => $taxi_id, 'vendor_id' => $parent_id,  'is_country' => $countryCode));
					}
					
				}
				
				
			}
		
		}elseif($customer_type == 'bank'){
			if($user_id){
				$this->db->update('user_bank', $user_bank, array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				
			}
		}elseif($customer_type == 'profile'){
			if($user_id){
				//$this->db->update('users', array('is_edit' => 0), array('id' => $user_id));
				$this->db->update('user_document', $user_document, array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$this->db->update('user_address', $user_address, array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$this->db->update('user_profile', $user_profile, array('user_id' => $user_id, 'is_edit' => 1, 'is_country' => $countryCode));
				$this->db->update('users', $user, array('id' => $user_id, 'is_country' => $countryCode));
				
			}
		
		}
		
		if(!empty($user_id)){
			$b = $this->db->select('account_holder_name, account_no, bank_name, branch_name, ifsc_code, is_verify')
				->where('user_id', $user_id)->where('is_edit', 1)->where('is_country', $countryCode)->get('user_bank');
				
				if($b->num_rows()>0){
					if(!empty($b->row('account_holder_name')) && !empty($b->row('account_no')) && !empty($b->row('bank_name')) && !empty($b->row('ifsc_code')) && $b->row('is_verify') == 0){
						$bank_status = '1';	
					}else{
						$bank_status = '0';	
					}
				}else{
					$bank_status = '0';
				}
				
				$d = $this->db->select('ud.user_id, ud.aadhaar_image, ud.pancard_image, up.gender')->from('user_document ud')->join('user_profile up', 'up.user_id = ud.user_id AND up.is_edit = 1')
				->where('ud.user_id', $user_id)->where('ud.is_edit', 1)->where('ud.is_country', $countryCode)->get();
				
				if($d->num_rows()>0){
					if(empty($d->row('aadhaar_image')) && empty($d->row('pancard_image')) && empty($d->row('gender')) ){
						$document_status = '0';	
					}else{
						$document_status = '1';	
					}
				}else{
					$document_status = '0';
				}
				
				$t = $this->db->select('*')->where('driver_id', $user_id)->where('is_edit', 1)->get('taxi');
				
				if($t->num_rows()>0){
					$taxi_status = '1';	
				}else{
					$taxi_status = '0';
				}
				
				$response = array(
					'bank_status' => $bank_status,
					'document_status' => $document_status,
					'taxi_status' => $taxi_status
				);
				
				return $response;
		}
		
		return false;
    }
	
	function username_Exist($key)
	{
		$this->db->where('oauth_token', $key);
		$query = $this->db->get('users');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	
	function getWalletoffer($countryCode){
		$this->db->select('*');
		$this->db->where('is_country', $countryCode);
		$this->db->order_by('is_default', 'desc');
		$q = $this->db->get('walletoffer');
		if($q->num_rows()>0){
			return $q->result();	
		}
		return false;
			
	}
	
	function checkWallet($user_id, $countryCode){
		$query = "Select 
		SUM(CASE When wallet_type='1' AND flag = '1' Then cash Else 0 End ) as CashIncentive,
		SUM(CASE When wallet_type='1' AND flag = '2' Then cash Else 0 End ) as CashRides,
		SUM(CASE When wallet_type='1' AND flag = '3' Then cash Else 0 End ) as CashRefunded,
		SUM(CASE When wallet_type='1' AND flag = '4' Then cash Else 0 End ) as CashDeduction,
		SUM(CASE When wallet_type='1' AND flag = '5' Then cash Else 0 End ) as CashTransfer,
		SUM(CASE When wallet_type='1' AND flag = '6' Then cash Else 0 End ) as CashAddMoney,
		SUM(CASE When wallet_type='1' AND flag = '7' Then cash Else 0 End ) as CashSentMoney
		from {$this->db->dbprefix('wallet')}
		Where wallet_type='1' AND user_type = 2 AND user_id = ".$user_id." ";
		$q = $this->db->query($query);
		//print_r($this->db->last_query());
		
		if($q->num_rows()>0){
			$CashPaymentAmount = $q->row('CashRides') + $q->row('CashRefunded') + $q->row('CashAddMoney') + $q->row('CashIncentive') +  $q->row('CashTransfer') - $q->row('CashDeduction') - $q->row('CashSentMoney');
			
			$CreditPaymentAmount = $q->row('CreditRides') + $q->row('CreditRefunded') + $q->row('CreditAddMoney') + $q->row('CreditIncentive') + $q->row('CreditTransfer') - $q->row('CreditDeduction') - $q->row('CreditSentMoney');
			
			/*$data = array(
				'CashPaymentAmount' => $CashPaymentAmount,
				'CreditPaymentAmount' => $CreditPaymentAmount
			);*/
			//CashPaymentAmount
			//CreditPaymentAmount
			return $CashPaymentAmount;
		}
		return 0;
	}
	
	function rideStatus($user_id, $countryCode){
		$this->db->select('is_daily, is_rental, is_outstation');
		$this->db->where('driver_id', $user_id);
		$q = $this->db->get('taxi');
		if($q->num_rows()>0){
			return $q->row();
		}
		return false;
	}
	
	function getOfferWalletamount($offer_id, $countryCode){
		
		$this->db->select('*');
		$this->db->where('id', $offer_id);
		$q = $this->db->get('walletoffer');
		if($q->num_rows()>0){
			
			return $q->row();
		}
		return 0;
	}	
    
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
	
	function insertWaypointtime($res, $countryCode){
		if($this->db->insert('ride_waypoint_time', $res)){
			return true;
		}
		return false;
	}
	
	function waitingride($res, $countryCode){
		if($this->db->insert('ride_waiting', $res)){
			return true;
		}
		return false;
	}
	
	function add_booking($insert, $ride_insert, $ride_type, $ride_timing, $countryCode, $customer_id, $offer_code, $waypoint_array){
		
		$image_path = base_url('assets/uploads/');
		$insert['is_country'] = $countryCode;
		$this->db->insert('rides', $insert); //print_r($this->db->last_query());exit;
		if($ride_id = $this->db->insert_id()){
			if($offer_code != ''){
				$offer = $this->db->select('offer_fare_type, offer_fare')->where('offer_code', $offer_code)->get('offers');
				
				$this->db->insert('offers_user', array('user_id' => $customer_id, 'ride_id' => $ride_id, 'offer_code' => $offer_code, 'offer_type' => $offer->offer_fare_type, 'discount' => $offer->offer_fare, 'created_on' => date('Y-m-d H:i:s'), 'is_country' => $countryCode));
				
			}
			$booking_no = 'BK'.str_pad($ride_id, 5, 0, STR_PAD_LEFT);
			$this->db->update('rides', array('booking_no' => $booking_no), array('id' => $ride_id, 'is_country' => $countryCode));
			foreach($ride_insert as $ride){
				$ride['ride_id'] = $ride_id;
				$ride['is_country'] = $countryCode;
				$this->db->insert('ride_route', $ride);
				if($ride_type == 2){
					$this->db->insert('ride_later', array('ride_id' => $ride_id, 'timing' => $ride_timing, 'is_country' => $countryCode));	
				}
			}
			if(!empty($waypoint_array)){
				foreach($waypoint_array as $waypoint){
				$waypoint['ride_id'] = $ride_id;				
				$this->db->insert('ride_waypoints', $waypoint);				
				}
			}
			
			$new_ride = $this->site->newBookingRideGET($ride_id);
			
			return $new_ride;
		}
		
		return 0;
    }
	
	function getDrivers_radiusNEW($data, $countryCode){
		
		
		
	$image_path = base_url('assets/uploads/');
	
	if($data['taxi_type'] != ''){
		$where = "  AND FIND_IN_SET(".$data['taxi_type'].", t.multiple_type)";
	}else{
		$where = "  ";
	}
	
	
	$query = "SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.id as taxi_id, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, ti.image, ti.image_hover, ti.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians({$data['latitude']}) ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians({$data['longitude']}) ) + sin( radians({$data['latitude']}) ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM {$this->db->dbprefix('users')}  AS d 
	LEFT JOIN {$this->db->dbprefix('driver_current_status')} AS dcs ON dcs.driver_id = d.id  
	LEFT JOIN {$this->db->dbprefix('user_profile')} AS up ON up.user_id = d.id  
	LEFT JOIN {$this->db->dbprefix('taxi')} AS t ON t.id = dcs.taxi_id  
	LEFT JOIN {$this->db->dbprefix('taxi_type')} AS tt ON tt.id = t.type
	LEFT JOIN {$this->db->dbprefix('taxi_image')} AS ti ON ti.id = tt.taxi_image_id 
	LEFT JOIN {$this->db->dbprefix('user_setting')} AS us ON us.user_id = d.id  
	LEFT JOIN {$this->db->dbprefix('groups')} AS g ON g.id = d.group_id 
	WHERE d.is_country = '".$countryCode."' AND   (us.ride_stop = 1 OR us.ride_stop = 0)  AND (dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1    ".$where." GROUP BY d.id HAVING distance <= {$data['distance']} 
ORDER BY distance ASC";

//(dcs.mode = 1 OR dcs.mode = 3)   AND dcs.is_connected = 1  AND dcs.allocated_status = 1 
	$q = $this->db->query($query);
	
	//print_r($this->db->last_query());die;
	
	if($q->num_rows()>0){
	    $r = $q->result();
	    foreach($r as $row){
			if($data['exit_driver'] != $row->id){
				if($row->driver_photo !=''){
					$row->driver_photo = $image_path.$row->driver_photo;
				}else{
					$row->driver_photo = $image_path.'no_image.png';
				}
				
				if($row->taxi_photo !=''){
					$row->taxi_photo = $image_path.$row->taxi_photo;
				}else{
					$row->taxi_photo = $image_path.'no_image.png';
				}	
				
				if($row->image !=''){
					$row->image = $image_path.$row->image;
				}else{
					$row->image = '';
				}
				
				if($row->image_hover !=''){
					$row->image_hover = $image_path.$row->image_hover;
				}else{
					$row->image_hover = '';
				}
				
				if($row->type_image !=''){
					$row->type_image = $image_path.$row->type_image;
				}else{
					$row->type_image = '';
				}
				
				$d[] = $row;
			}
	    }
	    
	    return $d;
	}
	return false;
    }
	

	function checkHealth($driver_id){
		$currentdate = date('Y-m-d H:i:s');
		$this->db->select('id');
		$this->db->from('health_driver');
		$this->db->where('health_start_date <=', $currentdate);
		$this->db->where('health_end_date >=', $currentdate);
		$this->db->where('driver_id', $driver_id);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get();
		if($q->num_rows()>0){
			return true;
		}
		return false;

	}

	function gethealthname($health_id){
		$this->db->select('health_name');
		
		$this->db->where('id', $health_id);
		$q = $this->db->get('health');
		if($q->num_rows()>0){
			return $q->row('health_name');
		}
		return false;
	}

	function insertHealth($insert){
		$q = $this->db->insert('health_driver', $insert);
		if($q){
			return true;
		}
		return false;
	}

}
