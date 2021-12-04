<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct() {
        parent::__construct();
	$this->load->library('ion_auth');
    }

    public function get_total_qty_alerts() {
        $this->db->where('quantity < alert_quantity', NULL, FALSE)->where('track_quantity', 1);
        return $this->db->count_all_results('products');
    }


	/*BBQ*/
	public function getBBQbuyxgetxDAYS($days){
		$this->db->where('days', $days);
		$q = $this->db->get('bbq_buyx_getx');
        if ($q->num_rows() == 1) {
            return $q->row();
        }
		return FALSE;
	}
	
	public function getCustomerDetails($waiter_id, $table_id, $split_id){
		$this->db->select('*');
		$this->db->where('table_id', $table_id);
		$this->db->where('split_id', $split_id);
		$this->db->where('created_by', $waiter_id);
		$this->db->group_by('orders.split_id');
		$q = $this->db->get('orders');
		if ($q->num_rows() == 1) {
            return $q->row('customer_id');
        }
		return FALSE;
	}
	public function CalculationBBQbuyget($buy, $get, $total_number){
		
		$paid = 0;
		if(!empty($buy) && !empty($get)){
			
			$quotient = (int)($total_number / $buy);
			$paid = ($get * $quotient);
			
			return $paid;
		}
		return $paid;
	}
	
	public function getBBQdataCode($reference_no){
		$this->db->select('bbq.*, restaurant_tables.name as table_name');
		$this->db->join('restaurant_tables', 'restaurant_tables.id = bbq.table_id');
		$this->db->where('bbq.reference_no', $reference_no);
		$q = $this->db->get('bbq');
        if ($q->num_rows() == 1) {
			
            return $q->row();
        }
		return FALSE;
	}
	public function splitBBQCheckSalestable($split_id){
		$q = $this->db->get_where('sales', array('sales_split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
            return TRUE;
        }
        return FALSE;
	}
	public function BBQcheckTable($val){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$this->db->where('table_id', $val);
		$this->db->where('payment_status', '');
		$this->db->where('cancel_status', 0);
		//$this->db->where('created_on', $current_date);
		$q = $this->db->get('bbq');
		if ($q->num_rows() > 0) {
            return $q->row();
        }
		return FALSE;	
	}
	public function ordertypeTables($table_id){
		
		$current_date = date('Y-m-d');
		$this->db->where('table_id', $table_id);
		$this->db->where('order_status', 'Open');
		$this->db->where('DATE(date)', $current_date);
		$this->db->group_by('table_id');
		$this->db->order_by('id', 'DESC');
		$q = $this->db->get('orders');
		if ($q->num_rows() > 0) {
			
            return $q->row('order_type');
        }
		return FALSE;	
	}
	public function BBQcheckorders($table_id, $split_id, $customer_id){
		
		$this->db->where('bbq.reference_no', $split_id);
		$this->db->where('bbq.table_id', $table_id);
		$this->db->where('bbq.customer_id', $customer_id);
		$this->db->where('bbq.status', 'Open');
		$this->db->where('bbq.cancel_status', 0);
		$q = $this->db->get('bbq');
        if ($q->num_rows() == 1) {
			
            return TRUE;
        }
		return FALSE;
	}
	public function orderBBQTablecheck($val){
		
		$current_date = date('Y-m-d');
		
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";
//DATE(O.date) ='".$current_date."'  AND 
		$q = $this->db->query($main);
		
		if ($q->num_rows() > 0) {

			foreach (($q->result()) as $row) {
              
			 
				if($this->session->userdata('user_id') == $row->waiter_id){

					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bbq_bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id =".$val." ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }

				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function getbbqCategoryByID($id) {
        $q = $this->db->get_where('bbq_categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getBBQcurrentDiscount(){
	
		return false;	
	}
	public function GetAllBBQDiscounts() {
    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_bbq');
        // print_r($this->db->last_query());die;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function BBQsplitCountcheck($split_id){
		$current_date = date('Y-m-d');
		$q = $this->db->select('bbq.number_of_adult, bbq.number_of_child, bbq.number_of_kids')->where('bbq.reference_no', $split_id)->where('bbq.created_on', $current_date)->get('bbq');
		if ($q->num_rows() > 0) {
				$number_of_adult = $q->row('number_of_adult');
				$number_of_child = $q->row('number_of_child');
				$number_of_kids = $q->row('number_of_kids');
				
			return $data = $number_of_adult + $number_of_child + $number_of_kids;
		}
		return $data = 0;
	}
	
	function BBQgenerate_bill_number($tableWhitelisted){ 
	$billNumReset = $this->Settings->billnumber_reset;
	$today = time();//strtotime('2018-05-01');
	switch($billNumReset){
	    case 1://daily
		$start_time = date('Y-m-d 00:00:01');
		$end_time = date('Y-m-d 23:59:59');
		$billnumber = $this->BBQgetbillNumber($tableWhitelisted,$start_time,$end_time,'daily');
		break;
	    case 2://weekly
		$start_date = date('Y-m-d', strtotime('monday this week', $today));
		$end_date = date('Y-m-d', strtotime('sunday this week', $today));
		$billnumber = $this->BBQgetbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 3://monthly
		$start_date = date('Y-m-01', $today);
		$end_date = date('Y-m-t', $today);
		$billnumber = $this->BBQgetbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 4://yearly
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$start_date = date('Y-'.$financial_yr_from[1].'-'.$financial_yr_from[0], $today);
		$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    default://none
		$billnumber = $this->BBQgetbillNumber($tableWhitelisted);
		break;
	    
	}
	return $billnumber;
    }


 public function CheckConsolidate($splits){

    	$myQuery = "SELECT P.bill_number
			FROM ".$this->db->dbprefix('bils')." AS P		
			JOIN ". $this->db->dbprefix('sales') ." AS S ON S.id = P.sales_id   	
			Where S.sales_split_id ='".$splits."' ";			
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
        	$res = $q->row();
            return $res->bill_number;           
        }
        return FALSE;       
	}


    function BBQgetbillNumber($tableWhitelisted,$start=null,$end=null,$case=null){ 
	$this->db->select();
	if($case == "daily" && $start && $end){
	    $this->db->where(array('date>='=>$start,'date<'=>$end)); 
	}else if($case != "daily" && $start && $end){
	    $this->db->where(array('DATE(date)>='=>$start,'DATE(date)<'=>$end));
	}
	
	$this->db->where('bill_number!=','');
	if($tableWhitelisted){ 
	    $this->db->where('table_whitelisted',1);
	}else{
	    $this->db->where('table_whitelisted',0);
	}
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get('bils');
	if(!$tableWhitelisted){
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		if($result->bill_number[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($result->bill_number)."d",intval($result->bill_number)+1);
		}else {$bill_no = intval($result->bill_number)+1;}
		return $bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $bill_no;
	    }
	}else{
	    $billPrefix = 'tw-';
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		$prevbillno = str_replace($billPrefix,'',$result->bill_number);
		if($prevbillno[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($prevbillno)."d",intval($prevbillno)+1);
		}else {
		    $bill_no = intval($prevbillno)+1;
		    }
		return $billPrefix.$bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $billPrefix.$bill_no;
	    }
	}
	
    }
	
	/*BBQ END*/
	
	public function dineinbbqbothCheck($split_id){
		$this->db->select('GROUP_CONCAT(order_type) AS order_type');
		$this->db->where('split_id', $split_id);
		$q= $this->db->get('orders');
		if($q->num_rows() > 0){
			$data = array_unique(explode(',', $q->row('order_type')));
			if($data[0] == 4 && $data[1] == 1){
				return TRUE;	
			}
			
		}
		return FALSE;
	}
	
    public function get_expiring_qty_alerts() {
        $date = date('Y-m-d', strtotime('+3 months'));
        $this->db->select('SUM(quantity_balance) as alert_num')
        ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
        ->where('expiry <', $date);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (INT) $res->alert_num;
        }
        return FALSE;
    }

	
	public function create_notification($notification_array = array()){
	    $this->load->library('socketemitter');
		
		if(!empty($notification_array)){	
		
			$all = $this->db->insert('notiy', $notification_array['insert_array']);	
			$notifyid = $this->db->insert_id();
			if(isset($notification_array['from_role']) && $notification_array['from_role'] != SALE){	
				
				if($notification_array['from_role'] == WAITER){
					$role_form = 'Waiter';
				}elseif($notification_array['from_role'] == KITCHEN){
					$role_form = 'Kitchen';
				}elseif($notification_array['from_role'] == CASHIER){
					$role_form = 'Cashier';					
				}
				
				
				if($notification_array['insert_array']['role_id'] == WAITER){
					$role_to = 'Waiter';
				}elseif($notification_array['insert_array']['role_id'] == KITCHEN){
					$role_to = 'Kitchen';
				}elseif($notification_array['insert_array']['role_id'] == CASHIER){
					$role_to = 'Cashier';					
				}
				
				$notification = array(
					'msg' => $role_form.' to  '.$role_to,
					'type' => $notification_array['insert_array']['type'],
					'user_id' => $notification_array['insert_array']['user_id'],	
					'table_id' => $notification_array['insert_array']['table_id'],	
					'role_id' => SALE,
					'warehouse_id' => $notification_array['insert_array']['warehouse_id'],
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
				$s = $this->db->insert('notiy', $notification);	
				
					
			}
			
			
			if($notification_array['customer_role'] == CUSTOMER){
				$notification_customer = array(
					'msg' => $notification_array['customer_msg'],
					'type' => $notification_array['customer_type'],
					'user_id' => $notification_array['customer_id'],	
					'table_id' => $notification_array['insert_array']['table_id'],	
					'role_id' => CUSTOMER,
					'warehouse_id' => $notification_array['insert_array']['warehouse_id'],
					'created_on' => date('Y-m-d H:m:s'),
					'is_read' => 0
				);	
								
				$c = $this->db->insert('notiy', $notification_customer);	
			}
			$notification_title = $notification_array['insert_array']['type'];
			$notification_message = $notification_array['insert_array']['msg'];
			if($this->isSocketEnabled()){
			    $emit_notification['title'] = $notification_title;
			    $emit_notification['msg'] = $notification_message;
			    $this->socketemitter->setEmit('notification', $emit_notification);
			}
			return $notifyid;
		}
		return false;
	}
	
	public function notification_clear($notification_id){
		
		if(!empty($notification_id)){	
			
			$this->db->where_in('id', explode(',',$notification_id));
			$this->db->update('notiy', array('is_read' => 1));			
			
			return true;
		}
		return false;
	}
	
	public function request_count($group_id, $user_id, $warehouse_id){
		$current_date = date('Y-m-d');
		$data = array();
		//$req = $this->db->select('*')->where('warehouse_id', $warehouse_id)->where('DATE(date)', $current_date)->where('bilgenerator_type', 1)->where('customer_discount_status', 'pending')->get('bils');
		$req = $this->db->select('*')->where('warehouse_id', $warehouse_id)->where('DATE(date)', $current_date)->where('payment_status', NULL)->get('bils');
		if ($req->num_rows() > 0) {
			foreach($req->result() as $row){
				$reqbil[] = $row;
			}
		}
		$data['list'] = $reqbil;
		if(!empty($data['list'])){
			$data['req_length'] = count($data['list']);
			return $data;
		}else{
			return false;
		}
	}
	
	public function notification_count($group_id, $user_id, $warehouse_id){
		$current_date = date('Y-m-d');
		$data = array();
		
		$u = $this->db->select('*')->where('to_user_id', $user_id)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($u->num_rows() > 0) {
			foreach($u->result() as $uow){
				$user[] = $uow;
			}
		}
		
		/*$r =$this->db->select('*')->where('role_id', $group_id)->where('to_user_id', 0)->where('warehouse_id', $warehouse_id)->where('is_read', 0)->where('DATE(created_on)', $current_date)->get('notiy');
		if ($r->num_rows() > 0) {
			foreach($r->result() as $row){
				$group[] = $row;
			}
		}
		if(!empty($user) && empty($group)){
			$data['list'] = $user;
		}elseif(empty($user) && !empty($group)){
			$data['list'] = $group;
		}elseif(!empty($user) && !empty($group)){
			$data['list'] = array_merge($user, $group);
		}*/

		if(!empty($user)){
			$data['list'] = $user;
		}
		
		if(!empty($data['list'])){
			$data['count'] = count($data['list']);
			return $data;
		}else{
			return false;
		}
				
		
	}
	
	
	
    public function get_shop_sale_alerts() {
        $this->db->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
        ->where('sales.shop', 1)->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
        ->group_start()->where('deliveries.status !=', 'delivered')->or_where('deliveries.status IS NULL', NULL)->group_end();
        return $this->db->count_all_results('sales');
    }

    public function get_shop_payment_alerts() {
        $this->db->where('shop', 1)->where('attachment !=', NULL)->where('payment_status !=', 'paid');
        return $this->db->count_all_results('sales');
    }

    public function get_setting() {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function get_posSetting()
    {	
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }	
	public function getWaiter($split_id){
		
		$q = $this->db->get_where('orders', array('split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row('created_by');
        }
		return FALSE;
	}
	
	public function getOrderCustomerDATA($split_id){
		
		$q = $this->db->get_where('orders', array('split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
			
            return $q->row('customer_id');
        }
		return FALSE;
	}
	
	public function devicesCheck($api_key){
		$q = $this->db->get_where('api_keys', array('key' => $api_key), 1);		
        if ($q->num_rows() == 1) {
			
            return $q->row('devices_key');
        }
		return FALSE;
	}
	
	public function splitCheckSalestable($split_id){
		$q = $this->db->get_where('sales', array('sales_split_id' => $split_id), 1);
        if ($q->num_rows() == 1) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function splitCountcheck($split_id){
		$current_date = date('Y-m-d');
		$q = $this->db->select('orders.id AS order_id, order_items.id AS item_id')->join('order_items', 'order_items.sale_id = orders.id AND order_items.order_item_cancel_status = 0')->where('orders.split_id', $split_id)->where('orders.order_cancel_status', 0)->where('DATE(date)', $current_date)->get('orders');
		if ($q->num_rows() > 0) {
				$i=0;
				foreach (($q->result()) as $row) {
					$i++;
				}
			return $data = $i;
		}
		return $data = 0;
	}
	
	
	
	public function FinalamountRound($amount){
		
		$checkamount = $amount % 100;
		$extraamount = (100 - $amount % 100 ?: 100);
		if($checkamount < 50){
			 $grand_amount = $amount - $checkamount;
		}else{
			$grand_amount = $amount + $extraamount;
		}
		
		return $grand_amount;

	}
	
	public function checkTableStatus($table_id){
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$items['a'] = $this->db->select('COUNT(id) AS count_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->get()->result();
		$items['b'] = $this->db->select('COUNT(id) AS count_not_null', false)
		->from('orders')
		->where('orders.table_id', $table_id)
		//->where('DATE(date)', $current_date)
		->where('orders.payment_status', 'Paid')
		->get()->result();
		$items = array_merge($items['a'], $items['b']);
		if($items[0]->count_null == $items[1]->count_not_null){
			return TRUE;
		}
        return FALSE;
	}
	
	public function checkBuyget($recipe_id){
		
		$current_date = date('Y-m-d');
		$current_time = date('H:i:s');
		
		$check_get_x = 'buy_x_get_x';
		$check_get_y = 'buy_x_get_y';
		
		$buy_query_x = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		WHERE buy_get.buy_method = '".$check_get_x."' AND buy_get_items.buy_item = ".$recipe_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time ORDER BY buy_get.id DESC LIMIT 1";
		
		$buy_query_y = "SELECT buy_get.id, buy_get.buy_method, buy_get.buy_quantity, buy_get.get_quantity, buy_get_items.get_item, recipe.name AS free_recipe FROM ".$this->db->dbprefix('buy_get')." AS buy_get
		JOIN  ".$this->db->dbprefix('buy_get_items')." AS buy_get_items ON buy_get_items.buy_get_id = buy_get.id 
		JOIN ".$this->db->dbprefix('recipe')." AS recipe ON recipe.id = buy_get_items.get_item
		WHERE buy_get.buy_method = '".$check_get_y."' AND buy_get_items.buy_item = ".$recipe_id."  AND '".$current_date."' BETWEEN buy_get.start_date AND buy_get.end_date AND '".$current_time."' BETWEEN buy_get.start_time AND buy_get.end_time ORDER BY buy_get.id DESC LIMIT 1";
		
			
		$x = $this->db->query($buy_query_x);
		$y = $this->db->query($buy_query_y);
		
		if ($x->num_rows() > 0) {
			return $x->row();
		}elseif($y->num_rows() > 0){
			return $y->row();
		}
		 return FALSE;
	}
	public function allOrdersCancelStatus($order_id){
		$q = $this->db->get_where('order_items', array('sale_id' => $order_id, 'order_item_cancel_status' => 0));
        if ($q->num_rows() == 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	
	
	
	
	public function orderBBQTablecheckapi($val, $user_id){
		
		$current_date = date('Y-m-d');
		$current_date = $this->getTransactionDate();
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";
//DATE(O.date) ='".$current_date."'  AND 
		$q = $this->db->query($main);
		
		if ($q->num_rows() > 0) {

			foreach (($q->result()) as $row) {
              
				 $p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
			  if ($p->num_rows() > 0) { 
			  	if($p->row('pos-view_allusers_orders') == 0){
					if($user_id == $row->waiter_id){
						$other = 1;
					}else{
						$other = 0;
					}
				}else{
					$other = 1; 
				}
			  }else{
				 $other = 1; 
			  }
			  
				if($other == 1){

					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id =".$val." ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }

				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function orderTablecheck($val){
		
		$current_date = date('Y-m-d');
		
		
		$main = "SELECT KO.waiter_id
					FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
					JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
					JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
					WHERE DATE(O.date) ='".$current_date."'  AND RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";

		$q = $this->db->query($main);
		
		if ($q->num_rows() > 0) {

			foreach (($q->result()) as $row) {
              
			 
				$p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
				  if ($p->num_rows() > 0) { 
					if($p->row('pos-view_allusers_orders') == 0){
						if($user_id == $row->waiter_id){
							$other = 1;
						}else{
							$other = 0;
						}
					}else{
						$other = 1; 
					}
				  }else{
					 $other = 1; 
				  }
				if($other == 1){

					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }

					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id =".$val." ORDER BY O.id DESC limit 1";
						
						$q = $this->db->query($myQuery);
					
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            return $res->table_status;
				        }					
				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	public function orderTablecheckapi($val, $user_id, $group_id){
		
		$current_date = date('Y-m-d');		

		 $main = "SELECT KO.waiter_id
			FROM " . $this->db->dbprefix('restaurant_table_orders') . " AS RTO
			JOIN " . $this->db->dbprefix('kitchen_orders') . " KO ON KO.sale_id = RTO.order_id
			JOIN " . $this->db->dbprefix('orders') . " O ON O.id = RTO.order_id AND O.order_cancel_status = 0
			WHERE DATE(O.date) ='".$current_date."'  AND RTO.table_id='".$val."' AND O.payment_status is null GROUP BY RTO.table_id";

		$q = $this->db->query($main);
		
		
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
              
			  $p = $this->db->select('*')->where('group_id', $group_id)->get('permissions');
			  if ($p->num_rows() > 0) { 
			  	if($p->row('pos-view_allusers_orders') == 0){
					if($user_id == $row->waiter_id){
						$other = 1;
					}else{
						$other = 0;
					}
				}else{
					$other = 1; 
				}
			  }else{
				 $other = 1; 
			  }
				if($other == 1){
					
					$splits = "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."'  ORDER BY O.id DESC ";
					
					/*$splits= "SELECT O.split_id
					FROM " . $this->db->dbprefix('orders') . " AS O
					JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
					WHERE T.table_id ='".$val."' ";*/

					$s = $this->db->query($splits);
					
			        if ($s->num_rows() > 0) {
			            $spt = $s->row();
			            $split = $spt->split_id;
			        }
					
					$myQuery = "SELECT (CASE
					        WHEN ((OI.item_status = 'Inprocess') OR (OI.item_status = 'Preparing') OR (OI.item_status = 'Cancel')) THEN 'In_Kitchen'
					        WHEN (OI.item_status = 'Ready') THEN 'READY'
					        WHEN ((OI.item_status = 'Closed') OR (OI.item_status = 'Served') OR (B.payment_status ='null'))  THEN 'PENDING'
					        WHEN (OI.item_status = 'Served') THEN 'SERVED'					       
					        ELSE 'Available'
					        END) AS table_status
						FROM " . $this->db->dbprefix('orders') . " AS O
						JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
						JOIN " . $this->db->dbprefix('restaurant_table_orders') . " T ON T.order_id = O.id
						LEFT JOIN " . $this->db->dbprefix('bils') . " B ON B.sales_id = O.id
						WHERE O.split_id ='".$split."' AND T.table_id ='".$val."' ORDER BY O.id DESC limit 1";
						/*echo $myQuery;die;*/
						$q = $this->db->query($myQuery);
					
				        if ($q->num_rows() > 0) {
				            $res = $q->row();
				            $result =  $res->table_status;
				        }
						
					//$result = 'Ongoing';
				}else{
					$result = 'Ongoingothers';
				}
            }
			
			return $result;
		}else{
			return $result = 'Available';
		}
		return FALSE;	
	}
	
	public function getTableCancelstatus($item_id){
		
		$q = $this->db->get_where('order_items', array('id' => $item_id, 'order_item_cancel_status' => 1), 1);
        if ($q->num_rows() != 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function getOrderItem($item_id){
		
		$q = $this->db->get_where('order_items', array('id' => $item_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row();
        }
        return FALSE;
	}
	
	public function getOrderItemCustomer($item_id){
		$this->db->select('orders.customer_id');
		$this->db->join('orders', 'orders.id = order_items.sale_id');
		$q = $this->db->get_where('order_items', array('order_items.id' => $item_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row('customer_id');
        }
        return FALSE;
	}
	public function getOrderCustomer($order_id){
		$this->db->select('orders.customer_id');
		$q = $this->db->get_where('orders', array('orders.id' => $order_id), 1);
        if ($q->num_rows() == 1 ) {
            return $q->row('customer_id');
        }
        return FALSE;
	}
	
	
	public function splitClose($split){
		 $this->db->select('orders.id, order_items.item_status')
         ->join('order_items', 'orders.id=order_items.sale_id');
        $split_count = $this->db->get_where('orders', array('orders.split_id' => $split, 'order_items.item_status !='  => 'Closed', 'order_items.order_item_cancel_status' => 0 ));
		if($split_count->num_rows() == 0){
			return TRUE;
		}
		return FALSE;	
	}

    public function getDateFormat($id) {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllPONUMBER(){
		$q = $this->db->get_where('purchase_order', array('status' => ''));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
	
	public function getAllPONUMBERedit(){
		$q = $this->db->get('purchase_order');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}
public function getAllQUATATIONNUMBER(){
		$q = $this->db->get_where('quotes', array('status' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

	public function getAllQUATATIONNUMBERedit(){
		$q = $this->db->get('quotes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}

public function getAllMaterial_RequestNo(){
		$q = $this->db->get_where('material_request', array('status' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
	}		
    public function getAllCompanies($group_name) {
        $q = $this->db->get_where('companies', array('group_name' => $group_name));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id) {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id) {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getPreviousDayNightAudit($branch_id) {
		$date_format = 'Y-m-d';
		$yesterday = strtotime('-1 day');
		$previous_date = date($date_format, $yesterday);
		$check_row = $this->db->get('nightaudit');

		$installed_date = $this->Settings->installed_date;
		$install = strtotime($installed_date);        
		$install_date = date('Y-m-d', $install);
		$today_date = date('Y-m-d');
		
		if($install_date < $today_date){
			
			if($check_row->num_rows() > 0){
				$todaytransactionDay = $this->getTransactionDate();
				$previousTransactionDay = $this->getLastDayTransactionDate();
				if (!$todaytransactionDay || $todaytransactionDay==date('Y-m-d')){
				    $this->db->where('nightaudit_date', $previous_date);
				    $this->db->where('warehouse_id', $branch_id);
				    $q = $this->db->get('nightaudit');
				    if ($q->num_rows() > 0) {
					    
					     return TRUE;
				    }
				    else{
					    return FALSE;
				    }
				}else{
				    return true;
				}
			}
			else{	
				return FALSE;
					/*$this->db->where('DATE(date)', $previous_date);
					$this->db->where('warehouse_id', $branch_id);
					$p = $this->db->get('bils');
					if($p->num_rows() > 0){
						return FALSE;
				    }	
				    else{
				    	return TRUE;
				    }*/
			}
			
		}
		else{
			
			return TRUE;
		}
        return FALSE;
    }

	public function getDeliveryPersonall($warehouse_id){
		$this->db->select("users.id, users.first_name, users.last_name, users.email, groups.description");
		$this->db->join('groups', 'groups.id = users.group_id');
		$this->db->where('users.warehouse_id', $warehouse_id);
		$this->db->where('users.active', 1);
		$this->db->order_by('users.group_id', 'DESC');
		 $q = $this->db->get('users');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
                $data[] = $row;
            }
			
			
            return $data;
		}
		return FALSE;
	}

    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByID($id) {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	 public function getrecipeByID($id) {
        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getrecipeKhmer($id){
		$q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row('khmer_name');
        }
        return FALSE;
	}
	public function getrecipeKhmerimage($id){
		$q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row('khmer_image');
        }
        return FALSE;
	}

	
	 public function getAllGroups($pos_user = false) {
		 if($pos_user){
			 $this->db->where_not_in('id', array(1,2,3,4,9));
			 
		 } 
		 $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCurrencies() {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByCode($code) {
        $q = $this->db->get_where('currencies', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function defaultCurrencyData($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
 public function getExchangeCurrency($id) {
 	$this->db->select('symbol');
 	$this->db->where_not_in('id', array($id));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->symbol;
            }
            return $data;
        }
        return FALSE;
    }
    
	
	public function getCurrencyByID($id) {
        $q = $this->db->get_where('currencies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function CancelSalescheckData($sale_id){
		 $q = $this->db->get_where('sales', array('id' => $sale_id, 'payment_status' => NULL), 1);
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
	}
	
	public function getAddonByRecipe($recipe_id, $recipe_addon = array()) {
		
		
		$addons =   explode(',',$recipe_addon);
		
		$this->db->select('recipe_addon.*, recipe.name AS addon_name');
		$this->db->join('recipe', 'recipe.id = recipe_addon.addon_id');
		$this->db->where('recipe_addon.recipe_id', $recipe_id);
		$this->db->where_in('recipe_addon.id', $addons);
		
        $q = $this->db->get('recipe_addon');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllResKitchen() {
		
        $q = $this->db->get('restaurant_kitchens');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
public function getAllDefalutKitchen() {
		
		$default_kitchen = 0;

		$get_default_kitchen = "SELECT RK.id
 		    FROM ".$this->db->dbprefix('restaurant_kitchens')." AS RK
   			where RK.is_default = 1 ";   

		   $k = $this->db->query($get_default_kitchen);  
           
			if ($k->num_rows() > 0) {				
				$result = $k->row();
				$default_kitchen = $result->id;
				  return $default_kitchen;
			}
		return 0;      
    }	
    
	public function getResKitchenByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllTaxRates() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTaxRateByID($id) {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllWarehouses() {
        $q = $this->db->get_where('warehouses',array('type'=>0));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getWarehouseOrderByID($id) {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	
	public function getAllSalestype() {
        $q = $this->db->get('sales_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalestypeByID($id) {
        $q = $this->db->get_where('sales_type', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllAreas() {
        $q = $this->db->get('restaurant_areas');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAreasByID($id) {
        $q = $this->db->get_where('restaurant_areas', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllKitchens() {
        $q = $this->db->get('restaurant_kitchens');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllRecipes() {
        $q = $this->db->get('recipe');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getKitchensByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getAllTables() {
        $q = $this->db->get('restaurant_tables');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTablesByID($id) {
        $q = $this->db->get_where('restaurant_tables', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
    public function getAllCategories() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	
	
	public function getAllrecipeCategories() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('id');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	public function getrecipeSubCategories($parent_id) {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id) {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getrecipeCategoryByID($id) {
        $q = $this->db->get_where('recipe_categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByID($id) {
        $q = $this->db->get_where('gift_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByNO($no) {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateInvoiceStatus() {
        $date = date('Y-m-d');
        $q = $this->db->get_where('invoices', array('status' => 'unpaid'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->due_date < $date) {
                    $this->db->update('invoices', array('status' => 'due'), array('id' => $row->id));
                }
            }
            $this->db->update('settings', array('update' => $date), array('setting_id' => '1'));
            return true;
        }
    }

    public function modal_js() {
        return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function getReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = (!empty($prefix)) ? $prefix . '/' : '';

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return FALSE;
    }

    public function getRandomReference($len = 12) {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref) {
        $this->db->like('reference_no', $ref, 'before');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateReference($field) {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            $this->db->update('order_ref', array($field => $ref->{$field} + 1), array('ref_id' => '1'));
            return TRUE;
        }
        return FALSE;
    }

    public function checkPermissions() {
        $q = $this->db->get_where('permissions', array('group_id' => $this->session->userdata('group_id')), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }
	public function getUserByID($id)
    {
        $q = $this->db->get_where('users', array('id' => $id, 'active' => 1), 1);        
        if ($q->num_rows() > 0) {        	
            return $q->row();
        }
        return FALSE;
    }    
    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getNotifications() {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where("from_date <=", $date);
        $this->db->where("till_date >=", $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
	
	

    public function getUpcomingEvents() {
        $dt = date('Y-m-d');
        $this->db->where('start >=', $dt)->order_by('start')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        $q = $this->db->get('calendar');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = false) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q = $this->db->get_where('groups', array('id' => $group_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUserGroupID($user_id = false) {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

    public function getWarehouseProductsVariants($option_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasedItem($clause) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        if (!isset($clause['option_id']) || empty($clause['option_id'])) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        }
        $q = $this->db->get_where('purchase_items', $clause);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function setPurchaseItem($clause, $qty) {
        if ($product = $this->getProductByID($clause['product_id'])) {
            if ($pi = $this->getPurchasedItem($clause)) {
                $quantity_balance = $pi->quantity_balance+$qty;
                return $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
            } else {
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['quantity'] = $clause['unit_quantity'] = $clause['net_unit_cost'] = $clause['subtotal'] = 0;
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['quantity_balance'] = $qty;
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                return $this->db->insert('purchase_items', $clause);
            }
        }
        return FALSE;
    }

    public function syncVariantQty($variant_id, $warehouse_id, $product_id = NULL) {
        $balance_qty = $this->getBalanceVariantQuantity($variant_id);
        $wh_balance_qty = $this->getBalanceVariantQuantity($variant_id, $warehouse_id);
        if ($this->db->update('product_variants', array('quantity' => $balance_qty), array('id' => $variant_id))) {
            if ($this->getWarehouseProductsVariants($variant_id, $warehouse_id)) {
                $this->db->update('warehouses_products_variants', array('quantity' => $wh_balance_qty), array('option_id' => $variant_id, 'warehouse_id' => $warehouse_id));
            } else {
                if($wh_balance_qty) {
                    $this->db->insert('warehouses_products_variants', array('quantity' => $wh_balance_qty, 'option_id' => $variant_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id));
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getWarehouseProducts($product_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getWarehouserecipe($recipe_id, $warehouse_id = NULL) {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncProductQty($product_id, $warehouse_id) {
        $balance_qty = $this->getBalanceQuantity($product_id);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);
        if ($this->db->update('products', array('quantity' => $balance_qty), array('id' => $product_id))) {
            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {
                $this->db->update('warehouses_products', array('quantity' => $wh_balance_qty), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id));
            } else {
                if( ! $wh_balance_qty) { $wh_balance_qty = 0; }
                $product = $this->site->getProductByID($product_id);
                $this->db->insert('warehouses_products', array('quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }
            return TRUE;
        }
        return FALSE;
    }
	
	public function syncrecipetQty($recipe_id, $warehouse_id) {
        $balance_qty = $this->getBalanceQuantity($recipe_id);
        $wh_balance_qty = $this->getBalanceQuantity($recipe_id, $warehouse_id);
        if ($this->db->update('recipe', array('quantity' => $balance_qty), array('id' => $recipe_id))) {
            if ($this->getWarehouserecipe($product_id, $warehouse_id)) {
                $this->db->update('warehouses_recipe', array('quantity' => $wh_balance_qty), array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id));
            } else {
                if( ! $wh_balance_qty) { $wh_balance_qty = 0; }
                $product = $this->site->getrecipeByID($product_id);
                $this->db->insert('warehouses_recipe', array('quantity' => $wh_balance_qty, 'recipe_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSalePayments($sale_id) {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncSalePayments($id) {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid = 0;
            $grand_total = $sale->grand_total+$sale->rounding;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }

            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid)) {
                $payment_status = 'paid';
            } elseif ($sale->due_date <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        } else {
            $payment_status = ($sale->due_date <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', array('paid' => 0, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }

        return FALSE;
    }

    public function getPurchaseByID($id) {
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_id) {
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchasePayments($id) {
        $purchase = $this->getPurchaseByID($id);
        $paid = 0;
        if ($payments = $this->getPurchasePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $purchase->payment_status;
        if ($this->sma->formatDecimal($purchase->grand_total) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($purchase->grand_total) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('purchases', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    private function getBalanceQuantity($product_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    private function getBalanceVariantQuantity($variant_id, $warehouse_id = NULL) {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('option_id', $variant_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    public function calculateAVCost($recipe_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $recipe_name, $option_id, $item_quantity) {
        $recipe = $this->getrecipeByID($recipe_id);
        $real_item_qty = $quantity;
        $wp_details = $this->getWarehouserecipeone($warehouse_id, $recipe_id);
        $con = $wp_details ? $wp_details->avg_cost : $recipe->cost;
        $tax_rate = $this->getTaxRateByID($recipe->tax_rate);
        $ctax = $this->calculateTax($recipe, $tax_rate, $con);
        if ($recipe->tax_method) {
            $avg_net_unit_cost = $con;
            $avg_unit_cost = ($con + $ctax['amount']);
        } else {
            $avg_unit_cost = $con;
            $avg_net_unit_cost = ($con - $ctax['amount']);
        }

        if ($pis = $this->getPurchasedItems($recipe_id, $warehouse_id, $option_id)) {
            $cost_row = array();
            $quantity = $item_quantity;
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if (!empty($pi) && $pi->quantity > 0 && $balance_qty <= $quantity && $quantity > 0) {
                    if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                        $quantity = 0;
                    } elseif ($quantity > 0) {
                        $quantity = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                    }
                }
                if (empty($cost_row)) {
                    break;
                }
                $cost[] = $cost_row;
                if ($quantity == 0) {
                    break;
                }
            }
        }
        if ($quantity > 0 && !$this->Settings->overselling) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), ($pi->recipe_name ? $pi->recipe_name : $recipe_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        } elseif ($quantity > 0) {
            $cost[] = array('date' => date('Y-m-d'), 'recipe_id' => $recipe_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $real_item_qty, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => NULL, 'overselling' => 1, 'inventory' => 1);
            $cost[] = array('pi_overselling' => 1, 'recipe_id' => $recipe_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id);
        }
        return $cost;
    }

    public function calculateCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity) {
        $pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);
        $real_item_qty = $quantity;
        $quantity = $item_quantity;
        $balance_qty = $quantity;
        foreach ($pis as $pi) {
            $cost_row = NULL;
            if (!empty($pi) && $balance_qty <= $quantity && $quantity > 0) {
                $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
                if ($pi->quantity_balance >= $quantity && $quantity > 0) {
                    $balance_qty = $pi->quantity_balance - $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                    $quantity = 0;
                } elseif ($quantity > 0) {
                    $quantity = $quantity - $pi->quantity_balance;
                    $balance_qty = $quantity;
                    $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                }
            }
            $cost[] = $cost_row;
            if ($quantity == 0) {
                break;
            }
        }
        if ($quantity > 0) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        return $cost;
    }

    public function getPurchasedItems($product_id, $warehouse_id, $option_id = NULL) {
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
	
	 public function getrecipeComboItems($pid, $warehouse_id = NULL) {
        $this->db->select('recipe.id as id, recipe_combo_items.combo_item_id as code, recipe.name as name, recipe.type as type, recipe_combo_items.unit_price as unit_price, warehouses_recipe.quantity as quantity')
            ->join('recipe', 'recipe.code=recipe_combo_items.item_code', 'left')
            ->join('warehouses_recipe', 'warehouses_recipe.recipe_id=recipe.id', 'left')
            ->group_by('recipe_combo_items.id');
        if($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('recipe_combo_items', array('recipe_combo_items.recipe_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function item_costing($item, $pi = NULL) {
        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = NULL;
        }

        if ($this->Settings->accounting_method != 2 && !$this->Settings->overselling) {

            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard') {
                    $unit = $this->getUnitByID($item['product_unit_id']);
                    $item['net_unit_price'] = $this->convertToBase($unit, $item['net_unit_price']);
                    $item['unit_price'] = $this->convertToBase($unit, $item['unit_price']);
                    $cost = $this->calculateCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        if ($pr->type == 'standard') {
                            $cost[] = $this->calculateCost($pr->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $pr->name, NULL, $item_quantity);
                        } else {
                            $cost[] = array(array('date' => date('Y-m-d'), 'product_id' => $pr->id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => ($combo_item->qty * $item['quantity']), 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $combo_item->unit_price, 'sale_unit_price' => $combo_item->unit_price, 'quantity_balance' => NULL, 'inventory' => NULL));
                        }
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        } else {

            if ($this->getrecipeByID($item['recipe_id'])) {
                if ($item['recipe_type'] == 'standard') {
                    $cost = $this->calculateAVCost($item['recipe_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['recipe_name'], $item['option_id'], $item_quantity);
                } elseif ($item['recipe_type'] == 'combo') {
                    $combo_items = $this->getrecipeComboItems($item['recipe_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getrecipeByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        $cost[] = $this->calculateAVCost($combo_item->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $item['recipe_name'], $item['option_id'], $item_quantity);
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'recipe_id' => $item['recipe_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['recipe_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'recipe_id' => $item['recipe_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }

        }
        return $cost;
    }

    public function costing($items) {
        $citems = array();
		
		
        foreach ($items as $item) {
            $option = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
			
			
            $pr = $this->getrecipeByID($item['recipe_id']);
			
            $item['option_id'] = $option;
			
			
            if ($pr && $pr->type == 'standard') {
				
				
                if (isset($citems['p' . $item['recipe_id'] . 'o' . $item['option_id']])) {
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                } else {
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']] = $item;
                    $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                }
            } elseif ($pr && $pr->type == 'combo') {
                $wh = $this->Settings->overselling ? NULL : $item['warehouse_id'];
                $combo_items = $this->getrecipeComboItems($item['recipe_id'], $wh);
				
				
			
                foreach ($combo_items as $combo_item) {
					
                    if ($combo_item->type == 'standard') {
                        if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty*$item['quantity']);
							
							
                        } else {
                            $cpr = $this->getrecipeByID($combo_item->id);
							
							
							
                            if ($cpr->tax_rate) {
                                $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                if ($cpr->tax_method) {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                    $net_unit_price = $combo_item->unit_price - $item_tax;
                                    $unit_price = $combo_item->unit_price;
                                } else {
                                    $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price = $combo_item->unit_price + $item_tax;
                                }
                            } else {
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price;
                            }
                            $cproduct = array('recipe_id' => $combo_item->id, 'recipe_name' => $cpr->name, 'recipe_type' => $combo_item->type, 'quantity' => ($combo_item->qty*$item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate.'%' : $cpr_tax->rate), 'option_id' => NULL, 'recipe_unit_id' => $cpr->unit);
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']] = $cproduct;
                            $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty*$item['quantity']);
                        }
                    }
                }
            }
			
			
			
        }
		
         //$this->sma->print_arrays($combo_items, $citems);
		 
        $cost = array();
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['recipe_id'] . 'o' . $item['option_id']]['aquantity'];
            $cost[] = $this->item_costing($item, TRUE);
        }
        return $cost;
    }

    public function syncQuantity($sale_id = NULL, $purchase_id = NULL, $oitems = NULL, $recipe_id = NULL) {
        if ($sale_id) {

            $sale_items = $this->getAllSaleItems($sale_id);
            foreach ($sale_items as $item) {
                if ($item->recipe_type == 'standard') {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->recipe_id);
                    }
                } elseif ($item->recipe_type == 'combo') {
                    $wh = $this->Settings->overselling ? NULL : $item->warehouse_id;
                    $combo_items = $this->getrecipeComboItems($item->recipe_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if($combo_item->type == 'standard') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }
                    }
                }
            }

        } elseif ($purchase_id) {

            $purchase_items = $this->getAllPurchaseItems($purchase_id);
            foreach ($purchase_items as $item) {
                $this->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                }
            }

        } elseif ($oitems) {

            foreach ($oitems as $item) {
                if (isset($item->product_type)) {
                    if ($item->product_type == 'standard') {
                        $this->syncProductQty($item->product_id, $item->warehouse_id);
                        if (isset($item->option_id) && !empty($item->option_id)) {
                            $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                        }
                    } elseif ($item->product_type == 'combo') {
                        $combo_items = $this->getProductComboItems($item->product_id, $item->warehouse_id);
                        foreach ($combo_items as $combo_item) {
                            if($combo_item->type == 'standard') {
                                $this->syncProductQty($combo_item->id, $item->warehouse_id);
                            }
                        }
                    }
                } else {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                }
            }

        } elseif ($product_id) {
            $warehouses = $this->getAllWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->syncProductQty($product_id, $warehouse->id);
                if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                    }
                }
            }
        }
    }

    public function getProductVariants($product_id) {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllSaleItems($sale_id) {
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllPurchaseItems($purchase_id) {
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getAllPurchasesOrderItems($purchase_order_id) {
        $q = $this->db->get_where('purchase_order_items', array('purchase_order_id' => $purchase_order_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    

    public function getAllQuotationItems($quotes_id) {

        $q = $this->db->get_where('quote_items', array('quote_id' => $quotes_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchaseItems($data = array()) {
        if (!empty($data)) {
            foreach ($data as $items) {
                foreach ($items as $item) {
                    if (isset($item['pi_overselling'])) {
                        unset($item['pi_overselling']);
                        $option_id = (isset($item['option_id']) && !empty($item['option_id'])) ? $item['option_id'] : NULL;
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'option_id' => $option_id);
                        if ($pi = $this->getPurchasedItem($clause)) {
                            $quantity_balance = $pi->quantity_balance + $item['quantity_balance'];
                            $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
                        } else {
                            $clause['quantity'] = 0;
                            $clause['item_tax'] = 0;
                            $clause['quantity_balance'] = $item['quantity_balance'];
                            $clause['status'] = 'received';
                            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;
                            $this->db->insert('purchase_items', $clause);
                        }
                    } else {
                        if ($item['inventory']) {
                            $this->db->update('purchase_items', array('quantity_balance' => $item['quantity_balance']), array('id' => $item['purchase_item_id']));
                        }
                    }
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getrecipeByCode($code) {
        $q = $this->db->get_where('recipe', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function check_customer_deposit($customer_id, $amount) {
        $customer = $this->getCompanyByID($customer_id);
        return $customer->deposit_amount >= $amount;
    }

    public function getWarehouseProduct($warehouse_id, $product_id) {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getWarehouserecipeone($warehouse_id, $recipe_id) {
        $q = $this->db->get_where('warehouses_recipe', array('recipe_id' => $recipe_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
    public function getAllBaseUnits() {
        $q = $this->db->get_where("units", array('base_unit' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getAllUnits()
    {
        $q = $this->db->get('units');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	

    public function getUnitsByBUID($base_unit) {
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
        ->group_by('id')->order_by('id asc');
        $q = $this->db->get("units");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitByID($id) {
        $q = $this->db->get_where("units", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function GetIDBycostomerDiscounts($customer_discount_id){
		$this->db->select('diccounts_for_customer.id, group_discount.discount_val');
		$this->db->join('group_discount', 'group_discount.cus_discount_id = diccounts_for_customer.id ' );
		$this->db->where('diccounts_for_customer.status', 1);
		$this->db->where('diccounts_for_customer.id', $customer_discount_id);
		$this->db->group_by('diccounts_for_customer.id');
        $q = $this->db->get('diccounts_for_customer');
		 if ($q->num_rows() > 0) {
            return $q->row('discount_val').'%';
        }
		return FALSE;
	}
	
	public function GetIDByBBQDiscounts($bbq_discount_id){
		$this->db->select('diccounts_for_bbq.*');
		$this->db->where('diccounts_for_bbq.status', 1);
		$this->db->where('diccounts_for_bbq.id', $bbq_discount_id);
		$this->db->group_by('diccounts_for_bbq.id');
        $q = $this->db->get('diccounts_for_bbq');
		 if ($q->num_rows() > 0) {
            return $q->row('discount').'%';
        }
		return FALSE;
	}
	
    public function GetAllcostomerDiscounts() {
    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

   /*public function GetAllcostomerDiscounts(){
        $u_dis = $this->is_uniqueDiscountExist();
	if(!empty($u_dis)){
	    return FALSE;
	}
  	$date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
    	
    	$myQuery = "SELECT *
        FROM " . $this->db->dbprefix('diccounts_for_customer') . "         
            WHERE   FIND_IN_SET('".$today."' ,week_days)  AND status =1";            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }*/

public function getRecipeGroupId($recipe_id) {

	   $this->db->select('category_id');
    	$this->db->where('id', $recipe_id);
        $q = $this->db->get('recipe');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->category_id;
            }            
            return $data;
        }
        return FALSE;
    }

public function getCalculateCustomerDiscount($recipe_id) {

    	$this->db->where('status', 1);
        $q = $this->db->get('diccounts_for_customer');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerDiscount($id) {
        $q = $this->db->get_where("diccounts_for_customer", array('id' => $id,'status' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPriceGroupByID($id) {
        $q = $this->db->get_where('price_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getRecipeGroupPrice($product_id, $group_id) {
        $q = $this->db->get_where('recipe_prices', array('price_group_id' => $group_id, 'recipe_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBrands() {
        $q = $this->db->get("brands");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllSuppliers() {
		$this->db->where('group_name', 'supplier');
        $q = $this->db->get("companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	 public function getCompanyOrderByID($id) {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getBrandByID($id) {
        $q = $this->db->get_where('brands', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function getKitchenByID($id) {
        $q = $this->db->get_where('restaurant_kitchens', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getAreaByID($id) {
        $q = $this->db->get_where('restaurant_areas', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getTableByID($id) {
        $q = $this->db->get_where('restaurant_tables', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	public function getDiscountByID($id) {
        $q = $this->db->get_where('discount', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	

    public function convertToBase($unit, $value) {
        switch($unit->operator) {
            case '*':
                return $value / $unit->operation_value;
                break;
            case '/':
                return $value * $unit->operation_value;
                break;
            case '+':
                return $value - $unit->operation_value;
                break;
            case '-':
                return $value + $unit->operation_value;
                break;
            default:
                return $value;
        }
    }

    function calculateTax($recipe_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL) {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $recipe_details->cost : $recipe_details->price);
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
			
            if ($recipe_details && $recipe_details->tax_method == 1) {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            } else {
                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax = $this->sma->formatDecimal($tax_details->rate, 0);
        }
		if($tax_details) {
			return array('id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount);
		} else {
				return FALSE;
		}
    }

    function discountMultiple($id = NULL){
        //$id =1;
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
        $q = $this->db->get_where('recipe', array('id' => $id));
        if ($q->num_rows() > 0) {
            $row = $q->row();
	    
		
		$uniqueDaysDis = $this->is_uniqueDiscountExist();
		if(!empty($uniqueDaysDis)){
		    if(@$uniqueDaysDis->type=="discount_simple"){
		    
		
		//echo '<pre>';print_R($uniqueDaysDis));exit;
		
		if(!empty($row->id)){
		  $uniqueQuery = "SELECT max(CASE 
			  WHEN DI.item_type = 'in_list'  
			    AND DI.item_method = 'item_product'             			    
			    AND  FIND_IN_SET('".$row->id."' ,DI.item_type_id) 
			  THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
			  ELSE null END) AS DateDiscount
		  FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
		  JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
		  JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
		  WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id = ".$row->id." AND D.type='discount_simple' AND D.discount_status=1";
		$uniqueDaysproduct = $this->db->query($uniqueQuery);
		$only_discount = $uniqueDaysproduct->row();
		
		//echo '<pre>';print_R($only_discount);echo 66;exit;
		if(empty($only_discount->DateDiscount)){ //echo 5;
		     $uniqueQuery = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        
			AND !FIND_IN_SET('".$row->id."',DI.item_type_id)
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              
              WHERE D.id=".$uniqueDaysDis->discount_id." AND D.type='discount_simple' AND D.discount_status=1";
		    $uniqueDaysproduct = $this->db->query($uniqueQuery); 
		    $only_discount = $uniqueDaysproduct->row();//echo '<pre>';print_R($only_discount);
		    if(empty($only_discount->DateDiscount)){ 
			$category_inlist = "SELECT max(CASE 
				WHEN DI.item_type = 'in_list'  
				  AND DI.item_method = 'item_category'             
				 
				THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
				ELSE null END) AS DateDiscount
			    FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
			     JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
			     JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
			    
			     WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id = ".$row->category_id."  AND D.type='discount_simple' AND D.discount_status=1 ";
			   /*echo $category_inlist;die;  */
			    $category = $this->db->query($category_inlist);
			    $only_discount = $category->row();
			    if(empty($only_discount->DateDiscount)){
				$category_notlist = "SELECT max(CASE 
					  WHEN DI.item_type = 'not_in_list'  
					    AND DI.item_method = 'item_category'             
					   
					    AND !FIND_IN_SET('".$row->id."',DI.item_type_id)
					  THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
					  ELSE null END) AS DateDiscount
				  FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
				  JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
				  JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
				 
				  WHERE D.id=".$uniqueDaysDis->discount_id." AND DIL.item_id != ".$row->category_id." AND D.type='discount_simple' AND D.discount_status=1 ";
		    
				  $cate_not = $this->db->query($category_notlist);
				  $only_discount = $cate_not->row();
			    }
		    }
		}
	    }
	    //echo '<pre>';print_r($only_discount);exit;
	    $only_discount = explode(',',$only_discount->DateDiscount);
		
	    
	    $only_discount['unique_discount'] = true;
	    return $only_discount;
	    }else if(@$uniqueDaysDis->type=="discount_on_total"){
		$only_discount['DateDiscount'] = array();
		$only_discount['only_offer_dis'] = true;
		$only_discount['unique_discount'] = true;
		//echo '<pre>';print_R($only_discount);exit;
		return $only_discount;
	    }
	   } else{
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_simple' AND D.discount_status=1 AND D.unique_discount=0";
/*echo $product_inlist;
echo "<br>";
echo "<br>";*/
            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {

                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product->row('DateDiscount'));

                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('TimeDiscount'));
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('DaysDiscount'));
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product_not->row('DateDiscount'));
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('TimeDiscount'));
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('DaysDiscount'));
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){
                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
             FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";
            /*echo $category_inlist;die;  */
            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',', D.discount_type) 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_simple' AND D.discount_status=1  AND D.unique_discount=0";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL ||  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {

                        $discount_category = explode(',', $category->row('DateDiscount'));
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $category->row('TimeDiscount'));
                    }elseif($category->row('DaysDiscount') != NULL){
                        
                        $discount_category = explode(',', $category->row('DaysDiscount'));
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL ||  $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $cate_not->row('DateDiscount'));
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('TimeDiscount'));
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('DaysDiscount'));
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }
	    // if(!empty($discount_recipe)){
	    	if(!empty($discount_recipe) || !empty($discount_category)){
		if($discount_recipe[0] != 0){
		    return $value = $discount_recipe;
		}else{  
		    return $value = $discount_category;
		}
	    }else{
		return FALSE;
	    }
           }
	   return FALSE;
        }
        return FALSE;
    
    }
    function TotalDiscount(){
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

    $TotalDiscount = "SELECT max(CASE 
                   WHEN DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type) 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.amount,',', D.discount_type)
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discounts') . " D 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE  D.type = 'discount_on_total' AND D.discount_status=1 ";

            $TotalDiscount = $this->db->query($TotalDiscount);
	        $Total_Discount = '';	   
                if ($TotalDiscount->row('DateDiscount') != NULL ||  $TotalDiscount->row('TimeDiscount') != NULL ||  $TotalDiscount->row('DaysDiscount') != NULL) {

                    if($TotalDiscount->row('DateDiscount') != NULL)
                    {

                        $Total_Discount = explode(',', $TotalDiscount->row('DateDiscount'));

                    }elseif($TotalDiscount->row('TimeDiscount') != NULL){

                        $Total_Discount = explode(',', $TotalDiscount->row('TimeDiscount'));
                    }elseif($TotalDiscount->row('DaysDiscount') != NULL){                    	
                        $Total_Discount = explode(',', $TotalDiscount->row('DaysDiscount'));
                    }else{
                       $Total_Discount = '';
                    } 
                }
                
                return $value = $Total_Discount;
        }
      
    function HappyHourdiscount_X_X($id = NULL){
        
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $row = $q->row();
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_buy_x_get_x' ";

            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_buy_x_get_x' ";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {

                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = $product->row('DateDiscount');
                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = $product->row('TimeDiscount');
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = $product->row('DaysDiscount');
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = $product_not->row('DateDiscount');
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = $product_not->row('TimeDiscount');
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = $product_not->row('DaysDiscount');
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){

                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id  
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_buy_x_get_x' ";

            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN D.id 
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN D.id 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN D.id 
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id 
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_buy_x_get_x' ";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL ||  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {
                        $discount_category = $category->row('DateDiscount');
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = $category->row('TimeDiscount');
                    }elseif($category->row('DaysDiscount') != NULL){
                        $discount_category = $category->row('DaysDiscount');
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL ||  $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = $cate_not->row('DateDiscount');
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = $cate_not->row('TimeDiscount');
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = $cate_not->row('DaysDiscount');
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }

            if($discount_recipe[0] != 0){                
                return $value = $discount_recipe;
            }else{                                    
                return $value = $discount_category;
            }
        }
        return FALSE;    
    }        
    function HappyHourdiscount_X_Y($id = NULL){

        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";

        $q = $this->db->get_where('recipe', array('id' => $id), 1);
        if ($q->num_rows() > 0) {

            $row = $q->row();
            if(!empty($row->id)){

                $product_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id) 
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id = ".$row->id." AND D.type='discount_buy_x_get_y' ";

            $product = $this->db->query($product_inlist);

            $product_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date)) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_product' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE null  END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_product' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->id." AND D.type='discount_buy_x_get_y' ";

              $product_not = $this->db->query($product_notlist);
         
                if ($product->row('DateDiscount') != NULL ||  $product->row('TimeDiscount') != NULL ||  $product->row('DaysDiscount') != NULL) {
                    if($product->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product->row('DateDiscount'));
                    }elseif($product->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('TimeDiscount'));
                    }elseif($product->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product->row('DaysDiscount'));
                    }   else{
                       $discount_recipe = '';
                    } 
                }
                else if($product_not->row('DateDiscount') != NULL ||  $product_not->row('TimeDiscount') != NULL ||  $product_not->row('DaysDiscount') != NULL){
                    if($product_not->row('DateDiscount') != NULL)
                    {
                        $discount_recipe = explode(',', $product_not->row('DateDiscount'));
                    }elseif($product_not->row('TimeDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('TimeDiscount'));
                    }elseif($product_not->row('DaysDiscount') != NULL){
                        $discount_recipe = explode(',', $product_not->row('DaysDiscount'));
                    }
                    else{
                       $discount_recipe = '';
                    }
                }
            }
            
            if(!empty($row->category_id)){

                $category_inlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE D.id  END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null  END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id = ".$row->category_id."  AND D.type='discount_buy_x_get_y' ";

            $category = $this->db->query($category_inlist);

            $category_notlist = "SELECT max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category'             
                        AND DC.condition_method ='condition_date' 
                        AND ( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))  
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null END) AS DateDiscount,                      
                      max(CASE
                      WHEN DI.item_type = 'not_in_list' 
                      AND DI.item_method = 'item_category' 
                      AND DC.condition_method ='condition_time' 
                      AND CAST('".$current_time."' AS time) BETWEEN DC.from_time AND DC.to_time 
                    THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                   ELSE null END)AS TimeDiscount, 
                    max(CASE 
                      WHEN DI.item_type = 'not_in_list'  
                        AND DI.item_method = 'item_category' 
                        AND DC.condition_method ='condition_days' 
                       AND  FIND_IN_SET('".$today."' ,days) 
                      THEN CONCAT(D.id,',',D.discount, ',',D.buy_quantity,',', D.get_quantity,',', DI.item_get_id)  
                      ELSE null END) AS DaysDiscount
              FROM " . $this->db->dbprefix('discount_item_list') . " DIL 
              JOIN " . $this->db->dbprefix('discount_items') . " DI ON DI.id = DIL.discount_item_id 
              JOIN " . $this->db->dbprefix('discounts') . " D ON D.id = DI.discount_id 
              JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id  
              WHERE DIL.item_id != ".$row->category_id." AND D.type='discount_buy_x_get_y' ";

              $cate_not = $this->db->query($category_notlist);

              if ($category->row('DateDiscount') != NULL ||  $category->row('TimeDiscount') != NULL &&  $category->row('DaysDiscount') != NULL) {
                    if($category->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $category->row('DateDiscount'));
                    }elseif($category->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $category->row('TimeDiscount'));
                    }elseif($category->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $category->row('DaysDiscount'));
                    }   
                }
                else if($cate_not->row('DateDiscount') != NULL || $cate_not->row('TimeDiscount') != NULL ||  $cate_not->row('DaysDiscount') != NULL){
                    if($cate_not->row('DateDiscount') != NULL)
                    {
                        $discount_category = explode(',', $cate_not->row('DateDiscount'));
                    }elseif($cate_not->row('TimeDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('TimeDiscount'));
                    }elseif($cate_not->row('DaysDiscount') != NULL){
                        $discount_category = explode(',', $cate_not->row('DaysDiscount'));
                    }
                    else{
                        $discount_category = '';
                    }
                }
            }

            if($discount_recipe[0] != 0){                
                return $value = $discount_recipe;
            }else{                                    
                return $value = $discount_category;
            }
        }
        return FALSE;    
    }      
    public function getAddressByID($id) {
        return $this->db->get_where('addresses', ['id' => $id], 1)->row();
    }

    public function checkSlug($slug, $type = NULL) {
        if (!$type) {
            return $this->db->get_where('products', ['slug' => $slug], 1)->row();
        } elseif ($type == 'category') {
            return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
        } elseif ($type == 'brand') {
            return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
        }
        return FALSE;
    }

    public function calculateDiscount($discount = NULL, $amount) {
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                return $this->sma->formatDecimal($discount, 4);
            }
        }
        return 0;
    }

    public function calculate_Discount($discount = NULL, $amount = NULL, $total = NULL) {
     
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {

                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                  $per =  ($discount /$total)*100;
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (Float) ($per)) / 100), 4);
            }
        }
        return 0;
    }    

    public function calculateOrderTax($order_tax_id = NULL, $amount) {
        if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                if ($order_tax_details->type == 1) {
                    return $this->sma->formatDecimal((($amount * $order_tax_details->rate) / 100), 4);
                } else {
                    return $this->sma->formatDecimal($order_tax_details->rate, 4);
                }
            }
        }
        return 0;
    }

    public function getDiscounts($code) {

        $q = $this->db->get_where('recipe', array('code' => $code), 1);

        $this->db->select("discounts.id, discounts.name, discounts.buy_quantity, discounts.get_quantity, discounts.amount, discounts.discount_type, discounts.type, discounts.discount, discount_items.item_method, discount_items.item_type, discount_items.item_get_id, discount_item_list.item_id, discount_item_list.discount_item_id");
                $this->db->join("discount_items", "discount_items.id = discount_item_list.discount_item_id");
                $this->db->join("discounts", "discounts.id = discount_items.discount_id");
                $this->db->where("discount_items.item_method", "item_category");
                $this->db->where("discount_item_list.item_id", $row->category_id);
                $c = $this->db->get("recipe");

        if ($q->num_rows() > 0) {
            $ref = $q->row();
            
            /*switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = (!empty($prefix)) ? $prefix . '/' : '';

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }*/

            return $code;
        }
        return FALSE;
    }
   

 /*public function getDayCategorySale($start,$id,$billid,$warehouse_id) {
    	
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT P.bill_number,P.total,P.total_tax,P.total_discount,P.tax_type
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$billid." AND DATE(P.date) = '".$start."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            if($res->tax_type == 0)
            {	
            	$value = ($res->total)-($res->total_tax);
            }
            else
            {
				$value =($res->total)-($res->total_discount);
            }
            return $value;
        }
        return 0;
    }*/

    public function getDayCategorySale($start,$id,$billid,$warehouse_id) {
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT bill_number,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as amt
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE DATE(P.date) = '".$start."' AND RC.id =".$id." AND P.id =".$billid." AND
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;            
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->amt;
        }
        return 0;
	
	
    }
    public function getMonthlyCategorySale($start,$id,$warehouse_id,$bill_id) {
    	
        $where ='';
            if($warehouse_id != 0)
            {
                $where = "AND P.warehouse_id =".$warehouse_id."";
            }
        
        $myquery ="SELECT P.bill_number,P.total,P.total_tax,P.total_discount,P.tax_type,SUM(BI.subtotal-BI.item_discount-BI.off_discount-BI.input_discount+CASE WHEN (BI.tax_type = 1) THEN BI.tax ELSE 0 END) as amt
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            JOIN " . $this->db->dbprefix('recipe') . " R ON R.id = BI.recipe_id
            JOIN " . $this->db->dbprefix('recipe_categories') . " RC ON RC.id = R.category_id
            WHERE P.id= ".$bill_id." AND DATE_FORMAT( P.date,  '%Y-%m' ) =  '".$start."' AND RC.id =".$id." AND 
            P.payment_status ='Completed'  ".$where." group by R.category_id" ;
           
        $q = $this->db->query($myquery);
       
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $this->sma->formatMoney($res->amt);
            /*if($res->tax_type == 0)
            {	
            	$value = ($res->total)-($res->total_tax);
            }
            else
            {
				$value =($res->total)-($res->total_discount);
            }*/
            
            // return $this->sma->formatMoney($value);
        }
        return 0;
	
	
    }    
public function check_splitid_is_bill_generated($split_id){

    	$myQuery = "SELECT id
			FROM ".$this->db->dbprefix('sales')." 
			WHERE sales_split_id= '".$split_id."' ";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return TRUE;
        }
        return FALSE;
	}
	
    function my_is_unique($value,$field,$table){
	$q = $this->db->get_where($table,array($field=>$value));
	if($q->num_rows()>0){
	    return false;
	}
	return true;
    }
public function getOrderStatus($split_id){

		$myQuery = "SELECT O.id
		  FROM " . $this->db->dbprefix('orders') . " AS O
          JOIN " . $this->db->dbprefix('order_items') . " OI ON OI.sale_id = O.id
		WHERE O.split_id ='".$split_id."' AND ((OI.item_status = 'Inprocess') OR(OI.item_status = 'Preparing') OR (OI.item_status = 'Ready')) AND OI.order_item_cancel_status = 0";
		
		$q = $this->db->query($myQuery);

        if ($q->num_rows() == 0) {
            return TRUE;
        }
        else{
          return FALSE;
        }
	}   

    public function getDiscountsAmt($id) {

        $myquery ="SELECT SUM(BI.item_discount+off_discount+input_discount) AS discount_total
            FROM " . $this->db->dbprefix('bils') . " P
            JOIN " . $this->db->dbprefix('bil_items') . " BI ON BI.bil_id = P.id
            WHERE P.id =".$id." ";
            
        $q = $this->db->query($myquery);

        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->discount_total;
        }
        return 0;
    }
	
    function generate_bill_number($tableWhitelisted){ 
	$billNumReset = $this->Settings->billnumber_reset;
	$today = time();//strtotime('2018-05-01');
	switch($billNumReset){
	    case 1://daily
		$start_time = date('Y-m-d 00:00:01');
		$end_time = date('Y-m-d 23:59:59');
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_time,$end_time,'daily');
		break;
	    case 2://weekly
		$start_date = date('Y-m-d', strtotime('monday this week', $today));
		$end_date = date('Y-m-d', strtotime('sunday this week', $today));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 3://monthly
		$start_date = date('Y-m-01', $today);
		$end_date = date('Y-m-t', $today);
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    case 4://yearly
		$financial_yr_from = explode('/',$this->Settings->financial_yr_from);
		$financial_yr_to = explode('/',$this->Settings->financial_yr_to);
		$start_date = date('Y-'.$financial_yr_from[1].'-'.$financial_yr_from[0], $today);
		$end_date = date('Y-'.$financial_yr_to[1].'-'.$financial_yr_to[0],strtotime('+1 years'));
		$billnumber = $this->getbillNumber($tableWhitelisted,$start_date,$end_date);
		break;
	    default://none
		$billnumber = $this->getbillNumber($tableWhitelisted);
		break;
	    
	}
	return $billnumber;
    }
    function getbillNumber($tableWhitelisted,$start=null,$end=null,$case=null){ 
	$this->db->select();
	if($case == "daily" && $start && $end){
	    $this->db->where(array('date>='=>$start,'date<'=>$end)); 
	}else if($case != "daily" && $start && $end){
	    $this->db->where(array('DATE(date)>='=>$start,'DATE(date)<'=>$end));
	}
	
	$this->db->where('bill_number!=','');
	if($tableWhitelisted){ 
	    $this->db->where('table_whitelisted',1);
	}else{
	    $this->db->where('table_whitelisted',0);
	}
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get('bils');
	if(!$tableWhitelisted){
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		if($result->bill_number[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($result->bill_number)."d",intval($result->bill_number)+1);
		}else {$bill_no = intval($result->bill_number)+1;}
		return $bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $bill_no;
	    }
	}else{
	    $billPrefix = 'tw-';
	    if ($q->num_rows() > 0) {
		$result = $q->row();
		$prevbillno = str_replace($billPrefix,'',$result->bill_number);
		if($prevbillno[0]==0) {
		    $bill_no = sprintf("%'.0".strlen($prevbillno)."d",intval($prevbillno)+1);
		}else {
		    $bill_no = intval($prevbillno)+1;
		    }
		return $billPrefix.$bill_no;
	    }
	    else{
		$bill_no = ($this->Settings->bill_number_start_from!='')?$this->Settings->bill_number_start_from:sprintf("%'.05d", 1);
		return $billPrefix.$bill_no;
	    }
	}
	
    }
	
	
    
    /**** one login at a time ****/
    function isloggeddIn($user){
        
        $q = $this->db
        ->select()
        ->from('user_logins')
        //->where("username ='$user' or email = '$user' ")
	->where("login_type='A' AND (username ='$user' or email = '$user' )")
        ->order_by('id','DESC')
        ->get();
        $data = $q->row_array();
        if($q->num_rows() > 0){
            if($data['status']=="logged_out"){
                return false;
            }else if(time()>strtotime($data['expiry'])){
               return false;
            }/*else if(time()>strtotime($data['last_activity'])+120){
               return false;
            }*/else{
               return true;
            }
        }
       return false;
    }
    function updateLoginStatus($data){
        $session_id = $this->session->userdata('session_id');
        $this->db->where('session_id',$session_id);
        $this->db->update('user_logins',$data);
    }
    function isActiveUser(){
	if($this->router->fetch_method()=="logout"){return true;}
	$session_id = $this->session->userdata('session_id');
	$login_user = $this->session->userdata('username');
        $login_email = $this->session->userdata('email');
        $q = $this->db
        ->select()
        ->from('user_logins')
        ->where("login_type='A' AND (username ='$login_user' or email = '$login_email' )")
	
        ->order_by('id','DESC')
        ->get();
	
	//print_R($q->row());
        if($q->num_rows()>0){
            $row = $q->row();//print_r($row);
	    //echo $session_id.'=='.$row->session_id;exit;
            if($session_id!=$row->session_id) {
		
		/*$data['status'] = "inactive";	*/	
		$this->updateLoginStatus($data);
		$this->session->set_flashdata(lang('someone has logged in'));
		$this->ion_auth->logout();
		admin_redirect('login');
	    }
        }
    }
    /**** one login at a time - End****/
    public function getAllPrinters() {
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAvilAbleTables(){

    	$current_date = date('Y-m-d');
	$current_date = $this->getTransactionDate();
    	$myQuery = "SELECT T.id,T.name
        FROM " . $this->db->dbprefix('restaurant_tables') . " T
        
            WHERE T.id NOT IN (SELECT table_id from srampos_orders WHERE payment_status IS NULL AND order_cancel_status = 0)
             GROUP BY T.id";//DATE(date) ='".$current_date."'  AND 
            
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getAllPaymentMethods(){
	$q = $this->db->get_where('payment_methods', array('status' => 1));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    function getAvilAbleCustomers(){
	$q = $this->db->get_where('companies', array('group_name' => 'customer'));//print_R($this->db->error());
	if ($q->num_rows() > 0) {
           
                $data = $q->result();
            
            return $data;
        }
        return FALSE;
    }

    public function getCustomerDiscountval($id){

    	$myQuery = "SELECT CD.name
			FROM ".$this->db->dbprefix('diccounts_for_customer')." AS CD			
			Where CD.id =".$id." ";			
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->name;
            }
            return $data;
        }
        return FALSE;
       
	}
    function is_uniqueDiscountExist($checkformulti=false){
	$date = date('Y-m-d');
        $current_time = date('H:i:s');
        $mydate=getdate(date("U"));
        $today = "$mydate[weekday]";
	$uniqueQuery = "SELECT *,D.id as discount_id,DC.from_date,DC.to_date,DC2.from_time,DC2.to_time,DC1.days from " . $this->db->dbprefix('discounts') . " D
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC ON D.id = DC.discount_id AND DC.condition_method ='condition_date'
		left JOIN srampos_discount_conditions DC2 ON D.id = DC2.discount_id AND DC2.condition_method ='condition_time'
		
		left JOIN " . $this->db->dbprefix('discount_conditions') . " DC1 ON D.id = DC1.discount_id AND DC1.condition_method ='condition_days'
		WHERE
		
		((DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days) )
		OR  
		( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NULL  AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days) )
		
		
		OR
		    ( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND  DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days))
		
		OR  
		( DC.from_date IS NOT NULL AND DATE('".$date."') >= DATE(DC.from_date) and DATE('".$date."') <= DATE(DC.to_date) AND DC2.from_time IS NULL  AND DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND  DC2.from_time IS NOT NULL AND CAST('".$current_time."' AS time) BETWEEN DC2.from_time AND DC2.to_time AND  DC1.days IS NULL )
		OR
		    ( DC.from_date IS NULL AND DC2.from_time IS NULL  AND DC1.days IS NOT NULL AND FIND_IN_SET('".$today."' ,DC1.days)   ))
		    
		   
	        AND D.discount_status=1 AND D.unique_discount=1 order by D.id ";//DESC LIMIT 1";
		//( DATE(NOW()) >= DATE(DC.from_date) and DATE(NOW()) <= DATE(DC.to_date))
		//	    AND  FIND_IN_SET('".$today."' ,DC1.days) AND D.unique_discount=1 order by D.id DESC LIMIT 1
		//";
		$uniqueDaysDis = $this->db->query($uniqueQuery);
		//echo '<pre>';print_R($uniqueDaysDis->result());exit;
		//echo $uniqueDaysDis->num_rows();
		if(!$checkformulti && $uniqueDaysDis->num_rows()>0){
		    if($uniqueDaysDis->num_rows()==1){
			return $uniqueDaysDis->row();
		    }else if($uniqueDaysDis->num_rows()>1){
			foreach($uniqueDaysDis->result() as $k => $row){
			    if(date('Y-m-d',strtotime($row->apply_for_today)) == date('Y-m-d')){
				return $row;
			    }
			}			
		    }
		   
		} else if($checkformulti && $uniqueDaysDis->num_rows()>1){
		    $hasdisToday = false;
			foreach($uniqueDaysDis->result() as $k => $row){
			    if(date('Y-m-d',strtotime($row->apply_for_today)) == date('Y-m-d')){
				$hasdisToday = true;
			    }
			}
			if(!$hasdisToday){
				
			    return $uniqueDaysDis->result();
			}
		    }
		return array();
    }
	
    function set_unique_discount($id){
	$data['apply_for_today'] = date('Y-m-d');
	$this->db->where('id',$id);
	$this->db->update('discounts',$data);
	
    }
    
    public function getAllrecipeCategories_items() {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('id');
        $q = $this->db->get("recipe_categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $k => $row) {
		$data[$k] = $row;
                $data[$k]->sub_category = $this->getrecipeSubCategories($row->id);
		foreach($data[$k]->sub_category as $kk => $row1){
		     $data[$k]->sub_category[$kk]->recipes = $this->getrecipeBySubCategories($row1->id);
		}
            }
	    //print_R($data);exit;
            return $data;
        }
        return FALSE;
    }
    function getrecipeBySubCategories($sub_id){
	$this->db->where('subcategory_id', $sub_id)->order_by('name');
        $q = $this->db->get("recipe");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
	public function Check_birthday_discount_isavail($customer_id){

		$current_date =date("Y-m");
		$pos_settings = $this->get_posSetting();
		
		if($pos_settings->birthday_enable != 0  && $pos_settings->birthday_discount != 0){
			
			$customer_birthday = "SELECT C.id, C.birthday FROM ".$this->db->dbprefix('companies')." AS C where DATE_FORMAT(C.birthday, '%m') = DATE_FORMAT(NOW(), '%m') AND C.id=".$customer_id."";				
			$c = $this->db->query($customer_birthday); 
			if ($c->num_rows() > 0) {
				$check_discount_aflied = "SELECT B.id FROM ".$this->db->dbprefix('birthday')." AS B Where DATE_FORMAT(B.issue_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') AND B.customer_id=".$customer_id."";
				
				
				$d = $this->db->query($check_discount_aflied);
				if ($d->num_rows() >= 0) {
					if($d->row('id') == ''){
	            		return true;
					}else{
						return FALSE;
					}
	            }
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
	public function Check_bbq_birthday_discount_isavail($customer_id){

		$current_date =date("Y-m");
		$pos_settings = $this->get_posSetting();
		
		if($this->pos_settings->birthday_enable_bbq != 0  && $this->pos_settings->birthday_discount_for_bbq != 0){
			
			$customer_birthday = "SELECT C.id, C.birthday FROM ".$this->db->dbprefix('companies')." AS C where DATE_FORMAT(C.birthday, '%m') = DATE_FORMAT(NOW(), '%m') AND C.id=".$customer_id."";				
			$c = $this->db->query($customer_birthday); 
			if ($c->num_rows() > 0) {
				$check_discount_aflied = "SELECT B.id FROM ".$this->db->dbprefix('birthday')." AS B Where DATE_FORMAT(B.issue_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') AND B.customer_id=".$customer_id."";
				
				
				$d = $this->db->query($check_discount_aflied);
				if ($d->num_rows() >= 0) {
					if($d->row('id') == ''){
	            		return true;
					}else{
						return FALSE;
					}
	            }
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}	    
    function CalculatesimpleDiscount($itemdata){
		
	
	$dis = 0;
	//foreach($itemdata as $k => $row) {
	      $id = $itemdata->recipe_id;
		 $row['net_unit_price'];
	      $subtotal = $itemdata->subtotal;
	    $discount = $this->discountMultiple($id);
	
	    if(!empty($discount)){
		
                           
		if($discount[2] == 'percentage_discount'){

		    $discount_value = $discount[1].'%';

		}else{
		    $discount_value =$discount[1];
		}
		$dis += $this->site->calculateDiscount($discount_value, $subtotal);
		
		return $dis;
	    }
		
	//}
	return $dis;
    }
    function CalculateDiscount_onTotal($total,$existing_dis){
	$Total_Discount = $this->TotalDiscount();		
		
		if($Total_Discount[0] != 0)
                    { 
                         
                         if($Total_Discount[3] == 'percentage_discount'){

                                $totdiscount = $Total_Discount[1].'%';

                            }else{
                                $totdiscount =$Total_Discount[1];
                            }
                            
                        $totdiscount1 = $this->calculateDiscount($totdiscount, $value);
			$sub_total =array_sum($total) - array_sum($existing_dis);
			if($Total_Discount[2]  <= $sub_total){
			    return $Total_Discount[2];
			}else{
			    return 0;
			}
		    }
                return 0;
    }
    
    function setTimeout($fn,$reference,$count){
	$Settings = $this->site->get_setting();
	$timeout = $Settings->notification_time_interval;
	// sleep for $timeout milliseconds.
	sleep($timeout);

	$this->$fn($reference,$count);
    }
    function is_bbqCoversValidated($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bbq_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->where('tag','bbq-cover-validation');
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	if($count==1){
	    $this->db->where('is_read', 0);	    
	}else{
	    $this->db->having('SUM(is_read) = 0');
	}
	$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$bbqConfirmed = $this->db->get_where('bbq',array('reference_no'=>$reference,'status'=>'waiting'));
	if($bbqConfirmed->num_rows()>0 && $q->num_rows()>0){
	    $data = $q->result();
	    
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
			$notification_message = $table_name.' - Customer has sent BBQ Covers.';
			$notification_title = 'BBQ Covers validation request - '.$reference;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bbq-cover-validation',
			    //'reference'=>$row->reference,
			);
			$notifyID = $this->add_notification($notification_array);
			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    
				    $bbq_code = $reference;
				    $table_id = $table_id;
				    $this->site->send_BBQpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_cover_validation');
		    
		    
			    }
		    }
		
		}
	    }
	    //$notification['title'] = $notification_title;
	    //$notification['msg'] = $notification_message;
	    //$event = 'notification';
	    //$edata = $notification;
	    //$this->socketemitter->setEmit($event, $edata);
	    //if($count<3){
		$count++;
		$this->setTimeout('is_bbqCoversValidated',$reference,$count);
	   // }	    
	    
	}
    }
    public function deviceGET($user_id){
		$this->db->select('users.id, device_detail.device_token');
		$this->db->join('device_detail', 'device_detail.user_id = users.id', 'left');
		$this->db->where('users.id', $user_id);
		$q = $this->db->get('users');
		if ($q->num_rows() > 0) {
							
			foreach($q->result() as $row){
				$data[] = $row->device_token;
			}
			return $data;
			
		}
		return FALSE;
	}
	public function deviceDetails($user_id){
		$this->db->select('users.id, device_detail.device_token,socket_id');
		$this->db->join('device_detail', 'device_detail.user_id = users.id');
		$this->db->where('users.id', $user_id);
		$this->db->group_by('device_detail.user_id,device_detail.devices_key');
		$q = $this->db->get('users');
		
		if ($q->num_rows() > 0) {
							
			foreach($q->result() as $row){
				$data[] = $row;
			}
			return $data;
			
		}
		return FALSE;
	}
    public function getTablename($table_id){
		$this->db->select('*')->where('id', $table_id);
		$q = $this->db->get('restaurant_tables');
        if ($q->num_rows() > 0) {
            return $q->row('name');
        }
		return TRUE;
	}
    function send_pushNotification($title,$msg,$socketid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$event = 'push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function send_BBQpushNotification($title,$msg,$socketid,$bbq_code,$tableid,$notifyid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$push_notify['bbq_code'] = $bbq_code;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$event = 'bbq_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function send_billRequestpushNotification($title,$msg,$socketid,$splitid,$tableid,$notifyid){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	$push_notify['socket_id'] = $socketid;
	$push_notify['split_id'] = $splitid;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$event = 'billRequest_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function is_unique_category($pid,$value,$id){
	$this->db->select('*')
	->where(array('name'=>$value,'parent_id'=>$pid));
	if($id){
	    $this->db->where(array('id !='=>$id));
	}
	$q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            return true;
        }
	return false;
    }
    function is_unique_recipeCategories($pid,$value,$id){
	$this->db->select('*')
	->where(array('name'=>$value,'parent_id'=>$pid));
	if($id){
	    $this->db->where(array('id !='=>$id));
	}
	$q = $this->db->get('recipe_categories');
        if ($q->num_rows() > 0) {
            return true;
        }
	return false;
    }
    
    function start_server(){
	$settings = $this->get_setting();
	$host = str_replace('http://','',$settings->socket_host);
	$connection = @fsockopen($host, $settings->socket_port);
	//if(!is_resource($connection)){
	//    exec('START '.FCPATH.'startserver.bat');
	//    return true;
	//}else{
	//    return true;
	//}
	return false;
    }
    function getAllStewards(){
        $this->db->select()
	    ->from('users')
	    ->where_in('group_id',array(5,7))
	    ->where('active',1);
	$q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function getSteward($tableid){
		$q = $this->db->get_where('restaurant_tables',array('id'=>$tableid));
		if ($q->num_rows() > 0) {
			$data = $q->row();
			return $data->steward_id;
		}
        return FALSE;
    }
    function isTableProcessing($table_id){
	$current_date = date('Y-m-d');	
	    $this->db->select();
	    $this->db->from('bbq');
	    $this->db->where('table_id',$table_id);
	    $this->db->where('DATE(created_on)', $current_date);
	    //echo $this->db->get_compiled_select();
	    $this->db->limit(1);
	    $this->db->order_by('id','DESC');//echo $this->db->get_compiled_select();
	    $q = $this->db->get();
	    if ($q->num_rows() > 0) {
		
		$data = $q->row();
		$status = strtolower($data->status);
		if($status=="open" || $status=="waiting"){
		    return true;
		}else if($status=="closed" && $data->payment_status=="paid"){
		    $salereturn = $this->db->get_where('sale_return',array('split_id'=>$data->reference_no));
		    return ($salereturn->num_rows() > 0)?true:false;
		}else{
		    return false;
		}
	    }else{
		$current_date = date('Y-m-d');
		$myQuery = "SELECT *
		FROM " . $this->db->dbprefix('orders') . " WHERE table_id ='".$table_id."'  AND DATE(date) ='".$current_date."'  AND payment_status IS NULL AND order_cancel_status = 0";
		    
		$q = $this->db->query($myQuery);
		if ($q->num_rows() > 0) {
		    
		    return true;
		}else{
		    return false;
		}
		
	    }
	    return false;//allow login
    }
    function getBBQSteward($bbqcode){
	$q = $this->db->get_where('bbq',array('reference_no'=>$bbqcode))->row();
	return $q->confirmed_by;
    }
    
    function send_BBQReturnpushNotification($title,$msg,$socketid,$bbq_code,$tableid,$notifyid,$type="general"){
	$this->load->library('socketemitter');
	$push_notify['title'] = $title;
	$push_notify['msg'] = $msg;
	//$push_notify['type'] = $type;
	$push_notify['socket_id'] = $socketid;
	$push_notify['bbq_code'] = $bbq_code;
	$push_notify['notify_id'] = $notifyid;
	$push_notify['table_id'] = $tableid;
	$event = 'bbq_return_push_notification';
	$edata = $push_notify;
	$this->socketemitter->setEmit($event, $edata);
    }
    function is_bbqReturnCompleted($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bbq_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->where('tag','bbq-return');
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	if($count==1){
	    $this->db->where('is_read', 0);	    
	}else{
	    $this->db->having('SUM(is_read) = 0');
	}
	$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$salereturn = $this->db->get_where('sale_return',array('split_id'=>$reference));
	if($salereturn->num_rows()==0 && $q->num_rows()>0){
	    $data = $q->result();
	    
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
			$notification_message = $table_name.' - Customer has requested BBQ Return.';
			$notification_title = 'BBQ Return Request - '.$reference;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bbq-return',
			    //'reference'=>$row->reference,
			);
			$notifyID = $this->add_notification($notification_array);
			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    
				    $bbq_code = $reference;
				    $table_id = $table_id;
				    $this->site->send_BBQReturnpushNotification($title,$message,$socketid,$bbq_code,$table_id,$notifyID,'bbq_return');
		    
		    
			    }
		    }
		
		}
	    }
	    //$notification['title'] = $notification_title;
	    //$notification['msg'] = $notification_message;
	    //$event = 'notification';
	    //$edata = $notification;
	    //$this->socketemitter->setEmit($event, $edata);
	    //if($count<3){
		$count++;
		$this->setTimeout('is_bbqReturnCompleted',$reference,$count);
	   // }	    
	    
	}
    }
    function socket_refresh_tables($tableid){
	$this->load->library('socketemitter');
	$refreshTable['tableid'] = $tableid;	
	$event = 'update_table';
	$edata = $refreshTable;
	$this->socketemitter->setEmit($event, $edata);
    }
    function socket_refresh_bbqtables($tableid){
	$this->load->library('socketemitter');
	$refreshTable['tableid'] = $tableid;	
	$event = 'update_bbqtable';
	$edata = $refreshTable;
	$this->socketemitter->setEmit($event, $edata);
    }


	public function create_or_get_manual_recipe_details($recipe_name,$unit_price){
		/*echo "<pre>";
		print_r($this->settings);die;*/		
		$category = 'OPEN SALE ITEM';

	    $check_category = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."'";   	

		$c = $this->db->query($check_category);

		$default_kitchen = 0;

		$get_default_kitchen = "SELECT RK.id
 		    FROM ".$this->db->dbprefix('restaurant_kitchens')." AS RK
   			where RK.is_default = 1 ";   

		   $k = $this->db->query($get_default_kitchen);  
           
			if ($k->num_rows() > 0) {				
				$result = $k->row();
				$default_kitchen = $result->id;
			}

	if ($c->num_rows() > 0) {		

		 $category_id = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."' AND RC.parent_id = 0";  
		$c_id = $this->db->query($category_id);
           
           $catid =$c_id->row();
           $category_id = $catid->id;

            $sub_category_id = "SELECT RC.id,RC.name
 		    FROM ".$this->db->dbprefix('recipe_categories')." AS RC
   			where RC.name='".$category."' AND RC.parent_id = ".$category_id.""; 
   			   			
		   $sub_id = $this->db->query($sub_category_id);
           
           $subcatid =$sub_id->row();
           $subcategory = $subcatid->id;
           
           $data = array(
				'code' => 99999,
				'khmer_name' => $recipe_name ? $recipe_name : 0,
				'khmer_image' => str_replace(' ', '-',$recipe_name).'.png',                
                'name' => $recipe_name,
				'currency_type' => $this->settings->default_currency ? $this->settings->default_currency :0,
				'kitchens_id' => $default_kitchen ? $default_kitchen : 0,
                'type' => 'manual',
				'stock_quantity' => 0,				
                'category_id' => $category_id ? $category_id : 0,
                'subcategory_id' => $subcategory ? $subcategory : 0,                
				'cost' => $unit_price ? $unit_price : 0,
				'price' => $unit_price ? $unit_price : 0,                
				'active' => 1,
                'hide' =>  0,
		        'preparation_time' =>600,
            );
           /*echo "<pre>";
           print_r($data);die;*/
            if ($this->db->insert('recipe', $data)) {
            	
             $recipe_id = $this->db->insert_id();

               $warehouse = array(
				'recipe_id' => $recipe_id ? $recipe_id : 0,
				'warehouse_id' => $this->session->userdata('warehouse_id') ? $this->session->userdata('warehouse_id') : 0,
               );
               
               $this->db->insert('warehouses_recipe', $warehouse);   
                return $recipe_id;
               /*var_dump($recipe_id);die;*/
            }
            else{            	
            	  return 0;
            }                      
        }
        else{     

            $insert_category = array(
            'code' => 99999,
            'name' => $category,
            'parent_id' => 0,
            'kitchens_id' => $default_kitchen ? $default_kitchen :0,                        
        );
           $responce = $this->db->insert('recipe_categories', $insert_category);
           $cat_id = $this->db->insert_id();

           $this->db->insert('recipe_categories', $insert_category);

           $sub_cat_id = $this->db->insert_id();

           $this->db->update('recipe_categories', array('parent_id' => $cat_id), array('id' => $sub_cat_id));

            $data = array(
				'code' => 99999,
				'khmer_name' => $recipe_name ? $recipe_name : 0,
				'khmer_image' => str_replace(' ', '-',$recipe_name).'.png',                
                'name' => $recipe_name,
				'currency_type' => $this->settings->default_currency ? $this->settings->default_currency :0,
				'kitchens_id' => $default_kitchen ? $default_kitchen : 0,
                'type' => 'manual',
				'stock_quantity' => 0,				
                'category_id' => $cat_id ? $cat_id : 0,
                'subcategory_id' => $sub_cat_id ? $sub_cat_id : 0,                
				'cost' => $unit_price ? $unit_price : 0,
				'price' => $unit_price ? $unit_price : 0,                
				'active' => 1,
                'hide' =>  0,
		        'preparation_time' =>600,
            );
              

            if ($this->db->insert('recipe', $data)) {
             $recipe_id = $this->db->insert_id();
             $warehouse = array(
				'recipe_id' => $recipe_id ? $recipe_id : 0,
				'warehouse_id' => $this->session->userdata('warehouse_id') ? $this->session->userdata('warehouse_id') : 0,
            );
                 $this->db->insert('warehouses_recipe', $warehouse);             
                  return $recipe_id;
            }
            else{
            	  return 0;
            }
        }         
    }
    function create_nightauditDate(){
	$data['currentdate'] = date('Y-m-d H:i:s');
	
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>date('Y-m-d')));
	if($q->num_rows()==0){
	    $this->db->insert('transaction_date',$data);
	    return $this->db->insert_id();
	}
	
    }
    function check_nightauditDate(){//$transactionDate today or yesterday
	$curdate= date('Y-m-d');
	$user_number = $this->session->userdata('user_number');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	
	if($q->num_rows()>0){
	    $transactionDate = $q->row('transaction_date');
	    if($this->isNightaudit_done($transactionDate)){
		$return = $curdate;
		$this->update_nightauditDate('today');
	    }else{
		$return = $q->row('transaction_date');
	    }
	    echo json_encode(array('status'=>true,'date'=>$return,'user'=>$user_number));
	}else{
	    $date  = date('Y-m-d');
	    $transactionDate = date('Y-m-d', strtotime($date .' -1 day'));
	    if($this->isNightaudit_done($transactionDate)){
		$return = $curdate;
		$this->update_nightauditDate('today');
		echo json_encode(array('status'=>true,'date'=>$return,'user'=>$user_number));
	    }else{
		$lastTrandate = $this->getLastTransactionDate();
		$lastTrandate = date('d-m-Y',strtotime($lastTrandate));
		if($lastTrandate!=date('d-m-Y')){
		    echo json_encode(array('status'=>false,'date'=>$lastTrandate,'user'=>$user_number));
		}else{
		    $this->update_nightauditDate('today');
		    echo json_encode(array('status'=>true,'date'=>$lastTrandate,'user'=>$user_number));
		}
	    }
	    
	}
	exit;
	
    }
    function isNightaudit_done($date){
	$date = date('Y-m-d',strtotime($date));
	$q = $this->db->get_where('nightaudit',array('date(nightaudit_date)'=>$date));	
	if($q->num_rows()>0){
	    return true;
	}
	return false;
    }
    function update_nightauditDate($transactionDay='today'){//$transactionDate today or lastday
	
	$currentdate = date('Y-m-d');
	if($transactionDay=="today"){
	    $data['transaction_date'] = date('Y-m-d').' 00:00:00';
	}else{
	    $data['transaction_date'] = $this->getLastTransactionDate();//date('Y-m-d',strtotime("-1 days")).' 00:00:00';
	}
	$data['approved_by'] = $this->session->userdata('user_id');
	
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$currentdate));
	if($q->num_rows()==0){
	    $this->create_nightauditDate();
	}
	$this->db->where('date(currentdate)',$currentdate);
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$this->db->update('transaction_date',$data);
	
	return $data['transaction_date'];
    }
    function getTransactionDate(){
	$curdate = date('Y-m-d');
	$q = $this->db->get_where('transaction_date',array('date(currentdate)'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return false;
	
    }
    function getLastTransactionDate(){
	$this->db->select();
	$this->db->from('transaction_date');
	$this->db->where(array('transaction_date !='=>'0000-00-00 00:00:00'));
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get();
	//print_R($q->row());
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return date('Y-m-d');
	
    }
    function getLastDayTransactionDate(){
	$curdate = date('Y-m-d');
	$this->db->select();
	$this->db->from('transaction_date');
	$this->db->where(array('date(currentdate) <'=>$curdate,'transaction_date !='=>'0000-00-00 00:00:00'));
	$this->db->order_by('id','desc');
	$this->db->limit(1);
	$q = $this->db->get();
	//print_R($q->row());
	if($q->num_rows()>0){	    
	    return date('Y-m-d', strtotime($q->row('transaction_date')));
	}
	return false;
	
    }
    function isSocketEnabled(){
	$data = $this->get_setting();
	return @$data->socket_enable;
    }
    function update_dbbackup_date($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function update_filesbackup_date($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function update_ftpbackup($data){
	
	$this->db->update('ftp_backup',$data);
    }
    function getAutoback_details(){
	$q = $this->db->get('ftp_backup');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

	public function CheckLoyaltyPoints($customer_id){
		
		$l = $this->db->select('total_points,loyalty_id')->where('customer_id', $customer_id)->where('status', 1)->get('loyalty_points');
		
		if ($l->num_rows() == 1) {

	   		$total_points =  $l->row('total_points');
			$loyalty_id =  $l->row('loyalty_id');
			$e = $this->db->select('eligibity_point')->where('id', $loyalty_id)->where('status', 1)->get('loyalty_settings');
				if ($e->num_rows() == 1) {	
				  $eligibity_point =  $e->row('eligibity_point');
				  if($total_points >= $eligibity_point){
				  	$data = array(
							'total_points' => $total_points,
							'loyalty_id' => $loyalty_id,
							'eligibity_point' => $eligibity_point,							
						);
				  	 return $data;
				   }
				    return FALSE;
		        }		   	
        }
		return FALSE;	
	}	
	public function getLoyaltyRedemption($loyalty_id){  
		$this->db->select('LR.id,LR.points,LR.amount')
		    ->from('loyalty_redemption LR')
		    ->join('loyalty_settings S', 'S.id = LR.loyalty_id') 			            
		    ->where('S.id',$loyalty_id)			            
		    ->where('S.status',1);    
		    $this->db->order_by('S.id', 'ASC');		    
		    $r = $this->db->get();
		    if ($r->num_rows() > 0) {
                return $r->result();
            }
            return FALSE;
	// print_r($this->db->error());die;
    }
    public function getLoyaltyCardByNO($no) {
        $q = $this->db->get_where('loyalty_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getLoyaltyCardByID($id) {
        $q = $this->db->get_where('loyalty_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }    

    public function UpdateCustomerFromLoyalty($customer_id,$bill_id,$salesid,$split_id) {

		/*srampos_sales ==customer_id,customer
		srampos_restaurant_table_sessions ==customer_id
		srampos_orders ==customer_id,customer
		srampos_bils ==customer_id,customer*/

        if ($bill_id) {            
            $this->db->select('id,name')
            ->from('companies')                         
             ->where('group_name','customer')
             ->where('id',$customer_id);
            $this->db->limit(1);
            $c = $this->db->get();             			

	            if ($c->num_rows() > 0) { 	           		

					$customer_id =  $c->row('id');
					$customer_name =  $c->row('per_amounts');	

				$customer_array = array(
		            'customer_id' => $customer_id,
		            'customer' => $customer_name,
		        );

				$this->db->update('sales', $customer_array, array('id' => $salesid));
				$this->db->update('bils', $customer_array, array('id' => $bill_id));
		        
		        $this->db->update('orders', $customer_array, array('split_id' =>  $split_id));
		        $this->db->update('restaurant_table_sessions', array('customer_id' => $customer_id), array('split_id' => $split_id));
			}
			
			//}	      
        }
        
        return FALSE;
    }


    public function LoyaltyinserAndUpdate($bill_id,$total,$customer_id,$loyalty_used_points) {
    	
        if ($bill_id) {
            $cur_date = date('Y-m-d');
            $this->db->select('S.id AS loyalty_id,A.start_amount,A.end_amount,A.per_amounts,A.per_points')
            ->from('loyalty_accumalation A')
            ->join('loyalty_settings S', 'S.id = A.loyalty_id') 
             ->where('"'.$cur_date.'" BETWEEN DATE(S.from_date) and DATE(S.end_date)')            
            // ->where('A.start_amount <=',$total)
            // ->where('A.end_amount >=',$total)    
            ->where('S.status',1);    
            $this->db->order_by('A.id', 'ASC');
            $this->db->limit(1);
            $l = $this->db->get(); 
            
			//if($customer_id != $this->pos_settings->default_customer){

	            if ($l->num_rows() > 0) { 	           		

					$loyalty_id =  $l->row('loyalty_id');
					$per_amounts =  $l->row('per_amounts');
					$per_points =  $l->row('per_points');
					$start_amount =  $l->row('start_amount');
					$end_amount =  $l->row('end_amount');
				if($start_amount <= $total && $end_amount >= $total) {

					$count = $total /$per_amounts;
					$total_points  = intval($count) * $per_points;
	            
		            $loyalty_insert = array(
						    'loyalty_id' => $loyalty_id,
						    'bill_id' => $bill_id,
						    'customer_id' => $customer_id,
						    'total_points' => $total_points,				    
						    'created_on' => date('Y-m-d H:i:s'),
						    'status' => 1,
						);
		            $loyalty_points_add = array(
						    'bill_id' => $bill_id,						    
						    'loyalty_id' => $loyalty_id,						    
						    'accumulation_points' => $total_points,
						    'identify' => 1,						    
						);

		             $c = $this->db->select('customer_id,total_points')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($c->num_rows() > 0) {	             	
	    					$customer =  $c->row('customer_id');
	    					$points =  $c->row('total_points');
	    					$totalpoints = $points + $total_points;
						$this->db->set('total_points', $totalpoints,false);
						$this->db->where('customer_id',$customer);
						$this->db->update('loyalty_points');	

						$this->db->insert('loyalty_points_details', $loyalty_points_add);				
		             }else{	             	
		             	$this->db->insert('loyalty_points_details', $loyalty_points_add);				
		             	$this->db->insert('loyalty_points', $loyalty_insert);	             	
		             }	
		        }else{ 
		        	 $p = $this->db->select('customer_id,total_points')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($p->num_rows() == 0) {	  
		        	 $loyalty = array(
						    'loyalty_id' => $loyalty_id,
						    'bill_id' => $bill_id,
						    'customer_id' => $customer_id,
						    'total_points' => 0,				    
						    'created_on' => date('Y-m-d H:i:s'),
						    'status' => 1,
						);
		        	 $this->db->insert('loyalty_points', $loyalty);	
		            }	   
		        }     
				if(!empty($loyalty_used_points) && $loyalty_used_points != 0){
					 $redempoints = $this->db->select('customer_id,total_points,loyalty_id')->where('customer_id', $customer_id)->get('loyalty_points');
		             if ($redempoints->num_rows() > 0) {	             	
	    					$customer =  $redempoints->row('customer_id');
	    					$points =  $redempoints->row('total_points');
	    					$loyaltyid =  $redempoints->row('loyalty_id');

	    					/*$loyalty_points_reduce = array(
						    'bill_id' => $bill_id,						    
						    'loyalty_id' => $loyaltyid,						    
						    'points' => $loyalty_used_points,
						    'identify' => 2,						    
							);*/

	    				$totalpoints = $points - $loyalty_used_points;
						$this->db->set('total_points', $totalpoints,false);
						$this->db->where('customer_id',$customer);
						$this->db->update('loyalty_points');

						$this->db->set('redemption_points', $loyalty_used_points,false);
						$this->db->where('bill_id',$bill_id);
						$this->db->update('loyalty_points_details');

						// $this->db->insert('loyalty_points_details', $loyalty_points_reduce);				
		             }
				}
			}
			//}	      
        }
        return FALSE;
    }
    public function getCheckLoyaltyAvailable($customer_id){
    		$this->db->select('LP.total_points,LP.expiry_date')
		    ->from('loyalty_points LP')
		    ->join('loyalty_settings S', 'S.id = LP.loyalty_id') 			            
		    ->join('companies C', 'C.id = LP.customer_id') 
		    ->where('LP.total_points >',0)
		    ->where('LP.loyalty_card_no !=', '')   
		    ->where('LP.customer_id', $customer_id)   
		    ->where('S.status',1);  	
		    $r = $this->db->get();				    		    
		    if ($r->num_rows() > 0) {		    				 
                return 1;
            }
        return 0;		
	}    
    function add_notification($notification_array){
	$data = $notification_array['insert_array'];
	//file_put_contents('notify_values1.txt',json_encode($data),FILE_APPEND);
	$q = $this->db->get_where('notiy',array('to_user_id'=>$data['to_user_id'],'split_id'=>$data['split_id'],'tag'=>$data['tag']));
	//file_put_contents('notify_values2.txt',json_encode($q->row()),FILE_APPEND);
	if($q->num_rows()>0){
	    //file_put_contents('notify_values.txt',json_encode($q->row()),FILE_APPEND);
	    $id = $q->row('id');
	    $this->db->set('count','count+1', FALSE);
	    $this->db->where('id',$id);
	    $this->db->update('notiy');
	    return $id;
	}else{
	   $this->db->insert('notiy', $notification_array['insert_array']);
	   return $this->db->insert_id(); 
	}
	
    }
    function BillRequestNotification($reference,$count){
	$Settings = $this->site->get_setting();
	$interval = $Settings->notification_time_interval;
	$no_of_times = $Settings->bbq_bill_request_notify_no_of_times;
	$this->load->library('socketemitter');
	$this->load->library('push');
	$now = date('Y-m-d H:i:s');
	$today = date('Y-m-d');
	$this->db->select('*');
	$this->db->where('split_id',$reference);
	$this->db->from('notiy')
	->where('DATE(created_on)', $today);
	if($count==1){
	    $this->db->where('is_read', 0);	    
	}else{
	    $this->db->having('SUM(is_read) = 0');
	}
	$this->db->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>'.$interval);
	
	//->where('TIMESTAMPDIFF(SECOND,created_on, "'.$now.'")>30');
	$this->db->group_by('split_id,table_id');
	//echo $this->db->get_compiled_select();
	$q = $this->db->get();//echo '<pre>';print_R($q->result());exit;
	$this->db->select()
        ->from('bils')
        ->join('sales','sales.id=bils.sales_id and sales.sales_split_id="'.$reference.'"');
        $billgenerated = $this->db->get();
        
	if($billgenerated->num_rows()==0 && $q->num_rows()>0){
	    $data = $q->result();
	    
	    foreach($data as $k => $row){
		$touser = $row->to_user_id;
		$tableid = $row->table_id;
		if($count<$no_of_times){
		    $this->db->select('*');
		    $this->db->where('id',$touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		else if($count==$no_of_times || $count==$no_of_times+1){
		    $t = $this->db->get_where('restaurant_tables',array('id'=>$tableid))->row();
		    $areaID = $t->area_id;
		    $q = $this->db->get_where('restaurant_tables',array('area_id'=>$areaID))->result();
		    $AreaUsers = array();
		    foreach($q as $k => $urow){
			array_push($AreaUsers,$urow->steward_id);
		    }
		    $AreaUsers = array_unique($AreaUsers);
		    $this->db->select('*');
		    $this->db->where_in('id', $AreaUsers);
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}else{
		    $this->db->select('*');
		    $this->db->where_in('group_id', array(5,7,8,10));
		    $this->db->where('id !=', $touser);
		    $this->db->from('users');
		    $u = $this->db->get();
		    $users = $u->result();
		}
		$table_id = $row->table_id;
		$table_name = $this->getTablename($table_id);
		//file_put_contents('notify_values3.txt',json_encode($users),FILE_APPEND);
		foreach($users as $k1 => $user){
			
			$group_id = $user->group_id;
			$warehouse_id = $row->warehouse_id;
                        $notification_title = 'Bill Request';
			$notification_message = 'Customer has requested for bill  '.$reference.' from '.$table_name;
			//$notification_array['from_role'] = $group_id;
			$user_id = $user->id;
			
			$notification_array['insert_array'] = array(
			    'msg' => $notification_message,
			    'type' => $notification_title,
			    'table_id' => $table_id,
			    'user_id' => $row->user_id,
			    'to_user_id' => $user_id,	
			    'role_id' => $group_id,
			    'warehouse_id' => $warehouse_id,
			    'created_on' => date('Y-m-d H:m:s'),
			    'is_read' => 0,
			    'respective_steward'=>$row->respective_steward,
                            'split_id'=>$reference,
                            'tag'=>'bill-request',
			);
			//file_put_contents('notify_values5.txt',json_encode($notification_array),FILE_APPEND);
			$notifyID = $this->add_notification($notification_array);

			
			$device_token = $this->site->deviceDetails($user_id);
			foreach($device_token as $k =>$device){
			    $title = $notification_title;
			    $message = $notification_message;
			    $push_data = $this->push->setPush($title,$message);
			    if($this->isSocketEnabled() && $push_data == true && isset($device->socket_id) && $device->socket_id!=''){
				    $json_data = '';
				    $response_data = '';
				    $json_data = $this->push->getPush();
				    $regId_data = $device->device_token;
				    $socketid = $device->socket_id;
				    //$response_data = $this->firebase->send($regId_data, $json_data);
				    //var_dump($response_data);
				    
				    $table_id = $table_id;
				    $this->site->send_billRequestpushNotification($title,$message,$socketid,$reference,$table_id,$notifyID);
		    
		    
			    }
		    }
		
		}
	    }
            $count++;
            $this->setTimeout('BillRequestNotification',$reference,$count);

	}
    }
    function update_notification_status($data){
	$data['status'] = 1;
	$this->db->where(array('split_id'=>$data['split_id'],'tag'=>$data['tag']));
	$this->db->update('notiy',$data);
    }
    function get_product_version_history(){
	$q = $this->db->get('version_update_history');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function get_productVersion_date($version){
	$q = $this->db->get_where('version_update_history',array('version'=>$version));
        if ($q->num_rows() > 0) {
            return $q->row('time');
        }
        return FALSE;
    }
    function add_version_history($version){
	$data['time'] = date('Y-m-d H:i:s');
	$data['version'] = $version;
	$this->db->insert('version_update_history', $data);	
	return $this->db->insert_id();
    }
    function product_update_ftp(){
	$q = $this->db->get('product_upgrade');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function update_version($version){
	$data['version'] = $version;
	$this->db->update('settings',$data);
    }
}
